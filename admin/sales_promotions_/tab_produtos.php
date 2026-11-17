<?php
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

if(isset($_GET['descontoaccao']) && $_GET['descontoaccao']=='alterar')
{
	$sql="UPDATE sales_promotions_products SET DESCONTO='".$_POST['DESCONTO']."' WHERE promo_id='".$_GET['promo_id']."' AND DESCONTO='".$_POST['OldDESCONTO']."'";
	$res = Runquery($db_link,$DB_name,$sql);
}
elseif(isset($_GET['descontoaccao']) && $_GET['descontoaccao']=='adicionar')
{
	$sql="INSERT INTO sales_promotions_products (promo_id, DESCONTO, PROD_ID) VALUES ('".$_GET['promo_id']."', '".$_POST['DESCONTO']."', 0)";
	$res = Runquery($db_link,$DB_name,$sql);
}
elseif(isset($_GET['descontoaccao']) && $_GET['descontoaccao']=='apagar')
{
	$sql="DELETE FROM sales_promotions_products WHERE promo_id='".$_GET['promo_id']."' AND DESCONTO='".$_POST['OldDESCONTO']."'";
	$res = Runquery($db_link,$DB_name,$sql);
}


$REL = 0;
global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs = new DataObj;
$sql =
		'
		SELECT
			promo_prod_id,
			promo_id,
			DESCONTO,
			PROD_ID,
			FAMILIA,
			MARCA
		FROM
			 sales_promotions_products
		WHERE
			promo_id="'.$_GET['promo_id'].'"
		GROUP BY
			DESCONTO
		ORDER BY
			DESCONTO ASC
		';
$rs->db_open($db_link, $DB_name, $sql);
$total_desc = $rs->db_rows;

// echo 'total_desc:'.$total_desc.'<br />';

if(($TIPOCAMPANHA==2 && $total_desc<3) || ($TIPOCAMPANHA==7 && $total_desc<3) || $TIPOCAMPANHA==1)
{
?>
<form name="formpromocoes2" id="formpromocoes2" method="post" action="" enctype="multipart/form-data">
<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr>
<td align="left" colspan="5">
<table cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="right" align="left" width="150">Novo Desconto:&nbsp;</td>
<td><input type="text" name="DESCONTO" id="DESCONTO" class="form-control" value="0" style="width:150px" /></td>
<td width="2">&nbsp;</td>
<td><input class="btn btn-primary" type="submit" value="Adicionar Desconto" onclick="submitform2('pagina.php?promo_id=<?php echo $_GET['promo_id']?>&tab=3&descontoaccao=adicionar','formpromocoes2')" /></td>
</tr>
</table>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
</form>
<?php
}
elseif($TIPOCAMPANHA>2) // OFERTAS
{
$prods_list_offers = gera_lista_produtos2($_GET['promo_id'], 1, $TIPOCAMPANHA);
// $fams_list = gera_lista_familias($_GET['promo_id'], 1, 0);
// $marcas_list = gera_lista_marcas($_GET['promo_id'], 1, 0);
// $prods_list = gera_lista_produtos($_GET['promo_id'], 1, 0);
$DESCONTOf = 'offer';
?>
<form name="formpromocoes2" id="formpromocoes2" method="post" action="" enctype="multipart/form-data">
<?php if($TIPOCAMPANHA!=6){ ?>
<table width="100%" cellpadding="1" cellspacing="0" border="0">

<tr>
<td align="left" colspan="10">
<div class="boardbt" href="#" onclick="op_t('pesq_<?php echo $DESCONTOf?>')" style="position:relative; width:99%">Produto Ofertas</div>
<div style="position:relative">
<div style="position:absolute;top:5px;left:5px;width:794px;height:200px;background:#e9e9e9;display:none" id="pesq_<?php echo $DESCONTOf?>">
<div style="float:left;width:410px;text-align:center;margin-top:2px"><input type="text" onfocus="if(this.value=='Pesquisar Produto') this.value='';" name="pesquisa<?php echo $DESCONTOf?>" id="pesquisa<?php echo $DESCONTOf?>" class="form-control" value="Pesquisar Produto" style="width:400px;margin:0 auto" /></div>
<div style="float:left"><a href="#" onclick="pesquisa_prods('<?php echo $DESCONTOf?>','<?php echo $_GET['promo_id']?>','<?php echo $TIPOCAMPANHA;?>');return false;"><img src="img/reload.png" border="0" height="27" /></a></div>
<div class="clearboth"></div>
<div style="margin:10px; height:150px;overflow:auto" id="res_<?php echo $DESCONTOf?>">&nbsp;</div>
</div>
</div>
<div id="products_offer" class="board" style="width:99%"><?php echo $prods_list_offers;?></div>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<?php } ?>
<?php if($TIPOPROMOCAO!=3){?>
<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr>
<!--td align="left">
<?php $DESCONTOf = '';?>
<div class="boardbt fancybox2" href="seleccionarfamilias.php?ID_PAI=<?php // echo $_GET['promo_id']?>&Desconto=<?php // echo $DESCONTO?>">Categorias</div>
<div id="categories<?php //echo $DESCONTOf?>" class="board"><?php //echo $fams_list?></div>
</td>
<td align="left">
<div class="boardbt fancybox2" href="seleccionarmarcas.php?ID_PAI=<?php // echo $_GET['promo_id']?>&Desconto=<?php // echo $DESCONTO?>">Marcas</div>
<div id="brands<?php //echo $DESCONTOf?>" class="board"><?php //echo $marcas_list?></div>
</td-->
<td align="left">
<?php/* <div class="boardbt" href="#" onclick="op_t('pesq_<?php echo $DESCONTOf?>')" style="position:relative">Produtos</div> */?>
<div class="boardbt fancybox3" style="width: 99%;" href="avancada.php?ID_PAI=<?php echo $_GET['promo_id']?>&Desconto=<?php echo $DESCONTO?>">Produtos</div>
<div style="position:relative">
<div style="position:absolute;top:5px;left:5px;width:244px;height:200px;background:#e9e9e9;display:none" id="pesq_<?php echo $DESCONTOf?>">
<div style="float:left;width:210px;text-align:center;margin-top:2px"><input type="text" onfocus="if(this.value=='Pesquisar Produto') this.value='';" name="pesquisa<?php echo $DESCONTOf?>" id="pesquisa<?php echo $DESCONTOf?>" class="form-control" value="Pesquisar Produto" style="width:190px;margin:0 auto" /></div>
<div style="float:left"><a href="#" onclick="pesquisa_prods('<?php echo $DESCONTOf?>','<?php echo $_GET['promo_id']?>');return false;"><img src="img/reload.png" border="0" height="27" /></a></div>
<div class="clearboth"></div>
<div style="margin:10px; height:150px;overflow:auto" id="res_<?php echo $DESCONTOf?>">&nbsp;</div>
</div>
</div>

<?php/* <div id="products<?php echo $DESCONTOf?>" class="board"><?php echo $prods_list;?></div> */?>
<div id="products<?php echo $DESCONTOf?>" class="board" style="text-align:center;width:99%"><?php /* echo '<a class="preto fancybox3" style="font-size:14px" href="avancada.php?ID_PAI='. $_GET['promo_id'].'&Desconto='.$DESCONTO.'&prdcmp=1">Visualizar Produtos da Campanha</a>'; *///echo $prods_list;?>
<?php echo '<a class="preto fancybox3" style="font-size:14px;display:block;padding:10px;text-align:center" href="avancada.php?ID_PAI='. $_GET['promo_id'].'&Desconto='.$DESCONTO.'&prdcmp=1"><b>Configurar/Visualizar Produtos da Campanha</b></a>';?>
<div style="text-align:left;">
<?php echo gera_lista_produtosx($_GET['promo_id'], 1, $DESCONTO, 1, $_GET['promo_id'], $DESCONTO);?></div></div>
</td>
</tr>
<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<?php }?>
</form>
<?php
}
?>
<div id="info_declaracao">
<?php

if(!$rs->EOF() && ($TIPOCAMPANHA<3 || $TIPOCAMPANHA==7))
{
	while(!$rs->EOF())
	{
		$promo_prod_id=''; $DESCONTO = ''; $PROD_ID = '';$FAMILIA = ''; $MARCA = '';
		$promo_prod_id = $rs->db_data['promo_prod_id'];
		$DESCONTO = $rs->db_data['DESCONTO'];
		$DESCONTOf = str_replace('.','_',$rs->db_data['DESCONTO']);
		$PROD_ID = $rs->db_data['PROD_ID'];
		$FAMILIA = $rs->db_data['FAMILIA'];
		$MARCA = $rs->db_data['MARCA'];

		// $fams_list = gera_lista_familias($_GET['promo_id'], 1, $DESCONTO);
		// $marcas_list = gera_lista_marcas($_GET['promo_id'], 1, $DESCONTO);
		// $prods_list = gera_lista_produtos($_GET['promo_id'], 1, $DESCONTO);
		?>
		<form name="formpromocoes2_<?php echo $DESCONTOf?>" id="formpromocoes2_<?php echo $DESCONTOf?>" method="post" action="" enctype="multipart/form-data">
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
		<td align="left" colspan="5">
		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td align="right" align="left" width="100">Desconto:&nbsp;</td>
		<td><input type="text" name="DESCONTO" id="DESCONTO" class="form-control" value="<?php echo $DESCONTO?>" style="width:150px" /><input type="hidden" name="OldDESCONTO" id="OldDESCONTO" class="form-control" value="<?php echo $DESCONTO?>" /></td>
		<td width="2">&nbsp;</td>
		<td><input class="btn btn-primary" type="submit" value="Alterar Desconto" onclick="submitform2('pagina.php?promo_id=<?php echo $_GET['promo_id']?>&tab=3&descontoaccao=alterar','formpromocoes2_<?php echo $DESCONTOf?>')" /></td>
		<td width="2">&nbsp;</td>
		<td><input class="btn btn-danger" type="button" value="Remover" onclick="submitform2('pagina.php?promo_id=<?php echo $_GET['promo_id']?>&tab=3&descontoaccao=apagar','formpromocoes2_<?php echo $DESCONTOf?>')"></td>
		</tr>
		</table>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<!--td align="left">
		<div class="boardbt fancybox2" href="seleccionarfamilias.php?ID_PAI=<?php // echo $_GET['promo_id']?>&Desconto=<?php // echo $DESCONTO?>">Categorias</div>
		<div id="categories<?php // echo $DESCONTOf?>" class="board"><?php // echo $fams_list?></div>
		</td>
		<td align="left">
		<div class="boardbt fancybox2" href="seleccionarmarcas.php?ID_PAI=<?php // echo $_GET['promo_id']?>&Desconto=<?php // echo $DESCONTO?>">Marcas</div>
		<div id="brands<?php // echo $DESCONTOf?>" class="board"><?php // echo $marcas_list?></div>
		</td-->
		<td align="left">
		<div class="boardbt fancybox3" style="width: 99%;" href="avancada.php?ID_PAI=<?php echo $_GET['promo_id']?>&Desconto=<?php echo $DESCONTO?>">Categorias/Marcas/Produtos</div>
		<div style="position:relative">
		<div style="position:absolute;top:5px;left:5px;width:244px;height:200px;background:#e9e9e9;display:none" id="pesq_<?php echo $DESCONTOf?>">
		<div style="float:left;width:210px;text-align:center;margin-top:2px"><input type="text" onfocus="if(this.value=='Pesquisar Produto') this.value='';" name="pesquisa<?php echo $DESCONTOf?>" id="pesquisa<?php echo $DESCONTOf?>" class="form-control" value="Pesquisar Produto" style="width:190px;margin:0 auto" /></div>
		<div style="float:left"><a href="#" onclick="pesquisa_prods('<?php echo $DESCONTOf?>','<?php echo $_GET['promo_id']?>');return false;"><img src="img/reload.png" border="0" height="27" /></a></div>
		<div class="clearboth"></div>
		<div style="margin:10px; height:150px;overflow:auto" id="res_<?php echo $DESCONTOf?>">&nbsp;</div>
		</div>
		</div>

		<div id="products<?php echo $DESCONTOf?>" class="board" style="text-align:center;width: 99%;">
		<?php echo '<a class="preto fancybox3" style="font-size:14px;display:block;padding:10px;text-align:center" href="avancada.php?ID_PAI='. $_GET['promo_id'].'&Desconto='.$DESCONTO.'&prdcmp=1"><b>Configurar/Visualizar Produtos da Campanha</b></a>';?>
		<div style="text-align:left;">
		<?php echo gera_lista_produtosx($_GET['promo_id'], 1, $DESCONTO, 1, $_GET['promo_id'], $DESCONTO);?>
		</div>
		</div>
		</td>
		</tr>
		<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		</table>
		</form>
		<?php
		$rs->db_next();
	}
}
?>
</div>