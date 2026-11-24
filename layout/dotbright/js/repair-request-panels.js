(function () {
    'use strict';
    function setup() {
        var form = document.getElementById('form_repara');
        if (!form || !form.querySelector('[name="r_form_version"][value="2"],[name="r_form_version"][value="3"]')) return;
        function field(id) { return document.getElementById(id); }
        function value(id) { var el = field(id); return el ? el.value.trim() : ''; }
        function choice(name) { var el = form.querySelector('input[name="' + name + '"]:checked'); return el ? el.value : ''; }
        function conditional(id, active, required) {
            var box = field(id);
            box.hidden = !active;
            box.querySelectorAll('input,textarea,button').forEach(function (el) {
                el.disabled = !active;
                el.required = active && required.indexOf(el.name) !== -1;
            });
        }
        function refresh() {
            var access = choice('r_acesso');
            conditional('repair-access-code', access === 'code', ['r_unlock']);
            conditional('repair-access-pattern', access === 'pattern', ['m_padrao']);
            conditional('repair-delivery-fields', choice('r_entrega_tipo') === 'different', ['r_entrega','r_entrega_cp','r_entrega_cidade']);
            if (access === 'pattern' && typeof window.showpattern === 'function') window.showpattern();
        }
        form.addEventListener('change', function (event) {
            if (event.target.name === 'r_acesso' || event.target.name === 'r_entrega_tipo') refresh();
        });
        var clear = field('repair-pattern-clear');
        if (clear) clear.addEventListener('click', function () {
            if (window.pattern && typeof window.pattern.reset === 'function') window.pattern.reset();
            field('m_padrao').value = '';
        });
        function fail(id, text) {
            var el = field(id);
            if (el) { (el.closest('.form-group') || el).classList.add('has-error'); var focus = el.matches('input,textarea') ? el : el.querySelector('input'); if (focus) focus.focus(); }
            if (typeof window.Apprise === 'function') window.Apprise(text);
            return false;
        }
        window.reparacaoValidarOpcoes = function (stage) {
            refresh();
            ['repair-access-options','r_unlock','m_padrao','r_entrega','r_entrega_cp','r_entrega_cidade'].forEach(function (id) {
                var el = field(id); if (el) (el.closest('.form-group') || el).classList.remove('has-error');
            });
            if (stage === 3) {
                var access = choice('r_acesso');
                field('repair-access-options').classList.remove('has-error');
                if (!access) return fail('repair-access-options', 'Escolha como pretende disponibilizar o acesso ao equipamento.');
                if (access === 'code' && !value('r_unlock')) return fail('r_unlock', 'Indique o código de desbloqueio.');
                if (access === 'pattern' && !/^[1-9]{1,9}$/.test(value('m_padrao'))) return fail('m_padrao', 'Desenhe um padrão de desbloqueio válido.');
            }
            if (stage === 4 && choice('r_entrega_tipo') === 'different') {
                var required = ['r_entrega','r_entrega_cp','r_entrega_cidade'];
                for (var i = 0; i < required.length; i++) if (!value(required[i])) return fail(required[i], 'Preencha a morada, o código postal e a localidade de entrega.');
            }
            return true;
        };
        window.reparacaoResumoOpcoes = function () {
            var same = choice('r_entrega_tipo') === 'same';
            var access = form.querySelector('input[name="r_acesso"]:checked');
            var values = {
                resumo_recolha_obs: value('r_recolha_obs'),
                resumo_entrega_tipo: same ? 'Igual à morada de recolha' : 'Morada diferente',
                resumo_entrega: value(same ? 'r_recolha' : 'r_entrega'),
                resumo_entrega_cp: value(same ? 'r_cp' : 'r_entrega_cp'),
                resumo_entrega_cidade: value(same ? 'r_cidade' : 'r_entrega_cidade'),
                resumo_entrega_obs: same ? '' : value('r_entrega_obs'),
                resumo_acesso: access ? access.closest('label').textContent.trim() : ''
            };
            Object.keys(values).forEach(function (id) { var el = field(id); if (el) el.textContent = values[id] || '—'; });
        };
        refresh();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup);
    else setup();
}());
