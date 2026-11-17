<?php
	$path=dirname( __FILE__ ).'/../core/';
	include $path.'core_files.php';
	if(isset($_POST['prodid']) && $_POST['prodid'] > 0) $prodid=$_POST['prodid'];
	else $prodid=0;
	if($prodid > 0)
	{
		if($dados=cliente_logado())
		{
			$cli_id=$dados['cli_id'];
			if(isProductInWishList($prodid, $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) echo getLinguas('key_wishlist_rem');
			else echo getLinguas('key_wishlist_add');
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInWishList($prodid, 0, $_SESSION['CLIENTE_ID_TEMP'])) echo getLinguas('key_wishlist_rem');
			else echo getLinguas('key_wishlist_add');
		}
		else echo "-1";
	}
	else
	{
		echo "-1";
	}
?>