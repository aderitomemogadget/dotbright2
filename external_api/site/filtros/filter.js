var maxColumn=4;    // max cells in a row
var slider;
var range=3;    // range of num links to show
var rowsPerPage=0;
var currentpage=0;
var maxPrice=0;
var minPrice=0;
var sortBy='';
var sortByTitle='title';
var sortByPrice='price';
var sortOrder=0;
var marcas=0;
var marcas_sel=0;
var seach_sel=0;
var search_sel=0;
var fam_id1=0;
var fam_id2=0;
var fam_id3=0;
var fam_id4=0;
var fam_id5=0;
var fam_id6=0;
var fam_id7=0;
var fam_id8=0;
var fam_id9=0;
var fam_id10=0;
var minPrice2;
var maxPrice2;
var prod_info;

// Add ECMA262-5 Array methods if not supported natively
//
if (!('indexOf' in Array.prototype))
{
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
function truncateOnWord(str, limit)
{
    var trimmable='\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u180E\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u2028\u2029\u3000\uFEFF';
    var reg=new RegExp('(?=[' + trimmable + '])');
    var words=str.split(reg);
    var count=0;
    return words.filter(function(word) {
        count += word.length;
        return count <= limit;
    }).join('');
}

function trim(str, charlist)
{
  var whitespace,l=0,i=0;
  str += '';
  if (!charlist)
  {
    // default list
    whitespace =' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
  }
  else
  {
    // preg_quote custom list
    charlist += '';
    whitespace=charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  }
  l=str.length;
  for (i=0; i < l; i++) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str=str.substring(i);
      break;
    }
  }

  l=str.length;
  for (i=l - 1; i >= 0; i--) {
    if (whitespace.indexOf(str.charAt(i)) === -1) {
      str=str.substring(0, i + 1);
      break;
    }
  }

  return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
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
function numberformat(number, decimals, dec_point, thousands_sep)
{
	var n=number, prec=decimals;
	var toFixedFix=function (n,prec)
	{
		var k=Math.pow(10,prec);
		return (Math.round(n*k)/k).toString();
	};
	n=!isFinite(+n) ? 0 : +n;
	prec=!isFinite(+prec) ? 0 : Math.abs(prec);
	var sep=(typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
	var dec=(typeof dec_point === 'undefined') ? '.' : dec_point;
	var s=(prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0)=0;
	var abs=toFixedFix(Math.abs(n), prec);
	var _, i;
	if (abs >= 1000)
	{
		_=abs.split(/\D/);
		i=_[0].length % 3 || 3;

		_[0]=s.slice(0,i + (n < 0)) +
			  _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
		s=_.join(dec);
	} else   s=s.replace('.', dec);
	var decPos=s.indexOf(dec);
	if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
	else if (prec >= 1 && decPos === -1) s += dec+new Array(prec).join(0)+'0';
	return s+' &euro;';
}
function include(arr,obj)
{
	if(arr) return (arr.indexOf(obj) != -1);
}
function inArray(needle, haystack)
{
	var length=haystack.length;
	for(var i=0; i < length; i++)
	{
		if(haystack[i] == needle) return true;
	}
	return false;
}

var productsf;
var ignorar=0;
var filtros_valor_nome=new Array();
var filtros_cat_id=new Array();
var filtros_valor_id=new Array();
var n_categorias=0;
var html;

var filtros_m_id=new Array();
var filtros_m_nome=new Array();
var n_marcas=0;

function constroi_filtros()
{
	if(typeof products == 'undefined') {products=[];}

	//Constroi filtros laterais:
	$.each(products, function(i, object)
	{
			object.id=object.a1;
			object.ord=object.a2;
			object.ref=object.a3;
			object.price=object.a4;
			object.price_riscado=object.a5;
			object.pvp=object.a6;
			object.stock=object.a7;
			object.stock_txt=object.a8;
			object.image=object.a9;
			object.title=object.a10;
			object.subtitle=object.a11;
			object.m_id=object.a17;
			object.m_nome=object.a32;
			object.c=object.a18;
			object.f=object.a19;
			object.u_i=object.a20;
			object.u_c=object.a21;
			object.u_co=object.a22;
			object.fl=object.a30;
			object.Sync=object.a31;
			object.isFam=object.a34;
			c_sel=object.c;

			var id_marca;
		  id_marca=object.m_id;


	    if(id_marca!=undefined)
	    {
				if(!inArray(id_marca, filtros_m_id))
				{
					filtros_m_id[n_marcas]=id_marca;
					filtros_m_nome[id_marca]=object.m_nome;

					n_marcas++;
				}
			}

			$.each(c_sel, function(x, val)
			{
				var codigos=val['c_id'];
				var splt;
				var id_categoria;
		    if(codigos!=undefined)splt=codigos.split('_');
		    if(splt!=undefined)id_categoria=splt[0];

		    if(id_categoria!=undefined)
		    {
			    var z=0;
					if(!inArray(id_categoria, filtros_cat_id))
					{
						filtros_cat_id[n_categorias]=id_categoria;
						filtros_valor_nome[id_categoria]=Array();
						filtros_valor_id[id_categoria]=Array();
						n_categorias++;
					}
					else
					{
						z=filtros_valor_nome[id_categoria].length;
					}
					if(!inArray(codigos, filtros_valor_id[id_categoria]))
					{
						filtros_valor_nome[id_categoria][z]=val['cat'];
						filtros_valor_id[id_categoria][z]=codigos;
					}
				}
			});
	});

	/*
	var j=0;
	if(n_marcas > 0 )
	{
			html='<h3>Fabricante</h3>';
			html += '<ul id="m_l">';
			while(j<n_marcas)
			{
				id_marca=filtros_m_id[j];
				m_nome = filtros_m_nome[id_marca];

				html += '<li class="active">';
				html += '<h3 style="margin:0">';
				html += '<label class="label_check_p" for="marca_'+id_marca+'">';
				html += '<div id="f_left"><span>'+m_nome+'</span></div>';
				html += '<div id="f_right"><input id="marca_'+id_marca+'" value="'+id_marca+'" type="checkbox"></div>';
				html += '<div class="c"></div></label></h3></li>';
		  	j++;
			}
			$('#filtro-marcas').html(html);
	}*/

	var j=0;
	while(j<n_categorias)
	{
				html='';

				var j2=0;
				var id_categoria = filtros_cat_id[j];

				if(id_categoria != undefined)
				{
					var t=filtros_valor_nome[id_categoria].length;
					while(j2<t)
					{
						html += '<li class="filtros-sel tem" style="display:block">';
						html += '	<div id="rel_category_'+filtros_valor_id[id_categoria][j2]+'" class="t" title="'+filtros_valor_nome[id_categoria][j2]+'">';
						html += '		<label class="label_check_p" for="category_'+filtros_valor_id[id_categoria][j2]+'">';
						html += '			<div id="f_left"><span>'+filtros_valor_nome[id_categoria][j2]+'</span></div>';
						html += '			<div id="f_right">';
						html += '				<input valor="'+filtros_valor_nome[id_categoria][j2]+'" id="category_'+filtros_valor_id[id_categoria][j2]+'" value="'+filtros_valor_id[id_categoria][j2]+'" name="'+id_categoria+'" type="checkbox">';
						html += '			</div>';
						html += '			<div class="c"></div>';
						html += '		</label>';
						html += '	</div>';
						html += '</li>';

						j2++;
						$('#v'+id_categoria).html(html);
					}
				}
		j++;
	}


}
function setProducts()
{
	if(typeof products == 'undefined') {products=[];}
	// During a partial deployment the browser may still have the older module.
	// Only use the DB-backed API when both methods are available.
	var catalogFilters = window.DotbrightCatalogFilters;
	if (!catalogFilters || typeof catalogFilters.prepare !== 'function' || typeof catalogFilters.apply !== 'function') catalogFilters = null;
	if (catalogFilters) catalogFilters.prepare(products);

	sortBy=sortBy ? sortBy : $('#product_sort').val() ;
	rowsPerPage=rowsPerPage ? rowsPerPage:$('#f_visualizacao option:selected').val();
	var $c_pagination_order=$.cookies.get('c_pagination_order');
	if ($c_pagination_order){ currentpage=$c_pagination_order;}
	currentpage=currentpage > 0 ? currentpage : 1; // if current page is less than first page...

	var totalLoop=rowsPerPage;
	var loop=0;
	var countCellData=0;
	var offset=0;
	// LIMPAR O CONTEUDO
	$('#product_show').html('&nbsp;');
	var filterdProducts=[];  // ARRAY DOS PRODUTOS
	var filterdProducts2=[];  // ARRAY DOS PRODUTOS
	var key=0;
	var and=0;
	var reparacoes=0;
	rel_sel=$("#relevancia_list :checked").length;
	rel_f_sel=0;
	marcas_sel=$("#m_l :checked").length;
	stock_sel=$('select#f_stock').find(":selected").val();
	cart_sel=$("#c_l :checked").length;
	pric_sel=$("#range").length;

	if($("#search_box").length > 0 && $("#search_box").val().length > 0) search_sel=$("#search_box").val().length; else search_sel=0;


	// TODOS OS PRODUTOS
	if(stock_sel==0 && marcas_sel == 0 && rel_sel==0 && rel_f_sel==0 && cart_sel==0 && pric_sel==0 && search_sel==0 && fam_id1==0 && fam_id2==0 && fam_id3==0 && fam_id4==0 && fam_id5==0 && fam_id6==0 && fam_id7==0 && fam_id8==0 && fam_id9==0 && fam_id10==0) filterdProducts=products;
	if(products.length==0)
	{
		//Se não há filtros para mostrar, escondemos as divs
		$(".catalogo_topo_filtros").hide();
		$(".filtro").hide();
	}

	// PESQUISA
	if(search_sel > 0)
	{
		var mySearch=$("#search_box").val().toLowerCase();
		var arr=mySearch.split(" ");
	}

	$.each(products, function(i, object)
	{
		//Vamos reconverter as variaveis:

		if(object.a0==1)//Versao compacta
		{
			object.id=object.a1;
			object.ord=object.a2;
			object.ref=object.a3;
			object.price=object.a4;
			object.price_riscado=object.a5;
			object.pvp=object.a6;
			object.stock=object.a7;
			//object.stock_txt=object.a8;
			//object.image=object.a9;
			object.title=object.a10;
			//object.subtitle=object.a11;
			object.caminho=object.a12;
			object.promo=object.a13;
			object.promo_data="";
			//object.promo_data=object.a14;
			//object.global_promos=object.a15;
			object.destaque=object.a16;
			object.m_id=object.a17;
			object.c=object.a18;
			object.f=object.a19;
			//object.u_i=object.a20;
			//object.u_c=object.a21;
			//object.u_co=object.a22;
			//object.carrinho=object.a23;
			object.novidade=object.a24;
			//object.novidade_data=object.a25;
			object.novidade_data="";
			//object.outlet=object.a26;
			//object.outlet_data=object.a27;
			object.outlet_data="";
			//object.destaque_data=object.a28;
			object.destaque_data="";
			//object.dodia_data=object.a29;
			object.dodia_data="";
			//object.fl=object.a30;
			//object.produto_n_standard=object.a31;
			//object.c_submissao_ficheiro=object.a32;
			if(object.a34==1)reparacoes=1;
		}

		if(marcas_sel > 0) var marca_ok=0; else var marca_ok=1;
		if(stock_sel > 0) var stock_ok=0; else var stock_ok=1;
		if(rel_sel > 0) var relavancia_ok=0;  else var relavancia_ok=1;
		if(rel_f_sel > 0) var relavancia_f_ok=0;  else var relavancia_f_ok=1;
		if(cart_sel > 0) var caract_ok=0;  else var caract_ok=1;
		if(pric_sel > 0) var precos_ok=0; else var precos_ok=1;
		if(search_sel > 0) var search_ok=0; else var search_ok=1;
		if(fam_id1 > 0) var fam_id1_ok=0; else fam_id1_ok=1;
		if(fam_id2 > 0) var fam_id2_ok=0; else fam_id2_ok=1;
		if(fam_id1 > 0) var fam_ok=0; else fam_ok=1;
		if(fam_id2 > 0) var fam_ok=0; else fam_ok=1;


		// FAMILIAS
		f_sel=object.f;
		//FAM1
		var fam1_sel_in=new Array();	var count=0;	var count2=0;
		if(fam_id1 > 0)
		{
			fam_ok=0;
			$.each(f_sel, function(x, val){	fam1_sel_in[count2]=val['f_id'];	count2++;});
			if(inArray(fam_id1, fam1_sel_in)) fam_ok=1;
		}

		// PESQUISA
		m_search=object.title+" "+object.ref;
		
		if(m_search != undefined)m_search_lower=m_search.toLowerCase();
		if(search_sel > 0)
		{
			var search_ok2=1;
			for( var i=0, len=arr.length; i < len; i++ )
			{
				var mySearch2=arr[i];
				if (m_search_lower.indexOf(mySearch2) > -1)
				{
					if(!include(filterdProducts,object))
					{
						search_ok=1;
					}
				}
				else
				{
					search_ok2=0;
				}
			}
			if(search_ok2 == 0)search_ok=0;//Se por acaso um dos termos nao existir, anula este produto
		}
		// MARCAS
		m_sel=object.m_id;
		$("#m_l :checked").each(function()
		{
			if(this.value ==  m_sel)
			{
				if(!include(filterdProducts,object))
				{
					marca_ok=1;
				}
			}
		});

		st_sel=object.stock;

		// RELEVANCIA HOME PAGE, este filtro foi feito para mostrar produtos ordenados pelo campo Relevancia
		var novidade=object.novidade;
		var promo=object.promo;
		var destaque=object.destaque;
		var oportunidade=object.oportunidade;
		var outlet=object.outlet;

		// CARACTERISTICAS
		c_sel=object.c;
		var c_sel_in=new Array();
		var count=0;
		var count2=0;
		//caract_ok=1;
		$("#c_l :checked").each(function()
		{
			ct_val=this.value;

			$.each(c_sel, function(x, val)
			{
				c_sel_in[count2]=val['c_id'];
				count2++;
			});


			if(inArray(ct_val, c_sel_in)) count++;

			if(count == cart_sel) caract_ok=1;
		});
		// ENTRE PRECOS
		var curentPrice = parseFloat(object.price);
		var priceMinOk = true;
		var priceMaxOk = true;
		var minPrice = parseFloat(minPrice2);
		var maxPrice = parseFloat(maxPrice2);

		if(maxPrice<curentPrice) priceMaxOk = false;
		if(minPrice>curentPrice) priceMinOk = false;

		if( priceMinOk && priceMaxOk )
		{
			if(!include(filterdProducts,object)) precos_ok = 1;
		}

		if( stock_ok == 1 && marca_ok == 1 && relavancia_ok == 1 && relavancia_f_ok == 1 && caract_ok ==1 && precos_ok == 1 && search_ok == 1 && fam_ok==1)
		{
			if(!include(filterdProducts,object))
			{
				filterdProducts[key]=object;
				filterdProducts2[key]=object;
				key++;
			}
		}
	});
	if (catalogFilters) {
		filterdProducts = catalogFilters.apply(filterdProducts, products);
		filterdProducts2 = filterdProducts.slice();
	}
	// get the amount of results
	var totalResults=filterdProducts.length;
	if(totalResults > 0)
	{
		function StockDESC(a,b) { var a2=a['stock'], b2=b['stock']; return parseFloat(a2) < parseFloat(b2) ? 1 : -1;} // DO MAIOR PARA MENOR
		function PrecoDESC(a,b) { var a2=a['price'], b2=b['price']; return parseFloat(a2) < parseFloat(b2) ? 1 : -1;} // DO MAIOR PARA MENOR
		function PrecoASC(a,b) {var a2=a['price'], b2=b['price']; return parseFloat(a2) > parseFloat(b2) ? 1 : -1;} // DO MENOR PARA MAIOR
		function OrdemASC(a,b) {var a2=a['ord'], b2=b['ord']; return parseFloat(a2) > parseFloat(b2) ? 1 : -1;} // DO MENOR PARA MAIOR
		function numberRAND(a,b) { return 0.5 - Math.random();} // RANDOM
		function numberNEW(a,b) {  return b['id'] - a['id'];} // NEW
		function AtoZ(a,b) { var a1=a['title'], b1=b['title']; a1=a1.toLowerCase(); b1=b1.toLowerCase(); return a1 > b1 ? 1 : -1;} // A-Z
		function ZtoA(a,b) { var a1=a['title'], b1=b['title']; a1=a1.toLowerCase(); b1=b1.toLowerCase(); return (a1 > b1) ? -1 : (a1 < b1) ? 1 : 0;} // Z-A

		function mysortfunction(a, b) {
		  var a1=parseInt(a['novidade']);
		  var a2=parseInt(b['novidade']);
		  var b1=parseInt(a['destaque']);
		  var b2=parseInt(b['destaque']);
		  var c1=parseInt(a['promo']);
		  var c2=parseInt(b['promo']);
		  if (a1 < a2) return -1;
		  if (a1 > a2) return 1;
		  if (b1 < b2) return -1;
		  if (b1 > b2) return 1;
		  if (c1 < c2) return -1;
		  if (c1 > c2) return 1;
		  return 0;
		}

		function value(el) {
			var x=el;
			return x === 0 ? Infinity : x;
		}

		if (!sortOrder) {
		    sortOrder = 3;
		}


		//variavel Definida no header.inc.php
		//if(defaultsortOrder == 'undefined'){var defaultsortOrder=-1;}

		/*
		if(defaultsortOrder == 'undefined' || defaultsortOrder == undefined)
		{
			//var defaultsortOrder=3;
			var defaultsortOrder=4;
			if(reparacoes==1)defaultsortOrder=4;
		}
		if(defaultsortOrder>0)sortOrder=defaultsortOrder;
		*/

		if(reparacoes==1)sortOrder = 4;

		if(sortOrder == -1) // Relevancia
		{
			var mySearch_top=$("#search_query_top").val();
			if(mySearch_top != '' && mySearch_top != 'o que procura?'){}
			//else filterdProducts.sort(StockDESC);
		}
		else if(sortOrder == 5) // Pelo que tem mais stock
		{
			filterdProducts.sort(StockDESC);
		}
		else if(sortOrder == 4) // ORDEM
		{
			filterdProducts.sort(OrdemASC);
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


		console.log(sortOrder);

	}
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
		   pagination += '<button type="button" name="'+(currentpage - 1)+'" class="product_button_back" title="Anterior"><i class="fas fa-angle-left"></i>Anterior<\/button>';
		   // show page 1 only if the min page isn`t page 1 (prevent page 1 to show twice)
		   if(currentpage > 3)
			{
				pagination += '<button type="button" name="1" class="product_button" title="1" >1<\/button>';
			}
		   if(minPage > 1)
		   {
				if(HOME=='0' && produto_ativo=='') pagination += '<button type="button" name="1" class="product_button" title="1" >...<\/button>';
		   }
		}


		// loop to show links to range of pages around current page
		if(HOME=='0' && produto_ativo=='')
		{
			//pagination += '<button type="button" name="1" class="product_button" title="1" >1<\/button>';
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
			if(currentpage != (totalpages-1))
			{
				if(HOME=='0' && produto_ativo=='')
				{
					pagination += '<button type="button" name="'+(totalpages-1)+'" class="product_button" title="'+(totalpages-1)+'" >...<\/button>';
					pagination += '<button type="button" name="'+totalpages+'" class="product_button" title="'+totalpages+'" >'+totalpages+'<\/button>';
				}
			}

			 pagination += '<button type="button" name="'+nextPage+'" class="product_button_next" title="Próxima">Próxima<i class="fas fa-angle-right"></i><\/button>';
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

	var count_products = '';
	var range_products = '';

	if(filterdProducts.length > 0)
	{
		if(filterdProducts.length >= rowsPerPage ) range_products = ((((currentpage*rowsPerPage)-rowsPerPage)+1)+' - '+(currentpage*rowsPerPage));
		else range_products = ((((currentpage*filterdProducts.length)-filterdProducts.length)+1)+' - '+(currentpage*filterdProducts.length));

		if(filterdProducts.length == 1) count_products = '<div class="">Mostrar <span>'+range_products+'</span> de <span>'+filterdProducts.length+'</span> item</div>';
		else count_products = 'Mostrar <span>'+range_products+'</span> de <span>'+filterdProducts.length+'</span> itens';
		$('.info-extra-paginacao').html(count_products);
	}
	var offsetEnd=parseFloat(rowsPerPage) + parseFloat(offset);
	/****** end build pagination links ******/
	// build cells content

	//var cell='<ul>';
	var cell='';
	// get the keys is in the display pagination range
	var pageProducts=filterdProducts.slice(offset,offsetEnd);
	// loop over the query list
	// if(!SESSAO || REVEN) bl='bloco'; else  bl='bloco_3';

	var vista='';
	view_type = readCookie('vista');

	if(view_type == 1) vista='_lista';
	
	var ax=maxColumn;
	var ax2=0;
	var st='';
	var todayDate=new Date();
	var date_ct=new Date();
	var day_ct=date_ct.getDate();
	var monthIndex_ct=date_ct.getMonth();
	var year_ct=date_ct.getFullYear();
	var todayDate=new Date(year_ct, (monthIndex_ct), day_ct);


	var lista_prods_ids='';
	var count=0;
	$.each(pageProducts, function(i, object)
	{
		//console.log(object.isFam);

		//Vamos ignorar os IDs de familias, apenas queremos ler artigos
		if(object.isFam=='1')
		{
		}
		else
		{
			if(count==0)lista_prods_ids=object.id;
			else lista_prods_ids=lista_prods_ids+';'+object.id;
		}
		count++;
	});
	
	$.ajax({
		type: "GET",
		url: SITE_HTTP_AJAX_URL + "ajax_prod_info.php",
		data: "filterdProducts="+lista_prods_ids,
		cache: false,
		async: false,
		success: function(html)
		{
			prod_info=html;
		}
	});

	
	$.each(pageProducts, function(i, object)
	{
		$.each(prod_info, function(i2, object2)
		{
			//Apenas se forem produtos, se for familia de produtos não deve mudar:
			if(String(object.a1)==String(object2.a1))
			{
				object.ord=object2.a2;
				object.ref=object2.a3;
				object.price=object2.a4;
				object.price_riscado=object2.a5;
				object.pvp=object2.a6;
				object.stock=object2.a7;
				object.stock_txt=object2.a8;
				object.image=object2.a9;
				object.title=object2.a10;
				object.subtitle=object2.a11;
				object.caminho=object2.a12;
				object.promo=object2.a13;
				object.global_promos=object2.a15;
				object.destaque=object2.a16;
				object.u_i=object2.a20;
				object.u_c=object2.a21;
				object.u_co=object2.a22;
				object.carrinho=object2.a23;
				object.novidade=object2.a24;
				object.outlet=object2.a26;
				object.fl=object2.a30;
				object.produto_n_standard=object2.a31;
				
				object.outlet_box=object2.a33;
			}
		});

		countCellData++; // flug to know if there is content
		cell += bloco_produto(vista, object);
		totalLoop--;
		loop++;
		ax2++;
	});

	//cell += '<\/ul>';

	if(countCellData > 0 && pageProducts.length > 0) // IF EXIST CONTENT
	{
		$('#product_show').html(cell);
		// site.js registers the delegated cart handler once, including new cards.
		displayFilters2(filterdProducts2);
	}
	else
	{
		$('#product_show').html('<center>Não foram encontrados produtos!</center><br>');
	}


	setPagination();
	// show_precos();
	show_proms();
	markrow();
}
function show_img(id)
{
	if(id > 0)
	{
		var dataString='prod_id='+ id;
		$.ajax({
			type: "GET",
			url: SITE_HTTP_AJAX_URL + "ajax_img.php",
			data: dataString,
			cache: false,
			async: true,
			success: function(html)
			{
				if(html!='') $('#img_'+id).attr('src', html);
			}
		});
	}
}
function show_designacao(id)
{
	if(id > 0)
	{
		var dataString='prod_id='+ id;
		$.ajax({
			type: "GET",
			url: SITE_HTTP_AJAX_URL + "ajax_desi.php",
			data: dataString,
			cache: false,
			async: true,
			success: function(html)
			{
				//console.log(html);
				if(html!='') $('#des_'+id).html(html);
			}
		});
	}
}
function show_preco(id)
{
	if(id > 0)
	{
		var dataString='prod_id='+ id;
		$.ajax({
			type: "GET",
			url: SITE_HTTP_AJAX_URL + "ajax_preco.php",
			data: dataString,
			cache: false,
			async: true,
			success: function(html)
			{
				if(html!='') $('#precos_'+id).html(html);
			}
		});
	}
}
function markrow()
{
    var topa;
	$('.catprod_wrap .item').each(function(i){

		$(this).removeClass('removeleftborder');

	});

	var topa;
	$('.catprod_wrap .item').each(function(i){
		var thistop=$(this).offset().left;
		if (i==0){
			topa=thistop;
		}
		if (topa==thistop){
			$(this).addClass('removeleftborder');
		}
	});

}


$(window).on('resize', function(){ markrow(); });
$(window).on('scroll', function(){ markrow(); });
function strtotime (text, now) {
  //  discuss at: http://locutus.io/php/strtotime/
  // original by: Caio Ariede (http://caioariede.com)
  // improved by: Kevin van Zonneveld (http://kvz.io)
  // improved by: Caio Ariede (http://caioariede.com)
  // improved by: A. Matías Quezada (http://amatiasq.com)
  // improved by: preuter
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Mirko Faber
  //    input by: David
  // bugfixed by: Wagner B. Soares
  // bugfixed by: Artur Tchernychev
  // bugfixed by: Stephan Bösch-Plepelits (http://github.com/plepe)
  //      note 1: Examples all have a fixed timestamp to prevent
  //      note 1: tests to fail because of variable time(zones)
  //   example 1: strtotime('+1 day', 1129633200)
  //   returns 1: 1129719600
  //   example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200)
  //   returns 2: 1130425202
  //   example 3: strtotime('last month', 1129633200)
  //   returns 3: 1127041200
  //   example 4: strtotime('2009-05-04 08:30:00 GMT')
  //   returns 4: 1241425800
  //   example 5: strtotime('2009-05-04 08:30:00+00')
  //   returns 5: 1241425800
  //   example 6: strtotime('2009-05-04 08:30:00+02:00')
  //   returns 6: 1241418600
  //   example 7: strtotime('2009-05-04T08:30:00Z')
  //   returns 7: 1241425800
  var parsed
  var match
  var today
  var year
  var date
  var days
  var ranges
  var len
  var times
  var regex
  var i
  var fail=false
  if (!text) {
    return fail
  }
  // Unecessary spaces
  text=text.replace(/^\s+|\s+$/g, '')
    .replace(/\s{2,}/g, ' ')
    .replace(/[\t\r\n]/g, '')
    .toLowerCase()
  // in contrast to php, js Date.parse function interprets:
  // dates given as yyyy-mm-dd as in timezone: UTC,
  // dates with "." or "-" as MDY instead of DMY
  // dates with two-digit years differently
  // etc...etc...
  // ...therefore we manually parse lots of common date formats
  var pattern=new RegExp([
    '^(\\d{1,4})',
    '([\\-\\.\\/:])',
    '(\\d{1,2})',
    '([\\-\\.\\/:])',
    '(\\d{1,4})',
    '(?:\\s(\\d{1,2}):(\\d{2})?:?(\\d{2})?)?',
    '(?:\\s([A-Z]+)?)?$'
  ].join(''))
  match=text.match(pattern)
  if (match && match[2] === match[4]) {
    if (match[1] > 1901) {
      switch (match[2]) {
        case '-':
          // YYYY-M-D
          if (match[3] > 12 || match[5] > 31) {
            return fail
          }
          return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
        case '.':
          // YYYY.M.D is not parsed by strtotime()
          return fail
        case '/':
          // YYYY/M/D
          if (match[3] > 12 || match[5] > 31) {
            return fail
          }
          return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
      }
    } else if (match[5] > 1901) {
      switch (match[2]) {
        case '-':
          // D-M-YYYY
          if (match[3] > 12 || match[1] > 31) {
            return fail
          }
          return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
        case '.':
          // D.M.YYYY
          if (match[3] > 12 || match[1] > 31) {
            return fail
          }
          return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
        case '/':
          // M/D/YYYY
          if (match[1] > 12 || match[3] > 31) {
            return fail
          }
          return new Date(match[5], parseInt(match[1], 10) - 1, match[3],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
      }
    } else {
      switch (match[2]) {
        case '-':
          // YY-M-D
          if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
            return fail
          }
          year=match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1]
          return new Date(year, parseInt(match[3], 10) - 1, match[5],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
        case '.':
          // D.M.YY or H.MM.SS
          if (match[5] >= 70) {
            // D.M.YY
            if (match[3] > 12 || match[1] > 31) {
              return fail
            }
            return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
          }
          if (match[5] < 60 && !match[6]) {
            // H.MM.SS
            if (match[1] > 23 || match[3] > 59) {
              return fail
            }
            today=new Date()
            return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
            match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000
          }
          // invalid format, cannot be parsed
          return fail
        case '/':
          // M/D/YY
          if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
            return fail
          }
          year=match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5]
          return new Date(year, parseInt(match[1], 10) - 1, match[3],
          match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000
        case ':':
          // HH:MM:SS
          if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
            return fail
          }
          today=new Date()
          return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
          match[1] || 0, match[3] || 0, match[5] || 0) / 1000
      }
    }
  }
  // other formats and "now" should be parsed by Date.parse()
  if (text === 'now') {
    return now === null || isNaN(now)
      ? new Date().getTime() / 1000 | 0
      : now | 0
  }
  if (!isNaN(parsed=Date.parse(text))) {
    return parsed / 1000 | 0
  }
  // Browsers !== Chrome have problems parsing ISO 8601 date strings, as they do
  // not accept lower case characters, space, or shortened time zones.
  // Therefore, fix these problems and try again.
  // Examples:
  //   2015-04-15 20:33:59+02
  //   2015-04-15 20:33:59z
  //   2015-04-15t20:33:59+02:00
  pattern=new RegExp([
    '^([0-9]{4}-[0-9]{2}-[0-9]{2})',
    '[ t]',
    '([0-9]{2}:[0-9]{2}:[0-9]{2}(\\.[0-9]+)?)',
    '([\\+-][0-9]{2}(:[0-9]{2})?|z)'
  ].join(''))
  match=text.match(pattern)
  if (match) {
    // @todo: time zone information
    if (match[4] === 'z') {
      match[4]='Z'
    } else if (match[4].match(/^([+-][0-9]{2})$/)) {
      match[4]=match[4] + ':00'
    }
    if (!isNaN(parsed=Date.parse(match[1] + 'T' + match[2] + match[4]))) {
      return parsed / 1000 | 0
    }
  }
  date=now ? new Date(now * 1000) : new Date()
  days={
    'sun': 0,
    'mon': 1,
    'tue': 2,
    'wed': 3,
    'thu': 4,
    'fri': 5,
    'sat': 6
  }
  ranges={
    'yea': 'FullYear',
    'mon': 'Month',
    'day': 'Date',
    'hou': 'Hours',
    'min': 'Minutes',
    'sec': 'Seconds'
  }
  function lastNext (type, range, modifier) {
    var diff
    var day=days[range]
    if (typeof day !== 'undefined') {
      diff=day - date.getDay()
      if (diff === 0) {
        diff=7 * modifier
      } else if (diff > 0 && type === 'last') {
        diff -= 7
      } else if (diff < 0 && type === 'next') {
        diff += 7
      }
      date.setDate(date.getDate() + diff)
    }
  }
  function process (val) {
    // @todo: Reconcile this with regex using \s, taking into account
    // browser issues with split and regexes
    var splt=val.split(' ')
    var type=splt[0]
    var range=splt[1].substring(0, 3)
    var typeIsNumber=/\d+/.test(type)
    var ago=splt[2] === 'ago'
    var num=(type === 'last' ? -1 : 1) * (ago ? -1 : 1)
    if (typeIsNumber) {
      num *= parseInt(type, 10)
    }
    if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
      return date['set' + ranges[range]](date['get' + ranges[range]]() + num)
    }
    if (range === 'wee') {
      return date.setDate(date.getDate() + (num * 7))
    }
    if (type === 'next' || type === 'last') {
      lastNext(type, range, num)
    } else if (!typeIsNumber) {
      return false
    }
    return true
  }
  times='(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
    '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
    '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)'
  regex='([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?'
  match=text.match(new RegExp(regex, 'gi'))
  if (!match) {
    return fail
  }
  for (i=0, len=match.length; i < len; i++) {
    if (!process(match[i])) {
      return fail
    }
  }
  return (date.getTime() / 1000)
}
function calculatelisinrow()
{

	/* SEMPRE QUE A FUNCAO FOR EXECUTADA VAMOS REMOVER OS OBJ ANTERIORMENTE CRIADOS */
	$('.bigobjc').remove(); $('.bigobj').remove();

	/* VAMOS SABER O WIDTH TOTAL COM PADDINGS E MARGEM DO THUMB */
	width_obj= $('.prd_d').outerWidth();


	/* VAMOS VERRICAR QUANTOS OBJETOS EXISTEM EM CADA LINHA */
	var lisInRow=0;
	$('div.catprod_wrap  div.prd_d').each(function()
	{
		if($(this).prev().length > 0)
		{
			if($(this).position().top != $(this).prev().position().top) return false;
			lisInRow++;

		}
		else
		{
			lisInRow++;
		}
	});

	/* POR CADA lisInRow OBTIDO VAMOS ACRESCENTAR ASSEGUIR UMA DIV QUE VAI MOSTRAR O AMBIENTE */
	total_linha=lisInRow;
	total_objs=$('div.catprod_wrap div.prd_d').length;

	var b=1;
	var c=0;
	for(var a=1; a<=total_objs; a++)
	{
		if(b==total_linha && a!=total_objs)
		{
			c++;
			b=0;
			$('div.catprod_wrap > #prd_d'+a).after('<div class=\"bigobjc clear\"></div><div id=\"bigobj'+c+'\" class=\"bigobj\" style=\"width:'+(width_obj * total_linha)+'px\"></div>');
		}

		if(a == total_objs)
		{
			c++;
			$('div.catprod_wrap > #prd_d'+a).after('<div class=\"bigobjc clear\"></div><div id=\"bigobj'+c+'\" class=\"bigobj\" style=\"width:'+(width_obj * total_linha)+'px\"></div>');
		}

		b++;
	}
}

function show_precos()
{
	var dataString='';
	$("div.prdprice_n").each(function()
	{

		var x_id=$(this).attr('info');
		if(x_id != '')
		{
			if(dataString != '') dataString += ';'+x_id;
			else dataString=x_id;
		}
	});
	var dados;
	$.ajax
		({
			type: "POST",
			url: SITE_HTTP_AJAX_URL + "do_blocos_precos.php",
			data: 'prods='+dataString+'&dp='+new Date(),
			cache: false,
			success: function(html)
			{
				dados=html.split('[split]');



				var length=dados.length;
				for(var i=0; i < length; i++)
				{
					var prods=dados[i].split('|');
					var prod_id=prods[0];
					var html_prod=prods[1];
					$('#precos_'+prod_id).html(html_prod);
				}

			}
		});
}
function includeJs(jsFilePath)
{
    var js=document.createElement("script");
    js.type="text/javascript";
    js.src=jsFilePath;

	document.body.appendChild(js);
}
function formatDate(date) {
    var d=new Date(date),
        month='' + (d.getMonth() + 1),
        day='' + d.getDate(),
        year=d.getFullYear();

    if (month.length < 2) month='0' + month;
    if (day.length < 2) day='0' + day;

    return [year, month, day].join('-');
}
function show_proms()
{
	if(typeof products2 == 'undefined') {products2=[];}
	var currentdate=new Date();
	var datetime= currentdate.getFullYear()+ "-"+ (currentdate.getMonth()+1)  + "-"+ currentdate.getDate() + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
	var cl=new Array();
	$("div.prdprice_n").each(function()
	{
		var x_id=$(this).attr('info2');
		var cl_OK=new Array();

		cl[x_id]=0;

		$.each(products2, function(i, object)
		{
			if(x_id == object.id)
			{
				if(object.DATA_INICIO <= strtotime(datetime) && object.DATA_FIM >=strtotime(datetime) && !inArray(object.des, cl_OK))
				{

					var d_ini=formatDate(new Date(object.DATA_INICIO * 1000));
					var d_fim=formatDate(new Date(object.DATA_FIM * 1000));
					$('#prd_d_'+x_id).prepend('<div style="position:absolute;top:'+(cl[x_id])+'px;background:#FF0000;color:#FFF;padding:2px;width:20px;height:20px;font-weight:bold;margin-bottom:1px" title="'+object.des+' (De: '+d_ini+' a '+d_fim+')">%</div>');

					cl[x_id]=cl[x_id]+20;
					cl_OK[cl[x_id]]=object.des;

				}
			}
		});
	});
}
function show_proms_ficha_prod()
{
	if(id_produto !='')
	{
		if(typeof products2 == 'undefined') {products2=[];}
		var currentdate=new Date();
		var datetime= currentdate.getFullYear()+ "-"+ (currentdate.getMonth()+1)  + "-"+ currentdate.getDate() + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();

		var cc='';
		var x_id=id_produto;
		var cl_OK=new Array();
		$.each(products2, function(i, object)
		{
			if(x_id == object.id)
			{
				if(object.DATA_INICIO <= strtotime(datetime) && object.DATA_FIM >=strtotime(datetime) && !inArray(object.des, cl_OK))
				{

					var d_ini=formatDate(new Date(object.DATA_INICIO * 1000));
					var d_fim=formatDate(new Date(object.DATA_FIM * 1000));
					cc += '<div style="position:relative;background:#FF0000;color:#FFF;padding:6px;width:30px;height:30px;font-weight:bold;font-size:18px;cursor:pointer;margin-bottom:1px" title="De: '+d_ini+' a '+d_fim+': '+object.des+'">%</div>';
					cl_OK[i]=object.des;
				}
			}
		});

		if(cc != '') $('.campanhas-prods').html(cc).show();
	}
}
  // page button click
  function setPagination()
  {
       $('.paginacao button[name!=0]').click(function()
	   {
			currentpage=$(this).attr('name');

			$.cookies.set('c_pagination_order', currentpage);
			displayProducts();
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
				if(id_produto > 0) {}
				else{ $(window).scrollTop(($('.header-container').offset().top));}
			});
		});
		if(produto_ativo != ''){ $('#div_cont_right_cat2').html(""); $('#filtros_no_prd').show();}
  }

function seleciona_filtros_retorno()
{
	var select_filter_cb=($.cookies.get('c_familia_return_'+familia_ativa) != null ? $.cookies.get('c_familia_return_'+familia_ativa) : '');
	var select_filter_cb_f;
	if(select_filter_cb != '')
	{
		select_filter_cb_f=select_filter_cb.split('|');
		destroy_all_coockies();
		for(var a=0; a<select_filter_cb_f.length;a++)
		{
			if(select_filter_cb_f[a] != undefined)
			{
				$('#category_'+select_filter_cb_f[a]).attr("checked", 'checked');
				setupLabel();
			}
		}
		setupLabel();
	}
}
$(document).ready(function()
{
	//Altera o numero de artigos visiveis por pagina mediante o tamanho do browser
	var largura_disponivel = $("#product_show").width();
	var largura_box_produto = 300;
	var num_elementos_linha= Math.floor(largura_disponivel / largura_box_produto);
	var num_elementos= num_elementos_linha*4;

	/*	
	var newOption1 = $('<option value="'+num_elementos+'" selected="selected">'+num_elementos+' Produtos</option>');
	var newOption2 = $('<option value="'+(num_elementos*2)+'">'+(num_elementos*2)+' Produtos</option>');
	$('#f_visualizacao').empty(); //remove all child nodes
	$('#f_visualizacao').append(newOption1);
	$('#f_visualizacao').append(newOption2);
	$('#f_visualizacao').append('<option value="99999">Ver Todos</option>');
	$('#f_visualizacao').trigger("chosen:updated");
	*/

	//f_visualizacao

	constroi_filtros();

	show_proms_ficha_prod();
	if($('#f_marcas').length > 0)
	{
	}
	else if($('#fam_level1').length > 0)
	{
		$('#f_l').insertAfter('.filtro_view');
		$("#fam_level1 > option[value='"+familia_ativa+"']").attr('selected', 'selected');
	}
	seleciona_filtros_retorno();
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

			currentpage=0;
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

			currentpage=0;
			displayProducts();
		}

		event.stopPropagation();
		event.preventDefault();
	});

	if(filtro_selecionado != "")
	{
		destroy_all_coockies();
		$('#category_'+filtro_selecionado).attr("checked", true);
		setupLabel();
	}

	if(!produto_ativo)
	{
		sortOrder= ($("#product_order").length > 0 ? $("#product_order option:selected").val() : '-1');
	}

	setProducts();

	slider = document.getElementById('range');

	if(slider){
		//Antes de construir o slider vamos ver qual e o preco minimo e maximo
		if(products.length > 0)
		{
			var min_pvp=99999999;
			var max_pvp=0;
			$.each(products, function(i, object)
			{
					curentPrice=parseFloat(object.a4).toFixed(2);
					if(Math.fround(min_pvp) > Math.fround(curentPrice))
					{
						min_pvp=curentPrice;
					}
					if(Math.fround(max_pvp) < Math.fround(curentPrice))
					{
						max_pvp=curentPrice;
					}
			});
			if(min_pvp == max_pvp)min_pvp=0;
			$('#range').attr('min', min_pvp);
  		$('#range').attr('max', max_pvp);
  		var min_pvp_txt = number_format_(min_pvp,2,',','.') + '&euro;';
  		var max_pvp_txt = number_format_(max_pvp,2,',','.') + '&euro;';
			$('#slider-snap-value-lower-text').html(min_pvp_txt);
  		$('#slider-snap-value-upper-text').html(max_pvp_txt);
		}
		noUiSlider.create(slider, {
			start: [parseFloat($('#range').attr('min')), parseFloat($('#range').attr('max'))],
			snap: false,
			connect: true,
			range: {
				'min': parseFloat($('#range').attr('min')),
				'max': parseFloat($('#range').attr('max'))
			},
			format: {
			  to: function ( value ) {
				return number_format_(value,2,',','.') + '&euro;';
			  },
			  from: function ( value ) {
				return value;
			  }
			}
		});
	}


	var snapValues = [
		document.getElementById('slider-snap-value-lower-text'),
		document.getElementById('slider-snap-value-upper-text')
	];

     $('#product_sort').live('change',function()
	 {
		 sortBy=$(this).val();
         currentpage=0;
         sortOrder=0;
         displayProducts();
     });
     $('#product_order').live('change',function()
	 {
         sortOrder =$(this).val();
         currentpage=0;
         displayProducts();
     });
     $('#f_visualizacao').live('change',function()
	 {
         currentpage=0;
         rowsPerPage =$(this).val();
         displayProducts();
     });
	 $('#f_stock').live('change',function()
	 {
         currentpage=0;
         displayProducts();
     });
	 $("#m_l input:checkbox").live('click',function()
	 {
		currentpage=0;
		displayProducts();
	 });

	 $("#relevancia_list input:checkbox").live('click',function()
	 {
		currentpage=0;
		displayProducts();
	 });


	$("#c_l input:checkbox").live('click',function()
	 {
		currentpage=0;
		displayProducts();
	 });

	 $("#price_list input:checkbox").live('click',function()
	 {
		currentpage=0;
		displayProducts();
	 });

	 if(slider) {
        slider.noUiSlider.on('slide', function(values, handle) {
            snapValues[handle].innerHTML = values[handle];

            var values2 = values;
            /**
             * 09-03-2018
             * 1º remover o simbolo do euro
             * 2º remover o . dos milhares para vazio
             * 3º trocar a virgula para ponto
             */
            minPrice2 = values2[0].replace('&euro;', '').replace('.','').replace(',','.');
            maxPrice2 = values2[1].replace('&euro;', '').replace('.','').replace(',','.');
            currentpage = 0;

        });
        slider.noUiSlider.on('change', function(values, handle) {
            snapValues[handle].innerHTML = values[handle];

            var values2 = values;
            /**
             * 09-03-2018
             * 1º remover o simbolo do euro
             * 2º remover o . dos milhares para vazio
             * 3º trocar a virgula para ponto
             */
            minPrice2 = values2[0].replace('&euro;', '').replace('.','').replace(',','.');
            maxPrice2 = values2[1].replace('&euro;', '').replace('.','').replace(',','.');
            currentpage = 0;
            displayProducts();
        });
    }

	$("#search_box").live('keyup',function()
	{
		clearTimeout(seach_sel);
		seach_sel=window.setTimeout(function(){currentpage=0; displayProducts(); clearTimeout(seach_sel);},500);
	});
	$("#search_box2").live('keyup',function()
	{
		var mySearch=$("#search_box2").val().toLowerCase();
		$("#search_box").val(mySearch);
		clearTimeout(seach_sel);
		seach_sel=window.setTimeout(function(){currentpage=0; displayProducts(); clearTimeout(seach_sel);},500);
	});
	$(".chzn-select").chosen({disable_search_threshold: 99,  no_results_text: "Não foram encontrados resultados!", width: "107px", enable_new_filter:false});
	$(".chzn-select2").chosen({disable_search_threshold: 99,  no_results_text: "Não foram encontrados resultados!", width: "120px", enable_new_filter:false});
	$(".chzn-select3").chosen({disable_search_threshold: 8,  no_results_text: "Não foram encontrados resultados!", enable_new_filter:false});
	$("img.lazy").lazyload({skip_invisible : false, threshold : 400});
});

function fam_level(nivel,id_forcado)
{
	var nivel_seguinte=Number(nivel+1);

	$('#fam_level'+nivel).live('change', function(event)
	{
		var id=$(this).val();

		if(id > 0)
		{
			var dataString='id_org='+ id + '&nivel=' + nivel_seguinte + '&marca_id=' + marca_ativa;
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
	var nivel_seguinte=Number(nivel+1);

		var id=id_forcado;

		if(id > 0)
		{
			var dataString='id_org='+ id + '&nivel=' + nivel_seguinte + '&marca_id=' + marca_ativa;
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

function load_filtros_offline(id,nivel_seguinte,nivel)
{
	$('#muda_c').html('<div class="titulo_prods" style="font-size:11px;width:100px;border:#000 0px solid"><img src="'+SITE_HTTP_URL+'layout/loading__.gif" width="20" align="absmiddle" /> Loading...<\/div>');
	var dataString='id_org=' + id + '&filtro_marcas=' + marca_ativa;

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
						var $c_color_order=$.cookies.get('c_color_order');
						if ($c_color_order){	$('#category_'+$c_color_order).attr("checked", 'checked'); setupLabel(); /*displayFilters2();*/setProducts();	/*displayProducts();*/}
					}else{
						var $c_category_order=$.cookies.get('c_category_order'+$(this).val());
						if ($c_category_order){	$('#category_'+$c_category_order).attr("checked", 'checked'); setupLabel(); /*displayFilters2();*/setProducts();	/*displayProducts();*/}
					}
				});
			}

		}
	});
}
function displayFilters2(products)
{
	if(id_produto == '')
	{
		// seleciona_filtros_retorno();
		var counter_cl=0;
		var counter_pl=0;
		var counter_ml=0;
		var counter_fl=0;

		// VERIFICAR SE TEM MARCA
		var tem_marca=0;
		if($('#f_marcas').length > 0) {tem_marca=1;}

		// VERIFICAR SE TEM FAMILIA
		var tem_familia=0;
		if($('#fam_level1').length > 0) {tem_familia=1;}

		// VAMOS BUSCAR TODAS AS CARACTERISTICAS
		var cl=new Array();
		var cl_OK=new Array();
		var counter=0;
		$("#c_l input").each(function(){
			cl[counter]=this.value;
			counter++;
		});


		// VAMOS BUSCAR TODOS OS PREÇOS
		// var pl=new Array();
		var pl_OK=new Array();
		var counter=0; $("#price_list input").each(function(){ pl[counter]=this.value; counter++});

		// VAMOS BUSCAR TODAS AS MARCAS
		// if(tem_marca == 1) {var ml=new Array();
		var ml_OK=new Array();
		var counter=0; $("#f_marcas option").each(function(){ if(this.value > 0){ ml[counter]=this.value; counter++}});

		// VAMOS BUSCAR TODAS AS FAMILIAS
		// if(tem_familia == 1) {var fl=new Array();
		var fl_OK=new Array();
		var counter=0; $("#fam_level1 option").each(function(){ if(this.value > 0){ fl[counter]=this.value; counter++}});

		// PARA CADA PRODUTO DEVOLVIDO
		$.each(products, function(i, object)
		{
			// VAMOS BUSCAR AS MARCAS E COMPARAR SE EXISTEM
			if(tem_marca == 1) { m_sel=object.m_id;  $.each(ml, function(x, val){ if(inArray(m_sel, ml)){ml_OK[counter_ml]=m_sel; counter_ml++;}});}

			// VAMOS BUSCAR AS FAMILIAS E COMPARAR SE EXISTEM
			if(tem_familia == 1) { f_sel=object.f;  $.each(f_sel, function(x, val){ if(inArray(val['f_id'], fl)){if(!inArray(val['f_id'], fl_OK)){fl_OK[counter_fl]=val['f_id']; counter_fl++;}}});}

			// VAMOS BUSCAR OS FILTROS E COMPARAR SE EXISTEM
			c_sel=object.c;
			$.each(c_sel, function(x, val)
			{
				if(inArray(val['c_id'], cl))
				{
					if(!inArray(val['c_id'], cl_OK))
					{
						cl_OK[counter_cl]=val['c_id'];
						counter_cl++;
					}
				}
			});

		});


		// VAMOS MOSTRAR OU ESCONDER MARCAS QUE ESTEJAM ASSOCIADOS AOS PRODUTOS DISPONIVEIS
		if(tem_marca == 1)
		{
			$("#f_marcas option").each(function(){ if(this.value > 0){	if(inArray(this.value, ml_OK)){	$(this).removeAttr('disabled');	}else{ $(this).attr('disabled','disabled');  }}}); $('.chzn-select').trigger("chosen:updated");
		}

		// VAMOS MOSTRAR OU ESCONDER FAMILIAS QUE ESTEJAM ASSOCIADOS AOS PRODUTOS DISPONIVEIS
		if(tem_familia == 1){ $("#fam_level1 option").each(function(){ if(this.value > 0){ if(inArray(this.value, fl_OK)){	$(this).removeAttr('disabled');	}else{ $(this).attr('disabled','disabled');}}}); $('.chzn-select2').trigger("chosen:updated"); }

		// VAMOS MOSTRAR OU ESCONDER FILTROS QUE ESTEJAM ASSOCIADOS AOS PRODUTOS DISPONIVEIS
		var title_ct=0
		$("#c_l input").each(function()
		{
			if(inArray(this.value, cl_OK)) //&& familia_ativa > 0
			{
				$("#"+this.id).closest("li").attr('style','display:block');
				$("#"+this.id).closest("li").removeClass('sematv');
				$("#"+this.id).closest("li").addClass('tem');
			}
			else
			{
				$("#"+this.id).closest("li").attr('style','display:none');
				$("#"+this.id).closest("li").addClass('sematv');
				$("#"+this.id).closest("li").removeClass('tem');
			}

			if($("#"+this.id).closest("li").is(':visible')) title_ct=1;
		});

		$("#c_l li.f_ativos3").each(function()
		{
			var id=$(this).attr('id');
			var tem=($(this).has('li[class*="tem"]').length);
			if(tem > 0)
			{
				$('#'+id).show();
			}
			else
			{
				$('#'+id).hide();
			}
		});

		if(title_ct == 1) $("#cl_t").show(); else  $("#cl_t").hide();
		// CASO EXISTAM FILTROS ESCONDIDOS VAMOS MOSTRAR O BOTAO PARA LIMPAR
		if(title_ct == 1 || $.cookies.get('c_familia_return_'+familia_ativa))
		{
			if($("#c_l :checked").length > 0 && $('#cleanf').length <= 0)
			{
				$("#muda_c").prepend("<a href='#' id='cleanf' onclick='clean_filters();return false;' class='t clear_filters' style=''><i class=\"fa fa-times\" aria-hidden=\"true\"></i> "+txt_limpar_pesquisa[LINGUA]+"&nbsp;</a><div id='clearbot' style='clear:both'></div>");
			}
			else if($("#c_l :checked").length <= 0)
			{
				$( "#cleanf" ).remove();
				$( "#clearbot" ).remove();
			}
		}
		else if($.cookies.get('c_familia_return_'+familia_ativa) == null)
		{
			$( "#cleanf" ).remove();
			$( "#clearbot" ).remove();
		}

		if($("#cl_t").is(':visible') || $("#pl_t").is(':visible'))
		{
			$("#top_f").show();
		}
		else
		{
			$("#top_f").hide();
		}


		var hash_return='';
		var hash_names='';

		$("#muda_c input:checked").each(function()
		{
			if(hash_return) hash_return += '|' + $(this).val();	else hash_return=$(this).val();
			hash_names += '<a href="javascript:;" class="breadfinal" style="" onclick="triggerclick(\'category_'+$(this).val()+'\');return false;">'+$('#category_'+$(this).val()).attr('valor')+' <i class="fa fa-times" aria-hidden="true" style="color:#FFF;font-size:16px"></i></a>';
		});


		if(hash_return != '')
		{
			$.cookies.set('c_familia_return_'+familia_ativa, hash_return);

			$('.filtroson').remove();
			$('#cleanf').after('<div class="filtroson" style="">'+hash_names+'</div>');
		}
		else
		{
			$('.filtroson').remove();
			$.cookies.set('c_familia_return_'+familia_ativa, null);
		}
	}
	$("img.lazy").lazyload({skip_invisible : false, threshold : 100});
}
function displayFilters(filters){}

function existeprodfilter(filter, selected)
{
	var n_prod=0;
	var c_sel_in=new Array();
	$.each(products, function(i, object)
	{
		var counter_prod=0;
		c_sel=object.c;
		$.each(c_sel, function(x, val)
		{
			for(var x=0; x < selected.length; x++){	if(selected[x]==val['c_id']) counter_prod++;}
		});
		if(counter_prod == selected.length) n_prod++;
	});

	return n_prod;
}

function triggerclick(clicked)
{
	$("#"+clicked).trigger("click");
	setupLabel();
	return false;
}
function clean_filters_silent()
{
	$.cookies.set('c_familia_return_'+familia_ativa, null);
	var myCookies=getCookies();
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
	currentpage=0;
	return false;
}
function clean_filters()
{

	$.cookies.set('c_familia_return_'+familia_ativa, null);
	$('#c_l input[type=checkbox]').each(function() { this.checked=false;setupLabel(); prpl=this.value.split('_'); mostra_esconde('caract_'+prpl[1], 'off_caract_'+prpl[1]); });

	var myCookies=getCookies();
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

	currentpage=0;

	setupLabel(); setProducts(); displayProducts();
	return false;
}

function clean_filters_small(id)
{
	$('#'+id).find('input[type=checkbox]:checked').removeAttr('checked');
	currentpage=0;
	setupLabel();setProducts();
	return false;
}

function getFiltros_temp(id,nivel_seguinte,nivel)
{
	$('#muda_c').html('<div class="titulo_prods" style="font-size:11px;width:100px;border:#000 0px solid"><img src="'+SITE_HTTP_URL+'layout/loading__.gif" width="20" align="absmiddle" /> Loading...<\/div>');
	var dataString='id_org=' + id + '&filtro_marcas=' + marca_ativa;

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
						var $c_color_order=$.cookies.get('c_color_order');
						if ($c_color_order){	$('#category_'+$c_color_order).attr("checked", 'checked'); setupLabel(); /*displayFilters2();*/setProducts();	/*displayProducts();*/}
					}else{
						var $c_category_order=$.cookies.get('c_category_order'+$(this).val());
						if ($c_category_order){	$('#category_'+$c_category_order).attr("checked", 'checked'); setupLabel(); /*displayFilters2();*/setProducts();	/*displayProducts();*/}
					}
				});
			}

		}
	});
}

function mostra(p,v)
{
	var e1=document.getElementById(p+v);
	e1.style.display="none";
	var e2=document.getElementById("off_"+p+v);
	e2.style.display="block";
}

function mostra_esconde(a,b)
{
	var e1=document.getElementById(a);
	if(e1) e1.style.display="block";

	var e2=document.getElementById(b);
	if(e2) e2.style.display="none";
}

$(window).load(function(){ $('html, body').animate({scrollTop: '0px'}, 0); });

function mudaRange(catid,value)
{
	for(var i=0; i < $("input[id^='category_"+catid+"_']").length; i++)
	{
		var input= $("input[id^='category_"+catid+"_']").eq(i);
		$('#category_'+input.val()).removeAttr("checked");
		if(value!=0)
		{
			if(input.attr("valor") == value) $('#category_'+input.val()).attr("checked", 'checked');
		}
	}
	setupLabel(); setProducts();
	$('html, body').animate({scrollTop: '0px'}, 0);
}

function mudaVista(value)
{
	view_type=value;
	if(value==0)
	{
		createCookie('vista', view_type, 1);
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
		createCookie('vista', view_type, 1);
		$("#icon_view").removeClass();
		$("#icon_view").addClass("icon_view");
		$("#list_view").removeClass();
		$("#list_view").addClass("list_view view_activo");
		$("#img_icons_view").show();
		$("#img_icons_view_activo").hide();
		$("#img_list_view").hide();
		$("#img_list_view_activo").show();
	}
	setupLabel(); setProducts();
}

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
