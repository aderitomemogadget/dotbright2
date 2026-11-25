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
	echo 'Fornecedor£';
	echo 'Familia£';
	echo 'Referência£';
	echo 'Nome do produto£';
	echo 'PVP£';
	echo 'NOVO PVP£';
	echo 'EM DESTAQUE/NOVIDADE£';
	echo 'VISIVEL£';
	echo "\n";

	$count =0;
	$sql1="SELECT products.*, Cabecalho, Cabecalho_tmp FROM products 
	INNER JOIN products_lang ON products_lang.ID_PAI =products.ID 
	group by products.ID order by products.ID_ORG asc, visitas desc ";

	$hRes=$rs->db_open($db_link,$DB_name,$sql1);
	while(!$rs->EOF())
	{
		$Cab=$rs->db_data["Cabecalho"];
		$Cab_tmp=$rs->db_data["Cabecalho_tmp"];
		if($Cab_tmp) $Cab.='<br /><font color=red>'.$Cab_tmp.'</font>';
		if(!$Cab) $Cab=" - ";
		$ID_PROD=$rs->db_data["ID"];
		$Ref=$rs->db_data["Ref"];
		$org=$rs->db_data["ID_ORG"];
		$precocusto=$rs->db_data["PrecoCusto"];
		$preco=$rs->db_data["Preco1"];
		$preco_tmp=$rs->db_data["Preco1_tmp"];
		$importado=$rs->db_data["Importacao"];
		$aproved=$rs->db_data["Aproved"];
		$visitas=$rs->db_data["visitas"];
		$id_fornecedor=$rs->db_data["id_fornecedor"];
		
		if($count % 2 !=0)	$cor="#E0E0E0";
		else				$cor="#F6F6F6";
	
		$sql2="SELECT products_families.*,  products_families_lang.*, products_families.ID, products_families_lang.ID as IDD 
		FROM products_families INNER JOIN products_families_lang ON products_families_lang.ID_PAI =products_families.ID 
		where products_families.ID='".$org."'";
		$hRes=$rs2->db_open($db_link,$DB_name,$sql2);
		$id=$rs2->db_data["Ref"];
		$destaque=$rs2->db_data["Destaque"];
		$Cab2=$rs2->db_data["Cabecalho"];
		$rs2->db_close();
	
		if($id_fornecedor==1) $fornecedor="JPSACOUTO";
		elseif($id_fornecedor==2) $fornecedor="CPC";
		else $fornecedor="----";
		
		echo $fornecedor.'£';
		echo $id.' - '.$Cab2.'£';
		echo $Ref.'£';
		echo $Cab.'£';	
		echo $preco.'£';
		echo $preco_tmp.'£';
		if($destaque)	echo 'SIM£';
		else			echo 'NÃO£';
		
		if($aproved)	echo 'SIM£';
		else			echo 'NÃO£';
		echo "\n";
		$rs->db_next();
		$count++;
	}
	$rs->db_close();
?>