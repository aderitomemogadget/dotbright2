<?php

function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_GET[TBID]) && $_GET[TBID] > 0) ? $_GET[TBID] : 0;
	$modulo_duplicar = (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;
	if(isset($_GET['task_form']) && $_GET['task_form'] == 'apaga')
    {
        $modulo_acao = 'apaga';
    }
    else $modulo_acao = (isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_GET DOS CAMPOS
		{
		    if($campos != TBID)
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
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
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_GET DOS CAMPOS
		{
			if($campos != TBID)
			{
				// echo '<pre style="color:#FFF">'.$campos.'</pre><br />';
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
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
    elseif($modulo_acao=='apaga')
    {
        $sql = 'DELETE FROM '.TBMODULO.' WHERE ID = ' . $modulo_id;
    }

	$res = Runquery($db_link, $DB_name, $sql);
    // echo '<pre style="color:#FFF">'.$sql.'</pre><br />';

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	//if($res) submeteDadosLinguas($id_acao);

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $modulo_id = (isset($_GET[TBID]) && $_GET[TBID] > 0) ? $_GET[TBID] : 0;
	$modulo_duplicar = (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;
	$rs = new DataObj;
	$sql='SELECT '.TBID.' FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$modulo_id.' AND Lang="'.$GLOBALS['admin_lang'].'"';
	// echo $sql.'<br />';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()) $modulo_acao = 'update'; else $modulo_acao = 'insere';
	$rs->db_close();

	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO_LANG;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'ID'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_GET DOS CAMPOS
		{
			if($campos != 'ID')
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= '"'.$modulo_id.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_GET[$campos]).'"';
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
		$sql = 'UPDATE '.TBMODULO_LANG.' SET ';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_GET DOS CAMPOS
		{
			if($campos != TBID && $campos != 'Lang' && $campos != 'ID')
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= $campos.'="'.$modulo_id.'"';
				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_GET[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= 'WHERE ID_PAI='.$_GET[TBID].' AND Lang="'.$GLOBALS['admin_lang'].'"';
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
}


function submete_ficheiro($file_name, $nome_ficheiro_old)
{
	$img_path = '../../files/'.FOLDER.'/';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;
	if($Img_peq["name"]!="")
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], ($pos+1), strlen($Img_peq['name']) + 1 - $pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old)) unlink($img_path.$nome_ficheiro_old);
		$token = date('YmdHis').uniqId();
		$nome_ficheiro = $token.".".$img_ext;

		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return $nome_ficheiro;
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?php

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?php echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	</div>
<?php
}
function show_fast_tools()
{
	global $ID, $admin_id, $BOTOES_L;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		$a=1;
		foreach($BOTOES_L as $key => $value)
		{
			if($a==2 && isset($_GET[TBID]) && $_GET[TBID] > 0)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}
			elseif($a!=2)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}

			$a++;
		}
	}

	/*<tr>
	<td class="preto" align="center" style="background:none">&nbsp;</td>
	</tr>
	<tr>
	<td class="preto" align="center" style="background:#EAEAEA"><?php include_once 'easycreate.php';?></td>
	</tr>*/?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?php }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff"><?php echo MODULO?></h4>
        </div>
    </div>

<?php }
function show_page_bottom_tools()
{?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="150" height="35" align="right" style="font-weight:bold">Para os Selecionados:&nbsp;</td>
<td height="35" width="275"><select name="ACCOES" id="ACCOES" class="form-control">
<option value="">-</option>
<?php if(isset($GLOBALS['BOTOES_B']) && !empty($GLOBALS['BOTOES_B'])){
foreach($GLOBALS['BOTOES_B'] as $value => $key){
echo '<option value="'.$key.'">'.$value.'</option>';
}}?>
</select></td>
<td height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>

<tr>
	<td colspan="3" align="left" style="font-weight:bold;padding: 30px">Para invocar o webservice deverá abrir a url: <font style="color:#0000ff"><?=$GLOBALS['site_http_url'].'core/webservices/get_orders.php?start_id=XXXXX'?></font><br><br>em que XXXX representa o número de encomenda a partir de onde pretende fazer a exportação</td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
<?php
}

function acoes_selecionados()
{
	if(isset($_GET['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		if($_GET['ACCOES']==1) // REMOVER REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==3) // ATIVAR REGISTOS
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_Activo=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==2) // DESATIVAR REGISTOS
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_Activo=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
	elseif(isset($_POST['ACCOES']))
	{
	 	global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['ACCOES']==1) // REMOVER REGISTO
		{
			$str_id = $_GET['bloco_id'];
			if($str_id > 0)
			{
				$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link) . $sql . '<br>';
			}
		}
		elseif($_POST['ACCOES']==3) // ATIVAR REGISTO
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Activo=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==2) // DESATIVAR REGISTO
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET Activo=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;

	$pesq = '';
	$sql = '
			SELECT
				b.'.TBID.', b.IP, b.Activo
			FROM
				'.TBMODULO.' b
			ORDER BY
				b.IP ASC
			';

	$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td width="30" height="35" align="center">&nbsp;</td>

		<td width="50" valign="middle" align="center"><b>Activo</b></td>
		<td valign="middle" align="left"><b>IP</b></td>
		<td width="10" valign="middle">&nbsp;</td>

		</tr>

		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];

			$Activo = $rs->db_data["Activo"];
			$IP = $rs->db_data["IP"];


			if($Activo==1)
			{
				if($cor=='#c8ecfa')	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#c8ecfa";$class="r2";$n="2";}
			}
			else
			{
				if($cor=='#FF9999')	{$cor="#FF6666";$class="r1";$n="1";}
				else				{$cor="#FF9999";$class="r2";$n="2";}
			}

			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
				<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
				<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo ($Activo ? 'SIM' : 'NÃO'); ?></a></td>
				<td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?=$IP?></a></td>
				<td valign="middle" height="32"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?task=apagar&<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><img src="img/delete-item.png" border="0" /></a></td>
			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{

			if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages = 'pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_ativo='.urlencode($_GET['bloco_ativo']);
			else $url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_ativo='.urlencode($_GET['bloco_ativo']);

			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}

		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php
	}
	$rs->db_close();
}
?>