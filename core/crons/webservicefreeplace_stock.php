<?php
$path = dirname( __FILE__ ) . '/../../core/';
require_once $path."core_files.php";

$path2=dirname( __FILE__ ) . '/../../';
include $path2 . 'admin/products/filtros/funcoes.inc.php';

error_reporting(E_ALL);
ini_set('display_errors',0);
header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time","60000");
ini_set("max_input_time","60000");
ini_set("memory_limit","-1");

global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs=new DataObj;

$filename=$path=dirname( __FILE__ ) . '/../../files/json_stock.txt';

$st=file_get_contents('http://www.macorlux.pt/webservice_freeplace_stock.php?user=memogadget&pass=domesticos&id_revendedor=14047&exec=1');

if($st && $json=file_get_contents('http://www.macorlux.pt/img_upload/tmp/json_stock.txt'))
{
	file_put_contents($filename,$json);$json=file_get_contents($filename);

	$json=json_decode($json, true);

	foreach($json as $produtos)
	{
		foreach($produtos as $prod)
		{
			$sql_ref='SELECT ID, ID_ORG, ID_Marca, Sync FROM products WHERE Ref="'.$prod['Referencia'].'"';
			$rs->db_open($db_link,$DB_name,$sql_ref);
			if(!$rs->EOF())
			{

				if($rs->db_data['Sync'] == 0)
				{
					$produto_id=$rs->db_data['ID'];
					$sub_fam_id=$rs->db_data['ID_ORG'];
					$prod_Marca_id=$rs->db_data['ID_Marca'];

					$sql_upd_obj='UPDATE products_objs
									SET
										obj_stock="'.$prod['Stock_novo'].'"
									WHERE
										obj_type_id="'.$produto_id.'"';
					$res=Runquery($db_link,$DB_name,$sql_upd_obj);

					if($produto_id > 0 && $sub_fam_id > 0 && $prod_Marca_id > 0) gera_produtos($produto_id, $sub_fam_id, $prod_Marca_id, 1);
				}
			}
		}
	}
	unlink($filename);
}
?>