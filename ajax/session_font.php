<?php
	$path=dirname( __FILE__ ) . '/../core/';
	include $path . 'core_files.php';
	error_reporting(E_ALL);
	ini_set('display_errors',0);
	$return='';

	global $tem_acessibilidade;

	if($tem_acessibilidade)
	{
		if(isset($_POST['minus']))
		{
			if (($_SESSION['font']-1) > 0) 
			{
				$_SESSION['font'] = $_SESSION['font'] - 1;
				echo true;	
			}
		}
		elseif(isset($_POST['plus']))
		{
			var_dump($_SESSION['font']);
			if (($_SESSION['font']+1) < 5) 
			{
				$_SESSION['font'] = $_SESSION['font'] + 1;
				echo true;
			}
		}
	}
	echo false;	
?>