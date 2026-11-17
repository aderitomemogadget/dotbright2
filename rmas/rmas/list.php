<?php
	if(isset($_GET['pesquisar']) && $_GET['pesquisar']==2)
	{
		/*$filename="RMAS_".date('Y-m-d').".xls";

		header ( "Expires:0");
		header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
		header ( "Pragma: no-cache" );
		header ( "Content-type: application/octet-stream; name=".$filename);
		//header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-Description: MID Gera excel" );

		echo '<html><body>';
		echo "\n";*/


		$filename="RMAS_".date('Y-m-d').".csv";
		header('Content-Encoding: UTF-8');
		header('Content-type: text/csv; charset=UTF-8');
		//header("Content-type: application/csv-tab-delimited-table;");
		header("Content-Disposition: attachment; filename=".$filename);
		header('Expires: 0');
		header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
		header('Pragma: public');
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		include "settings.inc.php";

		show_content_list();

		//echo '</body></html>';

		die();
	}


	header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');
	include "settings.inc.php";


	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	$loja = $dados['Loja'];
	$and_loja='';
	$where_loja='';
	if($loja != '') {$where_loja=' WHERE Loja = "'.$loja.'"';$and_loja=' AND Loja = "'.$loja.'"';}

	ini_set('display_errors',0);
	ini_set('display_startup_errors',0);
	error_reporting(0);

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// apagar


	// 5 - Técnico Maia
	// 6 - Comercial Maia
	// 7 - Técnico Matosinhos
	// 8 - Comercial Matosinho

	// print_r($_SESSION);
	$grupo = get_user_grupo($_SESSION['ss_admin_id']);

	// echo 'grupo: '.$grupo.'<br>';

	if(!isset($_GET['f_origem']))
	{
		if($grupo == 5 || $grupo == 6) $_GET['f_origem'] = 3;
		elseif($grupo == 7 || $grupo == 8) $_GET['f_origem'] = 2;
		else $_GET['f_origem'] = "";
	}

	if($flag==1)
	{
		foreach($_GET as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"delete from rmas WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br />";
				$res=Runquery($db_link,$DB_name,"delete from rmas_linhas WHERE ID_ORG=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br />";
			}
		};
	}
	$reg="";
/* 	if(isset($_GET['f_tracking']))
	{
		if(isset($_GET["m_pesquisa"])) $registos=SearchRecords($_GET["m_pesquisa"]);
		else $registos=SearchRecords('');
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data['ID'];$registos->db_next();}
		if(!$reg) $reg = 'empty';
		$registos->db_close();
	} */
	if(isset($_GET['x_id']) && isset($_GET['x_estado']))
	{
		$rs=new DataObj;
		$rs->db_open($db_link,$DB_name,"Select * from rmas WHERE ID=".$_GET['x_id']." $and_loja ");
		if(!$rs->EOF())
		{
			$estado_old = $rs->db_data['Estado'];

			$DataFinalizado='';
			if( ($_GET['x_estado']== 6 || $_GET['x_estado']== 7 || $_GET['x_estado']== 8) && $estado_old!=6 && $estado_old!=7 && $estado_old!=8) $DataFinalizado=' ,DataFinalizado="'.date('Y-m-d H:i:s').'"';

			$res=Runquery($db_link,$DB_name,'UPDATE rmas SET Estado="'.$_GET['x_estado'].'"'.$DataFinalizado.' WHERE ID="'.$_GET['x_id'].'" '.$and_loja);
			if(!$res) echo db_error($db_link).$sql."<br>";
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
	<script type="text/javascript" src="<?php echo $GLOBALS['site_http_url']?>rmas/layout/js/jquery.mask.phone.js"></script>
	<form action="list.php" method="get" name="lista_paginas" id="lista_paginas" onSubmit="return checkFields()">
	<input type="hidden" value="0" name="pesquisar" id="pesquisar">
	<?php if(isset($_GET['quick_filter']) && $_GET['quick_filter'] !== '') { ?><input type="hidden" value="<?php echo htmlspecialchars((string)$_GET['quick_filter'], ENT_QUOTES, 'UTF-8'); ?>" name="quick_filter"><?php } ?>
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><?php

			include "../layout/menus.inc.php";

			echo '<br><table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
			<tr>
			<td class="p1" style="width: 170px;">
			<input class="botao" type="button" style="width:160px;height:30px" value="'.write_lang('key_criar_novo_rma','Criar Novo RMA').'" onclick="javascript:window.open(\'pagina.php?ID=0&Dump='.uniqid("").'\',\'_self\')">
			</td>
			<td class="p1">
			<input class="botao" type="button" style="width:160px;height:30px" value="Emprestados" onclick="javascript:window.open(\'list_emp.php\',\'_self\')">
			</td>
			</tr>
			<tr><td valign="top" align="left"></td></tr></table>';

			// CAMPOS PESQUISA
			show_page_tools($grupo);

			// LISTAGEM DE RMAS
			$list = show_content_list();

			// AÇOES RMAS
			show_page_bottom_tools();?>
			<table border=0 cellspacing=0 cellpadding=1 width="100%" style="background:#FFF">
			<tr>
			<td>
			<table border=0 cellspacing=0 cellpadding=1 width="100%" style="background:#FFF">
				<tr>
					<td width="20" height="20" style="background:#9ACD32;border:#000 1px solid"><br></td><td height="20">Equipamento Novo/Aberto com menos de 7 dias</td>
				</tr>
				<tr>
					<td  height="1" colspan="2"></td>
				</tr>
				<tr>
					<td width="20" height="20" style="background:#FFFF99;border:#000 1px solid"><br></td><td height="20">Equipamento Aberto com 7 ou mais dias </td>
				</tr>
				<tr>
					<td  height="1" colspan="2"></td>
				</tr>
				<tr>
					<td width="20" height="20" style="background:#FFA500;border:#000 1px solid"><br></td><td height="20">Equipamento reincidente, clique em <img src="../layout/history.png" width="20" border="0" style="vertical-align:middle"> para ver todos</td>
				</tr>
			</table>
			</td>
				</tr>
			</table>
			<img src="../images/bsh.png" width="100%">
		</td>
		<td width="10">&nbsp;</td>
		<td valign="top"></td>
	</tr>
	</table>
	</form>
<?php
include "../layout/bottom.inc.php";?>
