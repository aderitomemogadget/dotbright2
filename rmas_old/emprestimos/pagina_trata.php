<?php
	include "settings.inc.php";
	// ini_set('display_errors',1);
	// ini_set('display_startup_errors',1);
	// error_reporting(-1);
	
	if(isset($_POST['slashes']))
	{		
		$result = submeteDados();
		$res = $result['resultado'];
		$id_acao = $result['id_acao'];

		$acao = $result['acao']=='insere' ? 1 : 2;
		
		// if(isset($_POST[TBID]) && $_POST[TBID] > 0 || $id_acao > 0) $retorno = 'pagina.php?'.TBID.'='.((isset($id_acao) && $id_acao > 0) ? $id_acao : $_POST[TBID]).'&tab='.preg_replace("/[^0-9]/","",$_POST['tab_a']);
		if(isset($_POST[TBID]) && $_POST[TBID] > 0 || $id_acao > 0) $retorno = 'pagina.php?ID='.((isset($id_acao) && $id_acao > 0) ? $id_acao : $_POST[TBID]);
		else $retorno = 'list.php';
	
		if($res)
		{ 
			show_box_OK($acao, $retorno);
		}
		else show_box_ERROR($acao, $retorno);
	}
	else show_box_ERROR(4,$retorno);
	