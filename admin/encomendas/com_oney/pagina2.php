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
	if($x_id)
	{
		$sql="Select * from encomendas_produtos WHERE ID=$x_id";
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$tipo=$rs->db_data["Tipo"];
			$ID_Enc=$rs->db_data["ID_PAI"];
			$ID_Produto=$rs->db_data["ID_Produto"];
			$ID_Cliente=$rs->db_data["ID_Cliente"];
			$Estado_anterior=$rs->db_data["Estado"];
			$rs->db_close();
			
			if($tipo==1)
			{
				//packs
				$sql="Select * from packs WHERE ID=$ID_Produto";
				$db_res=$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$NLances=$rs->db_data["NLances"];
					$rs->db_close();
				}
				if($NLances>0)
				{
					$sql="Select * from clientes WHERE ID=$ID_Cliente";
					$db_res=$rs->db_open($db_link,$DB_name,$sql);
					if(!$rs->EOF())
					{
						$Carteira=$rs->db_data["Carteira"];
						$rs->db_close();
					}
					if($x_estado==1)//aceite
					{
						$Carteira=$Carteira+$NLances;					
						$sql='UPDATE clientes SET Carteira="'.$Carteira.'" WHERE ID = "'.$ID_Cliente.'"';
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res)echo db_error($db_link).$sql."<br>";
					}
					elseif($Estado_anterior==1)//deixou de ser aceite e estava aceite
					{
						$Carteira=$Carteira-$NLances;
						if($Carteira<0)$Carteira=0;
						$sql='UPDATE clientes SET Carteira="'.$Carteira.'" WHERE ID = "'.$ID_Cliente.'"';
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res)echo db_error($db_link).$sql."<br>";
					}
				}
			}
			$sql='UPDATE encomendas_produtos SET Estado="'.$x_estado.'" WHERE ID = "'.$x_id.'"';
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res)echo db_error($db_link).$sql."<br>";
			$notify=1;
			//Agora vamos atualizar o estado geral
			$sql="Select * from encomendas_produtos WHERE ID_PAI='".$ID_Enc."' AND Estado <> '$x_estado'";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs->EOF())
			{
				//como nao existe mais nenhum produto com outro estado, vamos alterar o estado da encomenda para este.
				$sql='UPDATE encomendas SET Estado="'.$x_estado.'" WHERE ID = "'.$ID_Enc.'"';
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";			
			}
			$rs->db_close();
		}
	}
	if($ID!=0)
	{
		$sql="Select * from encomendas WHERE ID=$ID";

		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$IDb=$rs->db_data["ID"];
			$Referencia=$rs->db_data["Referencia"];
			$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
			
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
			$Encomenda_Status = $rs->db_data["Encomenda_Status"];
			$ATM = base64_decode($rs->db_data["ATM"]);
			
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
<script>
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
	var url="pagina.php?ID=<?=$ID?>&x_id="+n;
	if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
	window.open(url,"_self");
}
</script>
<style>td.preto2{font-size:11px}</style>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
		<input type="hidden" name="slashes" value="'">
    	<table width="880px" border=0 cellpadding=0 cellspacing=0 align="center">
        <tr>
            <td valign="top" align="left"><? 
				include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="0" align="center" id="area_impressao">
                <tr> 
                    <td valign="top" align="left">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">
                                <table width="100%" border=0 cellspacing=0 cellpadding="4">
								<tr>
                                	<td colspan="2" align="left" class="preto2" height="30"><strong>RESUMO</strong></td>
                              	</tr>
								<tr> 
                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Data da encomenda:</td>
                                    <td class="preto2" align="left"><?=$Data_Criacao?></td>
                                </tr>
                                <tr>
                                    <td class="preto2" align="left" width="190" style="font-weight:bold">Estado da encomenda:</td>
                                    <td class="preto2" align="left">	
									<?
									if(!$Estado) echo '<font color="#e39f1c">Pendente</font>';
                 	if($Estado==2) echo '<font color="#FF0000">Cancelada</font>';
									if($Estado==3) echo '<font color="#0000FF">Pagamento Aceite</font>';
									if($Estado==4) echo '<font color="#FF0000">Pagamento Cancelado</font>';
									if($Estado==5) echo '<font color="#FF0000">Pagamento Pendente</font>';
									if($Estado==6) echo '<font color="#FF0000">Em Expedição</font>';
									if($Estado==7) echo '<font color="#FF0000">Concluído</font>';
									?>				
									</td>
                                </tr>  
								<tr>
                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Encomenda foi paga:</td>
                                    <td class="preto2" align="left"><?php echo ($Encomenda_Paga==1 || $Estado==3 || $Estado==1|| $Estado==6|| $Estado==7) ? '<font color="#0000FF">Sim</font>' : '<font color="#FF0000">Não</font>'; ?></td>
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
		                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Estado do PayPal:</td>
		                                    <td class="preto2" align="left"><?=$Encomenda_Status?></td>
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
		                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Estado da Unicre:</td>
		                                    <td class="preto2" align="left"><?=$Encomenda_Status?></td>
		                                </tr>
										<? 
									}
								?>
                                <tr>
                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Nº da encomenda:</td>
                                    <td class="preto2" align="left"><?=$ID?></td>
                                </tr>
                                <tr> 
                                    <td class="preto2" align="left" width="120" style="font-weight:bold">Metodo de Pagamento:</td>
                                    <td class="preto2" align="left"><?=getNomeMetodoPagamentoByTipo($Metodo_pagamento)?></td>
                                </tr>
                                <tr> 
                                    <td class="preto2" align="left" width="120" style="font-weight:bold;color:#00b311;font-size:14px">TOTAL:</td>
                                    <td class="preto2" align="left" style="font-weight:bold;color:#00b311;font-size:14px"><?=numberformat($TotalDocComIva)?></td>
                                </tr>          
                                </table>
                           </td>
						</tr>
                        </table>
                        <br />
                        <table width="880px" border=1 cellspacing=0 cellpadding="10" bgcolor="#F6F6F6" class="border1">
                        <tr>
                            <td valign="top" width="300">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                	<td colspan="2" align="left" class="preto2" height="30"><strong>INFORMAÇÃO PARA ENTREGA</strong></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Nome:</td>
                                    <td class="preto" align="left"><?=$enc_Nome_ent?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Apelido:</td>
                                    <td class="preto" align="left"><?=$enc_Apelido_ent?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Morada:</td>
                                    <td class="preto" align="left"><?=$enc_Morada_ent?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Cidade:</td>
                                    <td class="preto" align="left"><?=$enc_Cidade_ent?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Código Postal:</td>
                                    <td class="preto" align="left"><?=$enc_Codigo_Postal_ent?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">País:</td>
                                    <td class="preto" align="left"><?=$enc_Pais_ent?></td>
                                </tr> 
                                </table>
							</td>
                            <td valign="top">
                                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                    <td colspan="2" align="left" class="preto2" height="30"><strong>INFORMAÇÃO PARA FATURAÇÃO</strong></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Nome:</td>
                                    <td class="preto" align="left"><?=$enc_Nome?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Apelido:</td>
                                    <td class="preto" align="left"><?=$enc_Apelido?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">NIF:</td>
                                    <td class="preto" align="left"><?=$enc_Nif?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Morada:</td>
                                    <td class="preto" align="left"><?=$enc_Morada?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Cidade:</td>
                                    <td class="preto" align="left"><?=$enc_Cidade?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Data Nascimento:</td>
                                    <td class="preto" align="left"><?=$enc_DataNascimento?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Sexo:</td>
                                    <td class="preto" align="left">
                                    	<?php 
                                    		if(!$enc_Sexo)	echo '';
                                    		elseif($enc_Sexo==1)	echo 'Masculino';
                                    		elseif($enc_Sexo==2)	echo 'Feminino';
                                    	?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Código Postal:</td>
                                    <td class="preto" align="left"><?=$enc_Codigo_Postal?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">País:</td>
                                    <td class="preto" align="left"><?=$enc_Pais?></td>
                                </tr> 
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Telefone:</td>
                                    <td class="preto" align="left"><?=$enc_Telefone?></td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="120" style="font-weight:bold">Email:</td>
                                    <td class="preto" align="left"><?=$enc_Email?></td>
                                </tr>
                                </table>
							</td>
                        </tr>
                       </table>
                       <br />
                       <? if($Obs)
					   {?>
	                        <br>
	                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
	                        <tr> 
	                            <td valign="top" class="preto2" height="30"><strong>OBSERVAÇÕES</strong></td>
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
	                            <td valign="top" class="preto2" height="30"><strong>MENSAGEM PERSONALIZADA PARA EMBRULHO</strong></td>
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
						<table width="880px" border=0 cellspacing=0 bgcolor="#F6F6F6" class="border1">
                        <tr>
                        	<td valign="top">
                            	<table width="100%" border=0 cellpadding="2" cellspacing="4">
                                <tr>
                                    <td class="preto" align="left" height="30" width="50px" style="font-weight:bold;text-align:center">ID Produto</td>
                                    <td class="preto" align="left" width="70px" style="font-weight:bold">Referência</td>
                                    <td class="preto" align="left" style="font-weight:bold">Nome</td>
                                    <td class="preto" align="left" width="5px"></td>
                                    <td class="preto" align="left" width="80px" style="font-weight:bold">Preço</td>
                                    <td class="preto" align="left" width="80px" style="font-weight:bold">Preço Desconto</td>
                                    <td class="preto" align="left" width="50px" style="font-weight:bold">Qtd</td>
									<td class="preto" align="left" width="80px" style="font-weight:bold">Total Preço</td>
                                    <td class="preto" align="center" width="80px" style="font-weight:bold">Código do Voucher</td>
                                    <td class="preto" align="left" width="100px" style="font-weight:bold;text-align:center">Estado</td>
                                    <td class="preto" align="left" width="20px" style="font-weight:bold">&nbsp;</td>
                                </tr><?
								$sql="Select * from encomendas_produtos WHERE ID_PAI=$ID";
								$db_res=$rs->db_open($db_link,$DB_name,$sql);
								while(!$rs->EOF())
								{
									$IDb=$rs->db_data["ID"];
									$ID_Cliente=$rs->db_data["ID_Cliente"];
									$ID_Produto=$rs->db_data["ID_Produto"];
									$Ref=$rs->db_data["Ref"];
									$Nome=$rs->db_data["Nome"];
									$Qtd=$rs->db_data["Qtd"];
									$Tamanho=$rs->db_data["Tamanho"];
									$Cor=$rs->db_data["Cor"];
									$Valor=$rs->db_data["Valor"];
									$Valor_Desconto=$rs->db_data["Valor_Desconto"];
									$Tipo=$rs->db_data["Tipo"];
									$m_estado=$rs->db_data["Estado"];
									$ID_Conf=$rs->db_data["ID_Conf"];
									$login=$rs->db_data["login"];
									$Chave=$rs->db_data["Chave"];
									$Obs=$rs->db_data["Obs"];
									$voucher_id=$rs->db_data["voucher_id"];
									if($voucher_id>0) $ID_Produto='';
									$info = get_voucher_info($voucher_id, 1);
									if($cor=='#a3df3d') $cor='#e2ed6e';
									else $cor='#a3df3d';
									?>
                                    <tr bgcolor="<?=$cor?>"> 
    	                                <td class="preto" height="30" style="text-align:center" style="font-weight:bold"><a target="_blank" href="<?=$site_http_url?>admin/products/products.php?ID=<? echo $ID_Produto?>"><? echo $ID_Produto?></a></td>
        	                            <td class="preto" align="left" style="font-weight:bold"><?=$Ref?></td>
                                        <td class="preto" align="left" style="font-weight:bold"><?=$Nome?></td> <td class="preto" align="left" width="5px"></td>
                                        <td class="preto" align="left" style="font-weight:bold"><?php echo numberformat($Valor)?></td>
                                        <td class="preto" align="left" style="font-weight:bold"><?php if($Valor_Desconto>0) echo ' - '.numberformat($Valor_Desconto); else echo numberformat($Valor_Desconto);?></td>
										<td class="preto" align="left" width="50px" style="font-weight:bold"><?=$Qtd?></td>
										<td class="preto" align="left" width="50px" style="font-weight:bold"><?=numberformat(($Valor * $Qtd)-$Valor_Desconto)?></td>
                                        <td class="preto" align="center" style="font-weight:bold"><?=$info['voucher_code']?></td>
										<td align="right" class=preto style="font-weight:bold">
                                        <select name="m_estado_<?=$IDb?>" id="m_estado_<?=$IDb?>" style="width:100px">
                                           <option value="" <? if(!$m_estado) echo 'selected="selected"';?>>1 - Pendente</option>
                                            <option value="2" <? if($m_estado==2) echo 'selected="selected"';?>>3 - Cancelada</option>
                                            <option value="3" <? if($m_estado==3) echo 'selected="selected"';?>>4 - Pagamento Aceite</option>
                                            <option value="4" <? if($m_estado==4) echo 'selected="selected"';?>>5 - Pagamento Cancelado</option>
                                            <option value="5" <? if($m_estado==5) echo 'selected="selected"';?>>6 - Pagamento Pendente</option>
                                            <option value="6" <? if($m_estado==6) echo 'selected="selected"';?>>7 - Em expedição</option>
                                            <option value="7" <? if($m_estado==7) echo 'selected="selected"';?>>8 - Concluída</option>
                                        </select>
                                       	</td>
                                        <td class="preto" align="right"><a href="javascript:;" onclick="update_status(<?=$IDb?>)"><img border="0" src="save.png" /></a></td>
	                                </tr><?
									$rs->db_next();
								}
								$sql="SELECT * FROM encomendas_ofertas WHERE ID_PAI=$ID";
								$db_res=$rs->db_open($db_link,$DB_name,$sql);
								while(!$rs->EOF())
								{
									$bid 				= $rs->db_data["ID_Brand"];
									$infobrand 			= get_produtc_brand($bid);
									if($cor=='#a3df3d') $cor='#e2ed6e';
									else $cor='#a3df3d';
									?>
                                    <tr bgcolor="<? echo $cor?>"> 
                                        <td class="preto" height="23px" style="text-align:center" style="font-weight:bold"></td>
                                        <td class="preto" align="left" style="font-weight:bold"></td>
                                        <td class="preto" align="left" style="font-weight:bold">Amostra Gratuita - <?=$infobrand['marca_nome']?></td> 
                                        <td class="preto" align="left" width="5px"></td>
                                        <td class="preto" align="left" style="font-weight:bold"></td>
                                        <td class="preto" align="left" width="50px" style="font-weight:bold"></td>
                                        <td class="preto" align="left" style="font-weight:bold"></td>
                                        <td class="preto" align="left" width="40px" style="font-weight:bold"></td>
                                        <? if($module_voucher==1){?><td class="preto" align="left" width="40px" style="font-weight:bold"></td><? }?>
                                        <td align="right" class=preto style="font-weight:bold"></td>
                                        <td class="preto" align="right"></td>
                                    </tr><?
									$rs->db_next();
								}
								$rs->db_close();
								?>
								<tr bgcolor="#bdbdbd"> 
                                    <td class="preto" height="30" style="text-align:center" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td> <td class="preto" align="left" width="5px"></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold" colspan="2">TOTAL</td>
                                    <td class="preto" align="left" width="50px" style="font-weight:bold"><?=numberformat($TotalDocIva)?></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td align="right" class=preto style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#d3d2d2"> 
                                    <td class="preto" height="30" style="text-align:center" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td> <td class="preto" align="left" width="5px"></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold" colspan="2">Taxas</td>
                                    <td class="preto" align="left" width="50px" style="font-weight:bold"><?=numberformat($Taxas)?></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td align="right" class=preto style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#bdbdbd"> 
                                    <td class="preto" height="30" style="text-align:center" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td> <td class="preto" align="left" width="5px"></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold" colspan="2">Portes</td>
                                    <td class="preto" align="left" width="50px" style="font-weight:bold"><?=numberformat($Portes)?></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td align="right" class=preto style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#d3d2d2"> 
                                    <td class="preto" height="30" style="text-align:center" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td> <td class="preto" align="left" width="5px"></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="left" style="font-weight:bold" colspan="2">TOTAL com portes</td>
                                    <td class="preto" align="left" width="50px" style="font-weight:bold"><?=numberformat($TotalDocComIva)?></td>
                                    <td class="preto" align="left" style="font-weight:bold">&nbsp;</td>
                                    <td align="right" class=preto style="font-weight:bold">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                    <td class="preto" align="right">&nbsp;</td>
                                </tr> 
                                </table>
							</td>
                        </tr>
                        </table>
                	</td>
				</tr>
                </table>
				<? show_page_buttons();?>
            </td>
            <td width="10px">&nbsp;</td>
    		<td valign="top"><? show_fast_tools();?></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>