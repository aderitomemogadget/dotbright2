<?php
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// vamos verificar se os campos existem todos na base de dados
    $fields=db_list_fields($DB_name,'templates_imgs',$db_link);
    $columns=db_num_fields($fields);
    for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('ID_PAI',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD ID_PAI INT(6) NOT NULL');
    if(!in_array('imge_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD imge_ext VARCHAR(6) NOT NULL');
	if(!in_array('link_imge_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD link_imge_ext VARCHAR(255) NOT NULL');
	if(!in_array('bgcolore',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD bgcolore VARCHAR(10) NOT NULL');
	if(!in_array('imgd_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD imgd_ext VARCHAR(6) NOT NULL');
	if(!in_array('link_imgd_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD link_imgd_ext VARCHAR(255) NOT NULL');
	if(!in_array('bgcolord',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD bgcolord VARCHAR(10) NOT NULL');
	if(!in_array('activa',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_imgs ADD activa VARCHAR(2) NOT NULL');

	$fields=db_list_fields($DB_name,'templates',$db_link);
    $columns=db_num_fields($fields);
    for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('link_to',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates ADD link_to VARCHAR(255) NOT NULL');
	if(!in_array('DateUpdate',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates ADD DateUpdate datetime NULL ');


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
				$sql="UPDATE templates SET ordem='$dv' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// apagar
	if($flag==1)
	{
		foreach($_POST as $varName => $value)
		{
			$dv = $value;
			$str_id = substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				if(verifica_protegido($str_id)==0)
				{
					$res = Runquery($db_link,$DB_name, "delete from templates WHERE ID=$str_id");
					if(!$res) echo db_error($db_link).$sql."<br>";
					$res = Runquery($db_link,$DB_name, "delete from templates_lang WHERE ID_PAI=$str_id");
					if(!$res) echo db_error($db_link).$sql."<br>";
				}
			}
		};
	}
	// mover
	if($flag==3)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE templates SET ID_ORG='$m_familia' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// tornar visivel
	if($flag==4)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE templates SET Visible='1' WHERE ID=$str_id";
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
				$sql="UPDATE templates SET Visible='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// proteger
	if($flag==6)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE templates SET Protected='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desproteger
	if($flag==7)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv = $value;
			$str_id = substr($varName,strpos($varName,"_") + 1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql = "UPDATE templates SET Protected='0' WHERE ID=$str_id";
				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link).$sql."<br>";
			}
		}
	}
	if($_POST && $_POST["m_pesquisa"])
	{
		$registos=SearchRecords($_POST["m_pesquisa"]);
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data["ID_PAI"];$registos->db_next();}
		$registos->db_close();
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
			if($reg || $_POST["m_pesquisa"]) show_content_from_search($reg);
			else show_content_list();
			show_page_bottom_tools();?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>