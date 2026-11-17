/* Search - Live*/
function ImprimeHtml(strid) 
{
    var prtContent = document.getElementById(strid);
    var WinPrint = window.open('', '', 'left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write('<!DOCTYPE html><!--[if IE 9]><html xmlns="http://www.w3.org/1999/xhtml" lang="pt" class="ie ie9"> <![endif]--><!--[if gt IE 9]><!--><html xmlns="http://www.w3.org/1999/xhtml" lang="pt"><!--<![endif]--><head>');
    WinPrint.document.write('<link rel="stylesheet" href="layout/default/html5/reset.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="external_api/bootstrap/css/bootstrap.min.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/css_external/global.css" media="all"/>');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/template.css" type="text/css" media="all" />');
    WinPrint.document.write('<link rel="stylesheet" href="layout/'+PROJETO+'/css/template_cart.css" type="text/css" media="all" />');
    WinPrint.document.write('</head><body style="font-size:11px"><div style="width:1024px;padding:10px;"><img height="77" width="196" alt="" src="layout/'+PROJETO+'/logos/logo_top.png"></div><div style="width:1024px">');
    WinPrint.document.write(prtContent.innerHTML);
	WinPrint.document.write('<div style="width:1024px;padding:10px;font-size:12px"><b>DotBright · telecomunicações</b><br>Tel: 229 429 211<br>E-mail: lojaonline@dotbright.pt<br>Maia - Avenida Visconde Barreiros 167 4470-151 Maia<br> Matosinhos - Rua Roberto Ivens, 879 4450-256 Matosinhos </div>');
	WinPrint.document.write('</body></html>');
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}

function ImprimeConteudo(strid) 
{
	var prtContent = document.getElementById(strid);
	if(prtContent)
	{
		var WinPrint = window.open('', '', 'left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}
}

var first_selected = 0;

function is_touch_device() 
{
	if(screen.width <960) return true;
	else return 'ontouchstart' in window || navigator.maxTouchPoints;
}

var first_selected = 0;



function filtros_change_multiple(_this, tipo)
{
	if(first_selected == 0) first_selected = _this.attr('datacatid');

	
	
	//	SELECT BOX	
	if(first_selected == _this.attr('datacatid'))
	{
		$("select.opcoesprod").each(function() 
		{				
			if($(this).attr('datacatid')!=first_selected) $('select[datacatid='+$(this).attr('datacatid')+']').prop('selectedIndex', 0);
			
		});	
	} 
		
		
	var codes = '';		
	total_select = $('select.opcoesprod').length;
	total_selected = 0;
	$('select.opcoesprod').find("option:selected").each(function()
	{
		cat_id = $(this).attr('data-catid');
		val_id = $(this).attr('data-valueid');
		if(cat_id > 0 && val_id > 0)
		{ 
			codes += cat_id+'-'+val_id+'|';				
			
			total_selected++;
		}
	});
		
	if(codes != '')
	{
		val_sel = _this.val();			
		prod_id = _this.find('option:selected').attr('data-prodid');
		cat_id = _this.find('option:selected').attr('data-catid');
		val_id = _this.find('option:selected').attr('data-valueid');
		select_catid = _this.attr('datacatid');			
		
		$("select.opcoesprod").not(_this).each(function() 
		{				
			select_subcatid = $(this).attr('datacatid');
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
			inf_sep = res.split('[splp]');
			inf = inf_sep[0].split('[spl]');
			// console.log(inf.length);
			for(var a=0; a<inf.length;a++)
			{
				inf2 = inf[a].split('[spl2]');
				
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
				$('#price_change').html(inf_sep[1]);
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
		inf_sep = res.split('[splp]');
		inf = inf_sep[0].split('[spl]');
		// console.log(inf.length);
		for(var a=0; a<inf.length;a++)
		{
			inf2 = inf[a].split('[spl2]');
			
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
			$('#price_change').html(inf_sep[1]);
		}
	});	
}

function filtros_change()
{
	
	$('.label_check_ps').on('click', function()
	{
		lbl_datacatid = 'sel_'+$(this).attr('id');		
		data_catid = $(this).attr('data-catid');		
		console.log('#sel'+data_catid+' option[id='+lbl_datacatid+']');
		$('#sel'+data_catid+' option[id='+lbl_datacatid+']').prop("selected", "selected").trigger('change');
		
	}); 
	
	$('.opcoesprod').live('change',function(e)
	{
		console.log('select changed');
		
		if(first_selected == 0) first_selected = $(this).attr('datacatid');
		
		if(first_selected == $(this).attr('datacatid'))
		{
			$("select.opcoesprod").each(function() 
			{				
				if($(this).attr('datacatid')!=first_selected) $('select[datacatid='+$(this).attr('datacatid')+']').prop('selectedIndex', 0);				
			});	
		} 
		
		
		var codes = '';		
		total_select = $('select.opcoesprod').length;
		total_selected = 0;
		$('select.opcoesprod').find("option:selected").each(function()
		{
			cat_id = $(this).attr('data-catid');
			val_id = $(this).attr('data-valueid');
			if(cat_id > 0 && val_id > 0)
			{ 
				codes += cat_id+'-'+val_id+'|';				
				
				total_selected++;
			}
		});
		
		
		
		if(codes != '')
		{
			val_sel = $(this).val();			
			prod_id = $(this).find('option:selected').attr('data-prodid');
			cat_id = $(this).find('option:selected').attr('data-catid');
			val_id = $(this).find('option:selected').attr('data-valueid');
			select_catid = $(this).attr('datacatid');			
			
			$("select.opcoesprod").not($(this)).each(function() 
			{				
				select_subcatid = $(this).attr('datacatid');
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
				inf_sep = res.split('[splp]');
				inf = inf_sep[0].split('[spl]');
				// console.log(inf.length);
				for(var a=0; a<inf.length;a++)
				{
					inf2 = inf[a].split('[spl2]');
					
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
									lbl_id = $(this).attr('id');
									$('#'+lbl_id).hide();					
									
									$('#wrap_'+inf2[0]+' option').each(function()
									{
										data_catid = $(this).attr('data-catid');
										data_valueid = $(this).attr('data-valueid');
										
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
					$('#price_change').html(inf_sep[1]).promise().done(function()
					{
						// alert(1);
						
						
					});
				}
				// else $('.price').html('<div class="c"></div><div class="prdprice_right"></div><div class="c"></div><div class="c"></div>');
			});	
		} 
		// setupLabel2();
	});	
}

function filtros_update()
{
    var codes = '';   
    total_select = $('select.opcoesprod').length;
    total_selected = 0;
    $('select.opcoesprod').find("option:selected").each(function()
    {
      cat_id = $(this).attr('data-catid');
      val_id = $(this).attr('data-valueid');
      prod_id = $(this).attr('data-prodid');
      if(cat_id > 0 && val_id > 0)
      { 
        codes += cat_id+'-'+val_id+'|';       
        total_selected++;
      }
    });
    if(codes != '')
    {
      //val_sel = $(this).val();      
      //prod_id = $(this).find('option:selected').attr('data-prodid');
      //cat_id = $(this).find('option:selected').attr('data-catid');
      //val_id = $(this).find('option:selected').attr('data-valueid');
      //select_catid = $(this).attr('datacatid');


      $.ajax(
      {
        type: 'POST',
        url: SITE_HTTP_AJAX_URL + 'ajaxShop_filtros.php',
        data: { codes:codes, prodid:prod_id}
      })
      .done(function(res) 
      {
        inf_sep = res.split('[splp]');
        inf = inf_sep[0].split('[spl]');
        // console.log(inf.length);

        for(var a=0; a<inf.length;a++)
        {
          inf2 = inf[a].split('[spl2]');
          if($('select[datacatid='+inf2[0]+']').find('option:selected').val()!=''){}
          else
          {
            $('#wrap_'+inf2[0]).html(inf2[1]);
          }
        } 

        if(total_selected==total_select)
        {
          $('#price_change').html(inf_sep[1]);
        }
        // else $('.price').html('<div class="c"></div><div class="prdprice_right"></div><div class="c"></div><div class="c"></div>');
      }); 
    }  
}

function go(url)
{
	window.open (url,'_self',false)
}
function getLinguas(key)
{
	var result = '';
	$.ajax({
		type: "GET",
		async: !1,
		url: SITE_HTTP_AJAX_URL + "getGlobals.php",
		data: {k_lingua: key}
	})
	.done(function (resultado) 
	{
		result = resultado;
	});	
	
	return result;
}
function getPageWidth()
{
	if (self.innerWidth) {return self.innerWidth; }
	if (document.documentElement && document.documentElement.clientWidth) {return document.documentElement.clientWidth; }
	if (document.body) {return document.body.clientWidth; }
}
function updatepagewith()
{
	pagewidth = getPageWidth();
}
var pagewidth = getPageWidth();
window.addEventListener("resize", updatepagewith);
var is_mobile=0;
if(pagewidth<=950)is_mobile=1;

function showHintsSearch()
{
	console.log(is_mobile);
	
	if(is_mobile==0)
	{
		if($("#search_query_top").val() != '')
		{
			var str = $("#search_query_top").val();
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
		}
	}






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
});

$(window).on('resize', function()
{
	topo_redimensiona();
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
	var w = 0;
	var h = 0;
	//IE
	if(!window.innerWidth)
	{
	//strict mode
		if(!(document.documentElement.clientWidth == 0))
		{
			w = document.documentElement.clientWidth;
			h = document.documentElement.clientHeight;
		}
		//quirks mode
		else
		{
			w = document.body.clientWidth;
			h = document.body.clientHeight;
		}
	}
	//w3c
	else
	{
		w = window.innerWidth;
		h = window.innerHeight;
	}
	return h;
}
function showdeadcenterdiv(Xwidth,Yheight,divid) 
{
	var scrolledX, scrolledY; 
	if(self.pageYOffset)
	{ 
		scrolledX = self.pageXOffset; 
		scrolledY = self.pageYOffset; 
	} 
	else if(document.documentElement && document.documentElement.scrollTop) 
	{ 
		scrolledX = document.documentElement.scrollLeft; 
		scrolledY = document.documentElement.scrollTop; 
	} 
	else if(document.body) 
	{ 
		scrolledX = document.body.scrollLeft; 
		scrolledY = document.body.scrollTop; 
	} 

	var centerX, centerY; 
	if(self.innerHeight) 
	{ 
		centerX = self.innerWidth; 
		centerY = self.innerHeight; 
	} 
	else if(document.documentElement && document.documentElement.clientHeight) 
	{ 
		centerX = document.documentElement.clientWidth; 
		centerY = document.documentElement.clientHeight; 
	} 
	else if( document.body ) 
	{ 
		centerX = document.body.clientWidth; 
		centerY = document.body.clientHeight; 
	} 

	var leftOffset = scrolledX + (centerX - Xwidth) / 2; 
	var topOffset =  (scrolledY) + (centerY - Yheight) / 2;
	
	// var o=document.getElementById(divid); 
	// var r = o.style; 
	// r.position = 'absolute'; 
	// r.top = topOffset + 'px';
	// r.left = leftOffset + 'px';
	return [topOffset, leftOffset];
}
$.fn.center_window = function () 
{
	this.css("position","absolute");
	dt = showdeadcenterdiv(290,198,'buy_frm');
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
		$.ajax({
			beforeSend: function()
			{
				// $(".se-pre-con").show();
				$(".search").append('<div style="position:absolute;top:12px;left:10px;opacity:0.2" class="load_sea"><img src="layout/'+PROJETO+'/images/loading.gif" /></div>');
			},
			url: SITE_HTTP_AJAX_URL+"livesearch.php",
			dataType:"html",
			data: {q: str},
			method: "GET",
			async: false,
			success: function(data)
			{
				if(data !== null)
				{
					$("#search_hinters").html(data);
					$("#search_hinters").show();
					$('.load_sea').remove();

					// $("#search_default").hide();
					// $("#search_results_wrapper").html(data);
				}
				$('.load_sea').remove();
			},
			error: function()
			{
				// $(".se-pre-con").fadeOut("slow");
				// $("#search_field").show();
				// $("#search_field").focus();
				$('.load_sea').remove();
			}
		});
	}
}
function topo_redimensiona()
{

/* 	if($(document).scrollTop() > $('header').outerHeight())
	{

			if($('header').data('size') == 'big')
			{
				$('header').data('size','small');
				$('.gap').stop().animate({height:'100px'},600);
				$('header').stop().animate({height:'100px'},600);
				// $('.logo img').stop().animate({height:'35px'},600);
				// $('nav').stop().animate({top:'50px'},600);
				//$('nav').css({'position' : 'fixed', 'width' : '100%', 'top' : $('header').height(), 'background' : '#FFFFFF', 'z-index' : '9999999'});

			}

	}
	else
	{
		if($('header').data('size') == 'small')
        {
            $('header').data('size','big');
            $('.gap').stop().animate({height:'200px'},600);
            $('header').stop().animate({height:'200px'},600);
			// $('.logo img').stop().animate({height:'57px'},600);
			// $('nav').stop().animate({top:'80px'},600);
        } 
	}  */
}

$(document).ready(function()
{
	$('#telemovel').on('change', function(){
		var telemovel = $(this).val();
		$.ajax({
			url: SITE_HTTP_AJAX_URL+"simulador_usados_capacidade.php",
			dataType:"html",
			data: {telemovel: telemovel},
			method: "GET",
			async: true,
			success: function(data)
			{
				$('#capacidade').html(data);
				$('#gama').html('');
				$('#valor').val('');
			},
			error: function()
			{
				
			}
		});
	});

	$('#capacidade').on('change', function(){
		var telemovel = $('#telemovel').val();
		var capacidade = $(this).val();
		$.ajax({
			url: SITE_HTTP_AJAX_URL+"simulador_usados_gama.php",
			dataType:"html",
			data: {capacidade: capacidade, telemovel: telemovel},
			method: "GET",
			async: true,
			success: function(data)
			{
				$('#gama').html(data);
			},
			error: function()
			{
				
			}
		});
	});

	$('#gama').on('change', function(){
		var telemovel = $('#telemovel').val();
		var capacidade = $('#capacidade').val();
		var gama = $(this).val();
		$.ajax({
			url: SITE_HTTP_AJAX_URL+"simulador_usados_valor.php",
			dataType:"html",
			data: {capacidade: capacidade, telemovel: telemovel, gama: gama},
			method: "GET",
			async: true,
			success: function(data)
			{
				$('#valor').val(data);
			},
			error: function()
			{
				
			}
		});
	});




	if(PRODUCT_DIA != 0)
	{
		
		$('.clock').countdown(PRODUCT_DIA+' 23:59:59').on('update.countdown', function(event) 
		{ 
			var totalHours = event.offset.totalDays * 24 + event.offset.hours; 
			$(this).html(event.strftime('%H:%M:%S'));
		})
		.on('finish.countdown', function(event) 
		{
			$(".ofertas").hide();
		});
		
		$(".ofertas").show();
	}
	
	
	filtros_change();
	

	
	$(".chzn-select").chosen({disable_search_threshold: 10,width: '100%' }); $(".chzn-select-deselect").chosen({allow_single_deselect:true});
	
	var delay = (function() {var timer = 0; return function(callback, ms) {  clearTimeout(timer);   timer = setTimeout(callback, ms);  };})();
	$('#search').keyup(function() { var target = $(this); delay(function() {getSearchResults(target.val()); }, 1000);});

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
	$('.fancybox-img').fancybox({'openEffect': 'elastic','closeEffect': 'elastic'});
	$(".prd_whish").tooltipster({trigger:'click',animation: 'fade',
	delay: 200,
	theme: 'tooltipster-default','functionReady':function(){
    var el=$(this);
    setTimeout(function(){
      el.tooltipster('hide',function(){
           el.tooltipster('content',el.attr("title"));
      });
     
    },1000)
   }});
   // $('#fancy_zoom').fancybox();

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

	$('.fancy_stock').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 300, maxHeight : 170, scrolling : 'no', type : 'html', onComplete: function() {/* $("#fancybox-wrap").stop().animate({'top':'194px',}, 0); */ $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('.fancy_content').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width:'100%', maxWidth : 650, maxHeight : 450, scrolling : 'no', type : 'html', onComplete: function() { $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});


	$(".prodby").live('click', function()
	{
		
		var prodid = $(this).children('input[name="prodid"]').val();
		var prodfiltros = $(this).children('input[name="prodfiltros"]').val();
		var qtd = 1;
		if(prodfiltros) window.open(SITE_HTTP_URL+prodfiltros,'_self');
		else adicionarCarrinhoProduto(prodid,qtd);
	});
	 
	if((marca_ativa > 0 || familia_ativa > 0) && (produto_ativo == '' || produto_ativo == '0'))
	{
	}
	else
	{
		// $(".prd_d").hover(function()
		// {
			// $(this).find(".prd_layer2").animate({height: 238}, 'slow');
		// }
		// ,function(){
			// $(".prd_layer2").attr('style','height:1px');
		// });     
	}

  bindCustomCheckbox(".customCheckboxCriarConta","customCheckboxCriarContaOn");
  bindCustomCheckbox(".customCheckboxAceitarCompra","customCheckboxAceitarCompraOn");
  bindCustomCheckbox(".customCheckboxEmbrulho","customCheckboxEmbrulhoOn");
  bindCustomCheckbox(".customCheckboxOferta","customCheckboxOfertaOn");
  bindCustomCheckbox(".customCheckboxNewsletter","customCheckboxNewsletterOn");
  bindCustomCheckbox(".customCheckboxNotificao","customCheckboxNotificaoOn");

	$(".customCheckboxDadosDiferentes").click(function(e)
	{
		var elem = $(this);
		var input = elem.attr("for");

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
		document.encomendas.submit();
		e.preventDefault();
	});


	if(!("placeholder" in document.createElement("input"))) 
	{
		$("input[placeholder], textarea[placeholder]").each(function() 
		{
			var val = $(this).attr("placeholder");
			
			if (this.value == '') this.value = val;
			
			$(this).focus(function() 
			{
				if ( this.value == val ) this.value = '';
			})
			.blur(function()
			{
				if ($.trim(this.value) == '') this.value = val;
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

	if(document.getElementById('description')) showProductTab('description');
	else if(document.getElementById('comofunciona')) showProductTab('comofunciona');
	else if(document.getElementById('envio')) showProductTab('envio');
	else if(document.getElementById('customizado')) showProductTab('customizado');
	else if(document.getElementById('relacionados')) showProductTab('relacionados');
	else if(document.getElementById('sugestoes')) showProductTab('sugestoes');
	
	// if (typeof microfiche == 'function') 
	// {
		// $('.brands-sld').microfiche({ autoplay: 5,cyclic: true });
	// }
	
	
	// var dd = $('.vticker').easyTicker({
		// direction: 'up',
		// easing: 'easeInOutBack',
		// speed: 'slow',
		// interval: 2000,
		// height: '170px',
		// visible: 2,
		// mousePause: 1,
		// controls: {
			// up: '.up',
			// down: '.down',
			// toggle: '.toggle',
			// stopText: 'Stop !!!'
		// }
	// }).data('easyTicker');
	if(document.getElementById('wp_other')) $('#wp_other').microfiche({'bullets':false, 'buttons':true});
});

function calcularPosicaoChat()
{
	if($('header').length>0)
	{
		var anchor = $('header').offset();
		var largura = $('header').width();
		var left_pos = (anchor.left+largura)-308;
		$("#chat_unopen").css("left", left_pos+"px");
		$("#chat_open_wrapper").css("left", left_pos+"px");
	}
}
function calcularPosicaoComprarFrame()
{
	if($('.main_content').length>0)
	{
		var anchor = $('.main_content').offset();
		var largura = $('.main_content').width();
		var left_pos = (anchor.left+largura)-308;		
		$('#buy_frm').center_window();
	}
}
function toggleLoginBox()
{
	$.fancybox({
        width: 420,
        height: 220,
        autoSize: false,
        href: SITE_HTTP_AJAX_URL + 'ajax_login.php',		
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
		var pagina = ((((currentscroll + 940) * 1) / 940) + 1);		
		var inicio = pagina - 1;inicio = inicio * 10;
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
		var pagina = ((((currentscroll + 944) * 1) / 944) + 1);		
		var inicio = pagina - 1;inicio = inicio * 4;
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
		var pagina = ((((currentscroll + 904) * 1) / 904) + 1);		
		var inicio = pagina - 1;inicio = inicio * 4;
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
		var al = document.getElementById(pos+'_ct');
		var br = document.getElementById("produto_conteudos").getElementsByTagName("div");

		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++)
			{
				if (br[i].className=="ptab"){ br[i].style.display = 'none';}				
				if (br[i].className=="tabs active"){ br[i].className = 'tabs';}	
				
			}
			al.style.display="block";
			$("#"+pos).addClass("tabs active");
		}
	}
}

function addProductWishList(id_prod){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishList.php",
        data: { action: "add",prodid:id_prod }
      }).done(function(resultado) {

        if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","removeProductWishList('"+id_prod+"','0')");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist_added.png");
            $(".produtowl"+id_prod).attr("title","O produto foi removido com sucesso dos seus favoritos");

          }
      });
}
function removeProductWishList(id_prod,refresh){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishList.php",
        data: { action: "remove",prodid:id_prod }
      }).done(function(resultado) {
        if(refresh==1){
          window.location.reload();
        }else{
          if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","addProductWishList('"+id_prod+"')");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist.png");
            $(".produtowl"+id_prod).attr("title","O produto foi adicionado com sucesso aos seus favoritos");
          }
        }
      });
}
function addProductWishListOffline(id_prod){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishListOffline.php",
        data: { action: "add",prodid:id_prod }
      }).done(function(resultado) {

        if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","removeProductWishListOffline('"+id_prod+"','0');");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist_added.png");
            $(".produtowl"+id_prod).attr("title","O produto foi removido com sucesso dos seus favoritos");
          }
      });
}
function removeProductWishListOffline(id_prod,refresh){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishListOffline.php",
        data: { action: "remove",prodid:id_prod }
      }).done(function(resultado) {
        if(refresh==1){
          window.location.reload();
        }else{
          if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","addProductWishListOffline('"+id_prod+"');");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist.png");
            $(".produtowl"+id_prod).attr("title","O produto foi adicionado com sucesso aos seus favoritos");
          }
        }
      });
}
function addProductWishList2(id_prod){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishList.php",
        data: { action: "add",prodid:id_prod }
      }).done(function(resultado) {

        if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","removeProductWishList2('"+id_prod+"','0')");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist2_added.png");
            $(".produtowl"+id_prod).attr("title","O produto foi removido com sucesso dos seus favoritos");
          }
      });
}
function removeProductWishList2(id_prod,refresh){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishList.php",
        data: { action: "remove",prodid:id_prod }
      }).done(function(resultado) {
        if(refresh==1){
          window.location.reload();
        }else{
          if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","addProductWishList2('"+id_prod+"')");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist2.png");
            $(".produtowl"+id_prod).attr("title","O produto foi adicionado com sucesso aos seus favoritos");
          }
        }
      });
}

function addProductWishList2Offline(id_prod){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishListOffline.php",
        data: { action: "add",prodid:id_prod }
      }).done(function(resultado) {

        if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","removeProductWishList2Offline('"+id_prod+"','0');");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist2_added.png");
            $(".produtowl"+id_prod).attr("title","O produto foi removido com sucesso dos seus favoritos");
          }
      });
}
function removeProductWishList2Offline(id_prod,refresh){
  $.ajax({
      type: "POST",
      url: SITE_HTTP_AJAX_URL+"addRemoveProductWishListOffline.php",
        data: { action: "remove",prodid:id_prod }
      }).done(function(resultado) {
        if(refresh==1){
          window.location.reload();
        }else{
          if(resultado==0){
            $(".produtowl"+id_prod).attr("onclick","addProductWishList2Offline('"+id_prod+"');");
            $(".produtowl"+id_prod).children("img").attr("src","layout/wishlist2.png");
            $(".produtowl"+id_prod).attr("title","O produto foi adicionado com sucesso aos seus favoritos");
          }
        }
      });
}

function bindCustomCheckbox(elemento,classhover){
  $(elemento).click(function(){
    var elem=$(this);
    var input=elem.attr("for");
    
    if($("#"+input).is(":checked")){
      $(elem).removeClass(classhover);
      $("#"+input).attr("checked",false);
    }else{
      $(elem).addClass(classhover);
      $("#"+input).attr("checked",true);
    }
  }); 
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

function validarLogin()
{
  $m_email =$("#popm_email");
  $m_pw    =$("#popm_password");

  if($.trim($m_email.val())!="" && $.trim($m_pw.val())!="")
  {
    return true;
  }
  if($.trim($m_email.val())=="")
  {
    Apprise("Por favor insira o seu email");  
    return false;
  }
  if($.trim($m_pw.val())=="")
  {
    Apprise("Por favor insira a sua password");  
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
    Apprise("Por favor insira o seu email");  
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