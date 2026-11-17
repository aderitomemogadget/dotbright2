jQuery.extend(jQuery.easing,{easeOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b;},easeInQuad:function(x,t,b,c,d){return c*(t/=d)*t+b;},easeInOutCirc:function(x,t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b;},easeInOutSine:function(x,t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b;}});(function(w,$){var defaults={selectedul:0,loadingdiv:'<div class="loadingscreen"><div>Fetching content...</div></div>',rightarrow:'<span class="right fa fa-angle-right"></span>',backarrow:'<span class="left fa  fa-angle-left"></span>',slideduration:500,easing:'easeInOutCirc'}
var menuzindex=1000
function flattenuls($mainul,cloneulBol,callback,finalcall){var callback=callback||function(){}
var finalcall=finalcall||function(){}
var $headers=$mainul.find('ul').parent()
var $mainulcopy=cloneulBol?$mainul.clone():$mainul
var $flattened=jQuery(document.createDocumentFragment())
var $headers=$mainulcopy.find('ul').parent()
for(var i=$headers.length-1;i>=0;i--){var $header=$headers.eq(i)
var $subul=$header.find('>ul').prependTo($flattened)
callback(i,$header,$subul)}$mainulcopy.prependTo($flattened)
finalcall($mainulcopy)
return $flattened}w.Powerlistmenu=function(options){var s=$.extend({},defaults,options)
var $nav=$('#'+s.menuid)
var menuinstance=this
this.s=s
this.$nav=$nav
this.buildcount=0
this.$loadingscreen=$(s.loadingdiv).appendTo($nav)
this.loadmenu(null,s.menucontent)}
w.Powerlistmenu.prototype={constructor:Powerlistmenu,loadmenu:function($target,menucontent){var menuinstance=this
var $anchor=null
var $reverseanchor=null
function preparebuildmenu($anchor,$rootul,cloneulBol,$reverseanchor){menuinstance.buildmenu($anchor,$rootul,cloneulBol,$reverseanchor)
if($anchor)
{
	$anchor.trigger('click.showsubmenu');
}
else if($reverseanchor){$reverseanchor.trigger('click')}else{menuinstance.selectMenu(menuinstance.s.selectedul)}}if(menucontent&&(!$target||!$target.data('fetchstate')||$target.data('fetchstate')=='error')){if($target){$target.data('fetchstate','starting')}$.ajax({url:menucontent,dataType:'html',error:function(ajaxrequest){menuinstance.$loadingscreen.css({visibility:'hidden'})

if($target){$target.data('fetchstate','error')}alert('Error fetching content.<br />Server Response: '+ajaxrequest.responseText)},success:function(content){var $rootul=$(content)
if($target){$target.off('click.lazyload')
$target.data('fetchstate','finished')
if($target.prop('tagName')=='LI')$anchor=$target
else
$reverseanchor=$target}preparebuildmenu($anchor,$rootul,false,$reverseanchor)}})}else{var $rootul=this.$nav.find('>ul')
preparebuildmenu($anchor,$rootul,true,$reverseanchor)}},rtrim:function(str,ch){for(i=str.length-1;i>=0;i--){if(ch!=str.charAt(i)){str=str.substring(0,i+1);break;}}return str;},buildmenu:function($anchor,$ul,cloneulBol,$reverseanchor){var $headers=$()
var $reversebreadcrumb=null
var menuinstance=this
var cloneulBol=(typeof cloneulBol=='undefined')?false:cloneulBol
var $flattened=flattenuls($ul,cloneulBol,function(i,$header,$subul){$headers=$headers.add($header)
$header.addClass('header header'+menuinstance.buildcount).find('a:eq(0)').append(menuinstance.s.rightarrow)
var $submenu=$subul.wrap('<div class="drawer"/>').parent().addClass('submenu submenu'+menuinstance.buildcount)
$header.data('$dest',$submenu)
$submenu.data('$parent',$header)
if($subul.data('selected')==1){menuinstance.s.selectedul=$subul}menuinstance.buildcount++
$('<div class="breadcrumb">'+(($header.text()!=''&&$header.text()!=' ')?menuinstance.rtrim($header.text(),'+'):'go back')+'</div>').prepend(menuinstance.s.backarrow).prependTo($submenu)},function($mainul){$mainul.wrap('<div class="drawer"/>')
if($anchor!=null){var $header=$anchor
$headers=$headers.add($header)
var headerbuildnum=$header.prop('className').match(/header(\d+)/i)[1]
var $submenu=$mainul.parent()
$submenu.addClass('submenu submenu'+headerbuildnum)
$header.data('$dest',$submenu)
$submenu.data('$parent',$header)
$('<div class="breadcrumb">'+($header.text()!=''?$header.text():'go back')+'</div>').prepend(menuinstance.s.backarrow).prependTo($submenu)}else{if($mainul.data('lazyload')){var headerinfo=$mainul.data('reverseheader').split(/,\s*/)
$reversebreadcrumb=$('<div class="breadcrumb">'+headerinfo[1]+'</div>').prepend(menuinstance.s.backarrow).prependTo($mainul)
$reversebreadcrumb.data('lazyload',$mainul.data('lazyload'))
$reversebreadcrumb.data('headerid',headerinfo[0].substr(1))}}if($mainul.data('selected')==1){menuinstance.s.selectedul=$mainul}})
if($anchor==null&&cloneulBol){$ul.before($flattened)
$ul.remove()}else{this.$nav.append($flattened)}if($reverseanchor){menuinstance.buildcount++
var $dynamicheader=$('#'+$reverseanchor.data('headerid'))
if($dynamicheader.length==1){$dynamicheader.addClass('header header'+menuinstance.buildcount).find('a:eq(0)').append(menuinstance.s.rightarrow)
var $submenu=$reverseanchor.parent().parent('div.drawer').addClass('submenu submenu'+menuinstance.buildcount)
$dynamicheader.data('$dest',$submenu)
$submenu.data('$parent',$dynamicheader)
$headers=$headers.add($dynamicheader)}}$headers.each(function(i){var $header=$(this)
var $headermenu=$header.parents('div.drawer:eq(0)')
var $submenu=$header.data('$dest')
var $breadcrumb=$submenu.find('div.breadcrumb')
$header.on('click.showsubmenu',function(e){
	$headermenu.css({zIndex:menuzindex++}).animate({left:'-100%'},menuinstance.s.slideduration,menuinstance.s.easing)
$submenu.css({zIndex:menuzindex++,left:'100%'}).animate({left:0},menuinstance.s.slideduration,menuinstance.s.easing)
e.preventDefault()})
$breadcrumb.on('click',function(e){$headermenu.css({zIndex:menuzindex++,left:'-100%'}).animate({left:0},menuinstance.s.slideduration,menuinstance.s.easing)
$submenu.animate({left:'100%'},menuinstance.s.slideduration,menuinstance.s.easing)})})
var $lazyheaders=this.$nav.find('li[data-lazyload]').addClass('header header'+menuinstance.buildcount++)
$lazyheaders.find('a:eq(0)').append(menuinstance.s.rightarrow)
if($reversebreadcrumb){$lazyheaders=$lazyheaders.add($reversebreadcrumb)}$lazyheaders.on('click.lazyload',function(e){menuinstance.$loadingscreen.css({visibility:'visible'})
var $header=$(this)
menuinstance.loadmenu($header,$header.data('lazyload'))
$header.removeAttr('data-lazyload')
e.preventDefault()})
this.$loadingscreen.css({visibility:'hidden'})},selectMenu:function(selector){var $targetmenu
if(typeof selector=="number"){$targetmenu=this.$nav.find('div.drawer').eq(selector)}else{$targetmenu=$(selector).parent('div.drawer')}if($targetmenu.length==0){$targetmenu=this.$nav.find('div.drawer').eq(0)}$targetmenu.css({left:0,zIndex:menuzindex++})}}})(window,jQuery)