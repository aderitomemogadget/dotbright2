<?
function show_page_buttons()
{
	global $task_button;
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

<?
}
function show_page_tools($filter=0)
{?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" class="btn btn-primary" ></td>
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
				<option value="2"><? echo write_lang('key_mover_para_o_grupo','Mover para o Grupo')?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_lista($Grupo_ID,"grupos_emails","Grupo_ID");?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>

<?
}
function show_fast_tools($list='')
{?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>

		<td class="titulo2" align="center">FAST TOOLS</td>

	</tr>
    <tr>

		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_grupo','Criar Novo Grupo')?>" onclick="javascript:window.open('../grupos_emails/pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>

	</tr>
	<tr>

		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_criar_novo_email','Criar Novo Email')?>" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>

	</tr>
    <tr>

		<td class=preto align="center">
		<?
		if($list){
			$clean_list=array();
			$clean_list=array_unique($list);
			$i=0;
			$ID=array();
			foreach ($clean_list as $key => $value)
			{
			  if(!is_null($value))
			  {
				$ID[$i]=$value;
				$i++;
			  }
			}
			$ID_List=$ID;
			if(count($ID)>0)
			{ ?>
            <form name="export_all" method="post" action="emails_exporta.php" target="_blank"/>
                <input type="submit" name="export" value=" <? echo write_lang('key_exportar_listagem','Exportar Listagem')?> " class="btn btn-primary">
                <?
                if($ID_List)
				{
					foreach($ID_List as $value){?><input type="hidden" name="id_list[]" value="<?=$value;?>"><? }
				}
               	?>
            </form>
            <?
			 }
		}?>
            </td>

	</tr>
	</table>

<? }
function show_content_list()
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql_all="Select * from emails";
	$sql="Select * from clientes limit $from,$qtd";
	$db_res=$rs->db_open($db_link,$DB_name,$sql_all);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Grupo_ID=$rs->db_data["Grupo_ID"];
		$Nome=$rs->db_data["Nome"];
		$Email=$rs->db_data["Email"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;

		if($Grupo_ID)
		{
			$sql="Select * from grupos_emails WHERE ID=$Grupo_ID";
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())$grupo_nome=$rs2->db_data["Descricao"];
			$rs2->db_close();
		}
		//else $grupo_nome="N/A";

		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" /></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>

			<td valign="middle" width="35"><img title="<? echo write_lang('key_editar_email','Editar Email')?>" src="ok.png" border=0 align="absmiddle" /></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&Dump=<? echo uniqid("");?>"><? if($Email){echo htmlentities_b(stripslashes($Email));} else echo "[Edit]";?></a></td>

            <td width="100"><? if($grupo_nome){echo htmlentities_b(stripslashes($grupo_nome));} /*else echo "N/A";*/?></td>

			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_email','Apagar Email')?>" src="del2.png" border=0></a></td>

		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();

	show_paginacao_bk($total,$qtd);

	$db_res=$rs->db_open($db_link,$DB_name,$sql_all);
	while(!$rs->EOF())
	{
		$list[]=$rs->db_data['ID'];
		$rs->db_next();
	}
	$rs->db_close();
	return $list;
}
function show_content_from_search($reg)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from emails WHERE ID IN($reg) order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Nome=$rs->db_data["Nome"];
		$Email=$rs->db_data["Email"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? echo write_lang('key_editar_email','Editar Email')?>" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Email){echo htmlentities_b(stripslashes($Email));} else echo "[Editar]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_email','Apagar Email')?>" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}

function show_content_from_search2($f_data1,$f_data2)
{

	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select * from clientes WHERE Data BETWEEN '".$f_data1."' AND '".$f_data2."' order by Nome ASC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data['ID'];
		$list[]=$rs->db_data['ID'];
		$Nome=$rs->db_data["Nome"];
		$Email=$rs->db_data["Email"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>

			<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? echo write_lang('key_editar_email','Editar Email')?>" src="ok.png" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($Email){echo htmlentities_b(stripslashes($Email));} else echo "[Edit]";?></a></td>
			<td width="100" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_email','Apagar Email')?>" src="del2.png" border=0></a></td>
		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
	return $list;
}

function InsertNewRecord($Email,$Nome,$Grupo_ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$Email=str2SQLstr($Email);
	$Nome=str2SQLstr($Nome);
	$Grupo_ID=str2SQLstr($Grupo_ID);
	$sql="insert into emails (Email,Nome,Data,Grupo_ID) values ($Email,$Nome,'".date("Y-m-d")."',$Grupo_ID)";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name,"delete from emails where ID=$id");
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$Email,$Nome,$Grupo_ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from emails WHERE ID=$id");
	if(!$rs->EOF())$altera=1;
	$rs->db_close();
	if(!$altera)
	{
		return InsertNewRecord($Email,$Nome,$Grupo_ID);
	}
	else
	{
		$Nome=str2SQLstr($Nome);
		$Grupo_ID=str2SQLstr($Grupo_ID);
		$Email=str2SQLstr($Email);
		$sql="Update emails Set Nome=$Nome,Email=$Email,Data_modify='".date("Y-m-d")."',Grupo_ID=$Grupo_ID where ID=$id";
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
	$sql="SELECT distinct ID,
	lower(Descricao) as Descricao,length(lower(Descricao)) AS len,
	((length(Descricao)-length(replace(lower(Descricao),'".$d."','')))/length('".$d."'))/(length(lower(Descricao))+1-length(replace(lower(Descricao),' ','')) ) AS occwds,
	((length(Descricao)-length(replace(lower(Descricao),'".$d."','')))/length('".$d."')) AS occ
	FROM pages_lang
	WHERE (lower(Descricao) LIKE '%".$d."%') ORDER BY occ DESC,occwds DESC  ";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo "ERRO";
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
		echo '<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
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
?>