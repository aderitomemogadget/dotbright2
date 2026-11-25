(function () {
  "use strict";

  var cardSelector = "table.new_quadros, table.new_quadros2, table.new_quadros3, table.new_quadros4, table.new_quadros5, table.new_quadros6";
  var modules = {
    rmas: { label: "RMA", create: "Criar novo RMA", description: "Dados do cliente, equipamento e intervenção técnica." },
    clientes: { label: "cliente", create: "Criar novo cliente", description: "Dados de conta, faturação, entrega e contactos." },
    sa_clientes: { label: "cliente", create: "Criar novo cliente", description: "Identificação, contactos e informação associada ao cliente." },
    rma_lojas: { label: "loja", create: "Criar nova loja", description: "Morada, contactos e identificação pública da loja." },
    localizacoes: { label: "localização", create: "Criar nova localização", description: "Organização e visibilidade da localização." },
    sa_estados_rmas: { label: "estado de RMA", create: "Criar novo estado de RMA", description: "Comportamento, comunicação e textos associados ao estado." },
    sa_descricao_tecnico: { label: "descrição técnica", create: "Criar nova descrição técnica", description: "Texto reutilizável para os relatórios de intervenção." },
    emprestimos: { label: "telemóvel de empréstimo", create: "Criar telemóvel de empréstimo", description: "Equipamento disponibilizado temporariamente ao cliente." },
    gerir_marcas: { label: "marca", create: "Criar nova marca", description: "Identificação da marca de equipamento." },
    gerir_modelos: { label: "modelo", create: "Criar novo modelo", description: "Identificação e associação do modelo à respetiva marca." },
    tipos_avarias: { label: "tipo de avaria", create: "Criar novo tipo de avaria", description: "Classificação usada no diagnóstico técnico." },
    equip_substituicao: { label: "equipamento de substituição", create: "Criar equipamento de substituição", description: "Dados do equipamento de substituição." },
    users: { label: "utilizador", create: "Criar novo utilizador", description: "Acesso, perfil e permissões do utilizador." },
    grupos: { label: "grupo", create: "Criar novo grupo", description: "Configuração do grupo e respetivas permissões." },
    grupos_users: { label: "grupo de utilizadores", create: "Criar grupo de utilizadores", description: "Configuração do grupo de acesso." },
    grupos_clientes: { label: "grupo de clientes", create: "Criar grupo de clientes", description: "Classificação e condições do grupo de clientes." },
    definicoes: { label: "definições", create: "Criar definições", description: "Parâmetros de funcionamento do backoffice." },
    pages: { label: "página", create: "Criar nova página", description: "Conteúdo e opções de publicação." }
  };

  function directTopCard(mainCell) {
    var cards = mainCell.querySelectorAll(cardSelector);
    for (var i = 0; i < cards.length; i += 1) {
      if (!cards[i].parentElement.closest(cardSelector)) return cards[i];
    }
    return null;
  }

  function moduleKey() {
    var parts = window.location.pathname.split("/");
    var rmasIndex = parts.indexOf("rmas");
    return rmasIndex >= 0 && parts[rmasIndex + 1] ? parts[rmasIndex + 1] : "registo";
  }

  function titleCase(value) {
    return value.replace(/[_-]+/g, " ").replace(/\b\w/g, function (letter) { return letter.toUpperCase(); });
  }

  function isCreateForm(form) {
    var params = new URLSearchParams(window.location.search);
    var names = ["ID", "id", "zone_id", "marca_id", "modelo_id", "page_id", "user_id"];
    for (var i = 0; i < names.length; i += 1) {
      if (params.has(names[i]) && String(params.get(names[i])) === "0") return true;
    }

    for (var j = 0; j < names.length; j += 1) {
      var hidden = form.querySelector('input[type="hidden"][name="' + names[j] + '"]');
      if (hidden && String(hidden.value) === "0") return true;
    }
    return false;
  }

  function headingIcon() {
    var icon = document.createElement("span");
    icon.className = "rma-form-heading-icon";
    icon.setAttribute("aria-hidden", "true");
    icon.innerHTML = '<svg viewBox="0 0 24 24" focusable="false"><path d="M7 3.75h7.5L18.25 7.5V20.25H7z"/><path d="M14 3.75V8h4.25M9.75 12h5.5M9.75 15.5h5.5"/></svg>';
    return icon;
  }

  function createHeading(form, card, nav) {
    var key = moduleKey();
    var details = modules[key] || {
      label: titleCase(key),
      create: "Criar " + titleCase(key),
      description: "Criação e edição do registo selecionado."
    };
    var creating = isCreateForm(form);
    var heading = document.createElement("section");
    var copy = document.createElement("div");
    var title = document.createElement("h1");
    var description = document.createElement("p");
    var oldHeader = card.querySelector("table.subtitulo_janela");
    var backLink = oldHeader ? oldHeader.querySelector("a[href]") : null;

    heading.className = "rma-form-heading";
    copy.className = "rma-form-heading-copy";
    title.textContent = creating ? details.create : "Editar " + details.label;
    description.textContent = details.description;

    heading.appendChild(headingIcon());
    copy.appendChild(title);
    copy.appendChild(description);
    heading.appendChild(copy);

    if (backLink) {
      var back = backLink.cloneNode(true);
      back.className = "rma-form-back";
      back.textContent = back.textContent.replace(/^\s*[<‹←]\s*/, "").trim();
      back.setAttribute("aria-label", back.textContent);
      heading.appendChild(back);
      oldHeader.classList.add("rma-legacy-card-header");
      var oldHeaderRow = oldHeader.closest("tr");
      if (oldHeaderRow) oldHeaderRow.classList.add("rma-legacy-header-row");
    }

    card.parentNode.insertBefore(heading, card);
    nav.classList.add("rma-form-navigation");
  }

  function modernizeLegacyFields(card) {
    var tables = card.querySelectorAll("table");
    var legacyParents = [];
    for (var i = 0; i < tables.length; i += 1) {
      var table = tables[i];
      var style = table.getAttribute("style") || "";
      var decorativeImage = table.querySelector('img[src*="pagpub_cx_"]');
      if (style.indexOf("pagpub_cx_fundo") === -1 && !decorativeImage) continue;

      table.classList.add("rma-legacy-field-table");
      if (legacyParents.indexOf(table.parentElement) === -1) legacyParents.push(table.parentElement);
      var cells = table.querySelectorAll("td");
      for (var c = 0; c < cells.length; c += 1) {
        if (cells[c].querySelector('img[src*="pagpub_cx_"]')) cells[c].classList.add("rma-field-decoration");
        if (cells[c].classList.contains("new_preto")) cells[c].classList.add("rma-field-label");
        if (cells[c].querySelector("input, select, textarea")) cells[c].classList.add("rma-field-control");
      }
    }

    for (var p = 0; p < legacyParents.length; p += 1) {
      if (legacyParents[p].querySelectorAll(":scope > table.rma-legacy-field-table").length > 1) {
        legacyParents[p].classList.add("rma-legacy-fields-grid");
      }
    }

    var fieldGroups = card.querySelectorAll(".ct");
    for (var g = 0; g < fieldGroups.length; g += 1) fieldGroups[g].classList.add("rma-field-group");

    var checkboxes = card.querySelectorAll('input[type="checkbox"]');
    for (var t = 0; t < checkboxes.length; t += 1) {
      var checkboxName = (checkboxes[t].name || "").toLowerCase();
      if (checkboxes[t].closest("label.label_check, label.label_check2")) checkboxes[t].classList.add("rma-styled-check");
      if (checkboxName.indexOf("visible") !== -1 || checkboxName.indexOf("visivel") !== -1) checkboxes[t].classList.add("rma-switch");
    }

    var sections = card.querySelectorAll(".titulo_janela, .cart_tit, .bigtitletab, .bigtitletaboff");
    for (var s = 0; s < sections.length; s += 1) sections[s].classList.add("rma-section-control");

    var tabControls = card.querySelectorAll('td[id^="pinta"]');
    for (var a = 0; a < tabControls.length; a += 1) tabControls[a].classList.add("rma-tab-control");

    var formTables = card.querySelectorAll("table");
    for (var f = 0; f < formTables.length; f += 1) {
      if (formTables[f].classList.contains("rma-legacy-field-table")) continue;
      var controls = formTables[f].querySelectorAll("input:not([type=hidden]), select, textarea");
      for (var q = 0; q < controls.length; q += 1) {
        if (controls[q].closest("table") === formTables[f]) {
          formTables[f].classList.add("rma-responsive-fields");
          break;
        }
      }
    }

    var rows = card.querySelectorAll("tr");
    for (var r = 0; r < rows.length; r += 1) {
      if (!rows[r].textContent.trim() && !rows[r].querySelector("input:not([type=hidden]), select, textarea, img, button")) {
        rows[r].classList.add("rma-spacer-row");
      }
    }
  }

  function modernizeActions(form, mainCell) {
    var actionTables = form.querySelectorAll("table.p1, table");
    var candidates = [];
    for (var i = 0; i < actionTables.length; i += 1) {
      var table = actionTables[i];
      if (!mainCell.contains(table)) continue;
      var actionControl = table.querySelector('input[type="submit"], button[type="submit"], input[name*="cancelar"], input[name="Apagar"]');
      if (!actionControl || actionControl.closest("table") !== table) continue;
      if (table.closest("td") && !mainCell.contains(table.closest("td"))) continue;
      table.classList.add("rma-form-actions");
      candidates.push(table);
    }
    if (candidates.length > 1) candidates[candidates.length - 1].classList.add("rma-form-actions-bottom");

    var buttons = form.querySelectorAll('input[type="button"], input[type="submit"], button');
    for (var b = 0; b < buttons.length; b += 1) {
      var value = (buttons[b].value || buttons[b].textContent || "").toLowerCase();
      if (value.indexOf("cancel") !== -1 || value.indexOf("voltar") !== -1) buttons[b].classList.add("rma-button-secondary");
      if (value.indexOf("apagar") !== -1 || value.indexOf("eliminar") !== -1) buttons[b].classList.add("rma-button-danger");
    }
  }

  function protectSubmission(form) {
    form.addEventListener("submit", function (event) {
      window.setTimeout(function () {
        if (event.defaultPrevented) return;
        var submits = form.querySelectorAll('input[type="submit"], button[type="submit"]');
        for (var i = 0; i < submits.length; i += 1) {
          submits[i].disabled = true;
          submits[i].classList.add("rma-is-saving");
          if (submits[i].tagName === "INPUT") {
            submits[i].setAttribute("data-original-value", submits[i].value);
            submits[i].value = "A guardar…";
          }
        }
        form.classList.add("rma-form-submitting");
      }, 0);
    });
  }

  function enhanceForm(form) {
    var nav = form.querySelector("#navigation_horiz");
    if (!nav) return;
    var mainCell = nav.closest("td");
    if (!mainCell) return;
    var card = directTopCard(mainCell);
    if (!card) return;

    document.body.classList.add("rma-form-page");
    form.classList.add("rma-modern-form");
    mainCell.classList.add("rma-form-main");
    card.classList.add("rma-form-card");
    var shell = mainCell.closest("table.t3");
    if (shell) shell.classList.add("rma-form-shell");

    createHeading(form, card, nav);
    modernizeLegacyFields(card);
    modernizeActions(form, mainCell);
    protectSubmission(form);
  }

  function restoreSubmissionState() {
    var buttons = document.querySelectorAll(".rma-is-saving");
    for (var i = 0; i < buttons.length; i += 1) {
      buttons[i].disabled = false;
      if (buttons[i].hasAttribute("data-original-value")) buttons[i].value = buttons[i].getAttribute("data-original-value");
      buttons[i].classList.remove("rma-is-saving");
    }
  }

  function init() {
    var forms = document.querySelectorAll("form");
    for (var i = 0; i < forms.length; i += 1) enhanceForm(forms[i]);
  }

  // This file is loaded with defer: the DOM is complete and enhancement can run
  // before DOMContentLoaded/first paint instead of visibly restyling afterwards.
  init();
  window.addEventListener("pageshow", restoreSubmissionState);
}());
