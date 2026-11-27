(function () {
    'use strict';
    function el(tag, cls, text) {
        var n=document.createElement(tag);n.className=cls || '';if(text)n.textContent=text;return n;
    }
    function section(title, description) {
        var box=el('section','dbf-section');box.appendChild(el('h2','',title));
        if(description)box.appendChild(el('p','dbf-description',description));return box;
    }
    // Move existing controls, keeping their values, names, handlers and form owner.
    function fields(panel, title, description) {
        if(!panel)return;
        var table=panel.querySelector('table.border1');if(!table)return;
        var box=section(title,description),grid=el('div','dbf-field-grid');box.appendChild(grid);
        Array.from(table.rows).forEach(function(row){
            if(row.closest('table')!==table)return;
            var controls=Array.from(row.querySelectorAll('input:not([type=hidden]),select,textarea'));
            if(!controls.length)return;
            var field=el('div','dbf-field'),first=controls[0],controlCell=first.closest('td');
            if(!controlCell || controlCell.parentElement!==row)return;
            var cells=Array.from(row.cells),labelCell=cells.find(function(cell){return cell!==controlCell && !cell.querySelector('input,select,textarea') && cell.textContent.trim();});
            var label=labelCell?labelCell.textContent.trim().replace(/:$/,''):'';
            if(label){var caption=el('label','dbf-label',label);if(!first.id)first.id='dbf-'+first.name;caption.htmlFor=first.id;field.appendChild(caption);}
            var body=el('div','dbf-control');
            cells.forEach(function(cell){if(cell===labelCell)return;if(cell.textContent.trim() || cell.querySelector('input,select,textarea,img,a')){while(cell.firstChild)body.appendChild(cell.firstChild);}});
            if(first.type==='checkbox')field.classList.add('dbf-toggle');
            if(first.name==='prod_txt1' || first.name==='Margem_formula')field.classList.add('dbf-wide');
            field.appendChild(body);grid.appendChild(field);
        });
        if(!grid.children.length)return;
        table.before(box);table.hidden=true;
    }
    function images(panel) {
        if(!panel)return;var table=panel.querySelector('table.border1');if(!table)return;
        var box=section('Imagens da família','Gere o ícone, a imagem de partilha e a imagem da categoria.'),grid=el('div','dbf-image-grid');box.appendChild(grid);
        var titles={ICON_img:'Ícone da família',SEO_img:'Imagem de partilha (SEO)',img_peq1:'Imagem da categoria'};
        Array.from(table.rows).forEach(function(row){
            if(row.closest('table')!==table)return;
            var file=row.querySelector('input[type=file]'),links=row.querySelectorAll('a'),image=row.querySelector('.imagens_400 img');
            if(!file && !links.length && !image)return;
            var title=file?(titles[file.name] || 'Imagem'):image?(/_ICON_/i.test(image.src)?titles.ICON_img:/_seo_/i.test(image.src)?titles.SEO_img:titles.img_peq1):'Partilhar categoria';
            var card=el('article','dbf-image-card'+(!file && !image?' dbf-share-card':''));card.appendChild(el('h3','',title));
            var body=el('div','dbf-image-body');Array.from(row.cells).forEach(function(cell){while(cell.firstChild)body.appendChild(cell.firstChild);});card.appendChild(body);grid.appendChild(card);
            if(file){
                file.setAttribute('aria-label',title);
                body.querySelectorAll('input[type=checkbox]').forEach(function(check){check.setAttribute('aria-label','Apagar '+title.toLowerCase());});
                var preview=body.querySelector('.imagens_400 img');
                if(!preview){preview=el('img','dbf-upload-preview');preview.hidden=true;preview.alt='Pré-visualização';body.insertBefore(preview,body.firstChild);}
                var original=preview.getAttribute('src'),objectUrl;
                file.addEventListener('change',function(){if(objectUrl)URL.revokeObjectURL(objectUrl);if(file.files.length && /^image\//.test(file.files[0].type)){objectUrl=URL.createObjectURL(file.files[0]);preview.src=objectUrl;preview.hidden=false;}else{preview.hidden=!original;if(original)preview.src=original;else preview.removeAttribute('src');}});
            }
        });
        if(grid.children.length){table.before(box);table.hidden=true;}
    }
    function start() {
        var form=document.querySelector('.db-family-editor');if(!form || form.classList.contains('dbf-ready'))return;
        var master=form.querySelector('#masterdiv'),tabs=master?Array.from(master.querySelectorAll('td[id^=pinta]')):[];
        var name=form.querySelector('[name=cabecalho]'),heading=form.querySelector('.panel-heading');
        if(heading){heading.classList.add('dbf-heading');var text=el('div');text.append(el('span','dbf-eyebrow','Catálogo / Famílias de produtos'),el('h1','',name && name.value?name.value:'Nova família'),el('p','','Organiza a categoria e a sua apresentação na loja.'));heading.replaceChildren(text);
            var visible=form.querySelector('[name=visivel]');if(visible){var badge=el('span','dbf-status');function status(){badge.textContent=visible.checked?'Visível na loja':'Oculta na loja';badge.classList.toggle('is-visible',visible.checked);}status();visible.addEventListener('change',status);heading.appendChild(badge);}
            if(name)name.addEventListener('input',function(){heading.querySelector('h1').textContent=name.value || 'Nova família';});
        }
        if(tabs.length){var nav=tabs[0].closest('table');nav.classList.add('dbf-tabs');nav.setAttribute('role','tablist');nav.setAttribute('aria-label','Editar família');
            function sync(){tabs.forEach(function(tab){var id=tab.getAttribute('aria-controls'),panel=document.getElementById(id),active=panel && panel.style.display==='block';tab.setAttribute('aria-selected',String(active));tab.tabIndex=active?0:-1;});}
            tabs.forEach(function(tab){var id=tab.id.replace('pinta','sub'),panel=document.getElementById(id);tab.setAttribute('role','tab');tab.setAttribute('aria-controls',id);if(panel){panel.setAttribute('role','tabpanel');panel.setAttribute('aria-labelledby',tab.id);}tab.addEventListener('click',sync);tab.addEventListener('keydown',function(event){var i=tabs.indexOf(tab),next=event.key==='ArrowRight'?(i+1)%tabs.length:event.key==='ArrowLeft'?(i+tabs.length-1)%tabs.length:event.key==='Home'?0:event.key==='End'?tabs.length-1:-1;if(next>=0){event.preventDefault();tabs[next].click();tabs[next].focus();}else if(event.key==='Enter' || event.key===' '){event.preventDefault();tab.click();}});});
            new MutationObserver(sync).observe(master,{subtree:true,attributes:true,attributeFilter:['style']});sync();
        }
        fields(form.querySelector('#sub1'),'Dados da família','Identificação, organização e visibilidade da categoria.');
        fields(form.querySelector('#sub4'),'Margens e portes','Condições aplicadas a esta família de produtos.');
        images(form.querySelector('#sub2'));
        form.classList.add('dbf-ready');
    }
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',start);else start();
}());
