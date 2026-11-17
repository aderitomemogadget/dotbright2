<?php
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($voucher_id==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'vouchers',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('voucher_desconto_afeta',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE vouchers ADD voucher_desconto_afeta TINYINT(1) NOT NULL');



	$tem_voucher_prods = 0;
	if($voucher_id!=0)
	{
		// global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		// $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs3 = new DataObj;
		$sql = "Select * from vouchers WHERE voucher_id=$voucher_id";
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$voucher_id = $rs->db_data["voucher_id"];
			$voucher_title = $rs->db_data["voucher_title"];
			$voucher_type = $rs->db_data["voucher_type"];
			$voucher_type_discount = $rs->db_data["voucher_type_discount"];
			$voucher_dataini = $rs->db_data["voucher_dataini"];
			$voucher_datafim = $rs->db_data["voucher_datafim"];
			$voucher_usage = $rs->db_data["voucher_usage"];
			$voucher_code = $rs->db_data["voucher_code"];
			$voucher_prod_id = $rs->db_data["voucher_prod_id"];
			$voucher_cliente_id = $rs->db_data["voucher_cliente_id"];
			$voucher_discount = $rs->db_data["voucher_discount"];
			$voucher_partner_profit = $rs->db_data["voucher_partner_profit"];
			$voucher_acumulavel = $rs->db_data["voucher_acumulavel"];
			$voucher_desconto_afeta = $rs->db_data["voucher_desconto_afeta"];

			// echo $voucher_desconto_afeta;

			if($voucher_prod_id > 0)
			{

				$sql = "Select Cabecalho from products_lang where lang='".$admin_lang."' and ID_PAI='".$voucher_prod_id."'";
				$rs3->db_open($db_link,$DB_name,$sql);
				$video_title = stripslashes($rs3->db_data["Cabecalho"]);
				$rs3->db_close();
			}

			if($voucher_cliente_id > 0)
			{
				// $rs3 = new DataObj;
				$sql = "Select Nome,Apelido from clientes where ID='".$voucher_cliente_id."'";
				$rs3->db_open($db_link,$DB_name,$sql);
				$nome_cliente = stripslashes($rs3->db_data["Nome"].' '.$rs3->db_data["Apelido"]);
				$rs3->db_close();
			}

			if($voucher_desconto_afeta > 0)
			{
				// $rs3 = new DataObj;
				$sql = "SELECT promo_id FROM vouchers_products where promo_id='".$voucher_id."'";
				$rs3->db_open($db_link,$DB_name,$sql);
				if(!$rs3->EOF()) $tem_voucher_prods = $rs3->db_rows;
				$rs3->db_close();
			}

		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<script>
$(function(){ $('.result').hide();$('.result2').hide(); $('.fancybox3').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1000, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); },'afterClose': function() {
                               window.open('pagina.php?voucher_id=<?= $_GET["voucher_id"]?>&Dump=<?= date('YmdHis')?>','_top');
                             } }); return false;}); });

function get_cs_user()
{
	var t = $('.result').is(':visible');

	if(t==false)
	{
		$('#choose').html('<img src="del2.png" border="0" title="close box" />');
		$.get('get_prods.php?dp=<?=date("YmdHis")?>', function(data) {  $('.result').show("slow"); $('.result').html(data);});
	}
	else
	{
		$('#choose').html('<img src="config.png" border="0"  title="choose a products" />');
		$('.result').hide("slow");
	}
}
function get_cs_clientes()
{
	var t = $('.result2').is(':visible');

	if(t==false)
	{
		$('#choose2').html('<img src="del2.png" border="0" title="close box" />');
		$.get('get_clientes.php?dp=<?=date("YmdHis")?>', function(data) {  $('.result2').show("slow"); $('.result2').html(data);});
	}
	else
	{
		$('#choose2').html('<img src="config.png" border="0"  title="choose a client" />');
		$('.result2').hide("slow");
	}
}
function totext(div)
{
	$('#m_cliente_url').val($('#'+div).val());
	$('#voucher_prod_id').val($('#_'+div).val());
	$('#choose').html('<img src="config.png" border="0"  title="choose a product" />');
	$('.result').hide("slow");
}
function totext2(div)
{
	$('#m_cliente_url2').val($('#'+div).val());
	$('#voucher_cliente_id').val($('#_'+div).val());
	$('#choose2').html('<img src="config.png" border="0"  title="choose a client" />');
	$('.result2').hide("slow");
}
function search()
{
	$.get('get_prods.php?dp=<?=date("YmdHis")?>&pesquisa='+$('#search_by').val(), function(data) { $('.result').html(data);});
}
function search2()
{
	$.get('get_clientes.php?dp=<?=date("YmdHis")?>&pesquisa='+$('#search_by2').val(), function(data) { $('.result2').html(data);});
}
function empty()
{
	$('#m_cliente_url').val("");
	$('#voucher_prod_id').val(0);
	$('#choose').html('<img src="config.png" border="0"  title="choose a product" />');
	$('.result').hide("slow");
}
function empty2(){
	$('#m_cliente_url2').val("");
	$('#voucher_cliente_id').val(0);
	$('#choose2').html('<img src="config.png" border="0"  title="choose a client" />');
	$('.result2').hide("slow");
}
$(document).ready(function()
{
	$('#voucher_dataini').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:ss'
	});
	$('#voucher_datafim').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:ss'
	});
})
</script>

	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?voucher_id=<?php echo $voucher_id?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
			<input type="hidden" name="voucher_id" value="<?php echo $voucher_id;?>" />
			<input type="hidden" name="slashes" value="'" />
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
					<tr>
					<td valign="top">
						<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
						<tr>
							<td valign="middle"><a href="list.php" class="branco"> &#60; <?php echo write_lang('key_voucher_back_listvouchers','Voltar para a lista de vouchers')?></a></td>
						</tr>
						</table>
					</tr>
                    <tr>
						<td align="left" valign="top" class="preto">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr>

								<td width="200" >&nbsp;&nbsp;Código Aleatório:&nbsp;</td>
                                <td><div style="float:left"><input  type="text" name="voucher_code" style="WIDTH: 150px; " maxlength="5" value="<?php echo $voucher_code;?>" readonly="" class="box" /></div><div style="float:left;padding-left: 8px;padding-top: 8px;"><font color="#FF0000">(código com 6 caracteres, que é gerado automaticamente)</font></div></td>

							</tr>
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>

								<td width="200" >&nbsp;&nbsp;Código Manual:&nbsp;</td>
                                <td><input  type="text" name="voucher_pre" style="WIDTH: 100px" class="box" /></td>

							</tr>
                            <tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_title','Título do cupão')?>:&nbsp;</td>
                                <td><input  type="text" name="voucher_title" style="WIDTH: 500px" maxlength="255" value="<?php echo $voucher_title;?>" class="box" /></td>

							</tr>
                            <tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_type','Tipo de cupão')?>:&nbsp;</td>
								<td style="font-weight:bold"><?php echo write_lang('key_voucher_orderdiscount','Desconto na encomenda')?>&nbsp;&nbsp;<input name="voucher_type" type="radio" value="0"  onclick="$('#prod_voucher').hide();$('#clientdiscount').hide();$('#discount').show();" <?php if(!$voucher_type || $voucher_type==0){?>checked="checked"<?php }?> />&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo write_lang('key_voucher_productoffer','Oferta de produto')?>&nbsp;&nbsp;<input name="voucher_type" type="radio" value="1" <?php if($voucher_type==1){?>checked="checked"<?php }?> onclick="$('#prod_voucher').show();$('#clientdiscount').hide();$('#discount').hide();" />&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo "Desconto Cliente"?>&nbsp;&nbsp;<input name="voucher_type" type="radio" value="3" <?php if($voucher_type==3){?>checked="checked"<?php }?> onclick="$('#prod_voucher').hide();$('#clientdiscount').show();$('#discount').hide();" />&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo "Desconto Portes"?>&nbsp;&nbsp;<input name="voucher_type" type="radio" value="4" <?php if($voucher_type==4){?>checked="checked"<?php }?> onclick="$('#prod_voucher').hide();$('#clientdiscount').hide();$('#discount').show();" /></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							<div id="discount" style="<?php if(!$voucher_type || $voucher_type==0 || $voucher_type==4){?>display:block<?php }else{?>display:none<?php }?>">
                            <table width="100%" cellpadding=0 cellspacing=0 border=0>
                            <tr>
								<td width="200" >&nbsp;&nbsp;Tipo de desconto:&nbsp;</td>
								<td style="font-weight:bold">Desconto por Percentagem&nbsp;&nbsp;<input name="voucher_type_discount" type="radio" value="0"  onclick="$('#value_discount').hide();$('#perc_discount').show();" <?php if(!$voucher_type_discount || $voucher_type_discount==0){?>checked="checked"<?php }?> />&nbsp;&nbsp;|&nbsp;&nbsp;Desconto por valor&nbsp;&nbsp;<input name="voucher_type_discount" type="radio" value="1" <?php if($voucher_type_discount==1){?>checked="checked"<?php }?> onclick="$('#perc_discount').hide();$('#value_discount').show();" /></td>
							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Desconto Afeta:&nbsp;</td>
								<td style="font-weight:bold">
								Todos os Produtos&nbsp;&nbsp;<input name="voucher_desconto_afeta" type="radio" value="0"  onclick="$('#afeta_discount').hide();" <?php if(!$voucher_desconto_afeta || $voucher_desconto_afeta==0){?>checked="checked"<?php }?> />&nbsp;&nbsp;|&nbsp;&nbsp;
								Selecionar Familias/Marcas/Produtos&nbsp;&nbsp;<input name="voucher_desconto_afeta" type="radio" value="1" <?php if($voucher_desconto_afeta==1){?>checked="checked"<?php }?> onclick="$('#afeta_discount').show();" /></td>

							</tr>
							</table>

							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							<div id="afeta_discount" style="<?php if($voucher_desconto_afeta==1){?>display:block<?php }else{?>display:none<?php }?>">

							<?php if(isset($_GET['voucher_id']) && $_GET['voucher_id'] > 0){?>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Produtos afetados pelo Desconto:&nbsp;</td>
								<td style="font-weight:bold">
								<div class="boardbt fancybox3" href="avancada.php?ID_PAI=<?php echo $_GET['promo_id']?>&Desconto=<?php echo $DESCONTO?>">&nbsp;</div>
								<?php if($tem_voucher_prods > 0){?>
								<div id="products<?php echo $DESCONTOf?>" class="board" style="text-align:center"><?php echo '<a class="preto fancybox3" style="font-size:14px" href="avancada.php?ID_PAI='. $_GET['voucher_id'].'&Desconto='.$voucher_discount.'&prdcmp=1">Visualizar Produtos da Campanha<br><br></a><div>'.($tem_voucher_prods).' Produtos Configurados</div>';//echo $prods_list;?></div>
								<?php }else{?>
								<div id="products<?php echo $DESCONTOf?>" class="board" style="text-align:center"><?php echo '<a class="preto fancybox3" style="font-size:14px" href="avancada.php?ID_PAI='. $_GET['voucher_id'].'&Desconto='.$voucher_discount.'&prdcmp=1">Não foram configurados produtos!</a>';//echo $prods_list;?></div>
								<?php }?>
								</td>

							</tr>
							</table>
							<?php }else{ echo 'Clique em "Gravar Novo Registo" para poder inserir produtos neste voucher<br>';}?>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>

							<div id="perc_discount" style="<?php if($voucher_type_discount==0){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_orderdiscount','Desconto na encomenda')?>:&nbsp;</td>
								<td><div style="float:left;padding-right: 8px;padding-top: 8px;">%</div><div style="float:left"><input type="text" name="voucher_discount_perc" style="WIDTH: 150px" maxlength="5" value="<?php echo $voucher_discount;?>" class="box" /></div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							<div id="value_discount" style="<?php if($voucher_type_discount==1){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Desconto na encomenda:&nbsp;</td>
								<td><div style="float:left"><input type="text" name="voucher_discount_value" style="WIDTH: 150px" maxlength="5" value="<?php echo $voucher_discount;?>" class="box" /></div><div style="float:left;padding-left: 8px;padding-top: 8px;">&euro;</div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							</div>
							<div id="prod_voucher" style="<?php if($voucher_type==1){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_product','Produto do cupão')?>:&nbsp;</td>
								<td>
								<div style="width:240px;float:left;border:#000 0px solid"><input class="box" style="width:230px;background:#E6E6E6" type="text" name="m_cliente_url" id="m_cliente_url" readonly="" value="<?php echo $video_title?>" /><input type="hidden" name="voucher_prod_id" id="voucher_prod_id" value="<?php echo $voucher_prod_id?>" /></div><div style="width:150px;float:left;margin-top:-2px;position:relative"><div><a href="#" onclick="get_cs_user()" id="choose"><img src="config.png" border="0" title="choose a product" /></a></div><div style="position:absolute; height:330px;width:330px;overflow:auto;right:120px;border:#c0c0c0 1px solid;background:#ececec;padding:5px" class="result">&nbsp;</div></div>
								</td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							<div id="clientdiscount" style="<?php if($voucher_type==3){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Tipo de desconto:&nbsp;</td>
								<td style="font-weight:bold">Desconto por Percentagem&nbsp;&nbsp;<input name="voucher_type_discount3" type="radio" value="0"  onclick="$('#value_discount3').hide();$('#perc_discount3').show();" <?php if(!$voucher_type_discount || $voucher_type_discount==0){?>checked="checked"<?php }?> />&nbsp;&nbsp;|&nbsp;&nbsp;Desconto por valor&nbsp;&nbsp;<input name="voucher_type_discount3" type="radio" value="1" <?php if($voucher_type_discount==1){?>checked="checked"<?php }?> onclick="$('#perc_discount3').hide();$('#value_discount3').show();" /></td>

							</tr>
							</table>

							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>



							<div id="perc_discount3" style="<?php if($voucher_type_discount==0){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_orderdiscount','Desconto na encomenda')?>:&nbsp;</td>
								<td><div style="float:left;padding-right:8px;padding-top:8px">%</div><div style="float:left;"><input type="text" name="voucher_partner_discount_perc" style="WIDTH: 150px" maxlength="5" value="<?php echo $voucher_discount;?>" class="box" /></div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							<div id="value_discount3" style="<?php if($voucher_type_discount==1){?>display:block<?php }else{?>display:none<?php }?>">
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Desconto na encomenda:&nbsp;</td>
								<td><div style="float:left;"><input type="text" name="voucher_partner_discount_value" style="WIDTH: 150px" maxlength="5" value="<?php echo $voucher_discount;?>" class="box" /></div><div style="float:left;padding-left: 8px;padding-top: 8px;">&euro;</div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo "Cliente"?>:&nbsp;</td>
								<td>
								<div style="width:240px;float:left;border:#000 0px solid"><input class="box" style="width:230px;background:#E6E6E6" type="text" name="m_cliente_url2" id="m_cliente_url2" readonly="" value="<?php echo $nome_cliente?>" /><input type="hidden" name="voucher_cliente_id" id="voucher_cliente_id" value="<?php echo $voucher_cliente_id?>" /></div><div style="width:150px;float:left;margin-top:-2px;position:relative"><div><a href="#" onclick="get_cs_clientes()" id="choose2"><img src="config.png" border="0" title="choose a client" /></a></div><div style="position:absolute; height:330px;width:330px;overflow:auto;right:120px;border:#c0c0c0 1px solid;background:#ececec;padding:5px" class="result2">&nbsp;</div></div>
								</td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							</div>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_date_start','Inicio do cupão')?>:&nbsp;</td>
								<td style="font-weight:bold"><div style="width:130px"><input  type="text" name="voucher_dataini" id="voucher_dataini" style="WIDTH: 180px" value="<?php echo $voucher_dataini;?>" class="box" /></div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
                           <table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_date_end','Fim do cupão')?>:&nbsp;</td>
								<td style="font-weight:bold;text-align:left"><div style="width:130px"><input type="text" name="voucher_datafim" id="voucher_datafim" style="WIDTH: 180px" value="<?php echo $voucher_datafim;?>" class="box" /></div></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;<?php echo write_lang('key_voucher_usage','Uso do cupão')?>:&nbsp;</td>
								<td style="font-weight:bold"><?php echo write_lang('key_voucher_unlimited','Ilimitado')?>&nbsp;&nbsp;<input name="voucher_usage" type="radio" value="0" <?php if(!$voucher_usage || $voucher_usage==0){?>checked="checked"<?php }?> />&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo write_lang('key_voucher_onetimeonly','Apenas uma vez')?>&nbsp;&nbsp;<input name="voucher_usage" type="radio" value="1" <?php if($voucher_usage==1){?>checked="checked"<?php }?> /></td>

							</tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
							<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							</table>
							<table width="100%" cellpadding=0 cellspacing=0 border=0 >
                            <tr>

								<td width="200" >&nbsp;&nbsp;Acumulável:&nbsp;</td>
								<td style="font-weight:bold"><input name="voucher_acumulavel" type="checkbox" value="1" <?php if($voucher_acumulavel==1){?>checked="checked"<?php }?> /></td>

							</tr>
							</table>
						</td>
					</tr>
					</table>
                    <?php show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200"><?php show_fast_tools();?></td>
            </tr>
            </table>
		</form>
	</div>
<?php 	include "../layout/bottom.inc.php";?>