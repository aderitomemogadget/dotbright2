<?
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include "funcoes.inc.php";
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][10])) die("Sem Permiss&otilde;es");

	$pages = array(
				38 => 'Pagina retorno pagamentos',
				45 => 'Rodapé pagamentos/segurança',
				49 => 'Assistência Técnica',
				2 => 'Catalogo',
				109 => 'Marcas',
				3 => 'Produto',
				4 => 'Pesquisa',
				6 => 'Carrinho',
				27 => 'Carrinho Finalizar',
				40 => 'Carrinho Alterar',
				100 => 'Produtos do dia',
				101 => 'Carrinho Reparações',
				104 => 'Favoritos',
				96 => 'Baixas de preço',
				97 => 'Destaques',
				98 => 'Outlet',
				99 => 'Novidades'
			);
?>