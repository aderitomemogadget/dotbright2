<?php
function compress_html($compress){$compress = str_replace(array("\n", "\r", "\t", "\o", "\xOB"), '', $compress);return $compress;}
function verifica_aux2($valor1)
{
	return 1;
}
function deleteDir($dirPath)
{
    if (! is_dir($dirPath)) throw new InvalidArgumentException("$dirPath must be a directory");
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')  $dirPath .= '/';
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file)
	{
        if (is_dir($file)) deleteDir($file);
        else unlink($file);
    }
    rmdir($dirPath);
}
function BuscarProdutos($ID=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql = '
			SELECT
				products.*, products_lang.*
			FROM products
				INNER JOIN products_lang ON products.ID=products_lang.ID_PAI
			WHERE
				products_lang.Lang="'.$GLOBALS['site_lang'].'"
				AND products.Aproved=1
				AND products.ID="'.$ID.'"
			';
	$obj->db_open($db_link,$DB_name,$sql);
	return $obj;
}
function getCategoriesProds($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$familia = new DataObj;

	$sql = '
			SELECT
				cat_id,valor_id
			FROM
				filters_products
			WHERE
				prod_id='.$prod_id.'
			GROUP BY
				valor_id
			';
	$familia->db_open($db_link,$DB_name,$sql);
	if(!$familia->EOF())
	{
		$old_cat_id = array('');
		while(!$familia->EOF())
		{
			$valor_id=$familia->db_data['valor_id'];	$valor_nome=buscavalorfiltros($valor_id);

			if($valor_nome) $arr_valores[] = array($familia->db_data['cat_id'].'_'.$valor_id, $valor_nome);

			$familia->db_next();
		}
	}
	$familia->db_close();
	$return='';
	for($a=0; $a < count($arr_valores); $a++)
	{
		if($a > 0) $return .= ',{ "cat" : "'.$arr_valores[$a][1].'","c_id" : "'.$arr_valores[$a][0].'" }';
		else $return .= '{ "cat" : "'.$arr_valores[$a][1].'","c_id" : "'.$arr_valores[$a][0].'" }';
	}
	return $return;
}
function getFamiliesProds($families=array())
{
	$return='';
	for($a=0; $a < count($families); $a++)
	{
		if($a > 0) $return .= ',{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
		else $return .= '{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
	}
	return $return;
}
function bloco_grelha_exclusivos_filtros_data($settings=array(""),$families=array(""))
{
	global $site_http_url;
	// CARACTERISTICAS DO PRODUTO
	$categories_prod = getCategoriesProds($settings["id"]);
	$families_prod = getFamiliesProds($families);

    if($settings["preco"]=='') $settings["pvp"]='null';
	else $settings["pvp"]=numberformat($settings["preco"]);
	if($settings["preco"]=='') $settings["preco"]='null';
	else $settings["preco"]=str_replace(',', '', numberformat($settings["preco"],4));
	if($settings["preco_riscado"]=='') $settings["preco_riscado"]='null';
	else $settings["preco_riscado"]=str_replace(',', '', numberformat($settings["preco_riscado"],4));

	$return =
	'
	{
		"id" : "'.$settings["id"].'",
		"ref" : "'.$settings["ref"].'",
		"price" : '.$settings["preco"].',
		"price_riscado" : '.$settings["preco_riscado"].',
		"pvp" : "'.$settings["pvp"].'",
		"stock": "'.$settings["stock"].'",
		"image":"'.str_replace($site_http_url, "", $settings["imagem"]).'",
		"title":"'.$settings["titulo"].'",
		"subtitle":"'.$settings["subtitulo"].'",
		"caminho" : "'.str_replace($site_http_url, "", $settings["caminho"]).'",
		"novidade" : "'.$settings["novidade"].'",
		"outlet" : "'.$settings["outlet"].'",
		"promo" : "'.$settings["promocao"].'",
		"destaque" : "'.$settings["destaque"].'",
		"travelsize" : "'.$settings["travelsize"].'",
		"tp_novidade_txt" : "'.$settings["tp_novidade_txt"].'",
		"tp_destaque_txt" : "'.$settings["tp_destaque_txt"].'",
		"marca_ordem" : "'.$settings["marca_ordem"].'",
		"m_id" : '.$settings["marca_id"].',
		"c" : ['.$categories_prod.'],
		"f" : ['.$families_prod.']
	}
	';
	return $return;
}

function gera_familias($FAMILIA)
{
	global $site_shell_url;
	if(!isset($_SESSION['ss_site_country'])) $CC = 'PT';
	else $CC = $_SESSION['ss_site_country'];

	$familia_produtos = 'files/filtros_familias_tmp/fam_prods_' . $FAMILIA.'_'.$CC.'.txt';
	$familia_filtros = 'files/filtros_familias_tmp/filtros_prods_' . $FAMILIA.'_'.$CC.'.txt';
	$familia_filtros_c = 'files/filtros_familias_tmp/filtros_prods_' . $FAMILIA.'_c_'.$CC.'.txt';

	$existe = 0;
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$dados = new DataObj; $dados2 = new DataObj;

	$listagem2 = BuscarProdutosFiltros_NOVO($FAMILIA);

	$return = '';
	if(!$listagem2->EOF())
	{
		$ultimalinha_promocoes=0;
		$existe = 0;
		$total = $listagem2->db_rows;
		while(!$listagem2->EOF())
		{
			$prod_id = $listagem2->db_data['prod_id'];
			$fam_id1 = $listagem2->db_data['fam_id1'];

			$marca_id = $listagem2->db_data['marca_id'];
			$families = '';
			if($fam_id1 > 0) $families[] = array(buscacabecalho_cf($fam_id1), $fam_id1);

			if($prod_id)
			{
				$existe++;

				$prods = get_produtc_info($prod_id);
				if(!empty($prods))
				{
					$cache_filename = $site_shell_url.'files/tmp/prod_'.$prod_id.'_'.$GLOBALS['site_lang'].'.txt';
					if(file_exists($cache_filename)) unlink($cache_filename);

					$cb = $prods['prod_nome'];
					$ref 	= $prods['prod_ref'];
					$csb = $prods['prod_subtitulo'];
					$T1 = $prods['prod_descricao'];
					$im = $prods['prd_imgm_new'];

					$precopromo = $prods['prod_precos']['PRECO_ANTIGO'];
					$pvp = $prods['prod_precos']['PVP'];

					if($precopromo) $promo = 1;
					else $promo = 0;

					$novidade = $prods['prod_precos']['NOVO'];
					$destaque = $prods['prod_precos']['DESTAQUE'];
					$travelsize = $prods['prod_precos']['TRAVELSIZE'];
					$outlet = $prods['prod_precos']['OUTLET'];

					$stock 	= $prods['prod_precos']['STOCK'];
					$marca_ordem 	= $prods['prod_marca']['marca_ordem'];

					$tipo_destaque_txt='';
					$tipo_novidade_txt='';
					if($destaque){ $tipo_destaque_txt = $GLOBALS['txt_em_destaque'][$GLOBALS['site_lang']];}
					if($novidade){ $tipo_novidade_txt = $GLOBALS['key_prdtag_new'][$GLOBALS['site_lang']];}


					if(!$promo) $promo = 'null';
					if(!$destaque) $destaque = 'null';
					if(!$novidade) $novidade = 'null';
					if(!$travelsize) $travelsize = 'null';
					if(!$outlet) $outlet = 'null';

					if($pvp=='') $pvp = 0;
					if($stock=='') $stock = 0;

					$settings = array
					(
						'id' => $prod_id,
						'ref' => $ref,
						'marca_id' => $marca_id,
						'titulo' => strip_html_tags(str_replace('"',"'",$cb)),
						'subtitulo' => strip_html_tags(str_replace('"',"'",$csb)),
						'caminho' => seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2),
						'preco' => $pvp,
						'preco_riscado' => $precopromo,
						'imagem' => $im,
						'stock' => $stock,
						'promocao' => $promo,
						'destaque' => $destaque,
						'novidade' => $novidade,
						'outlet' => $outlet,
						'travelsize' => $travelsize,
						'tp_novidade_txt' => $tipo_novidade_txt,
						'tp_destaque_txt' => $tipo_destaque_txt,
						'marca_ordem' => $marca_ordem,
						'category_id' => $category_id,
						'fam_id1' => $fam_id1
					);

					$precos_bloco[] = $pvp;
					$return .= bloco_grelha_exclusivos_filtros_data($settings,$families);
					if($total!=$existe) $return .= ',';

				}
			}
			$listagem2->db_next();
		}
	}
	$listagem2->db_close();


	if($return)
	{
		$script = '<script>var products = ['.compress_html($return).'];</script>';
		$file_name = $GLOBALS['site_shell_url']. $familia_produtos;
		$fh = fopen($file_name, 'w') or die("can't open file");
		fwrite($fh, $script);
		fclose($fh);
		chmod($GLOBALS['site_shell_url']. $familia_produtos, 0755);
	}



	$filtros = '';

	// MARCAS
	$filtros .= FILTRO_MARCAS_NEW($FAMILIA);

	// PRECOS
	$filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($FAMILIA));

	// FILTROS
	$file_name = $GLOBALS['site_shell_url']. $familia_filtros;
	$fh = fopen($file_name, 'w') or die("can't open file");
	fwrite($fh, sanitize_output($filtros));
	fclose($fh);
	chmod($GLOBALS['site_shell_url']. $familia_filtros, 0755);

	// CARACTERISTICAS
	$filtros2 = FILTRO_CARACTERISTICAS_NEW($FAMILIA,0,0,2);

	// FILTROS
	$file_name = $GLOBALS['site_shell_url']. $familia_filtros_c;
	$fh = fopen($file_name, 'w') or die("can't open file");
	fwrite($fh, sanitize_output($filtros2));
	fclose($fh);
	chmod($GLOBALS['site_shell_url']. $familia_filtros_c, 0755);

	//validar criacao ficheiros
	if(!file_exists($GLOBALS['site_shell_url']. $familia_produtos) || !file_exists($GLOBALS['site_shell_url']. $familia_filtros) || !file_exists($GLOBALS['site_shell_url']. $familia_filtros_c))
	{
		if(file_exists($GLOBALS['site_shell_url']. $familia_produtos)) unlink($GLOBALS['site_shell_url']. $familia_produtos);
		if(file_exists($GLOBALS['site_shell_url']. $familia_filtros)) unlink($GLOBALS['site_shell_url']. $familia_filtros);
		if(file_exists($GLOBALS['site_shell_url']. $familia_filtros_c)) unlink($GLOBALS['site_shell_url']. $familia_filtros_c);
	}
}
function sanitize_output($buffer) {

    $search = array(
        '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        '/[^\S ]+\</s',  // strip whitespaces before tags, except space
        '/(\s)+/s'       // shorten multiple whitespace sequences
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}
function gera_marcas($MARCA)
{
	global $site_shell_url;
	if(!isset($_SESSION['ss_site_country'])) $CC = 'PT';
	else  $CC = $_SESSION['ss_site_country'];
	$marcas_produtos = 'files/filtros_marcas_tmp/marca_prods_' . $MARCA.'_'.$CC.'.txt';
	$marcas_filtros = 'files/filtros_marcas_tmp/marca_filtros_prods_' . $MARCA.'_'.$CC.'.txt';
	$marcas_filtros_c = 'files/filtros_marcas_tmp/marca_filtros_prods_' . $MARCA.'_c_'.$CC.'.txt';

	$existe = 0;
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados = new DataObj;$dados2 = new DataObj;

	$listagem2 = BuscarProdutosFiltros_NOVO($MARCA,1);

	$return = '';
	if(!$listagem2->EOF())
	{
		$ultimalinha_promocoes=0;
		$existe = 0;
		$total = $listagem2->db_rows;
		while(!$listagem2->EOF())
		{
			$prod_id = $listagem2->db_data['prod_id'];
			$prod_id = $listagem2->db_data['prod_id'];
			$fam_id1 = $listagem2->db_data['fam_id1'];
			$marca_id = $listagem2->db_data['marca_id'];

			$families = '';
			if($fam_id1 > 0) $families[] = array(buscacabecalho_cf($fam_id1), $fam_id1);

			if($prod_id)
			{
				$existe++;

				$prods = get_produtc_info($prod_id);

				if(!empty($prods))
				{
					$cache_filename = $site_shell_url.'files/tmp/prod_'.$prod_id.'_'.$GLOBALS['site_lang'].'.txt';
					if(file_exists($cache_filename)) unlink($cache_filename);

					$cb = $prods['prod_nome'];
					$ref 	= $prods['prod_ref'];
					$csb = $prods['prod_subtitulo'];
					$T1 = $prods['prod_descricao'];
					$im = $prods['prd_imgm_new'];

					$precopromo = $prods['prod_precos']['PRECO_ANTIGO'];
					$pvp = $prods['prod_precos']['PVP'];

					if($precopromo) $promo = 1;
					else $promo = 0;

					$novidade = $prods['prod_precos']['NOVO'];
					$destaque = $prods['prod_precos']['DESTAQUE'];
					$travelsize = $prods['prod_precos']['TRAVELSIZE'];
					$outlet = $prods['prod_precos']['OUTLET'];

					$stock 	= $prods['prod_precos']['STOCK'];

					$tipo_destaque_txt='';
					$tipo_novidade_txt='';
					if($destaque){ $tipo_destaque_txt = $GLOBALS['txt_em_destaque'][$GLOBALS['site_lang']];}
					if($novidade){ $tipo_novidade_txt = $GLOBALS['key_prdtag_new'][$GLOBALS['site_lang']];}


					if(!$promo) $promo = 'null';
					if(!$destaque) $destaque = 'null';
					if(!$novidade) $novidade = 'null';
					if(!$travelsize) $travelsize = 'null';
					if(!$outlet) $outlet = 'null';

					if($pvp=='') $pvp = 0;
					if($stock=='') $stock = 0;

					$settings = array(
						'id' => $prod_id,
						'ref' => $ref,
						'marca_id' => $marca_id,
						'titulo' => strip_html_tags(str_replace('"',"'",$cb)),
						'subtitulo' => strip_html_tags(str_replace('"',"'",$csb)),
						'caminho' => seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prod_id, 2),
						'preco' => $pvp,
						'preco_riscado' => $precopromo,
						'imagem' => $im,
						'stock' => $stock,
						'promocao' => $promo,
						'destaque' => $destaque,
						'novidade' => $novidade,
						'outlet' => $outlet,
						'travelsize' => $travelsize,
						'tp_novidade_txt' => $tipo_novidade_txt,
						'tp_destaque_txt' => $tipo_destaque_txt,
						'category_id' => $category_id,
						'fam_id1' => $fam_id1
					);

					$precos_bloco[] = $pvp;
					$return .= bloco_grelha_exclusivos_filtros_data($settings,$families);
					if($total!=$existe) $return .= ',';

				}
			}
			$listagem2->db_next();
		}
	}
	$listagem2->db_close();
	$filtros = '';

	if($return)
	{
		// PRODUTOS POR MARCA
		$script = '<script>var products = ['.compress_html($return).'];</script>';
		$file_name = $GLOBALS['site_shell_url']. $marcas_produtos;
		$fh = fopen($file_name, 'w') or die("can't open file");
		fwrite($fh, $script);
		fclose($fh);
		chmod($GLOBALS['site_shell_url']. $marcas_produtos, 0755);
	}

	// MARCAS
	//$filtros .= FILTRO_MARCAS_NEW($MARCA,1);

	// PRECOS
	$filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($MARCA,1));

	// FILTROS
	$file_name = $GLOBALS['site_shell_url']. $marcas_filtros;
	$fh = fopen($file_name, 'w') or die("can't open file");
	fwrite($fh, sanitize_output($filtros));
	fclose($fh);
	chmod($GLOBALS['site_shell_url']. $marcas_filtros, 0755);

	// CARACTERISTICAS
	$filtros2 = FILTRO_CARACTERISTICAS_NEW($MARCA,1,0,2);

	$file_name = $GLOBALS['site_shell_url']. $marcas_filtros_c;
	$fh = fopen($file_name, 'w') or die("can't open file");
	fwrite($fh, sanitize_output($filtros2));
	fclose($fh);
	chmod($GLOBALS['site_shell_url']. $marcas_filtros_c, 0755);

	//validar criacao ficheiros
	if(!file_exists($GLOBALS['site_shell_url']. $marcas_produtos) || !file_exists($GLOBALS['site_shell_url']. $marcas_filtros) || !file_exists($GLOBALS['site_shell_url']. $marcas_filtros_c))
	{
		if(file_exists($GLOBALS['site_shell_url']. $marcas_produtos)) unlink($GLOBALS['site_shell_url']. $marcas_produtos);
		if(file_exists($GLOBALS['site_shell_url']. $marcas_filtros)) unlink($GLOBALS['site_shell_url']. $marcas_filtros);
		if(file_exists($GLOBALS['site_shell_url']. $marcas_filtros_c)) unlink($GLOBALS['site_shell_url']. $marcas_filtros_c);
	}
}
?>
