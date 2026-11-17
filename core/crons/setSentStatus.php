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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(array('error' => 'Method not allowed'));
    exit;
}

if (!smsApiIsAuthorized()) {
    if (http_response_code() !== 503) {
        http_response_code(401);
    }
    echo json_encode(array('error' => 'Unauthorized'));
    exit;
}

$idRequest = isset($_POST['idRequest']) ? (int) $_POST['idRequest'] : 0;
$number = isset($_POST['number']) ? preg_replace('/[^0-9+]/', '', trim($_POST['number'])) : '';

if ($idRequest <= 0 || $number === '' || strlen($number) > 20) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

$dbLink = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$rs = new DataObj();
$safeNumber = addslashes($number);

$sql = "SELECT scu.ID
        FROM sms_scheduler AS ss
        INNER JOIN sms_scheduler_users AS scu ON ss.ID=scu.ID_ORG
        WHERE scu.Telemovel='$safeNumber'
          AND ss.ID=$idRequest
        LIMIT 1";
$rs->db_open($dbLink, $GLOBALS['DB_name'], $sql);

if ($rs->EOF()) {
    $rs->db_close();
    db_closedb_conection($dbLink);
    http_response_code(404);
    echo json_encode(array('error' => 'SMS not found'));
    exit;
}

$smsUserId = (int) $rs->db_data['ID'];
$rs->db_close();

$sql = "UPDATE sms_scheduler_users
        SET Enviado=1
        WHERE ID=$smsUserId
          AND ID_ORG=$idRequest
        LIMIT 1";
$result = Runquery($dbLink, $GLOBALS['DB_name'], $sql);
db_closedb_conection($dbLink);

if (!$result) {
    http_response_code(500);
    echo json_encode(array('error' => 'Unable to update status'));
    exit;
}

echo json_encode(array('success' => true));
