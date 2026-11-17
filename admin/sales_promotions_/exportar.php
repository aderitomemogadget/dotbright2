<?php

include "settings.inc.php";

global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$linha = '';
$rs = new DataObj;
$sql = '
		SELECT
			ID_PAI, Ref, Nome, date(Data_Criacao)
		FROM
			encomendas
			INNER JOIN encomendas_produtos ON encomendas_produtos.ID_PAI = encomendas.ID
		WHERE
			Perc_Desconto=100
			AND
			(date(Data_Criacao) BETWEEN "'.$_GET['p1'].'" and "'.$_GET['p2'].'")
		ORDER BY
			encomendas.ID Asc
		';

$rs->db_open($db_link,$DB_name,$sql);
$i=0;
if(!$rs->EOF())
{
	$linha = 'REF;NOME'."\n";
	while(!$rs->EOF())
	{
		$id_enc = $rs->db_data["ID_PAI"];
		$Ref = $rs->db_data["Ref"];
		$Nome = $rs->db_data["Nome"];

		$linha .= $Ref.';'.$Nome."\n";

		$rs->db_next();

	}
}
$rs->db_close();

if($linha)
{

$filename = "produtos_100".date("YmdHis").".csv";
header('Content-type: application/pdf');
header("Content-Disposition: attachment; filename=$filename");
header('Expires: 0');
header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
header('Pragma: public');
echo $linha;
}
else echo  'Não existe produtos a 100% de desconto para exportar!'
?>