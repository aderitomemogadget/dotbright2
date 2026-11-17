<?php
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	// activar
	if($flag==2)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);			
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE currency SET currency_estado='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	// desactivar
	if($flag==3)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);			
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE currency SET currency_estado='0' WHERE ID=$str_id";
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
				$res=Runquery($db_link,$DB_name,"delete from currency WHERE currency_id=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	if($_POST && $_POST["m_pesquisa"])
	{
		$registos=SearchRecords($_POST["m_pesquisa"]);
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data["ID"];$registos->db_next();}
		$registos->db_close();
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	
	//echo '<font color=\'#FFFFFF\'><b>Pais:</b> '.getCountryFromIp($tipo_v).'</font>';
	?>
	<form action="list.php" method="post" name="lista_paginas" onSubmit="return checkFields()">
    <table class="t3" border=0 cellpadding=0 cellspacing=0 align="center">
	<tr>
		<td valign="top" align="left"><?php 
			include "../layout/menus.inc.php";
			show_page_tools(filter);
			if($reg || $_POST["m_pesquisa"]) show_content_from_search($reg);
			else show_content_list();
			show_page_bottom_tools();?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><?php show_fast_tools();?></td>
	</tr>
	</table>
	</form>
<?php include "../layout/bottom.inc.php";?>