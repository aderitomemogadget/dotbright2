<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
//************************************* COMO JA NAO HA VARIAVEIS DE SESSAO USAMOS:
/**
  * function to emulate the register_globals setting in PHP
  * for all of those diehard fans of possibly harmful PHP settings :-)
  * @author Ruquay K Calloway
  * @param string $order order in which to register the globals, e.g. 'egpcs' for default
  */
function register_globals($order='egpcs')
{
	 // define a subroutine
	 if(!function_exists('register_global_array'))
	 {
		 function register_global_array(array $superglobal)
		 {
			 foreach($superglobal as $varname => $value)
			 {
				 global $$varname;
				 $$varname=$value;
			 }
		 }
	 }

	$order=explode("\r\n",trim(chunk_split($order, 1)));
	foreach($order as $k)
	{
		switch(strtolower($k))
		{
			case 'e':    if(is_array($_ENV))register_global_array($_ENV);       break;
			case 'g':    if(is_array($_GET))register_global_array($_GET);       break;
			case 'p':    if(is_array($_POST))register_global_array($_POST);      break;
			case 'c':    if(is_array($_COOKIE))register_global_array($_COOKIE);    break;
			case 's':    if(is_array($_SERVER))register_global_array($_SERVER);    break;
		}
	}
}

register_globals();
//**************************** DEFINE LINGUAS ACTIVAS PARA SITE E BK ****/
if(!isset($_SESSION))
{
	//primeira vez que é invocada a página
	session_start();
	$_SESSION['ss_uniq_page']=uniqid('');//Esta variável serve para identificar se estamos a abrir a página pela primeira vez
}
global $admin_lingua,$site_lingua,$oe,$admin_lang,$site_lang,$site_template,$module_voucher,$tem_acessibilidade;
if(isset($_GET['oe']) && $_GET['oe']==1)$_SESSION['ss_oe']=1;
if(isset($_GET['oe']) && $_GET['oe']==-1)$_SESSION['ss_oe']=0;
if($admin_lingua)$_SESSION['ss_admin_lang']=$admin_lingua;
if($site_lingua)$_SESSION['ss_site_lang']=$site_lingua;
if(!isset($_SESSION)){$_SESSION['ss_site_lang']=$GLOBALS['lingua_website'];$_SESSION['ss_admin_lang']=$GLOBALS['lingua_admin'];}
else{if(!isset($_SESSION['ss_site_lang']))$_SESSION['ss_site_lang']=$GLOBALS['lingua_admin'];if(!isset($_SESSION['ss_admin_lang']))$_SESSION['ss_admin_lang']=$GLOBALS['lingua_admin'];}
$site_lang=$_SESSION['ss_site_lang'];
$admin_lang=$_SESSION['ss_admin_lang'];
//**************************** DEFINE LINGUAS ACTIVAS PARA SITE E BK ****/
global $admin_mode,$admin_user,$admin_passwd,$admin_id;
//************* LIMPA TODAS AS VARIAVEIS*********************************/
$admin_mode="";$admin_user="";$admin_passwd="";$admin_id="";
//***********************************************************************/
if(isset($_SESSION['ss_admin']))$admin_mode=$_SESSION['ss_admin'];
if(isset($_SESSION['ss_admin_id']))$admin_id=$_SESSION['ss_admin_id'];
if(isset($_SESSION['ss_admin_user']))$admin_user=$_SESSION['ss_admin_user'];
if(isset($_SESSION['ss_admin_passwd']))$admin_passwd=$_SESSION['ss_admin_passwd'];
if(isset($_SESSION['ss_oe']))$online_edit=$_SESSION['ss_oe'];
if(isset($_SESSION['ss_site_template']))$site_template=$_SESSION['ss_site_template'];

if(isset($_SESSION['ss_admin_definicoes']))$admin_id_definicoes=$_SESSION['ss_admin_definicoes'];

// CARRINHO DE COMPRAS
if(isset($_SESSION['ss_shop']))$shop=$_SESSION['ss_shop'];
if(isset($_SESSION['ss_n_enc']))$n_enc=$_SESSION['ss_n_enc'];

// CARRINHO DE REPARAÇÕES
if(isset($_SESSION['ss_shop_reparacoes']))	$shop_reparacoes =$_SESSION['ss_shop_reparacoes'];
if(isset($_SESSION['ss_n_enc_reparacoes']))	$n_enc_reparacoes=$_SESSION['ss_n_enc_reparacoes'];

if(isset($_GET['logout'])){$_SESSION['ss_cli_id']="";	$_SESSION['ss_cli_token']=""; $cli_id="";$cli_token="";}
//********************************** clientes****/
global $cli_id,$cli_token,$cli_ft;
$cli_id="";$cli_token="";$cli_ft="";
if(isset($_SESSION['ss_cli_id']))$cli_id=$_SESSION['ss_cli_id'];
if(isset($_SESSION['ss_cli_token']))$cli_token=$_SESSION['ss_cli_token']; //por motivos de segurança nunca vamos usar a login e a password, mas sim um token gerando no momento da validaçao do login
if(isset($_SESSION['ss_cli_ft']))$cli_ft=$_SESSION['ss_cli_ft']; //first login attempt
// controlo de produtos
if(isset($_GET['orderby'])) $_SESSION['orderby']=$_GET['orderby'];
if(isset($_GET['visualby'])) $_SESSION['visualby']=$_GET['visualby'];
if(!isset($_SESSION['orderby'])) $_SESSION['orderby']=3;
if(!isset($_SESSION['visualby'])) $_SESSION['visualby']=1;
if(isset($_SESSION['ss_sessao']))$ss_sessao=$_SESSION['ss_sessao'];
if(isset($_SESSION['ss_email']))$ss_email=$_SESSION['ss_email'];
if(isset($GLOBALS['module_voucher']) && $GLOBALS['module_voucher']==1)
{
	if(isset($_SESSION['ss_voucher'])) $voucher=$_SESSION['ss_voucher'];
	if(isset($_SESSION['ss_n_voucher'])) $n_voucher=$_SESSION['ss_n_voucher'];
}
//************** MOEDA
if(!isset($_SESSION['ss_site_country'])) $_SESSION['ss_site_country']='PT';
if(!isset($_SESSION['ss_moeda']) || isset($_GET['site_country']))
{
	$_SESSION['ss_moeda']='&euro;';
	$_SESSION['ss_moeda_iso']='EUR';
	$_SESSION['ss_currency_value']=1;
	if(isset($_GET['site_country']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql="SELECT c.currency_id,c.currency_symbol,c.currency_value,cc.countries_currency FROM currency c left join currency_countries cc on cc.countries_id=c.currency_country where country='".$_GET['site_country']."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			unset($_SESSION['ss_moeda']);
			unset($_SESSION['ss_moeda_iso']);
			unset($_SESSION['ss_currency_id']);
			unset($_SESSION['ss_currency_value']);
			$_SESSION['ss_moeda']=$rs->db_data["currency_symbol"];
			$_SESSION['ss_moeda_iso']=rtrim(ltrim($rs->db_data["countries_currency"]));
			$_SESSION['ss_currency_id']=$rs->db_data["currency_id"];
			$_SESSION['ss_currency_value']=$rs->db_data["currency_value"];
			$_SESSION['ss_site_country']=$_GET['site_country'];
		}
		$rs->db_close();
	}
}
if($tem_acessibilidade && !isset($_SESSION['font'])) $_SESSION['font'] = 1;
define('MOEDA', $_SESSION['ss_moeda']);
define('MOEDA_ISO', $_SESSION['ss_moeda_iso']);
if(isset($_GET['a']) && intval($_GET['a'])>0)
{
	setcookie("domesticosafiliado", intval($_GET['a']), time()+(3600*24*30), "/");
}

if(isset($_GET['ism']))
{
	if(intval($_GET['ism'])==1)
	{
		$_SESSION['ss_ismobile']=1;
	}
	if(intval($_GET['ism'])==-1)
	{
		$_SESSION['ss_ismobile']=0;
	}
}
else
{
	//Não recebemos nenhum parametro

	if(!isset($_SESSION['ss_ismobile']))
	{
		if(is_mobile_or_tablet())$_SESSION['ss_ismobile']=1;
		else $_SESSION['ss_ismobile']=0;
	}
}

function is_mobile_or_tablet()
{
	if($_GET['mobile']==1)return 1;//PAra podermos simular mobile em PC

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Expressões comuns para dispositivos móveis e tablets
    $mobile_agents = ['Android', 'iPhone', 'iPod', 'BlackBerry', 'Opera Mini', 'IEMobile', 'Mobile'];
    $tablet_agents = ['iPad', 'Android 3', 'Silk', 'Kindle', 'Tablet'];

    foreach ($tablet_agents as $tablet) {
        if (stripos($user_agent, $tablet) !== false) {
            return 1;
        }
    }

    foreach ($mobile_agents as $mobile) {
        if (stripos($user_agent, $mobile) !== false) {
            return 1;
        }
    }

    return 0;
}
