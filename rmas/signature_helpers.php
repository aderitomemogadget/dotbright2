<?php

function rma_signature_csrf_token()
{
    if (empty($_SESSION['rma_signature_csrf']) || !is_string($_SESSION['rma_signature_csrf'])) {
        $_SESSION['rma_signature_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['rma_signature_csrf'];
}

function rma_signature_validate_request()
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        return false;
    }

    $expected = rma_signature_csrf_token();
    $received = isset($_POST['csrf_token']) && is_string($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    return $received !== '' && hash_equals($expected, $received);
}

function rma_signature_error($message, $status = 400)
{
    http_response_code((int) $status);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="pt"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Assinatura RMA</title><body style="margin:0;background:#f4f6f9;font-family:Arial,sans-serif;color:#18202f">';
    echo '<main style="max-width:560px;margin:10vh auto;padding:30px;border:1px solid #e3e7ee;border-radius:12px;background:#fff;text-align:center">';
    echo '<h1 style="font-size:22px">Não foi possível guardar a assinatura</h1>';
    echo '<p>'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</p><p><a href="javascript:history.back()">Voltar</a></p>';
    echo '</main></body></html>';
    exit;
}

