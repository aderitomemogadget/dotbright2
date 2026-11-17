<?php 
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	
	$fields=db_list_fields($DB_name,'encomendas',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('Lingua',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas ADD Lingua varchar(255) DEFAULT ""');  }
	if(!in_array('enc_Fatura',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE encomendas ADD enc_Fatura varchar(255) DEFAULT ""');  }
	
	
	$notify = 0;$reg = '';

	if(isset($flag) && $flag==1)
	{
		foreach($_POST as $varName=> $value) 
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);			
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"update encomendas set Estado=-1 WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}; 
	}
	$pesq=0;
	if($_POST && ($_POST["f_id"] || $_POST["f_data1"] || $_POST["f_data2"] || $_POST["f_idcli"] || $_POST["f_contacto"] || $_POST["f_pagamento"] || $_POST["f_estado"] || $_POST["f_guia"] || $_POST["f_promos"]))
	{
		$pesq=1;
		$registos=SearchRecords();
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data["ID"];$registos->db_next();}
		$registos->db_close();
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	
	
	
	?>    
	<link rel="stylesheet" type="text/css" href="sa.css" />
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
	<script language="JavaScript" src="sa.js"></script>
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><? ;
			include "../layout/menus.inc.php";
			
			
			if(isset($_POST["env_email"])){envia_email();}
			
			if($notify == 1)
			{				
				show_notify_area();
			}
			else
			{
			?>
	            <form action="list.php" method="post" name="lista_paginas" onSubmit="return checkFields()"><?

				show_page_tools();
				if($reg || $pesq==1) show_content_list($reg);
				else show_content_list();
				
				show_page_bottom_tools();?>
				
                </form><?php
			}
			?>
		</td>
	</tr>
	</table>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<? 	include "../layout/bottom.inc.php";?>
