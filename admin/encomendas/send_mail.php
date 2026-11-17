<?php
	include "settings.inc.php";
	if(!isset($_GET['enc_id']) && !$_GET['enc_id']) exit;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
<link rel="stylesheet" type="text/css" href="../admin_estilos.css" />
<link rel="stylesheet" type="text/css" href="sa.css" />
<style>html, body{background:#FFF !important}</style>
</head>
<body>
<?php
//
if(isset($_POST['envia_mail']) && $_POST['envia_mail']==1)
{
	$EMAIL = $_POST['EMAIL'];
	$TITULO = $_POST['TITULO'];
	// $RESUMO = str_replace("\n", "<br />", $_POST['RESUMO']);
	$RESUMO = $_POST['RESUMO'];
	$res = send_email($EMAIL, $TITULO, $RESUMO);
	if($res)
	{
		$log_dados = serialize(array($EMAIL, $TITULO, $RESUMO));
		echo show_alerta('Email enviado com sucesso!<br /><br /><a class="pretourl" href="#" onclick="parent.$.fancybox.close();return false;">Fechar Janela</a>','msg');
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql = 'INSERT INTO encomendas_emails (ID_ORG, Data_envio, MailTo, Subject, Body) VALUES("'.$_POST['ID_ENC'].'","'.date('Y-m-d H:i:s').'","'.$EMAIL.'","'.addslashes($TITULO).'","'.addslashes($RESUMO).'")';
		$res = Runquery($db_link,$DB_name,$sql);
	}
	else  echo show_alerta('Houve um problema ao enviar o Email, por favor, tente mais tarde!<br /><br /><a class="pretourl" href="#" onclick="parent.$.fancybox.close();return false;">Fechar Janela</a>','msg');
}
else
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;
	$sql='SELECT * FROM encomendas WHERE ID="'.$_GET['enc_id'].'"';
	$rs->db_open($db_link, $DB_name, $sql);
	$id = intval($_GET['enc_id']);
	$Email = $rs->db_data['enc_Email'];
	$Nome = $rs->db_data['enc_Nome'];
	$Apelido = $rs->db_data['enc_Apelido'];
	$Telefone = $rs->db_data['enc_Telefone'];
	$Lingua = $rs->db_data['Lingua'];
	$rs->db_close();

	$prods = explode(';', $_GET['prods']);

	$linhas_prods = '';
	$voucher_gift = '';
	for($a=0; $a<count($prods); $a++)
	{
		$sql='SELECT Ref, Nome, Estado, voucher_gift, Email_opcoes, Texto_opcoes FROM encomendas_produtos WHERE ID_PAI="'.$_GET['enc_id'].'" AND ID="'.$prods[$a].'"';
		// echo $sql.'<br>';
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$prod_estado = $rs->db_data['Estado'];
			$prod_ref = $rs->db_data['Ref'];
			$prod_nome = $rs->db_data['Nome'];
			$voucher_gift = $rs->db_data['voucher_gift'];
			$Email_opcoes = $rs->db_data['Email_opcoes'];
			$Texto_opcoes = $rs->db_data['Texto_opcoes'];
		}
		$rs->db_close();
		if($Email_opcoes != '') $Email = $Email_opcoes;

		// echo $prod_estado.'<br>';
		$enc_state = estado_encomenda($prod_estado, $Lingua);
		// echo 'xxx'.getLinguas('key_not_available');
		// echo $GLOBALS['prod'][$Lingua];
		// $prod_tr = str_replace('[produto]', str_replace('-', '', $prod_ref).' - '.$prod_nome, $GLOBALS['prod'][$Lingua]);
		$prod_tr = str_replace('[produto]', str_replace('-', '', $prod_ref).' - '.$prod_nome, $GLOBALS['prod'][$Lingua]);
		$prod_tr = str_replace('[estado]', $enc_state['titulo2'], $prod_tr);

		if(!substr_count($linhas_titulos, $enc_state['titulo']))
		{
			if($linhas_titulos != '') $linhas_titulos .= ' / '.$enc_state['titulo'];
			else $linhas_titulos .= $enc_state['titulo'];
		}

		if($linhas_prods != '')  $linhas_prods .= '<br>'.$prod_tr.'.';
		else  $linhas_prods .= $prod_tr.'.';

	}

	$body = array(
				'[produtos]' => $linhas_prods,
				'[area_cliente]' => $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1&id_enc='.$_GET['enc_id'],
				'[nome]' => $Nome.' '.$Apelido,
				'[movel]' => $Telefone,
				'[site]' => $GLOBALS['site_http_url'],
				'[voucher_code]' => $voucher_gift,
				'[friend_message]' => $Texto_opcoes
			);

	$message = template_email($GLOBALS['template_encomenda'], $body, 800, 0,$enc_state['texto'], 0, $linhas_titulos);
?>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
	<form action="" method="post">
	<input type="hidden" value="1" name="envia_mail" />
	<input type="hidden" name="ID_ENC" value="<?php echo $_GET['enc_id']?>" />
	<table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto" width="90%">
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
	<td width="20" align="left" style="font-size:12px">&nbsp;</td>
	<td width="130" align="left" style="font-size:12px">Email:</td>
	<td align="left"><input style="width:200px" class="FROMm-control"  type="text" name="EMAIL" id="EMAIL" value="<?php echo $Email;?>" class="new_input" /></td>
	</tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
	<td width="20" align="left" style="font-size:12px">&nbsp;</td>
	<td align="left" style="font-size:12px">Título:</td>
	<td align="left"><input style="width:500px" class="form-control"  type="text" name="TITULO" id="TITULO" value="<?php echo $linhas_titulos?>" class="new_input" /></td>
	</tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
	<td width="20" height="25" align="left" style="font-size:12px">&nbsp;</td>
	<td align="left" style="font-size:12px" valign="top" colspan="3">Corpo da Mensagem:</td>

	</tr>
	<tr>
	<td width="20" align="left" style="font-size:12px">&nbsp;</td>
	<td  colspan="2"><textarea style="WIDTH: 500px; HEIGHT: 150px"  class="form-control" name="RESUMO" id="RESUMO"><?php echo stripslashes($message['body'])?></textarea> <? show_editor_small("oEdit1","RESUMO",880,250)?></td>
	</tr>
	<tr><td colspan="10">&nbsp;</td></tr>
	<tr>
	<td width="20" align="left" style="font-size:12px">&nbsp;</td>
	<td align="right" style="font-size:12px" colspan="2"><input class="btn btn-primary" type="submit" name="Submit" value="Enviar Email"></td>

	</tr>
	</table>
	</form>
<?php }?>
</body>
</html>