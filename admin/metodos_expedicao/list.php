<?
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		// Tratar dos dados submetidos na propria pagina
	if(isset($_POST["flag"]) && $flag>0){
		$flag = $_POST["flag"];
		// tornar visivel
		if($flag==4)
		{
			foreach($_POST as $varName=> $value)
			{
				$dv=$value;
				$str_id=substr($varName,strpos($varName,"_")+1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql="UPDATE metodos_expedicao SET Visivel='1' WHERE ID=$str_id";
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
				}
			};
		}
		// tornar invisivel
		if($flag==5)
		{
			foreach($_POST as $varName=> $value)
			{
				$dv=$value;
				$str_id=substr($varName,strpos($varName,"_")+1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql="UPDATE metodos_expedicao SET Visivel ='0' WHERE ID=$str_id";
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
				}
			};
		}

		if($flag==1)
		{
			foreach($_POST as $varName=> $value)
			{
				$dv=$value;
				$str_id=substr($varName,strpos($varName,"_")+1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql="DELETE FROM metodos_expedicao WHERE ID=$str_id";
					$res=Runquery($db_link,$DB_name,$sql);
					$sql="DELETE FROM metodos_expedicao_lang WHERE ID_PAI=$str_id";
					$res=Runquery($db_link,$DB_name,$sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
				}
			};
		}
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	?>
	<form action="list.php" method="post" name="lista_paginas" onSubmit="return checkFields()">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><?
			include "../layout/menus.inc.php";
			show_page_tools(filter);
			show_content_list();
			show_page_bottom_tools();?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>