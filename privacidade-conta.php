<?php
// Keep raw form data outside the legacy register_globals / HTML input sanitizer.
$_GET=array_intersect_key($_GET,array_flip(array('export')));
$_POST=array_intersect_key($_POST,array_flip(array('_csrf','action','kind','message','acknowledged','id','newsletter','website')));
$_COOKIE=array_intersect_key($_COOKIE,array_flip(array(session_name())));
$privacy_input=$_POST;$privacy_query=$_GET;
$_POST=array();$_GET=array();$_REQUEST=array();$_FILES=array();
$GLOBALS['site']=1;$path=__DIR__.'/core/';require $path.'core_files.php';
require_once __DIR__.'/core/privacy_public.php';
header('Cache-Control: no-store, private');header('Referrer-Policy: no-referrer');header('X-Robots-Tag: noindex, nofollow');header('X-Content-Type-Options: nosniff');header('X-Frame-Options: SAMEORIGIN');
$privacy_state=array('customer'=>null,'error'=>'','success'=>'','requests'=>array(),'csrf'=>store_privacy_csrf());
try {
    if(!store_privacy_enabled())throw new RuntimeException('A área de privacidade não está ativa nesta instalação.');
    $privacy_db=store_privacy_db();
    $privacy_state=store_privacy_public($privacy_db,$_SESSION,$privacy_input,$privacy_query,$_SERVER['REQUEST_METHOD']);
    if(isset($privacy_state['download'])){
        header('Content-Type: application/json; charset=UTF-8');header('Content-Disposition: attachment; filename="dados-da-minha-conta.json"');
        echo json_encode($privacy_state['download'],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);exit;
    }
    if($_SERVER['REQUEST_METHOD']==='POST' && $privacy_state['customer']){
        $_SESSION['store_privacy_flash']=$privacy_state['success'];header('Location: privacidade-conta.php',true,303);exit;
    }
    if(isset($_SESSION['store_privacy_flash'])){$privacy_state['success']=$_SESSION['store_privacy_flash'];unset($_SESSION['store_privacy_flash']);}
}catch(InvalidArgumentException $e){
    $privacy_error=$e->getMessage();
    if(isset($privacy_db)){
        $privacy_input=is_array($privacy_input)?$privacy_input:array();
        if(is_string($privacy_input['message']??null))$privacy_input['message']=mb_substr($privacy_input['message'],0,2000);
        $privacy_state=store_privacy_public($privacy_db,$_SESSION,array(),array(),'GET');
    }
    $privacy_state['error']=$privacy_error;
}catch(Throwable $e){http_response_code(503);$privacy_state['error']='Não foi possível abrir esta área. Pode exercer os seus direitos através dos contactos da loja.';error_log('Store privacy unavailable: '.get_class($e));}
$layoutPath=__DIR__.'/layout/'.PROJETO.'/';
$GLOBALS['page_id']=0;$GLOBALS['template']=16;$page_info=array('title'=>'Privacidade e dados pessoais | '.STORE_PRIVACY_BRAND,'description'=>'Gestão dos dados da sua conta.');
$privacy_breakpoint=defined('STORE_PRIVACY_MOBILE_BREAKPOINT')?(int)STORE_PRIVACY_MOBILE_BREAKPOINT:1024;
$privacy_assets='<link rel="stylesheet" href="assets/privacy/privacy.css?v=20261103"><script src="assets/privacy/privacy.js?v=20261103b" data-mobile-breakpoint="'.$privacy_breakpoint.'"></script>';
if(is_file($layoutPath.'header.inc.php') && is_file($layoutPath.'template_topo.inc.php') && is_file($layoutPath.'template_rodape.inc.php')){
    ob_start();include $layoutPath.'header.inc.php';$privacy_header=ob_get_clean();
    echo preg_replace('/<head\b[^>]*>/i','$0'.$privacy_assets,$privacy_header,1);
    include $layoutPath.'template_topo.inc.php';include __DIR__.'/core/privacy_view.php';include $layoutPath.'template_rodape.inc.php';
}else{
    // Older catalogue versions have no reusable page wrapper. Keep a lightweight,
    // branded account page without importing the homepage feed or changing its theme.
    echo '<!doctype html><html lang="pt"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.store_privacy_escape($page_info['title']).'</title>'.$privacy_assets.'</head><body><header class="store-privacy"><a href="'.store_privacy_escape(STORE_PRIVACY_SITE_URL).'">'.store_privacy_escape(STORE_PRIVACY_BRAND).'</a></header>';
    include __DIR__.'/core/privacy_view.php';echo '</body></html>';
}
