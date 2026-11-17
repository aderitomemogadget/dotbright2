<?php
	$path=dirname( __FILE__ ).'/../core/';
	include $path.'core_files.php';
	if(isset($_POST['action'])) $action=$_POST['action']; else $action='';
	if(isset($_POST['prodid']) && intval($_POST['prodid']) > 0) $prodid=intval($_POST['prodid']);	else $prodid=0;
	if($prodid > 0 && $action != '')
	{
		if(isset($_SESSION['comp_prod_'.$prodid]))
		{
			unset($_SESSION['comp_prod_'.$prodid]);
			echo getLinguas('key_wishlist_add');
		}
		else
		{
			$_SESSION['comp_prod_'.$prodid]=$prodid;
			echo getLinguas('key_wishlist_rem');
		}
	}
?>