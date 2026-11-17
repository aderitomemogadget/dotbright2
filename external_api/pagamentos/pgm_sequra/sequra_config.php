<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] === __FILE__) exit;

$sequra_secret_files = array();
$sequra_custom_secret_file = getenv('SEQURA_SECRETS_FILE');
if ($sequra_custom_secret_file !== false && $sequra_custom_secret_file !== '') $sequra_secret_files[] = $sequra_custom_secret_file;
$sequra_secret_files[] = dirname(__DIR__, 4) . '/private/sequra_secrets.php';
$sequra_secret_files[] = __DIR__ . '/sequra_secrets.php'; // compatibilidade com instalacoes antigas
foreach ($sequra_secret_files as $sequra_secret_file) {
    if (is_file($sequra_secret_file)) {
        require_once $sequra_secret_file;
        break;
    }
}

function sequra_conf_value($global_key, $env_key, $default = '')
{
    if (isset($GLOBALS[$global_key]) && $GLOBALS[$global_key] !== '') return $GLOBALS[$global_key];
    if (defined($global_key) && constant($global_key) !== '') return constant($global_key);
    $value = getenv($env_key);
    return ($value !== false && $value !== '') ? $value : $default;
}

function sequra_config()
{
    return array(
        'enabled' => (int) sequra_conf_value('SEQURA_ENABLED', 'SEQURA_ENABLED', '0'),
        'sandbox' => (int) sequra_conf_value('SEQURA_SANDBOX', 'SEQURA_SANDBOX', '1'),
        'merchant_id' => trim(sequra_conf_value('SEQURA_MERCHANT_ID', 'SEQURA_MERCHANT_ID', '')),
        'username' => trim(sequra_conf_value('SEQURA_USERNAME', 'SEQURA_USERNAME', '')),
        'password' => (string) sequra_conf_value('SEQURA_PASSWORD', 'SEQURA_PASSWORD', ''),
        'endpoint' => rtrim(trim(sequra_conf_value('SEQURA_ENDPOINT', 'SEQURA_ENDPOINT', '')), '/'),
        'assets_key' => (string) sequra_conf_value('SEQURA_ASSETS_KEY', 'SEQURA_ASSETS_KEY', ''),
        'product' => trim(sequra_conf_value('SEQURA_PRODUCT', 'SEQURA_PRODUCT', 'pp3')),
        'culture' => trim(sequra_conf_value('SEQURA_CULTURE', 'SEQURA_CULTURE', 'pt-PT')),
        'order_prefix' => trim(sequra_conf_value('SEQURA_ORDER_PREFIX', 'SEQURA_ORDER_PREFIX', 'DOTBRIGHT')),
        'token_secret' => (string) sequra_conf_value('SEQURA_TOKEN_SECRET', 'SEQURA_TOKEN_SECRET', ''),
        'signature_secret' => (string) sequra_conf_value('SEQURA_SIGNATURE_SECRET', 'SEQURA_SIGNATURE_SECRET', ''),
        'paid_state' => (string) sequra_conf_value('SEQURA_PAID_STATE', 'SEQURA_PAID_STATE', '13'),
        'pending_state' => (string) sequra_conf_value('SEQURA_PENDING_STATE', 'SEQURA_PENDING_STATE', '5'),
        'failed_state' => (string) sequra_conf_value('SEQURA_FAILED_STATE', 'SEQURA_FAILED_STATE', '14'),
        'cancelled_state' => (string) sequra_conf_value('SEQURA_CANCELLED_STATE', 'SEQURA_CANCELLED_STATE', '15'),
        'currency' => 'EUR'
    );
}

function sequra_config_errors($require_enabled = true)
{
    $config = sequra_config();
    $errors = array();
    if ($require_enabled && !$config['enabled']) $errors[] = 'disabled';
    foreach (array('merchant_id', 'username', 'password', 'endpoint', 'assets_key', 'token_secret') as $key) {
        if ($config[$key] === '') $errors[] = 'missing_' . $key;
    }
    if ($config['endpoint'] !== '' && stripos($config['endpoint'], 'https://') !== 0) $errors[] = 'invalid_endpoint';
    return $errors;
}

function sequra_is_ready()
{
    return count(sequra_config_errors(true)) === 0;
}

function sequra_sandbox_access_allowed()
{
    $config = sequra_config();
    if (!$config['sandbox']) return true;

    $client_id = isset($GLOBALS['cli_id']) ? (int) $GLOBALS['cli_id'] : 0;
    if ($client_id <= 0 && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['ss_cli_id'])) {
        $client_id = (int) $_SESSION['ss_cli_id'];
    }
    if ($client_id <= 0 || !function_exists('cliente_perfil')) return false;

    $profile = cliente_perfil($client_id);
    return is_array($profile) && isset($profile['Testes']) && (int) $profile['Testes'] === 1;
}
