<?php
// The Windows exporters check this endpoint before reading Sage through ODBC.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
ini_set('display_errors', '0');

if (!isset($_GET['code']) || !is_string($_GET['code']) || !hash_equals('memogadget', $_GET['code'])) {
    http_response_code(403);
    echo json_encode(array('error' => 'Forbidden'));
    exit;
}

try {
    $path = __DIR__.'/../../core/';
    require_once $path.'settings.php';
    require_once $path.'db_engine_pdo.php';

    $db = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $lastSync = $db->query('SELECT MAX(Data) FROM sync_history WHERE Estado=1')->fetchColumn();

    // ODBC credentials belong to the local exporter, never to a public response.
    $config = array(
        'SYS_SERVER' => 'SageSQL',
        'SYS_DATABASE' => '',
        'LAST_SYNC' => $lastSync ?: null,
        'PRODUCT' => array('ItemID', 'AverageCostPrice', 'AvailableQty', 'Description',
            'FamilyID', 'FDescription', 'Level', 'ParentDescription', 'ParentID',
            'UnitPrice', 'TaxIncludedPrice'),
    );
    echo json_encode($config, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    error_log('Sage configuration endpoint failed: '.$error->getMessage());
    http_response_code(503);
    echo json_encode(array('error' => 'Configuration temporarily unavailable'));
}
