<?
function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1">
        	<input class="botao" type="submit" name="Submit" value=" <?php echo $task_button;?> ">
        	<input class="botao" type="button" name="cancelar" value=" <?php echo write_lang('key_cancelar','Cancelar')?> " onMouseUp="window.open('list.php','_self');">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}

function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_seleccionados','Com os selecionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?php echo write_lang('key_apagar','Apagar')?></option>
				<option value="2"><?php echo write_lang('key_mover_para_o_grupo','Mover para o Grupo')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_lista($Grupo_ID,"grupos_clientes","Grupo_ID");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools($list=null)
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


	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_criar_novo_cliente','Criar Novo Cliente')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?php echo uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Ver Grupos Clientes" onclick="javascript:window.open('../grupos_clientes/list.php','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_criar_novo_grupo','Criar Novo Grupo')?>" onclick="javascript:window.open('../grupos_clientes/pagina.php?ID=0&ID_ORG=0&Dump=<?php echo uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_exportar_listagem','Exportar Listagem')?>" onclick="javascript:window.open('form_records_exporta2.php?Dump=<?php echo uniqid("");?>&m_pesquisa=<?php echo $_GET['m_pesquisa']?>&filt1=<?php echo $_GET['filt1']?>&filt2=<?php echo $_GET['filt2'].$filt_p?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<br><?php
	if($_GET['filt1']!=7){?>
	<table width="200px" border=0 cellspacing=1 cellpadding=1>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op1_data" <? if($_GET['op1_data']==1) echo 'checked=""';?>> REGISTADOS ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data1_ini" value="<?php echo ((isset($_GET["m_data1_ini"]) && $_GET["m_data1_ini"] !='') ? $_GET["m_data1_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data1_fim" value="<?php echo ((isset($_GET["m_data1_fim"]) && $_GET["m_data1_fim"] !='') ? $_GET["m_data1_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK" src="../images/btp.jpg" class="botao" style="width:100%;height:20px" /></td>
	</tr>
	</table>
	<br><?php }?>
	<table width="200px" border=0 cellspacing=1 cellpadding=1>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op2_data" <? if($_GET['op2_data']==1) echo 'checked=""';?>> ENCOMENDARAM ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data2_ini" value="<?php echo ((isset($_GET["m_data2_ini"]) && $_GET["m_data2_ini"] !='') ? $_GET["m_data2_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data2_fim" value="<?php echo ((isset($_GET["m_data2_fim"]) && $_GET["m_data2_fim"] !='') ? $_GET["m_data2_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK" src="../images/btp.jpg" class="botao" style="width:100%;height:20px" /></td>
	</tr>
	</table>
	<br><?php
	if($_GET['filt1']!=7){?>
	<table width="200px" border=0 cellspacing=1 cellpadding=1>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op3_data" <? if($_GET['op3_data']==1) echo 'checked=""';?>> INSCRITOS NEWS ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data3_ini" value="<?php echo ((isset($_GET["m_data3_ini"]) && $_GET["m_data3_ini"] !='') ? $_GET["m_data3_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data3_fim" value="<?php echo ((isset($_GET["m_data3_fim"]) && $_GET["m_data3_fim"] !='') ? $_GET["m_data3_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK" src="../images/btp.jpg" class="botao" style="width:100%;height:20px" /></td>
	</tr>
	</table>
	<br><?php }?>
	<table width="200px" border=0 cellspacing=1 cellpadding=1>
    <tr>
		<td class="titulo2" align="center"><input value="1" type="checkbox" name="op4_data" <? if($_GET['op4_data']==1) echo 'checked=""';?>> NASCEU ENTRE</td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data4_ini" value="<?php echo ((isset($_GET["m_data4_ini"]) && $_GET["m_data4_ini"] !='') ? $_GET["m_data4_ini"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
    <tr>
		<td class=preto align="center"><input type="text" name="m_data4_fim" value="<?php echo ((isset($_GET["m_data4_fim"]) && $_GET["m_data4_fim"] !='') ? $_GET["m_data4_fim"] : date('Y-m-d'))?>" class="pesq dropdata" style="height:20px;width:100px" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input type="submit" value="OK" src="../images/btp.jpg" class="botao" style="width:100%;height:20px" /></td>
	</tr>
	</table>
	<!-- <tr>

		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center">
		<?
		if($list)
		{
			$clean_list=array();
			$clean_list=array_unique($list);
			$i=0;
			$ID=array();
			foreach ($clean_list as $key => $value)
			{
			  if(!is_null($value))
			  {
				$ID[$i]=$value;
				$i++;
			  }
			}
			$ID_List=$ID;

			if(count($ID)>0)
			{ ?>
            <form name="export_all" method="post" action="form_records_exporta.php" target="_blank"/>
                <input type="submit" name="export" value="<?php echo write_lang('key_exportar_listagem','Exportar Listagem')?>" class="botao">
               <?
                if($ID_List)
				{
					foreach($ID_List as $value)
                	{
               		?>
                    	<input type="hidden" name="id_list[]" value="<?php echo $value;?>">
                	<?
                	}
				}
               	?>
            </form>
            <?
			}
			}?>
            </td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table> -->


<? }

function show_special_tools(){}
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
	<input type="hidden" name="filt1" value="<?php echo $_GET['filt1']?>">
	<input type="hidden" name="filt2" value="<?php echo $_GET['filt2']?>">
    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
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
		<td align="left" style="color:#FFF"  height="30" colspan="2">
			&nbsp;&nbsp;
			<input type="checkbox" name="filt3_compra" value="1" <?php if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1){ echo 'checked="checked"';}?> /> CONCLUÍRAM A COMPRA&nbsp;&nbsp;
			<input type="checkbox" name="filt3_n_conc" value="1" <?php if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1){ echo 'checked="checked"';}?> /> NÃO CONCLUÍRAM A COMPRA&nbsp;&nbsp;
			<input type="checkbox" name="filt3_voucher" value="1" <?php if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1){ echo 'checked="checked"';}?> /> USARAM VOUCHER&nbsp;&nbsp;
		</td>
	</tr>
	</table>

<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$and=''; $order=''; $and2=''; $and3='';
	$porpagina=20;
	$pagina=$_GET['pagina'];
	if(isset($_GET['m_pesquisa']) && $_GET['m_pesquisa']!='') $and=" WHERE ( c.Username like '%".$_GET['m_pesquisa']."%' OR  c.Email like '%".$_GET['m_pesquisa']."%' OR c.Nome like '%".$_GET['m_pesquisa']."%' OR c.Apelido like '%".$_GET['m_pesquisa']."%')";
	else $and=" WHERE 1=1";

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

 	$sql .= $and.' '.$and2.' '.$and3.' '.$and_loja.' '.$groupby.' '.$order;


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
		//echo $sql;
	}


	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
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
		$return='<table width="100%"  class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr><td><table class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
		if($pagina > 6)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina=1&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';

			$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
		}
		for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina='.$pi.'&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#84c6fe" align="center" >'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
		}
		if($n_pags!=$pagina)
		{
			$pgn='onclick=window.open("list.php?m_pesquisa='.urlencode($_GET['m_pesquisa']).'&pagina='.$n_pags.'&filt1='.$filt1.'&filt2='.$filt2.$filt_p.'","_self")';
			$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
		}
		$return .= '</tr></table></td></tr></table>';
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
	<table border=0 cellspacing=0 cellpadding=0 class="r1b">
		<tr>
			<td width="29" align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_1.jpg"></td>
            <?php if($_GET['filt1']!=7){?><td align="center" width=80>Data do registo</td>
			<td width="4" align="right"><img width="2" src="../images/s6_1.jpg"></td><?php }?>
            <td align="middle"><input type="text" name="m_pesquisa" value="<?php echo $_GET["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
            <td align="left"><input type="submit" value="OK" src="../images/btp.jpg" class="botao" style="height:20px" /></td>
            <td align="right">ORDENAR:</td>
            <td align="right">
            	<select name="filt2" onchange="submit()">
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
		$password=base64_decode($rs->db_data['Password']);
		$Visivel=$rs->db_data['Estado'];//registou-se
		$Visivel2=$rs->db_data['Ativo'];//recebeu email e confirmou registo
		$Afiliado=$rs->db_data['Afiliado'];
		$Data_registo=$rs->db_data['Data'];

		$Newsletter=($rs->db_data['Newsletter']==1) ? 'NEWS' : '---';
		$m_sexo='---';
		if($rs->db_data['Sexo']==1)$m_sexo='MASCULINO';
		if($rs->db_data['Sexo']==2)$m_sexo='FEMININO';
		//$m_sexo=$rs->db_data['Sexo'] ? 'MASCULINO' : 'FEMININO';
		$m_datanascimento=$rs->db_data['DataNascimento'];

		if(!$designacao) $designacao=$rs->db_data["Nome"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		$i=0;


		$hoje=strtotime(date('Y-m-d'));


		if($_GET['filt1']==7) // lista de encomendas
		{
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
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
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>



            <td align="center" width=80><?php echo $Data_registo?></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>

            <td valign="middle">&nbsp;<a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($designacao){echo htmlentities_b(stripslashes($designacao));} else echo "[Edit]";?></a></td>

            <td valign="middle" width="20"></td>

            <? if($Afiliado){?><td valign="middle" title="Afiliado" width="20"><img src="afiliado.png" border=0 align="absmiddle"></td> <? }?>

            <td align="center" width=80><?php echo $m_datanascimento?></td>
            <td align="center" width=80><?php echo $m_sexo?></td>
            <td align="center" width=80><?php echo $Newsletter?></td>

            <td width="29" align="center"><img src="<? if($Visivel || $Visivel2) echo "../images/ok.png"; else echo "../images/del.png";?>" border=0 align="absmiddle"></td>

            <?php if($Visivel || $Visivel2){?>
            	<td valign="middle" title="Autenticar-se como cliente" width="20" onclick="LoginCliente('<?php echo $username?>', '<?php echo $password?>')"><img style="cursor:pointer;" src="key.png" border=0 align="absmiddle"></td>
			<?php }else{?>
				<td valign="middle" width="20"></td>
			<? }?>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Cliente')?>" src="del2.png" border=0></a></td>
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
		#### Sérgio ####
		$list[]=$rs->db_data['ID'];
		#### Sérgio ####
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
	$m_nome=clean_var($_POST["m_nome"]);
	$m_apelido=clean_var($_POST["m_apelido"]);
	$m_empresa=clean_var($_POST["m_empresa"]);
	$m_morada=clean_var($_POST["m_morada"]);
	$m_codpos=clean_var($_POST["m_codpos"]);
	$m_pais=clean_var($_POST["m_pais"]);
	$m_telefone=clean_var($_POST["m_telefone"]);
	$m_email=clean_var($_POST["m_email"]);
	$m_newsletter=clean_var($_POST["m_newsletter"]);
	$m_newsletter_t=clean_var($_POST["m_newsletter_t"]);
	$m_contribuinte=clean_var($_POST["m_contribuinte"]);
	$m2_nome=clean_var($_POST["m2_nome"]);
	$m2_apelido=clean_var($_POST["m2_apelido"]);
	$m2_empresa=clean_var($_POST["m2_empresa"]);
	$m2_morada=clean_var($_POST["m2_morada"]);
	$m2_codpos=clean_var($_POST["m2_codpos"]);
	$m2_pais=clean_var($_POST["m2_pais"]);
	$m2_telefone=clean_var($_POST["m2_telefone"]);
	$m2_email=clean_var($_POST["m2_email"]);
	$m2_contribuinte=clean_var($_POST["m2_contribuinte"]);
	$m_ativo=clean_var($_POST["m_ativo"]);
	$m_sexo=clean_var($_POST["m_sexo"]);
	$m_datanascimento=clean_var($_POST["m_datanascimento"]);
	$m_nickname=clean_var($_POST["m_nickname"]);
	$m_phone=clean_var($_POST["m_phone"]);
	$m_carteira=clean_var($_POST["m_carteira"]);
	$m_totalgasto=clean_var($_POST["m_totalgasto"]);
	$Grupo_ID=clean_var($_POST["Grupo_ID"]);
	$m_linguagem=clean_var($_POST["m_linguagem"]);
	$Afiliado=clean_var($_POST["Afiliado"]);
	$Afiliado_Perc=clean_var($_POST["Afiliado_Perc"]);
	$Img_peq1["name"]=$_FILES['img_peq1']['name'];
	$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}

	if($_POST["m_password"])
	{
		$m_password=encripta(clean_var($_POST["m_password"]));

		$sql=
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
					Ativo,
					Estado,
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
					Afiliado_Perc
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
					"'.$m_ativo.'",
					"'.$m_ativo.'",
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
					"'.$Afiliado_Perc.'"
				)
			';


	}
	else
	{
	$sql=
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
					Ativo,
					Estado,
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
					Afiliado_Perc
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
					"'.$m_ativo.'",
					"'.$m_ativo.'",
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
					"'.$Afiliado_Perc.'"
				)
			';
	}
	// echo $sql;
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	else
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copy($Img_peq1["temp"],$GLOBALS['img_path'].$new_id."_1.".$img_ext);
	}
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$id=$_POST['ID'];
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
	$id=$_POST['ID'];

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
		$Img_peq1["name"]=$_FILES['img_peq1']['name'];
		$Img_peq1["temp"]=$_FILES['img_peq1']['tmp_name'];
		$altera_img=0; if($Img_peq1["name"]){$altera_img=1; $pos=strrpos($Img_peq1["name"],"."); $img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		if($_POST['tem_foto1'] && $_POST['Apagar_img1']){$ficheiro1=$id."_1.".$img_ext; unlink($GLOBALS['img_path'].$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img) copy($Img_peq1["temp"],$GLOBALS['img_path'].$id."_1.".$img_ext);
		$m_nome=clean_var($_POST["m_nome"]);
		$m_apelido=clean_var($_POST["m_apelido"]);
		$m_empresa=clean_var($_POST["m_empresa"]);
		$m_morada=clean_var($_POST["m_morada"]);
		$m_codpos=clean_var($_POST["m_codpos"]);
		$m_pais=clean_var($_POST["m_pais"]);
		$m_telefone=clean_var($_POST["m_telefone"]);
		$m_email=clean_var($_POST["m_email"]);
		$m_newsletter=clean_var($_POST["m_newsletter"]);
		$m_newsletter_t=clean_var($_POST["m_newsletter_t"]);
		$m_contribuinte=clean_var($_POST["m_contribuinte"]);
		$m2_nome=clean_var($_POST["m2_nome"]);
		$m2_apelido=clean_var($_POST["m2_apelido"]);
		$m2_empresa=clean_var($_POST["m2_empresa"]);
		$m2_morada=clean_var($_POST["m2_morada"]);
		$m2_codpos=clean_var($_POST["m2_codpos"]);
		$m2_pais=clean_var($_POST["m2_pais"]);
		$m2_telefone=clean_var($_POST["m2_telefone"]);
		$m2_email=clean_var($_POST["m2_email"]);
		$m2_contribuinte=clean_var($_POST["m2_contribuinte"]);
		$m_ativo=clean_var($_POST["m_ativo"]);
		$m_sexo=clean_var($_POST["m_sexo"]);
		$m_datanascimento=clean_var($_POST["m_datanascimento"]);
		$m_nickname=clean_var($_POST["m_nickname"]);
		$m_phone=clean_var($_POST["m_phone"]);
		$m_carteira=clean_var($_POST["m_carteira"]);
		$m_totalgasto=clean_var($_POST["m_totalgasto"]);
		$Grupo_ID=clean_var($_POST["Grupo_ID"]);
		$m_linguagem=clean_var($_POST["m_linguagem"]);
		$Afiliado=clean_var($_POST["Afiliado"]);
		$Afiliado_Perc=clean_var($_POST["Afiliado_Perc"]);
		if($_POST["m_password"])
		{
			$m_password=encripta(clean_var($_POST["m_password"]));
			$sql='UPDATE
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
							Ativo="'.$m_ativo.'",
							Estado="'.$m_ativo.'",
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
							Afiliado_Perc="'.$Afiliado_Perc.'"
						WHERE
							ID="'.$id.'"
					';
		}
		else
		{

			$sql='UPDATE
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
							Ativo="'.$m_ativo.'",
							Estado="'.$m_ativo.'",
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
							Afiliado_Perc="'.$Afiliado_Perc.'"
						WHERE
							ID="'.$id.'"
					';
					// echo $sql;
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
function show_lista_teamsb($id_master=0){}
function show_lista_teams2b($id_master=0){}
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
		echo '
		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		<table  border=0 cellspacing=0 cellpadding=2>
		<tr>';
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
	$sql_all="Select * from encomendas where ID_Cliente=$cli_id AND Estado > -1 order BY ID DESC";
	$sql="Select * from encomendas where ID_Cliente=$cli_id AND Estado > -1 ORDER BY ID DESC limit $from,$qtd";
	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())$total=$rs->db_rows;
	$rs->db_close();

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;?>
    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
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
        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
        <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td width="29" align="center"><img src="../images/page.png"></td>
            <td valign="middle" width="150" class="preto"><a title="editar encomenda" class="lista_links" href="../encomendas/pagina.php?ID=<?php echo $IDb;?>&Dump=<? echo uniqid("");?>"><?php echo $Data_Criacao;?></a></td>
            <td valign="middle" width="35" class="preto"><?php echo $IDb;?></td>
            <td valign="middle" width="150" class="preto"><? echo number_format($TotalDocSemIva,2,',','.');?> </td>
            <td valign="middle" width="100" class="preto"><?php echo $ID_Cliente;?> </td>
            <td class="preto"><?php echo $Contacto;?> </td>
            <td width="100px" align="right" class=preto><?php echo $m_estado?> </td>
            <td width="10px"> </td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
	show_paginacao_bk($total,$qtd);
}
?>