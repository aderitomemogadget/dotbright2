<?php
function compress_html($compress){$compress = str_replace(array("\n","\r","\t","\o","\xOB"),'',$compress);return $compress;}
function verifica_aux2($valor1)
{
	return 1;
}
function BuscarArvoreFams_fam($id_org)
{
	$output[] = $id_org; // a propria

	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	$sql = "SELECT ID,ID_ORG FROM products_families WHERE ID_ORG=$id_org";
	$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
	while(!$obj->EOF())
	{
		$output[] = $obj->db_data["ID"];

		$out = BuscarArvoreFams_fam($obj->db_data["ID"]);
		if(!empty($out)) $output[] = $out;

		$obj->db_next();
	}
	$obj->db_close();

	return $output;
}
function array_flatten_fam($array)
{
   $return = array();
   foreach ($array as $key => $value)
   {
       if (is_array($value)){ $return = array_merge($return,array_flatten_fam($value));}
       else {$return[$key] = $value;}
   }
   return $return;
}
function coloca_margens($id_og=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link =  db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;	$rs2 = new DataObj;

	$fams1 = BuscarArvoreFams_fam($id_og);
	$fams2[] = $id_og;
	if(!empty($fams1) && !empty($fams2)) $fams = array_merge($fams1,$fams2);
	else $fams = $fams2;

	$fams = array_flatten_fam($fams);


	if(!empty($fams))
	{
		for($a=0; $a<count($fams); $a++)
		{
			if($a==0) $vars .= 'pfj.ID_ORG="'.$fams[$a].'"';
			else $vars .= ' OR pfj.ID_ORG="'.$fams[$a].'"';
		}

		$sql = 'SELECT p.ID,p.ID_Marca FROM products p INNER JOIN products_families_obj pfj ON pfj.ID_PROD=p.ID WHERE p.Aproved=1 AND ('.$vars.')';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$ID_Marca = $rs->db_data['ID_Marca'];
//				gera_produtos($rs->db_data['ID'],0,$ID_Marca,1);
				$rs->db_next();
			}
		}
		$rs->db_close();
	}
}
function deleteDir($dirPath)
{
   /*  if (! is_dir($dirPath)) throw new InvalidArgumentException("$dirPath must be a directory");
    if (substr($dirPath,strlen($dirPath) - 1,1) != '/')  $dirPath .= '/';
    $files = glob($dirPath . '*',GLOB_MARK);
    foreach ($files as $file)
	{
        if (is_dir($file)) deleteDir($file);
        else unlink($file);
    }
    rmdir($dirPath); */
}
function BuscarProdutos($ID=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql = '
			SELECT
				products.*,products_lang.*
			FROM products
				INNER JOIN products_lang ON products.ID=products_lang.ID_PAI
			WHERE
				products_lang.Lang="'.$GLOBALS['site_lang'].'"
				AND products.Aproved=1
				AND products.ID="'.$ID.'"
			';
	$obj->db_open($db_link,$GLOBALS['DB_name'],$sql);
	return $obj;
}
function _getCategoriesProds($prod_id=0,$lingua='')
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
	$familia->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$familia->EOF())
	{
		$old_cat_id = array('');
		while(!$familia->EOF())
		{
			$valor_id = $familia->db_data['valor_id']; $valor_nome = buscavalorfiltros($valor_id,$lingua);

			if($valor_nome) $arr_valores[] = array($familia->db_data['cat_id'].'_'.$valor_id,$valor_nome);

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
function _getFamiliesProds($families=array())
{
	$return='';
	for($a=0; $a < count($families); $a++)
	{
		if($a > 0) $return .= ',{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
		else $return .= '{ "fam" : "'.$families[$a][0].'","f_id" : '.$families[$a][1].' }';
	}
	return $return;
}
function bloco_grelha_exclusivos_filtros_data($settings=array(""),$families=array(""),$lingua='')
{
	global $site_http_url;
	// CARACTERISTICAS DO PRODUTO
	$categories_prod = getCategoriesProds($settings["id"],$lingua);
	$families_prod = getFamiliesProds($families,$lingua);

    if($settings["preco"]=='') $settings["pvp"]='null';
	else $settings["pvp"]=numberformat($settings["preco"]);
	if($settings["preco"]=='') $settings["preco"]='null';
	else $settings["preco"]=str_replace(',','',numberformat($settings["preco"],4));
	if($settings["preco_riscado"]=='') $settings["preco_riscado"]='null';
	else $settings["preco_riscado"]=str_replace(',','',numberformat($settings["preco_riscado"],4));

	$return =
	'
	{
		"id" : "'.$settings["id"].'",
		"ord" : '.$settings["ord"].',
		"ref" : "'.$settings["ref"].'",
		"price" : '.$settings["preco"].',
		"price_riscado" : '.$settings["preco_riscado"].',
		"pvp" : "'.$settings["pvp"].'",
		"t_stock": "'.$settings["t_stock"].'",
		"stock": "'.$settings["stock"].'",
		"enc_stock": "'.$settings["prod_enc_sem_stock"].'",
		"image":"'.str_replace($site_http_url,"",$settings["imagem"]).'",
		"title":"'.$settings["titulo"].'",
		"subtitle":"'.$settings["subtitulo"].'",
		"caminho" : "'.str_replace($site_http_url,"",$settings["caminho"]).'",
		"novidade" : "'.$settings["novidade"].'",
		"novidade_data" : "'.$settings["novidade_data"].'",
		"outlet" : "'.$settings["outlet"].'",
		"outlet_data" : "'.$settings["outlet_data"].'",
		"promo" : "'.$settings["promocao"].'",
		"promo_data" : "'.$settings["promocao_data"].'",
		"dodia" : "'.$settings["dodia"].'",
		"dodia_data" : "'.$settings["dodia_data"].'",
		"destaque" : "'.$settings["destaque"].'",
		"travelsize" : "'.$settings["travelsize"].'",
		"bonus" : "'.$settings["bonus"].'",
		"destaque_data" : "'.$settings["destaque_data"].'",
		"travelsize" : "'.$settings["travelsize"].'",
		"marca_ordem" : "'.$settings["marca_ordem"].'",
		"m_id" : "'.$settings["marca_id"].'",
		"c" : ['.$categories_prod.'],
		"f" : ['.$families_prod.'],
		"u_i" : "'.$settings["u_imagetiq"].'",
		"u_c" : "'.$settings["u_catetiq"].'",
		"u_co" : "'.$settings["u_coretiq"].'",
		"carrinho" : "'.$settings["carrinho"].'",
		"bonus" : "'.$settings["bonus"].'",
		"fl" : "'.filtros_ficha_produto_verifica($settings["id"]).'"
	}
	';
	return $return;
}

function BuscarArvoreFams_F($id_org)
{
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	$pai = $id_org;
	while($pai > 0)
	{
		$id_org = $pai;
		$sql = "SELECT ID,ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai = $obj->db_data["ID_ORG"];
		$arvore2[] = $obj->db_data["ID"];
	}
	$output = '';
	$t = (count($arvore2)-1);
	for($a=0; $a<count($arvore2); $a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();


	return $output;
}
function get_linguas_website()
{
	global $site_shell_url;
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;
	$sql = "SELECT Sigla FROM linguas";
	$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
	$ling= array();
	while(!$obj->EOF())
	{
		$ling[]=$obj->db_data["Sigla"];
		$obj->db_next();
	}
	$obj->db_close();
	return $ling;
}
function gera_produtos($prod_id=0,$fam=0,$marca=0,$auto=0,$lingua='')
{
	global $site_shell_url;
	$linguas=get_linguas_website();

	foreach( $linguas as $lingua )
	{
		if($lingua=='') $lingua = $GLOBALS['admin_lang'];
		$prods = get_produtc_info($prod_id,0,0,0,$lingua);
		if($lingua=='') $lingua = $GLOBALS['admin_lang'];
		if(!empty($prods) && $prods!=null)
		{
			$cache_filename = $site_shell_url.'files/cache/products/p'.$prod_id.'_'.$lingua.'.bin';
			if(file_exists($cache_filename)) @unlink($cache_filename);

			foreach (glob($GLOBALS['site_shell_url']."files/filtros/*_p".$prod_id."_".$lingua.".json") as $filename){ @unlink($filename);}

			$fam_id = $prods['fam_id'];
			if($marca > 0) $marca_id = $marca;
			else $marca_id = $_POST['marca'];

			// ARVORE DE FAMILIAS,PARA VÁRIOS NIVEIS
			$fams = BuscarArvoreFams_F($fam_id);
			$fm = '';
			$families = array();
			for($a=0;$a<count($fams);$a++)
			{
				if($fams[$a] > 0) $families[] = array('',$fams[$a]);
				if($a > 0){ $fm .= '_f'.$fams[$a];}else{$fm .= 'f'.$fams[$a];}
			}

			// FICHEIROS QUE VÃO SER GERADOS
			$familia_produtos = 'files/filtros/'.$fm.'_m'.$marca_id.'_p'.$prod_id.'_'.$lingua.'.json';

			$cache_filename = $site_shell_url.'files/filtros/json_'.$fam_id.'_pt.js';
			if(file_exists($cache_filename)) @unlink($cache_filename);
			$cache_filename = $site_shell_url.'files/filtros/jsonf_'.$fam_id.'_pt.js';
			if(file_exists($cache_filename)) @unlink($cache_filename);


			$cache_filename = $site_shell_url.'files/filtros/f_json_destaque.txt';
			if(file_exists($cache_filename)) @unlink($cache_filename);
			$cache_filename = $site_shell_url.'files/filtros/f_json_maisvendido.txt';
			if(file_exists($cache_filename)) @unlink($cache_filename);
			$cache_filename = $site_shell_url.'files/filtros/f_json_novidades.txt';
			if(file_exists($cache_filename)) @unlink($cache_filename);
			$cache_filename = $site_shell_url.'files/filtros/f_json_random.txt';
			if(file_exists($cache_filename)) @unlink($cache_filename);

			$estados_produto = $prods['estados_produto'];
			$prod_ordem = $prods['prod_ordem'];
			$cb = $prods['prod_nome'];
			$ref = $prods['prod_ref'];
			$csb = $prods['prod_subtitulo'];
			$T1 = $prods['prod_descricao'];
			$im = $prods['prd_imgm_new'];
			$prod_enc_sem_stock = $prods['prod_enc_sem_stock'];
			$bonus = ''; $bonus = $prods['BONUS'];
			$precopromo = $prods['prod_precos']['PRECO_ANTIGO'];
			$pvp = $prods['prod_precos']['PVP'];


			$promo = 0;
			$novidade = $prods['prod_precos']['NOVO'];
			$novidade_data = $prods['prod_precos']['NOVODT'];
			$destaque = $prods['prod_precos']['DESTAQUE'];
			$destaque_data = $prods['prod_precos']['DESTAQUEDT'];
			$travelsize = $prods['prod_precos']['TRAVELSIZE'];
			$outlet = $prods['prod_precos']['OUTLET'];
			$outlet_data = $prods['prod_precos']['OUTLETDT'];
			$promo = $prods['prod_precos']['PROMOCAO'];
			$promo_data = $prods['prod_precos']['PROMOCAODT'];

			$dodia = $prods['prod_precos']['DODIA'];
			$dodia_data = $prods['prod_precos']['DODIADT'];


			// $precopromo1 = 0; if($precopromo1) $promo = 1; else $promo = 0;

			$stock 	= $prods['prod_precos']['STOCK'];
			$marca_ordem 	= $prods['prod_marca']['marca_ordem'];
			$carrinho = 0; $carrinho = $prods['prod_carrinho'];

			$tipo_destaque_txt='';
			$tipo_novidade_txt='';
			if($destaque){ $tipo_destaque_txt = $GLOBALS['txt_em_destaque'][$GLOBALS['site_lang']];}
			if($novidade){ $tipo_novidade_txt = $GLOBALS['key_prdtag_new'][$GLOBALS['site_lang']];}

			if(!$promo) $promo = 'null';
			if(!$destaque) $destaque = 'null';
			if(!$novidade) $novidade = 'null';
			if(!$travelsize) $travelsize = 'null';
			if(!$outlet) $outlet = 'null';
			if(!$dodia) $dodia = 'null';

			if($pvp == '') $pvp = 0;
			if($stock == '') $stock = 0;

			$u_imagetiq = ''; $u_catetiq = ''; $u_coretiq = '';

			if($prods['u_catetiq'] != '')
			{
				$u_imagetiq = $prods['u_imagetiq'];
				$u_catetiq = $prods['u_catetiq'];
				$u_coretiq = $prods['u_coretiq'];
			}

			$settings = array
			(
				'id' => $prod_id,
				'ord' => intval($prod_ordem),
				'ref' => $ref,
				'marca_id' => $marca_id,
				'titulo' => strip_html_tags(str_replace('"',"'",$cb)),
				'subtitulo' => strip_html_tags(str_replace('"',"'",$T1)),
				'caminho' => seo('pages.php?page_id='.$GLOBALS['products_page_id'],$prod_id,2,$lingua),
				'preco' => $pvp,
				'preco_riscado' => $precopromo,
				'imagem' => $im,
				't_stock' => $estados_produto,// 0 - Usa stock do suplier,1 - O stock manual
				'stock' => $stock,
				'promocao' => $promo,
				'promocao_data' => $promo_data,
				'destaque' => $destaque,
				'destaque_data' => $destaque_data,
				'novidade' => $novidade,
				'novidade_data' => $novidade_data,
				'outlet' => $outlet,
				'outlet_data' => $outlet_data,
				'dodia' => $dodia,
				'dodia_data' => $dodia_data,
				'bonus' => $bonus,
				// 'travelsize' => $travelsize,
				'marca_ordem' => $marca_ordem,
				'category_id' => $category_id,
				'fam_id1' => $fam_id1,
				'u_imagetiq' => $u_imagetiq,
				'u_catetiq' => $u_catetiq,
				'u_coretiq' => $u_coretiq,
				'carrinho' => $carrinho,
				'prod_enc_sem_stock' => $prod_enc_sem_stock
			);

			$precos_bloco[] = $pvp;
			$return = bloco_grelha_exclusivos_filtros_data($settings,$families,$lingua);

			if($return)
			{
				$script = compress_html($return);
				$file_name = $GLOBALS['site_shell_url']. $familia_produtos;
				$fh = fopen($file_name,'w');
				fwrite($fh,$script);
				fclose($fh);
			}

			if($auto==0) // se for o importador a correr não corre esta parte
			{
				for($a=0;$a<count($fams);$a++)
				{

					$FAMILIA = $fams[$a];

					$familia_filtros = 'files/filtros/filtros_prods_'.$FAMILIA.'_'.$lingua.'.txt';
					$familia_filtros_c = 'files/filtros/filtros_prods_'.$FAMILIA.'_c_'.$lingua.'.txt';

					$filtros = '';

					// FAMILIAS
					$filtros .= FILTRO_FAMILIAS_NEW($FAMILIA,0,$lingua);

					// MARCAS
					// $filtros .= FILTRO_MARCAS_NEW($FAMILIA,0,$lingua);

					// PRECOS
					// $filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($FAMILIA));

					// FILTROS
					$file_name = $GLOBALS['site_shell_url']. $familia_filtros;
					$fh = fopen($file_name,'w');
					fwrite($fh,sanitize_output($filtros));

					// CARACTERISTICAS
					$filtros2 = FILTRO_CARACTERISTICAS_NEW($FAMILIA,0,0,2,$lingua);

					// FILTROS
					$file_name = $GLOBALS['site_shell_url']. $familia_filtros_c;
					$fh = fopen($file_name,'w');
					fwrite($fh,sanitize_output($filtros2));
					fclose($fh);
				}

				if($marca_id > 0)
				{
					$filtros = '';
					$marcas_filtros = 'files/filtros/marca_filtros_prods_' . $marca_id.'_'.$lingua.'.txt';
					$marcas_filtros_c = 'files/filtros/marca_filtros_prods_' . $marca_id.'_c_'.$lingua.'.txt';

					$filtros .= FILTRO_FAMILIAS_NEW($marca_id,1,$lingua);

					// $filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($marca_id,1));

					// FILTROS
					$file_name = $GLOBALS['site_shell_url']. $marcas_filtros;
					$fh = fopen($file_name,'w');
					fwrite($fh,sanitize_output($filtros));
					fclose($fh);

					// CARACTERISTICAS
					$filtros2 = FILTRO_CARACTERISTICAS_NEW($marca_id,1,0,2,$lingua);

					$file_name = $GLOBALS['site_shell_url']. $marcas_filtros_c;
					$fh = fopen($file_name,'w');
					fwrite($fh,sanitize_output($filtros2));
					fclose($fh);
				}
			}
		}
	}
}

function gera_produtos_filtros($lingua='')
{
	global $site_shell_url;
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	if($lingua=='') $lingua = $GLOBALS['admin_lang'];

	// $fam = 1;

	for($fam=1; $fam <= 4; $fam++)
	{
		$sql = 'SELECT fam_id'.$fam.' FROM filters_products WHERE fam_id'.$fam.' > 0 AND Flag=1 AND filtro_active=1 GROUP BY fam_id'.$fam;

		$obj->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$obj->EOF())
		{
			while(!$obj->EOF())
			{

				$FAMILIA = $obj->db_data['fam_id'.$fam];

				$familia_filtros = 'files/filtros/filtros_prods_'.$FAMILIA.'_'.$lingua.'.txt';
				$familia_filtros_c = 'files/filtros/filtros_prods_'.$FAMILIA.'_c_'.$lingua.'.txt';

				$filtros = '';

				// MARCAS
				// $filtros .= FILTRO_MARCAS_NEW($FAMILIA,0,$lingua);

				// PRECOS
				// $filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($FAMILIA));

				// FILTROS
				$file_name = $GLOBALS['site_shell_url']. $familia_filtros;
				$fh = fopen($file_name,'w');
				fwrite($fh,sanitize_output($filtros));
				fclose($fh);

				// CARACTERISTICAS
				$filtros2 = FILTRO_CARACTERISTICAS_NEW($FAMILIA,0,0,2,$lingua);

				// FILTROS
				$file_name = $GLOBALS['site_shell_url']. $familia_filtros_c;
				$fh = fopen($file_name,'w');
				fwrite($fh,sanitize_output($filtros2));
				fclose($fh);

				$obj->db_next();
			}
		}
		$obj->db_close();
	}


	$sql = 'SELECT marca_id FROM filters_products WHERE marca_id > 0 && Flag=1 AND filtro_active=1 GROUP BY marca_id';
	$obj->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$marca_id = $obj->db_data['marca_id'];
			if($marca_id > 0)
			{
				$filtros = '';
				$marcas_filtros = 'files/filtros/marca_filtros_prods_' . $marca_id.'_'.$lingua.'.txt';
				$marcas_filtros_c = 'files/filtros/marca_filtros_prods_' . $marca_id.'_c_'.$lingua.'.txt';

				$filtros .= FILTRO_FAMILIAS_NEW($marca_id,1,$lingua);

				// $filtros .= FILTRO_PRECOS_NEW(getPrecosblocos($marca_id,1));

				// FILTROS
				$file_name = $GLOBALS['site_shell_url']. $marcas_filtros;
				$fh = fopen($file_name,'w') or die("can't open file");
				fwrite($fh,sanitize_output($filtros));
				fclose($fh);
				// chmod($GLOBALS['site_shell_url']. $marcas_filtros,0755);

				// CARACTERISTICAS
				$filtros2 = FILTRO_CARACTERISTICAS_NEW($marca_id,1,0,2,$lingua);

				$file_name = $GLOBALS['site_shell_url']. $marcas_filtros_c;
				$fh = fopen($file_name,'w') or die("can't open file");
				fwrite($fh,sanitize_output($filtros2));
				fclose($fh);
				// chmod($GLOBALS['site_shell_url']. $marcas_filtros_c,0755);
			}

			$obj->db_next();
		}
	}
	$obj->db_close();
}

function sanitize_output($buffer) {

    $search = array(
        '/\>[^\S ]+/s', // strip whitespaces after tags,except space
        '/[^\S ]+\</s', // strip whitespaces before tags,except space
        '/(\s)+/s'       // shorten multiple whitespace sequences
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $buffer = preg_replace($search,$replace,$buffer);

    return $buffer;
}
?>