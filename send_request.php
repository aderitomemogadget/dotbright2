<?php
	header('Content-Type: text/html; charset=utf-8');
	$path = 'core/';
	include $path.'core_files.php';

	$is_popup=1;


	$return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$id_forms = (isset($_POST['id_forms']) && intval($_POST['id_forms']) > 0) ? intval($_POST['id_forms']) : 0;
	$is_popup = ((isset($_POST['popup']) && $_POST['popup'] == 1) || (isset($_POST['popup']) && $_POST['popup'] == 1)) ? 1 : 0;

	if($id_forms === 0)
	{
		//Não foi encontrado o Formulário
		header('Location: '.$GLOBALS['site_http_url']);
		exit();
	}

	$token=$_POST['token'.$id_forms];

	$ip='';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }


    $spam=0;
    if($_SESSION['ip_usado']==$ip && $_SESSION['data_usada']==date('YmdHi'))$spam=1;// Se tiverem sido submetidos dados em menus de 1 minuto é spam


/*if($is_popup == 1) 	require_once 'layout/header_pop.php';
else 				require_once 'layout/header.php';*/

/*if(mb_strtolower($_SESSION['securimage_code_value'], 'UTF-8') == mb_strtolower($_POST["captcha_code"], 'UTF-8'))*/
if($spam==0 && $ip!='' && $_POST['URL']=='' && $_POST['humman'.$id_forms]=='1' && $token!='' && (!isset($_SESSION['tokens_usados']) || !in_array($token,$_SESSION['tokens_usados'])  ))
{
	//unset($_SESSION['securimage_code_value']);
	//Agora vamos guardar o token para impedir o formulario de ser resubmetido novamente
    $_SESSION['tokens_usados'][]=$token;
	$_SESSION['ip_usado']=$ip;
    $_SESSION['data_usada']=date('YmdHi');


	$db_link 	= db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$dados 		= new DataObj();
	$dados2 	= new DataObj();
	$dados3 	= new DataObj();

	$sql = 'SELECT Email,Name,assunto,Texto2,Texto3,Texto4 FROM forms WHERE ID="'.$id_forms.'"';
	$dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$dados2->EOF())
	{
		$mailto 		= $dados2->db_data['Email'];
		$name 			= $dados2->db_data['Name'];
		$m_assunto 		= $dados2->db_data['assunto'];
		$txt_f_ok 		= limpa_editor_lixo($dados2->db_data['Texto2']);
		$txt_f_erro		= limpa_editor_lixo($dados2->db_data['Texto3']);
		$txt_f_email 	= limpa_editor_lixo($dados2->db_data["Texto4"]);
		$txt_f_email 	= str_replace('../../files/', $GLOBALS['site_http_url'].'files/', $txt_f_email);
		$txt_f_email 	= str_replace('[nome]', $name, $txt_f_email);
		$txt_f_email 	= str_replace("\n", '<br>', $txt_f_email);
	}
	$dados2->db_close();

	$campo_id_pai = guarda_bd_form($id_forms);

	$mailbody = '<div style="font-family:Arial;font-size:12px">';
	$sql = 'SELECT ID,ID_ORG,Name,Muda,Tipo FROM form_fields WHERE ID_ORG="'.$id_forms.'" ORDER BY Ordem';
	$dados->db_open($db_link, $GLOBALS['DB_name'], $sql);
	while(!$dados->EOF())
	{
		$campo_id 		= $dados->db_data['ID'];
		$campo_id_org 	= $dados->db_data['ID_ORG'];
		$campo 			= $dados->db_data['Name'];
		$linha 			= $dados->db_data['Muda'];
		$tipo 			= $dados->db_data['Tipo'];

		if($tipo == 6)
		{
			$contador 	= $_POST['field'.$campo_id.'_counter'];
			$valor 		= $_POST['field'.$campo_id.'_0'];
			$valor = protege($valor);
			$mailbody 	.= '<br><br>'.$campo.':<br>';
			for($k = 0; $k < $contador; $k++)
			{
				$variavel = $_POST['field'.$campo_id.'_'.$k];
				$variavel = protege($variavel);
				$valor 	.= "{}".$variavel;
				if($k > 0)	$mailbody .= ', '.$variavel;
				else 		$mailbody .= $variavel;
			}
			guarda_bd_campos($campo_id_pai, $campo_id, $valor);
		}
		elseif($tipo == 9)
		{
			$img_path 	= 'files/forms/';
			$ext 		= pathinfo($_FILES['field'.$campo_id]['name'], PATHINFO_EXTENSION);

			$Documento['name'] = $_FILES['field'.$campo_id]['name'];
			$Documento['temp'] = $_FILES['field'.$campo_id]['tmp_name'];

			guarda_doc_bd_campos($campo_id_pai, $campo_id, $img_path, $Documento);

			$sql3 = 'SELECT MAX(ID) AS IDD FROM form_records_fields WHERE ID_ORG="'.$campo_id_pai.'"';
			$dados3->db_open($db_link, $GLOBALS['DB_name'], $sql3);
			if($ext!="") $xt = $dados3->db_data["IDD"].'_1.'.$ext;
			$dados3->db_close();
			if($ext!="") $mailbody .= '<b>File:</b><br> <a style="font-family:Arial; font-size:12px;text-decoration:underline" href="'.$GLOBALS['site_http_url'].'files/forms/'.$xt.'">'.$campo.'</a><br>';
		}
		elseif($tipo == 10)
		{
			$ano 		= date('Y');
			$mes 		= date('m');
			$dia 		= date('d');
			if($mes < 10) $mes = '0'.$mes;
			if($dia < 10) $dia = '0'.$dia;
			$mailbody .= '<br><br>Data:<br>'.$ano.'-'.$mes.'-'.$dia;
			guarda_bd_campos($campo_id_pai, $campo_id, $ano.'-'.$mes.'-'.$dia);
		}
		elseif($tipo == 13)
		{
			$valor = $_POST['field'.$campo_id];
			$valor = protege($valor);
			$mailbody .= '<br><br>Data:<br>'.$valor.'<br>';
			guarda_bd_campos($campo_id_pai, $campo_id, $valor);
		}
		elseif($tipo == 31)
		{
			$Email = $_POST['Email'.$campo_id];
			$mailbody .= '<br><br>Email:<br>'.$Email;
			guarda_bd_campos($campo_id_pai, $campo_id, $Email);
		}
		elseif($tipo != 33)
		{
			$valor = $_POST['field'.$campo_id];
			$valor = protege($valor);

			if($campo != "")
			{
				if($tipo == 0)
				{
					$mailbody .= '<br>'.$campo.'<br>'.$valor;
				}
				elseif($tipo == 7)
				{
					$arr = BuscarRegistoOpcoes_email($valor);
					if($arr['Email_Campo']) $mailto = $arr['Email_Campo'];

					$sql3 = 'SELECT Value FROM form_options WHERE ID="'.$valor.'"';
					$dados3->db_open($db_link, $GLOBALS['DB_name'], $sql3);
					if(!$dados3->EOF()) $Value = $dados3->db_data['Value'];
					else $Value = $valor;
					$dados3->db_close();

					$mailbody .= '<br><br>'.$campo.':<br>'.$Value;
				}
				else
				{
					$mailbody .= '<br><br>'.$campo.':<br>'.$valor;
				}
			}
			else
			{
				$mailbody .= $valor.'<br>';
			}

			guarda_bd_campos($campo_id_pai, $campo_id, $valor);
		}

		$dados->db_next();
	}
	$dados->db_close();
	$mailbody .= '</div>';
	$mailbody = str_replace("\n", '<br>', $mailbody);

	$mailsubject = 'Novo email enviado pelo site '.$GLOBALS['Nome_Site'];
	if($m_assunto) $mailsubject = $m_assunto;

	if($txt_f_email)
	{
		$body = preg_replace_callback('/{campo\\((\\d+)\\)}/', function($match){return return_campo($match[1]);}, $txt_f_email);
	}
	else
	{
		$body = $mailbody;
	}

	$headers = 'From: '.$FromName."\r\n";

	/*echo '<table width="100%" height="300px" border="0">';
	echo '<tr>';
	echo '<td valign="middle" align="center" style="text-align:center">';*/
	if(send_email($mailto, $mailsubject, $body, $headers))
	{
		if($txt_f_ok) put_content($txt_f_ok);
		else
		{
			echo $GLOBALS['key_enviar_email2'][$GLOBALS['site_lang']];
			echo $GLOBALS['key_back_home'][$GLOBALS['site_lang']];
		}
	}
	else
	{
		if($txt_f_erro) put_content($txt_f_erro);
		else
		{
			echo $GLOBALS['key_enviar_email3'][$GLOBALS['site_lang']];
			echo $GLOBALS['key_back_home'][$GLOBALS['site_lang']];
		}
	}
	/*echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '<script type="text/javascript">setTimeout(function(){window.open("'.$return_url.'","_self");},2500);</script>';*/
}
else
{
	echo html_entity_decode($GLOBALS['key_erro3'][$GLOBALS['site_lang']], ENT_COMPAT ,'UTF-8').'!';

	/*echo '<script type="text/javascript">alert("'.html_entity_decode($GLOBALS['key_erro3'][$GLOBALS['site_lang']], ENT_COMPAT ,'UTF-8').'!");</script>';
	echo '<script type="text/javascript">window.open("'.$return_url.'","_self");</script>';*/
}

//if($is_popup == 0) require_once 'layout/footer.php';

/* FUNÇÕES */
/**
 * Função para obter nome da combo
 * @param integer $id_pai ID do campo
 */
function BuscarRegistoOpcoes_email($id_pai = 0)
{
	$db_link 	= db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$result 	= array('Nome_Campo' => '', 'Email_Campo' => '');
	$rs 		= new DataObj();
	$sql 		= 'SELECT Value,Email FROM form_options WHERE (ID="'.$id_pai.'" || Value="'.$id_pai.'") ORDER BY Ordem,ID DESC';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs->EOF()) $result = array('Nome_Campo' => $rs->db_data['Value'], 'Email_Campo' => $rs->db_data['Email']);
	$rs->db_close();
	return $result;
}
function return_campo($numero)
{
	$db_link 	= db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$dados 		= new DataObj();
	$dados2 	= new DataObj();
	$dados3 	= new DataObj();
	$obj 		= new DataObj();

	$str 		= '';
	$ID_ORG 	= $GLOBALS['campo_id_pai'];
	$sql 		= 'SELECT ID,Tipo,col_labels,row_labels,matrix_type FROM form_fields WHERE ID="'.$numero.'"';
	$dados->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$dados->EOF())
	{
		$ID_Field		= $dados->db_data['ID'];
		$tipo 			= $dados->db_data['Tipo'];
		$col_labels 	= $dados->db_data['col_labels'];
		$row_labels 	= $dados->db_data['row_labels'];
		$matrix_type 	= $dados->db_data['matrix_type'];

		if($col_labels)
		{
			$rows 		= explode(";", $row_labels);
			$cols 		= explode(";", $col_labels);
			$col_width 	= 90 / count($cols);

			$str .= '<br>';
			$str .= '<table width=100% class="border1" border="0" cellpadding="2" cellspacing="0">';
			$str .= '<tr>';
			$str .= '<td width="10%">&nbsp;</td>';

			for($k = 0; $k < count($cols); $k++)
			{
				$m_titulo 	= $cols[$k];
				$xpos1 		= strpos($m_titulo, '[');
				$xpos2 		= strpos($m_titulo, ']');
				if($xpos1 && $xpos2)	$m_titulo = substr($m_titulo, 0, $xpos1);
				$str .= '<td width="'.$colwidth.'%" align="center">'.$m_titulo.'</td>';
			}
			$str .= '</tr>';

			for($k = 0; $k < count($rows); $k++)
			{
				$str .= '<tr>';
				$str .= '<td width="10%">'.$rows[$k].'</td>';

				for($n = 0; $n < count($cols); $n++)
				{
					$resp = (($k + 1) * 100) + ($n + 1);
					$sql3 = 'SELECT * FROM form_records_fields WHERE Name="'.$ID_Field.'_r'.($k + 1).'_c'.($n + 1).'" and ID_ORG="'.$ID_ORG.'"';
					$dados3->db_open($db_link, $GLOBALS['DB_name'], $sql3);
					$str .= '<td width='.$colwidth.'% align=center>';
					if($matrix_type == 1)
					{
						$str .= '<input type="checkbox" name="field'.$ID_Field.'_r'.($k + 1).'_c'.($n + 1).'" value="x"';
						if(!$dados3->EOF() && $dados3->db_data['Valor'] == 'x') $str .= " checked";
						$str .= '>';
					}
					else
					{
						$campo_aux 	= $cols[$n];
						$pos1 		= strpos($campo_aux, '[');
						$pos2 		= strpos($campo_aux, ']');
						if($pos1 && $pos2)
						{
							$campo_aux2 = substr($campo_aux, $pos1 + 1);
							$pos2 		= strpos($campo_aux2,']');
							$campo_aux2 = substr($campo_aux2, 0, $pos2);
							$valores 	= explode(",", $campo_aux2);
							if($valores[0] == 14)
							{
								// VALORES DE OUTRO FORMULARIO -14
								if(!$dados3->EOF() && $dados3->db_data['Valor'] <> '') $Valor = $dados3->db_data['Valor'];

								$sql = 'SELECT ID,Valor FROM form_records_fields WHERE Name="'.$valores[2].'"';
								$obj->db_open($db_link, $GLOBALS['DB_name'], $sql);
								if(!$obj->EOF())
								{
									$str .= '<select name="field'.$ID_Field.'_r'.($k+1).'_c'.($n+1).'">';
									$str .= '<option value="0"';
									if($Valor == "0") $str .= " selected";
									$str .= '>----</option>';
									while(!$obj->EOF())
									{
										$str.='<option value="'.$obj->db_data['ID'].'"';
										if($obj->db_data['ID'] == $Valor) $str.=" selected";
										$str .= '>'.$obj->db_data['Valor'].'</option>';
										if($Obrigatorio == 1)
										{
											$str .= '<font color=ff0000> *</font>';
											$obrigatorio = 1;
										}

										$obj->db_next();
									}
									$obj->db_close();
									$str .= '</select>';
								}
							}
						}
						else
						{
						  $str .= '<input class="matrix" name="field'.$ID_Field.'_r'.($k + 1).'_c'.($n + 1).'" style="WIDTH:100%;HEIGHT:100%"';
						  if(!$dados3->EOF() && $dados3->db_data['Valor'] <> '') $str .= ' value="'.$dados3->db_data['Valor'].'"';
						  $str .= '>';
						}
					}
					$str .= '</td>';
				}
				$str .= '</tr>';
			}
			$str .= '</table>';
		}
		else
		{
			$sql = 'SELECT Valor FROM form_records_fields WHERE ID_ORG="'.$ID_ORG.'" AND Name="'.$ID_Field.'"';
			$dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$dados2->EOF()) $Valor = stripslashes($dados2->db_data['Valor']);
			$dados2->db_close();

			if($tipo == 32)
			{
				$sql = 'SELECT Cabecalho FROM products_lang WHERE ID_PAI="'.$Valor.'" and Lang="'.$GLOBALS['site_lang'].'"';
				$dados2->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if(!$dados2->EOF()) $str = $dados2->db_data['Cabecalho'];
				else 				$str = '';
				$dados2->db_close();
			}
			else
			{
				$Valor = str_replace("{}"," | ", $Valor);
				$str .= $Valor;
			}
		}
	}
	$dados->db_close();

	return $str;
}


function protege($v)
{
    $v = htmlentities($v,ENT_NOQUOTES,'UTF-8',false);
    $v = str_replace(array('&lt;','&gt;'),array('<','>'), $v);
    return addslashes($v);
}