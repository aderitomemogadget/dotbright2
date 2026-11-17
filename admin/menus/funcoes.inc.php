<?php
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br>
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
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_novo_link_de_menu','Criar Novo Link de Menu')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr><?
	if($ID){?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_novo_sub_link_de_menu','Criar Novo Sub-link de Menu')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
    <?
	}
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_translate_tools()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	global $ID,$ID_ORG;
	?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>

		<td class="titulo2" align="center">TRANSLATE TOOLS</td>

	</tr><?

	$sql="Select * from linguas";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Sigla=$rs->db_data["Sigla"];
		?>
		<tr>

			<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Traduzir para <?=$Sigla?>" onclick="javascript:window.open('pagina.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&translate_to=<?=$Sigla?>&d=<?=uniqid("");?>','_self')"></td>

		</tr><?
		$rs->db_next();
	}
	$rs->db_close();
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MENUS</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><?php echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<?php echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" class="btn btn-primary" ></td>
        <td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<!-- <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table> -->
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="form-control">
				<option value="" selected="selected"></option>
				<option value="1"><?php echo write_lang('key_apagar','Apagar')?></option>
				<option value="2"><?php echo write_lang('key_reordenar','Reordenar')?></option>
				<option value="3"><?php echo write_lang('key_mover_para_a_familia','Mover para a familia')?></option>
				<option value="4"><?php echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><?php echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_options(0,0,"menus_links","menus_links_lang");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?php
}
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
function show_options_visivel($logado=0,$id_first=0,$Nomeb='',$Nomeb_linguas='')
{

    echo '<select name="Logado" style="width:60%" class="form-control">';
	echo '<option value="0"'; if($logado==0) echo 'selected="selected"'; echo '>Todos</option>';
	echo '<option value="1"'; if($logado==1) echo 'selected="selected"'; echo '>Não logados</option>';
	echo '<option value="2"'; if($logado==2) echo 'selected="selected"'; echo '>Logados</option>';
    echo '</select>';

}
function show_options($id_org=0,$id_first=0,$Nomeb='',$Nomeb_linguas='', $space='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from $Nomeb where ID_ORG='$id_first' order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia2" style="width:60%" class="form-control">';
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
		if(!$rs2->db_data["URL_Nome2"] && $rs->db_data["URL_Nome"]) echo $rs->db_data["URL_Nome"].' (Sem titulo)';elseif(!$rs2->db_data["URL_Nome2"]) echo "N/A"; else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas,' - - ');
        $rs->db_next();
    }
    $rs->db_close();
    echo '</select>';
    db_closedb_conection($db_link);
}
function show_options_templates($template=0,$Nomeb='',$Nomeb_linguas='')
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
function show_optionsb($id_org=0,$id_first=0,$Nomeb='',$Nomeb_linguas='',$space='')
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
		echo '>'.$space;
		if(!$rs2->db_data["URL_Nome2"] && $rs->db_data["URL_Nome"]) echo $rs->db_data["URL_Nome"].' (Sem titulo)';elseif(!$rs2->db_data["URL_Nome2"]) echo "N/A"; else echo $rs2->db_data["URL_Nome2"];
		echo '</option>';
	    $rs2->db_close();
	    show_optionsb($id_org,$rs->db_data["ID"],$Nomeb,$Nomeb_linguas, $space.' - - ');
        $rs->db_next();
    }
    $rs->db_close();
}
function InsertNewRecord($id_org,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$tipo,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$template_id,$novajanela,$catalogo_id,$TipoMenu,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1["name"]){$altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos);}
	$altera_img2=0;if($Img_peq2["name"]){$altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos);}
	$data=date("Y-m-d");
	$video = $_POST['video'];
	$media = $_POST['media'];
	$class = $_POST['class'];
	$tipo_catalogo = $_POST['tipo_catalogo'];
	$sql="insert into menus_links(class, tipo_catalogo, media, video, ID_ORG,Data,Ordem,Visivel,URL_Nome,URL_Link,Tipo,Largura,Altura,img_ext,img2_ext,template_id,novajanela,catalogo_id,TipoMenu,Indexar,Logado,menu_icon_fa) values ('$class','$tipo_catalogo','$media','$video',$id_org,'$data','$ordem','$visivel','$url_nome','$url_link','$tipo','$largura','$altura','$img_ext','$img2_ext','$template_id','$novajanela','$catalogo_id','$TipoMenu','$Indexar','$Logado','$menu_icon_fa')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id = db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"], $img_path.$new_id."_1.".$img_ext);
		if($altera_img2) copia_segura($Img_peq2["temp"], $img_path.$new_id."_2.".$img2_ext);
		InsertNewRecordLinguas($new_id,$url_nome2,$m_lang,$texto_icon_fa);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return array('res' => $res, 'ID' => $new_id);
}
function InsertNewRecordLinguas($id_pai,$url_nome2,$m_lang,$texto_icon_fa)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="insert into menus_links_lang (ID_PAI,URL_Nome2,Lang,texto_icon_fa) values ($id_pai,'$url_nome2','$m_lang','$texto_icon_fa')";
	$res=Runquery($db_link,$DB_name,$sql);

	Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_m');//limpar a tabela de ram
	Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_lang_m');//limpar a tabela de ram

	if(!$res)echo db_error($db_link);
    return($res);
}
function DeleteRecord($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=GetRecords_Recursos(0,$id);
	while(!$rs->EOF())
	{
		$codigo=htmlentities_b($rs->db_data["ID"]);
		DeleteRecord($codigo);
		$rs->db_next();
	}
	$rs->db_close();
    $sql="delete from menus_links where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
        $sql2="delete from menus_links_lang where ID_PAI=$id";
	    $res2=Runquery($db_link,$DB_name,$sql2);

		Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_m');//limpar a tabela de ram
		Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_lang_m');//limpar a tabela de ram
	}
	else echo db_error($db_link);
	return($res);
}
function ChangeRecord($id,$id_org,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$tipo=0,$m_lang,$largura,$altura,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$Img_peq2,$Apagar_img2,$tem_foto2,$Img_peq3,$Apagar_img3,$tem_foto3,$template_id=0,$novajanela,$catalogo_id,$TipoMenu,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa,$apagar_fa_icon)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;

	if($apagar_fa_icon=='on' || $apagar_fa_icon==1)
	{
		$texto_icon_fa='';
		$menu_icon_fa='';
	}
	$rs->db_open($db_link,$DB_name,"Select * from menus_links WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
		$img2_ext=$rs->db_data["img2_ext"];
		$img3_ext=$rs->db_data["img3_ext"];
	}
	$rs->db_close();
	if(!$altera) return InsertNewRecord($id_org,$ordem,$visivel,$url_nome,$url_nome2,$url_link,$m_lang,$tipo,$largura,$altura,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$template_id,$novajanela,$catalogo_id,$Indexar,$Logado,$menu_icon_fa,$texto_icon_fa);
	else
	{
		$altera_img=0; if($Img_peq1["name"]){ $altera_img=1;$pos=strrpos($Img_peq1["name"],".");$img_ext=substr($Img_peq1["name"],$pos+1,strlen($Img_peq1["name"])+1-$pos); }
		$altera_img2=0; if($Img_peq2["name"]){ $altera_img2=1;$pos=strrpos($Img_peq2["name"],".");$img2_ext=substr($Img_peq2["name"],$pos+1,strlen($Img_peq2["name"])+1-$pos); }
		$altera_img3=0; if($Img_peq3["name"]){ $altera_img3=1;$pos=strrpos($Img_peq3["name"],".");$img3_ext=substr($Img_peq3["name"],$pos+1,strlen($Img_peq3["name"])+1-$pos); }

		// IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1 = $id."_1.".$img_ext;unlink($img_path.$ficheiro1); if(!$altera_img)$img_ext='';}	if($altera_img) copia_segura($Img_peq1["temp"], $img_path.$id."_1.".$img_ext);
		if($tem_foto2 && $Apagar_img2){$ficheiro2 = $id."_2.".$img2_ext;unlink($img_path.$ficheiro2); if(!$altera_img2)$img2_ext='';}	if($altera_img2) copia_segura($Img_peq2["temp"], $img_path.$id."_2.".$img2_ext);
		if($tem_foto3 && $Apagar_img3){$ficheiro3 = $id."_3.".$img3_ext;unlink($img_path.$ficheiro3); if(!$altera_img3)$img3_ext='';}	if($altera_img3) copia_segura($Img_peq3["temp"], $img_path.$id."_3.".$img3_ext);

		$video = $_POST['video'];
		$media = $_POST['media'];
		$class = $_POST['class'];
		$tipo_catalogo = $_POST['tipo_catalogo'];
		$data2 = date("Y-m-d");
		$sql = "UPDATE menus_links SET class='$class',tipo_catalogo='$tipo_catalogo',media='$media',video='$video',Logado='$Logado',ID_ORG='$id_org',Data_modify='$data2',Ordem='$ordem',Visivel='$visivel',URL_Nome='$url_nome',URL_Link='$url_link',Tipo='$tipo',img_ext='$img_ext',img2_ext='$img2_ext',img3_ext='$img3_ext',Largura='$largura',Altura='$altura',template_id='$template_id',novajanela='$novajanela',catalogo_id='$catalogo_id',TipoMenu='$TipoMenu',Indexar='$Indexar',menu_icon_fa='$menu_icon_fa' where ID=$id";

		$res = Runquery($db_link,$DB_name,$sql);
		ChangeRecordLinguas($id,$url_nome2,$m_lang,$texto_icon_fa);
		db_closedb_conection($db_link);
		return($res);
	}
}
function ChangeRecordLinguas($id_pai,$url_nome2,$m_lang,$texto_icon_fa)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from menus_links_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();
	if(!$altera)return InsertNewRecordLinguas($id_pai,$url_nome2,$m_lang,$texto_icon_fa);
	else
	{
		$sql="Update menus_links_lang Set URL_Nome2='$url_nome2',texto_icon_fa='$texto_icon_fa' where ID_PAI='$id_pai' and Lang='$m_lang'";
		$res=Runquery($db_link,$DB_name,$sql);

		Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_m');//limpar a tabela de ram
		Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS menus_links_lang_m');//limpar a tabela de ram

		return($res);
	}
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
	else $str.="<a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0, $hide=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	if($tipos==1) $sql="Select * from menus_links where Tipo=0 and ID_ORG='".$pai."' order by Ordem"; /// Paginas publicas
	elseif($tipos==2) $sql="Select * from menus_links where Tipo=1 and ID_ORG='".$pai."' order by Ordem"; /// Paginas privadas
	elseif($tipos==3) $sql="Select * from menus_links where Tipo=2 and ID_ORG='".$pai."' order by Ordem"; /// noticias
	else $sql="Select * from menus_links where ID_ORG='".$pai."' order by Ordem";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Visivel=$rs->db_data["Visivel"];
		$Protected=$rs->db_data["Protected"];
		$ordem=$rs->db_data["Ordem"];
		$tipo=$rs->db_data["Tipo"];
		$I3=$rs->db_data["img3_ext"];
		$descricao_geral = ''; $descricao_geral = ($rs->db_data["URL_Nome"]!='' ? '<span style="color:#666">'.$rs->db_data["URL_Nome"].' - <span style="font-size:10px">(SEM TÍTULO)</span></span>' : '');

		$sql = "SELECT URL_Nome2, lang FROM menus_links_lang WHERE ID_PAI='".$IDb."' AND lang='$admin_lang'";
		$rs2->db_open($db_link,$DB_name,$sql);
		$descricao = '';
		if(!preg_match('#(?<=<)\w+(?=[^<]*?>)#', $rs2->db_data["URL_Nome2"])) $descricao = $rs2->db_data["URL_Nome2"];
		$descricao = ($descricao!='' ? $descricao : $descricao_geral);
		$lingua_c=$rs2->db_data["lang"];
		$rs2->db_close();

		if($lingua_c=='')
		{
			// PARA OBTER A LINGUA DO MENU CRIADO
			$sql="SELECT lang FROM menus_links_lang WHERE ID_PAI='".$IDb."'";
			$rs2->db_open($db_link,$DB_name,$sql);
			$lingua_c=$rs2->db_data["lang"];
			$rs2->db_close();
		}
		if(!$descricao) $descricao = "N/A";

		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<?php 
			
			if($hide==0) { ?> 
				<td width="29px" align="center">
					<input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" />
				</td>
		<?php 
			} ?>

			<td width="25" onclick="if($('#mn<?php echo $IDb?>').is(':visible')){$('#mn<?php echo $IDb?>').hide();}else{$('#mn<?php echo $IDb?>').show();}" style="cursor:pointer"><?php

				$tem_sub_menu = 0;
				$rs2=new DataObj;
				$sql="Select * from menus_links where ID_ORG=".$rs->db_data["ID"];
				$db_res=$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF()){ $tem_sub_menu = 1;?><img alt="<?php echo write_lang('key_ver_mais_paginas','Ver mais páginas')?>" src="../images/<? if($pai==0) echo "more.gif"; else echo "more.gif";?>" border=0><? }
				else echo '<img width="22" height="8" src="../images/blank.gif" />';
				$rs2->db_close();?>
			</td>
            <td valign="middle"><? while($i<$contador){echo "-";$i++;}?><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? 
            
				
				

            	if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";
            
            ?></a></td>

			<td align="center" width="35"><?php 
				if($I3!="" && file_exists("../../files/menus/".$IDb."_3.".$I3))
				{
					if(file_exists("../../files/menus/$IDb"."_3.".$I3)){?><img style="width:25px;height:25px;float:left" alt="" src="<?php echo "../../files/menus/$IDb"."_3.".$I3.'?d='.uniqid('');?>"><? }
				}
			?></td>
			<td align="center" width="35"><img title="<? if($Visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "imgs/ok.png"; else echo "imgs/hidden.png";?>" border=0 align="absmiddle" /></td>
			<td width="30" align="center" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="imgs/duplicate.png" border=0></a></td>
			<td width="30" align="center" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Página')?>" src="imgs/del2.png" border=0></a><? }else echo '<img title="" src="imgs/locked.png"  border=0 align="absmiddle" />'; ?></td>
			<td width="30" align="center" class=preto>(<?php echo $lingua_c?>)</td>
			<?php if($hide==0){?>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>" onchange="$('#chk_<?php echo $IDb?>').prop('checked', true);" /></td><?php }?>
		</tr>
		<?php if($tem_sub_menu){?>
		<tr bgcolor="<?=$cor;?>">
		<td width="29"></td>
		<td colspan="10">
			<div style="display:none" id="mn<?php echo $IDb?>">
			<?php
			show_content_list($IDb,$contador,$count,$md,$span,$hide);
			?>
			</div>
		</td>
		</tr>
		<?php }?>
		</table><?
		// show_content_list($IDb,$contador,$count,$md,$span);
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs_lang=new DataObj;
	$sql="Select distinct(menus_links.ID) as IDb,menus_links.*,menus_links_lang.URL_Nome,menus_links_lang.Texto from menus_links inner join menus_links_lang on menus_links.ID=menus_links_lang.ID_PAI where ID_ORG='".$pai."' and lang='$admin_lang' AND ID_PAI IN($reg) order by Ordem,URL_Nome ASC";
	$rs_lang->db_open($db_link,$DB_name,$sql);
	$count=0;
	while(!$rs_lang->EOF())
	{	$count++;
		$IDb=$rs_lang->db_data["IDb"];
		$id_org=$rs_lang->db_data["ID_ORG"];
		$id_pai=$rs_lang->db_data["ID_PAI"];
		$Visivel=$rs_lang->db_data["Visivel"];
		$ordem=$rs_lang->db_data["Ordem"];
		$tipo=$rs_lang->db_data["Tipo"];
		$descricao=$rs_lang->db_data["URL_Nome"];
		$Protected=$rs_lang->db_data["Protected"];
		$T=$rs_lang->db_data["Texto"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
	?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" /></td>
		
        <td width="29" align="center"><?
			if($tipo==0) echo '<img src="../images/page.png" />';
			if($tipo==1) echo '<img src="../images/page_priv.png" />';
			if($tipo==2) echo '<img src="../images/page_news.png" />';

			?></td>
		<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? if($Visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "imgs/ok.png"; else echo "imgs/hidden.png";?>" border=0 align="absmiddle" /></td>
            <td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Edit]";?></a></td>
			<td width="72" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_editar_pagina','Duplicar Página')?>" src="imgs/duplicate.png" border=0></a></td>
			<td width="100" align="right" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Página')?>" src="imgs/del2.png" border=0 /></a><? }else echo '<img title="" src="imgs/locked.png"  border=0 align="absmiddle" />'; ?></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
	  </tr>
	  </table><?
	  $rs_lang->db_next();
  }
  $rs_lang->db_close();
}
?>