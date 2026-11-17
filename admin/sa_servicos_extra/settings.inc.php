<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = '../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';
	require_once 'funcoes_avancada.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'Serviços Extra');
	define('TBMODULO', 'servicos_extra');
	define('TBID', 'garantia_id');
	define('FOLDER', 'servicos_extra');

	// BOTOES LATERAIS
	$BOTOES_L = array(
		'INSERIR NOVO BLOCO' => 'pagina.php?garantia_id=0&dp='.date('YmdHis')
	);

	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2,
		'Remover Selecionados' => 4
	);

	// PAGINACAO
	define('N_PAGINAS', 20);

	$CMPS_MODULO_DADOS = array(
		"garantia_id",
		"familia_id",
		"bloco_ext",
		"img_ext",
		"titulo",
		"info",
		"garantia_n_anos",
		"garantia_valor",
		"garantia_ini",
		"garantia_fim",
		"promo_ativo",
		"valor_promo",
		"Acumulativo", 
		"garantia_visivel"
	);



/*
  CREATE TABLE `servicos_extra` (
  `garantia_id` int(11) NOT NULL AUTO_INCREMENT,
  `familia_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `garantia_n_anos` int(11) NOT NULL,
  `garantia_valor` decimal(10,4) NOT NULL,
  `garantia_ini` decimal(10,4) NOT NULL,
  `garantia_fim` decimal(10,4) NOT NULL,
  `garantia_visivel` tinyint(1) NOT NULL,
  `bloco_ext` varchar(255) NOT NULL,
  `img_ext` varchar(255) NOT NULL,
  `promo_ativo` tinyint(1) NOT NULL,
  `valor_promo` decimal(10,4) NOT NULL,
  PRIMARY KEY (`garantia_id`),
  KEY `familia_id` (`familia_id`),
  KEY `garantia_n_anos` (`garantia_n_anos`),
  KEY `garantia_ini` (`garantia_ini`),
  KEY `garantia_fim` (`garantia_fim`),
  KEY `garantia_visivel` (`garantia_visivel`),
  KEY `promo_ativo` (`promo_ativo`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `servicos_extra_products`
--

CREATE TABLE `servicos_extra_products` (
  `promo_prod_id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) NOT NULL,
  `DESCONTO` decimal(10,4) NOT NULL,
  `PRODUTO_OFERTA` int(11) NOT NULL,
  `PROD_ID` int(11) NOT NULL,
  `FAMILIA` int(11) NOT NULL,
  `MARCA` int(11) NOT NULL,
  `PROD_TXT` varchar(255) NOT NULL,
  `FAMILIAX` int(11) NOT NULL,
  `MARCAX` int(11) NOT NULL,
  PRIMARY KEY (`promo_prod_id`),
  KEY `promo_id` (`promo_id`,`PROD_ID`,`FAMILIA`,`MARCA`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


*/

?>