<?php
$path = dirname( __FILE__ ) . '/../../../core/';
require_once $path . 'core_files.php';
	
if(isset($_GET['remove2']) && $_GET['remove2'] != '' && file_exists('../../../files/importar/atualizar_web.txt'))
{
	@unlink('../../../files/importar/atualizar_web.txt');
}