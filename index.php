<?php
	$path=dirname( __FILE__ ).'/core/';
	include $path.'core_files.php';

	if(($page_locked || (defined('MODO_OFFLINE') && MODO_OFFLINE == 1)) && !$GLOBALS['admin_id'] && !$ss_token) {echo showLandingPage();die();}
	if(isset($_GET['error'])) {echo showLandingPage404();die();}
	if(isset($_GET['page_id'])) $page_id=intval($_GET['page_id']);
	include $path.'../layout/'.PROJETO.'/header.inc.php';

	include $path.'../layout/'.PROJETO.'/template_topo.inc.php';
	/* CONTEUDO */
	include $path.'../layout/'.PROJETO.'/conteudo.inc.php';
	/* RODAPE */
	include $path.'../layout/'.PROJETO.'/template_rodape.inc.php';
?>