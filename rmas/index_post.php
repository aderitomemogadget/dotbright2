<?php
$path = '../core/';
include $path . 'core_files.php';
require_once __DIR__ . '/../admin/auth.php';
adminSecurityHeaders();
adminSecurityInstall();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !adminSecurityCheckCsrf()) {
    header('Location: index.php?error=1');
    exit;
}

$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
$ipScope = adminSecurityScope('ip', adminSecurityIp());
$userScope = adminSecurityScope('user', $login);

if ($login === '' || $password === '' || adminSecurityLocked($ipScope) || adminSecurityLocked($userScope)) {
    usleep(500000);
    header('Location: index.php?error=1');
    exit;
}

$db = adminSecurityDb();
$rs = new DataObj();
$safeLogin = addslashes($login);
$rs->db_open($db, $GLOBALS['DB_name'], "SELECT * FROM logins WHERE username='$safeLogin' LIMIT 1");

$valid = !$rs->EOF()
    && (int) $rs->db_data['estado'] === 1
    && adminSecurityVerifyPassword($password, $rs->db_data['password']);

if (!$valid) {
    adminSecurityFailure($ipScope);
    adminSecurityFailure($userScope);
    if (!$rs->EOF()) {
        Runquery($db, $GLOBALS['DB_name'], 'UPDATE logins SET failed_attempts=failed_attempts+1 WHERE ID=' . (int) $rs->db_data['ID']);
    }
    $rs->db_close();
    db_closedb_conection($db);
    usleep(500000);
    header('Location: index.php?error=1');
    exit;
}

$loginData = $rs->db_data;
$loginId = (int) $loginData['ID'];
$groupId = (int) $loginData['Grupo_ID'];
$newHash = $loginData['password'];

if (!preg_match('/^\\$(2y|argon2i|argon2id)\\$/', $newHash) || password_needs_rehash($newHash, PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $safeHash = addslashes($newHash);
    Runquery($db, $GLOBALS['DB_name'], "UPDATE logins SET password='$safeHash',failed_attempts=0 WHERE ID=$loginId");
    $loginData['password'] = $newHash;
} else {
    Runquery($db, $GLOBALS['DB_name'], "UPDATE logins SET failed_attempts=0 WHERE ID=$loginId");
}

$store = '';
$groupRs = new DataObj();
$groupRs->db_open($db, $GLOBALS['DB_name'], "SELECT Loja FROM grupos_logins WHERE ID=$groupId LIMIT 1");
if (!$groupRs->EOF()) {
    $store = $groupRs->db_data['Loja'];
}
$groupRs->db_close();
$rs->db_close();
db_closedb_conection($db);

adminSecurityClear($ipScope);
adminSecurityClear($userScope);
adminSecurityStartSession($loginData);
$_SESSION['ss_loja'] = $store;

header('Location: index.php');
exit;
