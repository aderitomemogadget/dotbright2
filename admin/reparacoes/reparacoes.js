(function () {
    'use strict';
    document.querySelectorAll('.ro-detail').forEach(function (root) {
        var bar = root.querySelector('.ro-detail-tabs');
        if (!bar) return;
        var tabs = Array.prototype.slice.call(bar.querySelectorAll('[data-tab]'));
        function activate(tab, focus) {
            tabs.forEach(function (button) {
                var selected = button === tab;
                button.setAttribute('aria-selected', selected ? 'true' : 'false');
                button.tabIndex = selected ? 0 : -1;
                var panel = root.querySelector('#' + button.getAttribute('aria-controls'));
                if (panel) panel.hidden = !selected;
            });
            if (focus) tab.focus();
        }
        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function () { activate(tab, false); });
            tab.addEventListener('keydown', function (event) {
                var next;
                if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
                else if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
                else if (event.key === 'Home') next = 0;
                else if (event.key === 'End') next = tabs.length - 1;
                else return;
                event.preventDefault();
                activate(tabs[next], true);
            });
        });
        var initial = tabs.filter(function (tab) { return tab.dataset.tab === root.dataset.startTab; })[0] || tabs[0];
        if (initial) activate(initial, false);
        bar.hidden = false;
    });
}());
