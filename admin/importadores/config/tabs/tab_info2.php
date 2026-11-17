<?php
	$IDb=intval($_GET['ID']);
	$Supplier_ID=intval($_GET['ID']);

	$path = dirname(__FILE__).'/../../../../core/';
	include $path.'core_files.php';
	include "../settings_crons.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$Suppliers_nomes;
	$rs=new Dataobj;
	$rs2=new Dataobj;

	$total_produtos_activos=0;
	$total_produtos_inativos=0;
	$total_produtos_activos_stock=0;
	$total_precos=0;
	$total_novos_precos=0;

	global $DB_host, $DB_user, $DB_pass, $DB_name,$Supplier_ID;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	/*$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	INNER JOIN products_objs_history ON products_objs_history.ID_ORG=products_objs.obj_id
	WHERE products.Aproved=1 AND Supplier_ID = "'.$Supplier_ID.'" group by products.ID';*/

	$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	WHERE products.Aproved=1 AND obj_Supplier_ID = "'.$Supplier_ID.'" group by products.ID';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total_produtos_activos=$rs->db_rows;
	}
	$rs->db_close();

	/*$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	INNER JOIN products_objs_history ON products_objs_history.ID_ORG=products_objs.obj_id
	WHERE products.Aproved=0 AND Supplier_ID = "'.$Supplier_ID.'" group by products.ID';*/

	$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	WHERE products.Aproved=0 AND obj_Supplier_ID = "'.$Supplier_ID.'" group by products.ID';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total_produtos_inativos=$rs->db_rows;
	}
	$rs->db_close();


	/*$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	INNER JOIN products_objs_history ON products_objs_history.ID_ORG=products_objs.obj_id
	WHERE products.Aproved=1 AND products_objs_history.Stock >0 and Supplier_ID = "'.$Supplier_ID.'" group by products.ID';*/

	$sql = 'SELECT products.ID FROM products
	INNER JOIN products_objs ON products_objs.obj_type_id=products.ID
	WHERE products.Aproved=1 AND obj_stock > 0 AND obj_Supplier_ID = "'.$Supplier_ID.'" group by products.ID';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total_produtos_activos_stock=$rs->db_rows;
	}
	$rs->db_close();


	$sql = 'SELECT ID FROM products_objs_history WHERE Supplier_ID = "'.$Supplier_ID.'" group by ID_ORG';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total_precos=$rs->db_rows;
	}
	$rs->db_close();


	$sql = 'SELECT ID FROM products_objs_history WHERE Supplier_ID = "'.$Supplier_ID.'" and data_i like "'.date("Y-m-d").'%" group by ID_ORG';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total_novos_precos=$rs->db_rows;
	}
	$rs->db_close();


	$total_produtos_activos=number_format($total_produtos_activos,0,',','.');
	$total_produtos_inativos=number_format($total_produtos_inativos,0,',','.');
	$total_produtos_activos_stock=number_format($total_produtos_activos_stock,0,',','.');
	$total_precos=number_format($total_precos,0,',','.');
	$total_novos_precos=number_format($total_novos_precos,0,',','.');

	echo '<div style="padding:5px 0">Produtos ativos com preço deste fornecedor: <b>'.$total_produtos_activos.'</b> - <a href="javascript:;" onclick="load_prod(\'#info1\',\'precos_detalhe.php?ID='.$Supplier_ID.'\')">mostrar 100</a></div>  <div style="clear:both"></div><br>';
	echo '<div class="col-lg-12 prod" id="info1" style="opacity: 0; display: none; transition: NaNpx 0s ease 0s"></div>';
	echo '<div style="padding:5px 0">Produtos inativos com preço deste fornecedor: <b>'.$total_produtos_inativos.'</b></div><div style="clear:both"></div><br>';
	echo '<div style="padding:5px 0">Produtos ativos com stock deste fornecedor: <b>'.$total_produtos_activos_stock.'</b></div><div style="clear:both"></div><br>';
	echo '<div style="padding:5px 0">Total de preços deste fornecedor: <b>'.$total_precos.'</b></div><div style="clear:both"></div><br>';
	echo '<div style="padding:5px 0">Novos preços importados hoje: <b>'.$total_novos_precos.'</b></div><div style="clear:both"></div><br>';

?>