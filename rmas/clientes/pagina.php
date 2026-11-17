<?
	include "settings.inc.php";
	
	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';}
	$club_on=0;
	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$log2;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_GET['ID']) && intval($_GET['ID'])>0 && isset($_GET['RESETNUMENC']) && intval($_GET['RESETNUMENC'])==1)
	{
		$sql = 'UPDATE clientes 
				SET 
					NumEncomendas="0"	
					WHERE ID="'.intval($_GET['ID']).'"';
		$res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
	}
			
	if($ID!=0) 
	{
		if($ID!=0)
		{
			
			$deixa=0;	$rs = new Dataobj; $rs2 = new Dataobj;	
			
			$sql = 'SELECT * FROM clientes WHERE ID="'.$ID.'"';
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$IDb=$rs->db_data['ID'];
					//Dados para fatura
					$m_nome=$rs->db_data['Nome'];
					$Password = desencripta($rs->db_data['Password']);
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
					$m_carteira=$rs->db_data['Carteira'];
					if(!$m_carteira) $m_carteira=0;
					$m_totalgasto=$rs->db_data['TotalGasto'];
					if(!$m_totalgasto) $m_totalgasto=0;					
					$Club_data_ini1 = explode(' ',$rs->db_data['Club_data_ini']);
					$Club_data_fim1 = explode(' ',$rs->db_data['Club_data_fim']);
					$Afiliado=$rs->db_data['Afiliado'];
					$Afiliado_Perc=$rs->db_data['Afiliado_Perc'];
					$NumEncomendas=$rs->db_data['NumEncomendas'];
					
					$Club_data_ini = $Club_data_ini1[0];
					$Club_data_fim = $Club_data_fim1[0];
					
					
					$data_hora1 = explode(' ',$Club_data_ini);					
					$data1 = explode('-',$data_hora1[0]);
					$hora1 = explode(':',$data_hora1[1]);
					
					$data_hora2 = explode(' ',$Club_data_fim);					
					$data2 = explode('-',$data_hora2[0]);
					$hora2 = explode(':',$data_hora2[1]);
					
					
					$d_ini = mktime($hora1[0],$hora1[1],$hora1[2],$data1[1],$data1[2],$data1[0]);
					$d_fim = mktime($hora2[0],$hora2[1],$hora2[2],$data2[1],$data2[2],$data2[0]);
					
					if($d_ini<=strtotime("now") && $d_fim>=strtotime("now")) $club_on = 1;
					
					//Dados para entrega
					$m2_nome=$rs->db_data['Nome_ent'];
					$m2_apelido=$rs->db_data['Apelido_ent'];
					$m2_empresa=$rs->db_data['Empresa_ent'];
					$m2_morada=$rs->db_data['Morada_ent'];
					$m2_codpos=$rs->db_data['Codigo_Postal_ent'];
					$m2_pais=$rs->db_data['Pais_ent'];
					$m2_telefone=$rs->db_data['Telefone_ent'];
					$m2_email=$rs->db_data['Email_ent'];			
					$m2_contribuinte=$rs->db_data['Nif_ent'];
					
					if(!$m2_nome) $m2_nome = $m_nome;
					if(!$m2_apelido) $m2_apelido = $m_apelido;
					if(!$m2_empresa) $m2_empresa = $m_empresa;
					if(!$m2_morada) $m2_morada = $m_morada;
					if(!$m2_codpos) $m2_codpos = $m_codpos;
					if(!$m2_pais) $m2_pais = $m_pais;
					if(!$m2_telefone) $m2_telefone = $m_telefone;
					if(!$m2_email) $m2_email = $m_email;
				}
				$rs->db_close();
		}
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
?>
<script>
function mostra_dados(obj)
{
	if(document.getElementById)
	{
		var al=document.getElementById('dados'+obj);
		var al2=document.getElementById('bts'+obj);
		var br=document.getElementById("masterdiv").getElementsByTagName("div");

		if(al.style.display != "block"){
		
			for (var i=0; i<br.length; i++)
			{
				if ((br[i].className=="registo2" || br[i].className=="registo3") && br[i].id!='dados3'){ br[i].style.display="none";}
				if (br[i].className=="cart_tit"){ br[i].style.background="#FFFFFF";}
			}
		
			al.style.display="block";
			al2.style.background="#b6e2f4";
			$('#tab').val(obj);
		}
	}
}
function resetNumEncomendas(iduser)
{
	if(confirm("Ao efetuar esta acção o número de encomendas será colocado a zero e só terá efeito na próxima compra. Tem a certeza? "))
	{
		$.ajax({
			url: "pagina.php", type: "GET", 
			data: { ID: iduser,
			RESETNUMENC: 1 }
		}).done(function() {
			window.location.reload();
		});
	}
}
function validar_registo()
{
	var cor = "#fffc00";
	var cor2 = "#FFFFFF";
	var str_err = '';
	campos_obrigatorios = new Array("m_email","m_nome", "m_apelido");
	campos_obrigatorios2 = new Array("Email - Tab Dados da conta", "Nome - Tab Dados de Cliente/Faturação", "Apelido - Tab Dados de Cliente/Faturação");
	var campos_preenchidos;
	for (obj=0; obj<campos_obrigatorios.length; obj++) { document.forms['form'].elements[campos_obrigatorios[obj]].style.background=cor2; }
	for (obj=0; obj<campos_obrigatorios.length; obj++)  
	{		
		if(document.forms['form'].elements[campos_obrigatorios[obj]].value=="")
		{
			campos_preenchidos=1; 
			document.forms['form'].elements[campos_obrigatorios[obj]].style.background=cor;
			str_err+=campos_obrigatorios2[obj]+"\n";			
		}
	}
	if(campos_preenchidos==1)
	{
		alert("Os seguintes campos são obrigatórios:\n\n"+str_err);
		return false;
	}	
	return true;
}
$(function()
{
	$('#club_dataini').datepicker({showOn: "button", buttonImage: "<? echo $site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	$('#club_datafim').datepicker({showOn: "button", buttonImage: "<? echo $site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	$('#m_datanascimento').datepicker({showOn: "button", buttonImage: "<? echo $site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
}); 
</script>
<div class="center" id="masterdiv">
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<? echo $ID?>&ID_ORG=<? echo $ID_ORG?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>"  enctype="multipart/form-data" onsubmit="return  validar_registo();">
		<input type="hidden" name="slashes" value="'" />
		<input type="hidden" name="ID" value="<? echo $ID;?>" />
		<input type="hidden" name="tab" id="tab" value="<? if(isset($_GET['tab']) && $_GET['tab'] > 0){echo $_GET['tab'];}?>" />
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td valign="top" align="left">
				<? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr> 
                    <td>
                    	<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="0">
                        <tr>
                            <td>
                            	<div class="cart_tit" id="bts7" style="border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(7)"><? echo 'Dados Conta';?></div>
			                    <div class="cart_tit" id="bts1" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(1)"><? echo 'Dados Cliente/Faturação';?></div>
                                <div class="cart_tit" id="bts2" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(2)"><? echo 'Dados de Entrega'?></div>
                                <div class="cart_tit" id="bts5" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(5)"><? echo 'Lista de Encomendas'?></div>
							</td>
                        </tr>
                        </table>
                    
                        <table width="880px" border=0 cellspacing=0 cellpadding=0 class="p1">
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        </table>
                        
                        <table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td>
                
					<div style="background-color:#FFF;padding:10px">Todos os campos com (<span style="color:#FF0000"><span style="color:#FF0000">*</span></span>) são obrigatórios</div>
					<div style="clear:both; height:5px"></div>
					<div class="registo2" style="float:left;border:#000 0px solid;display:none" id="dados7">
                        <div class="ct" style="width:800px;"><div class="f_txt">Grupo</div>
                        <div class="f_ipt"><? show_lista($Grupo_ID,"grupos_clientes","Grupo_ID");?>&nbsp;<a href="../grupos_clientes/list.php" class="preto">[editar]</a></div></div>
                        <div style="clear:both"></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_email'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" autocomplete="off" title="<? echo $GLOBALS['key_email'][$GLOBALS['admin_lang']]?>" name="m_email" maxlength="255" value="<? echo $m_email?>" /> <span style="color:#FF0000;font-size:14px">*</span></div></div>
                        <div style="clear:both"></div>
                        <div class="ct" style="width:800px;"><div class="f_txt"><? echo $GLOBALS['key_password'][$admin_lang]?></div><div class="f_ipt"><input type="password" autocomplete="off" class="textx" title="" name="m_password" autocomplete="off"  maxlength="255" value="" /> </div></div>
                        <? if(isset($_GET['ID']) && $_GET['ID'] > 0){?><div class="ct" style="width:800px;"><div class="f_txt" onclick="document.getElementById('pass_show').style.display='block'" style="cursor:pointer;text-decoration:underline">mostrar:</div><div class="f_ipt" id="pass_show" style="display:none;font-weight:bold;"><? echo $Password?></div></div><? }?>
                        <div style="clear:both"></div>
                        <? 
                        if($subscribe_newsletter == 1)
                        {
                        ?>
                        <div class="ct" style="width:800px;"><div class="f_txt">NEWSLETTER</div>
                        <div class="f_ipt"><input type="radio" title="NEWSLETTER" name="m_newsletter" maxlength="255" value="1" <? if($Newsletter==1){ echo 'checked="checked"';}?>> <? echo "SIM";?>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" title="NEWSLETTER" name="m_newsletter" maxlength="255" value="0" <? if($Newsletter==0 || !$Newsletter){ echo 'checked="checked"';}?>> <? echo 'NÃO';?><br />
                        </div></div>		
                        
                        <div style="clear:both"></div>
                        <?
                        }
                        ?>
                        <div style="clear:both"></div>
                        <div class="ct" style="width:800px;"><div class="f_txt">Afiliado:</div>
                        <div class="f_ipt"><input type="checkbox" <? if($Afiliado==1){?>checked="checked"<? }?> name="Afiliado" value="1" /> Ativar opção de afiliado</div><? if($Afiliado==1){?><div class="f_ipt"><input type="text" value="<?=$Afiliado_Perc?>" name="Afiliado_Perc" style="width:40px"/> % de cada venda</div><? }?></div>
                        
                        
                        
                        <div style="clear:both"></div>
                        <div class="ct" style="width:800px;"><div class="f_txt"><? echo write_lang('key_ativo','Ativo')?></div>
                        <div class="f_ipt"><input type="checkbox" <? if($m_ativo==1){?>checked="checked"<? }?> name="m_ativo" value="1" /> <? echo $GLOBALS['key_sim'][$admin_lang]?></div></div>
                        <div style="clear:both"></div>
                        <div class="ct" style="width:800px;"><div class="f_txt">Nº Encomendas:</div>
                        <div class="f_ipt"> <?php echo $NumEncomendas?> <input type="button" style="width:160px;height:20px" onclick="resetNumEncomendas('<?=$IDb?>')" value="Reset Nº de Encomendas" class="botao" /></div></div>
						
					</div>
					<div class="registo2" style="float:left;border:#000 0px solid;display:none" id="dados1">
                        <div class="marg">
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_nome'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_nome'][$GLOBALS['admin_lang']]?>" name="m_nome" maxlength="255" value="<? echo $m_nome?>" /> <span style="color:#FF0000;font-size:14px">*</span></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?>" name="m_apelido" maxlength="255" value="<? echo $m_apelido?>" /> <span style="color:#FF0000;font-size:14px">*</span></div></div>
                        <div class="ct"><div class="f_txt">Sexo</div><div class="f_ipt">
                        	<select name="m_sexo" style="height:20px;width:210px">
                        		<option value="" <?php if(!$m_sexo) echo 'selected';?>>Escolher:</option>
                        		<option value="1" <?php if($m_sexo==1) echo 'selected';?>>Masculino</option>
                        		<option value="2" <?php if($m_sexo==2) echo 'selected';?>>Feminino</option>
                        	</select>
                        </div></div>
                        <div class="ct"><div class="f_txt">Data nascimento</div><div class="f_ipt"><input type="text" class="textx" title="Data Nascimento" name="m_datanascimento" maxlength="255" value="<? echo $m_datanascimento?>" id="m_datanascimento" /></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_nif'][$admin_lang]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_nif'][$admin_lang]?>" name="m_contribuinte" maxlength="255" value="<? echo $m_contribuinte?>" /></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?>" name="m_empresa" maxlength="255" value="<? echo $m_empresa?>" /></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_morada'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_morada'][$GLOBALS['admin_lang']]?>" name="m_morada" maxlength="255" value="<? echo $m_morada?>" /></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?>" name="m_codpos"  maxlength="255" value="<? echo $m_codpos?>" /></div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_pais'][$GLOBALS['admin_lang']]?></div><div class="f_ipt">
                        <select class="textx" name="m_pais" title="<?=$GLOBALS['key_pais'][$admin_lang]?>" style="height:20px;text-transform:uppercase;">

                        <?php
                       		$rs_paises = new Dataobj;	
							global $DB_host,$DB_user,$DB_pass,$DB_name;
							$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
							$sql = 'SELECT c.zone_id,czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$GLOBALS['admin_lang'].'" order by czl.zone_name ASC';
							$rs_paises->db_open($db_link, $DB_name, $sql);
							while(!$rs_paises->EOF())
							{
								$zone_name = $rs_paises->db_data['zone_name'];
								$zone_id = $rs_paises->db_data['zone_id'];
								echo '<option value="'.$zone_id.'"'; 
								if(!$m_pais) $m_pais = 165;
								if($m_pais==$zone_id) echo 'selected="selected"';
								echo'>'.$zone_name.'</option>';

								$rs_paises->db_next();
							}
							$rs_paises->db_close();
						?>

                        </select>
                        </div></div>
                        <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_tel'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_tel'][$GLOBALS['admin_lang']]?>" name="m_telefone" maxlength="255" value="<? echo $m_telefone?>" /></div></div>
                        </div>
					</div>
					<div class="registo3"  style="float:left;border-left:#FF0000 0px solid;display:none" id="dados2">
                        <div class="marg">
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_nome'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_nome'][$GLOBALS['admin_lang']]?>" name="m2_nome" maxlength="255" value="<? echo $m2_nome?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?>" name="m2_apelido" maxlength="255" value="<? echo $m2_apelido?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?>" name="m2_empresa" maxlength="255" value="<? echo $m2_empresa?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_morada'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_morada'][$GLOBALS['admin_lang']]?>" name="m2_morada" maxlength="255" value="<? echo $m2_morada?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?>" name="m2_codpos"  maxlength="255" value="<? echo $m2_codpos?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_pais'][$GLOBALS['admin_lang']]?></div><div class="f_ipt">
                            <select class="textx" name="m2_pais" title="<?=$GLOBALS['key_pais'][$admin_lang]?>" style="height:20px;text-transform:uppercase;">
                            <?php
	                       		$rs_paises = new Dataobj;	
								global $DB_host,$DB_user,$DB_pass,$DB_name;
								$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
								$sql = 'SELECT c.zone_id,czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$GLOBALS['admin_lang'].'" order by czl.zone_name ASC';
								$rs_paises->db_open($db_link, $DB_name, $sql);
								while(!$rs_paises->EOF())
								{
									$zone_name = $rs_paises->db_data['zone_name'];
									$zone_id = $rs_paises->db_data['zone_id'];
									echo '<option value="'.$zone_id.'"'; 
									if(!$m2_pais) $m2_pais = 165;
									if($m2_pais==$zone_id) echo 'selected="selected"';
									echo'>'.$zone_name.'</option>';

									$rs_paises->db_next();
								}
								$rs_paises->db_close();
							?>
                            </select>
                            </div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_tel'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_tel'][$GLOBALS['admin_lang']]?>" name="m2_telefone" maxlength="255" value="<? echo $m2_telefone?>" /></div></div>
                            <div class="ct"><div class="f_txt"><? echo $GLOBALS['key_email'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="textx" title="<? echo $GLOBALS['key_email'][$GLOBALS['admin_lang']]?>" name="m2_email" maxlength="255" value="<? echo $m2_email?>" /></div></div>
                        </div>
					</div>
					<div class="registo3" style="float:left;border-left:#FF0000 0px solid;display:none" id="dados3"></div>
                        <div style="clear:both"></div>
                        <div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados6">	
                        <div class="ct" style="width:800px;"><div class="f_txt">Imagem</div>
                        <div class="f_ipt">
                        <?
                            if($I1!="" && file_exists("../../files/clientes/".$ID."_1.".$I1))
                            {	
                                if(file_exists("../../files/clientes/$ID"."_1.".$I1)){?>&nbsp;&nbsp;<a class="fancybox preto" data-fancybox-group="gallery" href="<? echo "../../files/clientes/".$ID."_1.".$I1;?>">View Image</a>&nbsp;&nbsp;<? }?><?
                                if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1' /><? }
                            }
                            else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1' /> <input type='hidden' name='tem_foto1' value='0' /><? }
                        ?>
                        </div></div>
					</div>
					<div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados5">	
                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
                        <tr><td valign="top"><?php show_encomendas($ID)?></td></tr>
                        </table>
					</div>
                    
                    </td></tr>
                    </table>
                    
                	</td>
				</tr>
				<tr>
				
                </table><? show_page_buttons();?>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top"><? show_fast_tools();?><br /></td>
		</tr>
		</table>
    </form>
</div>
<script>
<? if(isset($_GET['tab']) && $_GET['tab'] > 0){?>mostra_dados(<? echo $_GET['tab'];?>);<? }else{?>mostra_dados(7);<? }?>
</script>
<? 	include "../layout/bottom.inc.php";?>