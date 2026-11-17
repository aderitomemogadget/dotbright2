<?php
require_once __DIR__ . '/../../../core/core_files.php';
require_once __DIR__ . '/sequra_config.php';
require_once __DIR__ . '/sequra_lib.php';
$enc_id = isset($_GET['id_enc']) ? (int) $_GET['id_enc'] : 0;
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($enc_id <= 0 || !sequra_valid_order_token($enc_id, $token)) { http_response_code(403); exit('Encomenda inválida.'); }
$config = sequra_config();
sequra_update_local_order($enc_id, false, $config['cancelled_state'], 'cancelled_by_customer');
sequra_log($enc_id, sequra_order_reference($enc_id), array('cancel'=>true), '', 'CANCEL', '');
header('Location: ' . SITE_HTTP_URL . 'p5-registo-utilizador-pt?id_enc=' . $enc_id . '&show_compras=1');
exit;
