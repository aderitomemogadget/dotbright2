<?php

	include "settings.inc.php";

	if($task=="apagar")	{$task_button="Apagar";$task_form="apaga";}

	elseif($ID==0){$task_button="Inserir";$task_form="insere";}

	else {$task_button="Alterar";$task_form="altera";}

	$D=getdate();

	$D2=getdate();

	$C="";$T="";$I1="";$I2="";$ordem="0";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$module_voucher;

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$rs=new DataObj;$rs2=new DataObj;

	

	include "../layout/header.inc.php";	

	include "../layout/body.inc.php";

	

	if($ID)

	{

		$sql='UPDATE encomendas SET Lida="1" where ID='.$ID;

		$res=Runquery($db_link,$DB_name,$sql);

		if(!$res)echo db_error($db_link).$sql."<br>";

	}

	

	if(isset($_POST['fac_send']) && $_POST['fac_send'] == 1)

	{ 

		$ficheiro = submete_ficheiro('fatura', '', intval($_GET["ID"]));

		

		$sql='UPDATE encomendas set enc_Fatura="'.$ficheiro.'" WHERE ID='.intval($_GET["ID"]);

		$res = Runquery($db_link,$DB_name,$sql);

	}

	

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

			

			// echo '$x_estado:'.$x_estado.'<br>';

			/* if($x_estado == 11) // SE FOR UM GIFT VOUCHER

			{				

				$voucher_code = uniqId($ID_Enc.$ID_Produto);

				$prods = get_produtc_info($ID_Produto);

				// echo '$voucher_gift:'.$voucher_gift.'<br>';

				// echo '$fam_id:'.$prods['fam_id'].'<br>';

				if($voucher_gift == '' && $prods['fam_id'] == 29) // É UM PRODUTO GIFT

				{

					$sql = "

							INSERT INTO 

								vouchers

									(voucher_title, voucher_type, voucher_type_discount, voucher_dataini, voucher_datafim, voucher_usage, voucher_code, voucher_prod_id, voucher_cliente_id, voucher_discount, voucher_partner_profit, voucher_acumulavel)

									VALUES 

									('".addslashes($prods['prod_nome'])."', '0', '1', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s', strtotime('+1 year'))."', '1', '".$voucher_code."', '0', '0', '".$prods['prod_precos']['PVP']."', '0.0000', '0')

							";

					$res = Runquery($db_link, $DB_name, $sql);

					$sql = 'UPDATE encomendas_produtos SET Estado="'.$x_estado.'", voucher_gift="'.$voucher_code.'" WHERE ID = "'.$x_id.'"';

				}

				else

				{

					$sql = 'UPDATE encomendas_produtos SET Estado="'.$x_estado.'" WHERE ID = "'.$x_id.'"';

				}

				

				

				

			}

			else */ 

			

			

			

			

			

			

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

			$email=$rs->db_data["email"];

			$Obs=$rs->db_data["Obs"];

			$Portes=$rs->db_data["Portes"];

			$Portes_Especial=$rs->db_data["Portes_Especial"];

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

			



			//Especificos Website

			$embrulhar 			= $rs->db_data['embrulhar'];

			$embrulho_msg		= $rs->db_data['embrulho_msg'];

		}

		$rs->db_close();

	}



?>

<link rel="stylesheet" type="text/css" href="sa.css" />

<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />

<script type="text/javascript">

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

function cancelar_oney(n_encomenda = 0)
{
	valor = $('#oney_valor_cancelar').val();

	$.ajax({
			type: 'POST', 
			url: '../../external_api2/pagamentos/pgm_oney/cancel_payment_cancellation.php',
			async: false,
			data: { n_encomenda : n_encomenda, valor:valor }, 
			dataType: 'html',
			success: function(data)
			{
				if(data == 1)
				{
					$("#alert-success").show().delay(5000).fadeOut();
				}
				else if(data == 0)
				{
					$("#alert-danger").show().delay(5000).fadeOut();
				}
			},
			error: function()
			{
				
			}
		});
}

function enviar_entrega(n_encomenda = 0)
{
	$.ajax({
			type: 'POST', 
			url: '../../external_api2/pagamentos/pgm_oney/confirm_ready_to_deliver.php',
			async: false,
			data: { n_encomenda:n_encomenda }, 
			dataType: 'html',
			success: function(data)
			{
				if(data == 1)
				{
					$("#alert-success2").show().delay(5000).fadeOut();
				}
				else if(data == 0)
				{
					$("#alert-danger2").show().delay(5000).fadeOut();
				}
			},
			error: function()
			{
				
			}
		});
}



</script>



<div class="center">

	

		

    	<table border=0 cellpadding=0 cellspacing=0 align="center">

        <tr>

            <td valign="top" align="left"><? 

				include "../layout/menus.inc.php"; ?>

                <table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center" id="area_impressao">

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

								<tr>

                                	<td colspan="2" align="left" height="30"><strong>RESUMO</strong></td>

                              	</tr>

								<tr> 

                                    <td  align="left" width="120" >Data da encomenda:</td>

                                    <td  align="left"><?php echo $Data_Criacao?></td>

                                </tr>

                                <tr>

                                    <td  align="left" width="190" >Estado da encomenda:</td>

                                    <td  align="left">	

									<?php

									$enc_state =  estado_encomenda($Estado, 'pt');

									echo '<span style="color:#000;font-weight:bold">'.$enc_state['titulo'].'</span>';

									?>				

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

		                                    <td  align="left" width="120" >Estado do PayPal:</td>

		                                    <td  align="left"><?php echo $Encomenda_Status?></td>

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

		                                    <td  align="left" width="120" >Estado da Unicre:</td>

		                                    <td  align="left"><?php echo $Encomenda_Status?></td>

		                                </tr>

										<? 

									}

								?>

                                <tr>

                                    <td  align="left" width="120" >Nº da encomenda:</td>

                                    <td  align="left"><?php echo $ID?></td>

                                </tr>

                                <tr> 

                                    <td  align="left" width="120" >Metodo de Pagamento:</td>

                                    <td  align="left"><?php echo getNomeMetodoPagamentoByTipo_bo($Metodo_pagamento)?></td>
                                    
                                </tr>
                                <?php if($Metodo_pagamento == 16){ ?>
                            	<tr> 

                                    <td  align="left" width="120" >
                                    	Valor a Cancelar:
                                    </td>
                                    <td>
                                    	<input type="number" class="numericOnly" id="oney_valor_cancelar" name="oney_valor_cancelar" min="0" max="<?php echo number_format($TotalDocComIva,2) ?>" value="<?php echo number_format($TotalDocComIva,2)?>">
                                    	<input type="button" class="botao" name="cancelar_oney" value="Cancelar Oney" onclick="cancelar_oney(<?= $IDb ?>)">
                                    	<input type="button" class="botao" name="enviar_entrega" value="Enviar para Entrega" onclick="enviar_entrega(<?= $IDb ?>)">
                                    </td>
                                </tr>

                                <tr>
                                	<td colspan="2">
                                		<div class="alert alert-success" id="alert-success" role="alert" style="display: none;">
										  Pagamento Cancelado com sucesso.
										</div>
										<div class="alert alert-danger" id="alert-danger" role="alert"  style="display: none;">
										  Surgiu um problema a cancelar o pagamento.
										</div>
                                	</td>
                                </tr>

                                <tr>
                                	<td colspan="2">
                                		<div class="alert alert-success" id="alert-success2" role="alert" style="display: none;">
										  Pedido de entrega enviado com sucesso.
										</div>
										<div class="alert alert-danger" id="alert-danger2" role="alert"  style="display: none;">
										  Surgiu um problema a enviar a entrega.
										</div>
                                	</td>
                                </tr>

                                <?php } ?>

								<tr> 

                                    <td  align="left" width="120" >Metodo de Expedição:</td>

                                    <td  align="left"><?php echo getNomeMetodoExpedicao_bo($Metodo_expedicao)?></td>

                                </tr>

								

                                <tr> 

                                    <td  align="left" width="120" height="25" style="font-weight:bold;color:#000;font-size:14px;border-top:#000 1px solid">TOTAL:</td>

                                    <td  align="left" style="font-weight:bold;color:#000;font-size:14px;border-top:#000 1px solid"><?php echo numberformat($TotalDocComIva)?></td>

                                </tr> 

								<?php if(getNomeMetodoPagamentoTipo($Metodo_pagamento) != 3){?>

								<tr> 

                                    <td  align="left" width="120" height="25" style="font-weight:bold;color:#000;font-size:14px;border-top:#000 1px solid">&nbsp;</td>

                                    <td  align="left" style="font-weight:bold;color:#000;font-size:14px;border-top:#000 1px solid"><?php echo str_replace('margin:0 auto;', 'margin:0;background:#FFF;', str_replace('align="center"', 'align="left"', $enc_pagamento_dados)); ?></td>

                                </tr> 

								<?php }?>									

                                </table>

                           </td>

						</tr>

						</table>

						</div>

					</div>

						<br>

						

						<div id="wraper" class="wraper">

						<div class="tabtop-open3" id="tab1">

						<form action="pagina.php?ID=<?php echo $_GET['ID']?>" name="form_env_email" method="post" enctype="multipart/form-data">

						<input type="hidden" name="fac_send" value="1">

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

						<td class="preto" align="left" style="font-size:12px"><b>ANEXAR FATURA:</b></td>

						<td align="left"><input style="width:200px" class="box" type="file" name="fatura" id="fatura" /></td>						

						</tr>

						<tr><td align="left" width="120" colspan="2">&nbsp;</td></tr>

						<tr>						

						<td class="preto" align="left" style="font-size:12px">&nbsp;</td>

						<td align="left"><input type="submit" class="botao" value="SUBMETER FICHEIRO" /></td>						

						</tr>

						</table>

						</form>

						</div>

						</div>

						<br>

						

						<div id="wraper" class="wraper">

						<div class="tabtop-open3" id="tab1">

						<table width="100%" border="0" cellpadding="0" cellspacing=0>

                       

                        <tr>

                            <td valign="top" width="50%">

                                <table width="100%" border="0" cellspacing="0" cellpadding="4">

                                <tr>

                                	<td colspan="2" align="left"  height="30"><strong>INFORMAÇÃO PARA ENTREGA</strong></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120">Nome:</td>

                                    <td align="left"><?php echo $enc_Nome_ent?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Apelido:</td>

                                    <td align="left"><?php echo $enc_Apelido_ent?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Morada:</td>

                                    <td align="left"><?php echo $enc_Morada_ent?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Cidade:</td>

                                    <td align="left"><?php echo $enc_Cidade_ent?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Código Postal:</td>

                                    <td align="left"><?php echo $enc_Codigo_Postal_ent?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >País:</td>

                                    <td align="left"><?php echo $enc_Pais_ent?></td>

                                </tr> 

                                </table>

							</td>

                            <td valign="top">

                                <table width="100%" border="0" cellspacing="0" cellpadding="4">

                                <tr>

                                    <td colspan="2" align="left"  height="30"><strong>INFORMAÇÃO PARA FATURAÇÃO</strong></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Nome:</td>

                                    <td align="left"><?php echo $enc_Nome?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Apelido:</td>

                                    <td align="left"><?php echo $enc_Apelido?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >NIF:</td>

                                    <td align="left"><?php echo $enc_Nif?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Morada:</td>

                                    <td align="left"><?php echo $enc_Morada?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Cidade:</td>

                                    <td align="left"><?php echo $enc_Cidade?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Código Postal:</td>

                                    <td align="left"><?php echo $enc_Codigo_Postal?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >País:</td>

                                    <td align="left"><?php echo $enc_Pais?></td>

                                </tr> 

                                <tr>

                                    <td align="left" width="120" >Telefone:</td>

                                    <td align="left"><?php echo $enc_Telefone?></td>

                                </tr>

                                <tr>

                                    <td align="left" width="120" >Email:</td>

                                    <td align="left"><?php echo $enc_Email?></td>

                                </tr>

                                </table>

							</td>

                        </tr>

                       </table>

					</div>

					</div>

                       <br />

                       <? if($Obs)

					   {?>

	                        <br>

	                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">

	                        <tr> 

	                            <td valign="top"  height="30"><strong>OBSERVAÇÕES</strong></td>

	                        </tr>

	                        <tr> 

	                            <td valign="top"><? echo nl2br($Obs);?></td>

	                        </tr>

	                        </table><?

						}

						if($embrulhar==1)

						{?>

	                        <br>

	                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">

	                        <tr> 

	                            <td valign="top"  height="30"><strong>MENSAGEM PERSONALIZADA PARA EMBRULHO</strong></td>

	                        </tr>

	                        <tr> 

	                            <td valign="top"><? echo nl2br($embrulho_msg);?></td>

	                        </tr>

	                        </table><?

						}

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

							<td width="95%" valign="middle" style="font-weight:bold;color:#FFF">Produtos Encomendados</td>

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

									

									$ID_Cliente = $rs->db_data["ID_Cliente"];

									$ID_Produto = $rs->db_data["ID_Produto"];

									$Ref = $rs->db_data["Ref"];

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

									$cor_texto = str_replace('#', '', color_inverse($enc_state['cor']));

									

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

									// echo $Tipo;

									?>

									<?php if($Tipo == 1) {?>

									<tr bgcolor="#<?php echo $enc_state['cor']?>" id="scroll<?php echo $ID_Produto?>"> 

									<td style="min-height:35px;text-align:center;border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><a class="preto" target="_blank" style="color:#<?php echo $cor_texto?>" href="<?php echo $site_http_url?>admin/products/products.php?ID=<? echo $ID_Produto?>"><u><?php echo $ID_Produto?></u></a></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;"><?php echo $Ref?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo $Nome?></td> 

									<td align="left" width="5px" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;"></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo numberformat($Valor)?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php if($Valor_Desconto>0) echo ' - '.numberformat($Valor_Desconto); else echo numberformat($Valor_Desconto);?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo $Qtd?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto)?></td>

									<td align="right"  style="border-bottom:#000 1px solid">



									<select class="estados" name="m_estado_<?php echo $IDb?>" id="m_estado_<?php echo $IDb?>" data="<?php echo $ID_Produto?>" style="width:200px" class="chzn-select">

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

									<td align="right" style="border-bottom:#000 1px solid" ><a href="javascript:;" title="GUARDAR ESTADO DA ENCOMENDA" onclick="update_status(<?php echo $IDb?>)"><img border="0" src="save.png" /></a></td>

									<td align="right" style="border-bottom:#000 1px solid" ><?php if($m_estado == 11){?><a title="ENVIAR EMAIL COM VOUCHER" href="javascript:;" onclick="envia_email2(<?php echo $IDb?>)"><img border="0" height="25" src="img/next-item.png" /></a><?php }else{?><img border="0" src="blank.gif" /><?php }?></td>

									</tr>

									<?php

									}

									else

									{

										?>

									<tr bgcolor="#<?php echo $enc_state['cor']?>" id="scroll<?php echo $ID_Produto?>"> 

									<td height="25" style="min-height:35px;text-align:center;border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" >&nbsp;</td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;">&nbsp;</td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo $Nome?></td> 

									<td align="left" width="5px" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;">&nbsp;</td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo ($Valor > 0 ? numberformat($Valor) : '')?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php if($Valor_Desconto>0) echo ' - '.($Valor_Desconto > 0 ? numberformat($Valor_Desconto) : ''); else echo ($Valor_Desconto > 0 ? numberformat($Valor_Desconto) : '');?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo ($Qtd > 0 ? $Qtd : '')?></td>

									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" >&nbsp;</td>

									<td align="right"  style="border-bottom:#000 1px solid">&nbsp;</td>

									<td align="right" style="border-bottom:#000 1px solid" >&nbsp;</td>

									<td align="right" style="border-bottom:#000 1px solid" ><?php if($m_estado == 11){?><a title="ENVIAR EMAIL COM VOUCHER" href="javascript:;" onclick="envia_email2(<?php echo $IDb?>)"><img border="0" height="25" src="img/next-item.png" /></a><?php }else{?><img border="0" src="blank.gif" /><?php }?></td>

									</tr>

									<?php

									}

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

								<tr bgcolor="#bdbdbd"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                   

                               

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

									

									<input type="button" class="botao" value="ENVIAR EMAIL PARA ESTADOS INDISPONIVEL/ESGOTADO" onclick="envia_email()" /><br><br>

									</td>

              

                                </tr>

				

								<tr bgcolor="#bdbdbd"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left"  colspan="2">TOTAL IVA</td>

                                    <td align="left" width="50px" ><?php echo numberformat($TotalDocIva)?></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="right" class=preto >&nbsp;</td>

                                    <td align="right">&nbsp;</td>

                 

                                </tr>

                                <tr bgcolor="#d3d2d2"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left"  colspan="2">TAXAS</td>

                                    <td align="left" width="50px" ><?php echo numberformat($Taxas)?></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="right" class=preto >&nbsp;</td>

                                    <td align="right">&nbsp;</td>



                                </tr>

                                <tr bgcolor="#bdbdbd"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left"  colspan="2">PORTES</td>

                                    <td align="left" width="50px" ><?php echo numberformat($Portes)?></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="right" class=preto >&nbsp;</td>

                                    <td align="right">&nbsp;</td>



                                </tr>

								<tr bgcolor="#bdbdbd"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left"  colspan="2">ENTREGA ESPECIAL</td>

                                    <td align="left" width="50px" ><?php echo numberformat($Portes_Especial)?></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="right" class=preto >&nbsp;</td>

                                    <td align="right">&nbsp;</td>



                                </tr>

                                <tr bgcolor="#d3d2d2"> 

                                    <td height="30" style="text-align:center" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left" >&nbsp;</td> <td align="left" width="5px"></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="left"  colspan="2">TOTAL ENCOMENDA</td>

                                    <td align="left" width="50px" ><?php echo numberformat($TotalDocComIva)?></td>

                                    <td align="left" >&nbsp;</td>

                                    <td align="right" class=preto >&nbsp;</td>

                                    <td align="right">&nbsp;</td>



                                </tr> 

                                </table>

							</td>

                        </tr>

                        </table>

                	</td>

				</tr>

                </table>

				<?php show_page_buttons();?>

            </td>

            

		</tr>

		</table>

    

</div>

<script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
	
	$(".numericOnly").keyup(function() {
		
	  var sanitized=$(this).val().replace(/[^0-9]\,/g,"");
	  $(this).val(sanitized);

	  console.log(sanitized);

	  var max=parseFloat($(this).attr('max'));
	  var min=parseFloat($(this).attr('min'));

	  console.log(max);
	  console.log(min);
	  if (sanitized > max)
	  {
	      $(this).val(sanitized.slice(0,-1));
	  }
	  else if (sanitized <= min)
	  {
	      $(this).val("");
	  }
	});

</script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>

<?php	include "../layout/bottom.inc.php";?>