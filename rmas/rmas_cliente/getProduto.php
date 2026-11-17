<?php
include "settings.inc.php";
$REF = $_GET['ref'];
$PVP = ($_GET['PVP']) ? 0 : 1;
if($REF != '')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	if($REF != '') $cond = "products.Ref='".$REF."'";
	$sql="
	Select products_lang.ID_PAI, products_lang.Cabecalho, products_objs.obj_pvp, products_objs.obj_stock from products
	inner join products_lang on (products_lang.ID_PAI=products.ID AND products_lang.Lang='pt')
	inner join products_objs on (products_objs.obj_type_id=products.ID AND products_objs.obj_val=$PVP AND obj_ativo=1)
	WHERE ".$cond;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$precociva = $rs->db_data['obj_pvp'];
		$precosiva = round($rs->db_data['obj_pvp']/1.23,2);
		echo $rs->db_data['ID_PAI'].'&;'.$rs->db_data['Cabecalho'].'&;'.$precosiva.'&;'.$rs->db_data['obj_stock'];
	}
}