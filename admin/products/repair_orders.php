<?php
// Compatibility for links/bookmarks published before the dedicated Repairs module.
$id=is_scalar($_POST['id'] ?? null)?(int)$_POST['id']:(is_scalar($_GET['id'] ?? null)?(int)$_GET['id']:0);
$query=array();
foreach (array('q','status','p') as $key) if (isset($_GET[$key]) && is_scalar($_GET[$key])) $query[$key]=(string)$_GET[$key];
if ($id>0) $query=array('id'=>$id);
$target='../reparacoes/'.($id>0?'pagina.php':'list.php');
header('Location: '.$target.($query?'?'.http_build_query($query):''),true,($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST'?307:302);
exit;
