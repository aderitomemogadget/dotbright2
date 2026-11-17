<?
function show_page_buttons()
{
	global $task_button,$ID;
	?>
	<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
			<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
            <input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onclick="window.open('list.php','_self')">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890">
<?
}
function show_fast_tools()
{?>
	<table width="200" border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td height="27">&nbsp;</td>
	</tr>
    </table>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5" align="left"><img src="../images/s2.jpg" /></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5" align="right"><img src="../images/s2.jpg" /></td>
	</tr>
	<tr>
		<td width="5" align="left"><img src="../images/s2.jpg" /></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="Create New Site Template" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5" align="right"><img src="../images/s2.jpg" /></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200" height="30" />
<? }
function show_page_tools($filter=0)
{?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb" /></td>
		<td width="10" align="center"></td>
		<td width="91" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="130" align="right"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa');?>" class="btn btn-primary"  /></td>
        <td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><? echo write_lang('key_apagar','Apagar')?></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
			</select>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px" /></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890">
<?
}
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select distinct(templates_site.ID) as IDb,templates_site.*,templates_site_lang.Cabecalho from templates_site inner join templates_site_lang on templates_site.ID=templates_site_lang.ID_PAI where lang='$admin_lang' order by Ordem,Cabecalho ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Tipo=$rs->db_data["Tipo"];
		$descricao=$rs->db_data["Cabecalho"];
		$Visivel=$rs->db_data["Visivel"];
		$ordem=$rs->db_data["Ordem"];
		$protegido=$rs->db_data["Protected"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
            <td width="29" align="center"><img src="../images/page.png"></td>
            <td valign="middle"><a title="<? echo write_lang('key_aplicar_acao','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>

            <td width="35"><img title="<? if($Visivel) echo write_lang('key_aplicar_acao','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_duplicar_ambiente','Duplicar Ambiente')?>" src="duplicate.png" border=0></a></td>
			<? if($protegido==0){?><td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_ambiente','Apagar Ambiente')?>" src="del2.png" border=0></a></td><? }?>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select distinct(templates_site.ID) as IDb,templates_site.*,templates_site_lang.Cabecalho from templates_site inner join templates_site_lang on templates_site.ID=templates_site_lang.ID_PAI where lang='$admin_lang' AND ID_PAI IN($reg) order by Ordem,Cabecalho ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Tipo=$rs->db_data["Tipo"];
		$descricao=$rs->db_data["Cabecalho"];
		$Visivel=$rs->db_data["Visivel"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
            <td width="29" align="center"><img src="../images/page.png"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>
            <td width="35"><img title="<? if($Visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_duplicar_ambiente','Duplicar Ambiente')?>" src="duplicate.png" border=0></a></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_ambiente','Apagar Ambiente')?>" src="del2.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord()
{
	$ordem=$_POST['ordem'];
	$data=str2date($_POST['ano']."-".$_POST['mes']."-".$_POST['dia']);
	$data2=str2date($_POST['ano2']."-".$_POST['mes2']."-".$_POST['dia2']);
	$img_path=$_POST['img_path'];
	$Img_peq1=$_POST['Img_peq1'];
	$visivel1=$_POST['visivel'];
	$visivel2=$_POST['visivel2'];
	$destaque=$_POST['destaque'];
	$margem_top=$_POST['margem_top'];
	$bg_color=$_POST['bg_color'];
	$efeito=$_POST['efeito'];
	$url=$_POST['url'];
	$site_width=$_POST['site_width'];
	$estilo=$_POST['estilo'];
	$estilo2=$_POST['estilo2'];
	$tipo=$_POST['tipo'];

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;
	if($Img_peq1["name"]) $altera_img=1;
	if(!$altera_img)
	{
		$img_ext='';
		$img_ext=str2SQLstr($img_ext);
	}
	else
	{
		$pos=strrpos($Img_peq1["name"],".");
		$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
		$img_ext=str2SQLstr($ext1);
	}
	$sql="insert into templates_site(Ordem,Data,Data2,img_ext,Visivel,Visivel2,Destaque,margem_top,bg_color,efeito,URL,site_width,estilo,estilo2,Tipo)
	values ('$ordem','$data','$data','$img_ext','$visivel1','$visivel2','$destaque','$margem_top','$bg_color','$efeito','$url','$site_width','$estilo','$estilo2','$tipo')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
	    $new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$ext1);
		InsertNewRecordLinguas($new_id);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewRecordLinguas($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$m_lang=$_POST['m_lang'];
	if(!$m_lang)$m_lang=$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho=$_POST['cabecalho'];
	$texto1=$_POST['texto1'];
	$texto2=$_POST['texto2'];
	$texto3=$_POST['texto3'];
	$texto4=$_POST['texto4'];
	$slashes=$_POST['slashes'];
	if($slashes=="'")
	{
		$cabecalho=addslashes($cabecalho);
		$texto1=addslashes($texto1);
		$texto2=addslashes($texto2);
		$texto3=addslashes($texto3);
		$texto4=addslashes($texto4);
	}

	$sql="insert into templates_site_lang (ID_PAI,Cabecalho,Texto1,Texto2,Texto3,Texto4,Lang) values ('$id_pai','$cabecalho','$texto1','$texto2','$texto3','$texto4','$admin_lang')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
	return($res);
}
function DeleteRecord()
{
	$id=$_GET['ID'];

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes= $obj->db_open($db_link,$DB_name,"Select * from templates_site where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$res=Runquery($db_link,$DB_name,"delete from templates_site where ID=$id");
	if($res)
	{
		/*if($ext1!="")		//CASO TENHA templates_site ELIMINA-AS
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}*/
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord()
{
	$ordem=$_POST['ordem'];
	$id=$_GET['ID'];if(!$id)return false;
	$data=str2date($_POST['ano']."-".$_POST['mes']."-".$_POST['dia']);
	$data2=str2date($_POST['ano2']."-".$_POST['mes2']."-".$_POST['dia2']);
	$img_path=$_POST['img_path'];
	$Img_peq1=$_POST['Img_peq1'];
	$visivel1=$_POST['visivel'];
	$visivel2=$_POST['visivel2'];
	$destaque=$_POST['destaque'];
	$margem_top=$_POST['margem_top'];
	$bg_color=$_POST['bg_color'];
	$efeito=$_POST['efeito'];
	$url=$_POST['url'];
	$site_width=$_POST['site_width'];
	$estilo=$_POST['estilo'];
	$estilo2=$_POST['estilo2'];
	$tipo=$_POST['tipo'];

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from templates_site WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)return InsertNewRecord();
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$hRes= $obj->db_open($db_link,$DB_name,"Select img_ext from templates_site where ID=$id");
		$ext1=$obj->db_data["img_ext"];
	    $altera_img=0;
		if($Img_peq1["name"]) $altera_img=1;
		if($hRes)
		{
			if($tem_foto1==0)
			{
				if(!$altera_img)
				{
					$img_ext="";
					$img_ext=str2SQLstr($img_ext);
				}
				else
				{
					$pos=strrpos($Img_peq1["name"],".");
					$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
					$img_ext=str2SQLstr($ext1);
					copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
				}
			}
			else
			{
				if($Apagar_img1==1)
				{
					$ficheiro1=$id."_1.".$ext1;
					unlink($img_path.$ficheiro1);
					if(!$altera_img)
					{
						$img_ext="";
						$img_ext=str2SQLstr($img_ext);
					}
					else
					{
						$pos=strrpos($Img_peq1["name"],".");
						$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
						$img_ext=str2SQLstr($ext1);
						copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
					}
				}
				else
				{
					if($altera_img)
					{
						$pos=strrpos($Img_peq1["name"],".");
						$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
						$img_ext=str2SQLstr($ext1);
						copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
					}
					else $img_ext=str2SQLstr($ext1);
				}
			}
		}
		$sql="Update templates_site Set Ordem='$ordem',Data='$data1',Data2='$data2',img_ext='$img_ext',Visivel='$visivel1',Visivel2='$visivel2',Destaque='$destaque',margem_top='$margem_top',bg_color='$bg_color',efeito='$efeito',URL='$url',site_width='$site_width',estilo='$estilo',estilo2='$estilo2',Tipo='$tipo' where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		ChangeRecordLinguas();
		return($res);
	}
}
function ChangeRecordLinguas()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;

	$m_lang=$_POST['m_lang'];
	if(!$m_lang)$m_lang=$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho=$_POST['cabecalho'];
	$texto1=$_POST['texto1'];
	$texto2=$_POST['texto2'];
	$texto3=$_POST['texto3'];
	$texto4=$_POST['texto4'];
	$slashes=$_POST['slashes'];
	if($slashes=="'")
	{
		$cabecalho=addslashes($cabecalho);
		$texto1=addslashes($texto1);
		$texto2=addslashes($texto2);
		$texto3=addslashes($texto3);
		$texto4=addslashes($texto4);
	}
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$id_pai=$_GET['ID'];
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from templates_site_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();
	if(!$altera)return InsertNewRecordLinguas($id_pai);
	else
	{
		$sql="Update templates_site_lang Set Cabecalho='$cabecalho',Texto1='$texto1',Texto2='$texto2',Texto3='$texto3',Texto4='$texto4' where ID_PAI=$id_pai and Lang='$admin_lang'";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID_PAI,
	lower(Texto1) as Texto1,length(lower(Texto1)) AS len,
	((length(Texto1)-length(replace(lower(Texto1),'".$d."','')))/length('".$d."'))/(length(lower(Texto1))+1-length(replace(lower(Texto1),' ','')) ) AS occwds,
	((length(Texto1)-length(replace(lower(Texto1),'".$d."','')))/length('".$d."')) AS occ
	FROM templates_site_lang
	WHERE (lower(Texto1) LIKE '%".$d."%' OR Cabecalho like '%".$d."%') AND Lang='$admin_lang' ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_other_templates($ID=0)
{?>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td width="5" align="left"><img src="../images/s2.jpg" /></td>
		<td class="titulo2" align="center">TEMPLATES</td>
		<td width="5" align="right"><img src="../images/s2.jpg" /></td>
	</tr><?

	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select distinct(templates_site.ID) as IDb,templates_site.*,templates_site_lang.Cabecalho from templates_site inner join templates_site_lang on templates_site.ID=templates_site_lang.ID_PAI where templates_site.ID <> '$ID' AND lang='$admin_lang' order by Visivel desc, Ordem,Cabecalho ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Tipo=$rs->db_data["Tipo"];
		$descricao=$rs->db_data["Cabecalho"];
		$Visivel=$rs->db_data["Visivel"];
		$ordem=$rs->db_data["Ordem"];
		$protegido=$rs->db_data["Protected"];

		?><tr>
		<td width="5" align="left"><img src="../images/s2.jpg" /></td>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value=" <? echo $descricao?> " onclick="javascript:window.open('pagina.php?ID=<?=$IDb?>&Dump=<?=uniqid("");?>','_self')"></td>
        <td width="5" align="right"><img src="../images/s2.jpg" /></td>
		</tr>

		<?

		$rs->db_next();
	}
	$rs->db_close();
	?>
	</table>
	<img src="../images/bsh.png" width="200" height="30" />
<? }
?>