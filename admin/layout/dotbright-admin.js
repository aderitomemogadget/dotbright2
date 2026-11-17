(function () {
    'use strict';

    function initialize() {
        var root = document.querySelector('.db-admin');
        var content = document.getElementById('content');
        if (!root || !content) return;

        root.querySelectorAll('input[placeholder]').forEach(function (field) {
            if (!field.labels.length && !field.hasAttribute('aria-label')) {
                field.setAttribute('aria-label', field.placeholder);
            }
        });

        var search = content.querySelector('input[name=" m_pesquisa"], input[name=m_pesquisa]');
        if (search && !search.hasAttribute('aria-label')) search.setAttribute('aria-label', 'Pesquisar');

        var editor = root.classList.contains('db-admin-content');
        var workspace = content.querySelector('table.t3');
        if (workspace && workspace.rows[0] && workspace.rows[0].cells.length === 3) {
            workspace.classList.add('db-workspace');
            workspace.rows[0].cells[2].classList.add('db-tools');
        }

        if (!editor) {
            content.querySelectorAll('table.p2, table.p1').forEach(function (table) {
                if (!table.closest('.db-tools')) table.classList.add('db-browse-toolbar');
            });
        }

        function directControls(cell, row) {
            return Array.from(cell.querySelectorAll('input:not([type=hidden]),select,textarea')).filter(function (control) {
                return control.closest('tr') === row;
            });
        }

        if (editor) {
            content.querySelectorAll('tr').forEach(function (row) {
                var cells = Array.from(row.cells || []);
                var controls = cells.flatMap(function (cell) { return directControls(cell, row); });
                if (!controls.length) return;

                if (controls.some(function (control) {
                    return /Gravar|Guardar|Pré-visualizar|Cancelar/.test(control.value || '') && /button|submit/.test(control.type);
                })) {
                    var actionsTable = row.closest('table');
                    if (actionsTable) actionsTable.classList.add('db-actions');
                    return;
                }

                if (row.closest('.titulo_janela')) {
                    row.classList.add('db-status-row');
                    return;
                }

                if (cells.length > 6 && controls.length > 5 && controls.every(function (control) {
                    return control.type === 'text' || control.type === 'number' || control.disabled;
                })) {
                    var priceTable = row.closest('table');
                    if (priceTable) priceTable.classList.add('db-price-matrix');
                    return;
                }

                if (controls.some(function (control) {
                    return control.closest('.cke') || control.type === 'button' || control.type === 'submit';
                })) return;
                if (cells.some(function (cell) { return cell.querySelector('table,iframe,.cke'); })) return;

                var labelCount = cells.filter(function (cell) {
                    return cell.textContent.trim() && !directControls(cell, row).length && !cell.querySelector('img');
                }).length;
                if (!labelCount || controls.some(function (control) { return /checkbox|radio/.test(control.type); })) return;

                row.classList.add('db-field-row');
                var formTable = row.closest('table');
                if (formTable) formTable.classList.add('db-form-table');
                if (controls.some(function (control) { return control.name === 'descricao'; })) row.classList.add('db-identity-row');

                var label = '';
                cells.forEach(function (cell) {
                    var fields = directControls(cell, row);
                    if (fields.length) {
                        cell.classList.add('db-control-cell');
                        fields.forEach(function (field) {
                            if (!field.labels.length && !field.hasAttribute('aria-label') && label) {
                                field.setAttribute('aria-label', label.replace(/:\s*$/, ''));
                            }
                        });
                    } else if (!cell.textContent.trim() && !cell.querySelector('img,a,button')) {
                        cell.classList.add('db-empty-cell');
                    } else {
                        cell.classList.add('db-label-cell');
                        label = cell.textContent.trim();
                    }
                });
            });
        }

        content.querySelectorAll('table.db-price-matrix, table#IDTabelaPrecos').forEach(function (table) {
            wrapTable(table, 'Tabela de preços e stock');
        });

        if (editor) {
            var text = content.querySelector('textarea[name=texto]');
            if (text && text.parentElement) text.parentElement.classList.add('db-content-editor');

            var seo = content.querySelector('#SEO');
            if (root.classList.contains('db-admin-products') && seo) {
                var seoPanel = document.getElementById('sub30');
                if (seoPanel) seoPanel.appendChild(seo);
            }
            if (seo) {
                seo.querySelectorAll('td').forEach(function (cell) {
                    if (!cell.textContent.trim() && !cell.querySelector('input,select,textarea,img,table,iframe,button,a')) {
                        cell.classList.add('db-empty-cell');
                    }
                });
            }

            var nav = document.createElement('nav');
            nav.className = 'db-editor-nav';
            nav.setAttribute('aria-label', 'Secções do editor');
            var targets = root.classList.contains('db-admin-pages') ? [
                ['Conteúdo', function () { return document.getElementById('cke_texto') || (text && text.parentElement); }],
                ['SEO e partilha', function () { if (seo) seo.style.display = 'block'; return seo; }],
                ['Definições', function () { return content.querySelector('.db-status-row'); }]
            ] : [];
            targets.forEach(function (target) {
                var button = document.createElement('button');
                button.type = 'button';
                button.textContent = target[0];
                button.addEventListener('click', function () {
                    var node = target[1]();
                    if (node) node.scrollIntoView({ block: 'start', behavior: 'smooth' });
                });
                nav.appendChild(button);
            });
            if (nav.children.length && workspace) workspace.before(nav);
        }

        var firstTab = content.querySelector('#pinta1');
        if (firstTab) {
            var bar = firstTab.closest('.tabs-bar,table');
            if (bar) {
                bar.classList.add('db-product-tabs');
                bar.setAttribute('role', 'tablist');
                bar.setAttribute('aria-label', 'Ficha de produto');
                var tabs = Array.from(bar.querySelectorAll('[id^=pinta]'));

                function reflectTabs() {
                    tabs.forEach(function (tab) {
                        var panel = document.getElementById('sub' + tab.id.replace('pinta', ''));
                        tab.setAttribute('aria-selected', String(!!panel && getComputedStyle(panel).display !== 'none'));
                    });
                }

                tabs.forEach(function (tab) {
                    var panel = document.getElementById('sub' + tab.id.replace('pinta', ''));
                    tab.setAttribute('role', 'tab');
                    tab.tabIndex = 0;
                    if (panel) {
                        tab.setAttribute('aria-controls', panel.id);
                        panel.setAttribute('role', 'tabpanel');
                        panel.setAttribute('aria-labelledby', tab.id);
                        new MutationObserver(reflectTabs).observe(panel, { attributes: true, attributeFilter: ['style'] });
                    }
                    tab.addEventListener('keydown', function (event) {
                        var next;
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            tab.click();
                        }
                        if (event.key === 'ArrowRight') next = tabs[(tabs.indexOf(tab) + 1) % tabs.length];
                        if (event.key === 'ArrowLeft') next = tabs[(tabs.indexOf(tab) + tabs.length - 1) % tabs.length];
                        if (next) {
                            event.preventDefault();
                            next.focus();
                            next.click();
                        }
                    });
                });
                reflectTabs();
            }
        }

        function wrapTable(table, label) {
            if (!table || table.closest('.db-table-scroll')) return;
            var wrapper = document.createElement('div');
            wrapper.className = 'db-table-scroll';
            wrapper.tabIndex = 0;
            if (!editor) wrapper.style.maxWidth = contentWidth() + 'px';
            wrapper.setAttribute('role', 'region');
            wrapper.setAttribute('aria-label', label);
            table.before(wrapper);
            wrapper.appendChild(table);
        }

        if (!editor) {
            content.querySelectorAll('table').forEach(function (table) {
                if (table.closest('.db-table-scroll') || table.querySelector('table') || table.classList.contains('t3')) return;
                var maxCells = Array.from(table.rows).reduce(function (n, row) {
                    return Math.max(n, row.cells.length);
                }, 0);
                if (table.rows.length > 2 && maxCells > 4) {
                    table.classList.add('db-data-table');
                    wrapTable(table, 'Lista de registos');
                }
            });

            content.querySelectorAll('td[width]').forEach(function (cell) {
                if (parseInt(cell.getAttribute('width'), 10) >= 150 && !cell.closest('.db-table-scroll')) {
                    cell.classList.add('db-wide-layout-cell');
                }
            });

            content.querySelectorAll('tr').forEach(function (row) {
                if (row.closest('.db-table-scroll,.db-browse-toolbar,.db-tools') || row.cells.length < 2 || row.cells.length > 4) return;
                var fields = Array.from(row.cells).flatMap(function (cell) { return directControls(cell, row); });
                if (!fields.some(function (field) { return field.tagName === 'SELECT' || field.type === 'text'; })) return;
                if (fields.some(function (field) { return /checkbox|radio/.test(field.type); })) return;
                if (Array.from(row.cells).some(function (cell) { return cell.querySelector('table'); })) return;
                row.classList.add('db-compact-row');
                var compactTable = row.closest('table');
                if (compactTable) compactTable.classList.add('db-compact-table');
            });
        }

        function contentWidth() {
            var style = getComputedStyle(content);
            return Math.max(0, content.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight));
        }

        function containWideTables() {
            if (editor) return;
            content.querySelectorAll('.db-table-scroll').forEach(function (wrapper) {
                wrapper.style.maxWidth = contentWidth() + 'px';
            });
        }

        containWideTables();
        if (document.fonts) document.fonts.ready.then(containWideTables);
        window.addEventListener('resize', function () { window.requestAnimationFrame(containWideTables); });

        if (root.classList.contains('db-admin-products') && firstTab && workspace) modernizeProductEditor();

        function modernizeProductEditor() {
            // Reuse the approved On4tech card, product-meta and information-grid
            // organization. Existing controls are moved, never cloned or renamed.
            var main = workspace.rows[0].cells[0];
            if (!main || main.querySelector('.db-ref-shell')) return;
            var shell = document.createElement('div');
            shell.className = 'db-ref-shell';
            Array.from(main.childNodes).forEach(function (node) { shell.appendChild(node); });
            main.appendChild(shell);
            Array.from(shell.children).forEach(function (panel) {
                if (!panel.classList.contains('panel')) return;
                if (panel.querySelector('.panel-heading')) panel.classList.add('db-ref-heading');
                if (panel.querySelector('.titulo_janela')) {
                    panel.classList.add('db-ref-breadcrumb');
                    panel.setAttribute('aria-label', 'Localização do produto');
                }
            });

            var formTable = content.querySelector('#sub1 [name=ref]');
            formTable = formTable && formTable.closest('table');
            if (formTable) {
                formTable.classList.add('db-product-form');
                var rows = Array.from(formTable.rows);
                var oldBodies = Array.from(formTable.tBodies);
                var group;
                function card(title, existingTitle) {
                    var body = document.createElement('tbody');
                    body.className = 'db-ref-card';
                    body.dataset.section = title;
                    formTable.appendChild(body);
                    if (!existingTitle) {
                        var row = body.insertRow();
                        var cell = row.insertCell();
                        var heading = document.createElement('h2');
                        row.className = 'db-ref-card-title';
                        cell.colSpan = 4;
                        heading.textContent = title;
                        cell.appendChild(heading);
                    }
                    return body;
                }
                group = card('Dados gerais', false);
                rows.forEach(function (row, index) {
                    var legend = row.querySelector('legend');
                    if (legend) {
                        group = card(legend.textContent.trim(), true);
                        row.classList.add('db-ref-card-title');
                    }
                    if (index === 0 && /Data cria/.test(row.textContent)) row.classList.add('db-product-metadata');
                    group.appendChild(row);
                });
                oldBodies.forEach(function (body) { if (!body.children.length) body.remove(); });

                Array.from(formTable.tBodies).forEach(function (body) {
                    if (!/^Outras informa/.test(body.dataset.section || '')) return;
                    var infoRows = Array.from(body.rows).filter(function (row) {
                        return !row.classList.contains('db-ref-card-title');
                    });
                    var outerRow = body.insertRow();
                    var outerCell = outerRow.insertCell();
                    var grid = document.createElement('div');
                    outerRow.className = 'db-ref-info-row';
                    outerCell.colSpan = 4;
                    grid.className = 'db-ref-info-grid';
                    outerCell.appendChild(grid);
                    var sections = ['Dimensões & Logística', 'Stock & Integração', 'Estado do Produto'].map(function (title) {
                        var section = document.createElement('section');
                        var heading = document.createElement('h3');
                        var table = document.createElement('table');
                        section.className = 'db-ref-info-section';
                        heading.textContent = title;
                        table.setAttribute('role', 'presentation');
                        section.append(heading, table);
                        grid.appendChild(section);
                        return table.createTBody();
                    });
                    infoRows.forEach(function (row) {
                        var fields = Array.from(row.querySelectorAll('input,select,textarea'));
                        var names = fields.map(function (field) { return field.name; });
                        var sectionIndex = names.some(function (name) {
                            return /^(carrinho|aproved|tem_prod_destaque_homepage)$/.test(name);
                        }) ? 2 : names.some(function (name) {
                            return /^(estados_produto|sync|preco_custo_custom|c_submissao_ficheiro|encomenda_sem_stock|produto_standard)$/.test(name);
                        }) ? 1 : 0;
                        if (names.length === 3 && names.every(function (name) { return /^(altura|largura|profundidade)$/.test(name); })) {
                            fields.forEach(function (field) {
                                var dimensionRow = document.createElement('tr');
                                var label = document.createElement('td');
                                var control = document.createElement('td');
                                dimensionRow.className = 'db-field-row';
                                label.className = 'db-label-cell';
                                control.className = 'db-control-cell';
                                label.textContent = { altura: 'Altura (cm)', largura: 'Largura (cm)', profundidade: 'Profundidade (cm)' }[field.name];
                                control.appendChild(field);
                                dimensionRow.append(label, control);
                                sections[0].appendChild(dimensionRow);
                            });
                            row.remove();
                        } else {
                            if (!fields.length && /Dimensões/.test(row.textContent)) row.classList.add('db-ref-dimension-caption');
                            sections[sectionIndex].appendChild(row);
                        }
                    });
                });
            }

            content.querySelectorAll('#sub1 input[type=checkbox]').forEach(function (field) {
                var row = field.closest('tr');
                if (row) row.classList.add('db-product-options');
                if (!field.labels.length && !field.hasAttribute('aria-label')) {
                    var rowLabel = row && row.cells.length ? row.cells[0].textContent.trim().replace(/:\s*$/, '') : '';
                    field.setAttribute('aria-label', field.name === 'kk' ? 'Não Kuanto Kusta' : rowLabel || 'Opção');
                }
            });

            content.querySelectorAll('.db-price-matrix').forEach(function (table) {
                if (!table.rows.length) return;
                Array.from(table.rows[0].cells).forEach(function (cell, index) {
                    var text = cell.textContent.trim();
                    var cls = /Preço site/i.test(text) ? 'db-price-pvp' : /Margem/.test(text) ? 'db-price-margin' : /^Stock/.test(text) ? 'db-price-stock' : '';
                    if (cls) Array.from(table.rows).forEach(function (row) {
                        if (row.cells[index]) row.cells[index].classList.add(cls);
                    });
                });
            });

            content.querySelectorAll('table[id=IDTabelaPrecos]').forEach(function (table) {
                if (!table.querySelector('input[type=checkbox]')) return;
                table.classList.add('db-promotion-options');
                var wrapper = table.closest('.db-table-scroll');
                if (wrapper && !wrapper.closest('.db-ref-promo')) {
                    var promo = document.createElement('section');
                    var heading = document.createElement('h3');
                    promo.className = 'db-ref-promo';
                    heading.textContent = 'Promoção';
                    wrapper.before(promo);
                    promo.append(heading, wrapper);
                }
                var labels = table.rows[0] && Array.from(table.rows[0].cells).map(function (cell) { return cell.textContent.trim(); });
                if (labels && table.rows[1]) Array.from(table.rows[1].cells).forEach(function (cell, index) {
                    var checkbox = cell.querySelector('input[type=checkbox]');
                    if (checkbox) checkbox.setAttribute('aria-label', labels[index]);
                });
                table.querySelectorAll('input[type=date]').forEach(function (field) {
                    field.setAttribute('aria-label', /ini/.test(field.name) ? 'Início da promoção' : 'Fim da promoção');
                });
            });

            content.querySelectorAll('#sub1 span').forEach(function (span) {
                if (span.textContent.indexOf('O campo IVA serve') >= 0 && span.parentElement.tagName === 'DIV') {
                    span.parentElement.classList.add('db-ref-help');
                }
            });
            var tools = content.querySelector('.db-tools');
            if (tools) tools.setAttribute('aria-label', 'Ações do produto');
            content.querySelectorAll('.db-actions input').forEach(function (button) {
                button.classList.add(/Gravar|Guardar/.test(button.value) ? 'db-action-save' : 'db-action-secondary');
            });
            root.classList.add('db-on4-product');
        }
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
})();
