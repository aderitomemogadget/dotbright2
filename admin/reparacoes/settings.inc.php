<?php
if (!defined('REPAIR_ADMIN_SCREEN')) { http_response_code(403); exit; }
$path='../../core/';
require_once __DIR__.'/../../core/core_files.php';
require_once __DIR__.'/../../core/admin_globals.php';
$page_titulo='Reparações';
$dados=getDadosAdministrador($admin_id ?? 0);
