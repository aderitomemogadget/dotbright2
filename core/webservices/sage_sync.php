<?php
// Shared transport and staging code for the two legacy Windows exporters.
function sage_sync_directory()
{
    $directory = dirname(__DIR__, 3).'/private/sage-sync';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Cannot create Sage job directory');
    }
    return $directory;
}

function sage_sync_payload($json, $kind)
{
    if (!is_string($json) || strlen($json) > 120 * 1024 * 1024) {
        throw new InvalidArgumentException('Invalid payload size');
    }
    if (substr(ltrim($json), 0, 1) !== '[') {
        throw new InvalidArgumentException('Expected a JSON array');
    }
    try {
        $products = json_decode($json, true, 64, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        throw new InvalidArgumentException('Invalid JSON');
    }
    if (!is_array($products) || !array_is_list($products) || ($kind === 'catalog' && count($products) < 10)) {
        throw new InvalidArgumentException('Expected a complete product list');
    }
    foreach ($products as $product) {
        if (!is_array($product) || !isset($product['ItemID']) || !is_string($product['ItemID'])
            || trim($product['ItemID']) === '' || !is_string($product['Description'] ?? null)
            || !is_array($product['IVA'] ?? null) || !is_numeric($product['IVA']['TaxRate'] ?? null)
            || !is_numeric($product['AvailableQty'] ?? null)) {
            throw new InvalidArgumentException('Invalid product record');
        }
        foreach (array('PRECOS', 'FAMS_LEVEL', 'EQUIVALENTES', 'GRAU') as $field) {
            if (isset($product[$field]) && !is_array($product[$field])) {
                throw new InvalidArgumentException('Invalid product '.$field);
            }
        }
        if ($kind === 'catalog' && (!is_array($product['FAMS_LEVEL'] ?? null)
            || count($product['FAMS_LEVEL']) === 0 || !array_key_exists('STOCK_ARM', $product)
            || ($product['STOCK_ARM'] !== null && !is_numeric($product['STOCK_ARM'])))) {
            throw new InvalidArgumentException('Missing catalogue stock or family');
        }
        if ($kind === 'grades' && (!is_array($product['GRAUS'] ?? null)
            || !is_numeric($product['GRAUS']['PRECO_GRAU'] ?? null))) {
            throw new InvalidArgumentException('Missing grade or price');
        }
    }
    return $products;
}

function sage_sync_atomic_write($file, $contents)
{
    $temporary = $file.'.'.bin2hex(random_bytes(6)).'.tmp';
    if (file_put_contents($temporary, $contents, LOCK_EX) !== strlen($contents)) {
        @unlink($temporary);
        throw new RuntimeException('Cannot write Sage job');
    }
    chmod($temporary, 0600);
    if (!rename($temporary, $file)) {
        @unlink($temporary);
        throw new RuntimeException('Cannot publish Sage job');
    }
}

function sage_sync_endpoint($kind)
{
    if (PHP_SAPI === 'cli') {
        global $argv;
        if (($argv[1] ?? '') !== '1') {
            fwrite(STDERR, "Usage: php receiver.php 1 [snapshot.json]\n");
            exit(1);
        }
        $prefix = $kind === 'catalog' ? 'produtos_' : 'produtos_grau_';
        $file = $argv[2] ?? dirname(__DIR__, 2).'/files/'.$prefix.date('Y_m_d').'.json';
        $command = array('/usr/bin/php', __DIR__.'/sage_worker.php', $kind, $file);
        $process = proc_open($command, array(0 => STDIN, 1 => STDOUT, 2 => STDERR), $pipes);
        exit(is_resource($process) ? proc_close($process) : 1);
    }
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    ini_set('display_errors', '0');
    $tokenFile = dirname(__DIR__, 3).'/private/sage-sync-token';
    $expectedToken = is_readable($tokenFile) ? trim(file_get_contents($tokenFile)) : '';
    $token = $_SERVER['HTTP_X_SAGE_TOKEN'] ?? '';
    if ($expectedToken === '' || !is_string($token) || !hash_equals($expectedToken, $token)) {
        http_response_code(403);
        echo json_encode(array('error' => 'Forbidden'));
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        echo json_encode(array('error' => 'POST required'));
        return;
    }
    try {
        $json = $_POST['prod'] ?? null;
        $products = sage_sync_payload($json, $kind);
        $job = $kind.'-'.date('Ymd-His').'-'.bin2hex(random_bytes(6));
        $file = sage_sync_directory().'/'.$job.'.json';
        sage_sync_atomic_write($file, $json);
        $command = '/usr/bin/php '.escapeshellarg(__DIR__.'/sage_worker.php').' '
            .escapeshellarg($kind).' '.escapeshellarg($file).' >> '
            .escapeshellarg(sage_sync_directory().'/'.$job.'.log').' 2>&1 < /dev/null &';
        exec($command, $output, $result);
        if ($result !== 0) {
            throw new RuntimeException('Cannot start Sage worker');
        }
        http_response_code(202);
        echo json_encode(array('accepted' => true, 'job' => $job, 'records' => count($products)));
    } catch (InvalidArgumentException $error) {
        http_response_code(422);
        echo json_encode(array('error' => $error->getMessage()));
    } catch (Throwable $error) {
        error_log('Sage receiver failed: '.$error->getMessage());
        http_response_code(503);
        echo json_encode(array('error' => 'Import temporarily unavailable'));
    }
}

function sage_sync_row($product, $grade = null)
{
    $number = static function ($value) { return is_numeric($value) ? 0 + $value : 0; };
    // ItemID and Description are addslashes()-encoded by the installed exporter.
    $row = array(
        'ID_SYNC' => '0', 'Data_Criado' => date('Y-m-d H:i:s'), 'Data_Alterado' => date('Y-m-d H:i:s'),
        'REF' => stripslashes($product['ItemID']), 'Designacao' => stripslashes($product['Description']),
        'Stock' => $number($product['AvailableQty']), 'Stock_Arm' => $number($product['STOCK_ARM'] ?? 0),
        'STOCK_DATE' => $product['STOCK_DATE'] ?? '', 'ARMAZEM' => $number($product['ARMAZEM'] ?? 0),
        'IVA' => $number($product['IVA']['TaxRate']), 'IVA_INFO' => $product['IVA']['TaxExemptionReason'] ?? '',
        'Marca' => $product['Marca'] ?? '', 'fam_level' => $number($product['Level'] ?? 0),
        'TaxableGroupID' => $number($product['TaxaPrivada']['TaxableGroupID'] ?? 0),
        'ItemTax' => $number($product['TaxaPrivada']['ItemTax'] ?? 0),
        'DIAGNOSTICO' => $product['DIAGNOSTICO'] ?? 0,
        'EAN' => $product['BARCODE'] ?? '', 'EAN2' => $product['EAN'] ?? '',
        'Equivalentes' => '',
    );
    $prices = array('PVP' => 'PVP1', 'REVENDA' => 'PVP2', 'PARCEIROS' => 'PVP3',
        'PRECOMERCADO' => 'PVP4', 'PRECOON4TECH' => 'PVP5', 'PRECOLINHASETE' => 'PVP7', 'PRECOSITE' => 'PVP8');
    foreach ($product['PRECOS'] ?? array() as $price) {
        foreach ($prices as $key => $column) {
            if (isset($price[$key])) {
                $row[$column] = $number($price[$key]);
            }
        }
    }
    // Used devices can have no base price; retain the established grade fallback.
    if (empty($row['PVP1']) && isset($product['GRAU'][0]['PRECO_GRAU'])) {
        $row['PVP1'] = $number($product['GRAU'][0]['PRECO_GRAU']);
    }
    foreach (array_slice($product['FAMS_LEVEL'] ?? array(), 0, 5) as $index => $family) {
        $parts = explode(';', $family);
        $level = $index + 1;
        $row['fam_id'.$level] = $number($parts[0]);
        $row['fam_desc'.$level] = $parts[1] ?? '';
        $row['InformationField'.$level] = '';
        foreach (array_slice($parts, 2, 10) as $value) {
            if ($value !== '') {
                $row['InformationField'.$level] .= '|'.$value.'|';
            }
        }
    }
    foreach ($product['EQUIVALENTES'] ?? array() as $reference) {
        $row['Equivalentes'] .= '|'.$reference.'|';
    }
    if ($grade !== null) {
        foreach (array('IMEI', 'GRAU', 'PRECO_GRAU', 'OPERADORA', 'BATTERY',
            'BOX1', 'BOX2', 'CABLE', 'CHARGER', 'HEADSET', 'COVER', 'ARMAZEM') as $field) {
            $value = $grade[$field] ?? '';
            $row[$field] = $value === 'Unknown' ? '' : $value;
        }
        $row['PVP1'] = $number($grade['PRECO_GRAU'] ?? 0);
        foreach (array(2, 3) as $tier) {
            if (isset($grade['PRECO'.$tier.'_GRAU'])) {
                $row['PVP'.$tier] = $number($grade['PRECO'.$tier.'_GRAU']);
            }
        }
    }
    return $row;
}

function sage_sync_stage(PDO $db, $products, $kind)
{
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET SESSION sql_mode=''"); // Legacy schema has mandatory empty numeric columns.
    $tables = $kind === 'catalog' ? array('products_sync', 'products_sync_graus') : array('products_sync_graus');
    $suffix = bin2hex(random_bytes(5));
    $counts = array();
    $created = array();
    try {
        foreach ($tables as $table) {
            $temporary = $table.'_new_'.$suffix;
            $db->exec('CREATE TABLE `'.$temporary.'` LIKE `'.$table.'`');
            $created[] = $temporary;
            $schema = $db->query('DESCRIBE `'.$temporary.'`')->fetchAll(PDO::FETCH_ASSOC);
            $columns = array();
            $defaults = array();
            foreach ($schema as $field) {
                if ($field['Extra'] === 'auto_increment') { continue; }
                $columns[] = $field['Field'];
                $defaults[$field['Field']] = preg_match('/int|float|double|decimal/', $field['Type']) ? 0 : '';
            }
            $insert = $db->prepare('INSERT INTO `'.$temporary.'` (`'.implode('`,`', $columns)
                .'`) VALUES ('.implode(',', array_fill(0, count($columns), '?')).')');
            $counts[$table] = 0;
            $db->beginTransaction();
            foreach ($products as $product) {
                $grades = $kind === 'grades' ? array($product['GRAUS']) : ($product['GRAU'] ?? array());
                $rows = $table === 'products_sync' ? array(sage_sync_row($product)) : array_map(
                    static function ($grade) use ($product) { return sage_sync_row($product, $grade); }, $grades);
                foreach ($rows as $row) {
                    $record = array_replace($defaults, array_intersect_key($row, $defaults));
                    foreach ($record as $key => $value) {
                        if (is_int($defaults[$key]) && !is_numeric($value)) { $record[$key] = 0; }
                    }
                    $insert->execute(array_values($record));
                    $counts[$table]++;
                }
            }
            $db->commit();
        }
        $renames = array();
        foreach ($tables as $table) {
            $renames[] = '`'.$table.'` TO `'.$table.'_old_'.$suffix.'`';
            $renames[] = '`'.$table.'_new_'.$suffix.'` TO `'.$table.'`';
        }
        // Both complete snapshots become visible together; no TRUNCATE of live tables.
        $db->exec('RENAME TABLE '.implode(', ', $renames));
        $created = array();
        foreach ($tables as $table) { $db->exec('DROP TABLE `'.$table.'_old_'.$suffix.'`'); }
    } catch (Throwable $error) {
        if ($db->inTransaction()) { $db->rollBack(); }
        foreach ($created as $table) { $db->exec('DROP TABLE IF EXISTS `'.$table.'`'); }
        throw $error;
    }
    return $counts;
}
