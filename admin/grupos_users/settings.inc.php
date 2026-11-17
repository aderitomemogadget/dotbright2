<?php
	$path='../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';

	global $admin_id,$tem_area_reservada,$tem_loja,$tem_sms;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][2])) die("Sem Permiss&otilde;es");

	$modulos = array();
	$modulos[1] = 'Backoffice - Utilizadores';
	$modulos[2] = 'Backoffice - Grupos de utilizadores';
	if($dados['Grupo_ID']==1) $modulos[3] = 'Definições da plataforma';
	$modulos[4] = 'Mensagens internas';
	$modulos[5] = 'Estatísticas';
	if($tem_loja) $modulos[6] = 'Configurador de moeda';
	$modulos[7] = 'Línguas';
	if($tem_loja) $modulos[8] = 'Produtos';
	if($tem_loja) $modulos[9] = 'Encomendas';
	$modulos[10] = 'Páginas';
	$modulos[11] = 'Media';
	$modulos[12] = 'Menus';
	$modulos[13] = 'Forms';
	if($dados['Grupo_ID']==1) $modulos[14] = 'Templates website';
	$modulos[15] = 'Templates email';
	$modulos[16] = 'Newsletter';
	if($tem_chat)$modulos[17] = 'Chat';
	if($tem_area_reservada) $modulos[18] = 'Clientes';
	if($tem_area_reservada) $modulos[19] = 'Grupos de clientes';
	if($tem_loja) $modulos[20] = 'Filtros';
	if($tem_loja) $modulos[21] = 'Países';
	if($tem_loja) $modulos[22] = 'Metodos de Pagamento';
	if($tem_loja) $modulos[23] = 'Metodos de Envio';
	if($tem_loja) $modulos[24] = 'Portes';
	if($tem_loja) $modulos[25] = 'Margens';
	// $modulos[26] = '';
	if($tem_promocoes) $modulos[27] = 'Promoções';
	if($module_voucher) $modulos[28] = 'Vouchers';
	if($tem_modulo_pontos) $modulos[29] = 'Pontos';
	$modulos[30] = 'Editar Online';
	if($tem_sms) $modulos[35] = 'SMS API';
	$modulos[31] = 'Componentes';

	if($GLOBALS['tem_timeline'] == 1)$modulos[32] = 'Time line';