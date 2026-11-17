function ImprimeHtml(strid)
{
    var prtContent=document.getElementById(strid);
    var WinPrint=window.open('', '', 'left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write('<!DOCTYPE html><!--[if IE 9]><html xmlns="http://www.w3.org/1999/xhtml" lang="pt" class="ie ie9"> <![endif]--><!--[if gt IE 9]><!--><html xmlns="http://www.w3.org/1999/xhtml" lang="pt"><!--<![endif]--><head>');
    WinPrint.document.write('<link rel="stylesheet" href="layout/default/html5/reset.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="external_api/bootstrap/css/bootstrap.min.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/css_external/global.css" media="all"/>');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/template.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/template_cart.css" type="text/css" media="all" />');
    WinPrint.document.write('</head><body style="font-size:11px"><div style="width:1024px;padding:10px;"><img height="77" width="196" alt="" src="layout/'+PROJETO+'/logos/logo_top.png"></div><div style="width:1024px">');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.write('</body></html>');
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}
function login_facebook()
{
    window.open(SITE_HTTP_AJAX_URL+"login_facebook.php", '_self');
}
function login_google()
{
    window.open(SITE_HTTP_AJAX_URL+"login_google.php", '_self');
}
function ImprimeConteudo(strid)
{
	var prtContent=document.getElementById(strid);
	if(prtContent)
	{
		var WinPrint=window.open('', '', 'left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}
}
var first_selected=0;
function is_touch_device()
{
	if(screen.width <960) return true;
	else return 'ontouchstart' in window || navigator.maxTouchPoints;
}
function getWishlist()
{
	var result='';
	$.ajax({
		type: "GET",
		url: SITE_HTTP_AJAX_URL + "getWishlist.php"
	})
	.done(function (resultado)
	{
		$('#wishlist-count').html(' ('+resultado+')');
	});
}

var first_selected=0;
function filtros_change_multiple(_this, tipo)
{
	if(first_selected == 0) first_selected=_this.attr('datacatid');

	//	SELECT BOX
	if(first_selected == _this.attr('datacatid'))
	{
		$("select.opcoesprod").each(function()
		{
			if($(this).attr('datacatid')!=first_selected) $('select[datacatid='+$(this).attr('datacatid')+']').prop('selectedIndex', 0);
		});
	}

	var codes='';
	total_select=$('select.opcoesprod').length;
	total_selected=0;
	$('select.opcoesprod').find("option:selected").each(function()
	{
		cat_id=$(this).attr('data-catid');
		val_id=$(this).attr('data-valueid');
		if(cat_id > 0 && val_id > 0)
		{
			codes += cat_id+'-'+val_id+'|';
			total_selected++;
		}
	});
	if(codes != '')
	{
		val_sel=_this.val();
		prod_id=_this.find('option:selected').attr('data-prodid');
		cat_id=_this.find('option:selected').attr('data-catid');
		val_id=_this.find('option:selected').attr('data-valueid');
		select_catid=_this.attr('datacatid');
		$("select.opcoesprod").not(_this).each(function()
		{
			select_subcatid=$(this).attr('datacatid');
			if($(this).find('option:selected').find('option:selected').val()!=''){}
			else
			{
				$('#wrap_'+select_subcatid).html('loading...');
			}
		});

		$.ajax(
		{
			type: 'POST',
			url: SITE_HTTP_AJAX_URL + 'ajaxShop_filtros.php',
			data: { codes:codes, prodid:prod_id}
		})
		.done(function(res)
		{
			inf_sep=res.split('[splp]');
			inf=inf_sep[0].split('[spl]');
			stock=inf_sep[2];
			console.log("a="+stock);
			// console.log(inf.length);
			for(var a=0; a<inf.length;a++)
			{
				inf2=inf[a].split('[spl2]');

				// console.log(first_selected+"!="+inf2[0]);
				if(inf2[0] && $('select[datacatid='+inf2[0]+']').find('option:selected').val()!='')
				{}
				else
				{
					$('#wrap_'+inf2[0]).html(inf2[1]);
				}
			}

			if(total_selected==total_select)
			{
				$('#pricechange').html(inf_sep[1]);
			}
		});
	}
}

function getFiltrosTipos(codes, prod_id)
{
	$.ajax(
	{
		type: 'POST',
		url: SITE_HTTP_AJAX_URL + 'ajaxShop_filtros.php',
		data: { codes:codes, prodid:prod_id}
	})
	.done(function(res)
	{
		inf_sep=res.split('[splp]');
		inf=inf_sep[0].split('[spl]');
		stock=inf_sep[2];
		console.log("b="+stock);
		// console.log(inf.length);
		for(var a=0; a<inf.length;a++)
		{
			inf2=inf[a].split('[spl2]');

			// console.log(first_selected+"!="+inf2[0]);
			if(inf2[0] && $('select[datacatid='+inf2[0]+']').find('option:selected').val()!='')
			{}
			else
			{
				$('#wrap_'+inf2[0]).html(inf2[1]);
			}
		}

		if(total_selected==total_select)
		{
			$('#pricechange').html(inf_sep[1]);
		}
	});
}

function filtros_change()
{
	$('.label_check_ps').live('click', function()
	{
		lbl_datacatid='sel_'+$(this).attr('id');
		data_catid=$(this).attr('data-catid');
		$('#sel'+data_catid+' option[id='+lbl_datacatid+']').prop("selected", "selected").trigger('change');

	});
	$('.opcoesprod').live('change',function(e)
	{
		if(first_selected == 0) first_selected=$(this).attr('datacatid');
		if(first_selected == $(this).attr('datacatid'))
		{
			$("select.opcoesprod").each(function()
			{
				if($(this).attr('datacatid')!=first_selected) $('select[datacatid='+$(this).attr('datacatid')+']').prop('selectedIndex', 0);
			});
		}
		var codes='';
		total_select=$('select.opcoesprod').length;
		total_selected=0;
		$('select.opcoesprod').find("option:selected").each(function()
		{
			cat_id=$(this).attr('data-catid');
			val_id=$(this).attr('data-valueid');
			if(cat_id > 0 && val_id > 0)
			{
				codes += cat_id+'-'+val_id+'|';

				total_selected++;
			}
		});

		if(codes != '')
		{
			val_sel=$(this).val();
			prod_id=$(this).find('option:selected').attr('data-prodid');
			cat_id=$(this).find('option:selected').attr('data-catid');
			val_id=$(this).find('option:selected').attr('data-valueid');
			select_catid=$(this).attr('datacatid');

			$("select.opcoesprod").not($(this)).each(function()
			{
				select_subcatid=$(this).attr('datacatid');
				if($(this).find('option:selected').find('option:selected').val()!=''){}
				else
				{
					$('#wrap_'+select_subcatid).html('loading...');
				}
			});

			$.ajax(
			{
				type: 'POST',
				url: SITE_HTTP_AJAX_URL + 'ajaxShop_filtros.php',
				data: { codes:codes, prodid:prod_id}
			})
			.done(function(res)
			{
				inf_sep=res.split('[splp]');
				inf=inf_sep[0].split('[spl]');
				stock=inf_sep[2];
				console.log("c="+stock);
				// console.log(inf.length);
				for(var a=0; a<inf.length;a++)
				{
					inf2=inf[a].split('[spl2]');
					// console.log(first_selected+"!="+inf2[0]);
					if(inf2[0] && $('select[datacatid='+inf2[0]+']').find('option:selected').val()!='')
					{
						// if(inf2[0]!=first_selected && inf2[0]!=select_catid){$('#wrap_'+inf2[0]).html(inf2[1]); }
					}
					else
					{
						$('#wrap_'+inf2[0]).html(inf2[1]).promise().done(function()
						{

							if($('#wrap_cores_'+inf2[0]).find('label').length > 0)
							{
								$('.label_check_ps').each(function()
								{
									lbl_id=$(this).attr('id');
									$('#'+lbl_id).hide();

									$('#wrap_'+inf2[0]+' option').each(function()
									{
										data_catid=$(this).attr('data-catid');
										data_valueid=$(this).attr('data-valueid');

										if(lbl_id == 'lbl'+data_catid+'_'+data_valueid) $('#'+lbl_id).show();

									});
								});
							}
						});
					}
				}
				// console.log(total_selected+'=='+total_select);
				if(total_selected==total_select)
				{
					$('#pricechange').html(inf_sep[1]).promise().done(function()
					{
						// alert(1);
					});
				}
				if(stock<1)
				{
					$('#add_to_cart').html('<a class="icon-coracao produtowl'+prod_id+'" href="#" onclick="addProductWishList(\''+prod_id+'\');return false;"><span title="Favoritos"><i class="far fa-heart" aria-hidden="true"></i></span></a><div class="clear"></div><span class="esgotado" title="Disponivel Brevemente"> Disponivel Brevemente</span><div class="clear"></div><button class="fp_comprar_esgotado fancy_stock" href="https://www.casaeletronica.pt/ajax/setNotificacaoProduto.php?idprod='+prod_id+'">Alerta de Stock</button><div class="clear"></div>');
				}
				if(stock==1)
				{
					$('#add_to_cart').html('<a class="icon-coracao produtowl'+prod_id+'" href="#" onclick="addProductWishList(\''+prod_id+'\');return false;"><span title="Favoritos"><i class="far fa-heart" aria-hidden="true"></i></span></a><div class="clear"></div><span class="disponivel" title="Disponibilidade Total"> Stock Reduzido</span><div class="clear"></div><button class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('+prod_id+',$(\'#quantity_wanted\').val());" type="button"><span>Comprar</span></button><div class="clear"></div>');
				}
				if(stock>1)
				{
					$('#add_to_cart').html('<a class="icon-coracao produtowl'+prod_id+'" href="#" onclick="addProductWishList(\''+prod_id+'\');return false;"><span title="Favoritos"><i class="far fa-heart" aria-hidden="true"></i></span></a><div class="clear"></div><span class="disponivel" title="Disponibilidade Total"> Stock disponivel</span><div class="clear"></div><button class="btn btn-default ajax_add_to_cart_product_button fp_comprar" onclick="adicionarCarrinhoProdutoOpcoes('+prod_id+',$(\'#quantity_wanted\').val());" type="button"><span>Comprar</span></button><div class="clear"></div>');
				}
				// else $('#pricechange').html('<div class="c"></div><div class="prdprice_right"></div><div class="c"></div><div class="c"></div>');
			});
		}
		// setupLabel2();
	});
}

function filtros_update()
{
    var codes='';
    total_select=$('select.opcoesprod').length;
    total_selected=0;
    $('select.opcoesprod').find("option:selected").each(function()
    {
      cat_id=$(this).attr('data-catid');
      val_id=$(this).attr('data-valueid');
      prod_id=$(this).attr('data-prodid');
      if(cat_id > 0 && val_id > 0)
      {
        codes += cat_id+'-'+val_id+'|';
        total_selected++;
      }
    });
    if(codes != '')
    {
      $.ajax(
      {
        type: 'POST',
        url: SITE_HTTP_AJAX_URL + 'ajaxShop_filtros.php',
        data: { codes:codes, prodid:prod_id}
      })
      .done(function(res)
      {
        inf_sep=res.split('[splp]');
        inf=inf_sep[0].split('[spl]');
        stock=inf_sep[2];
		console.log("d="+stock);
        // console.log(inf.length);

        for(var a=0; a<inf.length;a++)
        {
          inf2=inf[a].split('[spl2]');
          if($('select[datacatid='+inf2[0]+']').find('option:selected').val()!=''){}
          else
          {
            $('#wrap_'+inf2[0]).html(inf2[1]);
          }
        }

        if(total_selected==total_select)
        {
          $('#pricechange').html(inf_sep[1]);
        }
        // else $('#pricechange').html('<div class="c"></div><div class="prdprice_right"></div><div class="c"></div><div class="c"></div>');
      });
    }
}

function go(url)
{
	window.open (url,'_self',false)
}
function getLinguas(key)
{
	var result='';
	$.ajax({
		type: "GET",
		async: !1,
		url: SITE_HTTP_AJAX_URL + "getGlobals.php",
		data: {k_lingua: key}
	})
	.done(function (resultado)
	{
		result=resultado;
	});

	return result;
}
function showHintsSearch()
{
	/*if($("#search").val() != '')
	{
		var str=$("#search").val();
		$.ajax({
			type: "GET",
			url: SITE_HTTP_AJAX_URL+"livesearch.php",
			data: { q: str }
		}).done(function(resultado)
		{
			$("#search_hinters").html(resultado);
			$("#search_hinters").show();
		});
	}
	else
	{
		$("#search_hinters").hide();
	}*/
}

function loadImage(id_img)
{
	$('.figuraficha').hide();
	$('#big'+id_img).show();
}


function fs(str)
{
	if(str)$("#search").val(str);

	$("#search_hinters").hide();
	$("form[name='pesquisa']").submit();
}

function addToFavorites(el)
{
	if (window.sidebar && window.sidebar.addPanel)
	{ // Mozilla Firefox Bookmark
		window.sidebar.addPanel(document.title,window.location.href,'');
	}
	else if(window.external && ('AddFavorite' in window.external))
	{ // IE Favorite
		window.external.AddFavorite(location.href,document.title);
	}
	else if(window.opera && window.print)
	{ // Opera Hotlist
		this.title=document.title;
		return true;
	}
	else
	{ // webkit - safari/chrome
		alert('Press ' + (navigator.userAgent.toLowerCase().indexOf('mac') != - 1 ? 'Command/Cmd' : 'CTRL') + ' + D to bookmark this page.');
	}
}


$(window).bind("load", function()
{
	topo_redimensiona();
	muda_slider_fp();
	$('[data-toggle="tooltip"]').tooltip();
	CollapedFaq();
});

$(window).on('resize', function()
{
	topo_redimensiona();
	muda_slider_fp();
});

$(window).on('scroll',function()
{
	topo_redimensiona();

	/* if($(document).scrollTop()>($('.logo_account').height()))
	{
		$(".logo_account2").css('box-shadow','0 4px 2px -2px gray');
		$(".logo_account2").parent().show();
		$('.goup').css("display","block");
	}
	else
	{
		$(".logo_account2").css('box-shadow','none');
		$('.goup').css("display","none");
		$(".logo_account2").parent().hide();
	}

	calcularPosicaoComprarFrame(); */
	if(document.getElementById('submitform')) $('#submitform').val($(document).scrollTop());
});
function setupLabel2()
{
	if ($('.lbl_check input').length)
	{
		$('.lbl_check').each(function(){
			$(this).removeClass('lc_on');
		});
		$('.lbl_check input:checked').each(function(){
			$(this).parent('label').addClass('lc_on');
		});
	};
	if ($('.lbl_radio input').length)
	{
		$('.lbl_radio').each(function()
		{
			$(this).removeClass('lr_on');
		});
		$('.lbl_radio input:checked').each(function()
		{
			$(this).parent('label').addClass('lr_on');
		});
	};
	if ($('.label_check_ps input').length)
	{

		$('.label_check_ps').each(function()
		{
			$(this).removeClass('c_on_ps');

		});
		$('.label_check_ps input:checked').each(function()
		{

			$(this).closest('label').addClass('c_on_ps');
		});
	};
};
function getHeight()
{
	var w=0;
	var h=0;
	//IE
	if(!window.innerWidth)
	{
	//strict mode
		if(!(document.documentElement.clientWidth == 0))
		{
			w=document.documentElement.clientWidth;
			h=document.documentElement.clientHeight;
		}
		//quirks mode
		else
		{
			w=document.body.clientWidth;
			h=document.body.clientHeight;
		}
	}
	//w3c
	else
	{
		w=window.innerWidth;
		h=window.innerHeight;
	}
	return h;
}
function showdeadcenterdiv(Xwidth,Yheight,divid)
{
	var scrolledX, scrolledY;
	if(self.pageYOffset)
	{
		scrolledX=self.pageXOffset;
		scrolledY=self.pageYOffset;
	}
	else if(document.documentElement && document.documentElement.scrollTop)
	{
		scrolledX=document.documentElement.scrollLeft;
		scrolledY=document.documentElement.scrollTop;
	}
	else if(document.body)
	{
		scrolledX=document.body.scrollLeft;
		scrolledY=document.body.scrollTop;
	}

	var centerX, centerY;
	if(self.innerHeight)
	{
		centerX=self.innerWidth;
		centerY=self.innerHeight;
	}
	else if(document.documentElement && document.documentElement.clientHeight)
	{
		centerX=document.documentElement.clientWidth;
		centerY=document.documentElement.clientHeight;
	}
	else if( document.body )
	{
		centerX=document.body.clientWidth;
		centerY=document.body.clientHeight;
	}

	var leftOffset=scrolledX + (centerX - Xwidth) / 2;
	var topOffset= (scrolledY) + (centerY - Yheight) / 2;

	// var o=document.getElementById(divid);
	// var r=o.style;
	// r.position='absolute';
	// r.top=topOffset + 'px';
	// r.left=leftOffset + 'px';
	return [topOffset, leftOffset];
}
$.fn.center_window=function ()
{
	this.css("position","absolute");
	dt=showdeadcenterdiv(290,198,'buy_frm');
	this.css("top", dt[0] + "px");
	this.css("left", dt[1] + "px");
	return this;
}
function getSearchResults(str)
{

	if(str=='')
	{
		$("#search_hinters").hide();
	}
	else
	{
		/*selectitems("select * from products where Ref Like %"+str+"%",getval_success);
		function getval_success(result)
		{console.log('Ref');
			if(result.length>0)
			 {
			  for(var i = 0, item = null; i < result.length; i++)
			  {
			     item = result.item(i);
			     console.log(item['Ref']);
			  }
			 }
		}*/

		$.ajax({
			beforeSend: function()
			{
				$(".search").append('<div style="position:absolute;top:12px;left:10px;opacity:0.2" class="load_sea"><img src="layout/'+PROJETO+'/images/loading.gif" /></div>');
			},
			url: SITE_HTTP_AJAX_URL+"livesearch.php",
			dataType:"html",
			data: {q: str},
			method: "GET",
			async: true,
			success: function(data)
			{
				if(data !== null)
				{
					$("#search_hinters").html(data);
					$("#search_hinters").show();
					$('.load_sea').remove();
				}
				$('.load_sea').remove();
			},
			error: function()
			{
				$('.load_sea').remove();
			}
		});
	}
}
function topo_redimensiona()
{
}

function muda_slider_fp()
{
	var p_block=$('.primary_block').outerWidth();

	if(p_block < 780)
	{
		$('.resp1').hide();
		$('.resp2').show();
	}
	else
	{
		$('.resp2').hide();
		$('.resp1').show();
	}
}

function show_precos_home()
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
			data: 'prods='+dataString,
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
					$('#home_precos_'+prod_id).html(html_prod);
				}

			}
		});
}
function reload_shopping_cart()
{
	var getstr=''; var getobr=""; var answerx="";
	frm=document.getElementById('encomendas');

	if(!frm || !frm.elements) {Apprise("errao1");return;}

	var elms=frm.elements;
	for(var i=0, maxI=elms.length; i < maxI; ++i)
	{
		var elm=elms[i];

		if (elm.type == "checkbox")
		{
			if (elm.checked)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "radio")
		{
			if (elm.checked)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "text")
		{
			if (elm.value && elm.className=="inputtext_obr")
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
			else if (elm.value=="" && elm.className=="inputtext_obr")
			{
				if(getobr) getobr += ", "+elm.name;
				else  getobr += elm.name;
			}
			else
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "tel")
		{
			if (elm.value)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "email")
		{
			if (elm.value)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "hidden")
		{
			if (elm.value)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "select-one")
		{
			if (elm.value)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
		if (elm.type == "textarea")
		{
			if (elm.value)
			{
				getstr += elm.name + "=" + elm.value + "&";
			}
		}
	}

	var d='<div style="position:absolute;top:0;left:0;opacity:0.8;width:100%;height:100%;background:rgba(250,250,250, 0.5);text-align:center;" class="load_cart"><img border="0" src="layout/'+PROJETO+'/images/orders.gif" style="width: 106px;  height: 106px; position: fixed; top: 50%; left: 50%; margin-left: -100px; margin-top: -100px;" /></div>';
	$("#shopping_cart_loading").append(d);
	$.ajax
		({
			type: "POST",
			url: SITE_HTTP_AJAX_URL + "do_shopping_cart.php",
			data: getstr+'&reload_cart=1',
			cache: false,
			success: function(html)
			{
				$("#shopping_cart_loading").html(html);
				$(".chzn-select").chosen({disable_search_threshold: 10,width: '100%' });
				$(".chzn-select-deselect").chosen({allow_single_deselect:true});
				setupLabel2();
			}
		});

}


function getPageWidth()
{
  if (self.innerWidth) {return self.innerWidth; }
  if (document.documentElement && document.documentElement.clientWidth) {return document.documentElement.clientWidth; }
  if (document.body) {return document.body.clientWidth; }
}
var pagewidth = getPageWidth();
function updatepagewith()
{
	pagewidth = getPageWidth();
}
window.addEventListener("resize", updatepagewith);


var is_mobile=0;
if(pagewidth<=1024)is_mobile=1;


window.addEventListener('resize', function(event){
	if(pagewidth<=1024 && is_mobile==0)
	{
		//Reload page
		window.open('index.php?ism=1','_top');
	}
	if(pagewidth>1024 && is_mobile==1)
	{
		//Reload page
		window.open('index.php?ism=-1','_top');
	}
});


$(document).ready(function()
{
	var delay2 = (function() {var timer = 0; return function(callback, ms) {  clearTimeout(timer);   timer = setTimeout(callback, ms);  };})();

	$('#m_voucher').live('keyup',function() { var target2 = $(this); delay2(function() {getVoucherResults(target2.val()); }, 1000);});
	$('#m_voucher').live('keyup keypress', function(e)
	{
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) {
		e.preventDefault();
		return false;
	  }
	});

	if (is_mobile)
	{
		if(document.getElementById("mobile_banner") )document.getElementById("mobile_banner").style.display = "block";
  }

	// document.getElementById("mobile_banner").style.display = "block";

  /*
	getWishlist();
	*/

	/*$(function() {
		$('#main-menu').smartmenus({
			mainMenuSubOffsetX: -1,
			mainMenuSubOffsetY: 4,
			subMenusSubOffsetX: 6,
			subMenusSubOffsetY: -6
		});
	});*/


	$('#a_minus').on('click', function()
	{
		$.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "session_font.php",
        data: {minus: 1}
	    }).done(function (resultado) {
	    	if(resultado)
	    	{
		    	var fontSize = parseInt($("body").css("font-size"));
				fontSize = fontSize + -2 + "px";
				$("*").css({'font-size':fontSize});
	    	}
	    });

	});

	$('#a_plus').on('click', function()
	{
		$.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "session_font.php",
        data: {plus: 1}
	    }).done(function (resultado) {
	    	if(resultado)
	    	{
		    	var fontSize = parseInt($("body").css("font-size"));
				fontSize = fontSize + 2 + "px";
				$("*").css({'font-size':fontSize});
			}
	    });
	});

	$('#categorias-menu [data-accordion]').accordion();

	$('#observacoes-button').on('click', function(e){
		if($(window).width()<= 768)
		{
			if($('#condicoes-compra-mobile').is(':visible')) $('#condicoes-compra-mobile').hide();
			else $('#condicoes-compra-mobile').show()
		}
	});

	$('#encomendas').on('keyup keypress', function(e)
	{
	  var keyCode=e.keyCode || e.which;
	  if (keyCode === 13) {
		e.preventDefault();
		return false;
	  }
	});

	$("img.lazy").lazyload();

	// getWishlist();


	if($('.flexnav').length > 0)
	{
		$(".flexnav").flexNav();
	}

	if($('.bxslider').length > 0)
	{
		$('.bxslider').each(function()
		{
			var banner_id=$(this).attr('id');
			$('#'+banner_id).bxSlider({ adaptiveHeight: true, pager:false, mode: 'horizontal', autoHover:true, auto:true, pause:10000, onSliderLoad: function(){ $('#warp_'+banner_id).css('visibility', 'visible'); }});
		});

	}
	if($('#menu-slider').length > 0)
	{
		var slider_menu=$('#menu-slider').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false,onSliderLoad: function(el) {$('#menu-slider').css('visibility', 'visible');}});
		$('#goToPrevSlide_menu').on('click', function (){slider_menu.goToPrevSlide();});
		$('#goToNextSlide_menu').on('click', function (){slider_menu.goToNextSlide();});
	}

	if($('#home-destaque').length > 0)
	{
		var slider_destaque=$('#home-destaque').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false,onSliderLoad: function(el) {$('#home-destaque').css('visibility', 'visible');}});
		$('#goToPrevSlide_destaque').on('click', function (){slider_destaque.goToPrevSlide();});
		$('#goToNextSlide_destaque').on('click', function (){slider_destaque.goToNextSlide();});
	}
	if($('#home-promocao').length > 0)
	{
		var slider_promocao=$('#home-promocao').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false,onSliderLoad: function(el) {$('#home-promocao').css('visibility', 'visible');}});
		$('#goToPrevSlide_promocao').on('click', function (){slider_promocao.goToPrevSlide();});
		$('#goToNextSlide_promocao').on('click', function (){slider_promocao.goToNextSlide();});
	}
	if($('#home-maisvendido').length > 0)
	{
		var slider_maisvendido=$('#home-maisvendido').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false,onSliderLoad: function(el) {$('#home-maisvendido').css('visibility', 'visible');}});
		$('#goToPrevSlide_maisvendido').on('click', function (){slider_maisvendido.goToPrevSlide();});
		$('#goToNextSlide_maisvendido').on('click', function (){slider_maisvendido.goToNextSlide();});
	}
	if($('#home-novidades').length > 0)
	{
		var slider_novidades=$('#home-novidades').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false,onSliderLoad: function(el) {$('#home-novidades').css('visibility', 'visible');}});
		$('#goToPrevSlide_novidades').on('click', function (){slider_novidades.goToPrevSlide();});
		$('#goToNextSlide_novidades').on('click', function (){slider_novidades.goToNextSlide();});
	}
	if($('#home-visualizados').length > 0)
	{
		var slider_visualizados=$('#home-visualizados').lightSlider({ auto:true, loop:true, pauseOnHover:true, item: 2, vertical:true, verticalHeight: 230, gallery: false, pager:false, slideMargin:1, controls: true,onSliderLoad: function(el) {$('#novidades-catalogo').css('visibility', 'visible');}});
	}
	if($('#novidades-catalogo').length > 0)
	{
		var slider_visualizados=$('#novidades-catalogo').lightSlider({ auto:true, loop:true, pauseOnHover:true, item: 2, vertical:true, verticalHeight: 230, gallery: false, pager:false, slideMargin:1, controls: true,onSliderLoad: function(el) {$('#novidades-catalogo').css('visibility', 'visible');}});
	}
	/* if($('#menu-slider').length > 0)
	{
		var slider_menu=$('#menu-slider').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false});
		$('#goToPrevSlide_menu').on('click', function (){slider_menu.goToPrevSlide();});
		$('#goToNextSlide_menu').on('click', function (){slider_menu.goToNextSlide();});
	}

	if($('#home-destaque').length > 0)
	{
		var slider_destaque=$('#home-destaque').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false});
		$('#goToPrevSlide_destaque').on('click', function (){slider_destaque.goToPrevSlide();});
		$('#goToNextSlide_destaque').on('click', function (){slider_destaque.goToNextSlide();});
	}
	if($('#home-promocao').length > 0)
	{
		var slider_promocao=$('#home-promocao').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false});
		$('#goToPrevSlide_promocao').on('click', function (){slider_promocao.goToPrevSlide();});
		$('#goToNextSlide_promocao').on('click', function (){slider_promocao.goToNextSlide();});
	}
	if($('#home-maisvendido').length > 0)
	{
		var slider_maisvendido=$('#home-maisvendido').lightSlider({autoWidth:true,pager:false,slideMargin:1,controls: false});
		$('#goToPrevSlide_maisvendido').on('click', function (){slider_maisvendido.goToPrevSlide();});
		$('#goToNextSlide_maisvendido').on('click', function (){slider_maisvendido.goToNextSlide();});
	} */
	if($('.product-gallery').length > 0)
	{
		$(".product-gallery").lightSlider(
		{
			gallery: true,
			loop: false,
			item: 1,
			thumbItem:5,
			slideMargin: 15,
			vertical:false,
			verticalHeight:245,
			vThumbHeight:20
		});

		$(".product-gallery-resp").lightSlider(
		{
			item: 1,
			autoWidth:true,
			pager:false,
			slideMargin:0,
			controls: true
		});

		muda_slider_fp();
	}
	if($('.horizontalTab').length > 0)
	{
		$('.horizontalTab').easyResponsiveTabs({
		type: 'default',
		width: 'auto',
		fit: true,
		closed: 'accordion',
		activate: function(event) {
		var $tab=$(this);
		var $info=$('#tabInfo');
		var $name=$('span', $info);
		$name.text($tab.text());
		$info.show();
		}
		});
	}
	if($('.horizontalTab2').length > 0)
	{
		$('.horizontalTab2').easyResponsiveTabs({
		type: 'default',
		width: 'auto',
		fit: true,
		closed: 'accordion',
		activate: function(event) {
		var $tab=$(this);
		var $info=$('#tabInfo');
		var $name=$('span', $info);
		$name.text($tab.text());
		$info.show();
		}
		});
	}
	filtros_change();
	$(".chzn-select").chosen({disable_search_threshold: 10,width: '100%' }); $(".chzn-select-deselect").chosen({allow_single_deselect:true});
	var delay=(function() {var timer=0; return function(callback, ms) {  clearTimeout(timer);   timer=setTimeout(callback, ms);  };})();


	/*$('#search_query_top').autocomplete({
	source:SITE_HTTP_AJAX_URL+"livesearch2.php",
	minLength:2
    });*/

	//$('#search_query_top').keyup(function() { var target=$(this); delay(function() {getSearchResults(target.val()); }, 1000);});

	/*$('#search_query_top').change(function() { var target=$(this); delay(function() {getSearchResults(target.val()); }, 10);});*/

	function search() {
		var query_value = $('input#search_query_top').val();
		getSearchResults(query_value);
	}
	$('#search_query_top').on("input", function(e) {

		/*clearTimeout($.data(this, 'timer'));
		var search_string = $(this).val();
		$(this).data('timer', setTimeout(search, 100));*/


		clearTimeout(this.timer);
		if (this.value.length>1) {
			this.timer=setTimeout(search, 250);
		}
		else
		{
			$("#search_hinters").hide();
		}


	});



	$('.spinner .btn:last-of-type').on('click', function() {
		$('.spinner input').val( parseInt($('.spinner input').val(), 10) + 1);
	});
	$('.spinner .btn:first-of-type').on('click', function() {
		if(parseInt($('.spinner input').val(), 10) > 0) $('.spinner input').val( parseInt($('.spinner input').val(), 10) - 1);
	});
	$('body').addClass('has-js');
	$('.lbl_check, .lbl_radio, .lbl_check2, .label_check_ps').live('click', function(){setupLabel2();}); setupLabel2();
	$("html, body").bind("scroll mousedown DOMMouseScroll mousewheel keyup", function(){$('html, body').stop(); });
    $('.goup').click(function(){$("html, body").animate({scrollTop:0}); return false;});
	$('.fancybox').fancybox({'openEffect': 'elastic','closeEffect': 'elastic'});
	$('.fancybox2').fancybox({'openEffect': 'elastic','closeEffect': 'elastic'});
	$('.fancybox-img').fancybox({'openEffect': 'elastic','closeEffect': 'elastic'});
	$('.tooltip').tooltipster({maxWidth:320});
	$(".prd_whish").tooltipster({trigger:'click',animation: 'fade',	delay: 200,	theme: 'tooltipster-default','functionReady':function(){
    var el=$(this);
    setTimeout(function(){
      el.tooltipster('hide',function(){
           el.tooltipster('content',el.attr("title"));
      });

    },1000)
   }});
	$('.cmb-langs').selectpicker({
		style: 'btn-fjmpc1',
		size: 4
	});

	$('.selnormal').selectpicker({
		style: 'btn-fjmpc2',
		size: 4
	});


	$('#fancy_testemunho').fancybox({
    'width'             :   500,
    'height'            :   250,
    'autoScale'         :   false,         //if using older fancybox
    'autoSize'          :   false,         //if using newer fancybox
    'autoDimensions'    :   false
    });
   $('#fancy_comentario').fancybox({
    'width'             :   500,
    'height'            :   250,
    'autoScale'         :   false,         //if using older fancybox
    'autoSize'          :   false,         //if using newer fancybox
    'autoDimensions'    :   false
    });

	$('.fancy_stock').live('click', function(){ var href=$(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 300, maxHeight : 170, scrolling : 'no', type : 'html', onComplete: function() {/* $("#fancybox-wrap").stop().animate({'top':'194px',}, 0); */ $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
    $('.fancy_content').live('click', function(){
        var href=$(this).attr('href');
        $.fancybox({type:'iframe',href:href,autoSize:false,fitToView:true,
            width:Math.min(1040,window.innerWidth-32),height:Math.round(window.innerHeight*.84),
            maxWidth:'96%',maxHeight:'90%',padding:0,margin:16,wrapCSS:'db-legal-modal',
            iframe:{scrolling:'auto'},afterShow:function(){
                var frame=$('.db-legal-modal iframe')[0];
                function readable(){try{
                    var doc=frame.contentDocument;if(!doc||doc.getElementById('db-legal-reading'))return;
                    var style=doc.createElement('style');style.id='db-legal-reading';
                    style.textContent='html,body{overflow-x:hidden!important;width:auto!important;min-width:0!important}body{padding:clamp(18px,4vw,42px)!important;box-sizing:border-box;font-size:16px!important;line-height:1.75!important}body>div{max-width:100%!important;min-width:0!important;box-sizing:border-box}img,table{max-width:100%!important;height:auto}p{font-size:16px!important;line-height:1.75!important;overflow-wrap:anywhere}h1,h2{line-height:1.35!important}';doc.head.appendChild(style);
                }catch(e){/* Keep the same-origin document's existing styles if unavailable. */}}
                if(frame){frame.addEventListener('load',readable);readable();}
            }});return false;
    });
	$('.fancy_recomendar').live('click', function(){ var href=$(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 800, maxHeight : 450, scrolling : 'no', type : 'html', onComplete: function() {/* $("#fancybox-wrap").stop().animate({'top':'194px',}, 0); */ $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});

	$(".prodby").live('click', function()
	{
		var prodid=$(this).children('input[name="prodid"]').val();
		var prodfiltros=$(this).children('input[name="prodfiltros"]').val();
		var qtd=1;
		if(prodfiltros) window.open(SITE_HTTP_URL+prodfiltros,'_self');
		else adicionarCarrinhoProduto(prodid,qtd);
	});
	bindCustomCheckbox(".customCheckboxCriarConta","customCheckboxCriarContaOn");
	bindCustomCheckbox(".customCheckboxAceitarCompra","customCheckboxAceitarCompraOn");
	bindCustomCheckbox(".customCheckboxEmbrulho","customCheckboxEmbrulhoOn");
	bindCustomCheckbox(".customCheckboxOferta","customCheckboxOfertaOn");
	bindCustomCheckbox(".customCheckboxNewsletter","customCheckboxNewsletterOn");
	bindCustomCheckbox(".customCheckboxNotificao","customCheckboxNotificaoOn");

	$(".customCheckboxDadosDiferentes").live('click', (function(e)
	{
		var elem=$(this);
		var input=elem.attr("for");

		if($("#"+input).is(":checked"))
		{
			$(elem).removeClass("customCheckboxDadosDiferentesOn");
			$("#"+input).attr("checked", false);
		}
		else
		{
			$(elem).addClass("customCheckboxDadosDiferentesOn");
			$("#"+input).attr("checked", true);
		}
		reload_shopping_cart();
		e.preventDefault();
	}));


	if(!("placeholder" in document.createElement("input")))
	{
		$("input[placeholder], textarea[placeholder]").each(function()
		{
			var val=$(this).attr("placeholder");

			if (this.value == '') this.value=val;

			$(this).focus(function()
			{
				if ( this.value == val ) this.value='';
			})
			.blur(function()
			{
				if ($.trim(this.value) == '') this.value=val;
			});
		});
	}

	$("#brand_hidden").click(function()
	{
		if($(".brand_hidden").is(':visible'))
		{
			$(".brand_hidden").hide();
			$("#brand_hidden").html('<img src="layout/'+PROJETO+'/images/vermais.png" border="0" alt="" />');
		}
		else
		{
			$(".brand_hidden").show();
			$("#brand_hidden").html('<img src="layout/'+PROJETO+'/images/esconder.png" border="0" alt="" />');
		}
	});
	$("#fam_hidden").click(function()
	{
		if($(".fam_hidden").is(':visible'))
		{
			$(".fam_hidden").hide();
			$("#fam_hidden").html('<img src="layout/'+PROJETO+'/images/vermais.png" border="0" alt="" />');
		}
		else
		{
			$(".fam_hidden").show();
			$("#fam_hidden").html('<img src="layout/'+PROJETO+'/images/esconder.png" border="0" alt="" />');
		}
	});

	$(".filtro_vertodas").click(function()
	{
		if($(".filtro_vertodas").parent().children(".filtro_opcoes").css("max-height")=="none")
		{
			$(".filtro_vertodas").parent().children(".filtro_opcoes").css("max-height","140px");
			$(".filtro_vertodas").text( $(".filtro_vertodas").attr("closed"));
		}
		else
		{
			$(".filtro_vertodas").parent().children(".filtro_opcoes").css("max-height","none");
			$(".filtro_vertodas").text( $(".filtro_vertodas").attr("opened"));
		}
	});
	if(document.getElementById('wp_other')) $('#wp_other').microfiche({'bullets':false, 'buttons':true});
});

function calcularPosicaoChat()
{
	if($('header').length>0)
	{
		var anchor=$('header').offset();
		var largura=$('header').width();
		var left_pos=(anchor.left+largura)-308;
		$("#chat_unopen").css("left", left_pos+"px");
		$("#chat_open_wrapper").css("left", left_pos+"px");
	}
}
function calcularPosicaoComprarFrame()
{
	if($('.main_content').length>0)
	{
		var anchor=$('.main_content').offset();
		var largura=$('.main_content').width();
		var left_pos=(anchor.left+largura)-308;
		$('#buy_frm').center_window();
	}
}
function toggleLoginBox(referer)
{
	$.fancybox({
        width: 420,
        height: 470,
        autoSize: true,
        href: SITE_HTTP_AJAX_URL + 'ajax_login.php?redir='+referer,
        type: 'ajax',
		ajax: {
			type: "GET",
			cache : false
		}
    });
}
function toggleLoginBox2()
{
	$.fancybox({
        width: 250,
        height: 250,
        autoSize: false,
        href: SITE_HTTP_AJAX_URL + 'ajax_login.php',
        type: 'ajax',
		ajax: {
			type: "GET",
			cache : false
		}
    });
}
function adicionarCarrinhoProduto(prodid,qtd2)
{

	 $.fancybox({
        autoDimension:true,
        autoSize: true,
        href: SITE_HTTP_AJAX_URL + 'prod_properties.php',
        type: 'ajax',
		ajax: {
			type: "GET",
			cache : false,
			data: { prod_id: prodid, qtd:qtd2 },
			complete: function(jqXHR, textStatus)
			{
				$('#buy_frm').html(textStatus);
			}
		}
    });
}
function adicionarCarrinhoProdutoOpcoes(prodid, qtd2)
{
	var empty = '';
	var escolha = '';
	var escolhaids = '';
	var pos = 0;
	$('.opcoesprod').each(function(i, obj)
	{
		var data = $('option[value="'+$(this).val()+'"]').data();
		if($(this).val()=='' || $(this).val()==0) empty = 1;
		else
		{
			escolha += $(this).val() + ', ';
			if(pos==0) escolhaids += data.catid + '-' + data.valueid;
			else escolhaids += ';'+data.catid + '-' + data.valueid;
			pos++;
		}
	});
	if(empty==1)
	{
		Apprise("Por favor escolha as opções disponiveis");  	
	}
	else
	{	
		opcoes_id = $('#opcoes_id').val();
		var garantia = 0; 
		var ok = 0; 
		
		
		if($('input[name=prod_garantia]').length > 0)
		{
			garantia = $('input[name=prod_garantia]:checked').val();
			
			if(garantia > 0 && $('input[name=aceito]').is(':checked') == false)
			{
				Apprise("Para adicionar o seguro tem que aceitar os termos e condições");  	
			}
			else if(garantia > 0 && $('input[name=aceito]').is(':checked') == true)
			{
				ok = 1; 	
			}
			else if(garantia == 0)
			{
				ok = 1; 	
			}
		}
		else
		{
			ok = 1;
		}
		
		if(ok == 1)
		{
			$.fancybox({
			autoDimension:true,
			autoSize: true,
			href: SITE_HTTP_AJAX_URL + 'prod_properties.php',		
			type: 'ajax',
			ajax: {
					type: "GET",
					cache : false,
					data: { prod_id: prodid, qtd:qtd2, opcoes : escolha, opcoesids : escolhaids, opcoes_id : opcoes_id, garantia_add: garantia },
					complete: function(jqXHR, textStatus) 
					{
						$('#buy_frm').html(textStatus);
					}
				}
			});
		}
	}
}
function fecharPopupAdicionarProduto(){ $.fancybox.close();}

function toggleSessaoBox(){
if(document.getElementById)
  {
    var normal=document.getElementById('normal');
    var login=document.getElementById('login');
    var login_recupera=document.getElementById('login_recupera');
    if(normal.style.display=='block')
    {
      normal.style.display='none';
      login.style.display='block';

	  //var fb_login=document.getElementById('fb_login');
	  //fb_login.src="ajax_login.php";

    }
    else
    {
      normal.style.display='block';
      login.style.display='none';
    }
  }
}

function login_recup()
{
  if(document.getElementById)
  {
    var login=document.getElementById('login');
    var login_recupera=document.getElementById('login_recupera');
    if(login.style.display=='block')
    {
      login.style.display='none';
      login_recupera.style.display='block';
    }
    else
    {
      login.style.display='block';
      login_recupera.style.display='none';
    }
  }
}

function  brandsScrollRight(total)
{
	var currentscroll=$("#brandinnerslider").scrollLeft();
	if (!$("#brandinnerslider").is(':animated'))
	{
		$("#brandinnerslider").clearQueue();
		$("#brandinnerslider").stop();
		$("#brandinnerslider").animate({scrollLeft: (currentscroll+940)}, 800);
		var pagina=((((currentscroll + 940) * 1) / 940) + 1);
		var inicio=pagina - 1;inicio=inicio * 10;
		for(var a=(inicio + 1); a<=(10 * pagina); a++)
		{
			if(document.getElementById('mc_imgload'+a))
			{
				$('#mc_imgload'+a).attr('src', $('#mc_imgload'+a).attr('datasrc'));
			}
		}
	}
}

function  brandsScrollLeft()
{
  var currentscroll=$("#brandinnerslider").scrollLeft();
  if (!$("#brandinnerslider").is(':animated'))
  {
    $("#brandinnerslider").clearQueue();
    $("#brandinnerslider").stop();
    $("#brandinnerslider").animate({scrollLeft: (currentscroll-940)}, 800);
  }
}

function  destaquesScrollRight(pos, total)
{
	var currentscroll=$("#destaquesinnerslider_"+pos).scrollLeft();

	if (!$("#destaquesinnerslider_"+pos).is(':animated'))
	{
		$("#destaquesinnerslider_"+pos).clearQueue();
		$("#destaquesinnerslider_"+pos).stop();
		$("#destaquesinnerslider_"+pos).animate({scrollLeft: (currentscroll+944)}, 800);
		var pagina=((((currentscroll + 944) * 1) / 944) + 1);
		var inicio=pagina - 1;inicio=inicio * 4;
		for(var a=(inicio + 1); a<=(4 * pagina); a++)
		{
			if(document.getElementById(pos+'_imgload'+a))
			{
				$('#'+pos+'_imgload'+a).attr('src', $('#'+pos+'_imgload'+a).attr('datasrc'));
			}
		}
	}
}

function  destaquesScrollLeft(pos){
  var currentscroll=$("#destaquesinnerslider_"+pos).scrollLeft();
  if (!$("#destaquesinnerslider_"+pos).is(':animated'))
  {
    $("#destaquesinnerslider_"+pos).clearQueue();
    $("#destaquesinnerslider_"+pos).stop();
    $("#destaquesinnerslider_"+pos).animate({scrollLeft: (currentscroll-944)}, 800);

  }
}

function  promocoesScrollRight(){
  var currentscroll=$("#promocoesinnerslider").scrollLeft();
  if (!$("#promocoesinnerslider").is(':animated'))
  {
    $("#promocoesinnerslider").clearQueue();
    $("#promocoesinnerslider").stop();
    $("#promocoesinnerslider").animate({scrollLeft: (currentscroll+944)}, 800);
  }
}

function  promocoesScrollLeft(){
  var currentscroll=$("#promocoesinnerslider").scrollLeft();
  if (!$("#promocoesinnerslider").is(':animated'))
  {
    $("#promocoesinnerslider").clearQueue();
    $("#promocoesinnerslider").stop();
    $("#promocoesinnerslider").animate({scrollLeft: (currentscroll-944)}, 800);
  }
}

function  equivalentesScrollRight(total)
{
	var currentscroll=$("#equivalentesinnerslider").scrollLeft();
	if (!$("#equivalentesinnerslider").is(':animated'))
	{
		$("#equivalentesinnerslider").clearQueue();
		$("#equivalentesinnerslider").stop();
		$("#equivalentesinnerslider").animate({scrollLeft: (currentscroll+904)}, 800);
		var pagina=((((currentscroll + 904) * 1) / 904) + 1);
		var inicio=pagina - 1;inicio=inicio * 4;
		for(var a=(inicio + 1); a<=(4 * pagina); a++)
		{
			if(document.getElementById('imgloadrel'+a))
			{
				$('#imgloadrel'+a).attr('src', $('#imgloadrel'+a).attr('datasrc'));
			}
		}
	}
}

function  equivalentesScrollLeft(){
  var currentscroll=$("#equivalentesinnerslider").scrollLeft();
  if (!$("#equivalentesinnerslider").is(':animated'))
  {
    $("#equivalentesinnerslider").clearQueue();
    $("#equivalentesinnerslider").stop();
    $("#equivalentesinnerslider").animate({scrollLeft: (currentscroll-904)}, 800);
  }
}
function  sugestoesScrollRight(){
  var currentscroll=$("#sugestoesinnerslider").scrollLeft();
  if (!$("#sugestoesinnerslider").is(':animated'))
  {
    $("#sugestoesinnerslider").clearQueue();
    $("#sugestoesinnerslider").stop();
    $("#sugestoesinnerslider").animate({scrollLeft: (currentscroll+904)}, 800);
  }
}

function  sugestoesScrollLeft(){
  var currentscroll=$("#sugestoesinnerslider").scrollLeft();
  if (!$("#sugestoesinnerslider").is(':animated'))
  {
    $("#sugestoesinnerslider").clearQueue();
    $("#sugestoesinnerslider").stop();
    $("#sugestoesinnerslider").animate({scrollLeft: (currentscroll-904)}, 800);
  }
}
function spinnerUp(id){
      $("#"+id).val(parseInt($("#"+id).val())+1);
}
function spinnerDown(id){
    if(parseInt($("#"+id).val())<=1){
      $("#"+id).val(1);
    }else{
      $("#"+id).val(parseInt($("#"+id).val())-1);
    }
}





function toggleChatBox()
{


  if(document.getElementById('chat_open_wrapper').style.display=='block') {
    $("#chat_unopen").show();
    $('#chat_open_wrapper').slideUp('slow');
  }else{
    $('#chat_open_wrapper').slideDown('slow',function(){
      $("#chat_unopen").hide();
    });
  }

}

function showProductTab(pos)
{
	if(document.getElementById && document.getElementById("produto_conteudos"))
	{
		var al=document.getElementById(pos+'_ct');
		var br=document.getElementById("produto_conteudos").getElementsByTagName("div");

		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++)
			{
				if (br[i].className=="ptab"){ br[i].style.display='none';}
				if (br[i].className=="tabs active"){ br[i].className='tabs';}

			}
			al.style.display="block";
			$("#"+pos).addClass("tabs active");
		}
	}
}
/*WISHLIST*/
function addProductWishList(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishList.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

        if (resultado != '')
		{
            $(".produtowl" + id_prod).attr("onclick", "removeProductWishList('" + id_prod + "','0');return false;");
            $(".produtowl" + id_prod).addClass("active_heart");
	        $(".produtowl" + id_prod).children('span').html(key_wishlist_rem[LINGUA]+' <i class="fas fa-heart" aria-hidden="true"></i>');
			getWishlist();
         }
    });

	return false;
}

function removeProductWishList(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishList.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
            if (resultado != '') {
                $(".produtowl" + id_prod).attr("onclick", "addProductWishList('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
                $(".produtowl" + id_prod).removeClass("active_heart");
	            $(".produtowl" + id_prod).children('span').html(key_wishlist[LINGUA]+' <i class="far fa-heart" aria-hidden="true"></i>');
				getWishlist();
            }
        }
    });

	return false;
}

function addProductWishListOffline(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishListOffline.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

        if (resultado != '')
		{
            $(".produtowl" + id_prod).attr("onclick", "removeProductWishListOffline('" + id_prod + "','1');return false;");
			$(".produtowl" + id_prod).addClass("active");
            $(".produtowl" + id_prod).children('span').html('<i class="far fa-heart" aria-hidden="true"></i>');
        }
    });
}

function removeProductWishListOffline(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishListOffline.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
            if (resultado != '') {
                $(".produtowl" + id_prod).attr("onclick", "addProductWishListOffline('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
                $(".produtowl" + id_prod).removeClass("active_heart");
	            $(".produtowl" + id_prod).children('span').html('<i class="fas fa-heart" aria-hidden="true"></i>');
            }
        }
    });
}


/* ---- BEGIN COMPARADOR ----*/

function addProductComparador(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductComparador.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

        if (resultado != '')
		{
            $(".produtowl" + id_prod).attr("onclick", "removeProductComparador'" + id_prod + "','0');return false;");
            $(".produtowl" + id_prod).addClass("active_heart");
	        $(".produtowl" + id_prod).children('span').html('<i class="fas fa-heart" aria-hidden="true"></i>');
			getWishlist();
         }
    });

	return false;
}

function removeProductComparador(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductComparador.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
            if (resultado != '') {
                $(".produtowl" + id_prod).attr("onclick", "addProductComparador('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
                $(".produtowl" + id_prod).removeClass("active_heart");
	            $(".produtowl" + id_prod).children('span').html('<i class="far fa-heart" aria-hidden="true"></i>');
				getWishlist();
            }
        }
    });

	return false;
}

function addProductComparadorOffline(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductComparadorOffline.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

        if (resultado != '')
		{
            $(".produtowl" + id_prod).attr("onclick", "removeProductComparadorOffline('" + id_prod + "','1');return false;");
			$(".produtowl" + id_prod).addClass("active");
            $(".produtowl" + id_prod).children('span').html('<i class="far fa-heart" aria-hidden="true"></i>');
        }
    });
}

function removeProductComparadorOffline(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductComparadorOffline.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
            if (resultado != '') {
                $(".produtowl" + id_prod).attr("onclick", "addProductComparadorOffline('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
                $(".produtowl" + id_prod).removeClass("active_heart");
	            $(".produtowl" + id_prod).children('span').html('<i class="fas fa-heart" aria-hidden="true"></i>');
            }
        }
    });
}

/* ---- END COMPARADOR ----*/

function addProductWishList2(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishList.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

		if (resultado != '')
	    {
            $(".produtowl" + id_prod).attr("onclick", "removeProductWishList2('" + id_prod + "','0');return false;");
            $(".produtowl" + id_prod).addClass("active");
			$(".produtowl"+id_prod).html(resultado);
        }
    });
}

function removeProductWishList2(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishList.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
            if (resultado != '') {
                $(".produtowl" + id_prod).attr("onclick", "addProductWishList2('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
				$(".produtowl"+id_prod).html(resultado);
            }
        }
    });
}

function addProductWishList2Offline(id_prod) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishListOffline.php",
        data: {action: "add", prodid: id_prod}
    }).done(function (resultado) {

        if (resultado != '')
		{
            $(".produtowl" + id_prod).attr("onclick", "removeProductWishList2Offline('" + id_prod + "','0');return false;");
            $(".produtowl" + id_prod).addClass("active");
			$(".produtowl"+id_prod).html(resultado);
		}

    });
}

function removeProductWishList2Offline(id_prod, refresh) {
    $.ajax({
        type: "POST",
        url: SITE_HTTP_AJAX_URL + "addRemoveProductWishListOffline.php",
        data: {action: "remove", prodid: id_prod}
    }).done(function (resultado) {
        if (refresh == 1) {
            window.location.reload();
        } else {
           if (resultado != '')
		   {
                $(".produtowl" + id_prod).attr("onclick", "addProductWishList2Offline('" + id_prod + "');return false;");
                $(".produtowl" + id_prod).removeClass("active");
				$(".produtowl"+id_prod).html(resultado);
            }
        }
    });
}


function bindCustomCheckbox(elemento,classhover)
{
  $(elemento).live('click', (function(){
    var elem=$(this);
    var input=elem.attr("for");
    if($("#"+input).is(":checked"))
    {
      $(elem).removeClass(classhover);
      $("#"+input).attr("checked",false);
    }
    else
    {
      $(elem).addClass(classhover);
      $("#"+input).attr("checked",true);
    }
  }));
}

function validarNovoTestemunho(){

  var nome=$("#testemunho_nome").val();
  var local=$("#testemunho_local").val();
  var pais=$("#testemunho_pais").val();
  var msg=$("#testemunho_mensagem").val();
  var res=true;
  if(nome==""){
    $("#testemunho_nome").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#testemunho_nome").css("background-color","#FFF");
  }
  if(local==""){
    $("#testemunho_local").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#testemunho_local").css("background-color","#FFF");
  }
  if(pais==""){
    $("#testemunho_pais").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#testemunho_pais").css("background-color","#FFF");
  }
  if(msg==""){
    $("#testemunho_mensagem").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#testemunho_mensagem").css("background-color","#FFF");
  }

  return res;
}

function validarNovoComentario(){
  var nome=$("#comentario_nome").val();
  var local=$("#comentario_local").val();
  var pais=$("#comentario_pais").val();
  var msg=$("#comentario_mensagem").val();
  var res=true;
  if(nome==""){
    $("#comentario_nome").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_nome").css("background-color","#FFF");
  }
  if(local==""){
    $("#comentario_local").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_local").css("background-color","#FFF");
  }
  if(pais==""){
    $("#comentario_pais").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_pais").css("background-color","#FFF");
  }
  if(msg==""){
    $("#comentario_mensagem").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_mensagem").css("background-color","#FFF");
  }

  return res;
}
function validarNovaNotificao(){
  var nome=$("#comentario_nome").val();
  var local=$("#comentario_local").val();
  var pais=$("#comentario_pais").val();
  var msg=$("#comentario_mensagem").val();
  var res=true;
  if(nome==""){
    $("#comentario_nome").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_nome").css("background-color","#FFF");
  }
  if(local==""){
    $("#comentario_local").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_local").css("background-color","#FFF");
  }
  if(pais==""){
    $("#comentario_pais").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_pais").css("background-color","#FFF");
  }
  if(msg==""){
    $("#comentario_mensagem").css("background-color","#ffffe5");
    res=false;
  }else{
    $("#comentario_mensagem").css("background-color","#FFF");
  }
  if(res==true){
    parent.jQuery.fancybox.close();
  }

  return res;
}

function AddAllWishlist(url){
  var prods="";
  var pos=0;
  $('input[name="prodid"]').each(function(){
    var prodid=$(this).val();
    if(pos==0) prods+=prodid;
    else prods+=","+prodid;
    pos++;
  });
 $.ajax({
    type: "GET",
    url: SITE_HTTP_URL+"pages.php",
      data: { page_id:6, prod_add_wish: prods,prod_add_obj_wish:prods }
    }).done(function(resultado){
      openPage(url,"_top");
    });
}
function openPage(url,target){
  window.open(url,target);
}

function validarLogin(field)
{
	$m_email=$("#popm_email_"+field);
	$m_pw   =$("#popm_password_"+field);

	if($.trim($m_email.val())!="" && $.trim($m_pw.val())!="")
	{
		return true;
	}
	if($.trim($m_email.val())=="")
	{
		Apprise(key_aviso_email[LINGUA]);
		return false;
	}
	if($.trim($m_pw.val())=="")
	{
		Apprise(key_aviso_pw[LINGUA]);
	}
	return false;
}

function validarRecuperarPassword()
{
  $m_email =$("#poprecm_email");

  if($.trim($m_email.val())!="")
  {
    return true;
  }
  else
  {
    Apprise(key_aviso_email[LINGUA]);
    return false;
  }
  return false;
}

function SwitchMenuH(obj)
{
	if(document.getElementById && document.getElementById("menu_lateral1"))
	{
		var al=document.getElementById('sub'+obj);
		var br=document.getElementById("menu_lateral1").getElementsByTagName("div");
		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++){if (br[i].className=="submenu"){ br[i].style.display="none";}}
			al.style.display="block";
		}
	}
}


function CollapedFaq() {
	$('.accordion').on('show.bs.collapse', function () {
		$('.accordion .in').collapse('hide');
	});
}

function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function getVoucherResults(str)
{
	if(str=='')
	{
		$("#search_hinters").hide();
	}
	else
	{
		$.ajax({
			beforeSend: function()
			{
				if($('.load_vouchercls').length) $(".load_vouchercls").html('<img src="layout/'+PROJETO+'/images/loading.gif" />');
				else $(".vouchercls").after('<div style="display:inline-block;padding: 0;" class="load_vouchercls"><img src="layout/'+PROJETO+'/images/loading.gif" /></div>');
			},
			url: SITE_HTTP_AJAX_URL+"getCarrinhoVoucher.php",
			dataType:"html",
			data: {q: str},
			method: "GET",
			async: true,
			success: function(data)
			{
				if(data !== null)
				{
					if(data=='OK')
					{
						// $('#promo_voucher2').val(str);
						// setTimeout(function(){$('#codigo_pro').submit();},500);
						setTimeout(function(){$('#encomendas').submit();},500);
						$(".load_vouchercls").html('<img src="layout/'+PROJETO+'/images/ios-checkmark-empty.svg" height="25" />');
					}
					else $(".load_vouchercls").html('<div style="padding-top: 5px;padding-left: 12px;font-size: 11px; color:#FF0000">'+data+'</div>');
				}
				else
				{
					$(".load_vouchercls").html('<img src="layout/'+PROJETO+'/images/ios-close-empty.svg" height="40"  />');
				}
			},
			error: function()
			{
				$(".vouchercls").after('<div style="display:inline-block;padding: 0;" class="load_vouchercls"><img src="layout/'+PROJETO+'/images/ios-close-empty.svg"  height="40"  /></div>');
			}
		});
	}
}

function show_hide_filtros()
{
	var x = document.getElementById("filtros_left");
	if (x.style.display != "block")
	{
		//x.style.display = "block";

		$("#filtros_left").slideDown(500);

		///$("#filtros_left").css("display", "block").css("opacity", "1").unbind("transitionend webkitTransitionEnd oTransitionEnd otransitionend");


	}
	else
	{
		//$("#filtros_left").css("opacity", "0").on('transitionend webkitTransitionEnd oTransitionEnd otransitionend', HideTheElementAfterAnimation);

		//x.style.display = "none";

		$("#filtros_left").slideUp(500);
	}
}


/**************** MENUS ADERITO ************/

function Mobile_openMenu()
{
	$("body").addClass('mmenu-active');
}
function Mobile_closeMenu()
{
	$("body").removeClass('mmenu-active');
}

/*
function(e) {
  var $this = $(this);
  $(this).parent().next().slideToggle(300).parent().toggleClass("show");
  e.preventDefault();
}
*/
// Get all anchor elements with a specific class name


$(function() {                       //run when the DOM is ready
  $(".nav-link").click(function() {  //use a class, since your ID gets mangled
    $('.nav-tabs a').removeClass('active in');
    $(this).addClass('active in');      //add the class to the clicked element
    var link = $(this).attr('href');
    $('.tab-content div').removeClass('active');
    $('.tab-content '+link).addClass('active');
  });


  /*$(".toggle-btn").click(function() {  //use a class, since your ID gets mangled
    var $this = $(this);
	  $(this).parent().next().slideToggle(300).parent().toggleClass("show");
  });*/

   $(".toggle").click(function() {  //use a class, since your ID gets mangled
    var $this = $(this);
	  $(this).next().slideToggle(300).parent().toggleClass("show");
  });



});


//----------------
document.addEventListener("DOMContentLoaded", function () {
    var hasSubmenusNew = document.querySelectorAll(".has-submenu_new");

    hasSubmenusNew.forEach(function (hasSubmenu) {
        hasSubmenu.addEventListener("click", function (event) {
            // event.preventDefault();
            var megamenuNews = document.querySelectorAll(".megamenu_new");
            megamenuNews.forEach(function (megamenuNew) {
                if (megamenuNew !== hasSubmenu.querySelector(".megamenu_new")) {
                    megamenuNew.classList.remove("megamenu_new_show");
                }
            });

            var megamenuNew = this.querySelector(".megamenu_new");
            megamenuNew.classList.toggle("megamenu_new_show");
        });
    });

    document.addEventListener("click", function (event) {
        var isInsideHasSubmenu = Array.from(hasSubmenusNew).some(function (hasSubmenu) {
            return hasSubmenu.contains(event.target);
        });

        if (!isInsideHasSubmenu) {
            var megamenuNews = document.querySelectorAll(".megamenu_new");
            megamenuNews.forEach(function (megamenuNew) {
                megamenuNew.classList.remove("megamenu_new_show");
            });

            
        }
    });
});




function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Link copiado!');
        }, function(err) {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    var textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.style.position = "fixed"; 
    textarea.style.opacity = "0";
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
        document.execCommand('copy');
        alert('Link copiado!');
    } catch (err) {
        alert('Não foi possível copiar');
    }
    document.body.removeChild(textarea);
}










function debounce(fn, delay) {
  var timer;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function(){ fn.apply(context, args); }, delay);
  };
}

function showHintsSearch2()
{
  var query = $('#search_query_top2').val().trim();
  if (query.length === 0 || query.toLowerCase() === 'o que procura?') {
    $('#search_resultados').hide();
    //$('#search_populares').show();
    return;
  }

  $.ajax({
    url: SITE_HTTP_AJAX_URL + 'ajax_search_resultados.php',
    method: 'GET',
    data: { q: query },
    success: function (data) {
      //$('#search_populares').hide();
      $('#search_resultados').html(data).show();
    }
  });
}
var debouncedShowHintsSearch2 = debounce(showHintsSearch2, 250);
function showSearchPopulares()
{
  var $input = $('#search_query_top2');
  var val = $input.val().trim();
  if (val.length === 0 || val.toLowerCase() === 'o que procura?') {
    if ($('#search_populares').is(':visible')) return;
    $.ajax({
      url: SITE_HTTP_AJAX_URL + 'ajax_search_populares.php',
      method: 'GET',
      success: function (data) {
        $('#search_resultados').hide();
        $('#search_populares').html(data).show();
      }
    });
  } else {
    showHintsSearch_novo();
  }
}

$(function() {
  $('#search_query_top2').off('keyup').on('keyup', debouncedShowHintsSearch2);
  $('#search_query_top2').off('focus').on('focus', function() {
    if (this.value === 'o que procura?') this.value = '';
    //showSearchPopulares();
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('#search_query_top2, #search_resultados, #search_populares').length) {
      $('#search_resultados, #search_populares').hide();
    }
  });
});