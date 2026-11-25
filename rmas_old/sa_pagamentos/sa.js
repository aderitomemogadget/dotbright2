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
$( document ).ready(function() 
{   
    // $(window).load(function (){scrolling_bts();});	
	// $(window).scroll(function (){scrolling_bts();});
	
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
	$('.label_check, .label_radio').click(function(){
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
		clone.find('.BOLETIM_EXT').each(function() {this.setAttribute('id', 'BOLETIM_EXT'+quantity);this.setAttribute('name', 'BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_renovacoes');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_renovacoes").val(quantity);
	});	
	$('.addRow_endereco').click(function() 
	{		
		var num = Number($("#total_linhas_endereco").val());		
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#ENDERECO").clone(true);
		clone.attr('id', 'ENDERECO' + quantity).css('display','block');
		clone.find('.ENDERECO_ID').each(function()	{this.setAttribute('id', 'ENDERECO_ID'+quantity);this.setAttribute('name', 'ENDERECO_ID[]');this.setAttribute('value', quantity+';;ENDERECO');});
		clone.find('.ENDERECO_DE').each(function() {this.setAttribute('id', 'ENDERECO_DE'+quantity);this.setAttribute('name', 'ENDERECO_DE'+quantity);this.value='';});
		clone.find('.ENDERECO_PARA').each(function() {this.setAttribute('id', 'ENDERECO_PARA'+quantity);this.setAttribute('name', 'ENDERECO_PARA'+quantity);this.value='';});
		clone.find('.ENDERECO_NUMERO_BOLETIM').each(function() {this.setAttribute('id', 'ENDERECO_NUMERO_BOLETIM'+quantity);this.setAttribute('name', 'ENDERECO_NUMERO_BOLETIM'+quantity);this.value='';});
		clone.find('.ENDERECO_DATA_BOLETIM').each(function() {this.setAttribute('id', 'ENDERECO_DATA_BOLETIM'+quantity);this.setAttribute('name', 'ENDERECO_DATA_BOLETIM'+quantity);this.value='';});
		clone.find('.ENDERECO_BOLETIM_EXT').each(function() {this.setAttribute('id', 'ENDERECO_BOLETIM_EXT'+quantity);this.setAttribute('name', 'ENDERECO_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_endereco');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_endereco").val(quantity);
	});	
	$('.addRow_nome').click(function() 
	{	
		var num = Number($("#total_linhas_nome").val());		
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#NOME").clone(true);
		clone.attr('id', 'NOME' + quantity).css('display','block');
		clone.find('.NOME_ID').each(function()	{this.setAttribute('id', 'NOME_ID'+quantity);this.setAttribute('name', 'NOME_ID[]');this.setAttribute('value', quantity+';;NOME');});
		clone.find('.NOME_DE').each(function() {this.setAttribute('id', 'NOME_DE'+quantity);this.setAttribute('name', 'NOME_DE'+quantity);this.value='';});
		clone.find('.NOME_PARA').each(function() {this.setAttribute('id', 'NOME_PARA'+quantity);this.setAttribute('name', 'NOME_PARA'+quantity);this.value='';});
		clone.find('.NOME_NUMERO_BOLETIM').each(function() {this.setAttribute('id', 'NOME_NUMERO_BOLETIM'+quantity);this.setAttribute('name', 'NOME_NUMERO_BOLETIM'+quantity);this.value='';});
		clone.find('.NOME_DATA_BOLETIM').each(function() {this.setAttribute('id', 'NOME_DATA_BOLETIM'+quantity);this.setAttribute('name', 'NOME_DATA_BOLETIM'+quantity);this.value='';});
		clone.find('.NOME_BOLETIM_EXT').each(function() {this.setAttribute('id', 'NOME_BOLETIM_EXT'+quantity);this.setAttribute('name', 'NOME_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_nome');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_nome").val(quantity);
	});
	$('.addRow_transmissao').click(function() 
	{		
		var num = Number($("#total_linhas_transmissao").val());
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#TRANSMISSAO").clone(true);
		clone.attr('id', 'TRANSMISSAO' + quantity).css('display','block');
		clone.find('.TRANSMISSAO_ID').each(function() {this.setAttribute('id', 'TRANSMISSAO_ID'+quantity);this.setAttribute('name', 'TRANSMISSAO_ID[]');this.setAttribute('value', quantity+';;TRANSMISSAO');});
		clone.find('.TRANSMISSAO_DE').each(function() {this.setAttribute('id', 'TRANSMISSAO_DE'+quantity);this.setAttribute('name', 'TRANSMISSAO_DE'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_COM_DE').each(function() {this.setAttribute('id', 'TRANSMISSAO_COM_DE'+quantity);this.setAttribute('name', 'TRANSMISSAO_COM_DE'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_PARA').each(function() {this.setAttribute('id', 'TRANSMISSAO_PARA'+quantity);this.setAttribute('name', 'TRANSMISSAO_PARA'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_COM_PARA').each(function() {this.setAttribute('id', 'TRANSMISSAO_COM_PARA'+quantity);this.setAttribute('name', 'TRANSMISSAO_COM_PARA'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_NUMERO_BOLETIM').each(function() {this.setAttribute('id', 'TRANSMISSAO_NUMERO_BOLETIM'+quantity);this.setAttribute('name', 'TRANSMISSAO_NUMERO_BOLETIM'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_DATA_BOLETIM').each(function() {this.setAttribute('id', 'TRANSMISSAO_DATA_BOLETIM'+quantity);this.setAttribute('name', 'TRANSMISSAO_DATA_BOLETIM'+quantity);this.value='';});
		clone.find('.TRANSMISSAO_BOLETIM_EXT').each(function() {this.setAttribute('id', 'TRANSMISSAO_BOLETIM_EXT'+quantity);this.setAttribute('name', 'TRANSMISSAO_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_transmissao');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_transmissao").val(quantity);
	});
	$('.addRow_fusao').click(function () 
	{	
		var num = Number($("#total_linhas_fusao").val());
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#FUSAO").clone(true);
		clone.attr('id', 'FUSAO' + quantity).css('display','block');
		clone.find('.FUSAO_ID').each(function()	{this.setAttribute('id', 'FUSAO_ID'+quantity);this.setAttribute('name', 'FUSAO_ID[]');this.setAttribute('value', quantity+';;FUSAO');});
		clone.find('.FUSAO_DE').each(function() {this.setAttribute('id', 'FUSAO_DE'+quantity);this.setAttribute('name', 'FUSAO_DE'+quantity);this.value='';});
		clone.find('.FUSAO_COM_DE').each(function() {this.setAttribute('id', 'FUSAO_COM_DE'+quantity);this.setAttribute('name', 'FUSAO_COM_DE'+quantity);this.value='';});
		clone.find('.FUSAO_PARA').each(function() {this.setAttribute('id', 'FUSAO_PARA'+quantity);this.setAttribute('name', 'FUSAO_PARA'+quantity);this.value='';});
		clone.find('.FUSAO_COM_PARA').each(function() {this.setAttribute('id', 'FUSAO_COM_PARA'+quantity);this.setAttribute('name', 'FUSAO_COM_PARA'+quantity);this.value='';});
		clone.find('.FUSAO_NUMERO_BOLETIM').each(function() {this.setAttribute('id', 'FUSAO_NUMERO_BOLETIM'+quantity);this.setAttribute('name', 'FUSAO_NUMERO_BOLETIM'+quantity);this.value='';});
		clone.find('.FUSAO_DATA_BOLETIM').each(function() {this.setAttribute('id', 'FUSAO_DATA_BOLETIM'+quantity);this.setAttribute('name', 'FUSAO_DATA_BOLETIM'+quantity);this.value='';});
		clone.find('.FUSAO_BOLETIM_EXT').each(function() {this.setAttribute('id', 'FUSAO_BOLETIM_EXT'+quantity);this.setAttribute('name', 'FUSAO_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_fusao');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_fusao").val(quantity);
	});
	$('.addRow_licensa').click(function () 
	{		
		var num = Number($("#total_linhas_licensa").val());
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#LICENSA").clone(true);
		clone.attr('id', 'LICENSA' + quantity).css('display','block');
		clone.find('.LICENSA_ID').each(function()	{this.setAttribute('id', 'LICENSA_ID'+quantity);this.setAttribute('name', 'LICENSA_ID[]');this.setAttribute('value', quantity+';;LICENSA');});
		clone.find('.LICENSA_DE').each(function() {this.setAttribute('id', 'LICENSA_DE'+quantity);this.setAttribute('name', 'LICENSA_DE'+quantity);this.value='';});
		clone.find('.LICENSA_COM_DE').each(function() {this.setAttribute('id', 'LICENSA_COM_DE'+quantity);this.setAttribute('name', 'LICENSA_COM_DE'+quantity);this.value='';});
		clone.find('.LICENSA_PARA').each(function() {this.setAttribute('id', 'LICENSA_PARA'+quantity);this.setAttribute('name', 'LICENSA_PARA'+quantity);this.value='';});
		clone.find('.LICENSA_COM_PARA').each(function() {this.setAttribute('id', 'LICENSA_COM_PARA'+quantity);this.setAttribute('name', 'LICENSA_COM_PARA'+quantity);this.value='';});
		clone.find('.LICENSA_NUMERO_BOLETIM').each(function() {this.setAttribute('id', 'LICENSA_NUMERO_BOLETIM'+quantity);this.setAttribute('name', 'LICENSA_NUMERO_BOLETIM'+quantity);this.value='';});
		clone.find('.LICENSA_DATA_BOLETIM').each(function() {this.setAttribute('id', 'LICENSA_DATA_BOLETIM'+quantity);this.setAttribute('name', 'LICENSA_DATA_BOLETIM'+quantity);this.value='';});
		clone.find('.LICENSA_BOLETIM_EXT').each(function() {this.setAttribute('id', 'LICENSA_BOLETIM_EXT'+quantity);this.setAttribute('name', 'LICENSA_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_licensa');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_licensa").val(quantity);
	});
	$('.addRow_declaracao').click(function () 
	{		
		var num = Number($("#total_linhas_declaracao").val());	
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#DECLARACAO").clone(true);
		clone.attr('id', 'DECLARACAO' + quantity).css('display','block');
		clone.find('.DECLARACAO_ID').each(function()	{this.setAttribute('id', 'DECLARACAO_ID'+quantity);this.setAttribute('name', 'DECLARACAO_ID[]');this.setAttribute('value', quantity+';;DECLARACAO');});
		clone.find('.DECLARACAO_BOLETIM_DATA').each(function() {this.setAttribute('id', 'DECLARACAO_BOLETIM_DATA'+quantity);this.setAttribute('name', 'DECLARACAO_BOLETIM_DATA'+quantity);this.value='';});
		clone.find('.DECLARACAO_BOLETIM_EXT').each(function() {this.setAttribute('id', 'DECLARACAO_BOLETIM_EXT'+quantity);this.setAttribute('name', 'DECLARACAO_BOLETIM_EXT'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_declaracao');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_declaracao").val(quantity);
	});
	$('.addRow_oposicao').click(function () 
	{		
		var num = Number($("#total_linhas_oposicao").val());	
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#OPOSICAO").clone(true);
		clone.attr('id', 'OPOSICAO' + quantity).css('display','block');
		clone.find('.OPOSICAO_ID').each(function()	{this.setAttribute('id', 'OPOSICAO_ID'+quantity);this.setAttribute('name', 'OPOSICAO_ID[]');this.setAttribute('value', quantity+';;OPOSICAO');});
		clone.find('.OPOSICAO_DATA').each(function()	{this.setAttribute('id', 'OPOSICAO_DATA'+quantity);this.setAttribute('name', 'OPOSICAO_DATA'+quantity);this.value='';});
		clone.find('.OPOSICAO_OPONENTE').each(function() {this.setAttribute('id', 'OPOSICAO_OPONENTE'+quantity);this.setAttribute('name', 'OPOSICAO_OPONENTE'+quantity);this.value='';});
		clone.find('.OPOSICAO_OPONENTE_DIREITOS').each(function() {this.setAttribute('id', 'OPOSICAO_OPONENTE_DIREITOS'+quantity);this.setAttribute('name', 'OPOSICAO_OPONENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.OPOSICAO_REQUERENTE').each(function() {this.setAttribute('id', 'OPOSICAO_REQUERENTE'+quantity);this.setAttribute('name', 'OPOSICAO_REQUERENTE'+quantity);this.value='';});
		clone.find('.OPOSICAO_REQUERENTE_DIREITOS').each(function() {this.setAttribute('id', 'OPOSICAO_REQUERENTE_DIREITOS'+quantity);this.setAttribute('name', 'OPOSICAO_REQUERENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_oposicao');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_oposicao").val(quantity);
	});
	$('.addRow_resposta').click(function () 
	{		
		var num = Number($("#total_linhas_resposta").val());	
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#RESPOSTA").clone(true);
		clone.attr('id', 'RESPOSTA' + quantity).css('display','block');
		clone.find('.RESPOSTA_ID').each(function()	{this.setAttribute('id', 'RESPOSTA_ID'+quantity);this.setAttribute('name', 'RESPOSTA_ID[]');this.setAttribute('value', quantity+';;RESPOSTA');});
		clone.find('.RESPOSTA_DATA').each(function()	{this.setAttribute('id', 'RESPOSTA_DATA'+quantity);this.setAttribute('name', 'RESPOSTA_DATA'+quantity);this.value='';});
		clone.find('.RESPOSTA_OPONENTE').each(function() {this.setAttribute('id', 'RESPOSTA_OPONENTE'+quantity);this.setAttribute('name', 'RESPOSTA_OPONENTE'+quantity);this.value='';});
		clone.find('.RESPOSTA_OPONENTE_DIREITOS').each(function() {this.setAttribute('id', 'RESPOSTA_OPONENTE_DIREITOS'+quantity);this.setAttribute('name', 'RESPOSTA_OPONENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.RESPOSTA_REQUERENTE').each(function() {this.setAttribute('id', 'RESPOSTA_REQUERENTE'+quantity);this.setAttribute('name', 'RESPOSTA_REQUERENTE'+quantity);this.value='';});
		clone.find('.RESPOSTA_REQUERENTE_DIREITOS').each(function() {this.setAttribute('id', 'RESPOSTA_REQUERENTE_DIREITOS'+quantity);this.setAttribute('name', 'RESPOSTA_REQUERENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_resposta');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_resposta").val(quantity);
	});
	$('.addRow_reclamacao').click(function () 
	{		
		var num = Number($("#total_linhas_reclamacao").val());	
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#RECLAMACAO").clone(true);
		clone.attr('id', 'RECLAMACAO' + quantity).css('display','block');
		clone.find('.RECLAMACAO_ID').each(function()	{this.setAttribute('id', 'RECLAMACAO_ID'+quantity);this.setAttribute('name', 'RECLAMACAO_ID[]');this.setAttribute('value', quantity+';;RECLAMACAO');});
		clone.find('.RECLAMACAO_DATA').each(function()	{this.setAttribute('id', 'RECLAMACAO_DATA'+quantity);this.setAttribute('name', 'RECLAMACAO_DATA'+quantity);this.value='';});
		clone.find('.RECLAMACAO_OPONENTE').each(function() {this.setAttribute('id', 'RECLAMACAO_OPONENTE'+quantity);this.setAttribute('name', 'RECLAMACAO_OPONENTE'+quantity);this.value='';});
		clone.find('.RECLAMACAO_OPONENTE_DIREITOS').each(function() {this.setAttribute('id', 'RECLAMACAO_OPONENTE_DIREITOS'+quantity);this.setAttribute('name', 'RECLAMACAO_OPONENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.RECLAMACAO_REQUERENTE').each(function() {this.setAttribute('id', 'RECLAMACAO_REQUERENTE'+quantity);this.setAttribute('name', 'RECLAMACAO_REQUERENTE'+quantity);this.value='';});
		clone.find('.RECLAMACAO_REQUERENTE_DIREITOS').each(function() {this.setAttribute('id', 'RECLAMACAO_REQUERENTE_DIREITOS'+quantity);this.setAttribute('name', 'RECLAMACAO_REQUERENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_reclamacao');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_reclamacao").val(quantity);
	});
	$('.addRow_recurso').click(function () 
	{		
		var num = Number($("#total_linhas_recurso").val());
		$('.dropdate').datepicker('destroy');
		var quantity = Number(num+1);
		var clone = $("#RECURSO").clone(true);
		clone.attr('id', 'RECURSO' + quantity).css('display','block');
		clone.find('.RECURSO_ID').each(function() {this.setAttribute('id', 'RECURSO_ID'+quantity);this.setAttribute('name', 'RECURSO_ID[]');this.setAttribute('value', quantity+';;RECURSO');});
		clone.find('.RECURSO_DATA').each(function()	{this.setAttribute('id', 'RECURSO_DATA'+quantity);this.setAttribute('name', 'RECURSO_DATA'+quantity);this.value='';});
		clone.find('.RECURSO_OPONENTE').each(function() {this.setAttribute('id', 'RECURSO_OPONENTE'+quantity);this.setAttribute('name', 'RECURSO_OPONENTE'+quantity);this.value='';});
		clone.find('.RECURSO_OPONENTE_DIREITOS').each(function() {this.setAttribute('id', 'RECURSO_OPONENTE_DIREITOS'+quantity);this.setAttribute('name', 'RECURSO_OPONENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.RECURSO_REQUERENTE').each(function() {this.setAttribute('id', 'RECURSO_REQUERENTE'+quantity);this.setAttribute('name', 'RECURSO_REQUERENTE'+quantity);this.value='';});
		clone.find('.RECURSO_REQUERENTE_DIREITOS').each(function() {this.setAttribute('id', 'RECURSO_REQUERENTE_DIREITOS'+quantity);this.setAttribute('name', 'RECURSO_REQUERENTE_DIREITOS'+quantity);this.value='';});
		clone.find('.pretourl_small').remove();
		clone.appendTo('#info_recurso');
		$('.dropdate').datepicker({showOn: "button", buttonImage: "img/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
		$("#total_linhas_recurso").val(quantity);
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
function pesquisa()
{
	var hash='';
	var p1 = $('#NUMERO').val();
	var p2 = $('#TIPO').val();
	var p3 = $('#DESIGNACAO').val();
	var p4 = $('#DATA_PEDIDO').val();
	
	if(p1) hash = '&NUMERO='+p1;
	if(p2) hash += '&TIPO='+p2;
	if(p3) hash += '&DESIGNACAO='+p3;
	if(p4) hash += '&DATA_PEDIDO='+p4;
	
	if(hash) window.location.hash = 'list.php?p=1'+hash;
	else  window.open('list.php','_self');
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
