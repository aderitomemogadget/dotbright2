<?php
	$path='../core/';
	include $path.'core_files.php';
	if(isset($_POST['action'])) $action=$_POST['action'];
	else $action='';
	if(isset($_POST['prodid']) && $_POST['prodid'] > 0) $prodid=$_POST['prodid'];
	else $prodid=0;
	if($prodid > 0 && $action != '')
	{
		if(isset($_SESSION['comp_prod_'.$prodid]))
		{
			unset($_SESSION['comp_prod_'.$prodid]);
			echo getLinguas('key_comparador_add');
		}
		else
		{
			$_SESSION['comp_prod_'.$prodid]=$prodid;
			echo getLinguas('key_comparador_rem');
		}
	}
?>