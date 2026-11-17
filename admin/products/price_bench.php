<?
	include "settings.inc.php";
	header('Content-type: application/csv');
	header("Content-Disposition: attachment; filename=produtos.csv");
	header('Expires: 0');
	header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
	header('Pragma: public');

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	echo '"Fornecedor";';
	echo '"ID_PROD";';
	echo '"Ref";';
	echo '"EAN";';
	echo '"Nome";';
	echo '"PCUSTO";';
	echo '"PVP";';
	echo '"VISIVEL";';
	echo '"PVP MIN";';
	echo '"PVP MAX";';
	echo "\n";

	$count =0;
	$sql="SELECT products.*, Cabecalho, Cabecalho_tmp FROM products
	INNER JOIN products_lang ON products_lang.ID_PAI =products.ID
	group by products.ID order by products.ID_ORG asc, visitas desc ";

	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$Cab=$rs->db_data["Cabecalho"];
		$Cab_tmp=$rs->db_data["Cabecalho_tmp"];
		if($Cab_tmp) $Cab.='<br /><font color=red>'.$Cab_tmp.'</font>';
		if(!$Cab) $Cab=" - ";
		$ID_PROD=$rs->db_data["ID"];
		$Ref=$rs->db_data["Ref"];
		$Ref_Ean=$rs->db_data["Ref_Ean"];
		$org=$rs->db_data["ID_ORG"];
		$aproved=$rs->db_data["Aproved"];

		$fornecedor=$rs->db_data["fornecedor_importacao"];

		$sql="SELECT * FROM products_objs WHERE obj_type_id=$ID_PROD";
		$hRes=$rs2->db_open($db_link,$DB_name,$sql);
		$precocusto=$rs->db_data["obj_pvp"];
		$preco=$rs->db_data["obj_pvp2"];
		$rs2->db_close();

		if($count % 2 !=0)	$cor="#E0E0E0";
		else				$cor="#F6F6F6";


		echo '"'.$fornecedor.'";';
		echo '"'.$ID_PROD.'";';
		echo '"'.$Ref.'";';
		echo '"'.$Ref_Ean.'";';
		echo '"'.$Cab.'";';
		echo '"'.$precocusto.'";';
		echo '"'.$preco.'";';

		if($aproved)	echo '"SIM";';
		else			echo '"NÃO";';

		echo '"";';
		echo '"";';

		echo "\n";
		$rs->db_next();
		$count++;
	}
	$rs->db_close();
?>