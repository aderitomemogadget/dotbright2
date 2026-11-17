<?php
	/*header ( "Expires:0");
	header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
	header ( "Pragma: no-cache" );
	header('Content-Encoding: UTF-8');
	header('Content-type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment;filename="exportacao_'.date('dmYHis').'.csv"');
	header("Cache-Control: max-age=0");
	echo "\xEF\xBB\xBF";*/


	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name;


	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs3 = new DataObj;


	ini_set("max_execution_time","36000000");
	ini_set("memory_limit","80M");
	ini_set("post_max_size","80M");
	ini_set("upload_max_filesize","80M");


	ini_set('display_errors', 0);
	ini_set('display_startup_errors',1);
	error_reporting(0);


	$filename="exportacao_".date('dmYHis').".csv";
	$delimiter = ';';
	$enclosure = '"';
	// Tells to the browser that a file is returned, with its name : $filename.csv
header("Content-disposition: attachment; filename=" . $filename);
    // Tells to the browser that the content is a csv file
header("Content-Type: text/csv");

    // I open PHP memory as a file
    $fp = fopen("php://output", 'w');

    // Insert the UTF-8 BOM in the file
    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    // I add the array keys as CSV headers
    //fputcsv($fp,array_keys($data[0]),$delimiter,$enclosure);

	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

	//primeiro vamos buscar todos os filtros
	/*$sql='SELECT category_name,filters_categories.category_id from filters_categories inner join filters_categories_lang on filters_categories_lang.category_lang_id=filters_categories.category_id WHERE filters_categories.category_active=1 and category_name !="" group by category_name';
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$filtros_u[]=$rs->db_data['category_name'];
		$rs->db_next();
	}
	$rs->db_close();*/

	//primeiro vamos buscar todos os filtros
	$sql='SELECT category_name,filters_categories.category_id from filters_categories inner join filters_categories_lang on filters_categories_lang.category_lang_id=filters_categories.category_id WHERE filters_categories.category_active=1';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$Filtro=$rs->db_data['category_name'];

		if(!in_array($Filtro,$filtros_u))$filtros_u[]=$Filtro;
		$filtros[$i]=$Filtro;
		$filtros_IDs[$i]=$rs->db_data['category_id'];
		$filtros_IDs2[$i]='';
		$i++;

		$rs->db_next();
	}
	$rs->db_close();



    $headerrow = array("design", "marca", "ref", "ref_pai", "des.abreviada", "EAN", "sku", "epv1", "ivaincl", "familia", "descrição");
    $headerrow = array_merge($headerrow,$filtros_u);

    fputcsv($fp,$headerrow,$delimiter,$enclosure);

	$sql='SELECT
			products.ID,
			products.ref_ean,
			products.ref_sku,
			products.Ordem,
			products.ID_Marca,
			products.Ref,
			products.Ref_Pai,
			products.ID_ORG,
			products_lang.Cabecalho,
			products_lang.Subtitulo,
			products_lang.Texto1,
			products_lang.Texto3
		FROM
			products, products_lang
		WHERE
			products.ID = products_lang.ID_PAI
			ORDER BY products.ID ASC';

	$rs->db_open($db_link,$DB_name,$sql);


	while(!$rs->EOF())
	{
		$Prod_ID=$rs->db_data['ID'];
		//ler as familias


		$sql='SELECT * FROM products_families_obj WHERE ID_PROD = '.$Prod_ID;
		$rs2->db_open($db_link,$DB_name,$sql);
		$i=0;
		$Fam='';
		while(!$rs2->EOF())
		{
			if($i>0)$Fam.=';';
			$Fam.=$rs2->db_data['ID_ORG'];
			$rs2->db_next();
			$i++;
		}
		$rs2->db_close();



		$i=0;
		$filtros_IDs3=$filtros_IDs2;

		$sql='SELECT * FROM filters_products WHERE prod_id = '.$Prod_ID;
		$rs2->db_open($db_link,$DB_name,$sql);
		while(!$rs2->EOF())
		{
			$cat_id=$rs2->db_data['cat_id'];
			$valor_id=$rs2->db_data['valor_id'];

			$cat_name='';
			$sql='SELECT * FROM filters_categories_lang WHERE category_lang_id = '.$cat_id;
			$rs3->db_open($db_link,$DB_name,$sql);
			if(!$rs3->EOF())
			{
				$cat_name=$rs3->db_data['category_name'];
			}
			$rs3->db_close();

			$sql='SELECT * FROM filters_values_lang where values_lang_id = '.$valor_id;



			$rs3->db_open($db_link,$DB_name,$sql);
			if(!$rs3->EOF())
			{
				$filtro_valor=$rs3->db_data['values_name'];
				$i=0;
				while($i<count($filtros_IDs))
				{
					if($filtros_u[$i]==$cat_name)
					{
						$filtros_IDs3[$i]=$filtro_valor;
					}
					$i++;
				}
			}
			$rs3->db_close();



			$rs2->db_next();
		}
		$rs2->db_close();


		$row = array(html_entity_decode(strip_tags($rs->db_data['Cabecalho'])), $rs->db_data['ID_Marca'],$rs->db_data['Ref'],$rs->db_data['Ref_Pai'], html_entity_decode(strip_tags($rs->db_data['Subtitulo'])), $rs->db_data['ref_ean'], $rs->db_data['ref_sku'],'','',$Fam, html_entity_decode(strip_tags($rs->db_data['Texto1'])));
		$row = array_merge($row,$filtros_IDs3);

		fputcsv($fp, $row,$delimiter,$enclosure);
		$rs->db_next();
	}
	$rs->db_close();

    // Close the file
    fclose($fp);

?>