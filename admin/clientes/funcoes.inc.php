<?
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class="p1" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td align="center" valign="middle" class="t1">
        	<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
        	<input class="btn btn-secondary" type="button" name="cancelar" value=" <?=write_lang('key_cancelar','Cancelar')?> " onClick="window.open('list.php','_self');">
		</td>
     </tr>
	</table>
	<!-- <img src="../images/bsh.png" width="100%" height="30px"> -->
<?
}
function show_page_tools($filter=0)
{

	$filt_p='';

	if(isset($_GET['op1_data']))
	{
		$filt_p .= '&op1_data='.$_GET['op1_data'];
		$filt_p .= '&m_data1_ini='.$_GET['m_data1_ini'];
		$filt_p .= '&m_data1_fim='.$_GET['m_data1_fim'];
	}
	if(isset($_GET['op2_data']))
	{
		$filt_p .= '&op2_data='.$_GET['op2_data'];
		$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
		$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
	}
	if(isset($_GET['op3_data']))
	{
		$filt_p .= '&op3_data='.$_GET['op3_data'];
		$filt_p .= '&m_data3_ini='.$_GET['m_data3_ini'];
		$filt_p .= '&m_data3_fim='.$_GET['m_data3_fim'];
	}
	if(isset($_GET['op4_data']))
	{
		$filt_p .= '&op4_data='.$_GET['op4_data'];
		$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
		$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
	}

	if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
	{
		$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
	}
	if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
	{
		$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
	}
	if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
	{
		$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
	}



	?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">CLIENTES</h4>
        </div>
    </div>
	<style type="text/css">
		.pesq{padding:5px;}
	</style>
	<input type="hidden" name="filt1" value="<?php echo $_GET['filt1']?>">
	<input type="hidden" name="filt2" value="<?php echo $_GET['filt2']?>">
    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td align="left" height="30" style="color:#FFF">&nbsp;&nbsp;
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=0&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if(!$_GET['filt1']){?>style="text-decoration:none;color:#000;font-weight:bold;background:#FFF"<? }?>>TODOS REGISTADOS</a> |
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=1&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if($_GET['filt1']==1){?>style="text-decoration:none;color:#000;font-weight:bold;background:#FFF"<? }?>>REGISTADOS ATIVOS</a> |
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=2&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if($_GET['filt1']==2){?>style="text-decoration:none;color:#000;font-weight:bold;background:#FFF"<? }?>>REGISTADOS INATIVOS</a> |
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=5&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if($_GET['filt1']==5){?>style="text-decoration:none;color:#000;font-weight:bold;background:#FFF"<? }?>>REGISTADOS NA NEWSLETTER</a> |
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=6&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if($_GET['filt1']==6){?>style="text-decoration:underline"<? }?>>SÓ OS AFILIADOS</a> |
			<a href="list.php?m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=7&filt2=<?php echo $_GET['filt2'].$filt_p?>" class="branco" <? if($_GET['filt1']==7){?>style="text-decoration:underline"<? }?>>ENCOMENDAS SEM REGISTO</a>
		</td>
	</tr>
	<tr>
		<td align="left" style="color:#FFF"  height="30" colspan="2" class="preto">
			&nbsp;&nbsp;
			<input type="checkbox" name="filt3_compra" value="1" <?php if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1){ echo 'checked="checked"';}?> /> CONCLUÍRAM A COMPRA&nbsp;&nbsp;
			<input type="checkbox" name="filt3_n_conc" value="1" <?php if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1){ echo 'checked="checked"';}?> /> NÃO CONCLUÍRAM A COMPRA&nbsp;&nbsp;
			<input type="checkbox" name="filt3_voucher" value="1" <?php if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1){ echo 'checked="checked"';}?> /> USARAM VOUCHER&nbsp;&nbsp;
		</td>
	</tr>
	</table>

<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?=write_lang('key_com_os_seleccionados','Com os selecionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?=write_lang('key_apagar','Apagar')?></option>
				<option value="2"><?=write_lang('key_mover_para_o_grupo','Mover para o Grupo')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_lista($Grupo_ID,"grupos_clientes","Grupo_ID");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?=write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}

function show_fast_tools()
{
	global $admin_id;
	$dados=getDadosAdministrador($admin_id);
	$filt_p='';
	if(isset($_GET['op1_data']) && isset($_GET['m_data1_ini']) && isset($_GET['m_data1_fim']))
	{
		$filt_p .= '&op1_data='.$_GET['op1_data'];
		$filt_p .= '&m_data1_ini='.$_GET['m_data1_ini'];
		$filt_p .= '&m_data1_fim='.$_GET['m_data1_fim'];
	}
	if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))
	{
		$filt_p .= '&op2_data='.$_GET['op2_data'];
		$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
		$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
	}
	if(isset($_GET['op3_data']) && isset($_GET['m_data3_ini']) && isset($_GET['m_data3_fim']))
	{
		$filt_p .= '&op3_data='.$_GET['op3_data'];
		$filt_p .= '&m_data3_ini='.$_GET['m_data3_ini'];
		$filt_p .= '&m_data3_fim='.$_GET['m_data3_fim'];
	}
	if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))
	{
		$filt_p .= '&op4_data='.$_GET['op4_data'];
		$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
		$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
	}
	if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
	{
		$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
	}
	if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
	{
		$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
	}
	if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
	{
		$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
	}

	?><table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_novo_cliente','Criar Novo Cliente')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Ver Grupos Clientes" onclick="javascript:window.open('../grupos_clientes/list.php','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_novo_grupo','Criar Novo Grupo')?>" onclick="javascript:window.open('../grupos_clientes/pagina.php?ID=0&ID_ORG=0&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_exportar_listagem','Exportar Listagem')?>" onclick="javascript:window.open('form_records_exporta2.php?Dump=<?php echo uniqid("");?>&m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=<?php echo $_GET['filt1']?>&filt2=<?php echo $_GET['filt2'].$filt_p?>','_self')"></td>
	</tr>
	<?php
	if($_GET['filt1']!=7){?>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op1_data" <? if($_GET['op1_data']==1) echo 'checked=""';?>> REGISTADOS ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data1_ini" id="m_data1_ini" value="<?php echo ((isset($_GET["m_data1_ini"]) && $_GET["m_data1_ini"] !='') ? $_GET["m_data1_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data1_fim" id="m_data1_fim" value="<?php echo ((isset($_GET["m_data1_fim"]) && $_GET["m_data1_fim"] !='') ? $_GET["m_data1_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK"  class="btn btn-primary" style="width:50px" /></td>
	</tr>
	<?php }?>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op2_data" <? if($_GET['op2_data']==1) echo 'checked=""';?>> ENCOMENDARAM ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data2_ini" id="m_data2_ini" value="<?php echo ((isset($_GET["m_data2_ini"]) && $_GET["m_data2_ini"] !='') ? $_GET["m_data2_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data2_fim" id="m_data2_fim" value="<?php echo ((isset($_GET["m_data2_fim"]) && $_GET["m_data2_fim"] !='') ? $_GET["m_data2_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK"  class="btn btn-primary" style="width:50px" /></td>
	</tr>
	<?php
	if($_GET['filt1']!=7){?>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op3_data" <? if($_GET['op3_data']==1) echo 'checked=""';?>> INSCRITOS NEWS ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data3_ini" id="m_data3_ini" value="<?php echo ((isset($_GET["m_data3_ini"]) && $_GET["m_data3_ini"] !='') ? $_GET["m_data3_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data3_fim" id="m_data3_fim" value="<?php echo ((isset($_GET["m_data3_fim"]) && $_GET["m_data3_fim"] !='') ? $_GET["m_data3_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK"  class="btn btn-primary" style="width:50px" /></td>
	</tr>
	<?php }?>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op4_data" <? if($_GET['op4_data']==1) echo 'checked=""';?>> NASCEU ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data4_ini" id="m_data4_ini" value="<?php echo ((isset($_GET["m_data4_ini"]) && $_GET["m_data4_ini"] !='') ? $_GET["m_data4_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="form-control" type="text" name="m_data4_fim" id="m_data4_fim" value="<?php echo ((isset($_GET["m_data4_fim"]) && $_GET["m_data4_fim"] !='') ? $_GET["m_data4_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px;float:left;margin-left:50px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK"  class="btn btn-primary" style="width:50px" /></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<script>
		$(function()
		{
			$('#m_data1_ini').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data1_fim').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data2_ini').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data2_fim').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data3_ini').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data3_fim').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data4_ini').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});
			$('#m_data4_fim').datetimepicker({showOn:"button", buttonImage:"<?php echo $GLOBALS['site_http_url']?>external_api/datepicker/images/calendar.png", buttonImageOnly:true, dateFormat:'yy-mm-dd'});

		});
	</script><?
}
function show_special_tools()
{
	global $ID,$ID_ORG;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">PLAYER INFO</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="PLAYER TEAMS" onclick="javascript:window.open('pagina.php?teams=1&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="PLAYER TOURNAMENTS" onclick="javascript:window.open('pagina.php?tournament=1&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="PLAYER GAMEIDS" onclick="javascript:window.open('pagina.php?gameids=1&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list($ID_PAI=0,$ID_instituicao=0,$ID_departamento=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;

	$and=''; $order=''; $and2=''; $and3='';
	$porpagina=20;
	$pagina=$_GET['pagina'];
	if(isset($_GET['m_pesquisa']) && $_GET['m_pesquisa']!='') $and=" WHERE ( c.Username like '%".$_GET['m_pesquisa']."%' OR  c.Email like '%".$_GET['m_pesquisa']."%' OR c.Nome like '%".$_GET['m_pesquisa']."%' OR c.Apelido like '%".$_GET['m_pesquisa']."%')";
	else $and=" WHERE 1=1";

	if($GLOBALS['tem_instituicoes']==1)
	{		
		if($ID_instituicao!=0)$and.=" AND ID_instituicao=$ID_instituicao ";
		if($ID_departamento!=0)$and.=" AND ID_departamento=$ID_departamento ";
	}

	$filt1=$_GET['filt1'];
	$filt2=$_GET['filt2'];

	// ESTADO/TIPO
	if(isset($_GET['filt1']) && $_GET['filt1']==1) $and .= ' and (c.Ativo=1 || c.Estado=1)';
	if(isset($_GET['filt1']) && $_GET['filt1']==2) $and .= ' and (c.Ativo=0 and c.Estado=0)';
	if(isset($_GET['filt1']) && $_GET['filt1']==5) $and .= ' and c.Newsletter=1';
	if(isset($_GET['filt1']) && $_GET['filt1']==6) $and .= ' and c.Afiliado=1';


	// OUTROS PARAMETROS
	$filt_p='';

	if(isset($_GET['op1_data']) && isset($_GET['m_data1_ini']) && isset($_GET['m_data1_fim']))
	{
		$and .= ' AND (DATE(c.Data) >= "'.$_GET['m_data1_ini'].'" AND DATE(c.Data) <= "'.$_GET['m_data1_fim'].'")';
		$filt_p .= '&m_data1_ini='.$_GET['m_data1_ini'];
		$filt_p .= '&m_data1_fim='.$_GET['m_data1_fim'];
	}
	if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))
	{
		$and2 .= ' AND (DATE(e.Data_Criacao) >= "'.$_GET['m_data2_ini'].' 00:00:00" AND DATE(e.Data_Criacao) <= "'.$_GET['m_data2_fim'].' 23:59:59")';
		$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
		$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
	}
	if(isset($_GET['op3_data']) && isset($_GET['m_data3_ini']) && isset($_GET['m_data3_fim']))
	{
		$and3 .= ' AND (DATE(nl.data_hora) >= "'.$_GET['m_data3_ini'].'" AND DATE(nl.data_hora) <= "'.$_GET['m_data3_fim'].'")';
		$filt_p .= '&m_data3_ini='.$_GET['m_data3_ini'];
		$filt_p .= '&m_data3_fim='.$_GET['m_data3_fim'];
	}
	if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))
	{
		$and .= ' AND (DATE(c.DataNascimento) >= "'.$_GET['m_data4_ini'].'" AND DATE(c.DataNascimento) <= "'.$_GET['m_data4_fim'].'")';
		$filt_p .= '&op4_data='.$_GET['op4_data'];
		$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
		$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
	}
	if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
	{
		$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
		$and2 .= ' AND (e.Estado=7)';
	}
	if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
	{
		$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
		$and2 .= ' AND (e.Estado!=7)';
	}
	if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
	{
		$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
		$and2 .= ' AND (el.voucher_id > 0)';
	}

	// ORDERNACAO
	if(!$_GET['filt2'] || $_GET['filt2']==0) $order='ORDER BY c.Nome ASC, c.Apelido ASC';
	elseif($_GET['filt2']==1) $order='ORDER BY c.Nome DESC, c.Apelido DESC';
	elseif($_GET['filt2']==2) $order='ORDER BY c.Data ASC';
	elseif($_GET['filt2']==3) $order='ORDER BY c.Data DESC';
	elseif($_GET['filt2']==4) $order='ORDER BY c.DataNascimento ASC';
	elseif($_GET['filt2']==5) $order='ORDER BY c.DataNascimento DESC';
	$groupby=' GROUP BY c.ID';


	$contador+=5;
	if(!$from)$from=0;
	$qtd=30;
	$sql='SELECT c.* FROM clientes c ';

	if($and2 != '') $sql .= ' INNER JOIN encomendas e ON e.ID_Cliente=c.ID INNER JOIN encomendas_produtos el ON el.ID_PAI=e.ID';
	if($and3 != '') $sql .= ' INNER JOIN nl_emails_registados nl ON c.Username=nl.Email';

 	$sql_count = $sql.$and.' '.$and2.' '.$and3;
 	$sql .= $and.' '.$and2.' '.$and3.' '.$groupby.' '.$order;


 	//Nao fizeram registo
	if(isset($_GET['filt1']) && $_GET['filt1']==7)
	{

		// OUTROS PARAMETROS
		$filt_p='';

		if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))
		{
			$and2 .= ' AND (DATE(e.Data_Criacao) >= "'.$_GET['m_data2_ini'].' 00:00:00" AND DATE(e.Data_Criacao) <= "'.$_GET['m_data2_fim'].' 23:59:59")';
			$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
			$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
		}
		if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))
		{
			$and2 .= ' AND (DATE(e.enc_DataNascimento) >= "'.$_GET['m_data4_ini'].'" AND DATE(e.enc_DataNascimento) <= "'.$_GET['m_data4_fim'].'")';
			$filt_p .= '&op4_data='.$_GET['op4_data'];
			$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
			$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
		}
		if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
		{
			$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
			$and2 .= ' AND (e.Estado=7)';
		}
		if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
		{
			$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
			$and2 .= ' AND (e.Estado!=7)';
		}
		if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
		{
			$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
			$and2 .= ' AND (el.voucher_id > 0)';
		}


		// ORDERNACAO
		if(!$_GET['filt2'] || $_GET['filt2']==0) $order='ORDER BY e.enc_Nome ASC, e.enc_Apelido ASC';
		elseif($_GET['filt2']==1) $order='ORDER BY e.enc_Nome DESC, e.enc_Apelido DESC';
		elseif($_GET['filt2']==2) $order='ORDER BY e.Data_Criacao ASC';
		elseif($_GET['filt2']==3) $order='ORDER BY e.Data_Criacao DESC';
		elseif($_GET['filt2']==4) $order='ORDER BY e.enc_DataNascimento ASC';
		elseif($_GET['filt2']==5) $order='ORDER BY e.enc_DataNascimento DESC';

		$groupby=' GROUP BY e.enc_Email';

		$sql='SELECT e.enc_Nome as Nome, e.enc_Apelido as Apelido,e.Data_Criacao as Data,e.enc_Email as Email,e.enc_DataNascimento as DataNascimento,e.enc_Sexo as Sexo  FROM encomendas e WHERE ID_Cliente=0 '.$and2.' '.$groupby.' '.$order;

	}


	$sql_count=str_replace('c.*',' COUNT(c.ID) as T ', $sql_count);

	$rs->db_open($db_link,$DB_name,$sql_count);
	$total=$rs->db_data['T'];
	$rs->db_close();

	$n_pags=ceil($total / $porpagina); // nº de paginas
	if(!$pagina) $pagina="1";

	$inicio=$pagina - 1;
	$inicio=$inicio * $porpagina;
	//echo '<font color="#FFFFFF">'.$sql.'</font>';
	$sql2=$sql." Limit $inicio,$porpagina";



	$rs->db_open($db_link,$DB_name,$sql2);

	
	if($n_pags > 1)
	{
		$return='<div class="btn-group">';
		if($pagina > 6)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina=1&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';

			$return .= '<button type="button" class="btn btn-white" '.$pgn.'>1... </button>';
		}
		for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina='.$pi.'&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<button type="button" class="btn btn-white active">'.sprintf($pi).'</button>' : '<button type="button" class="btn btn-white" '.$pgn.'>'.sprintf($pi).'</button>' :'';
		}
		if($n_pags!=$pagina)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina='.$n_pags.'&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';
			$return .= '<button type="button" class="btn btn-white" '.$pgn.'> ...'.sprintf($n_pags).'</button>';
		}
		$return .= '</div>';
	}


	$i=0;

	?>
	<style>
	TABLE.r1b {
	    width: 100%;
	    background-color: #75C0DE;
	    border-bottom: 1px solid #A8C9D7;
	    height: 27px;
	}</style>
	<table border=0 cellspacing=0 cellpadding=5 class="r1b">
		<tr>
			<td width="29" align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>

            <?php if($_GET['filt1']!=7){?><td align="center" width=80 class="preto">Data do registo</td>
			<?php }?>
            <td align="middle"><input type="text" name="m_pesquisa" value="<?php echo $_GET["m_pesquisa"]?>" class="form-control" style="margin-left:5px;margin-right:5px"></td>
            <td align="left"><input type="submit" value="Pesquisar" class="btn btn-primary" /></td>
            <td align="right" class="preto">ORDENAR:</td>
            <td align="right">
            	<select name="filt2" onchange="submit()" class="form-control">
            		<option value="" <?  if(!$filt2) echo ' selected="" ';?>>A..Z</option>
            		<option value="1" <? if($filt2==1) echo ' selected="" ';?>>Z..A</option>
            		<option value="2" <? if($filt2==2) echo ' selected="" ';?>>DATA ASC</option>
            		<option value="3" <? if($filt2==3) echo ' selected="" ';?>>DATA DESC</option>
            		<option value="4" <? if($filt2==4) echo ' selected="" ';?>>DATA NASCIMENTO ASC</option>
            		<option value="5" <? if($filt2==5) echo ' selected="" ';?>>DATA NASCIMENTO DESC</option>
            	</select>
            </td>
            <td width="20"></td>
		</tr>
		</table>
	<?php

	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data['ID'];
		$Grupo_ID=$rs->db_data["Grupo_ID"];
		$designacao=$rs->db_data["Nome"].' '.$rs->db_data["Apelido"];
		$username=$rs->db_data['Username'];
		$password=$rs->db_data['Password'];
		$Visivel=$rs->db_data['Estado'];//registou-se
		$Visivel2=$rs->db_data['Ativo'];//recebeu email e confirmou registo
		$Afiliado=$rs->db_data['Afiliado'];
		$Data_registo=$rs->db_data['Data'];
		$testes=$rs->db_data['Testes'];


		if($GLOBALS['tem_instituicoes']==1)
		{
			$ID_instituicao=$rs->db_data['ID_instituicao'];

			$sql="SELECT * FROM instituicoes WHERE ID=$ID_instituicao";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$instituicao=$rs2->db_data['Descricao'];
			}
		}
        if($GLOBALS['tem_departamentos']==1)
        {
        	$ID_departamento=$rs->db_data['ID_departamento'];

        	$sql="SELECT * FROM departamentos WHERE ID=$ID_departamento";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$departamento=$rs2->db_data['Descricao'];
			}
        }

        if($GLOBALS['tem_users_varios_niveis']==1)
        {
        	$Privilegios=$rs->db_data['Privilegios'];

			if(!$Privilegios || $Privilegios==0) $Privilegio_nome='User';
			if($Privilegios==1) $Privilegio_nome='User Creator';
			if($Privilegios==2) $Privilegio_nome='Admin Creator e User Creator';
			if($Privilegios==3) $Privilegio_nome='Admin Viewer only';
        }



		$Newsletter=($rs->db_data['Newsletter']==1) ? 'NEWS' : '---';
		$m_sexo='---';
		if($rs->db_data['Sexo']==1)$m_sexo='MASCULINO';
		if($rs->db_data['Sexo']==2)$m_sexo='FEMININO';
		//$m_sexo=$rs->db_data['Sexo'] ? 'MASCULINO' : 'FEMININO';
		$m_datanascimento=$rs->db_data['DataNascimento'];

		if(!$designacao) $designacao=$rs->db_data["Nome"];
		if($count % 2 != 0)	{$cor="#DEF2FA";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;


		if($testes==1){$cor="#FFE2E2";}
		$hoje=strtotime(date('Y-m-d'));


		if($_GET['filt1']==7) // lista de encomendas
		{
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="background-color:<?php echo $cor;?>">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
            <td valign="middle">&nbsp;<? if($designacao){echo htmlentities_b(stripslashes($designacao));}?></td>
            <td valign="middle" width="20"></td>
            <td align="center" width=80><?php echo $m_datanascimento?></td>
            <td align="center" width=80><?php echo $m_sexo?></td>
            <td valign="middle" width="20"></td>
		</tr>
		</table><?
		}
		else
		{
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="background-color:<?php echo $cor;?>">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
            <td align="center" width=80 class="preto"><a href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&d=<?=uniqid('')?>"><?php echo $Data_registo?></a></td>
            <?php if($GLOBALS['tem_instituicoes']==1)
            {?>
				<td valign="middle" width=100 class="preto">&nbsp;<a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><?=$instituicao?></a></td><?
            }
            if($GLOBALS['tem_departamentos']==1)
            {?>
				<td valign="middle" width=100 class="preto">&nbsp;<a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><?=$departamento?></a></td>
            <? }
            if($GLOBALS['tem_users_varios_niveis']==1)
            {?>
				<td valign="middle" width=100 class="preto">&nbsp;<a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><?=$Privilegio_nome?></a></td>
            <? }?>

            <td valign="middle" class="preto">&nbsp;<a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Edit]";?></a></td>
            <? if($Afiliado){?><td valign="middle" title="Afiliado" width="20"><img src="afiliado.png" border=0 align="absmiddle"></td> <? }?>
            <td align="center" width=80 class="preto"><?php echo $m_datanascimento?></td>
            <td align="center" width=80 class="preto"><?php echo $m_sexo?></td>
            <td align="center" width=80 class="preto"><?php echo $Newsletter?></td>
            <td width="30" align="center"><img src="<? if($Visivel || $Visivel2) echo "../images/ok.png"; else echo "../images/del.png";?>" border=0 align="absmiddle"></td>
            <?php if($Visivel || $Visivel2){?>
            	<td width="30" align="center" title="Autenticar-se como cliente" onclick="LoginCliente('<?php echo $username?>', '<?php echo $password?>')"><img style="cursor:pointer;" src="key.png" border=0 align="absmiddle"></td>
			<?php }else{?>
				<td width="30" align="center"></td>
			<? }?>
			<td width="30" align="center" class=preto><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Cliente')?>" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		}
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
	echo $return;
}
function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from clientes WHERE ID IN($reg) order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data['ID'];
		$Sigla=$rs->db_data["Sigla"];
		$designacao=$rs->db_data["Nickname"]. " -- ".$rs->db_data["Nome"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<?php echo write_lang('key_editar_cliente','Editar Cliente')?>" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Edit]";?></a></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_cliente','Apagar Cliente')?>" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

function show_content_from_search2($f_data1,$f_data2)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from clientes WHERE Data BETWEEN '".$f_data1."' AND '".$f_data2."' order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data['ID'];
		$list[]=$rs->db_data['ID'];
		$Nome=$rs->db_data["Nome"];
		$Email=$rs->db_data["Email"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<?php echo write_lang('key_editar_email','Editar Email')?>" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Email){echo htmlentities_b(stripslashes($Email));} else echo "[Edit]";?></a></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_email','Apagar Email')?>" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
	return $list;
}

function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$m_nome = clean_var($_POST["m_nome"]);
	$m_apelido = clean_var($_POST["m_apelido"]);
	$m_empresa = clean_var($_POST["m_empresa"]);
	$m_morada = clean_var($_POST["m_morada"]);
	$m_codpos = clean_var($_POST["m_codpos"]);
	$m_pais = clean_var($_POST["m_pais"]);
	$m_telefone = clean_var($_POST["m_telefone"]);
	$m_email = clean_var($_POST["m_email"]);
	$m_newsletter = clean_var($_POST["m_newsletter"]);
	$m_newsletter_t = clean_var($_POST["m_newsletter_t"]);
	$club_dataini = clean_var($_POST["club_dataini"]);
	$club_datafim = clean_var($_POST["club_datafim"]);
	$m_contribuinte = clean_var($_POST["m_contribuinte"]);
	$m2_nome = clean_var($_POST["m2_nome"]);
	$m2_apelido = clean_var($_POST["m2_apelido"]);
	$m2_empresa = clean_var($_POST["m2_empresa"]);
	$m2_morada = clean_var($_POST["m2_morada"]);
	$m2_codpos = clean_var($_POST["m2_codpos"]);
	$m2_pais = clean_var($_POST["m2_pais"]);
	$m2_telefone = clean_var($_POST["m2_telefone"]);
	$m2_email = clean_var($_POST["m2_email"]);
	$m2_contribuinte = clean_var($_POST["m2_contribuinte"]);
	$m_ativo = clean_var($_POST["m_ativo"]);
	$m_estado = clean_var($_POST["m_estado"]);
	$testes = clean_var($_POST["testes"]);
	$m_sexo = clean_var($_POST["m_sexo"]);
	$m_datanascimento = clean_var($_POST["m_datanascimento"]);
	$m_nickname = clean_var($_POST["m_nickname"]);
	$m_phone = clean_var($_POST["m_phone"]);
	$m_carteira = clean_var($_POST["m_carteira"]);
	$m_totalgasto = clean_var($_POST["m_totalgasto"]);
	$Grupo_ID = clean_var($_POST["Grupo_ID"]);
	$m_linguagem = clean_var($_POST["m_linguagem"]);
	$Afiliado = clean_var($_POST["Afiliado"]);
	$Afiliado_Perc = clean_var($_POST["Afiliado_Perc"]);
	$cod_instituicao = clean_var($_POST["cod_instituicao"]);
	$ID_instituicao = clean_var($_POST["ID_instituicao"]);
	$ID_departamento = clean_var($_POST["ID_departamento"]);
	$Privilegios = clean_var($_POST["Privilegios"]);
	$Desconto = clean_var($_POST["Desconto"]);


	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}

	if($_POST["m_password"])
	{
		$m_password = encripta(clean_var($_POST["m_password"]));
		$sql =
			'
			INSERT INTO
				clientes
				(
					Username,
					Password,
					Nome,
					Apelido,
					Empresa,
					Morada,
					Codigo_Postal,
					Pais,
					Telefone,
					Email,
					Newsletter,
					Newsletter_Tipo,
					Nif,
					Data,
					Nome_ent,
					Apelido_ent,
					Empresa_ent,
					Morada_ent,
					Codigo_Postal_ent,
					Pais_ent,
					Telefone_ent,
					Email_ent,
					Nif_ent,
					Club_data_ini,
					Club_data_fim,
					Ativo,
					Estado,
					Testes,
					Sexo,
					DataNascimento,
					Nickname,
					img_ext,
					Phone,
					Carteira,
					TotalGasto,
					Grupo_ID,
					Language,
					Afiliado,
					Afiliado_Perc,
					cod_instituicao,
					ID_instituicao,
					ID_departamento,
					Privilegios,
					Desconto
				)
				VALUES
				(
					"'.$m_email.'",
					"'.$m_password.'",
					"'.$m_nome.'",
					"'.$m_apelido.'",
					"'.$m_empresa.'",
					"'.$m_morada.'",
					"'.$m_codpos.'",
					"'.$m_pais.'",
					"'.$m_telefone.'",
					"'.$m_email.'",
					"'.$m_newsletter.'",
					"'.$m_newsletter_t.'",
					"'.$m_contribuinte.'",
					"'.date("Y-m-d").'",
					"'.$m2_nome.'",
					"'.$m2_apelido.'",
					"'.$m2_empresa.'",
					"'.$m2_morada.'",
					"'.$m2_codpos.'",
					"'.$m2_pais.'",
					"'.$m2_telefone.'",
					"'.$m2_email.'",
					"'.$m2_contribuinte.'",
					"'.$club_dataini.'",
					"'.$club_datafim.'",
					"'.$m_ativo.'",
					"'.$m_estado.'",
					"'.$testes.'",
					"'.$m_sexo.'",
					"'.$m_datanascimento.'",
					"'.$m_nickname.'",
					"'.$img_ext.'",
					"'.$m_phone.'",
					"'.$m_carteira.'",
					"'.$m_totalgasto.'",
					"'.$Grupo_ID.'",
					"'.$m_linguagem.'",
					"'.$Afiliado.'",
					"'.$Afiliado_Perc.'",
					"'.$cod_instituicao.'",
					"'.$ID_instituicao.'",
					"'.$ID_departamento.'",
					"'.$Privilegios.'",
					"'.$Desconto.'"

				)
			';

	}
	else
	{
	$sql =
			'
			INSERT INTO
				clientes
				(
					Username,
					Nome,
					Apelido,
					Empresa,
					Morada,
					Codigo_Postal,
					Pais,
					Telefone,
					Email,
					Newsletter,
					Newsletter_Tipo,
					Nif,
					Data,
					Nome_ent,
					Apelido_ent,
					Empresa_ent,
					Morada_ent,
					Codigo_Postal_ent,
					Pais_ent,
					Telefone_ent,
					Email_ent,
					Nif_ent,
					Club_data_ini,
					Club_data_fim,
					Ativo,
					Estado,
					Testes,
					Sexo,
					DataNascimento,
					Nickname,
					img_ext,
					Phone,
					Carteira,
					TotalGasto,
					Grupo_ID,
					Language,
					Afiliado,
					Afiliado_Perc,
					cod_instituicao,
					ID_instituicao,
					ID_departamento,
					Privilegios,
					Desconto
				)
				VALUES
				(
					"'.$m_email.'",
					"'.$m_nome.'",
					"'.$m_apelido.'",
					"'.$m_empresa.'",
					"'.$m_morada.'",
					"'.$m_codpos.'",
					"'.$m_pais.'",
					"'.$m_telefone.'",
					"'.$m_email.'",
					"'.$m_newsletter.'",
					"'.$m_newsletter_t.'",
					"'.$m_contribuinte.'",
					"'.date("Y-m-d").'",
					"'.$m2_nome.'",
					"'.$m2_apelido.'",
					"'.$m2_empresa.'",
					"'.$m2_morada.'",
					"'.$m2_codpos.'",
					"'.$m2_pais.'",
					"'.$m2_telefone.'",
					"'.$m2_email.'",
					"'.$m2_contribuinte.'",
					"'.$club_dataini.'",
					"'.$club_datafim.'",
					"'.$m_ativo.'",
					"'.$m_estado.'",
					"'.$testes.'",
					"'.$m_sexo.'",
					"'.$m_datanascimento.'",
					"'.$m_nickname.'",
					"'.$img_ext.'",
					"'.$m_phone.'",
					"'.$m_carteira.'",
					"'.$m_totalgasto.'",
					"'.$Grupo_ID.'",
					"'.$m_linguagem.'",
					"'.$Afiliado.'",
					"'.$Afiliado_Perc.'",
					"'.$cod_instituicao.'",
					"'.$ID_instituicao.'",
					"'.$ID_departamento.'",
					"'.$Privilegios.'",
					"'.$Desconto.'"
				)
			';
	}

	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	else
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"],$GLOBALS['img_path'].$new_id."_1.".$img_ext);
	}
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$id = $_POST['ID'];
	$res=Runquery($db_link,$DB_name,"delete from clientes where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$id = $_POST['ID'];
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from clientes WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord();
	}
	else
	{
		$Img_peq1["name"] = $_FILES['img_peq1']['name'];
		$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
		$altera_img=0; if($Img_peq1["name"]){$altera_img=1; $pos=strrpos($Img_peq1["name"],"."); $img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		if($_POST['tem_foto1'] && $_POST['Apagar_img1']){$ficheiro1=$id."_1.".$img_ext; unlink($GLOBALS['img_path'].$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img) copia_segura($Img_peq1["temp"],$GLOBALS['img_path'].$id."_1.".$img_ext);
		$m_nome = clean_var($_POST["m_nome"]);
		$m_apelido = clean_var($_POST["m_apelido"]);
		$m_empresa = clean_var($_POST["m_empresa"]);
		$m_morada = clean_var($_POST["m_morada"]);
		$m_codpos = clean_var($_POST["m_codpos"]);
		$m_pais = clean_var($_POST["m_pais"]);
		$m_telefone = clean_var($_POST["m_telefone"]);
		$m_email = clean_var($_POST["m_email"]);
		$m_newsletter = clean_var($_POST["m_newsletter"]);
		$m_newsletter_t = clean_var($_POST["m_newsletter_t"]);
		$m_contribuinte = clean_var($_POST["m_contribuinte"]);
		$m2_nome = clean_var($_POST["m2_nome"]);
		$m2_apelido = clean_var($_POST["m2_apelido"]);
		$m2_empresa = clean_var($_POST["m2_empresa"]);
		$m2_morada = clean_var($_POST["m2_morada"]);
		$m2_codpos = clean_var($_POST["m2_codpos"]);
		$m2_pais = clean_var($_POST["m2_pais"]);
		$m2_telefone = clean_var($_POST["m2_telefone"]);
		$m2_email = clean_var($_POST["m2_email"]);
		$m2_contribuinte = clean_var($_POST["m2_contribuinte"]);
		$club_dataini = clean_var($_POST["club_dataini"]);
		$club_datafim = clean_var($_POST["club_datafim"]);
		$m_ativo = clean_var($_POST["m_ativo"]);
		$m_estado = clean_var($_POST["m_estado"]);
		$testes = clean_var($_POST["testes"]);
		$m_sexo = clean_var($_POST["m_sexo"]);
		$m_datanascimento = clean_var($_POST["m_datanascimento"]);
		$m_nickname = clean_var($_POST["m_nickname"]);
		$m_phone = clean_var($_POST["m_phone"]);
		$m_carteira = clean_var($_POST["m_carteira"]);
		$m_totalgasto = clean_var($_POST["m_totalgasto"]);
		$Grupo_ID = clean_var($_POST["Grupo_ID"]);
		$m_linguagem = clean_var($_POST["m_linguagem"]);
		$Afiliado = clean_var($_POST["Afiliado"]);
		$Afiliado_Perc = clean_var($_POST["Afiliado_Perc"]);
		$cod_instituicao = clean_var($_POST["cod_instituicao"]);
		$ID_instituicao = clean_var($_POST["ID_instituicao"]);
		$ID_departamento = clean_var($_POST["ID_departamento"]);
		$Privilegios = clean_var($_POST["Privilegios"]);
		$Desconto = clean_var($_POST["Desconto"]);

		if($_POST["m_password"])
		{
			$m_password = encripta(clean_var($_POST["m_password"]));
			$sql = 'UPDATE
							clientes
						SET
							Password="'.$m_password.'",
							Nome="'.$m_nome.'",
							Apelido="'.$m_apelido.'",
							Empresa="'.$m_empresa.'",
							Morada="'.$m_morada.'",
							Codigo_Postal="'.$m_codpos.'",
							Pais="'.$m_pais.'",
							Telefone="'.$m_telefone.'",
							Email="'.$m_email.'",
							Newsletter="'.$m_newsletter.'",
							Newsletter_Tipo="'.$m_newsletter_t.'",
							Nif="'.$m_contribuinte.'",
							Data_modify="'.date("Y-m-d").'",
							Nome_ent="'.$m2_nome.'",
							Apelido_ent="'.$m2_apelido.'",
							Empresa_ent="'.$m2_empresa.'",
							Morada_ent="'.$m2_morada.'",
							Codigo_Postal_ent="'.$m2_codpos.'",
							Pais_ent="'.$m2_pais.'",
							Telefone_ent="'.$m2_telefone.'",
							Email_ent="'.$m2_email.'",
							Nif_ent="'.$m2_contribuinte.'",
							Club_data_ini="'.$club_dataini.'",
							Club_data_fim="'.$club_datafim.'",
							Ativo="'.$m_ativo.'",
							Estado="'.$m_estado.'",
							Testes="'.$testes.'",
							Sexo="'.$m_sexo.'",
							DataNascimento="'.$m_datanascimento.'",
							Nickname="'.$m_nickname.'",
							img_ext="'.$img_ext.'",
							Phone="'.$m_phone.'",
							Carteira="'.$m_carteira.'",
							TotalGasto="'.$m_totalgasto.'",
							Grupo_ID="'.$Grupo_ID.'",
							Language="'.$m_linguagem.'",
							Afiliado="'.$Afiliado.'",
							Afiliado_Perc="'.$Afiliado_Perc.'",
							cod_instituicao="'.$cod_instituicao.'",
							ID_instituicao="'.$ID_instituicao.'",
							ID_departamento="'.$ID_departamento.'",
							Privilegios="'.$Privilegios.'",
							Desconto="'.$Desconto.'"

						WHERE
							ID="'.$id.'"
					';
		}
		else
		{
			$sql = 'UPDATE
							clientes
						SET
							Nome="'.$m_nome.'",
							Apelido="'.$m_apelido.'",
							Empresa="'.$m_empresa.'",
							Morada="'.$m_morada.'",
							Codigo_Postal="'.$m_codpos.'",
							Pais="'.$m_pais.'",
							Telefone="'.$m_telefone.'",
							Email="'.$m_email.'",
							Newsletter="'.$m_newsletter.'",
							Newsletter_Tipo="'.$m_newsletter_t.'",
							Nif="'.$m_contribuinte.'",
							Data_modify="'.date("Y-m-d").'",
							Nome_ent="'.$m2_nome.'",
							Apelido_ent="'.$m2_apelido.'",
							Empresa_ent="'.$m2_empresa.'",
							Morada_ent="'.$m2_morada.'",
							Codigo_Postal_ent="'.$m2_codpos.'",
							Pais_ent="'.$m2_pais.'",
							Telefone_ent="'.$m2_telefone.'",
							Email_ent="'.$m2_email.'",
							Nif_ent="'.$m2_contribuinte.'",
							Club_data_ini="'.$club_dataini.'",
							Club_data_fim="'.$club_datafim.'",
							Ativo="'.$m_ativo.'",
							Estado="'.$m_estado.'",
							Testes="'.$testes.'",
							Sexo="'.$m_sexo.'",
							DataNascimento="'.$m_datanascimento.'",
							Nickname="'.$m_nickname.'",
							img_ext="'.$img_ext.'",
							Phone="'.$m_phone.'",
							Carteira="'.$m_carteira.'",
							TotalGasto="'.$m_totalgasto.'",
							Grupo_ID="'.$Grupo_ID.'",
							Language="'.$m_linguagem.'",
							Afiliado="'.$Afiliado.'",
							Afiliado_Perc="'.$Afiliado_Perc.'",
							cod_instituicao="'.$cod_instituicao.'",
							ID_instituicao="'.$ID_instituicao.'",
							ID_departamento="'.$ID_departamento.'",
							Privilegios="'.$Privilegios.'",
							Desconto="'.$Desconto.'"
						WHERE
							ID="'.$id.'"
					';

		}
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Nome) as Descricao,length(lower(Nome)) AS len,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."'))/(length(lower(Nome))+1-length(replace(lower(Nome),' ','')) ) AS occwds,
	((length(Nome)-length(replace(lower(Nome),'".$d."','')))/length('".$d."')) AS occ
	FROM clientes
	WHERE (lower(Nome) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_lista_teamsb($id_master=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_shell_url,$m_http_url_images,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	if($id_master) $sql="SELECT teams.*,Texto2,Lang FROM teams INNER JOIN teams_lang ON teams_lang.ID_PAI=teams.ID WHERE Master_user_ID='$id_master' and Visivel=1 and Lang='$site_lang'";
	else $sql="SELECT teams.*,Texto2,Lang FROM teams INNER JOIN teams_lang ON teams_lang.ID_PAI=teams.ID WHERE Visivel=1 and Lang='$site_lang'";
	$rs->db_open($db_link,$DB_name,$sql);
	$c=0;
	if(!$rs->EOF())
	{
		echo '<table width="100%" border=0 cellpadding="0" cellspacing="0"><tr>';
		while(!$rs->EOF())
		{
			echo '<td width=10></td><td valign=top width=275>';
			$C=stripslashes($rs->db_data["Team"]);
			$Master_user_ID=stripslashes($rs->db_data["Master_user_ID"]);
			$T2=limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
			$sp_id=$rs->db_data['ID'];
			$I1=$rs->db_data['img_ext'];
			$URL_Link=$rs->db_data["URL_Link"];
			$pais=$rs->db_data["Pais"];
			$Game_ID=$rs->db_data["Game_ID"];

			$sql="SELECT * FROM games WHERE ID=$Game_ID AND Visivel=1";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$Game_name=stripslashes($rs2->db_data['Name']);
				$game_I1=$rs2->db_data['img_ext'];
				$game_img_file="files/games/".$Game_ID."_1.".$game_I1;
				$game_img_file_peq="files/games/".$Game_ID."_2b.".$game_I1;
				// 90 x 90
				if(file_exists($site_shell_url.$game_img_file)) $original =date("Y-m-d H:i:s",filemtime($site_shell_url.$game_img_file)); // data/hora em que foi modificada/criada
				if(file_exists($site_shell_url.$game_img_file_peq)) $peq =date("Y-m-d H:i:s",filemtime($site_shell_url.$game_img_file_peq)); //data/hora em que foi modificada/criada
				if((!file_exists($site_shell_url.$game_img_file_peq) || $peq<$original) && file_exists($site_shell_url.$game_img_file))
				{
					if(redimencionaImagem($site_shell_url,$game_img_file,15,15,$game_img_file_peq))cropImage($site_shell_url.$game_img_file_peq,$site_shell_url.$game_img_file_peq,15,15);
				}
			}
			$rs2->db_close();

			$img_file="files/teams/".$sp_id."_1.".$I1;
			$img_file_peq="files/teams/".$sp_id."_2.".$I1;
			// 90 x 90
			if(file_exists($site_shell_url.$img_file)) $original =date("Y-m-d H:i:s",filemtime($site_shell_url.$img_file)); // data/hora em que foi modificada/criada
			if(file_exists($site_shell_url.$img_file_peq)) $peq =date("Y-m-d H:i:s",filemtime($site_shell_url.$img_file_peq)); //data/hora em que foi modificada/criada
			if((!file_exists($site_shell_url.$img_file_peq) || $peq<$original) && file_exists($site_shell_url.$img_file))
			{
				if(redimencionaImagem($site_shell_url,$img_file,15,15,$img_file_peq))cropImage($site_shell_url.$img_file_peq,$site_shell_url.$img_file_peq,15,90);
			}
			?>
					<div class="image_border1">
                        <table width="90px" height="100px" cellpadding="0" cellspacing="1" border=0>
                        <tr>
    	                    <td style="text-align:center; background-color:#0e0e0e" valign="middle">
                            	<? if($I1!=""){?><a href="../teams/pagina.php?ID=<?=$sp_id?>"><img class="fade" src="<?=$m_http_url_images.'teams/'.$sp_id.'_2.'.$I1.'?d='.uniqid()?>" alt="" border=0></a><? }else{?>
                                <a href="../teams/pagina.php?ID=<?=$sp_id?>"><img class="fade" src="<?=$site_http_url.'layout/noimage2.png'?>" alt="" border=0></a><? }?>
                            </td>
                        </tr>
                        </table>
					</div>
                    <div class="box_team_area">
						<div class="box_team_name" style="cursor:pointer" onclick="javascript:window.open('../teams/pagina.php?ID=<?=$sp_id?>','_self')"><?
						if($game_I1!="" && file_exists($site_shell_url.'files/games/'.$Game_ID.'_2b.'.$game_I1))echo '<img align="absmiddle" title="'.$Game_name.'" class="fade" src="'.$m_http_url_images.'games/'.$Game_ID.'_2b.'.$game_I1.'?d='.uniqid().'" alt="" border=0>&nbsp;';
						?><?=$C?></div>
                    </div>
					</div>
		 <?
			$rs->db_next();
			$c++;
			echo '</td>';
			if($c==2)
			{
				echo '</tr>';
				$c=0;
			}
			else echo '</td>';
		}
		echo '</table>';
	}
	$rs->db_close();
}
function show_lista_teams2b($id_master=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_shell_url,$m_http_url_images;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	if(!$id_master) return false;
	$sql="Select * from teams_players where ID_Player=$id_master";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		echo '<table width="100%" cellpadding=10 cellspacing=0 border=0><tr><td class="titulo2" align="left">List of teams where he belong:</td></tr></table>';
		while(!$rs2->EOF())
		{
			$team_id=$rs2->db_data["ID_Team"];
			$sql="SELECT teams.*,Texto2,Lang FROM teams INNER JOIN teams_lang ON teams_lang.ID_PAI=teams.ID WHERE teams.ID='$team_id' and Visivel=1 and Lang='$site_lang'";
			$rs->db_open($db_link,$DB_name,$sql);
			$c=0;
			if(!$rs->EOF())
			{
				while(!$rs->EOF())
				{
					$C=stripslashes($rs->db_data["Team"]);
					$Master_user_ID=stripslashes($rs->db_data["Master_user_ID"]);
					$T2=limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
					$sp_id=$rs->db_data['ID'];
					$I1=$rs->db_data['img_ext'];
					$URL_Link=$rs->db_data["URL_Link"];
					$Bloked=$rs2->db_data["Bloked"];
					$img_file="files/teams/".$sp_id."_1.".$I1;
					$img_file_peq="files/teams/".$sp_id."_2.".$I1;
					// 90 x 90
					if(file_exists($site_shell_url.$img_file)) $original =date("Y-m-d H:i:s",filemtime($site_shell_url.$img_file)); // data/hora em que foi modificada/criada
					if(file_exists($site_shell_url.$img_file_peq)) $peq =date("Y-m-d H:i:s",filemtime($site_shell_url.$img_file_peq)); //data/hora em que foi modificada/criada
					if((!file_exists($site_shell_url.$img_file_peq) || $peq<$original) && file_exists($site_shell_url.$img_file))
					{
						if(redimencionaImagem($site_shell_url,$img_file,90,90,$img_file_peq))cropImage($site_shell_url.$img_file_peq,$site_shell_url.$img_file_peq,90,90);
					}
					?>
                    <div class="image_border1"><?
						if($I1!=""){?>
                        <table width="100px" height="100px" cellpadding="0" cellspacing="5" border=0>
                        <tr>
                        <td style="text-align:center; background-color:#0e0e0e" valign="middle"><a href="../teams/pagina.php?ID=<?=$sp_id?>"><img class="fade" src="<?=$m_http_url_images.'teams/'.$sp_id.'_2.'.$I1.'?d='.uniqid()?>" alt="" border=0></a></td>
                        </tr>
                        </table><? }else{?>
                        <table width="100px" height="100px" cellpadding="0" cellspacing="5" border=0>
                        <tr>
                        <td style="text-align:center; background-color:#0e0e0e" valign="middle"><a href="../teams/pagina.php?ID=<?=$sp_id?>"><img class="fade" src="<?=$site_http_url.'layout/noimage2.png'?>" alt="" border=0></a></td>
                        </tr>
                        </table><? }?>
					</div>
                    <div class="box_team_area">
                        <div class="box_team_name"><?=$C?></div>
                    </div>
                    <?
					$rs->db_next();
					$c++;
				}
			}
			$rs->db_close();
			$rs2->db_next();
		}
	}
	$rs2->db_close();
}
//Está em conflito esta funcao
function show_paginacao_bk($total,$qtd)
{
	global $from;
	$a=38;
	if($total>$qtd)
	{
		$pageURL=get_uri();
		if(substr_count($pageURL,"&from"))$pageURL=substr($pageURL,0,strpos($pageURL,"&from"));
		if(substr_count($pageURL,"?from")){$pageURL=substr($pageURL,0,strpos($pageURL,"?from"));}
		if(substr_count($pageURL,"?")>0)$op="&";else $op="?";
		$i=0;
		echo '<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		    <table  border=0 cellspacing=0 cellpadding=2>
			<tr>
				';

		echo "page ";
		while($i<($total/$qtd))
		{
			if($a==$a2)
			{
				echo "</tr><td colspan=\"50\"></td><tr>";
				$a2=0;
			}
			if($from==($i*$qtd)) echo "<td width=\"2\">&nbsp;</td><td class=\"t1\" style=\"text-align:center;background:#2b5779;color:#FFF\">".($i+1)."</td>";
			else echo "<td width=\"2\">&nbsp;</td><td class=\"t1\" onclick=\"window.open('".$pageURL.$op."from=".($i*$qtd)."','_self')\" style=\"text-align:center;background:#c0c0c0;cursor:pointer;text-decoration:underline;color:#000\">".($i+1)."</td>";
			$i++;
			$a2++;
		}
		echo "</tr></table>";
	}
}

function show_encomendas($cli_id)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$contador+=5;
	if(!$from)$from=0;
	$qtd=30;
	$sql_all="Select * from encomendas where ID_Cliente = $cli_id AND Estado > -1 order BY ID DESC";
	$sql="Select * from encomendas where ID_Cliente = $cli_id AND  Estado > -1 ORDER BY ID DESC limit $from,$qtd";
	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())$total=$rs->db_rows;
	$rs->db_close();
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;?>
    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
    <tr bgcolor="#6b9bb4">
        <td width="29"></td>
        <td valign="middle" width="150" class="preto">Data Criacao</td>
        <td valign="middle" width="35" class="preto">ID</td>
        <td valign="middle" width="150" class="preto">TotalDocComIva</td>
        <td valign="middle" width="100" class="preto">ID Cliente</td>
        <td class="preto">Contacto</td>
        <td width="100px" align="right" class=preto>Estado</td>
        <td width="10px"> </td>
    </tr>
    </table><?
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Data_Criacao=$rs->db_data["Data_Criacao"];
		$TotalDocSemIva=$rs->db_data["TotalDocComIva"];
		$ID_Cliente=$rs->db_data["ID_Cliente"];
		$Contacto=$rs->db_data["enc_Nome"].' '.$rs->db_data["enc_Apelido"];
		$Estado=$rs->db_data["Estado"];
		if(!$Contacto) $Contacto="N/A";
		if(!$Estado)$m_estado="Pendente";
		if($Estado==1)$m_estado="Aceite";
		if($Estado==2)$m_estado="Cancelada";
		if($Estado==3)$m_estado="Pagamento Aceite";
		if($Estado==4)$m_estado="Pagamento Cancelado";
		if($Estado==5)$m_estado="Pagamento Pendente";
		if($Estado==6)$m_estado='Em Expedição';
		if($Estado==7)$m_estado='Concluída';
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;

		?>
        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
        <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><img src="../images/page.png"></td>
            <td valign="middle" width="150" class="preto"><a title="editar encomenda" class="lista_links" href="../encomendas/pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><?=$Data_Criacao;?></a></td>
            <td valign="middle" width="35" class="preto"><?=$IDb;?></td>
            <td valign="middle" width="150" class="preto"><? echo number_format($TotalDocSemIva,2,',','.');?> </td>
            <td valign="middle" width="100" class="preto"><?=$ID_Cliente;?> </td>
            <td class="preto"><?=$Contacto;?> </td>
            <td width="100px" align="right" class=preto><?=$m_estado?> </td>
            <td width="10px"> </td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
	show_paginacao_bk($total,$qtd);
}


function loadLivroEnderecos_admin($id=0)
{
    if($id > 0) $dados = cliente_perfil($id); else return;


	$html .= '<div class="row"><div class="col-md-12">';
    $html .= '<div class="c"></div>
                <div class="row">
                    <div class="col-md-12">';
    if ($dados && $GLOBALS['tem_modulo_enderecos'] == 1)
	{
        if (isset($_GET['id_endereco']))
		{
            $idEndereco = intval($_GET['id_endereco']);
            if ($idEndereco > 0)
			{
                if (isset($_GET['apagar']))
				{
                    //obter a pagina de apagar
                    $html .= showFormLivroEnderecos_admin($idEndereco, 1, $dados);
                }
				else
				{
                    //obter a pagina de edicao
                    $html .= showFormLivroEnderecos_admin($idEndereco, 0, $dados);
                }
            }
            else
			{
                $html .= showFormLivroEnderecos_admin(0, 0, $dados);
            }
        }
		else
		{
            //obter a pagina de listagem
            $html .= showListLivroEnderecos_admin($dados);
            $html .= showFormLivroEnderecos_admin(0, 0, $dados);
        }
    }
    $html.='<br> <br>
                    </div>
                </div>
            </div>
        </div>';
    return $html;
}
function showFormLivroEnderecos_admin($idEndereco = 0, $apagar = 0, $dados=array())
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs_paises = new Dataobj();
    // $dados = cliente_logado();

   	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $action = 'pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $idEndereco;
	else $action = seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $idEndereco;

    if(isset($_GET['apagar']))
    {
        $action .= '&apagar=1';
    }

	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $action_back = 'pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1';
	else $action_back = seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1';

	if(isset($_GET['show_carteira']) && isset($_GET['id_endereco']) && isset($_GET['apagar']) && intval($_GET['show_carteira']) == 1 && intval($_GET['id_endereco']) > 0 && intval($_GET['apagar']) == 1) $html = '<h3 class="text-left">Apagar endereço</h3>';
	elseif(isset($_GET['show_carteira']) && isset($_GET['id_endereco']) && intval($_GET['show_carteira']) == 1 && intval($_GET['id_endereco']) > 0) $html = '<h3 class="text-left">Alterar endereço</h3>';
	elseif(isset($_GET['show_carteira']) && intval($_GET['show_carteira']) == 1) $html = '<h3 class="text-left">Criar novo endereço</h3>';

	$html .= '<div class="row">';
    $html .= '<div class="col-md-12">';
    $html .= '<script type="text/javascript">';
    $html .= "function validarEndereco() {
        var cor = '#ffffe5';
        var cor2 = '#FFFFFF';
        var campos_obrigatorios;
        campos_obrigatorios = new Array('m_nome', 'm_apelido', 'm_morada', 'm_telefone', 'm_codigo_postal', 'm_cidade', 'm_pais', 'm_contribuinte');

        var campos_preenchidos;
        var i = 0;
        while (i < campos_obrigatorios.length) {
            $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor2);
            i = i + 1;
        }
        i = 0;
        while (i < campos_obrigatorios.length) {
            if (document.forms['enderecoform'].elements[campos_obrigatorios[i]].value == \"\") {
                campos_preenchidos = 1;
                $('input[name=\"' + campos_obrigatorios[i] + '\"]').css('background-color', cor);
            }
            i = i + 1;
        }
        if (campos_preenchidos == 1) {
            Apprise('" . getLinguas('key_camposobr',$GLOBALS['site_lang']) . "');
            return false;
        }
        return true;
    }";
    $html .= '</script>';
    $html .= '<form method="post" onsubmit="return validarEndereco();" name="enderecoform" action="' . $action . '">';
    $titulo = 'ADICIONAR NOVO ENDEREÇO';
    $nomeBtn = 'GRAVAR';
    $showForm = 0;
    $nome = '';
    $apelido = '';
    $designacao = '';
    $morada = '';
    $telefone = '';
    $codigo_postal = '';
    $cidade = '';
    $contribuinte = '';
    $pais = 165;

    if($idEndereco == 0)
    {
        $nome = isset($_POST['m_nome']) ? $_POST['m_nome'] : '';
        $apelido = isset($_POST['m_apelido']) ? $_POST['m_apelido'] : '';
        // $designacao = isset($_POST['m_designacao']) ? $_POST['m_designacao'] : '';
        $morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : '';
        $telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : '';
        $codigo_postal = isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : '';
        $cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : '';
        $contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : '';
        $pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : 165;

        if (
            isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
            isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
            // isset($_POST['m_designacao']) && $_POST['m_designacao'] != '' &&
            isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
            isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
            isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
            isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
            isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
            isset($_POST['m_pais']) && $_POST['m_pais'] > 0
        )
        {
            $sql = "INSERT INTO clientes_enderecos(ID_Cliente, Nome, Apelido, Designacao, Morada, Codigo_Postal, Cidade, Pais, Telefone, Nif) VALUES ('{$dados['cli_id']}', '$nome', '$apelido', '$designacao', '$morada', '$codigo_postal', '$cidade', '$pais', '$telefone', '$contribuinte')";
            Runquery($db_link, $GLOBALS['DB_name'], $sql);
            $showForm = 2;

            $html .= '<script type="text/javascript">window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\');</script>';
            return $html;
        }
        else
        {
            $showForm = 3;
        }
    }
    else
    {
        $rs = getEnderecos($dados['cli_id'], 1, $idEndereco);
        if (!$rs->EOF())
        {

            $nome = isset($_POST['m_nome']) ? $_POST['m_nome'] : $rs->db_data['Nome'];
            $apelido = isset($_POST['m_apelido']) ? $_POST['m_apelido'] : $rs->db_data['Apelido'];
            $designacao = isset($_POST['m_designacao']) ? $_POST['m_designacao'] : $rs->db_data['Designacao'];
            $morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : $rs->db_data['Morada'];
            $telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : $rs->db_data['Telefone'];
            $codigo_postal = isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : $rs->db_data['Codigo_Postal'];
            $cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : $rs->db_data['Cidade'];
            $contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : $rs->db_data['Nif'];
            $pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : $rs->db_data['Pais'];

            if ($apagar == 1) {
                $titulo = 'APAGAR ENDEREÇO';
                $nomeBtn = 'CONFIRMAR APAGAR';
            } else {
                $titulo = 'EDITAR ENDEREÇO';
                $nomeBtn = 'ALTERAR';
            }

            if (
                isset($_POST['m_nome']) && $_POST['m_nome'] != '' &&
                isset($_POST['m_apelido']) && $_POST['m_apelido'] != '' &&
                //isset($_POST['m_designacao']) && $_POST['m_designacao'] != '' &&
                isset($_POST['m_morada']) && $_POST['m_morada'] != '' &&
                isset($_POST['m_telefone']) && $_POST['m_telefone'] != '' &&
                isset($_POST['m_codigo_postal']) && $_POST['m_codigo_postal'] != '' &&
                isset($_POST['m_cidade']) && $_POST['m_cidade'] != '' &&
                isset($_POST['m_contribuinte']) && $_POST['m_contribuinte'] != '' &&
                isset($_POST['m_pais']) && $_POST['m_pais'] > 0
            ) {
                if ($apagar == 1) {
                    $sql = "DELETE FROM clientes_enderecos WHERE ID_Cliente='{$dados['cli_id']}' AND ID='$idEndereco'";
                    Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    $showForm = 2;
                    $html .= '<script type="text/javascript">window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\');</script>';
                    return $html;

                } else {

                    $sql = "UPDATE clientes_enderecos SET ID_Cliente='{$dados['cli_id']}', Nome='$nome', Apelido='$apelido', Nif='$contribuinte', Designacao='$designacao', Morada='$morada', Codigo_Postal='$codigo_postal', Cidade='$cidade', Pais='$pais', Telefone='$telefone' WHERE ID='$idEndereco'";
                    Runquery($db_link, $GLOBALS['DB_name'], $sql);

                    /*$rs3 = getEnderecos($dados['cli_id'], 1);

                    if ($res && $rs3->db_data['ID'] == $idEndereco) {
                        $sql = "UPDATE clientes SET Empresa='$designacao', Morada='$morada', Codigo_Postal='$codigo_postal', Cidade='$cidade', Pais='$pais', Telefone='$telefone' WHERE ID='{$dados['cli_id']}'";
                        Runquery($db_link, $GLOBALS['DB_name'], $sql);
                    }*/
                    $showForm = 2;
                }
            } else {
                $showForm = 1;
            }
        }
        $rs->db_close();
    }


	if(isset($_GET['show_carteira']) && $_GET['show_carteira']==1)$showForm = 1;

    if ($showForm == 0)
	{
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">Endereço inexistente<br><br></div>';
        $html .= '<div class="col-md-12">';
        $html .= ' <div class="form-group text-center"><input type="button" value="&lt; VOLTAR" onclick="window.open(\'pagina.php?show_carteira=1\',\'_self\')" class="button3"></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
	elseif ($showForm == 1)
	{

        $html .= '<div class="subtitulo_d hidden">' . $titulo . '</div>';

        $html .= '<div class="row">';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_nome">'.getLinguas('key_nome').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_nome" name="m_nome" value="' . $nome . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_apelido">'.getLinguas('key_apelido').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_apelido" name="m_apelido" value="' . $apelido . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12 hidden">';
        $html .= '<div class="form-group"><label for="m_designacao">'.getLinguas('key_empresa').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_designacao" name="m_designacao" value="' . $designacao . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12">';
        $html .= '<div class="form-group"><label for="m_nome">'.getLinguas('key_morada').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text"class="form-control" id="m_morada" name="m_morada" value="' . $morada . '">';
        $html .= '</div>';
        $html .= '</div>';



        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_cidade">'.getLinguas('key_cidade').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_cidade" name="m_cidade" value="' . $cidade . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6"><label for="m_codigo_postal">'.getLinguas('key_codpos').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<div class="form-group">';
        $html .= '<input type="text" class="form-control" id="m_codigo_postal" name="m_codigo_postal" value="' . $codigo_postal . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_pais">'.getLinguas('key_pais').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<select class="form-control" name="m_pais" id="m_pais" data-live-search="true">';
        $sql = "SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='{$GLOBALS['site_lang']}' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
        $rs_paises->db_open($db_link, $GLOBALS['DB_name'], $sql);
        while (!$rs_paises->EOF())
        {
            $zone_name = $rs_paises->db_data['zone_name'];
            $zone_id = $rs_paises->db_data['zone_id'];
            $html .= '<option value="' . $zone_id . '" ';
            if ($pais == $zone_id)
            {
                $html .= 'selected="selected"';
            }
            $html .= '>' . $zone_name . '</option>';
            $rs_paises->db_next();
        }
        $rs_paises->db_close();
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group"><label for="m_contribuinte">'.getLinguas('key_nif').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_contribuinte" name="m_contribuinte" value="' . $contribuinte . '">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="col-md-12">';
        $html .= '<div class="form-group"><label for="m_telefone">'.getLinguas('key_tel').' (<span style="color:#FF0000;font-size:14px">*</span>)</label>';
        $html .= '<input type="text" class="form-control" id="m_telefone" name="m_telefone" value="' . $telefone . '">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

		if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))
		{

			$html .= ' <div class="form-group text-center"><input type="submit" value="' . $nomeBtn . '" class="button3"></div>';
			if(isset($_GET['show_carteira']))$html .= ' <div class="form-group text-center"><input type="button" value="VOLTAR À LISTAGEM DE MORADAS" class="btn btn-dotb" onclick="window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\')"></div>';
		}
		else
		{

			$html .= ' <div class="form-group text-center"><input type="button" value="VOLTAR" onclick="window.open(\''.$action_back. '\',\'_self\')" class="button3" style="margin-right:5px"><input type="submit" value="' . $nomeBtn . '" class="button3"></div>';
		}


    }
    elseif ($showForm == 3){
       $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= ' <div class="form-group text-center"><input type="button" value="INSERIR NOVA MORADA" onclick="window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco='.'\',\'_self\')" class="button3"></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    else {
       	//if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/')) $html .= '<script type="text/javascript">window.open(\'pagina.php?ID='.intval($_GET['ID']).'&tab=1\',\'_self\');</script>';
		//else $html .= '<script type="text/javascript">window.open(\'' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1\',\'_self\');</script>';
    }


    $html .= '</form>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function showListLivroEnderecos_admin($dados)
{
    $html = '<h3 class="text-left">Endereços</h3>';
    $rs = getEnderecos($dados['cli_id'], 2);
    if (!$rs->EOF())
	{
        $html .= '<ul class="list-enderecos">';
        while (!$rs->EOF())
		{

			$pais = country_name($rs->db_data['Pais']);

			// SE ESTIVER NO ADMIN
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))
			{
				$html .= '<li style="border-bottom:#333 1px solid;margin-bottom:10px">';

				$html .= '<div style="float:left;padding:10px;width:50%;box-sizing:border-box;font-size:15px">';
				$html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}, {$rs->db_data['Cidade']}<br>NIF: {$rs->db_data['Nif']}<br>TEL: {$rs->db_data['Telefone']}<br><br>";
				$html .= '</div>';
				$html .= '<div style="float:left;padding:10px;width:50%;box-sizing:border-box">';

				$html .= '<a class="preto" href="pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '" class="button3" style="margin-right:2px">Editar</a> | ';
				$html .= '<a class="preto" href="pagina.php?ID='.intval($_GET['ID']).'&tab=1&show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '&apagar=1" class="button3">Eliminar</a>';

				$html .= '</div><div style="clear:both"></div>';

				$html .= '</li>';
			}
			else
			{
				$html .= '<li style="border-bottom:#c0c0c0 1px solid;margin-bottom:10px">';
				$html .= '<div class="row" style="border:solid 1px #ccc">';
				$html .= '<div class="col-sm-7">';
				$html .= "{$rs->db_data['Nome']} {$rs->db_data['Apelido']}<br>{$rs->db_data['Morada']}<br>{$rs->db_data['Codigo_Postal']}, {$rs->db_data['Cidade']}<br>NIF: {$rs->db_data['Nif']}<br>TEL: {$rs->db_data['Telefone']}<br><br>";
				$html .= '</div>';
				$html .= '<div class="col-sm-5 text-right">';

				$html .= '<a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '" class="button3" style="color:#FFF;display:block;width:100px;text-align:center;margin:3px;padding:10px">Editar</a>';
				$html .= '<a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '?show_carteira=1&id_endereco=' . $rs->db_data['ID'] . '&apagar=1" class="button3" style="color:#FFF;display:block;width:100px;text-align:center;margin:3px;padding:10px">Eliminar</a>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</li>';
			}
            $rs->db_next();
        }
        $html .= '</ul>';
    }
    $rs->db_close();

	if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], 'admin/clientes/'))  $html .= '<div style="clear:both"></div><br>';

    return $html;
}
function show_logins($ID_PAI,$ID_instituicao=0,$ID_departamento=0)
{
	show_content_list($ID_PAI,$ID_instituicao,$ID_departamento);
}
function show_ficha_cliente($ID=0)
{?>
	Descubra aqui as suas medidas<br>
	1. Indique os tamanhos que usa habitualmente:<br>

	<div class="marg">
		<div class="f_txt">medida / tabela</div><div class="f_txt">alfabético</div><div class="f_txt">numérico</div><br>
		<div class="f_txt">superior</div><div class="f_txt">alfabético</div><div class="f_txt">numérico</div>
		<div class="f_txt">inferior</div><div class="f_txt">alfabético</div><div class="f_txt">numérico</div>
		<div class="f_txt">calçado</div><div class="f_txt">alfabético</div><div class="f_txt">numérico</div>
	</div>

<?}
?>