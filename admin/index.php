<?php
$path = '../core/';
include $path . 'core_files.php';
require_once __DIR__ . '/auth.php';
adminSecurityInstall();

global $admin_id, $ss_token;
if ($admin_id) {
    include $path . 'admin_globals.php';
}

if (!$admin_id && !$ss_token) {
    adminSecurityHeaders();
    $error = isset($_GET['error']);
    $expired = isset($_GET['expired']);
    $reset = isset($_GET['reset']);
    ?><!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>DotBright | Administração</title>
    <link rel="stylesheet" href="auth.css?v=1">
    <link rel="stylesheet" href="auth-polish.css?v=20261102">
</head>
<body class="auth-body">
<main class="auth-shell">
    <section class="auth-visual">
        <div class="brand">
            <div class="brand-mark">DB</div>
            <h1>Tecnologia pronta para voltar à vida.</h1>
            <p>Gestão central de telemóveis, reparações, clientes e operações DotBright.</p>
        </div>
        <div class="repair-strip">
            <span>Diagnóstico</span><span>Reparações</span><span>Equipamentos</span><span>Clientes</span>
        </div>
    </section>
    <section class="auth-panel">
        <h2>Bem-vindo</h2>
        <p class="auth-subtitle">Inicia sessão para aceder ao centro de operações.</p>
        <?php if ($error || $expired) { ?><div class="notice">Não foi possível iniciar sessão. Confirma os dados ou aguarda alguns minutos antes de tentar novamente.</div><?php } ?>
        <?php if ($reset) { ?><div class="notice success">Password atualizada. Já podes iniciar sessão.</div><?php } ?>
        <form method="post" action="index_post.php" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(adminSecurityCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="field">
                <label for="login">Utilizador</label>
                <input type="text" id="login" name="login" maxlength="190" autocomplete="username" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" maxlength="255" autocomplete="current-password" required>
            </div>
            <button class="auth-button" type="submit">Entrar em segurança</button>
        </form>
        <div class="auth-links"><a href="../">Voltar ao site</a><a href="recover_password.php">Recuperar password</a></div>
        <p class="security-note">Acesso protegido por controlo de tentativas, bloqueio progressivo e sessão segura.</p>
    </section>
</main>
</body>
</html><?php
    exit;
}

include 'layout/header.inc.php';
include 'layout/body.inc.php';
?>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr><td valign="top" align="left"><?php include 'tasks.php'; ?></td></tr>
</table>
<?php include 'layout/bottom.inc.php'; ?>
