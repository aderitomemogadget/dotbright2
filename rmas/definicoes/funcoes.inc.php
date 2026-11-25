<?
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?
		
				echo '<input class="botao" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('../index.php','_self')" /><? 
				}
				if($task_form=="altera"){?><input class="botao" type="button" name="Apagar" value="<? echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?=$ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="botao" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('../index.php','_self')" />
		</td>
     </tr>
	</table>
<?
}
function gravaFicheiroSettings(){


	$eol = "";

	$str ='<?php 
	if(isset($_SERVER["SCRIPT_FILENAME"]) && $_SERVER["SCRIPT_FILENAME"] == __FILE__ ) exit();
	$dev = '.$_POST["dev"].';
	// BASE DE DADOS
	$DB_host = \''.$_POST["DB_host"].'\';
	$DB_user = \''.$_POST["DB_user"].'\';
	$DB_pass = \''.$_POST["DB_pass"].'\';
	$DB_name = \''.$_POST["DB_name"].'\';
	// CAMINHOS ABSOLUTOS
	$http 					= \''.$_POST["http"].'\';
	$dev_folder 			= \''.$_POST["dev_folder"].'\';
	$site_shell_url 		= \''.$_POST["site_shell_url"].'\';
	$site_http_url 			= \''.$_POST["site_http_url"].'\';
	$site_https_url 		= \''.$_POST["site_https_url"].'\';
	$website_url_email 		= \''.$_POST["website_url_email"].'\';
	$m_http_url_images 		= \''.$_POST["m_http_url_images"].'\';
	$site_http_ajax_url 	= \''.$_POST["site_http_ajax_url"].'\';
	$site_http_url_internal = \''.$_POST["site_http_url_internal"].'\';
	// DADOS PARA WEBSITE / RSS
	$site_title 			= \''.$_POST["site_title"].'\';
	$rss_site_url 			= \''.$_POST["rss_site_url"].'\';
	$rss_site_description 	= \''.$_POST["rss_site_description"].'\';
	$rss_email_editor 		= \''.$_POST["rss_email_editor"].'\';
	$rss_email_autor 		= \''.$_POST["rss_email_autor"].'\';
	$rss_email_webmaster 	= \''.$_POST["rss_email_webmaster"].'\';
	$site_copyright 		= \''.$_POST["site_copyright"].'\';
	// CONFIGURAÇÃO DE EMAIL
	$FromName 						= \''.$_POST["FromName"].'\';
	$form_recipient 				= \''.$_POST["form_recipient"].'\';
	$Default_SMTP     				= \''.$_POST["Default_SMTP"].'\';
	$Default_SMTP_login 			= \''.$_POST["Default_SMTP_login"].'\';
	$Default_SMTP_pass 				= \''.$_POST["Default_SMTP_pass"].'\';
	$Default_SMTP_Email 			= \''.$_POST["Default_SMTP_Email"].'\';
	$Default_notifications_email 	= \''.$_POST["Default_notifications_email"].'\';
	// DADOS PARA CONFIGURAR BACKOFFICE/SITE
	$seo 							= '.intval($_POST["seo"]).';
	$usememorytables 				= '.intval($_POST["usememorytables"]).';
	$save_stats 					= '.intval($_POST["save_stats"]).';
	$subscribe_newsletter			= '.intval($_POST["subscribe_newsletter"]).';
	$module_voucher					= '.intval($_POST["module_voucher"]).';
	$nif_mandatory					= '.intval($_POST["nif_mandatory"]).';
	$lingua_admin 					= \''.$_POST["lingua_admin"].'\';
	$lingua_website 				= \''.$_POST["lingua_website"].'\';
	$n_imagens_familia 				= '.intval($_POST["n_imagens_familia"]).';
	$n_imagens_marca 				= '.intval($_POST["n_imagens_marca"]).';
	$tem_produtos 					= '.intval($_POST["tem_produtos"]).';
	$tem_marcas 					= '.intval($_POST["tem_marcas"]).';
	$tem_carrinho 					= '.intval($_POST["tem_carrinho"]).';
	$familias_destaques 			= '.intval($_POST["familias_destaques"]).';
	$registo_ativo_auto 			= '.intval($_POST["registo_ativo_auto"]).';
	$tem_carteira 					= '.intval($_POST["tem_carteira"]).';
	$tem_nickname 					= '.intval($_POST["tem_nickname"]).';
	$googleanalyticsmedia 			= '.intval($_POST["googleanalyticsmedia"]).';
	$media_substituicao_noticias	= '.intval($_POST["media_substituicao_noticias"]).';
	$facebook_id 					= \''.$_POST["facebook_id"].'\';
	$facebook_secret_id 			= \''.$_POST["facebook_secret_id"].'\';
	$twitter_consumer_id 			= \''.$_POST["twitter_consumer_id"].'\';
	$twitter_consumer_secret		= \''.$_POST["twitter_consumer_secret"].'\';
	$google_client_id 				= \''.$_POST["google_client_id"].'\';
	$google_client_secret			= \''.$_POST["google_client_secret"].'\';
	$margem_lucro_default 			= '.floatval($_POST["margem_lucro_default"]).';
	$iva_default 					= '.floatval($_POST["iva_default"]).';
	$valor_max_encomenda 			= '.floatval($_POST["valor_max_encomenda"]).';
	// CONFIGURAÇÕES ESPECIAIS
	$catalog_page_id   	= '.intval($_POST["catalog_page_id"]).';
	$products_page_id  	= '.intval($_POST["products_page_id"]).';
	$pesquisa_page_id  	= '.intval($_POST["pesquisa_page_id"]).';
	$registo_page_id   	= '.intval($_POST["registo_page_id"]).';
	$verify_page_id    	= '.intval($_POST["verify_page_id"]).'; 
	$login_page_id     	= '.intval($_POST["login_page_id"]).'; 
	$menu_topo_id 	   	= '.intval($_POST["menu_topo_id"]).';
	$cart_page_id      	= '.intval($_POST["cart_page_id"]).';
	$all_news_page_id  	= '.intval($_POST["all_news_page_id"]).';
	$rmas_page_id  	   	= '.intval($_POST["rmas_page_id"]).';
	$cart_purchage_id 	= '.intval($_POST["cart_purchage_id"]).';
	$newsletter_page_id = '.intval($_POST["newsletter_page_id"]).';
	$cartchange_page_id = '.intval($_POST["cartchange_page_id"]).';
	// TEMPLATES DE EMAIL
	$template_clientes 			= '.intval($_POST["template_clientes"]).';
	$template_verificacao 		= '.intval($_POST["template_verificacao"]).';
	$template_recomedacao_prod 	= '.intval($_POST["template_recomedacao_prod"]).';
	$template_forgotpass 		= '.intval($_POST["template_forgotpass"]).';
	$template_rma_estado 		= '.intval($_POST["template_rma_estado"]).';
	$template_rma_novouser 		= '.intval($_POST["template_rma_novouser"]).';
	$template_pedido_cotacao 	= '.intval($_POST["template_pedido_cotacao"]).';
	$template_pedido_recolha 	= '.intval($_POST["template_pedido_recolha"]).';
	$template_encomenda 		= '.intval($_POST["template_encomenda"]).';
	$template_sugestaosite 		= '.intval($_POST["template_sugestaosite"]).';
	$template_notificacaopontos = '.intval($_POST["template_notificacaopontos"]).';
	$template_notificacaovisita	= '.intval($_POST["template_notificacaovisita"]).';
	// CONFIGURAÇÃO DE TIPOS DE PUBLICIDADE';
    $cpublicidade = $_POST['publicidade_codigo'];
    $npublicidade = $_POST['publicidade_nome'];
    for($i=0;$i<count($cpublicidade);$i++){
	$str .='
	$publicidade["codigo"][] 	= \''.$cpublicidade[$i].'\';
	$publicidade["nome"][] 		= \''.$npublicidade[$i].'\';
	//$publicidade["url"][] 	= "Without Behavior";';
    }                
	$str .='
	// CONFIGURAÇÃO DE VIDEOS DO YOUTUBE
	$video_autoplay = '.$_POST["video_autoplay"].';
	$video_rel 		= '.$_POST["video_rel"].';
	// EDITOR
	$sBaseVirtual0 	= "'.$_POST["sBaseVirtual0"].'";
	$sBase0 		= "'.$_POST["sBase0"].'";
	// MOSTRA = 1 | ESCONDE = 0 ERROS
	if((isset($_GET["log_errors"]) && $_GET["log_errors"]==1)){
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
	}else{
		error_reporting(E_ALL);
		ini_set("display_errors", 0);
	}
	// DEFINICOES DE TIMEZONE
	date_default_timezone_set(\''.$_POST["timezone"].'\');
	// CARREGAR FICHEIROS MAIS ATUAIS
	define("dumpcache", date("H"));
	ini_set(\'session.cookie_httponly\',1);
	header(\'X-Frame-Options: SAMEORIGIN\');
	'.addslashes($_POST['novo_codigo']).'
	?>';
	file_put_contents('../files/settings2.php',$str);
	return true;
}
?>