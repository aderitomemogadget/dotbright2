<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {  exit();}
global $n_enc_reparacoes;
@session_start();
if (!$n_enc_reparacoes) { $n_enc_reparacoes = 0; $shop_reparacoes = array(); }
if (isset($_GET['prod_add_reparacoes'])) { $prod_id_reparacoes = intval($_GET['prod_add_reparacoes']);}
if (isset($_GET['prod_add_reparacoes_obj'])) {  $prod_obj_id_reparacoes = intval($_GET['prod_add_reparacoes_obj']); }
$prod_obj_opces_reparacoes = '';
if (isset($_GET['prod_add_reparacoes_opcoes'])) { $prod_obj_opces_reparacoes = clean_var($_GET['prod_add_reparacoes_opcoes']);}
if (isset($_GET['prod_add_reparacoes_qtd'])) {$prod_add_reparacoes_qtd = intval($_GET['prod_add_reparacoes_qtd']);} else {$prod_add_reparacoes_qtd = 1;}

// save_endereco();


if (!isset($_SESSION['CLIENTE_ID_TEMP_REPARACOES'])) { $_SESSION['CLIENTE_ID_TEMP_REPARACOES'] = date('YmdHis') . uniqid();}

if (!isset($d_rep)) { $d_rep = '';}

if (!$d_rep) { $d_rep = uniqid("");}

if (isset($_GET['num_reparacoes']) && $_GET['num_reparacoes'] > 0 && $_GET['qtd_reparacoes'] > 0)
{
    $verifica_qtd = verifica_qtd($shop_reparacoes[$_GET['num_reparacoes']][1], $_GET['qtd_reparacoes']); // se for um produto, vamos ver se tem stock
    $shop_reparacoes[$_GET['num_reparacoes']][2] = $verifica_qtd;
    if ($shop_reparacoes[$_GET['num_reparacoes']][2] <= 0)
	{
        RemLinhaEnc_reparacoes($_GET['num_reparacoes']);
    }
}

if (isset($_GET['prod_add_reparacoes']) && $prod_id_reparacoes > 0 && $prod_obj_id_reparacoes > 0)
{
    $verifica = 1;//verifica_stock($prod_id_reparacoes); // se for um produto, vamos ver se tem stock
    $verifica_qtd = verifica_qtd($prod_id_reparacoes, $prod_add_reparacoes_qtd); // se for um produto, vamos ver se tem stock

    if ($verifica == 1)
	{
        $i = 1;
        $found = 0;
        while ($i <= $n_enc_reparacoes && $found == 0) {
            if ($prod_obj_opces_reparacoes != '') {
                if ($shop_reparacoes[$i][1] == $prod_id_reparacoes && $shop_reparacoes[$i][5] == $prod_obj_id_reparacoes && $shop_reparacoes[$i][6] == $prod_obj_opces_reparacoes) {
                    $found = $i;
                }
            } else {
                if ($shop_reparacoes[$i][1] == $prod_id_reparacoes && $shop_reparacoes[$i][5] == $prod_obj_id_reparacoes) {
                    $found = $i;
                }
            }
            $i++;
        }
        if (!$found) {
            if ($prod_add_reparacoes_qtd <= 0) {   $prod_add_reparacoes_qtd = 1; }
            $n_enc_reparacoes++; // mais uma linha de encomenda
            $shop_reparacoes[$n_enc_reparacoes][1] = $prod_id_reparacoes; //codigo do produto
            $shop_reparacoes[$n_enc_reparacoes][2] = $verifica_qtd; //quantidade do produto por defeito
            $shop_reparacoes[$n_enc_reparacoes][3] = 1; // tipo de produto ( 1 = produtos 2 = Club)
            $shop_reparacoes[$n_enc_reparacoes][4] = $d_rep; // chave seguranca
            $shop_reparacoes[$n_enc_reparacoes][5] = $prod_obj_id_reparacoes; // chave seguranca
            $shop_reparacoes[$n_enc_reparacoes][6] = $prod_obj_opces_reparacoes; // chave seguranca
            if ($prod_combo_id > 0) {
                $shop_reparacoes[$n_enc_reparacoes][7] = $prod_combo_id;
            } // chave seguranca

        } else {
            $shop_reparacoes[$found][1] = $prod_id_reparacoes; //codigo do produto
            if (($shop_reparacoes[$found][2] + $verifica_qtd) > 0) {
                $shop_reparacoes[$found][2] += $verifica_qtd;
            } //quantidade do produto por defeito
            $shop_reparacoes[$found][3] = 1; // tipo de produto ( 1 = produtos 2 = Club)
            $shop_reparacoes[$found][4] = $d_rep; // chave seguranca
            $shop_reparacoes[$found][5] = $prod_obj_id_reparacoes; // chave seguranca
            $shop_reparacoes[$found][6] = $prod_obj_opces_reparacoes; // chave seguranca
            if ($prod_combo_id > 0) {
                $shop_reparacoes[$found][7] = $prod_combo_id;
            } // chave seguranca

            if ($shop_reparacoes[$found][2] <= 0) {
                $shop_reparacoes[$found][2] = 1;
            }
        }
    } else {
        $erro = 1;
    }
}


if (isset($_GET['reset']) && $_GET['reset'] == 1)
{
    unset($_SESSION['ss_shop_reparacoes']);
    unset($_SESSION['ss_n_enc_reparacoes']);
    $shop_reparacoes = array();
    $n_enc_reparacoes = 0;
}
elseif (isset($_GET['del_reparacoes']) && $_GET['del_reparacoes'] > 0)
{
    $i = 1;
    $found = 0;
    while ($i <= $n_enc_reparacoes && $found == 0) {
        if ($shop_reparacoes[$i][4] == $_GET['del_reparacoes']) {
            $found = $i;
        }
        $i++;
    }
    if ($found)
	{
        RemLinhaEnc_reparacoes($found);
        global $cli_id;
        if ($cli_id > 0)
		{
            deleteProdutosCarrinhoTemporario($cli_id, $prod_id_reparacoes);
        }
    }
}

$_SESSION['ss_shop_reparacoes'] = $shop_reparacoes;
$_SESSION['ss_n_enc_reparacoes'] = $n_enc_reparacoes;
function RemLinhaEnc_reparacoes($n_linha)
{
    global $n_enc_reparacoes, $shop_reparacoes;
    for ($i = $n_linha; $i <= $n_enc_reparacoes; $i++) //percorrer todas as linhas ate ao fim das encomendas
    {
        for ($col = 1; $col <= 7; $col++) //percorrer as colunas da matriz encomendas
        {
            $shop_reparacoes[$i][$col] = $shop_reparacoes[$i + 1][$col]; //puxar os valores para tras uma posição
        }
    }
    $n_enc_reparacoes--;
}

if (isset($_GET['prod_add_reparacoes'])) { exit;}

function validaCarrinho_reparacoes()
{
    global $n_enc_reparacoes, $shop_reparacoes;
    $i = 1;
    $valid_prods = 0;
    $emailmsg = '';
    while ($i <= $n_enc_reparacoes)
	{
        $prod_id = $shop_reparacoes[$i][1];
        $qtd = $shop_reparacoes[$i][2];
        $tipo = $shop_reparacoes[$i][3];
        $chave = $shop_reparacoes[$i][4];
        $prod_obj = $shop_reparacoes[$i][5];


        $verifica = 1; //verifica_stock($prod_id);
        if ($verifica == 1)
		{
            $verifica_qtd = verifica_qtd($prod_id, $qtd);
            if ($qtd <= 0)
			{
                $qtd = $verifica_qtd;
            }

            $infoprod = get_produtc_info($prod_id);
            if ($infoprod) {
                if ($infoprod['prod_carrinho'] == 1 && $infoprod['prod_precos']['PVP'] > 0) {
                    $valid_prods++;
                } else {
                    unset($shop_reparacoes[$i]);
                    $emailmsg = 'Tentativa de fraude por produto sem carrinho visivel e/ou sem PVP - Produto IDº ' . $prod_id;
                }
            } else {
                unset($shop_reparacoes[$i]);
                $emailmsg = 'Tentativa de fraude por produto não aprovado/inexistente - Produto IDº ' . $prod_id;
            }
        } else {
            unset($shop_reparacoes[$i]);
            $emailmsg = 'Tentativa de fraude por produto sem stock - Produto IDº ' . $prod_id;
        }
        $i++;
    }

    /* if ($emailmsg != '') {
        $message = '';
        global $cli_id;
        if ($cli_id > 0) {
            $message = 'O cliente Nº ' . $cli_id . ' realizou uma ' . $emailmsg;
        } else {
            $message = 'Ocorreu uma ' . $emailmsg;
        }
        send_message_admin('Tentativa de Fraude', $message, 6);
    } */

    if ($valid_prods > 0)
	{
        $shop_reparacoes = array_combine(range(1, count($shop_reparacoes)), array_values($shop_reparacoes));
        $_SESSION['ss_shop_reparacoes'] = $shop_reparacoes;
        $_SESSION['ss_n_enc_reparacoes'] = $valid_prods;
    } else {
        unset($_SESSION['ss_shop_reparacoes']);
        unset($_SESSION['ss_n_enc_reparacoes']);
        unset($_SESSION['CLIENTE_ID_TEMP_REPARACOES']);
        $shop_reparacoes = array();
        $n_enc_reparacoes = 0;
        $_SESSION['ss_shop_reparacoes'] = $shop_reparacoes;
        $_SESSION['ss_n_enc_reparacoes'] = $n_enc_reparacoes;
    }
}
function limparEncomenda_reparacoes()
{

	foreach (glob($GLOBALS['site_shell_url']."files/encomendas/*_".$GLOBALS['cli_id'].".enc") as $filename)
	{
		if(substr_count($filename, '.enc')) @unlink($filename);
	}

	unset($_SESSION['CLIENTE_ID_TEMP_REPARACOES']);
    unset($_SESSION['ss_shop_reparacoes']);
    unset($_SESSION['ss_n_enc_reparacoes']);
    $shop = array();
    $n_enc = 0;

    if ($GLOBALS['module_voucher'] == 1)
	{
        unset($_SESSION['ss_voucher_reparacoes']);
        unset($_SESSION['ss_n_voucher_reparacoes']);
        $voucher_reparacoes = array();
        $n_voucher_reparacoes = 0;
        $_SESSION['ss_voucher_reparacoes'] = $voucher_reparacoes;
        $_SESSION['ss_n_voucher_reparacoes'] = $n_voucher_reparacoes;
    }
    $_SESSION['ss_shop_reparacoes'] = $shop_reparacoes;
    $_SESSION['ss_n_enc_reparacoes'] = $n_enc_reparacoes;
}

function tratarMetodosPagamento_reparacoes($idEncomenda, $idMetodo, $totalEncomenda)
{
    $rs = getMetodoPagamentoById($idMetodo);
    if (!$rs->EOF())
	{


        $tipo = $rs->db_data['Tipo'];

        switch ($tipo)
		{
            case MP_TP_PAYPAL:
                $return = getMetodoPagamentoPaypalGateway($idMetodo, $totalEncomenda, $idEncomenda);
                break;
            case MP_TP_TRANS_BANCARIA:
                limparEncomenda_reparacoes();
                $return =  getMetodoPagamentoTransBancariaGateway($idMetodo, $totalEncomenda, $idEncomenda);
                break;
            case MP_TP_UNICRE:
                $return =  getMetodoPagamentoUnicreGateway($totalEncomenda, $idEncomenda);
                break;
            case MP_TP_MB_IFTHEN:
                limparEncomenda_reparacoes();
                $return =  getMetodoPagamentoMBIfthenGateway($idEncomenda);
                break;
			case MP_TP_MB_LUSOPAY:
                limparEncomenda_reparacoes();
                $return =  getMetodoPagamentoMBLusopayGateway($idEncomenda);
                break;
            case MP_TP_COBRANCA:
                limparEncomenda_reparacoes();
                $return =  getMetodoPagamentoCobrancaGateway();
                break;
            case MP_TP_HIPAY:
                $return = getMetodoPagamentoHipayGateway($idEncomenda);
                break;
        }
    }
    $rs->db_close();

	return $return;
}

function carrinho_purchase_reparacoes()
{
    validaCarrinho_reparacoes();

    global $n_enc_reparacoes, $shop_reparacoes, $iva_default, $n_voucher, $voucher;

	$d_repados_cliente = cliente_logado();	

    if (isset($_POST['finalizar']) && $_POST['finalizar'] == 1 && $n_enc_reparacoes > 0 && is_array($d_repados_cliente))
	{
        $valido = TRUE;
        $rs_cliente = new Dataobj;
        $d_repb_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);

        //dados de faturacao
        $m_end_faturacao = intval($_POST['m_end_faturacao']);
        if ($m_end_faturacao <= 0)
        {
            $erro=10;
            $valido = FALSE;
        }


        //dados de entrega
        $m_end_entrega = intval($_POST['m_end_entrega']);
        if ($m_end_entrega <= 0) {
            $erro=11;
            $valido = FALSE;
        }

        //metodo de pagamento
        $m_metodopagamento = intval($_POST['m_metodopagamento']);
        if ($m_metodopagamento <= 0) {
            $erro=12;
            $valido = FALSE;
        }

        //metodo de expedicao, nas reparações não existe
        /*$m_metodoenvio = intval($_POST['m_metodoenvio']);
        if ($m_metodoenvio <= 0) {
            $erro=13;
            $valido = FALSE;
        }*/
        $m_observacoes = addslashes($_POST['m_observacoes']);
		// echo $valido;
		// exit;
        if (!$valido)
		{
            echo '<script>window.location.href=\'' . seo('pages.php?page_id=' . $GLOBALS['page_id']) . '\';</script>';
        }
		else
		{
            $ID_Afiliado = 0;
            $Afiliado_Perc = 0;
            if (isset($_COOKIE['domesticosafiliado']) && intval($_COOKIE['domesticosafiliado']) > 0)
			{
                $ID_Afiliado = intval($_COOKIE['domesticosafiliado']);
                $sql = "SELECT Afiliado_Perc FROM clientes WHERE ID='$ID_Afiliado' AND Afiliado=1";
                $rs_cliente->db_open($d_repb_link, $GLOBALS['DB_name'], $sql);
                if (!$rs_cliente->EOF())
				{
                    $Afiliado_Perc = $rs_cliente->db_data['Afiliado_Perc'];
                }
				else
				{
                    $ID_Afiliado = 0;
                    $Afiliado_Perc = 0;
                }
                $rs_cliente->db_close();
            }

            // $d_repados = cliente_logado();
            $cli_id = $d_repados_cliente['cli_id'];
            $m_contribuinte = '';
            $m_email = '';

			if(is_array($d_repados_cliente))
			{
               $m_contribuinte = $d_repados_cliente['Nif'];
               $m_email = $d_repados_cliente['Email'];
            }

            // OBTER INFO DO ENDEREÇO DE FATURACAO
            $m_designacao = '';
            $m_morada = '';
            $m_codpos = '';
            $m_cidade = '';
            $m_pais = '';
            $m_telefone = '';

           $endFaturacao = getEnderecos($d_repados_cliente['cli_id'], 1, $m_end_faturacao);
			if (!$endFaturacao->EOF()) {
				$m_nome = $endFaturacao->db_data['Nome'];
				$m_apelido = $endFaturacao->db_data['Apelido'];
				$m_morada = $endFaturacao->db_data['Morada'];
				$m_codpos = $endFaturacao->db_data['Codigo_Postal'];
				$m_cidade = $endFaturacao->db_data['Cidade'];
				$m_pais = $endFaturacao->db_data['Pais'];
				$m_telefone = $endFaturacao->db_data['Telefone'];
				$m_contribuinte = $endFaturacao->db_data['Nif'];
			}
			$endFaturacao->db_close();

			//Obter info do endereço de entrega
			$m2_designacao = '';
			$m2_nome = '';
			$m2_apelido = '';
			$m2_morada = '';
			$m2_codpos = '';
			$m2_cidade = '';
			$m2_pais = '';
			$m2_telefone = '';

			$endEntrega = getEnderecos($d_repados_cliente['cli_id'], 1, $m_end_entrega);
			if (!$endEntrega->EOF()) {
				$m2_nome = $endEntrega->db_data['Nome'];
				$m2_apelido = $endEntrega->db_data['Apelido'];
				$m2_morada = $endEntrega->db_data['Morada'];
				$m2_codpos = $endEntrega->db_data['Codigo_Postal'];
				$m2_cidade = $endEntrega->db_data['Cidade'];
				$m2_pais = $endEntrega->db_data['Pais'];
				$m2_telefone = $endEntrega->db_data['Telefone'];
			}
			$endEntrega->db_close();


            // CALCULA ENCOMENDA COM PRODUTOS E COM VOUCHERS
            $totalEncomenda = calcularTotalEncomenda_reparacoes();

            // EXPEDIÇÃO
            $portes = 0;
            if ($metodoEnvioObj = getEncomendaMetodoExpedicao($m_metodoenvio, 2))
			{
                $portes = getPortesCarrinho($m_metodoenvio, $m_pais);
                $valor_portes_gratuitos = getPortesGratuitos();

                if ($totalEncomenda['TOTALCIVA'] >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
				{
                    $portes = 0;
                }
            }

            // PAGAMENTO
            $taxas = 0;
           if ($metodoPagamentoObj = getEncomendaMetodoPagamento($m_metodopagamento))
			{
				if($metodoPagamentoObj['metodo_taxa_tipo'] == 1)
				{
					$metodo_taxa2 = calcula_desconto($totalEncomenda['TOTALCIVA'], $metodoPagamentoObj['Taxa']);
					$taxas += $metodo_taxa2;
				}
				else
				{
					$taxas += $metodoPagamentoObj['Taxa'];
				}
            }

            $Data_Criacao = date("Y-m-d H:i:s");

            // $sql = "INSERT INTO encomendas(Data_Criacao, Metodo_pagamento, Metodo_envio, ID_Cliente, Contacto, enc_Nome, enc_Email, enc_Telefone, enc_Nif, enc_Pais, enc_Morada, enc_Cidade,  enc_Codigo_Postal, enc_Nome_ent, enc_Morada_ent, enc_Pais_ent, enc_Cidade_ent, enc_Codigo_Postal_ent, enc_Telefone_ent, Obs, ID_Afiliado, Afiliado_Perc) VALUES ('$Data_Criacao', '$m_metodopagamento', '$m_metodoenvio', '$cli_id', '$m_designacao', '$m_designacao', '$m_email', '$m_telefone', '$m_contribuinte', '$m_pais', '$m_morada', '$m_cidade', '$m_codpos', '$m2_designacao', '$m2_morada', '$m2_pais', '$m2_cidade', '$m2_codpos', '$m2_telefone', '$m_observacoes', '$ID_Afiliado', '$Afiliado_Perc')";
            $sql = 'INSERT INTO
					encomendas
					(
						Data_Criacao,
						Metodo_pagamento,
						Metodo_envio,
						ID_Cliente,
						Contacto,
						enc_Nome,
						enc_Apelido,
						enc_Email,
						enc_Telefone,
						enc_Nif,
						enc_Pais,
						enc_Morada,
						enc_Cidade,
						enc_DataNascimento,
						enc_Sexo,
						enc_Codigo_Postal,
						enc_Nome_ent,
						enc_Apelido_ent,
						enc_Morada_ent,
						enc_Pais_ent,
						enc_Cidade_ent,
						enc_Codigo_Postal_ent,
						Obs,
						ID_Afiliado,
						Afiliado_Perc,
						Lingua,
						Assistencia
					)
					VALUES
					(
						"'.date("Y-m-d H:i:s").'",
						"'.$m_metodopagamento.'",
						"'.$m_metodoenvio.'",
						"'.$cli_id.'",
						"'.$m_nome.' '.$m_apelido.'",
						"'.$m_nome.'",
						"'.$m_apelido.'",
						"'.$m_email.'",
						"'.$m_telefone.'",
						"'.$m_contribuinte.'",
						"'.$m_pais.'",
						"'.$m_morada.'",
						"'.$m_cidade.'",
						"'.$m_datanascimento.'",
						"'.$m_sexo.'",
						"'.$m_codpos.'",
						"'.$m2_nome.'",
						"'.$m2_apelido.'",
						"'.$m2_morada.'",
						"'.$m2_pais.'",
						"'.$m2_cidade.'",
						"'.$m2_codpos.'",
						"'.$m_observacoes.'",
						"'.$ID_Afiliado.'",
						"'.$Afiliado_Perc.'",
						"'.$GLOBALS['site_lang'].'",
						1
					)';
			// echo $sql;
			$res = Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);
            if ($res)
			{
                $DATAFIMCMP_arr = array();
                $ID_arr = array();
                $new_id = db_get_last_insert_id($d_repb_link);

                $enc_token = md5($new_id . $d_repados_cliente['cli_id']);

                $i = 1;
                $totalciva = 0;
                $totalsiva = 0;

                $offer_arr = showSalesPromotions_offer($GLOBALS['proms']);
                if ($offer_arr['p_html_offers'])
				{
                    $DATAFIMCMP_arr[] = $offer_arr['p_data_limite'];
                }

                while ($i <= $n_enc_reparacoes)
				{
					$prod_id = $shop_reparacoes[$i][1];
					$qtd = $shop_reparacoes[$i][2];
					$tipo = $shop_reparacoes[$i][3];
					$chave = $shop_reparacoes[$i][4];
					$prod_obj = $shop_reparacoes[$i][5];
					$opcoes ='';$opcoes = $shop_reparacoes[$i][6];
					$opcoes_id = 0; $opcoes_id = $shop_reparacoes[$i][7];

					$info = get_produtc_info($prod_id,$prod_obj);
					if($opcoes_id > 0) $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);
					$modelo = ''; if($modelo != $info['prod_precos']['Modelo']) $modelo .= $info['prod_precos']['Modelo'];  // MODELO SECUNDARIO

                    /* if ($modelo != $info['prod_precos']['Modelo'])
					{
                        $modelo .= '-' . $info['prod_precos']['Modelo'];
                    }  */ // MODELO SECUNDARIO

                    $valorqtd = $info['prod_precos']['PVP'] * $qtd;

                    $arr_prom = showSalesPromotions($GLOBALS['proms'], $prod_id, $valorqtd, 1);
                    $DATAFIMCMP_arr[] = $arr_prom['DATAFIMCMP'];
                    $ID_arr[] = $arr_prom['IDARR'];

                    if ($arr_prom['p_valor'] > 0)
					{
                        $totalciva += $arr_prom['p_valor'];
                    }
					else
					{
                        $totalciva += $valorqtd;
                    }

                    $valorciva = $info['prod_precos']['PVP'];

                    if ($arr_prom['p_valor'] > 0) {
                        $v_qtd = ($valorqtd - $arr_prom['p_valor']);
                    } else {
                        $v_qtd = ($valorqtd);
                    }


                    $iva = $info['prod_precos']['IVA'];
                    if ($iva <= 0)
					{
                        $iva = $iva_default;
                    }

                    if ($iva > 0)
					{
                        $iva = 1 + (floatval($iva) / 100);
                        if ($arr_prom['p_valor'] > 0)
						{
                            $psiva = (($arr_prom['p_valor']) / $iva);
                        }
						else
						{
                            $psiva = ($info['prod_precos']['PVP'] / $iva);
                        }
                    }
					else
					{
                        if ($arr_prom['p_valor'] > 0)
						{
                            $psiva = $arr_prom['p_valor'];
                        }
						else
						{
                            $psiva = $info['prod_precos']['PVP'];
                        }
                    }

                    $totalsiva += $psiva;



				   $sql = 'INSERT INTO
							encomendas_produtos
							(
								ID_PAI,
								ID_Cliente,
								ID_Produto,
								Ref,
								Nome,
								Qtd,
								Valor,
								Tipo,
								Estado,
								Valor_Desconto,
								Perc_Desconto,
								Chave,
								Obs
							)
							VALUES
							(
								"'.$new_id.'",
								"'.$d_repados_cliente['cli_id'].'",
								"'.$prod_id.'",
								"'.$modelo.'",
								"'.((isset($opcoes) && $opcoes!='') ?  $info['prod_nome'].','.$opcoes : $info['prod_nome']).'",
								"'.$qtd.'",
								"'.$valorciva.'",
								"'.$tipo.'",
								"0",
								"'.$v_qtd.'",
								"'.$arr_prom['p_desconto'].'",
								"'.$chave.'",
								"'.$obs.'"
							)';
                    Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);

					// aqui

					for($a=0;$a<$qtd;$a++)
					{
						$x++;


						 $sql = 'INSERT INTO
									encomendas_produtos_rmas
									(
										ID_PAI,
										ID_Cliente,
										ID_Produto,
										Ref,
										Nome,
										Qtd,
										Valor,
										Tipo,
										Estado,
										Valor_Desconto,
										Perc_Desconto,
										Chave,
										Obs,
										rma_imei,
										rma_n_serie,
										rma_obs
									)
									VALUES
									(
										"'.$new_id.'",
										"'.$d_repados_cliente['cli_id'].'",
										"'.$prod_id.'",
										"'.$modelo.'",
										"'.((isset($opcoes) && $opcoes!='') ?  $info['prod_nome'].','.$opcoes : $info['prod_nome']).'",
										"1",
										"'.$valorciva.'",
										"'.$tipo.'",
										"0",
										"1",
										"0",
										"'.$chave.'_'.$a.'",
										"'.$obs.'",
										"'.(isset($_POST['m_imei_'.$prod_id.'_'.$a]) && $_POST['m_imei_'.$prod_id.'_'.$a]!='' ? $_POST['m_imei_'.$prod_id.'_'.$a] : '').'",
										"'.(isset($_POST['m_serie_'.$prod_id.'_'.$a]) && $_POST['m_serie_'.$prod_id.'_'.$a]!='' ? $_POST['m_serie_'.$prod_id.'_'.$a] : '').'",
										"'.(isset($_POST['m_obs_'.$prod_id.'_'.$a]) && $_POST['m_obs_'.$prod_id.'_'.$a]!='' ? $_POST['m_obs_'.$prod_id.'_'.$a] : '').'"
									)';
							Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);


					}



                    //dar baixa de stock
                    // $sql = 'UPDATE products_objs SET obj_stock=obj_stock-' . $qtd . ' WHERE obj_type_id="' . $prod_id . '"';
                    // Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);

                    //remover produtos vistos pelo cliente
                    // if ($d_repados_cliente['cli_id'] > 0)
					// {
                        // actualizarProdutoNotificacao($d_repados_cliente['cli_id'], $prod_id);
                    // }

                    $i++;
                }

                //VOUCHERS
               /*  if ($GLOBALS['module_voucher'] == 1)
				{
                    $i = 1;
                    while ($i <= $n_voucher)
					{

                        $voucher_code = $voucher[$i][1];
                        $chave = $voucher[$i][4];
                        $prod_id = 0;

                        $info = get_voucher_info($voucher_code);
                        $v_discount = $info['voucher_discount'];

                        $nome_prod = '';
                        $valor_dif = 0;
                        $tipo = 0;
                        $ref_prod = '';
                        if ($info['voucher_type'] == 1)
						{
                            $prod_id = $info['voucher_prod_id'];
                            $info_prod = get_produtc_info($prod_id);
                            $nome_prod = getLinguas('key_voucher_oferta',$GLOBALS['site_lang']) . ': ' . $info_prod['prod_nome'];
                            $ref_prod = $info_prod['prod_ref'];
                            $valor_dif = 0;
                            $v_discount = 100;
                        }
						else
						{
                            if ($info['voucher_type'] == 0 || $info['voucher_type'] == 3)
							{
                                $nome_prod = getLinguas('key_voucher_desconto',$GLOBALS['site_lang']);
                                $prod_id = $info['voucher_prod_id'];
                                $info_prod = get_produtc_info($prod_id);
                                $ref_prod = $info_prod['prod_ref'];
                                if ($info['voucher_type_discount'] == 0)
								{
                                    $valor_dif = calcula_desconto($totalEncomenda['TOTALCIVA'], $v_discount);
                                }
								else
								{
                                    if ($info['voucher_type_discount'] == 1)
									{
                                        if ($v_discount > $totalEncomenda['TOTALCIVA'])
										{
                                            $valor_dif = $totalEncomenda['TOTALCIVA'];
                                            $v_discount = 100;
                                        }
										else
										{
                                            $valor_dif = $v_discount;
                                            $v_discount = ($v_discount * 100) / $totalEncomenda['TOTALCIVA'];
                                        }
                                    }
                                }
                            }
                        }

                        $sql = "INSERT INTO encomendas_produtos(ID_PAI, ID_Cliente, ID_Produto, Ref, Nome, Qtd, Valor, Valor_Desconto, Perc_Desconto, Tipo, Estado, Chave, voucher_id, download) VALUES ('$new_id', '$cli_id', '$prod_id', '$ref_prod', '$nome_prod', 1, 0, '$valor_dif', '$v_discount', '$tipo', 0, '$chave', '{$info['voucher_id']}', '{$info['prod_download']}')";
                        Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);
                        $i++;
                    }
                } */


                $result_ids = @array_unique(@array_filter(array_flatten($ID_arr)));
                $Promos_ID = '';
                foreach ($result_ids as $key => $value)
				{
                    $Promos_ID .= $value . ';';
                }
                $result_prom = @array_filter(array_flatten($DATAFIMCMP_arr));
                $min_key = @array_search(min($result_prom), $result_prom);
                $d_repata_limite = '';
                if (isset($result_prom[$min_key]) && is_array($result_prom) && !empty($result_prom[$min_key]))
				{
                    $d_repata_limite = $result_prom[$min_key];
                }

                $tot = ($totalEncomenda['TOTALCIVA'] - $totalEncomenda['DESCONTOS']) + $portes + $taxas;
                $ip = getenv("REMOTE_ADDR");
                $sql = "UPDATE encomendas SET TotalDocIva='{$totalEncomenda['TOTALIVA']}', TotalIva='{$totalEncomenda['TOTALIVA']}', TotalDocComIva='$tot', TotalDocSemIva='{$totalEncomenda['TOTALSIVA']}', ValorDesconto='{$totalEncomenda['DESCONTOS']}', Taxas='$taxas', enc_token='$enc_token', Promos_ID='$Promos_ID', Portes='$portes', IP='$ip'";
                if ($d_repata_limite != '')
				{
                    $sql .= ", Data_Limite='$d_repata_limite' ";
                }
                $sql .= " WHERE ID='$new_id'";

                Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);
                if ($d_repados_cliente['cli_id'])
				{
                    $sql = "UPDATE clientes SET NumEncomendas=NumEncomendas+1 WHERE ID='{$d_repados_cliente['cli_id']}'";
                    Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);
                }

				$message_mb = '';
				$m_pagamento_tipo = getNomeMetodoPagamentoByTipo_ID($m_metodopagamento);

				if($m_pagamento_tipo == MP_TP_TRANS_BANCARIA || $m_pagamento_tipo == MP_TP_MB_LUSOPAY || $m_pagamento_tipo == MP_TP_MB_IFTHEN)
				{
					// METODOS DE PAGAMENTO
					tratarMetodosPagamento_reparacoes($new_id, $m_metodopagamento, number_format($tot, 2, '.', ''));

					enviarEmailCliente($new_id);

					echo show_alerta2('A sua encomenda foi registada com sucesso. Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>' . $message_mb, 'ok2');

					echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id, '800', 1, '').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
				}
				elseif($m_pagamento_tipo == MP_TP_UNICRE)
				{

					$sql = '
							INSERT INTO pagamentos
								(Nome, Codigo_do_cliente, Referente_factura, Quantia, data, Lingua)
							VALUES
								("'.addslashes($m_nome.' '.$m_apelido).'", "'.$d_repados_cliente['cli_id'].'", "'.$new_id.'", "'.$tot.'", "'.date('Y-m-d H:i:s').'", "'.$GLOBALS['site_lang'].'")
							';

					$res = Runquery($d_repb_link, $GLOBALS['DB_name'], $sql);

					// METODOS DE PAGAMENTO
					echo tratarMetodosPagamento_reparacoes($new_id, $m_metodopagamento, number_format($tot, 2, '.', ''));
				}
				else
				{
					echo tratarMetodosPagamento_reparacoes($new_id, $m_metodopagamento, number_format($tot, 2, '.', ''));

					enviarEmailCliente($new_id);

					echo show_alerta2('A sua encomenda foi registada com sucesso. Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>' . $message_mb, 'ok2');

					echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id, '800', 1, '').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
				}
            }
		}
    }
	elseif ($n_enc_reparacoes > 0)
	{

        if (is_array($d_repados_cliente))
		{

			$enc_file = "files/encomendas/".$_SESSION['CLIENTE_ID_TEMP_REPARACOES'].'_'.$GLOBALS['cli_id'].'.enc';

			if(isset($_POST) && !empty($_POST['cart_quantity']))
			{
				if(stream_resolve_include_path($enc_file))
				{
					@unlink($enc_file);
					$fh = fopen($enc_file, 'w');
					if( $fh == false )
					{} // HOUVE PROBLEMAS
					else
					{
						fwrite($fh, serialize($_POST));
						fclose($fh);
					}
				}
				else
				{
					$fh = fopen($enc_file, 'w');
					if( $fh == false )
					{} // HOUVE PROBLEMAS
					else
					{
						fwrite($fh, serialize($_POST));
						fclose($fh);
					}
				}
			}
			else
			{
				if(stream_resolve_include_path($enc_file))
				{
					$_POST = unserialize(file_get_contents($enc_file));
				}
			}



			//cliente autenticado
            $d_repb_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
            $rs = new Dataobj;

            $m_pais = $d_repados_cliente['Pais'];

			$m_end_faturacao = 0;
			if (isset($_POST['m_end_faturacao']))
			{
				$m_end_faturacao = $_POST['m_end_faturacao'];
			}
			$m_end_entrega = 0;
			if (isset($_POST['m_end_entrega']))
			{
				$m_end_entrega = $_POST['m_end_entrega'];
			}
			$m_metodoenvio = 0;
			if (isset($_POST['m_metodoenvio']))
			{
				$m_metodoenvio = $_POST['m_metodoenvio'];
			}
			$m_metodopagamento = 0;
			if (isset($_POST['m_metodopagamento']))
			{
				$m_metodopagamento = $_POST['m_metodopagamento'];
			}

			?>
			<script type="text/javascript">
				function getCheckedValue(radioObj) {
					if (!radioObj) return "";
					var radioLength = radioObj.length;
					if (radioLength == undefined) {
						if (radioObj.checked) return radioObj.value;
						else return "";
					}
					for (var i = 0; i < radioLength; i++) {
						if (radioObj[i].checked) return radioObj[i].value;
					}
					return "";
				}
				function validar_encomenda(go_etapa)
				{

					var validado = true;
					var cor = "#ffffe5";
					var cor2 = "#FFFFFF";
					var campos_obrigatorios = ["m_end_faturacao", "m_end_entrega"];
					var campos_preenchidos;
					var i = 0;
					while (i < campos_obrigatorios.length) {
						if (document.forms['encomendas'].elements[campos_obrigatorios[i]]) document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor = cor2;
						i = i + 1;
					}
					i = 0;
					while (i < campos_obrigatorios.length) {
						if (document.forms['encomendas'].elements[campos_obrigatorios[i]]) {
							if (document.forms['encomendas'].elements[campos_obrigatorios[i]].value == "") {
								campos_preenchidos = 1;
								document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor = cor;
							}
						}
						i = i + 1;
					}
					if (campos_preenchidos == 1) {
						new Apprise("<?php  echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
						validado = false;
						return false;
					}
					if(document.getElementById('metodos_pagamento'))
					{
						var metodo_pagamento=getCheckedValue(document.forms['encomendas'].elements['pagamento']);
						if(metodo_pagamento==""){ Apprise("<?php echo getLinguas('key_shop_msg2',$GLOBALS['site_lang'])?>"); validado=false;return false;}
					}
					if(document.getElementById('metodos_envio'))
					{
						var metodo_envio=getCheckedValue(document.forms['encomendas'].elements['m_metodoenvio']);
						if(metodo_envio==""){ Apprise("<?php echo getLinguas('key_shop_msg2_envio',$GLOBALS['site_lang'])?>"); validado=false;return false;}
					}

					if(validado)
					{
						if(go_etapa == 2)
						{

							if($(".aparelhos_info").length > 0)
							{
								var ttx = 0;
								$(".aparelhos_info").each(function()
								{
									if($('#'+$(this).attr('data')).is(':checked'))
									{
									}
									else
									{
										if($(this).val() == '')	ttx++;
									}
								});




								if(ttx > 0)
								{
									Apprise("Por favor, preencha todos os campos de Imei e Nº de Série");
									return false;
								}
							}


							if(!$("#m_aceitarcompra").is(":checked"))
							{
								Apprise("<?php echo getLinguas('key_shop_termos',$GLOBALS['site_lang'])?>");
								return false;
							}
							else
							{
								document.getElementById('finalizar').value=1;
								document.encomendas.submit();
							}
						}
						else if(go_etapa == 1)
						{
							window.open('<?php echo seo('pages.php?page_id='.intval($_GET['page_id'])).'?etapa=2';?>','_self');
						}
						else
						{
							window.open('<?php echo seo('pages.php?page_id='.intval($_GET['page_id'])).'';?>','_self');
						}
					}
				}

				function changeEndereco(tipo, el, val)
				{
					if (val == -1)
					{
						$(el).show();
					}
					else
					{
						$(el).hide();

						// if (tipo == 1 || tipo == 2)
						// {
							document.encomendas.submit();
						// }
					}
				}
				function validarNovoEndereco(wrapper)
				{
					var cor = "#ffffe5";
					var cor2 = "#FFFFFF";
					var campos_obrigatorios = ["m_nome", "m_apelido", "m_morada", "m_codigo_postal", "m_cidade", "m_pais", "m_telefone", "m_contribuinte"];
					var campos_preenchidos = 0;
					var params = 'novo_endereco=1';

					$(wrapper + ' :input').each(function () {
						if ($.inArray($(this).attr('name'), campos_obrigatorios) > -1) {
							$(this).css('backgroundColor', cor2);
							if ($(this).val() == "") {
								campos_preenchidos = 1;
								$(this).css('backgroundColor', cor);
							}
							else {
								params += "&" + $(this).attr('name') + "=" + $(this).val();
							}
						}
					});

					if (campos_preenchidos == 1) {
						new Apprise("<?php  echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
						return false;
					}
					else {
						// console.log(params);
						$.post('<?php echo  $GLOBALS['site_http_url'] . seo('pages.php?page_id=' . $GLOBALS['cart_reparacoes_purchage_id']);?>', params, function () {
							window.location.href = '<?php echo  $GLOBALS['site_http_url'] . seo('pages.php?page_id=' . $GLOBALS['cart_reparacoes_purchage_id']);?>';
						});
					}
				}
			</script>


			<form action="<?php echo seo('pages.php?page_id=' . $GLOBALS['cart_reparacoes_purchage_id']).(isset($_GET['etapa']) && intval($_GET['etapa']) > 0 ? '?etapa='.intval($_GET['etapa']) : '');; ?>" method="post" name="encomendas" id="encomendas" class="encomendas">
				<input type="hidden" name="finalizar" id="finalizar" value="0"/>
				<input type="hidden" name="submitform" id="submitform" value="0"/>
				<h1 class="titulo_b" style="margin: 0 0 18px;"><?php/*  echo getLinguas('key_shop_title',$GLOBALS['site_lang']); */ ?>Assistências Técnicas </h1>

				<div class="container">
				<div class="row bs-wizard" style="border-bottom:0;">

					<div  onclick="validar_encomenda(0);"  class="col-xs-4 bs-wizard-step <?php echo (isset($_GET['etapa']) && intval($_GET['etapa']) == 2 ? 'complete' : (!isset($_GET['etapa']) ? 'active' : 'disabled')) ?>">
					  <div class="text-center bs-wizard-stepnum">Passo 1</div>
					  <div class="progress"><div class="progress-bar"></div></div>
					  <a href="#" class="bs-wizard-dot"></a>
					  <div class="bs-wizard-info text-center">Confirmação da Encomenda </div>
					</div>

					<div onclick="validar_encomenda(1);" class="col-xs-4 bs-wizard-step <?php echo (isset($_GET['etapa']) && intval($_GET['etapa']) == 2 ? 'active' : 'disabled')?>"><!-- complete -->
					  <div class="text-center bs-wizard-stepnum">Passo 2</div>
					  <div class="progress"><div class="progress-bar"></div></div>
					  <a href="#" class="bs-wizard-dot"></a>
					  <div class="bs-wizard-info text-center">Informações dos Dispositivos<br>(IMEI, Nº de Série)</div>
					</div>

					<div onclick="validar_encomenda(2);" class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
					  <div class="text-center bs-wizard-stepnum">Passo 3</div>
					  <div class="progress"><div class="progress-bar"></div></div>
					  <a href="#" class="bs-wizard-dot"></a>
					  <div class="bs-wizard-info text-center">Finalização e Resumo da Encomenda</div>
					</div>


				</div>
				</div>



				<?php /* <ul class="nav nav-wizard">
					<li onclick="validar_encomenda(0);" <?php echo (!isset($_GET['etapa']) ? 'class="active"' : '')?>><a href="#" >INFORMAÇÕES PARA COMPRA</a></li>
					<li onclick="validar_encomenda(1);" <?php echo (isset($_GET['etapa']) && intval($_GET['etapa']) == 2 ? 'class="active"' : '')?>><a href="#">INFORMAÇÕES DOS DISPOSITIVOS</a></li>
					<li onclick="validar_encomenda(2);"><a href="#">INFORMAÇÕES DA FINALIZAÇÃO DA ENCOMENDA</a></li>
				</ul> */?>
				<br>
				<div class="shop_left_top etapa1" style="display:<?php if(!isset($_GET['etapa'])){ echo 'block'; }else{ echo 'none';}?>">




					<!-- FATURACAO -->
					<h5 class="subtitulo_d_black">Informações de Facturação</h5>
					<p style="font-size:11px">SELECIONE O ENDEREÇO DO SEU LIVRO DE ENDEREÇOS OU INSIRA UM NOVO</p>
					<div class="form-group">

						<?php
						$ef = 0;
						$rs = getEnderecos($d_repados_cliente['cli_id'], 0);
						?>
						<select title="Informações de Facturação"  class="chzn-select" data-live-search="true" onchange="changeEndereco(1, '#faturacao-novo-endereco', this.value)" name="m_end_faturacao" style="width:100%">
						<?php if($rs->EOF()) {?><option value="">Escolha um endereço de faturação</option><?php }?>
							<?php

							while (!$rs->EOF()) {
								$pais = country_name($rs->db_data['Pais']);

								if(!$m_end_faturacao && $ef == 0) echo "<option value=\"{$rs->db_data['ID']}\"  selected='selected'>{$rs->db_data['Nome']} {$rs->db_data['Apelido']}, {$rs->db_data['Morada']}, {$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']}, {$pais}</option>";
								else echo "<option value=\"{$rs->db_data['ID']}\" " . (($m_end_faturacao == $rs->db_data['ID']) ? ' selected="selected" ' : '') . ">{$rs->db_data['Nome']} {$rs->db_data['Apelido']}, {$rs->db_data['Morada']}, {$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']}, {$pais}</option>";

								$ef++;
								$rs->db_next();
							}
							$rs->db_close();
							?>
							<option value="-1">Novo endereço</option>
						</select>
					</div>
					<div id="faturacao-novo-endereco" style="display:none">
						<?php shopShowFormNovoEndereco('#faturacao-novo-endereco'); ?>
					</div>
					<hr>
					<!-- ENVIO -->
					<h5 class="subtitulo_d_black">Informações de Envio</h5>
					<p style="font-size:11px">SELECIONE O ENDEREÇO DO SEU LIVRO DE ENDEREÇOS OU INSIRA UM NOVO</p>
					<div class="form-group">
						<?php
						$ef = 0;
						$rs = getEnderecos($d_repados_cliente['cli_id'], 0);
						?>
						<select title="Informações de Envio"  class="chzn-select" data-live-search="true" onchange="changeEndereco(2, '#envio-novo-endereco', this.value)" name="m_end_entrega"  style="width:100%;height:30px">
						<?php if($rs->EOF()) {?><option value="">Escolha um endereço de envio</option><?php }?>
							<?php
							while (!$rs->EOF())
							{
								$pais = country_name($rs->db_data['Pais']);

								if ($m_end_entrega == $rs->db_data['ID']) $m_pais = $rs->db_data['Pais'];

								if(!$m_end_entrega) echo "<option value=\"{$rs->db_data['ID']}\"  selected='selected'>{$rs->db_data['Nome']} {$rs->db_data['Apelido']}, {$rs->db_data['Morada']}, {$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']}, {$pais}</option>";
								else echo "<option value=\"{$rs->db_data['ID']}\" " . (($m_end_entrega == $rs->db_data['ID']) ? ' selected="selected" ' : '') . ">{$rs->db_data['Nome']} {$rs->db_data['Apelido']}, {$rs->db_data['Morada']}, {$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']}, {$pais}</option>";

								$ef++;
								$rs->db_next();
							}
							$rs->db_close();
							?>
							<option value="-1">Novo endereço</option>
						</select>
					</div>
					<div id="envio-novo-endereco" style="display:none">
						<?php shopShowFormNovoEndereco('#envio-novo-endereco'); ?>
					</div>

					<hr>
					<?php

					//calcula encomenda com produtos e com vouchers
					$totalEncomenda = calcularTotalEncomenda_reparacoes();
					/* Expedição */


					$portes = 0;
					// $metodoEnvioObj = getEncomendaMetodoExpedicao($m_metodoenvio);
					// print_r($metodoEnvioObj);
					if ($metodoEnvioObj = getEncomendaMetodoExpedicao($m_metodoenvio, 2))
					{
						// echo 1;
						$m_metodoenvio = $metodoEnvioObj['ID'];
						showLayoutMetodosExpedicao($m_metodoenvio, 2);

						$portes = getPortesCarrinho($m_metodoenvio, $m_pais);
						$valor_portes_gratuitos = getPortesGratuitos();

						if ($totalEncomenda['TOTALCIVA'] >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0) {
							$portes = 0;
						}
					}

					/* Pagamento */
					$taxas = 0;
					if ($metodoPagamentoObj = getEncomendaMetodoPagamento($m_metodopagamento))
					{
						$m_metodopagamento = $metodoPagamentoObj['ID'];

						if($metodoPagamentoObj['metodo_taxa_tipo'] == 1)
						{
							$metodo_taxa2 = calcula_desconto($totalEncomenda['TOTALCIVA'], $metodoPagamentoObj['Taxa']);
							$taxas += $metodo_taxa2;
						}
						else
						{
							$taxas += $metodoPagamentoObj['Taxa'];
						}


						showLayoutMetodosPagamento($m_metodopagamento);
					}


					//TODO FALTA

					/* LISTA DE PRODUTOS */
					linhas_encomenda_reparacoes($totalEncomenda, $n_enc_reparacoes, $shop_reparacoes, $portes, $taxas);


					// $offer_arr = showSalesPromotions_offer($GLOBALS['proms']);
					// if ($offer_arr['p_html_offers'])
					// {
						// echo $offer_arr['p_html_offers'];
					// }
					?>





					<div class="clear" style="clear:both"></div>
					<div class="row">
					<div class="col-md-3 text-center">&nbsp;</div>
					<div class="col-md-3 text-center"><button type="button" class="button3" onclick="ImprimeHtml('shopping-cart')">IMPRIMIR FICHA</button></div>
					<div class="col-md-3 text-center"><input type="button" class="button3" value="PRÓXIMA ETAPA" onclick="<?php if($d_repados_cliente){?>validar_encomenda(1);<?php }else{?>Apprise('<?php echo getLinguas('key_pleaselogin');?>');<?php }?>"></div>
					<div class="col-md-3 text-center">&nbsp;</div>
					</div>

				</div>

				<div class="shop_left_top etapa2" style="display:<?php if(isset($_GET['etapa']) && intval($_GET['etapa']) == 2){ echo 'block'; }else{ echo 'none';}?>">
				<?php linhas_encomenda_reparacoes_campos($totalEncomenda, $n_enc_reparacoes, $shop_reparacoes, $portes, $taxas);?>
				<div class="clear" style="clear:both"></div>
				<div class="form-group">
						<div class="checkbox">
							<label for="m_aceitarcompra">
								<input type="checkbox" id="m_aceitarcompra" name="m_aceitarcompra" <?php if (isset($_POST['m_aceitarcompra'])) {
									echo ' checked="checked" ';
								} ?>> Aceito as condições de compra
								<a href="<?php echo seo('pages.php?page_id=37'); ?>">(Ver Condições de Pagamento)</a>
							</label>
						</div>
				</div>
				<div class="clear" style="clear:both"></div>
				<div class="row">
				<div class="col-md-3 text-center">&nbsp;</div>
				<div class="col-md-3 text-center"><button type="button" class="button3" onclick="validar_encomenda(0);">VOLTAR AO CARRINHO</button></div>
				<div class="col-md-3 text-center"><input type="button" class="button3" value="ENCOMENDAR" onclick="validar_encomenda(2);"></div>
				<div class="col-md-3 text-center">&nbsp;</div>
				</div>
				</div>
			</form>
			<form id="codigo_pro"><input type="hidden" value="" name="voucher_add" id="promo_voucher2"></form>
			<?php
		}
		else
		{
			?><form action="<?php echo seo('pages.php?page_id=' . $GLOBALS['cart_reparacoes_purchage_id']); ?>" method="post" name="encomendas" id="encomendas"><?php
			$totalEncomenda = calcularTotalEncomenda_reparacoes();
			linhas_encomenda_reparacoes($totalEncomenda, $n_enc_reparacoes, $shop_reparacoes);

			if(!is_array($d_repados_cliente)){
			?>
				<div class="clear" style="clear:both"></div>
				<div class="row">
				<div class="col-md-3 text-center">&nbsp;</div>
				<div class="col-md-3 text-center"><button type="button" class="button3" onclick="ImprimeHtml('shopping-cart')">Imprimir Carrinho</button></div>
				<div class="col-md-3 text-center"><button type="button" class="button3" onclick="window.open('<?php echo seo('pages.php?page_id='.$GLOBALS['registo_page_id']);?>','_self')">Finalizar Encomenda</button></div>
				<div class="col-md-3 text-center">&nbsp;</div>
				</div>

			<?php
			}
			?></form><?php
		}
    }
	else
	{
        echo show_alerta2('<img src="layout/' . PROJETO . '/images/icon-empty-cart.png" alt="" />');
    }
}
function calcularTotalEncomenda_reparacoes()
{
    $n_enc = $GLOBALS['n_enc_reparacoes'];
    $shop = $GLOBALS['shop_reparacoes'];
    $n_voucher = $GLOBALS['n_voucher'];
    $voucher = $GLOBALS['voucher'];

    $descontos = 0;
    $totalsiva = 0;
    $totalciva = 0;
    $totaliva = 0;
    $result = array('TOTALIVA' => $totaliva, 'TOTALCIVA' => $totalciva, 'TOTALSIVA' => $totalsiva, 'DESCONTOS' => $descontos);

    $i = 1;
    $found = 0;

    while ($i <= $n_enc && $found == 0)
	{
		$prod_id = $shop[$i][1];
		$qtd = $shop[$i][2];
		$tipo = $shop[$i][3];
		$chave = $shop[$i][4];
		$prod_obj = $shop[$i][5];
		$opcoes = $shop[$i][6];
		$opcoes_id = 0;
		$opcoes_id = $shop[$i][7];

		$info = get_produtc_info($prod_id);
		if ($opcoes_id > 0) $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);

        $valorqtd = $info['prod_precos']['PVP'] * $qtd;

        // PROMOCOES QUE O PRODUTO TEM
        $arr_prom = showSalesPromotions($GLOBALS['proms'], $prod_id, $valorqtd);

        $iva = $info['prod_precos']['IVA'];
        if ($iva <= 0) {
            $iva = $GLOBALS['iva_default'];
        }

        if ($GLOBALS['produtos_com_iva'] == 1)
		{
            if ($arr_prom['p_valor'] > 0) {
                $totalsiva += $arr_prom['p_valor'];
            } else {
                $totalsiva += $valorqtd;
            }

            if ($iva > 0) {
                $iva = 1 + (floatval($iva) / 100);
                if ($arr_prom['p_valor'] > 0) {
                    $pciva = (($arr_prom['p_valor']) * $iva);
                } else {
                    $pciva = ($info['prod_precos']['PVP'] * $iva);
                }
            } else {
                $iva = 1 + (floatval($GLOBALS['iva_default']) / 100);
                if ($arr_prom['p_valor'] > 0) {
                    $pciva = (($arr_prom['p_valor']) * $iva);
                } else {
                    $pciva = ($info['prod_precos']['PVP'] * $iva);
                }
            }

            $totalciva += $pciva;
        } else {
            if ($arr_prom['p_valor'] > 0) {
                $totalciva += $arr_prom['p_valor'];
            } else {
                $totalciva += $valorqtd;
            }

            if ($iva > 0) {
                $iva = 1 + (floatval($iva) / 100);
                if ($arr_prom['p_valor'] > 0) {
                    $psiva = (($arr_prom['p_valor']) / $iva);
                } else {
                    $psiva = ($info['prod_precos']['PVP'] / $iva);
                }
            } else {
                $iva = 1 + (floatval($GLOBALS['iva_default']) / 100);
                if ($arr_prom['p_valor'] > 0) {
                    $psiva = (($arr_prom['p_valor']) / $iva);
                } else {
                    $psiva = ($info['prod_precos']['PVP'] / $iva);
                }
            }

            $totalsiva += $psiva;
        }

        $i++;
    }
    $totaliva = $totalciva - $totalsiva;
    $result['TOTALIVA'] = $totaliva;
    $result['TOTALCIVA'] = $totalciva;
    $result['TOTALSIVA'] = $totalsiva;

    $i = 1;
    $found = 0;
    while ($i <= $n_voucher && $found == 0)
	{
        $voucher_code = $voucher[$i][1];
        $valor_dif = 0;
        $info = get_voucher_info($voucher_code);
        if ($info['voucher_type'] != 1)
		{
            $valor_dif = calcula_desconto($totalciva, $info['voucher_discount']);
        }

        if ($info['voucher_type'] == 0 || $info['voucher_type'] == 2 || $info['voucher_type'] == 3)
		{
            $descontos += $valor_dif;
        }

        $i++;
    }

    //TODO ACRESCENTAR OS PONTOS

    $result['DESCONTOS'] = $descontos;


    return $result;
}
function linhas_encomenda_reparacoes($totalEncomenda, $n_enc_reparacoes, $shop_reparacoes, $portes=0, $taxas=0)
{

	// $totalEncomenda = calcularTotalEncomenda();
	?>
	<script>

	function removerProdutoCart_reparacoes(chave) {
		$.ajax({
			type: "GET",
			url: "<?php echo seo('pages.php?page_id=' . $GLOBALS['cart_reparacoes_purchage_id'])?>",
			data: {del_reparacoes: chave}
		}).done(function () {
			document.encomendas.submit();
		});
	}
	function spinnerUpShop_reparacoes(id,prodid, i)
	{
		$.ajax({
			type: "GET",
			url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id'])?>",
			data: { prod_add_reparacoes: prodid, prod_add_reparacoes_obj: prodid, prod_add_reparacoes_qtd:1, pos:i }
		}).done(function(){
			document.encomendas.submit();
		});
	}
	function spinnerDownShop_reparacoes(id,prodid, i)
	{
		if(parseInt($("#"+id).val())<=1){
			$("#"+id).val(1);
		}else{
			$.ajax({
				type: "GET",
				url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id'])?>",
				data: { prod_add_reparacoes: prodid,prod_add_reparacoes_obj: prodid,prod_add_reparacoes_qtd:-1, pos:i}
			}).done(function(){
				document.encomendas.submit();
			});

		}
	}
	<?php if(isset($_POST['submitform']) && $_POST['submitform'] > 0){?>$(document).ready(function () {  if ($(".encomendas").length > 0) window.scrollTo(0, <?php echo $_POST['submitform'];?>);});<?php }?>
	</script>

	<h5 class="subtitulo_d_black">O seu carrinho de compras</h5><br>
	<div class="clear"></div>
	<div class="shopping-cart" id="shopping-cart">

		<div class="column-labels">
		<label class="product-image"><?php echo getLinguas('key_imagem',$GLOBALS['site_lang']) ?></label>
		<label class="product-details"><?php echo getLinguas('key_produto',$GLOBALS['site_lang']) ?></label>
		<label class="product-price"><?php echo getLinguas('key_preco',$GLOBALS['site_lang']) ?></label>
		<label class="product-quantity"><?php echo getLinguas('key_quantidade',$GLOBALS['site_lang']) ?></label>
		<label class="product-removal"><?php echo getLinguas('key_remover',$GLOBALS['site_lang']) ?></label>
		<label class="product-line-price"><?php echo getLinguas('key_total',$GLOBALS['site_lang']) ?></label>
		</div>
		<?php
		$i = 1;
		$found = 0;

		while ($i <= $n_enc_reparacoes && $found == 0)
		{

			$prod_id = $shop_reparacoes[$i][1];
			$qtd = $shop_reparacoes[$i][2];
			$tipo = $shop_reparacoes[$i][3];
			$chave = $shop_reparacoes[$i][4];
			$prod_obj = $shop_reparacoes[$i][5];
			$opcoes = $shop_reparacoes[$i][6];
			$opcoes_id = 0;
			$opcoes_id = $shop_reparacoes[$i][7];

			// echo $opcoes_id .'xxx<br>';

			$info = get_produtc_info($prod_id);
			if ($opcoes_id > 0) $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);



			$valorqtd = $info['prod_precos']['PVP'] * $qtd;
			$oferta = 0;

			// PROMOCOES QUE O PRODUTO TEM
			$arr_prom = showSalesPromotions($GLOBALS['proms'], $prod_id, $valorqtd);

		?>
			<div class="product">
			<div class="product-image">
			<img src="<?php echo $info['prd_imgm_cart']; ?>" <?php echo (substr_count($info['prd_imgm_cart'], 'no_photo_cart.png') ? 'style="height:100px"' : '' ); ?>>
			</div>
			<div class="product-details">
			<div class="product-title"><?php echo $info['prod_ref']; ?></div>
			<p class="product-description"><?php echo rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'] . ', ' . $opcoes : $info['prod_nome']),','); ?></p>
			</div>
			<div class="product-price"><?php echo numberformat($info['prod_precos']['PVP'], 2) ?></div>
			<div class="product-quantity">
			<div class="produto_qtd">
			<input class="HvMd"  name="cart_quantity[]" id="prod_qtd<?php echo $i?>" value="<?php echo $qtd?>"  style="float:left;padding:0;text-indent:0;text-align:center" readonly="readonly" />
			<div class="spinnerwrapper">
			<div class="spinnerup" onclick="spinnerUpShop_reparacoes('prod_qtd<?php echo $i?>','<?php echo $prod_id?>','<?php echo $i?>');"></div>
			<div class="spinnerdown" onclick="spinnerDownShop_reparacoes('prod_qtd<?php echo $i?>','<?php echo $prod_id?>','<?php echo $i?>');"></div>
			</div>
			<input type="hidden" name="products_id[]" value="<?php echo $prod_id?>" />
			</div>
			</div>
			<div class="product-removal">
			<button class="remove-product" onclick="removerProdutoCart_reparacoes('<?php echo $chave?>');return false;">X</button>
			</div>
			<div class="product-line-price"><?php echo numberformat($info['prod_precos']['PVP'] * $qtd, 2) ?></div>

		<?php
			if ($arr_prom['p_html'])
			{
				echo $arr_prom['p_html'];
			}
			if ($oferta)
			{
				$infooferta = get_produtc_info($oferta);
				?>
				<div class="product">
				<div class="product-image">
				&nbsp;
				</div>
				<div class="product-details">
				<div class="product-title"><?php echo $infooferta['prod_ref']; ?></div>
				<p class="product-description"><?php echo $infooferta['prod_nome']; ?></p>
				</div>
				<div class="product-price"><?php echo numberformat(0, 2) ?></div>
				<div class="product-quantity">
				&nbsp;
				</div>
				<div class="product-removal">
				<button class="remove-product">
				Remove
				</button>
				</div>
				<div class="product-line-price">Oferta</div>
				</div>

				<?php
			}
			?></div><?php
			$i++;
		}
		?>
		<div class="totals">
		<div class="totals-item">
		<label>Subtotal</label>
		<div class="totals-value" id="cart-subtotal"><?php echo numberformat($totalEncomenda['TOTALSIVA'], 2); ?></div>
		</div>
		<div class="totals-item">
		<label>Portes</label>
		<div class="totals-value" id="cart-shipping"><?php echo numberformat($portes, 2); ?></div>
		</div>
		<div class="totals-item">
		<label>Taxas</label>
		<div class="totals-value" id="cart-tax"><?php echo numberformat($taxas, 2) ?></div>
		</div>
		<div class="totals-item">
		<label>IVA</label>
		<div class="totals-value" id="cart-tax"><?php echo numberformat($totalEncomenda['TOTALIVA'], 2) ?></div>
		</div>
		<div class="totals-item totals-item-total">
		<label>Total</label>
		<div class="totals-value" id="cart-total"><?php echo numberformat(($totalEncomenda['TOTALCIVA'] - $totalEncomenda['DESCONTOS']) + $portes + $taxas, 2); ?></div>
		</div>
		</div>
	</div>
	<?php
}
function linhas_encomenda_reparacoes_campos($totalEncomenda, $n_enc_reparacoes, $shop_reparacoes, $portes=0, $taxas=0)
{
	?>
	<h5 class="subtitulo_d_black">Por favor preencha os seguintes campos com os dados dos seus aparelhos</h5><br>
	<div class="clear"></div>
	<div class="shopping-cart" id="shopping-cart">

		<?php
		$i = 1;
		$found = 0;
		$x = 0;

		while ($i <= $n_enc_reparacoes && $found == 0)
		{

			$prod_id = $shop_reparacoes[$i][1];
			$qtd = $shop_reparacoes[$i][2];
			$tipo = $shop_reparacoes[$i][3];
			$chave = $shop_reparacoes[$i][4];
			$prod_obj = $shop_reparacoes[$i][5];
			$opcoes = $shop_reparacoes[$i][6];
			// $opcoes_id = 0;
			// $opcoes_id = $shop_reparacoes[$i][7];

			// echo $opcoes_id .'xxx<br>';

			$info = get_produtc_info($prod_id);

			for($a=0;$a<$qtd;$a++)
			{
				$x++;
			?>
				<div class="row" style="border-bottom:1px solid #000;margin-bottom:10px;padding-bottom:10px">
					<div class="col-md-12">
						<div class="form-group">
							<div class="product-title"><label><?php echo $x.' - '.$info['prod_ref']; ?></label></div>
							<p class="product-description"><label style="padding-left:20px"><?php echo rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'] . ', ' . $opcoes : $info['prod_nome']),','); ?></label></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="m_imei_<?php echo $prod_id?>_<?php echo $a?>">Imei/Nº Serie <span class="mandatory">*</span></label>
							<input <?php echo (isset($_POST['bloqeio_'.$prod_id.'_'.$a]) && $_POST['bloqeio_'.$prod_id.'_'.$a]!='' ? 'readonly=""' : '');?> id="m_imei_<?php echo $prod_id?>_<?php echo $a?>" data="bloqeio_<?php echo $prod_id?>_<?php echo $a?>" value="<?php echo (isset($_POST['m_imei_'.$prod_id.'_'.$a]) && $_POST['m_imei_'.$prod_id.'_'.$a]!='' ? $_POST['m_imei_'.$prod_id.'_'.$a] : '');?>" maxlength="255" autocomplete="off" value="" name="m_imei_<?php echo $prod_id?>_<?php echo $a?>" class="form-control aparelhos_info" style="" type="text">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group" style="vertical-align:top"><br>
							<label for="bloqeio_<?php echo $prod_id?>_<?php echo $a?>"  class="lbl_check" style="width:auto;padding-left:20px;margin-top:10px">
							<input <?php echo (isset($_POST['bloqeio_'.$prod_id.'_'.$a]) && $_POST['bloqeio_'.$prod_id.'_'.$a]!='' ? 'checked="checked"' : '');?> onclick="if($('#bloqeio_<?php echo $prod_id?>_<?php echo $a?>').is(':checked')){$('#m_imei_<?php echo $prod_id?>_<?php echo $a?>').attr('readonly', true);}else{$('#m_imei_<?php echo $prod_id?>_<?php echo $a?>').attr('readonly', false);}" id="bloqeio_<?php echo $prod_id?>_<?php echo $a?>" value="1" maxlength="255" autocomplete="off" value=""  name="bloqeio_<?php echo $prod_id?>_<?php echo $a?>" class="form-control" style="width:auto;height:auto;display:inline-block" type="checkbox"> <span style="display:inline-block;color:#000">Não sei o Imei / Nº Serie</span>
							</label>
						</div>
					</div>
					<div class="clear"></div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="m_serie_<?php echo $prod_id?>_<?php echo $a?>">Coloque o código de segurança ou remova os dados de bloqueio</label>
							<input onfocus="$('#t_m_serie_<?php echo $prod_id?>_<?php echo $a?>').show();" id="m_serie_<?php echo $prod_id?>_<?php echo $a?>" value="<?php echo (isset($_POST['m_serie_'.$prod_id.'_'.$a]) && $_POST['m_serie_'.$prod_id.'_'.$a]!='' ? $_POST['m_serie_'.$prod_id.'_'.$a] : '');?>" maxlength="255" autocomplete="off" value="" name="m_serie_<?php echo $prod_id?>_<?php echo $a?>" class="form-control" style="" type="text">
							<div id="t_m_serie_<?php echo $prod_id?>_<?php echo $a?>" style="display:none;float:left;text-align:center"><div style="text-align:right;padding:10px 0;cursor:pointer" onclick="$('#t_m_serie_<?php echo $prod_id?>_<?php echo $a?>').hide();">Fechar</div><div id="a_m_serie_<?php echo $prod_id?>_<?php echo $a?>"></div></div>
							<div class="clear"></div>
						</div>
					</div>
					<script>

					var lock = new PatternLock('#a_m_serie_<?php echo $prod_id?>_<?php echo $a?>',{allowRepeat : true, radius:10,margin:15, onDraw:function(pattern){$('#m_serie_<?php echo $prod_id?>_<?php echo $a?>').val(pattern);}});
					</script>
					<div class="col-md-12">
						<div class="form-group">
							<label for="m_obs_<?php echo $prod_id?>_<?php echo $a?>">Observações</label>
							<textarea id="m_obs_<?php echo $prod_id?>_<?php echo $a?>" autocomplete="off" name="m_obs_<?php echo $prod_id?>_<?php echo $a?>" class="form-control"><?php echo (isset($_POST['m_obs_'.$prod_id.'_'.$a]) && $_POST['m_obs_'.$prod_id.'_'.$a]!='' ? $_POST['m_obs_'.$prod_id.'_'.$a] : '');?></textarea>
						</div>
					</div>
					<div class="col-md-12 text-center"><input type="button" class="button3" value="GUARDAR" onclick="document.encomendas.submit();"></div>
				</div>

			<?php
			}
			$i++;
		}
	?></div><?php
}
function carrinho_small3_reparacoes($tipo = 'qtd')
{
    global $shop_reparacoes;

    if ($GLOBALS['n_enc_reparacoes'] > 0)
	{
        $total = 0;
        if ($tipo == 'qtd')
		{
            $qtd = 0;
            for ($p = 1; $p <= count($shop_reparacoes); $p++)
			{
                $qtd += $shop_reparacoes[$p][2];
            }
            echo '<span class="itensnum2">'.$qtd.'</span>';
        }
		elseif ($tipo == 'qtd2')
		{
            $qtd = 0;
            for ($p = 1; $p <= count($shop_reparacoes); $p++)
			{
                $qtd += $shop_reparacoes[$p][2];
            }
            echo $qtd;
        }
		elseif ($tipo == 'valor')
		{
            $i = 1;
            $found = 0;
            $portes_pagar = 0;
            while ($i <= $GLOBALS['n_enc_reparacoes'] && $found == 0)
			{
                $prod_id = $shop_reparacoes[$i][1];
                $qtd = $shop_reparacoes[$i][2];
                $opcoes = $shop_reparacoes[$i][6];
                $opcoes_id = 0;
                $opcoes_id = $shop_reparacoes[$i][7];

                $info = get_produtc_info($prod_id);
                if ($opcoes_id > 0)
				{
                    $info['prod_precos'] = get_produtc_price_combo($prod_id, $opcoes_id);
                }

                $total += ($info['prod_precos']['PVP'] * $qtd);
                $i++;
            }

            echo numberformat($total);
        }

    }
	else
	{
        if ($tipo == 'qtd')
		{
            echo '<span class="itensnum2">0</span>';
        }
		elseif ($tipo == 'qtd2')
		{
            echo '0';
        }
		elseif ($tipo == 'valor')
		{
            echo numberformat(0);
        }
    }
}