<?php
	include  "funcoes.php";
	//Configuracoes
	$CA_certificate='./certs/ca_chain/bundle.pem';
	$Private_Key='./certs/ext_client.key.pem';
	$Client_Certificate='./certs/ext_client.cert.pem';
	$url = 'https://www.wepay.pt:7002/financial_operations';
	$passphrase = 'n3wnot3mbw@yextclient';
	$API_KEY='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIxMDgwMDU2IiwiaXNzIjoiTUJfV0FZX1dJREdFVF9UUEFTX01BTkFHRVIiLCJzdWIiOiIxMDgwMDU2IiwiaWF0IjoiMjAxOC0xMi0wNyAxMTowMDo0NCIsInJvbCI6MX0.0ZPhaXqkwK8lS-IBdK6St7jz_MVROBIOlzP75VvvykQ';
	$TPA_ID='1080056';
	$Authorization='Bearer '.$API_KEY;
	//*****************************************************************************
	//
	//

	//ESTE EXEMPLO PERMITE LISTAR TODOS OS PAGAMENTOS E O ESTADO DE CADA UM
	//$resultados=get_all_mbway_pagamentos();

	//ESTE EXEMPLO PERMITE INVOCAR UM PAGAMENTO
	//$resultados=pagar_mbway('914931936','250','encomenda 125');

	//ESTE EXEMPLO PERMITE OBTER O ESTADO DE UM PEDIDO DE PAGAMENTO
	//$resultados = get_status_mbway_tel(44588);



	var_dump($resultados);


?>