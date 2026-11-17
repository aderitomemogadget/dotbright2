<?php
	$path='../core/';
	include $path.'core_files.php';
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$return='';
	if(intval($_GET['prod_id']) > 0)
	{
		$m_id=intval($_GET['prod_id']);
		$prods=get_produtc_info($m_id);
	    $dados=cliente_logado();
	    $precos=$prods['prod_precos'];
		$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];
		$return='<div class="price"';

		if( ($precos['STOCK'] <= 0 || $prods['prod_carrinho'] <= 0)  && $GLOBALS['produtos_sem_stock_ocultar_preco']==1)
		{
			$return.=' >&nbsp;</div><div class="clear"></div><span class="price" style="height:27px;display:block;text-align:center;cursor:pointer;line-height:30px">Ver Info</span>';
			//$return.='<div class="fp_comprar_esgotado2 fancy_stock" href="https://www.mhr.pt/site_novo/ajax/setNotificacaoProduto.php?idprod=167172">Ver Info</div>';
		}
		else
		{
			if($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP']){$price_riscado=numberformat($prods['prod_precos']['PRECO_ANTIGO']);}
			if($prods['prod_precos']['PVP'] > 0)
			{
				$pvp=$prods['prod_precos']['PVP'];
				$iva=intval($prods['prod_precos']['IVA']);
			}
			if($pvp <= 0 )$return.=' style="visibility:hidden" ';
			$return.=' >';

			if($price_riscado!='')$return.='<span class="preco_riscado">'.$price_riscado.'&nbsp;</span>';
			if($pvp>0)$return.=numberformat($pvp);


			$not_produto_standard=$prods['produto_standard'];
			$c_submissao_ficheiro=$prods['c_submissao_ficheiro'];

			if($not_produto_standard || $c_submissao_ficheiro)
			{
				$caminho=seo('pages.php?page_id='.$GLOBALS['products_page_id'], $id, 2, $GLOBALS['site_lang']);

				$botao= '<div class="fp_comprar_esgotado2 fancy_stock" href="'.$caminho.'">Ver mais detalhes</div>';
			}
			else
			{
				if($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1) $botao = '<div class="prodby">Comprar<input type="hidden" value="'.$prods['prod_id'].'" name="prodid"><input type="hidden" value="" name="prodfiltros"></div>';
				else $botao= '<div class="fp_comprar_esgotado2 fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</div>';
			}

			$return.='</div>';
			$return.='<div class="clear"></div>';
			if($pvp<=0)$return.='<span class="price" style="height:27px;display:block;text-align:center;cursor:pointer">Ver Info</span>';
			else $return.=$stock_txt.$wish.$botao;
		}

		$return.='<div class="clear"></div>';
	}
	echo $return;
?>