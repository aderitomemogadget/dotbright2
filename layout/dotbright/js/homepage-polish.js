(function(){'use strict';
 function ensureFooterTrackingLink(){
  var sections=document.querySelectorAll('#footer .footer-block');
  for(var i=0;i<sections.length;i++){
   var heading=sections[i].querySelector('h4');
   if(!heading||heading.textContent.trim().toUpperCase()!=='APOIO AO CLIENTE')continue;
   var list=sections[i].querySelector('ul.toggle-footer');
   if(!list||list.querySelector('a[href*="p141-traking-pt"]'))return;
   var item=document.createElement('li');item.className='item db-footer-tracking';
   var link=document.createElement('a');link.href='p141-traking-pt';link.title='Estado da reparação';link.rel='nofollow';
   var label=document.createElement('span');label.textContent='Estado da reparação';
   link.appendChild(label);item.appendChild(link);list.appendChild(item);return;
  }
 }
 function enhanceHome(){
  ensureFooterTrackingLink();
  if(!document.querySelector('.db-discovery-home')||!document.getElementById('banners'))return;
  document.body.classList.add('db-home-polish');
  // Lightweight local vector drawings; only generic theme placeholders change.
  var icons={
   phone:'<rect x="15" y="4" width="26" height="44" rx="5"/><path d="M24 9h8M26 42h4"/>',
   tablet:'<rect x="9" y="6" width="38" height="42" rx="4"/><path d="M25 43h6"/>',
   keypad:'<rect x="16" y="4" width="24" height="44" rx="5"/><path d="M21 10h14v13H21zM22 29h1m4 0h1m4 0h1m-11 6h1m4 0h1m4 0h1m-11 6h1m4 0h1m4 0h1"/>',
   headphones:'<path d="M10 31v-8a18 18 0 0 1 36 0v8M10 28H7v13h9V28zm36 0h3v13h-9V28z"/>',
   television:'<rect x="5" y="10" width="46" height="31" rx="3"/><path d="M18 48h20M28 41v7M12 17h17"/>',
   speakers:'<rect x="8" y="6" width="15" height="42" rx="3"/><rect x="33" y="6" width="15" height="42" rx="3"/><circle cx="15.5" cy="17" r="3"/><circle cx="40.5" cy="17" r="3"/><circle cx="15.5" cy="35" r="5"/><circle cx="40.5" cy="35" r="5"/>',
   soundbar:'<rect x="4" y="24" width="48" height="13" rx="6"/><circle cx="14" cy="30.5" r="3"/><circle cx="42" cy="30.5" r="3"/><path d="M24 29h8M24 33h8"/>',
   stream:'<rect x="7" y="27" width="42" height="19" rx="4"/><path d="m25 31 9 5-9 5zM15 12a22 22 0 0 1 26 0M21 19a12 12 0 0 1 14 0"/>',
   bulb:'<path d="M19 31a15 15 0 1 1 18 0l-3 5H22zM22 41h12M24 46h8M28 1v3M5 19h4M47 19h4M10 5l3 3M43 5l-3 3"/>',
   home:'<path d="m4 25 24-20 24 20M11 20v27h34V20M22 47V33h12v14M20 20a13 13 0 0 1 16 0M24 25a6 6 0 0 1 8 0"/>',
   pet:'<ellipse cx="28" cy="37" rx="12" ry="10"/><ellipse cx="12" cy="24" rx="5" ry="7"/><ellipse cx="23" cy="14" rx="5" ry="7"/><ellipse cx="36" cy="14" rx="5" ry="7"/><ellipse cx="46" cy="25" rx="5" ry="7"/>',
   vacuum:'<path d="M16 7h13v9l13 22M20 7V4h10M21 17l-9 24M8 43h24v6H8zM38 39h11v8H38z"/>',
   food:'<rect x="12" y="24" width="32" height="23" rx="6"/><path d="M17 24V9h22v15M20 9V5h16v4M19 31h18M27 36v5M47 30v9"/>',
   sterile:'<rect x="10" y="15" width="36" height="32" rx="4"/><path d="M20 15V8h16v7M28 23v16M20 31h16M46 5v8M42 9h8"/>',
   climate:'<rect x="5" y="9" width="46" height="19" rx="4"/><path d="M11 22h34M15 28v7q0 5-4 7M28 28v16M41 28v7q0 5 4 7M12 15h4"/>'
  };
  function key(label){
   label=label.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
   if(/senior|teclas/.test(label))return 'keypad';
   if(/tablet/.test(label))return 'tablet';
   if(/smartphone|telemove/.test(label))return 'phone';
   if(/acessor|auricular|auscult/.test(label))return 'headphones';
   if(/soundbar/.test(label))return 'soundbar';
   if(/audio|coluna/.test(label))return 'speakers';
   if(/stream|entretenimento/.test(label))return 'stream';
   if(/video|televis/.test(label))return 'television';
   if(/ilumin/.test(label))return 'bulb';
   if(/domot/.test(label))return 'home';
   if(/animais/.test(label))return 'pet';
   if(/limpeza|aspir/.test(label))return 'vacuum';
   if(/alimento|cozinha/.test(label))return 'food';
   if(/desinfec|esteril/.test(label))return 'sterile';
   if(/climat|aquec/.test(label))return 'climate';
   return null;
  }
  document.querySelectorAll('.db-discovery-home .db-disc-subcats>a').forEach(function(link){
   if(link.querySelector('img'))return;
   var old=link.querySelector('svg'),type=key(link.textContent);if(!old||!type)return;
   var svg=document.createElementNS('http://www.w3.org/2000/svg','svg');
   svg.setAttribute('viewBox','0 0 56 56');svg.setAttribute('aria-hidden','true');svg.setAttribute('focusable','false');svg.setAttribute('data-category-icon',type);svg.innerHTML=icons[type];old.replaceWith(svg);
  });
 }
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',enhanceHome);else enhanceHome();
})();
