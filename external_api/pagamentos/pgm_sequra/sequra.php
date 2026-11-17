<?php
require_once __DIR__ . '/../../../core/core_files.php';
require_once __DIR__ . '/sequra_config.php';
require_once __DIR__ . '/sequra_lib.php';
if (session_status() === PHP_SESSION_NONE) @session_start();
$enc_id = isset($_GET['id_enc']) ? (int) $_GET['id_enc'] : 0;
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if (!sequra_is_ready()) { http_response_code(503); exit('Pagamento temporariamente indisponível.'); }
if ($enc_id <= 0 || !sequra_valid_order_token($enc_id, $token)) { http_response_code(403); exit('Encomenda inválida.'); }
if (!sequra_sandbox_order_access_allowed($enc_id)) { http_response_code(403); exit('Pagamento de teste não autorizado.'); }
$order_uri = isset($_SESSION['SEQURA_ORDER_URI_' . $enc_id]) ? $_SESSION['SEQURA_ORDER_URI_' . $enc_id] : '';
if (!sequra_safe_order_uri($order_uri)) { http_response_code(409); exit('A sessão de pagamento expirou. Volte à encomenda e tente novamente.'); }
$form_result = sequra_get_identification_form($order_uri);
if ($form_result['http_code'] < 200 || $form_result['http_code'] > 299 || trim($form_result['response_raw']) === '') {
    sequra_log($enc_id, sequra_order_reference($enc_id), '', $form_result['response_raw'], 'FORM_ERROR', 'HTTP ' . $form_result['http_code'] . ' ' . $form_result['curl_error']);
    http_response_code(502); exit('Não foi possível carregar o formulário seQura.');
}
$summary_url = SITE_HTTP_URL . 'p5-registo-utilizador-pt?id_enc=' . $enc_id . '&show_compras=1';
?><!doctype html><html lang="pt"><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><title>Pagamento seQura</title></head><body>
<?php echo $form_result['response_raw']; ?>
<script>(function(){var target=<?php echo json_encode($summary_url); ?>,configured=false;function show(){if(window.SequraFormInstance&&window.SequraFormInstance.show){if(!configured&&window.SequraFormInstance.setCloseCallback){window.SequraFormInstance.setCloseCallback(function(){window.location.href=target;});configured=true;}window.SequraFormInstance.show();}else{setTimeout(show,250);}}show();})();</script>
</body></html>
