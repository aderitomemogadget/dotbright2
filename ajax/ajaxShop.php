<?php
	$path='../core/';
	include $path.'core_files.php';
	if(isset($_GET['tipo']) && $_GET['tipo']==0){carrinho_small2();}
	else if(isset($_GET['tipo']) && $_GET['tipo']==1){carrinho_small_visualiza();}
	else if(isset($_GET['tipo']) && $_GET['tipo']==3){carrinho_small3('qtd'); echo '[spl]'; carrinho_small3('valor');echo '[spl]';carrinho_small_visualiza(1);}
?>