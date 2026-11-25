<?php
	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');
	set_time_limit(0);
	$path = '../../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include 'funcoes.inc.php';

	// CONFIGURACAO MODULO
	define('MODULO', 'RMAS');
	define('TBMODULO', 'rmas');	 // TABELA
	define('TBMODULO_LANG', 'rmas_linhas');	 // TABELA LINHAS
	define('TBID', 'ID'); // ID PRINCIPAL DA TABELA

	// CAMOS DA TABELA
	$CMPS_MODULO_DADOS = array(
		"ID",
		"ID_Cliente",
		"ID_Cliente2",
		"Grupo_ID",
		"m_estado",
		// "nao_envio_email",
		"m_origem",
		"tipo_reparacao_po",
		"tipo_reparacao_gl",
		"tipo_reparacao_gf",
		"tipo_reparacao_gr",
		"ndocumento",
		"tipo_reparacao",
		"cliente_telefone1",
		"cliente_telefone2",
		"cliente_nome",
		"cliente_sobrenome",
		"cliente_apelido",
		"cliente_morada",
		"cliente_cp",
		"cliente_username",
		"cliente_empresa",
		"cliente_nif",
		"codigo_desbloqueio",
		"m_padrao",
		"m_referencia",
		// "nova_marca",
		"m_marca",
		// "novo_modelo",
		"m_modelo",
		"m_imei",
		"m_nserie",
		"software_dr",
		"software_rc",
		"software_na",
		"software_cg",
		"software_ci",
		"software_bl",
		"hardware_tp",
		"hardware_tnf",
		"hardware_lp",
		"hardware_lpb",
		"hardware_nc",
		"hardware_ssa",
		"hardware_sa",
		"hardware_sam",
		"hardware_sal",
		"hardware_saa",
		"hardware_nl",
		"hardware_ncrg",
		"hardware_ncrw",
		"hardware_svl",
		"hardware_cfnf",
		"hardware_cpnf",
		"hardware_bhn",
		"hardware_bvn",
		"hardware_bsn",
		"hardware_bof",
		"hardware_caj",
		"hardware_lsim",
		"hardware_lmsd",
		"hardware_lc",
		"hardware_sp",
		"m_avaria",
		"m_estado_equipamento",
		"m_estado_equipamento_text",
		"m_acessorios",
		"m_observacoes",
		"m_observacoestec",
		"m_valor",
		"envio_email_orcamento",
		"sel_local",
		"m_tecnico",
		"m_novo_imei",
		"m_observacoestec_relatorio",
		"m_obsint",
		// "qtd",
		// "ref",
		// "desc",
		"m_pago",
		"total",
		"preorcamento",
		"DataUpdate",
		"DataInsert",
		"fake",
		"lida",
		"m_aceite"
	);

	// TABELA LINHAS

	$CMPS_MODULO_DADOS_LANG = array(
		"ID",
		"ID_ORG",
		"Ref",
		"Cabecalho",
		"Quantidade",
		"Preco",
		"Disponibilidade",
		"Data"
	);