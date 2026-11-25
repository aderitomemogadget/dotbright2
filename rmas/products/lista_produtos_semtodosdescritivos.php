<?php
$path = '/var/www/dev/farmaciafs/core/';
require_once $path.'core_files.php';



$line= "Referencia;Nome; \n";
	
global $DB_host,$DB_user,$DB_pass,$DB_name; 
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
$sql='SELECT POL.Cabecalho as Ref,PL.Cabecalho as Nome FROM products_lang PL left join products_images PLI on PLI.ID_ORG=PL.ID_PAI INNER JOIN products_objs PO on PO.obj_type_id=PL.ID_PAI INNER JOIN products_objs_lang POL ON PO.obj_id=POL.ID_PAI  WHERE PL.Lang="pt" and PL.Texto1="" and PL.Texto3="" and (PL.Texto4="" || PL.Texto4="N/A") AND POL.Lang="pt" ORDER BY POL.Cabecalho+0 Asc';
$dados=new DataObj;
$hRes= $dados->db_open($db_link,$DB_name,$sql); 
$start_linha=1;
$obrigatorio=0;
$filename = "produtos_sem_todos_descritivos_".date("Ymd").".csv";

while(!$dados->EOF())
{
	 $Name=$dados->db_data["Nome"];
	 $Ref=$dados->db_data["Ref"];
	 $line.=$Ref.";".$Name."; \n";

	$dados->db_next();
}
$dados->db_close();

	
if($line)
{
  	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment;filename=\"".$filename."\"");
	header("Cache-Control: max-age=0");
 	echo iconv("UTF-8", "Windows-1252",$line);
}


	?>
