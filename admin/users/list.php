<?
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);


	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(isset($dados['Permissoes'][2]) && $dados['Permissoes'][2]==1)
	{
		//nada
	}
	else
	{
		die("Sem permissoes");
	}



	// Tratar dos dados submetidos na propria pagina
	// mover para o grupo
	if($flag==2)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE logins SET Grupo_ID='$Grupo_ID' WHERE ID=$str_id";
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
				$res=Runquery($db_link,$DB_name,"delete from logins WHERE ID=$str_id");
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
	include "../layout/body.inc.php";?>
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