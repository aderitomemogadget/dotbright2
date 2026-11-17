<?php
$GLOBALS['site']=1;
$path=__DIR__.'/core/';
require $path.'core_files.php';
if(($page_locked || (defined('MODO_OFFLINE') && MODO_OFFLINE==1)) && !$GLOBALS['admin_id'] && !$ss_token){echo showLandingPage();exit;}
$page_id=0;
include __DIR__.'/layout/'.PROJETO.'/header.inc.php';
include __DIR__.'/layout/'.PROJETO.'/template_topo.inc.php';
require_once __DIR__.'/layout/'.PROJETO.'/code/discovery.php';
db_disc_page('destaques');
include __DIR__.'/layout/'.PROJETO.'/template_rodape.inc.php';
