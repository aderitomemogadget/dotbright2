<?php
header('Content-Type: text/html; charset=utf-8');
$path='../../core/';
include $path.'core_files.php';
include $path.'admin_globals.php';
include 'funcoes.inc.php';

global $admin_id;
$dados = getDadosAdministrador($admin_id);
if(!isset($dados['Permissoes'][35])) die("Sem Permissões");

if (empty($_SESSION['sms_scheduler_csrf'])) {
    try {
        $_SESSION['sms_scheduler_csrf'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['sms_scheduler_csrf'] = hash('sha256', uniqid('', true) . session_id());
    }
}

function smsSchedulerCsrfToken()
{
    return isset($_SESSION['sms_scheduler_csrf']) ? $_SESSION['sms_scheduler_csrf'] : '';
}

function smsSchedulerValidateCsrf()
{
    $submittedToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    $sessionToken = smsSchedulerCsrfToken();

    if ($submittedToken === '' || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        die('Pedido inválido ou sessão expirada.');
    }
}
