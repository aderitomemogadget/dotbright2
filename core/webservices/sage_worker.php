<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/sage_sync.php';
set_time_limit(0);
ini_set('memory_limit', '512M');
$kind = $argv[1] ?? '';
$input = $argv[2] ?? '';
if (!in_array($kind, array('catalog', 'grades'), true) || !is_file($input)) { exit(1); }
$directory = sage_sync_directory();
$job = basename($input, '.json');
$status = array('job' => $job, 'kind' => $kind, 'state' => 'waiting', 'received_at' => date(DATE_ATOM));
$save = static function () use (&$status, $directory, $job) {
    sage_sync_atomic_write($directory.'/'.$job.'.status.json', json_encode($status, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
};
$save();
$lock = fopen($directory.'/pipeline.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX)) { exit(1); }
try {
    $status['state'] = 'running';
    $status['started_at'] = date(DATE_ATOM);
    $save();
    $json = file_get_contents($input);
    $products = sage_sync_payload($json, $kind);
    $status['records'] = count($products);
    $status['sha256'] = hash('sha256', $json);
    unset($json);
    $path = __DIR__.'/../../core/';
    require_once $path.'settings.php';
    require_once $path.'db_engine_pdo.php';
    $db = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $status['staging'] = sage_sync_stage($db, $products, $kind);
    unset($products);
    unset($db); // The CLI stages can outlast the server's idle connection timeout.
    $stages = $kind === 'catalog' ? array('sync_prods.php', 'sync_prods_2.php') : array('sync_prods_2.php');
    $stages[] = 'acelerador_pesquisa.php';
    putenv('DOTBRIGHT_SAGE_MANAGED=1');
    foreach ($stages as $stage) {
        $status['stage'] = $stage;
        $save();
        $log = $directory.'/'.$job.'.'.$stage.'.log';
        $stageStarted = microtime(true);
        $process = proc_open(array('/usr/bin/php', '-d', 'display_errors=stderr', '-d',
            'error_reporting=1', $path.'crons/'.$stage), array(
                0 => array('file', '/dev/null', 'r'), 1 => array('file', '/dev/null', 'w'), 2 => array('file', $log, 'a')), $pipes);
        $exitCode = is_resource($process) ? proc_close($process) : -1;
        $status['stages'][$stage] = array('exit_code' => $exitCode, 'seconds' => round(microtime(true) - $stageStarted, 2));
        if ($exitCode !== 0) {
            throw new RuntimeException('Stage failed: '.$stage);
        }
    }
    $status['state'] = 'complete';
    $status['finished_at'] = date(DATE_ATOM);
    $last_conection = null;
    $db = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $status['catalogue'] = $db->query('SELECT COUNT(*) AS total, MAX(udtime) AS last_update FROM products')->fetch(PDO::FETCH_ASSOC);
    $save();
    sage_sync_atomic_write($directory.'/last-success.json', json_encode($status, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    echo json_encode($status, JSON_UNESCAPED_UNICODE).PHP_EOL;
} catch (Throwable $error) {
    $status['state'] = 'failed';
    $status['finished_at'] = date(DATE_ATOM);
    $status['error'] = $error->getMessage();
    $save();
    error_log('Sage job '.$job.' failed: '.$error->getMessage());
    exit(1);
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}
