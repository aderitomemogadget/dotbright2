<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');

	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

?>
<style>
	.botao{padding:0 5px}
	.container{width:100%;margin:0 auto}
	.container label{display:block;padding:5px 0}
	ul.tabs{margin:0px;padding:0px;list-style:none}
	ul.tabs li{background:none;color:#222;display:inline-block;padding:5px 10px;cursor:pointer;font-weight:bold;border-right:solid 1px;border-top:solid 1px;font-size:11px;border-top-left-radius:4px;border-top-right-radius:4px}
	ul.tabs li.current{background:#FFF;color:#222}
	.tab-content{display:none;background:#FFF;padding:15px;font-size:12px}
	.tab-content.current{display:inherit}
</style>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top" align="left">
		<div class="panel panel-inverse overflow-hidden m-b-12">
			<div class="panel-heading">
				<h4 style="color:#fff">Resumo das importações<br> <?php

				$rs = new DataObj;


				$i=0;
				$t=count($GLOBALS['lista_importadores']);
				while($i<$t)
				{
					$Supplier_ID   = $GLOBALS['lista_importadores'][$i]['id'];
					$Supplier_name = $GLOBALS['lista_importadores'][$i]['name'];

					echo $Supplier_name;
					$sql = 'SELECT distinct(ID_ORG) FROM products_objs_history WHERE Supplier_ID='.$Supplier_ID;
					$rs->db_open($db_link,$DB_name,$sql);
					echo '<br>Total de preços: '.number_format($rs->db_rows,2,',','.');
					$rs->db_close();

					$sql = 'SELECT distinct(ID_ORG) FROM products_objs_history WHERE Supplier_ID='.$Supplier_ID.' AND data_i like ("%'.date('Y-m-d').'%")';
					$rs->db_open($db_link,$DB_name,$sql);
					echo '<br>Total de preços NOVOS inseridos hoje: '.number_format($rs->db_rows,2,',','.');
					$rs->db_close();


					$sql = 'SELECT distinct(ID_ORG) FROM products_objs_history WHERE Supplier_ID='.$Supplier_ID.' AND data_u like ("%'.date('Y-m-d').'%")';
					$rs->db_open($db_link,$DB_name,$sql);
					echo '<br>Total de preços atualizados hoje: '.number_format($rs->db_rows,2,',','.');
					$rs->db_close();

					echo '<br><br>';



	
					$i++;
				}

				?></h4>

			</div>
		</div>


		<img src="../../images/bsh.png" width="100%" height="30px">

	</td>
</tr>
</table><?php require_once('../../layout/bottom.inc.php');