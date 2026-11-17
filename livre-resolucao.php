<?php
// Limit legacy register_globals() input to this form's fields before bootstrapping.
$_GET=array_intersect_key($_GET,array_flip(array('site_lingua','order','receipt','download')));
$_POST=array_intersect_key($_POST,array_flip(array('_csrf','action','draft','name','order_reference','email','scope','items','received_on','website')));
// Keep the PHP session cookie, but do not let arbitrary cookies become PHP globals.
$_COOKIE=array_intersect_key($_COOKIE,array_flip(array(session_name())));
$_GET['site_lingua']=in_array($_GET['site_lingua'] ?? 'pt',array('pt','en','fr'),true) ? ($_GET['site_lingua'] ?? 'pt') : 'pt';
$withdrawal_input=$_POST;
$withdrawal_query=$_GET;
// The legacy sanitizer HTML-encodes input. Keep raw form data out of that bootstrap;
// the service validates it and the templates escape it once, at output.
$_POST=array(); $_GET=array('site_lingua'=>$withdrawal_query['site_lingua']); $_REQUEST=$_GET; $_FILES=array();
$GLOBALS['site']=1;
$path=__DIR__.'/core/';
require $path.'core_files.php';
require_once $path.'withdrawal_public.php';
header('Cache-Control: no-store, private');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex, nofollow');
header('X-Content-Type-Options: nosniff');
$withdrawal_language=store_withdrawal_language($GLOBALS['site_lang'] ?? 'pt');
$withdrawal_state=array('step'=>'unavailable','language'=>$withdrawal_language,'error'=>'unavailable');
try {
    if (store_withdrawal_enabled() && realpath(__DIR__)==='/home/www/dotbright.pt/public_html') {
        $withdrawal_state=store_withdrawal_public(store_withdrawal_db(),$withdrawal_input,$withdrawal_query,$withdrawal_language,store_withdrawal_secret(),'store_withdrawal_sender');
    } else http_response_code(503);
    if (!empty($withdrawal_state['redirect'])) { header('Location: '.$withdrawal_state['redirect'],true,303); exit; }
    if ($withdrawal_state['step']==='receipt' && isset($withdrawal_query['download'])) {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$withdrawal_state['row']['reference'].'.txt"');
        echo store_withdrawal_receipt($withdrawal_state['row']); exit;
    }
} catch (Throwable $e) { http_response_code(503); error_log('Dotbright withdrawal unavailable: '.get_class($e)); }
$layoutPath=__DIR__.'/layout/'.PROJETO.'/';
// Use the existing information-page template, not the default homepage/product feed.
$GLOBALS['page_id']=0;
$GLOBALS['template']=16;
$page_info=array('title'=>store_withdrawal_text('title',$withdrawal_language).' | Dotbright');
// Load this page's resize guard before the legacy site.js registers its redirect.
// Keep the shared header (which has other pending work) untouched.
ob_start();
include $layoutPath.'header.inc.php';
$withdrawal_header=ob_get_clean();
echo preg_replace('/<head>/i','<head><link rel="stylesheet" href="layout/dotbright/css/withdrawal.css?v=20261103b"><script src="layout/dotbright/js/withdrawal.js?v=20261103b"></script>',$withdrawal_header,1);
include $layoutPath.'template_topo.inc.php';
include $layoutPath.'withdrawal.inc.php';
include $layoutPath.'template_rodape.inc.php';
