<?php
	$path = dirname( __FILE__ ).'/../../../core/';
	include $path . 'core_files.php';
	include dirname( __FILE__ ).'/funcoes.inc.php';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link =  db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;	
	$sql = 'SELECT ID,ID_Marca FROM products WHERE Aproved=1';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			
			$id = $rs->db_data['ID'];			
			$ID_Marca = $rs->db_data['ID_Marca'];
			
			$rs2 = new DataObj;
			$rs2->db_open($db_link,$DB_name,'Select ID_ORG from products_families_obj WHERE ID_PROD="'.$id.'"');
			if(!$rs2->EOF()) $id_org = $rs2->db_data['ID_ORG'];
			$rs2->db_close();			
			
			$fams = BuscarArvoreFams_F($id_org);

			if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = 0;
			if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
			if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;

			$rs3 = new DataObj;
			$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
			if($rs3->EOF())
			{			
	
				$sql = '
						INSERT INTO filters_products
						(
							cat_id, valor_id, prod_id, fam_id1, fam_id2,  fam_id3, marca_id, filtros_rel2, FLAG, filtro_active
						)
						VALUES
						(
							"0", "0", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$ID_Marca.'", "", 1, 1
						)
					';
				
				$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);				
			}
			else
			{			
					$sql = '
							UPDATE filters_products
							SET
								marca_id="'.$ID_Marca.'",
								fam_id1="'.$fam_id1.'", 
								fam_id2="'.$fam_id2.'",  
								fam_id3="'.$fam_id3.'"
							WHERE 
								prod_id="'.$id.'"
						';
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
				
			}
			
			if($ID_Fam > 0) gera_produtos_destaques($id, $id_org, $ID_Marca, 1);
			else gera_produtos_destaques($id, $ID_Fam, $ID_Marca, 1);			
		
			$rs->db_next();
		}
	}
	$rs->db_close();
?>