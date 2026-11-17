<?php
function get_family_info_name($fam_id)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$fam_nome = '';
	if(isset($fam_id) && $fam_id > 0)
	{
		$sql = '
				SELECT
					PL.Cabecalho
				FROM
					products_families AS P
					INNER JOIN products_families_lang AS PL ON PL.ID_PAI=P.ID
				WHERE
					P.ID='.$fam_id.'
					AND PL.Lang="pt"
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			$fam_nome = $rs->db_data['Cabecalho'];
		}
		$rs->db_close();
	}
	return $fam_nome;
}
function getNomeMetodoPagamentoByTipo_bo($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;

	$metodo_pagamento = '';

	$sql="SELECT mpl.Cabecalho FROM metodos_pagamento mp inner join metodos_pagamento_lang mpl on mpl.ID_PAI=mp.ID WHERE (mp.ID=$tipo OR mp.Tipo=$tipo) AND mpl.Lang='".$admin_lang."'";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodo_pagamento = $rs2->db_data['Cabecalho'];
	}
	$rs2->db_close();
	return $metodo_pagamento;
}
function getNomeMetodoExpedicao_bo($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;

	$metodos_expedicao = '';

	$sql="SELECT mpl.Cabecalho FROM metodos_expedicao mp inner join metodos_expedicao_lang mpl on mpl.ID_PAI=mp.ID WHERE (mp.ID=$tipo) AND mpl.Lang='".$admin_lang."'";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodos_expedicao = $rs2->db_data['Cabecalho'];
	}
	$rs2->db_close();
	return $metodos_expedicao;
}

function show_page_buttons()
{
	global $task_button,$ID;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="5px"></td>
		<td align="left" valign="middle" class="t1">
			<input class="botao" type="button" name="cancelar" value=" Voltar " onMouseUp="window.open('list.php','_self')">
			<input class="botao" type="button" name="imprimir" value=" Imprimir " onMouseUp="javascript:Imprime('area_impressao');">
			<input class="botao" type="button" name="Vista Cliente" value=" Ver encomenda como o Cliente " onMouseUp="window.open('vistacliente.php?id_enc=<?php echo $ID?>','','left=-10,top=-10,width=1024,height=600,toolbar=0,scrollbars=1,status=0')">
			<input class="botao" type="button" name="Re-Enviar email a cliente" value=" Re-Enviar email a cliente " onMouseUp="window.open('list.php?notify=2&x_id=<?php echo $ID?>','_self')">
		</td>
	 </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_page_tools($filter=0)
{
	global $f_estado,$f_pagamento,$admin_lang,$DB_host,$DB_user,$DB_pass,$DB_name;?>
	<script>
	function update_status(n)
	{
		var url="list.php?x_id="+n;
		if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
		if(document.getElementById("m_guia_"+n)) url=url+"&x_guia="+document.getElementById("m_guia_"+n).value;
		window.open(url, "_self");
	}
	</script>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" height="50px" class="p1">
	<tbody><tr>
	<td width="5%" align="center">&nbsp;</td>
	<td width="95%" valign="middle" style="font-weight:bold;color:#FFF">ENCOMENDAS</td>
	</tr>
	</tbody></table>
	<table width="100%" border="0" cellspacing=0 cellpadding=2 class="p1">
	<tr>
		<td width="30" height="30" style="background:#FFFFFF"></td>
		<td width="4" style="background:#FFFFFF">&nbsp;</td>
		<td width="30" height="30" style="background:#FFFFFF">Fatura</td>
		<td width="70" class="text-center" style="background:#FFFFFF">Nº Enc</td>
		<td width="100" class="text-center" style="background:#FFFFFF">Data Ini</td>
		<td width="100" class="text-center" style="background:#FFFFFF">Data Fim</td>
		<?php /* <td width="80" class="text-center" style="background:#1e1e1e">Total c/Iva</td> */?>
		<td width="60" class="text-center" style="background:#FFFFFF">ID Cliente</td>
		<td class="text-center" style="background:#FFFFFF">Nome</td>
		<td width="80" class="text-center" style="background:#FFFFFF">Tipo Pagamento</td>
		<td width="90" class="text-center" style="background:#FFFFFF">Total</td>
		<td width="180" class="text-center" style="background:#FFFFFF">Estado</td>
		<td width="80" class="text-center" style="background:#FFFFFF">Tracking</td>
		<td width="30" height="30" style="background:#FFFFFF"></td>
	</tr>
	<tr>
		<td align="center" height="30" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb" /></td>
		<td style="background:#FFFFFF;border-bottom:1px solid #c0c0c0">&nbsp;</td>
		<td style="background:#FFFFFF;border-bottom:1px solid #c0c0c0">&nbsp;</td>
		<td class="branco" align="center" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_id" value="<?php echo (isset($_POST["f_id"]) ? $_POST["f_id"] : '');?>" style="width:50px" class="box"></td>
		<td class="branco" align="center" width="100" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_data1" id="f_data1" value="<?php echo (isset($_POST["f_data1"]) ? $_POST["f_data1"] : '');?>"  style="width:60px" class="box dropdate"></td>
		<td class="branco" align="center" width="100" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_data2" id="f_data2" value="<?php echo (isset($_POST["f_data2"]) ? $_POST["f_data2"] : '');?>"  style="width:60px" class="box dropdate"></td>
		<?php /* <td style="background:#FFFFFF;border-bottom:1px solid #c0c0c0">&nbsp;</td> */?>
		<td class="branco" align="center" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_idcli" value="<?php echo (isset($_POST["f_idcli"]) ? $_POST["f_idcli"] : '');?>" style="width:50px" class="box" /></td>
		<td class="branco" align="center" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_contacto" value="<?php echo (isset($_POST["f_contacto"]) ? $_POST["f_contacto"] : '');?>" style="width:95%" class="box" /></td>

		<td width="80" align="center" class="branco" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0">
		<?php
		$rsPagamentos = new DataObj;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql="SELECT metodos_pagamento.Tipo, metodos_pagamento_lang.Cabecalho FROM metodos_pagamento, metodos_pagamento_lang WHERE metodos_pagamento.ID = metodos_pagamento_lang.ID_PAI AND metodos_pagamento_lang.Lang='$admin_lang'";
		$db_res=$rsPagamentos->db_open($db_link,$DB_name,$sql);
		?>
		<select name="f_pagamento" style="width:150px;color:#000" class="chzn-select">
		<option value="">Todos</option>
		<?
		while(!$rsPagamentos->EOF())
		{
			$tipo_pag      = $rsPagamentos->db_data["Tipo"];
			$cabecalho_pag = $rsPagamentos->db_data["Cabecalho"];

			?><option value="<? echo $tipo_pag;?>"<? if($f_pagamento==$tipo_pag) echo 'selected="selected"';?>><?php echo $cabecalho_pag;?></option><?

			$rsPagamentos->db_next();
		}
		?>
		</select>
		</td>
		<td class="branco" align="center" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_total" value="<?php echo (isset($_POST["f_total"]) ? str_replace(',', '.', $_POST["f_total"]) : '');?>" style="width:95%" class="box" /></td>
		<td align="center" class="branco" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0">
		<select name="f_estado" style="width:100%" class="chzn-select">
		<option value="-1">Todos</option>
		<?php
		$rs2 = new DataObj;
		$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$admin_lang."' ORDER BY Ordem";
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$a=0;
			while(!$rs2->EOF())
			{
				$eel_id = $rs2->db_data['ID'];
				$Ordem = $rs2->db_data['Ordem'];
				$Cabecalho = $rs2->db_data['Cabecalho'];

				echo '<option value="'.$eel_id.'" '.($_POST["f_estado"]==$eel_id ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.'</option>';

				$a++;
				$rs2->db_next();
			}
		}
		$rs2->db_close();

		?>

		</select>
		</td>
		<td align="right" class="branco" style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><input type="text" name="f_guia" value="<?php echo (isset($_POST["f_guia"]) ? $_POST["f_guia"] : '');?>" style="width:95%" class="box" /></td>
		<td style="background:#FFFFFF;border-bottom:1px solid #c0c0c0"><a href="javascript:;" onclick="javascript:document.getElementById('from').value=0;document.forms['lista_paginas'].submit()"><img border="0" src="img/reload.png" /></a></td>
	</tr>

<? }
function show_page_bottom_tools()
{?>
	</table>

	<table width="100%"  style="background:#FFF">
		<tr>
		<td class="t1" height="35px">&nbsp;</td>
		 <td width="120">Com os selecionados:</td>
		 <td class="t1" >&nbsp;</td>
		 <td width="150">
		 <select name="flag" id="flag" class="chzn-select" style="width:150px">
			<option value="" selected="selected">Escolher</option>
			<option value="1">Apagar</option>
		</select>
		</td>
		<td class="t1">&nbsp;</td>
		<td align="left"><input type="submit"  name="submeter" value="Aplicar ação" class="botao" style="width:120px;"></td>
		</tr>
	</table>

	<br><br>
<?
}
function show_fast_tools()
{
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
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Volume de compras" onclick="javascript:window.open('../sa_pagamentos/list.php','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_content_list($reg=null, $limit='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$contador=5;
	if(!isset($_POST['from'])) $from=0;
	else $from = $_POST['from'];
	$qtd=30;

	if(!is_null($reg)) $sql_all="Select count(*) as Total,sum(TotalDocComIva) as TotalDocIvaTodas from encomendas where Estado > -1 and ID IN($reg) order by ID DESC";
	else $sql_all="Select count(*) as Total,sum(TotalDocComIva) as TotalDocIvaTodas from encomendas where Estado > -1 order by Data_Criacao Desc";

	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())
	{
		$TotalEncomendas=$total= $rs->db_data["Total"];
		$TotalDocIvaTodas	= $rs->db_data["TotalDocIvaTodas"];

	}
	$rs->db_close();


		if(!is_null($reg)) $sql="Select * from encomendas where Estado > -1 and ID IN($reg) order by Data_Criacao Desc limit $from,$qtd";
		else $sql="Select * from encomendas where Estado > -1 order by Data_Criacao Desc limit $from,$qtd";

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;$totalvalor=0;
	$count = 0;$cor='';
	if(!$rs->EOF()){
		while(!$rs->EOF())
		{
			$count++;
			$IDb				= $rs->db_data["ID"];
			$Data_Criacao		= $rs->db_data["Data_Criacao"];
			$TotalDocIva		= $rs->db_data["TotalDocComIva"];
			$ID_Cliente			= $rs->db_data["ID_Cliente"];
			$Contacto 			= $rs->db_data["Contacto"];
			$m_estado 			= $rs->db_data["Estado"];
			$m_pagamento 		= $rs->db_data["Metodo_pagamento"];
			$Data_expedicao 	= $rs->db_data["Data_expedicao"];

			$m_pagamento		= getNomeMetodoPagamentoByTipo_bo($m_pagamento);
			$m_guia 			= $rs->db_data["IDGuia"];
			$ATM 				= base64_decode($rs->db_data["ATM"]);
			$Ent_atm 			= $rs->db_data["Ent_atm"];
			$Ref_atm 			= $rs->db_data["Ref_atm"];
			$Lida 				= $rs->db_data["Lida"];
			$PHC 				= $rs->db_data["importado_phc"];
			$ValorDesconto		= $rs->db_data["ValorDesconto"];
			$IDGuia		=''; $IDGuia = $rs->db_data["IDGuia"];
			$Estado = $rs->db_data["Estado"];
			$Lingua = ($rs->db_data["Lingua"] == '' ? $GLOBALS['admin_lang'] : $rs->db_data["Lingua"]);
			$enc_Fatura = $rs->db_data["enc_Fatura"];

			$totalvalor += $TotalDocIva;

			$ent='';
			$ref='';
			$valor='';
			if($ATM)
			{
				$ent=$Ent_atm;
				$ref=$Ref_atm;
				preg_match('/<span id=\"order_value\">(.*)<\/span>/isU',$ATM,$matches3);
				$valor=$matches3[1];
			}
			if(!$Contacto) $Contacto="N/A";

			if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
			else				{$cor="#F6F6F6";$class="r2";$n="2";}

			$i=0;

			$total2=0;
			$sql="Select * from encomendas_produtos where ID_PAI = $IDb";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())$total2=$rs2->db_rows;
			$rs2->db_close();
			// if(!$total2)$cor="#ff0000";
			// echo $Estado.'<br>';

			if($IDb < 39)
			{
				if(!$m_estado) $estado = 'Pendente';
				// if($m_estado==1) echo 'Aceite';
				if($m_estado==2) $estado = 'Cancelada';
				if($m_estado==3) $estado = 'Pagamento Aceite';
				if($m_estado==4) $estado = 'Pagamento Cancelado';
				if($m_estado==5) $estado = 'Pagamento Pendente';
				if($m_estado==6) $estado = 'Em Expedição';
				if($m_estado==7) $estado = 'Concluída';

				$cor = str_replace('#', '', $cor);
				$cor_texto = str_replace('#', '', '000000');
			}
			else
			{
				$enc_state = estado_encomenda($Estado, $Lingua);
				// print_r($enc_state);
				$cor = $enc_state['cor'];
				// $estado = $enc_state['titulo'];
				// $cor = $enc_state['cor'];
				$cor_texto = str_replace('#', '', color_inverse($cor));
			}

			if($IDb < 39) $pagina = 'pagina2.php';
			else  $pagina = 'pagina.php';

			// $enc_state = estado_encomenda($Estado, $Lingua);
			// $cor = $enc_state['cor'];
			// $cor_texto = str_replace('#', '', color_inverse($cor));
			?>
			<tr bgcolor="#<?php echo $cor;?>"  style="cursor:pointer;">
				<td height="40" align="center" style="border-bottom:#c0c0c0 1px solid"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
				<td align="right" style="border-bottom:#c0c0c0 1px solid"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid"><a href="<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php if(file_exists($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!=''){?><a href="<?php echo $GLOBALS['site_http_url'].'files/encomendas/'.$enc_Fatura?>?dp=<?php echo date('YmdHis')?>" target="_blank"><img src="fatura.png" border="0" width="25" /></a> <?php }?></td>
				<td  align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto?>;text-decoration:none;" href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $IDb;?> </a></td>
				<td  align="center" style="border-bottom:#c0c0c0 1px solid"  colspan="2"><a style="color:#<?php echo $cor_texto?>;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $Data_Criacao;?> </a></td>
				<?php /*<td  align="center"><a style="color:#000;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><? echo number_format($TotalDocIva,2,',','.');?>  </a></td>*/?>
				<td  align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto?>;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $ID_Cliente;?>  </a></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto?>;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $Contacto;?> </a>&nbsp;</td>
				<td width="80" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto?>;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $m_pagamento?>  </a></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="../encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo numberformat($TotalDocIva); ?>  </a></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid">
				<select name="m_estado_<?php echo $IDb?>" id="m_estado_<?php echo $IDb?>" style="width:100%" class="chzn-select">
				<?php

					$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho, eel.Cabecalho2 FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$GLOBALS['admin_lang']."' ORDER BY Ordem";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$a=0; $status = '';
						while(!$rs2->EOF())
						{
							$eel_id = $rs2->db_data['ID'];
							$Ordem = $rs2->db_data['Ordem'];
							$Cabecalho = $rs2->db_data['Cabecalho2']!='' ? $rs2->db_data['Cabecalho2'] : $rs2->db_data['Cabecalho'];

							if($a == 0) $status .= '<option value="" '.(!$m_estado ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.($Estado==7 && $eel_id==7 ?  ' '.$Data_expedicao : '').'</option>';
							else $status .= '<option value="'.$eel_id.'" '.($m_estado==$eel_id ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.($Estado==7 && $eel_id==7 ?  ' '.$Data_expedicao : '').'</option>';

							$a++;
							$rs2->db_next();
						}
					}
					$rs2->db_close();

					echo $status;
				?>

				</select></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid"><?php //if($ValorDesconto > 0) echo 'C/ PROMO'; else echo 'S/ PROMO'?><input type="text" name="m_guia_<?php echo $IDb?>" id="m_guia_<?php echo $IDb?>" style="width:80px" class="box" value="<?php echo $IDGuia?>" /></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid;background:#def2fa"><a href="javascript:;" onclick="update_status(<?php echo $IDb?>)"><img border="0" src="../encomendas/save.png" /></a></td>
			</tr>
			<?
			$rs->db_next();
			$i++;
		}

		?>
			<tr bgcolor="#<?php echo $cor;?>"  style="cursor:pointer;">
				<td height="40" align="center" style="border-bottom:#c0c0c0 1px solid"></td>
				<td colspan="11" align="right" style="border-bottom:#c0c0c0 1px solid;font-size: 20px"><? echo $TotalEncomendas.' encomendas, '.numberformat($TotalDocIvaTodas,2);?></td>
				<td height="40" align="center" style="border-bottom:#c0c0c0 1px solid"></td>

			</tr>
			<?

	}else {?>
		<tr >
			<td colspan="11" height="25px" class="branco text-center"><? echo write_lang('key_sem_encomendas','Não foram encontradas encomendas')?></td>
		</tr>
	<?
	}

	$rs->db_close(1);
	show_paginacao_bk($total,$qtd);
}

function show_content_list_tasks_($reg=null, $limit='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$contador+=5;
	if(!$_POST['from'])$from=0;
	else $from = $_POST['from'];
	$qtd=30;

	if(!is_null($reg)) $sql_all="Select * from encomendas where Estado > -1 and ID IN($reg) order by ID DESC";
	else $sql_all="Select * from encomendas where Estado > -1 order by Data_Criacao Desc";

	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())$total=$rs->db_rows;
	$rs->db_close();


	if(!is_null($reg)) $sql="Select * from encomendas where Estado > -1 and ID IN($reg) order by Data_Criacao Desc limit 15";
	else $sql="Select * from encomendas where Estado > -1 order by Data_Criacao Desc limit 15";

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;$totalvalor=0;

	if(!$rs->EOF()){
		while(!$rs->EOF())
		{
			$count++;
			$IDb				= $rs->db_data["ID"];
			$Data_Criacao		= $rs->db_data["Data_Criacao"];
			$TotalDocIva		= $rs->db_data["TotalDocComIva"];
			$ID_Cliente			= $rs->db_data["ID_Cliente"];
			$Contacto 			= $rs->db_data["Contacto"];
			$m_estado 			= $rs->db_data["Estado"];
			$m_pagamento 		= $rs->db_data["Metodo_pagamento"];

			$m_pagamento		= getNomeMetodoPagamentoByTipo_bo($m_pagamento);
			$m_guia 			= $rs->db_data["IDGuia"];
			$ATM 				= base64_decode($rs->db_data["ATM"]);
			$Ent_atm 			= $rs->db_data["Ent_atm"];
			$Ref_atm 			= $rs->db_data["Ref_atm"];
			$Lida 				= $rs->db_data["Lida"];
			$PHC 				= $rs->db_data["importado_phc"];
			$ValorDesconto		= $rs->db_data["ValorDesconto"];
			$IDGuia		=''; $IDGuia = $rs->db_data["IDGuia"];
			$Estado = $rs->db_data["Estado"];
			$Lingua = $rs->db_data["Lingua"];
			$enc_Fatura = $rs->db_data["enc_Fatura"];
			$Assistencia = 0; $Assistencia = ($rs->db_data["Assistencia"] == 1 ? 'Assistência' : 'Encomenda');


			$totalvalor+=($TotalDocIva);

			$ent='';
			$ref='';
			$valor='';
			if($ATM)
			{
				$ent=$Ent_atm;
				$ref=$Ref_atm;
				preg_match('/<span id=\"order_value\">(.*)<\/span>/isU',$ATM,$matches3);
				$valor=$matches3[1];
			}
			if(!$Contacto) $Contacto="N/A";

			if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
			else				{$cor="#F6F6F6";$class="r2";$n="2";}

			$i=0;

			// $total2=0;
			// $sql="Select * from encomendas_produtos where ID_PAI = $IDb";
			// $rs2->db_open($db_link,$DB_name,$sql);
			// if(!$rs2->EOF())$total2=$rs2->db_rows;
			// $rs2->db_close();
			// if(!$total2)$cor="#ff0000";
			if($IDb < 39)
			{
				if(!$m_estado) $estado = 'Pendente';
				// if($m_estado==1) echo 'Aceite';
				if($m_estado==2) $estado = 'Cancelada';
				if($m_estado==3) $estado = 'Pagamento Aceite';
				if($m_estado==4) $estado = 'Pagamento Cancelado';
				if($m_estado==5) $estado = 'Pagamento Pendente';
				if($m_estado==6) $estado = 'Em Expedição';
				if($m_estado==7) $estado = 'Concluída';

				$cor_texto = str_replace('#', '', '000000');
			}
			else
			{
				$enc_state = estado_encomenda($Estado, $Lingua);
				// print_r($enc_state);
				$cor = $enc_state['cor'];
				$estado = $enc_state['titulo'];
				// $cor = $enc_state['cor'];
				$cor_texto = str_replace('#', '', color_inverse($cor));
			}

			if($IDb < 39) $pagina = 'pagina2.php';
			else  $pagina = 'pagina.php';
			?>
			<tr bgcolor="<?php echo $cor?>"  style="cursor:pointer">
				<td height="25" onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;" href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $IDb;?> </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid" ><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $Data_Criacao;?> </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $ID_Cliente;?>  </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $Contacto;?> </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $m_pagamento?>  </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo numberformat($TotalDocIva); ?>  </a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $estado;?></a></td>
				<td onclick="window.open('encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>','_self')" align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;"  href="encomendas/<?php echo $pagina;?>?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&D=<? echo uniqid("");?>"><?php echo $Assistencia;?></a></td>
				<td align="center" style="border-bottom:#c0c0c0 1px solid"><a style="color:#<?php echo $cor_texto; ?>;text-decoration:none;" target="_blank" href="encomendas/vistacliente.php?id_enc=<?php echo $IDb?>&D=<? echo uniqid("");?>">Visualizar</a></td>
			</tr>
			<?
			$rs->db_next();
			$i++;
		}
		/* ?>
		<tr >
			<td width="30" align="center" height="25px"></td>
			<td width="4" align="right"></td>
			<td width="30" align="right"></td>
			<td width="70" align="center"></td>
			<td width="150" align="center" style="color:#FFF;" colspan="2"></td>
			<td width="80px" align="center" style="color:#FFF;"><? echo number_format($totalvalor,2,',','.');?> </td>
			<td width="60px" align="center"></td>
			<td align="center"></td>
			<td width="100px" align="center"></td>
			<td width="120px" align="center"></td>
			<td width="30px" align="center"></td>
		</tr>
		<? */

	}else {?>
		<tr >
			<td colspan="11" height="25px" class="branco text-center"><? echo write_lang('key_sem_encomendas','Não foram encontradas encomendas')?></td>
		</tr>
	<?
	}

	$rs->db_close(1);
	// show_paginacao_bk($total,$qtd);
}
function show_content_list_tasks($reg=null, $limit='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$encomendas_edit_default;

	$pagina='pagina.php';
	if($encomendas_edit_default==1)$pagina='edita.php';


	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;

	$contador+=5;
	if(!$_POST['from'])$from=0;
	else $from = $_POST['from'];
	$qtd=30;

	$sql_all="Select ID from encomendas where Estado > -1 order by Data_Criacao Desc";

	$rs->db_open($db_link,$DB_name,$sql_all);
	if(!$rs->EOF())$total=$rs->db_rows;
	$rs->db_close();


	$sql="Select * from encomendas order by Data_Criacao Desc limit 15";

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;$totalvalor=0;

	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			$IDb				= $rs->db_data["ID"];
			$Referencia			= $rs->db_data["Referencia"];
			$Data_Criacao		= $rs->db_data["Data_Criacao"];
			$TotalDocIva		= $rs->db_data["TotalDocComIva"];
			$ID_Cliente			= $rs->db_data["ID_Cliente"];
			$Contacto 			= $rs->db_data["Contacto"];
			$m_estado 			= $rs->db_data["Estado"];
			$m_pagamento 		= $rs->db_data["Metodo_pagamento"];
			$m_expedicao 		= $rs->db_data["Metodo_envio"];
			$encomenda_paga 		= $rs->db_data["Encomenda_Paga"];

			$m_pagamento		= getNomeMetodoPagamentoByTipo_bo($m_pagamento);
			$m_expedicao 		= getNomeMetodoExpedicao_bo($m_expedicao);

			$m_guia 			= $rs->db_data["IDGuia"];
			$ATM 				= base64_decode($rs->db_data["ATM"]);
			$Ent_atm 			= $rs->db_data["Ent_atm"];
			$Ref_atm 			= $rs->db_data["Ref_atm"];
			$Lida 				= $rs->db_data["Lida"];
			$PHC 				= $rs->db_data["importado_phc"];
			$ValorDesconto		= $rs->db_data["ValorDesconto"];
			$IDGuia		=''; $IDGuia = $rs->db_data["IDGuia"];
			$Estado = $rs->db_data["Estado"];
			$Lingua = $rs->db_data["Lingua"];
			$enc_Fatura = $rs->db_data["enc_Fatura"];
			$Assistencia = 0; $Assistencia = ($rs->db_data["Assistencia"] == 1 ? 'Assistência' : 'Encomenda');


			$cl = cliente_perfil($ID_Cliente);


			$totalvalor+=($TotalDocIva);

			$ent='';
			$ref='';
			$valor='';
			if($ATM)
			{
				$ent=$Ent_atm;
				$ref=$Ref_atm;
				preg_match('/<span id=\"order_value\">(.*)<\/span>/isU',$ATM,$matches3);
				$valor=$matches3[1];
			}
			if(!$Contacto) $Contacto="N/A";

			if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#f0f3f5";$class="r2";$n="2";}

			$i=0;

			// $total2=0;
			// $sql="Select * from encomendas_produtos where ID_PAI = $IDb";
			// $rs2->db_open($db_link,$DB_name,$sql);
			// if(!$rs2->EOF())$total2=$rs2->db_rows;
			// $rs2->db_close();
			// if(!$total2)$cor="#ff0000";
			//$enc_state = estado_encomenda($Estado, $Lingua);
			// print_r($enc_state);
			//$cor = $enc_state['cor'];
			// $cor = $enc_state['cor'];

			//$cor_texto = str_replace('#', '', color_inverse($cor));

			$enc_state = estado_encomenda($Estado, $Lingua);
			$estado = $enc_state['titulo'];
			$cor_estado = $enc_state['cor'];
			$Pagamento_recebido = $enc_state['Pagamento_recebido'];

			$cor_preco = '#ffffff';
			if($encomenda_paga==1)$cor_preco = '#79bd3c';

			$cor_texto = '000000';

			$Data_Criacao = str_replace(date('Y-m-d'), 'HOJE', $Data_Criacao);

			$style='';
			if($cl['Testes']=='1') $style=';text-decoration: line-through';
			?>
			<tr bgcolor="<?=$cor?>"  style="cursor:pointer">
				<td height="30" onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto; ?>;text-decoration:none;" href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$IDb;?> </a></td>

				<? if($GLOBALS['usar_token_enc']==1){?><td height="30" onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto; ?>;text-decoration:none;" href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$Referencia;?> </a></td><? }?>

				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$Data_Criacao;?> </a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$ID_Cliente;?>  </a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid<?=$style?>"><a style="color:#<?=$cor_texto;?>;text-decoration:none;"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$Contacto;?> <? if($cl['Testes']=='1') echo "TESTE TESTE TESTE"; ?></a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$m_pagamento?>  </a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;background-color:<?=$cor_preco?>;padding-left:5px;padding-right:5px;padding-top:2px;padding-bottom:2px;border-radius:5px"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?php echo numberformat($TotalDocIva); ?>  </a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;background-color:#<?=$cor_estado?>;padding-left:5px;padding-right:5px;padding-top:2px;padding-bottom:2px;border-radius:5px"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$estado;?></a></td>
				<td onclick="window.open('encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>','_self')" align="center" style="border-bottom:#181c1f 1px solid"><a style="color:#<?=$cor_texto;?>;text-decoration:none;"  href="encomendas/<?=$pagina?>?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&D=<?=uniqid('');?>"><?=$m_expedicao;?></a></td>
				<td align="center" style="border-bottom:#181c1f 1px solid">
					<a target="_blank" href="encomendas/vistacliente.php?id_enc=<?=$IDb?>&D=<?=uniqid('')?>" class="btn btn-default btn-xs btn-rounded p-l-10 p-r-10"><i class="fa fa-cog"></i> Visualizar</a>
				</td>

			</tr>
			<?
			$rs->db_next();
			$i++;
		}
		/* ?>
					<a style="color:#<?=$cor_texto; ?>;text-decoration:none;" target="_blank" href="encomendas/vistacliente.php?id_enc=<?=$IDb?>&D=<?=uniqid('');?>">Visualizar</a>
		<tr >
			<td width="30" align="center" height="25px"></td>
			<td width="4" align="right"></td>
			<td width="30" align="right"></td>
			<td width="70" align="center"></td>
			<td width="150" align="center" style="color:#FFF;" colspan="2"></td>
			<td width="80px" align="center" style="color:#FFF;"><? echo number_format($totalvalor,2,',','.');?> </td>
			<td width="60px" align="center"></td>
			<td align="center"></td>
			<td width="100px" align="center"></td>
			<td width="120px" align="center"></td>
			<td width="30px" align="center"></td>
		</tr>
		<? */

	}else {?>
		<tr >
			<td colspan="12" height="25px" class="branco text-center"><? echo write_lang('key_sem_encomendas','Não foram encontradas encomendas')?></td>
		</tr>
	<?
	}

	$rs->db_close(1);
	// show_paginacao_bk($total,$qtd);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from packs where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Nome,$Preco,$NLances,$NPontos,$Estado,$img_path,$Img_peq1,$Apagar_img1,$norecize)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from packs WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
	}
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($Nome,$Preco,$NLances,$NPontos,$Estado,$img_path,$Img_peq1,$norecize);
	}
	else
	{
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);

		$res=Runquery($db_link,$DB_name,"Update packs Set Nome='$Nome',Preco='$Preco',Estado='$Estado',NLances='$NLances',NPontos='$NPontos',Data_modify='".date("Y-m-d")."',img_ext='$img_ext',norecize='$norecize' where ID=$id");
		db_closedb_conection($db_link);
		return($res);
	}
}
function SearchRecords()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT distinct ID FROM encomendas WHERE 1=1';

	if($_POST["f_id"]) $sql.=' AND ID like "%'.$_POST["f_id"].'%"';
	if($_POST["f_data1"] && !$_POST["f_data2"]) $sql.=' AND Data_Criacao >= "'.$_POST["f_data1"].' 00:00:00"';
	if(!$_POST["f_data1"] && $_POST["f_data2"]) $sql.=' AND Data_Criacao <= "'.$_POST["f_data2"].' 00:00:00"';
	if($_POST["f_data1"] && $_POST["f_data2"]) $sql.=' AND Data_Criacao >= "'.$_POST["f_data1"].' 00:00:00" AND Data_Criacao <= "'.$_POST["f_data2"].' 23:59:59"';
	if($_POST["f_total"] && $_POST["f_total"]!='') $sql.=' AND TotalDocComIva >= '. str_replace(',', '.', $_POST["f_total"]).'';
	if($_POST["f_idcli"]) $sql.=' AND ID_Cliente like "%'.$_POST["f_idcli"].'%"';
	if($_POST["f_contacto"]) $sql.=' AND Contacto like "%'.$_POST["f_contacto"].'%"';


	if($_POST["f_pagamento"]) $sql.=' AND Metodo_pagamento = '.$_POST["f_pagamento"];

	if($_POST["f_promos"]==1) $sql.=' AND ValorDesconto > 0';
	elseif($_POST["f_promos"]==2) $sql.=' AND ValorDesconto = 0';

	if(isset($_POST["f_estado"]) && $_POST["f_estado"]>=0)
	{
		if($_POST["f_estado"]==1)$sql.=' AND Estado = ""';
		else $sql.=' AND Estado = "'.$_POST["f_estado"].'"';
	}
	if($_POST["f_guia"]) $sql.=' AND IDGuia like "%'.$_POST["f_guia"].'%"';

	// echo $sql;
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";

	return $rs;
}
function show_paginacao_bk($total,$qtd)
{
	global $from;
	echo '<tr><td><input type="hidden" name="from" id="from" value="'.(isset($_POST['from']) ? $_POST['from'] : '').'"></td></tr>';
	if($total>$qtd)
	{
		$pageURL=get_uri();
		if(substr_count($pageURL,"&from"))$pageURL=substr($pageURL,0,strpos($pageURL,"&from"));
		if(substr_count($pageURL,"?from")){$pageURL=substr($pageURL,0,strpos($pageURL,"?from"));}
		if(substr_count($pageURL,"?")>0)$op="&";else $op="?";
		$i=0;
		?>
		<tr>
			<td colspan="90" style="background:#4e4e4e">
			<?php
				while($i<($total/$qtd))
				{
					if($from==($i*$qtd)) echo '<div style="float:left;text-align:center;width:20px;height:20px;line-height:20px;color:#FFF">'.($i+1).'</div>';
					else echo '<div style="float:left;text-align:center;cursor:pointer;background:#FFF;width:20px;height:20px;line-height:20px;" onclick="document.getElementById(\'from\').value=\''.($i*$qtd).'\';document.lista_paginas.submit();"><u>'.($i+1).'</u></div>';
					$i++;
				}
			?>
			</td>
		</tr>
		<?php
	}
}

function show_notify_area()
{
	global $x_id,$x_estado,$x_guia;

	$x_estado = ((isset($_GET['x_estado']) && $_GET['x_estado']) ? $_GET['x_estado'] : 0);


	// echo $x_estado;
	// $Lingua = 'pt';
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$FromName;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;


	/* if(isset($_GET['x_guia_'.$x_id]) && $_GET['x_guia_'.$x_id]!='')
	{
		$sql='UPDATE encomendas set IDGuia="'.$_GET['x_guia_'.$x_id].'" WHERE ID='.$x_id;
		$res = Runquery($db_link,$DB_name,$sql);
	}
	 */


	$sql='SELECT * FROM encomendas WHERE ID='.$x_id;
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$Lingua=$rs->db_data["Lingua"];
		$Data_Criacao=$rs->db_data["Data_Criacao"];
		$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
		$ID_Cliente=$rs->db_data["ID_Cliente"];
		$Contacto=$rs->db_data["Contacto"];
		$m_estado=$rs->db_data["Estado"];
		$m_pagamento=$rs->db_data["Metodo_pagamento"];
		$m_guia=$rs->db_data["IDGuia"];
		$Nome=$rs->db_data["enc_Nome"];
		$Apelido=$rs->db_data["enc_Apelido"];
		$Nickname=$rs->db_data["enc_Nome"];
		$Username=$rs->db_data["enc_Email"];
		$email_cliente=$rs->db_data["email_cliente"];
		$IDGuia=$rs->db_data["IDGuia"];
		$Telefone = $rs->db_data['enc_Telefone'];
		$enc_Fatura = $rs->db_data['enc_Fatura'];
		if(!$Contacto) $Contacto="N/A";
	}
	$rs->db_close();

	if($Lingua=='') $Lingua = 'pt';

	/* if($ID_Cliente > 0)
	{
		$sql='SELECT * FROM clientes WHERE ID='.$ID_Cliente;
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$Nome=$rs->db_data["Nome"];
			$Apelido=$rs->db_data["Apelido"];
			$Nickname=$rs->db_data["Nickname"];
			$Username=$rs->db_data["Username"];
		}
		if(!$Nickname) $Nickname=$Nome;
		$rs->db_close();
	} */



		// $msg = str_replace('<br>', "\n", $txt_enc[$x_estado][$Lingua]);

		$linhas_prods = '';$linhas_titulos = '';
		$sql='SELECT Ref, Nome, Estado FROM encomendas_produtos WHERE ID_PAI="'.$x_id.'" AND (Estado=4 OR Estado=9)';
		// echo $sql;
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$prod_estado = $rs->db_data['Estado'];
				$prod_ref = $rs->db_data['Ref'];
				$prod_nome = $rs->db_data['Nome'];

				// echo $prod_estado.'<br>';
				$enc_state = estado_encomenda($prod_estado, $Lingua);
				// $enc_state['titulo'];
				$prod_tr = str_replace('[produto]', str_replace('-', '', $prod_ref).' - '.$prod_nome, $GLOBALS['prod'][$Lingua]);
				$prod_tr = str_replace('[estado]', $enc_state['titulo2'], $prod_tr);

				if(!substr_count($linhas_titulos, $enc_state['titulo']))
				{
					if($linhas_titulos != '') $linhas_titulos .= ' / '.$enc_state['titulo'];
					else $linhas_titulos .= $enc_state['titulo'];
				}

				if($linhas_prods != '')  $linhas_prods .= '<br>'.$prod_tr.'.';
				else  $linhas_prods .= $prod_tr.'.';

				$rs->db_next();
			}
		}
		$rs->db_close();

		$body = array
		(
			'[area_cliente]' => $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1&id_enc='.$x_id,
			'[nome]' 	=> $Nome.' '.$Apelido,
			'[movel]' 	=> $Telefone,
			'[m_pagemento]' => getNomeMetodoPagamentoByTipo_bo($m_pagamento),
			'[n_enc]' =>  $x_id,
			'[produtos]' => $linhas_prods
		);

		$enc_state = estado_encomenda($m_estado, $Lingua);
		$message = template_email(0, $body, 800, 0, $enc_state['texto'], 0, ($enc_state['titulo2']!='' ? $enc_state['titulo2'] : $enc_state['titulo']));

		$assunto = ($enc_state['titulo']);
		?>
			<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
			<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
			<form action="../encomendas/list.php" name="form_env_email" method="post" enctype="multipart/form-data">

				<input type="hidden" name="ID" value="<?php echo $x_id;?>" />
				<input type="hidden" name="altera_estado" value="0" />
				<input type="hidden" name="env_email" value="1" />

				<table width="100%" cellspacing="0" cellpadding="10" border=0 style="border:#000 1px solid; background-color:#f6f6f6" align="center">
				<tr>
					<td width="10" height="10"></td>
					<td valign="top">
						<table width="100%" cellspacing="0" cellpadding="4" border=0>
						<?php if(isset($_GET['x_estado']) && $_GET['x_estado'] > 0 && ($_GET['x_estado'] == 6 || $_GET['x_estado'] == 5)){?>
						<tr>
						<td class="preto" align="left" style="font-size:12px"><b>Anexar Fatura:</b></td>
						<td align="left"><input style="width:200px" class="box" type="file" name="fatura" id="fatura" /></td>
						</tr>
						<?php if(file_exists($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!=''){?>
						<tr>
						<td class="preto" align="left" style="font-size:12px"><b>Fatura:</b></td>
						<td align="left"><a href="<?php echo $GLOBALS['site_http_url'].'files/encomendas/'.$enc_Fatura?>" target="_blank"><img src="fatura.png" border="0" width="100" /></a></td>
						</tr>
						<?php }}?>
						<tr>
							<td class=preto><b>Email:</td>
							<td align="left"><input style="width:200px" class="box"  type="text" name="EMAIL" id="EMAIL" value="<?php echo $Username;?>" class="new_input" /></td>
						</tr>
						<tr>
							<td class=preto><b>Assunto: </b></td>
							<td class=preto><input class="box" name="assunto" type="text" value="<?php echo $assunto?>" size="100%"><br></td>
						</tr>
						<tr>
							<td class=preto valign="top"><b>Mensagem: </b></td>
							<td class=preto><textarea name="texto" id="texto" style="WIDTH: 90%; HEIGHT: 150px;"><?php echo $message['body'];?></textarea> <?php show_editor_small("oEdit1","texto",880,250)?></td>
						</tr>
						<tr>
							<td class=preto></td>
							<td align="left" valign="top"><input class="botao" type="submit" name="Submit" value="Enviar email">&nbsp;<input class="botao" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);"></td>
						</tr>
						</table>

		<?php




			// echo $assunto.'<br>';

			// echo $message['body'];
	?>	</td>
				</tr>
				</table>
			</form><?php



}

function submete_ficheiro($file_name, $nome_ficheiro_old='', $enc_id=0)
{
	$img_path = '../../files/encomendas/';

	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;

	if($Img_peq["name"] != '')
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{
			unlink($img_path.$nome_ficheiro_old);
		}
		$token = 'encomenda_'.$enc_id.'_'.uniqid('encomendas');

		$nome_ficheiro = $token.".".$img_ext;

		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return $nome_ficheiro;
}

function envia_email()
{
	global $Default_SMTP_Email;
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$mailto = $_POST["EMAIL"];
	global $site_title;
	$mailsubject = $_POST["assunto"];

	$texto = $_POST["texto"];

	$ficheiro = submete_ficheiro('fatura', '', intval($_POST["ID"]));

	$attach = array();

	$enc_Fatura = '';

	if(!$ficheiro)
	{
		$rs = new DataObj;
		$sql='SELECT enc_Fatura FROM encomendas WHERE ID="'.intval($_POST["ID"]).'"';
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()) $enc_Fatura = $rs->db_data['enc_Fatura'];
		$rs->db_close();
	}
	if($ficheiro)
	{
		$attach = array($GLOBALS['site_shell_url'].'files/encomendas/'.$ficheiro, $ficheiro);

		$sql='UPDATE encomendas set enc_Fatura="'.$ficheiro.'" WHERE ID='.intval($_POST["ID"]);
		$res = Runquery($db_link,$DB_name,$sql);
	}
	elseif(file_exists($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!='')
	{
		$attach = array($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura, $enc_Fatura);
	}

	?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
	  <td>
		<table width="100%" cellspacing="4" cellpadding="0" border=0>
		  <tr>
				<td width="10" height="10"></td>
				<td class="preto text-center" style="color:#FFF;">
					<?php
					// send_email($xmail='', $xassunto='', $xmsg='', $xheaders='', $xtype=1, $attach=array())
					if(send_email($mailto, $mailsubject, $texto, '', 1, $attach))
					{
						echo show_alerta("<span style='color:#000'>O cliente foi devidamente informado da alteração do estado da sua encomenda!</span>", 'msg', "", 1);

						$sql = "INSERT INTO encomendas_emails(Tabela, ID_ORG, Data_envio, MailTo, Subject, Body) VALUES('encomendas', '".$ID."','".date("Y-m-d h:i:s")."','".$mailto."','".$mailsubject."','".$message['body']."')";
						$res = Runquery($db_link,$DB_name,$sql);
					}
					else
					{
						echo show_alerta("<span style='color:#000'>Devido a problemas técnicos não foi possivel alertar o utilizador da alteração do estado da encomenda, tente novamente!<br></span>", 'erro', "", 1);
					}
					?>
				 </td>
			</tr>
		  </table>
	  </td>
	</tr>
  </table>
  <?php
}
?>