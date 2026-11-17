function toggle(id)
{
	var ele=document.getElementById(id);
	if(ele.style.display=="block"){ele.style.display="none";ele.style.visibility="hidden";}
	else{ele.style.display="block";ele.style.visibility="visible";}
}
function toggle_menu(id)
{
	var ele=document.getElementById(id);
	if(ele.style.display=="block"){ele.style.display="none";ele.style.visibility="hidden";}
	else {ele.style.display="block";ele.style.visibility="visible";}
}
function LightboxDelegate(url){TB_show(null,url,true);}
$(document).ready(function(){calculaMargemMenu();});
$(window).resize(function(){calculaMargemMenu();});
function calculaMargemMenu()
{
	var window_width=$(window).width();
	var menu_size=$(".wrapper_header").width();
	var margin_left=(window_width-menu_size)/2;
	if(margin_left>0)$(".wrapper_header").css("margin-left",margin_left+"px");
}