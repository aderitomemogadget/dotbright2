<?php
$path = '/var/www/dev/farmaciafs/core/';
require_once $path.'core_files.php';



$line= "Referencia;Nome; \n";
	
global $DB_host,$DB_user,$DB_pass,$DB_name; 
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
$sql='SELECT pl.Cabecalho as Nome,pol.Cabecalho as Ref FROM filters_products fp inner join products p on p.ID=fp.prod_id inner join products_lang pl on pl.ID_PAI=p.ID inner join products_objs po on po.obj_type_id=p.ID inner join products_objs_lang pol on pol.ID_PAI=po.obj_id WHERE (fp.marca_id=0 or fp.marca_id=167) and pol.Lang="pt"  and pl.Lang="pt"  group by fp.prod_id  ORDER BY pol.Cabecalho+0 Asc';
$dados=new DataObj;
$hRes= $dados->db_open($db_link,$DB_name,$sql); 
$start_linha=1;
$obrigatorio=0;
$filename = "produtos_sem_marca_".date("Ymd").".csv";

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
