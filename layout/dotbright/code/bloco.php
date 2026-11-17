<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function stock_resultado($t_stock)
{
	if ($prods['prod_precos']['STOCK'] > 0 && $prods['prod_precos']['STOCK'] < 3)
	{
		$disponibilidade = '<span class="limitado">LIMITADO</span>';
	}
	else
	{
		if ($prods['prod_precos']['STOCK'] >= 3)
		{
			$disponibilidade = '<span class="disponivel">DISPONÍVEL</span>';
		}
		else
		{
			$disponibilidade = '<span class="esgotado">ESGOTADO</span>';
		}
	}


	if($t_stock == "Brevemente") return '<a href="javascript:;" onclick="call_alert('.$ID.')" class="texto2" title="'.$txt_breve_disp[$lang].'">Brev. Disp.</a>';
	elseif($t_stock == "Esgotado") return '<a href="javascript:;" onclick="call_alert('.$ID.')" class="texto2">'.$txt_esgotado[$lang].'</a>';
	else
	{
	    if($t_stock > 5) $t_stock = 5;

		if($t_stock < 1 || $t_stock==NULL)
		{
			$sufixo  = '3';
			$open_a  = '<span title="Esgotado">';
			$close_a = '</span>';
		}
		elseif($t_stock == 1)
		{
			$sufixo  = '3';
			$open_a  = '<span title="Esgotado">';
			$close_a = '</span>';
		}
		elseif($t_stock == 2)
		{
			$sufixo  = '2c';
			$open_a  = '<span title="Stock Limitado">';
			$close_a = '</span>';
		}
		elseif($t_stock == 3)
		{
			$sufixo  = '2b';
			$open_a  = '<span title="Stock Limitado">';
			$close_a = '</span>';
		}
		elseif($t_stock == 4)
		{
			$sufixo  = '2a';
			$open_a  = '<span title="Stock Limitado">';
			$close_a = '</span>';
		}
		else
		{
			$open_a  = '<span title="Dispon&iacute;vel">';
			$close_a = '</span>';
		}


		for($i=0; $i<$t_stock; $i++)
		{
			$ret .= $open_a.'<img src="layout/'.PROJETO.'/images/stock_cheio'.$sufixo.'.png" border="0" />'.$close_a;
		}

		for($j = $i; $j < 5; $j++)
		{

			if($t_stock < 1 && $j < 1)
			{
				$ret .= $open_a . '<img src="layout/'.PROJETO.'/images/stock_cheio3.png" border="0" />'.$close_a;
			}
			else
			{
				$ret .= $open_a . '<img src="layout/'.PROJETO.'/images/stock_vazio.png" border="0" />'.$close_a;
			}
		}

		return $ret;
	}
}
function mostra_bloco($prods)
{
	$url = str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prods['prod_id'], 2));
	$s = '<div class="prd_d">';
	$fam_info = get_family_info($prods['fam_id']);
	$titulo_fam = '<div class="prd_title_fam">'.$fam_info['fam_nome'].'</div>';
	$titulo = '<div class="prd_title"><a title="'.$prods['prod_nome'].'" href="'.$url.'">'.html_cut($prods['prod_nome'], 45).'</a></div>';
	$subtitle = '<div class="prd_subtitle">Ref: '.$prods['prod_ref'].'</div>';
	$caract = '<div class="prd_caract">'.$prods['prod_features'].'</div>';
	// $stock_txt = stock_resultado($prods['prod_precos']['STOCK']);
	if ($prods['prod_precos']['STOCK'] > 0 && $prods['prod_precos']['STOCK'] < 3)
	{
		$disponibilidade = '<span class="limitado">LIMITADO</span>';
	}
	else
	{
		if ($prods['prod_precos']['STOCK'] >= 3)$disponibilidade = '<span class="disponivel">DISPONÍVEL</span>';
		else 									$disponibilidade = '<span class="esgotado">ESGOTADO</span>';
	}
	$stock_txt = '';
	if ($prods['u_disponibilidade'] != '')
	{
		switch ($prods['u_disponibilidade'])
		{
			case '01':
				$stock_txt .= '<div class="produto_estado_stock">Stock: <span class="brevemente">BREVEMENTE</span></div>';
				break;
			case '02':
				$stock_txt .= '<div class="produto_estado_stock">Stock: <span class="porencomenda">POR ENCOMENDA</span></div>';
				break;
			case '03':
				$stock_txt .= '<div class="produto_estado_stock">Stock: <span class="prereserva">PRÉ-RESERVA</span></div>';
				break;
			case '04':
				$stock_txt .= '<div class="produto_estado_stock">Stock: <span class="esgotado">ESGOTADO</span></div>';
				break;
		}
	}
	else
	{
		if ($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1)
		{
			$stock_txt .= '<div class="produto_estado_stock">Stock: '.$disponibilidade.'</div>';
		}
		else
		{
			$stock_txt .= '<div class="produto_estado_stock">Stock: '.$disponibilidade.'</div>';
		}
	}
	$stock = '<div class="prdstock_n"><span class="stock">STOCK</span> '.$stock_txt.'</div>';
	$promo = 0;
	if($prods['prod_precos']['PROMOCAO']==1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP'])
	{
		$percdesconto = getDescontoAplicado($prods['prod_precos']['PRECO_ANTIGO'], $prods['prod_precos']['PVP']);
		$promo = numberformat($prods['prod_precos']['PRECO_ANTIGO']);
	}
	if($prods['prod_precos']['NOVO']==1)
	{
		$s .= '<div class="prd_novo">';
		$s .= '</div>';
	}
	$preco = '<div class="prdprice_n">';
	if($promo) $preco .= '<div class="promo">'.numberformat($promo).'</div>';
	$preco .= '<div class="price">'.numberformat($prods['prod_precos']['PVP'], 1).'</div>';
	$preco .= '</div>';


	$s.= '<div class="prd_img"><a title="'.$prods['prod_nome'].'" href="'.$url.'"><img src="layout/blank.gif" class="lazy" data-original="'.$prods['prd_imgm_new'].'" alt="" '.(isset($prods['prd_imgm_new']) && substr_count($prods['prd_imgm_new'],'no_photo.png') ? 'style="height:106px"' : '').'/></a></div>';
	$s.= '<div class="prd_layer">';
	$s.= $titulo_fam;
	$s.= $titulo;
	$s.= '<div class="c"></div>';
	//$s.= $subtitle;
	$s.= $stock;
	$s.= $preco;
	$s.= '<div class="c"></div>';
	$s.= '</div>';

	if(!isset($prods['prd_filtros'])) $prods['prd_filtros'] = '';

	if($prods['prod_precos']['STOCK'] > 0 && $prods['prod_precos']['PVP']>0 && $prods['prod_carrinho']==1) $s.= '<div class="prodby"><input type="hidden" value="'.$prods['prod_id'].'" name="prodid"/><input type="hidden" value="'.$prods['prd_filtros'].'" name="prodfiltros" /></div>';
	else $s.= '<div class="prodby-des fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">&nbsp;</div>';
	$s.= '</div>';

	return $s;
}