<?php
	$path='../core/';
	include $path.'core_files.php';
	$dados_cliente=cliente_logado();
	if(isset($_POST['prods']) && $_POST['prods'] != '')
	{
		$prd_def=array();
		$prods2=array();
		$precos=array();
		$rep=array();
		$prods=explode(';', $_POST['prods']);
		if(!empty($prods))
		{
			for($a=0; $a<count($prods); $a++)
			{
				$prd_def=explode('|', $prods[$a]);

				if(intval($prd_def[0]) > 0)
				{
					$prods2[]=$prd_def[0];
					$precos[]=get_produtc_price($prd_def[0]);
				}
			}
			for($a=0; $a<count($precos); $a++)
			{
				echo $prods2[$a].'|';
				$stock=$precos[$a]['STOCK'];
				$PODE_ENCOMENDAR=$precos[$a]['PODE_ENCOMENDAR'];
				$price=$precos[$a]['PVP'];
				$id=$prods2[$a];
				$caminho=seo('pages.php?page_id='.$GLOBALS['products_page_id'], $id, 2, $GLOBALS['site_lang']);
				if($precos[$a]['STOCK'] == 1) $stock_txt='<span class="limitado">Stock: <img src="layout/'.PROJETO.'/images/status_away.png" /></span>';
				else if($precos[$a]['STOCK'] > 1) $stock_txt='<span class="disponivel">Stock: <img src="layout/'.PROJETO.'/images/status.png" /></span>';
				else $stock_txt='<span class="esgotado">Stock: <img src="layout/'.PROJETO.'/images/status_busy.png" /></span>';
				$fl=filtros_ficha_produto_verifica($prods2[$a]);
				$wish=''; $res=0;
				if($dados_cliente)
				{
					$cli_id=$dados_cliente['cli_id'];
					if(isProductInWishList($id, $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
					else $res=0;
				}
				elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
				{
					if(isProductInWishList($id, 0, $_SESSION['CLIENTE_ID_TEMP'])) $res=1;
					else $res=0;
				}

				if($res == 1) $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="removeProductWishList(\''.$id.'\', 0);return false;"><span title="'.getLinguas('key_wishlist_rem').'"><i class="fa fa-star" aria-hidden="true"></i></span></a>';
				else $wish='<a class="icon-coracao produtowl'.$id.'" href="#" onclick="addProductWishList(\''.$id.'\');return false;"><span title="'.getLinguas('key_wishlist_add').'"><i class="fa fa-star-o" aria-hidden="true"></i></span></a>';

				$botao='';
				if(($stock > 0 || $PODE_ENCOMENDAR == 1) && $price > 0) $botao .= '<div class="prodby">'.getLinguas('key_comprar').'<input type="hidden" value="'.$id .'" name="prodid"/><input type="hidden" value="'.($fl == 1 ? $caminho : '').'" name="prodfiltros" /></div>';
				else $botao .= '<div class="prodby-des fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$id .'">&nbsp;</div>';

				echo '<div class="price">'.($precos[$a]['PRECO_ANTIGO'] > 0 ?  '<span>'.numberformat($precos[$a]['PRECO_ANTIGO']).'</span>' : '').'&nbsp;'.($precos[$a]['PVP']<=0 ? '' : numberformat($precos[$a]['PVP'])).'</div>';
				echo '<div class="clear"></div>';
				echo  $stock_txt.$wish.$botao;
				echo '<div class="c"></div>[split]';
			}
		}
	}
?>