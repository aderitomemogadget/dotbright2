function bloco_produto(vista, object)
{
	var cell = '';

	if(object.a34==1)/*reparacoes*/
	{
		var p_mercado=0;
		var stock_txt='';
		fam_rep=0;

		var id=object.id;
		var fl=object.fl;
		var title=object.title;
		var caminho=object.caminho;
		var image=object.image;
		var subtitle=object.subtitle;
		var ref=object.ref;

		//Lista de sub-familia
		if(vista == '') //lista 3 produtos de cada vez
		{
			cell += '<div class="prd_d" id="prd_d_'+id+'">';
			//cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper"></span><img alt="..." src="layout/'+PROJETO+'/images/orders.gif" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png')?'style="height:106px"':'style="max-height:130px"')+' border="0"></a></div>';
			cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper 20"></span><img alt="..." src="'+image+'" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png')?'style="height:106px"':'style="max-height:130px"')+' border="0"></a></div>';
			cell += '<div class="prd_layer">';
			cell += '<div class="prd_title"><a title="'+title+'" href="'+caminho+'" target="_top">'+title+'</a></div>';
			cell += '<div class="c"></div>';			
			cell += '</div><div class="c"></div></div></div>';
		}
	}
	else
	{
		if(object.ref.startsWith("REP"))
		{
			//REPARACAO
			var p_mercado=0;
			var stock_txt='';
			fam_rep=0;

			var id=object.id;
			var fl=object.fl;
			var title=object.title;
			var caminho=object.caminho;
			var image=object.image;
			
			//console.log(object.image);
			var subtitle=object.subtitle;
			var ref=object.ref;
			var price_riscado=''; price_riscado=(object.price_riscado > object.price ? '<span>'+numberformat(object.price_riscado,2,',','.')+'</span>' : '');
			var price=0; price=object.price;

			var botao = '<a class="prodrepara" title="'+title+'" href="'+caminho+'" style="height:35px;line-height:35px;display:block;text-align:center;cursor:pointer;font-size:12px">SOLICITAR REPARAÇÃO</a>';
			if(vista == '') //lista 3 produtos de cada vez
			{
				cell += '<div class="prd_d aa" id="prd_d_'+id+'">';
				//cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper"></span><img alt="..." src="layout/'+PROJETO+'/images/orders.gif" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png')?'style="height:106px"':'style="max-height:130px"')+' border="0"></a></div>';
				cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper 21"></span><img alt="..." src="'+image+'" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png')?'style="height:106px"':'style="max-height:130px"')+' border="0"></a></div>';
				cell += '<div class="prd_layer">';
				cell += '<div class="prd_title"><a title="'+title+'" href="'+caminho+'" target="_top">'+title+'</a></div>';
				cell += '<div class="c"></div>';

				if(price_riscado!='' && object.global_promos.length && (typeof(object.global_promos[0]['data_inicio']) !== 'undefined') )
				{
					cell += '<div style="text-align:center;display:block;padding:5px;background:#f8f8f8">';
					cell += '<h5 style="padding:0px;margin:0px">'+object.global_promos[0]['designacao']+'</h5>';
					cell += '<span style="font-size:10px">'+object.global_promos[0]['data_inicio'].slice(0, 10)+' - '+object.global_promos[0]['data_fim'].slice(0,10)+'<b data-toggle="tooltip" title="" data-original-title="'+object.global_promos[0]['descricao']+'">&nbsp<img src="layout/'+PROJETO+'/images/alert.png" width="13"></b></span>';
					cell += '</div>';
				}
				cell += '<div class="c"></div>';
				cell += '<div class="prd_stock">'+stock_txt+'</div>';
				cell += '<div class="prdprice_n" id="precos_'+id+'" info="'+id+'|'+enc_stock+'" info2="'+id+'" style="min-height:15px">';
				cell += '<div class="price" '+(price <= 0 ? 'style="visibility:hidden":' : '')+'>'+(price_riscado!='' ? price_riscado : '')+'&nbsp;'+(price<=0 ? '' : numberformat(price,2,',','.'))+'</div>';
				cell += '<div class="c"></div>';
				cell += '</div><div class="c"></div></div></div>';
			}
			else //lista 1 produto de cada vez
			{
				cell += '<div class="prd_d" id="prd_d_'+id+'">';
				cell += '<div class="left-block col-xs-4 col-sm-4 col-md-4">';
				cell += cell2;
				//cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper"></span><img alt="..." src="layout/'+PROJETO+'/images/orders.gif" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : '')+' border="0"></a></div>';
				cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper 22"></span><img alt="..." src="'+image+'" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : '')+' border="0"></a></div>';
				cell += '</div>';
				cell +='<div class="center-block col-xs-4 col-sm-4 col-md-4">';
				cell += '<div class="prd_layer">';
				cell += '<div class="prd_title"><a title="'+title+'" href="'+caminho+'" target="_top">'+title+'</a></div>';
				cell += '<div class="c"></div>';
				cell += '</div>';
				cell += '</div>';
				cell += '<div class="right-block col-xs-4 col-sm-4 col-md-4">';
				if(price_riscado!='' && object.global_promos.length)
				{
					cell += '<div style="text-align: center; display: block; padding: 5px; background: #f8f8f8;">';
					cell += '<h5 style="padding: 0px; margin: 0px">'+object.global_promos[0]['designacao']+'</h5>';
					cell += '<span style="font-size: 10px;">'+object.global_promos[0]['data_inicio'].slice(0, 10)+' - '+object.global_promos[0]['data_fim'].slice(0,10)+'<b data-toggle="tooltip" title="" data-original-title="'+object.global_promos[0]['descricao']+'">&nbsp<img src="layout/'+PROJETO+'/images/alert.png" width="13"></b></span>';
					cell += '</div>';
				}

				cell += '<div class="prdprice_n" id="precos_'+id+'" info="'+id+'|'+enc_stock+'" info2="'+id+'">';
				cell += '<div class="price" '+(price <= 0 ? 'style="visibility:hidden":' : '')+'>'+(price_riscado!='' ? price_riscado : '')+'&nbsp;'+(price<=0 ? '' : numberformat(price,2,',','.'))+'</div>';
				cell += '<div class="clear"></div>';
				cell += botao;
				cell += '<div class="c"></div>';
				cell += '</div><div class="c"></div></div>';
				cell += '</div>';

			}
			//show_img(object.id);
			//show_designacao(object.id);
		}
		else
		{
				var p_mercado=0;
				var titulo='<div class="prd_title"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'"  target="_top">'+object.title+'</a><\/div>';
				var stock_txt='';

				// DATA DE FIM PROMOÇÃO				
				var promo=object.promo;
				var promo_data=object.promo_data.split('-');
				var promoEndDate=new Date(promo_data[0], (promo_data[1]-1), promo_data[2]);

				// DATA DE FIM NOVIDADE
				var novidade=object.novidade;
				var novidade_data=object.novidade_data.split('-');
				var novidadeEndDate=new Date(novidade_data[0], (novidade_data[1]-1), novidade_data[2]);

				// DATA DE FIM OUTLET
				var outlet=object.outlet;
				var outlet_data=object.outlet_data.split('-');
				var outletEndDate=new Date(outlet_data[0], (outlet_data[1]-1), outlet_data[2]);
				var outlet_box=object.outlet_box;

				// DATA DE FIM DESTAQUE
				var destaque=object.destaque;
				var destaque_data=object.destaque_data.split('-');
				var destaqueEndDate=new Date(destaque_data[0], (destaque_data[1]-1), destaque_data[2]);

				
				/*var promo=0;*/
				var cell2='';

				fam_rep=0;

				var id=object.id;
				var fl=object.fl;
				var title=object.title;
				var caminho=object.caminho;
				var image=object.image;
				var subtitle=object.subtitle;
				var ref=object.ref;
				var price_riscado=''; price_riscado=(object.price_riscado > object.price ? '<span>'+numberformat(object.price_riscado,2,',','.')+'</span>' : '');
				var price=0; price=object.price;
				var stock=0; stock=object.stock;

				var stock_txt='';

				if(outlet==1)
				{
					cell2 += '<div class="ribbon-wrapper" style="cursor:pointer"><div class="ribbon-destaque">'+outlet_box+'</div></div>';
				}				
				if(novidade==1)
				{
					cell2 += '<div class="ribbon-wrapper" style="cursor:pointer"><div class="ribbon-destaque">NOVIDADE</div></div>';
				}
				if(promo==2)
				{
					cell2 += '<div class="ribbon-wrapper" style="cursor:pointer"><div class="ribbon-destaque">BEST PRICE</div></div>';
				}


				if(price > 0)
				{	
					if(object.stock > 0) stock_txt='<span class="disponivel" title="Disponibilidade Total">Stock: <img src="layout/'+PROJETO+'/images/status.png" alt="Disponibilidade Total" title="Disponibilidade Total" /></span>';
					else stock_txt='<span class="esgotado" title="Disponivel Brevemente">Stock: <img src="layout/'+PROJETO+'/images/status_busy.png" alt="Disponivel Brevemente" title="Disponivel Brevemente" /></span>';
				}
				else
				{
					stock_txt='<span class="esgotado" title="Disponivel Brevemente">Stock: <img src="layout/'+PROJETO+'/images/status_blue.png" alt="Disponivel Brevemente" title="Disponivel Brevemente" /></span>';
				}

				var botao='';
				if(object.carrinho == 2) botao += '';
				else if((stock >= 1 || TEM_ENC_SEM_STOCK == 1) && price > 0) botao += '<div class="prodby">'+key_comprar[LINGUA]+'<input type="hidden" value="'+id+'" name="prodid"/><input type="hidden" value="'+(fl==1 ? caminho : '')+'" name="prodfiltros" /></div>';
				else botao += '<div class="prodby-des fancy_stock" href="'+SITE_HTTP_AJAX_URL+'setNotificacaoProduto.php?idprod='+id+'">&nbsp;</div>';

				var wish='';
				var wish_top='';

				var etiqueta='';
				if(typeof object.u_c != 'undefined' && object.u_c != 'N/A')
				{
					if(trim(object.u_c) != '')
					{
						if(object.u_i!='')
						{
							etiqueta='<a style="background-color:'+object.u_co+'; border-right-color:'+object.u_co+'" class="eenergia2 fancybox" title="Etiqueta Energética" href="'+object.u_i+'" target="_blank">'+object.u_c+'</a>';
						}
						else
						{
							etiqueta='<a style="background-color:'+object.u_co+'; border-right-color:'+object.u_co+'" class="eenergia2" title="Etiqueta Energética">'+object.u_c+'</a>';
						}
					}
				}
				var enc_stock=0;
				if(typeof object.enc_stock != 'undefined')
				{
					enc_stock=object.enc_stock;
				}

				if(vista == '') //lista 3 produtos de cada vez
				{
					cell += '<div class="prd_d" id="prd_d_'+id+'">';
					cell += wish_top;
					cell += cell2;
					cell += etiqueta;
					//cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper"></span><img alt="..." src="layout/'+PROJETO+'/images/orders.gif" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : 'style="max-height:130px"')+' border="0"></a></div>';
					cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper 23"></span><img alt="..." src="'+image+'" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : 'style="max-height:130px"')+' border="0"></a></div>';
					cell += '<div class="prd_layer">';
					cell += '<div class="prd_title"><a title="'+title+'" href="'+caminho+'" target="_top">'+title+'</a></div>';
					//icons 
					if(typeof object.a33 != 'undefined' && object.a33 != '')
					{
						var grau_t = object.a33;
						var grau = grau_t.split(',');
						var top=5;
						console.log(grau);
						var grau_c = grau.length;
						if(grau_c>4)grau_c=4;
						for(var a=0; a<grau_c; a++)
						{
							var gg = grau[a].split('-');
							cell += '<div class="prod_cat_icons" style="top:'+top+'px;" title=""><img src="../../files/products_components/'+gg[0]+'_2.svg" height="48"></div>';
							top+=45;
						}
					}
					cell += '<div class="c"></div>';
					if(price_riscado!='' && object.global_promos.length &&  (typeof(object.global_promos[0]['data_inicio']) !== 'undefined') )
					{
						cell += '<div style="text-align: center; display: block; padding: 5px; background: #f8f8f8;">';
						cell += '<h5 style="padding: 0px; margin: 0px">'+object.global_promos[0]['designacao']+'</h5>';
						cell += '<span style="font-size: 10px;">'+object.global_promos[0]['data_inicio'].slice(0, 10)+' - '+object.global_promos[0]['data_fim'].slice(0,10)+'<b data-toggle="tooltip" title="" data-original-title="'+object.global_promos[0]['descricao']+'">&nbsp<img src="layout/'+PROJETO+'/images/alert.png" width="13"></b></span>';
						cell += '</div>';
					}
					cell += '<div class="c"></div>';
					cell += '<div class="prd_stock">'+stock_txt+'</div>';
					cell += '<div class="prdprice_n" id="precos_'+id+'" info="'+id+'|'+enc_stock+'" info2="'+id+'">';
					cell += '<div class="price" '+(price <= 0 ? 'style="visibility:hidden":' : '')+'>'+(price_riscado!='' ? price_riscado : '')+'&nbsp;'+(price<=0 ? '' : numberformat(price,2,',','.'))+'</div>';
					cell += '<div class="clear"></div>';
					cell += (price <= 0 && object.carrinho == 2 ? '<span class="price" style="height: 27px;display: block;text-align:center;cursor:pointer">Ver Info</span>' : botao);
					cell += '<div class="c"></div>';
					cell += '</div><div class="c"></div></div></div>';
				}
				else //lista 1 produto de cada vez
				{
					cell += '<div class="prd_d" id="prd_d_'+id+'">';
					cell += '<div class="left-block col-xs-4 col-sm-4 col-md-4">';
					cell += wish_top;
					cell += cell2;
					cell += etiqueta;
					//cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper"></span><img alt="..." src="layout/'+PROJETO+'/images/orders.gif" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : '')+' border="0"></a></div>';
					cell += '<div class="prd_img"><a title="'+title+'" href="'+caminho+'" style="text-decoration:none"><span class="helper 24"></span><img alt="..." src="'+image+'" class="lazy" id="img_'+id+'" data-original="'+image+'" '+(substr_count(image,'no_photo.png') ? 'style="height:106px"' : '')+' border="0"></a></div>';
					cell += '</div>';
					cell +='<div class="center-block col-xs-4 col-sm-4 col-md-4">';
					cell += '<div class="prd_layer">';
					cell += '<div class="prd_title"><a title="'+title+'" href="'+caminho+'" target="_top">'+title+'</a></div>';
					//icons 
					if(typeof object.a33 != 'undefined' && object.a33 != '')
					{
						var grau_t = object.a33;
						var grau = grau_t.split(',');
						var top=5;
						console.log(grau);
						var grau_c = grau.length;
						if(grau_c>4)grau_c=4;
						for(var a=0; a<grau_c; a++)
						{
							var gg = grau[a].split('-');
							cell += '<div class="prod_cat_icons" style="top:'+top+'px;" title=""><img src="../../files/products_components/'+gg[0]+'_2.svg" height="48"></div>';
							top+=45;
						}
					}
					cell += '<div class="c"></div>';
					cell += '</div>';
					cell += '</div>';
					cell += '<div class="right-block col-xs-4 col-sm-4 col-md-4">';
					if(price_riscado!='' && object.global_promos.length)
					{
						cell += '<div style="text-align: center; display: block; padding: 5px; background: #f8f8f8;">';
						cell += '<h5 style="padding: 0px; margin: 0px">'+object.global_promos[0]['designacao']+'</h5>';
						cell += '<span style="font-size: 10px;">'+object.global_promos[0]['data_inicio'].slice(0, 10)+' - '+object.global_promos[0]['data_fim'].slice(0,10)+'<b data-toggle="tooltip" title="" data-original-title="'+object.global_promos[0]['descricao']+'">&nbsp<img src="layout/'+PROJETO+'/images/alert.png" width="13"></b></span>';
						cell += '</div>';
					}

					cell += '<div class="prdprice_n" id="precos_'+id+'" info="'+id+'|'+enc_stock+'" info2="'+id+'">';
					cell += '<div class="price" '+(price <= 0 ? 'style="visibility:hidden":' : '')+'>'+(price_riscado!='' ? price_riscado : '')+'&nbsp;'+(price<=0 ? '' : numberformat(price,2,',','.'))+'</div>';
					cell += '<div class="clear"></div>';
					cell += (price <= 0 && object.carrinho == 2 ? '<span class="price" style="height: 27px;display: block;text-align:center;cursor:pointer">Ver Info</span>' : stock_txt+wish+botao);
					cell += '<div class="c"></div>';
					cell += '</div><div class="c"></div></div>';
					cell += '</div>';
				}
				//show_img(object.id);
				//show_designacao(object.id);
		}
	}
	return cell;
}