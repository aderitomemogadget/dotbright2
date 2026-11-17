<?php
    require_once('settings.inc.php');

	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');

    ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(0);
	
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$retorno="import.php";
	$importar_imagens_dir = "../../files/importar_imagens/";
	$extensions = "jpg,JPG,gif,GIF,png,PNG,jpeg,JPEG";

	if(isset($_POST['importar_imagens']) && intval($_POST['importar_imagens']) > 0)
	{		
		$result = 0;
		// agora vamos criar o produto ou atualizar
		
		$files = glob($importar_imagens_dir."*.{".$extensions."}", GLOB_BRACE);
		
		foreach ($files as $file) 
		{
		
			$result = 1;
			$filename = str_replace($importar_imagens_dir, '', $file);
			// var_dump($filename);
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$name = str_replace('.'.$ext, '', $filename);
			$URL_dest=$importar_imagens_dir.$filename;
			
			// var_dump($name);
			// var_dump($ext);

			$exploded = explode("_", $name);
	
			$ref = $exploded[0];
			$ordem = ($exploded[1] > 0 ? ($exploded[1]+1) : 1);
			// echo '<br>';

			$rs=new DataObj;
			$rs2=new DataObj;
				
			$sql = 'select ID, ID_ORG, ID_Marca from products where Ref = "'.$ref.'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				
				$id_prod=$rs->db_data["ID"];
				$prod_fam_id=$rs->db_data["ID_ORG"];
				$marca_id=$rs->db_data["ID_Marca"];
				$rs->db_close();
			
				//ja existem produtos com esta referencia
				$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					
					$sql = 'select * from products_images where ID_ORG = '.$id_prod.' AND Ordem='.$ordem;
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						// echo ' FOTO ATUALIZADA PARA A referencia '.$ref.' |ordem: '.$ordem;
						
						$sql = 'UPDATE products_images SET img_ext="'.$ext.'" WHERE ID_ORG = '.$id_prod.' AND Ordem='.$ordem;
						$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
						
						//ja existe a imagem, vamos substituir
						copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_".$ordem.".".$ext);
						unlink($URL_dest);
					}
					else
					{					
						// echo ' FOTO INSERIDA PARA A referencia '.$ref;
						// echo ' FOTO INSERIDA PARA A referencia '.$ref.' | ordem: '.$ordem;						
						
						$sql = 'INSERT INTO products_images (ID_ORG,Ordem,img_ext) VALUES ("'.$id_prod.'","'.$ordem.'","'.$ext.'")';
						$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
						
						copia_segura($URL_dest,$site_shell_url."files/products/".$id_prod."_".$ordem.".".$ext);
						unlink($URL_dest);
					}
					$rs2->db_close();					

					//Atualiza a cache dos artigos
					get_produtc_info($id_prod,'',1,0,$admin_lang,1);
					
				}
			}
			$rs->db_close();
	
			
		}
		// $result = 1;
		
		if($result)	show_box_OK(1,$retorno);
		else show_box_ERROR(1,$retorno);
	}
	else
	{		
		show_box_ERROR(1,$retorno);
	}
	
	
	
?>