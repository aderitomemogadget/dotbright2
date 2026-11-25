<?php
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
		<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td valign="top" align="left">
					<?php
					include "../layout/menus.inc.php";

					// LISTAGEM DE RMAS
					$list = show_content_list_emp();
					?>				
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