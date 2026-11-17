<?php
	//Primeiro vamos remover os produtos que são para remover e no final invocamos outro script para atualizar os novos produtos

	$importador=1;
	$path=dirname( __FILE__ ) . '/../../core/';
	require_once $path.'core_files.php';
	require_once $path.'../admin/products/funcoes.inc.php'; //Vamos carregar as funcoes do backoffice para criar ou alterar produtos
	require_once $path.'../admin/products/filtros/funcoes.inc.php'; //Vamos carregar as funcoes do backoffice para criar ou alterar produtos

	error_reporting(0);
	set_time_limit(0);
	header('Content-Type: text/html; charset=utf-8');
	ini_set("max_execution_time","6000000");
	ini_set("max_input_time","6000000");
	ini_set("memory_limit","-1");

	$linguas_importa[]='pt';
	$Erro='';
	$Estado=1;

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$rs4=new DataObj;

	$sql = "DELETE FROM products_sync WHERE fam_desc1='LIXEIRA'";
	$res=Runquery($db_link,$DB_name,$sql);

	$sql = "DELETE FROM products_sync WHERE fam_desc1 like '%NÃO VISIVEL'";
	$res=Runquery($db_link,$DB_name,$sql);

	

	
	// VAI LER TODOS OS PRODUTOS ALTERADOS APOS ESSA DATA
	$importar=0;
	$sql='SELECT
				REF,fam_id1,fam_id2,fam_id3,fam_id4,fam_id5,fam_desc1,fam_desc2,fam_desc3,fam_desc4,fam_desc5,Stock_Arm,STOCK_DATE,PVP1,PVP2,PVP3,PVP4,PVP5,PVP7,IVA,TaxableGroupID,ItemTax,ARMAZEM
			FROM
				products_sync
			WHERE
				(PVP1 > 0 OR PVP2 > 0 OR PVP3 > 0 OR PVP4 > 0) 
				OR (REF LIKE "%DIAGNOSTICO%" AND fam_id1=84)
			ORDER BY
				InformationField2 DESC ';

	$rs->db_open($db_link,$DB_name,$sql);
	$total=$rs->db_rows;
	$resto=0;
	if(!$rs->EOF())
	{
		if($total < 10) die(); // SE FOR DETECTADO POUCOS PRODUTOS MORRE AQUI
		$importar=1;


		// VAMOS COLOCAR A FLAG DE TODOS A ZERO E SÓ FINAL VAMOS COLOCAR INATIVOS AQUELES CUJO A FLAG CONTINUAR A 0,APENAS PRODUTOS DE INTEGRAÇÃO É QUE SERÃO AFETADOS
		$res=Runquery($db_link,$DB_name,'UPDATE products SET Flag=0 WHERE Sync=0 AND codigo_importacao=""');
		$res=Runquery($db_link,$DB_name,'UPDATE products_families SET FLAG=0 WHERE data_atualizacao != "0000-00-00 00:00:00" AND ID_Integracao > 0');

		$Data_fim=date('Y-m-d H:i:s');
		while(!$rs->EOF())
		{
			$REF=addslashes($rs->db_data['REF']);
			$fam_id1=$rs->db_data['fam_id1'];
			$fam_id2=$rs->db_data['fam_id2'];
			$fam_id3=$rs->db_data['fam_id3'];
			$fam_id4=$rs->db_data['fam_id4'];
			$fam_id5=$rs->db_data['fam_id5'];
			$fam_desc1=$rs->db_data['fam_desc1'];
			$fam_desc2=$rs->db_data['fam_desc2'];
			$fam_desc3=$rs->db_data['fam_desc3'];
			$fam_desc4=$rs->db_data['fam_desc4'];
			$fam_desc5=$rs->db_data['fam_desc5'];
			
			$Stock_novo=$rs->db_data['Stock_Arm'];
			$ARMAZEM=$rs->db_data['ARMAZEM'];
			$IVA=$rs->db_data['IVA'];
			$ItemTax=$rs->db_data['ItemTax'];
			$TaxableGroupID=$rs->db_data['TaxableGroupID'];
			if($TaxableGroupID == 18) // TAXA COPIA PRIVADA
			{
				$PVP1=(($rs->db_data['PVP1'] * (1 + ($IVA / 100))) + $ItemTax); // PVP
				$PVP2=(($rs->db_data['PVP2'] * (1 + ($IVA / 100))) + $ItemTax); // REVENDA
				$PVP3=(($rs->db_data['PVP3'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP4=(($rs->db_data['PVP4'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP5=(($rs->db_data['PVP5'] * (1 + ($IVA / 100))) + $ItemTax); // PARCEIROS
				$PVP7=(($rs->db_data['PVP7'] * (1 + ($IVA / 100))) + $ItemTax); // PRECODOTBRIGHT
			}
			else
			{
				$PVP1=$rs->db_data['PVP1'] * (1 + ($IVA / 100)); // PVP
				$PVP2=$rs->db_data['PVP2'] * (1 + ($IVA / 100)); // REVENDA
				$PVP3=$rs->db_data['PVP3'] * (1 + ($IVA / 100)); // PARCEIROS
				$PVP4=$rs->db_data['PVP4'] * (1 + ($IVA / 100)); // PARCEIROS
				$PVP5=$rs->db_data['PVP5'] * (1 + ($IVA / 100)); // PARCEIROS
				$PVP7=$rs->db_data['PVP7'] * (1 + ($IVA / 100)); // PRECODOTBRIGHT
			}


			echo '['.$total.': '.$REF.']';
			echo "\n";


			$sql='UPDATE products SET Data_fim="'.$Data_fim.'",udtime="'.date("Y-m-d H:i:s").'",Flag=1 WHERE Ref="'.$REF.'"';
			//echo $sql."\n";
			$res=Runquery($db_link,$DB_name,$sql);


			//--------------agora as familias:
			$active=1;
			if($PVP1 > 0 || (substr_count($REF,'DIAGNOSTICO') && $fam_id1 == 84)) $active=1; else $active=0;
			if($fam_id1 == 3 || $fam_id1 == 84){$active=1;}


			//elseif($Stock_novo <= 0) $active=0;


			$fam_id=0;
			$subfam_id=0;
			$subfam_id2=0;
			$subfam_id3=0;
			$subfam_id4=0;
			$familia_id=0;

			$fam_id=detect_family($fam_id1,$fam_desc1,0,$fam_id1);
			if($fam_id > 0 && $fam_id2 > 0)
			{
				$subfam_id=detect_family($fam_id2,$fam_desc2,$fam_id,$fam_id1.'_'.$fam_id2);
				if($subfam_id > 0 && $fam_id3 > 0)
				{
					$subfam_id2=detect_family($fam_id3,$fam_desc3,$subfam_id,$fam_id1.'_'.$fam_id2.'_'.$fam_id3);
					if($subfam_id2 > 0 && $fam_id4 > 0)
					{
						$subfam_id3=detect_family($fam_id4,$fam_desc4,$subfam_id2,$fam_id1.'_'.$fam_id2.'_'.$fam_id3.'_'.$fam_id4);
						if($subfam_id3 > 0 && $fam_id5 > 0)
						{
							$subfam_id4=detect_family($fam_id5,$fam_desc5,$subfam_id3,$fam_id1.'_'.$fam_id2.'_'.$fam_id3.'_'.$fam_id4.'_'.$fam_id5);
						}
					}
				}
			}




			$rs->db_next();
			$total--;
		}
	}
	$rs->db_close();

	// SE CONSEGUIU ACTUALIZAR TODOS OS PRODUTOS
	if($total <= 0 && $importar == 1)
	{
		//$res=Runquery($db_link,$DB_name,'UPDATE products SET Aproved=1 WHERE Flag=1 AND Sync=0 AND codigo_importacao=""');
		$res=Runquery($db_link,$DB_name,'UPDATE products SET Aproved=0 WHERE Flag=0 AND Sync=0 AND codigo_importacao=""');
		$res=Runquery($db_link,$DB_name,'UPDATE products_families SET visivel=0 WHERE FLAG=0 AND ID_Integracao > 0');	
 		//$res=Runquery($db_link,$DB_name,'UPDATE products_families SET visivel=1 WHERE FLAG=1 AND ID_Integracao > 0');
	}


	function detect_family($FAM_CODE,$FAM_NOME,$FAM_PAI=0,$CODE='')
	{
		$FAM_CODE=trim($FAM_CODE);
		echo "\nCODE: $CODE - FAM_CODE: $FAM_CODE - FAM_PAI:$FAM_PAI - FAM_NOME: $FAM_NOME";

		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$visivel=1;
		$fam_id=0;

		$udate=date('Y-m-d H:i:s');
		if($FAM_CODE != '')
		{
			if(substr_count($FAM_NOME,'NÃO VISIVEL'))$visivel=0;
			if(substr_count($FAM_NOME,'NAO VISIVEL'))$visivel=0;
			if(substr_count($FAM_NOME,'não visivel'))$visivel=0;
			if(substr_count($FAM_NOME,'nao visivel'))$visivel=0;
			if(substr_count($FAM_NOME,'Lixo'))$visivel=0;
			if(substr_count($FAM_NOME,'lixo'))$visivel=0;
			if(substr_count($FAM_NOME,'LIXEIRA'))$visivel=0;



			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$sql='SELECT ID FROM products_families WHERE ID_Integracao="'.$FAM_CODE.'" AND Ref_Integracao="'.$CODE.'"';
			
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$fam_id=$rs->db_data['ID'];

				if($visivel==0)
				{
					$sql='UPDATE products SET Aproved=0 WHERE ID_ORG="'.$fam_id.'" AND Flag=0 AND Sync=0 AND codigo_importacao=""';
					$res=Runquery($db_link,$DB_name,$sql);
				}


				if($FAM_PAI > 0) $sql='UPDATE products_families SET ID_ORG="'.$FAM_PAI.'",FLAG=1,visivel="'.$visivel.'",data_atualizacao="'.$udate.'",Ref_Integracao2="'.$CODE.'" WHERE ID_Integracao="'.$FAM_CODE.'" AND Ref_Integracao="'.$CODE.'"';
				else $sql='UPDATE products_families SET FLAG=1,visivel="'.$visivel.'",data_atualizacao="'.$udate.'",Ref_Integracao2="'.$CODE.'" WHERE ID_Integracao="'.$FAM_CODE.'" AND Ref_Integracao="'.$CODE.'"';
				echo " JA EXISTE - VOU ATUALIZAR";
				$res=Runquery($db_link,$DB_name,$sql);
			}
			else
			{
				$sql='INSERT INTO products_families (ID_ORG,visivel,ID_integracao,data_atualizacao,Ref_Integracao,Ref_Integracao2,FLAG) VALUES ( "'.$FAM_PAI.'","'.$visivel.'","'.$FAM_CODE.'","'.$udate.'","'.$CODE.'","'.$CODE.'",1)';
				echo " N EXISTE - VOU CRIAR";
				$res=Runquery($db_link,$DB_name,$sql);
				$fam_id=db_get_last_insert_id($db_link);
			}
			$rs->db_close(1);
		}
		return $fam_id;
	}

	if (getenv('DOTBRIGHT_SAGE_MANAGED') !== '1') {
		exec('/usr/bin/php '.escapeshellarg($path.'crons/sync_prods_2.php').' > /dev/null 2>&1 &');
	}
?>