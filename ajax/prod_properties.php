<?php
	if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$path='../core/';
		include $path.'core_files.php';
		echo products_objs_show($_GET['prod_id']);?>
		<div style="clear:both"></div><?php
	}
	elseif(isset($_POST['prod_id']) && $_POST['prod_id'] > 0)
	{
		$path='../core/';
		include $path.'core_files.php';
		echo products_objs_show($_POST['prod_id']);?>
		<div style="clear:both"></div><?php
	}else die('Não permissões para aceder a está pagina!');
?>