<?php
	$path='../core/';
	include $path.'core_files.php';
	if(isset($_GET['tipo']) && $_GET['tipo']==0){carrinho_small2();}
	else if(isset($_GET['tipo']) && $_GET['tipo']==1){carrinho_small_visualiza_reparacoes();}
	else if(isset($_GET['tipo']) && $_GET['tipo']==3){carrinho_small3_reparacoes('qtd'); echo '[spl]'; carrinho_small3_reparacoes('valor'); echo '[spl]'; carrinho_small3_reparacoes('qtd2');echo '[spl]';carrinho_small_visualiza_reparacoes(1);}
?>