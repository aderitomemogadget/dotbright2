<?php
require_once __DIR__.'/repair_product_settings.php';

/** Shared values for repair product pages, request summaries and emails. */
function dotbright_repair_quote($product, $now = null)
{
    $prices = isset($product['prod_precos']) ? $product['prod_precos'] : array();
    // PVP is the catalogue's final customer price, already adjusted for valid promotions/margins.
    $gross = (int)round(max(0, isset($prices['PVP']) ? (float)$prices['PVP'] : 0) * 100);
    $rate = max(0, isset($prices['IVA']) ? (float)$prices['IVA'] : 0);
    $net = (int)round($gross / (1 + $rate / 100));
    $shipping_gross = 490; // Existing shipping charge. New RRE conditions are a separate phase.
    $shipping_rate = max(0, isset($GLOBALS['iva_default']) ? (float)$GLOBALS['iva_default'] : 23);
    $shipping_net = (int)round($shipping_gross / (1 + $shipping_rate / 100));
    $now = $now === null ? time() : $now;
    $start = !empty($prices['Promo_data_ini']) ? strtotime($prices['Promo_data_ini']) : false;
    $end = !empty($prices['Promo_data_fim']) ? strtotime($prices['Promo_data_fim']) : false;
    $old = isset($prices['PRECO_ANTIGO']) ? (float)$prices['PRECO_ANTIGO'] : 0;
    $promotion = isset($prices['PROMOCAO']) && (int)$prices['PROMOCAO'] === 1
        && $start !== false && $end !== false && $start <= $now && $now <= $end
        && $old > $gross / 100;
    $reference = explode('_', isset($product['prod_ref']) ? $product['prod_ref'] : '');
    $months = substr($reference[0], 0, 3) === 'REP' && strpos($reference[0], 'BAT') !== false
        ? 12 : (isset($product['fam_id']) && (string)$product['fam_id'] === '163' ? 24 : 36);

    $service = dotbright_repair_service_info($product);
    if ($service['warranty_months'] !== null) $months = $service['warranty_months'];

    return array(
        'service' => $service,
        'repair_gross' => $gross / 100,
        'repair_net' => $net / 100,
        'shipping_gross' => $shipping_gross / 100,
        'shipping_net' => $shipping_net / 100,
        'vat' => ($gross - $net + $shipping_gross - $shipping_net) / 100,
        'total' => ($gross + $shipping_gross) / 100,
        'old_price' => $old,
        'promotion' => $promotion,
        'warranty_months' => $months,
        'warranty' => $months.' meses'
    );
}

function dotbright_repair_request_data($input)
{
    $data = array();
    foreach (array('r_nome', 'r_recolha', 'r_entrega', 'r_cp', 'r_cidade', 'r_tel', 'r_email', 'r_nif', 'r_nome2', 'r_morada2', 'r_cp2', 'r_nif2') as $key) {
        $data[$key] = isset($input[$key]) && is_scalar($input[$key]) ? trim((string)$input[$key]) : '';
    }
    foreach (array('r_entrega' => 'r_recolha', 'r_nome2' => 'r_nome', 'r_morada2' => 'r_recolha', 'r_cp2' => 'r_cp', 'r_nif2' => 'r_nif') as $key => $fallback) {
        if ($data[$key] === '') $data[$key] = $data[$fallback];
    }
    $period = isset($input['r_periodo']) && is_scalar($input['r_periodo']) ? (string)$input['r_periodo'] : '';
    $data['period'] = $period === '1' ? 'Manhã' : ($period === '2' ? 'Tarde' : 'Não especificado');
    return $data;
}

function dotbright_repair_email_summary($product, $data, $quote)
{
    $escape = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); };
    $section = function ($title, $rows) use ($escape) {
        $html = '<div style="border:1px solid #ddd;border-radius:10px;margin:15px 0;padding:15px"><h3>'.$escape($title).'</h3>';
        foreach ($rows as $label => $value) $html .= '<p><strong>'.$escape($label).':</strong> '.$escape($value === '' ? '—' : $value).'</p>';
        return $html.'</div>';
    };
    $html = $section('Dados do cliente e recolha', array(
        'Nome completo' => $data['r_nome'], 'Telemóvel' => $data['r_tel'], 'Email' => $data['r_email'], 'NIF' => $data['r_nif'],
        'Morada para recolha' => $data['r_recolha'], 'Código Postal' => $data['r_cp'], 'Cidade' => $data['r_cidade'],
        'Morada para entrega' => $data['r_entrega'], 'Período de recolha' => $data['period']
    ));
    $html .= $section('Dados de faturação', array('Nome completo' => $data['r_nome2'], 'Morada' => $data['r_morada2'], 'Código Postal' => $data['r_cp2'], 'NIF' => $data['r_nif2']));
    $reference = explode('_', $product['prod_ref']);
    $rows = array('Reparação' => rtrim($product['prod_nome'], '- '), 'Referência' => $reference[0], 'Garantia' => $quote['warranty']);
    $rows['Prazo estimado'] = $quote['service']['time'];
    $rows['Peça'] = $quote['service']['part'];
    if (!empty($product['Ref_Ean'])) $rows['EAN'] = $product['Ref_Ean'];
    $html .= $section('Resumo da avaria', $rows);
    $html .= $section('Valor estimado', array('Avaria' => numberformat($quote['repair_net']), 'Portes' => numberformat($quote['shipping_net']), 'IVA' => numberformat($quote['vat']), 'Total' => numberformat($quote['total'])));
    return $html.'<p>Valor estimado, sujeito a confirmação pela equipa. O pagamento será combinado posteriormente.</p>';
}
