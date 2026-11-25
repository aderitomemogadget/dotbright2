<?php
	include "settings.inc.php";
	$retorno="pagina.php";
	if(isset($_POST['slashes']))
	{
		$result = gravaFicheiroSettings();
		if($result)show_box_OK(1,$retorno);
		else show_box_ERROR(1,$retorno);
	}
	else show_box_ERROR(4,$retorno);
?>