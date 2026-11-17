<?php
	header('Content-Type: text/html; charset=utf-8');
	$path =  dirname( __FILE__ ).'/../../core/';
	require_once $path . 'core_files.php';
	require_once $path . 'admin_globals.php';
	require_once 'funcoes.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'MENSAGENS PRÉ-DEFINIDAS');
	define('TBMODULO', 'msg_predefinidas');
	define('TBID', 'ID');
	/*



CREATE TABLE `msg_predefinidas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Visivel` tinyint(1) NOT NULL,
  `Email` tinyint(1) NOT NULL,
  `Ordem` decimal(10,1) NOT NULL,
  `Cor` varchar(7) NOT NULL,
  `Obs` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Visivel` (`Visivel`),
  KEY `Ordem` (`Ordem`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `msg_predefinidas_lang`
--

CREATE TABLE `msg_predefinidas_lang` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PAI` int(11) NOT NULL,
  `Cabecalho` varchar(254) NOT NULL,
  `Cabecalho2` varchar(255) NOT NULL,
  `Texto` text NOT NULL,
  `Obs` text NOT NULL,
  `Lang` varchar(2) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_PAI` (`ID_PAI`),
  KEY `Lang` (`Lang`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




	 */

	// BOTOES LATERAIS
	$BOTOES_L = array
	(
		'INSERIR NOVO' => 'pagina.php?ID=0&dp='.date('YmdHis')
	);

	// BOTOES PARA ACOES
	$BOTOES_B = array(
		'Ativar Selecionados' => 3,
		'Desativar Selecionados' => 2
		// 'Remover Selecionados' => 4
	);

	// PAGINACAO
	define('N_PAGINAS', 30);

	$CMPS_MODULO_DADOS = array(
		"ID",
		"Visivel",
		"Email",
		"Ordem",
		"Obs",
		"Cor"
	);

	define('TBMODULO_LANG', 'msg_predefinidas_lang');

	$CMPS_MODULO_DADOS_LANG = array(
		"ID",
		"ID_PAI",
		"Cabecalho",
		"Cabecalho2",
		"Texto",
		"Lang"
	);

?>