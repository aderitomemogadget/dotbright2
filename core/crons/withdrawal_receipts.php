<?php
// CLI only. Default is a read-only queue check; --send enables delivery/recovery.
if (PHP_SAPI!=='cli') { http_response_code(403); exit; }
$root=dirname(__DIR__,2);
if (realpath($root)!=='/home/www/dotbright.pt/public_html') { fwrite(STDERR,"Instalação de produção obrigatória.\n"); exit(1); }
$_SERVER['HTTPS']='on';$_SERVER['HTTP_HOST']='www.dotbright.pt';$_SERVER['SERVER_NAME']='www.dotbright.pt';$_SERVER['SCRIPT_NAME']='/index.php';$_SERVER['SCRIPT_FILENAME']=$root.'/index.php';$_SERVER['REQUEST_URI']='/';$_SERVER['REQUEST_METHOD']='GET';$path=$root.'/core/';chdir($root);ob_start();require $path.'core_files.php';ob_end_clean();


require_once $root.'/core/withdrawals.php';
try {
    if (!store_withdrawal_enabled()) throw new RuntimeException('Disabled');
    $db=store_withdrawal_db();
    $lock=$db->query("SELECT GET_LOCK('store_withdrawal_receipts',0)")->fetchColumn();
    if (!$lock) { echo "Outra execução está ativa.\n"; exit; }
    try {
        $send=in_array('--send',$argv,true);
        $counts=store_withdrawal_process($db,$send,'store_withdrawal_sender');
        echo gmdate('c').' '.($send?'send':'dry-run').' '.json_encode($counts).PHP_EOL;
    } finally { $db->query("SELECT RELEASE_LOCK('store_withdrawal_receipts')"); }
    exit($counts['failed'] ? 1 : 0);
} catch (Throwable $e) { fwrite(STDERR,'Dotbright comprovativos: verificar configuração/BD ('.get_class($e).').'.PHP_EOL); exit(1); }
