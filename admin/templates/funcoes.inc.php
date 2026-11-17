<?php
function show_page_buttons()
{
	global $task_button,$btn_class;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
			<input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?> ">
            <input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="javascript:history.go(-1);">
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_fast_tools()
{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_template','Criar Novo Template')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_page_tools($filter=0)
{?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">TEMPLATES - WEBSITE</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb" /></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"></td>
		<td width="130px" align="right"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa');?>" class="btn btn-primary"  /></td>
        <td>&nbsp;</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools()
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<? echo write_lang('key_com_os_seleccionados','Com os seleccionados')?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><? echo write_lang('key_apagar','Apagar')?></option>
				<option value="2"><? echo write_lang('key_reordenar','Reordenar')?></option>
			</select>
		</td>
		<td>&nbsp;<input type="submit" name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px" /></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}
function show_content_list()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select distinct(templates.ID) as IDb,templates.*,templates_lang.Cabecalho from templates inner join templates_lang on templates.ID=templates_lang.ID_PAI where lang='$admin_lang' order by Visivel desc, Ordem,Cabecalho ASC";
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

		if($Visivel==1)
		{
			if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
			else				{$cor="#FFFFFF";$class="r2";$n="2";}
		}
		else
		{
			if($count % 2 != 0)	{$cor="#dbdada";$class="r1";$n="1";}
			else				{$cor="#e8e8e8";$class="r2";$n="2";}
		}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 style="background:<?=$cor;?>" width="100%">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>

            <td valign="middle"><a title="<? echo write_lang('key_aplicar_acao','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>

            <td width="35"><img title="<? if($Visivel) echo write_lang('key_aplicar_acao','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_duplicar_ambiente','Duplicar Ambiente')?>" src="duplicate.png" border=0></a></td>
			<? if($Visivel==0){?><td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_ambiente','Apagar Ambiente')?>" src="del2.png" border=0></a></td><? }?>

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
	$sql="Select distinct(templates.ID) as IDb,templates.*,templates_lang.Cabecalho from templates inner join templates_lang on templates.ID=templates_lang.ID_PAI where lang='$admin_lang' AND ID_PAI IN($reg) order by Ordem,Cabecalho ASC";
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
function GetRSRecords2($id=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)	$condicao= "WHERE ID=$id";
	else		$condicao="";
	$hRes= $obj->db_open($db_link,$DB_name,"SELECT $campos FROM templates $condicao ORDER BY Ordem");
	return $obj;
}

function verifica_protegido($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$Protected=0;
	$obj = new DataObj;
	$obj->db_open($db_link,$DB_name, "SELECT Protected FROM templates where ID='".$id."'");
	$Protected = $obj->db_data['Protected'];
	$obj->db_close();
	return $Protected;
}
function InsertNewRecord($ordem,$data,$data2,$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$altera_img=0;
	if($Img_peq1["name"]) $altera_img=1;
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
	}
	$data1=date2SQLstr($data);
	$data2=date2SQLstr($data2);
	$cabecalho1=str2SQLstr($cabecalho);
	$visivel1=str2SQLstr($visivel);
	$destaque1=str2SQLstr($destaque);
	$ordem=str2SQLstr($ordem);
	$Video=str2SQLstr($_POST['Video']);
	$sql="insert into templates (DateUpdate,Ordem,Data,Data2,img_ext,Visivel,Visivel2,Destaque,margem_top,bg_color,efeito,URL,site_width,estilo,estilo2,Video,link_to) values ('".date('Y-m-d H:i:s')."',$ordem,$data1,$data2,$img_ext,$visivel1,'$visivel2',$destaque1,'$margem_top','$bg_color','$efeito','$url','$site_width','$estilo','$estilo2',Video,'$link_to')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
	    $new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$ext1);
		InsertNewRecordLinguas($new_id,$cabecalho,$texto1,$texto2,$texto3,$texto4,$m_lang);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewRecordLinguas($id_pai,$cabecalho,$texto1,$texto2,$texto3,$texto4,$m_lang)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	if(!$m_lang)$m_lang=$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$cabecalho=str2SQLstr($cabecalho);
	$sql="insert into templates_lang (ID_PAI,Cabecalho,Texto1,Texto2,Texto3,Texto4,Lang) values ($id_pai,$cabecalho,'$texto1','$texto2','$texto3','$texto4','$admin_lang')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)  echo db_error($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes= $obj->db_open($db_link,$DB_name,"Select * from templates where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$res=Runquery($db_link,$DB_name,"delete from templates where ID=$id");
	if($res)
	{
		if($ext1!="")		//CASO TENHA templates ELIMINA-AS
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$ordem,$data,$data2,$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$Apagar_img1,$tem_foto1,$tem_foto2,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from templates WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)return InsertNewRecord($ordem,$data,$data2,$cabecalho,$texto1,$texto2,$texto3,$texto4,$img_path,$Img_peq1,$visivel,$visivel2,$destaque,$margem_top,$bg_color,$efeito,$url,$site_width,$estilo,$estilo2,$m_lang,$link_to);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$hRes= $obj->db_open($db_link,$DB_name,"Select img_ext from templates where ID=$id");
		$ext1=$obj->db_data["img_ext"];
	    $altera_img=0;
		if($Img_peq1["name"]) $altera_img=1;
		if($hRes)
		{
			//IMAGEM NUMERO 1
			if($tem_foto1==0)//NAO TINHA IMAGEM
			{
				if(!$altera_img)  //CONTINUA SEM IMAGEM
				{
					$img_ext="";
					$img_ext=str2SQLstr($img_ext);
				}
				else //PASSOU A TER IMAGEM
				{
					//RETIRA A EXTENSÃO DA 1 IMAGEM
					$pos=strrpos($Img_peq1["name"],".");
					$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
					$img_ext=str2SQLstr($ext1);
					//COPIA A IMAGEM 1
					copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
				}
			}
			else  //TINHA IMAGEM
			{
				//QUER APAGAR A IMAGEM
				if($Apagar_img1==1)
				{
					$ficheiro1=$id."_1.".$ext1;
					unlink($img_path.$ficheiro1);
					//NAO SUBSTITUI A IMAGEM
					if(!$altera_img){
						$img_ext="";
						$img_ext=str2SQLstr($img_ext);
					}
					else //SUBSTITUI A IMAGEM
					{
						//RETIRA A EXTENSÃO DA 1 IMAGEM
						$pos=strrpos($Img_peq1["name"],".");
						$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
						$img_ext=str2SQLstr($ext1);
						//COPIA A IMAGEM 1
						copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
					}
					//eliminar link associado
					$sqll = 'Update templates SET link_to="" WHERE ID='.$id;
					Runquery($db_link,$DB_name,$sqll);
				}
				//NAO QUER APAGAR IMAGEM
				else//MANTEM OS VALORES ANTERIORES
				{
					if($altera_img) //SUBSTITUI A IMAGEM
					{
						//RETIRA A EXTENSÃO DA 1 IMAGEM
						$pos=strrpos($Img_peq1["name"],".");
						$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
						$img_ext=str2SQLstr($ext1);
						//COPIA A IMAGEM 1
						copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
					}
					else
						$img_ext=str2SQLstr($ext1);
				}
			}
		}
		$data1=date2SQLstr($data);
		$data2=date2SQLstr($data2);
		$visivel1=str2SQLstr($visivel);
		$destaque1=str2SQLstr($destaque);
		$ordem=str2SQLstr($ordem);
		$Video=str2SQLstr($_POST['Video']);
		$sql="Update templates Set DateUpdate='".date('Y-m-d H:i:s')."',Ordem=$ordem,Data=$data1,Data2=$data2,img_ext=$img_ext,Visivel=$visivel1,Visivel2='$visivel2',Destaque=$destaque1,margem_top='$margem_top',bg_color='$bg_color',efeito='$efeito',URL='$url',link_to='$link_to',site_width='$site_width',estilo='$estilo',estilo2='$estilo2',Video=$Video where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		ChangeRecordLinguas($id,$cabecalho,$texto1,$texto2,$texto3,$texto4,$m_lang);
		return($res);
	}
}
function ChangeRecordLinguas($id_pai,$cabecalho,$texto1,$texto2,$texto3,$texto4,$m_lang)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from templates_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();
	if(!$altera)return InsertNewRecordLinguas($id_pai,$cabecalho,$texto1,$texto2,$texto3,$texto4,$m_lang);
	else
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$cabecalho1=str2SQLstr($cabecalho);
		$sql="Update templates_lang Set Cabecalho=$cabecalho1,Texto1='$texto1',Texto2='$texto2',Texto3='$texto3',Texto4='$texto4' where ID_PAI=$id_pai and Lang='$admin_lang'";
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
	FROM templates_lang
	WHERE (lower(Texto1) LIKE '%".$d."%' OR Cabecalho like '%".$d."%') AND Lang='$admin_lang' ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
	return $rs;
}
function show_other_templates($ID=0)
{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">TEMPLATES</td>
	</tr><?

	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;
	$sql="Select distinct(templates.ID) as IDb,templates.*,templates_lang.Cabecalho from templates inner join templates_lang on templates.ID=templates_lang.ID_PAI where lang='$admin_lang' order by Visivel desc, Ordem,Cabecalho ASC";
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

		if($ID == $IDb)
		{
			?><tr>
			<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px;background:#c0c0c0;color:#000;text-shadow:none" value=" <? echo $descricao?> " onclick="javascript:window.open('pagina.php?ID=<?=$IDb?>&Dump=<?=uniqid("");?>','_self')"></td>
			</tr>
			<?php
		}
		else
		{
			?><tr>
			<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value=" <? echo $descricao?> " onclick="javascript:window.open('pagina.php?ID=<?=$IDb?>&Dump=<?=uniqid("");?>','_self')"></td>
			</tr>
			<?php
		}
		$rs->db_next();
	}
	$rs->db_close();
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }

function addtableorfields($table='',$addcols=''){
	if($table == 'help'){
		echo "function arguments: addtableorfields(	&#x0024;table='',	&#x0024;addcols='')<br>";
		echo '&#x0024;table is the tablename<br>';
		echo '&#x0024;addcols is an array, follow this example<br>';
		echo "&#x0024;addcols = array(array('name'=>'ID_PAI','type'=>'INT(6)'),<br>
		      array('name'=>'imge_ext','type'=>'VARCHAR(6) NOT NULL'),<br>
		      array('name'=>'imgd_ext','type'=>'VARCHAR(6) NOT NULL')<br>
		     );<br><br>";
		die('end addtableorfields');

	}
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

 /* Buscar tabela -- se não existe -- adicionar*/
	$sql = "SELECT *
	FROM information_schema.tables
	WHERE table_schema = '".$DB_name."'
	    AND table_name = '".$table."'
	LIMIT 1";

	$rs = new DataObj;
	$rs->db_open($db_link, $DB_name, $sql);
	if ($rs->EOF()) // adiciona a tabela e os campos
	{
		$sql = 'CREATE TABLE '.$table.' (
				ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
				)';
		Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();

 //Adicionar campos a tabela
	$fields=db_list_fields($DB_name,$table,$db_link);
	$columns=db_num_fields($fields);
	$tv = sizeof($addcols);
	for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		foreach ($addcols as $col) {
			if(!in_array($col,$field_array))
				{
					$sql = 'ALTER TABLE '.$table.' ADD '.$col['name'].' '.$col['type'].' NOT NULL ';
					$res=Runquery($db_link,$DB_name,$sql);
				}
		}
}
?>