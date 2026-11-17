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
$( document ).ready(function() 
{   

	$(".navlinkx").tooltip({content: function (callback) { callback($(this).prop('title').replace(new RegExp("\\|", "g"), '<br />')); }});
	
	$('#chk_all').live('click', function() { if($(this).attr('checked')){ $('.cb').attr('checked','checked'); }else{ $('.cb').removeAttr('checked'); } setupLabel();});
	$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});	
	
	$('a.pretourl_small').after(function()
	{		
		$(this).after('<a href="#" class="del_file redurl_small" onclick="if(confirm(\'Tem a certeza que quer eliminar este ficheiro?\')){remove_file(\''+$(this).attr('href')+'\', $(this)); return false;}return false;">&nbsp;&nbsp;|&nbsp;&nbspRemover</a>');
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
	$('.label_check, .label_radio, .label_check2').click(function(){
		setupLabel();
	});
	
	$('.addRow').click(function () 
	{		
		var num = Number($("#total_linhas_renovacoes").val());
		
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#LINHASPRODS").clone(true);
		clone.attr('id', 'LINHASPRODS' + quantity).css('display','block');
		clone.find('.DESCONTOS_ID').each(function()	{this.setAttribute('id', 'DESCONTOS_ID'+quantity);this.setAttribute('name', 'DESCONTOS_ID[]');this.setAttribute('value', quantity);});
		clone.find('.LINHA_DESCONTO').each(function() {this.setAttribute('id', 'LINHA_DESCONTO'+quantity);this.setAttribute('name', 'LINHA_DESCONTO'+quantity);this.value='';});
		clone.find('.RENOVACAO_NUMERO').each(function() {this.setAttribute('id', 'RENOVACAO_NUMERO'+quantity);this.setAttribute('name', 'RENOVACAO_NUMERO'+quantity);this.value='';});
		clone.find('.BOLETIM_DATA').each(function() {this.setAttribute('id', 'BOLETIM_DATA'+quantity);this.setAttribute('name', 'BOLETIM_DATA'+quantity);this.value='';});
		clone.find('.BOLETIM_EXT').each(function() {this.setAttribute('id', 'BOLETIM_EXT'+quantity);this.setAttribute('name', 'BOLETIM_EXT'+quantity);this.value='';});
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


function submitform(urlconf)
{
	if(urlconf)
	{
		document.formpromocoes.action = urlconf;
		document.formpromocoes.target = '';
		document.formpromocoes.submit();
	}
}
function submitform2(urlconf, form)
{
	var form_sb = document.getElementById(form);
	if(urlconf && form_sb)
	{	
		// alert(urlconf+' | '+form);
		form_sb.action = urlconf;
		form_sb.target = '';
		form_sb.submit();
	}
}
function remove_renova(num)
{
	$('#renovacoes'+num).remove();
	$("#total_linhas_renovacoes").val($("#total_linhas_renovacoes").val()-1);
}
function pesquisa()
{
	var hash='';
	var p1 = $('#TIPOCAMPANHA').val();
	var p2 = $('#DESIGNACAO').val();
	
	if(p1) hash = '&TIPOCAMPANHA='+p1;
	if(p2) hash += '&DESIGNACAO='+p2;
	
	if(hash) window.location.hash = 'list.php?p=1'+hash;
	else  window.open('list.php','_self');
}
function op_t(id)
{
	var div =  document.getElementById(id);
	if(div.style.display=="none") div.style.display="block";
	else  div.style.display="none";
}
function setupLabel() 
{
	if ($('.label_check input').length) 
	{
		$('.label_check').each(function(){ 
			$(this).removeClass('c_on');
		});
		$('.label_check input:checked').each(function(){ 
			$(this).parent('label').addClass('c_on');
		});                
	};
	/* if ($('.label_check2 input').length) 
	{
		$('.label_check2').each(function(){ 
			$(this).removeClass('c_on2');
		});
		$('.label_check2 input:checked').each(function(){ 
			$(this).parent('label').addClass('c_on2');
		});                
	}; */
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
function pesquisa_prods(id,id_pai,t)
{
	var cx_pesq = document.getElementById('pesquisa'+id);
	if(cx_pesq)
	{
		if(t)
		{
			$.get('Seleccionarprodutos2.php?f_produto='+cx_pesq.value+'&desconto='+id+'&ID_PAI='+id_pai+'&tpromo='+t+'&dp='+new Date(), function(data) 
			{  
				$('#res_'+id).html(data);
			});
		
		}
		else
		{	
			$.get('Seleccionarprodutos.php?f_produto='+cx_pesq.value+'&desconto='+id+'&ID_PAI='+id_pai+'&dp='+new Date(), function(data) 
			{  
				$('#res_'+id).html(data);
			});
		}
	}
}
function submeter_produtos(id,id2,id3,t)
{

	var data = '';
	if ($('.prodclass_'+id).length) 
	{	
		$('.prodclass_'+id).each(function()
		{
			var myid = document.getElementById($(this).attr('id'));
			if(myid.checked) data += myid.value+'|';
		});		
	}
	
	if(data)
	{		
		if(t > 0)
		{
			$.get('Seleccionarprodutos2.php?g_produto='+data+'&desconto='+id+'&desconto2='+id2+'&ID_PAI='+id3+'&tpromo='+t+'&dp='+new Date(), function(data)
			{  
				$('#products_offer').html(data);
			});
		}
		else
		{		
			$.get('Seleccionarprodutos.php?g_produto='+data+'&desconto='+id+'&desconto2='+id2+'&ID_PAI='+id3+'&dp='+new Date(), function(data) 
			{  
				$('#products'+id).html(data);
			});
		}
	}
	
	op_t('pesq_'+id);
}
function del_prod(id,id2,id3,data,t)
{
	if(data)
	{
		if(t>0)
		{

			$.get('Seleccionarprodutos2.php?d_produto='+data+'&desconto='+id+'&desconto2='+id2+'&ID_PAI='+id3+'&tpromo='+t+'&dp='+new Date(), function(data) 
			{  
				$('#products_offer').html(data);
			});		
		}
		else
		{
			$.get('Seleccionarprodutos.php?d_produto='+data+'&desconto='+id+'&desconto2='+id2+'&ID_PAI='+id3+'&dp='+new Date(), function(data) 
			{  
				$('#products'+id).html(data);
			});
		}
	}
}
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
			// scrolling_bts();
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
