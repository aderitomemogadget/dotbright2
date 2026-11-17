<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

require_once __DIR__.'/legacy_compat.php';

	if(!isset($_SESSION))
	{
		//primeira vez que é invocada a página
		session_start();
		$_SESSION['ss_uniq_page']=uniqid('');//Esta variável serve para identificar se estamos a abrir a página pela primeira vez
	}

	if(isset($_GET['dev']))
	{
		$_SESSION['dev']=$_GET['dev'];
	}



/*if($_SESSION['dev']!='')require_once dirname( __FILE__ ) . '/../conf/conf_'.$_SESSION['dev'].'.php';
else */require_once dirname( __FILE__ ) . '/../conf/conf.php';
