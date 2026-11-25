<?	
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	// Reordenar
	if($flag==2)
	{
		foreach($_POST as $varName=> $value) 
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$var="field_".$str_id;
				$dv=$$var;
				$sql="UPDATE equip_substituicao SET zone_order='$dv' WHERE zone_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	// apagar
	if($flag==1)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);			
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"delete from equip_substituicao WHERE zone_id=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
				$res=Runquery($db_link,$DB_name,"delete from equip_substituicao_lang WHERE zone_id=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	// mover
	// tornar visivel
	if($flag==4)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);			
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE equip_substituicao SET zone_visible ='1' WHERE zone_id=$str_id";
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
				$sql="UPDATE equip_substituicao SET zone_visible ='0' WHERE zone_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	?>
	<form action="list.php" method="post" name="lista_paginas" onSubmit="return checkFields()">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><? 
			include "../layout/menus.inc.php";
			show_page_tools('filter');
			show_content_list();
			show_page_bottom_tools();?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top"><? show_fast_tools();?></td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>