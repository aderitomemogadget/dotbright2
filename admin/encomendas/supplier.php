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
			$Custos_logisticos=$rs->db_data["Custos_logisticos"];
			$Portes=$Portes-$Custos_logisticos;

			$PortesSemIva=$rs->db_data["PortesSemIva"];
			$IvaPortes=$rs->db_data["IvaPortes"];
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

			$Nota_privada = $rs->db_data["Nota_privada"];

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
		//Vamos gravar a nota
		$titulo_mgs=$_POST['titulo_mgs'];
		$txt_msgs=$_POST['txt_msgs'];
		$ID_Enc = $ID;
		$ID_Admin = $admin_id;

		$txt_msgs=nl2br($txt_msgs);

		$sql = 'INSERT INTO supplier_notas (ID_Cliente,ID_Admin,ID_Enc,Assunto,Mensagem,idate) values("'.$ID_Cliente.'","'.$ID_Admin.'","'.$ID_Enc.'","'.addslashes($titulo_mgs).'","'.addslashes($txt_msgs).'","'.date("Y-m-d H:i:s").'")';
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
function PDF(strid)
{
	window.open('pdf.php?ID='+strid);
}
function update_status(n)
{
	var url="pagina.php?ID=<?php echo $ID?>&x_id="+n;
	if(document.getElementById("m_estado_"+n)) url=url+"&x_estado="+document.getElementById("m_estado_"+n).value;
	window.open(url,"_self");
}
function send_msg()
{
	$('#enviar_msg').val('1');
	document.getElementById("myForm").submit();
}
</script>

<div class="center">
		<form action="supplier.php?ID=<?php echo $_GET['ID']?>" id="myForm" name="myForm" method="post" enctype="multipart/form-data">
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

							<table width="100%" border=0 cellspacing=0 cellpadding="4">
							<tr>
                                <td align="left" width="50%"><img style="width:150px" src="../encomendas/img/suppliers.png" border="0"></td>
                                <td align="left" width="50%">

									<table width="100%" border=0 cellspacing=0 cellpadding="4">
									<tr>
	                                    <td align="left" width="190"><i class="fas fa-user"></i> Cliente ID:</td>
	                                    <td align="left"><a href="../clientes/pagina.php?ID=<?=$ID_Cliente?>"><?=$ID_Cliente?></a></td>
	                                </tr>
	                                <tr>
	                                    <td align="left" width="120"><i class="fas fa-file-invoice-dollar"></i> Nº da encomenda:</td>
	                                    <td align="left"><b><?php echo $ID?></b> - <b><?php echo $Referencia ?></b></td>
	                                </tr>
	                                <tr>
		                            	<td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-truck"></i> Tracking:</td>
		                            	<td align="left" style="border-top:#ccc 1px solid"><?=$m_guia?></td>
	                                </tr>
									<tr>
	                                    <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="far fa-calendar-alt"></i> Data da encomenda:</td>
	                                    <td align="left" style="border-top:#ccc 1px solid"><?php echo $Data_Criacao?></td>
	                                </tr>
	                                <tr>
	                                    <td align="left" width="190" style="border-top:#ccc 1px solid"><i class="fas fa-clock"></i> Estado da encomenda:</td>
	                                    <td align="left" style="border-top:#ccc 1px solid">
										<?php
										$enc_state =  estado_encomenda($Estado, 'pt');
										echo '<span style="color:#000;font-weight:bold">'.$enc_state['titulo'].'</span>';
										?>
										</td>
	                                </tr>
	                            	</table>
	                            </td>
	                        </tr>
	                    	</table>
                        </div>
                        </div><br>						

                        <?php if(!$nova){?>


							<div id="wraper" class="wraper">
							<div class="tabtop-open3" id="tab1">
								<table width="100%" border="0" cellpadding="5" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="100%"><i class="far fa-envelope"></i> <strong>NOTAS</strong> (Aqui pode gravar notas relativas ao fornecedor a quem foi feita a encomenda, esta info só está visivel via backoffice)</td>
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
			                            <td valign="top" width="100%"><input class="btn btn-primary" type="button" name="Enviar" value=" Gravar " onclick="send_msg()"> </td>
			                        </tr>
			                    	</table>
		                    	</div>
		                    </div>
		                </div>

		                <div id="wraper" class="wraper">
							<div class="tabtop-open3" id="tab1">
								<table width="100%" border="0" cellpadding="5" cellspacing=0>
		                        <tr>
		                            <td valign="top" width="100%"><i class="far fa-envelope"></i> <strong>NOTAS</strong></td>
		                        </tr>
		                        <tr>
		                            <td valign="top" width="100%">


											<table width=100% border=0 cellspacing=0 cellpadding=2>
		                            		<tr>
											<td valign="top" colspan=2 class="agenda2">
											<?

												global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
												$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
												$rs=new DataObj;
												$rs2=new DataObj;
												$sql="SELECT * FROM supplier_notas WHERE ID_Enc = $ID Order BY ID DESC";
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

												if($count==0) echo '<table  class="'.$class.'" width=100%><tr><td height=100 class="agenda2" align=center>Sem notas!</td></tr></table>';
											?>
											</table>
		                            </td>
		                        </tr>
		                    	</table>
		                    </div>
		                </div>


							<? }?>


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
									$ficheiro_upload = $rs->db_data["file"];
									$largura = $rs->db_data["Largura"];
									$altura = $rs->db_data["Altura"];
									$qnt_min= $rs->db_data["qnt_min"];

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
									?>
									<tr bgcolor="<?php echo $cor?>" id="scroll<?php echo $ID_Produto?>">
									<td style="min-height:35px;text-align:center;border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><a class="preto" target="_blank" style="color:#<?php echo $cor_texto?>" href="../products/products.php?ID=<? echo $ID_Produto?>"><u><?php echo $ID_Produto?></u></a></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;"><?php echo $Ref?></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo $Nome?></td>
									<td align="left" width="5px" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;"></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo numberformat($Valor)?></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php if($Valor_Desconto>0) echo ' - '.numberformat($Valor_Desconto); else echo numberformat($Valor_Desconto);?></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo $Qtd?></td>
									<td align="left" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>;" ><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto)?></td>
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
									<td align="right" style="border-bottom:#000 1px solid" ></td>									
									<td align="right" style="border-bottom:#000 1px solid" ></td>
									</tr>
									<?php
									$rs->db_next();
								}

								$rs->db_close();
								?>
								<tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>


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
								<tr >
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4">TOTAL DE IVA</td>
                                    <td align="right" width="150px" ><?php echo numberformat($TotalDocIva)?></td>
                                    <td>&nbsp;</td>

                                </tr><?

                                if($Taxas>0)
                                {?>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">TAXAS</td>
                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><?php echo numberformat($Taxas)?></td>
                                    <td>&nbsp;</td>
                                </tr><?

                                }?>
                                <tr>
                                    <td height="30" style="text-align:center" >&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
                                    <td>&nbsp;</td>
                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">VALOR DE PORTES</td>
                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><?php echo numberformat($Portes)?></td>
                                    <td>&nbsp;</td>
                                </tr><?

                                if($Custos_logisticos>0)
                                {?>
                                	<tr>
	                                    <td height="30" style="text-align:center" >&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td> <td align="left" width="5px"></td>
	                                    <td>&nbsp;</td>
	                                    <td align="left" colspan="4" style="border-top:#ccc 1px solid">CUSTOS LOGISTICOS</td>
	                                    <td align="right" width="50px" style="border-top:#ccc 1px solid"><?php echo numberformat($Custos_logisticos)?></td>
	                                    <td>&nbsp;</td>
                                	</tr><?

                                }?>

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
            </td>

		</tr>
		</table>

		</form>


</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php	include "../layout/bottom.inc.php";?>