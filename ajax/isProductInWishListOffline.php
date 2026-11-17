<?php
	$path='../core/';
	include $path.'core_files.php';
	if(isset($_POST['prodid']) && $_POST['prodid'] > 0) $prodid=$_POST['prodid'];
	else $prodid=0;
	if($prodid > 0)
	{
		if(isset($_SESSION['comp_prod_'.$prodid])) echo getLinguas('key_wishlist_rem');
		else echo getLinguas('key_wishlist_add');
	}
	else
	{
		echo "-1";
	}
?>