<?
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	foreach($_POST as $varName=> $value)
	{
		$str_id=substr($varName,strpos($varName,"_")+1);
		if($str_id > 0 && substr_count($varName,"chk_") > 0)
		{
			$sql="DELETE from brands_offers";
			$res=Runquery($db_link,$DB_name,$sql);
			break;
		}
	}
	foreach($_POST as $varName=> $value)
	{
		$str_id=substr($varName,strpos($varName,"_")+1);
		if($str_id > 0 && substr_count($varName,"chk_") > 0)
		{
			$var="field_".$str_id;
			$dv=$$var;
			$sql="INSERT INTO brands_offers(ID_Brand,Ordem) VALUES ('$str_id','$dv')";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res)echo db_error($db_link).$sql."<br>";
		}
	};
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	?>
	<form action="list_offers.php" method="post" name="lista_paginas" onSubmit="return checkFields()">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><?
			include "../layout/menus.inc.php";
			show_page_tools_destaques();
			show_content_list_offers();
			?>
			<table class="p1" align="center" border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr>
	            <td width="65"></td>
				<td valign="middle" align="left"><input type="submit"  name="submeter" value="Atualizar" class="btn btn-primary" style="width:120px"></td>
			</tr>
			</table>
		</td>
		<td width="10px">&nbsp;</td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>