<?php
// Standalone regression tests. No database connection or email transmission is made.
date_default_timezone_set('Europe/Lisbon');
require_once __DIR__.'/../core/repair_request.php';

function repair_assert($condition, $message)
{
    if (!$condition) throw new Exception($message);
}
function numberformat($value, $side = 0)
{
    return number_format($value, 2, ',', '.').' €';
}
function repair_test_product($price = 469.90, $reference = 'REPIP17PROMAXDILCDOFI', $rate = 23)
{
    return array('prod_id' => 115852, 'prod_nome' => 'Reparação de teste', 'prod_ref' => $reference, 'fam_id' => 3665, 'Ref_Ean' => '',
        'prod_precos' => array('PVP' => $price, 'IVA' => $rate, 'PROMOCAO' => 0));
}
$GLOBALS['iva_default'] = 23;
$product = repair_test_product();
$quote = dotbright_repair_quote($product);
repair_assert($quote['total'] === 474.80, 'Total must include the full 4.90 shipping charge.');
repair_assert($quote['repair_net'] === 382.03 && $quote['shipping_net'] === 3.98 && $quote['vat'] === 88.79, 'VAT breakdown is inconsistent.');
foreach (array(0, 0.01, 12.90, 65, 199.90, 469.90) as $price) {
    foreach (array(0, 6, 13, 23) as $rate) {
        $q = dotbright_repair_quote(repair_test_product($price, 'REPTEST', $rate));
        repair_assert((int)round(($q['repair_net'] + $q['shipping_net'] + $q['vat']) * 100) === (int)round($q['total'] * 100), 'Displayed components must sum to total.');
    }
}
repair_assert(dotbright_repair_quote(repair_test_product(99.90, 'REPIP13BAT_variant'))['warranty'] === '12 meses', 'Battery warranty must use the current product rule.');
repair_assert($quote['warranty'] === '36 meses', 'Standard warranty changed.');
$seminovo = $product;
$seminovo['fam_id'] = 163;
repair_assert(dotbright_repair_quote($seminovo)['warranty'] === '24 meses', 'Existing 163 family fallback changed.');
$promo = repair_test_product(149.90);
$promo['prod_precos'] = array_merge($promo['prod_precos'], array('PROMOCAO' => 1, 'PRECO_ANTIGO' => 199.90,
    'obj_promo' => 100, 'Promo_data_ini' => '2026-11-01 00:00:00', 'Promo_data_fim' => '2026-11-30 23:59:59'));
$promotional_quote = dotbright_repair_quote($promo, strtotime('2026-11-19 12:00:00'));
repair_assert($promotional_quote['promotion'] && $promotional_quote['old_price'] === 199.90, 'Active promotion should show the catalogue old price.');
repair_assert($promotional_quote['repair_gross'] === 149.90 && $promotional_quote['total'] === 154.80, 'Never use raw obj_promo instead of the final catalogue PVP.');
repair_assert(!dotbright_repair_quote($promo, strtotime('2026-12-01'))['promotion'], 'Expired promotion must not show a promotional strike-through.');
repair_assert(!dotbright_repair_quote($promo, strtotime('2026-10-31'))['promotion'], 'Future promotion must not show a promotional strike-through.');

$input = array('prod_id' => '115852', 'r_nome' => ' Cliente Teste ', 'r_recolha' => ' Rua de Teste 10 ', 'r_cp' => '4470-151',
    'r_cidade' => 'Maia', 'r_tel' => '912345678', 'r_email' => 'cliente@example.test', 'r_nif' => '123456789', 'r_periodo' => '2',
    'r_nome2' => ' ', 'r_morada2' => '', 'r_cp2' => '', 'r_nif2' => '', 'r_entrega' => ' ', 'r_unlock' => 'TEST-ACCESS-CODE');
$data = dotbright_repair_request_data($input);
repair_assert($data['r_entrega'] === 'Rua de Teste 10' && $data['r_morada2'] === 'Rua de Teste 10', 'Collection fallback must be shared by delivery and billing.');
repair_assert($data['r_nome2'] === 'Cliente Teste' && $data['r_cp2'] === '4470-151' && $data['r_nif2'] === '123456789', 'Billing fallbacks differ from the browser summary.');
repair_assert($data['period'] === 'Tarde' && dotbright_repair_request_data(array())['period'] === 'Não especificado', 'Collection period labels changed.');
$different = dotbright_repair_request_data(array_merge($input, array('r_entrega' => 'Outra morada', 'r_morada2' => 'Morada de faturação')));
repair_assert($different['r_entrega'] === 'Outra morada' && $different['r_morada2'] === 'Morada de faturação', 'Explicit addresses must be retained.');
$escaped = dotbright_repair_email_summary($product, dotbright_repair_request_data(array_merge($input, array('r_nome' => '<script>test</script>'))), $quote);
repair_assert(strpos($escaped, '<script>') === false && strpos($escaped, '&lt;script&gt;') !== false, 'Request text must be escaped in email HTML.');

// Replace only the endpoint's bootstrapping with these in-memory dependencies.
function get_produtc_info($id) { return $GLOBALS['repair_test_product']; }
function db_opendb_conection($host, $user, $pass) { return true; }
function Runquery($link, $database, $sql) { $GLOBALS['repair_test_sql'][] = $sql; return $GLOBALS['repair_test_db_ok']; }
function dotbright_repair_order_create($product, $data, $quote, $input) {
    if (!$GLOBALS['repair_test_db_ok']) throw new RuntimeException('Test database failure');
    $GLOBALS['repair_test_saved'] = array('product'=>$product, 'data'=>$data, 'quote'=>$quote);
    return 93;
}
function dotbright_repair_order_send($id,$role,$to,$subject,$body) { return send_email($to,$subject,$body); }
function send_email($to, $subject, $body)
{
    $GLOBALS['repair_test_emails'][] = array('to' => $to, 'subject' => $subject, 'body' => $body);
    return !in_array($to, $GLOBALS['repair_test_fail_recipients'], true);
}
$endpoint = file_get_contents(__DIR__.'/../ajax/form_rapara.php');
foreach (array("include \$path.'core_files.php';", "require_once \$path.'repair_request.php';", "require_once \$path.'repair_orders.php';") as $bootstrap) {
    repair_assert(substr_count($endpoint, $bootstrap) === 1, 'Endpoint bootstrapping changed: review test isolation.');
    $endpoint = str_replace($bootstrap, '', $endpoint);
}
$endpoint = preg_replace('/^<\?php\s*/', '', $endpoint);
function repair_test_endpoint($endpoint, $input, $product, $db_ok = true, $failed_recipients = array())
{
    $DB_host = $DB_user = $DB_pass = $DB_name = 'test';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = $input;
    $GLOBALS['repair_test_product'] = $product;
    $GLOBALS['repair_test_db_ok'] = $db_ok;
    $GLOBALS['repair_test_fail_recipients'] = $failed_recipients;
    $GLOBALS['repair_test_emails'] = $GLOBALS['repair_test_sql'] = array();
    ob_start();
    eval($endpoint);
    return ob_get_clean();
}
$result = repair_test_endpoint($endpoint, $input, $promo);
repair_assert(count($GLOBALS['repair_test_emails']) === 3, 'Keep both staff recipients and the customer confirmation.');
$shared_summary = dotbright_repair_email_summary($promo, $data, dotbright_repair_quote($promo));
foreach ($GLOBALS['repair_test_emails'] as $email) {
    repair_assert(strpos($email['body'], $shared_summary) !== false, 'Every recipient must receive the same quote and normalized details.');
    repair_assert(strpos($email['body'], 'amanhã') === false, 'Do not promise an unbooked next-day collection.');
    repair_assert(strpos($email['body'], 'TEST-ACCESS-CODE') === false, 'Do not add unlock codes to email.');
}
repair_assert($result === 'Pedido enviado com sucesso!', 'Successful request response must contain only the short popup message.');
repair_assert(strpos($GLOBALS['repair_test_emails'][2]['body'], 'Somos rápidos a responder!') !== false && strpos($GLOBALS['repair_test_emails'][2]['body'], 'DotBright - Serviço RRE') !== false, 'The full approved message must remain in the customer email.');
repair_test_endpoint($endpoint, $input, repair_test_product(99.90, 'REPIP13BAT'));
foreach ($GLOBALS['repair_test_emails'] as $email) repair_assert(strpos($email['body'], '12 meses') !== false, 'Battery warranty differs in email.');
$result = repair_test_endpoint($endpoint, $input, $product, false);
repair_assert(count($GLOBALS['repair_test_emails']) === 0 && strpos($result, 'Não foi possível registar') !== false, 'Never email success after a failed insert.');
$result = repair_test_endpoint($endpoint, $input, $product, true, array('assistencia@dotbright.pt'));
repair_assert(count($GLOBALS['repair_test_emails']) === 1 && strpos($result, 'ficou registado') !== false, 'Staff email failure must be distinguishable from registration failure.');
$result = repair_test_endpoint($endpoint, $input, $product, true, array('cliente@example.test'));
repair_assert(strpos($result, 'não foi possível enviar o email de confirmação') !== false, 'Customer email failure must not claim confirmation was sent.');
echo "PASS: repair quotes, VAT, catalogue promotions, warranties, address defaults and isolated request emails/failures. No DB or email side effects.\n";
