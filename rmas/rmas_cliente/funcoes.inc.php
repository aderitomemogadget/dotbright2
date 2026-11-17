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
	<img src="../images/bsh.png" width="890">
<?php
}

function options_estado($selected = NULL, $flag = 0)
{
	$values = array(
		0 => 'Pendente',
		1 => 'Aceite',
		2 => 'Rececionado',
		3 => 'Rejeitado',
		4 => 'Substituído',
		5 => 'Creditado',
		6 => 'Reparado'
	);
	if(!$flag)
	{
		?>
		<option value="" <?php if(!$selected) echo 'selected';?>>Todos</option>
		<?php
	}
	else
	{
		?>
		<option value="" <?php if(!$selected) echo 'selected';?>>---------------</option>
		<?php
	}
	?>


	<?php
	foreach ($values as $key => $value)
	{
		?>
		<option value="<?=$key?>" <?php if(isset($selected) && $selected === "$key") echo 'selected';?>><?=$value?></option>
		<?php
	}

}
function show_page_tools($filter=0)
{
	global $f_estado,$f_pagamento;?>
	<style>.text-center{text-align: center;}</style>
	<script>
	function update_status(n)
	{
		var url="list.php?d=<?php echo uniqID('')?>&x_id="+n;
		if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
		window.open(url,"_self");
	}
	</script>

	<table width="100%" border="0" cellspacing=0 cellpadding=1 class="p1">
	<tr>
		<td width="30" height="30"></td>
		<td class="branco text-center">#</td>
		<td class="branco text-center">Nº Fat.</td>
		<td class="branco text-center">Nº Enc.</td>
		<td class="branco text-center">Cliente</td>
		<td class="branco text-center">Empresa</td>
		<td class="branco text-center">Data</td>
		<td class="branco text-center">Estado</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td align="center" height="30">
			<input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb" />
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_id" value="<?php if(isset($_GET["f_id"])) echo $_GET["f_id"]?>" style="height:20px;width:90%">
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_fat" value="<?php if(isset($_GET["f_fat"])) echo $_GET["f_fat"]?>" style="height:20px;width:90%">
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_enc" value="<?php if(isset($_GET["f_enc"])) echo $_GET["f_enc"]?>" style="height:20px;width:90%">
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_cli" value="<?php if(isset($_GET["f_cli"]))echo $_GET["f_cli"]?>" style="height:20px;width:90%" />
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_emp" value="<?php if(isset($_GET["f_emp"]))echo $_GET["f_emp"]?>" style="height:20px;width:90%" />
		</td>
		<td class="branco" align="center">
			<input type="text" name="f_data1" id="f_data1" value="<?php if(isset($_GET["f_data1"])) echo $_GET["f_data1"]?>"  style="height:20px;width:90%">
		</td>
		<td class="branco" align="center">
			<select name="f_estado" style="width:90%">
				<?php options_estado($_GET["f_estado"]); ?>
			</select>
		</td>
		<td colspan="2" align="right"><a title="pesquisar" href="javascript:;" onclick="javascript:document.forms['lista_paginas'].submit()"><img border="0" src="../layout/search.png" /></a></td>
	</tr>
<?php }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130" class="t1">&nbsp;<?php echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?php echo write_lang('key_apagar','Apagar')?></option>
			</select>
		</td>
		<td width="5"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_acao','Aplicar Acção')?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890">
<?php
}
function show_fast_tools($list=null)
{?>
	<table width="200" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td height="27">&nbsp;</td>
	</tr>
	</table>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width="5" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr>
		<td width="5" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo write_lang('key_criar_novo_rma','Criar Novo RMA')?>" onclick="javascript:window.open('pagina.php?ID=0&d=<?php echo uniqid("");?>','_self')"></td>
		<td width="5" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200" height="30">
<?php }

function show_content_list($reg=null)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$porpagina=20;
	if($reg) $sql='SELECT * FROM rmas_cliente WHERE ID IN('.$reg.') ORDER BY criado DESC';
	else $sql='SELECT * FROM rmas_cliente ORDER BY criado DESC';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	$total=$rs->db_rows;
	$rs->db_close();
	$n_pags=ceil($total / $porpagina);
	if(!isset($_GET['pagina'])) $pagina='1';
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * $porpagina;
	$sql2=$sql." LIMIT $inicio, $porpagina";
	$rs->db_open($db_link,$DB_name,$sql2);
	$i=0;$count=0;
	$return = '';
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			$IDb=$rs->db_data['id'];
			$client_id=$rs->db_data["cliente_id"];
			$nome_cliente=$rs->db_data["nome"].' '.$rs->db_data['apelido'];
			$empresa = $rs->db_data['empresa'];
			$nfat=$rs->db_data["nfat"];
			$nenc=$rs->db_data["nenc"];
			$criado=$rs->db_data["criado"];
			$alterado=$rs->db_data["alterado"];
			$estado=$rs->db_data["estado"];

			switch ($estado)
			{
				case 0: $cor="#FFFF99";break;
				case 1: $cor="#F7B500";break;
				case 2: $cor="#F6F6F6";break;
				case 3: $cor="#F6F6F6";break;
				case 4: $cor="#F6F6F6";break;
				case 5: $cor="#8EE59F";break;
				case 6: $cor="#00FF00";break;
				case 7: $cor="#FF3333";break;
				case 8: $cor="#FFAAAA";break;
				case 9: $cor="#F7B500";break;
			}

			if($count % 2 != 0)	{$class="r1";$n="1";}
			else				{$class="r2";$n="2";}
			$i=0;

			$url = 'pagina.php?ID='.$IDb;
			?>
				<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="border-bottom:solid 1px #999999">
					<td align="center">
						<input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb">
					</td>
					<td align="center">
						<a class="lista_links" href="<?=$url?>"><?php echo "RMA".str_pad((int) $IDb,6,"0",STR_PAD_LEFT);?></a>
					</td>
					<td align="left">
						<a class="lista_links" href="<?=$url?>"><?php echo $nfat?></a>
					</td>
					<td align="left">
						<a class="lista_links" href="<?=$url?>"><?php echo $nenc?></a>
					</td>
					<td align="center">
						<a class="lista_links" href="<?=$url?>"><?php echo $nome_cliente;?></a>
					</td>
					<td align="left">
						<a class="lista_links" href="<?=$url?>"><?php echo $empresa;?></a>
					</td>
					<td align="left">
						<a class="lista_links" href="<?=$url?>"><?php echo $criado;?></a>
					</td>
					<td align="center" valign="middle">
						<select id="m_estado_<?php echo $IDb?>">
							<?php options_estado($estado); ?>
						</select>
					</td>
					<td align="center">
						<a href="javascript:;" onclick="update_status(<?php echo $IDb?>)"><img border="0" src="save.png" /></a>
					</td>
					<td align="center">
						<a class="italico" href="<?=$url?>&task=apagar"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Cliente')?>" src="del2.png" border="0"></a>
					</td>
				</tr>
			<?php
			$rs->db_next();
			$i++;
		}
	}
	else
	{
	$class="r1";$n="1";
	?>
	<table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>" width="100%"><tr><td width="30" align="center">Não foram encontrados dados para a pesquisa efetuada!</td></tr></table>
	<?php
	}
	$rs->db_close();
	if($n_pags > 1)
	{
		echo '<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table><table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0><tr><td class="t1" style="text-align:center" align="center"><table border=0 cellspacing=1 cellpadding=1 align="center"><tr>';

		$f_id     = isset($_GET['f_id']) ? $_GET['f_id'] : '';
		$f_enc    = isset($_GET['f_enc']) ? $_GET['f_enc'] : '';
		$f_fat    = isset($_GET['f_fat']) ? $_GET['f_fat'] : '';
		$f_cli    = isset($_GET['f_cli']) ? $_GET['f_cli'] : '';
		$f_emp    = isset($_GET['f_emp']) ? $_GET['f_emp'] : '';
		$f_data1  = isset($_GET['f_data1']) ? $_GET['f_data1'] : '';
		$f_estado = isset($_GET['f_estado']) ? $_GET['f_estado'] : '';

		$url='&f_id='.$f_id.'&f_fat='.$f_fat.'&f_enc='.$f_enc.'&f_cli='.$f_cli.'&f_emp='.$f_emp.'&f_data1='.$f_data1.'&f_estado='.$f_estado;
		if($pagina >=8)
			$return .= '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'list.php?d='.uniqID('').'&pagina=1'.$url.'\',\'_self\')">primeira</td>';
		for($pi=($pagina - 6);$pi < ($pagina + 7);$pi++)
		{
			$ash='?pagina='.$pi;
			$pgn='list.php'.$ash.$url;
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina== $pi ? '<td width="20" height="20" align="center" style="background:#FFF;color:#000;font-size:11px"><strong>'.sprintf(($pi)).'</strong></td>' : '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\''.$pgn.'\',\'_self\')"><u>'.sprintf($pi).'</u></td>' : '';
		}
		if($pagina !=$n_pags) $return .= '<td width="20" height="20" align="center" style="background:#CCFF33;color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'list.php?d='.uniqID('').'&pagina='.$n_pags.$url.'\',\'_self\')">última</td>';
		echo $return;
		echo '</tr></table></td></tr></table>';
	}
	return 1;
}
function show_content_from_search($reg)
{
	if(!$reg)return FALSE;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$sql="SELECT * FROM rmas_cliente WHERE id IN($reg) ORDER BY criado DESC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;

	while(!$rs->EOF())
	{
		$IDb=$rs->db_data['ID'];
		$client_id=$rs->db_data["cliente_id"];
		$nome_cliente=$rs->db_data["nome"].' '.$rs->db_data['apelido'];
		$empresa = $rs->db_data['empresa'];
		$nfat=$rs->db_data["nfat"];
		$nenc=$rs->db_data["nenc"];
		$criado=$rs->db_data["criado"];
		$alterado=$rs->db_data["alterado"];
		$estado=$rs->db_data["estado"];

		switch ($estado)
		{
			case 0: $cor="#FFFF99";break;
			case 1: $cor="#F7B500";break;
			case 2: $cor="#F6F6F6";break;
			case 3: $cor="#F6F6F6";break;
			case 4: $cor="#F6F6F6";break;
			case 5: $cor="#8EE59F";break;
			case 6: $cor="#00FF00";break;
			case 7: $cor="#FF3333";break;
			case 8: $cor="#FFAAAA";break;
			case 9: $cor="#F7B500";break;
		}

		if($count % 2 != 0)	{$class="r1";$n="1";}
		else				{$class="r2";$n="2";}
		$i=0;

		$url = 'pagina.php?ID='.$IDb;
		?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="border-bottom:solid 1px #999999">
				<td align="center">
					<input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb">
				</td>
				<td align="center">
					<a class="lista_links" href="<?=$url?>"><?php echo "RMA".str_pad((int) $IDb,6,"0",STR_PAD_LEFT);?></a>
				</td>
				<td align="left">
					<a class="lista_links" href="<?=$url?>"><?php echo $nfat?></a>
				</td>
				<td align="left">
					<a class="lista_links" href="<?=$url?>"><?php echo $nenc?></a>
				</td>
				<td align="center">
					<a class="lista_links" href="<?=$url?>"><?php echo $nome_cliente;?></a>
				</td>
				<td align="left">
					<a class="lista_links" href="<?=$url?>"><?php echo $empresa;?></a>
				</td>
				<td align="left">
					<a class="lista_links" href="<?=$url?>"><?php echo $alterado;?></a>
				</td>
				<td align="center" valign="middle">
					<select id="m_estado_<?php echo $IDb?>">
						<?php options_estado($Estado); ?>
					</select>
				</td>
				<td align="center">
					<a href="javascript:;" onclick="update_status(<?php echo $IDb?>)"><img border="0" src="save.png" /></a>
				</td>
				<td align="center">
					<a class="italico" href="<?=$url?>&task=apagar"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Cliente')?>" src="del2.png" border="0"></a>
				</td>
			</tr>
		<?php
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$nome       = str2SQLstr($_POST["m_nome"]);
	$apelido    = str2SQLstr($_POST["m_apelido"]);
	$empresa    = str2SQLstr($_POST["m_empresa"]);
	$nif        = str2SQLstr($_POST["m_nif"]);
	$telefone   = str2SQLstr($_POST["m_telefone"]);
	$email      = str2SQLstr($_POST["m_email"]);
	$morada     = str2SQLstr($_POST["m_morada"]);
	$codp       = str2SQLstr($_POST["m_codp"]);
	$nenc       = str2SQLstr($_POST["m_nenc"]);
	$nfat       = str2SQLstr($_POST["m_nfat"]);
	$obs        = str2SQLstr($_POST["m_obs"]);
	$obsint     = str2SQLstr($_POST["m_obsint"]);
	$obscli     = str2SQLstr($_POST["m_obscli"]);
	$estado     = str2SQLstr($_POST["m_estado"]);
	$alterado   = str2SQLstr(date('Y-m-d H:i:s'));

	echo $sql="INSERT INTO rmas_cliente (nome, apelido, empresa, nif, telefone, email, morada, codp, nfat, nenc, obs, obsint, obscli, estado, criado) VALUES ($nome, $apelido, $empresa, $nif, $telefone, $email, $morada, $codp, $nfat, $nenc, $obs, $obsint, $obscli, $estado, $alterado)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)
		echo db_error($db_link);
	else
	{
		$id = db_get_last_insert_id($db_link);
		$refs = $_POST['ref'];
		$qtds = $_POST['qtd'];
		$descs = $_POST['desc'];
		if(count($refs))
		{
			for($i = 0; $i < count($refs); $i++)
			{
				$qtd = $qtds[$i];
				$ref = str2SQLstr($refs[$i]);
				$desc = str2SQLstr($descs[$i]);
				$sql = "INSERT INTO rmas_cliente_linha (rmas_cliente_id, qtd, ref, descricao, ordem, criado) VALUES ($id, $qtd, $ref, $desc, $i, $alterado)";
				Runquery($db_link, $DB_name, $sql);
			}
		}
	}
	db_closedb_conection($db_link);
	return $res;
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$id = (isset($_POST['ID']) && $_POST['ID']) ? $_POST['ID'] : NULL;

	if($id)
	{
		$res=Runquery($db_link,$DB_name,"DELETE FROM rmas_cliente WHERE ID=$id");
		if(!$res)
			echo db_error($db_link);
		else
		{
			$res1=Runquery($db_link,$DB_name,"DELETE FROM rmas_cliente_linha WHERE rma_cliente_id=$id");
			if(!$res1)
				echo db_error($db_link);
		}
		db_closedb_conection($db_link);
		return $res;
	}
	return NULL;
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$res = NULL;

	$id = (isset($_POST['ID']) && $_POST['ID']) ? $_POST['ID'] : NULL;

	$altera=0;
	$rs->db_open($db_link,$DB_name,"SELECT id FROM rmas_cliente WHERE ID=$id");
	$altera = (!$rs->EOF()) ? 1 : 0;
	$rs->db_close();

	if(!$altera)
		return InsertNewRecord();
	else
	{
		$nome       = str2SQLstr($_POST["m_nome"]);
		$apelido    = str2SQLstr($_POST["m_apelido"]);
		$empresa    = str2SQLstr($_POST["m_empresa"]);
		$nif        = str2SQLstr($_POST["m_nif"]);
		$telefone   = str2SQLstr($_POST["m_telefone"]);
		$email      = str2SQLstr($_POST["m_email"]);
		$morada     = str2SQLstr($_POST["m_morada"]);
		$codp       = str2SQLstr($_POST["m_codp"]);
		$nenc       = str2SQLstr($_POST["m_nenc"]);
		$nfat       = str2SQLstr($_POST["m_nfat"]);
		$obs        = str2SQLstr($_POST["m_obs"]);
		$obsint     = str2SQLstr($_POST["m_obsint"]);
		$obscli     = str2SQLstr($_POST["m_obscli"]);
		$estado     = str2SQLstr($_POST["m_estado"]);
		$alterado   = str2SQLstr(date('Y-m-d H:i:s'));

		$sql="UPDATE rmas_cliente SET nome=$nome, apelido=$apelido, empresa=$empresa, nif=$nif, telefone=$telefone, email=$email, morada=$morada, codp=$codp, nfat=$nfat, nenc=$nenc, obs=$obs, obsint=$obsint, obscli=$obscli, estado=$estado, alterado=$alterado WHERE ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)
			echo db_error($db_link);
		else
		{
			$sql_delete_linhas = "DELETE FROM rmas_cliente_linha WHERE rmas_cliente_id=$id";
			$res1=Runquery($db_link,$DB_name,$sql_delete_linhas);

			$refs = $_POST['ref'];
			$qtds = $_POST['qtd'];
			$descs = $_POST['desc'];
			if(count($refs))
			{
				for($i = 0; $i < count($refs); $i++)
				{
					echo $ref= $refs[$i].'<br>';
					$qtd = $qtds[$i];
					$ref = str2SQLstr($refs[$i]);
					$desc = str2SQLstr($descs[$i]);
					$sql = "INSERT INTO rmas_cliente_linha (rmas_cliente_id, qtd, ref, descricao, ordem, criado) VALUES ($id, $qtd, $ref, $desc, $i, $alterado)";
					echo $sql."<br>";
					Runquery($db_link, $DB_name, $sql);
				}
			}
		}
		db_closedb_conection($db_link);
	}
	if($_POST['envio_email']) enviaEmail($_POST['ID']);
	return $res;
}

function enviaEmail($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$sql='SELECT id AS Tracking, estado, email  FROM rmas_cliente WHERE id="'.$id.'"';
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$Tracking="RMA".str_pad((int) $rs->db_data['Tracking'],6,"0",STR_PAD_LEFT);
		$txt_obs = '';
		$values = array(
			0 => 'Pendente',
			1 => 'Aceite',
			2 => 'Rececionado',
			3 => 'Rejeitado',
			4 => 'Substituído',
			5 => 'Creditado',
			6 => 'Reparado'
		);

		$email = $rs->db_data['email'];
		$body=array(
			'[rmaid]'=> $Tracking,
			'[estado]'=> $values[$rs->db_data['estado']],
			'[rmapdf]'=> '<a target="_blank" href="'.$GLOBALS['site_http_url'].'gera_pdf5.php?idtracking='.$id.'&tipo=4&Dump='.uniqid("").'">clique aqui</a>',
			'[rmapage]'=> '<a target="_blank" href="'.seo('pages.php?page_id=38').'">clique aqui</a>',
		);

		$message=template_email($GLOBALS['template_estado_rmacliente'], $body);
		global $EmailFrom,$Default_RMAS_Email;
		$EmailFrom=$Default_RMAS_Email;
		send_email($email, 'O seu RMA mudou de estado para '.$values[$rs->db_data['estado']], $message['body']);
	}
}

function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$cond='';
	if(isset($_GET['f_estado']) && $_GET['f_estado'])
		$cond .= ' AND estado="'.$_GET['f_estado'].'" ';

	if(isset($_GET['f_obs']) && $_GET['f_obs'] != '')
		$cond .= 'AND obsint LIKE "%'.$_GET['f_obs'].'%"';

	if(isset($_GET['f_id']) && $_GET['f_id'] != '') $cond .= ' AND id LIKE "%'.$_GET['f_id'].'%" ';
	if(isset($_GET['f_fat']) && $_GET['f_fat'] != '') $cond .= ' AND nfat LIKE "%'.$_GET['f_fat'].'%" ';
	if(isset($_GET['f_enc']) && $_GET['f_enc'] != '') $cond .= ' AND nenc LIKE "%'.$_GET['f_enc'].'%" ';
	if(isset($_GET['f_emp']) && $_GET['f_emp'] != '') $cond .= ' AND (empresa LIKE "%'.$_GET['f_emp'].'%"';
	if(isset($_GET['f_data1']) && $_GET['f_data1'] != '') $cond .= ' AND criado LIKE "%'.$_GET['f_data1'].'%" ';
	if(isset($_GET['f_cli']) && $_GET['f_cli'] != '')
	{
		$v_nomecli = explode(' ', rtrim(ltrim($_GET['f_cli'])));
		if(count($v_nomecli)>1){
			$cond .= ' AND (';
			for ($i=0;$i < count($v_nomecli);$i++) {
				for ($j=0;$j < count($v_nomecli);$j++) {
					$cond .= ' (nome REGEXP "[[:<:]]'.$v_nomecli[$i].'[[:>:]]" AND apelido  REGEXP "[[:<:]]'.$v_nomecli[$j].'[[:>:]]") OR ';
				}
			}
			$cond = substr($cond, 0,-3);
			$cond .= ' )';
		}
		else
		{
			$cond .= ' AND (nome LIKE "%'.$_GET['f_cli'].'%" OR apelido LIKE "%'.$_GET['f_cli'].'%") ';
		}
	}

	$sql="SELECT DISTINCT (id) FROM rmas_cliente WHERE 1=1 $cond ORDER BY criado DESC";

	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res)
		echo "ERRO";
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

