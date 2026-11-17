<?php
	// header('Content-Type: text/html; charset=utf-8');
	header('Content-Type: text/plain');
	ini_set("max_execution_time","60000");
	ini_set("max_input_time","60000");
	ini_set("memory_limit","-1");

	// exit;
	set_time_limit(0);
	$path = dirname( __FILE__ ).'/../../core/';

	require_once $path.'core_files.php';
	require_once $path.'../admin/encomendas/funcoes.inc.php';


	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(0);


	global $site_http_url,$DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
	$rs = new DataObj;
	$rs2 = new DataObj;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

 	$IP = $_SERVER['REMOTE_ADDR'];
	$sql="Select IP from encomendas_ips_webservices where IP = '$IP'";
    $db_res=$rs->db_open($db_link,$DB_name,$sql);
    if($rs->EOF()) die('access not authorised');
    $rs->db_close();



	$start_id=intval($_GET['start_id']);


	/* create a dom document with encoding utf8 */
    $domtree = new DOMDocument('1.0', 'UTF-8');

    /* create the root element of the xml tree */
    $xmlRoot = $domtree->createElement("xml");
    /* append it to the document created */
    $xmlRoot = $domtree->appendChild($xmlRoot);

    $sql="Select * from encomendas where ID > $start_id order BY ID";
    $db_res=$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
	{
		$IDb				= $rs->db_data["ID"];
		$Data_Criacao		= $rs->db_data["Data_Criacao"];
		$TotalDocIva		= $rs->db_data["TotalDocComIva"];
		$TotalDocSemIva		= $rs->db_data["TotalDocSemIva"];

		$Portes				= $rs->db_data["Portes"];
		$PortesSemIva		= $rs->db_data["PortesSemIva"];
		$IvaPortes			= $rs->db_data["IvaPortes"];

		$ID_Cliente			= $rs->db_data["ID_Cliente"];
		$Contacto 			= $rs->db_data["Contacto"];
		$m_estado 			= $rs->db_data["Estado"];
		$m_pagamento 		= $rs->db_data["Metodo_pagamento"];
		$Data_expedicao 	= $rs->db_data["Data_expedicao"];
		$m_pagamento		= getNomeMetodoPagamentoByTipo_bo($m_pagamento);
		$m_guia 			= $rs->db_data["IDGuia"];
		$ATM 				= base64_decode($rs->db_data["ATM"]);
		$Ent_atm 			= $rs->db_data["Ent_atm"];
		$Ref_atm 			= $rs->db_data["Ref_atm"];
		$Lida 				= $rs->db_data["Lida"];
		$PHC 				= $rs->db_data["importado_phc"];
		$ValorDesconto		= $rs->db_data["ValorDesconto"];
		$IDGuia 			= $rs->db_data["IDGuia"];
		$Estado 			= $rs->db_data["Estado"];
		$Lingua 			= $rs->db_data["Lingua"];
		$enc_Fatura 		= $rs->db_data["enc_Fatura"];
		$TotalIva 			= $rs->db_data["TotalIva"];

		// DADOS DE ENTREGA E FATURACAO
		/*$enc_Nome 			= $rs->db_data['enc_Nome'];
		$enc_Nome_ent 		= $rs->db_data['enc_Nome_ent'];

		$enc_Nif 			= $rs->db_data['enc_Nif'];
		$enc_Nif_ent  		= $rs->db_data['enc_Nif_ent '];*/




		/*$enc_Apelido 		= $rs->db_data['enc_Apelido'];
		$enc_Apelido_ent 	= $rs->db_data['enc_Apelido_ent'];
		$enc_Morada 		= $rs->db_data['enc_Morada'];
		$enc_Morada_ent 	= $rs->db_data['enc_Morada_ent'];
		$enc_Cidade 		= $rs->db_data['enc_Cidade'];
		$enc_Sexo 			= $rs->db_data['enc_Sexo'];
		$enc_DataNascimento = $rs->db_data['enc_DataNascimento'];
		$enc_Cidade_ent 	= $rs->db_data['enc_Cidade_ent'];
		$enc_Codigo_Postal 	= $rs->db_data['enc_Codigo_Postal'];
		$enc_Codigo_Postal_ent = $rs->db_data['enc_Codigo_Postal_ent'];*/


		//$enc_Empresa = $rs->db_data['enc_Empresa'];
		//$enc_Empresa_ent = $rs->db_data['enc_Empresa_ent'];
		$enc_Nome = $rs->db_data['enc_Nome'];
		$enc_Apelido = $rs->db_data['enc_Apelido'];
		$enc_Morada = $rs->db_data['enc_Morada'];
		$enc_Cidade = $rs->db_data['enc_Cidade'];
		$enc_DataNascimento = $rs->db_data['enc_DataNascimento'];
		$enc_Sexo = $rs->db_data['enc_Sexo'];
		$enc_Localidade = $rs->db_data['enc_Localidade'];
		$enc_Codigo_Postal = $rs->db_data['enc_Codigo_Postal'];
		$enc_Email = $rs->db_data['enc_Email'];
		$enc_Telefone = $rs->db_data['enc_Telefone'];
		$enc_Nif = $rs->db_data['enc_Nif'];
		$enc_Pais = $rs->db_data['enc_Pais'];
		$enc_Nome_ent = $rs->db_data['enc_Nome_ent'];
		$enc_Apelido_ent = $rs->db_data['enc_Apelido_ent'];
		$enc_Morada_ent = $rs->db_data['enc_Morada_ent'];
		$enc_Cidade_ent = $rs->db_data['enc_Cidade_ent'];
		$enc_Localidade_ent = $rs->db_data['enc_Localidade_ent'];
		$enc_Codigo_Postal_ent = $rs->db_data['enc_Codigo_Postal_ent'];
		$enc_Email_ent = $rs->db_data['enc_Email_ent'];
		$enc_Telefone_ent = $rs->db_data['enc_Telefone_ent'];
		$enc_Nif_ent = $rs->db_data['enc_Nif_ent'];
		$enc_Pais_ent = $rs->db_data['enc_Pais_ent'];




		$totalvalor += $TotalDocIva;

		$ent='';
		$ref='';
		$valor='';
		if($ATM)
		{
			$ent=$Ent_atm;
			$ref=$Ref_atm;
			preg_match('/<span id=\"order_value\">(.*)<\/span>/isU',$ATM,$matches3);
			$valor=$matches3[1];
		}
		if(!$Contacto) $Contacto="N/A";

		$i=0;


		$enc_state = estado_encomenda($Estado, $Lingua);


		$currentOrder = $domtree->createElement("Order");
	    $currentOrder = $xmlRoot->appendChild($currentOrder);

	    /* you should enclose the following two lines in a cicle */




		$currentOrder->appendChild($domtree->createElement('Order_ID',empty($IDb)?'null':$IDb));
		$currentOrder->appendChild($domtree->createElement('CDateTime',empty($Data_Criacao)?'null':$Data_Criacao));

		$currentOrder->appendChild($domtree->createElement('TotalDocIva',empty($TotalDocIva)?'null':$TotalDocIva));
		$currentOrder->appendChild($domtree->createElement('TotalDocSemIva',empty($TotalDocSemIva)?'null':$TotalDocSemIva));
		$currentOrder->appendChild($domtree->createElement('TotalIva',empty($TotalIva)?'null':$TotalIva));

		$currentOrder->appendChild($domtree->createElement('Portes',empty($Portes)?'null':$Portes));
		$currentOrder->appendChild($domtree->createElement('PortesSemIva',empty($PortesSemIva)?'null':$PortesSemIva));
		$currentOrder->appendChild($domtree->createElement('IvaPortes',empty($IvaPortes)?'null':$IvaPortes));





		$currentOrder->appendChild($domtree->createElement('Cliente_ID',empty($ID_Cliente)?'null':$ID_Cliente));
		$currentOrder->appendChild($domtree->createElement('Contacto',empty($Contacto)?'null':$Contacto));
		$currentOrder->appendChild($domtree->createElement('Data_expedicao',empty($Data_expedicao)?'null':$Data_expedicao));
		$currentOrder->appendChild($domtree->createElement('Metodo_pagament',empty($m_pagamento)?'null':$m_pagamento));
		$currentOrder->appendChild($domtree->createElement('Ent_atm',empty($Ent_atm)?'null':$Ent_atm));
		$currentOrder->appendChild($domtree->createElement('Ref_atm',empty($Ref_atm)?'null':$Ref_atm));
		//$currentOrder->appendChild($domtree->createElement('ATM',empty($ATM)?'null':$ATM));
		$currentOrder->appendChild($domtree->createElement('ValorDesconto',empty($ValorDesconto)?'null':$ValorDesconto));
		//$currentOrder->appendChild($domtree->createElement('IDGuia',empty($IDGuia)?'null':$IDGuia));
		//$currentOrder->appendChild($domtree->createElement('Guia_transporte',empty($m_guia)?'null':$m_guia));
		//$currentOrder->appendChild($domtree->createElement('Lingua',empty($Lingua)?'null':$Lingua));
		//$currentOrder->appendChild($domtree->createElement('enc_Fatur',empty($enc_Fatur)?'null':$enc_Fatur));
		$currentOrder->appendChild($domtree->createElement('Lida',empty($Lida)?'null':$Lida));
		$currentOrder->appendChild($domtree->createElement('Estado',empty($enc_state['titulo'])?'null':$enc_state['titulo']));


		// DADOS DE ENTREGA
		$Dados_Entrega = $domtree->createElement("Dados_Entrega");
	    $Dados_Entrega = $xmlRoot->appendChild($Dados_Entrega);
		$currentOrder->appendChild($Dados_Entrega);

		$Dados_Entrega->appendChild($domtree->createElement('enc_Nif_ent',empty($enc_Nif_ent)?'null':$enc_Nif_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Nome_ent',empty($enc_Nome_ent)?'null':$enc_Nome_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Apelido_ent',empty($enc_Apelido_ent)?'null':$enc_Apelido_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Morada_ent',empty($enc_Morada_ent)?'null':$enc_Morada_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Cidade_ent',empty($enc_Cidade_ent)?'null':$enc_Cidade_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Codigo_Postal_ent',empty($enc_Codigo_Postal_ent)?'null':$enc_Codigo_Postal_ent));

		$Dados_Entrega->appendChild($domtree->createElement('enc_Localidade_ent',empty($enc_Localidade_ent)?'null':$enc_Localidade_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Email_ent',empty($enc_Email_ent)?'null':$enc_Email_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Telefone_ent',empty($enc_Telefone_ent)?'null':$enc_Telefone_ent));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Pais_ent',empty($enc_Pais_ent)?'null':country_name($enc_Pais_ent)));


		// DADOS DE FATURACAO
		$Dados_Entrega = $domtree->createElement("Dados_Faturacao");
	    $Dados_Entrega = $xmlRoot->appendChild($Dados_Entrega);
		$currentOrder->appendChild($Dados_Entrega);

		$Dados_Entrega->appendChild($domtree->createElement('enc_Nif',empty($enc_Nif)?'null':$enc_Nif));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Nome',empty($enc_Nome)?'null':$enc_Nome));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Apelido',empty($enc_Apelido)?'null':$enc_Apelido));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Morada',empty($enc_Morada)?'null':$enc_Morada));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Cidade',empty($enc_Cidade)?'null':$enc_Cidade));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Codigo_Postal',empty($enc_Codigo_Postal)?'null':$enc_Codigo_Postal));

		$Dados_Entrega->appendChild($domtree->createElement('enc_Localidade',empty($enc_Localidade)?'null':$enc_Localidade));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Email',empty($enc_Email)?'null':$enc_Email));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Telefone',empty($enc_Telefone)?'null':$enc_Telefone));
		$Dados_Entrega->appendChild($domtree->createElement('enc_Pais',empty($enc_Pais)?'null':country_name($enc_Pais)));




		$Produtos = $domtree->createElement("Produtos");
	    $Produtos = $xmlRoot->appendChild($Produtos);
		$currentOrder->appendChild($Produtos);

		$total2=0;
		$sql="Select * from encomendas_produtos where ID_PAI = $IDb";
		$rs2->db_open($db_link,$DB_name,$sql);
		while(!$rs2->EOF())
		{
			$ID_Produto		= $rs2->db_data["ID_Produto"];
			$Ref 			= $rs2->db_data["Ref"];
			$Nomes 			= explode('|', $rs2->db_data["Nome"]);
			$Nome 			= $Nomes[0];
			$Nome_opcoes 	= str_replace('[br]', '<br>', $rs2->db_data["Nome_opcoes"]);
			$Email_opcoes 	= $rs2->db_data["Email_opcoes"];
			$Texto_opcoes 	= $rs2->db_data["Texto_opcoes"];
			$Qtd 			= $rs2->db_data["Qtd"];
			$Tamanho 		= $rs2->db_data["Tamanho"];
			$Cor 			= $rs2->db_data["Cor"];
			$Valor 			= $rs2->db_data["Valor"];
			$Valor_Desconto = $rs2->db_data["Valor_Desconto"];
			$Tipo 			= $rs2->db_data["Tipo"];
			$m_estado 		= $rs2->db_data["Estado"];
			$enc_state 		= estado_encomenda($m_estado, 'pt');
			$cor_texto 		= str_replace('#', '', color_inverse($enc_state['cor']));
			$ID_Conf 		= $rs2->db_data["ID_Conf"];
			$login 			= $rs2->db_data["login"];
			$Chave 			= $rs2->db_data["Chave"];
			$Obs 			= $rs2->db_data["Obs"];
			$voucher_id		= $rs2->db_data["voucher_id"];
			$voucher_gift	= $rs2->db_data["voucher_gift"];


			$Produto = $domtree->createElement("Produto");
		    $Produto = $xmlRoot->appendChild($Produto);
			$Produtos->appendChild($Produto);


			$Produto->appendChild($domtree->createElement('ID_Produto',empty($ID_Produto)?'null':$ID_Produto));
			$Produto->appendChild($domtree->createElement('Ref',empty($Ref)?'null':$Ref));
			$Produto->appendChild($domtree->createElement('Nome',empty($Nome)?'null':$Nome));
			$Produto->appendChild($domtree->createElement('Qtd',empty($Qtd)?'null':$Qtd));
			$Produto->appendChild($domtree->createElement('Valor',empty($Valor)?'null':$Valor));
			$Produto->appendChild($domtree->createElement('Valor_Desconto',empty($Valor_Desconto)?'null':$Valor_Desconto));
			//$Produto->appendChild($domtree->createElement('Obs',empty($Obs)?'null':$Obs));
			//$Produto->appendChild($domtree->createElement('voucher_id',empty($voucher_id)?'null':$voucher_id));


			$rs2->db_next();
		}
		$rs2->db_close();


		$rs->db_next();
	}
	$rs->db_close(1);







	$domtree->preserveWhiteSpace = false;
    $domtree->formatOutput = true;
    /* get the xml printed */
    print_R($domtree->saveXML());
    



?>