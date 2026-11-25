function scrolling_bts()
{
/* 	var nav = $('#slidebottom');
	
	console.log($('#wp').height()+' > '+$(window).height());
	if($('#wp').height() > $(window).height())
	{
		if ($(window).scrollTop() < $('#wp').height())
		{ 
			nav.removeClass("f-nav2");			
			nav.addClass("f-nav");
		}
		else
		{ 
			nav.removeClass("f-nav");
			nav.addClass("f-nav2");			
		}
	}
	else
	{ 
		nav.addClass("f-nav2");
		nav.removeClass("f-nav");
	} */
}
$(window).bind('hashchange',function(event)
{
    var hash = location.hash.replace('#','');
    if(hash == '') $(window).scrollTop(lastPos);
    window.open(hash, '_self');
}); 
function remove_file(file, obj, obj2)
{
	var filex = str_replace(SITE_HTTP_URL, SITE_SHELL_URL, file);
	
	$.get('remove_file.php?dp='+new Date()+'&file_del='+filex, function(data)
	{ 
		$(obj).remove()
		$("a[href$='"+file+"']").remove();	
	});

	return false;
}
function remove_file2(file, obj, obj2)
{
	var filex = str_replace(SITE_HTTP_URL, SITE_SHELL_URL, file);
	
	$.get('remove_file2.php?dp='+new Date()+'&file_del='+filex, function(data)
	{ 
		$(obj).remove()
		$("a[href$='"+file+"']").remove();	
	});

	return false;
}


function validateQty(event, field, field2) 
{

    var englishAlphabetDigitsAndWhiteSpace = /[_a-z0-9]/g;   
  
    var key = String.fromCharCode(event.which);
	// alert(event.keyCode)
	
    if (event.keyCode == 9 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || englishAlphabetDigitsAndWhiteSpace.test(key)) 
	{		
		return true;
    }

    return false;
}


$( document ).ready(function() 
{   
    // $(window).load(function (){scrolling_bts();});	
	// $(window).scroll(function (){scrolling_bts();});
	
	
	
	
	$('#chk_all').live('click', function() { if($(this).attr('checked')){ $('.cb').attr('checked','checked'); }else{ $('.cb').removeAttr('checked'); } setupLabel();});
	$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});	
	
	$('a.pretourl_small').after(function()
	{		
		$(this).after('<a href="#" class="del_file redurl_small" onclick="if(confirm(\'Tem a certeza que quer eliminar este ficheiro?\')){remove_file(\''+$(this).attr('href')+'\', $(this)); return false;}return false;">&nbsp;&nbsp;|&nbsp;&nbsp;Remover</a>');
	}
	);
	
	$('a.pretourl_small2').after(function()
	{		
		$(this).after('<a href="#" class="del_file redurl_small" onclick="if(confirm(\'Tem a certeza que quer eliminar este ficheiro?\')){remove_file2(\''+$(this).attr('href')+'\', $(this)); return false;}return false;">&nbsp;&nbsp;|&nbsp;&nbsp;Remover</a>');
	}
	);
	
	$(".tabtop").click(function() 
	{
		if($('.tabtop-open').is(':visible'))
		{
			$( ".tabtop-open" ).hide('fast');			
			$( "#imgtab" ).attr('src','img/aberto.png');
		}
		else
		{
			$( ".tabtop-open" ).show('fast');
			$( "#imgtab" ).attr('src','img/fechado.png');			
		}
	});
	
	$('body').addClass('has-js');
	$('.label_check,.label_check2, .label_radio').click(function(){
		setupLabel();
	});
	
	$('.addRow').click(function () 
	{		
		var num = Number($("#total_linhas_renovacoes").val());
		
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#renovacoes").clone(true);
		clone.attr('id', 'renovacoes' + quantity).css('display','block');
		clone.find('.RENOVACAO_ID').each(function()	{this.setAttribute('id', 'RENOVACAO_ID'+quantity);this.setAttribute('name', 'RENOVACAO_ID[]');this.setAttribute('value', quantity);});
		clone.find('.RENOVACAO_DATA').each(function() {this.setAttribute('id', 'RENOVACAO_DATA'+quantity);this.setAttribute('name', 'RENOVACAO_DATA'+quantity);this.value='';});
		clone.find('.RENOVACAO_NUMERO').each(function() {this.setAttribute('id', 'RENOVACAO_NUMERO'+quantity);this.setAttribute('name', 'RENOVACAO_NUMERO'+quantity);this.value='';});
		clone.find('.BOLETIM_DATA').each(function() {this.setAttribute('id', 'BOLETIM_DATA'+quantity);this.setAttribute('name', 'BOLETIM_DATA'+quantity);this.value='';});
		clone.find('.BOLETIM_EXT').each(function() {this.setAttribute('id', 'bloco_img_ext'+quantity);this.setAttribute('name', 'bloco_img_ext'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_renovacoes');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_renovacoes").val(quantity);
	});	
	
	setupLabel();
	if (typeof tabsel != 'undefined')  infinittab(tabsel);
	if (typeof tabsel2 != 'undefined') infinittab2(tabsel2);
	if (typeof tabsel3 != 'undefined') infinittab3(tabsel3);
});
function remove_renova(num)
{
	$('#renovacoes'+num).remove();
	$("#total_linhas_renovacoes").val($("#total_linhas_renovacoes").val()-1);
}
function pesquisa(pagina)
{
	var hash='';
	var p1 = $('#bloco_visivel').val();
	var p3 = $('#DESIGNACAO').val();
	var p4 = $('#DATA_PEDIDO').val();
	var p2 = $('#listademarcasx').val();
	
	if(p1) hash = '&bloco_visivel='+p1;
	if(p3) hash += '&DESIGNACAO='+p3;
	if(p4) hash += '&DATA_PEDIDO='+p4;
	if(p2) hash += '&ATIVIDADE='+p2;
	
	if(pagina)
	{
		if(hash) window.location.hash = pagina+'&p=1'+hash;
		else window.open(pagina,'_self');	
	}
	else
	{
		if(hash) window.location.hash = 'list.php?p=1'+hash;
		else window.open('list.php','_self');
	}
}
function setupLabel() 
{
	if ($('.label_check input').length) 
	{
		$('.label_check').each(function(){ 
			$(this).removeClass('c_on');
			
			// alert($(this).children().attr('id'))
			// alert($(this).attr('id'));
			if($(this).children().attr('id')=='bloco_opcao_embalagem') $('#menutab2').css('display','none');
			if($(this).children().attr('id')=='bloco_opcao_cor') $('#menutab3').css('display','none');
			if($(this).children().attr('id')=='bloco_opcao_vinhos') $('#menutab4').css('display','none');
			if($(this).children().attr('id')=='bloco_opcao_iguarias') $('#menutab5').css('display','none');
		});
		$('.label_check input:checked').each(function()
		{ 
			$(this).parent('label').addClass('c_on');
			if($(this).attr('id')=='bloco_opcao_embalagem') $('#menutab2').css('display','block');
			if($(this).attr('id')=='bloco_opcao_cor') $('#menutab3').css('display','block');
			if($(this).attr('id')=='bloco_opcao_vinhos') $('#menutab4').css('display','block');
			if($(this).attr('id')=='bloco_opcao_iguarias') $('#menutab5').css('display','block');
		});                
	};
	if ($('.label_check2 input').length) 
	{
		$('.label_check2').each(function(){ 
			$(this).removeClass('c_on2');
		});
		$('.label_check2 input:checked').each(function(){ 
			$(this).parent('label').addClass('c_on2');
		});                
	};
	if ($('.label_radio input').length) 
	{
		$('.label_radio').each(function()
		{ 
			$(this).removeClass('r_on');
		});
		$('.label_radio input:checked').each(function(){ 
			$(this).parent('label').addClass('r_on');
		});
	};
};

function get_cs_user(value)
{
	var t=$('.result').is(':visible');
	if(t==false)
	{
		var search='';
		if($('#allarticlesearch').length != 0) search=$('#allarticlesearch').val();
		if(value==0)
		{
			$('#choose').html('<img src="img/del2.png" border="0" title="close box" />');
			$.get('get_youtube.php?dp='+new Date(), function(data) {  $('#result').show("slow"); $('.result').html(data);});
		}
		else if(value==1)
		{
			$('#choose1').html('<img src="img/del2.png" border="0" title="close box" />');
			$.get('allArticles.php?val='+value+'&allarticlesearch='+search+'&tipo=<?=$tipo?>&dp='+new Date(), function(data) {  $('#resulttop').show("slow"); $('#resulttop').html(data);});
		}
		else if(value==2)
		{
			$('#choose2').html('<img src="img/del2.png" border="0" title="close box" />');
			$.get('allArticles.php?val='+value+'&tipo=<?=$tipo?>&dp='+new Date(), function(data) {  $('#resultleft').show("slow"); $('#resultleft').html(data);});
		}
		else if(value==3)
		{
			$('#choose3').html('<img src="img/del2.png" border="0" title="close box" />');
			$.get('allArticles.php?val='+value+'&tipo=<?=$tipo?>&dp='+new Date(), function(data) {  $('#resultright').show("slow"); $('#resultright').html(data);});
		}
		else if(value==4)
		{
			$('#choose').html('<img src="img/del2.png" border="0" title="close box" />');
			$.get('allColors.php?dp='+new Date(), function(data) {  $('#result').show("slow"); $('.result').html(data);});
		}
	}
	else
	{
		$('#choose').html('<img src="img/config.png" border="0"  title="choose a video" />');
		$('#choose1').html('<img src="img/config.png" border="0"  title="choose a article" />');
		$('#choose2').html('<img src="img/config.png" border="0"  title="choose a article" />');
		$('#choose3').html('<img src="img/config.png" border="0"  title="choose a article" />');
		$('.result').hide("slow"); $('.resulttop').hide("slow"); $('.resultleft').hide("slow"); $('.resultright').hide("slow");
	}
}
function totext2(val,id, text)
{
	if(val==1) 
	{
		$('#m_toparticle_url').val(text);
		$('#toparticle_id').val(id);
	}else if(val==2) {
		$('#m_leftarticle_url').val(text);
		$('#leftarticle_id').val(id);
	}else if(val==3) {
		$('#m_rightarticle_url').val(text);
		$('#rightarticle_id').val(id);
	}else if(val==4) {
		$('#m_color').val(id);
	}		
	if(val==4) $('#choose').html('<img src="img/config.png" border="0"  title="choose a article" />');
	else $('#choose'+val).html('<img src="img/config.png" border="0"  title="choose a article" />');
	$('.result').hide("slow");
}

function infinittab(obj)
{
	if(document.getElementById && document.getElementById("wraper"))
	{	
		var al=document.getElementById('tab'+obj);
		var al2=document.getElementById('menutab'+obj);
		var br=document.getElementById("wraper").getElementsByTagName("div");
		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++)
			{
				if (br[i].className=="tabtop-open3"){ br[i].style.display="none";}
				if (br[i].className=="bigtitletab" || br[i].className=="bigtitletaboff"){br[i].className='bigtitletaboff';}
			}
			al.style.display="block";
			al2.className='bigtitletab';
			al2.style.borderLeft="#00000 1px solid";
			scrolling_bts();
			// if(getElementById('tab')
		}
	}
}

function infinittab2(obj)
{
	if(document.getElementById && document.getElementById("wraper2"))
	{
		var al=document.getElementById('xtab'+obj);
		var al2=document.getElementById('xmenutab'+obj);
		var br=document.getElementById("wraper2").getElementsByTagName("div");
		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++)
			{
				if (br[i].className=="xtabtop-open3"){ br[i].style.display="none";}
				if (br[i].className=="xbigtitletab" || br[i].className=="xbigtitletaboff"){br[i].className='xbigtitletaboff';}
			}
			al.style.display="block";
			al2.className='xbigtitletab';
			al2.style.borderLeft="#00000 1px solid";
		}
	}
}
function infinittab3(obj)
{
	if(document.getElementById && document.getElementById("wraper3"))
	{
		var al=document.getElementById('xxtab'+obj);
		var al2=document.getElementById('xxmenutab'+obj);
		var br=document.getElementById("wraper3").getElementsByTagName("div");
		if(al.style.display != "block")
		{
			for (var i=0; i<br.length; i++)
			{
				if (br[i].className=="xxtabtop-open3"){ br[i].style.display="none";}
				if (br[i].className=="xxbigtitletab" || br[i].className=="xxbigtitletaboff"){br[i].className='xxbigtitletaboff';}
			}
			al.style.display="block";
			al2.className='xxbigtitletab';
			al2.style.borderLeft="#00000 1px solid";
		}
	}
}

function str_replace(search, replace, subject, count) 
{
	var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,  f = [].concat(search),  r = [].concat(replace),  s = subject,   ra = Object.prototype.toString.call(r) === '[object Array]',   sa = Object.prototype.toString.call(s) === '[object Array]';
	s = [].concat(s);
	if (count) this.window[count] = 0;

	for (i = 0, sl = s.length; i < sl; i++) 
	{
		if (s[i] === '') continue;
		for (j = 0, fl = f.length; j < fl; j++) 
		{
			temp = s[i] + '';repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];s[i] = (temp).split(f[j]).join(repl);
			if (count && s[i] !== temp)  this.window[count] += (temp.length - s[i].length) / f[j].length;
		}
	}
	return sa ? s : s[0];
}
