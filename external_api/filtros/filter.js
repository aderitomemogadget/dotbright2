/* if(produto_ativo > 0 || TIPOPAGINA == 'wishlist' || HOME == 1) var maxColumn = 4;    // max cells in a row
else  */var maxColumn = 4;    // max cells in a row

var range = 3;    // range of num links to show
// var filtro_selecionado = '';

var rowsPerPage = 0; var currentpage = 0; var maxPrice = 0; var minPrice = 0; var sortBy = ''; var sortByTitle = 'title'; var sortByPrice = 'price'; var sortOrder = 4;  var marcas = 0; var marcas_sel = 0; var seach_sel = 0; var search_sel = 0;  var fam_id1 = 0; var fam_id2 = 0; var fam_id3 = 0; var fam_id4 = 0; var fam_id5 = 0; var fam_id6 = 0; var fam_id7 = 0; var fam_id8 = 0; var fam_id9 = 0; var fam_id10 = 0;

// Add ECMA262-5 Array methods if not supported natively
//
if (!('indexOf' in Array.prototype)) {
	Array.prototype.indexOf= function(find, i /*opt*/) {
		if (i===undefined) i= 0;
		if (i<0) i+= this.length;
		if (i<0) i= 0;
		for (var n= this.length; i<n; i++)
			if (i in this && this[i]===find)
				return i;
		return -1;
	};
}

function number_format_(number, decimals, dec_point, thousands_sep)
{
	var n = number, prec = decimals;
	var toFixedFix = function (n,prec)
	{
		var k = Math.pow(10,prec);
		return (Math.round(n*k)/k).toString();
	};
	n = !isFinite(+n) ? 0 : +n;
	prec = !isFinite(+prec) ? 0 : Math.abs(prec);
	var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
	var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
	var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
	var abs = toFixedFix(Math.abs(n), prec);
	var _, i;
	if (abs >= 1000)
	{
		_ = abs.split(/\D/);
		i = _[0].length % 3 || 3;

		_[0] = s.slice(0,i + (n < 0)) +
			  _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
		s = _.join(dec);
	} else   s = s.replace('.', dec);
	var decPos = s.indexOf(dec);
	if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
	else if (prec >= 1 && decPos === -1) s += dec+new Array(prec).join(0)+'0';
	return s;
}
function include(arr,obj)
{
	if(arr) return (arr.indexOf(obj) != -1);
}
function inArray(needle, haystack)
{
	var length = haystack.length;
	for(var i = 0; i < length; i++)
	{
		if(haystack[i] == needle) return true;
	}
	return false;
}
function setProducts()
{
	if(typeof products == 'undefined') {products = [];}

	sortBy = sortBy ? sortBy : $('#product_sort').val() ;
	rowsPerPage = rowsPerPage ? rowsPerPage:$('#f_visualizacao option:selected').val();
	// var $c_pagination_order = $.cookies.get('c_pagination_order');
	// if ($c_pagination_order){ currentpage=$c_pagination_order;}
	currentpage = currentpage > 0 ? currentpage : 1; // if current page is less than first page...

	var totalLoop = rowsPerPage;
	var loop = 0;
	var countCellData = 0;
	var offset = 0;
	// LIMPAR O CONTEUDO
	$('#product_show').html('&nbsp;');
	var filterdProducts = [];  // ARRAY DOS PRODUTOS
	var key = 0;
	var and = 0;
	rel_sel = $("#relevancia_list :checked").length;
	rel_f_sel = 0;
	marcas_sel = $("#m_l :checked").length;
	// marcas_sel = $('#m_l select').find(":selected").val();
	// marcas_sel =  ($('#m_l select').find(":selected").val() > 0 ? $('#m_l select').find(":selected").val() : (marca_ativa > 0 ? marca_ativa : $('#m_l select').find(":selected").val()));
	stock_sel =  $('select#f_stock').find(":selected").val();
	cart_sel = $("#c_l :checked").length;
	pric_sel = $("#price_list :checked").length;

	// fam_id1 = (familia_ativa > 0 ? familia_ativa : 0);

			if($("#search_box").length>0)search_sel = $("#search_box").val().length; else  search_sel=0;
			// TODOS OS PRODUTOS
			if(stock_sel==0 && marcas_sel == 0 && rel_sel==0 && rel_f_sel==0 && cart_sel==0 && pric_sel==0 && search_sel==0 && fam_id1==0 && fam_id2==0 && fam_id3==0 && fam_id4==0 && fam_id5==0 && fam_id6==0 && fam_id7==0 && fam_id8==0 && fam_id9==0 && fam_id10==0) filterdProducts = products;
			if(products.length==0)
			{
				$(".catalogo_topo_filtros").hide();
				$(".filtro").hide();
			}
			$.each(products, function(i, object)
			{

				//console.log(object);
				if(marcas_sel > 0) var marca_ok = 0; else var marca_ok = 1;
				if(stock_sel > 0) var stock_ok = 0; else var stock_ok = 1;
				if(rel_sel > 0) var relavancia_ok = 0;  else var relavancia_ok = 1;
				if(rel_f_sel > 0) var relavancia_f_ok = 0;  else var relavancia_f_ok = 1;
				if(cart_sel > 0) var caract_ok = 0;  else var caract_ok = 1;
				if(pric_sel > 0) var precos_ok = 0; else var precos_ok = 1;
				if(search_sel > 0) var search_ok = 0; else var search_ok = 1;

				if(fam_id1 > 0) var fam_id1_ok = 0; else fam_id1_ok = 1;
				if(fam_id2 > 0) var fam_id2_ok = 0; else fam_id2_ok = 1;

				if(fam_id1 > 0) var fam_ok = 0; else fam_ok = 1;
				if(fam_id2 > 0) var fam_ok = 0; else fam_ok = 1;

				// FAMILIAS
				f_sel = object.f;
				//FAM1
				var fam1_sel_in = new Array();	var count = 0;	var count2 = 0;
				if(fam_id1 > 0)
				{
					fam_ok = 0;
					$.each(f_sel, function(x, val){	fam1_sel_in[count2] = val['f_id'];	count2++;});
					if(inArray(fam_id1, fam1_sel_in)) fam_ok = 1;
				}

				// PESQUISA
				m_search = object.title;
				m_search_lower = m_search.toLowerCase()
				if(search_sel > 0)
				{
					var mySearch = $("#search_box").val().toLowerCase();

					if (m_search_lower.indexOf(mySearch) > -1)
					{
						if(!include(filterdProducts,object))
						{
							search_ok = 1;
						}
					}
				}
				// MARCAS
				m_sel = object.m_id;
				$("#m_l :checked").each(function()
				// $("#m_l :selected").each(function()
				{
					if(this.value ==  m_sel)
					{
						if(!include(filterdProducts,object))
						{
							marca_ok = 1;
						}
					}
				});

				st_sel = object.stock;

				$("#f_stock :selected").each(function()
				{
					if(this.value ==  1 && st_sel > 0)
					{
						if(!include(filterdProducts,object))
						{
							stock_ok = 1;
						}
					}
					else if(this.value == 2 && st_sel <= 0)
					{
						if(!include(filterdProducts,object))
						{
							stock_ok = 1;
						}
					}
				});
				// RELEVANCIA HOME PAGE
				var novidade = object.novidade;
				var promo = object.promo;
				var destaque = object.destaque;
				var oportunidade = object.oportunidade;
				var outlet = object.outlet;
				$("#relevancia_list :checked").each(function()
				{
					 if((this.id=='novidade' && novidade== 1) || (this.id=='promocao' && promo==1) || (this.id=='destaque' && destaque==1) || (this.id=='oportunidade' && oportunidade==1) || (this.id=='outlet' && outlet==1))
					 {
						if(!include(filterdProducts,object))
						{
							relavancia_ok = 1;
						}
					 }
				});

				// CARACTERISTICAS
				c_sel = object.c;
				var c_sel_in = new Array();
				var count = 0;
				var count2 = 0;
				$("#c_l :checked").each(function()
				{
					ct_val = this.value;
					$.each(c_sel, function(x, val)
					{
						c_sel_in[count2] = val['c_id'];
						count2++;
					});

					if(inArray(ct_val, c_sel_in)) count++;
					if(count == cart_sel) caract_ok = 1;
				});
				// ENTRE PRECOS
				var curentPrice = parseFloat(object.price);
				var priceMinOk = true;
				var priceMaxOk = true;

				$("#price_list :checked").each(function()
				{

					var priceMinOk = true;
					var priceMaxOk = true;
					price_range = this.value;
					price_range2 = price_range.split('-');

					var minPrice = parseFloat(price_range2[0]);
					var maxPrice = parseFloat(price_range2[1]);


					if(maxPrice<curentPrice) priceMaxOk = false;
					if(minPrice>curentPrice) priceMinOk = false;

					if( priceMinOk && priceMaxOk )
					{
						if(!include(filterdProducts,object)) precos_ok = 1;
					}
				});

				//BIG HAMMER
				//verifica se stock > 0 e se pertence à familia das reparações
				var stock_reparacoes = 1;
				if(object.stock > 0 || object.f[0].f_id == 85) stock_reparacoes = 1;

				//console.log(stock_ok+' '+marca_ok+' '+relavancia_ok+' '+relavancia_f_ok+' '+caract_ok+' '+precos_ok+' '+search_ok+' '+fam_ok+' '+stock_reparacoes);
				if(stock_ok == 1 && marca_ok == 1 && relavancia_ok == 1 && relavancia_f_ok == 1 && caract_ok ==1 && precos_ok == 1 && search_ok == 1 && fam_ok==1 && stock_reparacoes == 1)
				{
					if(!include(filterdProducts,object))
					{
						filterdProducts[key] = object;
						key++;
					}
				}
			});
			// get the amount of results
			var totalResults = filterdProducts.length;

			//console.log(totalResults);
			//console.log(sortOrder);


			if(totalResults > 0)
			{
				function PrecoDESC(a,b) { var a2 = a['price'], b2 = b['price']; return parseFloat(a2) < parseFloat(b2) ? 1 : -1;} // DO MAIOR PARA MENOR
				function PrecoASC(a,b) {var a2 = a['price'], b2 = b['price']; return parseFloat(a2) > parseFloat(b2) ? 1 : -1;} // DO MENOR PARA MAIOR

				function numberRAND(a,b) { return 0.5 - Math.random();} // RANDOM
				function numberNEW(a,b) {  return b['id'] - a['id'];} // NEW

				function AtoZ(a,b) { var a1 = a['title'], b1 = b['title']; a1 = a1.toLowerCase(); b1=b1.toLowerCase(); return a1 > b1 ? 1 : -1;} // A-Z
				function ZtoA(a,b) { var a1 = a['title'], b1 = b['title']; a1 = a1.toLowerCase(); b1=b1.toLowerCase(); return (a1 > b1) ? -1 : (a1 < b1) ? 1 : 0;} // Z-A

				function mysortfunction(a, b) {

				  var a1 = parseInt(a['novidade']);
				  var a2 = parseInt(b['novidade']);

				  var b1 = parseInt(a['destaque']);
				  var b2 = parseInt(b['destaque']);

				  var c1 = parseInt(a['promo']);
				  var c2 = parseInt(b['promo']);

				  if (a1 < a2) return -1;
				  if (a1 > a2) return 1;

				  if (b1 < b2) return -1;
				  if (b1 > b2) return 1;

				  if (c1 < c2) return -1;
				  if (c1 > c2) return 1;

				  return 0;
				}

				function value(el) {
					var x = el;
					return x === 0 ? Infinity : x;
				}

				if(sortOrder == 4) // Relevancia
				{
					// console.log(HOME);
					if(pesquisa!=''){}
					else
					{
						filterdProducts.sort(
						firstBy(function (v1, v2)
						{
							var x = parseInt(v2['ord']);
							var y = v1['ord'];
							return ((x < y) ? -1 : ((x > y) ? 1 : 0));
						})
						.thenBy(function (v1) { return v1['promo']; })
						.thenBy(function (v1) { return v1['novidade']; })
						.thenBy(function (v1) { return v1['destaque']; })
						.thenBy(function (v1) { return v1['outlet']; })
						.thenBy(function (v1,v2) { return v2['id'] - v1['id'];})
						);
					}
				}
				else if(sortOrder == 3) // MAIS CARO
				{
					filterdProducts.sort(PrecoDESC);
				}
				else if(sortOrder == 2) // MAIS BARATO
				{
					filterdProducts.sort(PrecoASC);
				}
				else if(sortOrder == 1) // Z-A
				{
					filterdProducts.sort(ZtoA);
				}
				else // A-Z
				{
					filterdProducts.sort(AtoZ);
				}
			}

			//console.log(filterdProducts);
			// console.log(filterdProducts);
			// show the amount of results
			$('.product_results').html(totalResults);
			 /****** start build pagination links ******/
			var totalpages = Math.ceil(totalResults/ rowsPerPage) ; // calculate the total pages
			currentpage = Number(currentpage);

			if(totalpages > 1)
			{
				// fix displayed page number if its biger then exist
				if (currentpage > totalpages)
				{
				   // set current page to last page
				   currentpage = totalpages;
				   $.cookies.set('c_pagination_order', currentpage);
				}
				// set the offset of the list, based on current page
				offset = (currentpage - 1) * rowsPerPage;

				var pagination = '';
				var lastPage = 0;
				// set the min page of the list, based on the range
				var minPage = parseFloat(currentpage) - parseFloat(range);
				minPage = minPage > 0 ? minPage : 1;
				// if not page 1, don't show back links
				if (currentpage > 1)
				{
				   // get previous page num
				   pagination += '<button type="button" name="'+(currentpage - 1)+'" class="product_button" title="Anterior"><img src="'+SITE_HTTP_URL+'layout/default/filtros/back.png" border="0" \/><\/button>';
				   // show page 1 only if the min page isn`t page 1 (prevent page 1 to show twice)
				   if(minPage > 1)
				   {
						if(HOME=='0' && produto_ativo=='') pagination += '<button type="button" name="1" class="product_button" title="1" >...<\/button>';
				   }
				}
				// loop to show links to range of pages around current page
				if(HOME=='0' && produto_ativo=='')
				{
					for(var pi = (currentpage - 2); pi < (currentpage + 2); pi++)
					{
					   pagination += (pi >= 1 && pi <= totalpages) ? currentpage == pi ? '<button type="button" name="0" class="product_button_selected" title="'+pi+'" >'+pi+'<\/button><\/div><\/div>' : '<button type="button" name="'+pi+'" class="product_button" title="'+pi+'" >'+pi+'<\/button>' : '';
					}
				}
				// if not on last page, show forward and last page links
				if (currentpage != totalpages)
				{
				   // get next page
					var nextPage = parseFloat(currentpage) + 1;

					if(lastPage < totalpages && totalpages > 2)
					{
						// show page last page only if the min page isn`t last page (prevent page 1 to show twice)
						if(HOME=='0' && produto_ativo=='') pagination += '<button type="button" name="'+totalpages+'" class="product_button" title="'+totalpages+'" >...<\/button>';
					}
					 pagination += '<button type="button" name="'+nextPage+'" class="product_button" title="Próximo"><img src="'+SITE_HTTP_URL+'layout/default/filtros/foward.png" border="0" \/><\/button>';
				}
				// update the html
				$('.paginacao').html(pagination);
			}
			else
			{
				// if no pages or just one page dont show pagination
				$('.paginacao').html('<button type="button" name="0" class="btn product_button_selected HvBd none" title="1" >1<\/button>');
				$.cookies.set('c_pagination_order', 1);
			}
			$('.page_results').html(currentpage);
			$('.page_results2').html(totalpages);
			var offsetEnd = parseFloat(rowsPerPage) + parseFloat(offset);
			/****** end build pagination links ******/
			// build cells content

			var cell='';
			// get the keys is in the display pagination range
			var pageProducts = filterdProducts.slice(offset,offsetEnd);
			// loop over the query list
			// if(!SESSAO || REVEN) bl = 'bloco'; else  bl = 'bloco_3';

			// console.log(pageProducts);
			var vista = '';
			if(view_type == 1) vista = '_lista';
			if(pageProducts.length<=0) cell += '<div class="titulo_prods" style="float:left;font-size:14px;padding-left:35px;padding-top:35px;width:500px;border:#000 0px solid">Não foram encontrados produtos para os filtros selecionados!<\/div>';

			var ax=maxColumn; var ax2=0; var st='';

			var todayDate = new Date();

			var date_ct = new Date();
			var day_ct = date_ct.getDate();
			var monthIndex_ct = date_ct.getMonth();
			var year_ct = date_ct.getFullYear();

			var todayDate = new Date(year_ct, (monthIndex_ct), day_ct);

			//console.log(pageProducts);

			$.each(pageProducts, function(i, object)
			{
				// get min and max price range for price range filter slider
				countCellData++; // flug to know if there is content


				// if(ax==ax2){ ax2=0; cell += '<div class="c"></div>';}
				//console.log(object.f[0].f_id+"="+object.title+"="+object.stock);
				//console.log(object.stock);
				 //if(1==1 || object.stock > 0 || object.f[0].f_id == 85) //Reparações
				 if(object.stock > 0 || object.f[0].f_id == 85) //Reparações
				 {
					if(HOME=='1' || produto_ativo > 0 || pesquisa!='') cell += '<div class="col-xs-12 col-sm-6 col-md-3">';
					else cell += '<div class="col-xs-12 col-sm-6 col-md-4">';

					if(produto_ativo > 0) cell += '<div class="prd_d less">';
					else cell += '<div class="prd_d">';

					var p_mercado = 0;

					var titulo = '<div class="prd_title"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'"  target="_top">'+object.title+'</a><\/div>';

					var stock_txt = '';
					/*if(object.stock == 1) stock_txt = '<span class="limitado">Stock: <img src="layout/'+PROJETO+'/images/status_away.png" /></span>';
					else if(object.stock > 1) stock_txt = '<span class="disponivel">Stock: <img src="layout/'+PROJETO+'/images/status.png" /></span>';
					else stock_txt = '<span class="esgotado">Stock: <img src="layout/'+PROJETO+'/images/status_busy.png" /></span>';*/

					if(object.stock > 0) stock_txt = '<span class="disponivel">Stock: <img src="layout/'+PROJETO+'/images/status.png" /></span>';
					else stock_txt = '<span class="esgotado">Stock: <img src="layout/'+PROJETO+'/images/status_busy.png" /></span>';


					var stock = '<div class="prdstock_n">'+stock_txt+'</div>';

					// DATA DE FIM PROMOÇÃO
					var promo_data = object.promo_data.split('-');
					var promoEndDate = new Date(promo_data[0], (promo_data[1]-1), promo_data[2]);

					// DATA DE FIM NOVIDADE
					var novidade_data = object.novidade_data.split('-');
					var novidadeEndDate = new Date(novidade_data[0], (novidade_data[1]-1), novidade_data[2]);

					// DATA DE FIM OUTLET
					var outlet_data = object.outlet_data.split('-');
					var outletEndDate = new Date(outlet_data[0], (outlet_data[1]-1), outlet_data[2]);

					// DATA DE FIM DESTAQUE
					var destaque_data = object.destaque_data.split('-');
					var destaqueEndDate = new Date(destaque_data[0], (destaque_data[1]-1), destaque_data[2]);

					// DATA DE FIM DESTAQUE
					var dodia_data = object.dodia_data.split('-');
					var dodiaEndDate = new Date(dodia_data[0], (dodia_data[1]-1), dodia_data[2]);



					var promo = 0;
					if(object.promo==1 &&  object.price_riscado > object.price && promoEndDate.getTime() >= todayDate.getTime()) //&& object.price_riscado > object.price
					{
						// if( object.price_riscado > object.price)
						// {
							var percdesconto = getDescontoAplicado(object.price_riscado, object.price);
							promo = number_format_(object.price_riscado,2,',','.');
						// }
						cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-promo">PROMOÇÃO</div></div>';
						// cell += '<div class="prd_tagdiscount">-'+percdesconto+'%</div>';
					}
					else if(object.promo==2 && object.price_riscado > object.price) //
					{
						p_mercado = 'style="padding-top:15px;"';
						cell += '<div class="ribbon-wrapper2" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer">€ '+number_format_(object.price_riscado,2,',','.')+'</div>';

						cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-destaque">BEST PRICE</div></div>';


						if(object.novidade==1 && novidadeEndDate.getTime() >= todayDate.getTime())
						{
							cell += '<div class="ribbon-wrapper3" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-novo3">NOVIDADE</div></div>';
						}
					}
					else/*  if(object.outlet==1 && outletEndDate.getTime() >= todayDate.getTime())
					{
						if(typeof object.outlettxt == 'undefined') cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-outlet">OUTLET</div></div>';
						else cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-outlet">'+object.outlettxt+'</div></div>';
					}
					else */ if(object.novidade==1 && novidadeEndDate.getTime() >= todayDate.getTime())
					{
						cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-novo">NOVIDADE</div></div>';
					}
					else if(object.destaque==1 && destaqueEndDate.getTime() >= todayDate.getTime())
					{
						// cell += '<div class="ribbon-wrapper"><div class="ribbon-destaque">BEST PRICE</div></div>';
					}
					else if(object.dodia==1 && dodiaEndDate.getTime() == todayDate.getTime())
					{
						cell += '<div class="ribbon-wrapper" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-destaque">DO DIA</div></div>';
					}

					if(object.outlet==1 && outletEndDate.getTime() >= todayDate.getTime())
					{
						if(typeof object.outlettxt == 'undefined') cell += '<div class="ribbon-wrapper4" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-outlet4">OUTLET</div></div>';
						else cell += '<div class="ribbon-wrapper4" onclick="window.open(\''+SITE_HTTP_URL+object.caminho+'\',\'_self\');" style="cursor:pointer"><div class="ribbon-outlet4">'+object.outlettxt+'</div></div>';
					}

					fam_rep = 0;
					f_sel = object.f;
					$.each(f_sel, function(x, val){	if(val['f_id'] == 85) fam_rep = 1});

					preco = '<div class="prdprice_n" id="precos_'+object.id+'" info="'+object.id+'|'+fam_rep+'" '+p_mercado+'>';					
					preco += '</div>';


					var imm = object.image;
					var medida = 'height:190px';

					if(substr_count_v(object.ref,'DIAGNOSTICO')) imm = 'layout/diagno_120.jpg';
					if(substr_count_v(object.ref,'REPIPHONE6WIFI')) imm = 'layout/WIFI.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7AUSCULTADOR')) img='AUSCULTADOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7AUSC')) img='AUSCULTADOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7SPEAKER')) img='AUSCULTADOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7MICROFONE')) img='MICROFONE.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7MIC')) img='MICROFONE.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7ALTIFALANTE')) img='ALTIFALANTE.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7ALTI')) img='ALTIFALANTE.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARA')) img='CAMARA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARAFRONTAL')) img='CAMARAFRONTAL.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARAFRONTAL')) img='CAMARAFRONTAL.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7VIBRADOR')) img='VIBRADOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7VIBRA')) img='VIBRADOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7ONOFF')) img='ONOFF.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7SILENCIO')) img='SILENCIO.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7MUTE')) img='SILENCIO.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7VOLUME')) img='VOLUME.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7HOME')) img='HOME.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7SENSOR')) img='SENSOR.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7AUDIOJACK')) img='AUDIOJACK.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7CARGA')) img='CCARGA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7BATERIA')) img='BATERIA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7CARCACA')) img='CARCACA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE7TRAS')) img='VIDROTRASEIRO.jpg';
					if(substr_count_v(object.ref,'REPSGA705TRASEIRA')) img='TAMPA_TRASEIRA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE12PRODIAGNOSTICO')) img='DIAGNOSTICO.jpg';
					if(substr_count_v(object.ref,'REPIPHONE12PRODIAG')) img='DIAGNOSTICO.jpg';
					if(substr_count_v(object.ref,'REPIPHONE12PROLENTECAMARA')) img='LENTECAMARA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE12PROLENTECAM')) img='LENTECAMARA.jpg';
					if(substr_count_v(object.ref,'REPIPHONE11LEITORSIM')) img='LEITORSIM.jpg';
					if(substr_count_v(object.ref,'REPIPHONE11SIM')) img='LEITORSIM.jpg';
					if(substr_count_v(object.ref,'REPIPAD7LCD')) img='LCD (1).jpg';
					if(substr_count_v(object.ref,'REPIPAD7TOUCH')) img='TOUCH.jpg';
				

					if(substr_count_v(object.ref,'REPIPHONE6ICAUDIO')) img='IC_BOARD.jpg';
					if(substr_count_v(object.ref,'REPIPHONE6ICTOUCH')) img='IC_BOARD.jpg';
					if(substr_count_v(object.ref,'REPIPHONE6ICBACKLIGHT')) img='IC_BOARD.jpg';
					if(substr_count_v(object.ref,'REPIPHONE6ICCARGA')) img='IC_BOARD.jpg';
					if(substr_count_v(object.ref,'REPIP11MODULOOR')) img='MODULO.jpg';
					if(substr_count_v(object.ref,'REPIPHONE11MODULOLCD')) img='LCD.jpg';


					/*REPIPHONE6PLACA devem ser associadas à imagem "board.jpg"
					REPIPHONE11PROMODULOCOMP" "modulo.jpg"
					REPIPHONE7SWAP devem ter associada a imagem "swap.jpg"*/






					if(substr_count_v(object.ref,'DIAGNOSTICO')) medida = 'height:106px';
					if(substr_count_v(object.ref,'REPIPHONE6WIFI')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7AUSCULTADOR')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7AUSC')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7SPEAKER')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7MICROFONE')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7MIC')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7ALTIFALANTE')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7ALTI')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARAFRONTAL')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7CAMARAFRONTAL')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7VIBRADOR')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7VIBRA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7ONOFF')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7SILENCIO')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7MUTE')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7VOLUME')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7HOME')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7SENSOR')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7AUDIOJACK')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7CARGA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7BATERIA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7CARCACA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE7TRAS')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPSGA705TRASEIRA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE12PRODIAGNOSTICO')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE12PRODIAG')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE12PROLENTECAMARA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE12PROLENTECAM')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE11LEITORSIM')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE11SIM')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPAD7LCD')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPAD7TOUCH')) medida = 'height:200px';

					if(substr_count_v(object.ref,'REPIPHONE6ICAUDIO')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE6ICTOUCH')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE6ICBACKLIGHT')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE6ICCARGA')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIP11MODULOOR')) medida = 'height:200px';
					if(substr_count_v(object.ref,'REPIPHONE11MODULOLCD')) medida = 'height:200px';

		


					cell += '<div class="prd_img"><a title="'+object.title+'" href="'+SITE_HTTP_URL+object.caminho+'" style="text-decoration:none">';
					cell += '<span class="helper 4"></span>';
					cell += '<img alt="..." src="'+imm+'" border="0" '+(substr_count_v(object.image,'no_photo.png') ? 'style="'+medida+'"' : 'style="'+medida+'"')+'/></a>';
					cell += '</div>';

					cell += '<div class="prd_layer">';
					cell += titulo;
					cell += '<div class="c"></div>';

					// if(typeof object.graus != 'undefined' && object.graus != '')
					// {
					// 	var grau_t = object.graus;
					// 	var grau = grau_t.split('|');
					// 	var left=10;
					// 	for(var a=0; a<grau.length; a++)
					// 	{

					// 		var gg = grau[a].split('-');
					// 		cell += '<div style="position:absolute;top:10px;left:'+left+'px;border-radius:100%;background:#000;width:30px;height:30px;padding: 6px;text-align:center;font-weight:bold;color:#FFF" title="">'+gg[0]+'</div>';
					// 		left+=35;
					// 	}
					// }

					//icons 
					// console.log(object);
					if(typeof object.a33 != 'undefined' && object.a33 != '')
					{
						var grau_t = object.a33;
						var grau = grau_t.split(',');
						var top=5;
						console.log(grau);
						for(var a=0; a<grau.length; a++)
						{
							var gg = grau[a].split('-');
							cell += '<div class="prod_cat_icons" style="top:'+top+'px;" title=""><img src="../../files/products_components/'+gg[0]+'_2.svg" height="48"></div>';
							top+=45;
						}
					}

					cell += preco;
					cell += '<div class="c"></div>';
					cell += '</div>';

					cell += '<\/div>';
					cell += '<\/div>';
				 }
				totalLoop--;
				loop++;
				ax2++;
			});
			cell += '<\/div>';

		if(countCellData > 0 || pageProducts.length <= 0) // IF EXIST CONTENT
		{


			$('#product_show').html(cell);

			$(".prodby").click(function()
			{
				var prodid=$(this).children('input[name="prodid"]').val();
				var qtd=1;
				adicionarCarrinhoProduto(prodid,qtd);
			});

			$(".prd_whish").tooltipster({trigger:'click',animation: 'fade', delay: 200, theme: 'tooltipster-default','functionReady':function(){ var el = $(this); setTimeout(function(){ el.tooltipster('hide',function(){ el.tooltipster('content',el.attr("title")); });  },1000) }});
		}

	setPagination();
	show_precos();

}

function show_precos()
{
	var dataString='';
	$("div.prdprice_n").each(function()
	{

		var x_id = $(this).attr('info');
		if(x_id != '')
		{
			if(dataString != '') dataString += ';'+x_id;
			else dataString = x_id;
		}
	});
	var dados;

	$.ajax
		({
			type: "POST",
			url: SITE_HTTP_AJAX_URL + "do_blocos_precos.php",
			data: 'prods='+dataString,
			cache: false,
			success: function(html)
			{
				dados = html.split('[split]');

				var length = dados.length;
				for(var i = 0; i < length; i++)
				{
					var prods = dados[i].split('|');
					var prod_id = prods[0];
					var html_prod = prods[1];
					$('#precos_'+prod_id).html(html_prod);
				}

			}
		});
}
  // page button click
  function setPagination()
  {

	   $('.paginacao button[name!=0]').click(function()
	   {
			currentpage = $(this).attr('name');

			console.log(currentpage);

			if(familia_ativa > 0 && produto_ativo=='') $.cookies.set('pagina_ativa'+familia_ativa, currentpage);
			else $.cookies.set('c_pagination_order', currentpage);



			// alert($.cookies.get('pagina_ativa'+familia_ativa));
			displayProducts();
			window.location.hash='pagina='+currentpage;
       });
  }
  // animante list display
  function displayProducts()
  {
		$('#product_show').fadeOut('fast', function()
		{
			setProducts();
			$('#product_show').fadeIn('fast',function()
			{
				// window.scrollTo(0,0);
				if(produto_ativo > 0) {}
				else{ $(window).scrollTop(($('.header-container').offset().top));}
				displayFilters();

			});
		});
		if(produto_ativo != ''){ $('#div_cont_right_cat2').html(""); $('#filtros_no_prd').show();}
  }

$(document).ready(function()
{
	if($('#f_marcas').length > 0)
	{
		// $('#m_l').insertAfter('.filtro_view');
	}
	else if($('#fam_level1').length > 0)
	{
		$('#f_l').insertAfter('.filtro_view');
		$("#fam_level1 > option[value='"+familia_ativa+"']").attr('selected', 'selected');
	}

	$('.label_check').unbind("click").bind('click', function(event){ setupLabel();});
	$('.label_check_rel').unbind("click").bind('click', function(event){ setupLabel();});
	$('.label_check_p').unbind("click").bind('click', function(event){ setupLabel();});
	$('.color_check').unbind("click").bind('click', function(event)
	{
		if($(this).children("input").is(':checked')==true)
		{
			//deselect
			$(".color_check").children("input").attr("checked",false);
			setupLabel();

			if($('#category_'+$(this).children("input").val()).is(':checked')==false)  $.cookies.set('c_color_order', null);
			else $.cookies.set('c_color_order', $(this).children("input").val());

			currentpage = 0;
			displayProducts();
		}
		else
		{
			// ativo
			$(".color_check").children("input").attr("checked",false);
			$(this).children("input").attr("checked",true);
			setupLabel();

			if($('#category_'+$(this).children("input").val()).is(':checked')==false)  $.cookies.set('c_color_order', null);
			else $.cookies.set('c_color_order', $(this).children("input").val());

			currentpage = 0;
			displayProducts();
		}

		event.stopPropagation();
		event.preventDefault();
	});

	if(filtro_selecionado != "")
	{
		destroy_all_coockies();

		if(substr_count_v(filtro_selecionado, 'ignora'))
		{
			var filtro_selecionado2 = filtro_selecionado.split('|');
			$("#c_l input").each(function()
			{
				if(filtro_selecionado2[1] == $(this).val()){}
				else
				{
					$('#category_'+$(this).val()).attr("checked", 'checked');
				}

			});
		}
		else
		{
			$('#category_'+filtro_selecionado).attr("checked", 'checked');
		}
		setupLabel(); displayFilters();
	}

	if(marca_ativa > 0 && produto_ativo=='')
	{
		$.cookies.set('c_familia_on', null);
		if($.cookies.get('c_marca_on') != marca_ativa) destroy_all_coockies();
		$.cookies.set('c_marca_on', marca_ativa);
	}
	else if(familia_ativa > 0 && produto_ativo=='')
	{
		$.cookies.set('c_marca_on', null);
		if($.cookies.get('c_familia_on') != familia_ativa) destroy_all_coockies();
		$.cookies.set('c_familia_on', familia_ativa);


		// currentpage = $.cookies.get('c_pagination_order_'+familia_ativa);
		// currentpage = $.cookies.get('pagina_ativa'+familia_ativa);
		// if(currentpage)displayProducts();

		// else $.cookies.set('c_pagination_order', currentpage);
	}


	var currentpage_hash = window.location.hash;
	if(substr_count_v(currentpage_hash, '#pagina='))
	{
		currentpage = currentpage_hash.replace('#pagina=','');
		console.log('currentpage:'+currentpage);
		if(currentpage) displayProducts();
	}

	if(!produto_ativo)
	{
		// Não deve estar a ser usado
		var $c_product_sort = $.cookies.get('c_product_sort');
		if ($c_product_sort) sortBy = $c_product_sort;

		// mais caro-mais barato....
		var $c_product_order = $.cookies.get('c_product_order');
		if ($c_product_order) sortOrder = $c_product_order;
		else sortOrder = 4;//pre definido A-Z

		// Marcas
		$("#m_l input").each(function()
		{
			var $c_marca_order = $.cookies.get('c_marca_order'+$(this).val());
			if ($c_marca_order){$('#marca_'+$c_marca_order).attr("checked", 'checked'); setupLabel();}
		});
		// Relevancia
		var $c_destaque_order = $.cookies.get('c_destaque_order');
		if ($c_destaque_order){ $('#destaque').attr("checked", 'checked'); setupLabel();}
		var $c_novidade_order = $.cookies.get('c_novidade_order');
		if ($c_novidade_order){ $('#novidade').attr("checked", 'checked'); setupLabel();}
		var $c_promocao_order = $.cookies.get('c_promocao_order');
		if ($c_promocao_order){ $('#promocao').attr("checked", 'checked'); setupLabel();}
		// Caracteristicas
		$("#c_l input").each(function()
		{
			if($(this).parent().hasClass("color_check"))
			{
				var $c_color_order = $.cookies.get('c_color_order');
				if ($c_color_order){	$('#category_'+$c_color_order).attr("checked", 'checked'); setupLabel(); displayFilters();}
			}
			else
			{
				var $c_category_order = $.cookies.get('c_category_order'+$(this).val());
				if ($c_category_order){	$('#category_'+$c_category_order).attr("checked", 'checked'); setupLabel(); displayFilters();}
			}
		});

		$("#price_list input").each(function()
		{
			var $c_preco_order = $.cookies.get('c_preco_order'+$(this).val());
			if ($c_preco_order){$('#preco_'+$c_preco_order).attr("checked", 'checked'); setupLabel();}
		});


		var $c_fam_id1_order = $.cookies.get('c_fam_id1_order');
		if ($c_fam_id1_order)
		{
			$("option[value='"+$c_fam_id1_order+"']").attr('selected', 'selected');
			fam_id1 = $c_fam_id1_order;
			if(!produto_ativo)fam_level_forcado(1, $c_fam_id1_order);
		}
		var $c_fam_id2_order = $.cookies.get('c_fam_id2_order');
		if ($c_fam_id2_order)
		{
			fam_id2 = $c_fam_id2_order;
			fam_level_forcado(2, $c_fam_id2_order);
		}
	}
	setProducts();

     $('#product_sort').live('change',function()
	 {
		 sortBy = $(this).val();
		 $.cookies.set('c_product_sort', sortBy);
         currentpage = 0;
         sortOrder = 4;
         displayProducts();
     });
     $('#product_order').live('change',function()
	 {
         sortOrder  = $(this).val();
		 $.cookies.set('c_product_order', sortOrder);
         currentpage = 0;
         displayProducts();
     });
     $('#f_visualizacao').live('change',function()
	 {
         currentpage = 0;
         rowsPerPage  = $(this).val();
         displayProducts();
     });
	 $('#f_stock').live('change',function()
	 {
         currentpage = 0;
         displayProducts();
     });
	 //
	 // $("#m_l select").live('change',function()
	 $("#m_l input:checkbox").live('click',function()
	 {

		if($('#marca_'+$(this).val()).is(':checked')==false)  $.cookies.set('c_marca_order'+$(this).val(), null);
		else $.cookies.set('c_marca_order'+$(this).val(), $(this).val());

		currentpage = 0;
		displayProducts();
	 });
	 $("#relevancia_list input:checkbox").live('click',function()
	 {
		if($('#destaque').is(':checked')==false)  $.cookies.set('c_destaque_order', null);
		else $.cookies.set('c_destaque_order', $(this).val());

		if($('#promocao').is(':checked')==false)  $.cookies.set('c_promocao_order', null);
		else $.cookies.set('c_promocao_order', $(this).val());

		if($('#novidade').is(':checked')==false)  $.cookies.set('c_novidade_order', null);
		else $.cookies.set('c_novidade_order', $(this).val());

		currentpage = 0;
		displayProducts();
	 });


	$("#c_l input:radio").live('click',function()
	 {

		// console.log(1);
		if($(this).parent().hasClass("color_check"))
		{
			if($('#category_'+$(this).val()).is(':checked')==false)  $.cookies.set('c_color_order', null);
			else $.cookies.set('c_color_order', $(this).val());
		}else{

			if($('#category_'+$(this).val()).is(':checked')==false)  $.cookies.set('c_category_order'+$(this).val(), null);
			else $.cookies.set('c_category_order'+$(this).val(), $(this).val());
		}
		currentpage = 0;
		displayProducts();
	 });

	 $("#price_list input:checkbox").live('click',function()
	 {
		if($('#preco_'+$(this).val()).is(':checked')==false)  $.cookies.set('c_preco_order'+$(this).val(), null);
		else $.cookies.set('c_preco_order'+$(this).val(), $(this).val());

		currentpage = 0;
		displayProducts();
	 });


	 $("#f_l #fam_level1").live('change',function()
	 {
		currentpage = 0;
		fam_id1 = $("#f_l #fam_level1").val();
		$.cookies.set('c_fam_id1_order', fam_id1);
		displayProducts();
	 });

	 $("#search_box").live('keyup',function()
	 {
		clearTimeout(seach_sel);
		seach_sel = window.setTimeout(function(){currentpage = 0; displayProducts(); clearTimeout(seach_sel);},500);
	 });
	fam_level(1);/* fam_level(2); */

	$(".chzn-select").chosen({disable_search_threshold: 99,  no_results_text: "Não foram encontrados resultados!", width: "107px", enable_new_filter:false});
	$(".chzn-select2").chosen({disable_search_threshold: 99,  no_results_text: "Não foram encontrados resultados!", width: "120px", enable_new_filter:false});
	$(".chzn-select3").chosen({disable_search_threshold: 8,  no_results_text: "Não foram encontrados resultados!", enable_new_filter:false});
});



function fam_level(nivel,id_forcado)
{
	var nivel_seguinte = Number(nivel+1);

	$('#fam_level'+nivel).live('change', function(event)
	{
		var id = $(this).val();

		if(id > 0)
		{
			var dataString = 'id_org='+ id + '&nivel=' + nivel_seguinte + '&marca_id=' + marca_ativa;
			$.ajax
			({
				type: "POST",
				url: SITE_HTTP_URL + "do_blocos_data.php",
				data: dataString,
				cache: false,
				success: function(html)
				{
					if(html!='empty')
					{
						$('#fam_level'+nivel_seguinte).html(html);	getFiltros_temp(id);
					}
					else	getFiltros_temp(id);
				}
			});
		}

		event.preventDefault();
	});
}
function fam_level_forcado(nivel,id_forcado)
{
	var nivel_seguinte = Number(nivel+1);

		var id = id_forcado;

		if(id > 0)
		{
			var dataString = 'id_org='+ id + '&nivel=' + nivel_seguinte + '&marca_id=' + marca_ativa;
			$.ajax
			({
				type: "POST",
				url: SITE_HTTP_URL + "do_blocos_data.php",
				data: dataString,
				cache: false,
				success: function(html)
				{
					if(html!='empty')
					{
						$('#fam_level'+nivel_seguinte).html(html);	getFiltros_temp(id,nivel);
					}
					else	getFiltros_temp(id,nivel);


					if(nivel==2)
					{
						$("option[value='"+fam_id2+"']").attr('selected', 'selected');
					}
				}
			});
		}


}

function displayFilters(filters)
{
	var counter = 0;
	$('li[del="1"]').remove();
	var f_reload2 = new Array();
	var carac_s = $("#c_l :checked").length;
	var zindex = 48;
	$("#c_l :checked").each(function()
	{
		ck_sel_val = this.value;
		f_reload2[counter] = ck_sel_val;
		$('.crumbs').append('<li del="1"><a href="#" onclick="triggerclick(\''+$(this).attr('id')+'\')" style="z-index:'+zindex+'"><img src="'+SITE_HTTP_URL+'layout/'+PROJETO+'/images/close2.png" border="0" alt="Remover" style="margin-right:3px;" />'+$(this).attr('valor')+'</a></li>');
		zindex--;
		counter++;
	});

	$("#c_l input").each(function()
	{
		ck_sel_id = this.id;
		ck_sel_value = $(this).attr('value');
		var counter2 = 0;

		if($('#cleanf').length == 0 && $('label.c_on_p').length > 0)
		{
			$("[id^='caract_']").css("display", "none");
			$(".esconde").css("display", "none");
			$("[id^='off_caract_']").css("display", "block");

			// $("#c_l").append('<label id="cleanf" class="label_check_p" onclick="clean_filters();return false;" style="cursor: pointer;" for="category"><div id="f_left"><span>VER TODAS</span></div> <div id="f_right"><input valor="" id="category" value="" name="3" data="" type="radio"></div><div class="c"></div></label><div id=\'clearbot\' style=\'clear:both\'></div>');


			$("#c_l > li > ul").prepend("<a href='#' id='cleanf' onclick='clean_filters();return false;' class='t' style='text-align:right;padding:5px 0;font-size:14px;font-weight:bold;background:none'><i class=\"fa fa-times\" aria-hidden=\"true\"></i> Limpar Pesquisa&nbsp;</a><div id='clearbot' style='clear:both'></div>");
		}
		else if($('label.c_on_p').length == 0){ $( "#cleanf" ).remove();$( "#clearbot" ).remove(); }

		if($("#"+ck_sel_id).parents("li").attr('noremove')=='yes'){}else{ $("#"+ck_sel_id).parents("li").attr('style','display:block');} $("#"+ck_sel_id).attr("disabled", false);$("#"+ck_sel_id).parents("label").css("cursor", "pointer");

		f_reload2[counter] = ck_sel_value;
		if(existeprodfilter(ck_sel_value, f_reload2)==0)
		{
			if($("#"+ck_sel_id).parents("li").attr('noremove')=='yes'){}else{$("#"+ck_sel_id).parents("li").attr('style','display:none');}
		}
	});


	// VARIFICAR SE O BLOCO CONTEM CHECKBOX DISPONIVEIS, SE TIVEREM TODAS ESCONDIDADS O BLOCK DESAPARECE
	var c = 0;
	$('.f_ativos3').each(function()
	{
		c++;
		nm = $(this).find("#f_ativos3").html();
		var all_checkboxes = $(' li', this);
		var count = 0;
		all_checkboxes.each(function(){ if($(this).attr('style')=='display:block' || $(this).is(":visible")){ count++;}});
		if (count > 0)
		{
			$(this).attr('style','display:block');
			if(count > 5){   $('.esconde', this).css("display", "block"); }
		}
		else
		{
			$(this).attr('style','display:none');
		}
	});
}

function existeprodfilter(filter, selected)
{
	var n_prod = 0;
	var c_sel_in = new Array();
	$.each(products, function(i, object)
	{
		if (typeof object != 'undefined')
		{
			var counter_prod = 0;
			c_sel = object.c;
			$.each(c_sel, function(x, val)
			{
				for(var x=0; x < selected.length; x++){	if(selected[x]==val['c_id']) counter_prod++;}
			});
			if(counter_prod == selected.length) n_prod++;
		}
	});

	return n_prod;
}

function triggerclick(clicked)
{
	$("#"+clicked).trigger("click");
	setupLabel();
	return false;
}
function clean_filters()
{
	$('#c_l input[type=radio]').each(function() { this.checked = false;setupLabel(); prpl = this.value.split('_'); mostra_esconde('caract_'+prpl[1], 'off_caract_'+prpl[1]); });

	var myCookies = getCookies();
	for(var a=0; a<myCookies.length;a++)
	{
		if(substr_count_v(myCookies[a], 'c_product_sort')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_product_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_marca_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_destaque_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_novidade_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_oportunidade_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_outlet_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_promocao_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_category_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_color_order')) $.cookies.set(myCookies[a], null);
		else if(substr_count_v(myCookies[a], 'c_preco_order')) $.cookies.set(myCookies[a], null);
	}

	currentpage = 0;

	setupLabel(); setProducts(); displayProducts();

	return false;
}

/* function displayFilters(filters)
{
	var counter = 0;
	var f_reload = new Array();
	var carac_s = $("#c_l :checked").length;

	$("#c_l :checked").each(function()
	{
		ck_sel_val = this.value;
		f_reload[counter] = ck_sel_val;
		counter++;
	});
	$("#c_l input").each(function()
	{
		ck_sel_id = this.id;
		ck_sel_data = $(this).attr('data');
		var counter2 = 0;
		for(var x=0; x < f_reload.length; x++)
		{

			if (ck_sel_data.indexOf(f_reload[x]+';') > -1)  counter2++;
			else
			{
				if(!$("#"+ck_sel_id).parent().hasClass("color_check")){
					if($("#"+ck_sel_id).is(':checked')==false)
					{
						$("#"+ck_sel_id).parents("label").fadeTo(0, 0.2);
						$("#"+ck_sel_id).parents("label").css("cursor", "default");
						$("#"+ck_sel_id).attr("disabled", true);
					}
				}

			}
		}
		if(counter2==f_reload.length){ $("#"+ck_sel_id).parents("label").fadeTo(0, 1); $("#"+ck_sel_id).attr("disabled", false);$("#"+ck_sel_id).parents("label").css("cursor", "pointer");}
		else if(carac_s==0){ $("#"+ck_sel_id).parents("label").fadeTo(0, 1);$("#"+ck_sel_id).attr("disabled", false);$("#"+ck_sel_id).parents("label").css("cursor", "pointer");}
	});
} */
function getFiltros_temp(id,nivel_seguinte,nivel)
{
	$('#muda_c').html('<div class="titulo_prods" style="font-size:11px;width:100px;border:#000 0px solid"><img src="'+SITE_HTTP_URL+'layout/loading__.gif" width="20" align="absmiddle" /> Loading...<\/div>');
	var dataString = 'id_org=' + id + '&filtro_marcas=' + marca_ativa;

	$.ajax
	({
		type: "POST",
		url: SITE_HTTP_URL + "do_blocos_data2.php",
		data: dataString,
		cache: false,
		success: function(html)
		{
			$('#muda_c').html(html);
			if(nivel_seguinte)
			{

				// Caracteristicas
				$("#c_l input").each(function()
				{
					if($(this).parent().hasClass("color_check")){
						var $c_color_order = $.cookies.get('c_color_order');
						if ($c_color_order){	$('#category_'+$c_color_order).attr("checked", 'checked'); setupLabel(); displayFilters();setProducts();	displayProducts();}
					}else{
						var $c_category_order = $.cookies.get('c_category_order'+$(this).val());
						if ($c_category_order){	$('#category_'+$c_category_order).attr("checked", 'checked'); setupLabel(); displayFilters();setProducts();	displayProducts();}
					}
				});
			}

		}
	});
}

function mostra(p,v)
{
	var e1 = document.getElementById(p+v);
	e1.style.display="none";
	var e2 = document.getElementById("off_"+p+v);
	e2.style.display="block";
}

function mostra_esconde(a,b)
{
	var e1 = document.getElementById(a);
	if(e1) e1.style.display="block";

	var e2 = document.getElementById(b);
	if(e2) e2.style.display="none";
}

$(window).load(function(){ $('html, body').animate({scrollTop: '0px'}, 0); });

function mudaRange(catid,value)
{
	for(var i = 0; i < $("input[id^='category_"+catid+"_']").length; i++)
	{
		var input =  $("input[id^='category_"+catid+"_']").eq(i);
		$('#category_'+input.val()).removeAttr("checked");
		if(value!=0)
		{
			if(input.attr("valor") == value) $('#category_'+input.val()).attr("checked", 'checked');
		}
	}
	setupLabel(); displayFilters();	setProducts();
	$('html, body').animate({scrollTop: '0px'}, 0);
}

function mudaVista(value)
{
	view_type = value;
	if(value==0)
	{
		$("#icon_view").removeClass();
		$("#icon_view").addClass("icon_view view_activo");
		$("#list_view").removeClass();
		$("#list_view").addClass("list_view");
		$("#img_icons_view").hide();
		$("#img_icons_view_activo").show();
		$("#img_list_view").show();
		$("#img_list_view_activo").hide();
	}
	else if(value==1)
	{
		$("#icon_view").removeClass();
		$("#icon_view").addClass("icon_view");
		$("#list_view").removeClass();
		$("#list_view").addClass("list_view view_activo");
		$("#img_icons_view").show();
		$("#img_icons_view_activo").hide();
		$("#img_list_view").hide();
		$("#img_list_view_activo").show();
	}
	setupLabel(); displayFilters();	setProducts();
}

/* function meteComparador(value)
{

	var cont = 0;
	for(var i = 0; i < $("input[id^='comparador_']").length; i++){
		if($("input[id^='comparador_']").eq(i).is(":checked"))
			cont++;
	}

	if(cont>4 || (comparar0 != 0 && comparar1 != 0 && comparar2 != 0 && comparar3 != 0)){
		var conf=confirm("Já tem 4 produtos no comparador.\nTem de retirar 1 produto para poder inserir este. Deseja ir à pagina do Comparador?");
		if(conf){
			window.location = "/comparador.php";
		}
		$("#comparador_"+value).attr("checked",false);
	}else{
		var accao = "rem";
		if($("#comparador_"+value).is(":checked")){
			accao = "add";
		}

		var request = $.ajax({
			type: "POST",
			url: "/comparador_ajax.php",
			dataType: "html",
			data: {
				id_comparador: value,
				ac_comparador: accao

			}
		 });
		if(accao == "add"){
			request.done(function(msg) {
				$("#comparador_botao_"+value).attr("onclick","window.location.href = '/comparador.php'");
				$("#comparador_botao_"+value).css("background","#008200");
				$("#comparador_botao_"+value).css("color","#FFF");
				$("#comparador_botao_"+value).css("cursor","pointer");
			});
		}else{
			request.done(function(msg) {
				$("#comparador_botao_"+value).attr("onclick","");
				$("#comparador_botao_"+value).css("background","#FFF");
				$("#comparador_botao_"+value).css("color","#898989");
				$("#comparador_botao_"+value).css("cursor","auto");
			});
		}

		request.fail(function(jqXHR, textStatus) {alert("Não conseguiu inserir o produto no comparador.\nTente de novo")});
	}
} */

function setupLabel()
{
	if ($('.label_check div input').length)
	{
		$('.label_check').each(function(){$(this).removeClass('c_on');});
		$('.label_check input:checked').each(function(){$(this).parent('div').parent('label').addClass('c_on');});
	};
	if ($('.label_check_rel div input').length)
	{
		$('.label_check_rel').each(function(){$(this).removeClass('c_on_novidade');});
		$('.label_check_rel input:checked').each(function(){$(this).parent('div').parent('label').addClass('c_on_novidade');});
	};
	if ($('.label_check_p div input').length)
	{
		$('.label_check_p').each(function(){$(this).removeClass('c_on_p');});
		$('.label_check_p input:checked').each(function(){$(this).parent('div').parent('label').addClass('c_on_p');});
	};
	if ($('.color_check input').length)
	{
		$('.color_check').each(function(){$(this).removeClass('color_on');});
		$('.color_check input:checked').each(function(){$(this).parent('label').addClass('color_on');});
	};
};

function isNumber(n){return !isNaN(parseFloat(n)) && isFinite(n);}
function getDescontoAplicado(precoantigo,preconovo){return parseInt((100-(preconovo/precoantigo)*100));}