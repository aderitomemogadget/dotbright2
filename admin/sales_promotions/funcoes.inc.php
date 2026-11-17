<?php
function gera_lista_familias($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.ID, pfl.Cabecalho
			FROM products_families pf
			INNER JOIN sales_promotions_products spp ON spp.FAMILIA = pf.ID
			INNER JOIN products_families_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				spp.MARCAX=0
				AND
				spp.FAMILIAX > 0
				AND
				spp.promo_id='".$id_org."'
				AND
				spp.DESCONTO='".$line."'
			GROUP BY
				pf.ID
			ORDER BY
				 pfl.Cabecalho ASC
			";
		// echo $sql.'<br />';

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';

		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' />';


		$return .= htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'</label>';


	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;

}
function show_breadcrums_recursivo_on($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$sql = 'select pf.ID, pf.ID_ORG, pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID="'.$id_pai.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$str) $str = '-';	else $str='';
	if(!$rs->EOF())
	{
		if($rs->db_data['ID_ORG']==0)
		{
			$str=htmlentities_memo(stripslashes(($rs->db_data['Cabecalho']))).$str;
		}
		else
		{
			$str=htmlentities_memo(stripslashes($rs->db_data['Cabecalho'])).$str;
			$str=show_breadcrums_recursivo_on($rs->db_data['ID_ORG']).$str;
		}
	}
	$rs->db_close();

	return $str;
}
function getbrandsx($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT Nome FROM brands_lang WHERE ID_PAI="'.$id.'" AND Lang="'.$GLOBALS['admin_lang'].'"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		return $rs->db_data['Nome'];
	}
	$rs->db_close();
	return;
}
function getfamiliesx($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT Cabecalho FROM products_families_lang WHERE ID_PAI="'.$id.'" AND Lang="'.$GLOBALS['admin_lang'].'"';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		return ' | '.$rs->db_data['Cabecalho'];
	}
	$rs->db_close();
	return;
}
function gera_lista_produtosx($id_org=0, $space=1, $line='', $tipo=0, $ID_PAI=0, $Desconto=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;
	$return = '';
	$sql = "
			SELECT pf.ID, pfl.Cabecalho, spp.MARCAX, spp.FAMILIAX, COUNT(DISTINCT(pf.ID)) AS TOTAL
			FROM products pf
			INNER JOIN sales_promotions_products spp ON spp.PROD_ID = pf.ID
			INNER JOIN products_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				spp.promo_id='".$id_org."'
				AND
				spp.DESCONTO='".$line."'
			GROUP BY
				spp.MARCAX, spp.FAMILIAX
			ORDER BY
				spp.MARCAX ASC, spp.FAMILIAX desc
			";

	$obj->db_open($db_link, $DB_name, $sql);
	$ln = '';
	if(!$obj->EOF())
	{
		$return = '<table width="100%">';
		$return .= '<tr><td height="25"><b> Marca / Familia</b></td><td width="130" align="center"> <b>Total Produtos</b></td><td width="130" align="center"> <b>Ver produtos</b></td></tr>';
		// $return .= '<tr><td> Marca / Familia</td><td width="100"> Total Produtos</td><td width="100"> Ver produtos</td></tr>';
		while(!$obj->EOF())
		{

			$return .= '<div style="clear:both"></div>';

			if($ln!=$obj->db_data["MARCAX"].'-'.$obj->db_data["FAMILIAX"])
			{


				// if($tipo == 1) $return .= ' - <a style="color:#1d7fd3;text-decoration:underline;font-size:12px" class="fancybox3" href="avancada.php?ID_PAI='.$_GET['ID_PAI'].'&Desconto='.$_GET['Desconto'].'&campo=&tipoorder=DESC&pesq_id=&pesq_ref=&pesq_desig=&sel_marca='.$obj->db_data["MARCAX"].'&m_familia='.$obj->db_data["FAMILIAX"].'">pesquisar</a>';
				// else $return .= '<b>'.getbrandsx($obj->db_data["MARCAX"]).', '. rtrim(show_breadcrums_recursivo_on($obj->db_data["FAMILIAX"]),'-').'</b> - <a style="color:#1d7fd3;text-decoration:underline;font-size:12px" href="avancada.php?ID_PAI='.$_GET['ID_PAI'].'&Desconto='.$_GET['Desconto'].'&campo=&tipoorder=DESC&pesq_id=&pesq_ref=&pesq_desig=&sel_marca='.$obj->db_data["MARCAX"].'&m_familia='.$obj->db_data["FAMILIAX"].'">pesquisar</a>';
				// $Desconto > 0 &&
				$return .= '<tr><td style="font-size:10px">'.getbrandsx($obj->db_data["MARCAX"]).', '. rtrim(show_breadcrums_recursivo_on($obj->db_data["FAMILIAX"]),' - ').'</td>';
				$return .= '<td align="center">'.$obj->db_data["TOTAL"].'</td>';
				if($tipo == 1 && $ID_PAI > 0) $return .= '<td  align="center"><a style="color:#1d7fd3;text-decoration:underline;font-size:12px" class="fancybox3" href="avancada.php?ID_PAI='.$ID_PAI.'&Desconto='.$Desconto.'&campo=&tipoorder=DESC&pesq_id=&pesq_ref=&pesq_desig=&sel_marca='.$obj->db_data["MARCAX"].'&m_familia='.$obj->db_data["FAMILIAX"].'&promo=1">Visualisar</a></td>';
				else $return .= '<td  align="center"><a style="color:#1d7fd3;text-decoration:underline;font-size:12px" href="avancada.php?ID_PAI='.$_GET['ID_PAI'].'&Desconto='.$_GET['Desconto'].'&campo=&tipoorder=DESC&pesq_id=&pesq_ref=&pesq_desig=&sel_marca='.$obj->db_data["MARCAX"].'&m_familia='.$obj->db_data["FAMILIAX"].'&promo=1">Visualisar</a></td>';

				$return .= '</tr>';

				$ln = $obj->db_data["MARCAX"].'-'.$obj->db_data["FAMILIAX"];
			}


			$obj->db_next();
		}
		$return .= '</table>';
    }
    $obj->db_close();


	return $return;

}
function gera_lista_produtos($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.ID, pfl.Cabecalho
			FROM products pf
			INNER JOIN sales_promotions_products spp ON spp.PROD_ID = pf.ID
			INNER JOIN products_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				spp.MARCA=0
				AND
				spp.FAMILIA=0
				AND
				spp.promo_id='".$id_org."'
				AND
				spp.DESCONTO='".$line."'
			GROUP BY
				pf.ID
			ORDER BY
				 pfl.Cabecalho ASC
			";

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '
					<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'">
					<div style="float:left;width:13px"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"
					';

		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' /></div>';

		$line2 = str_replace('.','_',$line);

		$return .= '
		<div style="float:left;width:190px">'.htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'<div class="clearboth"></div></div>
		<div style="float:left"><a href="#" onclick="del_prod(\''.$line2.'\',\''.$line.'\',\''.$id_org.'\',\''.$obj->db_data["ID"].'\');return false;"><img src="img/delete_small.png" border="0" /></a></div><div class="clearboth"></div></label><div class="clearboth"></div>';
// echo $obj->db_data["Cabecalho"].'<br />';

	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;

}
function gera_lista_produtos2($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.PROD_ID as ID, pf.DESIGNACAO as Cabecalho
			FROM sales_promotions_offers pf
			WHERE
				pf.promo_id='".$id_org."'
			ORDER BY
				 pf.DESIGNACAO ASC
			";
			// echo $sql.'<br />';

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '
					<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="offer_fam_'.$obj->db_data["ID"].'_'.$line.'">
					<div style="float:left;width:13px"><input type="checkbox" id="offer_fam_'.$obj->db_data["ID"].'_'.$line.'" name="offer_fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"
					';
		$return .= ' /></div>';

		$line2 = str_replace('.','_',$line);

		$return .= '
		<div style="float:left;width:400px">'.htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'<div class="clearboth"></div></div>
		<div style="float:left"><a href="#" onclick="del_prod(\''.$line2.'\',\''.$line.'\',\''.$id_org.'\',\''.$obj->db_data["ID"].'\',\''.$line.'\');return false;"><img src="img/delete_small.png" border="0" /></a></div><div class="clearboth"></div></label><div class="clearboth"></div>';

		// echo $obj->db_data["Cabecalho"].'<br />';
	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;

}

function gera_lista_marcas($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.ID, pfl.Nome as Cabecalho
			FROM brands pf
			INNER JOIN sales_promotions_products spp ON spp.MARCA = pf.ID
			INNER JOIN brands_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				spp.MARCA > 0
				AND
				spp.FAMILIA > 0
				AND
				spp.promo_id='".$id_org."'
				AND
				spp.DESCONTO='".$line."'
			GROUP BY
				spp.MARCA
			ORDER BY
				 pfl.Nome ASC
			";
		// echo $sql.'<br />';

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{

		// if($cor=='#ffe2b0') $cor = '#fcefd7';
		// else $cor='#ffe2b0';

		$return .= '<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px;" for="fam_'.$obj->db_data["ID"].'_'.$line.'"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';

		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' />';


		$return .= htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'</label>';


	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;
}


function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

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
				if(substr_count($campos,'_EXT'))
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
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_EXT'))
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
		$sql .= 'WHERE '.TBID.'='.$_POST[TBID];
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">';
	// echo $sql.'<br />';
	// print_r($_POST);
	// echo '</pre><br />';

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}
function submeteProdutos($id_acao)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RENOCAOES'];

	// echo '<pre style="color:#FFF">';
	$query = '';

	foreach($_POST['RENOVACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];

		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRENOVACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRENOVACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRENOVACAOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRENOVACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRENOVACAOID)
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRENOVACAOID.'='.$field_id;
		}
		$res = Runquery($db_link, $DB_name, $sql);
	}
	// echo '</pre>';
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

		copy($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return $nome_ficheiro;
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$task_form;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?php

				echo '<input class="btn btn-primary" type="button" name="Submit" value="'.$task_button.'" onclick="submitform(\'pagina_trata.php?'.TBID.'='.$GLOBALS['promo_id'].'&task_form='.$GLOBALS['task_form'].'&dp='.date('YmdHis').'\')">';

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

	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		foreach($BOTOES_L as $key => $value)
		{

			if(substr_count($value, 'exportar'))
			{
			?>
				<tr>
				<td class="preto" align="center">
				<table width="200px" border=0 cellspacing=0 cellpadding=0>
				<tr>
				<td height="35px" width="10">&nbsp;</td>
				<td height="35px"><input type="text" style="width:60px !important;padding:0 !important" name="eDATA_INICIO" id="eDATA_INICIO" class="box dropdate" value="<?php echo date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))))?>" /></td>
				<td height="35px"><input type="text" style="width:60px !important;padding:0 !important" name="eDATA_FIM" id="eDATA_FIM" class="box dropdate" value="<?php echo date('Y-m-d')?>" /></td>
				</tr>
				</table>
				</td>
				</tr>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" value="<?php echo $key?>" onclick="window.open('<?php echo $value.'&p1='?>'+$('#eDATA_INICIO').val()+'&p2='+$('#eDATA_FIM').val(),'<?php if(substr_count($value, 'exportar')){?>_blank<?php }else{?>_self<?php }?>')" /></td>
				</tr>
				<tr>
				<td class="preto" align="center">&nbsp;</td>
				</tr>
			<?php
			}
			else
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','<?php if(substr_count($value, 'exportar')){?>_blank<?php }else{?>_self<?php }?>')" /></td>
				</tr>
				<tr>
				<td class="preto" align="center">&nbsp;</td>
				</tr>
		<?php
			}
		}
	}
	?>
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
<td  height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if(isset($_POST['ACCOES']) &&$_POST['ACCOES'] > 0)
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$var = "field_".$str_id;
					$dv = $$var;
					if($_POST['ACCOES']==1) 	$sql = 'UPDATE '.TBMODULO.' SET removido=1 WHERE '.TBID.'='.$str_id; // REMOVER REGISTO
					elseif($_POST['ACCOES']==2) $sql = 'UPDATE '.TBMODULO.' SET ATIVO=1 WHERE '.TBID.'='.$str_id; // ATIVAR REGISTO
					elseif($_POST['ACCOES']==3) $sql = 'UPDATE '.TBMODULO.' SET ATIVO=0 WHERE '.TBID.'='.$str_id; // DESATIVAR REGISTO

					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
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
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['TIPOCAMPANHA']) && $_GET['TIPOCAMPANHA']!='') $pesq .= ' AND TIPOCAMPANHA="'.$_GET['TIPOCAMPANHA'].'"';
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND DESIGNACAO="'.$_GET['DESIGNACAO'].'"';
		if(isset($_GET['DATA_INICIO']) && $_GET['DATA_INICIO']!='') $pesq .= ' AND DATA_INICIO="'.$_GET['DATA_INICIO'].'"';
		if(isset($_GET['DATA_FIM']) && $_GET['DATA_FIM']!='') $pesq .= ' AND DATA_FIM="'.$_GET['DATA_FIM'].'"';
	}
	$sql='SELECT * FROM '.TBMODULO.' WHERE removido=0 '.$pesq.' ORDER BY '.TBID.' ASC';
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
		<table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
		<tr>
			<td width="5%" height="25" align="center">&nbsp;</td>
			<td width="100" valign="middle"><b>Tipo Campanha</b></td>
			<td valign="middle" align="left"><b>Designação</b></td>
			<td width="100" valign="middle" align="left"><b>Data Inicio</b></td>
			<td width="100" valign="middle" align="left"><b>Data Fim</b></td>
			<td width="10" valign="middle">&nbsp;</td>
			<td width="10" valign="middle">&nbsp;</td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td width="5%" height="35" align="center"><label class="label_check" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>
			<td width="120" valign="middle"><select style="width:80px;font-size:11px;padding-top:4px;" name="TIPOCAMPANHA" id="TIPOCAMPANHA" class="form-control"><option value="" <?php if(!isset($_GET['TIPOCAMPANHA']) && !$_GET['TIPOCAMPANHA']) echo 'selected="selected"';?>>TODOS</option><option value="1" <?php if(isset($_GET['TIPOCAMPANHA']) && $_GET['TIPOCAMPANHA']==1) echo 'selected="selected"';?>>Normal</option><option value="2" <?php if(isset($_GET['TIPOCAMPANHA']) && $_GET['TIPOCAMPANHA']==2) echo 'selected="selected"';?>>Desconto dos 3</option></select></td>
			<td valign="middle" align="left"><input type="text" style="width:270px" name="DESIGNACAO" id="DESIGNACAO" class="form-control" value="<?php if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>
			<td valign="middle" width="152" align="left"><input type="text" style="width:80px !important" name="DATA_INICIO" id="DATA_INICIO" class="box dropdate" value="<?php if(isset($_GET['DATA_INICIO'])) echo $_GET['DATA_INICIO']?>" /></td>
			<td valign="middle" width="152" align="left"><input type="text" style="width:80px !important" name="DATA_FIM" id="DATA_FIM" class="box dropdate" value="<?php if(isset($_GET['DATA_FIM'])) echo $_GET['DATA_FIM']?>" /></td>
			<td valign="middle" align="left" colspan="2"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa(); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$nome = $rs->db_data["DESIGNACAO"];
			$TIPO = $rs->db_data["TIPOCAMPANHA"];
			if($TIPO==1) $TIPO = 'NORMAL';
			elseif($TIPO==2) $TIPO = 'DESCONTO DOS 3';
			elseif($TIPO==6) $TIPO = '2=3';
			elseif($TIPO==7) $TIPO = 'Desconto de 2';
			else $TIPO = 'OFERTAS';

			$DATA_INICIO = $rs->db_data["DATA_INICIO"];
			$DATA_FIM = $rs->db_data["DATA_FIM"];
			$ATIVO = $rs->db_data["ATIVO"];


			if($ATIVO==1) // CAMPANHA ATIVA
			{
				$d_ini2 = strtotime($DATA_INICIO);
				$d_fim2 = strtotime($DATA_FIM);

				if($d_ini2<=strtotime("now") && $d_fim2>=strtotime("now"))  // CAMPANHA ATIVA E DENTRO DO PERIODO DEFINIDO
				{
					if($cor=='#c8ecfa')	{$cor="#def2fa";}
					else				{$cor="#c8ecfa";}
				}
				else  // CAMPANHA INATIVO
				{
					if($cor=='#c0c0c0')	{$cor="#eef0f0";}
					else				{$cor="#c0c0c0";}
				}
			}
			else  // CAMPANHA INATIVO
			{
				if($cor=='#c0c0c0')	{$cor="#eef0f0";}
				else				{$cor="#c0c0c0";}
			}

			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td height="30" align="center"><label class="label_check" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" style="text-transform:uppercase"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $TIPO;?></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($nome){echo htmlentities_b(stripslashes($nome));} else echo "[Edit]";?></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $DATA_INICIO;?></a></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $DATA_FIM;?></a></td>
			<td valign="middle">&nbsp;</td>
			<td valign="middle">&nbsp;</td>
			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{
			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("list.php?ID='.$_GET['ID'].'&m_pesquisa='.urlencode($m_pesquisa).'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("list.php?ID='.$_GET['ID'].'&m_pesquisa='.urlencode($m_pesquisa).'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("list.php?ID='.$_GET['ID'].'&&m_pesquisa='.urlencode($m_pesquisa).'&pagina='.$n_pags.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}
	}
	else
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
function show_produtosp($t)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj; $obj2=new DataObj; $obj3=new DataObj;

	if(isset($_GET['f_produto']) && ltrim(rtrim($_GET['f_produto']))!='')
	{

		$sql = '
				SELECT
					p1l.Cabecalho,p1l.ID_PAI as ID
				FROM
					products_lang p1l
				WHERE
					p1l.Cabecalho Like "%'.ltrim(rtrim($_GET['f_produto'])).'%"
					AND
					p1l.Lang="'.$GLOBALS['admin_lang'].'"
				ORDER BY
					p1l.Cabecalho ASC
				LIMIT 50
			';
		$obj->db_open($db_link, $DB_name, $sql);
		$total2 = 0;$return = '';
		if(!$obj->EOF())
		{
			$return = '<table width="100%">';
			while(!$obj->EOF())
			{

				$id_prod=$obj->db_data["ID"];
				$obj2->db_open($db_link, $DB_name,'SELECT ID FROM products WHERE Aproved=1 AND ID = '.$id_prod);

				if(!$obj2->EOF())
				{
					if($t==3)
					{
						$return .=  "
						<tr>
						<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"].";".urlencode($obj->db_data["Cabecalho"])."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
						<td style='border-bottom:solid 1px #000'>" . $obj->db_data["Cabecalho"]."</div></td>
						</tr>
						";
					}
					elseif($t==4)
					{
						$return .=  "
						<tr>
						<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"].";".urlencode($obj->db_data["Cabecalho"])."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."'name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
						<td style='border-bottom:solid 1px #000'>" . $obj->db_data["Cabecalho"]."</div></td>
						</tr>
						";
					}
					else
					{
						$return .=  "
						<tr>
						<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"]."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
						<td style='border-bottom:solid 1px #000'>" . $obj->db_data["Cabecalho"]."</div></td>
						</tr>
						";
					}
				}
				$obj2->db_close();


				$obj->db_next();
			}

			$return .= '</table>';
		}
		$obj->db_close();

		return array('list_prods' => $return, 'total_prod' => $total, 'total_prd_prom' => $total2);

		db_closedb_conection($db_link);
	}
}
?>