<?php
$path = dirname(__FILE__) . '/../../core/';
include $path . "core_files.php";

function smsApiIsAuthorized()
{
    $configuredToken = getenv('SMS_API_TOKEN');
    if (!$configuredToken && defined('SMS_API_TOKEN')) {
        $configuredToken = SMS_API_TOKEN;
    }

    if (!$configuredToken) {
        http_response_code(503);
        return false;
    }

    $providedToken = '';
    if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/^Bearer\s+(.+)$/i', trim($_SERVER['HTTP_AUTHORIZATION']), $matches)) {
        $providedToken = trim($matches[1]);
    } elseif (isset($_SERVER['HTTP_X_SMS_TOKEN'])) {
        $providedToken = trim($_SERVER['HTTP_X_SMS_TOKEN']);
    }

    return $providedToken !== '' && hash_equals((string) $configuredToken, (string) $providedToken);
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!smsApiIsAuthorized()) {
    if (http_response_code() !== 503) {
        http_response_code(401);
    }
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

$dbLink = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

$json = array();
$rs   = new DataObj();
$sql  = "SELECT ss.Texto, scu.Telemovel, ss.ID
         FROM sms_scheduler AS ss
         INNER JOIN sms_scheduler_users AS scu ON ss.ID=scu.ID_ORG
         WHERE scu.Enviado=0
           AND ss.Data_Envio <= NOW()
           AND ss.Ativo=1
           AND scu.Telemovel <> ''";
$rs->db_open($dbLink, $GLOBALS['DB_name'], $sql);

while (!$rs->EOF()) {
    $json[] = array(
        'id_request' => (int) $rs->db_data['ID'],
        'msg'        => $rs->db_data['Texto'],
        'tel'        => $rs->db_data['Telemovel']
    );
    $rs->db_next();
}
$rs->db_close();
db_closedb_conection($dbLink);

echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
