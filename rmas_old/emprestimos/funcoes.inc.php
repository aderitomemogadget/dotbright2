<?php

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

function show_fast_tools($list=null)
{
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
		<?php if($_GET['ID'] > 0){ ?>
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
		<?php /* <tr><td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_imprimir_reparacao','Imprime Folha Reparação')?>" onclick="javascript:window.open('../gera_pdf5.php?idtracking=<?php echo $_GET['ID'];?>&tipo=3&d=<?php echo uniqid('');?>','_self')"></td></tr><?php */ } ?>
	</table>
	<br>
	<?php 
}

function show_fast_tools2($list=null)
{
	?>
	<table width="200" border=0 cellspacing=0 cellpadding=0 style="border:#000 1px solid;background:#333039">
		<tr >
			<td height="25" class="titulo2" align="center" style="border-bottom:#000 1px solid">FAST TOOLS</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr >
			<td class=preto align="center"><input class="botao" type="button" style="width:190px;height:30px" value="CRIAR NOVO Telemóvel" onclick="javascript:window.open('pagina.php?ID=0&d=<?php echo uniqid("");?>','_self')"></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<?php 
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


/*--------------------------------------------------------------------------------------------------------------------------------------------------------*/

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
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
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

	// echo '<pre>'.$sql.'</pre>';
	// die();
	$res = Runquery($db_link, $DB_name, $sql);

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

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


function show_page_tools($filter = 0)
{
	global $f_estado, $f_pagamento;
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
									<td style="vertical-align:bottom"><?php mensagens_novas(); ?></td>
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

	$sql = 'SELECT * FROM telemoveis_emp';


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
			<td width="70" class="branco text-center">Marca</td>
			<td width="120" class="branco text-center">Modelo</td>
			<td width="120" class="branco text-center">Imei</td>
			<td width="120" class="branco text-center">Estado</td>
		</tr>
		<?php
		while (!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data['id'];
			$marca = $rs->db_data["marca"];
			$modelo = $rs->db_data["modelo"];
			$imei = $rs->db_data["imei"];
			$estado = $rs->db_data["estado"];

			if($estado == 1) $estado = 'Sem danos';
			elseif($estado == 2) $estado = 'Camara partida';
			elseif($estado == 3) $estado = 'Traseira danificada';

			?>
			<tr style="border-bottom:solid 1px #999999;   background-color: rgb(255, 255, 255);">
				<td width="40" height="30"></td>
				<td width="120" align="center"><a title="<?php echo $marca?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id?>" ><?php echo $marca?></a></td>
				<td width="120" align="center"><a title="<?php echo $modelo?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id?>" ><?php echo str_replace(';', '<br>', $modelo);?></a></td>
				<td width="120" align="center"><a title="<?php echo $imei?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id?>" ><?php echo $imei?></a></td>
				<td width="120" align="center"><a title="<?php echo $estado?>" class="lista_links" href="pagina.php?d=<?php echo uniqID('') ?>&ID=<?php echo $id?>" ><?php echo $estado?></a></td>
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


?>