<?php

/** Prefer the catalogue's model family; keep an unchanged title if no model is identifiable. */
function dotbright_repair_title_parts($name, $family_name = '')
{
    $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $family_name = trim(html_entity_decode(strip_tags($family_name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $start = null;
    if ($family_name !== '' && preg_match('~(?<![\pL\pN])'.preg_quote($family_name, '~').'(?![\pL\pN])~iu', $name, $match, PREG_OFFSET_CAPTURE)) $start = $match[0][1];
    // Fallback for catalogue names that abbreviate the family (e.g. MacBook Pro A1706).
    if (preg_match('~\b(?:(?:Apple|Samsung|Huawei|Xiaomi|Microsoft)\s+)?(?:iPhone|iPad|iPod|MacBook|iMac|Mac\s+(?:Mini|Pro|Studio)|Apple\s+Watch|Galaxy|Surface|Redmi|POCO|Pixel|PlayStation|Xbox)\b~iu', $name, $match, PREG_OFFSET_CAPTURE)) {
        if ($start === null || $match[0][1] < $start) $start = $match[0][1];
    }
    if ($start === null || $start === 0) return array($name, '', '');
    if (preg_match('~\b(?:Apple|Samsung|Huawei|Xiaomi|Microsoft|OnePlus|Oppo|Nokia|Sony|Lenovo|Asus|Acer|HP|Dell|Google|Motorola|Honor|Realme)\s+$~iu', substr($name, 0, $start), $match, PREG_OFFSET_CAPTURE)) $start = $match[0][1];
    $model = substr($name, $start);
    $note = '';
    if (preg_match('~\s+-\s+|\s+(?=\((?:peça|qualidade|compatível|original)\b)~iu', $model, $match, PREG_OFFSET_CAPTURE)) {
        $note = trim(substr($model, $match[0][1]));
        $model = substr($model, 0, $match[0][1]);
    }
    return array(rtrim(substr($name, 0, $start)), trim($model), $note);
}

/** Presentation for the repair information panel; the request form stays in produto.php. */
function dotbright_repair_info_panel($prods, $previous_panel)
{
    require_once __DIR__.'/../../../core/repair_request.php';
    $quote = dotbright_repair_quote($prods);
    $escape = function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    };
    $name = rtrim($prods['prod_nome'], '- ');
    $family = function_exists('get_family_info') && !empty($prods['fam_id']) ? get_family_info((int)$prods['fam_id']) : array();
    $title_parts = dotbright_repair_title_parts($name, $family['fam_nome'] ?? '');
    $reference = explode('_', $prods['prod_ref']);
    $reference = $reference[0];
    $piece = $quote['service']['part'];
    $piece_detail = $quote['service']['part_detail'];
    $piece_icon = $quote['service']['part_icon'];

    // Keep the current warranty rules and the existing price/gallery markup.
    $warranty = $quote['warranty'];
    preg_match('~<div class=[^>]*product-gallery-block resp2[^>]*>.*?</ul>\s*</div>~s', $previous_panel, $gallery);
    preg_match('~<p class="our_price_display".*?</p>~s', $previous_panel, $price);

    $page_url = $GLOBALS['site_http_url'].ltrim(str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id='.$GLOBALS['products_page_id'], $prods['prod_id'], 2)), '/');
    $whatsapp = 'https://wa.me/351914228888?text='.rawurlencode('Olá! Gostaria de confirmar a disponibilidade para '.$name.' ('.$piece.'). É possível realizar esta reparação em loja?'."\n\n".$page_url);
    $map = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode('DotBright Avenida Visconde Barreiros 167 4470-151 Maia');
    $icons = array(
        'clock' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2M9 2h6M12 2v3M19 5l2 2"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18m-12 6 2 2 4-4"/>',
        'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'shield' => '<path d="m12 2 9 4v6c0 5-9 10-9 10S3 17 3 12V6Z"/><path d="m8 12 3 3 5-6"/>',
        'team' => '<circle cx="8" cy="7" r="3"/><circle cx="17" cy="8" r="3"/><path d="M2 21v-4a6 6 0 0 1 12 0v4M15 13a5 5 0 0 1 7 5v3"/>',
        'tools' => '<path d="m14 7-9 9a3 3 0 0 0 4 4l9-9M14 7a5 5 0 0 1 6-5l-3 3 2 2 3-3a5 5 0 0 1-6 6M3 3l6 6m-6-6 1 4 3 1m6 7 7 7"/>',
        'box' => '<path d="m3 7 9-5 9 5v10l-9 5-9-5Zm0 0 9 5 9-5M12 12v10M7 4l10 6"/>',
        'arrow' => '<path d="M3 12h18m-6-6 6 6-6 6"/>',
        'chat' => '<path d="M21 11a9 9 0 0 1-13 8l-6 3 2-6A9 9 0 1 1 21 11Z"/><path d="M8 8c0 4 4 8 8 8l1-2-3-2-1 1-2-2 1-1-2-3Z"/>'
    );
    $icon = function ($type) use ($icons) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$icons[$type].'</svg>';
    };
    $asset_root = $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/';
    $asset_url = 'layout/'.PROJETO.'/';
    $html = '<link rel="stylesheet" href="'.$asset_url.'css/repair-info-panel.css?v='.filemtime($asset_root.'css/repair-info-panel.css').'">';
    $html .= '<script defer src="'.$asset_url.'js/repair-info-panel.js?v='.filemtime($asset_root.'js/repair-info-panel.js').'"></script>';
    $html .= '<section id="reparacao-info" class="col-xs-12 reparacao-painel db-repair-info" aria-labelledby="repair-panel-heading">';
    $html .= '<h2 id="repair-panel-heading" class="reparacao-etapa-titulo db-repair-step" tabindex="-1">Etapa 1 de 4 <span>Informação da reparação</span></h2>';
    $html .= '<div class="db-repair-grid"><div class="db-repair-product">'.(isset($gallery[0]) ? $gallery[0] : '');
    $html .= '<h1 itemprop="name" class="product-title"><span class="db-repair-title-service">'.$escape($title_parts[0]).'</span>';
    if ($title_parts[1] !== '') $html .= ' <span class="db-repair-title-model">'.$escape($title_parts[1]).'</span>';
    if ($title_parts[2] !== '') $html .= ' <span class="db-repair-title-note">'.$escape($title_parts[2]).'</span>';
    $html .= '</h1>';
    $html .= '<p class="db-repair-reference">REF: '.$escape($reference).'</p>';
    $html .= '<div class="db-repair-price">'.(isset($price[0]) ? $price[0] : '').'</div>';
    $html .= '<span class="db-repair-piece-badge">'.$icon($piece_icon).$escape($piece).'</span></div>';
    $html .= '<div class="db-repair-facts">';
    $html .= '<div class="db-repair-fact db-repair-time"><span class="db-repair-icon">'.$icon('clock').'</span><div><span class="db-repair-caption">Tempo estimado de reparação</span><strong>'.$escape($quote['service']['time']).'</strong><details class="db-repair-time-note"><summary>Informação sobre o prazo</summary><p>O prazo é estimado e pode variar consoante a disponibilidade da peça, o diagnóstico e o volume de trabalho. Refere-se à reparação em loja; o prazo RRE é comunicado separadamente.</p></details></div></div>';
    $html .= '<div class="db-repair-fact"><span class="db-repair-icon">'.$icon('calendar').'</span><div><strong>Sem necessidade de marcação</strong><p>Sujeito à disponibilidade da peça.</p><a class="db-repair-whatsapp" href="'.$escape($whatsapp).'" target="_blank" rel="noopener noreferrer">'.$icon('chat').'Confirmar disponibilidade</a></div></div>';
    $html .= '<div class="db-repair-fact"><span class="db-repair-icon">'.$icon('pin').'</span><div><span class="db-repair-caption">Local de reparação</span><strong>Loja da Maia</strong><p>Avenida Visconde Barreiros 167<br>4470-151 Maia</p><a class="db-repair-map" href="'.$escape($map).'" target="_blank" rel="noopener noreferrer">'.$icon('pin').'Ver no mapa</a></div></div>';
    $html .= '<div class="db-repair-fact"><span class="db-repair-icon">'.$icon($piece_icon).'</span><div><strong>'.$escape($piece).'</strong><p>'.$escape($piece_detail).'</p></div></div>';
    $html .= '<div class="db-repair-fact"><span class="db-repair-icon">'.$icon('team').'</span><div><strong>Técnicos qualificados</strong><p>Equipa especializada e experiente.</p></div></div>';
    $html .= '<div class="db-repair-fact"><span class="db-repair-icon">'.$icon('shield').'</span><div><strong>Garantia: '.$escape($warranty).'</strong><p>Consulte as condições aplicáveis à reparação.</p></div></div></div></div>';

    $description = !empty($prods['prod_descricao2']) ? $prods['prod_descricao2'] : (isset($prods['prod_descricao']) ? $prods['prod_descricao'] : '');
    $details = isset($prods['prod_como_funciona']) ? $prods['prod_como_funciona'] : '';
    $html .= '<div class="db-repair-tabs"><div class="db-repair-tablist" role="tablist" aria-label="Informações da reparação"><button type="button" id="repair-description-tab" role="tab" aria-selected="true" aria-controls="repair-description">Descrição</button><button type="button" id="repair-details-tab" role="tab" aria-selected="false" aria-controls="repair-details" tabindex="-1">Detalhes</button></div>';
    foreach (array('description' => $description, 'details' => $details) as $key => $content) {
        $html .= '<section id="repair-'.$key.'" class="db-repair-tabpanel" role="tabpanel" aria-labelledby="repair-'.$key.'-tab"'.($key === 'details' ? ' hidden' : '').'><div class="db-repair-copy" id="repair-'.$key.'-copy">';
        $html .= trim(strip_tags($content)) !== '' ? $content : '<p>'.($key === 'description' ? $escape($name) : 'Para mais informações sobre esta intervenção, confirme os detalhes com a nossa equipa.').'</p>';
        $html .= '</div><button type="button" class="db-repair-read-more" aria-expanded="false" aria-controls="repair-'.$key.'-copy" hidden>Ler mais</button></section>';
    }
    $html .= '</div><div class="db-repair-actions"><button type="button" class="db-repair-continue" onclick="reparacaoEtapa(2)">Continuar para dados do cliente '.$icon('arrow').'</button></div></section>';
    return $html;
}
