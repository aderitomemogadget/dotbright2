<?
	include "settings.inc.php";
	require_once("cache/funcoes.inc.php");


	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$prod_ref=isset($_POST['referencia']) ? $_POST['referencia'] : ''; // REFERENCIA DO PRODUTO
	$prod_id=isset($_POST['idproduto']) ? $_POST['idproduto'] : ''; // ID DO PRODUTO
	$marca_id=isset($_POST['marca']) ? $_POST['marca'] : ''; // MARCA DO PRODUTO
	$fam_id=isset($_POST['familia']) ? $_POST['familia'] : ''; // FAMILIA NIVEL 1
	$subfam_id=isset($_POST['subfamilia']) ? $_POST['subfamilia'] : '';  // FAMILIA NIVEL 2
	$filtros_rel=''; $filtros_rel2='';
	function inserefiltro($prod_id,$cat_id,$valor_id,$fam_id,$subfam_id,$marca_id,$filtros_rel='',$filtros_rel2='')
	{
		if($prod_id && $fam_id && $subfam_id && $marca_id)cria_filtro_produto($prod_id,$cat_id,$valor_id,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
	}
	function cria_filtro_produto($prod_id,$cat_id,$valor_id,$fam_id,$subfam_id,$marca_id,$filtros_rel='',$filtros_rel2='')
	{
		$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$sql ='SELECT cat_id,valor_id,prod_id FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'" AND(fam_id1="'.$fam_id.'" OR fam_id2="'.$subfam_id.'")';
		$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
		if($rs->EOF())
		{
			$sql ='INSERT INTO filters_products(
						cat_id,
						valor_id,
						prod_id,
						fam_id1,
						fam_id2,
						marca_id,
						filtros_rel2,
						FLAG,
						filtro_active
					)
					VALUES(
						"'.$cat_id.'",
						"'.$valor_id.'",
						"'.$prod_id.'",
						"'.$fam_id.'",
						"'.$subfam_id.'",
						"'.$marca_id.'",
						"'.$filtros_rel2.'",
						1,
						1
					)
				';
			$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
			$sql ='UPDATE products SET ID_Marca='.$marca_id.',Aproved=1 WHERE ID="'.$prod_id.'"';
			$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
		}
		else
		{

			$sql ='UPDATE filters_products SET
					fam_id1="'.$fam_id.'",
					fam_id2="'.$subfam_id.'",
					marca_id="'.$marca_id.'",
					filtros_rel2="'.$filtros_rel2.'",
					FLAG=1,
					filtro_active=1
				WHERE
					cat_id="'.$cat_id.'"
					AND
					valor_id="'.$valor_id.'"
					AND
					prod_id="'.$prod_id.'"
				';
			$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
			$sql ='UPDATE products SET ID_Marca='.$marca_id.',Aproved=1 WHERE ID="'.$prod_id.'"';
			$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
		}
		if($res)
		{
			$rs=new DataObj;
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
			passthru('(php '.$GLOBALS['site_shell_url'].'admin/filters/cache/do_familias.php  > /dev/null &)&&(php '.$GLOBALS['site_shell_url'].'admin/filters/cache/do_marcas.php  > /dev/null &)');
		}
	}
	if($prod_id && $marca_id && $fam_id && $subfam_id)
	{
		// FILTROS DO PRODUTO
		$cat_id1=isset($_POST['filtro1categoria']) ? $_POST['filtro1categoria'] : '';
		$valor_id1=isset($_POST['filtro1valor']) ? $_POST['filtro1valor'] : '';
		$cat_id2=isset($_POST['filtro2categoria']) ? $_POST['filtro2categoria'] : '';
		$valor_id2=isset($_POST['filtro2valor']) ? $_POST['filtro2valor'] : '';
		$cat_id3=isset($_POST['filtro3categoria']) ? $_POST['filtro3categoria'] : '';
		$valor_id3=isset($_POST['filtro3valor']) ? $_POST['filtro3valor'] : '';
		$cat_id4=isset($_POST['filtro4categoria']) ? $_POST['filtro4categoria'] : '';
		$valor_id4=isset($_POST['filtro4valor']) ? $_POST['filtro4valor'] : '';
		$cat_id5=isset($_POST['filtro5categoria']) ? $_POST['filtro5categoria'] : '';
		$valor_id5=isset($_POST['filtro5valor']) ? $_POST['filtro5valor'] : '';
		$cat_id6=isset($_POST['filtro6categoria']) ? $_POST['filtro6categoria'] : '';
		$valor_id6=isset($_POST['filtro6valor']) ? $_POST['filtro6valor'] : '';
		$cat_id7=isset($_POST['filtro7categoria']) ? $_POST['filtro7categoria'] : '';
		$valor_id7=isset($_POST['filtro7valor']) ? $_POST['filtro7valor'] : '';
		$cat_id8=isset($_POST['filtro8categoria']) ? $_POST['filtro8categoria'] : '';
		$valor_id8=isset($_POST['filtro8valor']) ? $_POST['filtro8valor'] : '';
		$cat_id9=isset($_POST['filtro9categoria']) ? $_POST['filtro9categoria'] : '';
		$valor_id9=isset($_POST['filtro9valor']) ? $_POST['filtro9valor'] : '';
		$cat_id10=isset($_POST['filtro10categoria']) ? $_POST['filtro10categoria'] : '';
		$valor_id10=isset($_POST['filtro10valor']) ? $_POST['filtro10valor'] : '';
		$cat_id11=isset($_POST['filtro11categoria']) ? $_POST['filtro11categoria'] : '';
		$valor_id11=isset($_POST['filtro11valor']) ? $_POST['filtro11valor'] : '';
		$sql='UPDATE filters_products SET FLAG=0 WHERE prod_id="'.$prod_id.'"';
		$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
		inserefiltro($prod_id,$cat_id1,$valor_id1,$fam_id,$subfam_id,$marca_id);
		if($valor_id1) $filtros_rel.=$valor_id1;
		if($valor_id1) $filtros_rel.=';';
		if($valor_id1) $filtros_rel2.=$cat_id1.'_'.$valor_id1;
		if($valor_id1) $filtros_rel2.=';';
		if($cat_id2)
		{
			inserefiltro($prod_id,$cat_id2,$valor_id2,$fam_id,$subfam_id,$marca_id);
			if($valor_id2) $filtros_rel.=$valor_id2;
			if($valor_id2) $filtros_rel.=';';
			if($valor_id2) $filtros_rel2.=$cat_id2.'_'.$valor_id2;
			if($valor_id2) $filtros_rel2.=';';
		}
		if($cat_id3)
		{
			inserefiltro($prod_id,$cat_id3,$valor_id3,$fam_id,$subfam_id,$marca_id);
			if($valor_id3) $filtros_rel.=$valor_id3;
			if($valor_id3) $filtros_rel.=';';
			if($valor_id3) $filtros_rel2.=$cat_id3.'_'.$valor_id3;
			if($valor_id3) $filtros_rel2.=';';
		}
		if($cat_id4)
		{
			inserefiltro($prod_id,$cat_id4,$valor_id4,$fam_id,$subfam_id,$marca_id);
			if($valor_id4) $filtros_rel.=$valor_id4;
			if($valor_id4) $filtros_rel.=';';
			if($valor_id4) $filtros_rel2.=$cat_id4.'_'.$valor_id4;
			if($valor_id4) $filtros_rel2.=';';
		}
		if($cat_id5)
		{
			inserefiltro($prod_id,$cat_id5,$valor_id5,$fam_id,$subfam_id,$marca_id);
			if($valor_id5) $filtros_rel.=$valor_id5;
			if($valor_id5) $filtros_rel.=';';
			if($valor_id5) $filtros_rel2.=$cat_id5.'_'.$valor_id5;
			if($valor_id5) $filtros_rel2.=';';
		}
		if($cat_id6)
		{
			inserefiltro($prod_id,$cat_id6,$valor_id6,$fam_id,$subfam_id,$marca_id);
			if($valor_id6) $filtros_rel.=$valor_id6;
			if($valor_id6) $filtros_rel.=';';
			if($valor_id6) $filtros_rel2.=$cat_id6.'_'.$valor_id6;
			if($valor_id6) $filtros_rel2.=';';
		}
		if($cat_id7)
		{
			inserefiltro($prod_id,$cat_id7,$valor_id7,$fam_id,$subfam_id,$marca_id);
			if($valor_id7) $filtros_rel.=$valor_id7;
			if($valor_id7) $filtros_rel.=';';
			if($valor_id7) $filtros_rel2.=$cat_id7.'_'.$valor_id7;
			if($valor_id7) $filtros_rel2.=';';
		}
		if($cat_id8)
		{
			inserefiltro($prod_id,$cat_id8,$valor_id8,$fam_id,$subfam_id,$marca_id);
			if($valor_id8) $filtros_rel.=$valor_id8;
			if($valor_id8) $filtros_rel.=';';
			if($valor_id8) $filtros_rel2.=$cat_id8.'_'.$valor_id8;
			if($valor_id8) $filtros_rel2.=';';
		}
		if($cat_id9)
		{
			inserefiltro($prod_id,$cat_id9,$valor_id9,$fam_id,$subfam_id,$marca_id);
			if($valor_id9) $filtros_rel.=$valor_id9;
			if($valor_id9) $filtros_rel.=';';
			if($valor_id9) $filtros_rel2.=$cat_id9.'_'.$valor_id9;
			if($valor_id9) $filtros_rel2.=';';
		}
		if($cat_id10)
		{
			inserefiltro($prod_id,$cat_id10,$valor_id10,$fam_id,$subfam_id,$marca_id);
			if($valor_id10) $filtros_rel.=$valor_id10;
			if($valor_id10) $filtros_rel.=';';
			if($valor_id10) $filtros_rel2.=$cat_id10.'_'.$valor_id10;
			if($valor_id10) $filtros_rel2.=';';
		}
		if($cat_id11)
		{
			inserefiltro($prod_id,$cat_id11,$valor_id11,$fam_id,$subfam_id,$marca_id);
			if($valor_id11) $filtros_rel .=$valor_id11;
			if($valor_id11) $filtros_rel .=';';
			if($valor_id11) $filtros_rel2.=$cat_id11.'_'.$valor_id11;
			if($valor_id11) $filtros_rel2.=';';
		}
		if($filtros_rel2)
		{
			if($cat_id1) inserefiltro($prod_id,$cat_id1,$valor_id1,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id2) inserefiltro($prod_id,$cat_id2,$valor_id2,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id3) inserefiltro($prod_id,$cat_id3,$valor_id3,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id4) inserefiltro($prod_id,$cat_id4,$valor_id4,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id5) inserefiltro($prod_id,$cat_id5,$valor_id5,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id6) inserefiltro($prod_id,$cat_id6,$valor_id6,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id7) inserefiltro($prod_id,$cat_id7,$valor_id7,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id8) inserefiltro($prod_id,$cat_id8,$valor_id8,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id9) inserefiltro($prod_id,$cat_id9,$valor_id9,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id10) inserefiltro($prod_id,$cat_id10,$valor_id10,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
			if($cat_id11) inserefiltro($prod_id,$cat_id11,$valor_id11,$fam_id,$subfam_id,$marca_id,$filtros_rel,$filtros_rel2);
		}
		$sql='DELETE FROM filters_products WHERE prod_id="'.$prod_id.'" AND FLAG=0';
		$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
	}
	else echo 'São necessários os seguintes campos: Referência,Familia,Subfamilia e Marca!';
?>