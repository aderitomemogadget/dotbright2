<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] === __FILE__) exit;

function sequra_order_reference($enc_id)
{
    $config = sequra_config();
    return strtoupper(preg_replace('/[^A-Z0-9_-]/i', '', $config['order_prefix'])) . '-' . (int) $enc_id;
}

function sequra_order_token($enc_id)
{
    $config = sequra_config();
    if ($config['token_secret'] === '') return '';
    return hash_hmac('sha256', sequra_order_reference($enc_id), $config['token_secret']);
}

function sequra_valid_order_token($enc_id, $token)
{
    $expected = sequra_order_token($enc_id);
    return $expected !== '' && is_string($token) && hash_equals($expected, $token);
}

function sequra_sandbox_order_access_allowed($enc_id)
{
    $config = sequra_config();
    if (!$config['sandbox']) return true;

    $enc_id = (int) $enc_id;
    if ($enc_id <= 0) return false;

    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs = new DataObj;
    $rs->db_open(
        $db_link,
        $GLOBALS['DB_name'],
        "SELECT c.Testes FROM encomendas e INNER JOIN clientes c ON c.ID=e.ID_Cliente WHERE e.ID='" . $enc_id . "' LIMIT 1"
    );
    $allowed = !$rs->EOF() && isset($rs->db_data['Testes']) && (int) $rs->db_data['Testes'] === 1;
    $rs->db_close();
    return $allowed;
}

function sequra_verify_signature($raw_body)
{
    $config = sequra_config();
    if ($config['signature_secret'] === '') return true;
    $provided = isset($_SERVER['HTTP_X_SEQURA_SIGNATURE']) ? trim($_SERVER['HTTP_X_SEQURA_SIGNATURE']) : '';
    if ($provided === '') return false;
    return hash_equals(hash_hmac('sha256', $raw_body, $config['signature_secret']), $provided);
}

function sequra_redact_log_value($value, $key = '')
{
    $sensitive = '/email|phone|mobile|address|postal|city|name|surname|given|nif|nin|vat|password|token|secret|access/i';
    if ($key !== '' && preg_match($sensitive, $key)) return '[REDACTED]';
    if (is_array($value)) {
        $clean = array();
        foreach ($value as $k => $v) $clean[$k] = sequra_redact_log_value($v, (string) $k);
        return $clean;
    }
    return $value;
}

function sequra_log_text($value)
{
    if (is_array($value)) $value = json_encode(sequra_redact_log_value($value));
    $value = (string) $value;
    $decoded = json_decode($value, true);
    if (is_array($decoded)) $value = json_encode(sequra_redact_log_value($decoded));
    elseif (stripos($value, '<html') !== false || stripos($value, '<form') !== false) $value = '[HTML response omitted]';
    return substr($value, 0, 12000);
}

function sequra_log($enc_id, $order_ref, $request, $response, $state, $errors = '')
{
    $config = sequra_config();
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $sql = "INSERT INTO sequra_logs (ID_Encomenda, Data, Merchant_ID, Order_Ref, Request, Response, Estado, Erros) VALUES ('" .
        (int) $enc_id . "','" . date('Y-m-d H:i:s') . "','" . addslashes($config['merchant_id']) . "','" .
        addslashes($order_ref) . "','" . addslashes(sequra_log_text($request)) . "','" .
        addslashes(sequra_log_text($response)) . "','" . addslashes($state) . "','" .
        addslashes(sequra_log_text($errors)) . "')";
    $result = @Runquery($db_link, $GLOBALS['DB_name'], $sql);
    if (!$result) error_log('seQura [' . $state . '] order=' . (int) $enc_id);
    return $result;
}

function sequra_money_cents($value) { return (int) round((float) $value * 100); }
function sequra_without_tax($cents, $rate) { return $rate > 0 ? (int) round($cents / (1 + ($rate / 100))) : (int) $cents; }
function sequra_clean_phone($phone) { return preg_replace('/[^0-9]/', '', (string) $phone); }

function sequra_endpoint_base()
{
    $endpoint = sequra_config()['endpoint'];
    return substr($endpoint, -7) === '/orders' ? substr($endpoint, 0, -7) : $endpoint;
}

function sequra_safe_order_uri($uri)
{
    $uri = trim((string) $uri);
    $endpoint = sequra_config()['endpoint'];
    $target = parse_url($uri);
    $allowed = parse_url($endpoint);
    return $uri !== '' && isset($target['scheme'], $target['host'], $allowed['host']) &&
        strtolower($target['scheme']) === 'https' && strtolower($target['host']) === strtolower($allowed['host']);
}

function sequra_get_http_header($headers, $name)
{
    foreach ((array) $headers as $header) {
        $separator = strpos($header, ':');
        if ($separator !== false && strcasecmp(trim(substr($header, 0, $separator)), $name) === 0) return trim(substr($header, $separator + 1));
    }
    return '';
}

function sequra_api_request($method, $url, $payload = null, $accept = 'application/json')
{
    if (!sequra_is_ready()) return array('http_code'=>503, 'curl_error'=>'Integração seQura não configurada.', 'request'=>'', 'response_raw'=>'', 'response'=>array(), 'headers'=>array(), 'location'=>'');
    if (!sequra_safe_order_uri($url) && strpos($url, sequra_endpoint_base() . '/orders') !== 0) return array('http_code'=>400, 'curl_error'=>'Destino seQura inválido.', 'request'=>'', 'response_raw'=>'', 'response'=>array(), 'headers'=>array(), 'location'=>'');
    $config = sequra_config();
    $body = $payload === null ? null : json_encode($payload);
    $headers_raw = array();
    $headers = array('Accept: ' . $accept, 'Sequra-Merchant-Id: ' . $config['merchant_id']);
    if ($config['assets_key'] !== '') $headers[] = 'X-Assets-Key: ' . $config['assets_key'];
    if ($body !== null) { $headers[] = 'Content-Type: application/json'; $headers[] = 'Content-Length: ' . strlen($body); }
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERPWD => $config['username'] . ':' . $config['password'],
        CURLOPT_USERAGENT => 'dotbright-sequra-php/' . phpversion(),
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$headers_raw) { $headers_raw[] = trim($header); return strlen($header); }
    ));
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $response = curl_exec($ch);
    $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    return array('http_code'=>$http_code, 'curl_error'=>$curl_error, 'request'=>$body, 'response_raw'=>(string) $response,
        'response'=>json_decode((string) $response, true), 'headers'=>$headers_raw, 'location'=>sequra_get_http_header($headers_raw, 'Location'));
}

function sequra_get_order_payload($enc_id, $state = '')
{
    $enc_id = (int) $enc_id;
    $config = sequra_config();
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $order_rs = new DataObj;
    $item_rs = new DataObj;
    $order_rs->db_open($db_link, $GLOBALS['DB_name'], "SELECT * FROM encomendas WHERE ID='" . $enc_id . "'");
    if ($order_rs->EOF()) { $order_rs->db_close(); return false; }
    $order = $order_rs->db_data;
    $reference = sequra_order_reference($enc_id);
    $items = array();
    $item_rs->db_open($db_link, $GLOBALS['DB_name'], 'SELECT * FROM encomendas_produtos WHERE ID_PAI=' . $enc_id . ' ORDER BY ID ASC');
    while (!$item_rs->EOF()) {
        $row = $item_rs->db_data;
        $quantity = max(1, (int) $row['Qtd']);
        $tax = isset($row['IVA_taxa']) && (float) $row['IVA_taxa'] > 0 ? (float) $row['IVA_taxa'] : 23;
        $with_tax = sequra_money_cents($row['Valor']);
        $without_tax = sequra_without_tax($with_tax, $tax);
        $items[] = array('reference'=>(string) $row['Ref'], 'name'=>trim(strip_tags($row['Nome'])), 'tax_rate'=>$tax,
            'quantity'=>$quantity, 'price_without_tax'=>$without_tax, 'total_without_tax'=>$without_tax * $quantity,
            'price_with_tax'=>$with_tax, 'total_with_tax'=>$with_tax * $quantity, 'downloadable'=>false, 'perishable'=>false,
            'personalized'=>false, 'category'=>'', 'manufacturer'=>'', 'supplier'=>'', 'product_id'=>(string) $row['ID_Produto'], 'url'=>'');
        $item_rs->db_next();
    }
    $item_rs->db_close();
    if ((float) $order['Portes'] > 0) {
        $shipping = sequra_money_cents($order['Portes']);
        $items[] = array('type'=>'handling', 'reference'=>'shipping', 'name'=>'Portes', 'tax_rate'=>23,
            'total_without_tax'=>sequra_without_tax($shipping, 23), 'total_with_tax'=>$shipping);
    }
    $total_with = 0; $total_without = 0;
    foreach ($items as $item) { $total_with += (int) $item['total_with_tax']; $total_without += (int) $item['total_without_tax']; }
    $first_name = trim($order['enc_Nome']);
    $last_name = trim($order['enc_Apelido']);
    if ($last_name === '') { $parts = preg_split('/\s+/', $first_name); $first_name = array_shift($parts); $last_name = $parts ? implode(' ', $parts) : 'Cliente'; }
    $phone = sequra_clean_phone($order['enc_Telefone_ent']);
    if ($phone === '') $phone = sequra_clean_phone($order['enc_Telefone']);
    $nif = preg_replace('/[^0-9]/', '', (string) $order['enc_Nif']);
    $address = array('given_names'=>$first_name, 'surnames'=>$last_name, 'company'=>'',
        'address_line_1'=>substr($order['enc_Morada_ent'] !== '' ? $order['enc_Morada_ent'] : $order['enc_Morada'], 0, 100),
        'address_line_2'=>'', 'postal_code'=>substr($order['enc_Codigo_Postal_ent'] !== '' ? $order['enc_Codigo_Postal_ent'] : $order['enc_Codigo_Postal'], 0, 20),
        'city'=>substr($order['enc_Cidade_ent'] !== '' ? $order['enc_Cidade_ent'] : $order['enc_Cidade'], 0, 100),
        'country_code'=>'PT', 'phone'=>'', 'mobile_phone'=>$phone, 'state'=>'', 'vat_number'=>$nif);
    $token = sequra_order_token($enc_id);
    $base = SITE_HTTP_URL . 'external_api/pagamentos/pgm_sequra/';
    $query = http_build_query(array('id_enc'=>$enc_id, 'token'=>$token));
    $payload = array('merchant'=>array('id'=>$config['merchant_id'], 'edit_url'=>SITE_HTTP_URL,
            'abort_url'=>$base . 'cancel.php?' . $query, 'return_url'=>$base . 'return.php?' . $query,
            'notify_url'=>$base . 'notify.php', 'notification_parameters'=>array('cart'=>(string) $enc_id, 'token'=>$token)),
        'cart'=>array('cart_ref'=>$reference, 'currency'=>$config['currency'], 'delivery_method'=>array('name'=>'Transportadora', 'days'=>'3 dias'),
            'gift'=>false, 'items'=>$items, 'order_total_without_tax'=>$total_without, 'order_total_with_tax'=>$total_with),
        'delivery_address'=>$address, 'invoice_address'=>$address,
        'customer'=>array('given_names'=>$first_name, 'surnames'=>$last_name, 'title'=>'Mr', 'email'=>$order['enc_Email'],
            'logged_in'=>((int) $order['ID_Cliente'] > 0), 'language_code'=>'pt', 'ip_number'=>isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'user_agent'=>isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', 'previous_orders'=>array(), 'nin'=>$nif, 'vat_number'=>$nif),
        'gui'=>array('layout'=>'desktop'),
        'platform'=>array('name'=>'Dotbright', 'version'=>'1.0.0', 'plugin_version'=>'1.0.0', 'php_version'=>phpversion(), 'php_os'=>PHP_OS,
            'uname'=>php_uname(), 'db_name'=>'mysql', 'db_version'=>'0.0.0'),
        'state'=>$state, 'order'=>array('reference'=>$reference));
    if ($state === 'confirmed') $payload['merchant_reference'] = array('order_ref_1'=>$reference);
    $order_rs->db_close();
    return $payload;
}

function sequra_create_order($payload)
{
    $enc_id = (int) str_replace(sequra_config()['order_prefix'] . '-', '', $payload['order']['reference']);
    if (session_status() === PHP_SESSION_NONE) @session_start();
    $session_uri = isset($_SESSION['SEQURA_ORDER_URI_' . $enc_id]) ? $_SESSION['SEQURA_ORDER_URI_' . $enc_id] : '';
    if ($enc_id > 0 && sequra_safe_order_uri($session_uri)) {
        return array(
            'http_code'=>200, 'curl_error'=>'', 'request'=>'', 'response_raw'=>'', 'headers'=>array(), 'location'=>$session_uri,
            'response'=>array('checkout_url'=>SITE_HTTP_URL . 'external_api/pagamentos/pgm_sequra/sequra.php?' . http_build_query(array('id_enc'=>$enc_id, 'token'=>sequra_order_token($enc_id))), 'order_uri'=>$session_uri)
        );
    }
    $api_order = $payload;
    if (isset($api_order['order'])) unset($api_order['order']);
    $result = sequra_api_request('POST', sequra_endpoint_base() . '/orders', array('order'=>$api_order));
    $uri = sequra_get_order_uri($result);
    if ($uri !== '' && sequra_safe_order_uri($uri)) {
        $_SESSION['SEQURA_ORDER_URI_' . $enc_id] = $uri;
        $result['response'] = array('checkout_url'=>SITE_HTTP_URL . 'external_api/pagamentos/pgm_sequra/sequra.php?' . http_build_query(array('id_enc'=>$enc_id, 'token'=>sequra_order_token($enc_id))), 'order_uri'=>$uri);
    }
    return $result;
}

function sequra_get_order_uri($result)
{
    foreach (array(isset($result['location']) ? $result['location'] : '', isset($result['response']['uri']) ? $result['response']['uri'] : '', isset($result['response']['order_uri']) ? $result['response']['order_uri'] : '') as $uri) {
        if (sequra_safe_order_uri($uri)) return $uri;
    }
    return '';
}

function sequra_get_identification_form($order_uri)
{
    $config = sequra_config();
    return sequra_api_request('GET', $order_uri . '/form_v2?' . http_build_query(array('product'=>$config['product'], 'ajax'=>'false')), null, 'text/html');
}

function sequra_update_local_order($enc_id, $paid, $state, $provider_status)
{
    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs = new DataObj;
    $rs->db_open($db_link, $GLOBALS['DB_name'], "SELECT Encomenda_Paga FROM encomendas WHERE ID='" . (int) $enc_id . "'");
    if ($rs->EOF()) { $rs->db_close(); return array('ok'=>false, 'became_paid'=>false); }
    $was_paid = (int) $rs->db_data['Encomenda_Paga'] === 1;
    $rs->db_close();
    $sql = "UPDATE encomendas SET Encomenda_Paga='" . ($paid ? 1 : 0) . "', Estado='" . addslashes($state) . "', Encomenda_Status='" . addslashes($provider_status) . "' WHERE ID='" . (int) $enc_id . "'";
    $ok = Runquery($db_link, $GLOBALS['DB_name'], $sql);
    return array('ok'=>(bool) $ok, 'became_paid'=>(bool) ($ok && $paid && !$was_paid));
}
