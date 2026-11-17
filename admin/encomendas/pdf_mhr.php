<?php
	include "settings.inc.php";

	require_once("vendor/autoload.php");

	ini_set('display_errors', 1);
    ini_set('display_startup_errors',1);
    error_reporting(0);


	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$module_voucher;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	// var_dump($GLOBALS['persist']);
	// die();

	if($ID != 0)
	{
		$sql = "SELECT * FROM encomendas WHERE ID=$ID";
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$IDb=$rs->db_data["ID"];
			$Referencia=$rs->db_data["Referencia"];
			$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
			$Metodo_expedicao=$rs->db_data["Metodo_envio"];

			$Cond_pag=$rs->db_data["Cond_pag"];
			$Local_Entrega=$rs->db_data["Local_Entrega"];
			$Tipo=$rs->db_data["Tipo"];
			$Anulada=$rs->db_data["Anulada"];
			$RazaoAnulacao=$rs->db_data["RazaoAnulacao"];
			$ID_Cliente=$rs->db_data["ID_Cliente"];
			$N_Contribuinte=$rs->db_data["N_Contribuinte"];
			$Des_Social=$rs->db_data["Des_Social"];
			$Contacto=$rs->db_data["Contacto"];
			$Morada=$rs->db_data["Morada"];
			$Localidade=$rs->db_data["Localidade"];
			$Cod_Pos=$rs->db_data["Cod_Pos"];
			$MoradaEntrega=$rs->db_data["MoradaEntrega"];
			$LocalidadeEntrega=$rs->db_data["LocalidadeEntrega"];
			$Cod_PosEntrega=$rs->db_data["Cod_PosEntrega"];
			$telefone=$rs->db_data["telefone"];
			$email=$rs->db_data["email"];
			$Obs=$rs->db_data["Obs"];
			$Portes=$rs->db_data["Portes"];
			$PortesSemIva=$rs->db_data["PortesSemIva"];
			$IvaPortes=$rs->db_data["IvaPortes="];
			$Taxas=$rs->db_data["Taxas"];
			$TaxasSemIva=$rs->db_data["TaxasSemIva"];
			$Cobrar=$rs->db_data["Cobrar"];
			$TaxasPaypal=$rs->db_data["TaxasPaypal"];
			$PercDesconto=$rs->db_data["PercDesconto="];
			$ValorDesconto=$rs->db_data["ValorDesconto"];
			$ID_Vendedor=$rs->db_data["ID_Vendedor"];
			$ID_Distribuidor=$rs->db_data["ID_Distribuidor"];
			$PropostaDe=$rs->db_data["PropostaDe"];
			$ID_Armazem=$rs->db_data["ID_Armazem"];
			$Local_carga=$rs->db_data["Local_carga"];
			$Local_descarga=$rs->db_data["Local_descarga"];
			$Expedicao=$rs->db_data["Expedicao"];
			$Viatura_expedicao=$rs->db_data["Viatura_expedicao"];
			$ID_Expedicao=$rs->db_data["ID_Expedicao"];
			$IDGuia=$rs->db_data["IDGuia"];
			$Operador=$rs->db_data["Operador"];
			$ValorDescontoExtra=$rs->db_data["ValorDescontoExtra"];
			$ValorJustificado=$rs->db_data["ValorJustificado"];
			$TotalDocComIva=$rs->db_data["TotalDocComIva"];
			$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
			$TotalDocDescontos=$rs->db_data["TotalDocDescontos"];
			$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
			$TotalDocIva=$rs->db_data["TotalDocIva"];
			$Data_Criacao=$rs->db_data["Data_Criacao"];
			$Data_Impressao=$rs->db_data["Data_Impressao"];
			$Data_Doc_Forn=$rs->db_data["Data_Doc_Forn"];
			$Data_expedicao=$rs->db_data["Data_expedicao"];
			$Data_entrega=$rs->db_data["Data_entrega"];
			$Estado=$rs->db_data["Estado"];
			$m_guia=$rs->db_data["IDGuia"];
			$Encomenda_Paga = $rs->db_data["Encomenda_Paga"];
			$enc_Fatura = $rs->db_data["enc_Fatura"];
			$Encomenda_Status = $rs->db_data["Encomenda_Status"];
			$ATM = base64_decode($rs->db_data["ATM"]);
			$Nota_privada = $rs->db_data["Nota_privada"];

			if($TotalDocIva==0)$TotalDocIva=$TotalDocComIva-$TotalDocSemIva;

			$enc_pagamento_dados = $rs->db_data["enc_pagamento_dados"];

			// DADOS DE ENTREGA E FATURACAO
			$enc_Nome = $rs->db_data['enc_Nome'];
			$enc_Nome_ent = $rs->db_data['enc_Nome_ent'];
			$enc_Apelido = $rs->db_data['enc_Apelido'];
			$enc_Apelido_ent = $rs->db_data['enc_Apelido_ent'];
			$enc_Morada = $rs->db_data['enc_Morada'];
			$enc_Morada_ent = $rs->db_data['enc_Morada_ent'];
			$enc_Cidade = $rs->db_data['enc_Cidade'];
			$enc_Sexo = $rs->db_data['enc_Sexo'];
			$enc_DataNascimento = $rs->db_data['enc_DataNascimento'];
			$enc_Cidade_ent = $rs->db_data['enc_Cidade_ent'];
			$enc_Codigo_Postal = $rs->db_data['enc_Codigo_Postal'];
			$enc_Codigo_Postal_ent = $rs->db_data['enc_Codigo_Postal_ent'];

			$enc_Pais = $rs->db_data['enc_Pais'];
			$enc_Pais = country_name($enc_Pais); // NOME

			$enc_Pais_ent = $rs->db_data['enc_Pais_ent'];
			$enc_Pais_ent = country_name($enc_Pais_ent); // NOME

			$enc_Telefone = $rs->db_data['enc_Telefone'];
			$enc_Telefone_ent = $rs->db_data['enc_Telefone_ent'];
			$enc_Email = $rs->db_data['enc_Email'];
			$enc_Email_ent = $rs->db_data['enc_Email_ent'];

			$enc_Nif = $rs->db_data['enc_Nif'];
			$enc_Nif_ent = $rs->db_data['enc_Nif_ent'];
			$enc_empresa = $rs->db_data['enc_empresa'];
			$enc_empresa_ent = $rs->db_data['enc_empresa_ent'];

			$taxas = $rs->db_data['Taxas'];

			$txt_traking=$rs->db_data['IDGuia'];

			$portes_taxa = $rs->db_data['IvaPortes'];
			//$total_portes_com_iva = $rs->db_data['total_portes_com_iva'];
			//$total_portes_sem_iva = $rs->db_data['total_portes_sem_iva'];
			$total_portes_com_iva = $rs->db_data['Portes'];
			$total_portes_sem_iva = $rs->db_data['PortesSemIva'];

			//Especificos Website
			$embrulhar 			= $rs->db_data['embrulhar'];
			$embrulho_msg		= $rs->db_data['embrulho_msg'];
		}
		$rs->db_close();
	}

	$Metodo_pagamento_txt=getNomeMetodoPagamentoByTipo_bo($Metodo_pagamento);
	$Metodo_expedicao_txt=getNomeMetodoExpedicao_bo($Metodo_expedicao);


	$sql = "SELECT Peso FROM encomendas_produtos WHERE ID_PAI=$ID Order By Estado=8";
	$rs->db_open($db_link, $DB_name, $sql);
	$peso=0;
	while(!$rs->EOF())
	{
		$peso += $rs->db_data["Peso"];
		$rs->db_next();
	}
	$rs->db_close();




	$txt_peso=number_format($peso).' Kg';
	$txt_entrega=$Metodo_expedicao_txt;


	$html='<style>html body td{font-size:14px;font-family:arial}</style>

		<table border=0 cellpadding=0 cellspacing=0 align="center">
        <tr>
            <td valign="top" align="left">
                <table style="background:#FFF;width:100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top" align="left">
                    	<table style="width:767px" border="0" cellpadding="0" cellspacing=0>
                    	<tr>
                    		<td><img src="../../layout/'.PROJETO.'/logos/logo_top_resp.png"></td>
                    		<td align="right" style="font-size:18px;color:#9e9fa2;font-size:14px;font-family:arial">Encomenda<br>'.$Referencia.' - '.$ID.'</td>
                		</tr>
                		</table>

                		<table style="width:100%" border="0" cellpadding="0" cellspacing=0>
                        <tr>
                        	<td valign="top">
                                <table style="width:50px" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td colspan="2" align="left" height="60"><strong style="font-size:13px;font-family:arial">Endereço de Faturação</strong></td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Nif.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Nome.' '.$enc_Apelido.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Morada.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Cidade.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Codigo_Postal.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Pais.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Telefone.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Email.'</td>
                                </tr>
                                </table>
							</td>
                            <td valign="top" width="50%">
                                <table style="width:50%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                	<td colspan="2" align="left" height="60"><strong style="font-size:13px;font-family:arial">Endereço de Entrega</strong></td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Nome_ent.' '.$enc_Apelido_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Morada_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Cidade_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Codigo_Postal_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Pais_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Telefone_ent.'</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-size:14px;font-family:arial">'.$enc_Email_ent.'</td>
                                </tr>
                                </table>
							</td>

                        </tr>
                        </table>

                        <br><br>
                        <table style="width:100%;border: solid 1px #ccc" border="0" cellpadding="2" cellspacing=0>
                        <tr>
                        	<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Encomenda:</td>
                        	<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Data Encomenda:</td>
                        	<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Método de Pagamento:</td>
                        </tr>
                        <tr>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$Referencia.' - '.$ID.'</td>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$Data_Criacao.'</td>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$Metodo_pagamento_txt.'</td>
                        </tr>
                        </table>
                        <br>
                        <table style="width:100%;border: solid 1px #ccc" border="0" cellpadding="2" cellspacing=0>
                        <tr>
                        	<td colspan="3" style="background-color:#cccccc;text-align:center;font-weight:bold;font-size:14px;font-family:arial">Expedição</td>
                        </tr>
                        <tr>
                        	<td style="background-color:#eeeeee;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Peso Total:</td>
                        	<td style="background-color:#eeeeee;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Entrega:</td>
                        	<td style="background-color:#eeeeee;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Tracking:</td>
                        </tr>
                        <tr>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$txt_peso.'</td>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$txt_entrega.'</td>
                        	<td style="background-color:#fff;text-align:center;font-size:14px;font-family:arial">'.$txt_traking.'</td>
                        </tr>
                        </table>
                        <br>

                        <br />
                    	<table style="width:767px" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td align="left" width="20%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Referência</td>
                            <td align="left" width="30%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Produto</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Preço S/Iva</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Preço C/Iva</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Desconto</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Qtd</td>
							<td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial;text-align:center">Total C/Iva</td>
                        </tr>';

						$sql = "SELECT * FROM encomendas_produtos WHERE ID_PAI=$ID Order By Estado=8";
						$rs->db_open($db_link, $DB_name, $sql);
						$n=0;
						while(!$rs->EOF())
						{

							$IDb = $rs->db_data["ID"];
							// echo $IDb.'<br>';
							$ID_Cliente = $rs->db_data["ID_Cliente"];
							$ID_Produto = $rs->db_data["ID_Produto"];
							$Ref = $rs->db_data["Ref"];

							/*$rs2->db_open($db_link, $DB_name, "SELECT Ref_Ean FROM products WHERE ID=$ID_Produto limit 0,1");
							$EAN = $rs2->db_data["Ref_Ean"];
							$rs2->db_close();*/

							$Nomes = explode('|', $rs->db_data["Nome"]);
							$Nome = $Nomes[0];
							$Nome_opcoes = str_replace('[br]', '<br>', $rs->db_data["Nome_opcoes"]);
							$Email_opcoes = ''; $Email_opcoes = $rs->db_data["Email_opcoes"];
							$Texto_opcoes = ''; $Texto_opcoes = $rs->db_data["Texto_opcoes"];
							$Qtd = $rs->db_data["Qtd"];
							$Tamanho = $rs->db_data["Tamanho"];
							$Cor = $rs->db_data["Cor"];
							$Valor = $rs->db_data["Valor"];
							$Valor_Pagar = $rs->db_data["Valor"];//Valor unitario sem iva
							$Valor_Desconto = $rs->db_data["Valor_Desconto"];
							$Tipo = $rs->db_data["Tipo"];
							$m_estado = $rs->db_data["Estado"];
							$enc_state =  estado_encomenda($m_estado, 'pt');
							//$cor_texto = str_replace('#', '', color_inverse($enc_state['cor']));
							$cor_texto = '#000000';

							$ID_Conf = $rs->db_data["ID_Conf"];
							$login = $rs->db_data["login"];
							$Chave = $rs->db_data["Chave"];
							$Obs2 = $rs->db_data["Obs"];
							$voucher_id=$rs->db_data["voucher_id"];
							$Apagar=$rs->db_data["Apagar"];
							$voucher_gift=$rs->db_data["voucher_gift"];



							$Valor_custo_sem_iva = $rs->db_data["Valor_custo_sem_iva"];
							$Valor_sem_iva = $rs->db_data["Valor_Pagar"];
							$Valor_com_iva = $rs->db_data["Valor"];

							$IVA_valor = $rs->db_data["IVA_valor"];
							$IVA_taxa = $rs->db_data["IVA_taxa"];
							$Peso = $rs->db_data["Peso"];
							$EAN = $rs->db_data["EAN"];

							$product_supplier_reference = $rs->db_data["product_supplier_reference"];

							$produtos[$n]=$ID_Produto;
							$n++;


							/*<td align="left" width="20%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">Referência</td>
                            <td align="left" width="30%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">Produto</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">PVP<br>(sem IVA)</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">PVP(com<br>IVA)</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">Desc<br>onto</td>
                            <td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">Qtd</td>
							<td align="right" width="10%" style="font-weight:bold;background-color:#cccccc;font-size:14px;font-family:arial">Total(com IVA)</td>*/




							$html.='
							<tr>
								<td align="left"  style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;text-align:center;font-family:arial">'.$Ref.'<br>'.$EAN.'</td>
								<td align="left"  style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;text-align:center;font-family:arial" >'.$Nome.'</td>
								<td align="right" style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;text-align:center;font-family:arial" >'.numberformat($Valor_sem_iva).'</td>
								<td align="right" style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;text-align:center;font-family:arial" >'.numberformat($Valor_com_iva).'</td>
								<td align="right" style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;text-align:center;font-family:arial" >'.numberformat($Valor_Desconto).'</td>';



								if($Qtd > 1){
								$html.='
								<td align="right" style="font-weight:bold;background-color:#eeeeee;color:#'.$cor_texto.';font-size:18px;font-family:arial" >'.$Qtd.'</td>';
								}
								else{
								$html.='
								<td align="right" style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;font-family:arial" >'.$Qtd.'</td>';
								}

								$html.='
								<td align="right" style="background-color:#eeeeee;color:#'.$cor_texto.';font-size:14px;font-family:arial" >'.numberformat(($Valor_com_iva * $Qtd)-$Valor_Desconto).'</td>
							</tr>
							';
							$rs->db_next();
						}

						$rs->db_close();

						$html.='
						<tr>
                            <td colspan="7">&nbsp;</td>
                        </tr>
						<tr>
                            <td align="right" colspan="6" style="font-size:14px;font-family:arial">Product Total (Tax Excl.)</td>
                            <td align="right" width="150px" style="font-size:14px;font-family:arial">'.numberformat($TotalDocSemIva-$total_portes_sem_iva).'</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="6" style="font-size:14px;font-family:arial">Product Total (Tax Incl.)</td>
                            <td align="right" width="150px"  style="font-size:14px;font-family:arial">'.numberformat($TotalDocComIva-$total_portes_com_iva).'</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="6" style="font-size:14px;font-family:arial">Custo de Envio</td>
                            <td align="right" width="150px"  style="font-size:14px;font-family:arial">'.numberformat($total_portes_com_iva).'</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="6" style="font-size:14px;font-family:arial">Total IVA</td>
                            <td align="right" width="150px"  style="font-size:14px;font-family:arial">'.numberformat($TotalDocIva).'</td>
                        </tr>
                        <tr>
                            <td align="right" colspan="6" style="font-size:14px;font-family:arial">Total</td>
                            <td align="right" width="150px"  style="font-size:14px;font-family:arial">'.numberformat($TotalDocComIva).'</td>
                        </tr>
                        </table>


                        <br />
                    	<table style="width:100%" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td align="left" style="font-weight:bold;background-color:#cccccc;color:#fff;font-size:14px;font-family:arial">Detalhe da taxa</td>
                            <td align="left" width="70" style="font-weight:bold;background-color:#cccccc;color:#fff;font-size:14px;font-family:arial">Taxa de imposto</td>
                            <td align="left" width="80" style="font-weight:bold;background-color:#cccccc;color:#fff;font-size:14px;font-family:arial">Total sem IVA</td>
                            <td align="left" width="100" style="font-weight:bold;background-color:#cccccc;color:#fff;font-size:14px;font-family:arial">Total IVA</td>
                        </tr>
                        <tr>
                            <td align="left" width="70" style="font-weight:bold;font-size:14px;font-family:arial">Produtos</td>
                            <td align="left" style="font-weight:bold;font-size:14px;font-family:arial">23 %</td>
                            <td align="left" width="80" style="font-weight:bold;font-size:14px;font-family:arial">'.numberformat($TotalDocSemIva-$total_portes_sem_iva).'</td>
                            <td align="left" width="100" style="font-weight:bold;font-size:14px;font-family:arial">'.numberformat(($TotalDocComIva-$total_portes_com_iva) - ($TotalDocSemIva-$total_portes_sem_iva)).'</td>
                        </tr>
                        <tr>
                            <td align="left" width="70" style="font-weight:bold;background-color:#eeeeee;font-size:14px;font-family:arial">Envio</td>
                            <td align="left" style="font-weight:bold;background-color:#eeeeee;font-size:14px;font-family:arial">'.$portes_taxa.' %</td>
                            <td align="left" width="80" style="font-weight:bold;background-color:#eeeeee;font-size:14px;font-family:arial">'.numberformat($total_portes_sem_iva).'</td>
                            <td align="left" width="100" style="font-weight:bold;background-color:#eeeeee;font-size:14px;font-family:arial">'.numberformat($total_portes_com_iva-$total_portes_sem_iva).'</td>
                        </tr>
                        </table>

                        <table style="width:767px" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                        	<td style="border-top:1px dotted #000">&nbsp;</td>
                        </tr>
                        </table>';

                        if($Obs)$html.='OBS: '.nl2br($Obs).'<br><br>';

                        if($Nota_privada)$html.='Nota privada: '.nl2br($Nota_privada).'<br><br>';


                        $t=$n;
                        $n=0;

                        global $Suppliers_nomes;

                        $html.=
								'<table style="width:100%;border: solid 1px #ccc" border="0" cellpadding="2" cellspacing=0>
								<tr>
								<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">ID PRODUTO</td>
								<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Fornecedor</td>
								<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Preço de Custo</td>
								<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Stock</td>
								<td style="background-color:#cccccc;font-weight:bold;text-align:center;font-size:14px;font-family:arial">Última Atualização</td>
								</tr>';
                        while($n<$t)
                        {
                        	$teve_registos = 0;
                        	// echo $teve_registos.'<br>';
                        	$ID_Produto	= $produtos[$n];

							$sql = "SELECT obj_id FROM products_objs WHERE obj_type_id=$ID_Produto";
							// echo $sql.'<br>';
							$rs2->db_open($db_link, $DB_name, $sql);
							if(!$rs2->EOF())
							{

								$obj_id = $rs2->db_data["obj_id"];
								$today = date("Y-m-d");


								// $sql = "SELECT distinct(Supplier_ID),pvp,Stock,data_i,data_u FROM products_objs_history WHERE ID_ORG=$obj_id AND data_u > '".$today." 00:00:00' AND data_u <= '".$today." 23:59:59' GROUP BY Supplier_ID Order by ID Desc ";
								$sql = "SELECT * from (Select distinct(Supplier_ID),pvp,Stock,data_i,data_u FROM products_objs_history WHERE ID_ORG=$obj_id AND data_u > '".$today." 00:00:00' AND data_u <= '".$today." 23:59:59' Order by ID Desc) as sup Group By Supplier_ID";
								// echo $sql.'<br>';
								// die();
								$rs3->db_open($db_link, $DB_name, $sql);

								while(!$rs3->EOF())
								{
									$teve_registos = 1;
									$data = $rs3->db_data["data_u"];
									if($data=='0000-00-00 00:00:00')$data = $rs3->db_data["data_i"];

									$Supplier_ID=$rs3->db_data["Supplier_ID"];
									$pvp = $rs3->db_data["pvp"];
									$pvp_siva = $pvp/1.23;




									// $html.='Supplier: '.$Suppliers_nomes[$Supplier_ID].', PC = '.numberformat($rs3->db_data["pvp"]).', Stock = '.$rs3->db_data["Stock"].', Data: '.$data.'<br>';

									$html.=
									'<tr>
									<td style="text-align:center;font-size:14px;font-family:arial">'.$ID_Produto.'</td>
									<td style="text-align:center;font-size:14px;font-family:arial">'.$Suppliers_nomes[$Supplier_ID].'</td>
									<td style="text-align:center;font-size:14px;font-family:arial">'.numberformat($pvp_siva).'</td>
									<td style="text-align:center;font-size:14px;font-family:arial">'.$rs3->db_data["Stock"].'</td>
									<td style="text-align:center;font-size:14px;font-family:arial">'.$data.'</td>
									</tr>
									';


									$rs3->db_next();
								}
								if($teve_registos == 0){
									$html.=
									'<tr>
									<td style="text-align:center">'.$ID_Produto.'</td>
									<td style="text-align:center">Produto '.$GLOBALS['site_title'].' ou sem atualização</td>
									</tr>';

								}
								$rs3->db_close();
							}
							$rs2->db_close();


							// echo $n.'<br>';
                        	$n++;
                        }




                        $html.='

                	</table>
                	</td>
				</tr>
                </table>
            </td>

		</tr>
		</table>';

	$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

// echo $html;

// die();

	// ESCREVE E IMPRIME
	// $mpdf = new mPDF('c', 'A4');

	// $mpdf = new \Mpdf\Mpdf(['debug' => true]);
	// echo $teve_registos;
	// ob_start();//Enables Output Buffering
	$mpdf = new \Mpdf\Mpdf(['orientation' => 'P']);
	$mpdf->allow_charset_conversion=true;
	$mpdf->charset_in='UTF-8';
	$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
	$mpdf->AddPageByArray([
	    'margin-left' 	=> '9mm',
	    'margin-right'	=> '9mm',
	    'margin-top' 	=> '9mm',
	    'margin-bottom' => '9mm',
	]);
	$footer='Rua de S. Tomé, 1114 - 4200 487 Porto | Email: mail@mhr.pt - Telf: 228331405';
	$footer = iconv("UTF-8", "UTF-8//IGNORE", $footer);
	//$footer = mb_convert_encoding($footer, 'UTF-8', 'UTF-8');
	$mpdf->SetHTMLFooter($footer);
	$mpdf->WriteHTML($html);
	// ob_end_clean();//End Output Buffering
	$mpdf->Output('ENC-'.limpa_special($Referencia).'-'.date('YmdHis').'.pdf', 'D');
	// $mpdf->Output();
?>