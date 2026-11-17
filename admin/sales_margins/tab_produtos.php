<?php
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

/*if(isset($_GET['descontoaccao']) && $_GET['descontoaccao']=='adicionar')
{
	$sql="INSERT INTO sales_margins_products (margin_id, PROD_ID) VALUES ('".$_GET['margin_id']."', 0)";
	$res = Runquery($db_link,$DB_name,$sql);
}
elseif(isset($_GET['descontoaccao']) && $_GET['descontoaccao']=='apagar')
{
	$sql="DELETE FROM sales_margins_products WHERE margin_id='".$_GET['margin_id']."'";
	$res = Runquery($db_link,$DB_name,$sql);
}*/

$REL = 0;
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs = new DataObj;
$sql =
		'
		SELECT
			promo_prod_id,
			margin_id,
			PROD_ID,
			FAMILIA,
			MARCA
		FROM
			 sales_margins_products
		WHERE
			margin_id="'.$_GET['margin_id'].'"
		';
$rs->db_open($db_link, $DB_name, $sql);
$total_desc = $rs->db_rows;

$fams_list = gera_lista_familias($_GET['margin_id'], 1, 0);
$marcas_list = gera_lista_marcas($_GET['margin_id'], 1, 0);
$prods_list = gera_lista_produtos($_GET['margin_id'], 1, 0);
?>
<form name="formpromocoes2" id="formpromocoes2" method="post" action="" enctype="multipart/form-data">

	<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td align="left">
				<?php
					$DESCONTOf = $VALOR;
					//echo "seleccionarfamilias.php?ID_PAI=".$_GET['margin_id']."&Desconto=".$DESCONTO;
				?>
				<div class="boardbt fancybox2" href="seleccionarfamilias.php?ID_PAI=<?php echo $_GET['margin_id']?>&Desconto=<?php echo $DESCONTO?>">Categorias</div>
				<div id="categories<?php echo $DESCONTOf?>" class="board"><?php echo $fams_list?></div>
			</td>
			</td>
			<td align="left">
				<div class="boardbt fancybox2" href="seleccionarmarcas.php?ID_PAI=<?php echo $_GET['margin_id']?>&Desconto=<?php echo $DESCONTO?>">Marcas</div>
				<div id="brands<?php echo $DESCONTOf?>" class="board"><?php echo $marcas_list?></div>
			</td>
			<td align="left">
				<div class="boardbt fancybox2" href="seleccionarprodutos.php?ID_PAI=<?php echo $_GET['margin_id']?>&Desconto=<?php echo $DESCONTO?>">Produtos</div>
				<div id="products<?php echo $DESCONTOf?>" class="board"><?php echo $prods_list?></div>
			</td>
		</tr>
		<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</form>