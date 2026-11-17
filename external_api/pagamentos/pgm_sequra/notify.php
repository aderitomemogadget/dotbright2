<?php
require_once __DIR__ . '/../../../core/core_files.php';
require_once __DIR__ . '/sequra_config.php';
require_once __DIR__ . '/sequra_lib.php';
require_once __DIR__ . '/sequra_ipn.php';
header('Content-Type: application/json; charset=utf-8');
if (!sequra_is_ready()) { http_response_code(503); echo json_encode(array('ok'=>false)); exit; }
$raw = file_get_contents('php://input');
$data = sequra_ipn_data($raw);
$enc_id = sequra_ipn_enc_id($data);
$token = isset($data['token']) ? (string) $data['token'] : (isset($data['m_token']) ? (string) $data['m_token'] : '');
if ($enc_id <= 0 || !sequra_valid_order_token($enc_id, $token) || !sequra_verify_signature($raw)) {
    sequra_log($enc_id, '', $data, '', 'IPN_FORBIDDEN', 'Token ou assinatura inválidos.');
    http_response_code(403); echo json_encode(array('ok'=>false)); exit;
}
$provider_reference = isset($data['order_ref']) ? trim($data['order_ref']) : '';
$status = sequra_ipn_state($data);
$config = sequra_config();
if (in_array($status, array('approved','accepted','authorized','confirmed','paid','success','ok'), true)) {
    $target_state = 'confirmed'; $paid = true; $local_state = $config['paid_state'];
} elseif (in_array($status, array('needs_review','review','on_hold'), true)) {
    $target_state = 'on_hold'; $paid = false; $local_state = $config['pending_state'];
} elseif (in_array($status, array('rejected','denied','cancelled','canceled','failed','error'), true)) {
    $update = sequra_update_local_order($enc_id, false, $status === 'cancelled' || $status === 'canceled' ? $config['cancelled_state'] : $config['failed_state'], $status);
    sequra_log($enc_id, sequra_order_reference($enc_id), $data, '', 'IPN_' . strtoupper($status), $update['ok'] ? '' : 'Falha ao atualizar encomenda.');
    echo json_encode(array('ok'=>(bool) $update['ok'])); exit;
} else {
    sequra_log($enc_id, sequra_order_reference($enc_id), $data, '', 'IPN_UNKNOWN', $status);
    http_response_code(501); echo json_encode(array('ok'=>false, 'error'=>'unknown_state')); exit;
}
$provider = sequra_confirm_provider_order($enc_id, $provider_reference, $target_state);
if ($provider['http_code'] < 200 || $provider['http_code'] > 299) {
    sequra_log($enc_id, sequra_order_reference($enc_id), $provider['request'], $provider['response_raw'], 'IPN_PROVIDER_ERROR', 'HTTP ' . $provider['http_code'] . ' ' . $provider['curl_error']);
    http_response_code($provider['http_code'] === 409 ? 409 : 500); echo json_encode(array('ok'=>false)); exit;
}
$local = sequra_update_local_order($enc_id, $paid, $local_state, $status);
if (!$local['ok']) { http_response_code(500); echo json_encode(array('ok'=>false)); exit; }
if ($local['became_paid'] && function_exists('enviarEmailCliente')) enviarEmailCliente($enc_id);
sequra_log($enc_id, sequra_order_reference($enc_id), $data, '', 'IPN_' . strtoupper($target_state), '');
echo json_encode(array('ok'=>true));
