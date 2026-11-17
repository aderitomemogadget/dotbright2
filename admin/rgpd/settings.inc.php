<?php
header('Content-Type: text/html; charset=utf-8');
$path='../../core/';
include $path.'core_files.php';
include $path.'admin_globals.php';
include 'funcoes.inc.php';

global $admin_id;
$dados = getDadosAdministrador($admin_id);
if(!isset($dados['Permissoes'][35])) die("Sem Permissões");
