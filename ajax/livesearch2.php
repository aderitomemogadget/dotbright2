<?php
	$path="../core/";
	include $path."core_files.php";
	$rs=new DataObj;
	$searchstr=$_GET['q'];
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$unico=1;

	$Str_pesquisar=addslashes(strtolower($searchstr));
	$and = ' lower(pl.Cabecalho) like "%'.$Str_pesquisar.'%" ';
	$and .= ' OR (lower(bl.Nome) like "%'.$Str_pesquisar.'%") ';
	$and .= ' OR (lower(p.Ref) like "%'.$Str_pesquisar.'%") ';
			$sql = '
					SELECT
						p.ID as PID, p.ID_Marca, pl.Cabecalho, bl.Nome, b.img_ext
					FROM
						products p
						INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
						INNER JOIN brands b ON b.ID=p.ID_Marca
						INNER JOIN brands_lang bl ON bl.ID_PAI=p.ID_Marca
					WHERE
						( '.$and.' )
						AND p.Aproved=1
						AND pl.Lang="'.$site_lang.'"
						AND bl.Lang="'.$site_lang.'"
					GROUP BY
						p.Ref
					ORDER BY
						pl.Cabecalho ASC
					 LIMIT 0 , 10
					';
	// echo $sql;
	$db_link=db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs->db_open($db_link,$DB_name,$sql);

	$a_json = array();
	$a_json_row = array();

	$hint='';$marcas_ids=array();
	$i=0;$x=0;
	while (!$rs->EOF())
	{
		$i++;
		$ID_Marca = $rs->db_data['ID_Marca'];
		if($i<=9)
		{
			$img_file='files/products/'.$rs->db_data['PID'].'_1_small.png';
			$imagem = '';
			if(file_exists($GLOBALS['site_shell_url'].$img_file)) $imagem='<img border="0" src="'.$img_file.'">';
			$url = seo('pages.php?page_id='.$GLOBALS['products_page_id'],$rs->db_data['PID'], 2);


			$value = htmlentities(stripslashes($rs->db_data['Cabecalho']));
			$a_json_row["id"] = $url;
			$a_json_row["value"] = $value;
			$a_json_row["label"] = $value;
			array_push($a_json, $a_json_row);



		}

		$rs->db_next();
	}
	$rs->db_close(1);
	
	// jQuery wants JSON data
	echo json_encode($a_json);
	flush();
?>