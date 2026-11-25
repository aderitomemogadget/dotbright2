<?php

require_once __DIR__.'/sms_gateway.php';
require_once __DIR__.'/notification_policy.php';
require_once __DIR__.'/loan_policy.php';

function dotbright_rma_html($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function show_page_buttons()
{
	global $task_button;
	?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="5"></td>
		<td align="left" valign="middle" class="t1">
			<input class="botao" type="submit" name="Submit" value=" <?php echo $task_button;?> ">
			<input class="botao" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="javascript:history.go(-1);">
		</td>
	 </tr>
	</table>
	<br>
	<?php
}

function show_pending_rma_fast_tool($label, $width)
{
	?>
	<tr>
		<td class="preto" align="center"><input class="botao" type="button" style="width:<?php echo (int) $width; ?>px;height:30px" value="<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>" disabled="disabled" aria-disabled="true" data-disabled-reason="Disponível depois de gravar o RMA" title="Disponível depois de gravar o RMA"></td>
	</tr>
	<?php
}

function show_fast_tools($list=null)
{
	$rma_id = isset($_GET['ID']) ? (int) $_GET['ID'] : 0;
	?>
	<table width="200" border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td height="27">&nbsp;</td>
		</tr>
	</table>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td class="titulo2" align="center">FAST TOOLS</td>
		</tr>
		<tr>
			<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:30px" value="<?php echo write_lang('key_criar_novo_rma','Criar Novo RMA')?>" onclick="javascript:window.open('pagina.php?ID=0&d=<?php echo uniqid("");?>','_self')"></td>
		</tr>
		<?php if($rma_id > 0){ ?>
		<tr>
			<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:30px" value="<?php echo write_lang('key_imprimir_recepcao','Imprime Folha Recepção')?>" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=1&d=<?php echo uniqid('');?>','_self')"></td>
		</tr>
		<tr>
			<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:30px" value="ASSINAR FOLHA DE RECEPÇÃO" onclick="javascript:window.open('../signed2/index.php?rma=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_blank')"></td>
		</tr>
		<tr>
			<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:30px" value="Imprime Relatório Intervenção" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_self')"></td>
		</tr>
		<tr>
			<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:30px" value="ASSINAR RELATÓRIO INTERVENÇÃO" onclick="javascript:window.open('../signed/index.php?rma=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_blank')"></td>
		</tr>
		<?php /* <tr><td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_imprimir_reparacao','Imprime Folha Reparação')?>" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=3&d=<?php echo uniqid('');?>','_self')"></td></tr><?php */ } else {
			show_pending_rma_fast_tool(write_lang('key_imprimir_recepcao','Imprime Folha Recepção'), 160);
			show_pending_rma_fast_tool('ASSINAR FOLHA DE RECEPÇÃO', 160);
			show_pending_rma_fast_tool('Imprime Relatório Intervenção', 160);
			show_pending_rma_fast_tool('ASSINAR RELATÓRIO INTERVENÇÃO', 160);
		} ?>
	</table>
	<br>
	<?php 
}

function show_fast_tools2($list=null)
{
	$rma_id = isset($_GET['ID']) ? (int) $_GET['ID'] : 0;
	?>
	<table width="200" border=0 cellspacing=0 cellpadding=0 style="border:#000 1px solid;background:#333039">
		<tr >
			<td height="25" class="titulo2" align="center" style="border-bottom:#000 1px solid">FAST TOOLS</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr >
			<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="CRIAR NOVO RMA" onclick="javascript:window.open('pagina.php?ID=0&d=<?php echo uniqid("");?>','_self')"></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<?php 
			if($rma_id > 0){
				?>
				<tr>
					<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="IMPRIME FOLHA RECEPÇÃO" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=1&d=<?php echo uniqid('');?>','_self')"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="ASSINAR FOLHA DE RECEPÇÃO" onclick="javascript:window.open('../signed2/index.php?rma=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_blank')"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="IMPRIME RELATÓRIO INTERVENÇÃO" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_self')"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="ASSINAR RELATÓRIO INTERVENÇÃO" onclick="javascript:window.open('../signed/index.php?rma=<?php echo $_GET['ID'];?>&tipo=2&d=<?php echo uniqid('');?>','_blank')"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<?php 
			} else {
				show_pending_rma_fast_tool('IMPRIME FOLHA RECEPÇÃO', 190);
				?><tr><td>&nbsp;</td></tr><?php
				show_pending_rma_fast_tool('ASSINAR FOLHA DE RECEPÇÃO', 190);
				?><tr><td>&nbsp;</td></tr><?php
				show_pending_rma_fast_tool('IMPRIME RELATÓRIO INTERVENÇÃO', 190);
				?><tr><td>&nbsp;</td></tr><?php
				show_pending_rma_fast_tool('ASSINAR RELATÓRIO INTERVENÇÃO', 190);
				?><tr><td>&nbsp;</td></tr><?php
			}
			if($GLOBALS['readonly'] == 0){
				?>
				<tr>
					<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="<?php echo $GLOBALS['task_button'];?> " onclick="$('#rma_form').submit();"></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<?php 
			} 
		?>
	</table>
	<br>
	<?php 
}

function show_rmas_estados($grupo_id=0, $estado_sel=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho FROM rmas_estados ee INNER JOIN rmas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$GLOBALS['admin_lang']."' AND f_estado='".$grupo_id."' ORDER BY Ordem";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$a=0;
		while(!$rs2->EOF())
		{
			$eel_id = $rs2->db_data['ID'];
			$Ordem = $rs2->db_data['Ordem'];
			$Cabecalho = $rs2->db_data['Cabecalho'];

			echo '<option value="'.$eel_id.'" '.($estado_sel==$eel_id ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.'</option>';

			$a++;
			$rs2->db_next();
		}
	}
	$rs2->db_close();
}

function show_rmas_descricao_tecnico($estado_sel=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql="SELECT ID, title FROM rmas_descricao_tecnico ORDER BY ordem";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$a=0;
		while(!$rs2->EOF())
		{
			$ID = $rs2->db_data['ID'];
			$title = $rs2->db_data['title'];

			echo '<option value="'.$ID.'"  '.($estado_sel==$ID ? 'selected="selected"' : '').' title="'.$title.'">'.$title.'</option>';

			$a++;
			$rs2->db_next();
		}
	}
	$rs2->db_close();
}

function get_user_grupo($user_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$return = 0;
	$sql="SELECT ID, Grupo_ID FROM logins WHERE ID='".$user_id."'";
	// echo $sql;
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) $return = $rs2->db_data['Grupo_ID'];
	$rs2->db_close();

	return $return;
}

function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from rmas WHERE ID IN($reg) $and_loja order by DataCriado DESC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;

	while(!$rs->EOF())
	{
		$IDb=$rs->db_data['ID'];
		$Tracking=$rs->db_data["Tracking"];
		$Ref=$rs->db_data["Referencia"];
		$ID_Cliente=$rs->db_data["ID_Cliente"];
		$Marca=$rs->db_data["Marca"];
		$Modelo=$rs->db_data["Modelo"];
		$TipoEquipamento=$rs->db_data["TipoEquip"];
		$DataFinalizado=$rs->db_data["DataFinalizado"];
		$NrSerie=$rs->db_data["NSerie"];
		if($DataFinalizado!='') $DataFinalizado=date('Y-m-d',strtotime($DataFinalizado));
		else $DataFinalizado=date('Y-m-d',strtotime($rs->db_data["DataUpdate"])).'*';

		if($ID_Cliente>0){
			$sql_cliente='SELECT Nome,Apelido,Empresa FROM clientes WHERE ID='.$ID_Cliente;
			$rs2->db_open($db_link,$DB_name,$sql_cliente);
			if(!$rs2->EOF()){
				$NomeCliente=$rs2->db_data['Nome']." ".$rs2->db_data['Apelido'];
				$EmpresaCliente=$rs2->db_data['Empresa'];
			}
		}
		$Estado=$rs->db_data['Estado'];
		switch ($Estado) {
			case 0: $cor="#FFFF99";break;
			case 1: $cor="#F7B500";break;
			case 2: $cor="#F6F6F6";break;
			case 3: $cor="#F6F6F6";break;
			case 4: $cor="#F6F6F6";break;
			case 5: $cor="#8EE59F";break;
			case 6: $cor="#00FF00";break;
			case 7: $cor="#FF3333";break;
			case 8: $cor="#FFAAAA";break;
			case 9: $cor="#FBD773";break;
		}

		$i=0;

		?>
		<table border=0 cellspacing=0 cellpadding=0 width="100%">
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">

				<td width="30" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
				<td width="10" align="center">&nbsp;</td>
				<td width="100" align="center"><a title="<?php echo $Tracking;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo "STG".str_pad((int) $IDb,6,"0",STR_PAD_LEFT);?></a></td>
				<td width="130" align="center"><a title="<?php echo $NrSerie;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo $NrSerie.'<br>'.$Modelo;?></a></td>
				<td width="130" align="center"><a title="<?php echo $DataFinalizado;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo $DataFinalizado;?></a></td>
				<td width="150" align="center"><a title="<?php echo $EmpresaCliente;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo $EmpresaCliente;?></a></td>
				<td width="150" align="center"><a title="<?php echo $NomeCliente;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo $NomeCliente;?></a></td>
				<td width="100" align="center"><a title="<?php echo $TipoEquipamento;?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb;?>"><?php echo $TipoEquipamento;?></a></td>

				<td align="center" valign="middle" width="160">
					<select id="m_estado_<?php echo $IDb?>">
						<option value="0" <?php if($Estado== 0) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado0','Aguarda diagnóstico') ?></option>
						<option value="1" <?php if($Estado== 1) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado1','Aguarda aceitação') ?></option>
						<option value="2" <?php if($Estado== 2) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado2','Aguarda reparação') ?></option>
						<option value="3" <?php if($Estado== 3) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado3','Aguarda peça') ?></option>
						<option value="4" <?php if($Estado== 4) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado7','Aguarda peça para despiste') ?></option>
						<option value="5" <?php if($Estado== 5) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado4','Pronto para entrega') ?></option>
						<option value="6" <?php if($Estado== 6) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado5','Concluído') ?></option>
						<option value="7" <?php if($Estado== 7) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado6','Recusado') ?></option>
						<option value="8" <?php if($Estado == 8) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado8','Sem Reparação - Pronto para levantar') ?></option>
						<option value="9" <?php if($Estado == 9) echo 'selected=""';?>><?php echo write_lang('key_rmas_estado9','Aguarda assistência eletrónica') ?></option>
					</select>
				</td>
				<td width="0" align="right" class="preto" style="display:inline;">
					<a href="javascript:;" onclick="update_status(<?php echo $IDb?>)"><img border="0" src="save.png" /></a>
					<a class="italico" href="pagina.php?d=<?php echo uniqID('')?>&ID=<?php echo $IDb?>&task=apagar"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Cliente')?>" src="del2.png" border=0></a>
				</td>
			</tr>
		</table><?php
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

/*--------------------------------------------------------------------------------------------------------------------------------------------------------*/

function historico_rma($id_user, $id_rma){
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$db = new DataObj();
	$today = date("m-d-y");

	$sql_user = 'SELECT Nome FROM clientes WHERE ID = "'.$id_user."'";
	$db->db_open($db_link,$DB_name,$sql_user);
	$user = $db_db_data['Nome'];

	$sql_insere = 'INSERT INTO historico_rma (ID, ID_rma, user, data) VALUES ("", $id_rma, $user, $today);';
	$db->db_open($db_link,$DB_name,$sql_user);
}

function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	$Img_peq = $_FILES['img_peq'];
	$Apagar_img = $_POST['Apagar_img'];
	$tem_foto = $_POST['tem_foto'];


	// NOVA MARCA OU NOVO MODELO
	$marca_id = 0; $nova_marca = '';$novo_modelo = '';

	if(isset($_POST['nova_marca']) && trim($_POST['nova_marca']) != '')
	{
		$sql = 'INSERT INTO marcas_rmas (marca) VALUES ("'.addslashes(trim($_POST['nova_marca'])).'");';
		Runquery($db_link, $DB_name, $sql);
		$marca_id = db_get_last_insert_id($db_link);
		$nova_marca = trim($_POST['nova_marca']);
	}

	if(isset($_POST['novo_modelo']) && trim($_POST['novo_modelo']) != '')
	{
		if(isset($_POST['nova_marca']) && trim($_POST['nova_marca']) != '' && $marca_id > 0)
		{
			$sql = 'INSERT INTO modelos_rmas (modelo, marca_id) VALUES ("'.addslashes(trim($_POST['novo_modelo'])).'", '.$marca_id.')';
			Runquery($db_link, $DB_name, $sql);
			$novo_modelo = trim($_POST['novo_modelo']);
		}
		elseif(isset($_POST['m_marca']) && trim($_POST['m_marca']) != '')
		{
			$rs3 = new DataObj();
			$sql2 = 'SELECT ID FROM marcas_rmas WHERE marca="'.addslashes(trim($_POST['m_marca'])).'"';
			$rs3->db_open($db_link, $GLOBALS['DB_name'], $sql2);
			if(!$rs3->EOF()) $marca_id = $rs3->db_data['ID'];
			$rs3->db_close();

			$sql = 'INSERT INTO modelos_rmas (modelo, marca_id) VALUES ("'.addslashes(trim($_POST['novo_modelo'])).'", '.$marca_id.')';
			Runquery($db_link, $DB_name, $sql);
			$novo_modelo = trim($_POST['novo_modelo']);
		}
	}

	// exit;
	// --------------------------

	if($modulo_acao=='insere')
	{
		global $loja;
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(Loja,';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES("'.$loja.'",';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i > 0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif($campos == 'DataUpdate')
				{
					$sql .= '"'.date('Y-m-d').'"';
				}
				elseif($campos == 'DataInsert')
				{
					$sql .= '"'.date('Y-m-d').'"';
				}
				elseif($campos == 'm_marca' && $nova_marca != '')
				{
					$sql .= '"'.addslashes($nova_marca).'"';
				}
				elseif($campos == 'm_modelo' && $novo_modelo != '')
				{
					$sql .= '"'.addslashes($novo_modelo).'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= '
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0;

		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID && $campos != 'DataInsert')
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos == 'DataUpdate')
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.date('Y-m-d').'"';
				}
				elseif($campos == 'm_marca' && $nova_marca != '')
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($nova_marca).'"';
				}
				elseif($campos == 'm_modelo' && $novo_modelo != '')
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($novo_modelo).'"';
				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];
	}

	if($modulo_acao=='update'){
		if (rma_loan_blocks_finalization(
			isset($_POST['m_estado']) ? $_POST['m_estado'] : 0,
			isset($_POST['estado_emp']) ? $_POST['estado_emp'] : 1,
			isset($_POST['imei_emp']) ? $_POST['imei_emp'] : ''
		)){
			return array('resultado' => rma_loan_block_message(), 'acao' => $modulo_acao, 'id_acao' => $modulo_id);
		}
	}

	$res = Runquery($db_link, $DB_name, $sql);

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	$log_id = grava_rma_log($id_acao, $_POST["m_estado"]);

	if($id_acao){
		$loan_submission = rma_normalize_loan_submission(
			isset($_POST['imei_emp']) ? $_POST['imei_emp'] : '',
			isset($_POST['marca_emp']) ? $_POST['marca_emp'] : '',
			isset($_POST['modelo_emp']) ? $_POST['modelo_emp'] : '',
			isset($_POST['estado_emp']) ? $_POST['estado_emp'] : 1
		);
		$marca_emp = addslashes($loan_submission['marca']);
		$modelo_emp = addslashes($loan_submission['modelo']);
		$imei_emp = addslashes($loan_submission['imei']);
		$estado_emp = $loan_submission['estado'];

		$rs_emp = new DataObj;
		$sql_emp = 'SELECT * FROM rmas_emp where id_rma = '.$id_acao;
		$rs_emp->db_open($db_link,$DB_name,$sql_emp);
		if(!$rs_emp->EOF())
		{
			$update_emp = '	UPDATE rmas_emp SET 
								marca = "'.$marca_emp.'",
								modelo = "'.$modelo_emp.'",
								imei = "'.$imei_emp.'",
								estado = "'.$estado_emp.'"
							WHERE id_rma = '.$id_acao;
			$res_emp = Runquery($db_link, $DB_name, $update_emp);
		}
		else{
			if($marca_emp != '' || $estado_emp != ''){
				$insert_emp = '	INSERT INTO rmas_emp (id_rma, marca, modelo, imei, estado)
								VALUES ('.$id_acao.', "'.$marca_emp.'", "'.$modelo_emp.'", "'.$imei_emp.'", "'.$estado_emp.'"); ';
				$res_emp = Runquery($db_link, $DB_name, $insert_emp);
			}			
		}

	}


	$id_cli_new = 0;

	if(isset($_POST['fake']))
	{
		if(isset($_POST['cliente_telefone1']) && $_POST['cliente_telefone1'] != '')
		{
			$cliente_telefone1  = $_POST['cliente_telefone1'];
			$ID = verifica_cliente_by_telefone($cliente_telefone1);
			if($ID > 0) //actualiza cliente
			{
				atualiza_cliente_by_telefone($ID, 2);
			}
			else //insere cliente
			{
				$id_cli_new = insere_cliente_by_telefone();
			}
		}
	}
	else
	{
		if(isset($_POST['cliente_username']) && $_POST['cliente_username'] != '')
		{
			$username  = $_POST['cliente_username'];
			$ID = verifica_cliente($username);
			if($ID > 0) //actualiza cliente
			{
				atualiza_cliente($ID, 2);
			}
			else //insere cliente
			{
				$id_cli_new = insere_cliente();
			}
		}
	}
	// die();


	// if(isset($_POST['cliente_nif']) && $_POST['cliente_nif'] != '')
	// {
	// 	$nif  = $_POST['cliente_nif'];
	// 	if(verifica_cliente($nif) == 1) //actualiza cliente
	// 	{
	// 		atualiza_cliente($_POST['ID_Cliente'], 2);
	// 	}
	// 	else //insere cliente
	// 	{
	// 		$id_cli_new = insere_cliente();
	// 	}
	// }

	/*
	if($res && isset($_POST['novo_cliente']) && $_POST['novo_cliente'] == 1)
	{
		$id_cli_new = insere_cliente(); // CASO NÃO EXISTA O CLIENTE VAMOS INSERIR
	}
	elseif($res && isset($_POST['novo_cliente']) && isset($_POST['ID_Cliente']) && $_POST['novo_cliente'] == 2 && $_POST['ID_Cliente'] > 0)
	{
		atualiza_cliente($_POST['ID_Cliente'], 2); // CASO NÃO EXISTA O CLIENTE VAMOS INSERIR
	}
	elseif($res && isset($_POST['novo_cliente']) && isset($_POST['ID_Cliente']) && $_POST['novo_cliente'] == 3 && $_POST['ID_Cliente'] > 0)
	{
		atualiza_cliente($_POST['ID_Cliente'], 3); // CASO NÃO EXISTA O CLIENTE VAMOS INSERIR
	}

	*/

	if($res) submeteDadosLinguas($id_acao);

	if($id_cli_new > 0)
	{
		$sql = 'UPDATE rmas SET ID_Cliente='.$id_cli_new.' WHERE '.TBID.'='.$id_acao;
		Runquery($db_link, $DB_name, $sql);
	}

	if($res) rmas_images_save($id_acao,$Img_peq,$tem_foto,$Apagar_img);

	// A opção suprime apenas o e-mail; o SMS configurado no estado mantém-se independente.
	$nao_envio_email = isset($_POST['nao_envio_email']) && (int)$_POST['nao_envio_email'] === 1 ? 1 : 0;
	$fake = isset($_POST['fake']) ? (int)$_POST['fake'] : 0;
	enviaEmail($id_acao, 0, $log_id, $fake, $nao_envio_email);
	if(isset($_POST['envio_email_orcamento']) && $_POST['envio_email_orcamento'] == 1) enviaEmail($id_acao, 1, 0, $fake);

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function verifica_cliente($username = '')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new DataObj;
	if($username != '')
	{
		$return = 0;
		$sql = "SELECT ID FROM clientes WHERE Username='".$username."'";
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()) $return = $rs->db_data['ID'];
		$rs->db_close();
	}
	return $return;
}

function verifica_cliente_by_telefone($telefone = '')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new DataObj;
	if($telefone != '')
	{
		$return = 0;
		$sql = "SELECT ID FROM clientes WHERE Telefone='".$telefone."'";
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()) $return = $rs->db_data['ID'];
		$rs->db_close();
	}
	return $return;
}

function insere_cliente()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$ID_Cliente = intval($_POST['ID_Cliente']);
	$Grupo_ID = intval($_POST['Grupo_ID']);
	if($ID_Cliente == '' || $ID_Cliente == 0)
	{
		$cliente_username = $_POST['cliente_username'];
		$fake = $_POST['fake'];

		if(!$cliente_username)
		{
			$fake = 1;
			$cliente_username=uniqid('D');// vamos gerar um numero aleatorio
		}
		if($cliente_username != '' && ($fake == 0 || ( $fake==1 && validEmail($cliente_username))   ))
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);

			$str = 'abcdefghijklmnopqrstuvwxyz01234567891011121314151617181920212223242526';
			$shuffled = str_shuffle($str);
			$shuffled = substr($shuffled,1,4);


			$cliente_password = str2SQLstr(base64_encode($shuffled));
			$cliente_username = str2SQLstr($_POST['cliente_username']);
			$fake = str2SQLstr($_POST['fake']);
			$sql = '
							INSERT INTO clientes
								(Username, Password, Nome, Sobrenome, Apelido, Empresa, Morada, Codigo_Postal, Telefone, Email, Nif, Data, Ativo, Estado, Grupo_ID, rma_associado)
							VALUES
								('.$cliente_username.', '.$cliente_password.', '.$cliente_nome.', '.$cliente_sobrenome.', '.$cliente_apelido.', '.$cliente_empresa.', '.$cliente_morada.', '.$cliente_cp.', '.$cliente_telefone.', '.$cliente_username.', '.$cliente_nif.', "'.date('Y-m-d H:i:s').'","1","1", "'.$Grupo_ID.'", 1)';
			// var_dump($sql);
			$res = Runquery($db_link, $DB_name, $sql);
			if(!$res)echo db_error($db_link);
			else
			{
				$ID_Cliente = db_get_last_insert_id($db_link);
				@enviaEmail($ID_Cliente, 2, 0, $_POST['fake']);

				return $ID_Cliente;
			}
		}
	}
}

function insere_cliente_by_telefone()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$ID_Cliente = intval($_POST['ID_Cliente']);
	$Grupo_ID = intval($_POST['Grupo_ID']);
	if($ID_Cliente == '' || $ID_Cliente == 0)
	{
		$cliente_username = $_POST['cliente_telefone1'];
		$fake = $_POST['fake'];

		if(!$cliente_username)
		{
			$fake = 1;
			$cliente_username=uniqid('D');// vamos gerar um numero aleatorio
		}
		if($cliente_username != '' && ($fake == 0 || ( $fake==1   )))
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);

			$str = 'abcdefghijklmnopqrstuvwxyz01234567891011121314151617181920212223242526';
			$shuffled = str_shuffle($str);
			$shuffled = substr($shuffled,1,4);


			$cliente_password = str2SQLstr(base64_encode($shuffled));
			$cliente_username = str2SQLstr($_POST['cliente_username']);
			$fake = str2SQLstr($_POST['fake']);
			$sql = '
							INSERT INTO clientes
								(Username, Password, Nome, Sobrenome, Apelido, Empresa, Morada, Codigo_Postal, Telefone, Email, Nif, Data, Ativo, Estado, Grupo_ID, rma_associado)
							VALUES
								('.$cliente_username.', '.$cliente_password.', '.$cliente_nome.', '.$cliente_sobrenome.', '.$cliente_apelido.', '.$cliente_empresa.', '.$cliente_morada.', '.$cliente_cp.', '.$cliente_telefone.', '.$cliente_username.', '.$cliente_nif.', "'.date('Y-m-d H:i:s').'","1","1", "'.$Grupo_ID.'", 1)';
			// var_dump($sql);
			// die();
			$res = Runquery($db_link, $DB_name, $sql);
			if(!$res)echo db_error($db_link);
			else
			{
				$ID_Cliente = db_get_last_insert_id($db_link);
				@enviaEmail($ID_Cliente, 2, 0, $_POST['fake']);

				return $ID_Cliente;
			}
		}
	}
}

function atualiza_cliente($ID_Cliente, $tipo = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	// echo $tipo;
	if($ID_Cliente > 0 && $tipo == 2)
	{
		$cliente_username = $_POST['cliente_username'];
		if($cliente_username != '')
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);

			$cliente_password = str2SQLstr(base64_encode($shuffled));
			$cliente_username = str2SQLstr($_POST['cliente_username']);
			$sql = '
				UPDATE clientes
				SET
					Nome = '.$cliente_nome.',
					Sobrenome = '.$cliente_sobrenome.',
					Apelido = '.$cliente_apelido.',
					Empresa = '.$cliente_empresa.',
					Morada = '.$cliente_morada.',
					Codigo_Postal = '.$cliente_cp.',
					Telefone = '.$cliente_telefone.',
					Telefone2 = '.$cliente_telefone2.',
					Email = '.$cliente_username.',
					Username = '.$cliente_username.',
					Nif = '.$cliente_nif.',
					rma_associado = 1
				WHERE
					ID = '.$ID_Cliente.'
			';

			// echo $sql;
			$res = Runquery($db_link, $DB_name, $sql);
			if(!$res)echo db_error($db_link);
		}
	}
	elseif($ID_Cliente > 0 && $tipo == 3)
	{
		$cliente_username = $_POST['cliente_username'];
		if($cliente_username != '')
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);
			$cliente_username = str2SQLstr($_POST['cliente_username']);

			$sql = 'UPDATE clientes SET Nome = '.$cliente_nome.' WHERE ID = '.$ID_Cliente.' AND Nome=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Sobrenome = '.$cliente_sobrenome.' WHERE ID =  '.$ID_Cliente.' AND Sobrenome=""';
			// echo $sql.'<br>';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Apelido = '.$cliente_apelido.' WHERE ID = '.$ID_Cliente.' AND Apelido=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Empresa = '.$cliente_empresa.' WHERE ID = '.$ID_Cliente.' AND Empresa=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Morada = '.$cliente_morada.' WHERE ID = '.$ID_Cliente.' AND Morada=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Codigo_Postal = '.$cliente_cp.' WHERE ID = '.$ID_Cliente.' AND Codigo_Postal=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Telefone = '.$cliente_telefone.' WHERE ID = '.$ID_Cliente.' AND Telefone=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Telefone2 = '.$cliente_telefone2.' WHERE ID = '.$ID_Cliente.' AND Telefone2=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Email = '.$cliente_username.' WHERE ID = '.$ID_Cliente.' AND Email=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Nif = '.$cliente_nif.' WHERE ID = '.$ID_Cliente.' AND (Nif="" OR AND Nif="0")';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET rma_associado = 1 WHERE ID = '.$ID_Cliente.'';
			$res = Runquery($db_link, $DB_name, $sql);
		}
	}
}

function atualiza_cliente_by_telefone($ID_Cliente, $tipo = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	// echo $tipo;
	if($ID_Cliente > 0 && $tipo == 2)
	{
		$cliente_username = $_POST['cliente_telefone1'];
		if($cliente_username != '')
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);
			$cliente_password = str2SQLstr(base64_encode($shuffled));
			$cliente_username = str2SQLstr($_POST['cliente_username']);
			$sql = '
				UPDATE clientes
				SET
					Nome = '.$cliente_nome.',
					Sobrenome = '.$cliente_sobrenome.',
					Apelido = '.$cliente_apelido.',
					Empresa = '.$cliente_empresa.',
					Morada = '.$cliente_morada.',
					Codigo_Postal = '.$cliente_cp.',
					Telefone = '.$cliente_telefone.',
					Telefone2 = '.$cliente_telefone2.',
					Email = '.$cliente_username.',
					Username = '.$cliente_username.',
					Nif = '.$cliente_nif.',
					rma_associado = 1
				WHERE
					ID = '.$ID_Cliente.'
			';

			// echo $sql;
			$res = Runquery($db_link, $DB_name, $sql);
			if(!$res)echo db_error($db_link);
		}
	}
	elseif($ID_Cliente > 0 && $tipo == 3)
	{
		$cliente_username = $_POST['cliente_telefone1'];
		if($cliente_username != '')
		{
			$cliente_nif = str2SQLstr($_POST['cliente_nif']);
			$cliente_nome = str2SQLstr($_POST['cliente_nome']);
			$cliente_apelido = str2SQLstr($_POST['cliente_apelido']);
			$cliente_morada = str2SQLstr($_POST['cliente_morada']);
			$cliente_cp = str2SQLstr($_POST['cliente_cp']);
			$cliente_telefone = str2SQLstr($_POST['cliente_telefone1']);
			$cliente_telefone2 = str2SQLstr($_POST['cliente_telefone2']);
			$cliente_empresa = str2SQLstr($_POST['cliente_empresa']);
			$cliente_sobrenome = str2SQLstr($_POST['cliente_sobrenome']);
			$cliente_username = str2SQLstr($_POST['cliente_username']);

			$sql = 'UPDATE clientes SET Nome = '.$cliente_nome.' WHERE ID = '.$ID_Cliente.' AND Nome=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Sobrenome = '.$cliente_sobrenome.' WHERE ID =  '.$ID_Cliente.' AND Sobrenome=""';
			// echo $sql.'<br>';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Apelido = '.$cliente_apelido.' WHERE ID = '.$ID_Cliente.' AND Apelido=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Empresa = '.$cliente_empresa.' WHERE ID = '.$ID_Cliente.' AND Empresa=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Morada = '.$cliente_morada.' WHERE ID = '.$ID_Cliente.' AND Morada=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Codigo_Postal = '.$cliente_cp.' WHERE ID = '.$ID_Cliente.' AND Codigo_Postal=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Telefone = '.$cliente_telefone.' WHERE ID = '.$ID_Cliente.' AND Telefone=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Telefone2 = '.$cliente_telefone2.' WHERE ID = '.$ID_Cliente.' AND Telefone2=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Email = '.$cliente_username.' WHERE ID = '.$ID_Cliente.' AND Email=""';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET Nif = '.$cliente_nif.' WHERE ID = '.$ID_Cliente.' AND (Nif="" OR AND Nif="0")';
			$res = Runquery($db_link, $DB_name, $sql);

			$sql = 'UPDATE clientes SET rma_associado = 1 WHERE ID = '.$ID_Cliente.'';
			$res = Runquery($db_link, $DB_name, $sql);
		}
	}
}

function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $rs = new DataObj;
	// $sql='SELECT ID_ORG FROM '.TBMODULO_LANG.' WHERE ID_ORG='.$modulo_id.'';
	// $rs->db_open($db_link, $DB_name, $sql);
	// if(!$rs->EOF()) $modulo_acao = 'update'; else
	// $rs->db_close();

	$modulo_acao = 'insere';

	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];

	// VAMOS ELIMINAR AS LINHAS
	$sql_delete_linhas = 'DELETE FROM '.TBMODULO_LANG.' WHERE ID_ORG="'.$modulo_id.'"';
	$res1 = Runquery($db_link, $DB_name, $sql_delete_linhas);

	$qtd = $_POST['qtd'];
	$ref = $_POST['ref'];
	$desc = $_POST['desc'];
	$prec = $_POST['prec'];
	$disp = $_POST['disp'];

	$data = date('Y-m-d H:i:s');

	for($a=0; $a < count($qtd); $a++)
	{
		if($qtd[$a] > 0)
		{
			$qtd_temp = $qtd[$a];
			$ref_temp = $ref[$a];
			$desc_temp = $desc[$a];
			$prec_temp = $prec[$a];
			$disp_temp = $disp[$a];

			if($modulo_acao == 'insere')
			{
				$sql = 'INSERT INTO '.TBMODULO_LANG;
				$sql .= '(';
				$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'ID'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i = 0;
				foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != 'ID')
					{
						if($i>0){$sql .= ', ';}

						if(substr_count($campos,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos, '');
							$sql .= '"'.$nome_ficheiro.'"';
						}
						elseif($campos=='ID_ORG')
						{
							$sql .= '"'.$modulo_id.'"';
						}
						elseif($campos=='Ref')
						{
							$sql .= '"'.addslashes($ref_temp).'"';
						}
						elseif($campos=='Cabecalho')
						{
							$sql .= '"'.addslashes($desc_temp).'"';
						}
						elseif($campos=='Quantidade')
						{
							$sql .= '"'.addslashes($qtd_temp).'"';
						}
						elseif($campos=='Disponibilidade')
						{
							$sql .= '"'.addslashes($disp_temp).'"';
						}
						elseif($campos=='Data')
						{
							$sql .= '"'.$data.'"';
						}
						else
						{
							$sql .= '"'.addslashes($_POST[$campos]).'"';
						}
						$i++;
					}
				}
				$sql .= ')';
			}

			$res = Runquery($db_link, $DB_name, $sql);
			// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
		}
	}
}

// select array_to_string(array(select name from animals where id=5), ', ');

function estado_encomenda_rmas($estado_id=0, $lingua='')
{

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	if($estado_id == 0 || $estado_id=='') $estado_id = 1;

	$rs2 = new DataObj;
	$sql = 'SELECT ee.Cor, ee.Ordem, ee.Email, ee.f_leitura, ee.sms_envia, eel.Cabecalho, eel.Cabecalho2, eel.Texto, eel.Texto2, eel.Sms FROM rmas_estados ee INNER JOIN rmas_estados_lang eel ON eel.ID_PAI = ee.ID WHERE ee.ID='.$estado_id.' AND eel.Lang="'.$lingua.'"';
	// echo $sql;
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$Cabecalho = $rs2->db_data['Cabecalho'];
			$Cabecalho2 = $rs2->db_data['Cabecalho2'];
			$Ordem = $rs2->db_data['Ordem'];
			$Texto = $rs2->db_data['Texto'];
			$Texto2 = $rs2->db_data['Texto2'];
			$Sms = $rs2->db_data['Sms'];
			$Email = $rs2->db_data['Email'];
			$f_leitura = $rs2->db_data['f_leitura'];
			$sms_envia = $rs2->db_data['sms_envia'];
			$Cor = str_replace('#', '', $rs2->db_data['Cor']);

			$return = array('nivel' => $Ordem, 'titulo' => $Cabecalho, 'titulo2' => $Cabecalho2, 'texto' => $Texto, 'texto_novo' => $Texto2, 'texto_sms' => $Sms, 'cor' => $Cor, 'envia_mail' => $Email, 'envia_sms' => $sms_envia, 'readonly' => $f_leitura);

			$rs2->db_next();
		}
	}
	$rs2->db_close();

	return $return;
}

function estado_encomenda_rmas_fechados()
{

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$return = array();

	$rs2 = new DataObj;
	$sql = 'SELECT ee.ID FROM rmas_estados ee WHERE f_leitura = 1';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$ID = $rs2->db_data['ID'];
			$return[] = $ID;

			$rs2->db_next();
		}
	}
	$rs2->db_close();

	return $return;
}

function estado_encomenda_rmas_fechados_tipo($grupo)
{


	if($grupo == '-10') $gp = 0;
	elseif($grupo == '-20') $gp = 1;
	elseif($grupo == '-30') $gp = 2;
	elseif($grupo == '-40') $gp = 3;
	elseif($grupo == '-50') $gp = 4;
	elseif($grupo == '-60') $gp = 5;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$return = array();

	$rs2 = new DataObj;
	$sql = 'SELECT ee.ID FROM rmas_estados ee WHERE f_estado = '.$gp;

	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$ID = $rs2->db_data['ID'];
			$return[] = $ID;

			$rs2->db_next();
		}
	}
	$rs2->db_close();

	return $return;
}

function valida_telemovel($telephone)
{
	$return = 0;

	if (substr(trim($telephone), 0, 2) == '91')
	{
		$return = 1;
	}
	elseif(substr(trim($telephone), 0, 2) == '92')
	{
		$return = 1;
	}
	else if (substr(trim($telephone), 0, 2) == '93')
	{
		$return = 1;
	}
	else if (substr(trim($telephone), 0, 2) == '94')
	{
		$return = 1;
	}
	else if (substr(trim($telephone), 0, 2) == '95')
	{
		$return = 1;
	}
	elseif (substr(trim($telephone), 0, 2) == '96')
	{
		$return = 1;
	}

	return $return;
}

function send_sms_old($numero = 0, $numero2 = 0, $sms = '')
{
	$mobile = 0;
	if(valida_telemovel($numero))
	{
		$mobile = trim($numero);
	}
	elseif(valida_telemovel($numero2))
	{
		$mobile = trim($numero2);
	}

	if($mobile > 0 && trim($sms) != '' && strlen($mobile) == 9)
	{
		$params = array('apikey'    => '6166d69692d12d13dde92617e541b144b1f1907c',
                    'listID'    => '1',
                    'cellphone' => '351-'.trim($mobile),
                    // 'email'    	=> 'address@mailserver.com',
                    'message'   => trim($sms),
                    'subject'   => 'new sms message',
                    'from'    	=> 'DOT BRIGHT');
		// using Soap with SoapClient
		$client = new SoapClient('http://api.e-goi.com/v2/soap.php?wsdl');
		$result = $client->sendSMS($params);
	
		$file = '/home/dotbright/public_html/rmas/rmas/logs/sms_'.date('Ymdhis').'.txt';
		file_put_contents($file,serialize($params).' -- '.serialize($result));

	}
}

function send_sms2($numero = 0, $numero2 = 0, $sms = '')
{
	$mobile = 0;
	if(valida_telemovel($numero))
	{
		$mobile = trim($numero);
	}
	elseif(valida_telemovel($numero2))
	{
		$mobile = trim($numero2);
	}
	if($mobile > 0 && trim($sms) != '' && strlen($mobile) == 9)
	{
		file_get_contents('https://www.4movel.com/4movelservice/enviarsms.ashx?token=6qz69pfkxkqc0hs1a1exycg2sjwlj3lqrzdd7vu2&telefone='.trim($mobile).'&mensagem='.trim($sms));
	}
}

function send_sms_nos_old($numero = 0, $numero2 = 0, $mensagem = '')
{
	include 'lib/nusoap.php';
	$mobile = 0;
	if(valida_telemovel($numero))
	{
		$mobile = trim($numero);
	}
	elseif(valida_telemovel($numero2))
	{
		$mobile = trim($numero2);
	}
	if($mobile > 0 && strlen($mobile) >= 9 && trim($mensagem) != '')
	{
		// echo "entrou";
		// $endpoint = 'http://smspro.nos.pt/SmsPro/smsprows.asmx';
		$endpoint = 'http://mpro.nos.pt/SmsPro/smsprows.asmx';
	    $result = array(
	        'error' => '',
	        'data'  => array()
	    );
	    $mensagem = trim($mensagem);
	    $soapHeader = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body>';
	    $soapFooter = '</s:Body></s:Envelope>';
	    $soapstr = '';
	    $soapstr .='<SendSMSSelId xmlns="http://www.outsystems.com" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
			$soapstr .='<TenantName>dotbright1</TenantName>';
			$soapstr .='<strUsername>DOTBRIGHTLDA</strUsername>';
			$soapstr .='<strPassword>KgzaCr9u</strPassword>';
			$soapstr .='<MsisdnList>'.$numero.'</MsisdnList>';
			$soapstr .='<strMessage>'.$mensagem.'</strMessage>';
			$soapstr .='<strIdentifier>dotbright1</strIdentifier>';/* numero original */
			$soapstr .='<bolInSender>true</bolInSender>';
	    $soapstr .= '</SendSMSSelId>';
		if ($soapstr == '')
	    {
	        echo 'Não foram passados parametros';
	        exit;
	    }
	    else
	    {
	        $soapstr = $soapHeader . $soapstr . $soapFooter;
			$client = new nusoap_client($endpoint, false, false, false, false, false, 0, 300); //se nao existir temos de fazer include
	        $err= $client->getError();
	        if ($err)
	        {
				$result['error'] = $err;
	            return $result;
	        }

	        $client->setUseCurl(0);
	        $client->soap_defencoding = 'UTF-8';
	        $client->decode_utf8 = false;
	        $client->encode_utf8 = true;
	        $client->useHTTPPersistentConnection();
	        // $soapResult = $client->send($soapstr, 'http://SmsPro/SMSPROWS/SendSMSSelId');
			$soapResult = $client->send($soapstr, 'http://mpro/SMSPROWS/SendSMSSelId');
	        $result = $soapResult;
	        if ($client->fault)
	        {
	            $result['error'] = print_r($soapResult, true);
	        }
	        else
	        {
	            $err = $client->getError();

				if ($err)
				{
					echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
				} 
	        }
	    }
	}
	if($soapResult['ReturnCode'] == '999') return 1;
	else return 0;
}

/*
function sms_nos2_get_bearer()
{
	if(file_exists('public_html/NOS_Token.txt'))
	{
		$response = base64_decode(file_get_contents('public_html/NOS_Token.txt'));
		$dados=json_decode($response,true);

		$issued_at=$dados['issued_at'];
		$expires_in=$dados['expires_in'];

		$currentTimeMs = round(microtime(true) * 1000); // Tempo atual em milissegundos
	  	$expiryTimeMs = $issued_at + ($expires_in * 1000); // Tempo de expiração em milissegundos
	  	if($currentTimeMs < $expiryTimeMs)
	  	{
				return $response;
	  	}
	}
	$key='1219036e-084d-4259-b6ef-400a81701495';
	$secret='5159ca47-9a68-4cc7-98d4-fdfd2b549701';
	$uri = 'https://api-mpro.apg.nos.pt/common/v1/auth/accessToken';
	$payload = json_encode([
	    'key' => $key,
	    'secret' => $secret
	]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($payload)
	]);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);

	file_put_contents('public_html/NOS_Token.txt',base64_encode($response));
	curl_close ($ch);
	return $response;
}

function send_sms_nos($numero = 0, $numero2 = 0, $mensagem = '', $token = '')
{
    $key = '1219036e-084d-4259-b6ef-400a81701495';

    $res = sms_nos2_get_bearer();
    $app_client = json_decode($res, true);

    if (!isset($app_client['access_token']))
        return 0;

    $access_token = $app_client['access_token'];

    $mobile = 0;

    if (valida_telemovel($numero)) $mobile = trim($numero);
    elseif (valida_telemovel($numero2)) $mobile = trim($numero2);

    if ($mobile == 0 || trim($mensagem) == '') return 0;

    // Formato E.164
    if (substr($mobile, 0, 1) != '+') $mobile = '+351' . $mobile;

    $endpoint = 'https://api-mpro.apg.nos.pt/communication/v1/sms';

    $payload = [
        'recipients' => [
            [
                'to' => $mobile
            ]
        ],
        'sender' => 'DOTBRIGHT',
        'title'  => 'DOTBRIGHT',
        'body'   => trim($mensagem),
        'smartEncode' => true
    ];

    $payload = json_encode($payload);

    $ch = curl_init($endpoint);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer '.$access_token,
            'X-ApiKey: '.$key,
            'Content-Length: '.strlen($payload)
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch))
    {
        curl_close($ch);
        return 0;
    }

    curl_close($ch);

    // log para debug
    file_put_contents(
        '/home/dotbright/public_html/rmas/rmas/logs/sms_'.date('YmdHis').'.txt',
        $payload."\n\n".$response
    );

    if ($httpCode == 200 || $httpCode == 201 || $httpCode == 202)
        return 1;

    return 0;
}
*/

function enviaEmail_teste()
{
	$id=31015;
	$tipo=1;
	$log_id=0;
	$fake = 0;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$Estado = '';
	if($tipo == 0)  // ENVIO DE EMAIL DO ESTADO DO RMA
	{
		$sql = 'SELECT
					ID as Tracking, m_estado, cliente_nome, cliente_apelido, cliente_username, m_marca, m_modelo, m_origem, cliente_morada, cliente_cp,
					total, m_observacoestec_relatorio, cliente_telefone1, cliente_telefone2
				FROM
					rmas r
				WHERE
					r.ID="'.$id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$m_estado = $rs->db_data['m_estado'];
			$total = number_format($rs->db_data['total'], 2);
			$m_observacoestec_relatorio = $rs->db_data['m_observacoestec_relatorio'];
			$cliente_telefone1 = trim($rs->db_data['cliente_telefone1']);
			$cliente_telefone2 = trim($rs->db_data['cliente_telefone2']);
			$estados_rmas = estado_encomenda_rmas($m_estado, 'pt');
			$titulo = $estados_rmas['titulo'];
			$titulo2 = $estados_rmas['titulo2'];
			$texto = ($estados_rmas['texto_novo']!='' ? $estados_rmas['texto_novo'] : $estados_rmas['texto']);
			$texto_sms = $estados_rmas['texto_sms'];
			$envia_mail = $estados_rmas['envia_mail'];
			$envia_sms = $estados_rmas['envia_sms'];
			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['rmas_page_id']);

			$Tracking = "DB".str_pad((int) $rs->db_data['Tracking'], 6, "0", STR_PAD_LEFT);
			$assinatura  = 'DotBright · telecomunicações<br><br>Loja da Maia<br>Avenida Visconde Barreiros 167<br>4470-151 Maia<br>Telefone: 229 429 211<br><br>';
			$assinatura .= '<br>Loja de Matosinhos<br>Avenida Roberto Ivens, 879<br>4450-256 Matosinhos <br>Telefone: 220 142 941<br>';

			$texto = str_replace('[nome]', $rs->db_data['cliente_nome'], $texto);
			$texto = str_replace('[apelido]', $rs->db_data['cliente_apelido'], $texto);
			$texto = str_replace('[marca]', $rs->db_data['m_marca'], $texto);
			$texto = str_replace('[modelo]', $rs->db_data['m_modelo'], $texto);
			$texto = str_replace('[registo_numero]', $Tracking, $texto);
			$texto_sms = str_replace('[registo_numero]', $Tracking, $texto_sms);

			// LOJA DA MAIA É DEFAULT
			$texto = str_replace('[local]', ($rs->db_data['m_origem']==1 ? 'Loja da Maia' : ($rs->db_data['m_origem']==2 ? 'Loja de Matosinhos' : ($rs->db_data['m_origem']==3 ? 'Loja da Maia' : ''))), $texto);
			$texto = str_replace('[horario_loja_local]', ($rs->db_data['m_origem']==1 ? ', 2ª a Sábado das 10h00 às 20h00' : ($rs->db_data['m_origem']==2 ? ', 2ª a 6ª das 10h00 às 13h00 e das 14h00 às 19h00, encerra aos sábados e domingos.' : ($rs->db_data['m_origem']==3 ? ', 2ª a Sábado das 10h00 às 20h00' : ''))), $texto);

			 
			$texto = str_replace('[morada_envio]', $rs->db_data['cliente_morada'].', '.$rs->db_data['cliente_cp'], $texto);
			$texto = str_replace('[assinatura]', $assinatura, $texto);
			$texto = str_replace('[relatorio_tecnico]', $m_observacoestec_relatorio, $texto);
			$texto = str_replace('[valor_reparacao]', $total.' €', $texto);

			$m_email = $rs->db_data['cliente_username'];
			$message = template_email_rmas($GLOBALS['template_rma_estado'], $texto);


			print_r($message);

	//$m_email='aderitofernandes@gmail.com';

			$ml = 0;$ml_txt = ''; $sm = 0;
		//	send_email_rma($m_email, 'Estado do seu pedido de assistência técnica '.$Tracking, $message['body']);
			
		}
		$rs->db_close();
	}
	elseif($tipo== 1)
	{
		$sql = '
				SELECT
					ID as Tracking, m_estado, cliente_nome, cliente_apelido, cliente_username,m_observacoestec,m_valor
				FROM
					rmas r
				WHERE
					r.ID="'.$id.'"
				';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$m_observacoestec = stripslashes($rs->db_data['m_observacoestec']);
			$m_valor = $rs->db_data['m_valor'];

			$Tracking = "DB".str_pad((int) $rs->db_data['Tracking'], 6, "0", STR_PAD_LEFT);

			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['rmas_page_id']);

			$body .= 'Caro(a) '.$rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_apelido'];
			$body .= '<br><br>';
			$body .= str_replace("\n", '<br>', $m_observacoestec);
			$body .= '<br><br>';
			$body .= 'Valor do Orçamento: €'.number_format($m_valor, 2, ',', '');
			$body .= '<br><br>';
			$body .= '<a href="'.$p_seo.'">Clique aqui para aceder ao seu pedido de assistência técnica</a>';

			$m_email = $rs->db_data['cliente_username'];
	//$m_email='aderitofernandes@gmail.com';
			
			$message = template_email_rmas($GLOBALS['template_rma_estado'], $body);

			//send_email_rma($m_email, 'Orçamento para reparação do equipamento '.$Tracking, $message['body']);
			//send_email_rma('aderitofernandes@gmail.com', 'Orçamento para reparação do equipamento '.$Tracking, $message['body']);
		}
		$rs->db_close();

	}
	elseif($tipo== 2) // EMAIL DADOS CLIENTE
	{
		$sql = 'SELECT * FROM clientes WHERE ID="'.$id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$username = $rs->db_data['Username'];
			$password = base64_decode($rs->db_data['Password']);
			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']);

			$m_email = $rs->db_data['Username'];
	//$m_email='aderitofernandes@gmail.com';

			$body .= 'Foi criada uma conta no site dotbright.pt para que possa aceder ao estado de reparação do seu equipamento:<br><br>';
			$body .= 'Nome: '.$rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
			$body .= '<br>';
			$body .= 'Username: '.$username;
			$body .= '<br><br>';
			$body .= 'Password: '.$password;
			$body .= '<br><br>';
			$body .= '<a href="'.$p_seo.'?show_rmas=1">Clique aqui para aceder à sua área reservada</a>';

			$message = template_email_rmas($GLOBALS['template_rma_novouser'], $body);			
			//send_email_rma($m_email, 'Criação de conta em dotbright.pt ', $message['body']);
			//send_email_rma('aderitofernandes@gmail.com', 'Criação de conta em dotbright.pt ', $message['body']);
		}
		$rs->db_close();
	}
}

function enviaEmail($id, $tipo, $log_id=0, $fake = 0, $nao_envio_email = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$Estado = '';
	if($tipo == 0)  // ENVIO DE EMAIL DO ESTADO DO RMA
	{
		$sql = 'SELECT
					ID as Tracking, m_estado, cliente_nome, cliente_apelido, cliente_username, m_marca, m_modelo, m_origem, cliente_morada, cliente_cp,
					total, m_observacoestec_relatorio, cliente_telefone1, cliente_telefone2
				FROM
					rmas r
				WHERE
					r.ID="'.$id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$m_estado = $rs->db_data['m_estado'];
			$total = number_format($rs->db_data['total'], 2);
			$m_observacoestec_relatorio = $rs->db_data['m_observacoestec_relatorio'];
			$cliente_telefone1 = trim($rs->db_data['cliente_telefone1']);
			$cliente_telefone2 = trim($rs->db_data['cliente_telefone2']);
			$estados_rmas = estado_encomenda_rmas($m_estado, 'pt');
			$titulo = $estados_rmas['titulo'];
			$titulo2 = $estados_rmas['titulo2'];
			$texto = ($estados_rmas['texto_novo']!='' ? $estados_rmas['texto_novo'] : $estados_rmas['texto']);
			$texto_sms = $estados_rmas['texto_sms'];
			$envia_mail = $estados_rmas['envia_mail'];
			$envia_sms = $estados_rmas['envia_sms'];
			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['rmas_page_id']);

			$Tracking = "DB".str_pad((int) $rs->db_data['Tracking'], 6, "0", STR_PAD_LEFT);
			$assinatura  = 'DotBright · telecomunicações<br><br>Loja da Maia<br>Avenida Visconde Barreiros 167<br>4470-151 Maia<br>Telefone: 229 429 211<br><br>';
			$assinatura .= '<br>Loja de Matosinhos<br>Avenida Roberto Ivens, 879<br>4450-256 Matosinhos <br>Telefone: 220 142 941<br>';

			$texto = str_replace('[nome]', $rs->db_data['cliente_nome'], $texto);
			$texto = str_replace('[apelido]', $rs->db_data['cliente_apelido'], $texto);
			$texto = str_replace('[marca]', $rs->db_data['m_marca'], $texto);
			$texto = str_replace('[modelo]', $rs->db_data['m_modelo'], $texto);
			$texto = str_replace('[registo_numero]', $Tracking, $texto);
			$texto_sms = str_replace('[registo_numero]', $Tracking, $texto_sms);

			// LOJA DA MAIA É DEFAULT
			$texto = str_replace('[local]', ($rs->db_data['m_origem']==1 ? 'Loja da Maia' : ($rs->db_data['m_origem']==2 ? 'Loja de Matosinhos' : ($rs->db_data['m_origem']==3 ? 'Loja da Maia' : ''))), $texto);
			$texto = str_replace('[horario_loja_local]', ($rs->db_data['m_origem']==1 ? ', 2ª a Sábado das 10h00 às 20h00' : ($rs->db_data['m_origem']==2 ? ', 2ª a 6ª das 10h00 às 13h00 e das 14h00 às 19h00, encerra aos sábados e domingos.' : ($rs->db_data['m_origem']==3 ? ', 2ª a Sábado das 10h00 às 20h00' : ''))), $texto);
			$texto = str_replace('[morada_envio]', $rs->db_data['cliente_morada'].', '.$rs->db_data['cliente_cp'], $texto);
			$texto = str_replace('[assinatura]', $assinatura, $texto);
			$texto = str_replace('[relatorio_tecnico]', $m_observacoestec_relatorio, $texto);
			$texto = str_replace('[valor_reparacao]', $total.' €', $texto);

			$m_email = $rs->db_data['cliente_username'];
			$message = template_email_rmas($GLOBALS['template_rma_estado'], $texto);

			$ml = 0;$ml_txt = ''; $sm = 0;
			$notification_policy = rma_notification_policy($fake, $nao_envio_email, $envia_mail, $texto, $envia_sms, $texto_sms);
			if($notification_policy['email'])
			{
				send_email_rma($m_email, 'Estado do seu pedido de assistência técnica '.$Tracking, $message['body']);
			}
			if($notification_policy['sms'])
			{
				$sms_result = rma_send_sms_notice($cliente_telefone1, $cliente_telefone2, (trim($texto_sms)));
				if(empty($sms_result['success']))
				{
					error_log('[RMA SMS] tracking='.$Tracking.' request_id='.(isset($sms_result['request_id']) ? $sms_result['request_id'] : 'indisponivel').' status='.(isset($sms_result['status']) ? (string)$sms_result['status'] : 'sem_estado'));
				}
			}
		}
		$rs->db_close();
	}
	elseif($tipo== 1)
	{
		$sql = '
				SELECT
					ID as Tracking, m_estado, cliente_nome, cliente_apelido, cliente_username,m_observacoestec,m_valor
				FROM
					rmas r
				WHERE
					r.ID="'.$id.'"
				';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$m_observacoestec = stripslashes($rs->db_data['m_observacoestec']);
			$m_valor = $rs->db_data['m_valor'];

			$Tracking = "DB".str_pad((int) $rs->db_data['Tracking'], 6, "0", STR_PAD_LEFT);

			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['rmas_page_id']);

			$body .= 'Caro(a) '.$rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_apelido'];
			$body .= '<br><br>';
			$body .= str_replace("\n", '<br>', $m_observacoestec);
			$body .= '<br><br>';
			$body .= 'Valor do Orçamento: €'.number_format($m_valor, 2, ',', '');
			$body .= '<br><br>';
			$body .= '<a href="'.$p_seo.'">Clique aqui para aceder ao seu pedido de assistência técnica</a>';

			$m_email = $rs->db_data['cliente_username'];
			$message = template_email_rmas($GLOBALS['template_rma_estado'], $body);

			send_email_rma($m_email, 'Orçamento para reparação do equipamento '.$Tracking, $message['body']);
			//send_email_rma('aderitofernandes@gmail.com', 'Orçamento para reparação do equipamento '.$Tracking, $message['body']);
		}
		$rs->db_close();

	}
	elseif($tipo== 2) // EMAIL DADOS CLIENTE
	{
		$sql = 'SELECT * FROM clientes WHERE ID="'.$id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$username = $rs->db_data['Username'];
			$password = base64_decode($rs->db_data['Password']);
			$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']);

			$m_email = $rs->db_data['Username'];

			$body .= 'Foi criada uma conta no site dotbright.pt para que possa aceder ao estado de reparação do seu equipamento:<br><br>';
			$body .= 'Nome: '.$rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
			$body .= '<br>';
			$body .= 'Username: '.$username;
			$body .= '<br><br>';
			$body .= 'Password: '.$password;
			$body .= '<br><br>';
			$body .= '<a href="'.$p_seo.'?show_rmas=1">Clique aqui para aceder à sua área reservada</a>';

			$message = template_email_rmas($GLOBALS['template_rma_novouser'], $body);			
			send_email_rma($m_email, 'Criação de conta em dotbright.pt ', $message['body']);
			//send_email_rma('aderitofernandes@gmail.com', 'Criação de conta em dotbright.pt ', $message['body']);
		}
		$rs->db_close();
	}
}

function template_email_rmas($template_id, $body=array(''),$width="800px", $align=0, $body2='', $logo_off = 0, $assunto='')
{
	$template_img  = '';
	$body2 = '<span style="font-size:12px; font-family:arial,sans-serif">'.$body.'</span>';
	$template_nome2 = '';
	$message .= $body2;

	$message = str_replace('font-size:12px','font-size:16px',$message);

	if($align==1) $message = '<div style="font-family:arial;color:#211d1e;font-size:16px;text-align:left">'.$message.'</div>';
	else $message = '<div style="font-family:arial;color:#211d1e;font-size:16px;width:'.$width.';">'.$message.'</div>';
	return array('subject' => $template_nome2, 'body' => email_html_rmas($logo_off, $message));
}

function email_html_rmas($logo_off=0, $message='')
{
	$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>[SUBJECT]</title>
	<style type="text/css">
	body {
		padding-top:0 !important;
		padding-bottom:0 !important;
		padding-top: 0 !important;
		padding-bottom: 0 !important;
		margin:0 !important;
		width: 100% !important;
		-webkit-text-size-adjust: 100% !important;
		-ms-text-size-adjust: 100% !important;
		-webkit-font-smoothing: antialiased !important;
	}
	.tableContent img {    border: 0 !important;    display: block !important;    outline: none !important;  }
	a{ color:#382F2E;}
	p, h1,h2,ul,ol,li,div{   margin:0;   padding:0; }
	h1,h2{   font-weight: normal;   background:transparent !important;   border:none !important; }
	.contentEditable h2.big,.contentEditable h1.big{ font-size: 26px !important; }
	.contentEditable h2.bigger,.contentEditable h1.bigger{ font-size: 37px !important; }
	td,table{   vertical-align: top; }
	td.middle{   vertical-align: middle; }
	a.link1{   font-size:16px;   color:#27A1E5;   line-height: 24px;   text-decoration:none; }
	a{   text-decoration: none; }
	.link2{ color:#ffffff; border-top:10px solid #27A1E5; border-bottom:10px solid #27A1E5; border-left:18px solid #27A1E5; border-right:18px solid #27A1E5; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; background:#27A1E5; }
	.link3{ color:#555555; border:1px solid #cccccc; padding:10px 18px; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; background:#ffffff; }
	.link4{ color:#27A1E5; line-height: 24px; }
	h2,h1{ line-height: 20px; }
	p{   font-size: 16px;   line-height: 21px;   color:#AAAAAA; }
	.contentEditable li{}
	.appart p{}
	.bgItem{background: #ffffff;}
	.bgBody{background: #ffffff;}
	img {outline:none;   text-decoration:none;-ms-interpolation-mode: bicubic; clear: both;   display: block;   float: none; }
	</style>

	</head>
	<body paddingwidth="0" paddingheight="0" bgcolor="#FFFFFF"  style="background:#FFFFFF;padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;" offset="0" toppadding="0" leftpadding="0">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td><table width="800" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style="font-family:helvetica" class="MainContainer">
	<tbody>
	<tr>
	<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td valign="top" width="20">&nbsp;</td>
	<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td class="movableContentContainer">
	<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td height="15"></td>
	</tr>
	<tr>
	<td>';
	if($logo_off == 0)
	{
		$return .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td valign="top" align="left"><a href="'.$GLOBALS['site_http_url'].'"><img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" border="0" alt="Logo" title="dotbright.pt" data-max-width="100" /></a></td>
		<td width="10" valign="top">&nbsp;</td>
		<td valign="middle" style="vertical-align: middle;">
		<div class="contentEditableContainer contentTextEditable">
		<div class="contentEditable" style="text-align: left;font-weight: light; color:#555555;font-size:26;line-height: 39px;font-family: Helvetica Neue;"></div>
		</div>
		</td>
		</tr>
		</tbody>
		</table>
		</td>
		</tr>
		</tbody>
		</table>';
	}
	$return .= '
	</td>
	</tr>

	</tbody>
	</table>
	</div>
	<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td height="40"></td>
	</tr>
	<tr>
	<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td width="40" valign="top">&nbsp;</td>
	<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr><td height="25"></td></tr>
	<tr>
	<td>
	<div class="contentEditableContainer contentTextEditable">
	'.$message.'
	</div>
	</td>
	</tr>
	<tr><td height="24"></td></tr>
	</table></td>
	<td width="40" valign="top">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
	<tr>
	<td height="40"></td>
	</tr>

	</tbody>
	</table>
	</td>
	</tr>
	<tr>
	<td height="40"></td>
	</tr>
	</tbody>
	</table>
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
	<td valign="top" width="20">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	</td>
	</tr>
	</tbody>
	</table>
	</td>
	</tr>
	</tbody>
	</table>
	</body>
	</html>';

	return $return;
}

/*--------------------------------------------------------------------------------------------------------------------------------------------------------*/

function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);



	// print_r($_POST);

	exit;
	foreach ($_POST as $key => $value)
	{
		if($key == "Submit")
		{
			$campos = substr($campos, 0, -2);
			$valores = substr($valores, 0, -2);
		}
		else
		{
			if($key != "nao_envio_email" && $key != "m_lang" && $key != "slashes" && $key != "tab_a" && $key != "tab_b" && $key != "ID" && $key != "qtd" && $key != "ref" && $key != "desc" && $key != "Submit")
			{
				if($key == "nova_marca" && $value != "")
				{
					$sql = 'INSERT INTO marcas_rmas (marca) VALUES ("'.$value.'");';
					Runquery($db_link, $DB_name, $sql);

					$nova_marca = $value;
				}
				if($key == "novo_modelo" && $value != "")
				{
					$sql = 'INSERT INTO modelos_rmas (ID, modelo) VALUES ("","'.$value.'");';
					Runquery($db_link, $DB_name, $sql);
					$novo_modelo = $value;
				}
				if($key == "m_marca" && $nova_marca != "") {
					$campos  .= $key.", ";
					$valores .= "'".$nova_marca."', ";
				}
				else if($key == "m_modelo" && $novo_modelo != "") {
					$campos  .= $key.", ";
					$valores .= "'".$novo_modelo."', ";
				}
				else if($key == "nova_marca" || $key == "novo_modelo")
				{
					// NÂO FAZ NADA
				}
				else
				{
					$campos .= $key.", ";
					$valores .= "'".$value."', ";
				}
			}
		}
	}
	//Cria cliente se não existir
	$ID_Cliente=$_POST['ID_Cliente'];
	if($ID_Cliente=='')
	{
		$cliente_nif=str2SQLstr($_POST['cliente_nif']);
		$cliente_nome=str2SQLstr($_POST['cliente_nome']);
		$cliente_sobrenome=str2SQLstr($_POST['cliente_sobrenome']);
		$cliente_apelido=str2SQLstr($_POST['cliente_apelido']);
		$cliente_morada=str2SQLstr($_POST['cliente_morada']);
		$cliente_cp=str2SQLstr($_POST['cliente_cp']);
		$cliente_username=str2SQLstr($_POST['cliente_username']);
		$cliente_telefone=str2SQLstr($_POST['cliente_telefone']);
		$cliente_empresa=str2SQLstr($_POST['cliente_empresa']);
		$cliente_password=str2SQLstr(base64_encode(uniqid("")));

		$sql_insert_cliente='INSERT INTO clientes (Username, Password, Nome, Sobrenome, Apelido, Empresa, Morada, Codigo_Postal, Telefone, Email, Nif, Data, Ativo, Estado)
		VALUES ('.$cliente_username.', '.$cliente_password.', '.$cliente_nome.', '.$cliente_sobrenome.', '.$cliente_apelido.', '.$cliente_empresa.', '.$cliente_morada.', '.$cliente_cp.', '.$cliente_telefone.', '.$cliente_username.', '.$cliente_nif.', "'.date('Y-m-d H:i:s').'","1","1")';
		$res=Runquery($db_link,$DB_name,$sql_insert_cliente);
		if(!$res)echo db_error($db_link);
		else
		{
			$ID_Cliente=db_get_last_insert_id($db_link);
			//TODO Envia E-mail de novo cliente criado
			enviaEmail($ID_Cliente,2, 0, $_POST['fake']);
		}
	}

	$sql_insert = "INSERT INTO rmas ($campos) VALUES ($valores);";
	$res = Runquery($db_link, $DB_name, $sql_insert);

	if(!$res)echo db_error($db_link);
	else
	{
		$new_id = db_get_last_insert_id($db_link);
		grava_rma_log($new_id, $_POST["m_estado"]);

		// TODO codigo de inserção de linhas
		$qtd = $_POST['qtd'];
		$ref = $_POST['ref'];
		$desc = $_POST['desc'];
		$prec = $_POST['prec'];
		$disp = $_POST['disp'];

		$sql_delete_linhas = 'DELETE FROM rmas_linhas WHERE ID_ORG="'.$new_id.'"';
		$res1 = Runquery($db_link, $DB_name, $sql_delete_linhas);
		for($i=0; $i < count($qtd); $i++)
		{
			if($qtd[$i] > 0)
			{
				$qtd_temp = str2SQLstr($qtd[$i]);
				$ref_temp = str2SQLstr($ref[$i]);
				$desc_temp = str2SQLstr($desc[$i]);
				$prec_temp = str2SQLstr($prec[$i]);
				$disp_temp = str2SQLstr($disp[$i]);

				$sql_insert_linhas = '
								INSERT INTO rmas_linhas
									(ID_ORG, Ref, Cabecalho, Quantidade, Preco, Disponibilidade, Data)
								VALUES
									("'.$new_id.'", '.$ref_temp.', '.$desc_temp.', '.$qtd_temp.', '.$prec_temp.', '.$disp_temp.', "'.date('Y-m-d H:i:s').'")';
				$res1 = Runquery($db_link,$DB_name,$sql_insert_linhas);
				if(!$res1) echo db_error($db_link);
			}
		}
	}

	// print_r($_POST);
	if($_POST['envio_email']) enviaEmail($new_id, 0, 0, $_POST['fake']);
	if($_POST['envio_email_diagnostico']) enviaEmail($new_id, 1, 0, $_POST['fake']);

	db_closedb_conection($db_link);
	return($new_id);
}

function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$and_loja;
	$id=$_POST['ID'];
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from rmas where ID=$id $and_loja");
	if(!$res){echo db_error($db_link);}
	else{
		$res1=Runquery($db_link,$DB_name,"delete from rmas_linhas where ID_PAI=$id");
		if(!$res1){echo db_error($db_link);}
	}
	db_closedb_conection($db_link);
	return($res);
}

function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$id=$_POST['ID'];
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from rmas WHERE ID=$id $and_loja");
	if(!$rs->EOF())
	{
		$estado_old = $rs->db_data['m_estado'];
		$altera=1;
	}
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord();
	}
	else
	{
		foreach ($_POST as $key => $value) {
			if($key == "Submit") {
				$campos = substr($campos, 0, -2);
				$valores = substr($valores, 0, -2);
			}
			else {
				if($key != "nao_envio_email" && $key != "m_lang" && $key != "slashes" && $key != "tab_a" && $key != "tab_b" && $key != "ID" && $key != "qtd" && $key != "ref" && $key != "desc" && $key != "Submit") {
					if($key == "nova_marca" && $value != "") {
						$sql = 'INSERT INTO marcas_rmas (ID, marca) VALUES ("", "'.$value.'");';
						$db_marca = new DataObj();
                        $db_marca->db_open($db_link,$DB_name,$sql);
						$nova_marca = $value;
					}
					if($key == "novo_modelo" && $value != "") {
						$sql = 'INSERT INTO modelos_rmas (ID, modelo) VALUES ("","'.$value.'");';
						$db_modelo = new DataObj();
                        $db_modelo->db_open($db_link,$DB_name,$sql);
						$novo_modelo = $value;
					}
					if($key == "m_marca" && $nova_marca != "") {
						$campos  .= $key.", ";
						$valores .= "'".$nova_marca."', ";
					}
					else if($key == "m_modelo" && $novo_modelo != "") {
						$campos  .= $key.", ";
						$valores .= "'".$novo_modelo."', ";
					}
					else if($key == "nova_marca" || $key == "novo_modelo") {
						//é só para nao fazer nada;
					}
					else{
					$campos  .= $key.", ";
					$valores .= "'".$value."', ";
					}
				}
			}
		}
		$campos = explode(', ', $campos);
		$valores = explode(', ', $valores);
		$size = count($campos);
		for($i = 0; $i < $size;$i++){
			$SET .= $campos[$i]." = ".$valores[$i].", ";
		}
		$SET = substr($SET, 0, -2);
		$sql_update = 'UPDATE rmas SET '.$SET.' WHERE ID="'.$id.'"';
		//echo $sql_update;
		$res=Runquery($db_link,$DB_name,$sql_update);
		if(!$res)echo db_error($db_link);
		else
		{
			//TODO codigo de inserção de linhas
			grava_rma_log($id, $_POST["m_estado"]);
			$qtd=$_POST['qtd'];
			$ref=$_POST['ref'];
			$desc=$_POST['desc'];
			$prec=$_POST['prec'];
			$disp=$_POST['disp'];
			$sql_delete_linhas='DELETE FROM rmas_linhas WHERE ID_ORG="'.$id.'"';
			$res1=Runquery($db_link,$DB_name,$sql_delete_linhas);
			for($i=0;$i < count($qtd);$i++)
			{
				if($qtd[$i] > 0)
				{
					$qtd_temp=str2SQLstr($qtd[$i]);
					$ref_temp=str2SQLstr($ref[$i]);
					$desc_temp=str2SQLstr($desc[$i]);
					$prec_temp=str2SQLstr($prec[$i]);
					$disp_temp=str2SQLstr($disp[$i]);
					$sql_insert_linhas='INSERT INTO rmas_linhas (ID_ORG, Ref, Cabecalho, Quantidade, Preco, Disponibilidade, Data) VALUES
								("'.$id.'", '.$ref_temp.', '.$desc_temp.', '.$qtd_temp.', '.$prec_temp.', '.$disp_temp.', "'.date('Y-m-d H:i:s').'")';
					$res1=Runquery($db_link,$DB_name,$sql_insert_linhas);
					if(!$res1)echo db_error($db_link);
				}
			}
		}
		db_closedb_conection($db_link);
		if($_POST['envio_email']) enviaEmail($id, 0, 0, $_POST['fake']);
		return($res);
	}
}

/* function enviaEmail($id, $tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	if($tipo == 0)
	{

		$sql='SELECT r.ID as Tracking, r.Estado as RMAEstado,r.RelatorioIntervencao, r.ObservacoesTecnico, c.* FROM rmas r INNER JOIN clientes c ON r.ID_Cliente=c.ID WHERE r.ID="'.$id.'"';
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$Tracking="STG".str_pad((int) $rs->db_data['Tracking'],6,"0",STR_PAD_LEFT);
			$txt_obs = '';
			switch ($rs->db_data['RMAEstado'])
			{
				case 0: $Estado=write_lang('key_rmas_estado0','Aguarda diagnóstico');break;
				case 1: $Estado=write_lang('key_rmas_estado1','Aguarda aceitação');break;
				case 2: $Estado=write_lang('key_rmas_estado2','Aguarda reparação');break;
				case 3: $Estado=write_lang('key_rmas_estado3','Aguarda peça');break;
				case 4:
					$Estado=write_lang('key_rmas_estado7','Aguarda peça para despiste');
					$txt_obs = '<br><br>Observações: '.str_replace("\n", '<br>', $rs->db_data['RelatorioIntervencao']);
					break;
				case 5:
					$Estado=write_lang('key_rmas_estado4','Pronto para entrega');
					$txt_obs = '<br><br>Observações: '.str_replace("\n", '<br>', $rs->db_data['ObservacoesTecnico']);
					break;
				case 6: $Estado=write_lang('key_rmas_estado5','Concluído');break;
				case 7: $Estado=write_lang('key_rmas_estado6','Recusado');break;
				case 8:
					$Estado=write_lang('key_rmas_estado8','Sem Reparação - Pronto para levantar');
					$txt_obs = '<br><br>Observações: '.str_replace("\n", '<br>', $rs->db_data['ObservacoesTecnico']);
				break;
				case 9: $Estado=write_lang('key_rmas_estado9','Aguarda assistência eletrónica');break;
			}

			$p_seo=seo('pages.php?page_id='.$GLOBALS['rmas_page_id']);
			$body=array(
				'[nome]'=> $rs->db_data['Nome'],
				'[apelido]'=> $rs->db_data['Apelido'],
				'[tracking]'=> $Tracking,
				'[estado]'=> $Estado,
				'[observacoes]'=> $txt_obs,
				'[url]'=> '<a href="'.$p_seo.'">clique aqui</a>'
			);

			$m_email=$rs->db_data['Username'];
			$message=template_email($GLOBALS['template_rma_estado'], $body);
			global $EmailFrom,$Default_RMAS_Email;
			$EmailFrom=$Default_RMAS_Email;
			send_email_rma($m_email, $message['subject'], $message['body']);
		}
	}else if($tipo== 1){
		//TODO envio de e-mail do diagnóstico

	}else if($tipo== 2){
		// TODO envio de e-mail de novo cliente

		$sql='SELECT * FROM clientes WHERE ID="'.$id.'"';
		//echo $sql;
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()){
			$username=$rs->db_data['Username'];
			$password=base64_decode($rs->db_data['Password']);
			$p_seo=seo('pages.php?page_id='.$GLOBALS['registo_page_id']);

			$body=array
					(
						'[nome]'=> $rs->db_data['Nome'],
						'[apelido]'=> $rs->db_data['Apelido'],
						'[username]'=> $username,
						'[password]'=> $password,
						'[url]'=> '<a href="'.$p_seo.'">clique aqui</a>'
					);

			$m_email=$rs->db_data['Username'];

			$message=template_email($GLOBALS['template_rma_novouser'], $body);
			global $EmailFrom,$Default_RMAS_Email;
			$EmailFrom=$Default_RMAS_Email;
			send_email_rma($m_email, $message['subject'], $message['body']);
		}

	}
} */

/*function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$cond='';

	switch ($_GET['f_estado'])
	{
		case 0:
			$cond .= '';
			break;
		case 1:
			$cond .= ' AND rmas.Estado="0" ';
			break;
		case 2:
			$cond .= ' AND rmas.Estado="1" ';
			break;
		case 3:
			$cond .= ' AND rmas.Estado="2" ';
			break;
		case 4:
			$cond .= ' AND rmas.Estado="3" ';
			break;
		case 5:
			$cond .= ' AND rmas.Estado="4" ';
			break;
		case 6:
			$cond .= ' AND rmas.Estado="5" ';
			break;
		case 7:
			$cond .= ' AND rmas.Estado="6" ';
			break;
		case 8:
			$cond .= ' AND rmas.Estado="7" ';
			break;
		case 9:
			$cond .= ' AND rmas.Estado="8" ';
			break;
		case 10:
			$cond .= ' AND rmas.Estado="9" ';
			break;
	}
	if(isset($_GET['f_obs']) && $_GET['f_obs'] != '') $cond .= 'AND rmas.Obsint LIKE "%'.$_GET['f_obs'].'%"';
	if(isset($_GET['f_tracking']) && $_GET['f_tracking'] != '') $cond .= ' AND rmas.ID LIKE "%'.$_GET['f_tracking'].'%" ';
	if(isset($_GET['f_ref']) && $_GET['f_ref'] != '') $cond .= ' AND rmas.Referencia LIKE "%'.$_GET['f_tracking'].'%" ';
	if(isset($_GET['f_nrserie']) && $_GET['f_nrserie'] != '') $cond .= ' AND (rmas.NSerie LIKE "%'.$_GET['f_nrserie'].'%" OR rmas.Modelo LIKE "%'.$_GET['f_nrserie'].'%") ';
	if(isset($_GET['f_data1']) && $_GET['f_data1'] != '') $cond .= ' AND rmas.DataUpdate LIKE "%'.$_GET['f_data1'].'%" ';
	if(isset($_GET['f_cli']) && $_GET['f_cli'] != '')
	{
		$v_nomecli = explode(' ', rtrim(ltrim($_GET['f_cli'])));
		if(count($v_nomecli)>1){
			$cond .= ' AND (';
			for ($i=0;$i < count($v_nomecli);$i++) {
				for ($j=0;$j < count($v_nomecli);$j++) {
					$cond .= ' (clientes.Nome REGEXP "[[:<:]]'.$v_nomecli[$i].'[[:>:]]" AND clientes.Apelido  REGEXP "[[:<:]]'.$v_nomecli[$j].'[[:>:]]") OR ';
				}
			}
			$cond = substr($cond, 0,-3);
			$cond .= ' )';
		}
		else
		{
			$cond .= ' AND (clientes.Nome LIKE "%'.$_GET['f_cli'].'%" OR clientes.Apelido LIKE "%'.$_GET['f_cli'].'%") ';
		}
	}
	if($_GET['f_tipoequip'] != '') $cond .= ' AND rmas.TipoEquip LIKE "%'.$_GET['f_tipoequip'].'%" ';
	if($_GET['f_emp'] != '') $cond .= ' AND clientes.Empresa LIKE "%'.$_GET['f_emp'].'%" ';

	$sql="SELECT DISTINCT (rmas.ID) FROM rmas INNER JOIN clientes ON rmas.ID_Cliente=clientes.ID WHERE 1=1 $cond ORDER BY rmas.DataCriado DESC";
	//echo $sql;
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}*/

function SearchRecords($d)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $admin_lang,$and_loja;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$cond = '';

	$rmaid = $_GET['f_tracking'];
	$rmaid = $str = ltrim($rmaid, 'DB');
	$rmaid = $str = ltrim($rmaid, '0');
	if(isset($_GET['f_tracking']) && $_GET['f_tracking'] != '') $cond .= ' AND rmas.ID LIKE "%'.$rmaid.'%" ';
	if(isset($_GET['f_nrserie']) && $_GET['f_nrserie'] != '') $cond .= ' AND (rmas.m_nserie LIKE "%'.$_GET['f_nrserie'].'%" OR rmas.m_modelo LIKE "%'.$_GET['f_nrserie'].'%") ';
	if(isset($_GET['f_data1']) && $_GET['f_data1'] != '') $cond .= ' AND ((rmas.DataFinalizado  BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\') OR (rmas.DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
	if(isset($_GET['f_tlm']) && $_GET['f_tlm'] != '') $cond .= ' AND (rmas.cliente_telefone1 LIKE "%'.$_GET['f_tlm'].'%" OR rmas.cliente_telefone2 LIKE "%'.$_GET['f_tlm'].'%") ';
	if(isset($_GET['f_origem']) && $_GET['f_origem'] != '')
	{
		$f_origem = $_GET['f_origem'];
		if($f_origem == 1) $cond .= 'AND (rmas.m_origem="0" OR rmas.m_origem="1")';
		else $cond .= 'AND rmas.m_origem LIKE "'.$_GET['f_origem'].'"';
	}
	if($_GET['f_emp'] != '') $cond .= ' AND rmas.cliente_empresa LIKE "%'.$_GET['f_emp'].'%" ';
	if(isset($_GET['f_cli']) && $_GET['f_cli'] != '')
	{
		$v_nomecli = explode(' ', rtrim(ltrim($_GET['f_cli'])));
		if(count($v_nomecli) > 1) {
			$cond .= ' AND (';
			for ($i = 0; $i < count($v_nomecli); $i++) {
				for ($j = 0; $j < count($v_nomecli); $j++) {
					$cond .= ' (rmas.cliente_nome REGEXP "[[:<:]]'.$v_nomecli[$i].'[[:>:]]" AND rmas.cliente_apelido  REGEXP "[[:<:]]'.$v_nomecli[$j].'[[:>:]]") OR ';
				}
			}
			$cond = substr($cond, 0, -3);
			$cond .= ' )';
		} else {
			$cond .= ' AND (rmas.cliente_nome LIKE "%'.$_GET['f_cli'].'%" OR rmas.cliente_apelido LIKE "%'.$_GET['f_cli'].'%") ';
		}
	}
	if(isset($_GET['f_estado']) && $_GET['f_estado'] != '')$cond .= ' AND rmas.m_estado="'.$_GET['f_estado'].'" ';
	if(isset($_GET['f_pago']) && $_GET['f_pago'] != '')$cond .= ' AND rmas.m_pago="'.$_GET['f_estado'].'" ';
	if(isset($_GET['f_origem']) && $_GET['f_origem'] != '')$cond .= ' AND rmas.m_origem="'.$_GET['f_origem'].'" ';

	$sql = "SELECT DISTINCT (rmas.ID) FROM rmas INNER JOIN clientes ON rmas.ID_Cliente=clientes.ID WHERE 1=1 $cond $and_loja ORDER BY rmas.ID DESC";

	$db_res = $rs->db_open($db_link, $DB_name, $sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}

function show_paginacao_bk($total,$qtd)
{
	global $from;
	if($total>$qtd)
	{
		$pageURL=get_uri();
		if(substr_count($pageURL,"&from"))$pageURL=substr($pageURL,0,strpos($pageURL,"&from"));
		if(substr_count($pageURL,"?from")){$pageURL=substr($pageURL,0,strpos($pageURL,"?from"));}
		if(substr_count($pageURL,"?")>0)$op="&";else $op="?";


		$i=0;
		echo '<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
			<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td class="t1" style="text-align:center">';
		echo "page ";
		while($i<($total/$qtd))
		{
			if($i>0)echo " - ";
			if($from==($i*$qtd))echo ($i+1);
			else echo "<a class='numbers' href='".$pageURL.$op."from=".($i*$qtd)."'>".($i+1)."</a>";
			$i++;
		}
		echo "</td></tr></table>";
	}
}

function criaSelectBox($campo,$valor){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$where_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql='SELECT distinct('.$campo.') FROM rmas '.$where_loja.' ORDER BY '.$campo.' ASC ';
	$db_res=$rs->db_open($db_link,$DB_name,$sql);

	?><option value="" >------------</option><?php

	while(!$rs->EOF())
	{
		?>
		<option value="<?php echo $rs->db_data[$campo];?>" <?php if($rs->db_data[$campo]== $valor) echo 'selected=""';?>><?php echo $rs->db_data[$campo];?></option>
		<?php
		$rs->db_next();
	}
	$rs->db_close();

}

function criaArrayJS($campo,$valor)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql='SELECT DISTINCT('.$campo.') FROM rmas WHERE '.$campo.' LIKE "%'.$valor.'%" '.$and_loja.' ORDER BY '.$campo.' ASC LIMIT 10';
	$db_res=$rs->db_open($db_link,$DB_name,$sql);

	if(!$rs->EOF()){

		while(!$rs->EOF())
		{
			if($rs->db_data[$campo]) {
				echo $rs->db_data[$campo].';';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();

}

function grava_rma_log($id, $estado)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$log_id = 0;
	$login_id = $_SESSION['ss_admin_id'];
	if($login_id != 10)
	{
		$criado = date('Y-m-d H:i:s');
		$post = addslashes(json_encode($_POST));
		$sql = "INSERT INTO rmas_logs (rma_id, login_id, estado, criado, POST) VALUES ($id, $login_id, $estado, '$criado', '".$post."')";
		$res = Runquery($db_link,$DB_name,$sql);

		$log_id = db_get_last_insert_id($db_link);
	}

	return $log_id;
}

function update_rma_log($log_id=0, $info='')
{
	/* 	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$login_id = $_SESSION['ss_admin_id'];
	// if($login_id != 10 && $log_id > 0)
	// {
		$criado = date('Y-m-d H:i:s');
		$post = addslashes(json_encode($_POST));
		$sql = "UPDATE rmas_logs SET DADOS_ENVIO='".addslashes($info)."' WHERE ID=".$log_id$log_id;
		$res = Runquery($db_link, $DB_name, $sql);

		// $log_id = db_get_last_insert_id($db_link);
	// }

	// return $log_id; 
	*/
}

function show_page_tools($filter = 0)
{
	global $f_estado, $f_pagamento;
	$quick_filter = isset($_GET['quick_filter']) ? trim((string)$_GET['quick_filter']) : '';
	$quick_filter_labels = array(
		'por_intervencionar' => 'Equipamentos por intervencionar',
		'mensagens_por_responder' => 'Equipamentos com mensagens por responder',
		'reparados_por_entregar' => 'Equipamentos reparados, por entregar'
	);
	?>
	<style>
		.text-center {
			text-align: center;
		}
	</style>

	<script>
		function update_status(n) {
			var url = "list.php?d=<?php echo uniqID('')?>&x_id=" + n;
			if(document.getElementById("m_estado_" + n)) url = url + "&x_estado=" + document.getElementById("m_estado_" + n).value;
			if(document.getElementById("m_guia_" + n)) url = url + "&x_guia=" + document.getElementById("m_guia_" + n).value;
			window.open(url, "_self");
		}
	</script>

	<table width="100%" border="0" cellspacing=0 cellpadding=1 style="background-color: #FFF">
		<tr>
			<td height="95">
				<?php 
					if(isset($quick_filter_labels[$quick_filter])){
						?><div class="rma-active-direct-filter" role="status">
							<span><strong>Pesquisa rápida:</strong> <?php echo htmlspecialchars($quick_filter_labels[$quick_filter], ENT_QUOTES, 'UTF-8'); ?></span>
							<a href="list.php">Limpar pesquisa</a>
						</div><?php
					}
					if($filter == 1){
						?>
						<div style="width:130px;float:left;background:#FFF;height:30px;line-height:30px;color:#000;padding-left:10px;font-weight:bold;font-size:15px;cursor:pointer" onclick="window.open('list.php?tab=1','_self');"><img src="down.png" border="0" style="float:left" /> Pesquisa</div>
						<div style="width:140px;float:left;background:#87ceee;height:30px;line-height:30px;color:#000;padding-left:10px;font-weight:bold;font-size:15px;cursor:pointer" onclick="window.open('list.php?tab=2','_self');"><img src="down.png" border="0" style="float:left" /> Relatórios</div>
						<div style="clear:both"></div>
						<?php 
					}

					if(!isset($_GET['tab']) || isset($_GET['tab']) && $_GET['tab'] == 1){
						?>
						<div style="display:<?php echo (!isset($_GET['tab']) || isset($_GET['tab']) && $_GET['tab'] == 1 ? 'block' : 'none')?>;padding:5px;background-color: #FFF; border-top:#000 1px solid" id="hardware">
							<input type="hidden" name="top_search" id="top_search" value="<?php echo (isset($_GET['top_search']) && $_GET['top_search']!='' ? $_GET['top_search'] : '');?>" />
							<table border="0" cellspacing=0 cellpadding=1>
								<tr>
									<td class="preto" align="center" width="100"><b>Tracking</b><br><input type="text" name="f_tracking" value="<?php if(isset($_GET["f_tracking"])) echo $_GET["f_tracking"] ?>" class="box" style="width:90%"></td>
									<td class="preto" align="center" width="125"><b>Série/Modelo/Marca</b><br><input type="text" name="f_nrserie" value="<?php if(isset($_GET["f_nrserie"])) echo $_GET["f_nrserie"] ?>"class="box" style="width:90%"></td>
									<td class="preto" align="center" width="180"><b>IMEI</b><br><input type="text" name="f_tipoequip" value="<?php if(isset($_GET["f_tipoequip"])) echo $_GET["f_tipoequip"] ?>"class="box" style="width:90%"></td>
									<td class="preto" align="center" width="280">
										<b>Estado</b><br><select name="f_estado" style="width:100%" class="chzn-select">
											<option value="" <?php if(isset($_GET["f_estado"]) && $_GET["f_estado"] == '') echo 'selected=""'?>>TODOS</option>
											<?php
											$z=10;
											for($a=0; $a<count($GLOBALS['estados_rmas_cab']); $a++)
											{
												?>
												<option value="-<?php echo $z;?>" <?php if(isset($_GET["f_estado"]) && $_GET["f_estado"] == '-'.$z) echo 'selected=""'?>>&nbsp;&nbsp;&nbsp;<?php echo $GLOBALS['estados_rmas_cab'][$a];?></option>
												<?php 
												$z+=10;
											}
											?>

											<option value="-1" <?php if((isset($_GET["f_estado"]) && $_GET["f_estado"] == '-1') ) echo 'selected=""'?>>TODOS EM ABERTO</option>

											<?php
											// ARRAY NO SETTINGS
											if(isset($GLOBALS['estados_rmas_cab']))
											{

												for($a=0; $a<count($GLOBALS['estados_rmas_cab']); $a++)
												{
													echo '<optgroup label="'.$GLOBALS['estados_rmas_cab'][$a].'">';

													show_rmas_estados($a, (isset($_GET['f_estado']) && $_GET['f_estado']!='' ? $_GET['f_estado'] : ''));

													/* for($a=0; $a<count($GLOBALS['estados_rmas'][$estado_cab]);$a++)
													{
														echo ' <option value="'.$GLOBALS['estados_rmas'][$estado_cab][$a]['id'].'" '.(($m_estado==$GLOBALS['estados_rmas'][$estado_cab][$a]['id']) ? 'selected="selected"' : '').'>'.$GLOBALS['estados_rmas'][$estado_cab][$a]['estado'].'</option>';
													} */
													echo '</optgroup>';
												}
											}
											?>
										</select>
									</td>
									<td class="preto" align="center" width="180px">
										<b>Origem</b><br><select id="f_origem" name="f_origem" class="chzn-select" style="width: 90%;">
											<option value="" <?php if(!isset($_GET["f_origem"]) || $_GET["f_origem"] == "") echo 'selected=""'?>>TODOS</option>
											<option	value="1" <? if((isset($_GET["f_origem"]) && $_GET["f_origem"] == 1) || !isset($_GET["f_origem"])) echo ' selected="selected"' ?> >Online</option>
											<option	value="2" <? if(isset($_GET["f_origem"]) && $_GET["f_origem"] == 2) echo ' selected="selected"' ?> >Loja de Matosinhos</option>
											<option	value="3" <? if(isset($_GET["f_origem"]) && $_GET["f_origem"] == 3) echo ' selected="selected"' ?> >Loja da Maia</option>
										</select>
									</td>

									<!-- estados -->
									<td class="preto" align="center" width="180px">
										<b>Estados listagem</b><br><select id="f_estados2" name="f_estados2" class="chzn-select" style="width: 90%;">
											<option value="" <?php if(!isset($_GET["f_estados2"]) || $_GET["f_estados2"] == "") echo 'selected=""'?>>TODOS</option>
											<option	value="1" <? if((isset($_GET["f_estados2"]) && $_GET["f_estados2"] == 1) ) echo ' selected="selected"' ?> >Novo/Aberto com menos de 7 dias</option>
											<option	value="2" <? if(isset($_GET["f_estados2"]) && $_GET["f_estados2"] == 2) echo ' selected="selected"' ?> >Aberto com 7 ou mais dias </option>
											<option	value="3" <? if(isset($_GET["f_estados2"]) && $_GET["f_estados2"] == 3) echo ' selected="selected"' ?> >Reincidente</option>
										</select>
									</td>
									<!-- estados -->
									<td></td>
								</tr>
								<tr><td colspan="6" height="2"></td></tr>
								<tr>
									<td class="preto" align="center"><b>Empresa</b><br><input type="text" name="f_emp" value="<?php if(isset($_GET["f_emp"])) echo $_GET["f_emp"] ?>" class="box" style="width:90%"/></td>
									<td class="preto" align="center"><b>Cliente</b><br><input type="text" name="f_cli" value="<?php if(isset($_GET["f_cli"])) echo $_GET["f_cli"] ?>" class="box" style="width:90%"/></td>

									<td class="preto" align="center"><b>Telemóvel/Email</b><br><input type="text" name="f_tlm" value="<?php if(isset($_GET["f_tlm"])) echo $_GET["f_tlm"] ?>" class="box" style="width:90%"/></td>
									<td class="preto" align="center">
										<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td class="preto" align="center"><b>De</b><br><input type="date" name="f_data1" id="f_data1" value="<?php if(isset($_GET["f_data1"])) echo $_GET["f_data1"] ?>" class="box dropdate" style="width:95%"></td>
											<td class="preto" align="center"><b>Até</b><br><input type="date" name="f_data2" id="f_data2" value="<?php if(isset($_GET["f_data2"])) echo $_GET["f_data2"] ?>" class="box dropdate" style="width:95%"></td>
										</tr>
										</table>
									</td>
									<td class="preto" align="center">
										<b>Pagamento</b><br>
										<select name="f_pago" style="width:90%" class="chzn-select">
											<option value="" <?php if(!isset($_GET["f_pago"]) || $_GET["f_pago"] == "") echo 'selected=""'?>>TODOS</option>
											<option value="0" <?php if(isset($_GET["f_pago"]) && $_GET["f_pago"] == "0") echo 'selected=""'?>><?php echo 'Não Pago'?></option>
											<option value="1" <?php if(isset($_GET["f_pago"]) && $_GET["f_pago"] == "1") echo 'selected=""'?>><?php echo 'Pago'?></option>
										</select>
									</td>
									<td class="preto" align="center">
										<b>Localização</b><br>
										<select name="f_local" class="chzn-select" style="width:150px">
											<option value="" <?php if(!isset($_GET["f_local"]) || $_GET["f_local"] == "") echo 'selected=""'?>>TODOS</option>
											<?php
													$sql = 'SELECT distinct(sel_local) FROM rmas';

													global $DB_host,$DB_user,$DB_pass,$DB_name;
													$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
													$rs=new DataObj;
													$rs2=new DataObj;
													$db_res=$rs->db_open($db_link,$DB_name,$sql);
													if(!$rs->EOF())
													{
														while(!$rs->EOF())
														{
															$sel_local=$rs->db_data['sel_local'];

															if($f_local==$sel_local) echo '<option value="'.$rs->db_data['sel_local'].'">'.$rs->db_data['sel_local'].'</option>';
															else echo '<option value="'.$rs->db_data['sel_local'].'">'.$rs->db_data['sel_local'].'</option>';

															$rs->db_next();
														}
													}
													$rs->db_close();
												?>
										</select>
									</td>
									<td style="vertical-align:bottom"><a title="Pesquisar" href="javascript:;" onclick="document.getElementById('pesquisar').value=1;document.getElementById('lista_paginas').submit()"><img height="32" border="0" src="../layout/search-12.png" style="vertical-align:bottom"></a><?php if(isset($_GET['pesquisar']) && $_GET['pesquisar'] == 1){?><a title="Reset Pesquisa" href="list.php"><img border="0" src="../layout/delete.png" style="vertical-align:bottom"></a><?php }?></td>
									<td style="vertical-align:bottom"></td>
									<td style="vertical-align:bottom"><a title="Pesquisar" href="javascript:;" onclick="document.getElementById('pesquisar').value=2;document.getElementById('lista_paginas').submit()"><img height="32" border="0" src="../layout/export-csv.png" style="vertical-align:bottom"></a></td>
									<td style="vertical-align:bottom"></td>
								</tr>
								<tr>
									<td colspan="10" class="rma-client-message-cell"><?php mensagens_novas(); ?></td>
								</tr>

							</table>
						</div>
						<?php 
					}

					if($filter == 1 && isset($_GET['tab']) && $_GET['tab'] == 2){
						?>
						<div style="display:<?php echo (isset($_GET['tab']) && $_GET['tab'] == 2 ? 'block' : 'none')?>;padding:5px;background-color: #87ceee; border-top:#000 1px solid;min-height:90px" id="software">
							<div id="pesq_conf"  style="background:#FFF">
								<table border="0" cellspacing=0 cellpadding=1 style="background:#FFF">
									<tr>
										<td class="preto" align="center" width="220"><br><b>Relatórios Pré Configurados</b></td>
										<td class="preto" align="center"><b>De</b><br><div style="position:relative"><input type="text" name="f_data1" id="f_data1_x" value="<?php if(isset($_GET["f_data1"])) echo $_GET["f_data1"] ?>" class="box dropdate2" style="width:95%"></div></td>
										<td class="preto" align="center"><b>Até</b><br><div style="position:relative"><input type="text" name="f_data2" id="f_data2_x" value="<?php if(isset($_GET["f_data2"])) echo $_GET["f_data2"] ?>" class="box dropdate2" style="width:95%"></div></td>

										<td class="preto" align="center" width="320">
											<br>
											<select name="f_relatorio" id="f_relatorio_x" style="width:100%" class="chzn-select">
												<option value="">---</option>
												<option value="101">Relatório de Reparações Entregues</option>
												<option value="102">Relatório de RMAS</option>
												<option value="103">Relatório Extrato Cliente</option>
												<option value="104">Número de reparações efetuada por técnico</option>
												<option value="105">Relatório de localização</option>
												<option value="106">Relatório de Entradas Diárias</option>
												<option value="1">Relatório Valor de Reparações</option>
												<option value="2">Relatório Número de Reparações</option>
												<option value="3">Relatório de Reparações</option>
												<!-- <option value="4">Relatório de Reparações Entregues</option> -->
												<option value="5">Extrato de Conta Por Cliente - Reparação</option>
											</select>

											<div id="choose_client" style="display: none"><br><input placeholder="Cliente(email):" style="height:26px;width:100%"  type="text" id="cliente_x" name="cliente_x"></div>
											<div id="choose_loja" style="display: none;text-align: left;width:100%"><br>
												<select name="f_loja" id="f_loja_x" class="chzn-select" style="width:320px">
													<option value="">Todas as LOJAS</option>
													<option value="1">Loja da Maia</option>
													<option value="2">Loja de matosinhos</option>
												</select>
											</div>
											<div id="choose_tecnico" style="display: none;text-align: left;width:100%"><br>
												<select name="f_tecnico" id="f_tecnico_x" class="chzn-select" style="width:320px">
													<option value="">Todos os TÉCNICOS</option><?

														$sql = 'SELECT * FROM logins where estado = 1';

														global $DB_host,$DB_user,$DB_pass,$DB_name;
														$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
														$rs=new DataObj;
														$rs2=new DataObj;
														$db_res=$rs->db_open($db_link,$DB_name,$sql);
														if(!$rs->EOF())
														{
															while(!$rs->EOF())
															{
																$Grupo_ID=$rs->db_data['Grupo_ID'];
																$tecnico=$rs->db_data['tecnico'];

																$sql = 'SELECT * FROM grupos_logins where ID = '.$Grupo_ID.' '.$and_loja;
																$db_res=$rs2->db_open($db_link,$DB_name,$sql);
																if($tecnico==1) echo '<option value="'.$rs->db_data['ID'].'">'.$rs->db_data['Nome'].' [TECNICO] </option>';
																else echo '<option value="'.$rs->db_data['ID'].'">'.$rs->db_data['Nome'].'</option>';

																$db_res=$rs2->db_open($db_link,$DB_name,$sql);

																$rs->db_next();
															}
														}
														$rs->db_close();
													?>
												</select>
											</div>

											<script type="text/javascript">
												$(document).ready(function () {
													$("#f_relatorio_x").change(function () {
														var val = $(this).val();
														console.log(val);

														$("#choose_client").hide();
														$("#choose_loja").hide();
														$("#choose_tecnico").hide();


														if (val == "101" || val == "102" || val == "103" || val == "104")
														{
															if (val == "103") $("#choose_client").show();
															if (val == "101" || val == "102")$("#choose_loja").show();
															if (val == "104") $("#choose_tecnico").show();
														}
														/*else
														{
															$("#choose_client").hide();
															$("#choose_loja").hide();
															$("#choose_tecnico").hide();
														}*/
													});
												});
											</script>

										</td>

										<td width="80" align="center"><br><img src="pdf.png" border="0" title="gerar pdf" style="cursor:pointer" onclick="/*if($('#f_data1_x').val() == '' || $('#f_data2_x').val() == ''){alert('Os campos de data tem que ser preenchidos');}else*/{window.open('../gera_relatorios.php?tab=2'+buildRequestStringData($('#pesq_conf')),'_blank');}" width="20"></td>
									</tr>
									<tr><td colspan="6" height="2"></td></tr>
								</table>
							</div>
							<br>
							<div id="pesq_conf2" style="background:#FFF">
								<table border="0" cellspacing=0 cellpadding=1 style="background:#FFF">
									<tr>
										<td class="preto" align="center" width="220" rowspan="2"><br><b>Relatórios P/ Pesquisa</b></td>
										<td class="preto" align="center" width="150"><b>De</b><br><div style="position:relative"><input type="text" name="f_data1" id="f_data1_x2" value="<?php if(isset($_GET["f_data1"])) echo $_GET["f_data1"] ?>" class="box dropdate2" style="width:95%"></div></td>
										<td class="preto" align="center" width="150"><b>Até</b><br><div style="position:relative"><input type="text" name="f_data2" id="f_data2_x2" value="<?php if(isset($_GET["f_data2"])) echo $_GET["f_data2"] ?>" class="box dropdate2" style="width:95%"></div></td>

										<td class="preto" align="center" width="320">
											<br>
											<select name="f_relatorio2" id="f_relatorio_x2" style="width:100%" class="chzn-select">
											<option value="10">Relatório Valor de Reparações</option>
											<option value="20">Relatório Número de Reparações</option>
											<option value="30">Relatório de Reparações</option>
											</select>
										</td>

										<td width="80" align="center"><br></td>
									</tr>
									<tr>
										<td class="preto" align="center">
											<b>Origem</b><br><select id="f_origem_x2" name="f_origem" class="chzn-select" style="width: 100%;">
												<option value="" <?php if(!isset($_GET["f_origem"]) || $_GET["f_origem"] == "") echo 'selected=""'?>>TODOS</option>
												<option	value="1" <? if((isset($_GET["f_origem"]) && $_GET["f_origem"] == 1) || !isset($_GET["f_origem"])) echo ' selected="selected"' ?> >Online</option>
												<option	value="2" <? if(isset($_GET["f_origem"]) && $_GET["f_origem"] == 2) echo ' selected="selected"' ?> >Loja de Matosinhos</option>
												<option	value="3" <? if(isset($_GET["f_origem"]) && $_GET["f_origem"] == 3) echo ' selected="selected"' ?> >Loja da Maia</option>
											</select>
										</td>
										<td class="preto" align="center">
											<b>Pagamento</b><br>
											<select name="f_pago" id="f_pago_x2" style="width:90%" class="chzn-select">
												<option value="" <?php if(!isset($_GET["f_pago"]) || $_GET["f_pago"] == "") echo 'selected=""'?>>TODOS</option>
												<option value="0" <?php if(isset($_GET["f_pago"]) && $_GET["f_pago"] == "0") echo 'selected=""'?>><?php echo 'Não Pago'?></option>
												<option value="1" <?php if(isset($_GET["f_pago"]) && $_GET["f_pago"] == "1") echo 'selected=""'?>><?php echo 'Pago'?></option>
											</select>
										</td>
										<td class="preto" align="center"><b>Estado</b><br>
											<select name="f_estado" id="f_estado_x2" style="width:100%" class="chzn-select">
												<option value="" <?php if(isset($_GET["f_estado"]) && $_GET["f_estado"] == '') echo 'selected=""'?>>TODOS</option>
												<?php
													$z=10;
													for($a=0; $a<count($GLOBALS['estados_rmas_cab']); $a++)
													{
														?>
														<option value="-<?php echo $z;?>" <?php if(isset($_GET["f_estado"]) && $_GET["f_estado"] == '-'.$z) echo 'selected=""'?>>&nbsp;&nbsp;&nbsp;<?php echo $GLOBALS['estados_rmas_cab'][$a];?></option>
														<?php 
														$z+=10;
													}
												?>

												<option value="-1" <?php if((isset($_GET["f_estado"]) && $_GET["f_estado"] == '-1') || !isset($_GET["f_estado"])) echo 'selected=""'?>>TODOS EM ABERTO</option>
												<?php
													// ARRAY NO SETTINGS
													if(isset($GLOBALS['estados_rmas_cab']))
													{

														for($a=0; $a<count($GLOBALS['estados_rmas_cab']); $a++)
														{
															echo '<optgroup label="'.$GLOBALS['estados_rmas_cab'][$a].'">';

															show_rmas_estados($a, (isset($_GET['f_estado']) && $_GET['f_estado']!='' ? $_GET['f_estado'] : ''));

															/* for($a=0; $a<count($GLOBALS['estados_rmas'][$estado_cab]);$a++)
															{
																echo ' <option value="'.$GLOBALS['estados_rmas'][$estado_cab][$a]['id'].'" '.(($m_estado==$GLOBALS['estados_rmas'][$estado_cab][$a]['id']) ? 'selected="selected"' : '').'>'.$GLOBALS['estados_rmas'][$estado_cab][$a]['estado'].'</option>';
															} */
															echo '</optgroup>';
														}
													}
												?>
											</select>
										</td>
										<td width="80" align="center"><br><img src="pdf.png" border="0" title="gerar pdf" style="cursor:pointer" onclick="if($('#f_data1_x2').val() == '' || $('#f_data2_x2').val() == ''){alert('Os campos de data tem que ser preenchidos');}else{window.open('../gera_relatorios.php?tab=2'+buildRequestStringData($('#pesq_conf2')),'_blank');}" width="20"></td>
									</tr>
									<tr><td colspan="6" height="2"></td></tr>
								</table>
							</div>
						</div>
						<?php 
					}
				?>
			</td>
		</tr>
	</table>

	<?php 
}

function show_page_bottom_tools()
{
	?>
	<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td width="130" class="t1">&nbsp;<?php echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
			<td width="125"><select name="flag" id="flag" class="chzn-select" style="width:150px">
					<option value="" selected="selected">Escolha uma ação</option>
					<option value="1"><?php echo write_lang('key_apagar','Apagar')?></option>
				</select>
			</td>
			<td width="5"></td>
			<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_acao','Aplicar Acção')?>" class="botao" style="width:120px;height:20px"></td>
		</tr>
	</table>
	<?php
}

function show_content_list($reg=null)
{
	$path = "../../core/";
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar,$and_loja;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs3 = new DataObj;
	$porpagina = 20;

	$f_tracking = ''; $f_nrserie = ''; $f_tipoequip = ''; $f_estado = ''; $f_origem = ''; $f_emp = ''; $f_cli = ''; $f_tlm = ''; $f_data1 = ''; $f_data2 = ''; $f_pago = ''; $f_origem='';

	$q = '';
	$quick_filter = isset($_GET['quick_filter']) ? trim((string)$_GET['quick_filter']) : '';
	if($quick_filter === 'por_intervencionar')
	{
		$q .= ' AND m_estado = 3';
	}
	elseif($quick_filter === 'mensagens_por_responder')
	{
		$q .= ' AND COALESCE(notes, "") <> "" AND COALESCE(lida, 0) = 0';
	}
	elseif($quick_filter === 'reparados_por_entregar')
	{
		$q .= ' AND m_estado = 12';
	}

	if(isset($_GET['top_search']) && $_GET['top_search']!='')
	{
		$rmaid = ltrim(trim($_GET['top_search']), 'DB');
		$rmaid = ltrim(trim($rmaid), 'db');
		$rmaid = ltrim($rmaid, '0');
		$q .= ' AND
				(ID="'.trim($rmaid).'"
				OR m_nserie LIKE "%'.trim($_GET['top_search']).'%"
				OR m_marca LIKE "%'.trim($_GET['top_search']).'%"
				OR m_modelo LIKE "%'.trim($_GET['top_search']).'%"
				OR m_imei LIKE "%'.trim($_GET['top_search']).'%"
				OR m_novo_imei LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_empresa LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_nome LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_apelido LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_telefone1 LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_telefone2 LIKE "%'.trim($_GET['top_search']).'%"
				OR cliente_username LIKE "%'.trim($_GET['top_search']).'%")
			';
	}

	$estados_fechados_entregue = estado_encomenda_rmas_fechados();
	// ESTADO DO RMA
	if((isset($_GET["f_estado"]) && $_GET["f_estado"] == '-1') )
	{
		$estados_fechados = estado_encomenda_rmas_fechados();
		for($a=0; $a<count($estados_fechados); $a++)
		{
			$q .= ' AND m_estado <> '.$estados_fechados[$a];

		}
	}
	elseif(isset($_GET['f_estado']) && (trim($_GET['f_estado']) == '-10' || trim($_GET['f_estado']) == '-20' || trim($_GET['f_estado']) == '-30' || trim($_GET['f_estado']) == '-40' || trim($_GET['f_estado']) == '-50' || trim($_GET['f_estado']) == '-60'))
	{

		$estados_fechados = estado_encomenda_rmas_fechados_tipo(trim($_GET['f_estado']));
		if(count($estados_fechados) > 0)
		{
			$q .= ' AND (';
			for($a=0; $a<count($estados_fechados); $a++)
			{
				if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
				else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

			}
			$q .= ')';
		}
	}
	elseif(isset($_GET['f_estado']) && trim($_GET['f_estado']) != '') $q .= ' AND m_estado="'.trim($_GET['f_estado']).'"';
	elseif(isset($_GET['f_estado']) && trim($_GET['f_estado']) == '') $q .= ''; // TODOS OS ESTADO


	// TRACKING
	if(isset($_GET['f_tracking']) && trim($_GET['f_tracking']) != '')
	{
		$rmaid = ltrim(trim($_GET['f_tracking']), 'DB');
		$rmaid = ltrim(trim($rmaid), 'db');
		$rmaid = ltrim($rmaid, '0');
		// $q .= ' AND m_tracking LIKE "%'.trim($_GET['f_tracking']).'%"';
		$q .= ' AND ID="'.trim($rmaid).'"';
	}

	// NR. SERIE | MODELO
	if(isset($_GET['f_nrserie']) && trim($_GET['f_nrserie']) != '')
	{
		$q .= ' AND
				(
					m_nserie LIKE "%'.trim($_GET['f_nrserie']).'%"
					OR m_marca LIKE "%'.trim($_GET['f_nrserie']).'%"
					OR m_modelo LIKE "%'.trim($_GET['f_nrserie']).'%"
				)';
	}

	// IMEI ou IMEI NOVO
	$imeis = '';
	if(isset($_GET['f_tipoequip']) && trim($_GET['f_tipoequip']) != '')
	{
		$imeis = explode(';', trim($_GET['f_tipoequip']));
		if(count($imeis) > 0)
		{
			$q .= ' AND (';
			for($a=0; $a<count($imeis); $a++)
			{
				if($a > 0 ) $q .= ' OR (m_imei LIKE "%'.trim($imeis[$a]).'%" OR m_novo_imei LIKE "%'.trim($imeis[$a]).'%") ';
				else $q .= ' (m_imei LIKE "%'.trim($imeis[$a]).'%" OR m_novo_imei LIKE "%'.trim($imeis[$a]).'%") ';
			}
			$q .= ' )';
		}
	}
	$imeis = '';

	// ORIGEM - LOJAS OU ONLINE
	if(isset($_GET['f_origem']) && trim($_GET['f_origem']) != '') $q .= ' AND m_origem="'.trim($_GET['f_origem']).'"';
	elseif(isset($_GET['f_origem']) && trim($_GET['f_origem']) == '') $q .= '';
	else $q .= ' AND m_origem=1 ';

    // ESTADOS LISTAGEM
    $today = 'now';
    $previousdate = strtotime ( '-7 day' , strtotime ( $today ) ) ;
    $previousdate = date ( 'Y-m-j' , $previousdate );

    if(isset($_GET['f_estados2']) && trim($_GET['f_estados2']) == '1') $q .= ' AND DATE(DataInsert) > "'.$previousdate.'"'; // LESS THAN 7 DAYS
    elseif(isset($_GET['f_estados2']) && trim($_GET['f_estados2']) == '2') $q .= ' AND DATE(DataInsert) <= "'.$previousdate.'"'; // EQUAL OR MORE THAN 7 DAYS
    elseif(isset($_GET['f_estados2']) && trim($_GET['f_estados2']) == '3') $q .= ' group by m_imei having count(*) > 1 '; // REINCIDENTE
    else $q .= '';

	// EMPRESA DO CLIENTE
	if(isset($_GET['f_emp']) && trim($_GET['f_emp']) != '') $q .= ' AND cliente_empresa LIKE "%'.trim($_GET['f_emp']).'%"';

	// DADOS DO CLIENTE
	if(isset($_GET['f_cli']) && $_GET['f_cli'] != '')
	{
		$v_nomecli = explode(' ', rtrim(ltrim($_GET['f_cli'])));
		if(count($v_nomecli) > 1) {
			$q .= ' AND (';
			for ($i = 0; $i < count($v_nomecli); $i++)
			{
				for ($j = 0; $j < count($v_nomecli); $j++)
				{
					$q .= ' (cliente_nome REGEXP "[[:<:]]'.$v_nomecli[$i].'[[:>:]]" AND cliente_apelido REGEXP "[[:<:]]'.$v_nomecli[$j].'[[:>:]]") OR ';
				}
			}
			$q = substr($q, 0, -3);
			$q .= ' )';
		}
		else
		{
			$q .= ' AND (cliente_nome LIKE "%'.$_GET['f_cli'].'%" OR cliente_apelido LIKE "%'.$_GET['f_cli'].'%") ';
		}
	}

	// TELEMOVEL
	if(isset($_GET['f_tlm']) && $_GET['f_tlm'] != '')
	{
		$q .= ' AND (cliente_telefone1 LIKE "%'.$_GET['f_tlm'].'%" OR cliente_telefone2 LIKE "%'.$_GET['f_tlm'].'%" OR cliente_username  LIKE "%'.$_GET['f_tlm'].'%" ) ';
	}



	// ENTRE DATAS
	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataFinalizado BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\') OR (DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
	elseif(isset($_GET['f_data1']) && $_GET['f_data1'] != '') $q .= ' AND ((DataFinalizado = \''.$_GET['f_data1'].'\') OR (DataInsert = \''.$_GET['f_data1'].'\')) ';

	// PAGAMENTO
	if(isset($_GET['f_pago']) && $_GET['f_pago'] != '') $q .= ' AND m_pago="'.$_GET['f_pago'].'" ';
	if(isset($_GET['f_origem']) && $_GET['f_origem'] != '') $q .= ' AND m_origem="'.$_GET['f_origem'].'" ';
	if(isset($_GET['f_local']) && $_GET['f_local'] != '') $q .= ' AND sel_local="'.$_GET['f_local'].'" ';



	



	$sql = '
			SELECT
				ID, m_imei, m_novo_imei, m_tracking, m_referencia, ID_Cliente, ID_Cliente2, m_marca, m_modelo,
				DataInsert, DataUpdate, DataFinalizado, m_nserie, DataUpdate, m_estado,
				cliente_nome, cliente_sobrenome, cliente_apelido, cliente_empresa, m_origem, m_pago, notes
			FROM
				rmas
			WHERE
				1=1
				'.$q.'
				'.$and_loja.'
			ORDER BY
				ID DESC
			';

    // echo "<br>";
    // echo $sql;
    // echo "<br>";

	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / $porpagina);
	if(!isset($_GET['pagina'])) $pagina = '1';
	else $pagina = $_GET['pagina'];
	$inicio = $pagina - 1;
	$inicio = $inicio * $porpagina;


	$sql2 = $sql." LIMIT $inicio, $porpagina";

	if(isset($_GET['pesquisar']) && $_GET['pesquisar']==2)
	{
		$sql2 = $sql;
	}


	$rs->db_open($db_link, $DB_name, $sql2);

	$i = 0; $count = 0;	$return = '';
	if(!$rs->EOF())
	{
		if(isset($_GET['pesquisar']) && $_GET['pesquisar']==2)
		{
			echo 'Traking;Marca | Modelo;IMEI | IMEI NOVO;Data Entrada;Última Ação;Origem;Empresa;Cliente;Estado';
			echo "\n";
		}
		else
		{
		?>
		<table width="100%" border="0" cellspacing=0 cellpadding=1 class="p1">
		<tr>
			<td width="40" height="30"></td>
			<td width="70" class="branco text-center">Traking</td>
			<td width="120" class="branco text-center">Marca | Modelo</td>
			<td width="120" class="branco text-center">IMEI | IMEI NOVO</td>
			<td width="120" class="branco text-center">Data Entrada</td>
			<td width="120" class="branco text-center">Última Ação</td>
			<td width="130" class="branco text-center">Origem</td>
			<td class="branco text-center">Empresa</td>
			<td width="150" class="branco text-center">Cliente</td>
			<td width="160" class="branco text-center">Estado</td>
			<td width="70" class="branco text-center">Icons</td>
		</tr>
		<?php
		}
		while (!$rs->EOF())
		{
			$count++;
			$IDb = $rs->db_data['ID'];
			$Tracking = $rs->db_data["m_tracking"];
			$Ref = $rs->db_data["m_referencia"];
			$ID_Cliente = $rs->db_data["ID_Cliente"];
			$ID_Cliente2 = $rs->db_data["ID_Cliente2"];
			$Marca = $rs->db_data["m_marca"];
			$Modelo = $rs->db_data["m_modelo"];
			$cliente_nome = $rs->db_data["cliente_nome"];
			
			$notes = $rs->db_data["notes"];

			

			$cliente_sobrenome = $rs->db_data["cliente_sobrenome"];
			$cliente_apelido = $rs->db_data["cliente_apelido"];
			$cliente_empresa = $rs->db_data["cliente_empresa"];
			$DataFinalizado = $rs->db_data["DataFinalizado"];
			$NrSerie = $rs->db_data["m_nserie"];
			$imei = $rs->db_data["m_imei"];
			$novo_imei = $rs->db_data["m_novo_imei"];

			$DataInsert = $rs->db_data["DataInsert"];
			$Dataacao = $rs->db_data["DataUpdate"];

			// $imei = $rs->db_data["m_imei"];
			$m_origem = $rs->db_data["m_origem"];


			if(!$m_origem || $m_origem==1) $m_origem = 'Online';
			if($m_origem == 2) $m_origem = 'Loja de Matosinhos';
			if($m_origem == 3) $m_origem = 'Loja da Maia';


			$dias_passados = 0;
			$now = time();
			$your_date = strtotime($DataInsert);
			$datediff = $now - $your_date;

			$dias_passados = floor($datediff / (60 * 60 * 24));

			$cor = '#9ACD32';

			if($dias_passados >= 7) $cor = '#FFFF99'; // PASSARAM 7 OU MAIS DIAS, TEM QUE FICAR AMARELO
			else $cor = '#9ACD32'; // INFERIOR A 7 DIAS

			$sql2 = '';

			$equipamento_reincidente = 0; $imeis = '';
			if($imei!='' && $novo_imei!='' && $imei!=0 && $novo_imei!=0)
			{
				$imeis = $imei.';'.$novo_imei;
				$sql2 = 'SELECT * FROM rmas WHERE (m_imei = "'.trim($imei).'" OR m_novo_imei = "'.trim($imei).'" OR m_imei = "'.trim($novo_imei).'" OR m_novo_imei = "'.trim($novo_imei).'") AND ID<>'.$IDb.' '.$and_loja;
			}
			elseif($imei!='' && $imei!=0)
			{
				$imeis = $imei;
				$sql2 = 'SELECT * FROM rmas WHERE (m_imei = "'.trim($imei).'" OR m_imei = "'.trim($imei).'") AND ID<>'.$IDb.' '.$and_loja;
			}

			if($sql2 != '')
			{
				$rs3->db_open($db_link, $GLOBALS['DB_name'], $sql2);
				if(!$rs3->EOF())
				{
					$equipamento_reincidente = 1;
					$cor = '#FFA500';
				}
				$rs3->db_close();
			}

			$m_pago = $rs->db_data["m_pago"];
			$Estado = ''; $Estado = $rs->db_data['m_estado'];
			$estados_rmas = estado_encomenda_rmas($Estado, 'pt');

			if($estados_rmas['cor'] != '')
			{
				$cor = '#'.str_replace('#', '', $estados_rmas['cor']);
			}

			$titulo = ''; $titulo = $estados_rmas['titulo'];
			$readonly = 0; $readonly = $estados_rmas['readonly'];

			if($count % 2 != 0) {
				$class = "r1";
				$n = "1";
			} else {
				$class = "r2";
				$n = "2";
			}
			$i = 0;

			if(!$_GET['f_estados2'] || ($_GET['f_estados2']==1 && $dias_passados < 7 ) || ($_GET['f_estados2']==2 && $dias_passados >= 7) || ($_GET['f_estados2']==3 && $equipamento_reincidente ==1)    )
			{
				if(isset($_GET['pesquisar']) && $_GET['pesquisar']==2)
				{
					//Marca | Modelo;IMEI | IMEI NOVO;Data Entrada;Última Ação;Origem;Empresa;Cliente;Estado

					echo "DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).';';
					echo $Marca.' / '.$Modelo.';';
					echo str_replace(';', '<br>', $imeis).';';
					echo $DataInsert.';';
					echo $Dataacao.';';
					echo $m_origem.';';
					echo $cliente_empresa.';';
					echo $cliente_nome.' '.$cliente_apelido.';';

					if($m_pago == 1)echo "Não Pago;";
					if($m_pago == 0)echo "Pago;";

					echo "\n";
				}
				else
				{
					//$codigo = "DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT);

					$tracking_code = 'DB'.str_pad((int)$IDb, 6, "0", STR_PAD_LEFT);
					$tracking_url = 'pagina.php?d='.uniqID('').'&ID='.(int)$IDb;
					$tracking_title = htmlspecialchars((string)$Tracking, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
					if(trim((string)$notes) !== '')
					{
						$note_plain = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string)$notes, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
						$note_title = htmlspecialchars('Resposta do cliente: '.$note_plain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
						$reply_label = htmlspecialchars($tracking_code.' — nova resposta do cliente', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
						$codigo = '<a title="'.$note_title.'" aria-label="'.$reply_label.'" class="lista_links rma-customer-reply" href="'.$tracking_url.'"><span class="rma-customer-reply-code">'.$tracking_code.'</span><span class="rma-customer-reply-badge" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9.7 9.7 0 0 1-4-.9L3 21l1.7-4.5A8.3 8.3 0 0 1 3 11.5a8.4 8.4 0 0 1 9-8.5 8.4 8.4 0 0 1 9 8.5Z"/><path d="M8 11.5h.01M12 11.5h.01M16 11.5h.01"/></svg><span>Resposta</span></span></a>';
					}
					else $codigo = '<a title="'.$tracking_title.'" class="lista_links" href="'.$tracking_url.'">'.$tracking_code.'</a>';

					?>
					<tr bgcolor="<?php echo $cor?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor?>'" style="border-bottom:solid 1px #999999">
					<td width="40" align="center" height="30"><input type="checkbox" name="chk_<?php echo $IDb ?>" id="chk_<?php echo $IDb ?>" value="<?php echo $IDb ?>" class="cb"></td>
					<td width="70" align="left"><?php echo $codigo;?></td>
					<td width="120" align="left"><a title="<?php echo dotbright_rma_html($Marca)?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo dotbright_rma_html($Marca.' / '.$Modelo)?></a></td>
					<td width="120" align="right"><a title="<?php echo dotbright_rma_html($imei)?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo str_replace(';', '<br>', dotbright_rma_html($imeis));?></a></td>
					<td width="120" align="center"><a title="<?php echo $DataInsert?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo $DataInsert?></a></td>
					<td width="120" align="center"><a title="<?php echo $DataInsert?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo $Dataacao?></a></td>
					<td width="130" align="center"><a title="<?php echo $m_origem?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo $m_origem?></a></td>
					<td align="center"><a class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo dotbright_rma_html($cliente_empresa)?></a></td>
					<td width="150" align="center"><a title="<?php echo dotbright_rma_html($cliente_nome.' '.$cliente_apelido)?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>"><?php echo ($ID_Cliente > 0 ? '<u title="Associado a conta website">'.dotbright_rma_html($cliente_nome.' '.$cliente_apelido).'</u>' : ($ID_Cliente2 > 0 ? '<u title="Associado a rma sem conta website" style="text-decoration: underline dotted #000;">'.dotbright_rma_html($cliente_nome.' '.$cliente_apelido).'</u>' : dotbright_rma_html($cliente_nome.' '.$cliente_apelido)));?></a></td>
					<td width="130" align="center" valign="middle">
					<?php
						// ARRAY NO SETTINGS
						echo $titulo;

					?>
					</td>
					<td width="100" align="right" valign="middle">
						<?php
						$title = '';
						if($m_pago == 1){ $m_pago_image = 'dollar_red.png'; $title = "Não Pago";}
						if($m_pago == 0){ $m_pago_image = 'dollar_green.png'; $title = "Pago";}
						?>


						<?php if($equipamento_reincidente == 1){?>
						<a class="lista_links" href="list.php?pesquisar=1&f_tracking=&f_nrserie=&f_tipoequip=<?php echo rawurlencode($imeis);?>&f_estado=&f_origem=&f_emp=&f_cli=&f_tlm=&f_data1=&f_data2=&f_pago=&f_local=&f_origem=&flag=" title="Equipamento reincidente"><img src="../layout/history.png" border="0" width="20" /><a/>
						<?php }?>
						<?php if(in_array($Estado, $estados_fechados_entregue)){?><img src="../layout/<?php echo $m_pago_image?>" border="0" style="width: 20px;" title="<?php echo $title?>" /><?php }?>
						<a class="lista_links" href="../gera_pdf5.php?idtracking=<?php echo $IDb;?>&tipo=1&d=<?php echo date('YmdHis')?>" title="Exporta Folha Recepção"><img src="../layout/reception.png" border="0" width="20" /><a/>
						<a class="lista_links" href="../gera_pdf5.php?idtracking=<?php echo $IDb;?>&tipo=2&d=<?php echo date('YmdHis')?>" title="Exporta Relatório Intervenção"><img src="../layout/obra.png" border="0" width="20" /><a/>
						<?php if($readonly == 1){?>
						<a class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $IDb?>&reabrir=1" title="Re-Abrir Assistência"><img src="../layout/open.png" border="0" width="20" /><a/>
						<?php }?>
					</td>
					</tr>
					<?php
				}
			}
			$rs->db_next();
			$i++;
		}
		if(isset($_GET['pesquisar']) && $_GET['pesquisar']==2)
		{
			die();
		}
		else
		{
			?></table>
			<?php
		}
	}
	else
	{
		$class = "r1";
		$n = "1";
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class ?>" width="100%">
			<tr>
				<td width="30" align="center">Não foram encontrados dados para a pesquisa efetuada!</td>
			</tr>
		</table>
		<?php
	}
	$rs->db_close();
	if($n_pags > 1)
	{
		echo '<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table><table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0><tr><td class="t1" style="text-align:center" align="center"><table border=0 cellspacing=1 cellpadding=1 align="center"><tr>';

		$f_tracking = isset($_GET['f_tracking']) ? $_GET['f_tracking'] : '';
		$f_nrserie = isset($_GET['f_nrserie']) ? $_GET['f_nrserie'] : '';
		$f_emp = isset($_GET['f_emp']) ? $_GET['f_emp'] : '';
		$f_cli = isset($_GET['f_cli']) ? $_GET['f_cli'] : '';
		$f_tipoequip = isset($_GET['f_tipoequip']) ? $_GET['f_tipoequip'] : '';
		$f_estado = isset($_GET['f_estado']) ? $_GET['f_estado'] : '';
		$f_estados2 = isset($_GET['f_estados2']) ? $_GET['f_estados2'] : '';
		$flag = isset($_GET['flag']) ? $_GET['flag'] : '';
		$f_origem = isset($_GET['f_origem']) ? $_GET['f_origem'] : '';
		$top_search = isset($_GET['top_search']) ? $_GET['top_search'] : '';
		$f_data1 = isset($_GET['f_data1']) ? $_GET['f_data1'] : '';
		$f_data2 = isset($_GET['f_data2']) ? $_GET['f_data2'] : '';
		$f_tlm = isset($_GET['f_tlm']) ? $_GET['f_tlm'] : '';
		$f_pago = isset($_GET['f_pago']) ? $_GET['f_pago'] : '';
		$f_origem = isset($_GET['f_origem']) ? $_GET['f_origem'] : '';
		$f_local = isset($_GET['f_local']) ? $_GET['f_local'] : '';
		$quick_filter = isset($_GET['quick_filter']) ? $_GET['quick_filter'] : '';

		$url = '&pesquisar=1&f_tracking='.$f_tracking.'&f_nrserie='.$f_nrserie.'&f_emp='.$f_emp.'&f_cli='.$f_cli.'&f_tipoequip='.$f_tipoequip.'&f_estado='.$f_estado.'&f_estados2='.$f_estados2.'&flag='.$flag.'&f_origem='.$f_origem.'&top_search='.$top_search;
		$url .= '&f_data1='.$f_data1.'&f_data2='.$f_data2.'&f_tlm='.$f_tlm.'&f_pago='.$f_pago.'&f_origem='.$f_origem.'&f_local='.$f_local.'&quick_filter='.rawurlencode($quick_filter);

		if($pagina >= 8) $return .= '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'list.php?d='.uniqID('').'&pagina=1'.$url.'\',\'_self\')">primeira</td>';
		for ($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++) {
			$ash = '?pagina='.$pi;
			$pgn = 'list.php'.$ash.$url;
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="20" height="20" align="center" style="background:#FFF;color:#000;font-size:11px"><strong>'.sprintf(($pi)).'</strong></td>' : '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\''.$pgn.'\',\'_self\')"><u>'.sprintf($pi).'</u></td>' : '';
		}
		if($pagina != $n_pags) $return .= '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'list.php?d='.uniqID('').'&pagina='.$n_pags.$url.'\',\'_self\')">última</td>';
		echo $return;
		echo '</tr></table></td></tr></table>';
	}
	return 1;
}

function show_content_list_emp($reg)
{
	$path = "../../core/";
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar,$and_loja;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs3 = new DataObj;
	$porpagina = 20;

	$sql = '
			SELECT
				e.id_rma as id_rma, e.marca as marca, e.modelo as modelo, e.imei as imei, e.estado as estado, r.m_tracking as m_tracking
			FROM
				rmas_emp e
			INNER JOIN 
				rmas r
				on e.id_rma = r.ID
			WHERE
				e.estado = 2
			';


	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / $porpagina);
	if(!isset($_GET['pagina'])) $pagina = '1';
	else $pagina = $_GET['pagina'];
	$inicio = $pagina - 1;
	$inicio = $inicio * $porpagina;

	$sql2 = $sql." LIMIT $inicio, $porpagina";

	$rs->db_open($db_link, $DB_name, $sql2);

	$i = 0; $count = 0;	$return = '';
	if(!$rs->EOF())
	{		
		?>
		<table width="100%" border="0" cellspacing=0 cellpadding=1 class="p1">
		<tr>
			<td width="40" height="30"></td>
			<td width="70" class="branco text-center">Traking</td>
			<td width="70" class="branco text-center">Marca</td>
			<td width="120" class="branco text-center">Modelo</td>
			<td width="120" class="branco text-center">Imei</td>
			<td width="120" class="branco text-center">Estado</td>
		</tr>
		<?php
		while (!$rs->EOF())
		{
			$count++;
			$id_emp = $rs->db_data['id'];
			$id_rma = $rs->db_data['id_rma'];
			$marca = $rs->db_data["marca"];
			$modelo = $rs->db_data["modelo"];
			$imei = $rs->db_data["imei"];
			$estado = $rs->db_data["estado"];

			$Tracking = $rs->db_data['m_tracking'];

			if($estado == 1) $estado = 'Não foi entregue';
			elseif($estado == 2) $estado = 'Entregue';
			elseif($estado == 3) $estado = 'Devolvido';

			if($notes)$codigo = '<a style="background-color:#ff0000" title="'.addslashes($notes).'" title="'.$Tracking.'" class="lista_links" href="pagina.php?d='.uniqID('').'&ID='.$IDb.'">DB'.str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</a>';
			else $codigo = '<a title="'.$Tracking.'" class="lista_links" href="pagina.php?d='.uniqID('').'&ID='.$IDb.'">DB'.str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</a>';
			
			?>
			<tr style="border-bottom:solid 1px #999999;   background-color: rgb(255, 255, 255);">
				<td width="40" height="30"></td>
				<td width="70" align="left"><?php echo $codigo;?></td>
				<td width="120" align="center"><a title="<?php echo $marca?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id_rma?>" ><?php echo $marca?></a></td>
				<td width="120" align="center"><a title="<?php echo $modelo?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id_rma?>" ><?php echo str_replace(';', '<br>', $modelo);?></a></td>
				<td width="120" align="center"><a title="<?php echo $imei?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id_rma?>" ><?php echo $imei?></a></td>
				<td width="120" align="center"><a title="<?php echo $estado?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id_rma?>" ><?php echo $estado?></a></td>
			</tr>
			<?php
			$rs->db_next();
			$i++;
		}
	}
	else
	{
		$class = "r1";
		$n = "1";
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class ?>" width="100%">
			<tr>
				<td width="30" align="center">Não foram encontrados dados para a pesquisa efetuada!</td>
			</tr>
		</table>
		<?php
	}
	$rs->db_close();

	return 1;
}

function getImagensRmas($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$arr_imagens=array();
	$pos=0;
	$sql="SELECT * FROM products_images_rmas WHERE ID_ORG='".$ID."' order by Ordem";
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()){
		while(!$rs->EOF()){
			$arr_imagens[$pos]['ID']=$rs->db_data['ID'];
			$arr_imagens[$pos]['img_ext']=$rs->db_data['img_ext'];
			$arr_imagens[$pos]['ID_ORG']=$rs->db_data['ID_ORG'];
			$arr_imagens[$pos]['Ordem']=$rs->db_data['Ordem'];
			$pos++;
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $arr_imagens;
}

function rmas_images_save($ID,$Img_peq,$tem_foto='',$Apagar_img='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	//percorrer as imagens recebidas
	$img_path = '../../files/rmas/';
	for ($i=1; $i<=count($Img_peq["name"]); $i++)
	{

		if($Img_peq["name"][$i]!='')
		{ //contem imagem nova
			//obter a extensao
			$sql="SELECT * FROM products_images_rmas WHERE ID_ORG='".$ID."' and Ordem='".$i."' order by Ordem";
			// echo $sql.'<br>';
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				//já tem imagem
				$pos=strrpos($Img_peq["name"][$i],".");
				$img_ext=substr($Img_peq["name"][$i],$pos+1,strlen($Img_peq["name"][$i])+1-$pos);

				$ficheiro=$ID."_".$i.".".$rs->db_data['img_ext'];
				unlink($img_path.$ficheiro);

				if($img_ext == 'pdf') $name = str_replace('.'.$img_ext, '', $Img_peq["name"][$i]);
				else $name = '';

				$sql='UPDATE products_images_rmas SET img_ext="'.$img_ext.'", URL_Source="'.$name.'" WHERE ID_ORG="'.$ID.'"  and Ordem="'.$i.'"';
				$res=Runquery($db_link,$DB_name,$sql);



				copia_segura($Img_peq["tmp_name"][$i],$img_path.$ID."_".$i.".".$img_ext);
			}
			else
			{
				//nova imagem
				$pos=strrpos($Img_peq["name"][$i],".");
				$img_ext=substr($Img_peq["name"][$i],$pos+1,strlen($Img_peq["name"][$i])+1-$pos);

				$sql='INSERT INTO products_images_rmas(ID_ORG,img_ext,Ordem, URL_Source) VALUES ("'.$ID.'","'.$img_ext.'","'.$i.'","'.$name.'")';
				// echo $sql.'<br>';
				$res=Runquery($db_link,$DB_name,$sql);

				copia_segura($Img_peq["tmp_name"][$i],$img_path.$ID."_".$i.".".$img_ext);
			}
			$rs->db_close();
		}
		else
		{
			//não tem



			if(is_array($tem_foto) && is_array($Apagar_img))
			{
				if(isset($Apagar_img[$i]))
				{
					if($tem_foto[$i]==1 && $Apagar_img[$i]==1)
					{
						//se tem foto e  pretende eliminar
						$sql="SELECT * FROM products_images_rmas WHERE ID_ORG='".$ID."'  and Ordem='".$i."' order by Ordem";
						$rs->db_open($db_link, $DB_name, $sql);
						if(!$rs->EOF())
						{
							$ficheiro = $ID."_".$i.".".$rs->db_data['img_ext'];
							unlink($img_path.$ficheiro);

							$sql='DELETE from `products_images_rmas` WHERE ID_ORG="'.$ID.'"  and Ordem="'.$i.'"';
							$res=Runquery($db_link,$DB_name,$sql);
						}
						$rs->db_close();
					}
				}
			}
		}
	}

	$sql = "SELECT rma1_ext FROM products_images_rmas WHERE ID_ORG='".$ID."' ORDER BY Ordem Limit 1";
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$sql='UPDATE rmas SET imrma1_extg_ext="'.$rs->db_data['img_ext'].'" WHERE ID="'.$ID.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	else
	{
		$sql='UPDATE rmas SET rma1_ext="" WHERE ID="'.$ID.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
}

function mensagens_novas()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'SELECT ID FROM rmas WHERE notes <> "" AND lida="0" ORDER BY ID DESC');

	if(!$rs->EOF())
	{
		$ids = array();
		while(!$rs->EOF())
		{
			$ids[] = (int)$rs->db_data['ID'];
			$rs->db_next();
		}

		echo '<div class="rma-client-message-alert" role="status">';
		echo '<div class="rma-client-message-copy"><span class="rma-client-message-icon" aria-hidden="true">!</span><span><strong>Mensagens de clientes sem resposta</strong><small>'.count($ids).' RMA'.(count($ids) === 1 ? '' : 's').' a aguardar resposta</small></span></div>';
		echo '<div class="rma-client-message-links" aria-label="RMAs com mensagens por responder">';
		foreach($ids as $id)
		{
			echo '<a href="pagina.php?ID='.$id.'" title="Abrir RMA '.$id.'">'.$id.'</a>';
		}
		echo '</div>';
		echo '<a class="rma-client-message-view-all" href="list.php?quick_filter=mensagens_por_responder">Ver lista</a>';
		echo '</div>';
	}
	$rs->db_close();
}

?>
