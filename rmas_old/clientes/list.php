<?php
	include "settings.inc.php";

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	$loja = $dados['Loja'];
	$and_loja='';
	$where_loja='';
	if($loja != '') {$where_loja=' WHERE Loja = "'.$loja.'"';$and_loja=' AND Loja = "'.$loja.'"';}

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
				$sql="UPDATE clientes SET Grupo_ID='$Grupo_ID' WHERE ID=$str_id";
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
				$res=Runquery($db_link,$DB_name,"delete from clientes WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	if($_POST && $_POST["m_pesquisa"])
	{
		$registos=SearchRecords($_POST["m_pesquisa"]);
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data['ID'];$registos->db_next();}
		$registos->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";?>
	<script>
		function LoginCliente(username, password)
		{
			$.post("<? echo $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']); ?>", { login: "1", submit1: "1", redirect: "0", referer: "", m_email: username, m_password: password})
			.done(function (data){
				window.open("<? echo $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']); ?>");
			});

		}
		$(function()
		{
			$('.dropdata').datepicker({showOn: "button", buttonImage: "../../external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
		});

	</script>
	<form action="list.php" method="get" name="lista_paginas">
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><?
			include "../layout/menus.inc.php";
			show_page_tools('filter');
			show_content_list();
			show_page_bottom_tools();
			?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top"><? show_fast_tools($list=null);?></td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>