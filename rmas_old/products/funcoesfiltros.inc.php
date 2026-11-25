<?php
function BuscarArvoreFams($id_org)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;    
	
	$pai = $id_org;
	while($pai > 0)
	{
		$id_org=$pai;
		$sql="SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai=$obj->db_data["ID_ORG"];
		$arvore2[]=$obj->db_data["ID"];
	}
	$output="";
	$t = (count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();
	return $output;
}
function inserefiltro($prod_id=0,$cat_id=0,$valor_id=0,$fam_id=0,$subfam_id=0,$subfam_id2=0,$subfam_id3=0,$marca_id=0,$filtros_rel='',$filtros_rel2='', $opt=0)
{
	if($prod_id > 0) cria_filtro_produto($prod_id, $cat_id, $valor_id, $fam_id, $subfam_id, $subfam_id2, $subfam_id3, $marca_id, $filtros_rel, $filtros_rel2, $opt);
}
function cria_filtro_produto($prod_id=0,$cat_id=0,$valor_id=0,$fam_id=0,$subfam_id=0,$subfam_id2=0,$subfam_id3=0,$marca_id=0,$filtros_rel='',$filtros_rel2='', $opt=0)
{
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
		
	if($subfam_id3 > 0) 	$sql = 'SELECT cat_id, valor_id, prod_id FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'" AND (fam_id1="'.$fam_id.'" OR fam_id2="'.$subfam_id.'" OR fam_id3="'.$subfam_id2.'" OR fam_id4="'.$subfam_id3.'")';
	elseif($subfam_id2 > 0) $sql = 'SELECT cat_id, valor_id, prod_id FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'" AND (fam_id1="'.$fam_id.'" OR fam_id2="'.$subfam_id.'" OR fam_id3="'.$subfam_id2.'")';
	elseif($subfam_id > 0) 	$sql = 'SELECT cat_id, valor_id, prod_id FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'" AND (fam_id1="'.$fam_id.'" OR fam_id2="'.$subfam_id.'")';
	else 					$sql = 'SELECT cat_id, valor_id, prod_id FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'" AND fam_id1="'.$fam_id.'"';

	$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
	if($rs->EOF())
	{
		$sql ='INSERT INTO filters_products
				(
					cat_id,
					valor_id,
					prod_id,
					fam_id1,
					fam_id2,
					fam_id3,
					fam_id4,
					marca_id,
					filtros_rel2,
					FLAG,
					filtro_active,
					filtro_area
				)
				VALUES
				(
					"'.$cat_id.'",
					"'.$valor_id.'",
					"'.$prod_id.'",
					"'.$fam_id.'",
					"'.$subfam_id.'",
					"'.$subfam_id2.'",
					"'.$subfam_id3.'",
					"'.$marca_id.'",
					"'.$filtros_rel2.'",
					1,
					1,
					"'.$opt.'"
				)
			';
		$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
	}
	else
	{		
		$sql = '
				UPDATE filters_products 
				SET
					fam_id1="'.$fam_id.'",
					fam_id2="'.$subfam_id.'",
					fam_id3="'.$subfam_id2.'",
					fam_id4="'.$subfam_id3.'",
					marca_id="'.$marca_id.'",
					filtros_rel2="'.$filtros_rel2.'",
					FLAG=1,
					filtro_active=1,
					filtro_area="'.$opt.'"
				WHERE 
					cat_id="'.$cat_id.'" 
					AND 
					valor_id="'.$valor_id.'" 
					AND 
					prod_id="'.$prod_id.'"
			';

		$res = Runquery($hConn,$GLOBALS['DB_name'],$sql);
	}
	$rs->db_close();
	
	if($res)
	{		
		if($subfam_id<=0) $subfam_id = $fam_id;
		$rs = new DataObj;
		$sql ='SELECT * FROM products_families_obj WHERE ID_PROD="'.$prod_id.'"';
		$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
		if($rs->EOF())
		{
			$sql="INSERT INTO products_families_obj (ID_ORG,ID_PROD) VALUES ('".$subfam_id."','".$prod_id."')";
			if(!Runquery($hConn,$GLOBALS['DB_name'],$sql)) echo "Data Base ERROR: " . GetError($hConn)."\n";	
		}
		else
		{
			$sql="UPDATE products_families_obj  SET ID_ORG='".$subfam_id."' WHERE ID_PROD='".$prod_id."'";
			if(!Runquery($hConn,$GLOBALS['DB_name'],$sql)) echo "Data Base ERROR: " . GetError($hConn)."\n";				
		}
		$rs->db_close();
	}
}
?>