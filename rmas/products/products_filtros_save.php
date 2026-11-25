<?php
	// include "settings.inc.php";

	include "funcoesfiltros.inc.php";
	
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	
	$rs = new DataObj;	
	
	$prod_id = isset($_GET['ID']) ? $_GET['ID'] : ''; // ID DO PRODUTO
	if(($prod_id=='' || $prod_id==0) && isset($prod_id_new) && $prod_id_new > 0) $prod_id = $prod_id_new; 

	$prods = get_produtc_info($prod_id, '', 0, $_POST['m_familia']);

	if(isset($_POST['marca']) || isset($_POST['m_familia']))
	{	
		$marca_id = $_POST['marca']; // MARCA DO PRODUTO
		$fam_id = $_POST['m_familia']; // fam DO PRODUTO		
		$fams = BuscarArvoreFams($fam_id);
		
		if(isset($fams[0])) $fam_id = $fams[0]; // FAMILIA NIVEL 1
		
		if(isset($fams[1])) $subfam_id = $fams[1];  // FAMILIA NIVEL 2
		else $subfam_id = 0;
		
		if(isset($fams[2])) $subfam_id2 = $fams[2];  // FAMILIA NIVEL 3
		else $subfam_id2 = 0;
		
		if(isset($fams[3])) $subfam_id3 = $fams[3];  // FAMILIA NIVEL 4
		else $subfam_id3 = 0;
		
		
		$filtros_rel = ''; $filtros_rel2 = '';		
		
		if($prod_id && $marca_id && $fam_id)
		{			
			$sql = 'UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_id.'"';
			$res = Runquery($hConn,$GLOBALS['DB_name'],$sql);
			
			for($a=0; $a<count($filtros_arr); $a++)
			{
				$cat_id = isset($filtros_arr[$a]) ? $filtros_arr[$a] : '';
				$valor_id = isset($valores_arr[$a]) ? $valores_arr[$a] : '';
			
				$opt = isset($opcoes_arr[$a]) ? $opcoes_arr[$a] : '';
				
				inserefiltro($prod_id, $cat_id, $valor_id, $fam_id, $subfam_id, $subfam_id2,  $subfam_id3, $marca_id, '', '', $opt);
				if($valor_id) $filtros_rel .= $valor_id;
				if($valor_id) $filtros_rel .= ';';
				if($valor_id) $filtros_rel2 .= $cat_id.'_'.$valor_id;
				if($valor_id) $filtros_rel2 .= ';';				
				
			}
			for($a=0; $a<count($filtros_arr); $a++)
			{
				$cat_id = isset($filtros_arr[$a]) ? $filtros_arr[$a] : '';
				$valor_id = isset($valores_arr[$a]) ? $valores_arr[$a] : '';			
				$opt = isset($opcoes_arr[$a]) ? $opcoes_arr[$a] : '';				
				if($cat_id) inserefiltro($prod_id, $cat_id, $valor_id, $fam_id, $subfam_id, $subfam_id2,  $subfam_id3, $marca_id, $filtros_rel, $filtros_rel2, $opt);				
			}
			$sql = 'DELETE FROM filters_products WHERE prod_id="'.$prod_id.'" AND FLAG=0';
			$res = Runquery($hConn, $GLOBALS['DB_name'], $sql);
		}
		else echo 'São necessários os seguintes campos: Referência,Familia,Subfamilia e Marca!';	
	}
?>