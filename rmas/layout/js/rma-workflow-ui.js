(function () {
  "use strict";

  var LIST_RETURN_KEY = "dotbright.rmas.list.return";
  var QUICK_ACTIONS_KEY = "dotbright.rmas.quickActions.collapsed";

  function textOf(control) {
    if (!control) return "";
    if (control.tagName === "SELECT") {
      var option = control.options[control.selectedIndex];
      return option ? option.textContent.replace(/\s+/g, " ").trim() : "";
    }
    return String(control.value || control.textContent || "").replace(/\s+/g, " ").trim();
  }

  function selectedValue(selector) {
    return textOf(document.querySelector(selector));
  }

  function addSummaryItem(list, label, value, modifier) {
    if (!value || value === "----------" || value === "NOVO" || value === "NOVA") return;
    var wrapper = document.createElement("div");
    var term = document.createElement("dt");
    var detail = document.createElement("dd");
    wrapper.className = "rma-workflow-summary-item" + (modifier ? " rma-workflow-summary-item--" + modifier : "");
    term.textContent = label;
    detail.textContent = value;
    detail.title = value;
    wrapper.appendChild(term);
    wrapper.appendChild(detail);
    list.appendChild(wrapper);
    return detail;
  }

  function statusClass(value) {
    if (/conclu|entregue|reparado/i.test(value)) return "success";
    if (/aguarda|espera|pendente|or.amento|laborat.rio/i.test(value)) return "warning";
    if (/cancel|abandon|anomalia|sem repara|recus/i.test(value)) return "danger";
    return "info";
  }

  function buildRmaSummary(form) {
    var heading = form.querySelector(".rma-form-heading");
    if (!heading || form.querySelector(".rma-workflow-summary")) return;
    var summary = document.createElement("dl");
    var tracking = selectedValue('input[name="m_tracking"]');
    var state = selectedValue("select#parent");
    var brand = selectedValue("select#sel_marca") || selectedValue('input[name="nova_marca"]');
    var model = selectedValue("select#sel_modelo") || selectedValue('input[name="novo_modelo"]');
    var firstName = selectedValue("#cliente_nome");
    var surname = selectedValue("#cliente_sobrenome");
    var lastName = selectedValue("#cliente_apelido");
    var customer = [firstName, surname, lastName].filter(Boolean).join(" ");
    var origin = selectedValue("select#m_origem");

    summary.className = "rma-workflow-summary";
    addSummaryItem(summary, "Tracking", tracking, "tracking");
    var status = addSummaryItem(summary, "Estado", state, "status");
    if (status) status.className = "rma-workflow-status rma-workflow-status--" + statusClass(state);
    addSummaryItem(summary, "Equipamento", [brand, model].filter(Boolean).join(" / "), "equipment");
    addSummaryItem(summary, "Cliente", customer, "customer");
    addSummaryItem(summary, "Proveniência", origin, "origin");
    if (summary.children.length) heading.parentNode.insertBefore(summary, heading.nextSibling);
  }

  function prependSectionHeading(table, title, icon, modifier) {
    if (!table || table.querySelector(":scope > tbody > tr:first-child > td > .rma-workflow-section-heading")) return;
    var firstCell = table.querySelector(":scope > tbody > tr:first-child > td");
    if (!firstCell) return;
    var heading = document.createElement("div");
    heading.className = "rma-workflow-section-heading";
    heading.setAttribute("data-icon", icon);
    heading.textContent = title;
    table.classList.add("rma-workflow-section", "rma-workflow-section--" + modifier);
    firstCell.insertBefore(heading, firstCell.firstChild);
  }

  function enhanceRmaSections(form) {
    var summaryTables = form.querySelectorAll("#sub1 > table.border1");
    if (summaryTables[0]) prependSectionHeading(summaryTables[0], "Cliente e enquadramento", "◉", "customer");
    if (summaryTables[1]) prependSectionHeading(summaryTables[1], "Equipamento e diagnóstico", "▣", "equipment");

    var report = form.querySelector("#sub3 > table.border1");
    if (report) prependSectionHeading(report, "Relatório de reparação", "✓", "report");
    var images = form.querySelector("#list_images");
    var imageTable = images && images.parentElement ? images.parentElement.closest("table") : null;
    if (imageTable && imageTable !== report) prependSectionHeading(imageTable, "Imagens e anexos", "▧", "attachments");

    var parts = form.querySelector("#tabela-reparacao");
    if (parts) {
      parts.classList.add("rma-parts-table");
      var heading = document.createElement("div");
      heading.className = "rma-parts-heading";
      parts.parentNode.insertBefore(heading, parts);
      var siblingNodes = Array.prototype.slice.call(parts.parentNode.childNodes);
      for (var n = 0; n < siblingNodes.length; n += 1) {
        if (siblingNodes[n].nodeType === 3 && /Invent.rio de Pe.as/i.test(siblingNodes[n].nodeValue || "")) siblingNodes[n].nodeValue = "";
      }
      var addLink = parts.parentNode.querySelector('a[onclick*="adicionaLinha"]');
      if (addLink) {
        addLink.classList.add("rma-parts-add");
        addLink.textContent = "Adicionar peça";
      }
    }
  }

  function actionGroup(label) {
    if (/criar|novo rma/i.test(label)) return { key: "create", label: "Criar" };
    if (/imprim/i.test(label)) return { key: "print", label: "Imprimir" };
    if (/assin/i.test(label)) return { key: "sign", label: "Assinar" };
    if (/grav|guard|regist/i.test(label)) return { key: "save", label: "Guardar" };
    return { key: "other", label: "Outras" };
  }

  function enhanceQuickActions(form) {
    var tables = form.querySelectorAll("table");
    var card = null;
    for (var i = 0; i < tables.length; i += 1) {
      if (/^\s*(FAST TOOLS|FERRAMENTAS)\s*$/i.test(tables[i].rows.length ? tables[i].rows[0].textContent : "") && tables[i].querySelector("input.botao, button.botao")) {
        card = tables[i];
        break;
      }
    }
    if (!card || card.classList.contains("rma-quick-actions")) return;
    card.classList.add("rma-quick-actions");

    var headerCell = card.rows[0] ? card.rows[0].cells[0] : null;
    if (headerCell) {
      headerCell.className = "rma-quick-actions-header";
      headerCell.textContent = "";
      var title = document.createElement("div");
      var strong = document.createElement("strong");
      var toggle = document.createElement("button");
      title.className = "rma-quick-actions-title";
      strong.textContent = "Ações rápidas";
      toggle.type = "button";
      toggle.className = "rma-quick-actions-toggle";
      toggle.setAttribute("aria-label", "Recolher ações rápidas");
      toggle.setAttribute("aria-expanded", "true");
      title.appendChild(strong);
      title.appendChild(toggle);
      headerCell.appendChild(title);

      try {
        if (window.localStorage.getItem(QUICK_ACTIONS_KEY) === "1") card.classList.add("is-collapsed");
      } catch (ignore) {}
      toggle.setAttribute("aria-expanded", card.classList.contains("is-collapsed") ? "false" : "true");
      toggle.addEventListener("click", function () {
        card.classList.toggle("is-collapsed");
        var expanded = !card.classList.contains("is-collapsed");
        toggle.setAttribute("aria-expanded", expanded ? "true" : "false");
        toggle.setAttribute("aria-label", expanded ? "Recolher ações rápidas" : "Expandir ações rápidas");
        try { window.localStorage.setItem(QUICK_ACTIONS_KEY, expanded ? "0" : "1"); } catch (ignore) {}
      });
    }

    var rows = Array.prototype.slice.call(card.rows, 1);
    var groupedRows = { create: [], print: [], sign: [], save: [], other: [] };
    var primary = null;
    for (var r = 0; r < rows.length; r += 1) {
      var button = rows[r].querySelector("input.botao, button.botao");
      if (!button) {
        rows[r].parentNode.removeChild(rows[r]);
        continue;
      }
      var label = textOf(button);
      var group = actionGroup(label);
      rows[r].classList.add("rma-quick-actions-row", "rma-quick-actions-row--" + group.key);
      button.removeAttribute("style");
      if (button.disabled) {
        rows[r].classList.add("rma-quick-actions-row--disabled");
        button.title = button.getAttribute("data-disabled-reason") || "Disponível depois de gravar o RMA";
      } else {
        button.title = label;
      }
      groupedRows[group.key].push(rows[r]);
      if (group.key === "save") primary = rows[r];
      else if (!primary && group.key === "create") primary = rows[r];
    }

    var body = card.tBodies[0];
    var groupOrder = ["create", "print", "sign", "save", "other"];
    var groupLabels = { create: "Criar", print: "Imprimir", sign: "Assinar", save: "Guardar", other: "Outras" };
    for (var g = 0; g < groupOrder.length; g += 1) {
      var key = groupOrder[g];
      if (!groupedRows[key].length) continue;
      var groupRow = document.createElement("tr");
      var groupCell = document.createElement("td");
      groupRow.className = "rma-quick-actions-group rma-quick-actions-group--" + key;
      groupCell.textContent = groupLabels[key];
      groupRow.appendChild(groupCell);
      body.appendChild(groupRow);
      for (var action = 0; action < groupedRows[key].length; action += 1) body.appendChild(groupedRows[key][action]);
    }
    if (primary) primary.classList.add("rma-quick-actions-row--primary");

    if (card.querySelector("input.botao:disabled, button.botao:disabled") && headerCell) {
      var note = document.createElement("span");
      note.className = "rma-quick-actions-note";
      note.textContent = "Guardar primeiro para ativar impressão e assinatura";
      headerCell.appendChild(note);
    }
  }

  function previewFile(input) {
    var oldPreview = input.parentNode.querySelector(":scope > .rma-file-preview");
    if (oldPreview) {
      var oldUrl = oldPreview.getAttribute("data-object-url");
      if (oldUrl) window.URL.revokeObjectURL(oldUrl);
      oldPreview.remove();
    }
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var preview = document.createElement("div");
    var copy = document.createElement("div");
    var name = document.createElement("strong");
    var note = document.createElement("span");
    preview.className = "rma-file-preview";
    name.textContent = file.name;
    note.textContent = "Pré-visualização local — será enviada apenas ao guardar.";
    copy.appendChild(name);
    copy.appendChild(note);
    if (/^image\//i.test(file.type)) {
      var image = document.createElement("img");
      var url = window.URL.createObjectURL(file);
      image.src = url;
      image.alt = "Pré-visualização de " + file.name;
      preview.setAttribute("data-object-url", url);
      preview.appendChild(image);
    }
    preview.appendChild(copy);
    input.parentNode.appendChild(preview);
  }

  function enhanceUploads(form) {
    form.addEventListener("change", function (event) {
      if (event.target.matches('#list_images input[type="file"]')) previewFile(event.target);
    });
  }

  function restoreListReturn(form) {
    var back = form.querySelector(".rma-form-back");
    if (!back) return;
    try {
      var saved = window.sessionStorage.getItem(LIST_RETURN_KEY);
      if (!saved) return;
      var url = new URL(saved, window.location.origin);
      if (url.origin === window.location.origin && /\/rmas\/rmas\/list\.php$/i.test(url.pathname)) {
        var returnTarget = url.pathname + url.search;
        back.href = returnTarget;
        back.title = "Voltar à listagem mantendo os filtros e a página";
        var cancelButtons = form.querySelectorAll('input[name^="cancelar"], button[name^="cancelar"]');
        for (var i = 0; i < cancelButtons.length; i += 1) {
          cancelButtons[i].setAttribute("onmouseup", "window.open(" + JSON.stringify(returnTarget) + ",'_self')");
          cancelButtons[i].title = "Cancelar e voltar mantendo os filtros e a página";
        }
      }
    } catch (ignore) {}
  }

  function enhanceSaveFeedback(form) {
    var live = document.createElement("div");
    live.className = "rma-save-status";
    live.setAttribute("role", "status");
    live.setAttribute("aria-live", "polite");
    live.hidden = true;
    document.body.appendChild(live);
    form.addEventListener("submit", function (event) {
      var removeImages = form.querySelectorAll('input[name^="Apagar_img"]:checked');
      if (removeImages.length && !window.confirm("Confirma a remoção das imagens selecionadas ao guardar?")) {
        event.preventDefault();
        return;
      }
      window.setTimeout(function () {
        if (event.defaultPrevented) return;
        live.textContent = "A guardar as alterações…";
        live.hidden = false;
      }, 0);
    });
  }

  function enhanceRmaForm() {
    if (!/\/rmas\/rmas\/pagina\.php$/i.test(window.location.pathname)) return;
    var form = document.querySelector("form#rma_form");
    if (!form) return;
    document.body.classList.add("rma-operational-form");
    buildRmaSummary(form);
    enhanceRmaSections(form);
    enhanceQuickActions(form);
    enhanceUploads(form);
    restoreListReturn(form);
    enhanceSaveFeedback(form);
  }

  function findOption(select, pattern, exactValue) {
    if (!select) return null;
    for (var i = 0; i < select.options.length; i += 1) {
      if (exactValue !== undefined && select.options[i].value === exactValue) return select.options[i];
      if (pattern && pattern.test(select.options[i].textContent.replace(/\s+/g, " ").trim())) return select.options[i];
    }
    return null;
  }

  function updateChosen(field) {
    if (!window.jQuery) return;
    window.jQuery(field).trigger("chosen:updated").trigger("liszt:updated");
  }

  function submitQuickFilter(form, quickFields, setup) {
    for (var i = 0; i < quickFields.length; i += 1) {
      if (!quickFields[i]) continue;
      quickFields[i].value = "";
      updateChosen(quickFields[i]);
    }
    setup();
    var searching = form.querySelector('input[name="pesquisar"]');
    if (searching) searching.value = "1";
    if (typeof form.requestSubmit === "function") form.requestSubmit();
    else form.submit();
  }

  function localDate() {
    var now = new Date();
    return now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, "0") + "-" + String(now.getDate()).padStart(2, "0");
  }

  function quickFilterButton(label, active, handler) {
    var button = document.createElement("button");
    button.type = "button";
    button.className = "rma-quick-filter";
    button.textContent = label;
    button.setAttribute("aria-pressed", active ? "true" : "false");
    button.addEventListener("click", handler);
    return button;
  }

  function enhanceQuickFilters(form) {
    var heading = form.querySelector(".rma-list-heading");
    if (!heading || heading.querySelector(".rma-quick-filters")) return;
    var state = form.querySelector('select[name="f_estado"]');
    var listState = form.querySelector('select[name="f_estados2"]');
    var from = form.querySelector('input[name="f_data1"]');
    var to = form.querySelector('input[name="f_data2"]');
    if (!state && !listState && !from) return;

    var region = document.createElement("section");
    var title = document.createElement("span");
    var list = document.createElement("div");
    region.className = "rma-quick-filters";
    title.className = "rma-quick-filters-title";
    list.className = "rma-quick-filters-list";
    title.textContent = "Filtros rápidos";

    if (from && to) {
      list.appendChild(quickFilterButton("Hoje", from.value === localDate() && to.value === localDate(), function () {
        submitQuickFilter(form, [state, listState, from, to], function () { from.value = localDate(); to.value = localDate(); });
      }));
    }
    var openOption = findOption(state, null, "-1");
    if (openOption) {
      list.appendChild(quickFilterButton("Em aberto", state.value === openOption.value, function () {
        submitQuickFilter(form, [state, listState, from, to], function () { state.value = openOption.value; updateChosen(state); });
      }));
    }
    var customerOption = findOption(state, /(aguarda|espera).*cliente|aceita..o.*cliente/i);
    if (customerOption) {
      list.appendChild(quickFilterButton("A aguardar cliente", state.value === customerOption.value, function () {
        submitQuickFilter(form, [state, listState, from, to], function () { state.value = customerOption.value; updateChosen(state); });
      }));
    }
    var completedOption = findOption(state, /repara..o conclu.da|pront.*entrega|aguarda.*levantamento/i);
    if (completedOption) {
      list.appendChild(quickFilterButton("Prontos para entrega", state.value === completedOption.value, function () {
        submitQuickFilter(form, [state, listState, from, to], function () { state.value = completedOption.value; updateChosen(state); });
      }));
    }
    var lateOption = findOption(listState, /7 ou mais dias/i);
    if (lateOption) {
      list.appendChild(quickFilterButton("Atrasados 7+ dias", listState.value === lateOption.value, function () {
        submitQuickFilter(form, [state, listState, from, to], function () { listState.value = lateOption.value; updateChosen(listState); });
      }));
    }
    if (!list.children.length) return;
    region.appendChild(title);
    region.appendChild(list);
    var filters = heading.querySelector(".rma-list-filters");
    heading.insertBefore(region, filters || null);
  }

  function enhanceListRows(form) {
    var table = form.querySelector(".rma-list-data-table");
    if (!table || !table.rows.length) return;
    var header = table.rows[0];
    var trackingIndex = -1;
    for (var i = 0; i < header.cells.length; i += 1) {
      if (/tracking/i.test(header.cells[i].textContent)) trackingIndex = i;
    }
    var rows = table.querySelectorAll(".rma-list-record-row");
    for (var r = 0; r < rows.length; r += 1) {
      if (trackingIndex >= 0 && rows[r].cells[trackingIndex]) rows[r].cells[trackingIndex].classList.add("rma-tracking-cell");
      var actions = rows[r].querySelectorAll("a.rma-list-icon-action");
      for (var a = 0; a < actions.length; a += 1) {
        var image = actions[a].querySelector("img");
        var label = image ? (image.title || image.alt) : "";
        var href = actions[a].getAttribute("href") || "";
        if (!label && /history|historico/i.test(href)) label = "Consultar histórico";
        if (!label && /pagina\.php/i.test(href)) label = "Abrir registo";
        if (!label) label = "Executar ação";
        actions[a].title = label;
        actions[a].setAttribute("aria-label", label);
      }
    }
  }

  function rememberListLocation(form) {
    form.addEventListener("click", function (event) {
      var link = event.target.closest('a[href*="pagina.php"]');
      if (!link) return;
      try { window.sessionStorage.setItem(LIST_RETURN_KEY, window.location.href); } catch (ignore) {}
    });
  }

  function enhanceRmaList() {
    if (!/\/rmas\/rmas\/list\.php$/i.test(window.location.pathname)) return;
    var form = document.querySelector("form#lista_paginas");
    if (!form) return;
    document.body.classList.add("rma-operational-list");
    enhanceQuickFilters(form);
    enhanceListRows(form);
    rememberListLocation(form);
  }

  function enhanceHome() {
    if (!/\/rmas\/(?:index\.php)?$/i.test(window.location.pathname)) return;
    document.body.classList.add("rma-workflow-home");
    var items = document.querySelectorAll(".dashboard-counts .item[onclick], .recent-updates .item[onclick]");
    for (var i = 0; i < items.length; i += 1) {
      items[i].tabIndex = 0;
      items[i].setAttribute("role", "link");
      items[i].addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          event.currentTarget.click();
        }
      });
    }
  }

  function init() {
    enhanceRmaForm();
    enhanceRmaList();
    enhanceHome();
  }

  // The script is deferred, so enhance the parsed DOM before DOMContentLoaded
  // and before the browser has a chance to paint the legacy workflow.
  init();
}());
