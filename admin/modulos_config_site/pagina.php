<?php
$path='../../core/';
include $path.'core_files.php';
global $admin_id_definicoes,$ss_token_definicoes;
if($admin_id_definicoes) include $path.'admin_globals.php';

/*if(!$admin_id_definicoes && !$ss_token_definicoes)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	get_cookie(chave_login);
	if($chave_login)
	{
		$sql="SELECT * FROM logins WHERE chave_login='$chave_login'";
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$admin_id=$rs->db_data["ID"];
			$admin_user=$rs->db_data["username"];
			$admin_passwd=$rs->db_data["password"];
			$ss_token_definicoes=uniqid();
			$rs->db_close();
			// if(!substr_count($referer,"index.php")  && !substr_count($referer,"logout.php") && $referer)echo'<meta http-equiv="refresh" content="0; URL='.$referer.'">';
			// else echo '<meta http-equiv="refresh" content="0; URL=index.php">';
		}
		$rs->db_close();
	}
}
if(!$admin_id_definicoes && !$ss_token_definicoes)
{

	// global $referer;
	echo comprime('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <title>ADMIN</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" type="text/css" href="'.$site_http_url.'admin/admin_estilos.css"/>
	<link rel="stylesheet" href="'.$site_http_url.'admin/layout/validationEngine.jquery.css" type="text/css" media="screen"  />
	<link rel="stylesheet" href="'.$site_http_url.'admin/layout/login.css" type="text/css" media="screen"  />
	<script type="text/javascript" src="'.$site_http_url.'admin/layout/jquery.min.js"></script>
    <script src="'.$site_http_url.'admin/layout/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
	<script src="'.$site_http_url.'admin/layout/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
	<script>
        jQuery(document).ready(function(){jQuery("#frm_login").validationEngine();});
    </script>
    <style>body{background:#d9e0e7;</style>
    </head>
    <body onLoad="document.frm_login.login.focus();">
    <div style="padding-top:100px;text-align:center"><img style="max-width:300px" src="'.$site_http_url.'admin/layout/logo.png" /></div>
    <br />
        <div>
                <table align="center" border=0 cellpadding=0 cellspacing=0 style="background-color:#2d353c;width:100%">
                <tr>
                    <td valign="top">
                    	<div id="formContainer">
							<form name="frm_login" id="frm_login" method="POST" action="index_post.php" enctype="application/x-www-form-urlencoded">
								<div style="padding-top:40px" class="m-b-20"><input class="form-control inverse-mode no-border" type="text" name="login" id="loginEmail" placeholder="Username" autocomplete="off" /></div>
								<div class="m-b-20"><input class="form-control inverse-mode no-border"  type="password" name="password" id="loginPass" placeholder="Password" autocomplete="off" /></div>
								<div class="login-buttons"><input class="form-control btn btn-success btn-block btn-lg" style="width:312px;height:43px;font-size:16px" type="submit" name="submit" value="Login" /></div>
							</form>
						</div>
					</td>
				</tr>
				</table>');

			if(isset($_GET['show_erro']) && $_GET['show_erro'] > 0)
			{
				if($_GET['show_erro']==1) $msg = write_lang('key_utilizador_inexistente','Utilizador errado ou inexistente!'); // Utililizador inexistente
				elseif($_GET['show_erro']==2) $msg = write_lang('key_password_errada','Passord Errada!'); // Password errada
				elseif($_GET['show_erro']==3) $msg = write_lang('key_password_errada','Passord Errada!'); // Password errada

				echo comprime('<div class="master"><div class="error"><div class="image"><img src="'.$site_http_url.'admin/layout/Warning.png" border="" alt="" /></div><div class="msg"><div>'.$msg.'</div></div></div></div>');
			}
            echo comprime('</div>
        </div>');
		if(isset($_GET['show_erro']) && $_GET['show_erro'] > 0)
		{
			echo comprime('<script>
			$(document).ready(function()
			{
				$(".error").slideDown("slow");
			});
			</script>');
		}
      	echo comprime('</body></html>');
}
else
{*/
		require_once "settings.inc.php"; //TODOss

		if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
		elseif(isset($_GET['duplicar']) && $_GET['duplicar']==1){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
		elseif($_GET[TBID]==0){$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
		else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
		$D = getdate();

		$_GET[TBID] = 1;

		acoes_selecionados();

		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang, $tem_loja, $tem_area_reservada;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


		$fields=db_list_fields($DB_name,'modulos_config_site',$db_link);
		$columns=db_num_fields($fields);
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('img_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD img_ext varchar(255) NOT NULL');
		if(!in_array('img2_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD img2_ext varchar(255) NOT NULL');
		if(!in_array('img3_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD img3_ext varchar(255) NOT NULL');
		if(!in_array('img4_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD img4_ext varchar(255) NOT NULL');
		if(!in_array('img5_ext',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD img5_ext varchar(2555) NOT NULL');


		if(!in_array('revendedor_id',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD revendedor_id int(11) NOT NULL');
		if(!in_array('pais_id_default',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD pais_id_default int(11) NOT NULL');
		if(!in_array('exec_caminho',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD exec_caminho varchar(255) NOT NULL');
		if(!in_array('forcar_https',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD forcar_https tinyint(1) Default 0');


		if(!in_array('FromName',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD FromName varchar(255) NOT NULL');
		if(!in_array('form_recipient',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD form_recipient varchar(255) NOT NULL');
		if(!in_array('Default_SMTP',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_SMTP varchar(255) NOT NULL');
		if(!in_array('Default_SMTP_login',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_SMTP_login varchar(255) NOT NULL');
		if(!in_array('Default_SMTP_pass',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_SMTP_pass varchar(255) NOT NULL');
		if(!in_array('Default_SMTP_Email',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_SMTP_Email varchar(255) NOT NULL');
		if(!in_array('Default_SMTP_Porta',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_SMTP_Porta INT(11) NOT NULL');

		if(!in_array('Default_notifications_email',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Default_notifications_email varchar(255) NOT NULL');

		if(!in_array('TAG_EMPRESA',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD TAG_EMPRESA varchar(255) NOT NULL');
		if(!in_array('TAG_DESIGSOCIAL',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD TAG_DESIGSOCIAL varchar(255) NOT NULL');
		if(!in_array('TAG_COPYRIGHT',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD TAG_COPYRIGHT varchar(255) NOT NULL');
		if(!in_array('SEO_FB',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD SEO_FB varchar(255) NOT NULL');
		if(!in_array('SEO_PHONE',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD SEO_PHONE varchar(255) NOT NULL');
		if(!in_array('SEO_DESCRIPTION',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD SEO_DESCRIPTION varchar(255) NOT NULL');

		if(!in_array('iva_default',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD iva_default decimal(10,4) NOT NULL');
		if(!in_array('offline',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD offline tinyint(1) NOT NULL');
		if(!in_array('offline_txt',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD offline_txt TEXT NOT NULL');
		if(!in_array('offline_cor',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD offline_cor varchar(255) NOT NULL');

		if(!in_array('emails_encomenda',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD emails_encomenda TEXT NOT NULL');
		if(!in_array('ordenar_por',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD ordenar_por varchar(255) NOT NULL');
		if(!in_array('Seta_scrollup',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD Seta_scrollup tinyint(1) NOT NULL');


		if(!in_array('stock_azul_aparece',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_azul_aparece tinyint(1) NOT NULL');
		if(!in_array('stock_vermelho_aparece',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_vermelho_aparece tinyint(1) NOT NULL');
		if(!in_array('stock_amarelo_aparece',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_amarelo_aparece tinyint(1) NOT NULL');
		if(!in_array('stock_verde_aparece',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_verde_aparece tinyint(1) NOT NULL');

		if(!in_array('stock_azul_qtd',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_azul_qtd int(11) NOT NULL');
		if(!in_array('stock_vermelho_qtd',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_vermelho_qtd int(11) NOT NULL');
		if(!in_array('stock_amarelo_qtd',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_amarelo_qtd int(11) NOT NULL');
		if(!in_array('stock_verde_qtd',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_verde_qtd int(11) NOT NULL');

		if(!in_array('stock_azul',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_azul varchar(255) NOT NULL');
		if(!in_array('stock_vermelho',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_vermelho varchar(255) NOT NULL');
		if(!in_array('stock_amarelo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_amarelo varchar(255) NOT NULL');
		if(!in_array('stock_verde',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_verde varchar(255) NOT NULL');

		if(!in_array('stock_azul_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_azul_ativo tinyint(1) NOT NULL');
		if(!in_array('stock_vermelho_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_vermelho_ativo tinyint(1) NOT NULL');
		if(!in_array('stock_amarelo_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_amarelo_ativo tinyint(1) NOT NULL');
		if(!in_array('stock_verde_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD stock_verde_ativo tinyint(1) NOT NULL');

		if(!in_array('facebook_id',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD facebook_id varchar(255) NOT NULL');
		if(!in_array('facebook_secret',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD facebook_secret varchar(255) NOT NULL');
		if(!in_array('facebook_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD facebook_ativo tinyint(1) NOT NULL');

		if(!in_array('google_id',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD google_id varchar(255) NOT NULL');
		if(!in_array('google_secret',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD google_secret varchar(255) NOT NULL');
		if(!in_array('google_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD google_ativo tinyint(1) NOT NULL');

		if(!in_array('twitter_id',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD twitter_id varchar(255) NOT NULL');
		if(!in_array('twitter_secret',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD twitter_secret varchar(255) NOT NULL');
		if(!in_array('twitter_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD twitter_ativo tinyint(1) NOT NULL');

		if(!in_array('dblocalhost',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD dblocalhost varchar(255) NOT NULL');
		if(!in_array('dbusername',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD dbusername varchar(255) NOT NULL');
		if(!in_array('dbpassword',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD dbpassword varchar(255) NOT NULL');
		if(!in_array('dbname',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD dbname varchar(255) NOT NULL');

		if(!in_array('title',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD title varchar(255) NOT NULL');
		if(!in_array('url',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD url varchar(255) NOT NULL');
		if(!in_array('url_email',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD url_email varchar(255) NOT NULL');
		if(!in_array('addthis_id',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD addthis_id varchar(255) NOT NULL');
		if(!in_array('addthis_ativo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD addthis_ativo tinyint(1)');

		if(!in_array('icon_bc',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD icon_bc tinyint(1)');
		if(!in_array('texto_bc',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD texto_bc varchar(100)');

		/* Cookies */
		if(!in_array('cookie_txt_barra',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD cookie_txt_barra varchar(255)');
		if(!in_array('cookie_txt_modal',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD cookie_txt_modal text');
		if(!in_array('cookie_posicao',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD cookie_posicao tinyint(1)');

		$add_fields = array('valor_min',
						    'onoff_valor_min'
						    );
		foreach ($add_fields as $field) {
			if(!in_array($field,$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE modulos_config_site ADD '.$field.' tinyint(1)');
		}

		$ID_pai = 0; $tem_voucher_prods = 0;
		if(isset($_GET[TBID]) && $_GET[TBID]!=0)
		{
			$rs = new DataObj; $rs2 = new DataObj;
			$sql = 'SELECT '.implode(',',$CMPS_MODULO_DADOS).' FROM '.TBMODULO.' WHERE '.TBID.'='.$_GET[TBID];
			$rs->db_open($db_link, $DB_name, $sql);
			if(!$rs->EOF())
			{
				$ID = $rs->db_data['ID'];
				$img_ext = $rs->db_data['img_ext'];
				$img2_ext = $rs->db_data['img2_ext'];
				$img3_ext = $rs->db_data['img3_ext'];
				$img4_ext = $rs->db_data['img4_ext'];
				$img5_ext = $rs->db_data['img5_ext'];
				$revendedor_id = $rs->db_data['revendedor_id'];
				$pais_id_default = $rs->db_data['pais_id_default'];
				$exec_caminho = $rs->db_data['exec_caminho'];
				$forcar_https = $rs->db_data['forcar_https'];
				$FromName1 = $rs->db_data['FromName'];
				$form_recipient1 = $rs->db_data['form_recipient'];
				$Default_SMTP1 = $rs->db_data['Default_SMTP'];
				$Default_SMTP_login1 = $rs->db_data['Default_SMTP_login'];
				$Default_SMTP_pass1 = $rs->db_data['Default_SMTP_pass'];
				$Default_SMTP_Email1 = $rs->db_data['Default_SMTP_Email'];
				$Default_SMTP_Porta1 = $rs->db_data['Default_SMTP_Porta'];
				$Default_notifications_email1 = $rs->db_data['Default_notifications_email'];
				$TAG_EMPRESA = $rs->db_data['TAG_EMPRESA'];
				$TAG_DESIGSOCIAL = $rs->db_data['TAG_DESIGSOCIAL'];
				$TAG_COPYRIGHT = $rs->db_data['TAG_COPYRIGHT'];
				$SEO_FB = $rs->db_data['SEO_FB'];
				$SEO_PHONE = $rs->db_data['SEO_PHONE'];
				$SEO_DESCRIPTION = $rs->db_data['SEO_DESCRIPTION'];
				$iva_default = $rs->db_data['iva_default'];
				$ordenar_por = $rs->db_data['ordenar_por'];
				$offline = $rs->db_data['offline'];
				$offline_txt = $rs->db_data['offline_txt'];
				$offline_cor = $rs->db_data['offline_cor'];
				$Seta_scrollup = $rs->db_data['Seta_scrollup'];
				$emails_encomenda = $rs->db_data['emails_encomenda'];

				$stock_azul_aparece = $rs->db_data['stock_azul_aparece'];
				$stock_vermelho_aparece = $rs->db_data['stock_vermelho_aparece'];
				$stock_amarelo_aparece = $rs->db_data['stock_amarelo_aparece'];
				$stock_verde_aparece = $rs->db_data['stock_verde_aparece'];

				$stock_azul_qtd = $rs->db_data['stock_azul_qtd'];
				$stock_vermelho_qtd = $rs->db_data['stock_vermelho_qtd'];
				$stock_amarelo_qtd = $rs->db_data['stock_amarelo_qtd'];
				$stock_verde_qtd = $rs->db_data['stock_verde_qtd'];

				$stock_azul = $rs->db_data['stock_azul'];
				$stock_vermelho = $rs->db_data['stock_vermelho'];
				$stock_amarelo = $rs->db_data['stock_amarelo'];
				$stock_verde = $rs->db_data['stock_verde'];

				$stock_azul_ativo = $rs->db_data['stock_azul_ativo'];
				$stock_vermelho_ativo = $rs->db_data['stock_vermelho_ativo'];
				$stock_amarelo_ativo = $rs->db_data['stock_amarelo_ativo'];
				$stock_verde_ativo = $rs->db_data['stock_verde_ativo'];

				$facebook_id = $rs->db_data['facebook_id'];
				$facebook_secret = $rs->db_data['facebook_secret'];
				$facebook_ativo = $rs->db_data['facebook_ativo'];

				$google_id = $rs->db_data['google_id'];
				$google_secret = $rs->db_data['google_secret'];
				$google_ativo = $rs->db_data['google_ativo'];

				$twitter_id = $rs->db_data['twitter_id'];
				$twitter_secret = $rs->db_data['twitter_secret'];
				$twitter_ativo = $rs->db_data['twitter_ativo'];

				$dblocalhost = $rs->db_data['dblocalhost'];
				$dbusername = $rs->db_data['dbusername'];
				$dbpassword = $rs->db_data['dbpassword'];
				$dbname = $rs->db_data['dbname'];

				$title = $rs->db_data['title'];
				$url = $rs->db_data['url'];
				$url_email = $rs->db_data['url_email'];

				$addthis_id = $rs->db_data['addthis_id'];
				$addthis_ativo = $rs->db_data['addthis_ativo'];
				$icon_bc = $rs->db_data['icon_bc'];
				$texto_bc = $rs->db_data['texto_bc'];

				$cookie_txt_barra = $rs->db_data['cookie_txt_barra'];
				$cookie_txt_modal = $rs->db_data['cookie_txt_modal'];
				$cookie_posicao = $rs->db_data['cookie_posicao'];
				$cred_min = $rs->db_data['cred_min'];
				$onoff_valor_min = $rs->db_data['onoff_valor_min'];
			}
			$rs->db_close();
		}

		require_once "../layout/header.inc.php";
		require_once "../layout/body.inc.php";
	?>
	<link rel="stylesheet" type="text/css" href="sa.css" />
	<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
	<script>var tabsel = '<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>';</script>
	<script>var tabsel2 = '<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>';</script>
	<script>var tabsel3 = '<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>';</script>
	<script>var SITE_HTTP_URL = '<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL = '<?php echo $GLOBALS['site_shell_url']?>';</script>
	<script>$(document).ready(function()
	{
		$('.fancybox_img').fancybox();
		$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
		$('.fancybox3').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1000, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	});
	function addToProduct(){
		$("#s_listafamiliasprod").append($("#listademarcas option:selected"));
	}
	function removeFromProduct(){
		$("#listademarcas").append($("#s_listafamiliasprod option:selected"));
	}

	</script>
	<script language="JavaScript" src="sa.js"></script>
	<div class="center" style="position:relative;" id="wp">
	<form name="form" method="post" action="pagina_trata.php?<?php echo TBID?>=<?php echo $ID?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang'];?>" />
	<input type="hidden" name="<?php echo TBID?>" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 0 : $ID;?>" />
	<input type="hidden" name="duplicar" value="<?php echo (isset($_GET['duplicar']) && $_GET['duplicar'] > 0) ? 1 : 0;?>" />
	<input type="hidden" name="ctab" id="ctab" value="<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>" />
	<input type="hidden" name="ctab2" id="ctab2" value="<?php echo (isset($_GET['tab2']) && $_GET['tab2'] > 0) ? $_GET['tab2'] : '1';?>" />
	<input type="hidden" name="ctab3" id="ctab3" value="<?php echo (isset($_GET['tab3']) && $_GET['tab3'] > 0) ? $_GET['tab3'] : '1';?>" />
	<input type="hidden" name="slashes" value="'" />
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
	<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
	<td valign="top">
	<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
	<tr>
	<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de <?php echo MODULO?></a> <?php echo show_menupath(((isset($_GET['ID_pai']) && $_GET['ID_pai'] > 0) ? $_GET['ID_pai'] : $_GET['ID']),'blocos');?></td>
	</tr>
	</table>
	</tr>
	<tr>
	<td align="left" valign="top" class="preto" style="background:#FFF">
	<div class="wraper" id="wraper">

	<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1">Plataforma</div>
	<div class="bigtitletab" onclick="infinittab(2);return false;" id="menutab2">Logotipos</div>
	<div class="bigtitletab" onclick="infinittab(3);return false;" id="menutab3">SMTP</div>
	<?php if($tem_loja) {?><div class="bigtitletab" onclick="infinittab(4);return false;" id="menutab4">Loja</div><?php } ?>
	<div class="bigtitletab" onclick="infinittab(5);return false;" id="menutab5">Manutenção</div>
	<?php if($tem_loja) {?><div class="bigtitletab" onclick="infinittab(6);return false;" id="menutab6">Stock</div><?php } ?>
	<div class="bigtitletab" onclick="infinittab(7);return false;" id="menutab7">Login APIs</div>
	<div class="bigtitletab" onclick="infinittab(8);return false;" id="menutab8">Base de Dados</div>
	<div class="bigtitletab" onclick="infinittab(9);return false;" id="menutab9">Cookies</div>
	<div class="clearboth"></div>
	<div class="tabtop-open3" id="tab1"><?php require_once 'tabs/tab_dados.php';?></div>
	<div class="tabtop-open3" id="tab2"><?php require_once 'tabs/tab_logos.php';?></div>
	<div class="tabtop-open3" id="tab3"><?php require_once 'tabs/tab_smtp.php';?></div>
	<div class="tabtop-open3" id="tab4"><?php require_once 'tabs/tab_loja.php';?></div>
	<div class="tabtop-open3" id="tab5"><?php require_once 'tabs/tab_manutencao.php';?></div>
	<div class="tabtop-open3" id="tab6"><?php require_once 'tabs/tab_stock.php';?></div>
	<div class="tabtop-open3" id="tab7"><?php require_once 'tabs/tab_login_social.php';?></div>
	<div class="tabtop-open3" id="tab8"><?php require_once 'tabs/tab_data_base.php';?></div>
	<div class="tabtop-open3" id="tab9"><?php require_once 'tabs/tab_cookies.php';?></div>

	</div>
		<?php show_page_buttons();?>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</form>

	</div>
	<script src="chosen.jquery.min.js" type="text/javascript"></script>
<?php
	require_once "../layout/bottom.inc.php";
/*}*/
?>