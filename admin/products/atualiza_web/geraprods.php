<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	include $path . 'core_files.php';
	include dirname( __FILE__ ).'/../filtros/funcoes.inc.php';
	
	
	$myFile = dirname( __FILE__ )."/../../../files/importar/atualizar_web.txt";

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link =  db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;	
	
	$del = array();
	$sql = 'SELECT ID, ID_ORG, Ref, ID_Marca, Aproved FROM products';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$fh = fopen($myFile, 'a+');
		fwrite($fh, $filename."\n");
		chmod($myFile, 0755);
				
		while(!$rs->EOF())
		{
			$id = $rs->db_data['ID'];
			$ItemCode = $rs->db_data['Ref'];
			$Aproved = $rs->db_data['Aproved'];
			
			if(trim($ItemCode) != '' && $Aproved == 1)
			{
				$id_org = $rs->db_data['ID_ORG'];
				
				$ID_Marca = $rs->db_data['ID_Marca'];
				
				echo "\n".$ItemCode;
				fwrite($fh, $ItemCode." | ");
				
				
				if($id_org == 0)
				{
					$rs2 = new DataObj;
					$rs2->db_open($db_link,$DB_name,'Select ID_ORG from products_families_obj WHERE ID_PROD="'.$id.'"');
					if(!$rs2->EOF()) $id_org = $rs2->db_data['ID_ORG'];
					$rs2->db_close();
				}			
				
				gera_produtos($id, $id_org, $ID_Marca, 1, 'pt');
			}
			else
			{
				$del[] = $id;
			}
			
			if(!file_exists($myFile)) die();
		
			$rs->db_next();
		}
	}
	$rs->db_close();
	
	
	echo "\n atualiza filtros... ";
	fwrite($fh, "<br> ATUALIZAR FILTROS...");
	gera_produtos_filtros('pt');
	

	if(count($del) > 0) 
	{ 
		echo 'A eliminar '.count($del).' ficheiros de produtos inativos....';
		fwrite($fh, "<br> A eliminar ficheiros de produtos inativos....");
	}
	for($a=0; $a<count($del); $a++)
	{
		foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$del[$a].".php") as $filename)
		{
			@unlink($filename);	
		}
		foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$del[$a]."_pt.php") as $filename)
		{
			@unlink($filename);	
		}
	}
	echo 'Terminado';
	fwrite($fh, "<br> Terminado");
	
	fclose($fh);
	if(file_exists($myFile)) @unlink($myFile);	
?>