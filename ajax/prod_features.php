<?php
	if(isset($_POST['prod_id']) && intval($_POST['prod_id']) > 0)
	{
		$path='../core/'; 
		include $path.'core_files.php';		
		$prods=get_produtc_info(intval($_POST['prod_id']));		
		echo $prods['prod_features'];
	}
	else die('Não permissões para aceder a está pagina!');
?>