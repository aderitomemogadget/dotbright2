<script type="text/javascript">
var isOpera = (navigator.userAgent.indexOf('Opera') != -1);
var isIE = (!isOpera && navigator.userAgent.indexOf('MSIE') != -1)
function mouseup(links){
	selected_link=links;
	var rightclick;
	if(!e) var e = window.event;
	if(e.which) rightclick = (e.which==3);
	else if(e.button) rightclick = (e.button==2);
	if(rightclick==true){
		reposition('menu1',tempX-10,tempY-10);
		hide('menu1');		show('menu1');
	}
}

function mouseup2(id){
	email_id=id;
	var rightclick;
	if(!e) var e = window.event;
	if(e.which) rightclick = (e.which==3);
	else if(e.button) rightclick = (e.button==2);
	if(rightclick==true){
		reposition('menu2',tempX-10,tempY-10);
		hide('menu2');		show('menu2');
	}
}

function abrir(){
	window.open(selected_link, "ifrm");
}

function abrir_email(){
//	window.open("viewmsg.php?mail_id="+email_id,"ifrm");
	document.location.href='viewmsg.php?mail_id='+email_id;
}
function abrir_email_janela(){
	window.open("viewmsg.php?mail_id="+email_id);
}
var IE = document.all?true:false
if(!IE) document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getMouseXY;

var tempX = 0
var tempY = 0
var selected_link="";
var email_id=0;
function getMouseXY(e){
  if(IE){ // grab the x-y pos.s if browser is IE
    tempX = event.clientX + document.body.scrollLeft
    tempY = event.clientY + document.body.scrollTop
  } else {  // grab the x-y pos.s if browser is NS
    tempX = e.pageX
    tempY = e.pageY
  }  
  // catch possible negative values in NS4
  if(tempX < 0){tempX = 0}
  if(tempY < 0){tempY = 0}  
  return true
}
function show(object){
	if(!isIE) document.getElementById(object).style.visibility = "visible";
	else
	{
    	if(document.layers && document.layers[object] != null) document.layers[object].visibility = 'visible';
		else if(document.all)	document.all[object].style.visibility = 'visible';
	}
}
function hide(object){
	if(!isIE) document.getElementById(object).style.visibility = "hidden";
	else
	{
	    if(document.layers && document.layers[object] != null)        document.layers[object].visibility = 'hidden';
    	else if(document.all)       document.all[object].style.visibility = 'hidden';
	}
}
function reposition(object,x,y){
	if(!isIE){
		document.getElementById(object).style.left = x;
		document.getElementById(object).style.top = y;
	}
	else
	{
		if(document.layers && document.layers[object] != null){
			document.layers[object].left = x;
			document.layers[object].top = y;
		}
		else if(document.all){
			document.all[object].style.posLeft = x;
			document.all[object].style.posTop = y;
		}
	}
}
</SCRIPT>
<script>
function addEvents(obj_id){	if(!isIE){ method='listener';capture=false; var x = new getObj(obj_id);	x.obj.addEventListener('mouseup',clicktest,capture);}}
function clicktest(e){	if(!e) e = window.event;	if(e.which==3){	reposition('menu1',tempX-10,tempY-10);		hide('menu1');		show('menu1');	}}
function addEvents2(obj_id){	if(!isIE){ method='listener';capture=false; var x = new getObj(obj_id);	x.obj.addEventListener('mouseup',clicktest2,capture);}}
function clicktest2(e){	if(!e) e = window.event;	if(e.which==3){	reposition('menu2',tempX-10,tempY-10);		hide('menu2');		show('menu2');	}}
function getObj(name){
  if(document.getElementById){  	this.obj = document.getElementById(name);	this.style = document.getElementById(name).style;  }
  else if(document.all){	this.obj = document.all[name];	this.style = document.all[name].style;  }
  else if(document.layers){	this.obj = getObjNN4(document,name);	this.style = this.obj;  }
}
function getObjNN4(obj,name){
	var x = obj.layers;
	var thereturn;
	for (var i=0;i<x.length;i++){
		if(x[i].id==name)		 	thereturn = x[i];
		else if(x[i].layers.length)	var tmp = getObjNN4(x[i],name);
		if(tmp) thereturn = tmp;
	}
	return thereturn;
}
</script>
<STYLE>
.menu { POSITION: absolute; VISIBILITY: hidden; BACKGROUND-COLOR: #D4D0C8; LAYER-BACKGROUND-COLOR: #D4D0C8; BORDER-LEFT: 1px solid #404040; BORDER-TOP: 1px solid #404040; BORDER-BOTTOM: 3px solid #404040; BORDER-RIGHT: 3px solid #404040; PADDING: 3px; z-index: 10; width: 100 }
.menuTitle { width: 35; BORDER-LEFT: 1px solid black; BORDER-TOP: 1px solid black; BORDER-BOTTOM: 1px solid black; BORDER-RIGHT: 1px solid black; PADDING: 3px; BACKGROUND-COLOR: #000000 }
.menuBarLink { text-decoration: none; font-style: bold; color: #ffffff; font-family: helvetica,arial; outline: none }
.menuLink { text-decoration: none; font-style: bold; color: #000000; outline: none }
a:hover.menuLink { text-decoration: none; font-style: bold; color: #ffffff }
#menuTitle1 { position: absolute; left: 10; top: 10; font-size: 14px }
#menuTitle2 { position: absolute; left: 50; top: 10; font-size: 14px }
#menu1 { position: absolute; left: 13; top: 29; font-size: 14px }
#menu2 { position: absolute; left: 53; top: 29; font-size: 14px }
#contentDiv { position: absolute; left: 10; top: 40; font-size: 14px }

.menu2 { POSITION: absolute; VISIBILITY: hidden; BACKGROUND-COLOR: #D4D0C8; LAYER-BACKGROUND-COLOR: #D4D0C8; BORDER-LEFT: 1px solid #404040; BORDER-TOP: 1px solid #404040; BORDER-BOTTOM: 3px solid #404040; BORDER-RIGHT: 3px solid #404040; PADDING: 3px; z-index: 10; width: 200 }
</STYLE>
<div id=menu1 class=menu onclick=hide('menu1'); onContextMenu="return false;"><a href="javascript:void(null)" onclick="abrir()" class=menuLink>Abrir</a><br>
<a href="javascript:;" class=menuLink>Nova sub-pasta</a><br>
<a href="javascript:;" class=menuLink>Apagar</a></div>

<div id=menu2 class=menu2 onclick=hide('menu2'); onContextMenu="return false;">
<a href="javascript:void(null)" onclick="abrir_email()" class=menuLink>Abrir</a><br>
<a href="javascript:void(null)" onclick="abrir_email_janela()" class=menuLink>Abrir numa nova Janela</a><br>
<a href="javascript:;" class=menuLink>Responder ao remetente</a><br>
<a href="javascript:;" class=menuLink>Marcar como lida</a><br>
<a href="javascript:;" class=menuLink>Marcar como não lida</a><br>
<a href="javascript:;" class=menuLink>Mover para a pasta</a><br>
<a href="javascript:;" class=menuLink>Catalogar como Junk</a><br>
<a href="javascript:;" class=menuLink>Apagar</a></div>