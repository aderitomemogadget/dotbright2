function stock_resultado(object)
{
	var open_a=close_a=sufixo='';
	var t_stock=object.stock;
	var t_stock_m=object.stock_m;
	var ID=object.id;
	var ret='';

	if(t_stock_m == "01") return '<a href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'" class="brevemente texto2 fancy_stock" title="">Brevemente</a>';
	else if(t_stock_m == "04") return '<a href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'" class="esgotado texto2 fancy_stock">Esgotado</a>';
	else if(t_stock_m == "02") return '<a href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'" class="porencomenda texto2 fancy_stock">Por encomenda</a>';
	else if(t_stock_m == "03") return '<a href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'" class="prereserva texto2 fancy_stock">Pré-reserva</a>';
	else
	{
	    if(t_stock > 5) t_stock=5;

		if(t_stock < 1 || t_stock == '' || t_stock == 0)
		{
			ret='<a href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'" class="esgotado texto2 fancy_stock">Esgotado</a>';
		}
		else if(t_stock == 1 || t_stock == 2 || t_stock == 3 || t_stock == 4)
		{
			ret='<span class="limitado">Limitado</span>';
		}
		else
		{
			ret='<span class="disponivel">Disponível</span>';
		}


		/*if(t_stock < 1 || t_stock == '' || t_stock == 0)
		{
			sufixo ='3';
			open_a ='<span title="Esgotado">';
			close_a='</span>';
		}
		else if(t_stock == 1)
		{
			sufixo ='3';
			open_a ='<span title="Esgotado">';
			close_a='</span>';
		}
		else if(t_stock == 2)
		{
			sufixo ='2c';
			open_a ='<span title="Stock Limitado">';
			close_a='</span>';
		}
		else if(t_stock == 3)
		{
			sufixo ='2b';
			open_a ='<span title="Stock Limitado">';
			close_a='</span>';
		}
		else if(t_stock == 4)
		{
			sufixo ='2a';
			open_a ='<span title="Stock Limitado">';
			close_a='</span>';
		}
		else
		{
			open_a ='<span title="Dispon&iacute;vel">';
			close_a='</span>';
		}


		for(var i=0; i < t_stock; i++)
		{
			ret += open_a + '<img src="layout/'+PROJETO+'/images/stock_cheio'+sufixo+'.png" border="0" alt="" />'+close_a;
		}

		for(var j=i; j < 5; j++)
		{

			if(t_stock < 1 && j < 1)
			{
				ret += open_a + '<img src="layout/'+PROJETO+'/images/stock_cheio3.png" border="0" alt="" />'+close_a;
			}
			else
			{
				ret += open_a + '<img src="layout/'+PROJETO+'/images/stock_vazio.png" border="0" alt="" />'+close_a;
			}
		}		*/
	}

	return ret;
}
function mostra_bloco(object)
{
	var cell='<div class="prd_d">';

	var titulo_fam='<div class="prd_title_fam">'+object.fm+'</div>';
	var titulo='<div class="prd_title"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'">'+object.title+'</a></div>';
	var subtitle='<div class="prd_subtitle">'+object.ref+'</div>';
	var caract='<div class="prd_caract">caract</div>';

	var stock_txt=stock_resultado(object);
	var stock='<div class="prdstock_n">'+stock_txt+'</div>';
	var promo=0;

	if(object.promo==1 && object.price_riscado > object.price && object.price > 0)
	{
		var percdesconto=getDescontoAplicado(object.price_riscado, object.price);
		promo=object.price_riscado;
	}
	if(object.promo==1)
	{
		cell += '<div class="prd_promodesconto">Promo Desconto</div>';
	}
	else if(object.novidade==1)
	{
		cell += '<div class="prd_novo">Novidade</div>';
	}

	var preco;
	preco='<div class="prdprice_n">';

	if(object.stock > 0 && object.stock_m != '04' && object.stock_m != '01' && object.price > 0 && object.carrinho == 1) preco += '<div class="prodby">Comprar <input type="hidden" value="'+object.id+'" name="prodid"/><input type="hidden" value="" name="prodfiltros" /></div>';
	else preco += '<div class="prodby-des fancy_stock" href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'">Alerta de Stock</div>';

	preco += '<div class="price">'+number_format_(object.price, 2)+' &euro;</div>';
	if(promo > 0) preco += '<div class="promo">'+number_format_(promo,2)+' &euro;</div>';
	preco += '</div>';

	cell += '<div class="prd_img"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'"><img src="'+object.image+'" border="0" '+(substr_count_v(object.image,'no_photo.png') ? 'style="height:106px"' : '')+'/></a></div>';

	cell += '<div class="prd_layer">';
	cell += titulo_fam;
	cell += titulo;
	cell += '<div class="prd_sub_stock">';
	//cell += subtitle;
	cell += stock;
	cell += '<div class="c"></div>';
	cell += '</div>';
	cell += preco;
	cell += '<div class="c"></div>';
	cell += '</div>';



	cell += '<\/div>';
	cell += '<\/div>';

	return cell;
}

function mostra_bloco_lista(object)
{
	var cell='<div class="prd_d_lista">';

	var titulo_fam='<div class="prd_title_fam">'+object.fm+'</div>';
	var titulo='<div class="prd_title"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'">'+object.title+'</a></div>';
	var subtitle='<div class="prd_subtitle">REF: '+object.ref+'</div>';
	var caract='<div class="prd_caract">caract</div>';

	var stock_txt=stock_resultado(object);
	var stock='<div class="prdstock_n"><span class="lbn">STOCK:</span> '+stock_txt+'</div>';
	var promo=0;

	if(object.promo==1 && object.price_riscado > object.price && object.price > 0)
	{
		var percdesconto=getDescontoAplicado(object.price_riscado, object.price);
		promo=object.price_riscado;
	}
	if(object.promo==1)
	{
		cell += '<div class="prd_novo">Promo Desconto</div>';
	}
	else if(object.novidade==1)
	{
		cell += '<div class="prd_novo">Novidade</div>';
	}

	var preco;
	preco='<div class="prdprice_n">';

	preco += '<div class="prd_sub_stock">';
	preco += stock;
	preco += '</div>';

	if(object.stock > 0 && object.stock_m != '04' && object.stock_m != '01' && object.price > 0 && object.carrinho == 1) preco += '<div class="prodby">Comprar <input type="hidden" value="'+object.id+'" name="prodid"/><input type="hidden" value="" name="prodfiltros" /></div>';
	else preco += '<div class="prodby-des fancy_stock" href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+object.id+'">Alerta de Stock</div>';

	preco += '<div class="price">'+number_format_(object.price, 2)+' &euro;</div>';
	if(promo > 0) preco += '<div class="promo">'+number_format_(promo,2)+' &euro;</div>';
	preco += '</div>';

	cell += '<div class="prd_img"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'"><img src="'+object.image+'" border="0" '+(substr_count_v(object.image,'no_photo.png') ? 'style="height:106px"' : '')+'/></a></div>';

	cell += '<div class="prd_layer">';
	cell += titulo_fam;
	cell += titulo;
	cell += subtitle;
	cell += preco;
	cell += '<div class="c"></div>';
	cell += '</div>';



	cell += '<\/div>';
	cell += '<\/div>';

	return cell;
}
function numberformat(value, lado, perc)
{
	MOEDA='&euro;';

	if(perc==1 && lado==0) return '% '+number_format_(value, 2, ',', '.');
	else if(perc==1 && lado==1) return number_format_(value, 2, ',', '.')+' %';
	else if(lado==0) return MOEDA+' '+number_format_(value, 2, ',', '.');
	else if(lado==1) return number_format_(value, 2, ',', '.')+' '+MOEDA;
	else if(lado==2) return number_format_(value, 2, ',', '.')+' &euro;';
	else if(lado==3) return '&euro; '+number_format_(value, 2, '.', '');
	else if(lado==4) return number_format_(value, 2);
}