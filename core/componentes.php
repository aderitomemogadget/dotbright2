<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function scroll_ini3($w=0,$h=0)
{
	return '<div id="container" style="overflow:hidden"><div id="wn"><div id="lyr1">';
}
function scroll_fim3()
{
	return '</div></div><div id="scrollbar"></div></div>';
}
function scroll_ini2($w=0,$h=0)
{
	return '<style>	#test2{OVERFLOW:auto;WIDTH:'.$w.'px;POSITION:relative;HEIGHT:'.$h.'px; outline:none;padding:4px;text-align:left;scrollbar-3dlight-color:#f9f9f9;scrollbar-darkshadow-color:#f9f9f9;scrollbar-face-color:#474747;scrollbar-highlight-color:#f9f9f9;scrollbar-shadow-color:#f9f9f9;scrollbar-track-color:#f9f9f9}
	::-webkit-scrollbar{width:15px;height:15px}
	::-webkit-scrollbar-button:start:decrement,::-webkit-scrollbar-button:end:increment{height:1px;display:block;background-color:#f9f9f9}
	::-webkit-scrollbar-button:horizontal:start:decrement,::-webkit-scrollbar-button:horizontal:end:increment{height:1px;display:block;background-color:#f9f9f9}
	::-webkit-scrollbar-track-piece{background-color:#f9f9f9}
	::-webkit-scrollbar-thumb:vertical,::-webkit-scrollbar-thumb:horizontal{background-color:#474747;border: 1px solid #639ACE;-webkit-border-radius:0px}
	</style>
	<DIV class=flexcroll id=test2>';
}
function scroll_fim2()
{
	return '</div>';
}
function scroll_ini($w=0,$h=0)
{
	$h2=$h-10;
	return '<table width='.$w.'px height='.$h.'px border="0" cellpadding=0 cellspacing=0><tr><td valign=top>
	<script type="text/javascript">
	iens6=document.all||document.getElementById;
	ns4=document.layers;
	var speed=3;
	if (iens6)
	{
		document.write(\'<div id="container" style="position:relative;width:'.$w.'px;height:'.$h2.'px;overflow:hidden">\');
		document.write(\'<div id="content" style="position:absolute;width:'.$w.'px;left:0px;top:0px;border:#000 0px solid;overflow:auto">\');
	}
	</script>
	<table width="100%" height="'.$h.'px" border=0 cellpadding=0 cellspacing=0><tr><td class="texto_scroll" valign=top>
	<ilayer name="nscontainer" left="0" width='.$w.'px height='.$h2.' clip="0,0,500,'.$h2.'" border=0>
	<layer name="nscontent" left="17" width='.$w.'px height='.$h2.' visibility=hidden border=0>';
}
function scroll_fim($w=0,$h=0)
{
	$h2=$h+1;
	return '</layer>
</ilayer></TD></TR></TABLE>
</td>
<script>
if (iens6)
{
	document.write(\'</div></div>\');
	var crossobj=document.getElementById ? document.getElementById("content"):document.all.content;
	var contentheight=crossobj.offsetHeight;
}
else if (ns4)
{
	var crossobj=document.nscontainer.document.nscontent;
	var contentheight=crossobj.clip.height;
}
function movedown()
{
	if (iens6&&parseInt(crossobj.style.top)>=(contentheight*(-1)+'.$h.'))  crossobj.style.top=parseInt(crossobj.style.top)-speed+"px";
	else if (ns4&&crossobj.top>=(contentheight*(-1)+100)) crossobj.top-=speed;
	movedownvar=setTimeout("movedown()",20);
}
function moveup()
{
	if (iens6&&parseInt(crossobj.style.top)<=0)	crossobj.style.top=parseInt(crossobj.style.top)+speed+"px";
	else if (ns4&&crossobj.top<=0) crossobj.top+=speed;
	moveupvar=setTimeout("moveup()",20);
}
function getcontent_height()
{
	if (iens6) contentheight=(crossobj.offsetHeight+20);
	if (contentheight<'.($h2+20).')
	{
		window.document.images["down01"].src="layout/default/images/blank.gif";
		document.getElementById("scroll").style.background="none";
	}
	else
	{
		window.document.images["down01"].src="layout/down.png";
		document.getElementById("scroll").style.background="url(\'layout/scroll.png\')";
	}
	if (contentheight<'.($h2+20).')
	{
		window.document.images["up01"].src="layout/default/images/blank.gif";
		document.getElementById("scroll").style.background="none";
	}
	else
	{
		window.document.images["up01"].src="layout/up.png";
		document.getElementById("scroll").style.background="url(\'layout/scroll.png\')";
	}
}
function height(){getcontent_height();}
window.onload=height;
document.body.onclick=height;
</script>
<td align=right id="scroll"><table height="'.$h.'" width="9" border=0 cellpadding=0 cellspacing=0><tr><td align=center valign=top><a href="javascript:height();" onMouseover="moveup()" onMouseout="clearTimeout(moveupvar)"><img name="up01" src="layout/blank.gif" border=0></a></td></tr><tr><td align=center valign=bottom><a href="javascript:height();" onMouseover="movedown()" onMouseout="clearTimeout(movedownvar)"><img name="down01" src="layout/blank.gif" border=0></a></td></tr></table>
</td></tr></table>';
}