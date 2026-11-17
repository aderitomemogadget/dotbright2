<?php
	include "settings.inc.php";

	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';$btn_class='btn-primary';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';$btn_class='btn-primary';}
	elseif($ID==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';$btn_class='btn-primary';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';$btn_class='btn-primary';}
	$club_on=0;
	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$log2, $tem_area_reservada, $tem_loja;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$fields=db_list_fields($DB_name,'clientes',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('obs',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD obs TEXT');
	if(!in_array('idate',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD idate datetime');
	if(!in_array('udate',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD udate datetime');
	if(!in_array('cod_instituicao',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD cod_instituicao varchar(50)');
	if(!in_array('Privilegios',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Privilegios int(11)');
	if(!in_array('ID_instituicao',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD ID_instituicao int(11)');
	if(!in_array('ID_departamento',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD ID_departamento int(11)');
	if(!in_array('Testes',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Testes tinyint(1)');
	if(!in_array('Plafom',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Plafom double');
	if(!in_array('Token2',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Token2 varchar(50)');
	if(!in_array('Num',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Num int(11)');

	//$res=Runquery($db_link,$DB_name,'ALTER TABLE clientes MODIFY Password VARCHAR(255);');

	if(!in_array('Desconto',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Desconto double');

	$fields=db_list_fields($DB_name,'clientes_enderecos',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Mobile',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE clientes_enderecos ADD Mobile varchar(100)');


	if($ID!=0)
	{
		if($ID!=0)
		{
			$deixa=0;
			$rs = new Dataobj;
			$rs2 = new Dataobj;

			$sql = 'SELECT * FROM clientes WHERE ID="'.$ID.'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$IDb=$rs->db_data['ID'];
				//Dados para fatura
				$m_nome=$rs->db_data['Nome'];
				$Password=$rs->db_data['Password'];
				$m_apelido=$rs->db_data['Apelido'];
				$m_empresa=$rs->db_data['Empresa'];
				$Desconto=$rs->db_data['Desconto'];
				$m_morada=$rs->db_data['Morada'];
				$m_codpos=$rs->db_data['Codigo_Postal'];
				$m_pais=$rs->db_data['Pais'];
				$m_telefone=$rs->db_data['Telefone'];
				$m_email=$rs->db_data['Email'];
				$Grupo_ID=$rs->db_data['Grupo_ID'];
				$Privilegios=$rs->db_data['Privilegios'];
				$ID_instituicao=$rs->db_data['ID_instituicao'];
				$ID_departamento=$rs->db_data['ID_departamento'];
				$m_contribuinte=$rs->db_data['Nif'];
				$Language=$rs->db_data['Language'];
				$Newsletter=$rs->db_data['Newsletter'];
				$Newsletter_Tipo=$rs->db_data['Newsletter_Tipo'];
				$m_ativo=$rs->db_data['Ativo'];
				$m_estado=$rs->db_data['Estado'];
				$testes=$rs->db_data['Testes'];
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
				$m_cidade=$rs->db_data['Cidade'];
				$m_cod_instituicao=$rs->db_data['cod_instituicao'];
				$m_num=$rs->db_data['Num'];
				$obs=stripslashes($rs->db_data['obs']);

				$Club_data_ini = $Club_data_ini1[0];
				$Club_data_fim = $Club_data_fim1[0];


				$d_ini = strtotime($Club_data_ini);
				$d_fim = strtotime($Club_data_fim);
				$agora = time();

				if($d_ini !== false && $d_fim !== false && $d_ini <= $agora && $d_fim >= $agora) $club_on = 1;

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
				$m2_cidade=$rs->db_data['Cidade_ent'];

				if(!$m2_nome) $m2_nome = $m_nome;
				if(!$m2_apelido) $m2_apelido = $m_apelido;
				if(!$m2_empresa) $m2_empresa = $m_empresa;
				if(!$m2_morada) $m2_morada = $m_morada;
				if(!$m2_codpos) $m2_codpos = $m_codpos;
				if(!$m2_pais) $m2_pais = $m_pais;
				if(!$m2_telefone) $m2_telefone = $m_telefone;
				if(!$m2_email) $m2_email = $m_email;


				if(isset($_POST['valor_bonus']) && $IDb > 0)
				{
					$valor = $_POST['valor_bonus'];
					$accao = $_POST['action'];
					if($accao == 'add')
					{
						$sql3 = 'INSERT INTO clientes_bonus(ID_Cliente,ID_Encomenda,ID_Produto,Tipo,Valor,Data) VALUES ("'.$IDb.'","0","0",2,"'.$valor.'","'.date("Y-m-d H:i:s").'")';
						$res = Runquery($db_link,$DB_name,$sql3);
					}
					else
					{
						$sql3 = 'INSERT INTO clientes_bonus(ID_Cliente,ID_Encomenda,ID_Produto,Tipo,Valor,Data) VALUES ("'.$IDb.'","0","0",4,"'.$valor.'","'.date("Y-m-d H:i:s").'")';
						$res = Runquery($db_link,$DB_name,$sql3);
					}
				}
			}
			$rs->db_close();
		}
	}
	if($GLOBALS['tabela_medidas']==1)
	{
		$rs2 = new Dataobj;

		$sql = "SELECT * FROM clientes_medidas WHERE ID_ORG='$IDb'";
		$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
		if(!$rs2->EOF())
		{
			$Fechado=$rs2->db_data['Fechado'];
		}
		$rs2->db_close();
    }
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	global $admin_lang;
?>
<script>
function LoginCliente(username, password){
		$.post("<? echo $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']); ?>", { login: "1", submit1: "1", redirect: "0", referer: "", m_email: username, m_password: password, admin: 1})
		.done(function (data){
			window.open("<? echo $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']); ?>");
		});

	}
function mostra_dados(obj)
{
	if(document.getElementById)
	{
		var al=document.getElementById('dados'+obj);
		if(!al) return;
		var br=document.getElementById("masterdiv").getElementsByTagName("div");

		if(al.style.display != "block"){

			for (var i=0; i<br.length; i++)
			{
				if ((br[i].className=="registo2" || br[i].className=="registo3") && br[i].id!='dados3'){ br[i].style.display="none";}
				if (br[i].className=="cart_tit"){ br[i].style.background="#FFFFFF";}
			}

			al.style.display="block";
			$('#tab').val(obj);
		}

		var tabs=document.querySelectorAll('.db-client-tabs [data-client-tab]');
		for(var t=0; t<tabs.length; t++)
		{
			var active=tabs[t].getAttribute('data-client-tab')==String(obj);
			tabs[t].classList.toggle('active',active);
			tabs[t].setAttribute('aria-selected',active ? 'true' : 'false');
			tabs[t].setAttribute('tabindex',active ? '0' : '-1');
		}
	}
}
function validar_registo()
{
	var cor = "#fffc00";
	var cor2 = "#FFFFFF";
	var str_err = '';
	campos_obrigatorios = new Array("m_email","m_nome", "m_apelido");
	campos_obrigatorios2 = new Array("Email - Tab ACCOUNT", "Name - Tab CLIENT INFO/BILLING", "Surname - Tab CLIENT INFO/BILLING");
	var campos_preenchidos;
	var primeiro_campo='';
	var primeiro_tab=0;
	for (obj=0; obj<campos_obrigatorios.length; obj++) { document.forms['form'].elements[campos_obrigatorios[obj]].style.background=cor2; }
	for (obj=0; obj<campos_obrigatorios.length; obj++)
	{
		if(document.forms['form'].elements[campos_obrigatorios[obj]].value=="")
		{
			campos_preenchidos=1;
			if(!primeiro_campo)
			{
				primeiro_campo=campos_obrigatorios[obj];
				primeiro_tab=(primeiro_campo=='m_email') ? 7 : 2;
			}
			document.forms['form'].elements[campos_obrigatorios[obj]].style.background=cor;
			str_err+=campos_obrigatorios2[obj]+"\n";
		}
	}
	if(campos_preenchidos==1)
	{
		mostra_dados(primeiro_tab);
		alert("The following fields are required:\n\n"+str_err);
		if(primeiro_campo && document.forms['form'].elements[primeiro_campo])
		{
			document.forms['form'].elements[primeiro_campo].focus();
		}
		return false;
	}
	return true;
}
$(function()
{
	$('#club_dataini').datepicker({showOn: "button", buttonImage: "<?=$site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	$('#club_datafim').datepicker({showOn: "button", buttonImage: "<?=$site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
	$('#m_datanascimento').datepicker({showOn: "button", buttonImage: "<?=$site_http_url?>external_api/datepicker/images/calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd'});
});
</script>

<div class="center db-clients-editor" id="masterdiv"><?php
	if((isset($_GET['show_carteira']) && $_GET['show_carteira']==1) ){}else{?>
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<? echo uniqID("")?>"  enctype="multipart/form-data" onsubmit="return  validar_registo();">
	<?php }?>
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td valign="top" align="left">
				<div class="panel panel-inverse overflow-hidden m-b-12">
			        <div class="panel-heading">
			            <h4 style="color:#fff">CLIENTES</h4>
			        </div>
			    </div>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td>


						<ul class="nav nav-tabs db-client-tabs" role="tablist" aria-label="Dados do cliente">
							<li class="nav-item">
								<a href="#dados7" class="nav-link" role="tab" data-client-tab="7" aria-controls="dados7" onclick="mostra_dados(7); return false;">Dados Conta</a>
							</li>
							<li class="nav-item">
								<a href="#dados2" class="nav-link" role="tab" data-client-tab="2" aria-controls="dados2" onclick="mostra_dados(2); return false;">Dados de Cliente</a>
							</li>
							<li class="nav-item">
								<a href="#dados1" class="nav-link" role="tab" data-client-tab="1" aria-controls="dados1" onclick="mostra_dados(1); return false;">Moradas Cliente</a>
							</li><?php if($GLOBALS['tem_bonus']==1) { ?>
							<li class="nav-item">
								<a href="#dados4" class="nav-link" role="tab" data-client-tab="4" aria-controls="dados4" onclick="mostra_dados(4); return false;">Histórico de Bónus</a>
							</li><?php } ?>
							<?php if($GLOBALS['tem_loja']==1) { ?>
							<li class="nav-item">
								<a href="#dados5" class="nav-link" role="tab" data-client-tab="5" aria-controls="dados5" onclick="mostra_dados(5); return false;">Lista de Encomendas</a>
							</li><?php } ?>
							<?php if($GLOBALS['tem_users_varios_niveis']==1) { ?>
							<li class="nav-item">
								<a href="#dados8" class="nav-link" role="tab" data-client-tab="8" aria-controls="dados8" onclick="mostra_dados(8); return false;">Utilizadores associados</a>
							</li><?php } ?>
							<?php if($GLOBALS['tem_ficha_cliente']==1) { ?>
							<li class="nav-item">
								<a href="#dados9" class="nav-link" role="tab" data-client-tab="9" aria-controls="dados9" onclick="mostra_dados(9); return false;">Ficha de cliente</a>
							</li><?php } ?>
						</ul>


                    	<!-- <table width="100%" height="30px" border=0 cellspacing=0 cellpadding="0">
                        <tr>
                            <td>
                            	<div class="nav-link" id="bts7" style="border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(7)"><? echo 'Dados Conta';?></div>
                            	<div class="nav-link" id="bts2" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(2)"><? echo 'Dados de Cliente';?></div>
			                    <div class="nav-link" id="bts1" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(1)"><? echo 'Moradas Cliente';?></div>
                                <?php /* <div class="cart_tit" id="bts2" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(2)"><? echo 'Dados de Entrega'?></div> */?>
                                <?php if($tem_loja) { ?>
                                <div class="nav-link" id="bts4" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(4)"><? echo 'Histórico de Bónus'?></div>
                                <div class="nav-link" id="bts5" style="margin-left:5px;border:#000 1px solid;float:left;padding:5px;cursor:pointer;font-size:12px;font-weight:bold;text-align:center" onclick="mostra_dados(5)"><? echo 'Lista de Encomendas'?></div>
                                <?php } ?>
							</td>
                        </tr>
                        </table> -->
                        <table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td>

                            	<div class="alert alert-success show">
									<span class="close" data-dismiss="alert">×</span>
									<i class="fa fa-check fa-2x pull-left m-r-10"></i>
									<p class="m-b-0">Todos os campos com (<span style="color:#FF0000"><span style="color:#FF0000">*</span></span>) são obrigatórios</p>
								</div>


					<div style="clear:both; height:5px"></div>
					<div class="registo2" style="float:left;border:#000 0px solid;display:none" id="dados7">

					<input type="hidden" name="slashes" value="'" />
					<input type="hidden" name="ID" value="<?=$ID;?>" />
					<input type="hidden" name="tab" id="tab" value="<? if(isset($_GET['tab']) && $_GET['tab'] > 0){echo $_GET['tab'];}?>" />
                        <div class="ct" style="width:100%">
                        	<div class="f_txt">Grupo</div>
                        	<div class="f_ipt">
                        		<? show_lista($Grupo_ID,"grupos_clientes","Grupo_ID");?>
                        	</div>
                        	<div class="f_ipt">
                        		&nbsp;<a href="../grupos_clientes/list.php" class="btn btn-primary">editar</a>
                        	</div>
                        </div><?php

                        if($GLOBALS['tem_users_varios_niveis']==1 && $GLOBALS['tem_instituicoes']==1)
						{?>



						<div style="clear:both"></div>
						<div class="ct" style="width:100%">
                        	<div class="f_txt">Privilegios</div>
                        	<div class="f_ipt">

                        		<select class="form-control" name="Privilegios">

	                        		<option value="0"  <?php if(!$Privilegios || $Privilegios==0) echo 'selected="selected"';?> >User</option>
									<option value="1"  <?php if($Privilegios==1) echo 'selected="selected"';?> >User Creator</option>
									<option value="2"  <?php if($Privilegios==2) echo 'selected="selected"';?> >Admin Creator e User Creator</option>
									<option value="3"  <?php if($Privilegios==3) echo 'selected="selected"';?> >Admin Viewer only</option>

                        		</select>


                        	</div>
                        </div>


						<div style="clear:both"></div>
						<div class="ct" style="width:100%">
                        	<div class="f_txt">Instituicao</div>
                        	<div class="f_ipt">
                        		<? show_lista($ID_instituicao,"instituicoes","ID_instituicao");?>
                        	</div>
                        	<div class="f_ipt">
                        		&nbsp;<a href="../instituicoes/list.php" class="btn btn-primary">editar</a>
                        	</div>
                        </div>
						<?
						}

						if($GLOBALS['tem_users_varios_niveis']==1 && $GLOBALS['tem_departamentos']==1)
						{?>
						<div style="clear:both"></div>
						<div class="ct" style="width:100%">
                        	<div class="f_txt">Departamento</div>
                        	<div class="f_ipt">
                        		<? show_lista($ID_departamento,"departamentos","ID_departamento");?>
                        	</div>
                        	<div class="f_ipt">
                        		&nbsp;<a href="../departamentos/list.php" class="btn btn-primary">editar</a>
                        	</div>
                        </div>
						<?
						}



                        ?>
                        <div style="clear:both"></div>
                        <div class="ct">
                        	<div class="f_txt"><?=$GLOBALS['key_email'][$GLOBALS['admin_lang']]?>(<span style="color:#FF0000;font-size:14px">*</span>)</div>
                        	<div class="f_ipt"><input type="email" placeholder="Enter email" class="form-control" autocomplete="off" title="<?=$GLOBALS['key_email'][$GLOBALS['admin_lang']]?>" name="m_email" maxlength="255" value="<?=$m_email?>" />
                        	</div>
                        </div>

                        <!-- <div style="clear:both"></div>
                        <div class="ct">
                        	<div class="f_txt"><?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?></div>
                        	<div class="f_ipt"><input type="text" class="form-control" autocomplete="off" title="<?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?>" name="m_nome" maxlength="255" value="<?=$m_nome?>" /> <span style="color:#FF0000;font-size:14px">*</span>
                        	</div>
                        </div> -->

                        <div style="clear:both"></div>
                        <div class="ct" style="width:100%">
                        	<div class="f_txt"><?=$GLOBALS['key_password'][$admin_lang]?></div>
                        	<div class="f_ipt"><input type="password" placeholder="Password" autocomplete="off" class="form-control" title="" name="m_password" autocomplete="off" maxlength="255" value="" /></div>
                        </div>
						<div title="Autenticar-se como cliente" onclick="LoginCliente('<?php echo $m_email?>', '<?php echo $Password?>')">&nbsp;<img style="cursor:pointer;" src="key.png" border=0 align="absmiddle"></div>


                        <?php /*if(isset($_GET['ID']) && $_GET['ID'] > 0){?><div class="ct" style="width:100%"><div class="f_txt" onclick="document.getElementById('pass_show').style.display='block'" style="cursor:pointer;text-decoration:underline">mostrar:</div><div class="f_ipt" id="pass_show" style="display:none;font-weight:bold;"><?=$Password?></div></div><? } */?>
                        <div style="clear:both"></div>
                        <?php
                        if(isset($GLOBALS['tem_desconto_cliente']) && $GLOBALS['tem_desconto_cliente'] == 1)
                        {
                        ?>
							<div class="ct" style="width:100%"><div class="f_txt">Desconto Cliente em %</div>
							<div class="f_ipt"><input type="text" placeholder="Desconto de cliente" class="form-control" autocomplete="off" title="Desconto" name="Desconto" maxlength="255" value="<?=$Desconto?>" />
							</div></div>

							<div style="clear:both"></div>
							<?php
                        }
                        if(isset($GLOBALS['subscribe_newsletter']) && $GLOBALS['subscribe_newsletter'] == 1)
                        {
                        ?>
							<div class="ct" style="width:100%"><div class="f_txt">Newsletter</div>
							<div class="f_ipt"><input type="radio" title="NEWSLETTER" name="m_newsletter" maxlength="255" value="1" <? if($Newsletter==1){ echo 'checked="checked"';}?>> <? echo "SIM";?>&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" title="NEWSLETTER" name="m_newsletter" maxlength="255" value="0" <? if($Newsletter==0 || !$Newsletter){ echo 'checked="checked"';}?>> <? echo 'NÃO';?><br />
							</div></div>

							<div style="clear:both"></div>
							<?php
                        }
						if(isset($GLOBALS['module_afiliado']) && $GLOBALS['module_afiliado'] == 1)
                        {
                        ?>
							<div style="clear:both"></div>
							<div class="ct" style="width:100%"><div class="f_txt">Afiliado:</div>
							<div class="f_ipt">
								<input type="checkbox" <? if($Afiliado==1){?>checked="checked"<? }?> name="Afiliado" value="1" /> Ativar opção de afiliado</div><? if($Afiliado==1){?><div class="f_ipt"><input type="text" value="<?=$Afiliado_Perc?>" name="Afiliado_Perc" style="width:40px"/> % de cada venda</div><? }?>
							</div>
                        <?php }?>


                        <div style="clear:both"></div>
                        <div class="ct" style="width:100%">
                        	<div class="f_txt">Observações</div>
                        	<div class="f_ipt"><textarea name="obs" style="width:100%;height:100px"><?php echo $obs;?></textarea> <?php echo $GLOBALS['key_sim'][$admin_lang]?></div>
                        </div>

                        <div style="clear:both"></div>
                        <div class="ct" style="width:100%">
                        	<div class="f_txt"><? echo write_lang('key_ativo','Ativo')?></div>
                        	<div class="f_ipt">
                        		<div class="checkbox checkbox-css pt-0 checkbox-inline">
									<input type="checkbox" <? if($m_ativo==1){?>checked="checked"<? }?> name="m_ativo" id="m_ativo" value="1" />
									<label for="m_ativo"><?=$GLOBALS['key_sim'][$admin_lang]?></label>
								</div>

                        	</div>
                        </div>


                        <div style="clear:both"></div>
                        <div class="ct" style="width:100%">
                        	<div class="f_txt">Email confirmado</div>
                        	<div class="f_ipt">
                        		<input type="checkbox" <? if($m_estado==1){?>checked="checked"<? }?> name="m_estado" value="1" /> <?=$GLOBALS['key_sim'][$admin_lang]?>
                        	</div>
                        </div>

                        <br><br>

                        <div style="clear:both"></div>
                        <div class="ct" style="width:100%">
                        	<div class="f_txt">Utilizador para testes</div>
                        	<div class="f_ipt">
                        		<input type="checkbox" <? if($testes==1){?>checked="checked"<? }?> name="testes" value="1" /> <?=$GLOBALS['key_sim'][$admin_lang]?>
                        	</div>
                        </div>

                        <div style="clear:both"></div>




						<div id="slidebottom">
						    <?php //show_page_buttons();?>
						</div>

					</div>
					<script>
					function adicionarBonus(id_cliente)
					{
						var valor = $("#bonus_new").val();
						if(valor>0){

							$.ajax({
								type: "POST",
								url: "pagina.php",
								data: { valor_bonus: valor, action: "add",ID: id_cliente }
							}).done(function() {
								window.location.reload();
							});
						}
					}
					function removerBonus(id_cliente){
						var valor = $("#bonus_new").val();
						if(valor>0){
							$.ajax({
								type: "POST",
								url: "pagina.php",
								data: { valor_bonus: valor, action: "remove",ID: id_cliente }
							}).done(function() {
								window.location.reload();
							});
						}
					}
					</script>
					<div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados4">

					<? //drawUIBonus($IDb)?>
					</div>
					<div class="registo2" style="float:left;border:#000 0px solid;display:none" id="dados1">
                        <div class="marg">


						<?php echo loadLivroEnderecos_admin($_GET['ID']);?>


                       <?php /*<div class="ct"><div class="f_txt"><?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?>" name="m_nome" maxlength="255" value="<?=$m_nome?>" /> <span style="color:#FF0000;font-size:14px">*</span></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?>" name="m_apelido" maxlength="255" value="<?=$m_apelido?>" /> <span style="color:#FF0000;font-size:14px">*</span></div></div>
                        <div class="ct"><div class="f_txt">Sexo</div><div class="f_ipt">
                        	<select name="m_sexo" style="height:20px;width:210px">
                        		<option value="" <?php if(!$m_sexo) echo 'selected';?>>Escolher:</option>
                        		<option value="1" <?php if($m_sexo==1) echo 'selected';?>>Masculino</option>
                        		<option value="2" <?php if($m_sexo==2) echo 'selected';?>>Feminino</option>
                        	</select>
                        </div></div>
                        <div class="ct"><div class="f_txt">Data nascimento</div><div class="f_ipt"><input type="text" class="form-control" title="Data Nascimento" name="m_datanascimento" maxlength="255" value="<?=$m_datanascimento?>" id="m_datanascimento" /></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_nif'][$admin_lang]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_nif'][$admin_lang]?>" name="m_contribuinte" maxlength="255" value="<?=$m_contribuinte?>" /></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?>" name="m_empresa" maxlength="255" value="<?=$m_empresa?>" /></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_morada'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_morada'][$GLOBALS['admin_lang']]?>" name="m_morada" maxlength="255" value="<?=$m_morada?>" /></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?>" name="m_codpos"  maxlength="255" value="<?=$m_codpos?>" /></div></div>
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_pais'][$GLOBALS['admin_lang']]?></div><div class="f_ipt">
                        <select class="form-control" name="m_pais" title="<?=$GLOBALS['key_pais'][$admin_lang]?>" style="height:20px;text-transform:uppercase;">

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
                        <div class="ct"><div class="f_txt"><?=$GLOBALS['key_tel'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_tel'][$GLOBALS['admin_lang']]?>" name="m_telefone" maxlength="255" value="<?=$m_telefone?>" /></div></div>
                        <div class="ct"><div class="f_txt">Cidade</div><div class="f_ipt"><input type="text" class="form-control" title="Cidade" name="m_cidade" maxlength="255" value="<?=$m_cidade?>" /></div></div>*/ ?>
                        </div>
					</div>
					<div class="clear"></div>
					<div class="registo3" style="float:left;border-left:#FF0000 0px solid;display:none" id="dados2">
                        <div class="marg">
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_nome'][$GLOBALS['admin_lang']]?>" name="m_nome" maxlength="255" value="<?=$m_nome?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_apelido'][$GLOBALS['admin_lang']]?>" name="m_apelido" maxlength="255" value="<?=$m_apelido?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_nif'][$admin_lang]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_nif'][$admin_lang]?>" name="m_contribuinte" maxlength="255" value="<?=$m_contribuinte?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_empresa'][$GLOBALS['admin_lang']]?>" name="m_empresa" maxlength="255" value="<?=$m_empresa?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_morada'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_morada'][$GLOBALS['admin_lang']]?>" name="m_morada" maxlength="255" value="<?=$m_morada?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_codpos'][$GLOBALS['admin_lang']]?>" name="m_codpos"  maxlength="255" value="<?=$m_codpos?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_pais'][$GLOBALS['admin_lang']]?></div><div class="f_ipt">
                            <select class="form-control" name="m_pais" title="<?=$GLOBALS['key_pais'][$admin_lang]?>" style="height:30px;">
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
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_tel'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_tel'][$GLOBALS['admin_lang']]?>" name="m2_telefone" maxlength="255" value="<?=$m2_telefone?>" /></div></div>
                            <div class="ct"><div class="f_txt"><?=$GLOBALS['key_email'][$GLOBALS['admin_lang']]?></div><div class="f_ipt"><input type="text" class="form-control" title="<?=$GLOBALS['key_email'][$GLOBALS['admin_lang']]?>" name="m2_email" maxlength="255" value="<?=$m2_email?>" /></div></div>
							<div class="ct"><div class="f_txt">Cidade</div><div class="f_ipt"><input type="text" class="form-control" title="Cidade" name="m2_cidade" maxlength="255" value="<?=$m2_cidade?>" /></div></div>
							<?php if($GLOBALS['tem_cod_instituicao']==1){?><div class="ct"><div class="f_txt">Código da instituição</div><div class="f_ipt"><input type="text" class="form-control" title="cod_instituicao" name="cod_instituicao" maxlength="255" value="<?=$m_cod_instituicao?>" /></div></div><?php }?>
                        </div>
					</div>
					<div class="registo3" style="float:left;border-left:#FF0000 0px solid;display:none" id="dados3"></div>
                        <div style="clear:both"></div>
                        <div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados6">
                        <div class="ct" style="width:100%"><div class="f_txt">Imagem</div>
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

					<?php if($GLOBALS['tem_users_varios_niveis']==1) { ?>

					<div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados8">
                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
                        <tr><td valign="top"><?php show_logins($ID,$ID_instituicao,$ID_departamento)?></td></tr>
                        </table>
					</div>

					<?php }?>



					<?php if($GLOBALS['tem_ficha_cliente']==1) { ?>

					<div class="registo3"  style="float:left;border-left:#FF0000 0px solid" id="dados9">
                        <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
                        <tr><td valign="top"><?php show_ficha_cliente($ID)?></td></tr>
                        </table>
					</div>

					<?php }?>

                    </td>

                </tr>
                <tr>
                    <td>
                    	<div id="slidebottom">
						</div>
                    </td>
                </tr>
                </table>

                <?php if((isset($_GET['show_carteira']) && $_GET['show_carteira']==1) ){}else{ show_page_buttons(); } ?>

            	</td>
				</tr>
                </table>

                <img src="../images/bsh.png" width="100%" height="30px">
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top" width="200"><? show_fast_tools();?><br /></td>
		</tr>
		</table>
<?php if((isset($_GET['show_carteira']) && $_GET['show_carteira']==1) ){}else{?>
</form><?php }?>
</div>
<script>
<? if(isset($_GET['tab']) && $_GET['tab'] > 0){?>mostra_dados(<?=$_GET['tab'];?>);<? }else{?>mostra_dados(7);<? }?>
</script>
<? 	include "../layout/bottom.inc.php";?>
