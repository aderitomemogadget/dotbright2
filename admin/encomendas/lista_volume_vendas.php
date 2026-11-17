<?php
$path = '/var/www/dev/farmaciafs/core/';
require_once $path.'core_files.php';



$line= "Nome;Email;Valor; \n";
	
global $DB_host,$DB_user,$DB_pass,$DB_name; 
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass); 
$sql='SELECT Contacto,SUM(TotalDocComIva) as Total,enc_Email FROM `encomendas` group by ID_Cliente,enc_Email,Contacto';
$dados=new DataObj;
$hRes= $dados->db_open($db_link,$DB_name,$sql); 
$start_linha=1;
$obrigatorio=0;
$filename = "lista_volume_compras_".date("Ymd").".csv";

while(!$dados->EOF())
{
	 $Total=$dados->db_data["Total"];
	 $Contacto=$dados->db_data["Contacto"];
	 $enc_Email=$dados->db_data["enc_Email"];
	 $line.=$Contacto.";".$enc_Email.";".numberformat($Total,4)."; \n";

	$dados->db_next();
}
$dados->db_close();

	
if($line)
{
  	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header("Content-Disposition: attachment;filename=\"".$filename."\"");
	header("Cache-Control: max-age=0");
 	echo iconv("UTF-8", "Windows-1252",$line);
}?>