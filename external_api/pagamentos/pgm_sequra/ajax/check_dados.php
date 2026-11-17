<?php
ob_start();
require_once __DIR__ . '/../../../../core/core_files.php';
require_once __DIR__ . '/../sequra_config.php';
require_once __DIR__ . '/../sequra_lib.php';

function sequra_ajax_response($payload, $status = 200)
{
    while (ob_get_level() > 0) ob_end_clean();
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

$enc_id = isset($_GET['id_enc']) ? (int) $_GET['id_enc'] : 0;
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if (!sequra_is_ready()) sequra_ajax_response(array('ok'=>false, 'error'=>'unavailable'), 503);
if ($enc_id <= 0 || !sequra_valid_order_token($enc_id, $token)) sequra_ajax_response(array('ok'=>false, 'error'=>'invalid_order'), 403);
if (!sequra_sandbox_order_access_allowed($enc_id)) sequra_ajax_response(array('ok'=>false, 'error'=>'forbidden'), 403);
$payload = sequra_get_order_payload($enc_id);
if (!$payload) sequra_ajax_response(array('ok'=>false, 'error'=>'order_not_found'), 404);
$result = sequra_create_order($payload);
$checkout_url = isset($result['response']['checkout_url']) ? $result['response']['checkout_url'] : '';
sequra_log($enc_id, sequra_order_reference($enc_id), $result['request'], $result['response_raw'], $checkout_url !== '' ? 'CHECKOUT_CREATED' : 'CHECKOUT_ERROR', $result['curl_error']);
if ($checkout_url === '') sequra_ajax_response(array('ok'=>false, 'error'=>'provider_error'), 502);
sequra_ajax_response(array('ok'=>true, 'checkout_url'=>$checkout_url));
