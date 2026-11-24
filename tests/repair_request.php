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
repair_assert($quote['total'] === 469.90 && $quote['shipping_gross'] === 0, 'RRE must be free for repairs of at least 65 euros.');
repair_assert($quote['repair_net'] === 382.03 && $quote['shipping_net'] === 0 && $quote['vat'] === 87.87, 'VAT breakdown is inconsistent.');
foreach (array(0, 0.01, 12.90, 29.90, 64.99, 65, 65.01, 199.90, 469.90) as $price) {
    foreach (array(0, 6, 13, 23) as $rate) {
        $q = dotbright_repair_quote(repair_test_product($price, 'REPTEST', $rate));
        repair_assert((int)round(($q['repair_net'] + $q['shipping_net'] + $q['vat']) * 100) === (int)round($q['total'] * 100), 'Displayed components must sum to total.');
        repair_assert($q['shipping_gross'] === ($price >= 65 ? 0 : 10), 'RRE threshold must use the VAT-inclusive repair price.');
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
repair_assert($promotional_quote['repair_gross'] === 149.90 && $promotional_quote['total'] === 149.90, 'Never use raw obj_promo instead of the final catalogue PVP.');
repair_assert(!dotbright_repair_quote($promo, strtotime('2026-12-01'))['promotion'], 'Expired promotion must not show a promotional strike-through.');
repair_assert(!dotbright_repair_quote($promo, strtotime('2026-10-31'))['promotion'], 'Future promotion must not show a promotional strike-through.');

foreach (array(array(29.90,10,39.90),array(64.99,10,74.99),array(65,0,65),array(65.01,0,65.01)) as $case) {
    $boundary=dotbright_repair_quote(repair_test_product($case[0]));
    repair_assert($boundary['shipping_gross']===$case[1] && $boundary['total']===$case[2], 'RRE boundary or total incorrect.');
}
$discounted=repair_test_product(64.99);
$discounted['prod_precos']['PRECO_ANTIGO']=79.90;
repair_assert(dotbright_repair_quote($discounted)['shipping_gross']===10, 'A discounted repair below 65 euros must not inherit free RRE from its old price.');
$paid_rre=dotbright_repair_quote(repair_test_product(29.90));
repair_assert($paid_rre['shipping_net']===8.13 && $paid_rre['vat']===7.46 && $paid_rre['total']===39.90, 'The 10 euro RRE charge must already include VAT.');
$summary_source=file_get_contents(__DIR__.'/../layout/dotbright/code/produto.php');
foreach (explode("\n",$summary_source) as $summary_line) {
    if (strpos($summary_line,"$"."return .= '<section class=\"db-summary-card db-summary-amounts\"")!==false) {
        $repair_quote=$paid_rre; $return=''; eval($summary_line);
        repair_assert(strpos($return,'Portes</dt><dd>8,13 €')!==false && strpos($return,'IVA</dt><dd>7,46 €')!==false && strpos($return,'Total</dt><dd>39,90 €')!==false, 'Panel 4 must display the shared RRE calculation without changing its layout.');
        $summary_verified=true;
        break;
    }
}
repair_assert(!empty($summary_verified), 'Repair amounts template not found.');

$input = array('prod_id' => '115852', 'r_avaria' => " Ecrã partido & sem toque.\nNão liga <teste>. ", 'r_nome' => ' Cliente Teste ', 'r_recolha' => ' Rua de Teste 10 ', 'r_cp' => '4470-151',
    'r_cidade' => 'Maia', 'r_tel' => '912345678', 'r_email' => 'cliente@example.test', 'r_nif' => '123456789', 'r_periodo' => '2',
    'r_nome2' => ' ', 'r_morada2' => '', 'r_cp2' => '', 'r_nif2' => '', 'r_entrega' => ' ', 'r_unlock' => 'TEST-ACCESS-CODE');
$data = dotbright_repair_request_data($input);
repair_assert($data['r_avaria'] === trim($input['r_avaria']), 'Fault description must preserve accents and line breaks.');
repair_assert($data['r_entrega'] === 'Rua de Teste 10' && $data['r_morada2'] === 'Rua de Teste 10', 'Collection fallback must be shared by delivery and billing.');
repair_assert($data['r_nome2'] === 'Cliente Teste' && $data['r_cp2'] === '4470-151' && $data['r_nif2'] === '123456789', 'Billing fallbacks differ from the browser summary.');
repair_assert($data['period'] === 'Tarde' && dotbright_repair_request_data(array())['period'] === 'Não especificado', 'Collection period labels changed.');
$different = dotbright_repair_request_data(array_merge($input, array('r_entrega' => 'Outra morada', 'r_morada2' => 'Morada de faturação')));
repair_assert($different['r_entrega'] === 'Outra morada' && $different['r_morada2'] === 'Morada de faturação', 'Explicit addresses must be retained.');
$escaped = dotbright_repair_email_summary($product, dotbright_repair_request_data(array_merge($input, array('r_nome' => '<script>test</script>'))), $quote);
repair_assert(strpos($escaped, 'Ecrã partido &amp; sem toque.<br />') !== false && strpos($escaped, 'Não liga &lt;teste&gt;.') !== false, 'Fault description must be escaped and retain line breaks in email.');
repair_assert(strpos($escaped, '<script>') === false && strpos($escaped, '&lt;script&gt;') !== false, 'Request text must be escaped in email HTML.');
$paid_email=dotbright_repair_email_summary(repair_test_product(29.90),$data,$paid_rre);
repair_assert(strpos($paid_email,'Portes:</strong> 8,13 €')!==false && strpos($paid_email,'IVA:</strong> 7,46 €')!==false && strpos($paid_email,'Total:</strong> 39,90 €')!==false, 'Emails must use the same VAT-inclusive RRE charge as the summary.');
$free_email=dotbright_repair_email_summary($product,$data,$quote);
repair_assert(strpos($free_email,'Portes:</strong> 0,00 €')!==false && strpos($free_email,'Total:</strong> 469,90 €')!==false, 'Eligible repair emails must show zero RRE shipping.');

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
repair_assert($GLOBALS['repair_test_saved']['data']['r_avaria'] === trim($input['r_avaria']), 'Endpoint dropped the fault description before storage.');
repair_assert(count($GLOBALS['repair_test_emails']) === 3, 'Keep both staff recipients and the customer confirmation.');
$shared_summary = dotbright_repair_email_summary($promo, $data, dotbright_repair_quote($promo));
foreach ($GLOBALS['repair_test_emails'] as $email) {
    repair_assert(strpos($email['body'], $shared_summary) !== false, 'Every recipient must receive the same quote and normalized details.');
    repair_assert(strpos($email['body'], 'amanhã') === false, 'Do not promise an unbooked next-day collection.');
    repair_assert(strpos($email['body'], 'TEST-ACCESS-CODE') === false, 'Do not add unlock codes to email.');
}
repair_assert($result === 'Pedido enviado com sucesso!', 'Successful request response must contain only the short popup message.');
repair_assert(strpos($GLOBALS['repair_test_emails'][2]['body'], 'Consulte abaixo o resumo do seu pedido e os próximos passos.') !== false && strpos($GLOBALS['repair_test_emails'][2]['body'], 'Em breve receberá um e-mail') === false && strpos($GLOBALS['repair_test_emails'][2]['body'], 'DotBright - Serviço RRE') !== false, 'Customer confirmation must present the next steps without announcing another email.');
repair_test_endpoint($endpoint, $input, repair_test_product(99.90, 'REPIP13BAT'));
foreach ($GLOBALS['repair_test_emails'] as $email) repair_assert(strpos($email['body'], '12 meses') !== false, 'Battery warranty differs in email.');
$result = repair_test_endpoint($endpoint, $input, $product, false);
repair_assert(count($GLOBALS['repair_test_emails']) === 0 && strpos($result, 'Não foi possível registar') !== false, 'Never email success after a failed insert.');
$result = repair_test_endpoint($endpoint, $input, $product, true, array('assistencia@dotbright.pt'));
repair_assert(count($GLOBALS['repair_test_emails']) === 1 && strpos($result, 'ficou registado') !== false, 'Staff email failure must be distinguishable from registration failure.');
$result = repair_test_endpoint($endpoint, $input, $product, true, array('cliente@example.test'));
repair_assert(strpos($result, 'não foi possível enviar o email de confirmação') !== false, 'Customer email failure must not claim confirmation was sent.');
$modern = array_merge($input, array('r_form_version'=>'2','r_acesso'=>'refuse','r_entrega_tipo'=>'different',
    'r_recolha_obs'=>"Portaria & campainha\nAté às 18h.",'r_entrega'=>'Rua entrega <teste>','r_entrega_cp'=>'4000-002',
    'r_entrega_cidade'=>'Porto','r_entrega_obs'=>"Balcão <B>\nPiso 2.",'r_nome2'=>'Empresa & Filhos','r_nif2'=>'500000000',
    'r_morada2'=>'BILLING-ADDRESS-NOT-USED','r_cp2'=>'BILLING-CP-NOT-USED','m_padrao'=>'SECRET-PATTERN'));
$result = repair_test_endpoint($endpoint, $modern, repair_test_product(29.90));
$modern_data = dotbright_repair_request_data($modern);
$modern_summary = dotbright_repair_email_summary(repair_test_product(29.90), $modern_data, $paid_rre);
repair_assert($result === 'Pedido enviado com sucesso!' && count($GLOBALS['repair_test_emails']) === 3, 'Modern request must prepare all three emails.');
foreach ($GLOBALS['repair_test_emails'] as $email) {
    repair_assert(strpos($email['body'], $modern_summary) !== false && strpos($email['body'], 'Pedido: REP-93') !== false, 'Recipients must share the same modern summary and request number.');
    foreach (array('Portaria &amp; campainha<br />', 'Até às 18h.', 'Rua entrega &lt;teste&gt;', '4000-002', 'Porto', 'Balcão &lt;B&gt;<br />', 'Piso 2.', 'Empresa &amp; Filhos', '500000000', 'Não pretendo facultar o acesso.', 'Total:</strong> 39,90 €') as $expected) {
        repair_assert(strpos($email['body'], $expected) !== false, 'Missing or unescaped new email detail: '.$expected);
    }
    foreach (array('TEST-ACCESS-CODE', 'SECRET-PATTERN', 'BILLING-ADDRESS-NOT-USED', 'BILLING-CP-NOT-USED') as $secret) repair_assert(strpos($email['body'], $secret) === false, 'Email must exclude secrets and removed billing fields.');
}
preg_match('/<h3>Dados de faturação<\/h3>(.*?)<\/div>/s', $modern_summary, $billing_section);
repair_assert(strpos($billing_section[1], 'Morada') === false && strpos($billing_section[1], 'Código Postal') === false, 'Modern billing email must only contain name/company and NIF.');
$same = array_merge($modern, array('r_entrega_tipo'=>'same','r_nome2'=>'','r_nif2'=>''));
$same_data = dotbright_repair_request_data($same);
repair_assert($same_data['r_entrega'] === trim($input['r_recolha']) && $same_data['r_entrega_cp'] === $input['r_cp'] && $same_data['r_entrega_cidade'] === $input['r_cidade'] && $same_data['r_entrega_obs'] === '', 'Same delivery must use collection details and exclude stale delivery notes in email.');
repair_assert($same_data['r_nome2'] === trim($input['r_nome']) && $same_data['r_nif2'] === $input['r_nif'], 'Email billing fallback must match stored request.');
foreach (array('remove','code','pattern','refuse') as $mode) {
    $mode_data = dotbright_repair_request_data(array_merge($same, array('r_acesso'=>$mode)));
    $body = dotbright_repair_email_summary($product, $mode_data, $quote);
    repair_assert(strpos($body, dotbright_repair_access_labels()[$mode]) !== false, 'Email must show the selected access intent.');
    repair_assert(strpos($body, 'TEST-ACCESS-CODE') === false && strpos($body, 'SECRET-PATTERN') === false, 'No access credential may be included in an email.');
    repair_assert(strpos($body, 'Rua entrega') === false && strpos($body, 'Piso 2.') === false && strpos($body, 'Igual à morada de recolha') !== false, 'Inactive delivery details must not appear in email.');
}

$current=array_merge($modern,array('r_form_version'=>'3','r_avaria'=>'','r_nif'=>'REMOVED-CUSTOMER-NIF','r_nif2'=>'500000000'));
$result=repair_test_endpoint($endpoint,$current,repair_test_product(29.90));
repair_assert($result==='Pedido enviado com sucesso!','Optional fault information must not block email preparation.');
foreach($GLOBALS['repair_test_emails'] as $email) {
    preg_match('/<h3>Dados do cliente e recolha<\/h3>(.*?)<\/div>/s',$email['body'],$client_section);
    repair_assert(strpos($client_section[1],'NIF')===false && strpos($email['body'],'REMOVED-CUSTOMER-NIF')===false && strpos($email['body'],'500000000')!==false,'Email must contain billing NIF only.');
    repair_assert(strpos($email['body'],'Informações adicionais sobre a avaria:</strong> —')!==false,'Empty optional information must be represented without invented fault details.');
}
repair_assert(dotbright_repair_request_data(array_merge($current,array('r_nif2'=>'')))['r_nif2']==='','Current billing NIF must stay empty when omitted.');

echo "PASS: repair quotes, VAT, catalogue promotions, warranties, modern/legacy details, billing-only NIF and isolated request emails/failures. No DB or email side effects.\n";
