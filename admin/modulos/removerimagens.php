<?php
require_once('settings.inc.php');

$origem = $GLOBALS['site_shell_url']."files/documentos";
$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
$rs=new DataObj;
if($dh = opendir($origem))
{
	while(($file = readdir($dh)) !== false)
	{
		switch(filetype("$origem/$file"))
		{
			case "file":
				//percorrer em busca de imagem do tipo jpg
				if(substr_count($file, '_thm')) $file = str_replace('_thm', '', $file);
				$sql = "SELECT bloco_img_ext FROM modulos_imgs WHERE bloco_img_ext='$file'";
				$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if($rs->EOF())
				{
					echo "$file";
					unlink("$origem/$file");
					echo '<br>';
				}
				$rs->db_close();

			break;
		}
	}
}




$origem = $GLOBALS['site_shell_url']."files/pages";
$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
$rs=new DataObj;
if($dh = opendir($origem))
{
	while(($file = readdir($dh)) !== false)
	{
		switch(filetype("$origem/$file"))
		{
			case "file":
				//percorrer em busca de imagem do tipo jpg
				if(substr_count($file, '_1.')) $ID = substr($file,0,strpos($file,'_1.'));
				if($ID>0)
				{
					$sql = "SELECT * FROM pages WHERE ID='$ID'";
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
					if($rs->EOF())
					{
						echo "$file";
						unlink("$origem/$file");
						echo '<br>';
					}
					$rs->db_close();
				}

			break;
		}
	}
}





$origem = $GLOBALS['site_shell_url']."files/products";
$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
$rs=new DataObj;
if($dh = opendir($origem))
{
	while(($file = readdir($dh)) !== false)
	{
		switch(filetype("$origem/$file"))
		{
			case "file":
				//percorrer em busca de imagem do tipo jpg
				if(substr_count($file, '_1.')) $ID = substr($file,0,strpos($file,'_1.'));
				if($ID>0)
				{
					$sql = "SELECT * FROM products WHERE ID='$ID'";
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
					if($rs->EOF())
					{
						echo "$file";
						unlink("$origem/$file");
						echo '<br>';
					}
					$rs->db_close();
				}

			break;
		}
	}
}