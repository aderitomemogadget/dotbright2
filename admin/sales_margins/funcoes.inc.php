<?php
function gera_lista_familias($id_org=0,$space=1, $line='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$sql = "
			SELECT pf.ID, pfl.Cabecalho
			FROM products_families pf
			INNER JOIN sales_margins_families smf ON smf.FAMILIA = pf.ID
			INNER JOIN products_families_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				smf.margin_id='".$id_org."'
			GROUP BY
				pf.ID
			ORDER BY
				 pfl.Cabecalho ASC
			";

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' />';
		$return .= htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'</label>';
        $obj->db_next();
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
			INNER JOIN sales_margins_products smf ON smf.PRODUCT = pf.ID
			INNER JOIN products_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				smf.margin_id='".$id_org."'
			GROUP BY
				pf.ID
			ORDER BY
				 pfl.Cabecalho ASC
			";

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' />';
		$return .= htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'</label>';
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
			INNER JOIN sales_margins_brands smf ON smf.BRAND = pf.ID
			INNER JOIN brands_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				smf.margin_id='".$id_org."'
			GROUP BY
				pf.ID
			ORDER BY
				 pfl.Nome ASC
			";

	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="fam_'.$obj->db_data["ID"].'_'.$line.'"><input type="checkbox" id="fam_'.$obj->db_data["ID"].'_'.$line.'" name="fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';
		if($id_org==$obj->db_data["ID"]) $return .= ' checked="checked" ';
		$return .= ' />';
		$return .= htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'</label>';
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


		echo TBMODULO;
		echo "<br>";
		echo TBID;
		echo "<br>";
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

	$modulo_id 		= (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$TIPOMARGEM 	= intval($_POST['TIPOMARGEM']);
	$VALOR 			= floatval($_POST['VALOR']);
	//$TIPORELACAO 	= floatval($_POST['TIPORELACAO']);


	// echo '<pre style="color:#FFF">';
	// print_r($_POST);
	$sql = 'SELECT po.obj_pvp,po.obj_promo,sp.PROD_ID,p.ID_Marca,pfo.ID_ORG
			FROM sales_margins_products sp
				INNER JOIN products_objs po ON po.obj_type_id=sp.PROD_ID
				INNER JOIN products p ON p.ID=sp.PROD_ID
				INNER JOIN products_families_obj pfo ON pfo.ID_PROD=sp.PROD_ID
			WHERE sp.margin_id="'.$modulo_id.'"
			GROUP BY pfo.ID_PROD,po.obj_type_id';
	$rs->db_open($db_link, $DB_name, $sql);
	// echo $sql."<br>";
	while(!$rs->EOF())
	{
		$pvp 		=  floatval($rs->db_data['obj_pvp']);
		$promo 		=  floatval($rs->db_data['obj_promo']);
		$PROD_ID 	=  intval($rs->db_data['PROD_ID']);
		$ID_Marca 	=  $rs->db_data['ID_Marca'];
		$ID_ORG 	=  intval($rs->db_data['ID_ORG']);

		$precomargem 		= 0;
		$precomargempromo 	= 0;
		if($VALOR!=0)
		{
			if($TIPOMARGEM==1) //VALOR
			{
				if($pvp>0)
				{
					$precomargem = round($pvp+$VALOR,2);
					// echo "PRECO: ".$pvp."<br>";
					// echo "VALOR MARGEM: ".$VALOR."€<br>";
					// echo "NOVO PRECO: ".($precomargem)."<br>";
				}

				if($promo>0)
				{
					$precomargempromo = round($promo+$VALOR,2);
					// echo "PRECO PROMO: ".$promo."<br>";
					// echo "VALOR MARGEM: ".$VALOR."€<br>";
					// echo "NOVO PRECO PROMO: ".($precomargempromo)."<br>";
				}
			}
			else if($TIPOMARGEM==2) //PERCENTAGEM
			{
				if($pvp>0)
				{
					$precomargem = round($pvp+($pvp*($VALOR/100)),2);
					// echo "PRECO: ".$pvp."<br>";
					// echo "PERCENTAGEM MARGEM: ".$VALOR."%<br>";
					// echo "NOVO PRECO: ".($precomargem)."<br>";
				}
				if($promo>0)
				{
					$precomargempromo = round($promo+($promo*($VALOR/100)),2);
					// echo "PRECO PROMO: ".$promo."<br>";
					// echo "PERCENTAGEM MARGEM: ".$VALOR."%<br>";
					// echo "NOVO PRECO PROMO: ".($precomargempromo)."<br>";
				}
			}
		}
		$sql='UPDATE products SET PrecoMargem="'.$precomargem.'",PrecoPromoMargem="'.$precomargempromo.'" WHERE ID="'.$PROD_ID.'"';
		// echo $sql."<br>";
		$res = Runquery($db_link, $DB_name, $sql);

		$cache_filename = $site_shell_url.'files/cache/prod_'.$PROD_ID.'_'.$admin_lang.'.txt';
		if(file_exists($cache_filename)) unlink($cache_filename);

		$exe_file = $GLOBALS['site_shell_url'] . "admin/products/gera_filtros.php ".$ID_ORG." ".$ID_Marca;
		exec('php '.$exe_file.' > /dev/null &');

		$rs->db_next();
	}
	$rs->db_close();


	// echo '</pre><br />';
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
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$task_form;
	?>

    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td align="center"><?php

				echo '<input class="btn btn-primary" type="button" name="Submit" value="'.$task_button.'" onclick="submitform(\'pagina_trata.php?'.TBID.'='.$GLOBALS['margin_id'].'&task_form='.$GLOBALS['task_form'].'&dp='.date('YmdHis').'\')">';

			?>
            <input class="btn btn-info" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>

<?php
}
function show_fast_tools()
{
	global $ID, $admin_id, $BOTOES_L;
	?>
	<table class="p1" width="200px" cellspacing="0" cellpadding="0" border="0">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		foreach($BOTOES_L as $key => $value)
		{
			?>
				<tr>
				<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','<?php if(substr_count($value, 'exportar')){?>_blank<?php }else{?>_self<?php }?>')" /></td>
				</tr>
				<tr>
				<td class="preto" align="center">&nbsp;</td>
				</tr>
		<?php
		}
	}
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px"><?php
}
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
    </div><?php
}

function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="150" height="35" align="right" style="font-weight:bold">Para os Selecionados:&nbsp;</td>
	<td height="35" width="275"><select name="ACCOES" id="ACCOES" class="box">
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
					// echo $sql;
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
	// ?p=1&NUMERO=15154/2009&TIPO=figurativa&DESIGNACAO=IMOINVESTE&DATA_PEDIDO=2009-05-07
	$pesq = '';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['DESIGNACAO']) && $_GET['DESIGNACAO']!='') $pesq .= ' AND DESIGNACAO="'.$_GET['DESIGNACAO'].'"';
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
			<td valign="middle" align="left"><b>Designação</b></td>
			<td width="10" valign="middle">&nbsp;</td>
			<td width="10" valign="middle">&nbsp;</td>
		</tr>
		<tr bgcolor="#FFFFFF">
		<td width="5%" height="35" align="center"><label class="label_check" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>
		<td valign="middle" align="left"><input type="text" style="width:270px" name="DESIGNACAO" id="DESIGNACAO" class="box" value="<?php if(isset($_GET['DESIGNACAO'])) echo $_GET['DESIGNACAO']?>" /></td>
		<td valign="middle" align="left" colspan="2"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa(); return false;" style="cursor:pointer" /></td>
		</tr>
		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$nome = $rs->db_data["DESIGNACAO"];
			$ATIVO = $rs->db_data["ATIVO"];

			if($ATIVO==1) // CAMPANHA ATIVA
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
			<td height="30" align="center"><label class="label_check" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><? if($nome){echo htmlentities_b(stripslashes($nome));} else echo "[Edit]";?></a></td>
			<td valign="middle">&nbsp;</td>
			<?php
			if($ATIVO==1){ // CAMPANHA ATIVA
					?>
					<td valign="middle" width="35"><img title="Página Visível" src="ok.png" border="0" align="absmiddle"></td>
					<?php
			}
			else{
					?>
					<td valign="middle">&nbsp;</td>
					<?php
			}
			?>
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
				if($t==3)
				{
					$return .=  "
					<tr>
					<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"].";".$obj->db_data["Cabecalho"]."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
					<td style='border-bottom:solid 1px #000'>" . $obj->db_data["Cabecalho"]."</div></td>
					</tr>
					";
				}
				elseif($t==4)
				{
					$return .=  "
					<tr>
					<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"].";".$obj->db_data["Cabecalho"]."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."'name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
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