<?php
	if(isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$path='../core/';
		include $path.'core_files.php';


		$prods=\get_produtc_info_essentials($_GET['prod_id']);
		$pvp=$price2=0;
		$global_promos='[{}]';
		if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
		{
			$price2=$prods['prod_precos']['PRECO_ANTIGO'];
			$global_promos='['.\show_global_promos().']';
		}


		$f_json='{
		"id" : "'.$_GET['prod_id'].'",
		"ord" : '.$prods['prod_ordem'].',
		"ref" : "'.$prods['prod_ref'].'",
		"price" : '.$pvp.',
		"price_riscado" : '.$price2.',
		"pvp" : "&euro; '.$pvp.'",
		"stock": "'.$prods['prod_precos']['STOCK'].'",
		"stock_txt": "'.addslashes($stock_txt).'",
		"image":"'.$prods['prd_imgm_new'].'",
		"title":"'.addslashes($prods['prod_nome']).'",
		"subtitle":"'.addslashes(strip_tags($prods['prod_descricao'],'<br>')).'",
		"caminho" : "'.$link.'",
		"promo" : "'.$prods['prod_precos']['PROMOCAO'].'",
		"promo_data" : "0000-00-00",
		"global_promos" : '.$global_promos.',
		"destaque" : "'.$prods['prod_precos']['DESTAQUE'].'",
		"m_id" : "'.$prods['prod_id_marca'].'",
		"c" : ['.$categories_prod.'],
		"f" : ['.$families_prod.'],
		"u_i" : "'.$prods["u_imagetiq"].'",
		"u_c" : "'.$prods["u_catetiq"].'",
		"u_co" : "'.$prods["u_coretiq"].'",
		"carrinho" : "'.$prods['prod_carrinho'].'",
		"novidade" : "'.$prods['prod_precos']['NOVO'].'",
		"novidade_data" : "0000-00-00",
		"outlet" : "'.$prods['prod_precos']['OUTLET'].'",
		"outlet_box" : "'.$prods['prod_precos']['OUTLETBOX'].'",
		"outlet_data" : "0000-00-00",
		"destaque_data" : "0000-00-00",
		"dodia_data" : "0000-00-00",

		"fl" : "0"}';


		//echo $f_json;
	echo ($f_json);

	}else die('Não permissões para aceder a está pagina!');
?>