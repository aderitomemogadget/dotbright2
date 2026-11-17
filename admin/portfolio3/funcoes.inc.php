<?php
function getFamTitle($obj_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql = "SELECT * FROM portfolio3_families_lang WHERE ID_PAI='".$obj_id."' AND Lang='".$admin_lang."'";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) return $obj_id.' - '.$rs2->db_data['Cabecalho'];
	$rs2->db_close();
	return;
}
function portfolio3_objs($obj_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$sql = "SELECT * FROM portfolio3_objs inner join portfolio3_objs_lang on portfolio3_objs_lang.ID_PAI = portfolio3_objs.obj_id WHERE obj_type='portfolio3' AND obj_type_id='".$obj_id."' and Lang='".$GLOBALS['admin_lang']."' ORDER BY portfolio3_objs_lang.Cabecalho ASC";
	// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$obj_id_p[]=$rs->db_data['obj_id'];
		$obj_desc[]=$rs->db_data['Cabecalho'];
		$obj_preco[]=$rs->db_data['obj_pvp'];
		$obj_stock[]=$rs->db_data['obj_stock'];
		$obj_promo[]=$rs->db_data['obj_promo'];
		$obj_promo_ativo[]=$rs->db_data['obj_promo_ativo'];
		$obj_ativo[]=$rs->db_data['obj_ativo'];
		$obj_val[]=$rs->db_data['obj_val'];
		$rs->db_next();
	}
	$rs->db_close();
		$return  = '<table id="IDTabelaPrecos" style="margin:0px;" border="0">';
		$return .= '<tr>
		<td align="center" style="width:70px" style="font-size:11px;text-align:center"><b>Predefinido</b></td>
		<td align="center" style="width:50px" style="font-size:11px;text-align:center"><b>Linha</b></td>
		<td align="center" width="100px" style="font-size:11px;text-align:center"><b>Preço s/ IVA</b></td>
		<td align="center" width="80px" style="font-size:11px;text-align:center"><b>Stock</b></td>
		<td align="center" width="150px" style="font-size:11px;text-align:center"><b>Preço Promoção s/ IVA</b></td>
		<td align="center" width="80px" style="font-size:11px;text-align:center"><b>Em Promoção</b></td>
		<td align="center" width="80px" style="font-size:11px;text-align:center"><b>Visivel</b></td>
		<td align="center" width="20px" style="font-size:11px;text-align:center"><b>&nbsp</b></td>
		</tr>
		';
		$guarda_id=0;	$count=0;
		/*NELSON - Alterado para que seje dinâmico */
		for($a=0;$a<count($obj_desc);$a++)
		{
			// echo $obj_val[$a].'=='.$obj_id_p[$a].'<br />';
			$return .= '<tr id="rem_'.$a.'" class="precos_line">';
			$return .= '<td style="width:70px" align="center"><input type="radio" value="'.($a+1).'" name="obj_val[]"';
			if($obj_val[$a]==1) $return .= ' checked="checked"';
			$return .= '/></td>';
			$return .= '<td style="width:50px" align="center"><input value="'.htmlentities_memo(stripslashes($obj_desc[$a])).'" type="text" name="obj_desc[]" id="obj_desc'.$a.'" style="WIDTH:30px; HEIGHT:23px;text-align:center" /></td>';
			$return .= '<td style="width:100px" align="center"><input type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px; HEIGHT:23px;" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td style="width:80px" align="center"><input type="text" name="obj_stock[]" onkeyup="mudaStock(this.value)" id="obj_stock'.$a.'" style="WIDTH:50px; HEIGHT:23px;" maxlength="255" value="'.$obj_stock[$a].'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td style="width:150px" align="center"><input type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; HEIGHT:23px;" maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(this)" /></td>';
			$return .= '<td style="width:80px" align="center"><input value="'.$obj_promo_ativo[$a].'" type="hidden" name="obj_p_ativo[]" id="obj_p_ativo'.$a.'"><input onclick="document.form.obj_p_ativo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
			if($obj_promo_ativo[$a]) $return .= ' checked="checked" ';
			$return .= '></td>';
			$return .= '<td style="width:80px" align="center"><input value="'.$obj_ativo[$a].'" type="hidden" name="obj_ativo[]" id="obj_ativo'.$a.'"><input onclick="document.form.obj_ativo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
			if($obj_ativo[$a]) $return .= ' checked="checked" ';
			$return .= '></td><td width="20">';
			$return .= '<a href="#" onclick="RemoveLinha('.$a.');return false;"><img src="del2.png" border="0" /></a>';
			$return .= '</td></tr>';
			$count++;
		}
			/*NELSON - Se não tiver nada faz uma linha vazia */
			if(count($obj_desc) == 0)
			{
				$return .= '<tr id="rem_'.$a.'" class="precos_line">';
				$return .= '<td style="width:70px" align="center"><input type="radio" value="'.($a+1).'" name="obj_val[]"';
				if($obj_val[$a]==1) $return .= ' checked="checked"';
				$return .= '/></td>';
				$return .= '<td style="width:50px" align="center"><input value="'.htmlentities_memo(stripslashes($obj_desc[$a])).'" type="text" name="obj_desc[]" id="obj_desc'.$a.'" style="WIDTH:30px; HEIGHT:23px;text-align:center" /></td>';
				$return .= '<td style="width:100px" align="center"><input type="text" name="obj_preco[]" id="obj_preco'.$a.'"  style="WIDTH:80px; HEIGHT:23px;" maxlength="255" value="'.$obj_preco[$a].'" onkeypress="handleKeyPress(this)" /></td>';
				$return .= '<td style="width:80px" align="center"><input type="text" name="obj_stock[]" id="obj_stock'.$a.'" style="WIDTH:50px; HEIGHT:23px;" maxlength="255" value="'.$obj_stock[$a].'" onkeypress="handleKeyPress(this)" /></td>';
				$return .= '<td style="width:150px" align="center"><input type="text" name="obj_promo[]" id="obj_promo'.$a.'" style="WIDTH:80px; HEIGHT:23px;" maxlength="255" value="'.$obj_promo[$a].'" onkeypress="handleKeyPress(this)" /></td>';
				$return .= '<td style="width:80px" align="center"><input value="'.$obj_promo_ativo[$a].'" type="hidden" name="obj_p_ativo[]" id="obj_p_ativo'.$a.'"><input onclick="document.form.obj_p_ativo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
				if($obj_promo_ativo[$a]) $return .= ' checked="checked" ';
				$return .= '></td>';
				$return .= '<td style="width:80px" align="center"><input value="'.$obj_ativo[$a].'" type="hidden" name="obj_ativo[]" id="obj_ativo'.$a.'"><input onclick="document.form.obj_ativo'.$a.'.value=(this.checked==1)?1:0" type="checkbox" value="1"';
				if($obj_ativo[$a]) $return .= ' checked="checked" ';
				$return .= '></td><td width="20">';
				$return .= '<a href="#" onclick="RemoveLinha('.$a.');return false;"><img src="del2.png" border="0" /></a>';
				$return .= '</td></tr>';
			}
			$return .= '</table><br /><script>
			function AdicionarLinha()
			{
				var n = Number($(".precos_line").length + 1);
				var row = $("#IDTabelaPrecos tr:last").clone();
				row.find(\'input:text\').each( function()
				{
					$(this).val( \'\' );
				});
				row.find(\'input:checkbox\').each( function()
				{
					$(this).removeAttr( \'checked\' );
				});
				row.appendTo("#IDTabelaPrecos");
				row.attr("id","rem_"+n);
				$(row).find("a").attr("onclick","RemoveLinha("+n+");return false;");
				$(row).find("input:radio").attr("value",n);
			}
			function RemoveLinha(i)
			{
				var cont = $("#rem_"+i).remove();
			}

			</script><input type="button" value=" + Adicionar Nova Linha" onclick="AdicionarLinha()" style="cursor:pointer"><br /><br />';
	return $return;
}
/*NELSON - Função alterada para que seja dinamica a inserção de novos "Modelos" para o mesmo produto (função antiga "portfolio3_objs_save_old")*/
function portfolio3_objs_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj_preco = $_POST['obj_preco'];
	$obj_stock = $_POST['obj_stock'];
	$obj_promo = $_POST['obj_promo'];
	$obj_p_ativo = $_POST['obj_p_ativo'];
	$obj_ativo = $_POST['obj_ativo'];
	$obj_val = $_POST['obj_val'];
	$obj_desc = $_POST['obj_desc'];
	// print_r($obj_val);
	$rs=new DataObj;$rs2=new DataObj;
	$sql = "SELECT * FROM portfolio3_objs WHERE obj_type='portfolio3' AND obj_type_id='".$ID_PROD."'";
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF()){
		$sql_delete = "DELETE FROM portfolio3_objs_lang WHERE ID_PAI='".$rs->db_data['obj_id']."'";
		$res = Runquery($db_link,$DB_name,$sql_delete);
		$rs->db_next();
	}
	$rs->db_close();
	$sql_delete = "DELETE FROM portfolio3_objs WHERE obj_type_id='".$ID_PROD."'";
	$res = Runquery($db_link,$DB_name,$sql_delete);
	for($a=0; $a<count($obj_preco); $a++)
	{
		$ln = ($obj_val[0]==($a+1) ? 1 : 0);
		$sql =
			"
			INSERT INTO
				portfolio3_objs
				(
					obj_type,
					obj_type_id,
					obj_pvp,
					obj_stock,
					obj_promo,
					obj_promo_ativo,
					obj_ativo,
					obj_val
				)
				VALUES
				(
					'portfolio3',
					'".$ID_PROD."',
					'".$obj_preco[$a]."',
					'".$obj_stock[$a]."',
					'".$obj_promo[$a]."',
					'".$obj_p_ativo[$a]."',
					'".$obj_ativo[$a]."',
					'".$ln."'
				)
			";
		$res = Runquery($db_link,$DB_name,$sql);
		$obj_id=db_get_last_insert_id($db_link);
		if($obj_id > 0)
		{
			$sql =
				"
				INSERT INTO
					portfolio3_objs_lang
					(
						Cabecalho,
						ID_PAI,
						Lang
					)
					VALUES
					(
						'".$obj_desc[$a]."',
						'".$obj_id."',
						'".$GLOBALS['admin_lang']."'
					)
				";
			$res = Runquery($db_link,$DB_name,$sql);
		}
	}
}

function portfolio3_objs_save_old($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$c = count($_POST); $keys = array_keys($_POST); $url = ""; $i=0;
	while($i < $c)
	{
		if(substr_count($url.$keys[$i],"obj_desc")) $obj_desc[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_preco")) $obj_preco[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_stock")) $obj_stock[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_promo")) $obj_promo[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_p_ativo")) $obj_p_ativo[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_val")) $obj_val[] = $_POST[$keys[$i]];
		if(substr_count($url.$keys[$i],"obj_ativo"))
		{
			if($_POST[$keys[$i]]==-1)$obj_ativo[]=0;
			else $obj_ativo[] = $_POST[$keys[$i]];
		}
		$i++;
	}

	for($a=0; $a<count($obj_preco); $a++)
	{

		$rs=new DataObj;$rs2=new DataObj;
		$sql = "SELECT * FROM portfolio3_objs WHERE obj_type='portfolio3' AND obj_type_id='".$ID_PROD."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()) // update
		{
			$obj_id=$rs->db_data['obj_id'];
			$sql =
				"UPDATE portfolio3_objs
				SET
					obj_pvp='".$obj_preco[$a]."',
					obj_stock='".$obj_stock[$a]."',
					obj_promo='".$obj_promo[$a]."',
					obj_promo_ativo='".$obj_p_ativo[$a]."',
					obj_ativo='".$obj_ativo[$a]."'
				WHERE
					obj_val='".$obj_val[$a]."'
					AND
					obj_type='portfolio3'
					AND
					obj_type_id='".$ID_PROD."'
					";
					echo $sql."<br>";
			$res = Runquery($db_link,$DB_name,$sql);
		}
		else // insert
		{
			$sql =
				"
				INSERT INTO
					portfolio3_objs
					(
						obj_type,
						obj_type_id,
						obj_pvp,
						obj_stock,
						obj_promo,
						obj_promo_ativo,
						obj_ativo,
						obj_val
					)
					VALUES
					(
						'portfolio3',
						'".$ID_PROD."',
						'".$obj_preco[$a]."',
						'".$obj_stock[$a]."',
						'".$obj_promo[$a]."',
						'".$obj_p_ativo[$a]."',
						'".$obj_ativo[$a]."',
						'".$obj_val[$a]."'
					)
				";
				//echo $sql."<br>";
			$res = Runquery($db_link,$DB_name,$sql);
			$obj_id=db_get_last_insert_id($db_link);
		}
		$rs->db_close();
		if($obj_id > 0)
		{
			$rs = new DataObj; $rs2 = new DataObj;
			$sql = "SELECT * FROM portfolio3_objs_lang WHERE ID_PAI='".$obj_id ."' AND Lang='".$GLOBALS['admin_lang']."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF()) // update
			{
				$obj_id2=$rs->db_data['ID'];
				$sql =
					"
					UPDATE portfolio3_objs_lang
					SET
						Cabecalho='".$obj_desc[$a]."'
					WHERE
						ID='".$obj_id2."'
						AND
						ID_PAI='".$obj_id."'
						AND
						Lang='".$GLOBALS['admin_lang']."'
					";
				$res = Runquery($db_link,$DB_name,$sql);
			}
			else // insert
			{
				$sql =
					"
					INSERT INTO
						portfolio3_objs_lang
						(
							Cabecalho,
							ID_PAI,
							Lang
						)
						VALUES
						(
							'".$obj_desc[$a]."',
							'".$obj_id."',
							'".$GLOBALS['admin_lang']."'
						)
					";
					//echo $sql."<br>";
				$res = Runquery($db_link,$DB_name,$sql);
			}
		}
	}
}

function getportfolio3objs($obj_id,$modulo='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj;
	$sql = "SELECT * FROM portfolio3_objs WHERE obj_type='".$modulo."' AND obj_type_id='".$obj_id."'";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) return $rs2;
	$rs2->db_close();
	return;
}
function show_product_tools()
{
	global $ID_ORG,$ID;
	echo '<script>
	function relacionados()
	{
		var x = (window.innerWidth / 2) - (600 / 2);
		var y = (window.innerHeight / 2) - (400 / 2);
		newwindow = window.open("portfolio3_related.php?id_produto='.$ID.'","janela_'.$ID.'","top="+y+",left="+x+",width=600,height=400,toolbar=0,status=0,scrollbars=1,location=0,menubar=0,directories=0,resizable=1","_self");
		if (window.focus) {newwindow.focus()}
		if (!newwindow.closed) {newwindow.focus()}
	}
	</script>
	';
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center">CROSS/UP SELLING</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Produtos relacionados" onclick="javascript:relacionados()"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PORTFOLIO</h4>
        </div>
    </div>
	<form action="portfolio3_families.php" method="post">
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
		<td align="right" class="branco"><? echo write_lang('key_pesquisar_por_nome','Pesquisar por Nome')?>: <input style="width:100px;" type="text" name="pesquisa" id="pesquisa" value="<?=$pesquisa?>">
				<? echo write_lang('key_or','ou')?> <? echo write_lang('key_por_ref','por referência')?>:
				<input style="width: 100px;" type="text" name="pesquisa2" id="pesquisa2" value="<?=$pesquisa2?>">
				<input type="submit" name="Pesquisar" value="<? echo write_lang('key_pesquisar','Pesquisar')?>" style="width:70px;"></td>
		<td width="5"></td>
	</tr>
	</table>
	</form>
<? }
function show_page_bottom_tools()
{
	global $edit;?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
				<? if($edit){?><option value="3"><? echo write_lang('key_atualizar_nomes','Atualizar Nomes')?></option><? }?>
			</select>
		</td>
		<td width="5px"></td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
		<td align="right"><input type="button" onclick="javascript:window.open('list.php?edit=1','_self')" value="<? echo write_lang('key_ativar_edicao_na_pagina','Activar Edição na Página')?>" class="btn btn-primary" style="width:150px">&nbsp;</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="AVANCADA" onclick="javascript:window.open('avancada.php?Dump=<?=uniqid("");?>','_self')" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_ver_em_forma_de_arvore','Ver em Forma de Árvore')?>" onclick="javascript:window.open('treecatalog.php?Dump=<?=uniqid("");?>','_self')" /></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_ver_em_lista','Ver em Lista')?>" onclick="javascript:window.open('list.php?Dump=<?=uniqid("");?>','_self')" /></td>
	</tr>
	<? //if(isset($_GET['ID'])){?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_nova','Criar Nova')?> <? if($ID) echo 'SUB-';?><? echo write_lang('key_categoria','Category')?>" onclick="javascript:window.open('portfolio3_families.php?ID=0&ID_ORG=<? echo $ID?>&Dump=<? echo uniqid("");?>','_self')"></td>
	</tr>
	<? //}?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_item','Criar Novo Item')?>" onclick="javascript:window.open('portfolio3.php?ID=0&ID_ORG=<?=$ID;?>&from_prods=<?=$from_prods?>&to_prods=<?=$to_prods?>&total_prods=<?=$total_prods?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Exportar Catálogo" onclick="javascript:window.open('exporta.php','_blank')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;
	?>
    <table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
    <?
    $rs=GetRecords_Recursos_1(0,$ID_ORG,0);
    $count=0;
    while(!$rs->EOF())
    {
		$count++;
		$codigo=$rs->db_data["ID"];
		$ordem=$rs->db_data["Ordem"];
		$desconto=number_format($rs->db_data["Desconto"],2);
		$Visivel=$rs->db_data["visivel"];
		$cabecalho=stripslashes($rs->db_data["Cabecalho"]);
		$cabecalho2=stripslashes($rs->db_data["Cabecalho2"]);
		$Protected=$rs->db_data["Protected"];
		if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}

		if($cabecalho2)$cabecalho=$cabecalho2;
		?>
	    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<? if($Protected==0){?><td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb"></td><? }?>

            <td width="29" align="center"><a class="preto" href="portfolio3_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><img src="../images/page.png" border=0 /></a></td>

            <td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
            <td><?
            if($edit){?><input style="width:100%" type="text" name="name_<?php echo $codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
			else {?><a class="preto" href="portfolio3_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=0&Nivel=0"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
            </td>
            <? if($desconto > 0){?> <td width="80" class="desconto<?php echo $n?>"><?php echo $desconto?> %</td> <? }else{?> <td width="30" class="desconto<?php echo $n?>">&nbsp;</td> <td width="4" align="right">&nbsp;</td><?php }?>
            <td align="center" width="30"><img title="<? if($Visivel) echo write_lang('key_pagina_visivel','Página Visivel'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
			<? if($Protected==0){?><td width="30" align="center"><a class="italico" href="portfolio3_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
			<td width="30" align="center"><a class="italico" href="portfolio3_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=0&Nivel=0&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td><? }?>
           <? if($Protected==0){?> <td class="ordem<?php echo $n?>"><input class="ordem" type=text name="chks_<?php echo $codigo?>" value="<?php echo $ordem?>"></td><? }?>
         </tr>
        </table>
        <?
        $rs->db_next();
    }
    $rs->db_close(1);
}
function show_content_product_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $edit;?>
	<table class="listagem" width="100%" border=0 cellspacing=0 cellpadding="2">
	<?
	$rs=GetRecords_Recursos_1_1(0,$ID_ORG,0);
	$count=0;
	while(!$rs->EOF())
	{
		$count++;
		$codigo=$rs->db_data["ID"];
		$ordem=$rs->db_data["Ordem"];
		$Visivel=$rs->db_data["Aproved"];
		$cabecalho=$rs->db_data["Cabecalho"];
		if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}

		$url_produt = 'portfolio3.php?ID='.$codigo;
		?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$codigo?>" id="chk_<?=$codigo?>" value="<?=$codigo?>" class="cb"></td>
		<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
		<td width="29" align="center"><a class="preto" href="<? echo $url_produt?>"><img src="../images/page.png" border=0 /></a></td>
		<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
		<td width="5"><img width="5" height="8" src="../images/blank.gif"></td>
		<td valign="middle" width="35"><img title="<? if($Visivel) echo write_lang('key_pagina_visivel','Página Visivel'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
		<td>
		<? if($edit){?><input style="width:100%" type="text" name="name_<?=$codigo?>" value="<? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?>" /><? }
		else {?><a class="preto" href="<? echo $url_produt?>"><? if($cabecalho) echo htmlentities_b(stripslashes($cabecalho)); else echo "[Edit]";?></a><? }?>
		</td>
		<td width="20" align="right"><a class="italico" href="<? echo $url_produt?>&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
		<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
		<td class="ordem<?=$n?>"><input class="ordem" type=text name="chk_<?=$codigo?>" value="<?=$ordem?>"></td>
		</tr>
		</table>
		<?
		$rs->db_next();
	}
	$rs->db_close(1);
}
function gera_lista_familias($id_org=0,$tabela1,$tabela1_lang)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	echo '<select name="m_familia">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
	while(!$obj->EOF())
	{
		if($ID!=$obj->db_data["ID"])
		{
			echo '<option value="'.$obj->db_data["ID"].'"';
			if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
			$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
			$obj2->db_close();
		}
		gera_lista_subfamilias($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);
		$obj->db_next();
	}
	$obj->db_close();
	echo '</select>';
}
function gera_lista_subfamilias($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	while(!$obj->EOF())
	{
		if($ID!=$obj->db_data["ID"])
		{
			echo '<option value="'.$obj->db_data["ID"].'"';
			if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
			$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			echo '>';
			$i=0;while($i<$contador){echo "&nbsp;";$i++;}
			echo  htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
			$obj2->db_close();
		}
		gera_lista_subfamilias($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+5);
		$obj->db_next();
	}
	$obj->db_close();
}
function show_portfolio3_menu_path($id_org=0,$m_pagina="",$tabela1="",$number_per_page=15,$checkshowimages=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$id_org2=$id_org;
	if($id_org==0) return "";
	else
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$condicao="where ID=$id_org";
		$sql="Select * from ".$tabela1." $condicao order by Ordem Desc";
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		if(!$obj->EOF())
		{
			$sql="Select * from ".$tabela1."_lang where ID_PAI=$id_org and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			$C=htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"]));
			$obj2->db_close();
			$id_org=$obj->db_data["ID_ORG"];
			$obj->db_close();
			$result=show_portfolio3_menu_path($id_org,$m_pagina,$tabela1,$number_per_page,$checkshowimages);
			if($id_org > 0) echo " - ";
			echo '<a class=new_caminho href="'.$m_pagina.'?ID='.$id_org2.'&ID_ORG='.$id_org2.'&total_prods='.$number_per_page.'&checkshowimages='.$checkshowimages.'">'.$C.'</a>';
			return ($result+1);
		}
		else
		{
			$obj->db_close();
			return 0;
		}
	}
}
function gera_lista_marcas($nome1,$nome2,$nome2_lang,$m_nome="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$flag=0;
	if($dbh)
	{
		$result=new DataObj;
		$sql="select ".$nome2.".* from ".$nome2." inner join ".$nome2_lang." on ".$nome2.".ID=".$nome2_lang.".ID_PAI order by ".$nome2_lang.".nome ASC";
		echo $sql;
		$result->db_open($dbh,$DB_name,$sql);
		$a=0;
		while (!$result->EOF())
		{
			$m=explode(";",$m_nome);
			$idd=$result->db_data["ID"];
			echo '<br><input type="checkbox" name="'.$nome1.'_'.($a+1).'" value="'.$idd.'"';
			if($m[$a]==$idd) echo ' checked ';
			echo '>&nbsp;';
			$result2=new DataObj;
			$sql="select * from ".$nome2_lang." where ID_PAI=".$result->db_data["ID"]." and Lang='$admin_lang'";
			$result2->db_open($dbh,$DB_name,$sql);
			echo $result2->db_data["Nome"];
			$result2->db_close();
			$result->db_next();
			$a++;
		}
		$result->db_close();
		db_closedb_conection($dbh);
	}
}
function gera_lista_marcas2($sel_nm,$tabela1,$tabela1_lang,$id_marca=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	echo '<select name="'.$sel_nm.'">';
	if($id_marca==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
	while(!$obj->EOF())
	{
		if($ID!=$obj->db_data["ID"])
		{
			echo '<option value="'.$obj->db_data["ID"].'"';
			if($id_marca==$obj->db_data["ID"] ) echo ' selected ';
			$sql="Select Nome from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);
			echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Nome"])).'</option>';
			$obj2->db_close();
		}
		gera_lista_subfamilias($obj->db_data["ID"],0,$tabela1,$tabela1_lang);
		$obj->db_next();
	}
	$obj->db_close();
	echo '</select>';
}
function GetRecords_Recursos($id=0,$id_org=0,$nivel=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where ID=$id";
		if($id_org!=0)	$condicao="$condicao and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	else
	{
		$condicao="where ID > 0";
		if($id_org!=0)	$condicao=$condicao." and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	$sql="Select $campos from portfolio3_families $condicao";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function GetRecords_Recursos_1($id=0,$id_org=0,$nivel=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where portfolio3_families.ID=$id and portfolio3_families_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao="$condicao and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	else
	{
		$condicao="where portfolio3_families.ID > 0 and portfolio3_families_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao=$condicao." and ID_ORG=$id_org";
		else $condicao="$condicao and ID_ORG='0'";
	}
	$sql="Select $campos,portfolio3_families.ID from portfolio3_families inner join portfolio3_families_lang on portfolio3_families_lang.ID_PAI=portfolio3_families.ID $condicao order by portfolio3_families.Ordem asc,portfolio3_families_lang.Cabecalho asc ";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function GetRecords_Recursos_1_1($id=0,$id_org=0,$nivel=0,$campos="*",$limite_from,$limite_to,$total_prods)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where portfolio3.ID=$id and portfolio3_lang.Lang='".$admin_lang."'";
		if($id_org!=0)	$condicao="$condicao and ID_ORG=$id_org and portfolio3_lang.Lang='".$admin_lang."'";
		else $condicao="$condicao and ID_ORG='0'";
	}
	else
	{
		if($limite_to!=0) $key_limite=" LIMIT $limite_from ,$total_prods";
		else $key_limite="";
		$condicao="where portfolio3.ID > 0  and portfolio3_lang.Lang='".$admin_lang."'";
		if($id_org!=0) $condicao=$condicao." and portfolio3.ID_ORG=$id_org";
		else $condicao="$condicao and portfolio3.ID_ORG='0'";
	}
	$sql="Select $campos,portfolio3.ID from portfolio3 inner join portfolio3_lang on  portfolio3_lang.ID_PAI=portfolio3.ID $condicao order by portfolio3.Aproved desc,portfolio3_lang.Cabecalho asc $key_limite";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function InsertNewFam($id_org=0,$ordem,$cabecalho,$URL,$texto1,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$areas)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
	$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
	$altera_img3=0;if($Img_peq3["name"]){$altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
	$ordem1=str2SQLstr($ordem);
	$margemcli=str2SQLstr($margemcli);
	$margemrev=str2SQLstr($margemrev);
	$cor=str2SQLstr($cor);
	$visivel=str2SQLstr($visivel);
	$destaque=str2SQLstr($destaque);
	if(!$id_org)$id_org=0;
	$sql="insert into portfolio3_families (ID_ORG,Ordem,img_ext,img2_ext,img3_ext,MargemCli,MargemRev,cor,visivel,Destaque,cat1,cat2,cat3,Download) values ('$id_org',$ordem1,'$img_ext','$img2_ext','$img3_ext',$margemcli,$margemrev,$cor,$visivel,$destaque,'$cat1','$cat2','$cat3','$download')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copy($Img_peq1["temp"],$img_path.$new_id."_1.".$ext1);
		if($altera_img2) copy($Img_peq2["temp"],$img_path.$new_id."_2.".$ext2);
		if($altera_img3) copy($Img_peq3["temp"],$img_path.$new_id."_3.".$ext3);
		InsertNewFamLinguas($new_id,$cabecalho,$URL,$texto,$areas);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	//gerar_sitemap();
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertNewFamLinguas($id_pai,$cabecalho,$URL,$texto,$areas)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$texto1=$texto;
	$URL1=str2SQLstr($URL);
	$sql="insert into portfolio3_families_lang (ID_PAI,Cabecalho,Areas,URL,Texto,Lang) values ($id_pai,$cabecalho1,'$areas',$URL1,'$texto1','$admin_lang')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
	return($res);
}
function InsertNewProd($id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
	$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
	$altera_img3=0;if($Img_peq3["name"]){$altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
	$altera_img4=0;if($Img_peq4["name"]){$altera_img4=1;$pos=strrpos($Img_peq4["name"],".");$img4_ext=substr($Img_peq4["name"],$pos+1,strlen($Img_peq4["name"])+1-$pos);}
	$rs = new DataObj;
	$cabecalho1=str2SQLstr($cabecalho);
	$preco_prec=str2SQLstr($preco_prec);
	$preco1=str2SQLstr($preco1);
	$preco2=str2SQLstr($preco2);
	$precopromo1=str2SQLstr($precopromo1);
	$precopromo2=str2SQLstr($precopromo2);
	$destaque=str2SQLstr($destaque);
	$novidade=str2SQLstr($novidade);
	$promocao=str2SQLstr($promocao);
	$home=str2SQLstr($home);
	$margem=str2SQLstr($margem);
	$margem_activa=str2SQLstr($margem_activa);
	$saldo=str2SQLstr($saldo);
	$aproved=str2SQLstr($aproved);
	$ref=str2SQLstr($ref);
	$tipo=str2SQLstr($tipo);
	$stock_txt1=str2SQLstr($stock_txt);
	$strSearchWords=str2SQLstr($strSearchWords);
	$disponivelencomenda=str2SQLstr($_POST['disponivelencomenda']);
	$alertastocks=str2SQLstr($_POST['alertastocks']);
	$sql ="INSERT INTO portfolio3
			(
				ID_ORG,
				Ordem,
				img_ext,
				img2_ext,
				img3_ext,
				img4_ext,
				Destaque,
				Aproved
			)
			VALUES
			(
				'$id_org',
				'$ordem',
				'$img_ext',
				'$img2_ext',
				'$img3_ext',
				'$img4_ext',
				$destaque,
				$aproved
			)
		";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img) copy($Img_peq1["temp"],$img_path.$new_id."_1.jpg");
		if($altera_img2)copy($Img_peq2["temp"],$img_path.$new_id."_2.jpg");
		if($altera_img3)copy($Img_peq3["temp"],$img_path.$new_id."_3.jpg");
		if($altera_img4)copy($Img_peq4["temp"],$img_path.$new_id."_4.jpg");
		portfolio3_objs_save($new_id);
		$prods = get_produtc_info($new_id);
		if($prods)
		{
			$precos = $prods['prod_precos'];
			$preco1 = $prods['prod_precos']['PVP'];
			$stock = $prods['prod_precos']['STOCK'];
			$sql = '
					UPDATE
						portfolio3
					SET
						Preco1="'.$preco1.'",
						Qtd="'.$stock.'"
					WHERE
						ID="'.$new_id.'"
					';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		InsertProdLinguas($new_id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$areas1,$pais1,$ano1);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$texto4,$areas1,$pais1,$ano1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho1=str2SQLstr($cabecalho);
	$URL1=str2SQLstr($URL);
	$areas1='';
	if(isset($_POST['areas1']) && !empty($_POST['areas1']))foreach($_POST['areas1'] as $areas) $areas1.=$areas.';';
	$sql="insert into portfolio3_lang (ID_PAI,Cabecalho,URL,Texto1,Texto2,Texto3,Tamanho,Cor,Lang,Texto4,Areas1,Pais1,Ano1) values ($id_pai,$cabecalho1,$URL1,'$texto1','$texto2','$texto3','$tamanho','$cor','$admin_lang','$texto4','$areas1','$pais1','$ano1')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
	return($res);
}
function BuscarNivelId($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select Nivel from portfolio3_families where ID=$id");
	$nivel=$obj->db_data["Nivel"];
	return($nivel);
}
function MaxNivel($db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select max(Nivel) as maximo from portfolio3_families");
	$nivel=$obj->db_data["maximo"];
	return ($nivel);
}
function BuscarImg($id,$db_link)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name,"Select * from portfolio3_families where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	return ($ext1);
}
function DeleteFam($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$Conn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$nivel=BuscarNivelId($id);
	$nivel=$nivel+1;
	$vector_filhos[0]=$id;
	$count=0;
	$MaxNivel=MaxNivel($Conn);
	for($i=$nivel;$i<=$MaxNivel;$i++)
	{
		$rs=GetRecords_Recursos(0,$vector_filhos[$count],$i);
		while(!$rs->EOF())//se tiver filhos percorre-os
		{
			$count=$count+1;
			$codigo=htmlentities_b($rs->db_data["ID"]);
			$vector_filhos[$count]=$codigo;
			$rs->db_next();
		}
		$rs->db_close(1);
	}
	$Conn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	for($i=0;$i<count($vector_filhos);$i++)
	{
		$img=BuscarImg($vector_filhos[$i],$Conn);
		$del="delete from portfolio3_families where ID=".$vector_filhos[$i];
		$res=Runquery($Conn,$DB_name,$del);
		if($res)
		{
			$sql2="delete from portfolio3_families_lang where ID_PAI=".$vector_filhos[$i];
			$res2=Runquery($Conn,$DB_name,$sql2);
		}
		else echo db_error($Conn);
	}
	//gerar_sitemap();
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function DeleteProd($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$Conn=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	//$img=BuscarImg($id,$Conn);
	$res=Runquery($Conn,$DB_name,"delete from portfolio3 where ID=$id");
	if($res)
	{
//		if($ext1!=""){$ficheiro1=$id."_1.".$ext1;unlink($img_path.$ficheiro1);}
		$sql2="delete from portfolio3_lang where ID_PAI=".$id;
		$res2=Runquery($Conn,$DB_name,$sql2);
	}
	else echo db_error($Conn);
	//gerar_sitemap();
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function ChangeFam($id,$id_org,$ordem,$cabecalho,$URL,$texto,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$Img_peq2,$Apagar_img2,$tem_foto2,$Img_peq3,$Apagar_img3,$tem_foto3,$margemcli,$margemrev,$cor,$visivel,$destaque,$tipo_check,$tipo,$cat1,$cat2,$cat3,$download,$areas)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from portfolio3_families WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
		$img2_ext=$rs->db_data["img2_ext"];
		$img3_ext=$rs->db_data["img3_ext"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewFam($id_org,$data,$cabecalho,$URL,$texto,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$margemcli,$margemrev,$cor,$visivel,$destaque,$cat1,$cat2,$cat3,$download,$areas);
	else
	{
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
		$altera_img3=0;if($Img_peq3["name"]){$altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img){
			copy($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
			putWatermarkImage($img_path,$id."_1.".$img_ext);
		}
		//IMAGEM NUMERO 2
		if($tem_foto2 && $Apagar_img2){$ficheiro2=$id."_2.".$img2_ext;unlink($img_path.$ficheiro2);if(!$altera_img2)$img2_ext='';}
		if($altera_img2){
			copy($Img_peq2["temp"],$img_path.$id."_2.".$img2_ext);
			putWatermarkImage($img_path,$id."_2.".$img_ext);
		}
		//IMAGEM NUMERO 3
		if($tem_foto3 && $Apagar_img3){$ficheiro3=$id."_3.".$img3_ext;unlink($img_path.$ficheiro3);if(!$altera_img3)$img3_ext='';}
		if($altera_img3){
			copy($Img_peq3["temp"],$img_path.$id."_3.".$img3_ext);
			putWatermarkImage($img_path,$id."_3.".$img_ext);
		}
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		$texto1=str2SQLstr($texto);
		$URL1=str2SQLstr($URL);
		$margemcli=str2SQLstr($margemcli);
		$margemrev=str2SQLstr($margemrev);
		$cor=str2SQLstr($cor);
		$visivel=str2SQLstr($visivel);
		$destaque=str2SQLstr($destaque);
		$sql="Update portfolio3_families Set ID_ORG='$id_org',Ordem=$ordem1,img_ext='$img_ext',img2_ext='$img2_ext',img3_ext='$img3_ext',MargemCli=$margemcli,MargemRev=$margemrev,cor=$cor,visivel=$visivel,Destaque=$destaque,cat1='$cat1',cat2='$cat2',cat3='$cat3',Download='$download' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if($tipo_check==1)
		{
			$sql="Update portfolio3 Set Tipo=$tipo where ID_ORG=$id";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		db_closedb_conection($db_link);
		ChangeFamLinguas($id,$cabecalho,$URL,$texto,$areas);
		//gerar_sitemap();
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
	}
}
function ChangeFamLinguas($id_pai,$cabecalho,$URL,$texto,$areas)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from portfolio3_families_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();
	if(!$altera)return InsertNewFamLinguas($id_pai,$cabecalho,$URL,$texto);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$cabecalho1=str2SQLstr($cabecalho);
		$texto1=$texto;
		$URL1=str2SQLstr($URL);
		$sql="Update portfolio3_families_lang Set Cabecalho=$cabecalho1,URL=$URL1,Texto='$texto1',Areas='$areas' where ID_PAI=$id_pai and Lang='$admin_lang'";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function altera_imagem($id,$ext2,$tem_foto2,$altera_img2,$img_path,$Img_peq2,$num=4,$Apagar_img2)
{
  if($tem_foto2==0)
  {
		if(!$altera_img2){$img2_ext="";$img2_ext=str2SQLstr($img2_ext);}
		else{$pos=strrpos($Img_peq2["name"],".");
			$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
			$img2_ext=str2SQLstr($ext2);
			copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
			redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_".$num."_2.".$ext2);// redimensiona para o tamanho default
			redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_".$num."_1.".$ext2);// gera o tamanho pequeno
		}
	}
	else  //TINHA IMAGEM
	{
		if($Apagar_img2==1)
		{
			if($num==4){$ficheiro2=$id."_1.".$ext2; unlink($img_path.$ficheiro2);}
			if($num==4){$ficheiro2=$id."_2.".$ext2; unlink($img_path.$ficheiro2);}
			$ficheiro2=$id."_".$num.".".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_1.".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_2.".$ext2; unlink($img_path.$ficheiro2);
			$ficheiro2=$id."_".$num."_0.".$ext2; unlink($img_path.$ficheiro2);
			//NAO SUBSTITUI A IMAGEM
			if(!$altera_img2){$img2_ext="";$img2_ext=str2SQLstr($img2_ext);}
			else
			{
				$pos=strrpos($Img_peq2["name"],".");
				$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
				$img2_ext=str2SQLstr($ext2);
				copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_2.".$ext2);// redimensiona para o tamanho default
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_1.".$ext2);// gera o tamanho pequeno
			}
		}
		else
		{
			if($altera_img2)
			{
				$pos=strrpos($Img_peq2["name"],".");
				$ext2=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);
				$img2_ext=str2SQLstr($ext2);
				copy($Img_peq2["temp"],$img_path.$id."_".$num."_3.".$ext2);// Gravar o tamanho original
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,285,220,$id."_".$num."_2.".$ext2);// redimensiona para o tamanho default
				redimencionaJpeg($img_path,$id."_".$num."_3.".$ext2,122,122,$id."_".$num."_1.".$ext2);// gera o tamanho pequeno
			}
			else $img2_ext=str2SQLstr($ext2);
		}
	}
	return $img2_ext;
}
function ChangeProd($id,$id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$Apagar_img1=0,$Apagar_img2=0,$Apagar_img3=0,$Apagar_img4=0,$Apagar_img6=0,$Apagar_img7=0,$tem_foto1,$tem_foto2,$tem_foto3,$tem_foto4,$tem_foto6,$tem_foto7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$decumentario,$texto4,$pais1,$ano1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang) $m_lang = $admin_lang;
	if($id_org == $id) $id_org=0;
	$rs = new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,'Select * from portfolio3 WHERE ID="'.$id.'"');
	if(!$rs->EOF())
	{
		$altera = 1;
		$img_ext=$rs->db_data["img_ext"]; $img2_ext=$rs->db_data["img2_ext"]; $img3_ext=$rs->db_data["img3_ext"]; $img4_ext=$rs->db_data["img4_ext"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewProd($id_org,$ordem,$cabecalho,$ref,$tamanho,$cor,$texto1,$texto2,$texto3,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peq6,$Img_peq7,$preco1,$preco2,$precopromo1,$precopromo2,$margem,$margem_activa,$destaque,$novidade,$promocao,$home,$saldo,$aproved,$tipo,$preco_prec,$strSearchWords,$marca,$peso,$usado,$stock_txt,$configurado,$configurador,$medidas,$precocusto,$stock,$download,$img_path_download,$Download_peq,$decumentario,$texto4,$pais1,$ano1);
	else
	{
		$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
		$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
		$altera_img3=0;if($Img_peq3["name"]){$altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos);}
		$altera_img4=0;if($Img_peq4["name"]){$altera_img4=1;$pos=strrpos($Img_peq4["name"],".");$img4_ext=substr($Img_peq4["name"],$pos+1,strlen($Img_peq4["name"])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img){
			copy($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
			putWatermarkImage($img_path,$id."_1.".$img_ext);
		}
		//IMAGEM NUMERO 2
		if($tem_foto2 && $Apagar_img2){$ficheiro2=$id."_2.".$img2_ext;unlink($img_path.$ficheiro2);if(!$altera_img2)$img2_ext='';}
		if($altera_img2){
			copy($Img_peq2["temp"],$img_path.$id."_2.".$img2_ext);
			putWatermarkImage($img_path,$id."_2.".$img2_ext);
		}
		//IMAGEM NUMERO 3
		if($tem_foto3 && $Apagar_img3){$ficheiro3=$id."_3.".$img3_ext;unlink($img_path.$ficheiro3);if(!$altera_img3)$img3_ext='';}
		if($altera_img3){
			copy($Img_peq3["temp"],$img_path.$id."_3.".$img3_ext);
			putWatermarkImage($img_path,$id."_3.".$img3_ext);
		}
		//IMAGEM NUMERO 4
		if($tem_foto4 && $Apagar_img4){$ficheiro4=$id."_4.".$img4_ext;unlink($img_path.$ficheiro4);if(!$altera_img4)$img4_ext='';}
		if($altera_img4){
			copy($Img_peq4["temp"],$img_path.$id."_4.".$img4_ext);
			putWatermarkImage($img_path,$id."_4.".$img4_ext);
		}
		$id_org1=str2SQLstr($id_org);
		$ordem1=str2SQLstr($ordem);
		$cabecalho1=str2SQLstr($cabecalho);
		$ref1=str2SQLstr($ref);
		$preco_prec=str2SQLstr($preco_prec);
		$preco11=str2SQLstr($preco1);
		$preco21=str2SQLstr($preco2);
		$precopromo11=str2SQLstr($precopromo1);
		$precopromo21=str2SQLstr($precopromo2);
		$margem1=str2SQLstr($margem);
		$margem_activa1=str2SQLstr($margem_activa);
		$destaque1=str2SQLstr($destaque);
		$novidade1=str2SQLstr($novidade);
		$promocao1=str2SQLstr($promocao);
		$home1=str2SQLstr($home);
		$saldo=str2SQLstr($saldo);
		$aproved=str2SQLstr($aproved);
		$tipo=str2SQLstr($tipo);
		$stock_txt1=str2SQLstr($stock_txt);
		$strSearchWords=str2SQLstr($strSearchWords);
		$disponivelencomenda=str2SQLstr($_POST['disponivelencomenda']);
		$alertastocks=str2SQLstr($_POST['alertastocks']);
		$sql =
			"
				UPDATE portfolio3
				SET
					ID_ORG = $id_org1,
					Ordem = $ordem1,
					Ref = $ref1,
					img_ext = '$img_ext',
					img2_ext = '$img2_ext',
					img3_ext = '$img3_ext',
					img4_ext = '$img4_ext',
					Preco1 = $preco11,
					Preco2 = $preco21,
					PrecoPromo1 = $precopromo11,
					PrecoPromo2 = $precopromo21,
					Preco_prec = $preco_prec,
					Margem = $margem1,
					MargemActiva = $margem_activa1,
					StockTxt = $stock_txt1,
					Destaque = $destaque1,
					Novidade = $novidade1,
					Promo = $promocao1,
					Home = $home1,
					Saldo = $saldo,
					Aproved = $aproved,
					Tipo = $tipo,
					Hottags = $strSearchWords,
					ID_Marca = '$marca',
					Peso = '$peso',
					usado = '$usado',
					Medidas = '$medidas',
					PrecoCusto = '$precocusto',
					Qtd = '$stock',
					Download = '$download',
					DisponivelEncomenda = $disponivelencomenda,
					AlertaStocks = $alertastocks,
					decumentario = '$decumentario'
				WHERE
					ID=$id
			";
		$res = Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			portfolio3_objs_save($id);
			$prods = get_produtc_info($id);
			if($prods)
			{
				$precos = $prods['prod_precos'];
				$preco1 = $prods['prod_precos']['PVP'];
				$stock = $prods['prod_precos']['STOCK'];
				$sql = 'UPDATE portfolio3 SET Preco1="'.$preco1.'",Qtd="'.$stock.'" WHERE ID="'.$id.'"';
				$res=Runquery($db_link,$DB_name,$sql);
			}
		}
		db_closedb_conection($db_link);
		ChangeProdLinguas($id,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$pais1,$ano1);
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
  }
}
function ChangeProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor,$img_path_download,$Download_peq,$Apagar_download,$tem_download,$texto4,$pais1,$ano1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang) $m_lang = $admin_lang;
	if($id_org == $id) $id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"SELECT * FROM portfolio3_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())
	{
		$altera=1; $id_video=$rs->db_data["ID"]; // $tamanho=$rs->db_data["Tamanho"];
	}
	$rs->db_close();
	if(!$altera)return InsertProdLinguas($id_pai,$cabecalho,$URL,$texto1,$texto2,$texto3,$tamanho,$cor);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$altera_download=0; if($Download_peq["name"]){ $altera_download = 1; $pos = strrpos($Download_peq["name"],"."); $tamanho=substr($Download_peq["name"],$pos+1,strlen($Download_peq["name"])+1-$pos); }
		if(!$tem_download && $Apagar_download){$ficheiro = $id_video."_download.".$tamanho; unlink($img_path_download.$ficheiro); if(!$altera_download) $tamanho = '';}
		if($altera_download) copy($Download_peq["temp"],$img_path_download . $id_video . "_download." . $tamanho);
		// echo $img_path_download . $id_video . "_download." . $tamanho;
		$obj = new DataObj;
		$cabecalho1 = str2SQLstr($cabecalho);
		$URL1 = str2SQLstr($URL);

		$areas1='';
		if(isset($_POST['areas1']) && !empty($_POST['areas1']))foreach($_POST['areas1'] as $areas) $areas1.=$areas.';';
		$sql ="UPDATE portfolio3_lang
				SET
					Cabecalho=$cabecalho1,
					URL=$URL1,
					Texto1='$texto1',
					Texto2='$texto2',
					Texto3='$texto3',
					Tamanho='$tamanho',
					Cor='$cor',
					Texto4='$texto4',Areas1='$areas1',Pais1='$pais1',Ano1='$ano1'
				WHERE
					ID_PAI=$id_pai
					AND
					Lang='$admin_lang'";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function conta_produtos($ID,$show=1)
{//$show 0->Antigos 1->Alterados 4->Todos
  global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
  $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
  $rs=new DataObj();
  $rs2=new DataObj();
  $contador=0;
  if($show==-1) $claus="";
  elseif($show==0) $claus="AND Alterado=0";
  elseif($show==1) $claus="AND Alterado=1";
  elseif($show==4) $claus="";
  $sql2="Select count(*) as Cont from portfolio3 where ID_ORG=$ID $claus";
  $rs2->db_open($db_link,$DB_name,$sql2);
  if(!$rs2->EOF()) $contador=$rs2->db_data["Cont"];
  $rs2->db_close();
  $sql="select * from portfolio3_families where ID_ORG=$ID";
  $rs->db_open($db_link,$DB_name,$sql);
  while(!$rs->EOF())
  {
	$ID_ORG=$rs->db_data["ID"];
	$contador+=conta_produtos($ID_ORG,$show);//Conta produtos das "Familias Filhas" na hierarquia
	$rs->db_next();
  }
  $rs->db_close();
  return $contador;
}
function conta_produtos_d($ID,$tipo=0)
{//$show 0->Antigos 1->Alterados 4->Todos
  global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
  $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
  $rs=new DataObj();
  $rs2=new DataObj();
  $contador=0;
  if($tipo==0) $claus="AND Promo=1";
  if($tipo==1) $claus="AND Novidade=1";
  if($tipo==2) $claus="AND Destaque=1";
  if($tipo==3) $claus="AND Saldo=1";
  $sql2="Select count(*) as Cont from portfolio3 where ID_ORG=$ID $claus";
  $rs2->db_open($db_link,$DB_name,$sql2);
  if(!$rs2->EOF()) $contador=$rs2->db_data["Cont"];
  $rs2->db_close();
  $sql="select * from portfolio3_families where ID_ORG=$ID";
  $rs->db_open($db_link,$DB_name,$sql);
  while(!$rs->EOF())
  {
	$ID_ORG=$rs->db_data["ID"];
	$contador+=conta_produtos($ID_ORG,$show);//Conta produtos das "Familias Filhas" na hierarquia
	$rs->db_next();
  }
  $rs->db_close();
  return $contador;
}
function show_num_pages2_backoffice($id_org=0,$campos="*",$join="",$tabela1="",$m_pagina="",$number_per_page=0,$checkshowimages=0,$ordenacao=0)
{
	global $from_prods;
	if($number_per_page==0) return 0;
	$total_records=0;
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$condicao="where ID_ORG=$id_org";
	if($tabela1) $sql="Select $campos from $tabela1 $join $condicao";
	else $sql="Select $campos from portfolio3_families $join $condicao";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	if(!$obj->EOF())
	{
		$total_records=$obj->db_rows;
		$i=0;
		$num_pages=1;
		echo " de $total_records iten";
		if($total_records>1) echo "s";
		echo ") ";
		if($i<$total_records) echo "Página: ";
		while ($i<$total_records)
		{
			if($from_prods==$i)	echo '<a class=page_number2 href="'.$m_pagina.'&from_prods='.$i.'&to_prods='.($i+$number_per_page-1).'&total_prods='.$number_per_page.'">'.$num_pages.'</a> ';
			else				echo '<a class=page_number href="'.$m_pagina.'&from_prods='.$i.'&to_prods='.($i+$number_per_page-1).'&total_prods='.$number_per_page.'">'.$num_pages.'</a> ';
			$i+=$number_per_page;
			$num_pages++;
		}
	}
	$obj->db_close();
}

function strtolower2($str)
{
	$str =html_entity_decode($str);
	$valores=array("(",")","3","°","2","+","1","%","«","»","?","a","o","®","©","AfÂ§");	$str=str_replace($valores,'',$str);
	$valores=array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","+","|","\\");	$str=str_replace($valores,'-',$str);
	$str=str_replace('--','-',$str);
	$str=str_replace('-',' ',$str);
	$valores=array("á","a","a","â","ä"); 	$str=str_replace($valores,'a',$str);
	$valores=array("é","e","e","ë"); 		$str=str_replace($valores,'e',$str);
	$valores=array("í","i","î","i"); 		$str=str_replace($valores,'i',$str);
	$valores=array("ó","o","o","ô","ö"); 	$str=str_replace($valores,'o',$str);
	$valores=array("ú","u","u","ü"); 		$str=str_replace($valores,'u',$str);
	$valores=array("n"); 		$str=str_replace($valores,'n',$str);
	$valores=array("ç"); 		$str=str_replace($valores,'c',$str);
	$valores=array("Á","A","A","Â","Ä"); 	$str=str_replace($valores,'A',$str);
	$valores=array("É","E","E","Ë"); 		$str=str_replace($valores,'E',$str);
	$valores=array("Í","I","Î","I"); 		$str=str_replace($valores,'I',$str);
	$valores=array("Ó","O","O","Ô","Ö"); 	$str=str_replace($valores,'O',$str);
	$valores=array("Ú","U","U","Ü"); 		$str=str_replace($valores,'U',$str);
	$valores=array("N"); 		$str=str_replace($valores,'N',$str);
	$valores=array("Ç"); 		$str=str_replace($valores,'C',$str);
	return ltrim(rtrim(strtolower($str)));
}
function limpa_carateres($str="")
{
	$str =str_replace('\\\\','{b}',$str);
	$str =str_replace(' gb','gb',$str);	$str =str_replace(' GB','GB',$str);
	$str =str_replace(' Gb','GB',$str);	$str =str_replace(' tb','tb',$str);
	$str =str_replace(' TB','TB',$str);	$str =str_replace(' Tb','TB',$str);
	$str =str_replace(' mb','mb',$str);	$str =str_replace(' MB','MB',$str);
	$str =str_replace(' Mb','MB',$str);	$str =str_replace('USB 2.0','USB2.0',$str);
	$str=ereg_replace("[  ]"," ",$str);
	$str=ereg_replace("[®T'\]","",$str);
	return rtrim(ltrim($str));
}
function Pesquisa_produtos_backoffice($Str_pesquisar,$cat=0,$t_cat="portfolio3")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$sugest,$count,$total_palavras,$num,$produtos,$Str_pesquisar2,$ID_Fam;
	$rs=new DataObj;$obj=new DataObj;$obj2=new DataObj;
	$Str_pesquisar2=$Str_pesquisar;
	$Str_pesquisar=limpa_carateres($Str_pesquisar);
	$Tabela=$t_cat."_lang";
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,$Tabela,$db_link);
	//**************************************************************
	// primeiro vamos verificar se o termo introduzido e uma Ref
	if($ID_Fam) $sql="Select * from $Tabela INNER JOIN $t_cat ON $t_cat.ID=$Tabela.ID_PAI WHERE Ref='".$Str_pesquisar2."' AND $t_cat.ID_ORG =$ID_Fam ORDER BY Cabecalho ASC";
	else		$sql="Select * from $Tabela INNER JOIN $t_cat ON $t_cat.ID=$Tabela.ID_PAI WHERE Ref='".$Str_pesquisar2."' ORDER BY Cabecalho ASC";
	$hRes=$obj->db_open( $db_link,$DB_name,$sql);
	$count=0;
	if(!$obj->EOF())
	{
		$found=1;
		while (!$obj->EOF())
		{
			if (!$produtos[$cat] || (!in_array($obj->db_data["ID_PAI"],$produtos[$cat])))
			{
				$produtos[$cat][]=$obj->db_data["ID_PAI"];
				$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
				$nomeb=str_replace("'","",$nomeb);

				$sugest[$num][0]=$obj->db_data["ID_PAI"];
				$sugest[$num][4]=$obj->db_data["ID_ORG"];
				$sugest[$num][3]=$cat;
				$sugest[$num][2]=$nomeb;
				$num++;
				$count++;
			}
			$obj->db_next();
		}
	}
	else
	{
		if($ID_Fam) $sql="Select * from $Tabela INNER JOIN $t_cat ON $t_cat.ID=$Tabela.ID_PAI WHERE Cabecalho Like '%".$Str_pesquisar2."%' AND $t_cat.ID_ORG =$ID_Fam ORDER BY Cabecalho ASC";
		else		$sql="Select * from $Tabela INNER JOIN $t_cat ON $t_cat.ID=$Tabela.ID_PAI WHERE Cabecalho Like '%".$Str_pesquisar2."%' ORDER BY Cabecalho ASC";
		$hRes=$obj->db_open( $db_link,$DB_name,$sql);
		$count=0;
		if(!$obj->EOF())
		{
			$found=1;
			while (!$obj->EOF())
			{
				if (!$produtos[$cat] || (!in_array($obj->db_data["ID_PAI"],$produtos[$cat])))
				{
					$produtos[$cat][]=$obj->db_data["ID_PAI"];
					$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
					$nomeb=str_replace("'","",$nomeb);

					$sugest[$num][0]=$obj->db_data["ID_PAI"];
					$sugest[$num][4]=$obj->db_data["ID_ORG"];
					$sugest[$num][3]=$cat;
					$sugest[$num][2]=$nomeb;
					$num++;
					$count++;
				}
				$obj->db_next();
			}
		}
		else
		{
			// se for, nao se faz mais nenhuma pesquisa
			//**************************************************************
			// Para melhorarmos a pesquisa vamos analisar se algum termo é familia
			$pesq=$Str_pesquisar;
			$instrucao="";
			$ArrayPalavras=split('[ /.;,+*:_-]',$pesq);
			$n_palavras=count($ArrayPalavras);
			if($cat!="")
			{
				$sql="SELECT * FROM portfolio3_families_lang WHERE ID_PAI=$cat";
				$hRes=$obj2->db_open( $db_link,$DB_name,$sql);
				if(!$obj2->EOF())
				{
					$nome_fam=str_replace(" ", "-",$obj2->db_data["Cabecalho"]);
					$str_and.=" AND (LOWER(REPLACE($t_cat.Google,'\\\\','{b}')) LIKE '%".$nome_fam."%') or";
					$str_and=ltrim(rtrim(substr($str_and,0 ,strlen($str_and)-2)));
				}
			}
			$instrucao.="(LOWER(REPLACE($t_cat.Google,'\\\\','{b}')) LIKE '%".strtolower2($Str_pesquisar)."%') or";
			$instrucao=ltrim(rtrim(substr($instrucao,0 ,strlen($instrucao)-2)));
			$sql="Select * from $Tabela,$t_cat where $t_cat.ID=$Tabela.ID_PAI AND (".$instrucao.") $str_and ORDER BY Cabecalho ASC";
			$hRes=$obj->db_open( $db_link,$DB_name,$sql);
			if(!$obj->EOF())
			{
				$found=1;
				while (!$obj->EOF())
				{
					if (!$produtos[$cat] || (!in_array($obj->db_data["ID_PAI"],$produtos[$cat])))
					{
						$produtos[$cat][]=$obj->db_data["ID_PAI"];
						$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
						$nomeb=str_replace("'","",$nomeb);

						$sugest[$num][0]=$obj->db_data["ID_PAI"];
						$sugest[$num][4]=$obj->db_data["ID_ORG"];
						$sugest[$num][3]=$cat;
						$sugest[$num][2]=$nomeb;
						$num++;
						$count++;
					}
					$obj->db_next();
				}
			}
		}
		if($obj->EOF())
		{
			// Nao existem produtos, vamos entao procurar palavra a palavra
			$instrucao="";
			$pesq=$Str_pesquisar;
			$ArrayPalavras=split('[ /.;,+*:_-]',$pesq);
			$n_palavras=count($ArrayPalavras);
			if(is_array($ArrayPalavras))
			{
				$instrucao=" AND ( ";
				for($j=0;$j<$n_palavras;$j++)
				{
					if(strlen($ArrayPalavras[$j])>2)
					{
						$ArrayPalavras[$j]=str2SQLLikestr($ArrayPalavras[$j]);
						$ArrayPalavras[$j]=ereg_replace("[  ]"," ",$ArrayPalavras[$j]);
						$instrucao.=" (LOWER($Tabela.Cabecalho) LIKE '%".strtolower2($ArrayPalavras[$j])."%') OR  (LOWER($t_cat.Ref) LIKE '%".strtolower2($ArrayPalavras[$j])."%') OR ";
					}
				}
				$instrucao.=" 1=0 ) ";
				if($ID_Fam) $str_and=" AND $t_cat.ID_ORG =$ID_Fam ";
				$sql="Select * from $Tabela,$t_cat where $t_cat.ID=$Tabela.ID_PAI ".$instrucao." $str_and  ORDER BY Cabecalho ASC";
				$hRes=$obj->db_open( $db_link,$DB_name,$sql);
			}
			if(!$obj->EOF()) $found=1;
			if($obj->EOF())
			{
				// Nao existem produtos, vamos entao ver se a pesquisa se refere a alguma familia
				$sql ="SELECT * FROM ".$t_cat."_familias_lang WHERE LOWER(Cabecalho) ='".strtolower2($Str_pesquisar)."'";
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$Familia =$Str_pesquisar;
					$familia2=strtolower2($Familia);
					$rs->db_close();
					// é uma familia, entao vamos mostar todos os produtos dessa familia
					$sql ="SELECT ".$t_cat.".ID,".$t_cat.".ID_ORG,".$Tabela.".Cabecalho FROM ".$Tabela." INNER JOIN ".$t_cat."_familias_lang ON ".$t_cat."_familias_lang.ID =".$t_cat.".ID_ORG WHERE LOWER(".$t_cat."_familias_lang.Cabecalho) LIKE '%".strtolower2(($familia2)). "%' order by Cabecalho";
					$obj->db_open($db_link,$DB_name,$sql);
					if(!$obj->EOF()) $found=2;
					else return 0;
				}
			}
			//**************************** Se encontramos produtos vamos guardar
			while (!$obj->EOF())
			{
				if (!$produtos[$cat] || (!in_array($obj->db_data["ID_PAI"],$produtos[$cat])))
				{
					$produtos[$cat][]=$obj->db_data["ID_PAI"];
					$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
					$nomeb=str_replace("'","",$nomeb);

					$sugest[$num][0]=$obj->db_data["ID_PAI"];
					$sugest[$num][4]=$obj->db_data["ID_ORG"];
					$sugest[$num][3]=$cat;
					$sugest[$num][2]=$nomeb;
					$nomeg=str_replace('http://memogadget.com/','',$obj->db_data["Google"]);
					$nomeg=str_replace("-","",$nomeg);
					$nomeg=str_replace(".php","",$nomeg);
					$nomeg=$nomeb;
					$sugest[$num][5]=strtolower2($nomeg);
					$num++;
					$count++;
				}
				$obj->db_next();
			}
		}
		else
		{
			$count=0;
			$found=1;
			$pesq_adv=1;
			while (!$obj->EOF())
			{
				if (!$produtos[$cat] || (!in_array($obj->db_data["ID_PAI"],$produtos[$cat])))
				{
					$produtos[$cat][]=$obj->db_data["ID_PAI"];
					$nomeb=str_replace('"','',$obj->db_data["Cabecalho"]);
					$nomeb=str_replace("'","",$nomeb);
					$sugest[$num][0]=$obj->db_data["ID_PAI"];
					$sugest[$num][4]=$obj->db_data["ID_ORG"];
					$sugest[$num][3]=$cat;
					$sugest[$num][2]=$nomeb;
					$nomeg=str_replace('http://memogadget.com/','',$obj->db_data["Google"]);
					$nomeg=str_replace("-","",$nomeg);
					$nomeg=str_replace(".php","",$nomeg);
					$nomeg=$nomeb;
					$sugest[$num][5]=strtolower2($nomeg);
					$num++;
					$count++;
				}
				$obj->db_next();
			}
		}
		$obj->db_close();
	}
	return $found;
}
function gera_resultados($m_nome)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
	global $sugest,$count,$total_palavras,$num,$peso,$cfg_esquema,$Str_pesquisar2;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$maior_num_palavras=0;
	class clsPesquisa
	{
		var $db_link,$hRes,$sDB,$db_data,$db_data2,$sSQLQuery,$iPos,$db_rows;
		function inicializa()
		{
			$this->iPos=0;
			$this->db_rows=0;
		}
		function db_close($nada)
		{
			// Nada
		}
		function db_next()
		{
			if(!$this->EOF())
			{
				$this->iPos++;
				$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
				$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
				$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
				$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
				$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
				$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
				$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
				$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
				$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
				$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
			}
		}
		function Previous()
		{
			if ($this->iPos==0)	$this->iPos--;
			else
			{
				$this->iPos--;
				$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
				$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
				$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
				$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
				$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
				$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
				$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
				$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
				$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
				$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
			}
		}
		function EOF()
		{
			if ($this->iPos==$this->db_rows)	return 1;
			else							return 0;
		}
		function BOF()
		{
			if ($this->iPos<0)	return 1;
			else				return 0;
		}
		function avanca()
		{
			// Actualizar vector temporario
			$this->db_data2[$this->db_rows]["Aproved"]=$this->db_data["Aproved"];
			$this->db_data2[$this->db_rows]["ID"]=$this->db_data["ID"];
			$this->db_data2[$this->db_rows]["img_ext"]=$this->db_data["img_ext"];
			$this->db_data2[$this->db_rows]["Ref"]=$this->db_data["Ref"];
			$this->db_data2[$this->db_rows]["Preco1"]=$this->db_data["Preco1"];
			$this->db_data2[$this->db_rows]["Preco2"]=$this->db_data["Preco2"];
			$this->db_data2[$this->db_rows]["PrecoCusto"]=$this->db_data["PrecoCusto"];
			$this->db_data2[$this->db_rows]["ID_ORG"]=$this->db_data["ID_ORG"];
			$this->db_data2[$this->db_rows]["Importado"]=$this->db_data["Importado"];
			$this->db_data2[$this->db_rows]["id_fornecedor"]=$this->db_data["id_fornecedor"];
			$this->db_rows++;
		}
		function first()
		{
			$this->iPos=0;
			$this->db_data["Aproved"]=$this->db_data2[$this->iPos]["Aproved"];
			$this->db_data["ID"]=$this->db_data2[$this->iPos]["ID"];
			$this->db_data["img_ext"]=$this->db_data2[$this->iPos]["img_ext"];
			$this->db_data["Ref"]=$this->db_data2[$this->iPos]["Ref"];
			$this->db_data["Preco1"]=$this->db_data2[$this->iPos]["Preco1"];
			$this->db_data["Preco2"]=$this->db_data2[$this->iPos]["Preco2"];
			$this->db_data["PrecoCusto"]=$this->db_data2[$this->iPos]["PrecoCusto"];
			$this->db_data["ID_ORG"]=$this->db_data2[$this->iPos]["ID_ORG"];
			$this->db_data["Importado"]=$this->db_data2[$this->iPos]["Importado"];
			$this->db_data["id_fornecedor"]=$this->db_data2[$this->iPos]["id_fornecedor"];
		}
	}
	$resultados=new clsPesquisa;
	$resultados->inicializa();
	$m_nome =str_replace(' gb','gb',$m_nome);	$m_nome =str_replace(' GB','GB',$m_nome);
	$m_nome =str_replace(' Gb','GB',$m_nome);	$m_nome =str_replace(' tb','tb',$m_nome);
	$m_nome =str_replace(' TB','TB',$m_nome);	$m_nome =str_replace(' Tb','TB',$m_nome);
	$m_nome =str_replace(' mb','mb',$m_nome);	$m_nome =str_replace(' MB','MB',$m_nome);
	$m_nome =str_replace(' Mb','MB',$m_nome);	$m_nome =str_replace('USB 2.0','USB2.0',$m_nome);
	$m_nome2=$m_nome;
	$m_nome=strtolower2($m_nome);
	$m_str1 =explode(" ",$m_nome);
	$num_palavras =count($m_str1);
	$count2=0;
	while($count2<$num_palavras)
	{
		$count2++;
		$total_palavras[$count2]=0;
	}
	$count2=0;
	while($count2<$num)
	{
		$i=0;
		$conta=0;
		$x_str=$sugest[$count2][5];
		if($log4) echo "x_str=".$x_str."<br>";
		while($i < count($m_str1))
		{
			$t=substr_count($x_str,$m_str1[$i]);
			if($num_palavras > 1){if($t > 0) $conta+=1;}
			else				 {$conta+=$t;}
			$i++;
		}
		if($conta > $maior_num_palavras) $maior_num_palavras =$conta;
		$sugest[$count2][4]=$conta;
		$numy =$conta;
		$total_palavras[$numy]++;
		$sugest2[$numy][$total_palavras[$numy]]=$count2;
		$count2++;
	}
	$count2=0;$count3=0;
	$fim=0;
	$j=0;
	$num_palavras=$maior_num_palavras;
	$niveis=$num_palavras;
	while($num_palavras >=0)
	{
		$i=0;
		while($i <=$total_palavras[$num_palavras] && !$fim)
		{
			$chave =$sugest2[$num_palavras][$i];
			// Se estivermos no nivel maximo vamos mostrar tudo
			if($total > 0 && $niveis>2)
			{
				if($num_palavras < $niveis) $j++;
				if($j>30) $fim=1;
			}
			$ID_do_produto=$sugest[$chave][0];
			$Str_name=$sugest[$chave][2];
			$ID_da_Familia=$sugest[$chave][4];
			$cat=$sugest[$chave][3];
			$Pagina="produtos.php?ID=";
			$t_name2="1";
			$t_name=$t_name2;
			$obj2=new DataObj;
			if($ID_do_produto)
			{
					global $m_tab; $m_tab="";
					$sql="Select * FROM portfolio3 where ID=".$ID_do_produto;
					$hRes=$obj2->db_open( $db_link,$DB_name,$sql);
					if(!$obj2->EOF())
					{
						$ID_da_Familia=$obj2->db_data["ID_ORG"];
						$x_ref=$obj2->db_data["Ref"];
						$img_ext=$obj2->db_data["img_ext"];
						$resultados->db_data["Aproved"]=$obj2->db_data["Aproved"];
						$resultados->db_data["ID"]=$obj2->db_data["ID"];
						$resultados->db_data["img_ext"]=$obj2->db_data["img_ext"];
						$resultados->db_data["Ref"]=$obj2->db_data["Ref"];
						$resultados->db_data["Preco1"]=$obj2->db_data["Preco1"];
						$resultados->db_data["Preco2"]=$obj2->db_data["Preco2"];
						$resultados->db_data["PrecoCusto"]=$obj2->db_data["PrecoCusto"];
						$resultados->db_data["ID_ORG"]=$obj2->db_data["ID_ORG"];
						$resultados->db_data["Importado"]=$obj2->db_data["Importado"];
						$resultados->db_data["id_fornecedor"]=$obj2->db_data["id_fornecedor"];
						$resultados->avanca();
					}
					$obj2->db_close();
					$count3++;
			}
			$count2++;
			$i++;
		}
		$num_palavras--;
	}
	$resultados->first();
	return $resultados;
}
function Pesquisa_backoffice_v1($pesquisa)
{
	global $count,$num,$produtos;
	$sucesso=0;
	if($pesquisa)
	{
		if(trim($pesquisa)!="" && strlen($pesquisa)>1)
		{
			$count=0;
			$num=0;
			$produtos[1][]="";
			$pesquisa=trim($pesquisa);
			$sucesso =Pesquisa_produtos_backoffice($pesquisa,1,"portfolio3",$cat);
			return gera_resultados($pesquisa);
		}
	}
}
function Pesquisa_backoffice($pesquisa,$tipo=1)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($tipo==1)
	{
		$sql ='SELECT p.*,pl.Cabecalho
		FROM portfolio3 p
			INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
		WHERE pl.Cabecalho="'.strtolower($pesquisa).'" AND pl.Lang="'.$admin_lang.'"
		ORDER BY pl.Cabecalho ASC';
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		if($rs->EOF())
		{
			$stringbooleanmode = searchIntoBooleanMode(" ","+",$pesquisa);
			$sql ='SELECT p.*,pl.Cabecalho,MATCH(pl.Cabecalho) AGAINST (\''.$stringbooleanmode.'\' IN BOOLEAN MODE) AS score
				FROM portfolio3 p
					INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
				WHERE MATCH(pl.Cabecalho) AGAINST (\''.$stringbooleanmode.'\' IN BOOLEAN MODE) AND pl.Lang="'.$admin_lang.'"
				ORDER BY score DESC,pl.Cabecalho ASC';
			$hRes=$rs->db_open($db_link,$DB_name,$sql);
			if($rs->EOF())
			{
				$sql ='SELECT p.*,pl.Cabecalho,MATCH(pl.Cabecalho) AGAINST (\''.strtolower($pesquisa).')\' IN BOOLEAN MODE) AS score
				FROM portfolio3 p
					INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
				WHERE MATCH(pl.Cabecalho) AGAINST (\''.strtolower($pesquisa).'\' IN BOOLEAN MODE) AND pl.Lang="'.$admin_lang.'"
				ORDER BY score DESC,pl.Cabecalho ASC';
				$hRes=$rs->db_open($db_link,$DB_name,$sql);
				if($rs->EOF())
				{
					$sql ='SELECT p.*,pl.Cabecalho
						FROM portfolio3 p
							INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
						WHERE pl.Cabecalho like "%'.strtolower($pesquisa).'%" AND pl.Lang="'.$admin_lang.'"
						ORDER BY pl.Cabecalho ASC';
					$hRes=$rs->db_open($db_link,$DB_name,$sql);
				}
			}
		}
	}
	elseif($tipo==2)
	{
		$sql ='SELECT p.*,pl.Cabecalho
		FROM portfolio3 p
			INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
		WHERE Ref="'.strtolower($pesquisa).'" AND pl.Lang="'.$admin_lang.'"
		ORDER BY pl.Cabecalho ASC';
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		if($rs->EOF())
		{
			$stringbooleanmode = searchIntoBooleanMode(" ","+",$pesquisa);
			$sql ='SELECT p.*,pl.Cabecalho,MATCH(Ref) AGAINST (\''.$stringbooleanmode.'\' IN BOOLEAN MODE) AS score
				FROM portfolio3 p
					INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
				WHERE MATCH(Ref) AGAINST (\''.$stringbooleanmode.'\' IN BOOLEAN MODE) AND pl.Lang="'.$admin_lang.'"
				ORDER BY score DESC,pl.Cabecalho ASC';
			$hRes=$rs->db_open($db_link,$DB_name,$sql);
			if($rs->EOF())
			{
				$sql ='SELECT p.*,pl.Cabecalho,MATCH(Ref) AGAINST (\''.strtolower($pesquisa).')\' IN BOOLEAN MODE) AS score
				FROM portfolio3 p
					INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
				WHERE MATCH(Ref) AGAINST (\''.strtolower($pesquisa).'\' IN BOOLEAN MODE) AND pl.Lang="'.$admin_lang.'"
				ORDER BY score DESC,pl.Cabecalho ASC';
				$hRes=$rs->db_open($db_link,$DB_name,$sql);
				if($rs->EOF())
				{
					$sql ='SELECT p.*,pl.Cabecalho
						FROM portfolio3 p
							INNER JOIN portfolio3_lang pl ON pl.ID_PAI=p.ID
						WHERE Ref like "%'.strtolower($pesquisa).'%" AND pl.Lang="'.$admin_lang.'"
						ORDER BY pl.Cabecalho ASC';
					$hRes=$rs->db_open($db_link,$DB_name,$sql);
				}
			}
		}
	}
	return $rs;
}
/*ALERTA STOCKS*/
function show_page_tools_stocks($filter=0)
{
?>
	<style>
		.text-center{text-align: center;}
	</style>
	<table width="100%" border="0" cellspacing=0 cellpadding=1 class="p1">
	<tr>
		<td width="70px" height="30" class="branco text-center">Nome</td>
		<td width="70px" height="30" class="branco text-center">Email</td>
		<!--td width="70px" height="30" class="branco text-center">Nº Pedidos</td-->
		<td width="100px" class="branco text-center">Referência</td>
		<td class="branco text-center">Nome</td>
		<td width="60px" class="branco text-center">PVP</td>
		<td width="60px" class="branco text-center">Stock</td>
		<td width="30px"></td>
	</tr>
	<tr>
		<td class="branco" align="center"></td>
		<td class="branco" align="center"></td>
		<!--td class="branco" align="center"><input type="text" name="f_npedidos" value="<?php echo $_GET["f_npedidos"]?>" style="height:20px; width:90%"></td-->
		<td class="branco" align="center"><input type="text" name="f_referencia" value="<?=$_GET["f_referencia"]?>" style="height:20px; width:90%"></td>
		<td class="branco" align="center"><input type="text" name="f_nome" id="f_nome" value="<?=$_GET["f_nome"]?>"  style="height:20px; width:90%"></td>
		<td class="branco" align="center"><input type="text" name="f_pvp" value="<?=$_GET["f_pvp"]?>" style="height:20px; width:90%" /></td>
		<td class="branco" align="center"><input type="text" name="f_stock" value="<?=$_GET["f_stock"]?>" style="height:20px; width:90%" /></td>
		<td colspan="2" align="right"><a title="pesquisar" href="javascript:;" onclick="javascript:document.forms['lista_paginas'].submit()"><img border="0" src="../layout/search.png" /></a></td>
	</tr>
<?php
}
function show_content_list_stocks()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagarm,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$and = '';
	if($_GET['f_npedidos']) $and.= ' AND NumPedidos="'.$_GET['f_npedidos'].'" ';
	if($_GET['f_referencia']) $and.= ' AND P.Ref like "%'.$_GET['f_referencia'].'%" ';
	if($_GET['f_nome']) $and.= ' AND PL.Cabecalho like "%'.$_GET['f_nome'].'%" ';
	if($_GET['f_pvp']) $and.= ' AND PO.obj_pvp="'.$_GET['f_pvp'].'" ';
	if($_GET['f_stock']) $and.= ' AND PO.obj_stock="'.$_GET['f_stock'].'" ';
	$porpagina=20;
	/*$sql='SELECT count(ast.ID) as NumPedidos,ast.ID_PROD,ast.Nome,ast.Email,P.Ref,PO.obj_pvp,PO.obj_stock,PL.Cabecalho
	FROM alerta_stock ast
		INNER JOIN portfolio3 P on P.ID=ast.ID_PROD
		INNER JOIN portfolio3_lang PL on PL.ID_PAI=ast.ID_PROD
		INNER JOIN portfolio3_objs PO on PO.obj_type_id=ast.ID_PROD
	where ast.ENVIADO=0 AND PO.obj_val=1 AND PL.Lang="'.$admin_lang.'" '.$and.' group by ast.ID_PROD DESC';
	*/
	$sql='SELECT ast.ID,ast.ID_PROD,ast.Nome,ast.Email,P.Ref,PO.obj_pvp,PO.obj_stock,PL.Cabecalho
	FROM alerta_stock ast
		INNER JOIN portfolio3 P on P.ID=ast.ID_PROD
		INNER JOIN portfolio3_lang PL on PL.ID_PAI=ast.ID_PROD
		INNER JOIN portfolio3_objs PO on PO.obj_type_id=ast.ID_PROD
	where ast.ENVIADO=0 AND PO.obj_val=1 AND PL.Lang="'.$admin_lang.'" '.$and.'';

	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	$total=$rs->db_rows;
	$rs->db_close();
	$n_pags=ceil($total / $porpagina); // num de paginas
	if(!isset($_GET['pagina'])) $pagina='1';
	else $pagina=$_GET['pagina'];
	$inicio=$pagina - 1;
	$inicio=$inicio * $porpagina;
	$sql2=$sql." LIMIT $inicio, $porpagina";
	$rs->db_open($db_link,$DB_name,$sql2);
	$i=0;$count=0;
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			//$NumPedidos=$rs->db_data['NumPedidos'];
			$ProdID=$rs->db_data["ID_PROD"];
			$p_ref = $rs->db_data['Ref'];
			$p_nome = stripslashes($rs->db_data['Cabecalho']);
			$p_pvp = $rs->db_data['obj_pvp'];
			$p_stock = $rs->db_data['obj_stock'];
			$p_Nome = $rs->db_data['Nome'];
			$p_Email = $rs->db_data['Email'];
			if($count % 2 != 0)	{$cor="#dedede";$class="r1";$n="1";}
			else				{$cor="#ffffff";$class="r2";$n="2";}
			$i=0;
			?>
				<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="border-bottom:solid 1px #999999">
					<td align="center" height="30"><?php echo $p_Nome; ?></td>
					<td align="center" height="30"><?php echo $p_Email; ?></td>
					<!--td align="center" height="30"><a class="preto" href="portfolio3.php?ID=<?php echo $ProdID?>" target="_blank"><?php echo $NumPedidos; ?></a></td-->
					<td align="center"><a class="preto" href="portfolio3.php?ID=<?php echo $ProdID?>" target="_blank"><?php echo $p_ref; ?></a></td>
					<td align="center"><a class="preto" href="portfolio3.php?ID=<?php echo $ProdID?>" target="_blank"><?php echo $p_nome; ?></a></td>
					<td align="center"><a class="preto" href="portfolio3.php?ID=<?php echo $ProdID?>" target="_blank"><?php echo numberformat($p_pvp); ?></a></td>
					<td align="center"><a class="preto" href="portfolio3.php?ID=<?php echo $ProdID?>" target="_blank"><?php echo $p_stock; ?></a></td>
					<td align="center"></td>
				</tr>
			<?
			$rs->db_next();
			$i++;
		}
		echo '</table>';
	}
	else
	{
		$class="r1";$n="1";
		?>
		<table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>" width="100%"><tr bgcolor="<?=$cor;?>"><td width="30px" align="center">Não foram encontrados pedidos de alerta de stock!</td></tr></table>
		<?php
	}
	$rs->db_close();
	if($n_pags > 1)
	{
		echo '<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table><table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0><tr><td class="t1" style="text-align:center" align="center"><table border=0 cellspacing=1 cellpadding=1 align="center"><tr>';
		$url='&f_npedidos='.$_GET['f_npedidos'].'&f_referencia='.$_GET['f_referencia'].'&f_nome='.$_GET['f_nome'].'&f_pvp='.$_GET['f_pvp'].'&f_stock='.$_GET['f_stock'];
		if($pagina >=8) $return .= '<td width="20" height="20" align="center" style="background:#CCFF33; color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'alerta_stock.php?pagina=1'.$url.'\',\'_self\')">primeira</td>';
		for($pi=($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			$ash='?pagina='.$pi;
			$pgn='alerta_stock.php'.$ash.$url;
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina== $pi ? '<td width="20" height="20" align="center" style="background:#FFF; color:#000;font-size:11px"><strong>'.sprintf(($pi)).'</strong></td>' : '<td width="20" height="20" align="center" style="background:#CCFF33; color:#000;cursor:pointer;font-size:11px" onclick="window.open(\''.$pgn.'\',\'_self\')"><u>'.sprintf($pi).'</u></td>' : '';
		}
		if($pagina !=$n_pags) $return .= '<td width="20" height="20" align="center" style="background:#CCFF33; color:#000;cursor:pointer;font-size:11px" onclick="window.open(\'alerta_stock.php?pagina='.$n_pags.$url.'\',\'_self\')">última</td>';
		echo $return;
		echo '</tr></table></td></tr></table>';
	}
	return 1;
}
function show_page_bottom_tools_stocks()
{
?>
	<img src="../images/bsh.png" width="890px">
<?
}
?>
