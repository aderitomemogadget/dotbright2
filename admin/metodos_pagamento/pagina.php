<?php
	include "settings.inc.php";

	if($task == "apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task == "duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($id_metodo == 0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}

	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$fields=db_list_fields($DB_name, 'metodos_pagamento', $db_link);
	$columns = db_num_fields($fields);
	for ($i=0; $i < $columns; $i++){ $field_array[] = db_field_name($fields,$i);}
	if(!in_array('limite', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD limite decimal(10,4) NOT NULL');
	if(!in_array('metodo_taxa_tipo', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD metodo_taxa_tipo tinyint(1) NOT NULL');

	if(!in_array('ep_account_id_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_account_id_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_api_key_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_api_key_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_user_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_user_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_cin_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_cin_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_entity_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_entity_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_url_sandbox', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_url_sandbox varchar(100) NOT NULL');
	if(!in_array('ep_account_id_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_account_id_production varchar(100) NOT NULL');
	if(!in_array('ep_api_key_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_api_key_production varchar(100) NOT NULL');
	if(!in_array('ep_user_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_user_production varchar(100) NOT NULL');
	if(!in_array('ep_cin_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_cin_production varchar(100) NOT NULL');
	if(!in_array('ep_entity_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_entity_production varchar(100) NOT NULL');
	if(!in_array('ep_url_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_url_production varchar(100) NOT NULL');
	if(!in_array('ep_sandbox_production', $field_array)) $res = Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD ep_sandbox_production varchar(100) NOT NULL');
	if(!in_array('Abreviatura',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD Abreviatura varchar(150) NOT NULL');
	if(!in_array('Ordem',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE metodos_pagamento ADD Ordem int(11) NOT NULL');



	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	if($id_metodo != 0)
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;$rs2 = new DataObj;
		$sql = 'SELECT * FROM metodos_pagamento WHERE ID='.$id_metodo.' ORDER BY Ordem ASC';
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$id_metodo 					= $rs->db_data["ID"];
			$Ordem 						= $rs->db_data["Ordem"];
			$Visivel					= $rs->db_data['Visivel'];
			$img_ext					= $rs->db_data['img_ext'];
			$metodo_sandbox				= $rs->db_data['Sandbox'];
			$metodo_username			= $rs->db_data['Username'];
			$metodo_username_sandbox	= $rs->db_data['Username_sandbox'];
			$metodo_password 			= $rs->db_data['Password'];
			$metodo_password_sandbox	= $rs->db_data['Password_sandbox'];
			$metodo_taxa 				= $rs->db_data['Taxa'];
			$tipo 						= $rs->db_data['Tipo'];
			$metodo_limite 				= $rs->db_data['limite'];
			$metodo_taxa_tipo 			= $rs->db_data['metodo_taxa_tipo'];
			$Abreviatura				= $rs->db_data['Abreviatura'];

			$sql2='SELECT * FROM metodos_pagamento_lang WHERE ID_PAI="'.$id_metodo.'" and Lang="'.$admin_lang.'"';
			$rs2->db_open($db_link,$DB_name,$sql2);
			if(!$rs2->EOF())
			{
				$descricao = $rs2->db_data['Cabecalho'];
				$metodo_descricao = stripslashes($rs2->db_data['Texto1']);
			}
			$rs2->db_close();

			$metodo_chave 				= $rs->db_data['chavebo']; //MB ifthen
			$metodo_entidade 			= $rs->db_data['entidade'];
			$metodo_subentidade 		= $rs->db_data['subentidade'];
			$metodo_nib 				= $rs->db_data['nib'];
			$metodo_iban 				= $rs->db_data['iban'];
			$metodo_swift 				= $rs->db_data['swift'];
			$metodo_contractnumber		= $rs->db_data['Contractnumber'];
			$metodo_account_id			= $rs->db_data['Account_id'];
			$metodo_account_id_sandbox	= $rs->db_data['Account_id_sandbox'];
			$metodo_merchant_site_id 	= $rs->db_data['Merchant_site_id'];
			$metodo_merchant_site_id_sandbox = $rs->db_data['Merchant_site_id_sandbox'];
		}
		$rs->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
	<script>$(function(){ $('.result').hide();$('.result2').hide();});</script>
	<link rel="stylesheet" href="sa.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" href="chosen/chosen.css?dp=<?php echo dumpacache?>" />

	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?id_metodo=<?php echo $id_metodo?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">

		<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
		<input type="hidden" name="id_metodo" value="<?php echo $id_metodo;?>" />
		<input type="hidden" name="slashes" value="'" />

		<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td valign="top" align="left">

		<?php include "../layout/menus.inc.php"; ?>

		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<tr>
		<td valign="top">

		<table width="100%" border="0" cellspacing="0" cellpadding="5" class="subtitulo_janela">
		<tr>
		<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de Métodos de Pagamento</a></td>
		</tr>
		</table>

		</td>
		</tr>
		<tr>
		<td align="left" valign="top" class="preto">

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp; </td></tr>
		<tr>
		<td width="5" height="26">&nbsp; </td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Visível:&nbsp;</td>
		<td style="font-weight:bold"><input  type="checkbox" name="Visivel" id="Visivel" <? if($Visivel == 1) echo 'checked="checked"';?> value="1" /></td>
		<td width="5" height="26">&nbsp; </td>
		</tr>
		</table>

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp; </td></tr>
		<tr>
		<td width="5" height="26">&nbsp;</td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Ordem:&nbsp;</td>
		<td class="new_preto"><input type="text" name="Ordem" id="Ordem" style="WIDTH: 100px"  value="<?php echo $Ordem;?>" class="box" /></td>
		<td width="5" height="26">&nbsp; </td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>


		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp; </td></tr>
		<tr>
		<td width="5" height="26">&nbsp;</td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Nome:&nbsp;</td>
		<td class="new_preto"><input type="text" name="nome_metodo" id="nome_metodo" style="WIDTH: 90%"  value="<?php echo $descricao;?>" class="box" /></td>
		<td width="5" height="26">&nbsp; </td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>


		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp; </td></tr>
        <tr>
			<td width="5" height="26">&nbsp;</td>
			<td width="200" class="new_preto">&nbsp;&nbsp;Abreviatura:&nbsp;</td>
			<td class="new_preto"><input type="text" name="Abreviatura" id="Abreviatura"  style="WIDTH: 90%" value="<?php echo $Abreviatura;?>" class="box" /></td>
			<td width="5" height="26">&nbsp;</td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp; </td></tr>
		</table>

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		<tr>
		<td width="5" height="26"></td>
		<td width="200" class="new_preto" valign="top">&nbsp;&nbsp;Tipo:&nbsp;</td>
		<td class="new_preto">

		<?php

			global $admin_id;

			?>
				<select name="tipo_metodo" id="tipo_metodo" class="chzn-select" style="width:90%">
				<?php foreach($pgm_metodos as $key => $value){?>
				<option value="<?php echo $key?>" <?php if($tipo == $key) echo ' selected="selected" ';?>><?php echo $value['nome'];?></option>
				<?php }?>
				</select>

		</td>
		<td width="5" height="26"></td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		<tr>
		<td width="5" height="26"></td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Imagem:&nbsp;</td>
		<td class="new_preto">
		<?php
		if($img_ext != '')
		{
		?>
			<div class="imagens_400" style="max-height:100px;"><?php if(file_exists('../../files/metodos_pagamento/'.$id_metodo.'_1.'.$img_ext)){?><img alt="" src="<?php echo '../../files/metodos_pagamento/'.$id_metodo.'_1.'.$img_ext;?>" /><?php }?></div>
		<?php
			if($task != "apagar")
			{
				echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1' /> <?php echo write_lang('key_ou','ou'); echo ' '.write_lang('key_alterar','alterar');?> <input type='file' name='img_peq1' /> <input type='hidden' name='tem_foto1' value='1' /><?php
			}
		}
		else
		{
			echo write_lang('key_nova','Nova');?>: <input type="file" name='img_peq1' /> <input type='hidden' name='tem_foto1' value='0' />
		<?php }?>
		</td>
		<td width="5" height="26"></td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		<tr>
		<td width="5" height="26"></td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Informação:&nbsp;</td>
		<td class="new_preto"><textarea name="metodo_descricao" id="metodo_descricao" style="WIDTH: 90%; HEIGHT: 80px"><?php echo $metodo_descricao;?></textarea></td>
		<td width="5" height="26"></td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>

		<?php
			if(isset($tipo))
			{
				// PAYPAL
				if(isset($pgm_metodos[1]) && $tipo == 1) require 'metodos/paypal.php';

				// TRANSFERENCIA
				if(isset($pgm_metodos[2]) && $tipo == 2) require 'metodos/transferencia.php';

				// UNICRE
				if(isset($pgm_metodos[3]) && $tipo == 3) require 'metodos/unicre.php';

				// MB IFTHEN
				if(isset($pgm_metodos[4]) && $tipo == 4) require 'metodos/ref_multibanco_ifthen.php';

				// À COBRANÇA
				if(isset($pgm_metodos[5]) && $tipo == 5) require 'metodos/cobranca.php';

				// HYPAY
				if(isset($pgm_metodos[6]) && $tipo == 6) require 'metodos/hipay.php';

				// MEMOPAY
				if(isset($pgm_metodos[7]) && $tipo == 7) require 'metodos/memopay.php';

				// LUSOPAY MB
				if(isset($pgm_metodos[9]) && $tipo == 9) require 'metodos/ref_multibanco_lusopay.php';

				// LUSOPAY MBWAY
				if(isset($pgm_metodos[15]) && $tipo == 15) require 'metodos/mbway_lusopay.php';

				// SIBS MULTIBANCO
				if(isset($pgm_metodos[11]) && $tipo == 11) require 'metodos/ref_multibanco_sibs.php';

				// SIBS MBWAY
				if(isset($pgm_metodos[12]) && $tipo == 12) require 'metodos/mbway_sibs.php';

				//ONEY
				if(isset($pgm_metodos[21]) && $tipo == 21) require 'metodos/oney.php';
			}
		?>

		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		<tr>
		<td width="5" height="26">&nbsp; </td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Taxa:&nbsp;</td>
		<td>

			<div style="float:left"><input type="text" name="metodo_taxa" id="metodo_taxa" style="WIDTH:150px"  value="<?php echo $metodo_taxa;?>" class="box" /></div>
			<div style="float:left;padding-top:8px;padding-left:8px"><input type="radio" name="metodo_taxa_tipo" id="metodo_taxa_tipo"  value="0" <?php if(!$metodo_taxa_tipo || $metodo_taxa_tipo==0 ){echo 'checked="checked"';}?> />Valor (€)</div>
			<div style="float:left;padding-top:8px;padding-left:8px"><input type="radio" name="metodo_taxa_tipo" id="metodo_taxa_tipo"  value="1" <?php if($metodo_taxa_tipo==1){echo 'checked="checked"'; }?> /> Percentagem(%)</div>

		</td>
		<td width="5" height="26">&nbsp; </td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>


		<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF;margin-bottom:5px">
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		<tr>
		<td width="5" height="26">&nbsp; </td>
		<td width="200" class="new_preto">&nbsp;&nbsp;Limite:&nbsp;</td>
		<td><input type="text" name="limite" id="limite" style="WIDTH: 90%"  value="<?php echo $metodo_limite;?>" class="box" /></td>
		<td width="5" height="26">&nbsp; </td>
		</tr>
		<tr><td colspan="10" height="10">&nbsp;</td></tr>
		</table>
		</td>
		</tr>

		</table>
		<?php show_page_buttons();?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="220"><?php show_fast_tools();?></td>
		</tr>
		</table>
		</form>
	</div>
<script src="chosen/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php include "../layout/bottom.inc.php";?>