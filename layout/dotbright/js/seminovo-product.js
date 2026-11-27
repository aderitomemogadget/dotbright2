(function () {
    'use strict';
    function node(tag, className, text) {
        var element = document.createElement(tag); element.className = className || '';
        if (text != null) element.textContent = text;
        return element;
    }
    function icon(name) {
        var paths = {battery:'M3 7h16v10H3zM21 10v4M6 10v4m4-4v4m4-4v4', shield:'M12 3l8 3v6c0 5-8 9-8 9s-8-4-8-9V6zM8 12l3 3 5-6',
            store:'M3 10h18L19 4H5zM5 10v11h14V10M9 21v-7h6v7', truck:'M2 5h12v12H2zM14 9h4l4 5v3h-8M6 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4m12 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4',
            check:'M5 12l4 4L19 6', cart:'M2 3h3l3 12h11l3-9H6M9 15l-1 3h11M10 21h.01M18 21h.01',
            camera:'M3 7h4l2-3h6l2 3h4v14H3zM12 10a4 4 0 1 0 0 8 4 4 0 0 0 0-8',
            heart:'M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z',
            whatsapp:'M21 11.5a9 9 0 0 1-13.4 7.8L3 21l1.7-4.6A9 9 0 1 1 21 11.5zM8 7c-1 4 1 7 5 9l2-2-3-1-1 1-2-3 1-1z',
            device:'M7 2h10v20H7zM10 19h4M18 3l2-2m0 4h3'};
        var span = node('span', 'db-semi-icon');
        span.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'+(paths[name] || paths.check)+'"/></svg>';
        return span;
    }
    function start(root) {
        if (root.classList.contains('db-semi-ready')) return;
        var payload = root.querySelector('.db-semi-data');
        if (!payload) return;
        var data; try { data = JSON.parse(payload.textContent); } catch (error) { return; }
        var product = root.querySelector('.product-single');
        var info = product && product.querySelector('.product-info');
        var gallery = product && product.querySelector('.product-gallery-block');
        if (!info || !gallery) return;
        var left = gallery.parentElement, right = info.parentElement;
        product.classList.add('db-semi-main'); left.classList.add('db-semi-gallery-column'); right.classList.add('db-semi-info-column');
        var card = node('div', 'db-semi-gallery-card'); gallery.before(card); card.appendChild(gallery);
        var currentCondition = data.conditions[data.grade];
        if (data.grade) card.appendChild(node('span', 'db-card-grade db-semi-grade', data.grade));
        var wish = left.querySelector('.wish');
        if (!wish && typeof window.addProductWishList === 'function') {
            wish=node('div','wish');var guestWish=node('a','icon-coracao produtowl'+data.id);guestWish.href='#';guestWish.setAttribute('aria-label','Adicionar aos favoritos');guestWish.appendChild(icon('heart'));guestWish.addEventListener('click',function(event){event.preventDefault();window.addProductWishList(data.id);});wish.appendChild(guestWish);
        }
        if (wish) { wish.classList.add('db-semi-wish'); card.appendChild(wish); var wishLink=wish.querySelector('a'); if(wishLink)wishLink.setAttribute('aria-label','Favoritos'); }
        var share = left.querySelector('.share'); if (share) share.classList.add('db-semi-share');
        var galleryItems=gallery.querySelectorAll('.product-gallery > li');
        if(galleryItems.length===1){var mainImage=galleryItems[0].querySelector('img.zoom');if(mainImage){var thumbWrap=node('div','db-semi-single-thumb'),thumb=node('button');thumb.type='button';thumb.setAttribute('aria-label','Imagem 1 selecionada');thumb.setAttribute('aria-pressed','true');thumb.appendChild(mainImage.cloneNode());thumb.addEventListener('click',function(){var link=galleryItems[0].querySelector('a');if(link)link.focus();});thumbWrap.appendChild(thumb);card.appendChild(thumbWrap);}}
        var header = info.querySelector('.product-info-header');
        if (!header) return;
        header.prepend(node('span', 'db-semi-eyebrow', 'SEMINOVO'));
        var title = header.querySelector('h1');
        if (title && typeof window.catalogProductDetails === 'function') {
            var details = window.catalogProductDetails(data.name); title.textContent = details.name;
            if (details.variant) title.after(node('p', 'db-semi-variant', details.variant));
        }
        var warrantyText = data.warranty || '';
        header.querySelectorAll('#gt > span').forEach(function (span) {
            if (/GARANTIA/i.test(span.textContent)) { warrantyText=span.textContent.trim(); span.hidden=true; }
        });
        var facts = node('div', 'db-semi-facts');
        var battery = node('article', 'db-semi-fact db-semi-battery'); battery.appendChild(icon('battery'));
        battery.appendChild(node('strong', '', data.battery === null ? 'Não indicada' : data.battery+'%'));
        battery.appendChild(node('span', '', 'Saúde da bateria')); facts.appendChild(battery);
        var condition = node('article', 'db-semi-fact db-semi-condition');
        if (data.grade) condition.appendChild(node('span', 'db-semi-condition-grade', data.grade));
        condition.appendChild(node('strong', '', currentCondition ? currentCondition.name : (data.grade || 'Não indicado')));
        condition.appendChild(node('span', '', 'Estado de conservação'));
        if (currentCondition) condition.appendChild(node('p', '', currentCondition.description)); facts.appendChild(condition);
        header.after(facts);
        if (warrantyText) { var warranty=node('p','db-semi-warranty');warranty.append(icon('shield'),node('span','',warrantyText));facts.after(warranty); }
        var legacyInfo = info.querySelector('#prod_info_dt'); if (legacyInfo) legacyInfo.hidden=true;
        var purchase = info.querySelector('.product-purchase-container');
        if (purchase) {
            purchase.classList.add('db-semi-buy');
            var price = purchase.querySelector('.our_price_display');
            if (price) { price.querySelectorAll('span').forEach(function(n){if(/^\s*PVP:\s*$/i.test(n.textContent))n.hidden=true;});Array.prototype.filter.call(price.querySelectorAll('span'),function(n){return /\d.*€/.test(n.textContent) && !n.querySelector('span');}).slice(-1).forEach(function(n){n.classList.add('db-semi-price-value');}); }
            var availability = node('div', 'db-semi-availability');
            availability.appendChild(node('span',data.stock > 0 ? 'is-in-stock' : 'is-no-stock',data.stock > 0 ? 'Em stock' : 'Sem stock imediato'));
            var location = info.querySelector('.localizacao_fisica');
            if (location) availability.appendChild(location);
            var form = purchase.querySelector('.product-form'); if (form) form.before(availability); else purchase.appendChild(availability);
            purchase.querySelectorAll('.product_quantity_up').forEach(function(n){n.setAttribute('aria-label','Aumentar quantidade');n.textContent='+';});
            purchase.querySelectorAll('.product_quantity_down').forEach(function(n){n.setAttribute('aria-label','Diminuir quantidade');n.textContent='−';});
            var buy = purchase.querySelector('button[onclick*="adicionarCarrinhoProdutoOpcoes"]');
            if (buy) {
                buy.classList.add('db-semi-cart'); var label=buy.querySelector('span');
                if (label) label.textContent='Adicionar ao carrinho'; else buy.appendChild(node('span','','Adicionar ao carrinho'));
                buy.prepend(icon('cart'));
                var payment = node('p','db-semi-secure');payment.append(icon('shield'),node('span','','Pagamento seguro'));purchase.appendChild(payment);
                sticky(root, buy, price);
            }
        }
        var photo = left.querySelector('.sl_fotos a[href*="wa.me"]');
        if (photo) {
            var photoCard=node('section','db-semi-photo-request');
            photoCard.append(node('h2','','Queres ver o equipamento real?'),node('p','','Solicita fotografias do equipamento antes de finalizar a compra.'));
            photo.classList.add('db-semi-whatsapp');photo.replaceChildren(icon('whatsapp'),document.createTextNode('Solicitar fotografias por WhatsApp'));
            photo.setAttribute('rel','noopener');photoCard.appendChild(photo);left.querySelector('.sl_fotos').replaceWith(photoCard);
        }
        var benefits = node('section','db-semi-benefits'); benefits.setAttribute('aria-label','Informação deste equipamento');
        (data.benefits || []).forEach(function(fact){
            var text=fact.source==='warranty'?warrantyText:fact.label;
            if(!text)return;var item=node('div','db-semi-benefit'+(fact.tone==='green'?' is-green':''));item.append(icon(fact.icon),node('strong','',text));benefits.appendChild(item);
        });
        product.after(benefits);
        var delivery = node('section','db-semi-delivery');
        [['Entrega ao domicílio',data.delivery || data.shipping,'truck'],['Levantamento em loja',data.location,'store']].forEach(function(row){
            var item=node('div','db-semi-delivery-row');item.append(icon(row[2]),node('strong','',row[0]));
            if(row[1])item.appendChild(node('span','',row[1]));delivery.appendChild(item);
        });
        if(purchase)purchase.after(delivery);
        tabs(root, product, benefits, data, warrantyText);
        related(root);
        root.classList.add('db-semi-ready'); document.body.classList.add('db-seminovo-page');
        // Let the existing LightSlider measure the final column, after the layout changes.
        window.requestAnimationFrame(function(){window.dispatchEvent(new Event('resize'));});
    }
    function tabs(root, product, benefits, data, warranty) {
        var section=node('section','db-semi-complement'),list=node('div','db-semi-tabs');list.setAttribute('role','tablist');list.setAttribute('aria-label','Informação do equipamento');
        section.appendChild(list);var panels=[];
        [['Estado do equipamento','state'],['Características técnicas','technical'],['Garantia','warranty'],['Entregas e devoluções','shipping']].forEach(function(tab,index){
            var button=node('button','db-semi-tab',tab[0]);button.type='button';button.id='db-semi-tab-'+data.id+'-'+tab[1];button.setAttribute('role','tab');
            var panel=node('div','db-semi-tab-panel');panel.id='db-semi-panel-'+data.id+'-'+tab[1];panel.setAttribute('role','tabpanel');panel.setAttribute('aria-labelledby',button.id);panel.tabIndex=0;
            button.setAttribute('aria-controls',panel.id);button.setAttribute('aria-selected',String(index===0));button.tabIndex=index===0?0:-1;panel.hidden=index!==0;
            button.addEventListener('click',function(){list.querySelectorAll('button').forEach(function(b){b.setAttribute('aria-selected',String(b===button));b.tabIndex=b===button?0:-1;});panels.forEach(function(p){p.hidden=p!==panel;});});
            button.addEventListener('keydown',function(event){var buttons=Array.from(list.children),i=buttons.indexOf(button),next=event.key==='ArrowRight'?(i+1)%buttons.length:event.key==='ArrowLeft'?(i+buttons.length-1)%buttons.length:event.key==='Home'?0:event.key==='End'?buttons.length-1:-1;if(next>=0){event.preventDefault();buttons[next].focus();buttons[next].click();}});
            list.appendChild(button);section.appendChild(panel);panels.push(panel);
        });
        var grades=node('div','db-semi-grades');
        Object.keys(data.conditions).forEach(function(grade){var value=data.conditions[grade],card=node('article','db-semi-grade-card'+(grade===data.grade?' is-current':''));card.append(node('span','db-semi-condition-grade',grade),node('h3','',value.name),node('p','',value.description));if(grade===data.grade){var current=node('span','db-semi-current','Este equipamento');current.prepend(icon('check'));card.appendChild(current);}grades.appendChild(card);});panels[0].appendChild(grades);
        var original=root.querySelector('.horizontalTab');
        if(original){var contents=original.querySelector('.resp-tabs-container');if(contents){Array.from(contents.children).forEach(function(child){if(child.tagName!=='DIV')return;child.classList.remove('resp-tab-content-active');child.style.removeProperty('display');panels[1].appendChild(child);});}original.remove();}
        if(!panels[1].children.length)panels[1].appendChild(node('p','','As características disponíveis estão apresentadas nesta ficha.'));
        panels[2].appendChild(node('p','',warranty || 'Consulte as condições de garantia associadas ao equipamento.'));
        var warrantyLink=Array.from(document.querySelectorAll('footer a, #footer a, .footer a')).find(function(a){return /garantia/i.test(a.textContent);});if(warrantyLink)panels[2].appendChild(warrantyLink.cloneNode(true));
        panels[3].appendChild(node('p','',data.shipping || data.delivery || 'Consulte as condições de entrega e devolução disponíveis no site.'));
        Array.from(document.querySelectorAll('footer a, #footer a, .footer a')).filter(function(a){return /entrega|devolu|resolu|condiç/i.test(a.textContent);}).forEach(function(a){panels[3].appendChild(a.cloneNode(true));});
        benefits.after(section);
    }
    function related(root) {
        var part=root.querySelector('#part3');if(!part || typeof window.catalogProductCard!=='function')return;
        var cards=Array.from(part.querySelectorAll('[data-db-semi-card]')).map(function(n){try{return JSON.parse(n.getAttribute('data-db-semi-card'));}catch(error){return null;}}).filter(Boolean);
        var track=part.querySelector('#w_prod .swiper-wrapper');
        if(!cards.length || !track)return;
        // Keep the existing carousel, scroll position, arrow controls and keyboard handlers.
        track.classList.add('db-semi-related-grid');part.classList.add('db-semi-related','catprod_wrap');
        Array.from(track.querySelectorAll('[data-db-semi-card]')).forEach(function(slide,index){
            slide.removeAttribute('onclick');slide.style.removeProperty('cursor');
            slide.innerHTML=window.catalogProductCard(cards[index]);
        });
        var heading=part.querySelector('#w_prod h4');if(heading)heading.textContent='Também te pode interessar';
        var viewport=track.parentElement;if(viewport.swiper)viewport.swiper.update();
    }
    function sticky(root, buy, price) {
        if(!price || !window.IntersectionObserver)return;
        var bar=node('div','db-semi-sticky');bar.hidden=true;var value=node('strong','',price.textContent.replace(/^\s*PVP:\s*/i,'').trim()),button=node('button','','Adicionar ao carrinho');button.type='button';button.addEventListener('click',function(){if(!buy.disabled && buy.isConnected)buy.click();});bar.append(value,button);root.appendChild(bar);
        var onScreen=true;
        function update(){var footer=document.querySelector('footer, #footer, .footer'),blocked=false;document.querySelectorAll('[role="dialog"], #cookie-bar, .cc-window, #cookieConsent, .cookies-banner, .modal.show, [id*="cookie" i], [class*="cookie" i]').forEach(function(n){var r=n.getBoundingClientRect();if(r.width>0 && r.height>0 && r.bottom>0 && r.top<window.innerHeight)blocked=true;});
            if(footer){var f=footer.getBoundingClientRect();if(f.top<window.innerHeight && f.bottom>0)blocked=true;}
            bar.hidden=onScreen || blocked || window.innerWidth>767 || buy.disabled || !buy.isConnected;
        }
        new IntersectionObserver(function(entries){onScreen=entries[0].isIntersecting;update();},{threshold:0}).observe(buy);
        document.addEventListener('scroll',update,true);window.addEventListener('resize',update);new MutationObserver(update).observe(document.body,{childList:true,subtree:true});
    }
    function boot(){document.querySelectorAll('.db-seminovo-detail').forEach(start);}
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot);else boot();
}());
