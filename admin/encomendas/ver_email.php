<?php
	include "settings.inc.php";
	global $site_shell_url;
	header('Content-Type: text/html; charset=utf-8');

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT Body from encomendas_emails WHERE ID=".$_GET['id_email']."";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	?><style>html{background-color:#d5d8da}body{background-color: transparent;background:none;}</style>
	<div id="content" class="content">
		<div class="invoice" style="text-align:center"><?php 
				if(!$rs->EOF())
				{
					$email_cliente= $rs->db_data["Body"];
					$email_cliente=preg_replace( "/\r|\n/", "", $email_cliente);
					echo (html_entity_decode(stripslashes($email_cliente)));
				}
				$rs->db_close();
				?>
		</div>
	</div>