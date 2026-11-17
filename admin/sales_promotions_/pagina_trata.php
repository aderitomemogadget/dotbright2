<?php
	include 'settings.inc.php';
	if(isset($_POST['slashes']))
	{
		$result = submeteDados();
		$res = $result['resultado'];
		$id_acao = $result['id_acao'];
		$acao = $result['acao']=='insere' ? 1 : 2;
		if($res)
		{
			submeteProdutos($id_acao);
		}

		if(isset($_POST[TBID]) && $_POST[TBID]) $retorno = 'pagina.php?'.TBID.'='.$_POST[TBID].'&tab=';
		else $retorno = 'list.php';

		if($res) show_box_OK($acao, $retorno);
		else show_box_ERROR($acao, $retorno);
	}
	else show_box_ERROR(4,$retorno);
?>