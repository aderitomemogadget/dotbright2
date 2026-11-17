<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function str_limpa_newsletter($str,$endereco)
{
	global $website_url_email;
	if(!$endereco) $endereco=$website_url_email.'files/';
	$str=limpa_editor_lixo($str);
	$str=str_replace('src="/files','src="http://www.ricon.pt/files',$str);
	$str=str_replace('<img ','<img border="0" ',$str);
	$str=str_replace('http://www.ricon.pthttp','http',$str);
	$str=str_replace('http://www.ricon.pt/dev/riconhttp:','http',$str);
	return $str;
}
function BuscarNewsletter($id=0,$ID_ORG=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)
	{
		$condicao="where ID=$id";
		if($ID_ORG!=0)	$condicao="$condicao and where ID_ORG=$ID_ORG";
	}
	else
	{
		$condicao="";
		if($ID_ORG!=0)	$condicao="where ID_ORG=$ID_ORG";
	}
	$hRes=$obj->db_open($db_link,$DB_name, "Select $campos from nl_newsletter $condicao order by Ordem Asc");
	return $obj;
}

function InsereNewsletter($ID_ORG,$data,$cabecalho,$texto,$img_path,$Img_peq1)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;
	if ($Img_peq1["name"]) $altera_img=1;

	//SE NÂO TIVER 1 IMAGEM
	if (!$altera_img){
		$img_ext="";
		$img_ext=str2SQLstr($img_ext);
		}
	else
	{
		//RETIRA A EXTENSÃO DA 1 IMAGEM
		$pos=strrpos($Img_peq1["name"],".");
		$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
		$img_ext=str2SQLstr($ext1);
	}
	$data1=date2SQLstr($data);
	$cabecalho1=str2SQLstr($cabecalho);
	$texto1=str2SQLstr($texto);
	//INSERE VALORES NA BD
	$sql="insert into nl_newsletter (ID_ORG,Data,Cabecalho,Texto,img_ext) values ($ID_ORG,$data1,$cabecalho1,$texto1,$img_ext)";
	$res=Runquery($db_link,$DB_name, $sql);
	//CASO TENHA INSERIDO OS VALORES COM SUCESSO VAI COPIAR AS IMAGENS
	if ($res)
	{
		//COPIA A IMAGEM 1
		if($altera_img)
		{
			$obj= new DataObj;
			$obj->db_open($db_link,$DB_name, "Select MAX(ID) from nl_newsletter");
			copia_segura($Img_peq1["temp"],$img_path.$obj->db_data[0] ."_1.".$ext1);
			$obj->db_close();
		}
	}
	else
	{
		echo db_error($db_link);
	}
	db_closedb_conection($db_link);
	return($res);
}
function ApagaNewsletter($id,$img_path)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$res->db_open($db_link,$DB_name, "Select * from nl_newsletter where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$obj->db_close();
	//ELIMINA O REGISTO
	$res=Runquery($db_link,$DB_name, "delete from nl_newsletter where ID=$id");
	if ($res)
	{
		//CASO TENHA IMAGENS ELIMINA-AS
		if($ext1!="")
		{
			$ficheiro1=$id."_1.".$ext1;
			unlink($img_path.$ficheiro1);
		}
	}
	else
	{
		echo db_error($db_link);
	}
	db_closedb_conection($db_link);
	return($res);
}

function AlteraNewsletter($id,$ID_ORG,$data,$cabecalho,$texto,$img_path,$Img_peq1,$Apagar_img1,$teste1,$teste2)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;
	if ($Img_peq1["name"]) $altera_img=1;
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name, "Select img_ext from nl_newsletter where ID=$id");
	$ext1=$obj->db_data["img_ext"];
	$obj->db_close();
	if($hRes)
	{
		//IMAGEM NUMERO 1
		//NAO TINHA IMAGEM
		if($teste1==0)  //CONTINUA SEM IMAGEM
		{
			if (!$altera_img)
			{
				$img_ext="";
				$img_ext=str2SQLstr($img_ext);
			}
			else //PASSOU A TER IMAGEM
			{
				//RETIRA A EXTENSÃO DA 1 IMAGEM
				$pos=strrpos($Img_peq1["name"],".");
				$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
				$img_ext=str2SQLstr($ext1);
				//COPIA A IMAGEM 1
				copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
			}
		}
		else  //TINHA IMAGEM
		{
			//QUER APAGAR A IMAGEM
			if($Apagar_img1==1){
				$ficheiro1=$id."_1.".$ext1;
				unlink($img_path.$ficheiro1);
				//NAO SUBSTITUI A IMAGEM
				if (!$altera_img){
					$img_ext="";
					$img_ext=str2SQLstr($img_ext);
				}
				else //SUBSTITUI A IMAGEM
				{
					//RETIRA A EXTENSÃO DA 1 IMAGEM
					$pos=strrpos($Img_peq1["name"],".");
					$ext1=substr($Img_peq1["name"],$pos + 1,strlen($Img_peq1["name"]) + 1 - $pos);
					$img_ext=str2SQLstr($ext1);
					//COPIA A IMAGEM 1
					copia_segura($Img_peq1["temp"],$img_path.$id ."_1.".$ext1);
				}
			}
			//NAO QUER APAGAR IMAGEM
			else{
				//MANTEM OS VALORES ANTERIORES
				$img_ext=str2SQLstr($ext1);
			}
		}
	}
	$data1=date2SQLstr($data);
	$cabecalho1=str2SQLstr($cabecalho);
	$texto1=str2SQLstr($texto);
	$sql="Update nl_newsletter Set ID_ORG=$ID_ORG,Data=$data1,Cabecalho=$cabecalho1,Texto=$texto1,img_ext=$img_ext where ID=$id";
	$res=Runquery($db_link,$DB_name, $sql);
	db_closedb_conection($db_link);
	return($res);
}

function BuscarNewsletterindice($N=0,$lingua="")
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;

	if($lingua=="en"){
		$condicao=" WHERE Visivel=2 OR Visivel=3";
	}else if($lingua=="pt") {
		$condicao=" WHERE Visivel=1 OR Visivel=3";
	}

	if(!$N){
		$Limit ="";
	}else{
		$Limit=" LIMIT 0,$N";
	}
	$hRes=$obj->db_open($db_link,$DB_name, "Select * from nl_newsletter".$condicao." order by Data Desc".$Limit);
	return $obj;
}

function ContarNewsletterindice()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name, "Select count(ID) as contador from nl_newsletter ");
	return $obj;
}

function BuscarNewsletterindice2($id=0,$campos="*")
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	if($id!=0)	$condicao="where ID=$id";
	else		$condicao="";
	$hRes=$obj->db_open($db_link,$DB_name, "Select $campos from nl_newsletter $condicao order by Data Desc");
	return $obj;
}

function BuscarNewsletterindice3()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$hRes=$obj->db_open($db_link,$DB_name, "Select * from nl_newsletter where Visivel=1 order by Data Desc");
	return $obj;
}

function InsereNewsletterindice($data,$nome,$texto,$visivel)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$data1=date2SQLstr($data);
	$nome1=str2SQLstr($nome);
	$texto1=str2SQLstr($texto);
	$visivel1=str2SQLstr($visivel);
	$sql="insert into nl_newsletter (Data,Nome,Texto,Visivel) values ($data1,$nome1,$texto1,$visivel1)";
	$res=Runquery($db_link,$DB_name, $sql);
	if (!$res)	echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function ApagaNewsletterindice($id)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($db_link,$DB_name, "delete from nl_newsletter where ID=$id");
	if (!$res)	echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}

function AlteraNewsletterindice($id,$data,$nome,$texto,$visivel)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$data1=date2SQLstr($data);
	$nome1=str2SQLstr($nome);
	$texto1=str2SQLstr($texto);
	$visivel1=str2SQLstr($visivel);
	$sql="Update nl_newsletter Set Data=$data1,Nome=$nome1,Texto =$texto1,Visivel=$visivel1 where ID=$id";
	$res=Runquery($db_link,$DB_name, $sql);
	db_closedb_conection($db_link);
	return($res);
}

function RemoveUtilizadorNewsleter(){
	$erro=$sucesso='';
	if($_POST){
		global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;$rs2=new Dataobj;

		$sql='SELECT * FROM nl_emails_registados WHERE Email="'.$_POST['uns_email'].'"';
		$hRes=$rs->db_open($db_link,$DB_name,$sql);

		if(!$rs->EOF()){

			$sql_insert='INSERT INTO nl_emails_removidos (Nome, Apelido, ID_ORG, Empresa, Email, Contacto, Morada, CodPos1, CodPos2, Localidade, Tel, Fax, DataNasc, Obs, DataRemocao, N_Contribuinte, Telemovel, Url, CodPos, Telefone)
			VALUES ("'.$rs->db_data['Nome'].'", "'.$rs->db_data['Apelido'].'", "'.$rs->db_data['ID_ORG'].'", "'.$rs->db_data['Empresa'].'", "'.$rs->db_data['Email'].'", "'.$rs->db_data['Contacto'].'", "'.$rs->db_data['Morada'].'",
				"'.$rs->db_data['CodPos1'].'", "'.$rs->db_data['CodPos2'].'", "'.$rs->db_data['Localidade'].'", "'.$rs->db_data['Tel'].'", "'.$rs->db_data['Fax'].'", "'.$rs->db_data['DataNasc'].'", "'.$rs->db_data['Obs'].'", "'.date('Y-m-d').'",
				"'.$rs->db_data['N_Contribuinte'].'", "'.$rs->db_data['Telemovel'].'", "'.$rs->db_data['Url'].'", "'.$rs->db_data['CodPos'].'", "'.$rs->db_data['Telefone'].'")';
			$res=Runquery($db_link,$DB_name,$sql_insert);

			if($res){
				$sql_delete='DELETE FROM nl_emails_registados WHERE ID="'.$rs->db_data['ID'].'" AND Email="'.$_POST['uns_email'].'"';

				$res1=Runquery($db_link,$DB_name,$sql_delete);
				if(!$res1){ $erro=$GLOBALS['key_txt_maillinglist_erro'][$site_lang]; }
				else{ $sucesso=$GLOBALS['key_txt_maillinglist_sucesso'][$site_lang]; }
			}else{  $erro=$GLOBALS['key_txt_maillinglist_erro'][$site_lang]; }

		}else{
			$erro=$GLOBALS['key_txt_maillinglist_naoencontrado'][$site_lang];
		}
		$rs->db_close();
	}
	?>

	<br><br><br>
	<?php if($sucesso==''){ ?>
		<div class="page_title" style="color:#990033; font-size:12pt; width:100%; text-align:center;"><?php echo $erro.'<br><br>'; ?></div>
		<form name="unsubscribe" action="" method="post">
			<div>
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
				<tbody>
					<tr>
						<td height="22" align="right">Email</td>
						<td width="5"></td>
						<td><input type="text" class="form_input" name="uns_email" style=";WIDTH: 360px;"></td>
					</tr>
					<tr>
						<td height="22" align="right"></td>
						<td width="5"></td>
						<td><input type="image" class="image" src="files/forms/896_1.png" title="&nbsp;Submit&nbsp;" alt="&nbsp;Submit&nbsp;"></td>
					</tr>
				</tbody>
			</table>
			</div>
		</form>
	<?php
	}else { ?>
		<div class="page_title" style="color:#009966; font-size:16pt; width:100%; text-align:center;"><?php echo $sucesso; ?></div>
	<?php }
}


function get_disclaimer($unsubscrive,$ID_email,$from,$email,$id_newsletter,$with_bottom=1,$tipo=0)
{
	global $site_copyright,$site_title,$website_url_email,$id_pol_priv;
	if($unsubscrive)
	{
		$html_bottom='<table width="701" cellspacing="0" cellpadding="0" border="0" align="center" bordercolor="#FFFFFF">';
		$html_bottom.='<tr><td colspan="5" align="center">&nbsp;</td></tr>';
		$html_bottom.='<tr><td colspan="5"><hr/></td></tr>';
		$html_bottom.='<tr><td colspan="5" align="center">&nbsp;</td></tr>';
		if($tipo==0){
			$html_bottom.='<tr><td colspan="5" align="center" style="line-height:15px;color:#727477;font-family:arial,sans-serif;font-size:13px">Está a receber este email na sequência da subscrição da nossa newsletter.<br/>Não quer receber mais newsletters? clique <a href="'.$website_url_email.'unsubscrive.php?Email='.$email.'&nw='.$ID_email.'&t=0" style="color:#43877E;text-decoration:none;font-family:arial,sans-serif;font-size:13px">aqui</a>.</td></tr>';
		}
		else if($tipo==1)
		{
			$html_bottom.='<tr><td colspan="5" align="center" style="line-height:15px;color:#727477;font-family:arial,sans-serif;font-size:13px">Está a receber este email na sequência da subscrição da nossa notificação de produtos visitados.<br/>Não quer receber mais esta notificação? clique <a href="'.$website_url_email.'unsubscrive.php?Email='.$email.'&nw='.$ID_email.'&t=1" style="color:#43877E;text-decoration:none;font-family:arial,sans-serif;font-size:13px">aqui</a>.</td></tr>';
		}
		else if($tipo==2)
		{
			$html_bottom.='<tr><td colspan="5" align="center" style="line-height:15px;color:#727477;font-family:arial,sans-serif;font-size:13px">Está a receber este email na sequência da subscrição da nossa notificação de novidades.<br/>Não quer receber mais esta notificação? clique <a href="'.$website_url_email.'unsubscrive.php?Email='.$email.'&nw='.$ID_email.'&t=2" style="color:#43877E;text-decoration:none;font-family:arial,sans-serif;font-size:13px">aqui</a>.</td></tr>';
		}
		$html_bottom.='<tr><td colspan="5" align="center">&nbsp;</td></tr>';
		$html_bottom.='<tr><td colspan="5" align="center" style="color:#727477;font-family:arial,sans-serif;font-size:13px">'.$GLOBALS['site_title'].' <span style="font-family:Arial">&copy;</span> '.date('Y-m-d').' | Todos os direitos reservados</td></tr>';
		$html_bottom.='<tr><td colspan="5" align="center">&nbsp;</td></tr>';
		$html_bottom.='</table>';
	}
	else
	{
		$html_bottom='';
	}
	if($with_bottom==1) return $html_bottom.'</BODY></HTML>';
	else return $html_bottom;
}
function trata_email($trata,$nome,$apelido,$email,$empresa,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$telemovel,$url,$datanasc,$grupo,$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome,$key_txt_maillinglist6;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$nome=str2SQLstr($nome);
	$apelido=str2SQLstr($apelido);
	$empresa=str2SQLstr($empresa);
	$email=str2SQLstr($email);
	$morada=str2SQLstr($morada);
	$codpos1=str2SQLstr($codpos1);
	$codpos2=str2SQLstr($codpos2);
	$tel=str2SQLstr($tel);
	$fax=str2SQLstr($fax);
	$telemovel=str2SQLstr($telemovel);
	$url=str2SQLstr($url);
	$datanasc=str2SQLstr($datanasc);
	$grupo1=str2SQLstr($grupo);
	$tipo=str2SQLstr($tipo);

	$obj=new DataObj;
	$sql="Select * from nl_emails_registados where emails_registados.Email=$email and ID_ORG=$grupo";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
	if(!$obj->EOF()) // Já está registado nesse grupo
	{
		$obj->db_close();
		if($trata==0)
		{
			$datax=getdate();
			$dataremocao1=str2SQLstr($datax["year"]."-".$datax["mon"]."-".$datax["mday"]);
			$sql="insert into nl_emails_removidos (ID_ORG,Email,Nome,Apelido,Empresa,Morada,Codpos1,Codpos2,Localidade,Tel,Fax,Telemovel,Url,DataNasc,DataRemocao) values
												 ($grupo1,$email,$nome,$apelido,$empresa,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$telemovel,$url,$datanasc,$dataremocao1)";

			$res2=Runquery($db_link,$DB_name, $sql);
			$sql="delete from nl_emails_registados where Email=$email and ID_ORG=$grupo";
			$res=Runquery($db_link,$DB_name, $sql);
			if (!$res)	echo db_error($db_link);
		}
		if($trata==1)
		{
			$sql="Update nl_emails_registados Set Nome=$nome,Apelido=$apelido,email=$email,Empresa=$empresa,Morada=$morada,Codpos1=$codpos1,Codpos2=$codpos2,Localidade=$localidade,Tel=$tel,Fax=$fax,Telemovel=$telemovel,Url=$url,DataNasc=$datanasc,Tipo=$tipo where Email=$email and ID_ORG=$grupo";

			$res=Runquery($db_link,$DB_name, $sql);
			if ($res)	return "";
			else		return db_error($db_link);
		}
	}
	else // Não está registado,registar.
	{
		$obj->db_close();
		if($trata==1)
		{
			//INSERE VALORES NA BD
			$sql="insert into nl_emails_registados (ID_ORG,Email,  Nome,Apelido,Empresa,Morada,Codpos1,Codpos2,Localidade,Tel,Fax,Telemovel,Url,DataNasc) values
												($grupo1,$email,$nome,$apelido,$empresa,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$telemovel,$url,$datanasc)";

			$res=Runquery($db_link,$DB_name, $sql);
			if ($res)	return "";
			else		return db_error($db_link);
		}
	}
}

function subscreve_email($nome,$apelido,$email,$empresa,$contacto,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$telemovel,$url,$datanasc,$data_hora,$grupo,$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $Nome,$key_txt_maillinglist6;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$nome=str2SQLstr($nome);
	$apelido=str2SQLstr($apelido);
	$empresa=str2SQLstr($empresa);
	$email=str2SQLstr($email);
	$contacto=str2SQLstr($contacto);
	$morada=str2SQLstr($morada);
	$codpos1=str2SQLstr($codpos1);
	$codpos2=str2SQLstr($codpos2);
	$localidade=str2SQLstr($localidade);
	$tel=str2SQLstr($tel);
	$fax=str2SQLstr($fax);
	$telemovel=str2SQLstr($telemovel);
	$datanasc=str2SQLstr($datanasc);
	$url=str2SQLstr($url);
	$grupo1=str2SQLstr($grupo);
	$tipo=str2SQLstr($tipo);

	$hRes=$obj->db_open($db_link,$DB_name,"Select * from nl_emails_registados where Email=$email1 and ID_ORG=$grupo1");
	if(!$obj->EOF()) // Já está registado
	{
		$obj->db_close();
		return $key_txt_maillinglist6;
	}
	else // Não está registado,registar.
	{
		$obj->db_close();
		//INSERE VALORES NA BD
		$sql="insert into nl_emails_registados (ID_ORG,Email,Nome,Apelido,Empresa,Morada,Codpos1,Codpos2,Localidade,Tel,Fax,Telemovel,Url,DataNasc, data_hora) values
									($grupo1,$email,$nome,$apelido,$empresa,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$telemovel,$url,$datanasc,'$data_hora')";
		$res= Runquery($db_link,$DB_name, $sql);
		if ($res)	return "";
		else		return db_error($db_link);
	}
}

function BuscarRegistosMaillingList2($email="",$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$email1=str2SQLstr($email);
	if($email!="")	$condicao="where Email=$email1";
	else			$condicao="";
	$hRes=$obj->db_open( $db_link,$DB_name,"Select $campos from nl_emails_registados $condicao");
	return $obj;
}
function BuscarRegistosLogin($email="",$campos="*")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$email1=str2SQLstr($email);
	if($email!="")	$condicao="where Email=$email1";
	else			$condicao="";
	$hRes=$obj->db_open( $db_link,$DB_name,"Select $campos from clientes $condicao");
	return $obj;
}

function Unsubscrive($email)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$email1=str2SQLstr($email);
	$reg=BuscarRegistosMaillingList2($email,"*");
	$nome=$reg->db_data["Nome"];
	$apelido=$reg->db_data["Apelido"];
	$empresa=$reg->db_data["Empresa"];
	$contacto=$reg->db_data["Contacto"];
	$morada=$reg->db_data["Morada"];
	$codpos1=$reg->db_data["CodPos1"];
	$codpos2=$reg->db_data["CodPos2"];
	$localidade=$reg->db_data["Localidade"];
	$tel=$reg->db_data["Tel"];
	$fax=$reg->db_data["Fax"];
	$movel=$reg->db_data["Telemovel"];
	$url=$reg->db_data["Url"];
	$id_org=$reg->db_data["ID_ORG"];
	$id_org=str2SQLstr($id_org);
	$nome=str2SQLstr($nome);
	$apelido=str2SQLstr($apelido);
	$empresa=str2SQLstr($empresa);
	$email=str2SQLstr($email);
	$morada=str2SQLstr($morada);
	$codpos1=str2SQLstr($codpos1);
	$codpos2=str2SQLstr($codpos2);
	$localidade=str2SQLstr($localidade);
	$tel=str2SQLstr($tel);
	$fax=str2SQLstr($fax);
	$movel=str2SQLstr($movel);
	$url=str2SQLstr($url);
	$datanasc1=str2SQLstr($reg->db_data["DataNasc"]);
	$data=getdate();
	$dataremocao1=str2SQLstr($data["year"]."-".$data["mon"]."-".$data["mday"]);
	$sql="insert into nl_emails_removidos (ID_ORG,Nome,Apelido,Empresa,Email,Morada,Codpos1,Codpos2,Localidade,Tel,Fax,Telemovel,Url,DataNasc,DataRemocao) values ($id_org,$nome,$apelido,$empresa,$email,$morada,$codpos1,$codpos2,$localidade,$tel,$fax,$movel,$url,$datanasc1,$dataremocao1)";
	$res2=Runquery($db_link,$DB_name, $sql);
	$sql="update clientes set Newsletter=0 Where Email='$email'";
	$res2=Runquery($db_link,$DB_name, $sql);
	$sql2="delete from nl_emails_registados where Email=$email";
	$res=Runquery($db_link,$DB_name, $sql2);
	if (!$res)	echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}