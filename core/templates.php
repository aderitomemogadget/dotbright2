<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) exit();

function template_emailxxxx($template_id, $body = array(''), $width = "500px", $align = 0)
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new DataObj;
	$sql = 'SELECT
						img_ext, Cabecalho, Texto1
					FROM
						templates_email
						INNER JOIN templates_email_lang ON templates_email.ID=templates_email_lang.ID_PAI
					WHERE
						templates_email_lang.lang="' . $GLOBALS['site_lang'] . '"
						AND
						templates_email.ID="' . $template_id . '"';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if (!$rs->EOF())
	{
		$template_img = $rs->db_data['img_ext'];
		$template_nome = $rs->db_data['Cabecalho'];
		//$template_body = str_replace("\n", '<br />', $rs->db_data['Texto1']);
		$template_body = $rs->db_data['Texto1'];
		if (!empty($body))
		{
			foreach ($body as $key => $value)
			{
				$template_body = str_replace($key, $value, $template_body);
			}
		}
		$template_body = str_replace('[path]', $GLOBALS['website_url_email'], $template_body);
		$template_body = str_replace('[projeto]', PROJETO, $template_body);
	}
	$rs->db_close();
	if ($template_img && dotbright_image_exists($GLOBALS['site_shell_url'] . 'files/templates_email/' . $template_id . '_1.' . $template_img))
	{
		$message = '<a href="' . $GLOBALS['website_url_email'] . '" target="_blank"><img src="' . $GLOBALS['website_url_email'] . 'files/templates_email/' . $template_id . '_1.' . $template_img . '" alt="" /></a><br /><br />';
		$message .= $template_body;
	}
	else
	{
		$message = '<a href="' . $GLOBALS['website_url_email'] . '" target="_blank"><img src="' . $GLOBALS['website_url_email'] . 'layout/'.PROJETO.'/logos/logo_top.png" alt="" /></a><br /><br />';
		$message .= $template_body;
	}
	if ($align == 1)
	{
		$message = '<div style="font-family:arial;color:#211d1e;font-size:12px;text-align:left">' . $message . '</div>';
	}
	else
	{
		$message = '<div style="font-family:arial;color:#211d1e;font-size:12px;width:' . $width . ';">' . $message . '</div>';
	}

	return array('subject' => $template_nome, 'body' => $message);
}

function template_email($template_id, $body=array(''),$width="800px", $align=0, $body2='', $logo_off = 0, $assunto='')
{
	$template_img  = '';
	if($template_id > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs = new DataObj;
		$sql = 'SELECT img_ext,img2_ext, Cabecalho, Texto1, Texto2 FROM templates_email INNER JOIN templates_email_lang ON templates_email.ID=templates_email_lang.ID_PAI WHERE templates_email_lang.lang="'.$GLOBALS['site_lang'].'"AND templates_email.ID="'.$template_id.'"';

		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs->EOF())
		{
			$template_img  = $rs->db_data['img_ext'];
			$template_img2  = $rs->db_data['img2_ext'];
			$template_nome2 = $rs->db_data['Cabecalho'];
			$template_nome = $rs->db_data['Texto2'];
			$template_body = $rs->db_data['Texto1'];
			//$template_body = str_replace("\n",'<br />',$rs->db_data['Texto1']);
			if(!empty($body))
			{
				foreach($body as $key => $value)
				{
					$template_body = str_replace($key, $value, $template_body);
				}
			}
			$template_body = str_replace('[path]', $GLOBALS['website_url_email'], $template_body);
		}
		$rs->db_close();


		if(trim($body2) !='')
		{
			if(!empty($body))
			{
				foreach($body as $key => $value)
				{
					$body2 = str_replace($key, $value, $body2);
				}
			}
		}
	}
	else
	{
		$body2 = '<span style="font-size:12px; font-family:arial,sans-serif">'.$body.'</span>';
	}

	if($template_img && dotbright_image_exists($GLOBALS['site_shell_url'].'files/templates_email/'.$template_id.'_1.'.$template_img))
	{
		$d=dotbright_image_mtime($GLOBALS['site_shell_url'] . 'files/templates_email/' . $template_id . '_1.' . $template_img);

		//$message .= '<img 107 src="'.$GLOBALS['website_url_email'].'files/templates_email/'.$template_id.'_1.'.$template_img.'?d='.$d.'">';
        $message .= '
    <div style="width:100%; margin:0; padding:0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center">
                    <img src="'.$GLOBALS['website_url_email'].'files/templates_email/'.$template_id.'_1.'.$template_img.'?d='.$d.'" 
                         alt="" 
                         style="display:block; margin:0 auto; border:0; outline:none; text-decoration:none;">
                </td>
            </tr>
        </table>
    </div>
    ';
		$message .= $template_body;
		$message .= $body2;
	}
	else
	{
		$message .= '';
		$message .= $template_body;
		$message .= $body2;
	}

	if($template_img2 && dotbright_image_exists($GLOBALS['site_shell_url'].'files/templates_email/'.$template_id.'_2.'.$template_img2))
	{
		$d=dotbright_image_mtime($GLOBALS['site_shell_url'] . 'files/templates_email/' . $template_id . '_2.' . $template_img2);

		$message .= '<img 122 src="'.$GLOBALS['website_url_email'].'files/templates_email/'.$template_id.'_2.'.$template_img2.'?d='.$d.'">';
        
        
	}

	if($align==1) $message = '<div 125 style="font-family:arial;color:#211d1e;font-size:12px;text-align:left">'.$message.'</div>';
	else $message = '<div 126 style="font-family:arial;color:#211d1e;font-size:12px;width:'.$width.' text-align:center;margin:0 auto;">'.$message.'</div>';


	$logo_off=1; // tem de vir de bd da template

	return array('subject' => $template_nome2, 'body' => email_html($logo_off, $message));
}

function email_html($logo_off=0, $message='', $img_email='')
	{
		$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>[SUBJECT]</title>
		<style type="text/css">
		body {    padding-top: 0 !important;    padding-bottom: 0 !important;    padding-top: 0 !important;    padding-bottom: 0 !important;    margin:0 !important;    width: 100% !important;    -webkit-text-size-adjust: 100% !important;    -ms-text-size-adjust: 100% !important;    -webkit-font-smoothing: antialiased !important;  }
		.tableContent img {    border: 0 !important;    display: block !important;    outline: none !important;  }
		a{ color:#382F2E;}
		p, h1,h2,ul,ol,li,div{   margin:0;   padding:0; }
		h1,h2{   font-weight: normal;   background:transparent !important;   border:none !important; }
		.contentEditable h2.big,.contentEditable h1.big{ font-size: 26px !important; }
		.contentEditable h2.bigger,.contentEditable h1.bigger{ font-size: 37px !important; }
		td,table{   vertical-align: top; }
		td.middle{   vertical-align: middle; }
		a.link1{   font-size:13px;   color:#27A1E5;   line-height: 24px;   text-decoration:none; }
		a{   text-decoration: none; }
		.link2{ color:#ffffff; border-top:10px solid #27A1E5; border-bottom:10px solid #27A1E5; border-left:18px solid #27A1E5; border-right:18px solid #27A1E5; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; background:#27A1E5; }
		.link3{ color:#555555; border:1px solid #cccccc; padding:10px 18px; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; background:#ffffff; }
		.link4{ color:#27A1E5; line-height: 24px; }
		h2,h1{ line-height: 20px; }
		p{   font-size: 14px;   line-height: 21px;   color:#AAAAAA; }
		.contentEditable li{}
		.appart p{}
		.bgItem{background: #ffffff;}
		.bgBody{background: #ffffff;}
		img {outline:none;   text-decoration:none;-ms-interpolation-mode: bicubic; clear: both;   display: block;   float: none; }
		</style>

		</head>
		<body paddingwidth="0" paddingheight="0" bgcolor="#FFFFFF"  style="background:#FFFFFF;padding-top: 0; padding-bottom: 0; padding-top: 0; padding-bottom: 0; background-repeat: repeat; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-font-smoothing: antialiased;" offset="0" toppadding="0" leftpadding="0">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td><table width="800" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style="font-family:helvetica, sans-serif;" class="MainContainer">
		<tbody>
		<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td valign="top" width="20">&nbsp;</td>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td class="movableContentContainer">
		<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td height="15"></td>
		</tr>
		<tr>
		<td>';
		if($logo_off == 0)
		{
			$return .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
			<td valign="top" align="center"><a href="'.$GLOBALS['website_url_email'].'"><img style="width:100px" src="'.(defined('LOGOTIPO_EMAIL') ? LOGOTIPO_EMAIL : $GLOBALS['website_url_email'].'layout/'.PROJETO.'/logos/logo_email_preto.png').'" border="0" alt="Logo" title="" data-max-width="100" /></a></td>
			<td width="10" valign="top">&nbsp;</td>
			<td valign="middle" style="vertical-align: middle;">
			<div class="contentEditableContainer contentTextEditable">
			<div class="contentEditable" style="text-align: left;font-weight: light; color:#555555;font-size:26;line-height: 39px;font-family: Helvetica Neue;"></div>
			</div>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>';
		}
		$return .= '
		</td>
		</tr>

		</tbody>
		</table>
		</div>
		<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td height="40"></td>
		</tr>
		<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>

		<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr><td height="25"></td></tr>
		<tr>
		<td>
		<div class="contentEditableContainer contentTextEditable">
		'.$message.'
		</div>
		</td>
		</tr>
		<tr><td height="24"></td></tr>
		</table></td>

		</tr>
		</tbody>
		</table>
		</td>
		</tr>
		</tbody>
		</table>
		</div>
		<div class="movableContent" style="border: 0px; padding-top: 0px; position: relative;">

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
		<td height="40"></td>
		</tr>

		</tbody>
		</table>
		</td>
		</tr>
		<tr>
		<td height="40"></td>
		</tr>
		</tbody>
		</table>
		</div>
		</td>
		</tr>
		</tbody>
		</table>
		</td>
		<td valign="top" width="20">&nbsp;</td>
		</tr>
		</tbody>
		</table>
		</td>
		</tr>
		</tbody>
		</table>
		</td>
		</tr>
		</tbody>
		</table>
		</body>
		</html>';

	return dotbright_files_images_html($return);
}
function show_alerta($msg, $tipo='', $url_back_mod = '', $anime = '')
{
	$animescript = '';
	if ($tipo == 'erro')
	{
		$imgdiv = '';
	}
	elseif ($tipo == 'msg')
	{
		$imgdiv = '';
	}
	elseif ($tipo == 'loading')
	{
		$imgdiv = '';
	}
	else
	{
		$imgdiv = '<img src="layout/'.PROJETO.'/images/ok2.png" alt="'.$msg.'">';
	}

	if ($anime == 1)
	{
		$animescript = '<script type="text/javascript">$(\'.showalert\').delay(2000).slideUp(\'slow\');</script>';
	}

	return '<div class="showalert">' . $imgdiv . '<br>' . $msg . '</div>' . $animescript;
}

function show_alerta_cart($tipo = 'erro', $anime = 1)
{
	global $log;
	if ($log)
	{
		debug(__FILE__, __FUNCTION__, __LINE__);
	}
	global $key_voltarareacli, $site_lang, $key_voltarhome, $site_http_url;
	if ($GLOBALS['erro'] != '')
	{
		$msg = $GLOBALS['erro'];
		echo '<div class="showalert" style="margin-bottom:10px">';
		if ($tipo == 'erro')
		{
			echo '<div class="img"><img src="layout/' . PROJETO . '/images/alert.png" /></div>';
		}
		elseif ($tipo == 'msg')
		{
			echo '<div class="img"><img src="layout/' . PROJETO . '/images/ok2.png" /></div>';
		}
		elseif ($tipo == 'loading')
		{
			echo '<div class="img"><img src="layout/' . PROJETO . '/images/loading.gif" /></div>';
		}
		echo '<div class="msg">' . $msg . '</div>';
		echo '</div>';
		if ($anime == 1)
		{
			echo '<script>$(\'.showalert\').delay(5000).slideUp(\'slow\');</script>';
		}
	}
}

function show_alerta2($msg='', $image='ok2', $font='')
{
	$string  = '<div class="row notify successbox text-center">';
    if(dotbright_image_exists('layout/'.PROJETO.'/images/'.$image.'.png') && $font == '') $string .= '<span class="alerticon" style="background:url(layout/'.PROJETO.'/images/'.$image.'.png) no-repeat center;background-size:64px auto"></span>';
	elseif($font != '') $string .= '<span class="alerticon">'.$font.'</span>';
    $string .= '<p><b>'.$msg.'</b></p>';
    $string .= '</div>';

	return $string;
}


?>