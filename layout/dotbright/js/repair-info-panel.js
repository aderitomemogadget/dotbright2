(function () {
  'use strict';
  function setupRepairInfo() {
    var root = document.getElementById('reparacao-info');
    if (!root) return;
    var model = root.querySelector('.db-repair-title-model');
    function fitModel() {
      if (!model || !model.clientWidth) return;
      model.style.fontSize = '';
      var available = model.clientWidth;
      var natural = model.scrollWidth;
      if (natural > available) model.style.fontSize = (parseFloat(getComputedStyle(model).fontSize) * available / natural) + 'px';
    }
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    function updateReadMore(panel) {
      var copy = panel.querySelector('.db-repair-copy');
      var button = panel.querySelector('.db-repair-read-more');
      if (!copy || !button || panel.hidden) return;
      button.hidden = !copy.classList.contains('is-expanded') && copy.scrollHeight <= copy.clientHeight + 1;
    }
    function selectTab(tab, focus) {
      tabs.forEach(function (item) {
        var selected = item === tab;
        item.setAttribute('aria-selected', selected ? 'true' : 'false');
        item.tabIndex = selected ? 0 : -1;
        var panel = document.getElementById(item.getAttribute('aria-controls'));
        panel.hidden = !selected;
        if (selected) updateReadMore(panel);
      });
      if (focus) tab.focus();
    }
    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () { selectTab(tab, false); });
      tab.addEventListener('keydown', function (event) {
        var next;
        if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
        else if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
        else if (event.key === 'Home') next = 0;
        else if (event.key === 'End') next = tabs.length - 1;
        else return;
        event.preventDefault();
        selectTab(tabs[next], true);
      });
    });
    root.querySelectorAll('.db-repair-read-more').forEach(function (button) {
      button.addEventListener('click', function () {
        var copy = document.getElementById(button.getAttribute('aria-controls'));
        var expanded = copy.classList.toggle('is-expanded');
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        button.textContent = expanded ? 'Ler menos' : 'Ler mais';
      });
    });
    function refresh() {
      fitModel();
      root.querySelectorAll('.db-repair-tabpanel').forEach(updateReadMore);
    }
    refresh();
    window.addEventListener('resize', refresh);
    window.addEventListener('load', refresh);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitModel);
    if (model && window.ResizeObserver) {
      var previousWidth = 0;
      new ResizeObserver(function () {
        var width = model.clientWidth;
        if (width !== previousWidth) { previousWidth = width; fitModel(); }
      }).observe(model);
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupRepairInfo);
  else setupRepairInfo();
}());
