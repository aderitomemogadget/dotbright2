<?php
$path = '../core/';
include $path . 'core_files.php';
require_once __DIR__ . '/auth.php';
adminSecurityHeaders();
adminSecurityInstall();

$token = isset($_GET['token']) ? (string) $_GET['token'] : (isset($_POST['token']) ? (string) $_POST['token'] : '');
$tokenHash = hash('sha256', $token);
$db = adminSecurityDb();
$rs = new DataObj();
$safeHash = addslashes($tokenHash);
$rs->db_open($db, $GLOBALS['DB_name'], "SELECT login_id FROM admin_password_resets WHERE token_hash='$safeHash' AND expires_at>NOW() LIMIT 1");
$validToken = !$rs->EOF();
$loginId = $validToken ? (int) $rs->db_data['login_id'] : 0;
$rs->db_close();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    if (!adminSecurityCheckCsrf()) {
        $error = 'Pedido inválido ou sessão expirada.';
    } else {
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $repeat = isset($_POST['password_repeat']) ? (string) $_POST['password_repeat'] : '';
        if ($password !== $repeat || !adminSecurityPasswordValid($password)) {
            $error = 'A password não cumpre os requisitos ou não coincide.';
        } else {
            $hash = addslashes(password_hash($password, PASSWORD_DEFAULT));
            Runquery($db, $GLOBALS['DB_name'], "UPDATE logins SET password='$hash',failed_attempts=0 WHERE ID=$loginId");
            Runquery($db, $GLOBALS['DB_name'], "DELETE FROM admin_password_resets WHERE login_id=$loginId");
            db_closedb_conection($db);
            session_regenerate_id(true);
            header('Location: index.php?reset=1');
            exit;
        }
    }
}
db_closedb_conection($db);
?><!doctype html><html lang="pt"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Nova password</title><link rel="stylesheet" href="auth.css?v=1"></head><body class="auth-body"><main class="auth-shell"><section class="auth-visual"><div class="brand"><div class="brand-mark">DB</div><h1>Nova chave de acesso.</h1><p>Escolhe uma password exclusiva e difícil de prever.</p></div><div class="repair-strip"><span>12+ caracteres</span><span>Password cifrada</span></div></section><section class="auth-panel"><h2>Definir nova password</h2>
<?php if (!$validToken) { ?><div class="notice">O link é inválido ou já expirou.</div><div class="auth-links"><a href="recover_password.php">Pedir novo link</a></div><?php } else { ?>
<p class="auth-subtitle">Usa pelo menos 12 caracteres, maiúscula, minúscula, número e símbolo.</p><?php if ($error) { ?><div class="notice"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php } ?><form method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminSecurityCsrf(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><div class="field"><label for="password">Nova password</label><input type="password" id="password" name="password" minlength="12" maxlength="255" autocomplete="new-password" required><div class="password-help">12+ caracteres, maiúscula, minúscula, número e símbolo.</div></div><div class="field"><label for="password_repeat">Repetir password</label><input type="password" id="password_repeat" name="password_repeat" minlength="12" maxlength="255" autocomplete="new-password" required></div><button class="auth-button" type="submit">Guardar nova password</button></form><?php } ?></section></main></body></html>
