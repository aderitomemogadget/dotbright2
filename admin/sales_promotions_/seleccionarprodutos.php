<?php
require_once "settings.inc.php";
ini_set('meemory_limit', '1000M');
ini_set('post_max_size', '1000M');
ini_set('upload_max_filesize', '1000M');

if(isset($_GET['d_produto']) && $_GET['d_produto']!='')
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$desconto = $_GET['desconto2'];
	$prods = explode('|', $_GET['g_produto']);

	$sql = '
			DELETE FROM sales_promotions_products
			WHERE
				promo_id="'.$_GET['ID_PAI'].'"
				AND
				DESCONTO="'.$desconto.'"
				AND
				PROD_ID="'.$_GET['d_produto'].'"
			';
	$res = Runquery($db_link, $DB_name, $sql);

	db_closedb_conection($db_link);

	$fams_list = gera_lista_produtos($_GET['ID_PAI'], 1, $desconto);
	echo $fams_list;
}
elseif(isset($_GET['g_produto']) && $_GET['g_produto']!='')
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;

	$desconto = $_GET['desconto2'];
	$prods = explode('|', $_GET['g_produto']);

	for($a=0;$a<count($prods); $a++)
	{
		$sql = '
				SELECT promo_id
				FROM
					sales_promotions_products
				WHERE
					promo_id="'.$_GET['ID_PAI'].'"
					AND
					DESCONTO="'.$desconto.'"
					AND
					PROD_ID="'.$prods[$a].'"
				';
		$rs->db_open($db_link, $DB_name, $sql);
		if($rs->EOF())
		{
			$sql='INSERT INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID) VALUES ("'.$_GET['ID_PAI'].'","'.$desconto.'","'.$prods[$a].'")';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		$rs->db_close();
	}
	db_closedb_conection($db_link);

	$fams_list = gera_lista_produtos($_GET['ID_PAI'], 1, $desconto);
	echo $fams_list;
}
else
{
	$DESCONTOf = str_replace('.','_',$_GET['desconto']);
	$DESCONTOf2 = str_replace('_','.',$_GET['desconto']);


	?><table width="100%" border="0" align="left"><?php

	$prods_list = show_produtosp();
	if($prods_list['list_prods'])
	{
		echo '<tr><td  style="border-bottom:solid 1px #000; " class=\'preto\' align=\'center\'><input type="button" onclick="submeter_produtos(\''.$DESCONTOf.'\',\''.$DESCONTOf2.'\',\''.$_GET['ID_PAI'].'\');" value="Submeter Produtos" class="btn btn-primary" /></td></tr>';
		echo "<tr bgcolor='".$cor."'><td colspan=\"2\" class='preto' align='left' height='25'>";
		echo "<div style='float:left'><b> <span style='color:#008B45'>".$prods_list['list_prods']."</span></b></div>";
		echo "</td></tr>";
	}
	else
	{
		echo "<tr><td  colspan=\"2\" style='border-bottom:solid 1px #000; height:30px;' class='preto' align='center'><b>Não foram encontrados registos para a pesquisa efetuada!</b></td></tr>";
	}
	?>
	</table><?

}
?>