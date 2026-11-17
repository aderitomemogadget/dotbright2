<?php
function show_page_buttons()
{
	global $converter,$task_button,$category_id,$value_id,$Estado,$T;
	?>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?
			echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">&nbsp;';
			if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar"){?><input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><? }
			if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<? echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<? echo $category_id?>&task_form=apaga','_self')" /><? }
			if(isset($value_id)){?><input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('pagina.php?category_id=<? echo $category_id;?>','_self')" /><? }
			else{?><input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><? }?>
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{
	global $ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_categoria','Criar nova Categoria')?>" onclick="javascript:window.open('pagina.php?category_id=0&Dump=<? echo uniqid("");?>','_self')"></td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Associar Filtros" onclick="javascript:window.open('produtos_2.php','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">FILTROS</h4>
        </div>
    </div>
    <form action="list.php" method="get">
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
    <tr>
    	<td width=29 align="center"><? /*<input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1"  class="cb">*/?></td>
		<td width="10px" align="center"></td>
	    <td align="right" class="branco"><? echo write_lang('key_pesquisar_por_nome','Pesquisar por Nome')?>: <input style="width:150px;" type="text" name="pesquisa" id="pesquisa" value="<? echo $pesquisa?>">

                <input type="submit" name="Pesquisar" value="<? echo write_lang('key_pesquisar','Pesquisar')?>" style="width:70px;"></td>
    	<td width="5"></td>
    </tr>
    </table>
    </form>
<? }
function show_page_bottom_tools(){}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID,
	lower(Texto) as Descricao,length(lower(Texto)) AS len,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."'))/(length(lower(Texto))+1-length(replace(lower(Texto),' ','')) ) AS occwds,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."')) AS occ
	FROM menus_links_lang
	WHERE ((lower(Texto) LIKE '%".$d."%' OR URL_Nome like '%".$d."%') AND Lang='$admin_lang' ) ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);

	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_options($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia2" style="width:60%; height:16px">';
	if($id_org==0) echo '<option value="0" selected>- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
	else echo '<option value="0">- '.write_lang('key_nenhuma','Nenhuma').' -</option>';
    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$rs->db_data["ID"]. " and Lang='$admin_lang'";
        $rs2=new DataObj;
	    $db_res=$rs2->db_open($db_link,$DB_name,$sql);
		echo '>';
		if(!$rs2->db_data["URL_Nome2"]) echo "N/A";else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
    echo '</select>&nbsp;';
    db_closedb_conection($db_link);
}
function show_options_templates($template=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='Select '.$Nomeb.'.ID, '.$Nomeb_linguas.'.Cabecalho from '.$Nomeb.' inner join '.$Nomeb_linguas.' on '.$Nomeb_linguas.'.ID_PAI = '.$Nomeb.'.ID where Visivel=1 and Lang="'.$admin_lang.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	$template = explode(';', $template);
	while(!$rs->EOF())
    {
		echo '<input type="checkbox" value="1" name="template_id'.$rs->db_data["ID"].'"';
		if(in_array($rs->db_data["ID"],$template)){ echo ' checked="checked" ';}
		echo' />&nbsp;'.$rs->db_data["Cabecalho"].'&nbsp;&nbsp;';
        $rs->db_next();
    }
    $rs->db_close();
    db_closedb_conection($db_link);
}
function show_optionsb($id_org=0,$id_first=0,$Nomeb,$Nomeb_linguas)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
    {
        echo '<option value="'. $rs->db_data["ID"] .'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$rs->db_data["ID"]. " and Lang='$admin_lang'";
        $rs2=new DataObj;
	    $db_res=$rs2->db_open($db_link,$DB_name,$sql);
		echo '>';
		if(!$rs2->db_data["URL_Nome2"]) echo "N/A";else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas);
        $rs->db_next();
    }
    $rs->db_close();
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang) $m_lang = $admin_lang;





	$category_type = $_POST['category_type'];
	$category_name = addslashes($_POST['category_name']);
	$category_name2 = addslashes($_POST['category_name2']);
	$category_order = $_POST['category_order'];
	$category_active = $_POST['category_active'];
	$category_active2 = $_POST['category_active2'];
	$sql = 'INSERT INTO filters_categories(category_type,category_order,category_active,category_active2) VALUES ("'.$category_type.'","'.$category_order.'","'.$category_active.'","'.$category_active2.'")';
	$res = Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);

		$sql = 'INSERT INTO filters_categories_lang(category_id,category_name,category_name2,category_lang) VALUES ("'.$new_id.'","'.$category_name.'","'.$category_name2.'","'.$m_lang.'")';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewRecordValues()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang) $m_lang = $admin_lang;

	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$img_path = "../../files/filters/";
	if($Img_peq1["name"]){$altera_img = 1; $pos = strrpos($Img_peq1["name"],"."); $img_ext = substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);	}

	$category_id = $_POST['category_id'];
	$values_name = addslashes($_POST['values_name']);
	$values_name2 = addslashes($_POST['values_name2']);
	$values_colorhex = addslashes($_POST['values_colorhex']);
	$value_order = $_POST['value_order'];
	$value_active = $_POST['value_active'];
	$value_id_import_ref = $_POST['value_id_import_ref'];
	$sql = 'INSERT INTO filters_values(category_id,value_order,value_active, value_ext, value_id_import_ref) VALUES ("'.$category_id.'","'.$value_order.'","'.$value_active.'","'.$img_ext.'","'.$value_id_import_ref.'")';
	$res = Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);

		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);

		$sql = 'INSERT INTO filters_values_lang(values_id,values_name,values_name2,values_lang,values_colorhex, value_id_import_ref) VALUES ("'.$new_id.'","'.$values_name.'","'.$values_name2.'","'.$m_lang.'","'.$values_colorhex.'","'.$value_id_import_ref.'")';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$category_id = $_GET['category_id'];
    $sql="delete from filters_categories where category_id=$category_id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="delete from filters_categories_lang where category_id=$category_id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="delete from filters_products where cat_id=$category_id";
	$res=Runquery($db_link,$DB_name,$sql);

	if(!$res) echo db_error($db_link);
	return($res);
}
function DeleteRecordValues()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$value_id = $_GET['value_id'];
    $sql="delete from filters_values where value_id=$value_id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="delete from filters_values_lang where values_id=$value_id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="delete from filters_products where valor_id=$value_id";
	$res=Runquery($db_link,$DB_name,$sql);

	if(!$res) echo db_error($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	if(!$m_lang) $m_lang = $admin_lang;
	$category_id = $_GET['category_id'];
	$category_type = $_POST['category_type'];
	$category_name = addslashes($_POST['category_name']);
	$category_name2 = addslashes($_POST['category_name2']);
	$category_order = $_POST['category_order'];
	$category_active = $_POST['category_active'];
	$category_active2 = $_POST['category_active2'];
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from filters_categories WHERE category_id='$category_id'");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera) return InsertNewRecord();
	else
	{
		$sql="UPDATE filters_categories SET category_type='$category_type', category_order='$category_order',category_active='$category_active' ,category_active2='$category_active2' where category_id=$category_id";
		$res = Runquery($db_link,$DB_name,$sql);
		$dados_tab = verificaExisteLingua("filters_categories_lang",$m_lang,$category_id);
		if($dados_tab->EOF()) $sql = "INSERT INTO filters_categories_lang(category_id,category_name,category_name2,category_lang) VALUES ('$category_id','".$category_name."','".$category_name2."','$m_lang')";
		else $sql="update filters_categories_lang set  category_name='".$category_name."', category_name2='".$category_name2."' where category_id='".$dados_tab->db_data['category_id']."' and category_lang='".$m_lang."'";
		$res = Runquery($db_link,$DB_name,$sql);

		db_closedb_conection($db_link);
		return($res);
	}
}
function verificaExisteLingua($tabela,$lang,$id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj_tab = new DataObj;
	$sql="Select * from ".$tabela." where category_id='".$id."' and category_lang = '".$lang."'";
	$obj_tab->db_open($db_link,$DB_name,$sql);
	return $obj_tab;
}
function ChangeRecordValues()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;


	$Img_peq1["name"] = $_FILES['img_peq1']['name'];
	$Img_peq1["temp"] = $_FILES['img_peq1']['tmp_name'];
	$img_path = "../../files/filters/";


	$tem_foto1 		= intval($_POST['tem_foto1']);
	$Apagar_img1 	= intval($_POST['Apagar_img1']);

	if(!$m_lang) $m_lang = $admin_lang;
	$value_id = $_GET['value_id'];
	$category_id = $_POST['category_id'];
	$values_name = addslashes($_POST['values_name']);
	$values_name2 = addslashes($_POST['values_name2']);
	$values_colorhex = addslashes($_POST['values_colorhex']);
	$value_order = $_POST['value_order'];
	$value_active = $_POST['value_active'];
	$value_id_import_ref = $_POST['value_id_import_ref'];
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from filters_values WHERE value_id='$value_id'");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["value_ext"];
	}
	$rs->db_close();
	if(!$altera) return InsertNewRecord();
	else
	{
		$id = $value_id;
		$altera_img=0; if($Img_peq1["name"]){ $altera_img=1; $pos = strrpos($Img_peq1["name"],"."); $img_ext = substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos); }
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1 = $id."_1.".$img_ext;unlink($img_path.$ficheiro1); if(!$altera_img) $img_ext='';}
		if($altera_img) copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);


		$sql="UPDATE filters_values SET category_id='$category_id', value_order='$value_order',value_active='$value_active', value_ext='".$img_ext."', value_id_import_ref='".$value_id_import_ref."' where value_id=$value_id";
		$res = Runquery($db_link,$DB_name,$sql);
		$dados_tab = verificaExisteLinguaValues("filters_values_lang",$m_lang,$value_id);
		if($dados_tab->EOF()) $sql = "INSERT INTO filters_values_lang(values_id,values_name,values_name2,values_lang,values_colorhex,value_id_import_ref) VALUES ('$value_id','".$values_name."','".$values_name2."','$m_lang','".$values_colorhex."','".$value_id_import_ref."')";
		else $sql="update filters_values_lang set  values_name='".$values_name."',values_name2='".$values_name2."',values_colorhex='".$values_colorhex."',value_id_import_ref='".$value_id_import_ref."' where values_id='".$dados_tab->db_data['values_id']."' and values_lang='".$m_lang."'";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function verificaExisteLinguaValues($tabela,$lang,$id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj_tab = new DataObj;
	$sql="Select * from ".$tabela." where values_id='".$id."' and values_lang = '".$lang."'";
	$obj_tab->db_open($db_link,$DB_name,$sql);
	return $obj_tab;
}
function GetRecords_Recursos($id=0,$id_org=0,$campos="*",$first=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0)
	{
		$cond="where ID=$id";
		if($id_org!=0 || $first!=0)	$cond="$cond and ID_ORG=$id_org";
	}
	else
	{
		$cond="where ID > 0";
		if($id_org!=0 || $first!=0)	$cond=$cond." and ID_ORG=$id_org";
	}
    $sql="Select $campos from menus_links $cond order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function show_menupath($id_org,$table="menus_links",$pagina="pagina.php")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT * FROM $table WHERE ID=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];
		$rs->db_close();
		$sql="SELECT * FROM $table"."_lang WHERE ID_PAI=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["URL_Nome2"]);
		$C1=$C!="" ? $C : "[EDIT]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1);
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&Dumpe=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$porpagina = 20;

	$contador += 5;
	$rs=new DataObj;$rs2=new DataObj;
	if(isset($_GET['pesquisa']))$search = ' AND fl.category_name like "%'.$_GET['pesquisa'].'%" ';
	$sql = '
			SELECT
				f.*,fl.category_name, fl.category_name2
			FROM
				filters_categories as f
				INNER JOIN filters_categories_lang as fl ON f.category_id=fl.category_id
			WHERE
				fl.category_lang="'.$GLOBALS['admin_lang'].'"
				'.$search.'
				GROUP By f.category_id
			ORDER BY
				f.category_order ASC,fl.category_name ASC
			';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$total=$rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / $porpagina); // n de paginas
	if(!isset($_GET['pagina'])) $pagina = '1';
	else $pagina = $_GET['pagina'];
	$inicio = $pagina - 1;
	$inicio = $inicio * $porpagina;
	$sql2=$sql." LIMIT $inicio, $porpagina";
	// echo $sql2;


	if($n_pags > 1)
	{
		$return = '<div class="clear" style="height:10px"></div><table border=0 cellspacing=5 cellpadding=5 style="margin-left:auto;margin-right:auto"><tr>';

		 $p_seo = 'list.php?ID='.$_GET['ID'].'&ID_ORG='.$_GET['ID_ORG'].'&pesquisa='.$_GET['pesquisa'];

		if($pagina > 6)
		{
			if(substr_count($p_seo, '?')) $ash = '&pagina=1';	else $ash = '?pagina=1';
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<td width="2px"></td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597">1</td><td width="2px">...</td>';
		}
		for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
		{

			if(substr_count($p_seo, '?')) $ash = '&pagina='.$pi;	else $ash = '?pagina='.$pi;
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px"></td><td width="20" height="15" bgcolor="#006d9f" align="center" style="font-size:10px;color:#FFF;padding-top:2px;border:#939597 1px solid" >'.sprintf(($pi)).'</td>' : '<td width="2px"></td><td width="20" height="15" bgcolor="#edf4f9" onmouseover="this.style.background=\'#006d9f\';this.style.color=\'#FFFFFF\'" onmouseout="this.style.background=\'#edf4f9\';this.style.color=\'#939597\'" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;margin:2px;color:#939597" >'.sprintf($pi).'</td>' :'';
		}
		if($n_pags!=$pagina)
		{

			if(substr_count($p_seo, '?')) $ash = '&pagina='.$n_pags;	else $ash = '?pagina='.$n_pags;
			$pgn = 'onclick=window.open("'.$p_seo.$ash.'","_self")';
			$return .= '<td width="2px"></td><td width="2px">...</td><td width="20" height="15" bgcolor="#ffffff" align="center" '.$pgn.' style="font-size:10px;cursor:pointer;padding-top:2px;border:#939597 1px solid;color:#939597" >'.sprintf($n_pags).'</td>';
		}
		$return .= '</tr></table><br>';
	}

	$rs->db_open($db_link,$DB_name,$sql2);
	$i=0;
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			$category_id 	= $rs->db_data["category_id"];
			$category_type  = $rs->db_data["category_type"];
			$category_order = $rs->db_data["category_order"];
			$category_active = $rs->db_data["category_active"];
			$category_name = $rs->db_data["category_name"];
			$category_name2 = $rs->db_data["category_name2"];

			if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#fff";$class="r2";$n="2";}

			$i=0;
			?>
				<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<? echo $class?>">
				<tr bgcolor="<? echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
				<td width="10">&nbsp;</td>
				<td valign="middle" width="200"><? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?category_id=<? echo $category_id;?>&Dump=<? echo uniqid("");?>"><? if($category_name){echo htmlentities_b(stripslashes($category_name));} else echo "[Edit]";?></a></td>
				<td valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?category_id=<? echo $category_id;?>&Dump=<? echo uniqid("");?>"><? if($category_name2){echo htmlentities_b(stripslashes($category_name2));}?></a></td>
				<td width="35" style="text-align:left"><img  src="<? if($category_active) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
				<td width="30" valign="middle"><a title="Remover" class="lista_links" href="pagina.php?category_id=<? echo $category_id;?>&task=apagar&Dump=<? echo uniqid("");?>"><img alt="" src="del2.png" border="0" align="absmiddle" /></a></td>
                <td width="30" align="center" ><? echo $category_order;?></td>
				</tr>
				</table>
			<?
			$rs->db_next();
			$i++;
		}

		echo $return;
	}
	else
	{
	?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem Filtros</td>
		</tr>
		</table>
	<?
	}
	$rs->db_close();
	?><img src="../images/bsh.png" width="100%" height="30px"><?
}
function show_content_list2($pai=0,$contador=-5,$count=0,$md=0,$span=0,$category_type=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador += 5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql = '
			SELECT
				f.*,fl.values_name,fl.values_name2,fl.values_colorhex
			FROM
				filters_values as f
				INNER JOIN filters_values_lang as fl ON f.value_id=fl.values_id
			WHERE
				fl.values_lang="'.$GLOBALS['admin_lang'].'"
				AND
				f.category_id="'.$pai.'"
			GROUP BY f.value_id
			order by value_order asc,values_name asc
			';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			$value_id 		= $rs->db_data["value_id"];
			$value_active 	= $rs->db_data["value_active"];
			$value_order 	= $rs->db_data["value_order"];
			$value_name 	= $rs->db_data["values_name"];
			$values_name2 	= $rs->db_data["values_name2"];
			$values_colorhex = str_replace('#', '', $rs->db_data["values_colorhex"]);
			$I1				= $rs->db_data["value_ext"];
			if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#fff";$class="r2";$n="2";}
			$i=0;
			?>
				<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<? echo $class?>">
				<tr bgcolor="<? echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<? echo $cor;?>'">
				<td width="5%" align="center"><? echo $value_order;?></td>
				<td width="5%" style="text-align:left"><img  src="<? if($value_active) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
				<td width="5%" style="text-align:left"><?
				if($I1!="")
				{
					?><img src="<?php echo "../../files/filters/".$value_id."_1.".$I1;?>" width="30"><?
				}
				elseif($category_type == 2){ echo '<div style="background:#'.$values_colorhex.';width:30px;height:30px">&nbsp;</div>';}
				?>
				</td>
				<td width="200" valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina_values.php?value_id=<? echo $value_id;?>&category_id=<? echo $pai;?>&Dump=<? echo uniqid("");?>"><? if($value_name){echo htmlentities_b(stripslashes($value_name));} else echo "[Edit]";?></a></td>
				<td valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina_values.php?value_id=<? echo $value_id;?>&category_id=<? echo $pai;?>&Dump=<? echo uniqid("");?>"><? if($values_name2){echo htmlentities_b(stripslashes($values_name2));} else echo "[Edit]";?></a></td>
				<td width="5%" valign="middle"><?php if($category_type == 2){ echo $values_colorhex;}?></td>
				<td width="5%" valign="middle"><a title="Remover" class="lista_links" href="pagina_values.php?value_id=<? echo $value_id;?>&task=apagar&Dump=<? echo uniqid("");?>"><img alt="" src="del2.png" border="0" align="absmiddle" /></a></td>
				</tr>
				</table>
			<?
			$rs->db_next();
			$i++;
		}
	}
	else
	{
	?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não existem valores associados a este filtro</td>
		</tr>
		</table>
	<?
	}
	$rs->db_close();
}
function gera_lista_filtros($id_org=0,$tabela1,$tabela1_lang)
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
	    gera_lista_subfiltros($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfiltros($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=5)
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
	    gera_lista_subfiltros($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+5);
        $obj->db_next();
    }
    $obj->db_close();
}
function gera_lista_categorias($id_org)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador += 5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql = '
			SELECT
				f.*,fl.category_name
			FROM
				filters_categories as f
				INNER JOIN filters_categories_lang as fl ON f.category_id=fl.category_id
			WHERE
				fl.category_lang="'.$GLOBALS['admin_lang'].'"
			ORDER BY
				f.category_type ASC
			';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	echo '<select name="category_id">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$count++;
			$category_id 	= $rs->db_data["category_id"];
			$category_type  = $rs->db_data["category_type"];
			$category_order = $rs->db_data["category_order"];
			$category_active = $rs->db_data["category_active"];
			$category_name = $rs->db_data["category_name"];
			echo '<option value="'.$category_id.'"';
			if($category_id==$id_org) echo ' selected ';
			echo '>'. $category_name.'</option>';
			$rs->db_next();
			$i++;
		}
	}
	$rs->db_close();
}
?>