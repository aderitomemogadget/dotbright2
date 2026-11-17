<?php
$path = '../core/';
include $path . 'core_files.php';
require_once __DIR__ . '/auth.php';
adminSecurityHeaders();
adminSecurityInstall();

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!adminSecurityCheckCsrf()) {
        http_response_code(403);
        exit('Pedido inválido.');
    }
    $identity = isset($_POST['identity']) ? trim($_POST['identity']) : '';
    $scope = adminSecurityScope('recover-ip', adminSecurityIp());
    if (!adminSecurityLocked($scope)) {
        adminSecurityFailure($scope);
        $db = adminSecurityDb();
        $rs = new DataObj();
        $safe = addslashes($identity);
        $rs->db_open($db, $GLOBALS['DB_name'], "SELECT ID,username,email FROM logins WHERE estado=1 AND (username='$safe' OR email='$safe') LIMIT 1");
        if (!$rs->EOF() && filter_var($rs->db_data['email'], FILTER_VALIDATE_EMAIL)) {
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $id = (int) $rs->db_data['ID'];
            $expires = date('Y-m-d H:i:s', time() + 1800);
            $ip = addslashes(adminSecurityIp());
            Runquery($db, $GLOBALS['DB_name'], "REPLACE INTO admin_password_resets (login_id,token_hash,expires_at,requested_ip,created_at) VALUES ($id,'$tokenHash','$expires','$ip',NOW())");
            $url = $GLOBALS['site_http_url'] . 'admin/reset_password.php?token=' . urlencode($rawToken);
            $body = '<p>Foi pedida a recuperação da password de administração DotBright.</p><p><a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">Definir nova password</a></p><p>Este link é válido durante 30 minutos. Se não fizeste este pedido, ignora esta mensagem.</p>';
            send_email($rs->db_data['email'], 'Recuperação de password - DotBright', $body);
        }
        $rs->db_close();
        db_closedb_conection($db);
    }
    $sent = true;
}
?><!doctype html><html lang="pt"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Recuperar password</title><link rel="stylesheet" href="auth.css?v=1"></head>
<body class="auth-body"><main class="auth-shell"><section class="auth-visual"><div class="brand"><div class="brand-mark">DB</div><h1>Recuperação segura.</h1><p>Recebe um link temporário para voltares ao centro de operações.</p></div><div class="repair-strip"><span>Link único</span><span>30 minutos</span></div></section><section class="auth-panel"><h2>Recuperar password</h2><p class="auth-subtitle">Indica o utilizador ou email associado à conta.</p>
<?php if ($sent) { ?><div class="notice success">Se os dados estiverem associados a uma conta ativa, receberás um email com as instruções.</div><?php } ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminSecurityCsrf(), ENT_QUOTES, 'UTF-8') ?>"><div class="field"><label for="identity">Utilizador ou email</label><input id="identity" name="identity" maxlength="190" required></div><button class="auth-button" type="submit">Enviar instruções</button></form><div class="auth-links"><a href="index.php">Voltar ao login</a></div></section></main></body></html>
