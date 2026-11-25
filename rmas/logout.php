<?php
$path = '../core/';
include $path . 'core_files.php';

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
delete_cookie();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Location: index.php');
exit;
