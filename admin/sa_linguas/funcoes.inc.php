<?php

function gera_lista_grupos_users($blocos_users)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$blocos_users2 = explode(';',$blocos_users);

	global $admin_id;
	$where = 'WHERE Grupo_ID=5';
	$rs->db_open($db_link,$DB_name,'Select ID, Nome from logins '.$where);
	echo '<select name="listagrupos[]" id="listagrupos" multiple="multiple"  class="form-control">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if(in_array($rs->db_data['ID'],$blocos_users2)) echo ' selected ';
		echo '>';
		echo $rs->db_data['Nome'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}

function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT bloco_id, bloco_id_pai FROM $table WHERE bloco_id=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["bloco_id"];
		$id_org1=$rs->db_data["bloco_id_pai"];
		$rs->db_close();
		$sql="SELECT bloco_titulo FROM $table"."_lang WHERE bloco_id=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["bloco_titulo"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" - <a href=\"$pagina"."?bloco_id=$codigo&do=".date('YmdHis')."\" class=\"brancoh\">$C1</a>";
	}
	return $str;
}

function gera_lista_path($id_org=0,$T,$Tlang,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="0" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="'.TBID.'_pai" style="width:350px" class="form-control">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}

function gera_lista_path_open($id_org=0,$T,$Tlang,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="'.$_GET['bloco_id'].'" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="bloco_open" style="width:350px" class="form-control">';
	if($id_org==0) echo '<option value="0" selected>- Esta -</option>';
	else echo '<option value="0">- Esta -</option>';
    while(!$rs->EOF())
    {
	    if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
		gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path_open2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
       if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}


function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	$bloco_embalagem_arr = array(); $bloco_cor_arr = array(); $bloco_iguaria_arr = array(); $bloco_vinho_arr = array();
	$c=count($_POST); $keys=array_keys($_POST);
	while($ml < $c)
	{
		if(substr_count($keys[$ml],'bloco_embalagem_')) $bloco_embalagem_arr[] = $_POST[$keys[$ml]];
		if(substr_count($keys[$ml],'bloco_cor_')) $bloco_cor_arr[] = $_POST[$keys[$ml]];
		if(substr_count($keys[$ml],'bloco_iguaria_')) $bloco_iguaria_arr[] = $_POST[$keys[$ml]];
		if(substr_count($keys[$ml],'bloco_vinho_')) $bloco_vinho_arr[] = $_POST[$keys[$ml]];
		$ml++;
	}

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i>0){$sql .= ', ';}

				$sql .= '"'.addslashes($_POST[$campos]).'"';

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
			if($campos != TBID)
			{

				if($i > 0){$sql .= ', ';}
				$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';

				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	if($res) submeteDadosLinguas($id_acao);

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs = new DataObj;$rs2 = new DataObj;



	$sql = 'SELECT Sigla FROM linguas  ORDER BY Ordem ASC';
	// where Visivel=1
	$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{


			$sql='SELECT '.TBID.' FROM '.TBMODULO_LANG.' WHERE '.TBID.'='.$modulo_id.' AND bloco_lang="'.$rs2->db_data['Sigla'].'"';
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF()) $modulo_acao = 'update'; else $modulo_acao = 'insere';
			$rs->db_close();

			$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];

			if($modulo_acao=='insere')
			{
				$sql = 'INSERT INTO '.TBMODULO_LANG;
				$sql .= '(';
				$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'bloco_lang_id'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i = 0;
				foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != 'bloco_lang_id')
					{
						if($i>0){$sql .= ', ';}
						if($campos=='bloco_id')
						{
							$sql .= '"'.$modulo_id.'"';
						}
						elseif($campos=='bloco_texto')
						{
							$sql .= '"'.addslashes($_POST[$campos.'_'.$rs2->db_data['Sigla']]).'"';
						}
						elseif($campos=='bloco_lang')
						{
							$sql .= '"'.addslashes($rs2->db_data['Sigla']).'"';
						}
						elseif($campos=='bloco_variavel')
						{
							$sql .= '"'.addslashes($_POST['bloco_titulo']).'"';
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
				$sql = 'UPDATE '.TBMODULO_LANG.' SET ';
				$i = 0;
				foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
				{
					if($campos != TBID && $campos != 'bloco_lang' && $campos != 'bloco_lang_id')
					{
						if($campos=='bloco_id')
						{
							if($i > 0){$sql .= ', ';}
							$sql .= $campos.'="'.$modulo_id.'"';
							$i++;
						}
						elseif($campos=='bloco_texto')
						{
							if($i > 0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST[$campos.'_'.$rs2->db_data['Sigla']]).'"';
							$i++;
						}
						elseif($campos=='bloco_variavel')
						{
							if($i > 0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST['bloco_titulo']).'"';
							$i++;
						}
						/* else
						{
							if($i > 0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
						} */

					}
				}
				$sql .= ' WHERE '.TBID.'='.$modulo_id.' AND bloco_lang="'.$rs2->db_data['Sigla'].'"';
			}
			$res = Runquery($db_link, $DB_name, $sql);

			// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';

			$rs2->db_next();
		}
	}
	$rs2->db_close();
}


function submete_ficheiro($file_name, $nome_ficheiro_old)
{
	$img_path = '../../files/documentos/';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;
	if($Img_peq["name"]!="")
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{
			unlink($img_path.$nome_ficheiro_old);
		}
		$token = date('YmdHis').bin2hex(openssl_random_pseudo_bytes(5));
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
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php


			$a++;
		}
	}
	?>
	</table>
<?php }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff"><?php echo MODULO?></h4>
        </div>
    </div>

	<script>
	function checkFields(){
		return true;
	}
	</script>

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
<td  height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>
</table>
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['ACCOES']==4) // REMOVER REGISTOs
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

				}
			}
		}
	}
	elseif(isset($_GET['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_GET['ACCOES']==4) // REMOVER REGISTOs
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

	$pesq = '';$pesq2='';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND (b.bloco_titulo like "%'.$_GET['DESIGNACAO'].'%" OR bl.bloco_texto like "%'.$_GET['DESIGNACAO'].'%")';
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq2 = ' INNER JOIN '.TBMODULO_LANG.' bl ON bl.bloco_id = b.bloco_id ';
	}
	$sql = '
			SELECT
				b.'.TBID.', b.bloco_titulo
			FROM
				'.TBMODULO.' b
				 '.$pesq2.'
			WHERE
				1=1
				'.$pesq.'
			GROUP BY
				b.'.TBID.' ASC
			ORDER BY
				b.'.TBID.' ASC
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
		<td height="35" align="center">&nbsp;</td>
		<td valign="middle" align="left"><b>Chave/Texto</b></td>
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td>

		</tr>
		<tr bgcolor="#FFFFFF">
		<td height="35" align="center"><label class="label_check2" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>

		<td valign="middle" align="left"><input type="text" style="width:100%" name="DESIGNACAO" id="DESIGNACAO" class="form-control" value="<?php if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>
		<td valign="middle" align="center" colspan="2" width="50"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa('<?php echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$bloco_visivel = 1;
			$bloco_visivel_img = (isset($bloco_visivel) && $bloco_visivel==1) ? 'ok.png' : 'hidden.png';
			$bloco_titulo = $rs->db_data["bloco_titulo"];


			if($bloco_visivel==1)
						{
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#FFFFFF";$class="r2";$n="2";}
			}
			else
			{
				if($count % 2 != 0)	{$cor="#dbdada";$class="r1";$n="1";}
				else				{$cor="#e8e8e8";$class="r2";$n="2";}
			}

			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">

			<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($bloco_titulo){echo htmlentities_b(stripslashes($bloco_titulo));} else echo "[Edit]";?></a></td>
			<td align="center" valign="middle" colspan="2"><a title="Duplicar" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&duplicar=1&Dump=<? echo uniqid("");?>"><img src="img/next-item.png" border="0" /></a></td>


			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{

			if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages = 'pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']);
			else $url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']);

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
				$pgn='onclick=window.open("'.$url_pages.'&pagina='.$n_pags.'","_self")';
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