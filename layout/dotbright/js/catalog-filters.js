(function () {
    'use strict';

    var groups = [];
    var selected = Object.create(null);
    var panel;
    var reset;
    var status;
    var options = [];
    var storageKey = 'dotbright-catalog-db-filters:v1:' + location.pathname;

    function assignments(product) {
        return (Array.isArray(product.a18) ? product.a18 : product.c || []).filter(function (filter) {
            return filter && /^\d+_\d+$/.test(String(filter.c_id)) && filter.cat_name && filter.cat;
        });
    }

    function values(product) {
        var result = Object.create(null);
        assignments(product).forEach(function (filter) {
            var key = String(filter.c_id).split('_')[0];
            if (!result[key]) result[key] = [];
            if (result[key].indexOf(filter.c_id) === -1) result[key].push(filter.c_id);
        });
        return result;
    }

    function matches(productValues, except) {
        return groups.every(function (group) {
            return group.key === except || !selected[group.key].length || selected[group.key].some(function (id) {
                return (productValues[group.key] || []).indexOf(id) !== -1;
            });
        });
    }

    function refresh() {
        try { sessionStorage.setItem(storageKey, JSON.stringify(selected)); } catch (e) { /* Optional state persistence. */ }
        currentpage = 1;
        $.cookies.set('c_pagination_order', null);
        setProducts();
    }

    function make(tag, className, text) {
        var el = document.createElement(tag);
        if (className) el.className = className;
        if (text !== undefined) el.textContent = text;
        return el;
    }

    function initialise(products) {
        if (panel) return true;
        var wrapper = document.querySelector('#filtros_left .filtros_wrapper');
        if (!wrapper || !products.length || document.querySelector('.cat_wrap.ishome')) return false;
        if (products.every(function (p) { return /^REP/.test(p.ref || p.a3 || '') || p.a34 == 1; })) return false;

        // Every group, value and ordering comes from the listing-visible BO assignments.
        var byCategory = Object.create(null);
        products.forEach(function (product) {
            assignments(product).forEach(function (filter) {
                var key = String(filter.c_id).split('_')[0];
                if (!byCategory[key]) {
                    byCategory[key] = { key: key, title: filter.cat_name, order: Number(filter.cat_order) || 0, choices: [] };
                    groups.push(byCategory[key]);
                }
                var group = byCategory[key];
                if (!group.choices.some(function (choice) { return choice.id === filter.c_id; })) {
                    group.choices.push({ id: filter.c_id, title: filter.cat, order: Number(filter.value_order) || 0 });
                }
            });
        });
        groups.sort(function (a, b) { return a.order - b.order || Number(a.key) - Number(b.key); });
        groups.forEach(function (group) {
            selected[group.key] = [];
            group.choices.sort(function (a, b) { return a.order - b.order || a.title.localeCompare(b.title, 'pt', { numeric: true }); });
        });
        try {
            var stored = JSON.parse(sessionStorage.getItem(storageKey));
            if (stored) groups.forEach(function (g) {
                if (Array.isArray(stored[g.key])) selected[g.key] = stored[g.key].filter(function (id) {
                    return g.choices.some(function (choice) { return choice.id === id; });
                });
            });
        } catch (e) { /* Start with all options when no valid state is saved. */ }
        // Migrate any selections restored by the old filter cookies before its engine runs.
        wrapper.querySelectorAll('#c_l input:checked').forEach(function (input) {
            var key = input.value.split('_')[0];
            if (byCategory[key] && byCategory[key].choices.some(function (choice) { return choice.id === input.value; })) {
                if (selected[key].indexOf(input.value) === -1) selected[key].push(input.value);
                input.checked = false;
                input.removeAttribute('checked');
            }
        });

        wrapper.classList.add('db-filter-sidebar');
        panel = make('div', 'db-filter-panel');
        var heading = make('div', 'db-filter-heading');
        heading.appendChild(make('h2', '', 'Filtrar resultados'));
        reset = make('button', 'db-filter-reset', 'Limpar');
        reset.type = 'button';
        reset.addEventListener('click', function () {
            groups.forEach(function (g) { selected[g.key] = []; });
            options.forEach(function (option) { option.input.checked = false; });
            refresh();
        });
        heading.appendChild(reset);
        panel.appendChild(heading);

        groups.forEach(function (group) {
            var section = make('details', 'db-filter-section');
            section.open = true;
            section.appendChild(make('summary', 'db-filter-summary', group.title));
            var list = make('div', 'db-filter-options');
            list.id = 'db-filter-options-' + group.key;
            group.choices.forEach(function (choice, index) {
                var label = make('label', 'db-filter-option');
                var input = make('input');
                input.type = 'checkbox';
                input.value = choice.id;
                input.name = 'catalog_' + group.key;
                input.checked = selected[group.key].indexOf(choice.id) !== -1;
                input.addEventListener('change', function () {
                    selected[group.key] = selected[group.key].filter(function (id) { return id !== choice.id; });
                    if (input.checked) selected[group.key].push(choice.id);
                    refresh();
                });
                label.appendChild(input);
                label.appendChild(make('span', 'db-filter-option-text', choice.title));
                var count = make('span', 'db-filter-count');
                label.appendChild(count);
                if (index >= 7) label.classList.add('db-filter-extra');
                list.appendChild(label);
                options.push({ key: group.key, value: choice.id, input: input, count: count, label: label });
            });
            section.appendChild(list);
            if (group.choices.length > 7) {
                var more = make('button', 'db-filter-more', 'Ver todos');
                more.type = 'button';
                more.setAttribute('aria-expanded', 'false');
                more.setAttribute('aria-controls', list.id);
                more.addEventListener('click', function () {
                    var expanded = section.classList.toggle('db-filter-expanded');
                    more.setAttribute('aria-expanded', String(expanded));
                    more.textContent = expanded ? 'Ver menos' : 'Ver todos';
                });
                section.appendChild(more);
            }
            panel.appendChild(section);
        });

        status = make('div', 'db-filter-status');
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        panel.appendChild(status);
        wrapper.insertBefore(panel, wrapper.firstChild);
        var search = wrapper.querySelector('.pesq_input');
        if (search) {
            search.placeholder = 'Pesquisar produtos';
            search.setAttribute('aria-label', 'Pesquisar produtos nesta categoria');
        }
        var navigation = wrapper.querySelector('.PC');
        if (navigation) {
            var categorySection = make('details', 'db-filter-navigation');
            categorySection.appendChild(make('summary', '', 'Categorias'));
            navigation.parentNode.insertBefore(categorySection, navigation);
            categorySection.appendChild(navigation);
        }
        return true;
    }

    window.DotbrightCatalogFilters = {
        prepare: initialise,
        apply: function (candidates, products) {
            if (!initialise(products)) return candidates;
            var candidateValues = candidates.map(values);
            var counts = Object.create(null);
            groups.forEach(function (g) { counts[g.key] = Object.create(null); });
            candidateValues.forEach(function (v) {
                groups.forEach(function (g) {
                    if (matches(v, g.key)) (v[g.key] || []).forEach(function (id) {
                        counts[g.key][id] = (counts[g.key][id] || 0) + 1;
                    });
                });
            });
            options.forEach(function (option) {
                var count = counts[option.key][option.value] || 0;
                option.count.textContent = '(' + count + ')';
                option.input.disabled = count === 0 && !option.input.checked;
                option.label.classList.toggle('db-filter-unavailable', option.input.disabled);
                option.label.classList.toggle('db-filter-selected', option.input.checked);
            });
            reset.hidden = !groups.some(function (g) { return selected[g.key].length; });
            var result = candidates.filter(function (p, i) { return matches(candidateValues[i]); });
            status.textContent = result.length + (result.length === 1 ? ' resultado' : ' resultados');
            return result;
        }
    };
}());
