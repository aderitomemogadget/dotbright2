<?php
	include "settings.inc.php";
	global $site_http_url;
	$linguas_arr = explode(",",$linguas);
	$linguas_desc_arr = explode(",",$linguas_desc);
	if($task=="apagar"){$task_button = "Apagar este Registo"; $task_form = "apaga"; $btn_class='btn-danger';}
	elseif ($task == "copiar")	{$task_button = "Copiar"; $task_form = "copia";}
	elseif($ID==0){$task_button = "Gravar o Novo Registo"; $task_form = "insere"; $btn_class='btn-primary';}
	else {$task_button = "Gravar alterações"; $task_form = "altera"; $btn_class='btn-primary';}
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}



	include '../layout/header.inc.php';
	include '../layout/body.inc.php';
?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<script language="JavaScript" src="sa.js"></script>
<style>.new_quadros{background:#FFF !important}</style>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top" align="left"><?php include '../layout/menus.inc.php';?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
            <div class="panel-heading">
                <h4 style="color:#fff">NEWSLETTER - CONFIG</h4>
            </div>
        </div><?

	$cabecalho='';	$texto='';	$m_visivel='';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if($ID != 0)
	{
		$dados = BuscarNewsletterindice2($ID);
		if($dados)
		{
			$ID = $dados->db_data["ID"];
			$cabecalho = $dados->db_data["Cabecalho"];
			$assunto = $dados->db_data["Assunto"];
            if($task == "copiar") $m_nome .= " (CÓPIA)";
			$texto = $dados->db_data["Texto"];
			$texto = limpa_editor_lixo($texto);
			$m_lingua = $dados->db_data["Lingua"];
			$m_visivel = $dados->db_data["Visivel"];
			$SMTP = $dados->db_data["SMTP"];
			$SMTP_Email = $dados->db_data["SMTP_Email"];
			$SMTP_login = $dados->db_data["SMTP_login"];
			$SMTP_pass = $dados->db_data["SMTP_pass"];
			$EmailToReply = $dados->db_data["EmailToReply"];
			$cabecalhoToReply = $dados->db_data["cabecalhoToReply"];
			$Template = $dados->db_data["Template"];
			$Titulo = $dados->db_data["Titulo"];
		}
		$dados->db_close();
	}

	if($cabecalho=='' && $texto=='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$config= new DataObj;
		$sql_config="select * from nl_config where ID=1";
		$hRes_config= $config->db_open($db_link,$DB_name,$sql_config);

		if(!$config->EOF())
		{
			$cabecalho=$config->db_data["cabecalho"];
			$SMTP=$config->db_data["SMTP"];
			$SMTP_Email=$config->db_data["SMTP_Email"];
			$SMTP_login=$config->db_data["SMTP_login"];
			$SMTP_pass=$config->db_data["SMTP_pass"];
			$emails_vez=$config->db_data["emails_vez"];

			$EmailToReply=$config->db_data["EmailToReply"];
			$cabecalhoToReply=$config->db_data["cabecalhoToReply"];
		}
		$config->db_close();
	} ?>

<form name="form" method="post" action="newsletter_trata.php?dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<input type="hidden" name="ID" value="<?php echo $ID?>" />
	<input type="hidden" name="task_form" value="<?php echo $task_form?>" />
	<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
	<script type="text/javascript" src="jscolor/jscolor.js"></script>

	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
	    <td width="3" height="10"></td>
	    <td width="138" align="left" valign="top"></td>
	    <td align="left" valign="top" width="578"></td>
	    <td width="6"></td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Nome interno:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalho" maxlength="255" value="<?php echo $cabecalho;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP server:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP" maxlength="255" value="<?php echo $SMTP;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP Email:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_Email" maxlength="255" value="<?php echo $SMTP_Email;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>

	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP login:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_login" maxlength="255" value="<?php echo $SMTP_login;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP pass:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_pass" maxlength="255" value="<?php echo $SMTP_pass;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Enviar e-mail para responder:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="EmailToReply" maxlength="255" value="<?php echo $EmailToReply;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Cabeçalho para responder:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalhoToReply" maxlength="255" value="<?php echo $cabecalhoToReply;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Assunto:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="assunto" maxlength="255" value="<?php echo $assunto;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="top" width="138">Título da newsletter:</td>
		<td align="left" valign="top" width="578"> <input class="form-control" name="n_titulo" maxlength="255" value="<?php echo $Titulo;?>"></td>
		<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td colspan="2" align="left">Conteúdo da Newsletter:<textarea id="texto" name="texto" style="WIDTH: 100%; HEIGHT: 200px"><?php echo $texto;?></textarea><? show_editor("oEdit1","texto", 880, 450);?></td>
		<td></td>
	</tr>
	</table>

	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>

	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td align="center" valign="middle" class="t1">
		<input class="btn btn-primary" type="submit" name="Submit" value=" <?php echo $task_button?> ">
		&nbsp;<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onClick="window.open('list.php','_self')">
		&nbsp;<input class="btn btn-info" type="button" name="pagina" value=" Pré-visualizar newsletter " onClick="window.open('../../show_newsletter.php?ID=<?php echo $ID?>&amp;prev=1','_blank')">
		</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?php if($Template){ ?>
	<input type="hidden" name="TEMPLATE" value="1" />
<?php }?>
</form>
</td>
<td width="10px">&nbsp;</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>
<? 	include "../layout/bottom.inc.php";?>