<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

// if(file_exists('/home/www/domesticos.pt/public_html/rmas/files/settings2.php')) require_once('/home/www/domesticos.pt/public_html/rmas/files/settings2.php');
// else
// {
	$dev = 0; // EM DESENVOLVIMENTO
	// BASE DE DADOS
	if($dev == 1)
	{
		$DB_host = "localhost";
		$DB_user = "root";
		$DB_pass = "verbatim";
		$DB_name = "domesticos";
	}
	else
	{
		$DB_host = "localhost";
		$DB_user = "root";
		$DB_pass = "Ywwuol6XZk2mJ";
		$DB_name = "domesticos";
	}
	$http = 'http://';
	if(isset($_SERVER['HTTPS'])){ if ($_SERVER["HTTPS"] == "on"){$http = 'https://';}}
	// CAMINHOS ABSOLUTOS	
	$dev_folder = '';
	if($dev == 1)
	{
		$dev_folder 			= 'domesticos';
		$site_shell_url 		= '/var/www/dev/'.$dev_folder.'/';
		$site_http_url 			= $http.'192.168.1.202/dev/'.$dev_folder.'/';
		$site_https_url 		= 'https://192.168.1.202/dev/'.$dev_folder.'/';
		$website_url_email 		= $http.'192.168.1.202/dev/'.$dev_folder.'/';
		$m_http_url_images 		= $http.'192.168.1.202/dev/'.$dev_folder.'/files/';
		$site_http_ajax_url 	= $http.'192.168.1.202/dev/'.$dev_folder.'/ajax/';
		$site_http_url_internal	= $site_http_url;
	}
	else
	{
		$site_shell_url 		= '/home/www/domesticos.pt/public_html/';
		$site_http_url 			= $http.'www.domesticos.pt/';
		$site_https_url 		= 'https://www.domesticos.pt/';
		$website_url_email 		= $http.'www.domesticos.pt/';
		$m_http_url_images 		= $http.'www.domesticos.pt/files/';
		$site_http_ajax_url 	= $http.'www.domesticos.pt/ajax/';
		$site_http_url_internal	= $site_http_url;
	}
	// DADOS PARA WEBSITE / RSS
	$site_title 			= "Domésticos.pt - Loja Online";
	$rss_site_url 			= $site_http_url;
	$rss_site_description 	=  "Domésticos.pt - Loja Online";
	$rss_email_editor 		= "";
	$rss_email_autor 		= "";
	$rss_email_webmaster 	= "";
	$site_copyright 		= "Domésticos.pt <span style=\"font-family:Arial\">&copy;</span> 2014";
	// CONFIGURAÇÃO DE EMAIL
	$FromName = 'Domésticos.pt';
	$form_recipient 				= 'joao@memogadget.com';
	$Default_SMTP     				= "mail.freeplace.pt";
	$Default_SMTP_login 			= "suporte@freeplace.pt";
	$Default_SMTP_pass 				= "verbatim2020";
	$Default_SMTP_Email 			= "suporte@freeplace.pt";
	$Default_notifications_email 	= "joao@memogadget.com";
	// DADOS PARA CONFIGURAR BACKOFFICE/SITE
	$seo 							= 1; // PARA SEO
	$usememorytables 				= 0; // PARA MEMORY TABLES
	$save_stats        				= 1;
	$subscribe_newsletter 			= 1; // show option to subscribe newsletter in the register form - Sérgio 
	$module_voucher					= 1;
	$nif_mandatory					= 0;
	$lingua_admin 					= 'pt'; // LINGUA PRE-DEFINIDA
	$lingua_website 				= 'pt'; // LINGUA PRE-DEFINIDA
	$n_imagens_familia 				= 4;
	$n_imagens_marca   				= 2;
	$tem_produtos      				= 1;
	$tem_marcas      				= 1;
	$tem_carrinho      				= 1;
	$familias_destaques 			= 1;
	$registo_ativo_auto 			= 0;
	$tem_carteira 					= 0;
	$tem_nickname 					= 0;
	$googleanalyticsmedia 			= 3;
	$media_substituicao_noticias	= 4;
	$facebook_id 					= '1447618082145892';
	$facebook_secret_id 			= 'da11f7bdfb506a703aa1c51afb404ff4';
	$twitter_consumer_id 			= 'pHNVqf2JBuKaxcVA4Zfchy4Hx';
	$twitter_consumer_secret		= 'jlMY5Yz7NQF0xRrjY7MS3S2xvpS2Tj8jnepcrPyA9OUczmGrc3';
	$google_client_id 				= '1011513424928-4a58kl5t7ol0qqe99ldaikg0podgghe3.apps.googleusercontent.com';
	$google_client_secret			= '76jNZXI2WeRrPOT2snbC1j6R';
	$margem_lucro_default 			= 0;
	$iva_default 					= 23;
	$valor_max_encomenda			= 50000;
	// CONFIGURAÇÕES ESPECIAIS
	$catalog_page_id   	= 2;
	$products_page_id  	= 3;
	$pesquisa_page_id  	= 4;
	$registo_page_id   	= 5;
	$verify_page_id    	= 159;
	$login_page_id     	= 160;
	$menu_topo_id 	   	= 6;
	$cart_page_id      	= 6;
	$all_news_page_id  	= 10;
	$rmas_page_id  	   	= 25;
	$cart_purchage_id 	= 27;
	$newsletter_page_id = 31;
	$cartchange_page_id = 40;
	// TEMPLATES DE EMAIL
	$template_clientes 			= 3;
	$template_verificacao 		= 4;
	$template_recomedacao_prod 	= 5;
	$template_forgotpass 		= 7;
	$template_rma_estado 		= 1;
	$template_rma_novouser 		= 2;
	$template_pedido_cotacao 	= 5;
	$template_pedido_recolha 	= 6;
	$template_encomenda 		= 8;
	$template_sugestaosite		= 9;
	$template_notificacaopontos	= 10;
	$template_notificacaovisita	= 11;
	$template_notificacaonovidades	= 12;
	// CONFIGURAÇÃO DE TIPOS DE PUBLICIDADE
	// CODIGO DO VIDEO				 	// TEXTO MODULO													// URL DO MODULO (PARA O FUTURO)
	$publicidade['codigo'][] = '';		$publicidade['nome'][] = 'Without Behavior'; 					// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '-1';	$publicidade['nome'][] = 'Script';              				// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '1';  	$publicidade['nome'][] = 'Random opening page'; 				// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '3'; 	$publicidade['nome'][] = 'Slide';								// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '13'; 	$publicidade['nome'][] = 'Slide pro';							// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '14'; 	$publicidade['nome'][] = 'Portfolio with zoom';					// $publicidade['url'][] = 'Without Behavior'; 
	$publicidade['codigo'][] = '15'; 	$publicidade['nome'][] = 'Portfolio without zoom';				// $publicidade['url'][] = 'Without Behavior'; 
	// CONFIGURAÇÃO DE VIDEOS DO YOUTUBE
	if($dev == 1)
	{
		$video_autoplay = 0; // Os videos começam logo quando carrega a página
		$video_rel 		= 0; // Mostrar relacioandos no fim do video
	}
	else
	{
		$video_autoplay = 1; // Os videos começam logo quando carrega a página
		$video_rel 		= 0; // Mostrar relacioandos no fim do video
	}
	// EDITOR
	if($dev == 1)
	{
		$sBaseVirtual0 	= '/dev/'.$dev_folder.'/files';
		$sBase0 		= $site_shell_url.'files';
	}
	else
	{
		$sBaseVirtual0 	= '/files';
		$sBase0 		= $site_shell_url.'files';
	}
	// MOSTRA = 1 | ESCONDE = 0 ERROS
	if(isset($_GET['log_errors']) && $_GET['log_errors']==1){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}else{
		error_reporting(E_ALL);
		ini_set('display_errors', 0);
	}
	// CARREGAR FICHEIROS MAIS ATUAIS
	if($dev == 1) define("dumpcache", date('H'));
	else  define("dumpcache", date('His'));
	// DEFINICOES DE TIMEZONE
	date_default_timezone_set('Europe/Lisbon');

	ini_set( 'session.cookie_httponly', 1 );
	header('X-Frame-Options: SAMEORIGIN');
// }
?>