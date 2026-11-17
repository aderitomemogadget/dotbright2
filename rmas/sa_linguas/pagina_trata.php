<?php
	include 'settings.inc.php';	
	if(isset($_POST['slashes']))
	{		
		$result = submeteDados();
		$res = $result['resultado'];
		$id_acao = $result['id_acao'];

		$acao = $result['acao']=='insere' ? 1 : 2;
		
		if(isset($_POST[TBID]) && $_POST[TBID] > 0 || $id_acao > 0) $retorno = 'pagina.php?'.TBID.'='.((isset($id_acao) && $id_acao > 0) ? $id_acao : $_POST[TBID]).'&tab=';
		else $retorno = 'list.php';
	
		if($res) show_box_OK($acao, $retorno);
		else show_box_ERROR($acao, $retorno);
		
		
		
		$path = dirname( __FILE__ ) . '/gerar_ficheiros.php';
		$exe_file = "php ".$path." > /dev/null &";
		exec($exe_file);
	
	}
	else show_box_ERROR(4,$retorno);
?>