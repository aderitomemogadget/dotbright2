<?php
function enviaEmail($id, $tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$Estado = '';


	$sql = 'SELECT * FROM clientes WHERE ID="'.$id.'" '.$and_loja;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$username = $rs->db_data['Username'];
		$password = base64_decode($rs->db_data['Password']);
		$p_seo = $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['registo_page_id']);

		$m_email = $rs->db_data['Username'];

		$body .= 'Dados da sua conta no site dotbright.pt para que possa aceder ao estado de reparação do seu equipamento:<br><br>';
		$body .= 'Nome: '.$rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
		$body .= '<br>';
		$body .= 'Username: '.$username;
		$body .= '<br><br>';
		$body .= 'Password: '.$password;
		$body .= '<br><br>';
		$body .= '<a href="'.$p_seo.'?show_rmas=1">Clique aqui para aceder à sua área reservada</a>';

		$message = template_email_rmas($GLOBALS['template_rma_novouser'], $body);
		send_email($m_email, 'Dados de conta em dotbright.pt ', $message['body']);
	}
	$rs->db_close();

}

function template_email_rmas($template_id, $body=array(''),$width="800px", $align=0, $body2='', $logo_off = 0, $assunto='')
{
	$template_img  = '';

	$body2 = '<span style="font-size:12px; font-family:arial,sans-serif">'.$body.'</span>';
	$template_nome2 = '';

	$message .= $body2;



	if($align==1) $message = '<div style="font-family:arial;color:#211d1e;font-size:12px;text-align:left">'.$message.'</div>';
	else $message = '<div style="font-family:arial;color:#211d1e;font-size:12px;width:'.$width.';">'.$message.'</div>';

	return array('subject' => $template_nome2, 'body' => email_html_rmas($logo_off, $message));
}


	function email_html_rmas($logo_off=0, $message='')
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
			<td valign="top" align="left"><a href="'.$GLOBALS['site_http_url'].'"><img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" border="0" alt="Logo" title="dotbright.pt" data-max-width="100" /></a></td>
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
		<td width="40" valign="top">&nbsp;</td>
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
		<td width="40" valign="top">&nbsp;</td>
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

	return $return;
}

function cabecalhos_estados($f_estado=0)
{
?><select name="f_estado" style="width:100%" class="chzn-select">
<?php
// ARRAY NO SETTINGS
if(isset($GLOBALS['estados_rmas_cab']))
{
	foreach($GLOBALS['estados_rmas_cab'] as $estado_cab => $cab)
	{
		if($f_estado == $estado_cab)echo '<option value="'.$estado_cab.'" selected="">'.$cab.'</option>';
		else echo '<option value="'.$estado_cab.'">'.$cab.'</option>';
	}
}
?>
</select><?php
}
function showProduct_cores_select($prod_id=0, $txt='')
{
	$prods = get_produtc_info($prod_id);
	$return = '';
	if($prods['prod_aproved'])
	{
		if(is_array($prods['outras_img']))
		{
			foreach ($prods['outras_img'] as $value)
			{
				$stock = $value['stock'];
/*
				$return .= '
				<div class="prod-o" style="height:auto"><img src="'.$value['img'].'" />
					<div class="prod-o-nome" style="min-height:20px">'.$value['nome'].$stock.'</div>'; */
				$return .= '<tr onMouseOver="this.style.backgroundColor=\'#DBE7F1\'" onMouseOut="this.style.backgroundColor=\''.$cor.'\'"><td align="left">';
				$return .= '<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0>';
				$return .= '<tr onMouseOver="this.style.backgroundColor=\'#DBE7F1\'" onMouseOut="this.style.backgroundColor=\''.$cor.'\'">';
				$return .= '<td align="left"><a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].';'.htmlentities_b(addslashes($nomeb)).'")\'>'.$value['nome'].'</a></td>';
				$return .= '</tr>';
				$return .= '</table>';
				$return .= '</td></tr>';

			}
		}
	}


	return $return;
}
function gera_pdf_orcamento($orcamento_id=0)
{
	// global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	// $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $rs = new DataObj;$rs2 = new DataObj; $dados = new DataObj; $tbl = ''; $prods = array(); $arr_imagens = array();
	// $sql = 'SELECT '.implode(',', $GLOBALS['CMPS_MODULO_DADOS']).' FROM '.TBMODULO.' WHERE '.TBID.'='.$orcamento_id;
	// $rs->db_open($db_link, $DB_name, $sql);
	// if(!$rs->EOF())
	// {
		// $bloco_id = $rs->db_data['bloco_id'];
		// $bloco_nome = $rs->db_data['bloco_nome'];
		// $bloco_cliente = $rs->db_data['bloco_cliente'];
		// $bloco_data = $rs->db_data['bloco_data'];
		// $bloco_proposta_num = $rs->db_data['bloco_proposta_num'];
	// }
	// $rs->db_close();


	// $sql = '
		// SELECT
			 // '.implode(',', $GLOBALS['CMPS_MODULO_DADOS_LANG']).'
		// FROM
			// '.TBMODULO_LANG.'
		// WHERE
			// bloco_id='.$_GET['bloco_id'].'

		// ';

	// $dados->db_open($db_link,$DB_name,$sql);
	// $total = $dados->db_rows;
	// if(!$dados->EOF())
	// {
		// $a=0;
		// while(!$dados->EOF())
		// {
			// $a++;
			// $arr_acab_save[$a]['bloco_linhas_id'] = $dados->db_data['bloco_linhas_id'];
			// $arr_acab_save[$a]['bloco_id'] = $dados->db_data['bloco_id'];
			// $prod_id = htmlentities($dados->db_data['prod_id']);
			// $produto_desc = htmlentities($dados->db_data['produto_desc']);
			// $prod_qtd1 = $dados->db_data['prod_qtd1'];
			// $prod_qtd2 = $dados->db_data['prod_qtd2'];
			// $preco1 = $dados->db_data['preco1'];
			// $preco2 = $dados->db_data['preco2'];

			// $bloco_prazo_entrega = $dados->db_data['bloco_prazo_entrega'];

			// $prods = get_produtc_info($prod_id);
			// $arr_imagens = get_product_images($prod_id);
			// $img = '';

			// echo $GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_1.'.$arr_imagens[0]['img_ext'].'<br>';
			// if(!empty($arr_imagens) && $arr_imagens[0] != '' && file_exists($GLOBALS['site_shell_url'].'files/products/'.$prod_id.'_1.'.$arr_imagens[0]['img_ext']))
			// {
				// $img = '<img src="'.$GLOBALS['site_http_url'].'files/products/'.$prod_id.'_1.'.$arr_imagens[0]['img_ext'].'" border="0" />';
			// }

			// $sql = 'SELECT Texto1 FROM products_lang WHERE ID_PAI='.$prod_id;
			// $rs->db_open($db_link, $DB_name, $sql);
			// if(!$rs->EOF())
			// {
				// $Texto1 = $rs->db_data['Texto1'];
			// }
			// $rs->db_close();


			// $tbl .= '<table cellpadding="0" cellspacing="0" width="1122" style="background:#FFFFFF">';
			// $tbl .= '<tr><td><img src="templates/topo.png" border="0" /></td></tr>';
			// $tbl .= '<tr><td><img src="templates/blank.gif" height="2" width="1" border="0" /></td></tr>';
			// $tbl .= '<tr><td  align="center">';
			// $tbl .= '<table cellpadding="0" cellspacing="0" width="1115" style="background:#d9d9d9">';
			// $tbl .= '<tr><td height="25" align="center"><b>Cliente: '.$bloco_cliente.'</b></td><td align="center"><b>Nome: '.$bloco_nome.'</b></td><td align="center"><b>Data: '.$bloco_data.'</b></td><td align="center"><b>Proposta Nº '.$bloco_proposta_num.'</b></td></tr>';
			// $tbl .= '</table><br>';
			// $tbl .= '<table cellpadding="0" cellspacing="0" width="1071" style="border:#3e80cf 2px solid">';
			// $tbl .= '<tr><td align="center" colspan="2" height="500">'.$img.'</td></tr>';
			// $tbl .= '</table>';
			// $tbl .= '<table cellpadding="0" cellspacing="0" width="1071" style="border:#3e80cf 2px solid;border-top:none">';

			// $tbl .= '<tr><td align="center" width="464"><b>'.$produto_desc.$Texto1.'</b></td>';
			// $tbl .= '<td align="center">';
			// $tbl .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="border-left:#3e80cf 2px solid">';
			// $tbl .= '<tr><td height="25" align="center" width="150" style="border-right:2px solid #3e80cf;"><b>Prazo de entrega</b></td><td align="center" colspan="2"><b>'.$bloco_prazo_entrega.'</b></td></tr>';
			// $tbl .= '<tr><td height="25" align="center" style="border-right:2px solid #3e80cf;border-top:2px solid #3e80cf;"><b>Quantidade</b></td><td align="center" style="border-right:2px solid #3e80cf;border-top:2px solid #3e80cf;"><b>'.$prod_qtd1.' Un.</b></td><td align="center" style="border-top:2px solid #3e80cf;"><b>'.$prod_qtd2.' Un.</b></td></tr>';
			// $tbl .= '<tr><td height="25" align="center" style="border-right:2px solid #3e80cf;border-top:2px solid #3e80cf;"><b>Preço/Un</b></td><td align="center" style="border-right:2px solid #3e80cf;border-top:2px solid #3e80cf;"><b>'.numberformat($preco1,1).'</b></td><td align="center" style="border-top:2px solid #3e80cf;"><b>'.numberformat($preco2,1).'</b></td></tr>';
			// $tbl .= '</table>';

			// $tbl .='</td></tr>';
			// $tbl .= '</table>';
			// $tbl .= '</td></tr>';


			// $tbl .= '</table>';
			// if($total!=$a)$tbl .= '<pagebreak />';
			// $dados->db_next();
		// }
	// }
	// $dados->db_close();

	// include('mpdf/mpdf.php');

	// $mpdf=new mPDF('c','A4-L',10,'chelvetica',0,0,0);
	// $mpdf->SetTitle('MARKAMOS');
	// $mpdf->SetHTMLFooter('<table cellpadding="0" cellspacing="0" width="1071" align="center"><tr><td>Todo o contéudo deste documento e eventuais anexos são susceptíveis de conter informação sujeita a sigilo profissional, ao regime legal de proteção de dados pessoais, de direitos de autor ou outro, pelo que a sua divulgação depende de autorização da MARKAMOS comunicação global</td></tr></table><div style="text-align: right; font-weight: bold;padding-right:20px">{PAGENO}</div>');
	// $html = $tbl;
	// $mpdf->WriteHTML($html);
	// $mpdf->Output($GLOBALS['site_shell_url'].'files/cache/orcamento'.$orcamento_id.'.pdf','F');
}

function linhas_save($ID_PROD=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$bloco_linhas_id = $_POST['bloco_linhas_id'];

	$produto = $_POST['produto'];
	$cor = $_POST['cor'];
	$produto_desc = $_POST['produto_desc'];
	// $bloco_prazo_entrega = $_POST['bloco_prazo_entrega'];
	// $prod_qtd1 = $_POST['prod_qtd1'];
	// $preco1 = $_POST['preco1'];
	// $prod_qtd2 = $_POST['prod_qtd2'];
	// $preco2 = $_POST['preco2'];


	// print_r($ambiente);

	$rs = new DataObj; $rs2=new DataObj;

	$sql = "
			UPDATE galeria_products
			SET
				 flag = 0
			WHERE
				ID_Galeria='".$ID_PROD."'
			";
			$res = Runquery($db_link, $DB_name, $sql);

	for($a=0; $a<count($bloco_linhas_id); $a++)
	{

			$sql = "SELECT ID FROM galeria_products WHERE ID='".$bloco_linhas_id[$a]."' AND ID_Galeria='".$ID_PROD."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$saveid = $rs->db_data['ID'];
				$sql = "
						UPDATE galeria_products
						SET
							ID_Product='".$produto[$a]."',
							ID_COR='".$cor[$a]."',
							Product_Desc='".addslashes($produto_desc[$a])."',
							flag=1
						WHERE
							ID='".$bloco_linhas_id[$a]."'
							AND ID_Galeria='".$ID_PROD."'
						";
				$res = Runquery($db_link, $DB_name, $sql);
			}
			else
			{
				$sql = "
						INSERT INTO
							galeria_products
							(
								ID_Galeria,
								ID_Product,
								ID_COR,
								Product_Desc,
								Criado,
								flag
							)
							VALUES
							(
								'".$ID_PROD."',
								'".$produto[$a]."',
								'".$cor[$a]."',
								'".addslashes($produto_desc[$a])."',
								'".date('Y-m-d H:i:s')."',
								1
							)
						";
				$res = Runquery($db_link, $DB_name, $sql);
				// $obj_id = db_get_last_insert_id($db_link);
			}

			// echo $sql.'<br>';
			$rs->db_close();

	}

		$sql = "
			DELETE FROM
				galeria_products
			WHERE
				ID_Galeria='".$ID_PROD."'
				AND flag=0
			";
			$res = Runquery($db_link, $DB_name, $sql);
}

function gera_lista_grupos_users($blocos_users)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$and_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$blocos_users2 = explode(';',$blocos_users);

	global $admin_id;
	$where = 'WHERE Grupo_ID=5 '.$and_loja;
	$rs->db_open($db_link,$DB_name,'Select ID, Nome from logins '.$where);
	echo '<select name="listagrupos[]" id="listagrupos" multiple="multiple"  class="chzn-select">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if(in_array($rs->db_data['ID'],$blocos_users2)) echo ' selected ';
		echo '>';
		echo $rs->db_data['Nome'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}

function GET_BLOCOS_ATIVIDADES()
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs = new DataObj;$rs2 = new DataObj; $return = '';
	$sql = '
			SELECT
				b.bloco_id, b.bloco_ext, b.bloco_data, b.bloco_open, bl.bloco_titulo, bl.bloco_resumo, bl.bloco_texto
			FROM
				blocos b
				INNER JOIN blocos_lang bl ON bl.bloco_id = b.bloco_id
			WHERE
				bl.bloco_lang = "'.$GLOBALS['admin_lang'].'"
				AND b.bloco_categorias="73;"
				AND b.bloco_id_pai=0
			ORDER BY
				b.bloco_ordem ASC, b.bloco_data DESC
			';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		$a = 6;
		$a2 = 0;

		$return .= '<div class="dashboard">';
		$return .= '<ul>';

		while(!$rs->EOF())
		{

			$bloco_id = $rs->db_data['bloco_id'];
			$bloco_open = $rs->db_data['bloco_open'];
			$bloco_titulo = $rs->db_data['bloco_titulo'];
			$bloco_data = $rs->db_data['bloco_data'];
			$bloco_resumo = $rs->db_data['bloco_resumo'];
			$bloco_texto = $rs->db_data['bloco_texto'];
			$bloco_ext = $rs->db_data['bloco_ext'];


			$img_file = 'files/'.FOLDER.'/'.$bloco_ext;
			$ext = pathinfo($img_file, PATHINFO_EXTENSION);
			$img_file_med = str_replace('.'.$ext, '_dash.'.$ext, $img_file);

			if(file_exists($GLOBALS['site_shell_url'].$img_file)) $original = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file)); // data/hora em que foi modificada/criada
			if(file_exists($GLOBALS['site_shell_url'].$img_file_med)) $miniatura = date("Y-m-d H:i:s",filemtime($GLOBALS['site_shell_url'].$img_file_med)); //data/hora em que foi modificada/criada

			if((!file_exists($GLOBALS['site_shell_url'].$img_file_med) || $miniatura<$original) && file_exists($GLOBALS['site_shell_url'].$img_file))
			{
				if(resize_image($GLOBALS['site_shell_url'].$img_file,100,0,true, $GLOBALS['site_shell_url'].$img_file_med)){myimagefilledpolygon($GLOBALS['site_shell_url'].$img_file_med, $GLOBALS['site_shell_url'].$img_file_med, '#e31d1a');}
			}


			if($a==$a2)
			{
				$return .= '</ul></div><div class="dashboard"><ul>';
				$a2 = 0;
			}

			if($bloco_open > 0) $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_open.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			else $URL_Link = $GLOBALS['site_http_url'].'bl-'.$bloco_id.'-'. str_replace(' ','',limpa_special($bloco_titulo)).'-'.$GLOBALS['site_lang'];
			$URL_Link = str_replace($GLOBALS['site_http_url'],'',$URL_Link);

			$return .= '<li style="cursor:pointer" onclick="window.open(\'sa_blocos/pagina.php?bloco_id='.$bloco_id.'\',\'_self\')"><div style="height:100px"><img src="'.$GLOBALS['site_http_url'].$img_file_med.'" border="0" /></div>';
			$return .= '<div style="height:20px;display: table-cell;vertical-align:middle;text-align: center;width:130px">';
			$return .= ''.$bloco_titulo.'</div>';
			$return .= '<div class="clear"></div>';
			$return .= '</li>';

			$a2++;
			$rs->db_next();
		}
		$return .= '</ul></div>';
	}
	$rs->db_close(1);

	return $return;
}
function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT bloco_id, bloco_id_pai FROM $table WHERE bloco_id=$id_org";
		$rs->db_open($db_link,$DB_name,$sql);
		$codigo=$rs->db_data["bloco_id"];
		$id_org1=$rs->db_data["bloco_id_pai"];
		$rs->db_close();
		$sql="SELECT bloco_titulo FROM $table"."_lang WHERE bloco_id=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql);
		$C=stripslashes($rs1->db_data["bloco_titulo"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" - <a href=\"$pagina"."?bloco_id=$codigo&do=".date('YmdHis')."\" class=\"brancoh\">$C1</a>";
	}
	return $str;
}

function gera_lista_path($id_org=0,$T,$Tlang,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="0" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="'.TBID.'_pai" style="width:350px" class="chzn-select">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}

function gera_lista_path_open($id_org=0,$T,$Tlang,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT * FROM blocos WHERE bloco_id_pai="'.$_GET['bloco_id'].'" ORDER BY bloco_ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="bloco_open" style="width:350px" class="chzn-select">';
	if($id_org==0) echo '<option value="0" selected>- Esta -</option>';
	else echo '<option value="0">- Esta -</option>';
    while(!$rs->EOF())
    {
	    if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data["bloco_id"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id='.$rs->db_data['bloco_id'].' and bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>'.$rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
		gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function gera_lista_path_open2($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from blocos where bloco_id_pai="'.$id_start.'" ORDER BY bloco_ordem ASC');
    while(!$rs->EOF())
    {
       if(isset($_GET['bloco_id']) && $_GET['bloco_id'] == $rs->db_data['bloco_id']) $des = 'disabled="disabled"';
		else $des = '';

		echo '<option '.$des.' value="'.$rs->db_data['bloco_id'].'"';
		if($id_org==$rs->db_data['bloco_id']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from blocos_lang where bloco_id="'.$rs->db_data['bloco_id'].'" AND bloco_lang="'.$GLOBALS['admin_lang'].'"');
		echo '>';
		$i=0;while($i<$c){echo '- ';$i++;}
		echo $rs2->db_data['bloco_titulo'].'</option>';
	    $rs2->db_close();
	    gera_lista_path_open2($rs->db_data['bloco_id'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();
}

function getBrands($arr_brd)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$result=array();

	$sql = '
			SELECT
				b.ID,
				b.img_ext,
				bo.*
			FROM
				brands b
				INNER JOIN brands_lang bo ON bo.ID_PAI=b.ID
			ORDER BY
				b.Ordem ASC
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	while(!$rs->EOF())
	{
		if(!in_array($rs->db_data['ID'], $arr_brd))
		{
			$brand_id=$rs->db_data['ID'];
			$brands=get_produtc_brand($brand_id);
			$brands['brand_id']=$brand_id;
			$result[]=$brands;
		}
		$rs->db_next();
	}
	$rs->db_close();

	return $result;
}
function gera_lista_grupos($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;



    echo '<select id="blocos_users" name="blocos_users[]" class="box chzn-select" multiple="multiple" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}
function gera_lista_brands_cat($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcas" name="listademarcas[]" class="box chzn-select" style="font-size:13px;width:300px">';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_lista_brands_list($arr_familias, $arr_brd)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

    echo '<select id="listademarcasx" name="listademarcasx" class="chzn-select" style="font-size:13px;width:150px">';
	echo '<option value="">TODOS</option>';
    for($a=0; $a<count($arr_familias); $a++)
	{
		echo '<option value="'.$arr_familias[$a]['brand_id'].'"';
		if(in_array($arr_familias[$a]['brand_id'], $arr_brd)) echo ' selected="selected">'.$arr_familias[$a]['marca_nome'].'</option>';
		else echo '>'.$arr_familias[$a]['marca_nome'].'</option>';
    }
    echo '</select>';
}

function gera_brands_name($marca_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

   $sql = '
			SELECT
				bo.Nome
			FROM
				brands_lang bo
			WHERE
				ID_PAI="'.$marca_id.'"
				';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	if(!$rs->EOF()) $brand_nome = $rs->db_data['Nome'];
	$rs->db_close();

	return $brand_nome;
}
function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= '
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{


			if($campos != TBID)
			{

				// echo '<pre style="color:#FFF">'.$campos.'</pre><br />';
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';

				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= ' WHERE '.TBID.'='.$_POST[TBID];
	}
	$res = Runquery($db_link, $DB_name, $sql);
	if($res)
	{
		if(isset($_POST['ID']) && $_POST['ID'] != 0) $ID_Cliente = $_POST['ID'];
		if(isset($_POST['Grupo_ID'])) $Grupo_ID = $_POST['Grupo_ID'];
		if(isset($_POST['Nif']) && $_POST['Nif'] != '') $Nif = addslashes($_POST['Nif']);
		if(isset($_POST['Nome']) && $_POST['Nome'] != '') $Nome = addslashes($_POST['Nome']);
		if(isset($_POST['Sobrenome']) && $_POST['Sobrenome'] != '') $Sobrenome = addslashes($_POST['Sobrenome']);
		if(isset($_POST['Apelido']) && $_POST['Apelido'] != '') $Apelido = addslashes($_POST['Apelido']);
		if(isset($_POST['Username']) && $_POST['Username'] != '') $Username = addslashes($_POST['Username']);
		if(isset($_POST['Empresa']) && $_POST['Empresa'] != '') $Empresa = addslashes($_POST['Empresa']);
		if(isset($_POST['Morada']) && $_POST['Morada'] != '') $Morada = addslashes($_POST['Morada']);
		if(isset($_POST['Cidade']) && $_POST['Cidade'] != '') $Cidade = addslashes($_POST['Cidade']);
		if(isset($_POST['Codigo_Postal']) && $_POST['Codigo_Postal'] != '') $Codigo_Postal = addslashes($_POST['Codigo_Postal']);
		if(isset($_POST['Telefone']) && $_POST['Telefone'] != '') $Telefone = addslashes($_POST['Telefone']);
		if(isset($_POST['Telefone2']) && $_POST['Telefone2'] != '') $Telefone2 = addslashes($_POST['Telefone2']);

		$sql ="UPDATE rmas SET cliente_nome = '$Nome',
								cliente_sobrenome = '$Sobrenome',
								cliente_apelido = '$Apelido',
								cliente_morada = '$Morada',
								cliente_empresa = '$Empresa',
								cliente_nif = '$Nif',
								cliente_username = '$Username',
								cliente_cp = '$Codigo_Postal',
								cliente_telefone1 = '$Telefone',
								cliente_telefone2 = '$Telefone2',
								Grupo_ID = $Grupo_ID
								WHERE ID_Cliente = $ID_Cliente AND cliente_nif = '$Nif'";

		$res = Runquery($db_link, $DB_name, $sql);

	}
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	if($res) submeteDadosLinguas($id_acao);

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}

function submeteDadosLinguas($modulo_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	// $modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	$rs = new DataObj;
	$sql='SELECT '.TBID.' FROM '.TBMODULO_LANG.' WHERE ID_PAI='.$modulo_id.' AND Lang="'.$GLOBALS['admin_lang'].'"';
	// echo $sql.'<br />';
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF()) $modulo_acao = 'update'; else $modulo_acao = 'insere';
	$rs->db_close();

	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS_LANG'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO_LANG;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != 'ID'){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != 'ID')
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= '"'.$modulo_id.'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= '
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO_LANG.' SET ';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID && $campos != 'Lang' && $campos != 'ID')
			{
				if(substr_count($campos,'_ext'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif($campos=='ID_PAI')
				{
					$sql .= $campos.'="'.$modulo_id.'"';
				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= 'WHERE ID_PAI='.$_POST[TBID].' AND Lang="'.$GLOBALS['admin_lang'].'"';
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';
}

function submeteimagens($bloco_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_duplicar = (isset($_POST['duplicar']) && $_POST['duplicar'] > 0) ? 1 : 0;
	if($modulo_duplicar == 0)
	{
		$modulo_id = $bloco_id;
		$CMPS_MODULO = $GLOBALS['CMPS_MODULO_IMGS'];

		// echo '<pre style="color:#FFF">';
		$query = '';

		foreach($_POST['RENOVACAO_ID'] as $field_num2)
		{
			$codis = explode(';', $field_num2);
			$field_num = $codis[0];
			$field_id = $codis[1];

			$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

			if($modulo_acao=='insere')
			{
				$sql = 'INSERT INTO '.TBIMAGENS;
				$sql .= '(';
				$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBIMGID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
				$sql .= ')VALUES(';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if($i>0){$sql .= ', ';}

						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							$sql .= '"'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							$sql .= '"'.$field_num.'"';
						}
						elseif(substr_count($campos,'bloco_id'))
						{
							$sql .= '"'.$modulo_id.'"';
						}
						else
						{
							$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ')';
			}
			elseif($modulo_acao=='update')
			{
				$sql = 'UPDATE '.TBIMAGENS.' SET ';
				$i=0;
				foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
				{
					if($campos != TBIMGID)
					{
						if(substr_count($campos.$field_num,'_ext'))
						{
							$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
							if($i>0 && $nome_ficheiro){$sql .= ', ';}
							if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
						}
						elseif(substr_count($campos,'_rel'))
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$field_num.'"';
						}
						elseif($campos=='bloco_id')
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.$modulo_id.'"';
						}
						else
						{
							if($i>0){$sql .= ', ';}
							$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
						}
						$i++;
					}
				}
				$sql .= ' WHERE '.TBIMGID.'='.$field_id;
			}
			$res = Runquery($db_link, $DB_name, $sql);
			// echo $sql.'<br />';
		}
	}
	// echo '</pre>';
}
function submeteAverbamentos($id_marca)
{
	// echo '<pre style="color:#FFF">';
	tabs_submete_av('ENDERECO_ID');
	tabs_submete_av('NOME_ID');
	tabs_submete_av('TRANSMISSAO_ID');
	tabs_submete_av('FUSAO_ID');
	tabs_submete_av('LICENSA_ID');
	// echo '</pre>';
}

function submeteOposicoes($id_marca)
{
	echo '<pre style="color:#FFF">';
	tabs_submete_op('OPOSICAO_ID');
	tabs_submete_op('RESPOSTA_ID');
	echo '</pre>';
}

function tabs_submete_op($tab)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_OPOSICAO'];

	foreach($_POST[$tab] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBOPOSICOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBOPOSICAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBOPOSICOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBOPOSICAOID.'='.$field_id.' AND objeto_bloco="'.$field_bloco.'"';
		}
		// echo $sql.'<br />';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteReclamacoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECLAMACAO'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECLAMACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECLAMACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECLAMACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECLAMACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECLAMACAOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteRecursos($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECURSOS'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECURSO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECURSOS;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECURSOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECURSOS.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECURSOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submete_ficheiro($file_name, $nome_ficheiro_old)
{
	$img_path = '../../files/'.FOLDER.'/';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;
	if($Img_peq["name"]!="")
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], ($pos+1), strlen($Img_peq['name']) + 1 - $pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old)) unlink($img_path.$nome_ficheiro_old);
		$token = date('YmdHis').uniqId();
		$nome_ficheiro = $token.".".$img_ext;

		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return $nome_ficheiro;
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?php

				echo '<input class="botao" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="botao" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="botao" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onMouseUp="window.open('pagina_trata.php?ID=<?php echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="botao" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	</div>
<?php
}
function show_fast_tools()
{
	global $ID, $admin_id, $BOTOES_L;
	?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td height="35px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		$a=1;
		foreach($BOTOES_L as $key => $value)
		{
			// echo $a;
			if($a==2 && isset($_GET[TBID]) && $_GET[TBID] > 0)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}
			elseif($a!=2)
			{
			?>
				<tr>
				<td class="preto" align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
				</tr>
			<?php
			}

			$a++;
		}
	}

	/*<tr>
	<td class="preto" align="center" style="background:none">&nbsp;</td>
	</tr>
	<tr>
	<td class="preto" align="center" style="background:#EAEAEA"><?php include_once 'easycreate.php';?></td>
	</tr>*/?>
	</table>
<?php }
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td width="5%" align="center">&nbsp;</td>
		    <td width="95%" valign="middle" style="font-weight:bold;color:#FFF"><?php echo MODULO?></td>
		</tr>
	</table>

<?php }
function show_page_bottom_tools()
{?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="150" height="35" align="right" style="font-weight:bold">Para os Selecionados:&nbsp;</td>
<td height="35" width="275"><select name="ACCOES" id="ACCOES" class="box">
<option value="">-</option>
<?php if(isset($GLOBALS['BOTOES_B']) && !empty($GLOBALS['BOTOES_B'])){
foreach($GLOBALS['BOTOES_B'] as $value => $key){
echo '<option value="'.$key.'">'.$value.'</option>';
}}?>
</select></td>
<td  height="35" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
</tr>
</table>
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['ACCOES']==1) // REMOVER REGISTO
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
		elseif($_POST['ACCOES']==3) // ATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==2) // DESATIVAR REGISTOS
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_POST['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_POST as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
	elseif(isset($_GET['ACCOES']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_GET['ACCOES']==1) // REMOVER REGISTO
		{
			$str_id = $_GET['bloco_id'];
			if($str_id > 0)
			{
				$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);
				$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
				$res = Runquery($db_link,$DB_name,$sql);

			}
		}
		elseif($_GET['ACCOES']==3) // ATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_visivel=1 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==2) // DESATIVAR REGISTO
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'UPDATE '.TBMODULO.' SET bloco_visivel=0 WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					if(!$res) echo db_error($db_link) . $sql . '<br>';
				}
			}
		}
		elseif($_GET['ACCOES']==4) // REMOVER REGISTOs
		{
			foreach($_GET as $varName=> $value)
			{
				$str_id = substr($varName,strpos($varName,"_") + 1);
				if($str_id > 0 && substr_count($varName,"chk_") > 0)
				{
					$sql = 'DELETE FROM '.TBMODULO.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBMODULO_lang.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = 'DELETE FROM '.TBIMAGENS.' WHERE '.TBID.'='.$str_id;
					$res = Runquery($db_link,$DB_name,$sql);
				}
			}
		}
	}
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$and_loja;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;

	$pesq = '';
	if(isset($_GET['p']) && $_GET['p']==1)
	{
		if(isset($_GET['Nome']) && $_GET['Nome']!='') $pesq .= ' AND b.Nome like "%'.trim($_GET['Nome']).'%"';
		if(isset($_GET['Apelido']) && $_GET['Apelido']!='') $pesq .= ' AND b.Apelido like "%'.trim($_GET['Apelido']).'%"';
		if(isset($_GET['Nif']) && $_GET['Nif']!='') $pesq .= ' AND b.Nif like "%'.trim($_GET['Nif']).'%"';
		if(isset($_GET['Email']) && $_GET['Email']!='') $pesq .= ' AND b.Username like "%'.trim($_GET['Email']).'%"';
		if(isset($_GET['Telefone']) && $_GET['Telefone']!='') $pesq .= ' AND b.Telefone like "%'.trim($_GET['Telefone']).'%"';
		if(isset($_GET['Telefone2']) && $_GET['Telefone2']!='') $pesq .= ' AND b.Telefone2 like "%'.trim($_GET['Telefone2']).'%"';
		if(isset($_GET['Empresa']) && $_GET['Empresa']!='') $pesq .= ' AND b.Empresa like "%'.trim($_GET['Empresa']).'%"';
	}
	$sql = 'SELECT
				*
			FROM
				'.TBMODULO.' b
			WHERE
				rma_associado = 1
				'.$pesq.'
				'.$and_loja.'
			ORDER BY
				b.Nome ASC, b.Apelido DESC';

	$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();
	// echo $sql;
	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>
		<table width="100%" height="50px" border=1 cellspacing=0 cellpadding=0 class="p1" bordercolor="#f0f0f0">
		<tr bgcolor="#FFFFFF">
		<td width="60" height="35" align="center">&nbsp;</td>

		<td width="110" valign="middle" align="left">&nbsp;<b>Nome</b></td>
		<td width="110" valign="middle" align="left">&nbsp;<b>Sobrenome</b></td>
		<td width="110" valign="middle" align="left">&nbsp;<b>Apelido</b></td>
		<td width="130" valign="middle" align="left">&nbsp;<b>Empresa</b></td>
		<td width="100"  valign="middle" align="center"><b>Nif</b></td>
		<td valign="middle">&nbsp;<b>Email</b></td>
		<td  width="80" valign="middle" align="center">&nbsp;<b>Telefone 1</b></td>
		<td  width="80" valign="middle" align="center">&nbsp;<b>Telefone 2</b></td>
		<td width="10" valign="middle">&nbsp;</td>

		</tr>

		<tr bgcolor="#FFFFFF">
		<td height="35" align="center"><label class="label_check2" for="chk_all"><input type="checkbox" name="chk_all" id="chk_all" value="1" class="cb" /></label></td>

		<td valign="middle" align="left"><input type="text" style="width:90%" name="Nome" id="Nome" class="box" value="<?php if(isset($_GET['Nome'])) echo trim($_GET['Nome']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Sobrenome" id="Sobrenome" class="box" value="<?php if(isset($_GET['Sobrenome'])) echo trim($_GET['Sobrenome']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Apelido" id="Apelido" class="box" value="<?php if(isset($_GET['Apelido'])) echo trim($_GET['Apelido']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Empresa" id="Empresa" class="box" value="<?php if(isset($_GET['Empresa'])) echo trim($_GET['Empresa']); ?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Nif" id="Nif" class="box" value="<?php if(isset($_GET['Nif'])) echo trim($_GET['Nif']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Email" id="Email" class="box" value="<?php if(isset($_GET['Email'])) echo trim($_GET['Email']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Telefone" id="Telefone" class="box" value="<?php if(isset($_GET['Telefone'])) echo trim($_GET['Telefone']);?>" /></td>
		<td valign="middle" align="left"><input type="text" style="width:90%" name="Telefone2" id="Telefone2" class="box" value="<?php if(isset($_GET['Telefone2'])) echo trim($_GET['Telefone2']);?>" /></td>
		<td valign="middle" align="center" colspan="2" width="50"><img src="img/reload.png" title="Pesquisar" border="0" onclick="pesquisa('<?php echo ((isset($_GET[TBID]) && $_GET[TBID] > 0) ? 'pagina.php?'.TBID.'='.$_GET[TBID] : '')?>'); return false;" style="cursor:pointer" /></td>
		</tr>

		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$bloco_visivel = 1;

			$Username = strtolower($rs->db_data["Username"]);
			$Nome = $rs->db_data["Nome"];
			$Visivel = $rs->db_data["Estado"];
			$Apelido = $rs->db_data["Apelido"];
			$Sobrenome = $rs->db_data["Sobrenome"];
			$Empresa = $rs->db_data["Empresa"];
			$Nif = $rs->db_data["Nif"];
			$Telefone = $rs->db_data["Telefone"];
			$Telefone2 = $rs->db_data["Telefone2"];

			if($Visivel==1)
			{
				if($cor=='#c8ecfa')	{$cor="#def2fa";$class="r1";$n="1";}
				else				{$cor="#c8ecfa";$class="r2";$n="2";}
			}
			else
			{
				if($cor=='#FF9999')	{$cor="#FF6666";$class="r1";$n="1";}
				else				{$cor="#FF9999";$class="r2";$n="2";}
			}

			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">


			<td align="center"><label class="label_check2" for="chk_<?php echo $id?>"><input type="checkbox" name="chk_<?php echo $id?>" id="chk_<?php echo $id?>" value="<?php echo $id?>" class="cb" /></label></td>
			<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Nome; ?></a></td>
			<td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Sobrenome; ?></a></td>
			<td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Apelido; ?></a></td>
			<td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Empresa; ?></a></td>

			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Nif;?></a></td>
			<td valign="middle"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Username;?></a></td>
			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Telefone;?></a></td>
			<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="pagina.php?<?php echo TBID?>=<?php echo $id;?>&Dump=<? echo uniqid("");?>"><?php echo $Telefone2;?></a></td>

			<td valign="middle" height="32"><?php /*<a title="Remover" class="lista_links" onclick="if(confirm('Tem a certeza que pretente remover este registo?')) return true; else return false;" href="list.php?<?php echo TBID?>=<?php echo $id;?>&ACCOES=1&Dump=<? echo uniqid("");?>"><img src="img/delete-item.png" border="0" /></a>*/?></td>

			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{

			if(isset($_GET[TBID]) && $_GET[TBID] > 0)  $url_pages = 'pagina.php?'.TBID.'='.$_GET[TBID].'&DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_ativo='.urlencode($_GET['bloco_ativo']);
			else $url_pages = 'list.php?DESIGNACAO='.urlencode($_GET['DESIGNACAO']).'&bloco_ativo='.urlencode($_GET['bloco_ativo']);

			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}

		return 1;
	}
	elseif($pai==0)
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php
	}
	$rs->db_close();
}
?>