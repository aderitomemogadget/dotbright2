<?php
	if(isset($_GET['id_forms'])) $id_forms=intval($_GET['id_forms']);
	elseif(isset($_POST['id_forms'])) $id_forms=intval($_POST['id_forms']);

	// include_once $site_shell_url."external_api/captcha/contact-securimage.php";
	// $securimage=new Securimage();

	$captcha_valid=1;
	/*if (isset($_POST["recaptcha_response_field"]) && $_POST["recaptcha_response_field"])
	{
		require_once('external_api/recaptcha-1.11/recaptchalib.php');
		$privatekey="6LfHKPISAAAAAJ1AsIMuupTbMFxr4ag4tGwpx-EN";
        $resp=recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) { $captcha_valid=0;}
		else  $captcha_valid=1;
	}*/


	$token=$_POST['token'.$id_forms];
	$ss=$_POST['ss'.$id_forms];


	$ip='';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }


    $spam=0;
    //if($_SESSION['ip_usado']==$ip && $_SESSION['data_usada']==date('YmdHi'))$spam=1;// Se tiver submito dados a menus de 1 minuto é spam
    //if($_SESSION['ss'.$id_forms]!=$ss || $ss=='')$spam=1;// Se tiver submito dados a menus de 1 minuto é spam

	if($spam==0 && $ip!='' && $_POST['URL']=='' && $_POST['humman'.$id_forms]=='1' && $token!='' && (!isset($_SESSION['tokens_usados']) || !in_array($token,$_SESSION['tokens_usados'])  ))
	{
	    //Agora vamos guardar o token para impedir o formulario de ser resubmetido novamente
		//Agora vamos guardar o token para impedir o formulario de ser resubmetido novamente
	    $_SESSION['tokens_usados'][]=$token;
		$_SESSION['ip_usado']=$ip;
	    $_SESSION['data_usada']=date('YmdHi');

		if($captcha_valid == '')
		{
			if($site_lang=='en') echo show_alerta2('The code from Spam prevention is incorrect!', "erro");
			elseif($site_lang=='es') echo show_alerta2('El código de prevención de spam es incorrecto!', "erro");
			elseif($site_lang=='pt') echo show_alerta2('O código de prevenção spam está incorrecto!', "erro");
			// exit;
		}
		else{
			function getExt($sFileName)
			{
				$sTmp=$sFileName;
				while($sTmp != '')
				{
					$sTmp=strstr($sTmp,'.');
					if($sTmp!="")
					{
						$sTmp=substr($sTmp,1);
						$sExt=$sTmp;
					}
				}
				return strtolower($sExt);
			}

			$data=getdate();
			$ano=$data["year"];
			$mes=$data["mon"];
			$dia=$data["mday"];
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$dados2=new DataObj;
			$dados2->db_open($db_link,$DB_name,'SELECT * FROM forms WHERE ID='.$id_forms);
			if(isset($_POST['m_email']) && $_POST['m_email']) $mailto=$_POST['m_email'];
			else $mailto=$dados2->db_data["Email"];
			$name=$dados2->db_data["Name"];
			$txt_f_ok=limpa_editor_lixo($dados2->db_data["Texto2"]);
			$txt_f_erro=limpa_editor_lixo($dados2->db_data["Texto3"]);
			$m_assunto=$assunto=$dados2->db_data["assunto"];
			$txt_f_email=limpa_editor_lixo($dados2->db_data["Texto4"]);
			$txt_f_email=str_replace("../../files/",$site_path."files/", $txt_f_email);
			$txt_f_email=str_replace("[nome]",$name,$txt_f_email);
			$mensagem=limpa_editor_lixo($dados2->db_data["Texto4"]);
			$mensagem=str_replace("[nome]",$name,$mensagem);
			$dados2->db_close();

			$campo_id_pai=guarda_bd_form($id_forms);
			$dados=new DataObj;
			$mailbody .="<div style='font-family:Arial; font-size:12px;'>";
			$dados->db_open($db_link,$DB_name,"Select * from form_fields where ID_ORG=$id_forms Order by Ordem");
			while(!$dados->EOF())
			{
				$campo=$dados->db_data["Name"];
				$campo_id=$dados->db_data["ID"];
				$linha=$dados->db_data["Muda"];
				$campo_id_org=$dados->db_data["ID_ORG"];
				$tipo=$dados->db_data["Tipo"];
				if($tipo==9)
				{
					$variavel_name="field".$campo_id;
					$Documento["name"]=$_FILES[$variavel_name]['name'];
					$Documento["temp"]=$_FILES[$variavel_name]['tmp_name'];
					$ext=substr($Documento["name"], strrpos($Documento["name"], '.')+1);
					$img_path="files/forms/";
					guarda_doc_bd_campos($campo_id_pai,$campo_id,$img_path,$Documento);

					global $DB_host,$DB_user,$DB_pass,$DB_name;
					$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
					$dados3=new DataObj;
					$sql3="Select Max(ID) as IDD from form_records_fields where ID_ORG=$campo_id_pai";
					$hRes=$dados3->db_open($db_link,$DB_name,$sql3);
					if($ext!="") $xt=$dados3->db_data["IDD"]."_1.".$ext;
					$dados3->db_close();

					global $site_http_url;

					if($ext!="") $mailbody.= "<b>File:</b><br> <a style='font-family:Arial; font-size:12px;text-decoration:underline' href='".$site_http_url."files/forms/".$xt."'>$campo</a>".$ln;

				}
				elseif($tipo==13)
				{
				  $ano="ano".$campo_id;
				  $mes="mes".$campo_id;
				  $dia="dia".$campo_id;
				  $valor=$_POST[$ano]."-".$_POST[$mes]."-".$_POST[$dia];
				  $valor = protege($valor);
				  $mailbody.= "\n\nData:\n".$valor.$ln;
				  guarda_bd_campos($campo_id_pai,$campo_id,$valor);
				}
				elseif($dados->db_data["Tipo"]==10)
				{
				  $D=getdate();
				  $ano=$D["year"];
				  $mes=$D["mon"];
				  $dia=$D["mday"];
				  if($mes < 10) $mes="0".$mes;
				  if($dia < 10) $dia="0".$dia;
				  $mailbody.= "\n\nData:\n".$ano."-".$mes."-".$dia;
				  guarda_bd_campos($campo_id_pai,$campo_id,"$ano-$mes-$dia");
				}
				elseif($tipo==6)
				{
					$campo_html="field".$campo_id."_counter";
					$contador=$_POST[$campo_html];
					$variavel="field".$campo_id."_0";
					$valor=$_POST[$variavel];
					$valor = protege($valor);
					// echo $contador;

					$mailbody.= "\n\n$campo:\n";
					for($k=0;$k<$contador;$k++)
					{
						$variavel="field".$campo_id."_".$k;
						$valor.= "{}".protege($_POST[$variavel]);
						if($k > 0)$mailbody.= ", ".$_POST[$variavel];
						else $mailbody.= $_POST[$variavel];
					}
					guarda_bd_campos($campo_id_pai,$campo_id,$valor);
				}
				elseif($tipo!=33)
				{
					$variavel="field".$campo_id;
					if($tipo!=31)
					{

						if($campo!="")
						{
							if($tipo==0) $mailbody.= "\n\n$campo\n".$_POST[$variavel];
							elseif($tipo==7)
							{
								// query para nome na combo
								if (!function_exists('BuscarRegistoOpcoes_email'))
								{
									function BuscarRegistoOpcoes_email($id_pai=0)
									{
										global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome_linguas,$site_lang;
										$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
										$rs=new DataObj;
										$sql="Select Value, Email from form_options where (ID='$id_pai' || Value='$id_pai') Order by Ordem,ID desc";
										$rs->db_open($db_link,$DB_name, $sql);
										$Nome_Campo = $rs->db_data['Value'];
										$Email_Campo = $rs->db_data['Email'];
										$rs->db_close();

										return array('Nome_Campo' => $Nome_Campo, 'Email_Campo' => $Email_Campo);
									}
								}
								$id_opt=$_POST[$variavel];
								$arr=BuscarRegistoOpcoes_email($id_opt);

								if($arr['Email_Campo']) $mailto=$arr['Email_Campo'];
								// echo $mailto;

								global $DB_host,$DB_user,$DB_pass,$DB_name;
								$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
								$dados3=new DataObj;
								$sql3="Select Value from form_options where ID=".$_POST[$variavel];
								$dados3->db_open($db_link,$DB_name,$sql3);
								if(!$dados3->EOF()) $Value=$dados3->db_data["Value"];
								else $Value=$_POST[$variavel];
								$dados3->db_close();

								$mailbody.= "\n\n$campo:\n".$Value;
							}
							else
							{
								$mailbody.= "\n\n$campo:\n".$_POST[$variavel];
							}

						}else $mailbody.= $_POST[$variavel].$ln;
					}
					else
					{
						// $variavel="Email2";
						$variavel="Email".$campo_id;
						$mailbody.= "\n\nEmail:\n".$_POST[$variavel];
						$Email_send=''; $Email_send=$_POST[$variavel];
					}
					$aux=$_POST[$variavel];
					guarda_bd_campos($campo_id_pai,$campo_id,$aux);
				}
				$dados->db_next();
			}
			$dados->db_close();
			$mailbody.="</div>";


			$txt_f_email=str_replace("\n","<br>",$txt_f_email);
			$mailbody=str_replace("\n","<br>",$mailbody);
			// echo $mailbody;
			$mailbody2=parseContentsMail($txt_f_email);

			$headers="From: $email_sender\r\n";
			$from=$email_sender;
			//**************************************************************
			$mailsubject="Novo email enviado pelo site ".$Nome_Site;
			if($m_assunto) $mailsubject=$m_assunto;


			if($txt_f_email)
			{
				// Vamos sustituir o body default pelo novo
				$mailbody2=parseContentsMail($txt_f_email);

				if(strtoupper(substr(PHP_OS,0,3)=='WIN')){   $eol="\r\n";} elseif(strtoupper(substr(PHP_OS,0,3)=='MAC')){   $eol="\r";} else {   $eol="\n";}
				$now=uniqid("")."@memogadget.com";
				$headers="";
				$headers.='From: "'.$rss_site_url.' - Submetido forms: '.$assunto.'" <'.$from.'>'.$eol;
				$headers.='Reply-To: '.$from.$eol;
				$headers.='Return-Path: '.$from.$eol;    // these two to set reply address
				$headers.="Message-ID: <".$now.">".$eol;
				$headers.="X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
				$mime_boundary=md5(time());
				$headers.='MIME-Version: 1.0'.$eol;
				$headers.="Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

				$mailbody2='
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>'.$mailsubject.'</title>
				</head>
				<body>
				'.$mailbody2.'
				</body>
				</html>
				';

				$mailbody='
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>'.$mailsubject.'</title>
				</head>
				<body>
				'.$mailbody.'
				</body>
				</html>
				';

			}else{

			$mailbody2=$mailbody;
			$now=uniqid("")."@memogadget.com";
			if(strtoupper(substr(PHP_OS,0,3)=='WIN')){   $eol="\r\n";} elseif(strtoupper(substr(PHP_OS,0,3)=='MAC')){   $eol="\r";} else {   $eol="\n";}
				$headers="";
				$headers.='From: "'.$rss_site_url.' - Submetido forms: '.$assunto.'" <'.$from.'>'.$eol;
				$headers.='Reply-To: '.$from.$eol;
				$headers.='Return-Path: '.$from.$eol;    // these two to set reply address
				$headers.="Message-ID: <".$now.">".$eol;
				$headers.="X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
				$mime_boundary=md5(time());
				$headers.='MIME-Version: 1.0'.$eol;
				$headers.="Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

				$mailbody2='
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>'.$mailsubject.'</title>
				</head>
				<body>
				'.$mailbody2.'
				</body>
				</html>
				';
			}
			$dados->db_close();

			if(send_email($mailto,$mailsubject,$mailbody,$headers))
			{
				//if($Email_send) send_email($Email_send, $mailsubject, $mailbody, $headers);
					echo '<script>
							$(document).ready(function(){
								Apprise("Os seus dados foram submetidos com sucesso!");
							});
						  </script>';
			}
			else{
				echo '<script>
						$(document).ready(function(){
							Apprise("Os seus dados NÃO foram submetidos! <br> Algo se passou!");
						});
					   </script>';
			}
		}

	}
	else
	{
	    echo html_entity_decode($GLOBALS['key_erro3'][$GLOBALS['site_lang']], ENT_COMPAT ,'UTF-8').'!';
	}



function parseContents($conteudo)
{
    $conteudo="!".$conteudo;
	$conteudo=str_replace("../../files/","files/",$conteudo);
    $pos[0]=strpos($conteudo,"[");
    $i=0;
    while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
    $lower_value=$pos[$i];
    for($k=$i;$k<sizeof($pos);$k++){
        if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
    }
    $flag=array_sum($pos);
    while($flag){
        if($pos[0]==$lower_value){
            if($pos[0]>1) echo substr($conteudo,1,$pos[0]-1);
            $conteudo=substr($conteudo,$pos[0]+1);
            $numero=substr($conteudo,0,strpos($conteudo,"]"));
            $conteudo=substr($conteudo,strpos($conteudo,"]")+2);
			$t_aux=str_replace("$","",$numero);
			echo $globalS[$t_aux];
        }

        $conteudo="!".$conteudo;
        $pos[0]=strpos($conteudo,"[");

        $i=0;
        while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
        $lower_value=$pos[$i];
        for($k=$i;$k<sizeof($pos);$k++){
            if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
        }

        $flag=array_sum($pos);
    }

    return substr($conteudo,1);
}
function parseContentsMail($conteudo)
{
	$str="";
    $conteudo="!".$conteudo;
    $pos[0]=strpos($conteudo,"{campodesc(");

    $i=0;
    while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
    $lower_value=$pos[$i];
    for($k=$i;$k<sizeof($pos);$k++){
        if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
    }
    $flag=array_sum($pos);
    while($flag){
        if($pos[0]==$lower_value){
            if($pos[0]>1) $str.=substr($conteudo,1,$pos[0]-1);
            $conteudo=substr($conteudo,$pos[0]+11);
            $numero=substr($conteudo,0,strpos($conteudo,")"));
            $conteudo=substr($conteudo,strpos($conteudo,")")+2);
            $str.=return_campo(1,$numero);
        }
        $conteudo="!".$conteudo;
        $pos[0]=strpos($conteudo,"{campodesc(");
        $i=0;
        while(intval($pos[$i])==0 && $i<sizeof($pos)) $i++;
        $lower_value=$pos[$i];
        for($k=$i;$k<sizeof($pos);$k++){
            if((intval($pos[$k])<>0) && ($lower_value>intval($pos[$k]))) $lower_value=$pos[$k];
        }
        $flag=array_sum($pos);
    }
    $str.=substr($conteudo,1);
	return $str;
}
function return_campo($tipo,$numero)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$id_org,$campo_id_pai,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$dados2=new DataObj;
	$str="";
	$ID=$campo_id_pai;
    $sql="SELECT * FROM form_fields WHERE id=".$numero;
    $hRes=$dados->db_open($db_link,$DB_name,$sql);

    if(!$dados->EOF())
	{
		if($dados->db_data["col_labels"])
		{
				$cod=$dados->db_data["ID"];
				$dados3=new DataObj;
				$rows=explode(";",$dados->db_data["row_labels"]);
				$cols=explode(";",$dados->db_data["col_labels"]);
				$col_width=90/sizeof($cols);

				$str.='<br>';
				$str.='<table width=100% class="border1" border=0 cellpadding=2 cellspacing=0>';
				$str.='<tr>';
				$str.='<td width=10%>&nbsp;</td>';
				for($k=0;$k<sizeof($cols);$k++){
					$m_titulo=$cols[$k];
					$xpos1=strpos($m_titulo,'[');
					$xpos2=strpos($m_titulo,']');
					if($xpos1 && $xpos2)	$m_titulo=substr($m_titulo,0,$xpos1);
					$str.='<td width='.$colwidth.'% align=center>'.$m_titulo.'</td>';
				}
				$str.='</tr>';

				for($k=0;$k<sizeof($rows);$k++){
				  $str.='<tr>';
				  $str.='<td width=10%>'.$rows[$k].'</td>';
				  for($n=0;$n<sizeof($cols);$n++){
					$resp=(($k+1)*100)+($n+1);
					$sql3='select * from form_records_fields where Name="'.$cod.'_r'.($k+1).'_c'.($n+1).'" and ID_ORG='.$ID;

					$hRes=$dados3->db_open($db_link,$DB_name,$sql3);

					$str.='<td width='.$colwidth.'% align=center>';

					if($dados->db_data["matrix_type"]==1)
					{
					  $str.='<input type="checkbox" name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'" value="x"';
					  if(!$dados3->EOF() && $dados3->db_data["Valor"]=="x") $str.=" checked";
					  $str.='>';
					}
					else
					{

					  $campo_aux=$cols[$n];
					  $pos1=strpos($campo_aux,'[');
					  $pos2=strpos($campo_aux,']');
					  if($pos1 && $pos2)
					  {
						$campo_aux2=substr($campo_aux,$pos1+1);
						$pos2=strpos($campo_aux2,']');
						$campo_aux2=substr($campo_aux2,0,$pos2);
						$valores=explode(",",$campo_aux2);
						if($valores[0]==14)
						{
							// VALORES DE OUTRO FORMULARIO -14
							/*************************************************************************************/
							if(!$dados3->EOF() && $dados3->db_data["Valor"]<>"") $Valor=$dados3->db_data["Valor"];
							$sql="Select * from form_records_fields where Name=".$valores[2];
							$obj=new DataObj;
							$hRes=$obj->db_open($db_link,$DB_name,$sql);
							if(!$obj->EOF())
							{
								$str.='<select name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'">';
								$str.='<OPTION value="0"';
								if($Valor=="0") $str.=" selected";
								$str.='>----</OPTION>';
								while(!$obj->EOF())
								{
									$str.='<OPTION value="'.$obj->db_data["ID"].'"';
									if($obj->db_data["ID"]==$Valor) $str.=" selected";
									$str.='>'.$obj->db_data["Valor"].'</OPTION>';
									if($Obrigatorio==1){$str.='<font color=ff0000> *</font>';$obrigatorio=1;}
									$obj->db_next();
								}
								$obj->db_close();
								$str.='</select>';
							}
							/*************************************************************************************/
						}
					  }
					  else
					  {
						  $str.='<input class="matrix" name="field'.$cod.'_r'.($k+1).'_c'.($n+1).'" style="WIDTH: 100%; HEIGHT: 100%"';
						  if(!$dados3->EOF() && $dados3->db_data["Valor"]<>"") $str.=' value="'.$dados3->db_data["Valor"].'"';
						  $str.='>';
					  }
					}

					$dados3->db_close();
					$str.='</td>';
				  }
				  $str.='</tr>';
				}
				$str.='</table>';
		}
		else
		{
			$tipo=$dados->db_data["Tipo"];

			$sql="SELECT * FROM form_records_fields WHERE ID_ORG='".$ID."' AND Name='".$dados->db_data["ID"]."'";
		    $hRes=$dados->db_open($db_link,$DB_name,$sql);
		    if(!$dados->EOF()) $Valor=stripslashes($dados->db_data["Valor"]);

			if($tipo==32)
			 {
			 	$hRes=$dados2->db_open($db_link,$DB_name,"Select * from products_lang WHERE ID_PAI=$Valor and Lang='$site_lang'");
				$str= $dados2->db_data["Cabecalho"];
				$dados2->db_close();
			 }
			 else
			 {
			 	$Valor=str_replace("{}"," | ",$Valor);
			 	$str.=$Valor;
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