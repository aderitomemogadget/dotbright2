<?php
	/*========================================================================*/
	/*                         ATENÇÃO! ATENÇÃO! ATENÇÃO!                     */
	/*========================================================================*/
	/* Estes settings são sobrepostos pelo ficheiro core/settings_modulos.php */
	/* através do menu Configurações->Definições da plataforma no Backoffice. */
	/*========================================================================*/

	if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

	/* Improve PHP configuration to prevent issues */
	ini_set('upload_max_filesize', '500M');
	ini_set('default_charset', 'utf-8');
	ini_set('magic_quotes_runtime', 0);
	ini_set('memory_limit', '1024M');

	$dev=4;

	$http='https://';

	$dev_folder='';
	if($dev == 4)
	{
		/* DOTBRIGHT_DEV2_BOOTSTRAP: isolated DEV3 installation. */
		error_reporting(E_ERROR);
		ini_set('display_errors', '0');
		ini_set('display_startup_errors', '0');

		$dotbright_dev2_config = require '/home/www/dev3.memogadget.com/private/dotbright2-config.php';
		$DB_name = $dotbright_dev2_config['db_name'];
		$DB_host = $dotbright_dev2_config['db_host'];
		$DB_user = $dotbright_dev2_config['db_user'];
		$DB_pass = $dotbright_dev2_config['db_pass'];

		$dev_folder = 'dotbright2/';
		$site_shell_url = dirname(__FILE__) . '/../';
		$site_http_url = 'https://dev3.memogadget.com/dotbright2/';
		$website_url_email = $site_http_url;
		define('IMG_FOLDER', 'files');
		define('DB_ENGINE', 'db_engine_pdo.php');
		$m_http_url_images = $site_http_url . IMG_FOLDER . '/';
		$site_http_ajax_url = $site_http_url . 'ajax/';
		$site_http_url_internal = $site_http_url;
	}
	elseif($dev == 0 )
	{
		error_reporting(E_ERROR);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(0);


		/*if (isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['HTTPS'])) {
	        if ($_SERVER["HTTPS"] != "on") {
	            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	            exit();
	        }
	    }*/
	    $http = 'https://';
	    $https = 'https://';
	    if (isset($_SERVER['HTTPS'])) {
	        if ($_SERVER["HTTPS"] == "on") {
	            $http = 'https://';
	            ini_set('session.cookie_secure', 1);
	        }
	    }
	    if (isset($_SERVER['HTTP_HOST']) && !substr_count($_SERVER['HTTP_HOST'], 'www')) {
	        header("Location: " . $http . "www." . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	        exit();
	    }



		$DB_name = "dotbright";
		$DB_host = "localhost";
		$DB_user = '';
		$DB_pass = '';

		// CAMINHOS ABSOLUTOS
		$dev_folder 			= '';
		$site_shell_url 		= dirname( __FILE__ ) . '/../';
		$site_http_url 			= 'https://www.dotbright.pt/'.$dev_folder;
		$website_url_email 		= $http.'www.dotbright.pt/'.$dev_folder;
		define('IMG_FOLDER','files');
		define('DB_ENGINE','db_engine_pdo.php');
		$m_http_url_images 		= $http.'www.dotbright.pt/'.$dev_folder.IMG_FOLDER.'/';
		$site_http_ajax_url 	= $http.'www.dotbright.pt/'.$dev_folder.'ajax/';
		$site_http_url_internal	= $site_http_url;
		
	}
	elseif($dev == 3 )
	{
		error_reporting(E_ERROR);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(0);

		$DB_name = "dotbright";
		$DB_host = "localhost";
		$DB_user = '';
		$DB_pass = '';

		// CAMINHOS ABSOLUTOS
		$dev_folder 			= '';
		$site_shell_url 		= dirname( __FILE__ ) . '/../';
		$site_http_url 			= 'https://pre.dotbright.pt/'.$dev_folder;
		$website_url_email 		= $http.'pre.dotbright.pt/'.$dev_folder;
		define('IMG_FOLDER','files');
		define('DB_ENGINE','db_engine_pdo.php');
		$m_http_url_images 		= $http.'pre.dotbright.pt/'.$dev_folder.IMG_FOLDER.'/';
		$site_http_ajax_url 	= $http.'pre.dotbright.pt/'.$dev_folder.'ajax/';
		$site_http_url_internal	= $site_http_url;
	}


	
	define('SMS_API_TOKEN', '');

	// DADOS BASE DADOS
	define('DB_HOST', $DB_host);
	define('DB_USER', $DB_user);
	define('DB_PASS', $DB_pass);
	define('DB_NAME', $DB_name);

	// CAMINHO SERVIDOR
	define('SITE_SHELL_URL', dirname( __FILE__ ) . '/../');

	// DADOS PARA WEBSITE / RSS
	$site_title = 'dotbright';

	// PASTA DO PROJETO
	define('PROJETO', 'dotbright');
	// JQUERY VERSION
	define('JQUERY', 'jquery_v1.11.1.js');
	// AUTOR
	define('AUTOR', 'memogadget');
	// EDITOR
	define('EDITOR', 'ckeditor_4_5_1');
	// CAMINHO PHP EXEC
	$exec_caminho = 'php';
	// PAIS SELECIONADO POR DEFEITO
	define('PAIS_DEFAULT', 165); //PORTUGAL

	// NUMERO DE CASA DECIMAIS PARA ARREDONDAMENTO DE PRECOS - MHR

	// CONFIGURAÇÃO DO MODULO CATEGORIAS DOS PRODUTOS
	// CONF IMG	(width, height, ativo, crop_white_space, make_transparent, crop)
	define('IMG_FAM',  serialize(array('w' => 100, 'h' => 100, 'ativo' => 1, 'c_white' => 1, 'm_trans' => 0, 'crop' => 0)));

	// CONFIGURAÇÃO DO MODULO PRODUTOS
	// CONF IMG	(width, height, ativo, crop_white_space, make_transparent, crop)
	define('IMG_SMALL', serialize(array('w' => 50, 	'h' => 50, 	'ativo' => 1, 'c_white' => 1, 'm_trans' => 1, 'crop' => 0))); // FICHA DO PRODUTO THUMBNAILS / CARRINHO COMPRAS
	define('IMG_PROD', 	serialize(array('w' => 500, 'h' => 500, 'ativo' => 1, 'c_white' => 1, 'm_trans' => 1, 'crop' => 0))); // FICHA DO PRODUTO
	define('IMG_THUM', 	serialize(array('w' => 250, 'h' => 200, 'ativo' => 1, 'c_white' => 1, 'm_trans' => 1, 'crop' => 0))); // DESTAQUES HOMEPAGE
	define('IMG_NEWS', 	serialize(array('w' => 90, 	'h' => 90, 	'ativo' => 1, 'c_white' => 1, 'm_trans' => 1, 'crop' => 0)));
	define('IMG_FB', 	serialize(array('w' => 200, 'h' => 500, 'ativo' => 0, 'c_white' => 1, 'm_trans' => 1, 'crop' => 0)));

	// MODULOS LOJA
	define('PRODCONF',  serialize(array('BONUS' => 0)));

	$rss_site_url 			= $site_http_url;
	$rss_site_description 	= "dotbright";
	$rss_email_editor 		= "";
	$rss_email_autor 		= "";
	$rss_email_webmaster 	= "";
	$site_copyright 		= "dotbright <span style=\"font-family:Arial\">&copy;</span> 2018";

	// CONFIGURAÇÃO DE EMAIL
	$FromName = 'dotbright - telecomunicações';
	$form_recipient 	= 'lojaonline@dotbright.pt';
	$Default_SMTP     	= 'mail.dotbright.pt';
	$Default_SMTP_login = '';
	$Default_SMTP_pass = '';
	$Default_SMTP_porta = 25;

	$Default_SMTP_Email = 'lojaonline@dotbright.pt';
	$Default_SMTP_Email_rma = 'assistencia@dotbright.pt';
	$Default_notifications_email 	= "lojaonline@dotbright.pt";
	// not rmas
	$Default_notifications_email_rma 	= "assistencia@dotbright.pt";

	// TAGS POLITICA DE PRIVACIDADE
	$TAG_EMPRESA = 'dotbright';
	$TAG_DESIGSOCIAL = 'dotbright';
	$TAG_COPYRIGHT = '2018 dotbright';

	// SEO CAMPOS
	$SEO_FB='https://pre.facebook.com/dotbright/';
	$SEO_PHONE='+351-229862544';
	$SEO_DESCRIPTION='dotbright|Encontre tudo o que necessita sem taxas adicionais nem complicações';

	// DADOS PARA CONFIGURAR BACKOFFICE/SITE
	$page_locked					= 0; // PÁGINA APENAS VISIVEL PARA ADMINISTRADORES
	$seo 							= 1; // PARA SEO
	$seo_novo						= 1; // PARA SEO URL NOVAS
	$save_stats        				= 1;
	$subscribe_newsletter 			= 1; // show option to subscribe newsletter in the register form - Sérgio
	$module_voucher					= 0;
	$nif_mandatory					= 0;
	$lingua_admin 					= 'pt'; // LINGUA PRE-DEFINIDA
	$lingua_website 				= 'pt'; // LINGUA PRE-DEFINIDA
	$site_lingua 				    = 'pt'; // LINGUA PRE-DEFINIDA
	$n_imagens_familia 				= 4;
	$n_imagens_marca   				= 2;
	$tem_produtos      				= 1;
	$tem_marcas      				= 1;
	$tem_carrinho      				= 1;
	$familias_destaques 			= 1;
	$registo_ativo_auto 			= 0;
	$tem_nickname 					= 0;
	$googleanalyticsmedia 			= 3;
	$media_substituicao_noticias	= 4;
	$tem_loja                       = 1;
	$tem_wishlist					= 0;
	$tem_area_reservada             = 1;
	$tem_promocoes 					= 1;
	$tem_produtos_visualizados		= 0;
	$tem_admin_alertas				= 0;

	$facebook_id 					= '';
	$facebook_secret_id 			= '';
	$twitter_consumer_id 			= '';
	$twitter_consumer_secret		= '';
	$google_client_id 				= '465105147677-4s0s8u23ddhjl4jft22fh8o963c68hd0.apps.googleusercontent.com';
	$google_client_secret			= 'ezr6kMcvkiVQZl2bQ3mZdBpI';
	$margem_lucro_default 			= 0;
	$iva_default 					= 23;
	$valor_max_encomenda			= 50000;
	$tem_modulo_enderecos			= 1;

	$theme_color='#000000';

	define('GOOGLE_CAPTCHA_SITE_KEY', '6LdTASIUAAAAAGMZE57puauuhCACJIq-0_XCMU4S');
	define('GOOGLE_CAPTCHA_SECRET_KEY', '6LdTASIUAAAAANH9MwvqS54tPrrFsHxFTKgx-oAl');

	// CONFIGURACOES MODULO PRODUTOS
	// FICHA DA FAMILIA
	define('FAM_CONF', serialize(array('tem_imagens' => 1, 'tem_caract' => 0, 'tem_p_c_final' => 0, 'tem_banner' => 0, 'tem_fams_destaque' => 0, 'tem_desconto' => 0, 'tem_margem' => 1, 'tem_portes' => 1, 'tem_n_alterna' => 1, 'tem_bonus' => 0)));
	// FICHA DO PRODUTO
	define('PROD_CONF', serialize(array('tem_estados' => 1, 'tem_etiquetas_energeticas' => 1, 'tem_personalizacao' => 0, 'tem_filtros' => 1,'tem_filtros_combo' => 1, 'tem_bonus' => 0, 'tem_sincro' => 1, 'tem_tabelas_precos' => 1, 'tem_carrinho' => 1, 'tem_caract' => 1, 'tem_tab_extra' => 1, 'tem_comousar' => 0, 'tem_acabamentos' => 0, 'tem_tempo_entrega' => 1, 'tem_encomenda_sem_stock' => 1,'tem_upload_fx' => 1,'tem_prod_grafica' => 1,'tem_dimensoes' => 1,'tem_servicos_associados' => 1,'tem_varios_tempos_entrega' => 1)));
	define('PROD_PERSONALIZACAO', serialize(array('0' => array('tipo' => '0', 'nome' => 'FRASE NO PRODUTO'), '1' => array('tipo' => '1', 'nome' => 'AJUDA'))));
	// CARRINHO DE COMPRAS
	define('CARRINHO_CONF', serialize(array('tem_loja_pertosi' => 0, 'tem_extensao_garantia' => 1, 'tem_multiportes' => 0, 'tem_enc_sem_stock' => 0, 'tem_servicos_extra' => 1, 'tem_custos_logisticos' => 1, 'tem_desconto_segundo_custos_logisticos' => 50, 'portes_premium_custos_logistos_gratis' => 1, 'tem_comparador' => 1)));

	$pais_id_default = 165;

	// CONFIGURAÇÕES ESPECIAIS
	$catalog_page_id   	= 2;
	$reparacoes_page_id = 148;
	$products_page_id  	= 3;
	$pesquisa_page_id  	= 4;
	$registo_page_id   	= 5;
	$minhaconta_page_id   	= 5;
	$verify_page_id    	= 145;
	$login_page_id     	= 160;
	$menu_topo_id 	   	= 6;
	$cart_page_id      	= 6;
	$all_news_page_id  	= 10;
	$news1_page_id  	= 11;
	$rmas_page_id  	   	= 25;
	$cart_purchage_id 	= 27;
	$cart_reparacoes_purchage_id = 101;
	$newsletter_page_id=31;
	$termos_page_id=37;
	$termosregisto_page_id=43;
	$cartchange_page_id=40;
	$programas_page_id=79;
	$wishlist_page_id=104;
	$recuperar_page_id=147;
	$videos_page_id=0;

	$promocao_page_id=96;
	$destaque_page_id=97;
	$outlet_page_id=98;
	$novidade_page_id=99;
	$dodia_page_id=100;

	$pagina_comparador=113;


	$tem_marketing=1;
	$tem_webmail=1;

	$tem_encomenda_fornecedor=1;
	//MHR:
	$usar_token_enc=0;//Em vez de usar o ID da encomenda usar um token

	//tabelas auxiliares produtos
	$produtos_componentes=1;
	$portfolio3=0;

	// TEMPLATES DE EMAIL
	$template_topo_email 		= 1;
	$template_bottom_email 		= 2;
	$template_clientes 			= 3;
	$template_verificacao 		= 4;
	$template_recomedacao_prod 	= 5;
	$template_forgotpass 		= 7;
	$template_pedido_cotacao 	= 5;
	$template_pedido_recolha 	= 6;
	$template_encomenda 		= 8;
	$template_sugestaosite		= 9;
	$template_notificacaopontos	= 10;
	$template_notificacaovisita	= 11;
	$template_notificacaonovidades	= 12;



	$tem_importadores = 1;
	$imp_XLS = 1;
	$imp_XLS_url = 'admin/importadores/xls/import.php';
	$imp_SERVISOFT = 1;

	//codigos dos supliers
	$Suppliers_nomes[1] = 'CSV';
	$Suppliers_nomes[3] = 'SERVISOFT';
	$Suppliers_nomes[4] = 'FD';
	$Suppliers_nomes[5] = 'WDMI';
	$Suppliers_nomes[6] = 'HURTEL';


	$i = 0;
	$lista_importadores[$i]['id'] = 1;
	$lista_importadores[$i]['name'] = 'CSV';
	$lista_importadores[$i]['url'] = 'importadores/config/import.php?Supplier_ID=1';
	$i++;
	$lista_importadores[$i]['id'] = 3;
	$lista_importadores[$i]['name'] = 'SERVISOFT';
	$lista_importadores[$i]['url'] = 'importadores/config/import.php?Supplier_ID=3';
	$i++;
	$lista_importadores[$i]['id'] = 4;
	$lista_importadores[$i]['name'] = 'FD';
	$lista_importadores[$i]['url'] = 'importadores/config/import.php?Supplier_ID=4';
	$i++;
	$lista_importadores[$i]['id'] = 5;
	$lista_importadores[$i]['name'] = 'WDMI';
	$lista_importadores[$i]['url'] = 'importadores/config/import.php?Supplier_ID=5';

	$i++;
	$lista_importadores[$i]['id'] = 6;
	$lista_importadores[$i]['name'] = 'HURTEL';
	$lista_importadores[$i]['url'] = 'importadores/config/import.php?Supplier_ID=6';








	$tem_log_kk=1;
	$tem_menu_top=1;

	$filtro_marcas_id = 3;
	// CONFIGURAÇÃO DE TIPOS DE PUBLICIDADE
	// CODIGO DO VIDEO				 	// TEXTO MODULO													// URL DO MODULO (PARA O FUTURO)
	$publicidade['codigo'][]='';		$publicidade['nome'][]='Without Behavior'; 					// $publicidade['url'][]='Without Behavior';
	$publicidade['codigo'][]='-1';	$publicidade['nome'][]='Script';              				// $publicidade['url'][]='Without Behavior';
	$publicidade['codigo'][]='1'; 	$publicidade['nome'][]='Slides';								$publicidade['url'][]='core/media/slides/';
	$publicidade['codigo'][]='2'; 	$publicidade['nome'][]='Google Analytics';					$publicidade['url'][]='';
	$publicidade['codigo'][]='3';		$publicidade['nome'][]='history line'; 					// $publicidade['url'][]='Without Behavior';

	// OPÇÔES DE MENUS
	$menus_conf['codigo'][]=''; 				$menus_conf['nome'][]='Nada Selecionado';
	if($tem_loja){$menus_conf['codigo'][]='familias'; 		$menus_conf['nome'][]='Categorias';}
	$menus_conf['codigo'][]='marcas'; 			$menus_conf['nome'][]='Marcas';
	$menus_conf['codigo'][]='login'; 			$menus_conf['nome'][]='Login Não Logado';
	$menus_conf['codigo'][]='logout'; 			$menus_conf['nome'][]='Logout';
	$menus_conf['codigo'][]='minha_conta'; 		$menus_conf['nome'][]='Minha Conta';
	$menus_conf['codigo'][]='favoritos'; 		$menus_conf['nome'][]='Favoritos';
	$menus_conf['codigo'][]='carrinho'; 		$menus_conf['nome'][]='Carrinho';
	$menus_conf['codigo'][]='linguas'; 			$menus_conf['nome'][]='Linguas/Bandeiras';
	$menus_conf['codigo'][]='menu_reservado'; 	$menus_conf['nome'][]='Menus Área Reservada';
	$menus_conf['codigo'][]='minas_encomendas'; $menus_conf['nome'][]='Minhas Encomendas';
	$menus_conf['codigo'][]='newsletter'; 		$menus_conf['nome'][]='Newsletter';
	$menus_conf['codigo'][]='pesquisa'; 		$menus_conf['nome'][]='Pesquisa';

	//$menu_portes='sa_portes';//mhr
	$menu_portes='portes';

	// TIPOS CAMPOS PERSONALIZAÇÃO PRODUTO
	$campos_tipo[0] ='Texto (255 Caracteres)';
	$campos_tipo[1] ='Textarea';
	$campos_tipo[2] ='Selectbox';
	$campos_tipo[3] ='Multipla Escolha';
	$campos_tipo[4] ='Escolha Única';
	$campos_tipo[5] ='Envio Ficheiro/Imagem';
	$campos_tipo[6] ='Campo Escolha Data';
	$campos_tipo[7] ='Campo Valor(Preço)';
	$campos_tipo[8] ='Campos De Escolha';
	$campos_tipo[9] ='Campo Texto com URL';
	$campos_tipo[10]='Campo Fotos com URL';


	define('MP_TP_PAYPAL', 1);
	define('MP_TP_TRANS_BANCARIA', 2);
	define('MP_TP_UNICRE', 3);
	define('MP_TP_MB_IFTHEN', 4);
	define('MP_TP_COBRANCA', 5);
	define('MP_TP_HIPAY', 6);
	define('MP_TP_MEMOPAY', 7);
	define('MP_TP_MB_EASYPAY', 8);
	define('MP_TP_MB_LUSOPAY', 9);
	define('MP_TP_CC_EASYPAY', 10);
	define('MP_TP_MB_SIBS', 11);
	define('MP_TP_MBWAY_SIBS', 12);
	define('MP_TP_MBWAY_LUSOPAY', 15);
	define('MP_TP_MBWAY_IFTHEN', 16);
	define('MP_TP_FLOAPAY', 18);
	define('MP_TP_EM_LOJA', 20);
	define('MP_TP_MBWAY_UNICRE', 21);
	define('MP_TP_MBWAY_EASYPAY', 22);
	define('MP_TP_SEQURA', 50);

	$pgm_metodos[MP_TP_MB_LUSOPAY]=array('nome' => 'LUSOPAY - REFERENCIA MULTIBANCO', 	'url' => 'external_api/pagamentos/pgm_mb_lusopay/');

	$pgm_metodos[MP_TP_PAYPAL]=array('nome' => 'PAYPAL', 						 		 	'url' => 'external_api/pagamentos/pgm_paypal/');
	$pgm_metodos[MP_TP_TRANS_BANCARIA]=array('nome' => 'TRANSFERENCIA BANCÁRIA', 		 	'url' => '');
	//$pgm_metodos[MP_TP_MB_IFTHEN]=array('nome' => 'REFERENCIA MULTIBANCO - IFTHEN',  		'url' => 'external_api/pagamentos/pgm_mb_ifthen/');
	$pgm_metodos[MP_TP_COBRANCA]=array('nome' => 'À COBRANÇA',  					 		'url' => '');
	//$pgm_metodos[MP_TP_HIPAY]=array('nome' => 'HIPAY',  						 			'url' => 'external_api/pagamentos/pgm_hipay/');
	//$pgm_metodos[MP_TP_MB_SIBS]=array('nome' => 'REFERENCIA MULTIBANCO - SIBS', 	'url' => 'external_api/pagamentos/pgm_sibs/');
	//$pgm_metodos[MP_TP_MBWAY_SIBS]=array('nome' => 'MBWAY - SIBS', 	'url' => 'external_api2/pagamentos/pgm_sibs/mbway/');
	$pgm_metodos[MP_TP_MBWAY_LUSOPAY] = array('nome' => 'LUSOPAY - MBWAY', 	'url' => 'external_api2/pagamentos/pgm_mb_lusopay/mbway/');
	$pgm_metodos[MP_TP_MBWAY_IFTHEN] = array('nome' => 'IFTHEN - MBWAY', 	'url' => 'external_api2/pagamentos/pgm_mb_ifthen/mbway/');
	$pgm_metodos[MP_TP_EM_LOJA]=array('nome' => 'PAGAMENTO NA LOJA',  					 		'url' => '');

	
	$pgm_metodos[MP_TP_FLOAPAY] = array('nome' => 'FLOA PAY', 'url' => 'external_api/pagamentos/pgm_floa/');
	$pgm_metodos[MP_TP_SEQURA] = array('nome' => 'seQura', 'url' => 'external_api/pagamentos/pgm_sequra/');
	//$pgm_metodos[MP_TP_MBWAY_UNICRE] = array('nome' => 'MBWAY - UNICRE', 'url' => 'external_api/pagamentos/pgm_unicre/mbway_spg/');//MBWAY
	//$pgm_metodos[MP_TP_UNICRE] = array('nome' => 'VISA SPG - UNICRE', 'url' => 'external_api/pagamentos/pgm_sibs_visa_spg/');//VISA

$config_floateste = [
        'Hmac'            => '336AC9E91CE394145B177CD14807D4F199A6AC74',
        'UserName'        => 'TestUser',
        'Password'        => 'TestUser1234*',
        'url_com'         => 'https://paymentgateway.integration-cb4x.fr',
        'ulr_com_eleg'    => 'https://eligibility.integration-cb4x.fr'
];

$config_floa = [
        'Hmac'            => 'FFF0C91609E2E53CF712915F9BB9EC626A9EE05D',
        'UserName'        => '4tfqDOTBRIGHTvU^cj-!~DRAFodSHHAN',
        'Password'        => 'M59TG@sKKy869IXoepz$FNTZZgf5$WKthKs3Dw^5f1$',
        'url_com'         => 'https://paymentgateway.cb4x.fr',
        'ulr_com_eleg'    => 'https://eligibility.cb4x.fr'
];


	$pgm_metodos[MP_TP_MBWAY_EASYPAY]=array(
		'nome'=>'EASYPAY - MBWAY',
		'url'=>'external_api/pagamentos/pgm_easypay/',
		'user'=>'2cedbb14-2387-4919-9eae-df5b16837839',
		'cin'=>'',
		'entity'=>'11683',
		'key'=>'5d9b448a-dfc3-4c23-815f-79c034b455b5',
		'code'=>'5b78ecb97a50f4f52c5141448f357d91'
	);
	$pgm_metodos[MP_TP_MB_EASYPAY]=array(
		'nome'=>'EASYPAY - REFERENCIA MULTIBANCO',
		'url'=>'external_api/pagamentos/pgm_easypay/',
		'user'=>'2cedbb14-2387-4919-9eae-df5b16837839',
		'cin'=>'',
		'entity'=>'11683',
		'key'=>'5d9b448a-dfc3-4c23-815f-79c034b455b5',
		'code'=>'5b78ecb97a50f4f52c5141448f357d91'
	);
	$pgm_metodos[MP_TP_CC_EASYPAY]=array(
		'nome'=>'EASYPAY - CARTÃO DE CREDITO',
		'url'=>'external_api/pagamentos/pgm_easypay/',
		'user'=>'2cedbb14-2387-4919-9eae-df5b16837839',
		'cin'=>'',
		'entity'=>'11683',
		'key'=>'5d9b448a-dfc3-4c23-815f-79c034b455b5',
		'code'=>'9b24e8f8462ba65019d8f07f3c17c544'
	);


	$export_grupos[0]=array('nome' => 'Aplicar Margens a produtos');
	$export_grupos[1]=array('nome' => 'Exportação de produtos para o marketplace-worten');
	$export_grupos[2]=array('nome' => 'Exportação de produtos para o marketplace-Dott');
	$export_grupos[3]=array('nome' => 'Exportação de produtos para o marketplace-Amazon');
	$export_grupos[4]=array('nome' => 'Exportação de produtos para o marketplace-Fnac');
	$export_grupos[5]=array('nome' => 'Exportação de produtos para o marketplace-KUANTOKUSTA');
	$export_grupos[6]=array('nome' => 'Exportação de produtos para o marketplace-FACEBOOK');

	// CONFIGURAÇÃO DE VIDEOS DO YOUTUBE
	$video_autoplay=0; // OS VIDEOS COMEÇAM LOGO QUANDO CARREGA A PÁGINA
	$video_rel=0; // MOSTRAR RELACIOANDOS NO FIM DO VIDEO

	// EDITOR
	$sBaseVirtual0 	= '/dev/'.$dev_folder.'files';
	$sBase0 		= $site_shell_url.'files';
	if($dev == 1)
	{
		$sBaseVirtual0 	= '/dev/'.$dev_folder.'files';
		$sBase0 		= $site_shell_url.'files';
	}
	else
	{
		$sBaseVirtual0='/'.$dev_folder.'files';
		$sBase0=$site_shell_url.'files';
	}

	// DEFINIR O ID DE CLIENTE NO SITE
	$id_revendedor=20747;

	// DEFINICOES DE TIMEZONE
	/* Please do not use LC_ALL here http://pre.php.net/manual/fr/function.setlocale.php#25041 */
	date_default_timezone_set("Europe/Lisbon");
	setlocale(LC_TIME, "Portuguese_Standard");
	setlocale(LC_ALL, "en_US.UTF-8;LC_NUMERIC=C");

	// CARREGAR FICHEIROS MAIS ATUAIS
	define('dumpcache','A6'); // DEVE SER ALTERADO ESTE VALOR SEMPRE QUE HAJA NOVAS VERSÕES DE CODIGO*/
	ini_set('session.cookie_httponly', 1);
	define('ORDENAR_POR','3');


	/* FAMILIAS INTERDITAS KUANTOKUSTA */
	$GLOBALS['kuantokusta_familias_interditas'] = array();

	/* Definição do tipo permitido de pesquisas */

	$pesq_type= array('produtos'=>1,'paginas'=>1);

	/* Tipo de Notícias */
	$tipo_noticias = array(
		1 => "Notícias Normal",
		2 => "Video"
	);

	/* tipo de bread cum */
	$short_breadcum=1;


	//GLOBAL Variables
	$id_menu_logins = 213;
	$menus_conf['codigo'][]='login_facebook'; 			$menus_conf['nome'][]='login via facebook';

	//lateral background
	//configurações on off
	$conf_on_off = array('lateralbackground'=>0, 'brand_filter'=>1,'show_price_range'=>1); // 0 uma imagem, 1 -> 2 imagenes laterales
	$content_width = '1920';

	$linguas_topo_mobile = 0;


	$cor_border='#5a0a00';
	$cor_alerta_border='#e83b44';

	$update_db=1;



	// CONFIGURAÇÃO RMAs-----------------------------------------------------------------------------------------------------------------------
	// ESTADOS RMAS
	$estados_rmas_cab[0] = 'ENTRADA';
	// $estados_rmas[0][] = array('id'=> 1, 'estado' => 'Entrada', 'envia_email' => 0);
	// $estados_rmas[0][] = array('id'=> 2, 'estado' => 'ONLINE - Entrada em Loja', 'envia_email' => 0);

	$estados_rmas_cab[1] = 'EM REPARAÇÃO';
	// $estados_rmas[1][] = array('id'=> 3, 'estado' => 'Equipamento Em Testes', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 4, 'estado' => 'Em reparação - Laboratório', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 5, 'estado' => 'Aguarda Código Desbloqueio', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 6, 'estado' => 'Espera Aceitação do Orçamento Pelo Cliente', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 7, 'estado' => 'Orçamento', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 8, 'estado' => 'Espera Peças para Reparação', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 9, 'estado' => 'Orçamento Aceite pelo Cliente', 'envia_email' => 0);

	$estados_rmas_cab[2] = 'EM ESPERA';
	// $estados_rmas[1][] = array('id'=> 3, 'estado' => 'Equipamento Em Testes', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 4, 'estado' => 'Em reparação - Laboratório', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 5, 'estado' => 'Aguarda Código Desbloqueio', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 6, 'estado' => 'Espera Aceitação do Orçamento Pelo Cliente', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 7, 'estado' => 'Orçamento', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 8, 'estado' => 'Espera Peças para Reparação', 'envia_email' => 0);
	// $estados_rmas[1][] = array('id'=> 9, 'estado' => 'Orçamento Aceite pelo Cliente', 'envia_email' => 0);

	$estados_rmas_cab[3] = 'ORÇAMENTOS';

	$estados_rmas_cab[4] = 'CONCLUÍDAS';
	// $estados_rmas[2][] = array('id'=> 10, 'estado' => 'Anomalia Descrita Não Detectada', 'envia_email' => 0);
	// $estados_rmas[2][] = array('id'=> 11, 'estado' => 'Orçamento Não Aceite Pelo Cliente', 'envia_email' => 0);
	// $estados_rmas[2][] = array('id'=> 12, 'estado' => 'Reparação Concluída', 'envia_email' => 0);
	// $estados_rmas[2][] = array('id'=> 13, 'estado' => 'Sem intervenção Técnica', 'envia_email' => 0);
	// $estados_rmas[2][] = array('id'=> 14, 'estado' => 'Sem reparação', 'envia_email' => 0);

	$estados_rmas_cab[5] = 'ENTREGUES AO CLIENTE';
	// $estados_rmas[3][] = array('id'=> 15, 'estado' => 'Entregue ao Cliente Com Reparação', 'envia_email' => 0);
	// $estados_rmas[3][] = array('id'=> 16, 'estado' => 'Entregue ao Cliente Sem Intervenção', 'envia_email' => 0);
	// $estados_rmas[3][] = array('id'=> 17, 'estado' => 'Entregue ao Cliente Sem Reparação', 'envia_email' => 0);
	// $estados_rmas[3][] = array('id'=> 18, 'estado' => 'Enviado por Correio', 'envia_email' => 0);

	// LOJAS RMAS
	$lojas_rmas[1] = 'Online';
	$lojas_rmas[2] = 'Loja de Matosinhos';
	$lojas_rmas[3] = 'Loja de Maia';

	$reparacoes_rmas[] = '';
	// FIM RMAs---------------------------------------------------------------------------------------------------------------------
	header('X-Frame-Options: SAMEORIGIN');

	/* DOTBRIGHT_DEV2_OUTBOUND_GUARD: never deliver production notifications. */
	if($dev == 4)
	{
		if(!defined('DOTBRIGHT_ENVIRONMENT')) define('DOTBRIGHT_ENVIRONMENT', 'development');
		if(!defined('DOTBRIGHT_OUTBOUND_DISABLED')) define('DOTBRIGHT_OUTBOUND_DISABLED', true);
		$Default_SMTP = '127.0.0.1';
		$Default_SMTP_login = '';
		$Default_SMTP_pass = '';
		$Default_SMTP_porta = 1;
		$Default_SMTP_Email = 'dev-null@invalid.example';
		$Default_SMTP_Email_rma = 'dev-null@invalid.example';
		$Default_notifications_email = 'dev-null@invalid.example';
		$Default_notifications_email_rma = 'dev-null@invalid.example';
		$save_stats = 0;
	}