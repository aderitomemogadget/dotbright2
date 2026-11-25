<?php
$path = '../core/';
include $path . 'core_files.php';
require_once __DIR__ . '/../admin/auth.php';

global $admin_id;
if (!$admin_id) {
    header('Location: index.php?expired=1');
    exit;
}
include $path . 'admin_globals.php';

adminSecurityHeaders();

function smsTestRedact($value)
{
    $text = is_string($value) ? $value : print_r($value, true);
    $patterns = array(
        '/(Authorization\s*:\s*Bearer\s+)[^\s"\']+/i',
        '/("(?:access_token|token|password|secret|client_secret|api_key)"\s*:\s*")[^"]+(")/i',
        '/((?:access_token|token|password|secret|client_secret|api_key)\s*[=:]\s*)[^\s&]+/i'
    );
    return preg_replace($patterns, '$1[OCULTO]$2', $text);
}

$sent = false;
$error = '';
$diagnostics = array();
$recipient = '914931936';
$message = 'Teste SMS DotBright - sistema de RMA operacional.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!adminSecurityCheckCsrf()) {
        $error = 'Pedido inválido. Atualiza a página e tenta novamente.';
    } elseif (!isset($_POST['confirm']) || $_POST['confirm'] !== '1') {
        $error = 'É necessário confirmar o envio.';
    } else {
        $startedAt = microtime(true);
        $library = '/home/dotbright/private/lib/nos-sms-secure/funcoes_nos_sms.inc.php';
        $diagnostics[] = 'Data/hora: ' . date('Y-m-d H:i:s');
        $diagnostics[] = 'PHP: ' . PHP_VERSION;
        $diagnostics[] = 'Biblioteca privada existe: ' . (is_file($library) ? 'sim' : 'não');
        $diagnostics[] = 'Biblioteca privada legível: ' . (is_readable($library) ? 'sim' : 'não');
        $diagnostics[] = 'Extensão cURL: ' . (extension_loaded('curl') ? 'carregada' : 'em falta');
        $diagnostics[] = 'Extensão OpenSSL: ' . (extension_loaded('openssl') ? 'carregada' : 'em falta');
        $diagnostics[] = 'Extensão SOAP: ' . (extension_loaded('soap') ? 'carregada' : 'em falta');

        set_error_handler(function ($severity, $text, $file, $line) use (&$diagnostics) {
            $diagnostics[] = 'PHP ' . $severity . ': ' . $text . ' em ' . basename($file) . ':' . $line;
            return true;
        });

        ob_start();
        try {
            require_once $library;
            $diagnostics[] = 'Função send_sms_nos disponível: ' . (function_exists('send_sms_nos') ? 'sim' : 'não');

            if (!function_exists('send_sms_nos')) {
                throw new RuntimeException('A biblioteca não definiu a função send_sms_nos().');
            }

            $diagnostics[] = 'A iniciar pedido detalhado para o destino fixo ' . $recipient;
            if (function_exists('send_sms_nos_detalhado')) {
                $result = send_sms_nos_detalhado($recipient, '', $message);
                $diagnostics[] = 'Resultado detalhado: ' . smsTestRedact(var_export($result, true));
                $sent = !empty($result['success']);
                if (!$sent) {
                    $error = 'A NOS recusou ou não conseguiu processar o pedido.';
                }
            } else {
                $result = send_sms_nos($recipient, '', $message);
                $diagnostics[] = 'Tipo do retorno: ' . gettype($result);
                $diagnostics[] = 'Retorno: ' . smsTestRedact(var_export($result, true));
                $sent = ((int) $result === 1);
                if (!$sent) {
                    $error = 'A função de envio devolveu falha.';
                }
            }
        } catch (Throwable $exception) {
            $diagnostics[] = 'EXCEÇÃO ' . get_class($exception) . ': ' . $exception->getMessage();
            $diagnostics[] = 'Origem: ' . basename($exception->getFile()) . ':' . $exception->getLine();
            error_log('DotBright SMS test failed: ' . $exception->getMessage());
            $error = 'O processo de envio terminou com erro.';
        }

        $capturedOutput = ob_get_clean();
        restore_error_handler();
        if ($capturedOutput !== '') {
            $diagnostics[] = "Output produzido pela biblioteca:\n" . smsTestRedact($capturedOutput);
        } else {
            $diagnostics[] = 'Output produzido pela biblioteca: nenhum';
        }

        $lastError = error_get_last();
        if ($lastError) {
            $diagnostics[] = 'Último erro PHP: ' . $lastError['message'] . ' em ' . basename($lastError['file']) . ':' . $lastError['line'];
        }
        $diagnostics[] = 'Duração: ' . number_format(microtime(true) - $startedAt, 3, '.', '') . ' segundos';
    }
}
?><!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Diagnóstico temporário de SMS | DotBright</title>
    <link rel="stylesheet" href="../admin/auth.css?v=2">
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-visual">
        <div class="brand">
            <div class="brand-mark">SMS</div>
            <h1>Diagnóstico temporário</h1>
            <p>Captura o retorno, output, avisos e exceções do fornecedor, ocultando credenciais.</p>
        </div>
    </section>
    <section class="auth-panel">
        <h2>Enviar SMS de teste</h2>
        <p class="auth-subtitle">Destino fixo: <?= htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8') ?></p>

        <?php if ($sent) { ?>
            <div class="notice success">A função terminou sem lançar uma exceção. Isto não garante que o fornecedor tenha aceite ou entregue o SMS.</div>
        <?php } ?>
        <?php if ($error !== '') { ?><div class="notice"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php } ?>

        <?php if ($diagnostics) { ?>
            <h3>Diagnóstico capturado</h3>
            <pre><?= htmlspecialchars(smsTestRedact(implode("\n", $diagnostics)), ENT_QUOTES, 'UTF-8') ?></pre>
            <p class="security-note">Copia este diagnóstico e envia-mo. As credenciais conhecidas são ocultadas automaticamente.</p>
        <?php } else { ?>
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
            <form method="post" action="test_sms.php" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminSecurityCsrf(), ENT_QUOTES, 'UTF-8') ?>">
                <label>
                    <input type="checkbox" name="confirm" value="1" required>
                    Confirmo um novo envio para <?= htmlspecialchars($recipient, ENT_QUOTES, 'UTF-8') ?>.
                </label>
                <button class="auth-button" type="submit">Executar teste e mostrar diagnóstico</button>
            </form>
        <?php } ?>

        <div class="auth-links"><a href="test_sms.php">Novo teste</a><a href="index.php">Voltar aos RMAs</a></div>
    </section>
</main>
</body>
</html>
