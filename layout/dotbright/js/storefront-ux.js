(function(){'use strict';
function start(){
 var reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
 var header=document.getElementById('header');
 if(header){
  var space=document.createElement('div');space.className='db-header-space';space.setAttribute('aria-hidden','true');header.parentNode.insertBefore(space,header);
  header.classList.add('db-fixed-header');
  var nav=header.parentNode.querySelector('nav.menu');if(nav)nav.classList.add('db-fixed-nav');
  function size(){space.style.height=header.getBoundingClientRect().height+'px';}
  size();if(window.ResizeObserver)new ResizeObserver(size).observe(header);window.addEventListener('resize',size);
 }
 var top=document.createElement('button');top.id='db-back-top';top.type='button';top.setAttribute('aria-label','Voltar ao topo');top.title='Voltar ao topo';document.body.appendChild(top);
 function scroller(){return document.querySelector('.os-viewport')||document.scrollingElement||document.documentElement;}
 function update(){top.classList.toggle('is-visible',Math.max(scroller().scrollTop,window.scrollY)>260);}
 document.addEventListener('scroll',update,true);window.addEventListener('scroll',update,{passive:true});update();
 top.addEventListener('click',function(){scroller().scrollTo({top:0,behavior:reduced?'auto':'smooth'});window.scrollTo({top:0,behavior:reduced?'auto':'smooth'});});
 if(document.querySelector('.product-single')){
  document.body.classList.add('db-product-page');
  var wrap=document.querySelector('#w_prod .swiper-wrapper');
  if(wrap){
   var viewport=wrap.parentNode;if(viewport.swiper)viewport.swiper.destroy(true,true);
   wrap.id='db-related-list';wrap.setAttribute('role','region');wrap.setAttribute('aria-label','Sugestões de produtos');wrap.tabIndex=0;
   wrap.querySelectorAll('.swiper-slide-duplicate').forEach(function(e){e.remove();});
   wrap.querySelectorAll('.swiper-slide').forEach(function(slide){
    var onclick=slide.getAttribute('onclick')||'';var match=onclick.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
    if(match){var link=slide.querySelector('a');if(link)link.setAttribute('href',match[1]);}
   });
   var controls=document.createElement('div');controls.className='db-related-controls';
   ['Anteriores','Seguintes'].forEach(function(label,i){var button=document.createElement('button');button.type='button';button.textContent=i?'›':'‹';button.setAttribute('aria-label',label+' sugestões');button.setAttribute('aria-controls',wrap.id);button.addEventListener('click',function(){wrap.scrollBy({left:(i?1:-1)*wrap.clientWidth*.85,behavior:reduced?'auto':'smooth'});});controls.appendChild(button);});
   document.getElementById('w_prod').appendChild(controls);
   var hint=document.createElement('p');hint.className='db-related-hint';hint.textContent='Descobre mais. Desliza ou utiliza as setas.';wrap.parentNode.parentNode.insertBefore(hint,wrap.parentNode);
   function arrows(){controls.children[0].disabled=wrap.scrollLeft<2;controls.children[1].disabled=wrap.scrollLeft+wrap.clientWidth>=wrap.scrollWidth-2;}
   wrap.addEventListener('scroll',arrows,{passive:true});window.addEventListener('resize',arrows);arrows();
   wrap.addEventListener('keydown',function(e){if(e.key==='ArrowRight'||e.key==='ArrowLeft'){e.preventDefault();wrap.scrollBy({left:(e.key==='ArrowRight'?1:-1)*280,behavior:reduced?'auto':'smooth'});}});
  }
 }
 var tracking=document.getElementById('traking_id');
 if(tracking){var row=tracking.closest('.row');if(row){row.classList.add('db-tracking');var form=tracking.closest('form'),h=document.createElement('h2');h.textContent='A tua reparação, sempre a par.';form.insertBefore(h,form.firstChild);tracking.setAttribute('aria-label','Número de tracking da reparação');tracking.setAttribute('autocomplete','off');var help=document.createElement('p');help.textContent='Encontras o número de tracking no comprovativo da reparação.';form.appendChild(help);var btn=form.querySelector('button span');if(btn)btn.textContent='Consultar estado';}}
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',start);else start();
})();
