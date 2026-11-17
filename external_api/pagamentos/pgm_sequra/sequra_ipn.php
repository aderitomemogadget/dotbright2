<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] === __FILE__) exit;

function sequra_ipn_data($raw)
{
    $data = json_decode($raw, true);
    if (!is_array($data)) { parse_str($raw, $data); if (!is_array($data)) $data = array(); }
    foreach ($_POST as $key => $value) $data[$key] = $value;
    return $data;
}

function sequra_ipn_enc_id($data)
{
    if (isset($data['cart']) && (int) $data['cart'] > 0) return (int) $data['cart'];
    if (isset($data['m_cart']) && (int) $data['m_cart'] > 0) return (int) $data['m_cart'];
    $prefix = preg_quote(sequra_config()['order_prefix'], '/');
    foreach (array('order_ref_1', 'reference', 'cart_ref') as $key) {
        if (isset($data[$key]) && preg_match('/^' . $prefix . '-(\d+)$/i', $data[$key], $match)) return (int) $match[1];
    }
    return 0;
}

function sequra_ipn_state($data)
{
    foreach (array('sq_state', 'status', 'state', 'order_status', 'decision', 'result') as $key) {
        if (isset($data[$key]) && trim($data[$key]) !== '') return strtolower(trim($data[$key]));
    }
    return '';
}

function sequra_ipn_order_url($provider_reference)
{
    $provider_reference = trim((string) $provider_reference);
    if (sequra_safe_order_uri($provider_reference)) return $provider_reference;
    if (!preg_match('/^[a-f0-9-]{20,64}$/i', $provider_reference)) return '';
    return sequra_endpoint_base() . '/orders/' . rawurlencode($provider_reference);
}

function sequra_confirm_provider_order($enc_id, $provider_reference, $target_state)
{
    $payload = sequra_get_order_payload($enc_id, $target_state);
    if (!$payload) return array('http_code'=>404, 'curl_error'=>'Encomenda não encontrada.', 'request'=>'', 'response_raw'=>'');
    unset($payload['order']);
    $payload['merchant_reference'] = array('order_ref_1'=>sequra_order_reference($enc_id));
    $url = sequra_ipn_order_url($provider_reference);
    if ($url === '') return array('http_code'=>400, 'curl_error'=>'Referência seQura inválida.', 'request'=>'', 'response_raw'=>'');
    return sequra_api_request('PUT', $url, array('order'=>$payload));
}
