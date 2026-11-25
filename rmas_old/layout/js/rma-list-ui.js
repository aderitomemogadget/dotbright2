(function () {
  "use strict";

  var modules = {
    rmas: { title: "Gestão de RMAs", description: "Consulte, filtre e acompanhe as assistências técnicas." },
    clientes: { title: "Clientes", description: "Consulte e gira os registos de clientes." },
    sa_clientes: { title: "Clientes", description: "Consulte e gira os clientes associados às assistências." },
    rma_lojas: { title: "Lojas", description: "Consulte e organize as lojas disponíveis." },
    localizacoes: { title: "Localizações", description: "Consulte e organize as localizações dos equipamentos." },
    sa_estados_rmas: { title: "Estados de RMA", description: "Consulte e configure os estados das assistências." },
    sa_descricao_tecnico: { title: "Descrições técnicas", description: "Consulte os textos reutilizáveis nos relatórios técnicos." },
    emprestimos: { title: "Telemóveis de empréstimo", description: "Consulte e gira os equipamentos emprestados." },
    gerir_marcas: { title: "Marcas", description: "Consulte e organize as marcas de equipamentos." },
    gerir_modelos: { title: "Modelos", description: "Consulte e organize os modelos de equipamentos." },
    tipos_avarias: { title: "Tipos de avaria", description: "Consulte e organize as classificações de avaria." },
    equip_substituicao: { title: "Equipamentos de substituição", description: "Consulte e gira os equipamentos de substituição." },
    users: { title: "Utilizadores", description: "Consulte e gira os utilizadores do backoffice." },
    grupos: { title: "Grupos", description: "Consulte e gira os grupos e permissões." },
    grupos_users: { title: "Grupos de utilizadores", description: "Consulte e gira os grupos de utilizadores." },
    grupos_clientes: { title: "Grupos de clientes", description: "Consulte e gira os grupos de clientes." },
    pages: { title: "Páginas", description: "Consulte e gira as páginas e conteúdos." },
    products: { title: "Produtos", description: "Consulte e gira o catálogo de produtos." },
    emails: { title: "Emails", description: "Consulte e gira os modelos de email." },
    messages: { title: "Mensagens", description: "Consulte as mensagens internas." },
    sa_linguas: { title: "Idiomas", description: "Consulte e gira os idiomas disponíveis." },
    sa_marcas_modelos: { title: "Marcas e modelos", description: "Consulte e organize marcas e modelos." },
    sa_rmas: { title: "Assistências", description: "Consulte e gira os registos de assistência." },
    sa_pagamentos: { title: "Pagamentos", description: "Consulte os registos de pagamento." }
  };

  function moduleKey() {
    var parts = window.location.pathname.split("/");
    var index = parts.indexOf("rmas");
    return index >= 0 && parts[index + 1] ? parts[index + 1] : "registos";
  }

  function detailsForPage() {
    var key = moduleKey();
    if (key === "sa_rmas" && window.location.pathname.indexOf("/sa_rmas/locais/") !== -1) {
      return { title: "Locais de assistência", description: "Consulte e gira os locais associados às assistências." };
    }
    if (key === "sa_rmas" && window.location.pathname.indexOf("/sa_rmas/estados/") !== -1) {
      return { title: "Estados de assistência", description: "Consulte e gira os estados das assistências." };
    }
    if (key === "sa_rmas" && window.location.pathname.indexOf("/sa_rmas/clientes/") !== -1) {
      return { title: "Clientes de assistência", description: "Consulte os clientes associados às assistências." };
    }
    return modules[key] || { title: "Registos", description: "Consulte e gira os registos disponíveis." };
  }

  function directBlock(node, mainCell) {
    var block = node;
    while (block && block.parentElement !== mainCell) block = block.parentElement;
    return block && block.parentElement === mainCell ? block : null;
  }

  function uniquePush(items, item) {
    if (item && items.indexOf(item) === -1) items.push(item);
  }

  function listIcon() {
    var icon = document.createElement("span");
    icon.className = "rma-list-heading-icon";
    icon.setAttribute("aria-hidden", "true");
    icon.innerHTML = '<svg viewBox="0 0 24 24" focusable="false"><path d="M8.5 6.5h10M8.5 12h10M8.5 17.5h10"/><path d="M4.5 6.5h.01M4.5 12h.01M4.5 17.5h.01"/></svg>';
    return icon;
  }

  function actionCandidates(form, mainCell, sideCell) {
    var candidates = [];
    var selector = 'input[type="button"], button[type="button"], a.botao';
    var nodes = [];
    if (sideCell) nodes = nodes.concat(Array.prototype.slice.call(sideCell.querySelectorAll(selector)));
    nodes = nodes.concat(Array.prototype.slice.call(mainCell.querySelectorAll(selector)));
    for (var i = 0; i < nodes.length; i += 1) {
      var label = (nodes[i].value || nodes[i].textContent || "").trim();
      if (!/^(criar|inserir|novo|nova|emprestados)/i.test(label)) continue;
      uniquePush(candidates, nodes[i]);
    }
    return candidates.slice(0, 3);
  }

  function buildHeading(form, mainCell, sideCell, visibleCount) {
    var details = detailsForPage();
    var heading = document.createElement("section");
    var top = document.createElement("div");
    var copy = document.createElement("div");
    var title = document.createElement("h1");
    var description = document.createElement("p");
    var meta = document.createElement("span");
    var actions = document.createElement("div");

    heading.className = "rma-list-heading";
    top.className = "rma-list-heading-top";
    copy.className = "rma-list-heading-copy";
    actions.className = "rma-list-heading-actions";
    title.textContent = details.title;
    description.textContent = details.description;
    meta.className = "rma-list-count";
    meta.textContent = visibleCount + (visibleCount === 1 ? " registo visível" : " registos visíveis");

    copy.appendChild(title);
    copy.appendChild(description);
    copy.appendChild(meta);
    top.appendChild(listIcon());
    top.appendChild(copy);

    var candidates = actionCandidates(form, mainCell, sideCell);
    var sourceBlocks = [];
    for (var i = 0; i < candidates.length; i += 1) {
      uniquePush(sourceBlocks, directBlock(candidates[i], mainCell));
      candidates[i].classList.add(i === 0 ? "rma-list-create" : "rma-list-secondary-action");
      actions.appendChild(candidates[i]);
    }
    for (var b = 0; b < sourceBlocks.length; b += 1) {
      if (!sourceBlocks[b].querySelector("input, button, a[href]")) sourceBlocks[b].classList.add("rma-list-obsolete-actions");
    }
    if (actions.children.length) top.appendChild(actions);
    heading.appendChild(top);
    return heading;
  }

  function filterBlocks(mainCell, records) {
    var selector = [
      'input[name="m_pesquisa"]', 'input[name="top_search"]', 'input[name="title"]',
      'input[name="Nome"]', 'input[name="DESIGNACAO"]', 'input[name^="f_"]',
      'input[name^="filt"]', 'select[name^="f_"]', 'select[name^="filt"]'
    ].join(",");
    var controls = mainCell.querySelectorAll(selector);
    var blocks = [];
    for (var i = 0; i < controls.length; i += 1) {
      if (controls[i].type === "hidden" && controls[i].name !== "top_search") continue;
      var block = directBlock(controls[i], mainCell);
      if (!block || block.id === "navigation_horiz" || records.indexOf(block) !== -1) continue;
      uniquePush(blocks, block);
    }
    return blocks;
  }

  function hasActiveFilters(form) {
    var fields = form.querySelectorAll('input[type="text"], input[type="search"], input[type="date"], select');
    for (var i = 0; i < fields.length; i += 1) {
      if (fields[i].closest(".rma-list-bulk")) continue;
      if (String(fields[i].value || "").trim() !== "") return true;
    }
    var params = new URLSearchParams(window.location.search);
    var ignored = { d: 1, Dump: 1, pagina: 1, tipo: 1, tab: 1 };
    var active = false;
    params.forEach(function (value, key) {
      if (!ignored[key] && value !== "") active = true;
    });
    return active;
  }

  function clearUrl() {
    var params = new URLSearchParams();
    var current = new URLSearchParams(window.location.search);
    if (current.has("tipo")) params.set("tipo", current.get("tipo"));
    var query = params.toString();
    return window.location.pathname + (query ? "?" + query : "");
  }

  function modernizeFilters(form, mainCell, heading, recordBlocks) {
    var blocks = filterBlocks(mainCell, recordBlocks);
    if (!blocks.length) return;
    var region = document.createElement("div");
    var actions = document.createElement("div");
    region.className = "rma-list-filters";
    actions.className = "rma-list-filter-actions";
    for (var i = 0; i < blocks.length; i += 1) region.appendChild(blocks[i]);

    var submitters = region.querySelectorAll('input[type="submit"], button[type="submit"], a[title*="Pesquisar" i]');
    for (var s = 0; s < submitters.length; s += 1) submitters[s].classList.add("rma-list-search-action");

    if (hasActiveFilters(form)) {
      var clear = document.createElement("a");
      clear.className = "rma-list-clear";
      clear.href = clearUrl();
      clear.textContent = "Limpar pesquisa";
      actions.appendChild(clear);
    }
    if (actions.children.length) region.appendChild(actions);
    heading.appendChild(region);
  }

  function recordBlocks(mainCell, checkboxes) {
    var blocks = [];
    for (var i = 0; i < checkboxes.length; i += 1) uniquePush(blocks, directBlock(checkboxes[i], mainCell));
    return blocks;
  }

  function meaningfulAccent(color) {
    var normalized = String(color || "").toLowerCase().replace(/\s/g, "");
    var decorative = ["", "#fff", "#ffffff", "#f6f6f6", "#def2fa", "#c8ecfa", "#dbe7f1", "#e8e7e7"];
    return decorative.indexOf(normalized) === -1 ? normalized : "";
  }

  function modernizeRecordRows(container) {
    var rows = container.querySelectorAll("tr");
    for (var i = 0; i < rows.length; i += 1) {
      var checkbox = rows[i].querySelector('input[type="checkbox"][name^="chk_"]:not([name="chk_all"])');
      if (!checkbox) continue;
      rows[i].classList.add("rma-list-record-row");
      var accent = meaningfulAccent(rows[i].getAttribute("bgcolor"));
      if (accent) rows[i].style.setProperty("--rma-list-row-accent", accent);
      rows[i].removeAttribute("bgcolor");
      rows[i].removeAttribute("onmouseover");
      rows[i].removeAttribute("onmouseout");

      var links = rows[i].querySelectorAll("a[href]");
      var editLinkMarked = false;
      for (var l = 0; l < links.length; l += 1) {
        if (links[l].querySelector("img")) links[l].classList.add("rma-list-icon-action");
        else {
          links[l].classList.add("rma-list-row-link");
          if (!editLinkMarked && /pagina\.php/i.test(links[l].getAttribute("href") || "")) {
            links[l].classList.add("rma-list-edit-link");
            editLinkMarked = true;
          }
        }
      }
      var deleteLinks = rows[i].querySelectorAll('a[href*="task=apagar"], a[href*="ACCOES=1"]');
      for (var d = 0; d < deleteLinks.length; d += 1) deleteLinks[d].classList.add("rma-list-delete-action");
      var cells = rows[i].children;
      for (var c = 0; c < cells.length; c += 1) {
        if (cells[c].querySelector("a img, img[title]")) cells[c].classList.add("rma-list-actions-cell");
      }
    }
  }

  function modernizeRecords(mainCell, blocks, checkboxes) {
    if (!blocks.length) return null;
    var container = document.createElement("section");
    container.className = "rma-list-records" + (blocks.length === 1 ? " rma-list-table-mode" : " rma-list-card-mode");
    if (!checkboxes.length) container.classList.add("rma-list-empty-state");
    mainCell.insertBefore(container, blocks[0]);
    for (var i = 0; i < blocks.length; i += 1) container.appendChild(blocks[i]);
    var previous = container.previousElementSibling;
    while (previous && /^(SCRIPT|STYLE|BR)$/i.test(previous.tagName)) previous = previous.previousElementSibling;
    if (previous && previous.tagName === "TABLE" && !previous.querySelector("input, select, button, a[href]") && previous.textContent.trim().length < 80) {
      previous.classList.add("rma-list-obsolete-title");
    }
    modernizeRecordRows(container);

    var tables = container.querySelectorAll("table");
    for (var t = 0; t < tables.length; t += 1) {
      if (tables[t].querySelector('input[type="checkbox"][name^="chk_"]')) tables[t].classList.add("rma-list-data-table");
    }
    return container;
  }

  function modernizePagination(mainCell) {
    var tables = mainCell.querySelectorAll("table");
    for (var i = 0; i < tables.length; i += 1) {
      if (tables[i].querySelector('input[type="checkbox"][name^="chk_"]')) continue;
      var html = tables[i].innerHTML;
      if (html.indexOf("pagina=") === -1 && !/primeira|última/i.test(tables[i].textContent)) continue;
      tables[i].classList.add("rma-list-pagination");
    }
  }

  function bulkBlock(mainCell) {
    var select = mainCell.querySelector("select#flag, select#ACCOES");
    if (!select) return null;
    var block = directBlock(select, mainCell) || select.closest("table");
    if (!block) return null;
    block.classList.add("rma-list-bulk");
    return { block: block, select: select };
  }

  function addDeleteConfirmations(form) {
    var links = form.querySelectorAll(".rma-list-delete-action");
    for (var i = 0; i < links.length; i += 1) {
      if (links[i].getAttribute("onclick")) continue;
      links[i].addEventListener("click", function (event) {
        if (!window.confirm("Tem a certeza de que pretende eliminar este registo?")) event.preventDefault();
      });
    }
  }

  function selectionController(form, checkboxes, bulk) {
    var masters = form.querySelectorAll('input[type="checkbox"]#filter, input[type="checkbox"]#chk_all');
    var status = document.createElement("span");
    status.className = "rma-list-selection-count";
    if (bulk) {
      var statusHost = bulk.block.querySelector("td") || bulk.block;
      statusHost.insertBefore(status, statusHost.firstChild);
    }

    function selectedCount() {
      var total = 0;
      for (var i = 0; i < checkboxes.length; i += 1) if (checkboxes[i].checked) total += 1;
      return total;
    }

    function update() {
      var count = selectedCount();
      status.textContent = count + (count === 1 ? " registo selecionado" : " registos selecionados");
      if (bulk) bulk.block.classList.toggle("rma-list-bulk-visible", count > 0);
      for (var m = 0; m < masters.length; m += 1) {
        masters[m].indeterminate = count > 0 && count < checkboxes.length;
        masters[m].checked = checkboxes.length > 0 && count === checkboxes.length;
      }
    }

    for (var i = 0; i < checkboxes.length; i += 1) checkboxes[i].addEventListener("change", update);
    for (var j = 0; j < masters.length; j += 1) {
      masters[j].addEventListener("click", function (event) {
        var checked = event.currentTarget.checked;
        window.setTimeout(function () {
          for (var c = 0; c < checkboxes.length; c += 1) checkboxes[c].checked = checked;
          update();
        }, 0);
      });
    }

    form.addEventListener("submit", function (event) {
      if (!bulk || selectedCount() === 0) return;
      var option = bulk.select.options[bulk.select.selectedIndex];
      var action = option ? option.textContent : "";
      if (/apagar|remover|eliminar/i.test(action) && !window.confirm("Tem a certeza de que pretende aplicar esta operação aos registos selecionados?")) {
        event.preventDefault();
      }
    });
    update();
  }

  function enhanceList(form) {
    if (!/(?:list|list_emp)\.php$/i.test(window.location.pathname)) return;
    var nav = form.querySelector("#navigation_horiz");
    if (!nav) return;
    var mainCell = nav.closest("td");
    var shell = mainCell ? mainCell.closest("table.t3") : null;
    if (!mainCell || !shell) return;

    var checkboxes = Array.prototype.slice.call(form.querySelectorAll('input[type="checkbox"][name^="chk_"]:not([name="chk_all"])'));
    var blocks = recordBlocks(mainCell, checkboxes);
    if (!blocks.length) {
      var emptyTables = mainCell.querySelectorAll("table.r1, table.r2");
      for (var e = 0; e < emptyTables.length; e += 1) {
        if (/não\s+(existem|foram encontrados)|nenhum registo/i.test(emptyTables[e].textContent)) {
          uniquePush(blocks, directBlock(emptyTables[e], mainCell));
        }
      }
    }
    var cells = shell.rows.length ? shell.rows[0].cells : [];
    var sideCell = cells.length > 2 ? cells[cells.length - 1] : null;
    var heading = buildHeading(form, mainCell, sideCell, checkboxes.length);

    document.body.classList.add("rma-list-page");
    form.classList.add("rma-modern-list");
    shell.classList.add("rma-list-shell");
    mainCell.classList.add("rma-list-main");
    if (sideCell && sideCell !== mainCell) sideCell.classList.add("rma-list-side-tools");
    nav.parentNode.insertBefore(heading, nav.nextSibling);

    modernizeFilters(form, mainCell, heading, blocks);
    modernizeRecords(mainCell, blocks, checkboxes);
    modernizePagination(mainCell);
    var bulk = bulkBlock(mainCell);
    selectionController(form, checkboxes, bulk);
    addDeleteConfirmations(form);
  }

  function init() {
    var form = document.querySelector('form#lista_paginas, form[name="lista_paginas"]');
    if (form) enhanceList(form);
  }

  // Deferred execution already guarantees a parsed DOM. Running now avoids a
  // legacy-list frame before the modern structure is applied.
  init();
}());
