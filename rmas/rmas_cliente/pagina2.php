<?
	include "settings.inc.php";
	
	if($_GET['log']){
		error_reporting(E_ALL);
		ini_set('display_errors',1);
	}

	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}
	
	if(trim($Nivel)=="") $Nivel=0;
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	$C="";$T="";
	$ordem=0;

	//echo 'ID: '.$ID;
	if($ID!=0)
	{
		$rs->db_open($db_link,$DB_name, 'SELECT * FROM rmas WHERE ID="'.$ID.'"');
		//echo '<pre>'.print_r($rs).'</pre>';
		if($rs)
		{		
			$IDb=$rs->db_data['ID'];
				
			$ID_Cliente=$rs->db_data['ID_Cliente'];
			$sql_cliente = 'SELECT * FROM clientes WHERE ID='.$ID_Cliente;
			$rs2->db_open($db_link,$DB_name,$sql_cliente);
			if(!$rs2->EOF()){
				$Cliente_Nome = $rs2->db_data['Nome'];
				$Cliente_Apelido = $rs2->db_data['Apelido'];
				$Cliente_Morada = $rs2->db_data['Morada'];
				$Cliente_CP = $rs2->db_data['Codigo_Postal'];
				$Cliente_Email = $rs2->db_data['Username'];
				$Cliente_Telefone = $rs2->db_data['Telefone'];
				$Cliente_Empresa = $rs2->db_data['Empresa'];
				$Cliente_Contribuinte = $rs2->db_data['Nif'];
			}
			$Estado = $rs->db_data['Estado'];
			$ID_Tecnico=$rs->db_data['ID_Tecnico'];
			$m_referencia=$rs->db_data['Referencia'];
			$m_marca=$rs->db_data['Marca'];
			$m_modelo=$rs->db_data['Modelo'];
			$m_tipoequip=$rs->db_data['TipoEquip'];
			$m_nserie= $rs->db_data['NSerie'];
			$m_avaria=$rs->db_data['Avaria'];
			$m_estadoequip=$rs->db_data['EstadoEquip'];
			$m_acessorios=$rs->db_data['Acessorios'];
			$m_estado=$rs->db_data['Estado'];
			$m_observacoestec=$rs->db_data['ObservacoesTecnico'];
			$m_relintervencao=$rs->db_data['RelatorioIntervencao'];
			//$m_cliente=$rs->db_data['ID_Cliente'];
			$m_tecnico=$rs->db_data['ID_Tecnico'];
			$m_obsint=$rs->db_data['Obsint'];
		}

			
		$rs->db_close();
	}
	else $ID_Fam=$ID_ORG;
?>
<script language=JavaScript src='../editor/scripts/innovaeditor.js'></script>
<script language="JavaScript">
if(document.getElementById)
{ 
	document.write('<style type="text/css">\n')
	document.write('.submenu{display: none;}\n')
	document.write('</style>\n')
}
function showmenu_prod(obj,obj2){
document.form.tab_a.value=obj;
document.form.tab_b.value=obj2;
if(document.getElementById){

	var el=document.getElementById(obj);
	var el2=document.getElementById(obj2);
	
	var ar=document.getElementById("masterdiv").getElementsByTagName("div"); 
	var ar2=document.getElementById("masterdiv").getElementsByTagName("td"); 
	
		if(el.style.display !="block"){
			for (var i=0; i<ar.length; i++){
				v="sub"+(i+1);
				 if(ar[i].className=="submenu") ar[i].style.display="none";
			}
			
			for (var i=0; i<ar2.length; i++){
				v="sub"+(i+1);
				 if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
			}
			el.style.display="block";
			el2.style.background="#000000";
		}else{
			//el.style.display="none";
		}
	}

}
window.onload = function(){<? if($t1 && $t2){?>showmenu_prod('<? echo $t1?>','<? echo $t2?>');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}

$(function(){ $('.result').hide();});

function get_cs_user()
{
	var t = $('.result').is(':visible');

	if(t==false)
	{
		$('#choose').html('<img src="del2.png" border="0" title="close box" />');
		$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>', function(data) {  $('.result').show("slow"); $('.result').html(data);});
	}
	else
	{
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}
}
function totext(div)
{
	$('#m_cliente_url').val($('#'+div).val());
	$('#cor').val($('#_'+div).val());
	$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
	$('.result').hide("slow");
}
function search()
{
	$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>&pesquisa='+$('#search_by').val(), function(data) { $('.result').html(data);});
}
function empty()
{
	$('#m_cliente_url').val("");
	$('#cor').val(0);
	$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
	$('.result').hide("slow");
}
function getCliente(tipo, valor){
	$.get('getCliente.php?'+tipo+'='+valor, function(data) {
		if(data.trim()!=''){
			var array_cli = data.split('&;');
			if(array_cli.length > 0){
				$('#ID_Cliente').val(array_cli[0]).attr('title',array_cli[0]);
				$('#cliente_nome').val(array_cli[1]).attr('title',array_cli[1]);
				$('#cliente_apelido').val(array_cli[2]).attr('title',array_cli[2]);
				$('#cliente_username').val(array_cli[3]).attr('title',array_cli[3]);
				$('#cliente_morada').val(array_cli[7]).attr('title',array_cli[7]);
				$('#cliente_cp').val(array_cli[8]).attr('title',array_cli[8]);
				$('#cliente_telefone').val(array_cli[4]).attr('title',array_cli[4]);
				$('#cliente_empresa').val(array_cli[5]).attr('title',array_cli[5]);
				//$('#m_cliente_contribuinte').val(array_cli[6]);
			}
			$('#dados_cliente').show();
		}

	});
}

function getProduto(valor, linha){
	$.get('getProduto.php?ref='+valor, function(data) {
		if(data!=''){
			var array_cli = data.split('&;');
			if(array_cli.length > 0){
				$('input[name="desc[]"]').eq(linha).val(array_cli[1]);
				$('input[name="prec[]"]').eq(linha).val(array_cli[2]);
				//$('input[name="disp[]"]').eq(linha).val(array_cli[3]);

			}
		}

	});
}

function validaCampos(){
	<?php if($task_form!='apaga'){ ?>
	var campos = ['cliente_nome','cliente_apelido','cliente_morada','cliente_cp','cliente_username','cliente_telefone'];
	var alertas = [' - Nome Cliente \n',' - Apelido Cliente \n',' - Morada Cliente \n',' - Codigo Postal Cliente \n',' - Email Cliente \n',' - Telefone Cliente \n'];
	var alerta = '';
	for(var i = 0; i < campos.length; i++){
		if(document.getElementById(campos[i]).value == ''){
			alerta += alertas[i];
		}
	}
	if(alerta != ''){
		alert('Faltam os seguintes campos: \n'+alerta);
	}
	else{
		document.forms['form'].submit();
	}
	<?php } else{ ?>
		document.forms['form'].submit();
	<?php } ?>
}

</script>

<form name="form" method="post" action="pagina_trata.php?ID=<? echo $ID?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<? echo $admin_lang;?>">
	<input type="hidden" name="slashes" value="'">
	<input type="hidden" name="tab_a" value="0">
	<input type="hidden" name="tab_b" value="0">
	<input type="hidden" name="ID" value="<? echo $ID?>">

<div class="center">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
    	<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
        	<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
            <tr> 
                <td valign="top">
                
                    <table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
                    <tr>
                        <td></td>
                    </tr>
                    </table>
					
                    <div id="masterdiv"><br />
                        <table height="20" border=0 cellspacing=0 cellpadding=0>
                        <tr>
                            <td width="120" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer;background:#000" id="pinta1">RESUMO</td><td><img src="cm_fill.gif" width="8" height="1"></td>						
                            <td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2"><? echo write_lang('key_cliente','CLIENTE')?></td>
                            <td><img src="cm_fill.gif" width="8" height="1"></td>
                            <td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="cursor:pointer" id="pinta3"><? echo write_lang('key_relatorio_reparacao','CARACTERÍSTICAS')?></td><td><img src="cm_fill.gif" width="8" height="1"></td>
                        </tr>
                        </table> 
                    
                    <div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">
                            
                                <table width="100%" border=0 cellpadding="2" cellspacing=0>
                                <tr>
                                    <td class="preto" width="120">Tracking:</td>
                                    <td class="preto" align="left" valign="top">
                                        <table border=0 cellspacing=0 cellpadding=0>
                                        <tr>
                                        	<td width="100"><input name="m_tracking" style="WIDTH:100px; HEIGHT:20px; text-align:center;" maxlength="255" disabled value="<? if($IDb!=0) echo "STG".str_pad((int) $IDb,6,"0",STR_PAD_LEFT); else echo ''; ?>"></td>
                                        	<td width="100" class="preto" align="right"><? echo write_lang('key_rmas_estado','Estado')?>:&nbsp;&nbsp;&nbsp;</td>
                                            <td width="100">
                                            	<select name="m_estado">
								            		<option value="0" <? if($Estado == 0) echo 'selected'; ?>><? echo write_lang('key_rmas_estado0','Aguarda diagnóstico') ?></option>
								            		<option value="1" <? if($Estado == 1) echo 'selected'; ?>><? echo write_lang('key_rmas_estado1','Aguarda aceitação') ?></option>
								            		<option value="2" <? if($Estado == 2) echo 'selected'; ?>><? echo write_lang('key_rmas_estado2','Aguarda reparação') ?></option>
								            		<option value="3" <? if($Estado == 3) echo 'selected'; ?>><? echo write_lang('key_rmas_estado3','Aguarda peça') ?></option>
								            		<option value="4" <? if($Estado == 4) echo 'selected'; ?>><? echo write_lang('key_rmas_estado7','Aguarda peça para despiste') ?></option>
								            		<option value="5" <? if($Estado == 5) echo 'selected'; ?>><? echo write_lang('key_rmas_estado4','Pronto para entrega') ?></option>
								            		<option value="6" <? if($Estado == 6) echo 'selected'; ?>><? echo write_lang('key_rmas_estado5','Concluído') ?></option>
								            		<option value="7" <? if($Estado == 7) echo 'selected'; ?>><? echo write_lang('key_rmas_estado6','Recusado') ?></option>
								            		<option value="8" <? if($Estado == 8) echo 'selected'; ?>><? echo write_lang('key_rmas_estado8','Sem Reparação - Pronto para levantar') ?></option>
								            	</select>
                                            </td>
                                            <td class="preto">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="envio_email" value="1">&nbsp;&nbsp;&nbsp;Envio de e-mail ao Cliente </td>
                                        </tr>
                                        </table>

                                    </td>
                                </tr>
								<tr> 
                                    <td class="preto"><? echo write_lang('key_referencia','Referência')?>:</td>
                                    <td class="preto" align="left" valign="top">
                                        <table width="100%" border=0 cellspacing=0 cellpadding=0>
                                        <tr>
                                            <td width="100"><input name="m_referencia" style="WIDTH:100px; HEIGHT:20px;text-align:center;" maxlength="255" value="<? echo $m_referencia;?>"></td>
                                            <td width="130" class="preto" align="right"><? echo write_lang('key_rmas_tipoequip','Tipo Equipamento')?>:&nbsp;&nbsp;&nbsp;</td>
                                            <td width="100">
                                                <input type="text" name="m_tipoequip" id="m_tipoequip" value="<?=$m_tipoequip?>">
                                                <script>
                                                    <?=criaArrayJS('TipoEquip',$m_tipoequip)?>
                                                    $( "#m_tipoequip" ).autocomplete({  source: TipoEquip });
                                                </script>
                                            	<?php

                                                /*<select name="m_tipoequip">
								            		<? criaSelectBox('TipoEquip',$m_tipoequip); ?>
								            	</select>
                                                */?>
								            	
                                            </td>
                                            <td class="preto"><?php /*<input name="m_tipoequip1" style="WIDTH:100px; HEIGHT:20px" maxlength="255" value=""> */ ?></td>
                                        </tr>
                                        </table>
                                    </td>
                                </tr> 
                                <tr>
                                    <td class="preto" width="120"> Marca:</td>
                                    <td class="preto" align="left" valign="top">
                                    	<table border=0 cellspacing=0 cellpadding=0>
	                                        <tr>
	                                        	<td>
                                                    <input type="text" name="m_marca" id="m_marca" value="<?=$m_marca?>">
                                                    <script>
                                                        <?=criaArrayJS('Marca',$m_marca)?>
                                                        $( "#m_marca" ).autocomplete({  source: Marca });
                                                    </script>
                                                    <?php /*
	                                        		<select name="m_marca">
														<? criaSelectBox('Marca',$m_marca); ?>
													</select>
                                                    <input name="m_marca1" style="WIDTH:100px; HEIGHT:20px" maxlength="255" value="">
                                                     */ 
                                                    ?>
													
	                                        	</td>
	                                        	<td width="70" class="preto" align="right"><? echo write_lang('key_modelo','Modelo')?>:&nbsp;&nbsp;&nbsp;</td>
	                                        	<td>
                                                    <input type="text" name="m_modelo" id="m_modelo" value="<?=$m_modelo?>">
                                                    <script>
                                                        <?=criaArrayJS('Modelo',$m_marca)?>
                                                        $( "#m_modelo" ).autocomplete({  source: Modelo });
                                                    </script>
                                                    <?php /*
	                                        		<select name="m_modelo">
														<? criaSelectBox('Modelo',$m_modelo); ?>
													</select>
													<input name="m_modelo1" style="WIDTH:100px; HEIGHT:20px" maxlength="255" value="">
                                                    */ ?>
	                                        	</td>
	                                        	<td width="70" class="preto" align="right"><? echo write_lang('key_nr_serie','Nr. Serie')?>:&nbsp;&nbsp;&nbsp;</td>
	                                        	<td>
													<input name="m_nserie" style="WIDTH:150px; HEIGHT:20px;float:left;" maxlength="255" value="<? echo $m_nserie; ?>" onchange="verificaNrSerie6Meses(this.value)">
													<div id="avisoNrSerie" style="float:left;"></div>
													<script>
														function verificaNrSerie6Meses(valor){
															$.get('verificaNrSerie.php?nr='+valor, function(data) {
															  $('#avisoNrSerie').html(data);
															});
														}
													</script>
	                                        	</td>
	                                        </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr> 
                                    <td class="preto" width="120"><? echo write_lang('key_avaria','Avaria')?>:</td>
                                    <td class="preto" align="left" valign="top"><textarea name="m_avaria" id="m_avaria" style="height:100px; border-color:#5694B4"><? echo $m_avaria?></textarea><? /* show_editor("m_avaria","m_avaria",780,150) */?></td>
                                </tr>
                               <tr> 
                                    <td class="preto" width="120"><? echo write_lang('key_estado_equip','Estado Equipamento')?>:</td>
                                    <td class="preto" align="left" valign="top"><textarea name="m_estadoequip" id="m_estadoequip" style="height:100px; border-color:#5694B4"><? echo $m_estadoequip?></textarea><? /* show_editor("m_estadoequip","m_estadoequip",780,150) */?></td>
                                </tr>
                                <tr> 
                                    <td class="preto" width="120"><? echo write_lang('key_acessorios','Acessórios')?>:</td>
                                    <td class="preto" align="left" valign="top"><textarea name="m_acessorios" id="m_acessorios" style="height:100px; border-color:#5694B4"><? echo $m_acessorios?></textarea><? /* show_editor("m_acessorios","m_acessorios",780,150) */?></td>
                                </tr>
                                <tr>
                                    <td class="preto" width="120" height="20px" colspan="2"></td>
                                </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                      </div>  
                      <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
                      	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        	<tr> 
	                            <td valign="top">
	                                <table width="100%" border=0 cellpadding="2" cellspacing=0>
	                                <tr>
	                                    <td class="preto" width="120"><? echo write_lang('key_nif','NIF')?>:</td>
	                                    <td class="preto" align="left" valign="top">
	                                    	<input name="ID_Cliente" id="ID_Cliente" type="hidden" value="<? echo $ID_Cliente; ?>">
	                                    	<input name="cliente_nif" id="cliente_nif" onchange="getCliente('nif',this.value)" style="WIDTH:150px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Contribuinte; ?>">
	                                    </td>
	                                </tr>                                                            
	                                
	                                <tr>
	                                    <td class="preto" width="120"><? echo write_lang('key_nome','Nome')?>:</td>
	                                    <td class="preto" align="left" valign="top">
	                                    	<table border=0 cellspacing=0 cellpadding=0>
		                                        <tr>
		                                        	<td>
														<input name="cliente_nome" id="cliente_nome" style="WIDTH:150px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Nome; ?>">
		                                        	</td>
		                                        	<td width="98" class="preto" align="right"><? echo write_lang('key_apelido','Apelido')?>:&nbsp;&nbsp;&nbsp;</td>
		                                        	<td>
														<input name="cliente_apelido" id="cliente_apelido" style="WIDTH:150px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Apelido; ?>">
		                                        	</td>
		                                        </tr>
	                                        </table>
	                                    </td>
	                                </tr>                                                            
									 <tr>
	                                    <td class="preto" width="120"><? echo write_lang('key_morada','Morada')?>:</td>
	                                    <td class="preto" align="left" valign="top">
											<table border=0 cellspacing=0 cellpadding=0>
		                                        <tr>
		                                        	<td>
														<input name="cliente_morada" id="cliente_morada" style="WIDTH:400px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Morada; ?>">
		                                        	</td>
		                                        	<td width="98" class="preto" align="right"><? echo write_lang('key_codigo_postal','Código Postal')?>:&nbsp;&nbsp;&nbsp;</td>
		                                        	<td>
														<input name="cliente_cp" id="cliente_cp" style="WIDTH:150px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_CP; ?>">
		                                        	</td>
		                                        </tr>
	                                        </table>
	                                    	
	                                    </td>
	                                </tr>
									 <tr>
	                                    <td class="preto" width="120">E-mail:</td>
	                                    <td class="preto" align="left" valign="top">
	                                    	<table border=0 cellspacing=0 cellpadding=0>
		                                        <tr>
		                                        	<td>
														<input name="cliente_username" id="cliente_username" onchange="getCliente('email',this.value)" style="WIDTH:400px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Email; ?>">
		                                        	</td>
		                                        	<td width="98" class="preto" align="right"><? echo write_lang('key_telefone','Telefone')?>:&nbsp;&nbsp;&nbsp;</td>
		                                        	<td>
														<input name="cliente_telefone" id="cliente_telefone" onchange="getCliente('telef',this.value)" style="WIDTH:150px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Telefone; ?>">
		                                        	</td>
		                                        </tr>
	                                        </table>
	                                    </td>
	                                </tr>
	                                <tr>
	                                    <td class="preto" width="120"><? echo write_lang('key_empresa','Empresa')?>:</td>
	                                    <td class="preto" align="left" valign="top">
	                                    	<input name="cliente_empresa" id="cliente_empresa" style="WIDTH:400px; HEIGHT:20px" maxlength="255" value="<? echo $Cliente_Empresa; ?>">
	                                    </td>
	                                </tr>                                                           
	                                </table>
	                              </td>
                              </tr>
                          </table>
                      </div>  
                      <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3">
                      	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr> 
                            <td valign="top">
                            
                                <table width="100%" border=0 cellpadding="2" cellspacing=0>
                                 <tr>
                                    <td class="preto" align="left" ><? echo write_lang('key_tecnico','Tecnico')?>:
                                    	<select name="m_tecnico">
                                    		<option value="0">-----------</option>
                                    		<? 
                                    		$rs->db_open($db_link,$DB_name, 'SELECT * FROM logins WHERE Grupo_ID=0 AND estado=1 ORDER BY Nome ASC');
                                    		while(!$rs->EOF()){
                                    			?><option value="<? echo $rs->db_data['ID']; ?>" <? if($rs->db_data['ID'] == $ID_Tecnico) echo 'selected'; ?>><? echo $rs->db_data['Nome']; ?></option><? 
                                    			$rs->db_next();
                                    		}
                                    		$rs->db_close();
                                    		?>
                                    	</select>

                                    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!--input type="checkbox" name="envio_email_diagnostico" value="1">&nbsp;&nbsp;&nbsp;Envio de e-mail ao Cliente do diagnóstico--> 
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preto"><? echo write_lang('key_relatorio_intervencao','Relatório de Intervenção')?>:</td>
                                </tr>
                                <tr>
                                    <td class="preto"><textarea name="m_relintervencao" id="m_relintervencao" style="WIDTH:100%; HEIGHT:100px"><? echo $m_relintervencao;?></textarea><? /*show_editor("oEdit4","texto1",840,250) */?></td>
                                </tr>
                                <tr>
                                    <td class="preto"><? echo write_lang('key_observacoes','Observações')?>:</td>
                                </tr>
                                <tr>
                                    <td class="preto"><textarea name="m_observacoestec" id="m_observacoestec" style="WIDTH:100%; HEIGHT:100px"><? echo $m_observacoestec;?></textarea><? /*show_editor("oEdit4","texto1",840,250) */?></td>
                                </tr>
                                <tr>
                                <td class="preto" ><? echo write_lang('key_observacoes_internas','Observações Internas')?>:</td>
                                </tr>
                                <tr> 
                                    
                                    <td class="preto" align="left" valign="top"><textarea name="m_obsint" id="m_obsint" style="height:100px; border-color:#5694B4"><? echo $m_obsint?></textarea><? /* show_editor("m_acessorios","m_acessorios",780,150) */?></td>
                                </tr>
                                 <tr>
                                    <td class="preto"><? echo write_lang('key_reparacao_prevista','Reparação prevista')?>:</td>
                                </tr>
                                <tr>
                                    <td class="preto">
                                    
                                    <style type="text/css">
                                    	.input-reparacao{ background:#FFF; border: none !important; width:95% !important; text-align: center; }
                                    </style>
                                    <script>
                                    function removeLinha(value){
                                    	$('#tabela-reparacao tr').eq(value+1).remove();
                                    	reordenaLinhasTabela();
                                    }
                                    function adicionaLinha(){
                                    	
                                    	$('#tabela-reparacao tr:last').after('<tr>'+$('#tabela-reparacao tr:last').html()+'</tr>');
                                    	reordenaLinhasTabela();
                                    	return false;
                                    }
                                    function reordenaLinhasTabela(){
                                    	 $('img[id="linha-reparacao"]').each(function (index) {
                                    	 	$(this).attr('onclick', 'removeLinha('+index+')');
                                    	 	$('input[name="ref[]"]').eq(index).attr('onkeyup','getProduto(this.value,'+index+')');
                                    	 });
                                    }

                                    function actualizaTotais(){
                                    	var totalsIVA = 0;
                                    	$('input[name="prec[]"]').each(function(index) {
                                    		if(!isNaN($(this).val()) && $(this).val()!=''){
                                    			$('input[name="qtd[]"]').eq(index).val()
                                    			totalsIVA += $('input[name="qtd[]"]').eq(index).val()*parseFloat($(this).val());
                                    		}
                                    	});
                                    	$('#totalsiva-reparacao').html('€ '+totalsIVA.toFixed(2));
                                    	$('#totalciva-reparacao').html('€ '+(totalsIVA*1.23).toFixed(2));
                                    	return false;
                                    }
                                    </script>
                                    	<table width="100%" border="1" id="tabela-reparacao">
                                    		<tr>
                                    			<th align="center" width="40">Qtd.</th>
                                    			<th align="center" width="70">Referencia</th>
                                    			<th align="center" width="400">Descrição</th>
                                    			<th align="center" width="50">Preço S/IVA</th>
                                    			<th align="center" width="100">Disponibilidade</th>
                                    			<th align="center" width="30">Acção</th>
                                    		</tr>
                                    		<?php 
                                    		$rs->db_open($db_link,$DB_name, 'SELECT * FROM rmas_linhas WHERE ID_ORG="'.$IDb.'" ORDER BY ID ASC');
                                    		//print_r($rs);
                                    		$lin = 0; $total = 0;
                                    		while(!$rs->EOF()){
                                    			?>
                                    			<tr>
                                    				<td><input class="input-reparacao" type="text" name="qtd[]" value="<?php echo $rs->db_data['Quantidade']; ?>"></td>
                                    				<td><input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao" type="text" name="ref[]" value="<?php echo $rs->db_data['Ref']; ?>"></td>
                                    				<td><input style="text-align: left;" class="input-reparacao" type="text" name="desc[]" value="<?php echo $rs->db_data['Cabecalho']; ?>"></td>
                                    				<td><input class="input-reparacao" type="text" name="prec[]" value="<?php echo $rs->db_data['Preco']; ?>"></td>
                                    				<td><input class="input-reparacao" type="text" name="disp[]" value="<?php echo $rs->db_data['Disponibilidade']; ?>"></td>
                                    				<td align="center"><img id="linha-reparacao" src="del2.png" border=0 onclick="removeLinha(<?php echo $lin; ?>)"></td>
                                    			</tr>
                                    			<?
                                    			$total += ($rs->db_data['Quantidade']*$rs->db_data['Preco']);
                                    			$lin++;
                                    			$rs->db_next();
                                    		}
                                    		$rs->db_close();

                                    		?>
                                    		<tr>
                                				<td><input class="input-reparacao" type="text" name="qtd[]" value=""></td>
                                				<td><input onkeyup="getProduto(this.value,<?php echo $lin; ?>)" class="input-reparacao" type="text" name="ref[]" value=""></td>
                                				<td><input style="text-align: left;" class="input-reparacao" type="text" name="desc[]" value=""></td>
                                				<td><input class="input-reparacao" type="text" name="prec[]" value=""></td>
                                				<td><input class="input-reparacao" type="text" name="disp[]" value=""></td>
                                				<td align="center"><img id="linha-reparacao" src="del2.png" border=0 onclick="removeLinha(<?php echo $lin; ?>)"></td>
                                			</tr>
                                    	</table>
                                    	<br/><br/>
                                    	<a href="#" onclick="adicionaLinha(); return false;">Adicionar Linha</a><br/>
                                    	<table width="100%" border="0">
                                    		<tr>
                                    			<td width="30%" align="right"><b>Total:</b>&nbsp;&nbsp;&nbsp;</td>
                                    			<td width="20%" id="totalsiva-reparacao">€ <?php echo number_format($total, 2); ?></td>
                                    			<td width="30%" align="right"><b>Total c/IVA:</b>&nbsp;&nbsp;&nbsp;</td>
                                    			<td width="20%" id="totalciva-reparacao">€ <?php echo number_format(($total*1.23), 2); ?></td>
                                    			<td><a href="#" onclick="actualizaTotais(); return false;">Actualizar</a><br/></td>
                                    		</tr>
                                    	</table>
                                    </td>
                                </tr>
                                
                                 </table>
                              </td>
                             </tr>
                            </table>
                       </div>                       
                    </div>
                 </td>
             </tr>
             </table>
				<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
                <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
                <tr>
                    <td>&nbsp;<input class=botao type="button" name="Submit" onclick="validaCampos()" value="<? echo $task_button;?>"> <input class=botao type="button" name="cancelar" value=" Cancelar " onMouseUp="window.open('list.php','_self')" />
                </tr>
                </table>
                <img src="../images/bsh.png" width="890px">
          </td>   
          <td width="10px">&nbsp;</td>
          <td valign="top" width="200px"><? show_fast_tools();?><br /></td>
       </tr>
     </table>  
</div>
</form>
</body>
</html>