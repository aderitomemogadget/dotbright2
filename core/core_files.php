<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
global $path;

if(isset($_GET["id"]))$_GET["id"]=intval($_GET["id"]);
if(isset($_GET["page_id"]))$_GET["page_id"]=intval($_GET["page_id"]);
if(isset($_GET["site_lingua"]))$_GET["site_lingua"]=substr($_GET["site_lingua"],0,3);
if(isset($_GET["amp"]))$_GET["amp"]=intval($_GET["amp"]);
if(isset($_GET["prod_id"]))$_GET["prod_id"]=intval($_GET["prod_id"]);
$_REQUEST ='';
unset($_REQUEST);

require_once $path.'settings.php';
require_once $path.'seguranca.php';
require_once $path.DB_ENGINE;
require_once $path.'settings_modulos.php';
require_once $path.'sessions_engine.php';
require_once $path.'global.php';
require_once $path.'admin_engine.php';

// CLIENTE SPECIAL
if(stream_resolve_include_path($GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/checkout.php'))require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/checkout.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/area_cliente.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/bloco.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/shop.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/produto.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/catalogo.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/menu.php';
require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/paginas.php';

if(stream_resolve_include_path($GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/oportunidades_carreira.php'))require_once $GLOBALS['site_shell_url'].'layout/'.PROJETO.'/code/oportunidades_carreira.php';
if(stream_resolve_include_path($GLOBALS['site_shell_url'].'core/pontos_mapa.php'))require_once $GLOBALS['site_shell_url'].'core/pontos_mapa.php';


// ESTE INCLUDE ESTÁ SER USADO PARA QUANDO UM USER FAZ UMA ENCOMENDA SABERMOS EM QUE DISPOSITIVO FOI FEITO
require_once $path.'Mobile_Detect.php';
$detect = new Mobile_Detect;
define('Dispositivo', ($detect->isMobile() ? 'mobile' : 'computer'));

// MODULOS de LOJA
if(defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();
if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)require_once $path.'garantia.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'servicos_extra.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'portes.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'portes_extra.php';


require_once $path.'media.php';
require_once $path.'menus.php';
require_once $path.'seo.php';
require_once $path.'linguas.php';
require_once $path.'gd.php';
require_once $path.'cookies.php';
require_once $path.'email.php';
require_once $path.'forms.php';
require_once $path.'componentes.php';
require_once $path.'rma.php';
require_once $path.'stats.php';
require_once $path.'search.php';
require_once $path.'special.php';
require_once $path.'logins.php';
require_once $path.'noticias.php';

if(isset($GLOBALS['noticias_por_catalogo']) && $GLOBALS['noticias_por_catalogo'] == 1)require_once $path.'noticias1.php';
if(isset($GLOBALS['imoveis_por_catalogo'])  && $GLOBALS['imoveis_por_catalogo'] == 1 )require_once $path.'imoveis.php';
require_once $path.'calendario.php';
require_once $path.'produtos.php';
if(isset($GLOBALS['tem_portfolio']) && $GLOBALS['tem_portfolio'] == 1)require_once $path.'portfolio3.php';
require_once $path.'promocoes.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'metodos_pagamento.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'metodos_expedicao.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'loja.php';
if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja'] == 1)require_once $path.'loja_reparacoes.php';

if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_comparador'] == 1)require_once $path.'comparador.php';

require_once $path.'templates.php';
require_once $path.'filtros.php';
if(isset($GLOBALS['tem_chat']) && $GLOBALS['tem_chat'] == 1)require_once $path.'chat.php';
require_once $path.'newsletter.php';
require_once $path.'modulos.php';
if(isset($GLOBALS['tem_chat']) && $GLOBALS['tem_chat'] == 1)require_once $path.'ebook.php';
require_once $path.'cache.php';
require_once $path.'return_filtros.php';
if($prod_id > 0 && (isset($prod_servicos_extra_id)) && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1))require_once $path.'servicos_extra.php';

//$browser = browser_detect();