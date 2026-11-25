<?php
	header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');
	include "settings.inc.php";

	ini_set('display_errors',0);
	ini_set('display_startup_errors',0);
	error_reporting(-1);

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// apagar
	if($flag==1)
	{
		foreach($_GET as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res = Runquery($db_link,$DB_name,"DELETE FROM rmas_cliente WHERE id=$str_id");
				if(!$res)
					echo db_error($db_link).$sql."<br />";
				$res = Runquery($db_link,$DB_name,"DELETE FROM rmas_cliente_linha WHERE rmas_cliente_id=$str_id");
				if(!$res)
					echo db_error($db_link).$sql."<br />";
			}
		};
	}
	$reg="";
	$search = false;
	foreach ($_GET as $key => $value)
	{
		if(strpos($key, 'f_') !== false)
		{
			$search = true;
			break;
		}
	}

	if($search)
	{
		$reg="";
		$registos = SearchRecords('');
		while(!$registos->EOF())
		{
			if($reg)
				$reg.=",";
			$reg .= $registos->db_data['id'];
			$registos->db_next();
		}
		if(!$reg)
			$reg = 'empty';
		$registos->db_close();
	}
	else if(isset($_GET['x_id']) && isset($_GET['x_estado']))
	{
		$rs=new DataObj;
		$rs->db_open($db_link,$DB_name,"SELECT * FROM rmas_cliente WHERE id=".$_GET['x_id']);
		if(!$rs->EOF())
		{
			$estado_old = $rs->db_data['estado'];

			$res=Runquery($db_link,$DB_name,'UPDATE rmas_cliente SET estado="'.$_GET['x_estado'].'" WHERE ID="'.$_GET['x_id'].'"');
			if(!$res)
				echo db_error($db_link).$sql."<br>";
			else
			{
				// acrecentei o pagina para caso se esteja numa pagina este continue na mesma
				if(isset($_GET['pagina'])) echo '<script>window.open("list.php?d='.uniqID('').'&pagina='.$_GET['pagina'].'","_self");</script>';
				else echo '<script>window.open("list.php?d='.uniqID('').'","_self");</script>';
			}
		}
		$rs->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";?>
	<form action="list.php" method="get" name="lista_paginas" onSubmit="return checkFields()">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left">

			<?php
			include "../layout/menus.inc.php";
			echo '<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td class="p1">
						<input class="botao" type="button" style="width:160px;height:20px" value="'.write_lang('key_criar_novo_rma','Criar Novo RMA').'" onclick="javascript:window.open(\'pagina.php?ID=0&Dump='.uniqid("").'\',\'_self\')">
					</td>
				</tr>
				<tr><td valign="top" align="left"></td></tr></table-->';
				show_page_tools();

			if($_GET) show_content_list($reg=null);
			else $list = show_content_list($reg=null);
			show_page_bottom_tools();?></form>
		</td>
		<td width="10">&nbsp;</td>
		<td valign="top"></td>
	</tr>
	</table>
	</form>
<?php
include "../layout/bottom.inc.php";?>