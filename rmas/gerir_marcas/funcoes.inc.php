<?
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?
		
				echo '<input class="botao" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" /><? 
				}
				if($task_form=="altera"){?><input class="botao" type="button" name="Apagar" value="<? echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function show_fast_tools()
{
	global $ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Inserir nova marca" onclick="javascript:window.open('pagina.php?ID=0&ID_ORG=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>	
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_page_tools($filter=0)
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" align="center">&nbsp;</td>
		<td width="91px" class="t1"><? echo write_lang('key_pesquisar','Pesquisar')?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
		<td width="28px"><input type="submit" value="<? echo write_lang('key_submeter_pesquisa','Submeter Pesquisa')?>" src="../images/btp.jpg" class="botao" style="width:120px;height:20px"></td>
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
				<option value="4"><? echo write_lang('key_tornar_visivel','Tornar visivel')?></option>
				<option value="5"><? echo write_lang('key_tornar_invisivel','Tornar invisivel')?></option>
			</select>
		</td>
        <td width="5px"></td>

		<td>&nbsp;<input type="submit"  name="submeter" value="<? echo write_lang('key_aplicar_acao','Aplicar Ação')?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
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
  
	$template = explode(';',$template);
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
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	
	$Ordem = $_POST['Ordem'];
	$marca = $_POST['marca'];
	
	$sql = "insert into marcas_rmas(Ordem,marca) values ('$Ordem','$marca')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
   
	$ID = $_POST['ID'];
	
    $sql="delete from marcas_rmas where ID=$ID";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	
	$ID = $_POST['ID'];
	$marca = $_POST['marca'];
	$Ordem = $_POST['Ordem'];
		
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from marcas_rmas WHERE ID='$ID'");
	if(!$rs->EOF()) $altera=1;
	$rs->db_close();	
	
	if(!$altera) return InsertNewRecord();
	else
	{				
		$sql="Update marcas_rmas Set Ordem='$Ordem',marca='$marca' where ID=$ID";
		$res = Runquery($db_link,$DB_name,$sql);
		db_closedb_conection($db_link);
		return($res);
	}
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
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&Dump=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?Dump=".uniqid('')."\" class=\"new_caminho\">Menus</a>";
	return $str;  
}

function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipos;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from marcas_rmas order by Ordem";
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$ID=$rs->db_data["ID"];
		$Ordem=$rs->db_data["Ordem"];
		$marca=$rs->db_data["marca"];
		$rs2->db_close();
		
		if(!$marca) $descricao="N/A";
		
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		
		$i=0;
		?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="29" align="center"><input type="checkbox" name="chk_<?=$ID?>" id="chk_<?=$ID?>" value="<?=$ID?>" class="cb"></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
            <td valign="middle">&nbsp;<? while($i<$contador){echo "-";$i++;}?><a title="<? echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?ID=<?=$ID;?>&Dump=<? echo uniqid("");?>"><? if($marca){echo htmlentities_b(stripslashes($marca));} else echo "[Edit]";?></a></td>
			<!-- <td class="ordem<?=$n?>"><select name="zone_type_<?=$ID?>"><? for($a=0;$a<9;$a++){?><option value="<? echo $a;?>" <? if($zone_type==$a){?>selected="selected"<? }?>>Zone <? echo $a;?></option><? }?></select></td> -->
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$ID?>" value="<?=$Ordem?>" /></td>
			<td width="20" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$ID?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
		</tr>
		</table><?
		$rs->db_next(); 
		$i++;
	}
	$rs->db_close();
}
?>