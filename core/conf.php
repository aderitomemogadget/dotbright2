<?php

/* ======================================================================== */
/*                         ATENÇÃO! ATENÇÃO! ATENÇÃO!                     */
/* ======================================================================== */
/* Estes settings são sobrepostos pelo ficheiro core/settings_modulos.php */
/* através do menu Configurações->Definições da plataforma no Backoffice. */
/* ======================================================================== */


if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)exit();
$dev = 1;
$http = 'http://';

$dev_folder = '';
if ($dev == 1)
{
    $DB_host="localhost";
    $DB_user='root';
    $DB_pass='Ywwuol6XZk2mJ';
    $DB_name="precosbaixos";

    // CAMINHOS ABSOLUTOS
    $dev_folder       = '';
    $site_shell_url     = dirname( __FILE__ ) . '/../';
    $site_http_url      = 'http://dev.memogadget.com/precosbaixos/'.$dev_folder;
    $website_url_email    = $http.'dev.memogadget.com/precosbaixos/'.$dev_folder;
    define('IMG_FOLDER','files');
    define('DB_ENGINE','db_engine.php');
    $m_http_url_images    = $http.'dev.memogadget.com/precosbaixos/'.$dev_folder.IMG_FOLDER.'/';
    $site_http_ajax_url   = $http.'dev.memogadget.com/precosbaixos/'.$dev_folder.'ajax/';
    $site_http_url_internal = $site_http_url;

    $MB_WAY_KEY = '';
    $CHAVE_ANTI_PHISHING = '';

    define('SITE_HTTP_URL', $site_http_url);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);
} 
else
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(0);

    if($_GET["log"]==1)error_reporting(1);


    if (isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['HTTPS'])) {
        if ($_SERVER["HTTPS"] != "on") {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
            exit();
        }
    }
    $http = 'https://';
    $https = 'https://';
    /*if (isset($_SERVER['HTTPS'])) {
        if ($_SERVER["HTTPS"] == "on") {
            $http = 'https://';
            ini_set('session.cookie_secure', 1);
        }
    }
    if (isset($_SERVER['HTTP_HOST']) && !substr_count($_SERVER['HTTP_HOST'], 'www')) {
        header("Location: " . $http . "www." . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }*/

    $DB_host = "localhost";
    $DB_user = "root";
    $DB_pass = "Ywwuol6XZk2mJ";
    $DB_name = "precosbaixos";

    define('DB_ENGINE', 'db_engine.php');

    // CAMINHOS ABSOLUTOS
    $dev_folder = '';
    $site_shell_url = dirname(__FILE__) . '/../';
    $site_http_url = $http . 'www.precosbaixos/';
    // $site_https_url 		= 'https://www.memogadgetcms/'.$dev_folder;
    $website_url_email = $http . 'www.precosbaixos/' . $dev_folder;
    $m_http_url_images = $http . 'www.precosbaixos/' . $dev_folder . 'files/';
    $site_http_ajax_url = $http . 'www.precosbaixos/' . $dev_folder . 'ajax/';
    $site_http_url_internal = $site_http_url;

    define('SITE_HTTP_URL', 'https://www.precosbaixos/');

}

// DADOS BASE DADOS
define('DB_HOST', $DB_host);
define('DB_USER', $DB_user);
define('DB_PASS', $DB_pass);
define('DB_NAME', $DB_name);

define('PHP', 'php');

define('IMG_FOLDER', 'files');

// CAMINHO SERVIDOR
define('SITE_SHELL_URL', dirname(__FILE__) . '/../');


// DADOS PARA WEBSITE / RSS
$site_title = 'precosbaixos';

// PASTA DO PROJETO
define('PROJETO', 'precosbaixos');

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



// CONFIGURAÇÃO DO MODULO CATEGORIAS DOS PRODUTOS
// CONF IMG	(width, height, ativo, crop_white_space, make_transparent, crop)
define('IMG_FAM', serialize(array('w' => 100, 'h' => 100, 'ativo' => 1, 'c_white' => 1, 'm_trans' => 0, 'crop' => 0)));

// CONFIGURAÇÃO DO MODULO PRODUTOS
// CONF IMG	(width, height, ativo, crop_white_space, make_transparent, crop)
define('IMG_SMALL', serialize(array('w' => 50, 'h' => 50, 'ativo' => 1, 'c_white' => 0, 'm_trans' => 1, 'crop' => 0))); // FICHA DO PRODUTO THUMBNAILS / CARRINHO COMPRAS
define('IMG_PROD', serialize(array('w' => 500, 'h' => 500, 'ativo' => 1, 'c_white' => 0, 'm_trans' => 1, 'crop' => 0))); // FICHA DO PRODUTO
define('IMG_THUM', serialize(array('w' => 250, 'h' => 200, 'ativo' => 1, 'c_white' => 0, 'm_trans' => 1, 'crop' => 0))); // DESTAQUES HOMEPAGE
define('IMG_NEWS', serialize(array('w' => 90, 'h' => 90, 'ativo' => 1, 'c_white' => 0, 'm_trans' => 1, 'crop' => 0)));
define('IMG_FB', serialize(array('w' => 200, 'h' => 500, 'ativo' => 0, 'c_white' => 0, 'm_trans' => 1, 'crop' => 0)));

// MODULOS LOJA
define('PRODCONF', serialize(array('BONUS' => 0)));

$rss_site_url = $site_http_url;
$rss_site_description = "precosbaixos";
$rss_email_editor = "";
$rss_email_autor = "";
$rss_email_webmaster = "";
$site_copyright = "precosbaixos <span style=\"font-family:Arial\">&copy;</span> 2018";

// CONFIGURAÇÃO DE EMAIL
$FromName = 'precosbaixos';
$form_recipient = 'geral@precosbaixos.pt';


$Default_SMTP = "mail.on4tech.pt"; // SMTP servers
$Default_SMTP_login = "geral@on4tech.pt";  // SMTP username
$Default_SMTP_pass = 'T^Zg=XmRR7G['; // SMTP password
$Default_SMTP_Email = "geral@on4tech.pt";
$Default_SMTP_Porta = 587;


define('SMTPSecure','tls');
define('SMTP_PORTA',$Default_SMTP_Porta);


$Default_notifications_email = "geral@precosbaixos.pt";
$Default_reports_email = "geral@precosbaixos.pt";
// TAGS POLITICA DE PRIVACIDADE
$TAG_EMPRESA = 'precosbaixos';
$TAG_DESIGSOCIAL = 'precosbaixos';
$TAG_COPYRIGHT = '2018 precosbaixos';

// SEO CAMPOS
$SEO_FB = 'https://www.facebook.com/precosbaixos/';
$SEO_PHONE = '+351-229862544';
$SEO_DESCRIPTION = 'precosbaixos|Encontre tudo o que necessita sem taxas adicionais nem complicações';

// DADOS PARA CONFIGURAR BACKOFFICE/SITE
$page_locked = 0; // PÁGINA APENAS VISIVEL PARA ADMINISTRADORES
$seo = 1; // PARA SEO
$seo_novo = 1; // PARA SEO URL NOVAS
$save_stats = 1;
$subscribe_newsletter = 1; // show option to subscribe newsletter in the register form - Sérgio
$module_voucher = 0;
$nif_mandatory = 0;
$lingua_admin = 'pt'; // LINGUA PRE-DEFINIDA
$lingua_website = 'pt'; // LINGUA PRE-DEFINIDA
$site_lingua = 'pt'; // LINGUA PRE-DEFINIDA
$n_imagens_familia = 4;
$n_imagens_marca = 2;
$tem_produtos = 1;
$tem_marcas = 1;
$tem_carrinho = 1;
$familias_destaques = 1;
$registo_ativo_auto = 0;
$tem_carteira = 0;
$tem_nickname = 0;
$googleanalyticsmedia = 3;
$media_substituicao_noticias = 4;
$tem_loja = 1;
$tem_wishlist = 1;
$tem_area_reservada = 1;
$tem_promocoes = 0;
$noticias_por_catalogo = 1;
$headers_adicionais = 1;
$envia_emails_enc_admin=1;

$use_elastic_search=0;
$ignorar_margens=1;

$google_client_id = '465105147677-4s0s8u23ddhjl4jft22fh8o963c68hd0.apps.googleusercontent.com';
$google_client_secret = 'ezr6kMcvkiVQZl2bQ3mZdBpI';
$margem_lucro_default = 0;
$iva_default = 23;
$valor_max_encomenda = 50000;
$tem_modulo_enderecos = 1;

$theme_color = '#000000';

define('GOOGLE_CAPTCHA_SITE_KEY', '6LdTASIUAAAAAGMZE57puauuhCACJIq-0_XCMU4S');
define('GOOGLE_CAPTCHA_SECRET_KEY', '6LdTASIUAAAAANH9MwvqS54tPrrFsHxFTKgx-oAl');


// CONFIGURACOES MODULO PRODUTOS
    define('FAM_CONF', serialize(array(
        'tem_imagens' => 1,
        'tem_caract' => 0,
        'tem_p_c_final' => 0,
        'tem_banner' => 0,
        'tem_fams_destaque' => 0,
        'tem_desconto' => 0,
        'tem_margem' => 1,
        'tem_portes' => 1,
        'tem_n_alterna' => 1,
        'tem_bonus' => 0)));
    // FICHA DO PRODUTO
    define('PROD_CONF', serialize(array('tem_estados' => 1,
        'tem_etiquetas_energeticas' => 0,
        'tem_personalizacao' => 0,
        'tem_filtros' => 1,
        'tem_filtros_combo' => 1,
        'tem_bonus' => 0,
        'tem_sincro' => 0,
        'tem_tabelas_precos' => 1,
        'tem_carrinho' => 1,
        'tem_caract' => 1,
        'tem_tab_extra' => 0,
        'tem_comousar' => 0,
        'tem_acabamentos' => 0,
        'tem_tempo_entrega' => 0,
        'tem_encomenda_sem_stock' => 1,
        'tem_upload_fx' => 0,
        'tem_prod_grafica' => 0,
        'tem_dimensoes' => 0,
        'tem_servicos_associados' => 0,
        'tem_varios_tempos_entrega' => 0,
        'tem_relacionados' => 1)));
    define('PROD_PERSONALIZACAO', serialize(array('0' => array('tipo' => '0', 'nome' => 'FRASE NO PRODUTO'), '1' => array('tipo' => '1', 'nome' => 'AJUDA'))));
    
    // CARRINHO DE COMPRAS    
    define('CARRINHO_CONF', serialize(array('tem_loja_pertosi' => 0,
        'tem_extensao_garantia' => 1,
        'tem_multiportes' => 1,
        'multi_portes_prod' => 1,
        'tem_enc_sem_stock' => 1,
        'tem_servicos_extra' => 0,
        'tem_custos_logisticos' => 0,
        'tem_desconto_segundo_custos_logisticos' => 0,
        'portes_premium_custos_logistos_gratis' => 0,
        'tem_comparador' => 0)));


$pais_id_default = 165;

// CONFIGURAÇÕES ESPECIAIS
$catalog_page_id = 2;
$products_page_id = 3;
$pesquisa_page_id = 4;
$registo_page_id = 5;
$minhaconta_page_id = 5;
$verify_page_id = 71;
$login_page_id = 160;
$menu_topo_id = 6;
$cart_page_id = 27;
$all_news_page_id = 10;
$news1_page_id = 11;
$rmas_page_id = 25;
$cart_purchage_id = 27;
$cart_purchage_final_id = 27;
$newsletter_page_id = 31;
$termos_page_id = 37;
$termosregisto_page_id = 43;
$cartchange_page_id = 40;
$programas_page_id = 79;
$wishlist_page_id = 104;
$recuperar_page_id = 105;
$videos_page_id = 0;

$promocao_page_id = 96;
$destaque_page_id = 97;
$outlet_page_id = 98;
$novidade_page_id = 99;
$dodia_page_id = 100;

$produtos_com_stock_nunca_limitar=1;

//importadores
$tem_importadores = 0;


// TEMPLATES DE EMAIL
$template_topo_email = 1;
$template_bottom_email = 2;
$template_clientes = 3;
$template_verificacao = 4;
$template_recomedacao_prod = 5;
$template_forgotpass = 7;
$template_pedido_cotacao = 5;
$template_pedido_recolha = 6;
$template_encomenda = 8;
$template_sugestaosite = 9;
$template_notificacaopontos = 10;
$template_notificacaovisita = 11;
$template_notificacaonovidades = 12;

$tem_log_google = 1;

$stock_minimo_nulo=0;

$tem_menu_top = 1;

$filtro_marcas_id = 3;
// CONFIGURAÇÃO DE TIPOS DE PUBLICIDADE
// CODIGO DO VIDEO				 	// TEXTO MODULO													// URL DO MODULO (PARA O FUTURO)
$publicidade['codigo'][] = '';
$publicidade['nome'][] = 'Without Behavior';
$publicidade['codigo'][] = '-1';
$publicidade['nome'][] = 'Script';
$publicidade['codigo'][] = '1';
$publicidade['nome'][] = 'Slides';
$publicidade['url'][] = 'core/media/slides/';
$publicidade['codigo'][] = '2';
$publicidade['nome'][] = 'Google Analytics';
$publicidade['url'][] = '';
$publicidade['codigo'][] = '3';
$publicidade['nome'][] = 'history line';
$publicidade['codigo'][]='26';
$publicidade['nome'][]='EBOOK'; 
// OPÇÔES DE MENUS
$menus_conf['codigo'][] = '';
$menus_conf['nome'][] = 'Nada Selecionado';
if ($tem_loja)
{
    $menus_conf['codigo'][] = 'familias';
    $menus_conf['nome'][] = 'Categorias';
}
$menus_conf['codigo'][] = 'marcas';
$menus_conf['nome'][] = 'Marcas';
$menus_conf['codigo'][] = 'login';
$menus_conf['nome'][] = 'Login Não Logado';
$menus_conf['codigo'][] = 'logout';
$menus_conf['nome'][] = 'Logout';
$menus_conf['codigo'][] = 'minha_conta';
$menus_conf['nome'][] = 'Minha Conta';
$menus_conf['codigo'][] = 'favoritos';
$menus_conf['nome'][] = 'Favoritos';
$menus_conf['codigo'][] = 'carrinho';
$menus_conf['nome'][] = 'Carrinho';
$menus_conf['codigo'][] = 'linguas';
$menus_conf['nome'][] = 'Linguas/Bandeiras';
$menus_conf['codigo'][] = 'menu_reservado';
$menus_conf['nome'][] = 'Menus Área Reservada';
$menus_conf['codigo'][] = 'minas_encomendas';
$menus_conf['nome'][] = 'Minhas Encomendas';
$menus_conf['codigo'][] = 'newsletter';
$menus_conf['nome'][] = 'Newsletter';
$menus_conf['codigo'][] = 'pesquisa';
$menus_conf['nome'][] = 'Pesquisa';

$menu_portes = 'sa_portes';

// TIPOS CAMPOS PERSONALIZAÇÃO PRODUTO
$campos_tipo[0] = 'Texto (255 Caracteres)';
$campos_tipo[1] = 'Textarea';
$campos_tipo[2] = 'Selectbox';
$campos_tipo[3] = 'Multipla Escolha';
$campos_tipo[4] = 'Escolha Única';
$campos_tipo[5] = 'Envio Ficheiro/Imagem';
$campos_tipo[6] = 'Campo Escolha Data';
$campos_tipo[7] = 'Campo Valor(Preço)';
$campos_tipo[8] = 'Campos De Escolha';
$campos_tipo[9] = 'Campo Texto com URL';
$campos_tipo[10] = 'Campo Fotos com URL';


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
define('MP_TP_MBWAY_UNICRE', 16);




$pgm_metodos[MP_TP_PAYPAL] = array('nome' => 'PAYPAL', 'url' => 'external_api/pagamentos/pgm_paypal/');
$pgm_metodos[MP_TP_TRANS_BANCARIA] = array('nome' => 'TRANSFERENCIA BANCÁRIA', 'url' => '');
$pgm_metodos[MP_TP_UNICRE] = array('nome' => 'UNICRE', 'url' => 'external_api/pagamentos/pgm_unicre/');
$pgm_metodos[MP_TP_MB_IFTHEN] = array('nome' => 'REFERENCIA MULTIBANCO - IFTHEN', 'url' => 'external_api/pagamentos/pgm_mb_ifthen/');
$pgm_metodos[MP_TP_COBRANCA] = array('nome' => 'À COBRANÇA', 'url' => '');
$pgm_metodos[MP_TP_HIPAY] = array('nome' => 'HIPAY', 'url' => 'external_api/pagamentos/pgm_hipay/');
// $pgm_metodos[MP_TP_MEMOPAY]=array('nome' => 'MEMOPAY', 				 		 		'url' => 'external_api/pagamentos/pgm_mb_memopay/');
$pgm_metodos[MP_TP_MB_EASYPAY] = array('nome' => 'REFERENCIA MULTIBANCO - EASYPAY', 'url' => 'external_api/pagamentos/pgm_mb_easypay/');
$pgm_metodos[MP_TP_CC_EASYPAY] = array('nome' => 'CARTÃO DE CREDITO - EASYPAY', 'url' => 'external_api/pagamentos/pgm_easypay_visa/');
$pgm_metodos[MP_TP_MB_LUSOPAY] = array('nome' => 'REFERENCIA MULTIBANCO - LUSOPAY', 'url' => 'external_api/pagamentos/pgm_mb_lusopay/');

$pgm_metodos[MP_TP_MB_SIBS] = array('nome' => 'REFERENCIA MULTIBANCO - SIBS', 'url' => 'external_api/pagamentos/pgm_sibs/');
$pgm_metodos[MP_TP_MBWAY_SIBS] = array('nome' => 'MBWAY - SIBS', 'url' => 'external_api/pagamentos/pgm_sibs/mbway/');
$pgm_metodos[MP_TP_MBWAY_LUSOPAY] = array('nome' => 'MBWAY - LUSOPAY', 'url' => 'external_api/pagamentos/pgm_mb_lusopay/mbway/');
$pgm_metodos[MP_TP_MBWAY_UNICRE] = array('nome' => 'MBWAY - UNICRE', 'url' => 'external_api/pagamentos/pgm_unicre/mbway/');


$export_grupos[0] = array('nome' => 'Aplicar Margens a produtos');
$export_grupos[1] = array('nome' => 'Exportação de produtos para o marketplace-worten');
$export_grupos[2] = array('nome' => 'Exportação de produtos para o marketplace-Dott');
$export_grupos[3] = array('nome' => 'Exportação de produtos para o marketplace-Amazon');
$export_grupos[4] = array('nome' => 'Exportação de produtos para o marketplace-Fnac');
$export_grupos[5] = array('nome' => 'Exportação de produtos para o marketplace-KUANTOKUSTA');
$export_grupos[6] = array('nome' => 'Exportação de produtos para o marketplace-FACEBOOK');

// CONFIGURAÇÃO DE VIDEOS DO YOUTUBE
$video_autoplay = 0; // OS VIDEOS COMEÇAM LOGO QUANDO CARREGA A PÁGINA
$video_rel = 0; // MOSTRAR RELACIOANDOS NO FIM DO VIDEO
// EDITOR
$sBaseVirtual0 = '/dev/' . $dev_folder . 'files';
$sBase0 = $site_shell_url . 'files';
if ($dev == 1) {
    $sBaseVirtual0 = '/dev/' . $dev_folder . 'files';
    $sBase0 = $site_shell_url . 'files';
} else {
    $sBaseVirtual0 = '/' . $dev_folder . 'files';
    $sBase0 = $site_shell_url . 'files';
}

// DEFINIR O ID DE CLIENTE NO SITE
$id_revendedor = 24817;

// DEFINICOES DE TIMEZONE
date_default_timezone_set("Europe/Lisbon");
setlocale(LC_TIME, "Portuguese_Standard");
setlocale(LC_ALL, "en_US.UTF-8;LC_NUMERIC=C");

// CARREGAR FICHEIROS MAIS ATUAIS
define("dumpcache", date('YmdHi'));



//ini_set('session.cookie_httponly', 1);

/* FAMILIAS INTERDITAS KUANTOKUSTA */
$GLOBALS['kuantokusta_familias_interditas'] = array();

/* Definição do tipo permitido de pesquisas */

$pesq_type = array('produtos' => 1, 'paginas' => 1);

/* Tipo de Notícias */
$tipo_noticias = array(
    1 => "Notícias Normal",
    2 => "Video"
);

/* tipo de bread cum */
$short_breadcum = 1;


//GLOBAL Variables
$id_menu_logins = 213;
$menus_conf['codigo'][] = 'login_facebook';
$menus_conf['nome'][] = 'login via facebook';

//lateral background
//configurações on off
$conf_on_off = array('lateralbackground'=>0, 'brand_filter'=>1, 'show_price_range'=>0); // 0 uma imagem, 1 -> 2 imagenes laterales
$content_width = '1920';

$linguas_topo_mobile = 0;

$update_db = 1;

$cor_border = '#5a0a00';
$cor_alerta_border = '#e83b44';
