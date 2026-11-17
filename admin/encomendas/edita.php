<?php
	include "settings.inc.php";
	if($task=="apagar")	{$task_button="Apagar";$task_form="apaga";}
	elseif($ID==0){$task_button="Inserir";$task_form="insere";}
	else {$task_button="Alterar";$task_form="altera";}
	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$module_voucher,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs_paises=new DataObj;

	$nova=intval($_GET['nova']);
	$grava_nova=intval($_POST['grava_nova']);

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";

	if($ID)
	{
		$sql='UPDATE encomendas SET Lida="1" where ID='.$ID;
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)echo db_error($db_link).$sql."<br>";


		if((isset($_POST['altera_encomenda']) && $_POST['altera_encomenda'] == 1) || (isset($_GET['x_del']) && $_GET['x_del'] > 0) )
		{
			//Vamos gravar uma copia e alterar a encomenda

			$sql = 'Select * from encomendas WHERE ID='.$ID;
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$IDb=$rs->db_data["ID"];
				$Referencia=addslashes(stripslashes($rs->db_data["Referencia"]));
				$Metodo_pagamento=addslashes(stripslashes($rs->db_data["Metodo_pagamento"]));
				$Metodo_expedicao=addslashes(stripslashes($rs->db_data["Metodo_envio"]));
				$Cond_pag=addslashes(stripslashes($rs->db_data["Cond_pag"]));
				$Local_Entrega=addslashes(stripslashes($rs->db_data["Local_Entrega"]));
				$Tipo=addslashes(stripslashes($rs->db_data["Tipo"]));
				$Anulada=addslashes(stripslashes($rs->db_data["Anulada"]));
				$RazaoAnulacao=addslashes(stripslashes($rs->db_data["RazaoAnulacao"]));
				$ID_Cliente=addslashes(stripslashes($rs->db_data["ID_Cliente"]));
				$N_Contribuinte=addslashes(stripslashes($rs->db_data["N_Contribuinte"]));
				$Des_Social=addslashes(stripslashes($rs->db_data["Des_Social"]));
				$Contacto=addslashes(stripslashes($rs->db_data["Contacto"]));
				$Morada=addslashes(stripslashes($rs->db_data["Morada"]));
				$Localidade=addslashes(stripslashes($rs->db_data["Localidade"]));
				$Cod_Pos=addslashes(stripslashes($rs->db_data["Cod_Pos"]));
				$MoradaEntrega=addslashes(stripslashes($rs->db_data["MoradaEntrega"]));
				$LocalidadeEntrega=addslashes(stripslashes($rs->db_data["LocalidadeEntrega"]));
				$Cod_PosEntrega=addslashes(stripslashes($rs->db_data["Cod_PosEntrega"]));
				$telefone=addslashes(stripslashes($rs->db_data["telefone"]));
				$email=addslashes(stripslashes($rs->db_data["email"]));
				$Obs=addslashes(stripslashes($rs->db_data["Obs"]));
				$Portes=addslashes(stripslashes($rs->db_data["Portes"]));
				$PortesSemIva=addslashes(stripslashes($rs->db_data["PortesSemIva"]));
				$IvaPortes=addslashes(stripslashes($rs->db_data["IvaPortes="]));
				$Taxas=addslashes(stripslashes($rs->db_data["Taxas"]));
				$TaxasSemIva=addslashes(stripslashes($rs->db_data["TaxasSemIva"]));
				$Cobrar=addslashes(stripslashes($rs->db_data["Cobrar"]));
				$TaxasPaypal=addslashes(stripslashes($rs->db_data["TaxasPaypal"]));
				$PercDesconto=addslashes(stripslashes($rs->db_data["PercDesconto="]));
				$ValorDesconto=addslashes(stripslashes($rs->db_data["ValorDesconto"]));
				$ID_Vendedor=addslashes(stripslashes($rs->db_data["ID_Vendedor"]));
				$ID_Distribuidor=addslashes(stripslashes($rs->db_data["ID_Distribuidor"]));
				$PropostaDe=addslashes(stripslashes($rs->db_data["PropostaDe"]));
				$ID_Armazem=addslashes(stripslashes($rs->db_data["ID_Armazem"]));
				$Local_carga=addslashes(stripslashes($rs->db_data["Local_carga"]));
				$Local_descarga=addslashes(stripslashes($rs->db_data["Local_descarga"]));
				$Expedicao=addslashes(stripslashes($rs->db_data["Expedicao"]));
				$Viatura_expedicao=addslashes(stripslashes($rs->db_data["Viatura_expedicao"]));
				$ID_Expedicao=addslashes(stripslashes($rs->db_data["ID_Expedicao"]));
				$IDGuia=addslashes(stripslashes($rs->db_data["IDGuia"]));
				$Operador=addslashes(stripslashes($rs->db_data["Operador"]));
				$ValorDescontoExtra=addslashes(stripslashes($rs->db_data["ValorDescontoExtra"]));
				$ValorJustificado=addslashes(stripslashes($rs->db_data["ValorJustificado"]));
				$TotalDocComIva=addslashes(stripslashes($rs->db_data["TotalDocComIva"]));
				$TotalDocSemIva=addslashes(stripslashes($rs->db_data["TotalDocSemIva"]));
				$TotalDocDescontos=addslashes(stripslashes($rs->db_data["TotalDocDescontos"]));
				$TotalDocOutrasTaxas=addslashes(stripslashes($rs->db_data["TotalDocOutrasTaxas"]));
				$TotalDocIva=addslashes(stripslashes($rs->db_data["TotalDocIva"]));
				$Data_Criacao=addslashes(stripslashes($rs->db_data["Data_Criacao"]));
				$Data_Impressao=addslashes(stripslashes($rs->db_data["Data_Impressao"]));
				$Data_Doc_Forn=addslashes(stripslashes($rs->db_data["Data_Doc_Forn"]));
				$Data_expedicao=addslashes(stripslashes($rs->db_data["Data_expedicao"]));
				$Data_entrega=addslashes(stripslashes($rs->db_data["Data_entrega"]));
				$Estado=addslashes(stripslashes($rs->db_data["Estado"]));
				$Encomenda_Paga = addslashes(stripslashes($rs->db_data["Encomenda_Paga"]));
				$enc_Fatura = addslashes(stripslashes($rs->db_data["enc_Fatura"]));
				$Encomenda_Status = addslashes(stripslashes($rs->db_data["Encomenda_Status"]));
				$ATM = addslashes(stripslashes($rs->db_data["ATM"]));
				$enc_pagamento_dados = addslashes(stripslashes($rs->db_data["enc_pagamento_dados"]));
				// DADOS DE ENTREGA E FATURACAO
				$enc_Nome = addslashes(stripslashes($rs->db_data['enc_Nome']));
				$enc_Nome_ent = addslashes(stripslashes($rs->db_data['enc_Nome_ent']));
				$enc_Apelido = addslashes(stripslashes($rs->db_data['enc_Apelido']));
				$enc_Apelido_ent = addslashes(stripslashes($rs->db_data['enc_Apelido_ent']));
				$enc_Morada = addslashes(stripslashes($rs->db_data['enc_Morada']));
				$enc_Morada_ent = addslashes(stripslashes($rs->db_data['enc_Morada_ent']));
				$enc_Cidade = addslashes(stripslashes($rs->db_data['enc_Cidade']));
				$enc_Sexo = addslashes(stripslashes($rs->db_data['enc_Sexo']));
				$enc_DataNascimento = addslashes(stripslashes($rs->db_data['enc_DataNascimento']));
				$enc_Cidade_ent = addslashes(stripslashes($rs->db_data['enc_Cidade_ent']));
				$enc_Codigo_Postal = addslashes(stripslashes($rs->db_data['enc_Codigo_Postal']));
				$enc_Codigo_Postal_ent = addslashes(stripslashes($rs->db_data['enc_Codigo_Postal_ent']));
				$enc_Pais = addslashes(stripslashes($rs->db_data['enc_Pais']));
				$enc_Pais_ent = addslashes(stripslashes($rs->db_data['enc_Pais_ent']));
				$enc_Telefone = addslashes(stripslashes($rs->db_data['enc_Telefone']));
				$enc_Telefone_ent = addslashes(stripslashes($rs->db_data['enc_Telefone_ent']));
				$enc_Email = addslashes(stripslashes($rs->db_data['enc_Email']));
				$enc_Email_ent = addslashes(stripslashes($rs->db_data['enc_Email_ent']));
				$enc_Nif = addslashes(stripslashes($rs->db_data['enc_Nif']));
				$enc_Nif_ent = addslashes(stripslashes($rs->db_data['enc_Nif_ent']));
				$enc_empresa = addslashes(stripslashes($rs->db_data['enc_empresa']));
				$enc_empresa_ent = addslashes(stripslashes($rs->db_data['enc_empresa_ent']));
				$taxas = addslashes(stripslashes($rs->db_data['Taxas']));
				//Especificos Website
				$embrulhar 			= addslashes(stripslashes($rs->db_data['embrulhar']));
				$embrulho_msg		= addslashes(stripslashes($rs->db_data['embrulho_msg']));

				$sql = 'INSERT INTO
					encomendas_historico
					(
						ID_ORG,
						Referencia,
						Metodo_pagamento,
						Metodo_envio,
						Cond_pag,
						Local_Entrega,
						Tipo,
						Anulada,
						RazaoAnulacao,
						ID_Cliente,
						N_Contribuinte,
						Des_Social,
						Contacto,
						Morada,
						Localidade,
						Cod_Pos,
						MoradaEntrega,
						LocalidadeEntrega,
						Cod_PosEntrega,
						telefone,
						email,
						Obs,
						Portes,
						PortesSemIva,
						IvaPortes,
						Taxas,
						TaxasSemIva,
						Cobrar,
						TaxasPaypal,
						PercDesconto,
						ValorDesconto,
						ID_Vendedor,
						ID_Distribuidor,
						PropostaDe,
						ID_Armazem,
						Local_carga,
						Local_descarga,
						Expedicao,
						Viatura_expedicao,
						ID_Expedicao,
						IDGuia,
						Operador,
						ValorDescontoExtra,
						ValorJustificado,
						TotalDocComIva,
						TotalDocSemIva,
						TotalDocDescontos,
						TotalDocOutrasTaxas,
						TotalDocIva,
						Data_Criacao,
						Data_Impressao,
						Data_Doc_Forn,
						Data_expedicao,
						Data_entrega,
						Estado,
						Encomenda_Paga,
						enc_Fatura,
						Encomenda_Status,
						ATM,
						enc_pagamento_dados,
						enc_Nome,
						enc_Nome_ent,
						enc_Apelido,
						enc_Apelido_ent,
						enc_Morada,
						enc_Morada_ent,
						enc_Cidade,
						enc_Sexo,
						enc_DataNascimento,
						enc_Cidade_ent,
						enc_Codigo_Postal,
						enc_Codigo_Postal_ent,
						enc_Pais,
						enc_Pais_ent,
						enc_Telefone,
						enc_Telefone_ent,
						enc_Email,
						enc_Email_ent,
						enc_Nif,
						enc_Nif_ent,
						enc_empresa,
						enc_empresa_ent,
						embrulhar,
						embrulho_msg,
						ID_Admin,
						idtime
					)
					VALUES
					(
						"'.$IDb.'",
						"'.$Referencia.'",
						"'.$Metodo_pagamento.'",
						"'.$Metodo_expedicao.'",
						"'.$Cond_pag.'",
						"'.$Local_Entrega.'",
						"'.$Tipo.'",
						"'.$Anulada.'",
						"'.$RazaoAnulacao.'",
						"'.$ID_Cliente.'",
						"'.$N_Contribuinte.'",
						"'.$Des_Social.'",
						"'.$Contacto.'",
						"'.$Morada.'",
						"'.$Localidade.'",
						"'.$Cod_Pos.'",
						"'.$MoradaEntrega.'",
						"'.$LocalidadeEntrega.'",
						"'.$Cod_PosEntrega.'",
						"'.$telefone.'",
						"'.$email.'",
						"'.$Obs.'",
						"'.$Portes.'",
						"'.$PortesSemIva.'",
						"'.$IvaPortes.'",
						"'.$Taxas.'",
						"'.$TaxasSemIva.'",
						"'.$Cobrar.'",
						"'.$TaxasPaypal.'",
						"'.$PercDesconto.'",
						"'.$ValorDesconto.'",
						"'.$ID_Vendedor.'",
						"'.$ID_Distribuidor.'",
						"'.$PropostaDe.'",
						"'.$ID_Armazem.'",
						"'.$Local_carga.'",
						"'.$Local_descarga.'",
						"'.$Expedicao.'",
						"'.$Viatura_expedicao.'",
						"'.$ID_Expedicao.'",
						"'.$IDGuia.'",
						"'.$Operador.'",
						"'.$ValorDescontoExtra.'",
						"'.$ValorJustificado.'",
						"'.$TotalDocComIva.'",
						"'.$TotalDocSemIva.'",
						"'.$TotalDocDescontos.'",
						"'.$TotalDocOutrasTaxas.'",
						"'.$TotalDocIva.'",
						"'.$Data_Criacao.'",
						"'.$Data_Impressao.'",
						"'.$Data_Doc_Forn.'",
						"'.$Data_expedicao.'",
						"'.$Data_entrega.'",
						"'.$Estado.'",
						"'.$Encomenda_Paga.'",
						"'.$enc_Fatura.'",
						"'.$Encomenda_Status.'",
						"'.$ATM.'",
						"'.$enc_pagamento_dados.'",
						"'.$enc_Nome.'",
						"'.$enc_Nome_ent.'",
						"'.$enc_Apelido.'",
						"'.$enc_Apelido_ent.'",
						"'.$enc_Morada.'",
						"'.$enc_Morada_ent.'",
						"'.$enc_Cidade.'",
						"'.$enc_Sexo.'",
						"'.$enc_DataNascimento.'",
						"'.$enc_Cidade_ent.'",
						"'.$enc_Codigo_Postal.'",
						"'.$enc_Codigo_Postal_ent.'",
						"'.$enc_Pais.'",
						"'.$enc_Pais_ent.'",
						"'.$enc_Telefone.'",
						"'.$enc_Telefone_ent.'",
						"'.$enc_Email.'",
						"'.$enc_Email_ent.'",
						"'.$enc_Nif.'",
						"'.$enc_Nif_ent.'",
						"'.$enc_empresa.'",
						"'.$enc_empresa_ent.'",
						"'.$embrulhar.'",
						"'.$embrulho_msg.'",
						"'.$admin_id.'",
						"'.date("Y-m-d H:m:s").'"
					)';

					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
					$new_id = db_get_last_insert_id($db_link);
			}
			$rs->db_close();



			$sql = 'Select * from encomendas_produtos WHERE ID_PAI='.$ID;
			$rs->db_open($db_link,$DB_name,$sql);
			while(!$rs->EOF())
			{
				$Metodo_expedicao=$rs->db_data["Metodo_envio"];

				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$ID_Produto=$rs->db_data["ID_Produto"];
				$ID_Produto_Pai=$rs->db_data["ID_Produto_Pai"];
				$Ref=$rs->db_data["Ref"];
				$Nome=addslashes(stripslashes($rs->db_data["Nome"]));
				$Qtd=$rs->db_data["Qtd"];
				$Tamanho=$rs->db_data["Tamanho"];
				$Cor=$rs->db_data["Cor"];
				$Valor=$rs->db_data["Valor"];
				$Valor_Cambio=$rs->db_data["Valor_Cambio"];
				$Moeda_Cambio=$rs->db_data["Moeda_Cambio"];
				$Tipo=$rs->db_data["Tipo"];
				$Estado=$rs->db_data["Estado"];
				$ID_Conf=$rs->db_data["ID_Conf"];
				$login=$rs->db_data["login"];
				$download=$rs->db_data["download"];
				$download_completo=$rs->db_data["download_completo"];
				$Chave=$rs->db_data["Chave"];
				$Obs=$rs->db_data["Obs"];
				$Valor_Desconto=$rs->db_data["Valor_Desconto"];
				$Perc_Desconto=$rs->db_data["Perc_Desconto"];
				$voucher_id=$rs->db_data["voucher_id"];
				$Valor_Pagar=$rs->db_data["Valor_Pagar"];
				$rma_imei=$rs->db_data["rma_imei"];
				$rma_n_serie=$rs->db_data["rma_n_serie"];
				$rma_obs=$rs->db_data["rma_obs"];
				$Apagar=$rs->db_data["Apagar"];
				$ID_Enc_Fornecedor=$rs->db_data["ID_Enc_Fornecedor"];

				$sql = 'INSERT INTO
					encomendas_historico_produtos
					(
						ID_PAI,
						ID_Cliente,
						ID_Produto,
						ID_Produto_Pai,
						Ref,
						Nome,
						Qtd,
						Tamanho,
						Cor,
						Valor,
						Valor_Cambio,
						Moeda_Cambio,
						Tipo,
						Estado,
						ID_Conf,
						login,
						download,
						download_completo,
						Chave,
						Obs,
						Valor_Desconto,
						Perc_Desconto,
						voucher_id,
						Valor_Pagar,
						rma_imei,
						rma_n_serie,
						rma_obs,
						Apagar,
						ID_Enc_Fornecedor
					)
					values
					(
						"'.$new_id.'",
						"'.$ID_Cliente.'",
						"'.$ID_Produto.'",
						"'.$ID_Produto_Pai.'",
						"'.$Ref.'",
						"'.$Nome.'",
						"'.$Qtd.'",
						"'.$Tamanho.'",
						"'.$Cor.'",
						"'.$Valor.'",
						"'.$Valor_Cambio.'",
						"'.$Moeda_Cambio.'",
						"'.$Tipo.'",
						"'.$Estado.'",
						"'.$ID_Conf.'",
						"'.$login.'",
						"'.$download.'",
						"'.$download_completo.'",
						"'.$Chave.'",
						"'.$Obs.'",
						"'.$Valor_Desconto.'",
						"'.$Perc_Desconto.'",
						"'.$voucher_id.'",
						"'.$Valor_Pagar.'",
						"'.$rma_imei.'",
						"'.$rma_n_serie.'",
						"'.$rma_obs.'",
						"'.$Apagar.'",
						"'.$ID_Enc_Fornecedor.'"
					)';

					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					if(!$res)echo db_error($db_link).$sql."<br>";

				$rs->db_next();
			}
			$rs->db_close();




			$f_estado=$_POST['f_estado'];
			$enc_Nome_ent=$_POST['enc_Nome_ent'];
			$enc_Apelido_ent=$_POST['enc_Apelido_ent'];
			$enc_Morada_ent=$_POST['enc_Morada_ent'];
			$enc_Cidade_ent=$_POST['enc_Cidade_ent'];
			$enc_Codigo_Postal_ent=$_POST['enc_Codigo_Postal_ent'];
			$enc_Pais_ent=$_POST['enc_Pais_ent'];
			$enc_Telefone_ent=$_POST['enc_Telefone_ent'];

			$enc_Nome=$_POST['enc_Nome'];
			$enc_Apelido=$_POST['enc_Apelido'];
			$enc_Nif=$_POST['enc_Nif'];
			$enc_Morada=$_POST['enc_Morada'];
			$enc_Cidade=$_POST['enc_Cidade'];
			$enc_Codigo_Postal=$_POST['enc_Codigo_Postal'];
			$enc_Pais=$_POST['enc_Pais'];
			$enc_Telefone=$_POST['enc_Telefone'];
			$enc_Email=$_POST['enc_Email'];

			$Obs=addslashes($_POST['Obs']);
			$embrulho_msg=addslashes($_POST['embrulho_msg']);
			$Nota_privada=addslashes($_POST['Nota_privada']);

			$Taxas=$_POST['Taxas'];
			$Portes=$_POST['Portes'];

			$Metodo_pagamento=$_POST['m_metodopagamento'];
			$Metodo_expedicao=$_POST['m_metodoenvio'];




			if( (isset($_GET['x_del']) && $_GET['x_del'] > 0) )
			{

			}
			else
			{
				$sql = 'UPDATE encomendas set
				Estado="'.$f_estado.'",
				Nota_privada="'.$Nota_privada.'",
				enc_Nome_ent="'.$enc_Nome_ent.'",
				enc_Apelido_ent="'.$enc_Apelido_ent.'",
				enc_Morada_ent="'.$enc_Morada_ent.'",
				enc_Cidade_ent="'.$enc_Cidade_ent.'",
				enc_Codigo_Postal_ent="'.$enc_Codigo_Postal_ent.'",
				enc_Pais_ent="'.$enc_Pais_ent.'",
				enc_Nome="'.$enc_Nome.'",
				enc_Apelido="'.$enc_Apelido.'",
				enc_Nif="'.$enc_Nif.'",
				enc_Morada="'.$enc_Morada.'",
				enc_Cidade="'.$enc_Cidade.'",
				enc_Codigo_Postal="'.$enc_Codigo_Postal.'",
				enc_Pais="'.$enc_Pais.'",
				enc_Telefone="'.$enc_Telefone.'",
				enc_Telefone_ent="'.$enc_Telefone_ent.'",
				enc_Email="'.$enc_Email.'",
				Obs="'.$Obs.'",
				embrulho_msg="'.$embrulho_msg.'",
				Taxas="'.$Taxas.'",
				Portes="'.$Portes.'",
				Metodo_pagamento="'.$Metodo_pagamento.'",
				Metodo_envio="'.$Metodo_expedicao.'"

				WHERE ID='.$ID;

				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";

				if($f_estado==7 || $f_estado==16 || $f_estado==38)update_encomendas_paga($ID);
				if($f_estado==8 || $f_estado==37 || $f_estado==15 || $f_estado==23)update_encomendas_nao_paga($ID);


				foreach($_POST as $varName=> $value)
				{
					$str_id=substr($varName,strpos($varName,'_')+1);
					if($str_id > 0 && substr_count($varName,'chk_') > 0)
					{
						$var='Valor_'.$str_id;  $dv_valor=$$var;
						$var='Valor_Desconto_'.$str_id;  $dv_valor_desconto=$$var;
						$var='Qtd_'.$str_id;  $dv_qtd=$$var;
						$var='m_estado_'.$str_id;  $dv_estado=$$var;
						$var='m_id_'.$str_id;  $prod_id=$$var;//id do artigo


						$info=get_produtc_info($prod_id);
						$iva=$info['prod_precos']['IVA'];

						$Valor_sem_iva = $dv_valor;

						if($iva>0)
						{
							$Valor_sem_iva=$dv_valor/(1+$iva/100);
						}

						/*
						$Valor_sem_iva = $rs->db_data["Valor_Pagar"];
						$Valor_com_iva = $rs->db_data["Valor"];

						*/




						$sql="UPDATE encomendas_produtos SET Valor='$dv_valor',Valor_Pagar='$Valor_sem_iva',Valor_Desconto='$dv_valor_desconto',Qtd='$dv_qtd',Estado='$dv_estado' WHERE ID=$str_id";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res)echo db_error($db_link).$sql.'<br>';
					}
				};



			}



			$novo_id=$_POST['novo_id'];
			$Ref=$_POST['novo_ref'];
			$Valor=$_POST['novo_valor'];
			$Valor_Desconto=$_POST['novo_desconto'];
			$Qtd=$_POST['novo_qtd'];

			if($novo_id>0 && !$Ref)
			{
				$sql = "SELECT Ref FROM produtos WHERE ID=$novo_id";
				$rs2->db_open($db_link, $DB_name, $sql);
				if(!$rs2->EOF())
				{
					$Ref = $rs2->db_data["Ref"];
				}
				$rs2->db_close();


			}

			if($novo_id>0)
			{
				$sql = "SELECT Cabecalho FROM products_lang WHERE ID_PAI=$novo_id";
				$rs2->db_open($db_link, $DB_name, $sql);
				if(!$rs2->EOF())
				{
					$Nome = addslashes(stripslashes($rs2->db_data["Cabecalho"]));
				}
				$rs2->db_close();


				if(!$Valor || $Valor<=0)
				{
					$sql = "SELECT obj_pvp FROM products_objs WHERE obj_type_id=$novo_id order by obj_id Desc";
					$rs2->db_open($db_link, $DB_name, $sql);
					if(!$rs2->EOF())
					{
						$Valor = $rs2->db_data["obj_pvp"];
					}
					$rs2->db_close();
				}



				$sql = 'INSERT INTO
					encomendas_produtos
					(
						ID_PAI,
						ID_Cliente,
						ID_Produto,
						ID_Produto_Pai,
						Ref,
						Nome,
						Qtd,
						Tamanho,
						Cor,
						Valor,
						Valor_Cambio,
						Moeda_Cambio,
						Tipo,
						Estado,
						ID_Conf,
						login,
						download,
						download_completo,
						Chave,
						Obs,
						Valor_Desconto,
						Perc_Desconto,
						voucher_id,
						Valor_Pagar,
						rma_imei,
						rma_n_serie,
						rma_obs,
						Apagar,
						ID_Enc_Fornecedor
					)
					values
					(
						"'.$ID.'",
						"'.$ID_Cliente.'",
						"'.$novo_id.'",
						"",
						"'.$Ref.'",
						"'.$Nome.'",
						"'.$Qtd.'",
						"'.$Tamanho.'",
						"'.$Cor.'",
						"'.$Valor.'",
						"'.$Valor_Cambio.'",
						"'.$Moeda_Cambio.'",
						"'.$Tipo.'",
						"'.$Estado.'",
						"'.$ID_Conf.'",
						"'.$login.'",
						"'.$download.'",
						"'.$download_completo.'",
						"'.$Chave.'",
						"'.$Obs.'",
						"'.$Valor_Desconto.'",
						"'.$Perc_Desconto.'",
						"'.$voucher_id.'",
						"'.$Valor_Pagar.'",
						"'.$rma_imei.'",
						"'.$rma_n_serie.'",
						"'.$rma_obs.'",
						"'.$Apagar.'",
						"'.$ID_Enc_Fornecedor.'"
					)';

					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
			}

			if( (isset($_GET['x_del']) && $_GET['x_del'] > 0) )
			{
				$sql = 'DELETE FROM encomendas_produtos where ID = '.intval($_GET['x_del']);
				$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}



			//Agora vamos recalcular tudo
			$TotalDocComIva=0;
			$TotalDocDescontos=0;
			$sql = 'Select * from encomendas_produtos WHERE ID_PAI='.$ID;
			$rs2->db_open($db_link,$DB_name,$sql);
			while(!$rs2->EOF())
			{
				$Valor=$rs2->db_data["Valor"]*$rs2->db_data["Qtd"];
				$Valor_Desconto=$rs2->db_data["Valor_Desconto"];


				$TotalDocComIva+=$Valor-$Valor_Desconto;
				$TotalDocDescontos+=$Valor_Desconto;
				$rs2->db_next();
			}
			$rs2->db_close();

			$TotalDocIva=0;
			if($TotalDocComIva>0)
			{
				$TotalDocComIva+=$Taxas;
				$TotalDocComIva+=$Portes;


				$TotalDocSemIva=$TotalDocComIva/1.23;
				$TotalDocIva=$TotalDocComIva-$TotalDocSemIva;
			}

			$sql = 'UPDATE encomendas set
				TotalDocComIva="'.$TotalDocComIva.'",
				TotalDocSemIva="'.$TotalDocSemIva.'",
				TotalDocIva="'.$TotalDocIva.'",
				TotalIva="'.$TotalDocIva.'",
				TotalDocDescontos="'.$TotalDocDescontos.'"
				WHERE ID='.$ID;
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
			if(!$res)echo db_error($db_link).$sql."<br>";


		}
	}
	else
	{
		if($grava_nova)
		{
			$f_estado=$_POST['f_estado'];
			$enc_Nome_ent=$_POST['enc_Nome_ent'];
			$enc_Apelido_ent=$_POST['enc_Apelido_ent'];
			$enc_Morada_ent=$_POST['enc_Morada_ent'];
			$enc_Cidade_ent=$_POST['enc_Cidade_ent'];
			$enc_Codigo_Postal_ent=$_POST['enc_Codigo_Postal_ent'];
			$enc_Pais_ent=$_POST['enc_Pais_ent'];
			$enc_Nome=$_POST['enc_Nome'];
			$enc_Apelido=$_POST['enc_Apelido'];
			$enc_Nif=$_POST['enc_Nif'];
			$enc_Morada=$_POST['enc_Morada'];
			$enc_Cidade=$_POST['enc_Cidade'];
			$enc_Codigo_Postal=$_POST['enc_Codigo_Postal'];
			$enc_Pais=$_POST['enc_Pais'];
			$enc_Telefone=$_POST['enc_Telefone'];
			$enc_Email=$_POST['enc_Email'];
			$Obs=addslashes($_POST['Obs']);
			$embrulho_msg=addslashes($_POST['embrulho_msg']);
			$Taxas=$_POST['Taxas'];
			$Portes=$_POST['Portes'];

			$novo_id=$_POST['novo_id'];
			$Ref=$_POST['novo_ref'];
			$Valor=$_POST['novo_valor'];
			$Valor_Desconto=$_POST['novo_desconto'];
			$Qtd=$_POST['novo_qtd'];

			$Metodo_pagamento=$_POST['m_metodopagamento'];
			$Metodo_expedicao=$_POST['m_metodoenvio'];



			$sql = 'INSERT INTO encomendas (
				Lingua,
				Data_Criacao,
				Estado,
				enc_Nome_ent,
				enc_Apelido_ent,
				enc_Morada_ent,
				enc_Cidade_ent,
				enc_Codigo_Postal_ent,
				enc_Pais_ent,
				enc_Nome,
				enc_Apelido,
				enc_Nif,
				enc_Morada,
				enc_Cidade,
				enc_Codigo_Postal,
				enc_Pais,
				enc_Telefone,
				enc_Email,
				Obs,
				embrulho_msg,
				Taxas,
				Portes,
				Metodo_pagamento,
				Metodo_envio)
				values(
				"pt",
				"'.date("Y-m-d").'",
				"'.$f_estado.'",
				"'.$enc_Nome_ent.'",
				"'.$enc_Apelido_ent.'",
				"'.$enc_Morada_ent.'",
				"'.$enc_Cidade_ent.'",
				"'.$enc_Codigo_Postal_ent.'",
				"'.$enc_Pais_ent.'",
				"'.$enc_Nome.'",
				"'.$enc_Apelido.'",
				"'.$enc_Nif.'",
				"'.$enc_Morada.'",
				"'.$enc_Cidade.'",
				"'.$enc_Codigo_Postal.'",
				"'.$enc_Pais.'",
				"'.$enc_Telefone.'",
				"'.$enc_Email.'",
				"'.$Obs.'",
				"'.$embrulho_msg.'",
				"'.$Taxas.'",
				"'.$Porte.'",
				"'.$Metodo_pagamento.'",
				"'.$Metodo_expedicao.'"
				)';

				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";

				$ID = db_get_last_insert_id($db_link);

			if($novo_id>0 && !$Ref)
			{
				$sql = "SELECT * FROM produtos WHERE ID=$novo_id";
				$rs2->db_open($db_link, $DB_name, $sql);
				if(!$rs2->EOF())
				{
					$Ref = $rs2->db_data["Ref"];
				}
				$rs2->db_close();
			}

			if($novo_id>0)
			{
				$sql = 'INSERT INTO
					encomendas_produtos
					(
						ID_PAI,
						ID_Cliente,
						ID_Produto,
						ID_Produto_Pai,
						Ref,
						Nome,
						Qtd,
						Tamanho,
						Cor,
						Valor,
						Valor_Cambio,
						Moeda_Cambio,
						Tipo,
						Estado,
						ID_Conf,
						login,
						download,
						download_completo,
						Chave,
						Obs,
						Valor_Desconto,
						Perc_Desconto,
						voucher_id,
						Valor_Pagar,
						rma_imei,
						rma_n_serie,
						rma_obs,
						Apagar,
						ID_Enc_Fornecedor
					)
					values
					(
						"'.$ID.'",
						"'.$ID_Cliente.'",
						"'.$novo_id.'",
						"",
						"'.$Ref.'",
						"'.$Nome.'",
						"'.$Qtd.'",
						"'.$Tamanho.'",
						"'.$Cor.'",
						"'.$Valor.'",
						"'.$Valor_Cambio.'",
						"'.$Moeda_Cambio.'",
						"'.$Tipo.'",
						"'.$Estado.'",
						"'.$ID_Conf.'",
						"'.$login.'",
						"'.$download.'",
						"'.$download_completo.'",
						"'.$Chave.'",
						"'.$Obs.'",
						"'.$Valor_Desconto.'",
						"'.$Perc_Desconto.'",
						"'.$voucher_id.'",
						"'.$Valor_Pagar.'",
						"'.$rma_imei.'",
						"'.$rma_n_serie.'",
						"'.$rma_obs.'",
						"'.$Apagar.'",
						"'.$ID_Enc_Fornecedor.'"
					)';

					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}
	}

	/*if(isset($_POST['fac_send']) && $_POST['fac_send'] == 1)
	{
		$ficheiro = submete_ficheiro('fatura', '', intval($_GET["ID"]));

		$sql='UPDATE encomendas set enc_Fatura="'.$ficheiro.'" WHERE ID='.intval($_GET["ID"]);
		$res = Runquery($db_link,$DB_name,$sql);
	}*/

	if(isset($_GET['x_id']) && $_GET['x_id'] > 0)
	{
		$x_id = $_GET['x_id'];
		$x_estado = $_GET['x_estado'];

		$sql = 'Select * from encomendas_produtos WHERE ID='.$x_id;
		$$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{

		}
		$rs->db_close();

		$sql = 'Select * from encomendas_produtos WHERE ID='.$x_id;
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$tipo=$rs->db_data["Tipo"];
			$ID_Enc=$rs->db_data["ID_PAI"];
			$ID_Produto=$rs->db_data["ID_Produto"];
			$ID_Cliente=$rs->db_data["ID_Cliente"];
			$Estado_anterior=$rs->db_data["Estado"];
			$voucher_gift = ''; //$rs->db_data["voucher_gift"];
			$rs->db_close();
			$sql = 'UPDATE encomendas_produtos SET Estado="'.$x_estado.'" WHERE ID = "'.$x_id.'"';
			$res = Runquery($db_link,$DB_name,$sql);
			if(!$res)echo db_error($db_link).$sql."<br>";
			else
			{
				echo '<script>$(document).ready(function() {$("html, body").animate({ scrollTop: $("#scroll'.$ID_Produto.'").offset().top }, 1);});</script>';

			}
			$notify = 1;

			// AGORA VAMOS ATUALIZAR O ESTADO GERAL
			$sql = "SELECT Estado FROM encomendas_produtos WHERE ID_PAI='".$ID_Enc."' AND Estado <> '$x_estado'";
			$rs->db_open($db_link,$DB_name,$sql);
			if($rs->EOF())
			{
				// COMO NAO EXISTE MAIS NENHUM PRODUTO COM OUTRO ESTADO, VAMOS ALTERAR O ESTADO DA ENCOMENDA PARA ESTE.
				$sql='UPDATE encomendas SET Estado="'.$x_estado.'" WHERE ID = "'.$ID_Enc.'"';
				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link).$sql."<br>";
			}
			$rs->db_close();
		}
	}

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
			$email=$rs->db_data["enc_Email"];
			// $email_encomenda=$rs->db_data["enc_Email"];
			$Obs=$rs->db_data["Obs"];
			$Portes=$rs->db_data["Portes"];
			$PortesSemIva=$rs->db_data["PortesSemIva"];
			$IvaPortes=$rs->db_data["IvaPortes"];
			$Taxas=$rs->db_data["Taxas"];
			$TaxasSemIva=$rs->db_data["TaxasSemIva"];
			$Cobrar=$rs->db_data["Cobrar"];
			$TaxasPaypal=$rs->db_data["TaxasPaypal"];
			$PercDesconto=$rs->db_data["PercDesconto"];
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
			$Transaction_Code = $rs->db_data["Transaction_Code"];
			$ATM = base64_decode($rs->db_data["ATM"]);

			$enc_pagamento_dados = $rs->db_data["enc_pagamento_dados"];

			$Nota_privada = stripslashes($rs->db_data["Nota_privada"]);

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
			$enc_Pais_ent = $rs->db_data['enc_Pais_ent'];

			$enc_Telefone = $rs->db_data['enc_Telefone'];
			$enc_Telefone_ent = $rs->db_data['enc_Telefone_ent'];
			$enc_Email = $rs->db_data['enc_Email'];
			$enc_Email_ent = $rs->db_data['enc_Email_ent'];

			$enc_Nif = $rs->db_data['enc_Nif'];
			$enc_Nif_ent = $rs->db_data['enc_Nif_ent'];
			$enc_empresa = $rs->db_data['enc_empresa'];
			$enc_empresa_ent = $rs->db_data['enc_empresa_ent'];

			$taxas = $rs->db_data['Taxas'];


			//Especificos Website
			$embrulhar 			= $rs->db_data['embrulhar'];
			$embrulho_msg		= $rs->db_data['embrulho_msg'];
		}
		$rs->db_close();


		// if($ID != 0)
		// {
		// 	$sql = "SELECT * FROM encomendas_historico WHERE ID_ORG=$ID ORDER BY ID DESC Limit 0,1";
		// 	$rs->db_open($db_link,$DB_name,$sql);
		// 	if($rs)
		// 	{
		// 		$ID_Admin=$rs->db_data["ID_Admin"];
		// 		$idtime=$rs->db_data["idtime"];
		// 		$estado_encomenda=$rs->db_data["Estado"];
		// 	}
		// }
		// $rs->db_close();


		$sql = 'Select * from vivawallet_data WHERE enc_id='.$ID;
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$OrderCode=$rs->db_data["OrderCode"];
		}
		$rs->db_close();



		$sql = 'SELECT * FROM clientes WHERE ID="'.$ID_Cliente.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$idate=$rs->db_data['idate'];
			$m_nome=$rs->db_data['Nome'];
			$m_apelido=$rs->db_data['Apelido'];
			$m_empresa=$rs->db_data['Empresa'];
			$m_morada=$rs->db_data['Morada'];
			$m_codpos=$rs->db_data['Codigo_Postal'];
			$m_pais=$rs->db_data['Pais'];
			$m_telefone=$rs->db_data['Telefone'];
			$m_email=$rs->db_data['Email'];
			$Grupo_ID=$rs->db_data['Grupo_ID'];
			$m_contribuinte=$rs->db_data['Nif'];
			$Language=$rs->db_data['Language'];
			$Newsletter=$rs->db_data['Newsletter'];
			$Newsletter_Tipo=$rs->db_data['Newsletter_Tipo'];
			$m_ativo=$rs->db_data['Ativo']==1 ? $rs->db_data['Ativo'] : $rs->db_data['Estado'];
			$m_sexo=$rs->db_data['Sexo'];
			$m_datanascimento=$rs->db_data['DataNascimento'];
			$m_nickname=$rs->db_data['Nickname'];
			$I1=$rs->db_data['img_ext'];
			$m_phone=$rs->db_data['Phone'];
			$Afiliado=$rs->db_data['Afiliado'];
			$Afiliado_Perc=$rs->db_data['Afiliado_Perc'];
			$m_cidade=$rs->db_data['Cidade'];
		}
		$rs->db_close();



	}


	if($_POST['enviar_msg']==1)
	{
		global $admin_id,$ss_token;
		//Vamos gravar e enviar a mensagem ao cliente
		$titulo_mgs=$_POST['titulo_mgs'];
		$txt_msgs=$_POST['txt_msgs'];
		$ID_Enc = $ID;
		$ID_Admin = $admin_id;

		$txt_msgs=nl2br($txt_msgs);

		$sql = 'INSERT INTO mensagens (ID_Cliente,ID_Admin,ID_Enc,Assunto,Mensagem,idate) values("'.$ID_Cliente.'","'.$ID_Admin.'","'.$ID_Enc.'","'.addslashes($titulo_mgs).'","'.addslashes($txt_msgs).'","'.date("Y-m-d H:i:s").'")';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		if(!$res)echo db_error($db_link).$sql."<br>";

		if(!$email)$email=$m_email;
		if($email != '')
		{
			$res = send_email($email, 'Encomenda '.$Referencia.' - '.$titulo_mgs, $txt_msgs);
			if($res)
			{
				/*$log_dados = serialize(array($email, $titulo_mgs, $txt_msgs));
				$sql = 'INSERT INTO encomendas_emails (ID_ORG, Data_envio, MailTo, Subject, Body) VALUES("'.$ID_Enc.'","'.date('Y-m-d H:i:s').'","'.$email.'","'.addslashes($titulo_mgs).'","'.addslashes($txt_msgs).'")';
				$res = Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";*/

			}
		}

	}

	if($_POST['enviar_dinheiro']==1)
	{
		global $admin_id,$ss_token;
		$PagamentoValor=$_POST['PagamentoValor'];
		$PagamentoTipo=$_POST['PagamentoTipo'];
		$ID_Enc = $ID;
		$ID_Admin = $admin_id;
		if($ID_Admin != '' )
        {
        	$dados = getDadosAdministradorAdmin($ID_Admin);
        	// var_dump($dados); die();
        	$user_ap = $dados['Nome'];
 		}

		$sql = 'INSERT INTO encomendas_valores_pagos (ID_ORG,Valor,Tipo,DataHora,UserAP,PagamentoData) values("'.$ID_Enc.'","'.$PagamentoValor.'","'.$PagamentoTipo.'","'.date("Y-m-d H:i:s").'", "'.$user_ap .'","'.$PagamentoData.'")';
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		if(!$res)echo db_error($db_link).$sql."<br>";
	}



?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
<script>
function Imprime(strid)
{
	var prtContent = document.getElementById(strid);
	var WinPrint =window.open('','','left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
	WinPrint.document.write('<!DOCTYPE html>');
	WinPrint.document.write('<html fontawesome-i2svg-active fontawesome-i2svg-complete xmlns="http://www.w3.org/1999/xhtml">');
	WinPrint.document.write('<title>ADMIN</title>');

	WinPrint.document.write('<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/bootstrap-3.3.7/css/bootstrap.min.css" />');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/admin_estilos.css" />');
	WinPrint.document.write('<style>svg{max-width:.875em} div.wraper div.tabtop-open3 {font-size:11px !important} body {font-size:11px !important}.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px} .subm{margin:0px;padding:0px;position:relative} .subm{display:none;}</style>');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/reveal/jquery.fancybox.css" media="screen" /> ');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/fontawesome-free-5.7.2-web/css/all.css" media="screen" />');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/bootstrap/bootstrap-4.1.1/css/bootstrap-grid.css" media="screen" /> ');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/bootstrap-calendar/css/bootstrap_calendar.css" media="screen" /> ');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.css" media="screen" />');
	WinPrint.document.write('<link rel="stylesheet" href="<?=$GLOBALS['site_http_url']?>admin/layout/jquery-ui-1.10.3.css" />');
	WinPrint.document.write('<link rel="stylesheet" type="text/css" href="<?=$GLOBALS['site_http_url']?>admin/layout/datepicker/jquery-ui-timepicker-addon.css" media="screen" />');
	WinPrint.document.write('<link rel="stylesheet" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/animate.min.css" />');
	WinPrint.document.write('<link rel="stylesheet" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style.min.css" />');
	WinPrint.document.write('<link rel="stylesheet" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style.css" />');
	WinPrint.document.write('<link rel="stylesheet" href="<?=$GLOBALS['site_http_url']?>admin/layout/theme/css/style-responsive.min.css" />');

	WinPrint.document.write(prtContent.innerHTML);
	WinPrint.document.close();
	WinPrint.focus();
	WinPrint.print();
	WinPrint.close();
	prtContent.innerHTML=strOldOne;
}
function gera_PDF(strid)
{
	window.open('pdf.php?ID='+strid);
}
function update_status(n)
{
	var url="edita.php?ID=<?php echo $ID?>&x_id="+n;
	if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
	window.open(url,"_self");
}

<?
/*

function Imprime(strid)
{
	var prtContent = document.getElementById(strid);
	var WinPrint =window.open('','','left=-10,top=-10,width=0,height=0,toolbar=0,scrollbars=0,status=0');
	WinPrint.document.write(prtContent.innerHTML);
	WinPrint.document.close();
	WinPrint.focus();
	WinPrint.print();
	WinPrint.close();
	prtContent.innerHTML=strOldOne;
}
function update_status(n)
{
	var url="pagina.php?ID=<?php echo $ID?>&x_id="+n;
	if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
	window.open(url,"_self");
}
*/
?>

function remove_artigo(n)
{
	var r = confirm("Tem a certeza que pretende remover o artigo da encomenda?");
	if (r == true) {
		var url="edita.php?ID=<?php echo $ID?>&x_del="+n;
		window.open(url,"_self");
	} else {

	}


}


function send_msg()
{
	$('#enviar_msg').val('1');
	document.getElementById("myForm").submit();
}

function send_pagamento()
{
	$('#enviar_dinheiro').val('1');
	document.getElementById("myForm").submit();
}


</script>

<div class="center">
		<form action="edita.php?ID=<?php echo $_GET['ID']?>" id="myForm" name="myForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="fac_send" value="1">
		<input type="hidden" name="altera_encomenda" value="1">
		<input type="hidden" name="grava_nova" value="<?php echo $nova?>">
		<input type="hidden" id="enviar_msg" name="enviar_msg" value="0">
		<input type="hidden" id="enviar_dinheiro" name="enviar_dinheiro" value="0">



    	<table border=0 cellpadding=0 cellspacing=0 align="center">
        <tr>
            <td valign="top" align="left"><?
				include "../layout/menus.inc.php"; ?>
                <table style="background:#FFF;width:100%" border=0 cellspacing=0 cellpadding="0" align="center" id="area_impressao">
				<tr>
				<td valign="top">
				<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
				<tr>
				<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de encomendas</a></td>
				</tr>
				</table>
				</td>
				</tr>
                <tr>
                    <td valign="top" align="left">
					<div id="wraper" class="wraper">
					<div class="tabtop-open3" id="tab1">
                    	<table width="100%" border="0" cellpadding="0" cellspacing=0>
                        <tr>
                            <td valign="top" colspan="2">

                                <table width="100%" border=0 cellspacing=0 cellpadding="4">

								<? if(!$nova)
								{?>
								<tr>
                                    <td align="left" width="120"></td>
                                    <td align="right"><b><?php

                                    if($ID != 0)
									{
										$sql = "SELECT * FROM encomendas_historico WHERE ID_ORG=$ID ORDER BY ID DESC";
										$rs3 = new DataObj;
										$rs3->db_open($db_link,$DB_name,$sql);
										while(!$rs3->EOF())
										{
											$ID_Admin=$rs3->db_data["ID_Admin"];
											$idtime=$rs3->db_data["idtime"];
											$estado_encomenda=$rs3->db_data["Estado"];

											if($ID_Admin != '' )
											{
												$dados = getDadosAdministradorAdmin($ID_Admin);
												echo $dados['Nome'];
											}
											if($idtime != '' ) echo ' / '.$idtime;
											if($estado_encomenda != '' )
											{
												$rs2 = new DataObj;
												$sql = 'SELECT Cabecalho from encomendas_estados_lang where ID_PAI='.$estado_encomenda;
												$rs2->db_open($db_link,$DB_name,$sql);

												if(!$rs2->EOF())
												{
													$nome_estado = $rs2->db_data['Cabecalho'];
													echo ' / Estado anterior: '.$nome_estado.'<br>';
												}
												$rs2->db_close();

											}
											else{
												echo ' / Estado anterior: Pendente<br>';
											}
										$rs3->db_next();
										}
									$rs3->db_close();
									}


                                     ?></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120"><i class="fas fa-file-invoice-dollar"></i> Nº da encomenda:</td>
                                    <td align="left"><b><?php echo $ID?></b> - <b><?php echo $Referencia ?></b></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="far fa-calendar-alt"></i> Data da encomenda:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo $Data_Criacao?></td>
                                </tr><? }?>
                                <tr>
                                    <td align="left" width="190" style="border-top:#ccc 1px solid"><i class="fas fa-clock"></i> Estado da encomenda:</td>
                                    <td align="left" style="border-top:#ccc 1px solid">
                                    	<select name="f_estado" style="width:100%" class="form-control">
											<option value="-1">Todos</option>
											<?php
											$rs2 = new DataObj;
											$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$admin_lang."' ORDER BY Ordem";
											$rs2->db_open($db_link,$DB_name,$sql);
											if(!$rs2->EOF())
											{
												$a=0;
												while(!$rs2->EOF())
												{
													$eel_id = $rs2->db_data['ID'];
													$Ordem = $rs2->db_data['Ordem'];
													$Cabecalho = $rs2->db_data['Cabecalho'];

													echo '<option value="'.$eel_id.'" '.($Estado==$eel_id ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.'</option>';

													$a++;
													$rs2->db_next();
												}
											}
											$rs2->db_close();

											?>

										</select>
									</td>
                                </tr>

								<?php
									if(getNomeMetodoPagamentoTipo($Metodo_pagamento) == 1)
									{
										$Encomenda_Status2 = $Encomenda_Status;
										switch ($Encomenda_Status)
										{
											case "Canceled_Reversal":
												$Encomenda_Status = write_lang('key_paypal_canceled_reversal');
												break;
											case "Completed":
												$Encomenda_Status = write_lang('key_paypal_completed');
												break;
											case "Created":
												$Encomenda_Status = write_lang('key_paypal_created');
												break;
											case "Denied":
												$Encomenda_Status = write_lang('key_paypal_denied');
												break;
											case "Expired":
												$Encomenda_Status = write_lang('key_paypal_expired');
												break;
											case "Failed":
												$Encomenda_Status = write_lang('key_paypal_failed');
												break;
											case "Pending":
												$Encomenda_Status = write_lang('key_paypal_pending');
												break;
											case "Refunded":
												$Encomenda_Status = write_lang('key_paypal_refunded');
												break;
											case "Reversed":
												$Encomenda_Status = write_lang('key_paypal_reversed');
												break;
											case "Processed":
												$Encomenda_Status = write_lang('key_paypal_processed');
												break;
											case "Voided":
												$Encomenda_Status = write_lang('key_paypal_voided');
												break;
											default:
												$Encomenda_Status = write_lang('key_paypal_default');
												break;
										}
										global $site_title;
										if($Encomenda_Status2=='' && ($Estado==1 || $Estado==3||$Estado==6|| $Estado==7)) $Encomenda_Status = 'Encomenda foi confirmada pelo administrador da '.$site_title;
										elseif($Encomenda_Status2=='' && ($Estado==2 || $Estado==4)) $Encomenda_Status = 'Encomenda foi Cancelada pelo administrador da '.$site_title;
										?>
										<tr>
		                                    <td align="left" width="120" >Estado do PayPal:</td>
		                                    <td align="left"><?php echo $Encomenda_Status?></td>
		                                </tr>
										<?
									}
									else if(getNomeMetodoPagamentoTipo($Metodo_pagamento)==3)
									{
										$Encomenda_Status2 = $Encomenda_Status;
										switch ($Encomenda_Status)
										{
											case "Success":
												$Encomenda_Status = write_lang('key_unicre_success');
												break;
											case "Cancel":
												$Encomenda_Status = write_lang('key_unicre_cancel');
												break;
											case "Denied":
												$Encomenda_Status = write_lang('key_unicre_denied');
												break;
											case "Failed_Fraud":
												$Encomenda_Status = write_lang('key_unicre_deniedfraud');
												break;
											case "Failed_Technical":
												$Encomenda_Status = write_lang('key_unicre_failedtechnical');
												break;
											case "Failed":
												$Encomenda_Status = write_lang('key_unicre_failed');
												break;
											default:
												$Encomenda_Status = write_lang('key_unicre_default');
												break;
										}

										global $site_title;
										if($Encomenda_Status2=='' && ($Estado==1 || $Estado==3 || $Estado==6|| $Estado==7)) $Encomenda_Status = 'Encomenda foi confirmada pelo administrador da '.$site_title;
										elseif($Encomenda_Status2=='' && ($Estado==2 || $Estado==4)) $Encomenda_Status = 'Encomenda foi Cancelada pelo administrador da '.$site_title;

										?>
										<tr>
		                                    <td align="left" width="120" >Estado da Unicre:</td>
		                                    <td align="left"><?php echo $Encomenda_Status?></td>
		                                </tr>
										<?
									}

								if(!$nova){?>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-user"></i> Cliente ID:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><a href="../clientes/pagina.php?ID=<?=$ID_Cliente?>"><?=$ID_Cliente?></a></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid">&nbsp;</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?=$m_nome?>&nbsp;<?=$m_apelido?></td>
                                </tr>
                                <tr>
                                    <td align="left"><i class="far fa-calendar-check"></i> Conta criada:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?=$idate?></a></td>
                                </tr>
                                <tr>
                                     <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-truck"></i> Tracking:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?=$m_guia?></td>
                                </tr>
                                <tr>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-credit-card"></i> Código de Transação:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?=$Transaction_Code?></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-euro-sign"></i> Estado de Pagamento</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><? if($Encomenda_Paga){ echo '<p style="color:green; font-size:16px;margin-bottom:0">PAGA</p>';}else{echo '<p style="color:red;font-size:16px;margin-bottom:0">NÃO PAGA</p>';}?></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-lock"></i> Nota privada:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><textarea name="Nota_privada" style="height: 80px"><?=$Nota_privada?></textarea></td>
                                </tr>


								<?php
								}
								if($nova){?>
								<tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Metodo de Pagamento:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo showLayoutMetodosPagamento_admin()?></td>
                                </tr>
								<tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Metodo de Expedição:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo showLayoutMetodosExpedicao()?></td>
                                </tr>
								<? }
								else
								{?><tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Metodo de Pagamento:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo showLayoutMetodosPagamento_admin($Metodo_pagamento)?></td>
                                </tr>
                                <?php if(getNomeMetodoPagamentoTipo($Metodo_pagamento) != 3){?>
								<tr>
                                    <td align="left" width="120" height="25">&nbsp;</td>
                                    <td align="left" style="font-weight:bold;color:#000;font-size:14px"><?php echo str_replace('margin:0 auto;', 'margin:0;background:#FFF;', str_replace('align="center"', 'align="left"', $enc_pagamento_dados)); ?></td>
                                </tr>
								<?php }?>
								<tr>
                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Metodo de Expedição:</td>
                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo showLayoutMetodosExpedicao($Metodo_expedicao)?></td>
                                </tr>
                                <tr>
                                    <td align="left" width="120" height="25" style="font-weight:bold;color:#000;font-size:12px;border-top:#ccc 1px solid">TOTAL COM IVA E PORTES:</td>
                                    <td align="left" style="font-weight:bold;color:#000;font-size:14px;border-top:#ccc 1px solid"><?php echo numberformat($TotalDocComIva)?></td>
                                </tr><? }?>
                                </table>
                           </td>
						</tr>
						</table>
						</div>
					</div>
						<br><?php if(!$nova){?>

						<div id="wraper" class="wraper">
						<div class="tabtop-open3" id="tab1">

						<table width="100%" border="0" cellpadding="0" cellspacing=0>
						<?php
						if(file_exists($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!=''){?>
						<tr>
						<td valign="top" width="100"><b>FATURA</b></td>
						<td valign="top"><a href="<?php echo $GLOBALS['site_http_url'].'files/encomendas/'.$enc_Fatura?>?dp=<?php echo date('YmdHis')?>" target="_blank"><img src="fatura.png" border="0" width="50" /></a></td>
						</tr>
						<?php }?>

						<tr><td align="left" width="120" colspan="2">&nbsp;</td></tr>
                        <tr>
							<td class="preto" align="left" style="font-size:12px"><i class="far fa-file-pdf"></i> <b>ANEXAR FATURA:</b></td>
							<td align="left"><input style="width:200px" class="form-control" type="file" name="fatura" id="fatura" /></td>
						</tr>
						</table>

						</div>
						</div>
						<br><? }?>

						<div id="wraper" class="wraper">
							<div class="tabtop-open3" id="tab1">
								<table width="100%" border="0" cellpadding="0" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="50%">
		                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
		                                <tr>
		                                	<td colspan="2" align="left" height="30"><i class="fas fa-truck"></i> <strong>INFORMAÇÃO PARA ENTREGA</strong></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120">Nome:</td>
		                                    <td align="left"><input name="enc_Nome_ent" style="width:90%" type="text" value="<?php echo $enc_Nome_ent?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Apelido:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Apelido_ent" style="width:90%" type="text" value="<?php echo $enc_Apelido_ent?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Morada:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Morada_ent" style="width:90%" type="text" value="<?php echo $enc_Morada_ent?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Cidade:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Cidade_ent" style="width:90%" type="text" value="<?php echo $enc_Cidade_ent?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Código Postal:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Codigo_Postal_ent" style="width:90%" type="text" value="<?php echo $enc_Codigo_Postal_ent?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">País:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid">
		                                    	<select class="inputregisto HvMd" style="width:195px" name="enc_Pais_ent" id="enc_Pais_ent">
				                                    <?
				                                    $sql='SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang="' . $site_lang . '" AND zone_visible=1 ORDER BY czl.zone_name ASC';
				                                    $rs_paises->db_open($db_link, $DB_name, $sql);
				                                    $primeiro_pais=0;
				                                    while (!$rs_paises->EOF()) {
				                                        $zone_name=$rs_paises->db_data['zone_name'];
				                                        $zone_id=$rs_paises->db_data['zone_id'];
				                                        echo '<option value="' . $zone_id . '" ';
				                                        if (!$enc_Pais_ent) {
				                                            $enc_Pais_ent=$primeiro_pais=165;
				                                        }
				                                        if ($enc_Pais_ent == $zone_id) {
				                                            echo 'selected="selected"';
				                                            $m2_pais_nome=$zone_name;
				                                        }
				                                        echo '>' . $zone_name . '</option>';
				                                        $rs_paises->db_next();
				                                    }
				                                    $rs_paises->db_close();
				                                    if (!$enc_Pais_ent) {
				                                        $enc_Pais_ent=$primeiro_pais;
				                                    }
				                                    ?>
				                                </select>
		                                    </td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Telefone:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Telefone_ent" style="width:90%" type="text" value="<?php echo $enc_Telefone_ent?>"></td>
		                                </tr>
		                                </table>
									</td>
		                            <td valign="top">
		                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
		                                <tr>
		                                    <td colspan="2" align="left" height="30"><i class="fas fa-coins"></i> <strong>INFORMAÇÃO PARA FATURAÇÃO</strong></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120">Nome:</td>
		                                    <td align="left"><input name="enc_Nome" style="width:90%" type="text" value="<?php echo $enc_Nome?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Apelido:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Apelido" style="width:90%" type="text" value="<?php echo $enc_Apelido?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">NIF:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid">
		                                    	<input name="enc_Nif" style="width:90%" type="text" value="<?php echo $enc_Nif?>">
		                                    </td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Morada:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Morada" style="width:90%" type="text" value="<?php echo $enc_Morada?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Cidade:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Cidade" style="width:90%" type="text" value="<?php echo $enc_Cidade?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">Código Postal:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Codigo_Postal" style="width:90%" type="text" value="<?php echo $enc_Codigo_Postal?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid">País:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid">
		                                    	<select class="inputregisto HvMd" style="width:195px" name="enc_Pais" id="enc_Pais">
				                                    <?
				                                    $sql='SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang="' . $site_lang . '" AND zone_visible=1 ORDER BY czl.zone_name ASC';
				                                    $rs_paises->db_open($db_link, $DB_name, $sql);
				                                    $primeiro_pais=0;
				                                    while (!$rs_paises->EOF()) {
				                                        $zone_name=$rs_paises->db_data['zone_name'];
				                                        $zone_id=$rs_paises->db_data['zone_id'];
				                                        echo '<option value="' . $zone_id . '" ';
				                                        if (!$enc_Pais) {
				                                            $enc_Pais=$primeiro_pais=165;
				                                        }
				                                        if ($enc_Pais == $zone_id) {
				                                            echo 'selected="selected"';
				                                            $m2_pais_nome=$zone_name;
				                                        }
				                                        echo '>' . $zone_name . '</option>';
				                                        $rs_paises->db_next();
				                                    }
				                                    $rs_paises->db_close();
				                                    if (!$enc_Pais) {
				                                        $enc_Pais=$primeiro_pais;
				                                    }
				                                    ?>
				                                </select>
		                                    </td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-phone"></i> Telefone:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Telefone" style="width:90%" type="text" value="<?php echo $enc_Telefone?>"></td>
		                                </tr>
		                                <tr>
		                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="far fa-envelope"></i> Email:</td>
		                                    <td align="left" style="border-top:#ccc 1px solid"><input name="enc_Email" style="width:90%" type="text" value="<?php echo $enc_Email?>"></td>
		                                </tr>
		                                </table>
									</td>
		                        </tr>
		                       </table><br>




                        <table width="100%" border=0 cellpadding="10" cellspacing=0 class="border1">
                        <tr>
                            <td valign="top" height="30"><i class="far fa-clipboard"></i> <strong>OBSERVAÇÕES ENVIADAS PELO CLIENTE QUANDO FEZ A ENCOMENDA</strong></td>
                        </tr>
                        <tr>
                            <td valign="top"><textarea style="height:80px" name="Obs"><? echo ($Obs);?></textarea></td>
                        </tr>
                        </table>

                        <br>
                        <table width="100%" border=0 cellpadding="10" cellspacing=0 class="border1">
                        <tr>
                            <td valign="top" height="30"><i class="far fa-clipboard"></i> <strong>MENSAGEM PERSONALIZADA PELO CLIENTE PARA EMBRULHO</strong></td>
                        </tr>
                        <tr>
                            <td valign="top"><textarea style="height:80px" name="embrulho_msg"><? echo ($embrulho_msg);?></textarea></td>
                        </tr>
                        </table>
						</div>
						</div>
						                        <br />

						<?php if(!$nova){?>


							<div id="wraper" class="wraper">
							<div class="tabtop-open3" id="tab1">
								<table width="100%" border="0" cellpadding="5" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="100%"><i class="far fa-envelope"></i> <strong>MENSAGENS</strong></td>
		                        </tr>
		                        <tr>
		                            <td valign="top" width="100%">Escolha uma mensagem padrão: <?php show_mesagens_padrao()?> <input onclick="window.open('../sa_msg_predefinidas/list.php','_top')" class="btn btn-primary" type="button" name="Configurar mensagens predefinidas" value=" Configurar mensagens predefinidas "> </td>
		                        </tr>
		                    	</table>
		                    	<div id="bloco_mensagem">
			                    	<table width="100%" border="0" cellpadding="5" cellspacing=0>
			                        <tr>
			                            <td valign="top" width="100%">Assunto: </td>
			                        </tr>
			                        <tr>
			                            <td valign="top" width="100%"><input type="text" id="titulo_mgs" name="titulo_mgs" style="width:100%"></td>
			                        </tr>
			                        <tr>
			                            <td valign="top" width="100%">Mensagem: </td>
			                        </tr>
			                        <tr>
			                            <td valign="top" width="100%"><textarea name="txt_msgs" id="txt_msgs" style="width:100%;height:100px"></textarea></td>
			                        </tr>
			                        <tr>
			                            <td valign="top" width="100%"><input class="btn btn-primary" type="button" name="Enviar" value=" Enviar " onclick="send_msg()"> </td>
			                        </tr>
			                    	</table>
		                    	</div>
		                    </div>
		                </div>

		                <div id="wraper" class="wraper">
							<div class="tabtop-open3" id="tab1">
								<table width="100%" border="0" cellpadding="5" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="100%"><i class="far fa-envelope"></i> <strong>MENSAGENS</strong></td>
		                        </tr>
		                        <tr>
		                            <td valign="top" width="100%">


											<table width=100% border=0 cellspacing=0 cellpadding=2>
		                            		<tr>
											<td valign="top" colspan=2 class="agenda2">
											<?

											/*if($ID_Cliente > 0)
											{*/
												global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
												$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
												$rs=new DataObj;
												$rs2=new DataObj;
												$sql="SELECT * FROM mensagens WHERE ID_Enc = $ID Order BY ID DESC";
												//$sql="SELECT * FROM mensagens WHERE ID_Cliente = $ID_Cliente Order BY ID DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												$cor="#f0f3f5";$class="r1";$n="1";
												$count=0;
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<table border=0 cellspacing=0 cellpadding=2 class="'.$class.'" width=100%><tr><td width=29 class="agenda2">&nbsp;</td><td height=20 class="agenda2"><a target="_new"  class=preto><b>'.$rs->db_data["idate"].'</b><br><b>'.$rs->db_data["Assunto"].'</b><br>'.$rs->db_data["Mensagem"].'</a></td></tr></table>';
														$rs->db_next();
													}
													$rs->db_close();
												}


												$sql="SELECT * FROM encomendas_emails WHERE ID_ORG = $ID Order BY ID DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$ID_Email=$rs->db_data["ID"];
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<table border=0 cellspacing=0 cellpadding=2 class="'.$class.'" width=100%><tr><td width=29 class="agenda2">&nbsp;</td><td height=20 class="agenda2">EMAIL: <b>'.$rs->db_data["Data_envio"].'</b>, <b>'.$rs->db_data["Subject"].'</b>

															<a target="_blank" href="ver_email.php?id_email='.$ID_Email.'" class="btn btn-default btn-xs btn-rounded p-l-10 p-r-10"><i class="fa fa-cog"></i> Visualizar</a>


														</td></tr></table>';
														$rs->db_next();
													}
													$rs->db_close();
												}

												if($count==0) echo '<table  class="'.$class.'" width=100%><tr><td height=100 class="agenda2" align=center>Sem mensagens!</td></tr></table>';
											//}
											?>
											</table>
		                            </td>
		                        </tr>
		                    	</table>
		                    </div>
		                </div>


							<? }?>
                        <br>


                        <div id="wraper2" class="wraper">
							<div class="tabtop-open3" id="tab1" style="background-color:#f1fdec">
								<table width="100%" border="0" cellpadding="5" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="100%"><i class="far fa-envelope"></i> <strong>PAGAMENTOS DE CLIENTES</strong></td>
		                        </tr>
		                        <tr>
		                            <td valign="top" width="100%">
											<table width=100% border=0 cellspacing=0 cellpadding=2>
		                            		<tr>
											<td valign="top" colspan=2 class="agenda2">
												<table border="0" cellspacing="0" cellpadding="4" width="100%">
													<tr><td>Data/Hora</td><td>Valor</td><td>Método</td><td>Utilizador</td></tr>
											<?

												function getNomeMetodoPagamentoLocal($Metodo_pagamento)
						                    	{
						                    		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$module_voucher,$admin_id;
						                    		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
													$rs=new DataObj;
						                    		$sql="SELECT * FROM metodos_pagamento_lang WHERE ID_PAI='".$Metodo_pagamento."'";
													$hRes=$rs->db_open($db_link,$DB_name,$sql);
													if(!$rs->EOF())
														return $rs->db_data["Cabecalho"];
													else
														return false;
						                    	}

												global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
												$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
												$rs=new DataObj;
												$rs2=new DataObj;
												$sql="SELECT * FROM encomendas_valores_pagos WHERE ID_ORG = $ID Order BY ID DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												$cor="#f0f3f5";$class="r1";$n="1";
												$count=0;
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<tr style="background-color: '.$cor.'"><td height=20 class="agenda2"><b>'.$rs->db_data["PagamentoData"].'</td><td><b>'.numberformat($rs->db_data["Valor"]).'</b></td><td>'.$rs->db_data["Tipo"].'</td><td>'.$rs->db_data["UserAP"].'</td></tr>';
														$rs->db_next();
													}
													$rs->db_close();
												}


												$sql="SELECT * FROM ifthen_mbway_logs WHERE Estado='33' AND ID_Encomenda = $ID Order BY ID DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$valor=$rs->db_data["Erros"];
														$valor=substr($valor,strpos($valor,'<Valor>')+7,strlen($valor));
														$valor=substr($valor,0,strpos($valor,'</Valor>'));
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<tr style="background-color: '.$cor.'"><td height=20 class="agenda2"><b>'.$rs->db_data["Data"].'</td><td><b>'.numberformat($valor).'</b></td><td>'.getNomeMetodoPagamentoLocal($Metodo_pagamento).'</td><td>Automático</td></tr>';
														$rs->db_next();
													}
													$rs->db_close();
												}



												$sql="SELECT * FROM sibs_mbway_logs WHERE Estado='13' AND ID_Encomenda = $ID Order BY ID DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<tr style="background-color: '.$cor.'"><td height=20 class="agenda2"><b>'.$rs->db_data["Data"].'</td><td><b>'.numberformat($rs->db_data["Valor"]).'</b></td><td>'.getNomeMetodoPagamentoLocal($Metodo_pagamento).'</td><td>Automático</td></tr>';
														$rs->db_next();
													}
													$rs->db_close();
												}



												$sql="SELECT * FROM vivawallet_data WHERE order_state='P' AND enc_id = $ID Order BY id DESC";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														$count++;
														if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
														else				{$cor="#FFFFFF";$class="r2";$n="2";}
														echo '<tr style="background-color: '.$cor.'"><td height=20 class="agenda2"><b>'.$rs->db_data["Timestamp"].'</td><td><b>'.numberformat($rs->db_data["total_cost"]/100).'</b></td><td>'.getNomeMetodoPagamentoLocal($Metodo_pagamento).'</td><td>Automático</td></tr>';
														$rs->db_next();
													}
													$rs->db_close();
												}



												// busca encomendas com pag. por ref. multibanco q tenham c coluna Encomenda_Paga=1
												$sql="SELECT Data_Criacao, TotalDocComIva, enc_pagamento_dados FROM encomendas WHERE ID = '$ID' AND Metodo_pagamento='17' AND Encomenda_Paga = '1'";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													$valor=$rs->db_data["enc_pagamento_dados"];
													// echo $valor;
													$valor=substr($valor,strpos($valor,'&euro;&nbsp;')+12,strlen($valor));
													$valor=substr($valor,0,strpos($valor,'</td>'));
													$count++;
													if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
													else				{$cor="#FFFFFF";$class="r2";$n="2";}
													echo '<tr style="background-color: '.$cor.'"><td height=20 class="agenda2"><b>'.$rs->db_data["Data_Criacao"].'</td><td><b>'.$valor.'&euro;</b></td><td>'.getNomeMetodoPagamentoLocal($Metodo_pagamento).'</td><td>Automático</td></tr>';
													$rs->db_close();
												}


												if($count==0) echo '<tr style="background-color: '.$cor.'"><td height=30 class="agenda2" align=center>Sem pagamentos registados!</td></tr>';

											?>
											</table>
		                            </td>
		                        </tr>
		                    	</table>

		                    	<div id="bloco_mensagem">
			                    	<table border="0" cellpadding="5" cellspacing=0>
			                        <tr>
			                        	<td width="50px">Data/Hora: </td>
			                            <td width="100px">
				                            <input type="datetime-local" id="PagamentoData" name="PagamentoData" value="">
				                        </td>
			                            <td width="50px">Valor: </td>
			                            <td width="100px"><input type="text" id="PagamentoValor" name="PagamentoValor" style="width:100%"></td>
			                            <td width="50px">Tipo: </td>
			                            <td width="270px">
			                            	<select name="PagamentoTipo">
			                            		<?
			                            		$sql="SELECT * FROM `metodos_pagamento` LEFT JOIN metodos_pagamento_lang ON metodos_pagamento_lang.ID_PAI=metodos_pagamento.ID WHERE `Visivel` = 1 ORDER BY `metodos_pagamento`.`Ordem` ASC ";
												$hRes=$rs->db_open($db_link,$DB_name,$sql);
												if(!$rs->EOF())
												{
													while(!$rs->EOF())
													{
														echo '<option value="'.$rs->db_data["Cabecalho"].'">'.$rs->db_data["Cabecalho"].'</option>';
														$rs->db_next();
													}
													$rs->db_close();
												}
			                            		?>
			                            	</select>
			                           	</td>
			                            <td width="100px"><input class="btn btn-primary" type="button" name="Enviar" value=" Adicionar pagamento " onclick="send_pagamento()"> </td>
			                        </tr>
			                    	</table>
		                    	</div>



		                    </div>
		                </div>


                        <?

						if($ATM!="")
						{?>
                       		<br />
                       		<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
	                        <tr>
	                            <td valign="top" ><br /><? echo $ATM;?><br /></td>
	                        </tr>
	                        </table><?
						}?>
                        <br />
						<table width="100%" border=0 cellspacing=0>
                        <tr>
                        	<td valign="top">
							<table width="100%" cellspacing="0" cellpadding="0" border="0" height="50px" class="p1">
							<tbody><tr>
							<td width="2%" align="center">&nbsp;</td>
							<td width="95%" valign="middle" style="font-weight:bold">PRODUTOS ENCOMENDADOS</td>
							</tr>
							</tbody>
							</table>
                            	<table width="100%" border="0" cellpadding="2" cellspacing="0">
                                <tr>
                                    <td align="center" height="30" width="80" style="font-weight:bold">ID Produto</td>
                                    <td align="left" width="70" style="font-weight:bold">Referência</td>
                                    <td align="left" style="font-weight:bold">Nome</td>
                                    <td align="left" width="5" style="font-weight:bold"></td>
                                    <td align="left" width="80" style="font-weight:bold">Preço</td>
                                    <td align="left" width="100" style="font-weight:bold">Preço Desconto</td>
                                    <td align="left" width="50" style="font-weight:bold">Qtd</td>
									<td align="left" width="80" style="font-weight:bold">Total Preço</td>
                                    <?php /* <td align="center" width="80px" >Código do Voucher</td> */?>
                                    <td align="left" width="200" style="font-weight:bold;text-align:center">Estado</td>
                                    <td align="left" width="20" >&nbsp;</td>
                                    <td align="left" width="20" >&nbsp;</td>
                                </tr><?php
								$sql = "SELECT * FROM encomendas_produtos WHERE ID_PAI=$ID Order By Estado=8";
								$rs->db_open($db_link, $DB_name, $sql);
								while(!$rs->EOF())
								{
									$IDb = $rs->db_data["ID"];
									// echo $IDb.'<br>';
									$ID_Cliente = $rs->db_data["ID_Cliente"];
									$ID_Produto = $rs->db_data["ID_Produto"];
									$Ref = $rs->db_data["Ref"];
									$Ean = $rs->db_data["EAN"];


									if(!$Ref)
									{
										$sql = 'SELECT Ref from products WHERE ID = '.$ID_Produto;
										//$sql = 'SELECT Ref,ID_Admin,idtime,udtime from products WHERE ID = '.$ID_Produto;
										$rs2->db_open($db_link, $DB_name, $sql);
										$Ref = $rs2->db_data["Ref"];

										/*$ID_Admin = $rs2->db_data["ID_Admin"];
										$idtime = $rs2->db_data["idtime"];
										$udtime = $rs2->db_data["udtime"];`*/
										$rs2->db_close();
									}
									$Nomes = explode('|', $rs->db_data["Nome"]);
									$Nome = $Nomes[0];
									$Nome_opcoes = str_replace('[br]', '<br>', $rs->db_data["Nome_opcoes"]);
									$Email_opcoes = ''; $Email_opcoes = $rs->db_data["Email_opcoes"];
									$Texto_opcoes = ''; $Texto_opcoes = $rs->db_data["Texto_opcoes"];
									$Qtd = $rs->db_data["Qtd"];
									$Tamanho = $rs->db_data["Tamanho"];
									$Cor = $rs->db_data["Cor"];
									$Valor = $rs->db_data["Valor"];
									$Valor_Desconto = $rs->db_data["Valor_Desconto"];
									$Tipo = $rs->db_data["Tipo"];
									$m_estado = $rs->db_data["Estado"];
									$enc_state =  estado_encomenda($m_estado, 'pt');
									//$cor_texto = str_replace('#', '', color_inverse($enc_state['cor']));
									$cor_texto='000000';

									$ID_Conf = $rs->db_data["ID_Conf"];
									$login = $rs->db_data["login"];
									$Chave = $rs->db_data["Chave"];
									$Obs = $rs->db_data["Obs"];
									$voucher_id=$rs->db_data["voucher_id"];
									$Apagar=$rs->db_data["Apagar"];
									$voucher_gift=$rs->db_data["voucher_gift"];


									// if($voucher_id>0) $ID_Produto='';
									// $info = get_voucher_info($voucher_id, 1);
									if($cor=='#a3df3d') $cor='#e2ed6e';
									else $cor='#a3df3d';
									/*<tr bgcolor="#<?php echo $enc_state['cor']?>" id="scroll<?php echo $ID_Produto?>">*/
									?>
									<tr bgcolor="<?php echo $cor?>" id="scroll<?php echo $ID_Produto?>">
										<input type="hidden" name="m_id_<?php echo $IDb?>" value="<?=$ID_Produto?>">
										<input type="hidden" name="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" /></td>
									<td style="min-height:35px;text-align:center;border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><a class="preto" target="_blank" style="color:#<?php echo $cor_texto?>" href="../products/products.php?ID=<? echo $ID_Produto?>"><u><?php echo $ID_Produto?></u></a></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><?php echo $Ref.'<br>'.$Ean?></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>">

										<?php echo $Nome?><? /* <br>

										<font style="font-size:10px"><?=$idtime;?> |
                                    	<?=$udtime;?> |
                                    	<?=$ID_Admin;?></font>*/?>


									</td>
									<td align="left" width="5px" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><input style="width:80px" type="text" name="Valor_<?php echo $IDb?>" value="<?php echo ($Valor)?>"></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><input style="width:80px" type="text" name="Valor_Desconto_<?php echo $IDb?>" value="<?php echo $Valor_Desconto?>"></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><input style="width:50px" type="text" name="Qtd_<?php echo $IDb?>" value="<?php echo $Qtd?>"></td></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto)?></td>
									<td align="right"  style="border-bottom:#000 1px solid">
										<select class="estados" name="m_estado_<?php echo $IDb?>" id="m_estado_<?php echo $IDb?>" data="<?php echo $ID_Produto?>" style="width:200px" class="form-control">
										<?php
										$sql="SELECT ee.ID, ee.Ordem, eel.Cabecalho, eel.Cabecalho2 FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI = ee.ID where Lang='".$admin_lang."' ORDER BY Ordem";
										$rs2->db_open($db_link,$DB_name,$sql);
										if(!$rs2->EOF())
										{
											$a=0;
											while(!$rs2->EOF())
											{
												$eel_id = $rs2->db_data['ID'];
												$Ordem = $rs2->db_data['Ordem'];
												$Cabecalho = ($rs2->db_data['Cabecalho2']!='' ? $rs2->db_data['Cabecalho2'] : $rs2->db_data['Cabecalho']);

												if($a == 0) echo '<option value="" '.(!$m_estado ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.'</option>';
												else echo '<option value="'.$eel_id.'" '.($m_estado==$eel_id ? 'selected="selected"' : '').'>'.$Ordem.' - '.$Cabecalho.'</option>';

												$a++;
												$rs2->db_next();
											}
										}
										$rs2->db_close();
										?>
										</select>
									</td>
									<td align="right" style="border-bottom:#000 1px solid"><a href="javascript:;" title="REMOVER ARTIGO DA ENCOMENDA" onclick="remove_artigo(<?php echo $IDb?>)"><img border="0" src="del.png" /></a></td>
									<td align="right" style="border-bottom:#000 1px solid"></td>
									</tr>
									<?php
									if($Email_opcoes != '')
									{?>
									<tr bgcolor="#<?php echo $enc_state['cor']?>">
	                                    <td align="center" height="20" width="80" style="font-weight:bold"><img src="tree.png" alt="" border="0" /></td>
	                                    <td align="left" colspan="10" style="font-weight:normal"><b>EMAIL PARA O ENVIO DO VOUCHER:</b><?php echo ' '.$Email_opcoes.'';?></td>
									</tr>
									<?php if($Texto_opcoes != ''){?>
									<tr bgcolor="#<?php echo $enc_state['cor']?>">
	                                    <td align="center" height="20" width="80" style="font-weight:bold"><img src="tree.png" alt="" border="0" /></td>
	                                    <td align="left" colspan="10" style="font-weight:normal"><b>MENSAGEM PARA O ENVIO DO VOUCHER:</b><?php if($Texto_opcoes != ''){ echo ' '.$Texto_opcoes.'';}?></td>
									</tr>
									<?php } if($voucher_gift != ''){?>
									<tr bgcolor="#<?php echo $enc_state['cor']?>">
	                                    <td align="center" height="20" width="80" style="font-weight:bold"><img src="tree.png" alt="" border="0" /></td>
	                                    <td align="left" colspan="10" style="font-weight:normal"><b>VOUCHER ATRIBUÍDO:</b><?php echo ' '.$voucher_gift.'';?></td>
									</tr>
									<?php }?>
									<?php
									}
									$rs->db_next();
								}

								$rs->db_close();
								?>
								<tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left" width="5px"></td>
                                    <td align="right" class="preto" colspan="7">
									<script>
									// $(document).ready(function()
									// {
										function envia_email()
										{
											var escolha='';
											$('.estados').each(function(i, obj)
											{
												if($(this).val()==4 || $(this).val()==9)
												{
													if(escolha != '') escolha += ';' + $(this).attr('data');
													else escolha += $(this).attr('data');
												}
											});

											if(escolha!='') $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="send_mail.php?prods='+escolha+'&enc_id=<?php echo $ID?>"></iframe>', width : 754, maxHeight : 500, titlePosition: 'over', title: 'Enviar Email a cliente da alteração', scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } });
											else alert('Não existem produtos com os estados esgotado e/ou indisponivel');
										}

										function envia_email2(prod_id)
										{
											var escolha = prod_id;
											$.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="send_mail.php?prods='+escolha+'&enc_id=<?php echo $ID?>"></iframe>', width : 754, maxHeight : 500, titlePosition: 'over', title: 'Enviar Email a cliente da alteração', scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } });
										}
									// });
									</script>
									</td>
                                </tr>
                                <tr bgcolor="#ccc">
									<td style="min-height:35px;text-align:center"><input style="width:80px" type="text" name="novo_id" value=""></a></td>
									<td align="left"><input style="width:80px" type="text" name="novo_ref"></td>
									<td align="left">&nbsp;</td>
									<td align="left" width="5px" style="color:#<?php echo $cor_texto?>"></td>
									<td align="left" style="color:#<?php echo $cor_texto?>"><input style="width:80px" type="text" name="novo_valor" value=""></td>
									<td align="left" style="color:#<?php echo $cor_texto?>"><input style="width:80px" type="text" name="novo_desconto" value=""></td>
									<td align="left" style="color:#<?php echo $cor_texto?>"><input style="width:50px" type="text" name="novo_qtd" value=""></td></td>
									<td align="left"><input class="btn btn-primary" type="button" name="cancelar" value=" ADICIONAR ARTIGO " onClick="window.myForm.submit()"></td>
									<td align="left"></td>
									<td align="left">&nbsp;</td>
									<td align="left">&nbsp;</td>
								</tr>
								<tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4"></td>
                                    <td align="right" width="150px" ></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4">TOTAL DE IVA</td>
                                    <td align="right" width="150px" ><?php echo numberformat($TotalDocIva)?></td>
                                    <td>&nbsp;</td>

                                </tr>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">TAXAS</td>
                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><input style="width:50px" type="text" name="Taxas" value="<?=$Taxas?>"></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">VALOR DE PORTES</td>
                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><input style="width:50px" type="text" name="Portes" value="<?=$Portes?>"></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">TOTAL COM PORTES</td>
                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><?php echo numberformat($TotalDocComIva)?></td>
                                    <td>&nbsp;</td>
                                </tr>
                                </table>
							</td>
                        </tr>
                        </table>
                	</td>
				</tr>
                </table>
				<?php show_page_buttons2();?>
            </td>

		</tr>
		</table>

		</form>

</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php	include "../layout/bottom.inc.php";?>