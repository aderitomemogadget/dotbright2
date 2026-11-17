<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit;
}

function adminSecurityHeaders()
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
}

function adminSecurityDb()
{
    return db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
}

function adminSecurityInstall()
{
    $db = adminSecurityDb();
    Runquery($db, $GLOBALS['DB_name'], "CREATE TABLE IF NOT EXISTS admin_security_attempts (
        scope_key CHAR(64) NOT NULL,
        attempts INT UNSIGNED NOT NULL DEFAULT 0,
        first_attempt DATETIME NULL,
        last_attempt DATETIME NULL,
        lock_until DATETIME NULL,
        PRIMARY KEY (scope_key),
        KEY lock_until (lock_until)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    Runquery($db, $GLOBALS['DB_name'], "CREATE TABLE IF NOT EXISTS admin_password_resets (
        login_id INT UNSIGNED NOT NULL,
        token_hash CHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        requested_ip VARCHAR(45) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        PRIMARY KEY (login_id),
        UNIQUE KEY token_hash (token_hash),
        KEY expires_at (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    db_closedb_conection($db);
}

function adminSecurityCsrf()
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function adminSecurityCheckCsrf()
{
    $token = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    return $token !== '' && isset($_SESSION['admin_csrf']) && hash_equals($_SESSION['admin_csrf'], $token);
}

function adminSecurityIp()
{
    return isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : '';
}

function adminSecurityScope($type, $value)
{
    return hash('sha256', $type . '|' . strtolower(trim((string) $value)));
}

function adminSecurityLocked($scope)
{
    $db = adminSecurityDb();
    $rs = new DataObj();
    $safe = addslashes($scope);
    $rs->db_open($db, $GLOBALS['DB_name'], "SELECT lock_until FROM admin_security_attempts WHERE scope_key='$safe' LIMIT 1");
    $locked = false;
    if (!$rs->EOF() && !empty($rs->db_data['lock_until'])) {
        $locked = strtotime($rs->db_data['lock_until']) > time();
    }
    $rs->db_close();
    db_closedb_conection($db);
    return $locked;
}

function adminSecurityFailure($scope)
{
    $db = adminSecurityDb();
    $rs = new DataObj();
    $safe = addslashes($scope);
    $rs->db_open($db, $GLOBALS['DB_name'], "SELECT attempts,last_attempt FROM admin_security_attempts WHERE scope_key='$safe' LIMIT 1");
    $attempts = 1;
    if (!$rs->EOF()) {
        $last = strtotime($rs->db_data['last_attempt']);
        $attempts = ($last && $last >= time() - 900) ? ((int) $rs->db_data['attempts'] + 1) : 1;
    }
    $rs->db_close();

    $lockUntil = 'NULL';
    if ($attempts >= 5) {
        $minutes = min(1440, 15 * (int) pow(2, min(6, $attempts - 5)));
        $lockUntil = "'" . date('Y-m-d H:i:s', time() + ($minutes * 60)) . "'";
    }
    $now = date('Y-m-d H:i:s');
    Runquery($db, $GLOBALS['DB_name'], "INSERT INTO admin_security_attempts
        (scope_key,attempts,first_attempt,last_attempt,lock_until)
        VALUES ('$safe',$attempts,'$now','$now',$lockUntil)
        ON DUPLICATE KEY UPDATE attempts=$attempts,last_attempt='$now',lock_until=$lockUntil,
        first_attempt=IF($attempts=1,'$now',first_attempt)");
    db_closedb_conection($db);
}

function adminSecurityClear($scope)
{
    $db = adminSecurityDb();
    $safe = addslashes($scope);
    Runquery($db, $GLOBALS['DB_name'], "DELETE FROM admin_security_attempts WHERE scope_key='$safe'");
    db_closedb_conection($db);
}

function adminSecurityVerifyPassword($plain, $stored)
{
    if (preg_match('/^\$(2y|argon2i|argon2id)\$/', $stored)) {
        return password_verify($plain, $stored);
    }
    return hash_equals((string) $stored, (string) encripta($plain));
}

function adminSecurityPasswordValid($password)
{
    return strlen($password) >= 12
        && preg_match('/[a-z]/', $password)
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[^a-zA-Z0-9]/', $password);
}

function adminSecurityStartSession($login)
{
    session_regenerate_id(true);
    $_SESSION['ss_admin'] = 1;
    $_SESSION['ss_admin_id'] = (int) $login['ID'];
    $_SESSION['ss_admin_user'] = $login['username'];
    $_SESSION['ss_admin_passwd'] = $login['password'];
    $_SESSION['ss_admin_perm'] = $login['Grupo_ID'];
    $_SESSION['admin_fingerprint'] = hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    $_SESSION['admin_last_activity'] = time();
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}
