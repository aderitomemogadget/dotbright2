<?php
// In-memory database double only: never contacts a server or transmits email.
require_once __DIR__.'/../core/repair_request.php';
function check($ok, $message) { if (!$ok) throw new Exception($message); }
function numberformat($v) { return number_format($v, 2, ',', '.').' €'; }
$GLOBALS['DB_host'] = $GLOBALS['DB_user'] = $GLOBALS['DB_pass'] = $GLOBALS['DB_name'] = 'test';
$GLOBALS['settings_queries'] = $GLOBALS['settings_writes'] = array();
$GLOBALS['settings_rows'] = array();
$GLOBALS['settings_missing'] = in_array('--missing-table', $argv, true);
function db_opendb_conection($h, $u, $p) { return true; }
class DataObj {
    public $db_data;
    function db_open($link, $db, $sql) {
        $GLOBALS['settings_queries'][] = $sql;
        $this->db_data = false;
        if (preg_match('/FROM products_families WHERE ID=(\d+)/', $sql, $m)) {
            $parents = array(85 => 0, 86 => 85, 3665 => 86, 10 => 0, 11 => 10, 90 => 91, 91 => 90);
            if (isset($parents[$m[1]])) $this->db_data = array('ID' => (int)$m[1], 'ID_ORG' => $parents[$m[1]]);
        } elseif (strpos($sql, 'SHOW TABLES') === 0 && !$GLOBALS['settings_missing']) {
            $this->db_data = array('table' => 'repair_product_settings');
        } elseif (preg_match('/FROM repair_product_settings WHERE product_id=(\d+)/', $sql, $m)) {
            $this->db_data = isset($GLOBALS['settings_rows'][$m[1]]) ? $GLOBALS['settings_rows'][$m[1]] : false;
        }
        return true;
    }
    function db_close() {}
}
function Runquery($link, $db, $sql) { $GLOBALS['settings_writes'][] = $sql; return empty($GLOBALS['settings_fail']); }
function product($id, $family = 3665) {
    return array('prod_id' => $id, 'fam_id' => $family, 'prod_nome' => 'Reparação ecrã original', 'prod_ref' => 'REPTEST', 'prod_precos' => array('PVP' => 199.90, 'IVA' => 23));
}
check(dotbright_is_repair_family(85) && dotbright_is_repair_family(3665), 'Repair root/descendants not recognized.');
check(!dotbright_is_repair_family(11) && !dotbright_is_repair_family(90) && !dotbright_is_repair_family(999), 'Normal, cyclic or missing family accepted.');
if ($GLOBALS['settings_missing']) {
    check(!dotbright_repair_settings_ready(), 'Missing migration not detected.');
    check(dotbright_repair_quote(product(1))['warranty'] === '36 meses', 'Missing table must preserve original warranty.');
    check(dotbright_repair_quote(product(1))['service']['time'] === 'Sob consulta', 'Missing table must preserve original time.');
    check(!dotbright_repair_settings_save(1, 3665, array()), 'Save without migration accepted.');
    echo "PASS: missing migration safely preserves existing repair information.\n";
    exit;
}
$base = dotbright_repair_settings_validate(array('time_code' => '1h', 'part_type' => 'apple', 'part_icon' => 'tools', 'warranty_months' => '24'));
check(dotbright_repair_settings_validate($base) === $base, 'Validated values must survive saving/loading, including nullable warranty.');
$blank = dotbright_repair_settings_validate(array());
check(dotbright_repair_settings_validate($blank) === $blank, 'Nullable default warranty cannot be saved twice.');
foreach (array('1h' => '1 hora', '3h' => '3 horas', '1d' => '1 dia', '3d' => '3 dias', '1-2w' => '1 a 2 semanas', 'custom' => 'Após diagnóstico') as $code => $label) {
    $id = 100 + count($GLOBALS['settings_rows']);
    $GLOBALS['settings_rows'][$id] = array_merge($base, array('time_code' => $code, 'time_custom' => 'Após diagnóstico'));
    $q = dotbright_repair_quote(product($id));
    check($q['service']['time'] === $label && $q['service']['part'] === 'Peça oficial Apple' && $q['service']['part_icon'] === 'tools' && $q['warranty'] === '24 meses', 'Configured values not used consistently.');
    check($q['total'] === 199.90 && $q['shipping_gross'] === 0, 'Settings must not alter price or free RRE eligibility.');
}
foreach (array('apple', 'original', 'take-out', 'premium', 'compatible', 'custom') as $type) {
    $id = 200 + count($GLOBALS['settings_rows']);
    $GLOBALS['settings_rows'][$id] = array_merge($base, array('part_type' => $type, 'part_label' => 'Peça personalizada & testada', 'part_detail' => 'Descrição com acentos: ecrã.', 'warranty_months' => 0));
    $q = dotbright_repair_quote(product($id));
    check($q['service']['part'] === 'Peça personalizada & testada' && $q['warranty'] === '0 meses', 'Custom label or zero-month warranty lost.');
}
// Even if the ID has settings and its reference looks like a repair, a normal family cannot use them.
$GLOBALS['settings_rows'][300] = $base;
$before = count($GLOBALS['settings_queries']);
$q = dotbright_repair_quote(product(300, 11));
check($q['warranty'] === '36 meses' && $q['service']['time'] === 'Sob consulta', 'Settings leaked into ordinary product.');
check(count($GLOBALS['settings_queries']) === $before, 'Normal product queried repair settings table.');
check(!dotbright_repair_settings_save(300, 11, $base) && count($GLOBALS['settings_writes']) === 0, 'Normal product settings were written.');
check(dotbright_repair_settings_save(301, 3665, $base), 'Repair settings not saved.');
check(strpos($GLOBALS['settings_writes'][0], 'ON DUPLICATE KEY UPDATE') !== false && strpos($GLOBALS['settings_writes'][0], 'products SET') === false, 'Save must be isolated to settings upsert.');
$GLOBALS['settings_fail'] = true;
check(!dotbright_repair_settings_save(301, 3665, $base), 'Write failure not reported.');
foreach (array(array('time_code' => 'invalid'), array('part_icon' => '<svg>'), array('warranty_months' => -1), array('warranty_months' => 121), array('warranty_months' => '12.5'), array('part_label' => array()), array('part_label' => '<script>'), array('part_label' => str_repeat('ç', 121)), array('time_code' => 'custom'), array('part_type' => 'custom')) as $bad) {
    $thrown = false;
    try { dotbright_repair_settings_validate($bad); } catch (InvalidArgumentException $e) { $thrown = true; }
    check($thrown, 'Invalid configuration accepted.');
}
$GLOBALS['settings_rows'][400] = array_merge($base, array('part_detail' => 'Descrição curta & confirmação'));
$q = dotbright_repair_quote(product(400));
$email = dotbright_repair_email_summary(product(400), dotbright_repair_request_data(array()), $q);
check(strpos($email, '24 meses') !== false && strpos($email, '1 hora') !== false && strpos($email, 'Peça oficial Apple') !== false, 'Emails do not use configured repair details.');
check(strpos($email, 'Descrição curta &amp; confirmação') !== false, 'Email must include and escape the configured short description.');
// Render the actual summary fragment with the same quote used by the emails.
$summary_source = file_get_contents(__DIR__.'/../layout/dotbright/code/produto.php');
$summary_start = strpos($summary_source, "$"."return .= strtoupper($"."repair_quote['warranty']).' DE GARANTIA';");
$summary_end = strpos($summary_source, "$"."return .= '<section class=\"db-summary-card db-summary-amounts\"", $summary_start);
check($summary_start !== false && $summary_end !== false, 'Summary fragment not found.');
$repair_quote = $q;
$return = '';
eval(substr($summary_source, $summary_start, $summary_end - $summary_start));
check(strpos($return, '24 MESES DE GARANTIA') !== false && strpos($return, '1 hora') !== false && strpos($return, 'Peça oficial Apple') !== false && strpos($return, 'Descrição curta &amp; confirmação') !== false, 'Panel 4 must show the configured warranty, time, part and escaped description.');
// Render the actual existing information-panel template, changing data only.
require_once __DIR__.'/../layout/dotbright/code/repair_info_panel.php';
define('PROJETO', 'dotbright');
$GLOBALS['site_http_url'] = 'https://example.test/';
$GLOBALS['site_shell_url'] = dirname(__DIR__).'/';
$GLOBALS['products_page_id'] = 3;
function seo($url, $id, $type) { return 'repair-'.$id; }
$html = dotbright_repair_info_panel(product(400), '<p class="our_price_display">199,90 €</p>');
check(strpos($html, '<strong>1 hora</strong>') !== false && strpos($html, 'Garantia: 24 meses') !== false, 'Panel data not configured.');
check(strpos($html, 'reparacaoEtapa(2)') !== false && substr_count($html, 'db-repair-fact"') === 5, 'Panel structure/navigation changed.');
foreach (array(
    array('Reparação lente da câmara iPhone 17 Pro Max', 'iPhone 17 Pro Max', 'iPhone 17 Pro Max'),
    array('Reparação Ecrã OLED Premium iPhone 17 Pro Max - Qualidade equivalente ao original', 'iPhone 17 Pro Max', 'iPhone 17 Pro Max'),
    array('Reparação bateria iPhone 17 Pro Max (Peça oficial Apple)', 'iPhone 17 Pro Max', 'iPhone 17 Pro Max'),
    array('Substituição LCD completo MacBook Pro 13.3&quot; A1706 2016-2017', 'MacBook Pro 13.3&quot; A1706', 'MacBook Pro 13.3" A1706 2016-2017'),
    array('Substituição Bateria MacBook Pro A1706 2016-2017', 'MacBook Pro 13.3" A1706', 'MacBook Pro A1706 2016-2017'),
    array('Reparação ecrã Samsung Galaxy S24 Ultra', 'Galaxy S24 Ultra', 'Samsung Galaxy S24 Ultra'),
    array('Reparação ecrã OnePlus 12R', 'OnePlus 12R', 'OnePlus 12R'),
    array('Reparação ecrã Huawei P30 Pro', 'P30 Pro', 'Huawei P30 Pro')
) as $title_case) {
    $parts=dotbright_repair_title_parts($title_case[0],$title_case[1]);
    check($parts[1]===$title_case[2], 'Full device model not kept together: '.$title_case[0]);
    check(implode(' ',array_filter($parts,'strlen'))===html_entity_decode($title_case[0],ENT_QUOTES | ENT_HTML5,'UTF-8'),'Title formatting lost or reordered catalogue text.');
}
check(dotbright_repair_title_parts('Reparação de equipamento sem modelo')===array('Reparação de equipamento sem modelo','',''),'Unknown models must not invent a title split.');
$title_product=product(400);$title_product['prod_nome']='Reparação lente <teste> iPhone 17 Pro Max';
$title_html=dotbright_repair_info_panel($title_product,'<p class="our_price_display">199,90 €</p>');
check(strpos($title_html,'db-repair-title-service">Reparação lente &lt;teste&gt;')!==false && strpos($title_html,'db-repair-title-model">iPhone 17 Pro Max')!==false,'Title must escape text and separate intervention/model in the actual template.');
$repair_settings_tab = true; $ID = 400; $ID_Fam = 3665;
ob_start(); include __DIR__.'/../admin/products/tabs/tab_reparacao.php'; $admin = ob_get_clean();
check(strpos($admin, 'repair_settings[time_code]') !== false && strpos($admin, 'value="1h" selected') !== false && strpos($admin, 'value="24"') !== false, 'Admin does not load saved configuration.');
$repair_settings_tab = false;
ob_start(); include __DIR__.'/../admin/products/tabs/tab_reparacao.php'; $admin = ob_get_clean();
check($admin === '', 'Repair fields rendered for ordinary product.');
// Submit through the actual endpoint with configured product data and captured emails.
function get_produtc_info($id) { return product(400); }
function dotbright_repair_order_create($product,$data,$quote,$input) { return 94; }
function dotbright_repair_order_send($id,$role,$to,$subject,$body) { return send_email($to,$subject,$body); }
function send_email($to, $subject, $body) { $GLOBALS['settings_emails'][] = $body; return true; }
$GLOBALS['settings_fail'] = false;
$GLOBALS['settings_emails'] = array();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('prod_id' => 400, 'r_nome' => 'Cliente teste', 'r_avaria' => 'Lente partida & fotografias desfocadas.', 'r_email' => 'cliente@example.test');
$endpoint = file_get_contents(__DIR__.'/../ajax/form_rapara.php');
$endpoint = str_replace(array("include \$path.'core_files.php';", "require_once \$path.'repair_request.php';", "require_once \$path.'repair_orders.php';"), '', $endpoint);
$endpoint = preg_replace('/^<\?php\s*/', '', $endpoint);
ob_start(); eval($endpoint); $response = ob_get_clean();
check($response === 'Pedido enviado com sucesso!' && count($GLOBALS['settings_emails']) === 3, 'Configured repair submission failed.');
foreach ($GLOBALS['settings_emails'] as $body) check(strpos($body, '24 meses') !== false && strpos($body, '1 hora') !== false && strpos($body, 'Peça oficial Apple') !== false, 'Configured data lost in submitted request email.');
foreach ($GLOBALS['settings_emails'] as $body) check(strpos($body, 'Descrição curta &amp; confirmação') !== false, 'Short description missing from staff or customer email.');
foreach ($GLOBALS['settings_emails'] as $body) check(strpos($body, 'Lente partida &amp; fotografias desfocadas.') !== false, 'Fault description missing from customer or staff email.');
echo "PASS: repair family isolation, settings validation/save/failures, all times/parts/warranties, original pricing, emails, panel and admin rendering. No external side effects.\n";
