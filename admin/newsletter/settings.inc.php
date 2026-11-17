<?php 
	$path = '../../core/';
	include $path . 'core_files.php';
	include $path . 'admin_globals.php';
	include 'funcoes.inc.php';

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][16])) die("Sem Permiss&otilde;es");
		
	$arr_opt[1] = 'Texto Curto';
	$arr_opt[2] = 'Texto Longo';
	$arr_opt[3] = 'Imagem';
	$arr_opt[4] = 'Texto esq + imagem dir';
	$arr_opt[5] = 'Texto dir + imagem esq';
	$arr_opt[6] = '2 imagens';
	$arr_opt[7] = '2 blocos Texto';
	$arr_opt[8] = 'Problemas em visualizar.... clique aqui';
	$arr_opt[9] = 'Produtos';
	$arr_opt[10] = '3 imagens';



	$arr_fonts[1] = 'Arial';
	$arr_fonts[2] = 'Georgia';
	$arr_fonts[3] = 'Times New Roman';
	$arr_fonts[4] = 'Verdana';
	$arr_fonts[5] = 'Sans Serif';
	$arr_fonts[6] = 'Tahoma';
	$arr_fonts[7] = 'Impact';

?>
