<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] ==__FILE__) exit();
// Check if definitions are set on admin area (database), if not uses definitions from definitions file
function definicoes_plataforma()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	global $Default_SMTP,$Default_SMTP_login,$Default_SMTP_pass,$Default_SMTP_Email,$Default_SMTP_Porta;
	global $TAG_EMPRESA,$TAG_DESIGSOCIAL,$TAG_COPYRIGHT,$FromName,$form_recipient,$SEO_FB,$SEO_PHONE,$SEO_DESCRIPTION,$id_revendedor,$iva_default,$site_http_url,$site_title,$pais_id_default,$exec;

	if(!isset($_SESSION))session_start();

	if(isset($_SESSION['tmp_modulos_config_site_ID']) && $_SESSION['tmp_modulos_config_site_ID'] > 0)
	{
		$ID=$_SESSION['tmp_modulos_config_site_ID'];
		$img_ext_db=$_SESSION['tmp_modulos_config_site_img_ext_db'];
		$img2_ext_db=$_SESSION['tmp_modulos_config_site_img2_ext_db'];
		$img3_ext_db=$_SESSION['tmp_modulos_config_site_img3_ext_db'];
		$img4_ext_db=$_SESSION['tmp_modulos_config_site_img4_ext_db'];
		$img5_ext_db=$_SESSION['tmp_modulos_config_site_img5_ext_db'];
		$revendedor_id_db=$_SESSION['tmp_modulos_config_site_revendedor_id_db'];
		$pais_id_default_db=$_SESSION['tmp_modulos_config_site_pais_id_default_db'];
		$exec_caminho_db=$_SESSION['tmp_modulos_config_site_exec_caminho_db'];
		$forcar_https_db=$_SESSION['tmp_modulos_config_site_forcar_https_db'];
		$FromName_db=$_SESSION['tmp_modulos_config_site_FromName_db'];
		$form_recipient_db=$_SESSION['tmp_modulos_config_site_form_recipient_db'];
		$Default_SMTP_db=$_SESSION['tmp_modulos_config_site_Default_SMTP_db'];
		$Default_SMTP_login_db=$_SESSION['tmp_modulos_config_site_Default_SMTP_login_db'];
		$Default_SMTP_pass_db=$_SESSION['tmp_modulos_config_site_Default_SMTP_pass_db'];
		$Default_SMTP_Email_db=$_SESSION['tmp_modulos_config_site_Default_SMTP_Email_db'];
		$Default_SMTP_Porta_db=$_SESSION['tmp_modulos_config_site_Default_SMTP_Porta_db'];
		$TAG_EMPRESA_db=$_SESSION['tmp_modulos_config_site_TAG_EMPRESA_db'];
		$TAG_DESIGSOCIAL_db=$_SESSION['tmp_modulos_config_site_TAG_DESIGSOCIAL_db'];
		$TAG_COPYRIGHT_db=$_SESSION['tmp_modulos_config_site_TAG_COPYRIGHT_db'];
		$SEO_FB_db=$_SESSION['tmp_modulos_config_site_SEO_FB_db'];
		$SEO_PHONE_db=$_SESSION['tmp_modulos_config_site_SEO_PHONE_db'];
		$SEO_DESCRIPTION_db=$_SESSION['tmp_modulos_config_site_SEO_DESCRIPTION_db'];
		$iva_default_db=$_SESSION['tmp_modulos_config_site_iva_default_db'];
		$offline_db=$_SESSION['tmp_modulos_config_site_offline_db'];
		$offline_cor_db=$_SESSION['tmp_modulos_config_site_offline_cor_db'];
		$offline_txt_db=$_SESSION['tmp_modulos_config_site_offline_txt_db'];
		$Seta_scrollup_db=$_SESSION['tmp_modulos_config_site_Seta_scrollup_db'];
		$emails_encomenda_db=$_SESSION['tmp_modulos_config_site_emails_encomenda_db'];
		$ordenar_por_db=$_SESSION['tmp_modulos_config_site_ordenar_por_db'];
		$facebook_id_db=$_SESSION['tmp_modulos_config_site_facebook_id_db'];
		$facebook_secret_id_db=$_SESSION['tmp_modulos_config_site_facebook_secret_id_db'];
		$google_client_id_db=$_SESSION['tmp_modulos_config_site_google_client_id_db'];
		$google_client_secret_db=$_SESSION['tmp_modulos_config_site_google_client_secret_db'];
		$twitter_consumer_id_db=$_SESSION['tmp_modulos_config_site_twitter_consumer_id_db'];
		$twitter_consumer_secret_db=$_SESSION['tmp_modulos_config_site_twitter_consumer_secret_db'];
		$DB_host_db=$_SESSION['tmp_modulos_config_site_DB_host_db'];
		$DB_user_db=$_SESSION['tmp_modulos_config_site_DB_user_db'];
		$DB_pass_db=$_SESSION['tmp_modulos_config_site_DB_pass_db'];
		$DB_name_db=$_SESSION['tmp_modulos_config_site_DB_name_db'];
		$site_title_db=$_SESSION['tmp_modulos_config_site_site_title_db'];
		$site_http_url_db=$_SESSION['tmp_modulos_config_site_site_http_url_db'];
		$website_url_email_db=$_SESSION['tmp_modulos_config_site_website_url_email_db'];
	}
	else
	{
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql='SELECT * FROM modulos_config_site WHERE ID=1';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$ID=$rs->db_data['ID'];
			$img_ext_db=$rs->db_data['img_ext'];
			$img2_ext_db=$rs->db_data['img2_ext'];
			$img3_ext_db=$rs->db_data['img3_ext'];
			$img4_ext_db=$rs->db_data['img4_ext'];
			$img5_ext_db=$rs->db_data['img5_ext'];
			$revendedor_id_db=$rs->db_data['revendedor_id'];
			$pais_id_default_db=$rs->db_data['pais_id_default'];
			$exec_caminho_db=$rs->db_data['exec_caminho'];
			$forcar_https_db=$rs->db_data['forcar_https'];
			$FromName_db=$rs->db_data['FromName'];
			$form_recipient_db=$rs->db_data['form_recipient'];
			$Default_SMTP_db=$rs->db_data['Default_SMTP'];
			$Default_SMTP_login_db=$rs->db_data['Default_SMTP_login'];
			$Default_SMTP_pass_db=$rs->db_data['Default_SMTP_pass'];
			$Default_SMTP_Email_db=$rs->db_data['Default_SMTP_Email'];
			$Default_SMTP_Porta_db=$rs->db_data['Default_SMTP_Porta'];
			$TAG_EMPRESA_db=$rs->db_data['TAG_EMPRESA'];
			$TAG_DESIGSOCIAL_db=$rs->db_data['TAG_DESIGSOCIAL'];
			$TAG_COPYRIGHT_db=$rs->db_data['TAG_COPYRIGHT'];
			$SEO_FB_db=$rs->db_data['SEO_FB'];
			$SEO_PHONE_db=$rs->db_data['SEO_PHONE'];
			$SEO_DESCRIPTION_db=$rs->db_data['SEO_DESCRIPTION'];
			$iva_default_db=$rs->db_data['iva_default'];
			$offline_db=$rs->db_data['offline'];
			$offline_cor_db=$rs->db_data['offline_cor'];
			$offline_txt_db=$rs->db_data['offline_txt'];
			$Seta_scrollup_db=$rs->db_data['Seta_scrollup'];
			$emails_encomenda_db=$rs->db_data['emails_encomenda'];
			$ordenar_por_db=$rs->db_data['ordenar_por'];
			$facebook_id_db=$rs->db_data['facebook_id'];
			$facebook_secret_id_db=$rs->db_data['facebook_secret'];
			$google_client_id_db=$rs->db_data['google_id'];
			$google_client_secret_db=$rs->db_data['google_secret'];
			$twitter_consumer_id_db=$rs->db_data['twitter_id'];
			$twitter_consumer_secret_db=$rs->db_data['twitter_secret'];
			$DB_host_db=$rs->db_data['dblocalhost'];
			$DB_user_db=$rs->db_data['dbusername'];
			$DB_pass_db=$rs->db_data['dbpassword'];
			$DB_name_db=$rs->db_data['dbname'];
			$site_title_db=$rs->db_data['title'];
			$site_http_url_db=$rs->db_data['url'];
			$website_url_email_db=$rs->db_data['url_email'];
			$_SESSION['tmp_modulos_config_site_ID']=$ID;
			$_SESSION['tmp_modulos_config_site_img_ext_db']=$img_ext_db;
			$_SESSION['tmp_modulos_config_site_img2_ext_db']=$img2_ext_db;
			$_SESSION['tmp_modulos_config_site_img3_ext_db']=$img3_ext_db;
			$_SESSION['tmp_modulos_config_site_img4_ext_db']=$img4_ext_db;
			$_SESSION['tmp_modulos_config_site_img5_ext_db']=$img5_ext_db;
			$_SESSION['tmp_modulos_config_site_revendedor_id_db']=$revendedor_id_db;
			$_SESSION['tmp_modulos_config_site_pais_id_default_db']=$pais_id_default_db;
			$_SESSION['tmp_modulos_config_site_exec_caminho_db']=$exec_caminho_db;
			$_SESSION['tmp_modulos_config_site_forcar_https_db']=$forcar_https_db;
			$_SESSION['tmp_modulos_config_site_FromName_db']=$FromName_db;
			$_SESSION['tmp_modulos_config_site_form_recipient_db']=$form_recipient_db;
			$_SESSION['tmp_modulos_config_site_Default_SMTP_db']=$Default_SMTP_db;
			$_SESSION['tmp_modulos_config_site_Default_SMTP_login_db']=$Default_SMTP_login_db;
			$_SESSION['tmp_modulos_config_site_Default_SMTP_pass_db']=$Default_SMTP_pass_db;
			$_SESSION['tmp_modulos_config_site_Default_SMTP_Email_db']=$Default_SMTP_Email_db;
			$_SESSION['tmp_modulos_config_site_Default_SMTP_Porta_db']=$Default_SMTP_Porta_db;
			$_SESSION['tmp_modulos_config_site_TAG_EMPRESA_db']=$TAG_EMPRESA_db;
			$_SESSION['tmp_modulos_config_site_TAG_DESIGSOCIAL_db']=$TAG_DESIGSOCIAL_db;
			$_SESSION['tmp_modulos_config_site_TAG_COPYRIGHT_db']=$TAG_COPYRIGHT_db;
			$_SESSION['tmp_modulos_config_site_SEO_FB_db']=$SEO_FB_db;
			$_SESSION['tmp_modulos_config_site_SEO_PHONE_db']=$SEO_PHONE_db;
			$_SESSION['tmp_modulos_config_site_SEO_DESCRIPTION_db']=$SEO_DESCRIPTION_db;
			$_SESSION['tmp_modulos_config_site_iva_default_db']=$iva_default_db;
			$_SESSION['tmp_modulos_config_site_offline_db']=$offline_db;
			$_SESSION['tmp_modulos_config_site_offline_cor_db']=$offline_cor_db;
			$_SESSION['tmp_modulos_config_site_offline_txt_db']=$offline_txt_db;
			$_SESSION['tmp_modulos_config_site_Seta_scrollup_db']=$Seta_scrollup_db;
			$_SESSION['tmp_modulos_config_site_emails_encomenda_db']=$emails_encomenda_db;
			$_SESSION['tmp_modulos_config_site_ordenar_por_db']=$ordenar_por_db;
			$_SESSION['tmp_modulos_config_site_facebook_id_db']=$facebook_id_db;
			$_SESSION['tmp_modulos_config_site_facebook_secret_id_db']=$facebook_secret_id_db;
			$_SESSION['tmp_modulos_config_site_google_client_id_db']=$google_client_id_db;
			$_SESSION['tmp_modulos_config_site_google_client_secret_db']=$google_client_secret_db;
			$_SESSION['tmp_modulos_config_site_twitter_consumer_id_db']=$twitter_consumer_id_db;
			$_SESSION['tmp_modulos_config_site_twitter_consumer_secret_db']=$twitter_consumer_secret_db;
			$_SESSION['tmp_modulos_config_site_DB_host_db']=$DB_host_db;
			$_SESSION['tmp_modulos_config_site_DB_user_db']=$DB_user_db;
			$_SESSION['tmp_modulos_config_site_DB_pass_db']=$DB_pass_db;
			$_SESSION['tmp_modulos_config_site_DB_name_db']=$DB_name_db;
			$_SESSION['tmp_modulos_config_site_site_title_db']=$site_title_db;
			$_SESSION['tmp_modulos_config_site_site_http_url_db']=$site_http_url_db;
			$_SESSION['tmp_modulos_config_site_website_url_email_db']=$website_url_email_db;
		}
		$rs->db_close();
	}
	// vamos sobrepor as definições do ficheiros definitions.php com as definições da base de dados
	if(isset($id_revendedor_db) && $id_revendedor_db !='')  $id_revendedor=$id_revendedor_db;
	if(isset($pais_id_default_db) && $pais_id_default_db > 0)$pais_id_default=$pais_id_default_db;
	if(isset($exec_caminho_db) && $exec_caminho_db !='')$exec_caminho=$exec_caminho_db;
	if(isset($FromName_db) && $FromName_db !='')$FromName=$FromName_db;
	if(isset($form_recipient_db) && $form_recipient_db !='')$form_recipient=$form_recipient_db;
	if(isset($Default_SMTP_db) && $Default_SMTP_db !='')$Default_SMTP=$Default_SMTP_db;
	if(isset($Default_SMTP_login_db) && $Default_SMTP_login_db !='')$Default_SMTP_login=$Default_SMTP_login_db;
	if(isset($Default_SMTP_pass_db) && $Default_SMTP_pass_db !='')$Default_SMTP_pass=$Default_SMTP_pass_db;
	if(isset($Default_SMTP_Email_db) && $Default_SMTP_Email_db !='')$Default_SMTP_Email=$Default_SMTP_Email_db;
	if(isset($Default_SMTP_Porta_db) && $Default_SMTP_Porta_db !='')$Default_SMTP_Porta=$Default_SMTP_Porta_db;
	if(isset($TAG_EMPRESA_db) && $TAG_EMPRESA_db !='')$TAG_EMPRESA=$TAG_EMPRESA_db;
	if(isset($TAG_DESIGSOCIAL_db) && $TAG_DESIGSOCIAL_db !='')$TAG_DESIGSOCIAL=$TAG_DESIGSOCIAL_db;
	if(isset($TAG_COPYRIGHT_db) && $TAG_COPYRIGHT_db !='')$TAG_COPYRIGHT=$TAG_COPYRIGHT_db;
	if(isset($SEO_FB_db) && $SEO_FB_db !='')$SEO_FB=$SEO_FB_db;
	if(isset($SEO_PHONE_db) && $SEO_PHONE_db !='')$SEO_PHONE=$SEO_PHONE_db;
	if(isset($SEO_DESCRIPTION_db) && $SEO_DESCRIPTION_db !='')$SEO_DESCRIPTION=$SEO_DESCRIPTION_db;
	if(isset($iva_default_db) && $iva_default_db !='')$iva_default=$iva_default_db;

	$facebook_id=0;if(isset($facebook_id_db) && $facebook_id_db !='')$facebook_id=$facebook_id_db;
	$facebook_secret_id=0;if(isset($facebook_secret_id_db) && $facebook_secret_id_db !='')$facebook_secret_id=$facebook_secret_id_db;
	$google_client_id=0;if(isset($google_client_id_db) && $google_client_id_db !='')$google_client_id=$google_client_id_db;
	$google_client_secret=0;if(isset($google_client_secret_db) && $google_client_secret_db !='')$google_client_secret=$google_client_secret_db;
	$twitter_consumer_id=0;if(isset($twitter_consumer_id_db) && $twitter_consumer_id_db !='')$twitter_consumer_id=$twitter_consumer_id_db;
	$twitter_consumer_secret=0;if(isset($twitter_consumer_secret_db) && $twitter_consumer_secret_db !='')$twitter_consumer_secret=$twitter_consumer_secret_db;

	if((isset($DB_host_db) && $DB_host_db !='') && (isset($DB_user_db) && $DB_user_db !='') && (isset($DB_pass_db) && $DB_pass_db !='') && (isset($DB_name_db) && $DB_name_db !=''))
	{
		$db_error_connection=false;
		$link=@mysqli_connect($DB_host_db,$DB_user_db,$DB_pass_db);
		if (!$link) {
			$db_error_connection=true;
		}else{
			$DB_host=$DB_host_db;
			$DB_user=$DB_user_db;
			$DB_pass=$DB_pass_db;
			$DB_name=$DB_name_db;
		}
	}

	//if(isset($site_http_url_db) && $site_http_url_db !='')$site_http_url=$site_http_url_db;
	if(isset($website_url_email_db) && $website_url_email_db !='')$website_url_email=$website_url_email_db;
	//if(isset($forcar_https_db) && $forcar_https_db > 0)$site_http_url=str_replace("http","https",$site_http_url); //TODOss
	if(isset($site_title_db) && $site_title_db !='')$site_title=$site_title_db;

	return array(
		'LOGOTIPO_PRINCIPAL' => (isset($img_ext_db) && $img_ext_db !='' && stream_resolve_include_path(dirname( __FILE__ ).'/../files/modulos_config_site/'.$img_ext_db) ? $GLOBALS['site_http_url'].'files/modulos_config_site/'.$img_ext_db : $GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png'),
		'LOGOTIPO_POPUPS' => (isset($img3_ext_db) && $img3_ext_db !='' && stream_resolve_include_path(dirname( __FILE__ ).'/../files/modulos_config_site/'.$img3_ext_db) ? $GLOBALS['site_http_url'].'files/modulos_config_site/'.$img3_ext_db : $GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_apprise.png'),
		'LOGOTIPO_EMAIL' => (isset($img4_ext_db) && $img4_ext_db !='' && stream_resolve_include_path(dirname( __FILE__ ).'/../files/modulos_config_site/'.$img4_ext_db) ? $GLOBALS['site_http_url'].'files/modulos_config_site/'.$img4_ext_db : $GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_email.png'),
		'LOGOTIPO_MOBILE' => (isset($img5_ext_db) && $img5_ext_db !='' && stream_resolve_include_path(dirname( __FILE__ ).'/../files/modulos_config_site/'.$img5_ext_db) ? $GLOBALS['site_http_url'].'files/modulos_config_site/'.$img5_ext_db : $GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top_resp.png'),
		/*** begin done ***/
		'REVENDEDOR_ID' => $id_revendedor,
		'PAIS_ID_DEFAULT' => $pais_id_default,
		'EXEC_CAMINHO' => $exec_caminho,
		/*** end done ***/
		'FORCAR_HTTPS' => (isset($forcar_https_db) && $forcar_https_db > 0 ? 1 : 0),
		/*** begin done ***/
		'FROM_NAME' => $FromName,
		'FORM_RECIPIENT' => $form_recipient,
		'SMTP_HOST' => $Default_SMTP,
		'SMTP_LOGIN' => $Default_SMTP_login,
		'SMTP_PASS' => $Default_SMTP_pass,
		'SMTP_EMAIL' => $Default_SMTP_Email,
		'SMTP_PORTA' => $Default_SMTP_Porta,
		'TAG_EMPRESA' => $TAG_EMPRESA,
		'TAG_DESIGSOCIAL' => $TAG_DESIGSOCIAL,
		'TAG_COPYRIGHT' => $TAG_COPYRIGHT,
		'SEO_FB' => $SEO_FB,
		'SEO_PHONE' => $SEO_PHONE,
		'SEO_DESCRIPTION' => $SEO_DESCRIPTION,
		'IVA_DEFAULT' => $iva_default,
		'FACEBOOK_ID' => $facebook_id,
		'FACEBOOK_SECRET' => $facebook_secret_id,
		'GOOGLE_ID' => $google_client_id,
		'GOOGLE_SECRET' => $google_client_secret,
		'TWITTER_ID' => $twitter_consumer_id,
		'TWITTER_SECRET' => $twitter_consumer_secret,
		'DB_HOST' => $DB_host,
		'DB_USER' => $DB_user,
		'DB_PASS' => $DB_pass,
		'DB_NAME' => $DB_name,
		'SITE_HTTP_URL' => $site_http_url,
		'SITE_TITLE' => $site_title,
		/*** end done ***/
		'MODO_OFFLINE' => (isset($offline_db) && $offline_db > 0 ? 1 : 0),
		'MODO_OFFLINE_BG' => (isset($offline_cor_db) && $offline_cor_db !='' ? '#'.$offline_cor_db : '#FFFFFF'),
		'MODO_OFFLINE_BODY' => (isset($offline_txt_db) && $offline_txt_db !='' ? $offline_txt_db : ''),
		'SETA_SCROLLUP' => (isset($Seta_scrollup_db) && $Seta_scrollup_db > 0 ? 1 : 0),
		'EMAILS_ENCOMENDA' => (isset($emails_encomenda_db) && $emails_encomenda_db !=0 ? $emails_encomenda_db : ''),
		'ORDENAR_POR' => (isset($ordenar_por_db) && $ordenar_por_db !='' ? $ordenar_por_db : '-1')
	);
}

function push_confs($return='')
{
	if($return !='')
	{
		$definicoes_website=definicoes_plataforma();

		return $definicoes_website[$return];
	}
	return;
}

// Definimos constantes. Como não estamos, para já, a usar constantes no site todo teremos de também redefinir as globais
$definicoes_website=definicoes_plataforma();

if(!defined('LOGOTIPO_PRINCIPAL'))define('LOGOTIPO_PRINCIPAL',$definicoes_website['LOGOTIPO_PRINCIPAL']); // DONE LOCALHOST, este tem que ser nos templates no admin
if(!defined('LOGOTIPO_POPUPS'))define('LOGOTIPO_POPUPS',$definicoes_website['LOGOTIPO_POPUPS']);  // DONE
if(!defined('LOGOTIPO_EMAIL'))define('LOGOTIPO_EMAIL',$definicoes_website['LOGOTIPO_EMAIL']); // DONE
if(!defined('LOGOTIPO_MOBILE'))define('LOGOTIPO_MOBILE',$definicoes_website['LOGOTIPO_MOBILE']); // DONE
if(!defined('REVENDEDOR_ID'))define('REVENDEDOR_ID',$definicoes_website['REVENDEDOR_ID']);
if(!defined('PAIS_ID_DEFAULT'))define('PAIS_ID_DEFAULT',$definicoes_website['PAIS_ID_DEFAULT']); // DONE
if(!defined('EXEC_CAMINHO'))define('EXEC_CAMINHO',$definicoes_website['EXEC_CAMINHO']); // TODO
if(!defined('FORCAR_HTTPS'))define('FORCAR_HTTPS',$definicoes_website['FORCAR_HTTPS']); // TODO
if(!defined('FROM_NAME'))define('FROM_NAME',$definicoes_website['FROM_NAME']); // DONE
if(!defined('FORM_RECIPIENT'))define('FORM_RECIPIENT',$definicoes_website['FORM_RECIPIENT']); // DONE
if(!defined('SMTP_HOST'))define('SMTP_HOST',$definicoes_website['SMTP_HOST']); // DONE
if(!defined('SMTP_LOGIN'))define('SMTP_LOGIN',$definicoes_website['SMTP_LOGIN']); // DONE
if(!defined('SMTP_PASS'))define('SMTP_PASS',$definicoes_website['SMTP_PASS']); // DONE
if(!defined('SMTP_EMAIL'))define('SMTP_EMAIL',$definicoes_website['SMTP_EMAIL']); // DONE
if(!defined('SMTP_PORTA'))define('SMTP_PORTA',$definicoes_website['SMTP_PORTA']); // DONE
if(!defined('TAG_EMPRESA'))define('TAG_EMPRESA',$definicoes_website['TAG_EMPRESA']);
if(!defined('TAG_DESIGSOCIAL'))define('TAG_DESIGSOCIAL',$definicoes_website['TAG_DESIGSOCIAL']);
if(!defined('TAG_COPYRIGHT'))define('TAG_COPYRIGHT',$definicoes_website['TAG_COPYRIGHT']);
if(!defined('SEO_FB'))define('SEO_FB',$definicoes_website['SEO_FB']);
if(!defined('SEO_PHONE'))define('SEO_PHONE',$definicoes_website['SEO_PHONE']);
if(!defined('SEO_DESCRIPTION'))define('SEO_DESCRIPTION',$definicoes_website['SEO_DESCRIPTION']);
if(!defined('IVA_DEFAULT'))define('IVA_DEFAULT',$definicoes_website['IVA_DEFAULT']);

if(!defined('FACEBOOK_ID'))define('FACEBOOK_ID',$definicoes_website['FACEBOOK_ID']);
if(!defined('FACEBOOK_SECRET'))define('FACEBOOK_SECRET',$definicoes_website['FACEBOOK_SECRET']);
if(!defined('GOOGLE_ID'))define('GOOGLE_ID',$definicoes_website['GOOGLE_ID']);
if(!defined('GOOGLE_SECRET'))define('GOOGLE_SECRET',$definicoes_website['GOOGLE_SECRET']);

if(!defined('TWITTER_ID'))define('TWITTER_ID',$definicoes_website['TWITTER_ID']);
if(!defined('TWITTER_SECRET'))define('TWITTER_SECRET',$definicoes_website['TWITTER_SECRET']);

if(!defined('DB_HOST'))define('DB_HOST',$definicoes_website['DB_HOST']);
if(!defined('DB_USER'))define('DB_USER',$definicoes_website['DB_USER']);
if(!defined('DB_PASS'))define('DB_PASS',$definicoes_website['DB_PASS']);
if(!defined('DB_NAME'))define('DB_NAME',$definicoes_website['DB_NAME']);

if(!defined('SITE_HTTP_URL'))define('SITE_HTTP_URL',$definicoes_website['SITE_HTTP_URL']);
if(!defined('SITE_TITLE'))define('SITE_TITLE',$definicoes_website['SITE_TITLE']);

if(!defined('MODO_OFFLINE'))define('MODO_OFFLINE',$definicoes_website['MODO_OFFLINE']);
if(!defined('MODO_OFFLINE_BG'))define('MODO_OFFLINE_BG',$definicoes_website['MODO_OFFLINE_BG']);
if(!defined('MODO_OFFLINE_BODY'))define('MODO_OFFLINE_BODY',$definicoes_website['MODO_OFFLINE_BODY']);
if(!defined('SETA_SCROLLUP'))define('SETA_SCROLLUP',$definicoes_website['SETA_SCROLLUP']);
if(!defined('EMAILS_ENCOMENDA'))define('EMAILS_ENCOMENDA',$definicoes_website['EMAILS_ENCOMENDA']);
if(!defined('ORDENAR_POR'))define('ORDENAR_POR',$definicoes_website['ORDENAR_POR']);
