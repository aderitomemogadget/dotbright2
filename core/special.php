<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) exit();

function clean_var($variable)
{
    $variable = stripslashes(strip_tags(trim(rtrim($variable))));
    $variable = addslashes($variable);
    return $variable;
}

function referer_dir()
{
    $logout = '';
    $referer_from = '';
    if (isset($_GET['referer_from'])) $referer_from = $_GET['referer_from'];
    if (isset($_GET['logout'])) $logout = $_GET['logout'];
    if ($referer_from) $_SESSION['PAGE_FROM'] = $referer_from;
    elseif (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') $_SESSION['PAGE_FROM'] = $_SERVER['HTTP_REFERER'];
    elseif ($logout) $_SESSION['PAGE_FROM'] = 'index.php';
    elseif (!substr_count($_SERVER['REQUEST_URI'],'pages.php?page_id=67')) $_SESSION['PAGE_FROM'] = $_SERVER['REQUEST_URI'];
    return isset($_SESSION['PAGE_FROM']) ? $_SESSION['PAGE_FROM'] : 'index.php';
}
/*function validateCaptcha($captchaResponse)
{
	$postdata = http_build_query(array(
			'secret' => GOOGLE_CAPTCHA_SECRET_KEY,
			'response' => $captchaResponse
		)
	);
	$opts = array('http' =>
		array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		)
	);
	$context = stream_context_create($opts);
	$result = file_get_contents('https://www.google.com/recaptcha/api/siteverify',false,$context);
	$check = json_decode($result);
	return $check->success;
}*/

function verificar_conta()
{
	if(isset($_GET['code']) && $_GET['code'] !='')
	{
		$code = addslashes($_GET['code']);

		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados2 = new DataObj;
		$rs = new DataObj;
		$sql = "SELECT * FROM clientes WHERE MD5(Token)='".md5($code)."'";
		$dados2->db_open($db_link,$DB_name,$sql);
		if (!$dados2->EOF())
		{
			$Estado = $dados2->db_data['Estado'];
			if ($Estado == 1) // ja se encontra verificado
			{
				echo show_alerta2(getLinguas('key_msg_verify_erro'));
				echo '<script>setTimeout(function(){window.location.href="'.$site_http_url.'";},30000);</script>';
			}
			else
			{
				$sql = "UPDATE clientes SET Estado=1,Ativo=1 WHERE MD5(Token)='".md5($code)."'";
				$res = Runquery($db_link,$DB_name,$sql);
				if ($res)
				{
					echo show_alerta2(getLinguas('key_msg_verify',$GLOBALS['site_lang']));
					$sql = "SELECT Nome,Apelido,Username,Password,Email FROM clientes WHERE Estado=1 AND MD5(Token)='".md5($code)."'";
					$rs->db_open($db_link,$DB_name,$sql);
					$Nome = $rs->db_data['Nome'];
					$Apelido = $rs->db_data['Apelido'];
					$Username = $rs->db_data['Username'];
					$Email = $rs->db_data['Email'];
					$rs->db_close();
					$body = array
					(
						'[username]' => $Username,
						'[nome]' => $Nome,
						'[apelido]' => $Apelido
					);
					$message = template_email($GLOBALS['template_verificacao'],$body);
					send_email($Email,$message['subject'],$message['body']);

					echo '<script>setTimeout(function(){window.location.href="'.$site_http_url.'";},30000);</script>';
				}
			}
		}
		else
		{
			echo show_alerta2(getLinguas('key_msg_verify_erro2'));
			echo '<script>setTimeout(function(){window.location.href="'.$site_http_url.'";},30000);</script>';
		}
		$dados2->db_close();
	}
}
function recuperar_password()
{

	?>
	<script language="JavaScript" type="text/JavaScript">

		function validar_campos()
		{
			var password_1 = document.forms["recupera_pass"]["password_1"].value;
			var password_2 = document.forms["recupera_pass"]["password_2"].value;
			if (password_1 == '')
			{
				Apprise("O campo 'Nova Password' não está preenchido.");
				return false;
			}
			else if (password_2 == '')
			{
				Apprise("O campo 'Repita nova Password' não está preenchido.");
				return false;
			}
			else if (password_1 != '' && password_2 != '')
			{
				if (password_1 == password_2)
				{
					return true;
				}
				else
				{
					Apprise("As passwords são diferentes");
					return false;
				}
			}
			return false;
		}
	</script>

	<div class="row">
		<div class="col-sm-12 col-sm-3 col-md-3 hidden-xs"></div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		<div class="titulo_d"><?php echo getLinguas('key_alterar_pass');?></div>
		<div class="bloco_texto_d mbt"><?php echo getLinguas('key_repetepassword3');?></div>
			<?php
			$token = $_GET['token'];
			$password_1 = $_POST['password_1'];
			$nova_pass = $_POST['nova_pass'];

			if ($password_1 != '')
			{
				global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
				$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$dados1 = new DataObj;
				$sql = "Select * from clientes where token_pass='$nova_pass'";
				$hRes = $dados1->db_open($db_link,$DB_name,$sql);
				if (!$dados1->EOF())
				{
					$alterou = 1;
					$id = $dados1->db_data['ID'];
					$username = $dados1->db_data['Username'];
					$estado = $dados1->db_data['Ativo'];
					$sql2 = "UPDATE clientes SET Password='".encripta($password_1)."',token_pass='' WHERE Username='$username' and token_pass='$nova_pass'";
					$res = Runquery($db_link,$DB_name,$sql2);
					$msg = getLinguas('key_recuperar_pass_msg');
					echo show_alerta($msg,'');

					echo '<script>setTimeout(function(){window.location.href="'.SITE_HTTP_URL.'";},30000);</script>';
				}
				$dados1->db_close();
			}
			else
			{
				?>
				<form name="recupera_pass" action="<?php echo seo('pages.php?page_id='.intval($_GET['page_id']));?>" method="post" onsubmit="return validar_campos();">
					<input type="hidden" name="nova_pass" value="<?=$token; ?>" id="m_email" />

					<div class="form-group">
						<label for="m_email"><?php echo getLinguas('key_password2'); ?>
							<span class="mandatory">*</span></label>
						<input id="m_email" autocomplete="off" type="password" value="" name="password_1" class="form-control" />
					</div>

					<div class="form-group">
						<label for="m_email"><?php echo getLinguas('key_repetepassword2'); ?>
							<span class="mandatory">*</span></label>
						<input id="m_email" autocomplete="off" type="password" value="" name="password_2" class="form-control" />
					</div>

					<div class="form-group">
						<input type="submit" value="SUBMETER" class="button3">

						<div class="recuperar-registo pull-right">
							<span class="mandatory">(*)</span> <?php echo getLinguas('key_cliente_campoobrigatorio'); ?>
						</div>
					</div>
				</form>
				<?php
			}
			?>
		</div>
		<div class="col-sm-12 col-sm-3 col-md-3 hidden-xs"></div>
	</div>
<?php

}
function show_registo_cliente()
{
	global $error_message,$x_email,$x_nome,$x_apelido,$x_password,$x_password_repete;
	$error_message = '';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$logins_rs = new DataObj;
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs3 = new DataObj;

	//FACEBOOK
	if(isset($_GET['fbid']) && clean_var($_GET['fbid']) != '')
	{

		$fbid = clean_var($_GET['fbid']);
		$sql = "SELECT * FROM clientes WHERE fbid='".$fbid."'";
		$logins_rs->db_open($db_link,$DB_name,$sql);
		if(!$logins_rs->EOF())
		{

			$ID = $logins_rs->db_data['ID'];
			// $rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
			// $termos = isset($_POST['m_termos']) ? 1 : 0;
			// $versao = $logins_rs->db_data['versao'];
			// $data = date("Y-m-d H:i:s");

			// $sql = "UPDATE clientes SET Rgpd=$rgpd,Termos=$termos,versao=$versao,Data_ultimo_consentimento='$data' WHERE ID=".$ID;

			// Runquery($db_link,$DB_name,$sql);
		}
		/*else  //registo pelo facebook
		{
			$sql = "SELECT * FROM clientes_temp WHERE fbid='".$fbid."'";
			$rs3->db_open($db_link,$DB_name,$sql);
			if(!$rs3->EOF())
			{
				$ID_temp = $rs3->db_data['ID'];
				$email = $rs3->db_data['Username'];
				$nome = $rs3->db_data['Nome'];
				$apelido = $rs3->db_data['Apelido'];
				$pais = $rs3->db_data['Pais'];
				$cidade = $rs3->db_data['Cidade'];
				// $rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
				// $termos = isset($_POST['m_termos']) ? 1 : 0;
				$versao = 0;
				$newsletter = 0;
				$data = date("Y-m-d H:i:s");
				// if($rgpd == 1)
				// {
				// 	$newsletter = 1;
				// 	$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
				// 	$rs->db_open($db_link,$DB_name,$sql);
				// 	if(!$rs->EOF())
				// 	{
				// 		$versao = $rs->db_data['versao'];
				// 	}
				// 	$rs->db_close();
				// }
				$sql = "INSERT INTO clientes(Username,Email,Nome,Apelido,fbid,Estado,Pais,Cidade,Password,Newsletter,Data) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$fbid."',1,'".$pais."','".$cidade."','".$password."',$newsletter,'$data')";
				$res = Runquery($db_link,$DB_name,$sql);

				if($res)
				{
					$ID = db_get_last_insert_id($db_link);

					$nl1 = new DataObj;
					$nl2 = new DataObj;
					if($newsletter == 1)
					{

						$sql = "DELETE FROM nl_emails_removidos WHERE Email='$email'";
						Runquery($db_link,$DB_name,$sql);

						$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
						$nl2->db_open($db_link,$DB_name,$sql);
						if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
						else $id_grupo = 0;
						$nl2->db_close();

						$sql = "SELECT ID FROM nl_emails_registados WHERE Email='$email'";
						$nl1->db_open($db_link,$DB_name,$sql);
						if(!$nl1->EOF()) {
							$nlid = $nl1->db_data['ID'];
							$data = date("Y-m-d H:i:s");
							$sql = "UPDATE nl_emails_registados SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',data_hora='$data' WHERE ID='$nlid'";
							Runquery($db_link,$DB_name,$sql);
						} else {
							$sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido,Lang) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."','".$apelido."','$lang')";
							Runquery($db_link,$DB_name,$sql);
						}
						$nl1->db_close();
					} else {
						$sql = "DELETE FROM nl_emails_registados WHERE Email='$email'";
						Runquery($db_link,$DB_name,$sql);

						$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
						$nl2->db_open($db_link,$DB_name,$sql);
						if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
						else $id_grupo = 0;
						$nl2->db_close();

						$sql = "SELECT ID FROM nl_emails_removidos WHERE Email='$email'";
						$nl1->db_open($db_link,$DB_name,$sql);
						if(!$nl1->EOF()) {
							$nlid = $nl1->db_data['ID'];
							$data = date("Y-m-d H:i:s");
							$sql = "UPDATE nl_emails_removidos SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',DataRemocao='$data' WHERE ID='$nlid'";
							Runquery($db_link,$DB_name,$sql);
						} else {
							$sql = "INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido) values ('$id_grupo','$nome','$email','".date("Y-m-d")."','".$m_apelido."')";
							Runquery($db_link,$DB_name,$sql);
						}
						$nl1->db_close();
					}

					$sql = "DELETE FROM clientes_temp WHERE fbid='".$fbid."'";
					Runquery($db_link,$DB_name,$sql);
				}
			}
			$rs3->db_close();
		}*/
		$logins_rs->db_close();

		global $cli_id,$cli_token;
		$cli_id = $_SESSION['ss_cli_id'] = $ID;
		$cli_token = $_SESSION['ss_cli_token'] = uniqid("");
		$sql = "UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$ID;
		Runquery($db_link,$DB_name,$sql);

		foreach($_SESSION as $key => $val) {
			if(substr_count($key,'wish_prod_')) {
				if(addRemoveWishList($val,'add') == 0) {
					unset($_SESSION[$key]);
				}
			}
		}

		$_SESSION['ss_cli_ft'] = 0;//reset tentativas

		limparTentativaLogin();

		if($referer) {
			echo '<script>window.location.href="'.$referer.'";</script>';
			exit;
		} else {
			echo '<script>window.location.href="'.$GLOBALS['site_http_url'].'";</script>';
			exit;
		}
	}
	//GOOGLE
	elseif(isset($_GET['googleid']) && clean_var($_GET['googleid']) != '')
	{
		$googleid = clean_var($_GET['googleid']);
		$sql = "SELECT * FROM clientes WHERE googleid='".$googleid."'";
		// var_dump($sql);
		// die();
		$logins_rs->db_open($db_link,$DB_name,$sql);
		if(!$logins_rs->EOF())
		{
			$ID = $logins_rs->db_data['ID'];
			// $rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
			// $termos = isset($_POST['m_termos']) ? 1 : 0;
			// $versao = $logins_rs->db_data['versao'];
			// $data = date("Y-m-d H:i:s");
			// if($rgpd == 1)
			// {
			// 	$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
			// 	$rs->db_open($db_link,$DB_name,$sql);
			// 	if(!$rs->EOF())
			// 	{
			// 		$versao = $rs->db_data['versao'];
			// 	}
			// 	$rs->db_close();
			// }

			// $sql = "UPDATE clientes SET Rgpd=$rgpd,Termos=$termos,versao=$versao,Data_ultimo_consentimento='$data' WHERE ID=".$ID;
			// Runquery($db_link,$DB_name,$sql);
		}
		else  //registo pelo google
		{
			$sql = "SELECT * FROM clientes_temp WHERE googleid='".$googleid."'";
			$rs3->db_open($db_link,$DB_name,$sql);
			if(!$rs3->EOF())
			{
				$ID_temp = $rs3->db_data['ID'];
				$email = $rs3->db_data['Username'];
				$nome = $rs3->db_data['Nome'];
				$apelido = $rs3->db_data['Apelido'];
				$pais = $rs3->db_data['Pais'];
				$cidade = $rs3->db_data['password'];
				// $rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
				// $termos = isset($_POST['m_termos']) ? 1 : 0;
				$versao = 0;
				$newsletter = 0;
				$data = date("Y-m-d H:i:s");
				// if($rgpd == 1)
				// {
				// 	$newsletter = 1;
				// 	$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
				// 	$rs->db_open($db_link,$DB_name,$sql);
				// 	if(!$rs->EOF())
				// 	{
				// 		$versao = $rs->db_data['versao'];
				// 	}
				// 	$rs->db_close();
				// }
				$sql = "INSERT INTO clientes(Username,Email,Nome,Apelido,googleid,Estado,Ativo,Pais,Cidade,Password,Newsletter,Data) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$googleid."',1,1,'".$pais."','".$cidade."','".$password."',$newsletter,'$data')";
				// var_dump($sql);
				// die();
				$res = Runquery($db_link,$DB_name,$sql);

				if($res)
				{
					$ID = db_get_last_insert_id($db_link);

					$nl1 = new DataObj;
					$nl2 = new DataObj;
					if($newsletter == 1) {

						$sql = "DELETE FROM nl_emails_removidos WHERE Email='$email'";
						Runquery($db_link,$DB_name,$sql);

						$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
						$nl2->db_open($db_link,$DB_name,$sql);
						if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
						else $id_grupo = 0;
						$nl2->db_close();

						$sql = "SELECT ID FROM nl_emails_registados WHERE Email='$email'";
						$nl1->db_open($db_link,$DB_name,$sql);
						if(!$nl1->EOF()) {
							$nlid = $nl1->db_data['ID'];
							$data = date("Y-m-d H:i:s");
							$sql = "UPDATE nl_emails_registados SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',data_hora='$data' WHERE ID='$nlid'";
							Runquery($db_link,$DB_name,$sql);
						} else {
							$sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido,Lang) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."','".$apelido."','$lang')";
							Runquery($db_link,$DB_name,$sql);
						}
						$nl1->db_close();
					} else {
						$sql = "DELETE FROM nl_emails_registados WHERE Email='$email'";
						Runquery($db_link,$DB_name,$sql);

						$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
						$nl2->db_open($db_link,$DB_name,$sql);
						if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
						else $id_grupo = 0;
						$nl2->db_close();

						$sql = "SELECT ID FROM nl_emails_removidos WHERE Email='$email'";
						$nl1->db_open($db_link,$DB_name,$sql);
						if(!$nl1->EOF()) {
							$nlid = $nl1->db_data['ID'];
							$data = date("Y-m-d H:i:s");
							$sql = "UPDATE nl_emails_removidos SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',DataRemocao='$data' WHERE ID='$nlid'";
							Runquery($db_link,$DB_name,$sql);
						} else {
							$sql = "INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido) values ('$id_grupo','$nome','$email','".date("Y-m-d")."','".$m_apelido."')";
							Runquery($db_link,$DB_name,$sql);
						}
						$nl1->db_close();
					}

					$sql = "DELETE FROM clientes_temp WHERE googleid='".$googleid."'";
					Runquery($db_link,$DB_name,$sql);
				}
			}
			$rs3->db_close();


		}
		$logins_rs->db_close();
		global $cli_id,$cli_token;
		$cli_id = $_SESSION['ss_cli_id'] = $ID;
		$cli_token = $_SESSION['ss_cli_token'] = uniqid("");
		$sql = "UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$ID;
		Runquery($db_link,$DB_name,$sql);

		foreach($_SESSION as $key => $val) {
			if(substr_count($key,'wish_prod_')) {
				if(addRemoveWishList($val,'add') == 0) {
					unset($_SESSION[$key]);
				}
			}
		}

		$_SESSION['ss_cli_ft'] = 0;//reset tentativas

		limparTentativaLogin();

		if($referer) {
			echo '<script>window.location.href="'.$referer.'";</script>';
			exit;
		} else {
			echo '<script>window.location.href="'.$GLOBALS['site_http_url'].'";</script>';
			exit;
		}
	}
    if (!isset($_POST['submit1']))
	{
        global $logout;
        global $cli_id,$cli_token;
        if ($logout)
		{
            $_SESSION['ss_cli_id'] = '';
            $_SESSION['ss_cli_token'] = '';
            $cli_id = '';
            $cli_token = '';
        }

		show_clienteForm();


		/*
		if (isset($_GET['show_compras']) && $_GET['show_compras'] == 1)
		{

			echo menu_perfil();

			if (isset($_POST['cancelar_encomenda'])) cancelarEncomenda();
            if (isset($_POST['reencomendar'])) reEncomendarEncomenda();
            lista_compras();
        }
		else if (isset($_GET['show_wishlist']) && intval($_GET['show_wishlist']) == 1)
		{
            showWishList();
        }
		else if (isset($_GET['show_carteira']) && intval($_GET['show_carteira']) == 1)
		{
            echo menu_perfil();
            echo loadLivroEnderecos();

        }

		else show_clienteForm(); */
    }
	else
	{
        $error = 0;
        //*********************** VARIAVEIS COMUNS***********************/
        if (clean_var($_POST['m_email']) != '')
		{
            $x_email[2] = clean_var($_POST['m_email']);
            if (!validEmail($x_email[2])) {
                $error = 1;
                $x_email[3] = ' has-error ';
            } else {
                $x_email[3] = ' has-success ';
            }
        }
		else
		{
            $error = 1;
            $x_email[3] = ' has-error ';
        }
        if ($dados = cliente_logado())
		{
            if (clean_var($_POST['m_nome']) != '') {
                $x_nome[2] = clean_var($_POST['m_nome']);
                $x_nome[3] = ' has-success ';
            } else {
                $error = 1;
                $x_nome[3] = ' has-error ';
            }

            if (clean_var($_POST['m_apelido']) != '') {
                $x_apelido[2] = clean_var($_POST['m_apelido']);
                $x_apelido[3] = ' has-success ';
            } else {
                $error = 1;
                $x_apelido[3] = ' has-error ';
            }

            if (clean_var($_POST['m_password']) != '' || clean_var($_POST['m_password_repete']) != '') {
                $x_password[2] = clean_var($_POST['m_password']);
                $x_password_repete[2] = clean_var($_POST['m_password_repete']);

                if ($_POST['m_password'] != $_POST['m_password_repete']) {
                    $error = 1;
                    $x_password_repete[3] = ' has-error ';
                    $x_password[3] = ' has-error ';
                }
            }
        }
        if ($_POST['login'] == 1)
        {
            if (clean_var($_POST['m_password']) != '') {
                $x_password[2] = clean_var($_POST['m_password']);
                $x_password[3] = ' has-success ';
            } else {
                $error = 1;
                $x_password[3] = ' has-error ';
            }
        }
	    if (isset($_POST['registar']) && $_POST['registar'] == 1)
		{
            if (clean_var($_POST['m_nome']) != '')
			{
                $x_nome[2] = clean_var($_POST['m_nome']);
                $x_nome[3] = ' has-success ';
            }
			else
			{
                $error = 1;
                $x_nome[3] = ' has-error ';
            }

            if (clean_var($_POST['m_apelido']) != '')
			{
                $x_apelido[2] = clean_var($_POST['m_apelido']);
                $x_apelido[3] = ' has-success ';
            }
			else
			{
                $error = 1;
                $x_apelido[3] = ' has-error ';
            }

            if (clean_var($_POST['m_password']) != '')
			{
                $x_password[2] = clean_var($_POST['m_password']);
            }
			else
			{
                $error = 1;
                $x_password[3] = ' has-error ';
            }

            if (clean_var($_POST['m_password_repete']) != '')
			{
                $x_password_repete[2] = clean_var($_POST['m_password_repete']);
                if ($_POST['m_password'] != $_POST['m_password_repete'])
				{
                    $error = 1;
                    $x_password_repete[3] = ' has-error ';
                    $x_password[3] = ' has-error ';
                }
				else
				{
                    $x_password_repete[3] = ' has-success ';
                    $x_password[3] = ' has-success ';
                }
            }
			else
			{
                $error = 1;
                $x_password_repete[3] = ' has-error ';
            }

            if (clean_var($_POST['m_termos']) != 1 && clean_var($_POST['m_termos1']) != 1)
			{
                $error = 1;
                $x_termos[3] = ' has-error ';
            }
			else
			{
                $x_termos[3] = ' has-success ';
            }

			/*$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
			if (!validateCaptcha($recaptchaResponse))
			{
				$error = 1;
				$error .= 'Código de segurança inválido<br>';

			}*/
        }

        if ($error == 1)
		{
            if (isset($_POST['login'])) registarTentativaLogin();

            $error_message = getLinguas('key_captcha2',$GLOBALS['site_lang']);
            show_clienteForm();
        }
		else
		{


            global $key_erro13,$key_erro11,$key_erro14;
            global $DB_host,$DB_user,$DB_pass,$DB_name;

            if (isset($_POST['login']) && $_POST['login'] == 1)//tenta fazer login
            {
                $_SESSION['ss_cli_ft']++;//adiciona tentativa
                $m_email = clean_var($_POST['m_email']);
                $m_password = clean_var($_POST['m_password']);
                $referer = clean_var($_POST['referer']);
                $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                $logins_rs = new DataObj;
                $sql = "SELECT * FROM clientes WHERE Username='$m_email'";
                $logins_rs->db_open($db_link,$DB_name,$sql);
                if (!$logins_rs->EOF())
				{
                    //if ($logins_rs->db_data['Estado'] == 0)
                    if ($logins_rs->db_data['Ativo'] == 0)
					{
                        $error = 1;
                        $error_message = '<span style="">'.$key_erro14[$GLOBALS['site_lang']].'</span>';
                    }
					else
					{
						if ($GLOBALS['registo_validar_email']==1 && $logins_rs->db_data['Estado'] == 0)
						{
							//E obrigatorio validar o email
							$error = 1;
                        	$error_message = '<span style="">'.$key_erro14[$GLOBALS['site_lang']].'</span>';
						}
						else
						{
	                        $sql = "UPDATE clientes SET failed_attempts=failed_attempts+1 WHERE ID=".$logins_rs->db_data['ID'];
	                        Runquery($db_link,$DB_name,$sql);
	                        $admin = $_POST['admin'];
	                        if((encripta($m_password) == $logins_rs->db_data['Password']) || ($admin==1 && $m_password == $logins_rs->db_data['Password']) )
							{
	                            global $cli_id,$cli_token;
	                            $cli_id = $_SESSION['ss_cli_id'] = $logins_rs->db_data['ID'];
	                            $cli_token = $_SESSION['ss_cli_token'] = uniqid("");
	                            $sql = "UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$logins_rs->db_data['ID'];
	                            Runquery($db_link,$DB_name,$sql);

	                            foreach ($_SESSION as $key => $val) {
	                                if (substr_count($key,'wish_prod_')) {
	                                    if (addRemoveWishList($val,'add') == 0) {
	                                        unset($_SESSION[$key]);
	                                    }
	                                }
	                            }

	                            $_SESSION['ss_cli_ft'] = 0;//reset tentativas

	                            limparTentativaLogin();

	                            if ($referer) {
	                                echo '<script>window.location.href="'.$referer.'";</script>';
	                                exit;
	                            } else {
	                                echo '<script>window.location.href="'.$GLOBALS['site_http_url'].'";</script>';
	                                exit;
	                            }
	                        }
	                        else
	                        {
	                            $error = 1;
	                            $error_message = '<span style="">'.$key_erro13[$GLOBALS['site_lang']].'</span>';
	                        }
						}
                    }
                }
                else
                {
                    $error = 1;
                    $error_message = '<span style="">'.$key_erro11[$GLOBALS['site_lang']].'</span>';
                }
                $logins_rs->db_close();
                if ($error == 1) show_clienteForm();
				else echo '<script>setTimeout(function(){window.location.href="'.$GLOBALS['site_http_url'].'index.php";},30000);</script>';
            }


            if (isset($_POST['recupera']) && $_POST['recupera'] == 1)
			{
                $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

                $dbo = new DataObj();
                $m_email = clean_var($_POST['m_email']);
                $sql = "SELECT Username,Nome,Apelido  FROM clientes WHERE Username='$m_email'";
                $dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
                if (!$dbo->EOF())
				{
                    $username = $dbo->db_data['Username'];
                    $nm = $dbo->db_data['Nome'];
                    $apl = $dbo->db_data['Apelido'];
                    $dbo->db_close();
                    $token = md5($username);
                    Runquery($db_link,$GLOBALS['DB_name'],"UPDATE clientes SET token_pass='$token' WHERE Username='$username'");

                    $body = array
                    (
                        '[username]' => $m_email,
                        '[nome]' => $nm,
                        '[apelido]' => $apl,
                        '[token_pass]' => "Clique neste link para <a href=".SITE_HTTP_URL .seo('pages.php?page_id='.$GLOBALS['recuperar_page_id']).'?token='.$token."> Recuperar a Password</a>"
                    );

                    $message = template_email($GLOBALS['template_forgotpass'],$body);
                    send_email($m_email,$message['subject'],$message['body']);

                    echo show_alerta2(getLinguas('key_dentmomentos',$GLOBALS['site_lang']));
                    echo '<script type="text/javascript">setTimeout(function(){window.location.href="'.$GLOBALS['site_http_url'].'index.php";},30000);</script>';

                }
				else
				{
                    $error = 1;
                    $error_message = getLinguas('key_dentmomentos3',$GLOBALS['site_lang']);
                }
                $dbo->db_close();
                if ($error == 1) show_clienteForm();
            }

            if(1 == 1)
			{
				global $DB_host,$DB_user,$DB_pass,$DB_name;
				$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$logins_rs = new DataObj;
				$rs = new DataObj;
				$rs2 = new DataObj;
				$rs3 = new DataObj;

				$lang = $GLOBALS['site_lang'];
				$site_country = $_SESSION['ss_site_country'];

				//FACEBOOK
				if(isset($_POST['fbid']) && clean_var($_POST['fbid']) != '')
				{
					$fbid = clean_var($_POST['fbid']);
					$sql = "SELECT * FROM clientes WHERE fbid='".$fbid."'";
					$logins_rs->db_open($db_link,$DB_name,$sql);
					if(!$logins_rs->EOF())
					{

						$ID = $logins_rs->db_data['ID'];
						$rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
						$termos = isset($_POST['m_termos']) ? 1 : 0;
						$versao = $logins_rs->db_data['versao'];
						$data = date("Y-m-d H:i:s");
						if($rgpd == 1)
						{

							$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
							$rs->db_open($db_link,$DB_name,$sql);
							if(!$rs->EOF())
							{
								$versao = $rs->db_data['versao'];
							}
							$rs->db_close();
						}
						$sql = "UPDATE clientes SET Rgpd=$rgpd,Termos=$termos,versao=$versao,Data_ultimo_consentimento='$data' WHERE ID=".$ID;

						Runquery($db_link,$DB_name,$sql);
					}
					else  //registo pelo facebook
					{
						$sql = "SELECT * FROM clientes_temp WHERE fbid='".$fbid."'";
						$rs3->db_open($db_link,$DB_name,$sql);
						if(!$rs3->EOF())
						{
							$ID_temp = $rs3->db_data['ID'];
							$email = $rs3->db_data['Username'];
							$nome = $rs3->db_data['Nome'];
							$apelido = $rs3->db_data['Apelido'];
							$pais = $rs3->db_data['Pais'];
							$cidade = $rs3->db_data['password'];
							$rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
							$termos = isset($_POST['m_termos']) ? 1 : 0;
							$versao = 0;
							$newsletter = 0;
							$data = date("Y-m-d H:i:s");
							if($rgpd == 1)
							{
								$newsletter = 1;
								$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									$versao = $rs->db_data['versao'];
								}
								$rs->db_close();
							}
							$sql = "INSERT INTO clientes(Username,Email,Nome,Apelido,fbid,Estado,Ativo,Pais,Cidade,Password,Newsletter,Rgpd,Termos,versao,Data) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$fbid."',1,1,'".$pais."','".$cidade."','".$password."',$newsletter,$rgpd,$termos,$versao,'$data')";
	        				$res = Runquery($db_link,$DB_name,$sql);

	        				if($res)
	        				{
	        					$ID = db_get_last_insert_id($db_link);

	        					$nl1 = new DataObj;
								$nl2 = new DataObj;
								if($newsletter == 1) {

									$sql = "DELETE FROM nl_emails_removidos WHERE Email='$email'";
									Runquery($db_link,$DB_name,$sql);

									$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
									$nl2->db_open($db_link,$DB_name,$sql);
									if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
									else $id_grupo = 0;
									$nl2->db_close();

									$sql = "SELECT ID FROM nl_emails_registados WHERE Email='$email'";
									$nl1->db_open($db_link,$DB_name,$sql);
									if(!$nl1->EOF()) {
										$nlid = $nl1->db_data['ID'];
										$data = date("Y-m-d H:i:s");
										$sql = "UPDATE nl_emails_registados SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',data_hora='$data' WHERE ID='$nlid'";
										Runquery($db_link,$DB_name,$sql);
									} else {
										$sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido,Lang,Country,Rgpd) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."','".$apelido."','$lang','$site_country','$rgpd')";
										Runquery($db_link,$DB_name,$sql);
									}
									$nl1->db_close();
								} else {
									$sql = "DELETE FROM nl_emails_registados WHERE Email='$email'";
									Runquery($db_link,$DB_name,$sql);

									$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
									$nl2->db_open($db_link,$DB_name,$sql);
									if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
									else $id_grupo = 0;
									$nl2->db_close();

									$sql = "SELECT ID FROM nl_emails_removidos WHERE Email='$email'";
									$nl1->db_open($db_link,$DB_name,$sql);
									if(!$nl1->EOF()) {
										$nlid = $nl1->db_data['ID'];
										$data = date("Y-m-d H:i:s");
										$sql = "UPDATE nl_emails_removidos SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',DataRemocao='$data' WHERE ID='$nlid'";
										Runquery($db_link,$DB_name,$sql);
									} else {
										$sql = "INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido) values ('$id_grupo','$nome','$email','".date("Y-m-d")."','".$m_apelido."')";
										Runquery($db_link,$DB_name,$sql);
									}
									$nl1->db_close();
								}

	        					$sql = "DELETE FROM clientes_temp WHERE fbid='".$fbid."'";
	        					Runquery($db_link,$DB_name,$sql);
	        				}
						}
						$rs3->db_close();


					}
					$logins_rs->db_close();
					global $cli_id,$cli_token;
					$cli_id = $_SESSION['ss_cli_id'] = $ID;
					$cli_token = $_SESSION['ss_cli_token'] = uniqid("");
					$sql = "UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$ID;
					Runquery($db_link,$DB_name,$sql);

					foreach($_SESSION as $key => $val) {
						if(substr_count($key,'wish_prod_')) {
							if(addRemoveWishList($val,'add') == 0) {
								unset($_SESSION[$key]);
							}
						}
					}

					$_SESSION['ss_cli_ft'] = 0;//reset tentativas

					limparTentativaLogin();

					if($referer) {
						echo '<script>window.location.href="'.$referer.'";</script>';
						exit;
					} else {
						echo '<script>window.location.href="'.$GLOBALS['site_http_url'].'";</script>';
						exit;
					}
				}//GOOGLE
				elseif(isset($_POST['googleid']) && clean_var($_POST['googleid']) != '')
				{
					$googleid = clean_var($_POST['googleid']);
					$sql = "SELECT * FROM clientes WHERE googleid='".$googleid."'";
					// var_dump($sql);
					// die();
					$logins_rs->db_open($db_link,$DB_name,$sql);
					if(!$logins_rs->EOF())
					{
						$ID = $logins_rs->db_data['ID'];
						$rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
						$termos = isset($_POST['m_termos']) ? 1 : 0;
						$versao = $logins_rs->db_data['versao'];
						$data = date("Y-m-d H:i:s");
						if($rgpd == 1)
						{
							$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
							$rs->db_open($db_link,$DB_name,$sql);
							if(!$rs->EOF())
							{
								$versao = $rs->db_data['versao'];
							}
							$rs->db_close();
						}

						$sql = "UPDATE clientes SET Rgpd=$rgpd,Termos=$termos,versao=$versao,Data_ultimo_consentimento='$data' WHERE ID=".$ID;
						Runquery($db_link,$DB_name,$sql);
					}
					else  //registo pelo google
					{
						$sql = "SELECT * FROM clientes_temp WHERE googleid='".$googleid."'";
						$rs3->db_open($db_link,$DB_name,$sql);
						if(!$rs3->EOF())
						{
							$ID_temp = $rs3->db_data['ID'];
							$email = $rs3->db_data['Username'];
							$nome = $rs3->db_data['Nome'];
							$apelido = $rs3->db_data['Apelido'];
							$pais = $rs3->db_data['Pais'];
							$cidade = $rs3->db_data['password'];
							$rgpd = isset($_POST['m_rgpd']) ? 1 : 0;
							$termos = isset($_POST['m_termos']) ? 1 : 0;
							$versao = 0;
							$newsletter = 0;
							$data = date("Y-m-d H:i:s");
							if($rgpd == 1)
							{
								$newsletter = 1;
								$sql = "SELECT versao FROM pages WHERE ID = {$GLOBALS['rgpd_main_page_id']}";
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									$versao = $rs->db_data['versao'];
								}
								$rs->db_close();
							}
							$sql = "INSERT INTO clientes(Username,Email,Nome,Apelido,googleid,Estado,Ativo,Pais,Cidade,Password,Newsletter,Rgpd,Termos,versao,Data) VALUES('".$email."','".$email."','".$nome."','".$apelido."','".$googleid."',1,1,'".$pais."','".$cidade."','".$password."',$newsletter,$rgpd,$termos,$versao,'$data')";
							// var_dump($sql);
							// die();
	        				$res = Runquery($db_link,$DB_name,$sql);

	        				if($res)
	        				{
	        					$ID = db_get_last_insert_id($db_link);

	        					$nl1 = new DataObj;
								$nl2 = new DataObj;
								if($newsletter == 1) {

									$sql = "DELETE FROM nl_emails_removidos WHERE Email='$email'";
									Runquery($db_link,$DB_name,$sql);

									$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
									$nl2->db_open($db_link,$DB_name,$sql);
									if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
									else $id_grupo = 0;
									$nl2->db_close();

									$sql = "SELECT ID FROM nl_emails_registados WHERE Email='$email'";
									$nl1->db_open($db_link,$DB_name,$sql);
									if(!$nl1->EOF()) {
										$nlid = $nl1->db_data['ID'];
										$data = date("Y-m-d H:i:s");
										$sql = "UPDATE nl_emails_registados SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',data_hora='$data' WHERE ID='$nlid'";
										Runquery($db_link,$DB_name,$sql);
									} else {
										$sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido,Lang,Country,Rgpd) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."','".$apelido."','$lang','$site_country','$rgpd')";
										Runquery($db_link,$DB_name,$sql);
									}
									$nl1->db_close();
								} else {
									$sql = "DELETE FROM nl_emails_registados WHERE Email='$email'";
									Runquery($db_link,$DB_name,$sql);

									$sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
									$nl2->db_open($db_link,$DB_name,$sql);
									if(!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
									else $id_grupo = 0;
									$nl2->db_close();

									$sql = "SELECT ID FROM nl_emails_removidos WHERE Email='$email'";
									$nl1->db_open($db_link,$DB_name,$sql);
									if(!$nl1->EOF()) {
										$nlid = $nl1->db_data['ID'];
										$data = date("Y-m-d H:i:s");
										$sql = "UPDATE nl_emails_removidos SET ID_ORG='$id_grupo',Email='$email',Nome='$nome',Apelido='$apelido',DataRemocao='$data' WHERE ID='$nlid'";
										Runquery($db_link,$DB_name,$sql);
									} else {
										$sql = "INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido) values ('$id_grupo','$nome','$email','".date("Y-m-d")."','".$m_apelido."')";
										Runquery($db_link,$DB_name,$sql);
									}
									$nl1->db_close();
								}

	        					$sql = "DELETE FROM clientes_temp WHERE googleid='".$googleid."'";
	        					Runquery($db_link,$DB_name,$sql);
	        				}
						}
						$rs3->db_close();


					}
					$logins_rs->db_close();
					global $cli_id,$cli_token;
					$cli_id = $_SESSION['ss_cli_id'] = $ID;
					$cli_token = $_SESSION['ss_cli_token'] = uniqid("");
					$sql = "UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$ID;
					Runquery($db_link,$DB_name,$sql);

					foreach($_SESSION as $key => $val) {
						if(substr_count($key,'wish_prod_')) {
							if(addRemoveWishList($val,'add') == 0) {
								unset($_SESSION[$key]);
							}
						}
					}

					$_SESSION['ss_cli_ft'] = 0;//reset tentativas

					limparTentativaLogin();

					if($referer) {
						echo '<script>window.location.href="'.$referer.'";</script>';
						exit;
					} else {
						echo '<script>window.location.href="'.$GLOBALS['site_http_url'].'";</script>';
						exit;
					}
				}

			}
            if (isset($_POST['registar']) && $_POST['registar'] == 1)
			{
                $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                $logins_rs = new DataObj;
                $m_email = clean_var($_POST['m_email']);
                $sql = "SELECT * FROM clientes WHERE Username='$m_email'";
                $logins_rs->db_open($db_link,$DB_name,$sql);
                global $key_erro2;
                if (!$logins_rs->EOF())
				{
                    $error = 1;
                    $error_message = '<span style="">'.$key_erro2[$GLOBALS['site_lang']].'</span>';
                }
				else
				{
                    $m_nome = clean_var($_POST['m_nome']);
                    $m_apelido = clean_var($_POST['m_apelido']);
                    $m_email = clean_var($_POST['m_email']);
                    $m_newsletter = clean_var($_POST['m_newsletter']);
                    $m_password = encripta(clean_var($_POST['m_password']));
                    $dataRegisto = date("Y-m-d");


                    if($GLOBALS['registo_ativo_auto']==1)$sql = "INSERT INTO clientes (Username,Password,Nome,Apelido,Email,Newsletter,Data,Ativo) VALUES ('$m_email','$m_password','$m_nome','$m_apelido','$m_email','$m_newsletter','$dataRegisto','1')";
                    else $sql = "INSERT INTO clientes (Username,Password,Nome,Apelido,Email,Newsletter,Data) VALUES ('$m_email','$m_password','$m_nome','$m_apelido','$m_email','$m_newsletter','$dataRegisto')";
                    $res = Runquery($db_link,$DB_name,$sql);
                    if (!$res)
					{
                        echo db_error($db_link).$sql;
                        show_clienteForm();
                    }
					else
					{
						$new_id = db_get_last_insert_id($db_link); // isto tem que estar antes do envio do email porque se nao o id cliente deixa de ser o correto.
						save_endereco($new_id);

                        $nl1 = new DataObj;
                        $nl2 = new DataObj;
                        if ($m_newsletter == 1)
						{
                            $sql = "DELETE FROM nl_emails_removidos WHERE Email='$m_email'";
                            Runquery($db_link,$DB_name,$sql);

                            $sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
                            $nl2->db_open($db_link,$DB_name,$sql);
                            if (!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
                            else $id_grupo = 0;
                            $nl2->db_close();

                            $sql = "SELECT ID FROM nl_emails_registados WHERE Email='$m_email'";
                            $nl1->db_open($db_link,$DB_name,$sql);
                            if (!$nl1->EOF()) {
                                $nlid = $nl1->db_data['ID'];
                                $data = date("Y-m-d H:i:s");
                                $sql = "UPDATE nl_emails_registados SET ID_ORG='$id_grupo',Email='$m_email',Nome='$m_nome',Apelido='$m_apelido',data_hora='$data' WHERE ID='$nlid'";
                                Runquery($db_link,$DB_name,$sql);
                            } else {
                                $sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora,Apelido) values ('$id_grupo','$m_nome','$m_email','".date("Y-m-d H:i:s")."','".$m_apelido."')";
                                Runquery($db_link,$DB_name,$sql);
                            }
                            $nl1->db_close();
                        } else {
                            $sql = "DELETE FROM nl_emails_registados WHERE Email='$m_email'";
                            Runquery($db_link,$DB_name,$sql);

                            $sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
                            $nl2->db_open($db_link,$DB_name,$sql);
                            if (!$nl2->EOF()) $id_grupo = $nl2->db_data['ID'];
                            else $id_grupo = 0;
                            $nl2->db_close();

                            $sql = "SELECT ID FROM nl_emails_removidos WHERE Email='$m_email'";
                            $nl1->db_open($db_link,$DB_name,$sql);
                            if (!$nl1->EOF()) {
                                $nlid = $nl1->db_data['ID'];
                                $data = date("Y-m-d H:i:s");
                                $sql = "UPDATE nl_emails_removidos SET ID_ORG='$id_grupo',Email='$m_email',Nome='$m_nome',Apelido='$m_apelido',DataRemocao='$data' WHERE ID='$nlid'";
                                Runquery($db_link,$DB_name,$sql);
                            } else {
                                $sql = "INSERT INTO nl_emails_removidos (ID_ORG,Nome,Email,DataRemocao,Apelido) values ('$id_grupo','$m_nome','$m_email','".date("Y-m-d")."','".$m_apelido."')";
                                Runquery($db_link,$DB_name,$sql);
                            }
                            $nl1->db_close();
                        }

						$morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : '';
						// $telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : '';
						$codigo_postal = (isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : (isset($_POST['m_codpos']) ? $_POST['m_codpos'] : ''));
						$cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : '';
						$contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : '';
						$pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : 165;
						$pais = country_name($pais);

                        //registo aceite
                        if ($GLOBALS['registo_ativo_auto']) $msg = getLinguas('key_registo4b1',$GLOBALS['site_lang']);
                        else $msg = getLinguas('key_registo4b2',$GLOBALS['site_lang']);
                        $cli_token = uniqid("");
                        $sql = "UPDATE clientes SET failed_attempts=0,Token='$cli_token' WHERE ID='$new_id'";
                        Runquery($db_link,$DB_name,$sql);


                        if($GLOBALS['tem_cod_instituicao']==1)
                        {
                        	$cod_instituicao = isset($_POST['m_cod_instituicao']) ? $_POST['m_cod_instituicao'] : '';

                        	$sql = "UPDATE clientes SET cod_instituicao='$cod_instituicao' WHERE ID='$new_id'";
                        	Runquery($db_link,$DB_name,$sql);
                        }
                        echo show_alerta2($msg);
                        $body = array
                        (
                            '[code]' => SITE_HTTP_URL.seo('pages.php?page_id='.$GLOBALS['verify_page_id'],'','',$GLOBALS['site_lang']).'?code='.$cli_token,
                            '[name]' => $m_nome,
                            '[apelido]' => $m_apelido,
                            '[surname]' => $m_apelido,
                            '[email]' => $m_email,
                            '[username]' => $m_email,
                            // '[password]' => clean_var($_POST['m_password']),
                            '[password]' => '...',
                            '[address]' => $morada,
                            '[zipcode]' => $codigo_postal,
                            '[city]' => $cidade,
                            '[country]' => $pais,
                            '[nif]' => $contribuinte
                        );
                        $message = template_email($GLOBALS['template_clientes'],$body);
                        send_email($m_email,$message['subject'],$message['body']);
						// echo '<pre>';
						// echo 'email: '.$m_email.'<br>subject: '.$message['subject'].'<br>body: '.$message['body'];
						// echo '</pre>';
                        // send_email($GLOBALS['Default_notifications_email'],'Novo '.$message['subject'],$message['body']);
                        echo '<script>setTimeout(function(){window.location.href="'.$GLOBALS['site_http_url'].'index.php";},30000);</script>';

                    }
                }
                $logins_rs->db_close();
                if ($error == 1) show_clienteForm();
            }
			elseif ($_POST['registar_update'] == 1)
			{
                $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                $m_nome = clean_var($_POST['m_nome']);
                $m_apelido = clean_var($_POST['m_apelido']);
                $m_email = clean_var($_POST['m_email']);
                $m_password = encripta(clean_var($_POST['m_password']));
                if (isset($_POST['m_password']) && $_POST['m_password'] != '') $pp = 'Password="'.$m_password.'",';
                else $pp = '';
                $dateModify = date("Y-m-d");
                $sql = "UPDATE clientes SET $pp Nome='$m_nome',Apelido='$m_apelido',Email='$m_email',Data_modify='$dateModify' WHERE ID='{$GLOBALS['cli_id']}' AND Token='{$GLOBALS['cli_token']}'";
                $res = Runquery($db_link,$DB_name,$sql);
                if (!$res)
				{
                    echo db_error($db_link);
                    show_clienteForm();
                }
				else
				{
                    $msg = getLinguas('key_registo3b',$GLOBALS['site_lang']);
                    echo show_alerta2($msg);
                    echo '<script>setTimeout(function(){window.location.href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'";},30000);</script>';
                }
            }
        }
    }
}
function save_endereco($client_id=0)
{
	if (isset($_POST['novo_endereco']) || $client_id > 0)
	{

		if($client_id > 0) $client_id_save = $client_id; elseif(isset($GLOBALS['cli_id']) && $GLOBALS['cli_id'] > 0) $client_id_save = $GLOBALS['cli_id'];

		$nome = isset($_POST['m_nome']) ? $_POST['m_nome'] : '';
		$apelido = isset($_POST['m_apelido']) ? $_POST['m_apelido'] : '';
		$designacao = isset($_POST['m_designacao']) ? $_POST['m_designacao'] : '';
		$morada = isset($_POST['m_morada']) ? $_POST['m_morada'] : '';
		$telefone = isset($_POST['m_telefone']) ? $_POST['m_telefone'] : '';
		$codigo_postal = (isset($_POST['m_codigo_postal']) ? $_POST['m_codigo_postal'] : (isset($_POST['m_codpos']) ? $_POST['m_codpos'] : ''));
		$cidade = isset($_POST['m_cidade']) ? $_POST['m_cidade'] : '';
		$contribuinte = isset($_POST['m_contribuinte']) ? $_POST['m_contribuinte'] : '';
		$pais = isset($_POST['m_pais']) ? $_POST['m_pais'] : 165;



		// if ( $nome != '' &&  $apelido != '' && $morada != '' && $telefone != '' && $codigo_postal != '' && $cidade != '' && $pais > 0 && $contribuinte != '')
		// if ( $nome != '' &&  $morada != '' && $telefone != '' && $codigo_postal != '' && $cidade != '' && $pais > 0 && $contribuinte != '')
		if ( $nome != '' &&  $morada != '' && $telefone != '' && $codigo_postal != '' && $cidade != '' && $pais > 0)
		{
			$db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
			$sql = "INSERT INTO clientes_enderecos(ID_Cliente,Nome,Apelido,Designacao,Morada,Codigo_Postal,Cidade,Pais,Telefone,Nif) VALUES ('$client_id_save','$nome','$apelido','$designacao','$morada','$codigo_postal','$cidade','$pais','$telefone','$contribuinte')";
			// echo '<br>'.$sql;
			Runquery($db_link,$GLOBALS['DB_name'],$sql);
			// die();
		}
		else
		{
			// echo 'Faltam campos!';
			// die();
		}
	}


}
function show_clienteForm()
{
    if ($dados = cliente_logado())
	{
		// echo menu_perfil('PC');
        // showFormEditarPerfil();
		// EM LAYOUT/PROJETO/CODE/AREA_CLIENTE.PHP
		if (isset($GLOBALS['show_area_cliente_personalizada']) && $GLOBALS['show_area_cliente_personalizada']==1) show_area_cliente();
		else show_area_cliente_plataforma();
		
    }
	else
	{
        if (isset($GLOBALS['registar']) && $GLOBALS['registar'])
        {
            showFormRegisto();
        }
        elseif (isset($GLOBALS['recupera']) &&$GLOBALS['recupera'])
        {
            showFormRecuperarPassword();
        }
        else
        {
            login_form();
        }
    }

}

function show_area_cliente_plataforma($class='PC')
{
	$dados_cliente = cliente_logado();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$dm=new Dataobj;

	//Vai buscar o ID da ultima encomenda feita para redireccionar o cliente para essa encomenda
	if(isset($_GET['last']) && $_GET['last'] > 0)
	{
		$sql_all="Select ID from encomendas where ID_Cliente=".$dados_cliente['cli_id']." AND Estado > -1 order by ID DESC limit 0,1";
		$rs->db_open($db_link,$DB_name,$sql_all);
		if(!$rs->EOF())
		{
			$GLOBALS['id_enc']=$_GET['id_enc'] = $rs->db_data['ID'];
		}
		$rs->db_close();
	}


	//Agora vai carregar todos os menus que existam e que tenham sido carregados com o tipo menu_reservado
	$return='';
	$return_resp='';
	$sql = 'SELECT m.ID,m.catalogo_id,m.tipo_catalogo,m.img_ext,m.TipoMenu,m.URL_Link,ml.URL_Nome2
			FROM menus_links m
			INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
			WHERE
				m.TipoMenu = "menu_reservado"
				AND m.Visivel=1
				AND ml.Lang="'.$GLOBALS['site_lang'].'"
				'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
			ORDER BY m.Ordem ASC';
	// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	$return = '';
	if(!$rs->EOF())
	{
		echo '<div class="tabs" id="tabs-edit">	';
		$tabs = 0;
		while(!$rs->EOF())
		{
			$menu_id = $rs->db_data['ID'];
			$menu_ext = $rs->db_data['img_ext'];
			$catalogo_id = $rs->db_data['catalogo_id'];
			$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
			$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
			$menu_nome = $rs->db_data['URL_Nome2'];
			$menu_url = seo($rs->db_data['URL_Link']);
			if(isset($_GET['show_carteira']) && substr_count($menu_url,'show_carteira')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_compras']) && substr_count($menu_url,'show_compras')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_wishlist']) && substr_count($menu_url,'show_wishlist')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(isset($_GET['show_rmas']) && substr_count($menu_url,'show_rmas')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			elseif(!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas']) && !isset($_GET['show_wishlist']) && !substr_count($menu_url,'show_wishlist')&& !substr_count($menu_url,'show_compras')&& !substr_count($menu_url,'show_carteira')&& !substr_count($menu_url,'show_rmas')) echo '<div class="tab active" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			else echo '<div class="tab" id="tab'.$menu_id.'" onclick="window.open(\''.$menu_url.'\',\'_self\')"><div class="titulo-edit">'.$menu_nome.'</div></div>';
			$tabs = 1;
			$rs->db_next();
		}

		//Agora vamos criar os tabs correspondentes a cada opção de menu:
		echo '<div class="clear"></div>';
		if(isset($tabs) && $tabs > 0)
		{			
			echo '<div class="tabs-content">';
			if(!isset($_GET['show_carteira']) && !isset($_GET['show_compras']) && !isset($_GET['show_rmas']) && !isset($_GET['show_wishlist']) && !isset($_GET['show_domain']))
				echo '<div class="tab-content active" id="tab-content1">'.showFormEditarPerfil($dados_cliente).'</div>';
			elseif(isset($_GET['show_carteira']) && $_GET['show_carteira'] == 1)
				echo '<div class="tab-content active" id="tab-content2">'.loadLivroEnderecos().'</div>';
			elseif(isset($_GET['show_compras']) && $_GET['show_compras'] == 1)
			{
				if (isset($GLOBALS['show_area_cliente_personalizada']) && $GLOBALS['show_area_cliente_personalizada']==1)echo '<div class="tab-content active" id="tab-content3">'.showMenageOrders($dados_cliente).'</div>';
				else echo '<div class="tab-content active" id="tab-content3">'.showMyOrdersPlataforma($dados_cliente).'</div>';
			}
			// elseif(isset($_GET['show_domain']) && $_GET['show_domain'] == 1)
			// 	echo '<div class="tab-content active" id="tab-content4">'.showDomains($dados_cliente).'</div>';
			elseif(isset($_GET['show_wishlist']) && $_GET['show_wishlist'] == 1)
				echo '<div class="tab-content active" id="tab-content5">'.bloco_catalogo().'</div>';
			echo '</div>';
		}

		echo '</div>';
	}
	$rs->db_close(1);
}

function showMyOrdersPlataforma($dados)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$cli_id,$page_id,$key_wishlist_notloggedin,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new Dataobj;
	$i=0;
	if(!$dados)
    {
		echo ($key_wishlist_notloggedin[$site_lang]);
	}
    else
    {
    	if(isset($_GET['id_enc']) && intval($_GET['id_enc']) > 0)
		{

    		if(isset($_GET['novo_metodo']) && intval($_GET['novo_metodo']) > 0)
    		{
    			//Mudamos o metodo de pagamento
				$sql='UPDATE encomendas SET Metodo_pagamento= "'.intval($_GET['novo_metodo']).'" where ID_Cliente='.$cli_id.' AND ID = '.intval($_GET['id_enc']);
				Runquery($db_link,$GLOBALS['DB_name'],$sql);

				$idEncomenda=intval($_GET['id_enc']);
				$idMetodo=intval($_GET['novo_metodo']);

				$sql='Select * from encomendas where ID_Cliente='.$cli_id.' AND ID = '.intval($_GET['id_enc']);
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF())
				{
					$TotalIva=$rs->db_data["TotalIva"];
				}
				$rs->db_close();


				//Vamos atualizar uma variavel de post que e usada na funcao do site:
				if(isset($_GET['mbway_tel']) && $_GET['mbway_tel'] != '')$_POST['mbway_tel']=$_GET['mbway_tel'];
				echo tratarMetodosPagamento($idEncomenda,$idMetodo,$totalEncomenda);
				

    		}
			


			$sql='Select * from encomendas where ID_Cliente='.$cli_id.' AND ID = '.intval($_GET['id_enc']);
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$IDb=$rs->db_data["ID"];
				$Referencia=$rs->db_data["Referencia"];
				$Cond_pag=$rs->db_data["Cond_pag"];
				$Local_Entrega=$rs->db_data["Local_Entrega"];
				$Tipo=$rs->db_data["Tipo"];
				$Anulada=$rs->db_data["Anulada"];
				$RazaoAnulacao=$rs->db_data["RazaoAnulacao"];
				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$N_Contribuinte=$rs->db_data["enc_Nif"];
				$Des_Social=$rs->db_data["Des_Social"];
				$enc_Nome = $rs->db_data["enc_Nome"];
				$enc_Apelido = $rs->db_data["enc_Apelido"];
				$enc_NomeEntrega = $rs->db_data["enc_Apelido_ent"];
				$enc_ApelidoEntrega = $rs->db_data["enc_Apelido_ent"];
				$Morada=$rs->db_data["enc_Morada"];
				$Localidade=$rs->db_data["enc_Localidade"];
				$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
				$Cidade=$rs->db_data["enc_Cidade"];
				$CidadeEntrega=$rs->db_data["enc_Cidade_ent"];
				$Pais=country_name($rs->db_data["enc_Pais"]);
				$PaisEntrega=country_name($rs->db_data["enc_Pais_ent"]);
				$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
				$LocalidadeEntrega=$rs->db_data["LocalidadeEntrega"];
				$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
				$telefone=$rs->db_data["enc_Telefone"];
				$email=$rs->db_data["enc_Email"];
				$Obs=$rs->db_data["Obs"];
				$Portes=$rs->db_data["Portes"];
				$PortesSemIva=$rs->db_data["PortesSemIva"];
				$Taxas=$rs->db_data["Taxas"];
				$TaxasSemIva=$rs->db_data["TaxasSemIva"];
				$Cobrar=$rs->db_data["Cobrar"];
				$TaxasPaypal=$rs->db_data["TaxasPaypal"];
				$ValorDesconto=$rs->db_data["ValorDesconto"];
				$ID_Vendedor=$rs->db_data["ID_Vendedor"];
				$ID_Distribuidor=$rs->db_data["ID_Distribuidor"];
				$PropostaDe=$rs->db_data["PropostaDe"];
				$ID_Armazem=$rs->db_data["ID_Armazem"];
				$Local_carga=$rs->db_data["Local_carga"];
				$Local_descarga=$rs->db_data["Local_descarga"];
				$Expedicao=$rs->db_data["Expedicao"];
				$Viatura_expedicao=$rs->db_data["Viatura_expedicao"];
				$ID_Expedicao=$rs->db_data["ID_Expedicao"];
				$IDGuia=$rs->db_data["IDGuia"];
				$Operador=$rs->db_data["Operador"];
				$ValorDescontoExtra=$rs->db_data["ValorDescontoExtra"];
				$ValorJustificado=$rs->db_data["ValorJustificado"];
				$TotalDocComIva=$rs->db_data["TotalDocComIva"];
				$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
				$TotalDocDescontos=$rs->db_data["TotalDocDescontos"];
				$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
				$TotalDocIva=$rs->db_data["TotalDocIva"];
				$Data_Criacao=$rs->db_data["Data_Criacao"];
				$Data_Impressao=$rs->db_data["Data_Impressao"];
				$Data_Doc_Forn=$rs->db_data["Data_Doc_Forn"];
				$Data_expedicao=$rs->db_data["Data_expedicao"];
				$Data_entrega=$rs->db_data["Data_entrega"];
				$Estado=$rs->db_data["Estado"];
				$m_guia=$rs->db_data["IDGuia"];
				$MOEDA=$rs->db_data["MOEDA"];
				$entidadeatm=$rs->db_data["Ent_atm"];
				$referenciaatm=$rs->db_data["Ref_atm"];
				$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
				$Metodo_expedicao=$rs->db_data["Metodo_envio"];
				$Encomenda_Paga=$rs->db_data["Encomenda_Paga"];
				$enc_token=$rs->db_data["enc_token"];
				$TotalIva=$rs->db_data["TotalIva"];
				$enc_Fatura = $rs->db_data["enc_Fatura"];

				$enc_info='';

				$corTexto = '#FFF';
				if($count % 2 != 0)	{$cor="#999";$n="1";}
				else				{$cor="#777";$n="2";}

				if(!$m_guia)$m_guia='N/A';

				$enc_state =  estado_encomenda($Estado, $GLOBALS['site_lang']);
				$enc_info = ($enc_state['titulo2']!='' ? $enc_state['titulo2']: $enc_state['titulo']);
				$cor =  '#'.$enc_state['cor'];
				$corTexto = '#000';
				$extra='';
				$i =0;

				global $id_enc;
				if(isset($id_enc)) $id_enc=intval($id_enc);
				if(isset($_GET['pagina']) && intval($_GET['pagina']) > 0) $fromurl = '&paginato='.intval($_GET['pagina']);
				if(isset($_GET['paginato']) && intval($_GET['paginato']) > 0) $tourl = '?pagina='.intval($_GET['paginato']);
				$url_on = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_enc='.$IDb.$fromurl.'&show_compras=1';
				$url_off = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).$tourl.'?show_compras=1';


				?>
				<style type="text/css">
					.shopping-cart2 td{font-size:12px;padding: 5px}

					.shopping-cart2 .form-group{font-size:12px;font-weight:300;}
					.shopping-cart2 .form-group label{font-size:12px}
				</style>
				<div class="text-right"><div style="font-size:14px;padding:10px 0;margin-bottom:0;cursor:pointer" onclick="window.open('<?=$url_off?>', '_self')"> <i class="fa fa-times" aria-hidden="true"></i> Fechar Resumo</div></div>

				<div class=''>
					<div class="row shopping-cart2" style="background-color: #fffdf5;padding:10px">
						<table width="100%" border=0 cellspacing=0 cellpadding="4">
	                        <tr>
	                            <td align="left" width="120"><i class="fas fa-file-invoice-dollar"></i> Nº da encomenda:</td>
	                            <td align="left"><b><?=$IDb?></b></b></td>
	                        </tr>
	                        <tr>
	                        	<td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fas fa-truck"></i> Tracking:</td>
	                        	<td align="left" style="border-top:#ccc 1px solid"><?=$m_guia?></td>
	                        </tr>
							<tr>
	                            <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="far fa-calendar-alt"></i> Data da encomenda:</td>
	                            <td align="left" style="border-top:#ccc 1px solid"><?=$Data_Criacao?></td>
	                        </tr>
	                        <tr>
	                            <td align="left" width="190" style="border-top:#ccc 1px solid"><i class="fas fa-clock"></i> Estado da encomenda:</td>
	                            <td align="left" style="border-top:#ccc 1px solid">
								<?php
								$enc_state =  estado_encomenda($Estado, 'pt');
								echo '<span style="color:#'.$enc_state['cor'].';font-weight:bold">'.$enc_state['titulo'].'</span>';
								?>
								</td>
	                        </tr>
	                        <?php
	                        	$cor='#000';
								if(getNomeMetodoPagamentoTipo($Metodo_pagamento) == 1)
								{
									$Encomenda_Status2 = $Encomenda_Status;
									switch ($Encomenda_Status)
									{
										case "Canceled_Reversal":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_paypal_canceled_reversal');
											break;
										case "Completed":
	                        				$cor='#1ace3f';
											$Encomenda_Status = write_lang('key_paypal_completed');
											break;
										case "Created":
											$Encomenda_Status = write_lang('key_paypal_created');
											break;
										case "Denied":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_paypal_denied');
											break;
										case "Expired":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_paypal_expired');
											break;
										case "Failed":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_paypal_failed');
											break;
										case "Pending":
											$Encomenda_Status = write_lang('key_paypal_pending');
											break;
										case "Refunded":
											$Encomenda_Status = write_lang('key_paypal_refunded');
											break;
										case "Reversed":
											$Encomenda_Status = write_lang('key_paypal_reversed');
											break;
										case "Processed":
											$Encomenda_Status = write_lang('key_paypal_processed');
											break;
										case "Voided":
											$Encomenda_Status = write_lang('key_paypal_voided');
											break;
										default:
											$Encomenda_Status = write_lang('key_paypal_default');
											break;
									}
									global $site_title;
									?>
									<tr>
	                                    <td align="left" width="120" >Estado do PayPal:</td>
	                                    <td align="left"><?=$Encomenda_Status?></td>
	                                </tr>
									<?
								}
								else if(getNomeMetodoPagamentoTipo($Metodo_pagamento)==3)
								{
									$Encomenda_Status2 = $Encomenda_Status;
									switch ($Encomenda_Status)
									{
										case "Success":
	                        				$cor='#1ace3f';
											$Encomenda_Status = write_lang('key_unicre_success');
											break;
										case "Cancel":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_cancel');
											break;
										case "Denied":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_denied');
											break;
										case "Failed_Fraud":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_deniedfraud');
											break;
										case "Failed_Technical":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_failedtechnical');
											break;
										case "Failed":
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_failed');
											break;
										default:
	                        				$cor='#ff0000';
											$Encomenda_Status = write_lang('key_unicre_default');
											break;
									}
									global $site_title;
									
									/*?>
									<tr>
	                                    <td align="left" width="120" >Estado da Unicre:</td>
	                                    <td align="left" style="color:<?=$cor?>"><?=$Encomenda_Status?></td>
	                                </tr>
									<?*/
								}
								if($Encomenda_Paga==1)
								{
									if($Metodo_pagamento==13)
									{
										echo '<tr>
	                                    <td align="left" width="120">&nbsp;</td>
	                                    <td align="left"><div style="color:#1ace3f"><b>Encomenda paga com sucesso através de cartão de crédito</b></div>
	                                    </td></tr>';
									}
									elseif($Metodo_pagamento==18)
									{
										echo '<tr>
	                                    <td align="left" width="120">&nbsp;</td>
	                                    <td align="left"><div style="color:#1ace3f"><b>Encomenda paga com sucesso através de MB WAY</b></div>
	                                    </td></tr>';
									}
									else
									{
										echo '<tr>
	                                    <td align="left" width="120" >&nbsp;</td>
	                                    <td align="left"><div style="color:#1ace3f"><b>Encomenda paga</b></div>
	                                    </td></tr>';
									}
								}
								else
								{
									if($Metodo_pagamento==13)
									{
										//Rede unicre - VISA
										$rs2->db_open($db_link, $DB_name,'SELECT * FROM pagamentos_unicre WHERE ID_ENC = '.$IDb.' ORDER BY ID DESC limit 0,1');
										$Estado_unicre = $rs2->db_data['Estado'];
										$Code = $rs2->db_data['Code'];
										$rs2->db_close();
										if($Estado==0)
										{
											echo '<tr>
		                                    <td align="left" width="120" >&nbsp;</td>
		                                    <td align="left">';
											if($Estado_unicre==0)
											{
												echo '<b>Encomenda aguarda pagamento</b>';
												//echo '<br><b>Clique para pagar encomenda</b>';
											}
											if($Estado_unicre==4)echo '<div style="color:#ff0000"><b>Encomenda não paga</b><br>'.unicre_erro($Code).'</div>';
											if($Estado_unicre==13)echo '<div style="color:#1ace3f"><b>Encomenda paga com sucesso através de cartão de crédito</b></div>';
											echo '</td></tr>';
										}

									}
									if($Metodo_pagamento==18)
									{
										//Rede unicre - mbway
										//echo 'SELECT * FROM pagamentos_unicre WHERE ID_ENC = '.$IDb;


										$rs2->db_open($db_link, $DB_name,'SELECT * FROM pagamentos_unicre WHERE ID_ENC = '.$IDb.' ORDER BY ID DESC limit 0,1');
										$Estado_unicre = $rs2->db_data['Estado'];
										$Code = $rs2->db_data['Code'];
										$rs2->db_close();

										if($Estado==0 || $Estado==14 || $Estado==15)
										{
											echo '<tr>
		                                    <td align="left" width="120" >&nbsp;</td>
		                                    <td align="left">';
											if($Estado_unicre==0)
											{
												echo '<b>Encomenda aguarda pagamento</b>';
												//echo '<br><b>Clique para pagar encomenda</b>';
											}
											if($Estado_unicre==4)echo '<div style="color:#ff0000"><b>Erro obtido: </b>'.unicre_erro($Code).'</div>';
											//if($Estado_unicre==13)echo '<div style="color:#1ace3f"><b>Encomenda paga com sucesso através de mbway</b></div>';
											echo '</td></tr>';
										}

									}

									if($Estado==0 || $Estado==14 || $Estado==15 || $Estado==25)
									{
										//Tentar pagar novamente, mas so se nao estiver a decorrer um prazo para pagamento
										$aguarda=0;
										$mostra_opcoes_pagamento=1;

										$sql="SELECT * from sibs_mbway_logs where ID_Encomenda=$IDb and Estado=0 order BY ID DESC limit 0,1";
										$rs2->db_open($db_link, $DB_name,$sql);
										if(!$rs2->EOF())
										{
											$aguarda=1;
											$Data=$rs2->db_data['Data'];
										}
										$rs2->db_close();

										if($aguarda==1)
										{
											//Caso ainda não tenham passardo os 4 minutos?
											$from_time = strtotime($Data);
											$to_time = strtotime(date("Y-m-d H:m:i"));

											if(round(abs($to_time - $from_time) / 60,2)*100>4)
											{
												$Estado=5;
												$sql="UPDATE sibs_mbway_logs set Estado=5 where ID_Encomenda=$IDb";
												Runquery($db_link, $GLOBALS['DB_name'], $sql);
												$sql="UPDATE encomendas set Estado=25 where ID=$ID_Encomenda";
												Runquery($db_link, $GLOBALS['DB_name'], $sql);

												$sql="UPDATE pagamentos_unicre set Estado=5 where ID_ENC=$ID_Encomenda";
												Runquery($db_link, $GLOBALS['DB_name'], $sql);
											}
											else
											{
												//Ainda nao passaram 4 minutos
												$mostra_opcoes_pagamento=0;
											}
										}

										if($mostra_opcoes_pagamento)
										{
											$cl = cliente_perfil($GLOBALS['cli_id']);
											if($cl['Testes']=='1')$sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$site_lang' ORDER BY mp.ID ASC";
										    else $sql="SELECT mp.*,mpl.Cabecalho,mpl.Texto1 FROM metodos_pagamento AS mp INNER JOIN metodos_pagamento_lang AS mpl ON mp.ID=mpl.ID_PAI WHERE mpl.Lang='$site_lang' AND Visivel='1' ORDER BY mp.ID ASC";
										    $rs->db_open($db_link,$GLOBALS['DB_name'], $sql);

										    if(!$rs->EOF())
										    {
										    	echo '<tr>
				                                    <td align="left" width="120" >&nbsp;</td>
				                                    <td align="left"><h2><b>Tente pagar a encomenda novamente através de um dos seguintes métodos disponiveis</b></h2>';
										    	$pos=0;
								                while (!$rs->EOF())
								                {
								                    $nome_metodo=$rs->db_data['Cabecalho'];
								                    $ID=$rs->db_data['ID'];
								                    $metodo_descricao=$rs->db_data['Texto1'];
								                    $img_ext=$rs->db_data['img_ext'];
								                    $tipo=$rs->db_data['Tipo'];
								                    $pos++;
								                    ?>						                    
								                    <div class="form-group" style="background-color:#fff">
								                        <div class="radio">
								                            <label for="pagamento<?=$ID ?>" style="font-size:12px;border:solid 1px #ccc;width:100%;padding:5px;cursor:pointer">
								                                <div style="height:50px;line-height:50px;float:left">
								                                	<input class="metodos_pagamento" type="radio" name="m_metodopagamento" id="pagamento<?=$ID ?>" value="<?=$ID ?>" <?php echo ($Metodo_pagamento == $ID) ? ' checked="checked" ' : ''; ?>></div>
								                                <?php
								                                    if(stream_resolve_include_path($GLOBALS['site_shell_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext) )
								                                    {
								                                        echo '<div style="float:left;height:50px;margin-right:10px;width:150px"><img style="height:50px" src="'.$GLOBALS['site_http_url'].'files/metodos_pagamento/'.$ID.'_1.'.$img_ext.'?dp='.dumpcache.'" alt="'.$nome_metodo.'" /></div>';
								                                        if($metodo_descricao != '')
								                                        {
								                                            echo '<div class="pagamentos_descricao" style="min-height:50px;float:left;display:flex"><div style="align-self:center">';
								                                            echo str_replace("\n",'<br>', $metodo_descricao);
								                                            echo '</div></div>';
								                                        }
								                                    }
								                                    else
								                                    {
								                                        echo '<div style="height:50px;float:left;display:flex"><div style="align-self:center">';
								                                        if($metodo_descricao != '')echo str_replace("\n",'<br>', $metodo_descricao);
								                                        else echo $nome_metodo;
								                                        echo '</div></div>';
								                                    }?>
								                                    <br>
								                            <?php
								                            if( ($tipo==MP_TP_MBWAY_SIBS || $tipo==MP_TP_MBWAY_CGD || $tipo==MP_TP_MBWAY_LUSOPAY || $tipo==MP_TP_MBWAY_IFTHEN || $tipo==MP_TP_MBWAY_EUPAGO || $tipo==MP_TP_MBWAY_UNICRE) )
								                            {
								                            	$Metodo_pagamento_MBWAY=$ID;
								                                echo '<div style="display:table;width:100%;padding:10px" class="info_pagamento" id="info_'.$tipo.'" >
								                                    <br>Para solicitar o pagamento por MBWay, indique o telemóvel associado à sua aplicação MBWay <br>
								                                    A solicitação de pagamento só será efetuada após concluir a encomenda:<br>
								                                    <input class="form-control" style="width:150px" type="text" id="mbway_tel" name="mbway_tel"> (Não necessita de colocar +351)</div>';
								                            }

								                            ?>
								                            </label>
								                        </div>
								                    </div>
								                    <?php




								                    $rs->db_next();
								                }
								                $rs->db_close();

								                $url = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_enc='.$IDb.'&show_compras=1';
								                echo '<input type="button" class="button3 btn-primary" value="PAGAR" onclick="pagar_encomenda()">
								                <script>
								                function pagar_encomenda()
								                {
								                	var novo_metodo_pagamento = $(\'input[name="m_metodopagamento"]:checked\').val();
								                	if(novo_metodo_pagamento == '.$Metodo_pagamento_MBWAY.')
								                	{
								                		var mbway_tel = $(\'#mbway_tel\').val();
								                		window.open("'.$url.'&novo_metodo="+novo_metodo_pagamento+"&mbway_tel="+mbway_tel,"_top");
								                	}
								                	else window.open("'.$url.'&novo_metodo="+novo_metodo_pagamento,"_top");
								                }
								                </script>

								                </td></tr>';
										    }
										}
									}


								}

							?>
							<tr>
	                            <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fa fa-credit-card"></i> Metodo de Pagamento:</td>
	                            <td align="left" style="border-top:#ccc 1px solid"><?php 
	                            	

	                            	if($Metodo_pagamento==17)
									{
										$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc;margin:0 auto" align="center">';
										$tabelaMulti .= "<tr>";
										$tabelaMulti .=	'<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by LUSOPAY)</center></td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "<tr>";
										$tabelaMulti .=	'<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_MB125x80px.png" alt=""/></div></td>';
										$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
										$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.$entidadeatm.'</td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "<tr>";
										$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
										$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'. $referenciaatm.'</td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "<tr>";
										$tabelaMulti .=	'<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
										$tabelaMulti .=	'<td style="font-size: x-small; text-align:left">'.numberformat($TotalDocComIva).'</td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "<tr>";
										$tabelaMulti .= '<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
										$tabelaMulti .= '<td style="font-size: x-small; text-align:left">&nbsp;</td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "<tr>";
										$tabelaMulti .=	'<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
										$tabelaMulti .= "</tr>";
										$tabelaMulti .= "</table>";
										echo $tabelaMulti;
									}
									else echo getNomeMetodoPagamentoByTipo_plataforma($Metodo_pagamento);


	                            ?></td>
	                        </tr>
	                        <?php if(getNomeMetodoPagamentoTipo($Metodo_pagamento) != 3){?>
							<tr>
	                            <td align="left" width="120" height="25">&nbsp;</td>
	                            <td align="left" style="font-weight:bold;color:#000;font-size:14px"><?php echo str_replace('margin:0 auto;', 'margin:0;background:#FFF;', str_replace('align="center"', 'align="left"', $enc_pagamento_dados)); ?></td>
	                        </tr>
							<?php }?>
							<tr>
	                            <td align="left" width="120" style="border-top:#ccc 1px solid"><i class="fa fa-book"></i> Metodo de Expedição:</td>
	                            <td align="left" style="border-top:#ccc 1px solid"><?php echo getNomeMetodoExpedicao_plataforma($Metodo_expedicao)?></td>
	                        </tr>
	                    </table><br><br>
	                </div>

	                <br>





				<div class="row shopping-cart2" style="background-color: #f8fff5;padding:10px">
				<div class="col-md-6">
				<i class="fas fa-coins"></i>&nbsp;<b>Dados de Facturação</b><br>	<br>
				<div class="row">
					<div class="clear"></div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="m_nome">Nome</label><br>
							<?=$enc_Nome;?>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Apelido</label><br>
							<?=$enc_Apelido;?>
						</div>
					</div>
					<div class="clear"></div>
					<div class="col-xs-12">
						<div class="form-group">
							<label for="m_nome">Morada</label><br>
							<?=$Morada;?>
						</div>
					</div>
					<div class="clear"></div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Código Postal</label><br>
							<?=$Cod_Pos;?>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Localidade</label><br>
							<?=$Cidade;?>
						</div>
					</div>
				</div>
				</div>
				<div class="col-md-6">
				<i class="fas fa-truck"></i>&nbsp;<b>Dados de Entrega</b><br>	<br>
				<div class="row">
					<div class="clear"></div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="m_nome">Nome</label><br>
							<?=$enc_NomeEntrega;?>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Apelido</label><br>
							<?=$enc_ApelidoEntrega;?>
						</div>
					</div>
					<div class="clear"></div>
					<div class="col-xs-12">
						<div class="form-group">
							<label for="m_nome">Morada</label><br>
							<?=$MoradaEntrega;?>
						</div>
					</div>
					<div class="clear"></div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Código Postal</label><br>
							<?=$Cod_PosEntrega;?>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">

							<label for="m_apelido">Localidade</label><br>
							<?=$CidadeEntrega;?>
						</div>
					</div>
				</div>
				</div>
				</div>
				
				<br>

				<div class="shopping-cart2" style="background-color: #f5f9ff;padding:10px">

				<div class="column-labels">
				<label class="product-image">Produto</label>
				<label class="product-details">&nbsp;</label>
				<label class="product-price"><?php echo getLinguas('key_preco'); ?></label>
				<label class="product-quantity"><?php echo getLinguas('key_quantidade'); ?></label>
				<label class="product-line-price">Total</label>
				</div>

				<?php
				$altera = 0;
				$sql = "SELECT * FROM encomendas_produtos WHERE ID_PAI=".intval($_GET['id_enc'])." AND Apagar = 0 AND voucher_id = 0 AND Estado!=8";
				// echo $sql.'<br>';
				$rs2->db_open($db_link, $DB_name, $sql);
				while(!$rs2->EOF())
				{
					$IDb = $rs2->db_data["ID"];
					$ID_Cliente = $rs2->db_data["ID_Cliente"];
					$ID_Produto = $rs2->db_data["ID_Produto"];
					$Ref = ''; $Ref = $rs2->db_data["Ref"];
					$Nome = ''; $Nome = $rs2->db_data["Nome"];

					// INFO DO PRODUTO
					$info = get_produtc_info($ID_Produto);

					// MODELO DO PRODUTO
					// $modelo = getModelos($info['prod_modelo']);

					// STYLE DO PRODUTO
					// $style = getTipos($info['prod_tipo']);
					$Ref = rtrim($rs2->db_data["Ref"],'-');
					$Nome = $rs2->db_data["Nome"];
					$Qtd = $rs2->db_data["Qtd"];
					$Tamanho = $rs2->db_data["Tamanho"];
					$Cor = $rs2->db_data["Cor"];
					$Valor = $rs2->db_data["Valor"];
					$Valor_Desconto = $rs2->db_data["Valor_Desconto"];
					$Tipo = $rs2->db_data["Tipo"];
					$m_estado = $rs2->db_data["Estado"];
					$enc_state2 =  estado_encomenda($m_estado, $GLOBALS['site_lang']);
					$ID_Conf = $rs2->db_data["ID_Conf"];
					$login = $rs2->db_data["login"];
					$Chave = $rs2->db_data["Chave"];
					$Obs = $rs2->db_data["Obs"];

					$url = str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['products_page_id'], $ID_Produto, 2));

					?>
					<div class="product">
					<div class="product-image text-center">
					<?php if($Tipo == 0){echo '&nbsp;';}else{?><img src="<?=$info['prd_imgm']; ?>" alt="" border="0" /><?php }?>
					</div>
					<div class="product-details">
					<div class="product-title"><?php echo ($Ref!='' ? '<b>'.$Ref.'</b><br>' : ''); ?><?=$Nome; ?></div>

					</div>
					<div class="product-price"><?php echo numberformat($Valor)?></div>
					<div class="product-quantity"><?php echo number_format($Qtd,0); ?></div>
					<div class="product-line-price"><?php echo numberformat(($Valor * $Qtd))?></div>
					</div>
					<?php if($Valor_Desconto > 0){?>
					<div class="product">
					<div class="product-image text-center">
					&nbsp;
					</div>
					<div class="product-details" style="min-height:auto">
					<div class="product-title"><b>DESCONTO</b></div>

					</div>
					<div class="product-price">- <?php echo numberformat($Valor_Desconto)?></div>
					<div class="product-quantity">&nbsp;</div>
					<div class="product-line-price"><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto);?></div>
					</div><br>
					<?php }

					// getIMEIS($ID_Produto);

					$rs2->db_next();
				}

				$rs2->db_close();

				if($TotalDocIva==0)
				{
					$TotalDocIva = $TotalDocComIva-($TotalDocComIva/1.23);
				}


				?>

				<div class="totals">
					<div class="totals-item">
						<label class="text-left">Portes</label>
						<div class="totals-value" id="cart-shipping"><?php echo numberformat($Portes, 1);?></div>
					</div>
					<div class="clear"></div>
					<div class="totals-item totals-item-total">
						<label class="text-left">IVA</label>
						<div class="totals-value" id="cart-total"><?php echo numberformat($TotalDocIva, 1);?></div>
					</div>
					<div class="clear"></div>
					<div class="totals-item totals-item-total">
						<label class="text-left">Total com IVA</label>
						<div class="totals-value" id="cart-total"><?php echo numberformat($TotalDocComIva, 1);?></div>
					</div>
					<div class="clear"></div>
					</div>
				</div>

				</div>
				<?php
			}
			$rs->db_close();
		}
		else
		{
			//Lista todas as encomendas

			define('N_PAGINAS', 10);
			if(!isset($_GET['pagina'])) $pagina = 1;
			else $pagina = $_GET['pagina'];
			$inicio = $pagina - 1;
			$inicio = $inicio * N_PAGINAS;

			$qtd=10;
			$total = 0;
			$url = seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1';
			if(!$from)$from=0;

			if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0)
			{
				$total = 1;
				$n_pags = 1;
			}
			else
			{
				$sql='Select ID from encomendas where ID_Cliente='.$cli_id.' AND Estado > -1';
				$rs->db_open($db_link,$DB_name,$sql);
				if(!$rs->EOF()) $total=$rs->db_rows;
				$rs->db_close();		
				$n_pags = ceil($total / N_PAGINAS);
			}		
			//Agora vamos mostrar a lista das encomendas já feitas
			$sql = 'Select * from encomendas where ID_Cliente='.$cli_id.' AND Estado > -1 order by ID DESC LIMIT '.$inicio.', '.N_PAGINAS;
			$rs->db_open($db_link,$DB_name, $sql);
			if(!$rs->EOF())
			{?>
				<style type="text/css">
					.table {display: table;text-align: center;border-collapse: separate;font-weight: 400;border-right: #ccc 1px solid;border-left: #ccc 1px solid;border-bottom: #ccc 1px solid;}
					.table_row {display: table-row;}
					.table_row:hover{background-color: #e8ecf1 !important}
					.theader {display: table-row;}
					.table_header {display: table-cell;background:#30373e;color:#e5e5e5;padding-top:5px;padding-bottom:5px;font-weight:500;font-size:12px;}
					.table_small {display: table-cell;font-size:12px;}
					.table_row > .table_small > .table_cell:nth-child(odd) {display: none;background: #f0f3f5;color: #e5e5e5;padding-top: 10px;padding-bottom: 10px;}
					.table_row > .table_small > .table_cell {padding-top: 3px;padding-bottom: 3px;color: #5b5b5b;border-top: #ebeced 1px solid;}
					.table_row:last-child > .table_small:last-child > .table_cell:last-child {border-bottom-right-radius: 5px;}
					.table_row:last-child > .table_small:first-child > .table_cell:last-child {border-bottom-left-radius: 5px;}
					.table_row:nth-child(2n+3) {background: #f0f3f5;}			  
				  @media screen and (max-width: 650px) {
				  	.table {display: block;}
				    .table_row:nth-child(2n+3) {background: none;}
				    .theader {display: none;}
				    .table_row > .table_small > .table_cell:nth-child(odd) {display: table-cell;width: 50%;background:#30373e !important;}
				    .table_cell {display: table-cell;width: 50%;}
				    .table_row {display: table;width: 100%;border-collapse: separate;margin: 5% auto 0;text-align: center;}
				    .table_small {display: table-row;}
				    .table_row > .table_small:first-child > .table_cell:last-child {border-left: none;}
				    .table_row > .table_small > .table_cell:first-child {border-left: #ccc 1px solid;}
				    .table_row > .table_small:first-child > .table_cell:first-child {border-top-left-radius: 5px;border-top: #ccc 1px solid;}
				    .table_row > .table_small:first-child > .table_cell:last-child {border-top-right-radius: 5px;border-top: #ccc 1px solid;}
				    .table_row > .table_small:last-child > .table_cell:first-child {border-right: none;}
				    .table_row > .table_small > .table_cell:last-child {border-right: #ccc 1px solid;}
				    .table_row > .table_small:last-child > .table_cell:first-child {border-bottom-left-radius: 5px;}
				    .table_row > .table_small:last-child > .table_cell:last-child {border-bottom-right-radius: 5px;}
				  }
				</style>
				<div class="table" id="results">
					<div class='theader'>
						<div class='table_header'>Enc. Número</div>
						<div class='table_header'>Data</div>
						<div class='table_header'>Valor</div>
						<div class='table_header'>Tipo escolhido</div>
						<div class='table_header'>Estado da encomenda</div>
						<div class='table_header'>Estado do pagamento</div>
						<div class='table_header'></div>
					</div><?
				while(!$rs->EOF())
				{
					$count++;
					$IDb=$rs->db_data["ID"];
					$Referencia=$rs->db_data["Referencia"];
					$Cond_pag=$rs->db_data["Cond_pag"];
					$Local_Entrega=$rs->db_data["Local_Entrega"];
					$Tipo=$rs->db_data["Tipo"];
					$Anulada=$rs->db_data["Anulada"];
					$RazaoAnulacao=$rs->db_data["RazaoAnulacao"];
					$ID_Cliente=$rs->db_data["ID_Cliente"];
					$N_Contribuinte=$rs->db_data["enc_Nif"];
					$Des_Social=$rs->db_data["Des_Social"];
					$enc_Nome = $rs->db_data["enc_Nome"];
					$enc_Apelido = $rs->db_data["enc_Apelido"];
					$enc_NomeEntrega = $rs->db_data["enc_Apelido_ent"];
					$enc_ApelidoEntrega = $rs->db_data["enc_Apelido_ent"];
					$Morada=$rs->db_data["enc_Morada"];
					$Localidade=$rs->db_data["enc_Localidade"];
					$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
					$Cidade=$rs->db_data["enc_Cidade"];
					$CidadeEntrega=$rs->db_data["enc_Cidade_ent"];
					$Pais=country_name($rs->db_data["enc_Pais"]);
					$PaisEntrega=country_name($rs->db_data["enc_Pais_ent"]);
					$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
					$LocalidadeEntrega=$rs->db_data["LocalidadeEntrega"];
					$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
					$telefone=$rs->db_data["enc_Telefone"];
					$email=$rs->db_data["enc_Email"];
					$Obs=$rs->db_data["Obs"];
					$Portes=$rs->db_data["Portes"];
					$PortesSemIva=$rs->db_data["PortesSemIva"];
					$Taxas=$rs->db_data["Taxas"];
					$TaxasSemIva=$rs->db_data["TaxasSemIva"];
					$Cobrar=$rs->db_data["Cobrar"];
					$TaxasPaypal=$rs->db_data["TaxasPaypal"];
					$ValorDesconto=$rs->db_data["ValorDesconto"];
					$ID_Vendedor=$rs->db_data["ID_Vendedor"];
					$ID_Distribuidor=$rs->db_data["ID_Distribuidor"];
					$PropostaDe=$rs->db_data["PropostaDe"];
					$ID_Armazem=$rs->db_data["ID_Armazem"];
					$Local_carga=$rs->db_data["Local_carga"];
					$Local_descarga=$rs->db_data["Local_descarga"];
					$Expedicao=$rs->db_data["Expedicao"];
					$Viatura_expedicao=$rs->db_data["Viatura_expedicao"];
					$ID_Expedicao=$rs->db_data["ID_Expedicao"];
					$IDGuia=$rs->db_data["IDGuia"];
					$Operador=$rs->db_data["Operador"];
					$ValorDescontoExtra=$rs->db_data["ValorDescontoExtra"];
					$ValorJustificado=$rs->db_data["ValorJustificado"];
					$TotalDocComIva=$rs->db_data["TotalDocComIva"];
					$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
					$TotalDocDescontos=$rs->db_data["TotalDocDescontos"];
					$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
					$TotalDocIva=$rs->db_data["TotalDocIva"];
					$Data_Criacao=$rs->db_data["Data_Criacao"];
					$Data_Impressao=$rs->db_data["Data_Impressao"];
					$Data_Doc_Forn=$rs->db_data["Data_Doc_Forn"];
					$Data_expedicao=$rs->db_data["Data_expedicao"];
					$Data_entrega=$rs->db_data["Data_entrega"];
					$Estado=$rs->db_data["Estado"];
					$m_guia=$rs->db_data["IDGuia"];
					$MOEDA=$rs->db_data["MOEDA"];
					$entidadeatm=$rs->db_data["Ent_atm"];
					$referenciaatm=$rs->db_data["Ref_atm"];
					$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
					$Metodo_expedicao=$rs->db_data["Metodo_envio"];
					$Encomenda_Paga=$rs->db_data["Encomenda_Paga"];
					$enc_token=$rs->db_data["enc_token"];
					$TotalIva=$rs->db_data["TotalIva"];
					$enc_Fatura = $rs->db_data["enc_Fatura"];

					$enc_info='';

					$corTexto = '#FFF';
					if($count % 2 != 0)	{$cor="#999";$n="1";}
					else				{$cor="#777";$n="2";}

					$enc_state =  estado_encomenda($Estado, $GLOBALS['site_lang']);
					$enc_info = ($enc_state['titulo2']!='' ? $enc_state['titulo2']: $enc_state['titulo']);
					$cor =  '#'.$enc_state['cor'];
					$corTexto = '#000';
					$extra='';
					$i =0;

					global $id_enc;
					if(isset($id_enc)) $id_enc=intval($id_enc);
					if(isset($_GET['pagina']) && intval($_GET['pagina']) > 0) $fromurl = '&paginato='.intval($_GET['pagina']);
					if(isset($_GET['paginato']) && intval($_GET['paginato']) > 0) $tourl = '?pagina='.intval($_GET['paginato']);
					$url_on = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?id_enc='.$IDb.$fromurl.'&show_compras=1';
					$url_off = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).$tourl.'?show_compras=1';

					if($count % 2 != 0)	{$cor1="#f0f3f5";}
					else				{$cor1="#f0f3f5";}
					?>				
					<div class='table_row'>
						<div class='table_small'>
							<div class='table_cell'>Enc. Número</div>
							<div class='table_cell'><?=$IDb;?></div>
						</div>
						<div class='table_small'>
							<div class='table_cell'>Data</div>
							<div class='table_cell'><?=$Data_Criacao;?></div>
						</div>
						<div class='table_small'>
							<div class='table_cell'>Total</div>
							<div class='table_cell'><?php echo numberformat($TotalDocComIva);?></div>
						</div>
						<div class='table_small'>
							<div class='table_cell'>Pagamento</div>
							<div class='table_cell'><?php 
								echo getNomeMetodoPagamentoByTipo_plataforma($Metodo_pagamento);
							?></div>
						</div>
						<div class='table_small'>
							<div class='table_cell'>Estado da encomenda</div>
							<div class='table_cell'>
								<b style="color: #000000;font-weight: 300;
			text-decoration: none;padding-left: 5px;padding-right: 5px;padding-top: 2px;padding-bottom: 2px;border-radius: 5px;background-color:<?=$cor?>"><?=$enc_info.($Estado == 7 ? ' '.str_replace(',','<span style="font-family:verdana,sans-serif">,</span>', date('M d, Y', strtotime($Data_expedicao))) : '');?></b>
								<?php
	                        	$cor='#000';
	                        	$Encomenda_Status='';								
								?>
							</div>
						</div>
						<div class='table_small'>
							<div class='table_cell'>Estado do pagamento</div>
							<div class='table_cell'><?php 
								if($Encomenda_Paga==1)
								{
									if($Metodo_pagamento==13)echo '<div style="color:#1ace3f"><b>Paga com sucesso através de cartão de crédito</b></div>';
									else echo '<div style="color:#1ace3f"><b>Paga</b></div>';
								}
								else
								{
									if($Estado==0 || $Estado==14 || $Estado==15)
									{
										if($Metodo_pagamento==13)
										{
											$Estado_unicre=0;
											//Rede unicre - VISA
											$rs2->db_open($db_link, $DB_name,'SELECT * FROM pagamentos_unicre WHERE ID_ENC = '.$IDb.' ORDER BY ID DESC limit 0,1');
											$Estado_unicre = $rs2->db_data['Estado'];
											$rs2->db_close();
											if($Estado_unicre==0)echo '<b>aguarda pagamento</b>';
											if($Estado_unicre==4)echo '<div style="color:#ff0000"><b>Encomenda não paga, erro</b></div>';
											if($Estado_unicre==13)echo '<div style="color:#1ace3f"><b>Paga com sucesso através de cartão de crédito</b></div>';
										}
										else echo '<b>aguarda pagamento</b>';
									}
								}
							?>&nbsp;</div>
						</div>
						<div class='table_small'>
							<div class='table_cell'></div>
							<div class='table_cell'>
								
								<?php if(isset($_GET['id_enc']) && intval($_GET['id_enc']) == $IDb ){?><div style="cursor:pointer" onclick="window.open('<?=$url_off?>', '_self')"> - Fechar Resumo</div>
								<?php }else{?>
								<div style="cursor:pointer" onclick="window.open('<?=$url_on?>', '_self')"> <i class="fa fa-plus" aria-hidden="true"></i>
  Ver detalhes</div>
								<?php }?>

								<?php
								if(stream_resolve_include_path($GLOBALS['site_shell_url'].'files/encomendas/'.$enc_Fatura) && $enc_Fatura!='')
								{?>
									<div class="col-xs-12 col-sm-12 col-md-12 text-right padding3">
										<b>Clique no icon para fazer download da Fatura: </b><a href="<?=$GLOBALS['site_http_url'].'files/encomendas/'.$enc_Fatura?>?dp=<?php echo date('YmdHis')?>" target="_blank"><img style="height:30px" src="<?=SITE_HTTP_URL?>layout/<?=PROJETO?>/images/fatura.png" border="0" /></a>
									</div><?php 
								}?>

							</div>
						</div>
					</div>

					<?php
					
					$rs->db_next();
					$i++;
				}?>
				</div>
				<?


				$rs->db_close();
				// echo show_paginacao_encomendas($total,$qtd);

				//Paginação:
				//echo $return2;
				if($n_pags > 1)
				{
					$return2 = '<div style="margin: 0 auto">';
					$p_seo = seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1';

					if(substr_count($p_seo, '?')) $ash = '&pagina='.($pagina <= 1 ? 1 : ($pagina-1)).'&show_compras=1';	else $ash = '?pagina='.($pagina <= 1 ? 1 : ($pagina-1)).'&show_compras=1';
					$pgn = $p_seo.$ash;
					if($pagina > 2)
					{
						if(substr_count($p_seo, '?')) $ash = '&pagina=1&show_compras=1';	else $ash = '?pagina=1&show_compras=1';
						$pgn = $p_seo.$ash;
						$return2 .= '<a href="'.$pgn.'" style="display:inline-block;cursor:pointer;padding:2px 4px;color:#000;font-size:14px;text-decoration:none">1....</a>';
					}
					$ultimo_num = 0;
					for($pi = ($pagina - 1); $pi < ($pagina + 2); $pi++)
					{
						$arr_pag[] = $pi;
						if(substr_count($p_seo, '?')) $ash = '&pagina='.$pi.'&show_compras=1';	else $ash = '?pagina='.$pi.'&show_compras=1';
						$pgn = $p_seo.$ash;
						$return2 .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? ($pi!=1 ? '<div style="font-size:20px;padding:2px 4px;display:inline-block;color:#000">,</div>' : '').'<div style="font-size:14px;color:#FFF;padding:2px 4px;background:#000;display:inline-block" >'.$pi.'</div>' : ($pi!=1 ? '<div style="font-size:20px;padding:2px 4px;display:inline-block;color:#000">,</div>' : '').'<a href="'.$pgn.'" style="display:inline-block;font-size:14px;cursor:pointer;padding:2px 4px;color:#000;text-decoration:none" >'.$pi.'</a>' :'';
						$ultimo_num = ($pi > $n_pags) ? $n_pags : 0;
					}

					if($n_pags!=$ultimo_num && !in_array($n_pags, $arr_pag))
					{

						if(substr_count($p_seo, '?')) $ash = '&pagina='.$n_pags.'&show_compras=1';	else $ash = '?pagina='.$n_pags.'&show_compras=1';
						$pgn = $p_seo.$ash;
						$return2 .= '<div style="font-size:14px;padding:2px;display:inline-block;color:#000">,</div><a href="'.$pgn.'" style="display:inline-block;font-size:14px;cursor:pointer;padding:2px 4px;color:#000;text-decoration:none" >....'.$n_pags.'</a>';
					}

					if(substr_count($p_seo, '?')) $ash = '&pagina='.($pagina >= $n_pags ? $n_pags : ($pagina+1)).'&show_compras=1';	else $ash = '?pagina='.($pagina >= $n_pags ? $n_pags : ($pagina+1)).'&show_compras=1';
					$pgn = $p_seo.$ash;
					$return2 .= '</div>';
				}
				echo $return2;


				$i = $count = 0;?>
				<script>
				function cancelarEncomenda(id_enc)
				{
					if(confirm("Deseja Cancelar a Encomenda Nº: "+id_enc+"?"))return true;
					return false;
				}
				function submitopt(id_enc, enc_token)
				{
					<?php if($GLOBALS['n_enc'] > 0){?>Apprise("<?php echo getLinguas('key_prod_cart_on')?>");<?php }else{?>
					var empty = '';	var escolha = '';
					$('.opt_subs').each(function(i, obj){ if($(this).val()=='' || $(this).val()==0) empty = 1;});
					if(empty == 1)
					{
						Apprise("<?php echo getLinguas('key_shop_msg_sub')?>");
					}
					else if(confirm("<?php echo getLinguas('key_shop_msg_alterar')?>"))
					{
						return true;
					}
					<?php }?>
					return false;
				}
				</script>
				<?php
			}
		}
	}
	// return $i;
}
function unicre_erro($Code='')
{
	switch ($Code)
	{
		case '000.000.000':$erro=0;$msg='Transaction succeeded';break;
		case '000.000.100':$erro=0;$msg='successful request';break;
		case '000.100.105':$erro=0;$msg='Chargeback Representment is successful';break;
		case '000.100.106':$erro=0;$msg='Chargeback Representment cancellation is successful';break;
		case '000.100.110':$erro=0;$msg='Request successfully processed in Merchant in Integrator Test Mode';break;
		case '000.100.111':$erro=0;$msg='Request successfully processed in Merchant in Validator Test Mode';break;
		case '000.100.112':$erro=0;$msg='Request successfully processed in Merchant in Connector Test Mode';break;
		case '000.300.000':$erro=0;$msg='Two-step transaction succeeded';break;
		case '000.300.100':$erro=0;$msg='Risk check successful';break;
		case '000.300.101':$erro=0;$msg='Risk bank account check successful';break;
		case '000.300.102':$erro=0;$msg='Risk report successful';break;
		case '000.300.103':$erro=0;$msg='Exemption check successful';break;
		case '000.310.100':$erro=0;$msg='Account updated';break;
		case '000.310.101':$erro=0;$msg='Account updated (Credit card expired)';break;
		case '000.310.110':$erro=0;$msg='No updates found, but account is valid';break;
		case '000.600.000':$erro=0;$msg='transaction succeeded due to external update';break;

		case '000.400.000':$erro=0;$msg='Transaction succeeded (please review manually due to fraud suspicion)';break;
		case '000.400.010':$erro=0;$msg='Transaction succeeded (please review manually due to AVS return code)';break;
		case '000.400.020':$erro=0;$msg='Transaction succeeded (please review manually due to CVV return code)';break;
		case '000.400.040':$erro=0;$msg='Transaction succeeded (please review manually due to amount mismatch)';break;
		case '000.400.050':$erro=0;$msg='Transaction succeeded (please review manually because transaction is pending)';break;
		case '000.400.060':$erro=0;$msg='Transaction succeeded (approved at merchant\'s risk)';break;
		case '000.400.070':$erro=0;$msg='Transaction succeeded (waiting for external risk review)';break;
		case '000.400.080':$erro=0;$msg='Transaction succeeded (please review manually because the service was unavailable)';break;
		case '000.400.081':$erro=0;$msg='Transaction succeeded (please review manually, as the risk status not available yet due network timeout)';break;
		case '000.400.082':$erro=0;$msg='Transaction succeeded (please review manually, as the risk status not available yet due processing timeout)';break;
		case '000.400.090':$erro=0;$msg='Transaction succeeded (please review manually due to external risk check)';break;
		case '000.400.100':$erro=0;$msg='Transaction succeeded, risk after payment rejected';break;
		case '000.400.110':$erro=3;$msg='Authentication successful (frictionless flow)';break;

		case '000.200.000':$erro=3;$msg='transaction pending';break;
		case '000.200.001':$erro=3;$msg='Transaction pending for acquirer, the consumer is not present';break;
		case '000.200.100':$erro=3;$msg='successfully created checkout';break;
		case '000.200.101':$erro=3;$msg='successfully updated checkout';break;
		case '000.200.102':$erro=3;$msg='successfully deleted checkout';break;
		case '000.200.103':$erro=3;$msg='checkout is pending';break;
		case '000.200.200':$erro=3;$msg='Transaction initialized';break;
		case '000.200.201':$erro=3;$msg='QR Scanned/Link Clicked, waiting for the final authentication result';break;

		case '100.400.500':$erro=3;$msg='waiting for external risk';break;
		case '800.400.500':$erro=0;$msg='Waiting for confirmation of non-instant payment. Denied for now.';break;
		case '800.400.501':$erro=0;$msg='Waiting for confirmation of non-instant debit. Denied for now.';break;
		case '800.400.502':$erro=0;$msg='Waiting for confirmation of non-instant refund. Denied for now.';break;

		case '000.400.101':$erro=3;$msg='card not participating/authentication unavailable';break;
		case '000.400.102':$erro=3;$msg='user not enrolled';break;
		case '000.400.103':$erro=3;$msg='Technical Error in 3D system';break;
		case '000.400.104':$erro=3;$msg='Missing or malformed 3DSecure Configuration for Channel';break;
		case '000.400.105':$erro=3;$msg='Unsupported User Device - Authentication not possible';break;
		case '000.400.106':$erro=3;$msg='invalid payer authentication response(PARes) in 3DSecure Transaction';break;
		case '000.400.107':$erro=3;$msg='Communication Error to VISA/Mastercard Directory Server';break;
		case '000.400.108':$erro=3;$msg='Cardholder Not Found - card number provided is not found in the ranges of the issuer';break;
		case '000.400.109':$erro=3;$msg='Card is not enrolled for 3DS version 2';break;
		case '000.400.200':$erro=3;$msg='risk management check communication error';break;

		case '800.100.100':$erro=3;$msg='transaction declined for unknown reason';break;
		case '800.100.150':$erro=3;$msg='transaction declined (refund on gambling tx not allowed)';break;
		case '800.100.151':$erro=3;$msg='transaction declined (invalid card)';break;
		case '800.100.152':$erro=3;$msg='transaction declined by authorization system';break;
		case '800.100.153':$erro=3;$msg='transaction declined (invalid CVV)';break;
		case '800.100.154':$erro=3;$msg='transaction declined (transaction marked as invalid)';break;
		case '800.100.155':$erro=3;$msg='transaction declined (amount exceeds credit)';break;
		case '800.100.156':$erro=3;$msg='transaction declined (format error)';break;
		case '800.100.157':$erro=3;$msg='transaction declined (wrong expiry date)';break;
		case '800.100.158':$erro=3;$msg='transaction declined (suspecting manipulation)';break;
		case '800.100.159':$erro=3;$msg='transaction declined (stolen card)';break;
		case '800.100.160':$erro=3;$msg='transaction declined (card blocked)';break;
		case '800.100.161':$erro=3;$msg='transaction declined (too many invalid tries)';break;
		case '800.100.162':$erro=3;$msg='transaction declined (limit exceeded)';break;
		case '800.100.163':$erro=3;$msg='transaction declined (maximum transaction frequency exceeded)';break;
		case '800.100.164':$erro=3;$msg='transaction declined (merchants limit exceeded)';break;
		case '800.100.165':$erro=3;$msg='transaction declined (card lost)';break;
		case '800.100.166':$erro=3;$msg='transaction declined (Incorrect personal identification number)';break;
		case '800.100.167':$erro=3;$msg='transaction declined (referencing transaction does not match)';break;
		case '800.100.168':$erro=3;$msg='transaction declined (restricted card)';break;
		case '800.100.169':$erro=3;$msg='transaction declined (card type is not processed by the authorization center)';break;
		case '800.100.170':$erro=3;$msg='transaction declined (transaction not permitted)';break;
		case '800.100.171':$erro=3;$msg='transaction declined (pick up card)';break;
		case '800.100.172':$erro=3;$msg='transaction declined (account blocked)';break;
		case '800.100.173':$erro=3;$msg='transaction declined (invalid currency, not processed by authorization center)';break;
		case '800.100.174':$erro=3;$msg='transaction declined (invalid amount)';break;
		case '800.100.175':$erro=3;$msg='transaction declined (invalid brand)';break;
		case '800.100.176':$erro=3;$msg='transaction declined (account temporarily not available. Please try again later)';break;
		case '800.100.177':$erro=3;$msg='transaction declined (amount field should not be empty)';break;
		case '800.100.178':$erro=3;$msg='transaction declined (PIN entered incorrectly too often)';break;
		case '800.100.179':$erro=3;$msg='transaction declined (exceeds withdrawal count limit)';break;
		case '800.100.190':$erro=3;$msg='transaction declined (invalid configuration data)';break;
		case '800.100.191':$erro=3;$msg='transaction declined (transaction in wrong state on aquirer side)';break;
		case '800.100.192':$erro=3;$msg='transaction declined (invalid CVV, Amount has still been reserved on the customer\'s card and will be released in a few business days. Please ensure the CVV code is accurate before retrying the transaction)';break;
		case '800.100.195':$erro=3;$msg='transaction declined (UserAccount Number/ID unknown)';break;
		case '800.100.196':$erro=3;$msg='transaction declined (registration error)';break;
		case '800.100.197':$erro=3;$msg='transaction declined (registration cancelled externally)';break;
		case '800.100.198':$erro=3;$msg='transaction declined (invalid holder)';break;
		case '800.100.199':$erro=3;$msg='transaction declined (invalid tax number)';break;
		case '800.100.200':$erro=3;$msg='Refer to Payer due to reason not specified';break;
		case '800.100.201':$erro=3;$msg='Account or Bank Details Incorrect';break;
		case '800.100.202':$erro=3;$msg='Account Closed';break;
		case '800.100.203':$erro=3;$msg='Insufficient Funds';break;
		case '800.100.204':$erro=3;$msg='Mandate Expired';break;
		case '800.100.205':$erro=3;$msg='Mandate Discarded';break;
		case '800.100.206':$erro=3;$msg='Refund of an authorized payment requested by the customer';break;
		case '800.100.207':$erro=3;$msg='Refund requested';break;
		case '800.100.208':$erro=3;$msg='Direct debit not enabled for the specified account or bank';break;
		case '800.100.402':$erro=3;$msg='cc/bank account holder not valid';break;
		case '800.100.403':$erro=3;$msg='transaction declined (revocation of authorisation order)';break;
		case '800.100.500':$erro=3;$msg='Card holder has advised his bank to stop this recurring payment';break;
		case '800.100.501':$erro=3;$msg='Card holder has advised his bank to stop all recurring payments for this merchant';break;
		case '800.700.100':$erro=3;$msg='transaction for the same session is currently being processed, please try again later.';break;
		case '800.700.101':$erro=3;$msg='family name too long';break;
		case '800.700.201':$erro=3;$msg='given name too long';break;
		case '800.700.500':$erro=3;$msg='company name too long';break;
		case '800.800.102':$erro=3;$msg='Invalid street';break;
		case '800.800.202':$erro=3;$msg='Invalid zip';break;
		case '800.800.302':$erro=3;$msg='Invalid city';break;

		case '900.100.100':$erro=3;$msg='unexpected communication error with connector/acquirer';break;
		case '900.100.200':$erro=3;$msg='error response from connector/acquirer';break;
		case '900.100.201':$erro=3;$msg='error on the external gateway (e.g. on the part of the bank, acquirer,...)';break;
		case '900.100.202':$erro=3;$msg='invalid transaction flow, the requested function is not applicable for the referenced transaction.';break;
		case '900.100.203':$erro=3;$msg='error on the internal gateway';break;
		case '900.100.300':$erro=3;$msg='timeout, uncertain result';break;
		case '900.100.301':$erro=3;$msg='Transaction timed out without response from connector/acquirer. It was reversed.';break;
		case '900.100.310':$erro=3;$msg='Transaction timed out due to internal system misconfiguration. Request to acquirer has not been sent.';break;
		case '900.100.400':$erro=3;$msg='timeout at connectors/acquirer side';break;
		case '900.100.500':$erro=3;$msg='timeout at connectors/acquirer side (try later)';break;
		case '900.100.600':$erro=3;$msg='connector/acquirer currently down';break;
		case '900.200.100':$erro=3;$msg='Message Sequence Number of Connector out of sync';break;
		case '900.300.600':$erro=3;$msg='user session timeout';break;
		case '900.400.100':$erro=3;$msg='unexpected communication error with external risk provider';break;

		case '600.100.100':$erro=3;$msg='Unexpected Integrator Error (Request could not be processed)';break;
		case '800.500.100':$erro=3;$msg='direct debit transaction declined for unknown reason';break;
		case '800.500.110':$erro=3;$msg='Unable to process transaction - ran out of terminalIds - please contact acquirer';break;
		case '800.600.100':$erro=3;$msg='transaction is being already processed';break;
		case '800.800.400':$erro=3;$msg='Connector/acquirer system is under maintenance';break;
		case '800.800.800':$erro=3;$msg='The payment system is currenty unavailable, please contact support in case this happens again.';break;
		case '800.800.801':$erro=3;$msg='The payment system is currenty unter maintenance. Please apologize for the inconvenience this may cause. If you were not informed of this maintenance window in advance, contact your sales representative.';break;
		case '999.999.888':$erro=3;$msg='UNDEFINED PLATFORM DATABASE ERROR';break;
		case '999.999.999':$erro=3;$msg='UNDEFINED CONNECTOR/ACQUIRER ERROR';break;

		case '100.395.101':$erro=3;$msg='Bank not supported for Giropay';break;
		case '100.395.102':$erro=3;$msg='Account not enabled for Giropay e.g. test account';break;
		case '100.395.501':$erro=3;$msg='Previously pending online transfer transaction timed out';break;
		case '100.395.502':$erro=3;$msg='Acquirer/Bank reported timeout on online transfer transaction';break;
		case '100.396.101':$erro=3;$msg='Cancelled by user';break;
		case '100.396.102':$erro=3;$msg='Not confirmed by user';break;
		case '100.396.103':$erro=3;$msg='Previously pending transaction timed out';break;
		case '100.396.104':$erro=3;$msg='Uncertain status - probably cancelled by user';break;
		case '100.396.106':$erro=3;$msg='User did not agree to payment method terms';break;
		case '100.396.201':$erro=3;$msg='Cancelled by merchant';break;
		case '100.397.101':$erro=3;$msg='Cancelled by user due to external update';break;
		case '100.397.102':$erro=3;$msg='Rejected by connector/acquirer due to external update';break;

		case '300.100.100':$erro=3;$msg='Transaction declined (additional customer authentication required)';break;

		case '100.370.100':$erro=3;$msg='transaction declined';break;
		case '100.370.110':$erro=3;$msg='transaction must be executed for German address';break;
		case '100.370.111':$erro=3;$msg='system error( possible incorrect/missing input data)';break;
		case '100.380.100':$erro=3;$msg='transaction declined';break;
		case '100.380.101':$erro=3;$msg='transaction contains no risk management part';break;
		case '100.380.110':$erro=3;$msg='transaction must be executed for German address';break;
		case '100.380.201':$erro=3;$msg='no risk management process type specified';break;
		case '100.380.305':$erro=3;$msg='no frontend information provided for asynchronous transaction';break;
		case '100.380.306':$erro=3;$msg='no authentication data provided in risk management transaction';break;
		case '100.380.401':$erro=3;$msg='User Authentication Failed';break;
		case '100.380.501':$erro=3;$msg='risk management transaction timeout';break;
		case '100.400.000':$erro=3;$msg='transaction declined (Wrong Address)';break;
		case '100.400.001':$erro=3;$msg='transaction declined (Wrong Identification)';break;
		case '100.400.002':$erro=3;$msg='transaction declined (Insufficient credibility score)';break;
		case '100.400.005':$erro=3;$msg='transaction must be executed for German address';break;
		case '100.400.007':$erro=3;$msg='System error ( possible incorrect/missing input data)';break;
		case '100.400.020':$erro=3;$msg='transaction declined';break;
		case '100.400.021':$erro=3;$msg='transaction declined for country';break;
		case '100.400.030':$erro=3;$msg='transaction not authorized. Please check manually';break;
		case '100.400.039':$erro=3;$msg='transaction declined for other error';break;
		case '100.400.040':$erro=3;$msg='authorization failure';break;
		case '100.400.041':$erro=3;$msg='transaction must be executed for German address';break;
		case '100.400.042':$erro=3;$msg='transaction declined by SCHUFA (Insufficient credibility score)';break;
		case '100.400.043':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
		case '100.400.044':$erro=3;$msg='transaction not authorized. Please check manually';break;
		case '100.400.045':$erro=3;$msg='SCHUFA result not definite. Please check manually';break;
		case '100.400.051':$erro=3;$msg='SCHUFA system error (possible incorrect/missing input data)';break;
		case '100.400.060':$erro=3;$msg='authorization failure';break;
		case '100.400.061':$erro=3;$msg='transaction declined (Insufficient credibility score)';break;
		case '100.400.063':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
		case '100.400.064':$erro=3;$msg='transaction must be executed for Austrian, German or Swiss address';break;
		case '100.400.065':$erro=3;$msg='result ambiguous. Please check manually';break;
		case '100.400.071':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
		case '100.400.080':$erro=3;$msg='authorization failure';break;
		case '100.400.081':$erro=3;$msg='transaction declined';break;
		case '100.400.083':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
		case '100.400.084':$erro=3;$msg='transaction can not be executed for given country';break;
		case '100.400.085':$erro=3;$msg='result ambiguous. Please check manually';break;
		case '100.400.086':$erro=3;$msg='transaction declined (Wrong Address)';break;
		case '100.400.087':$erro=3;$msg='transaction declined (Wrong Identification)';break;
		case '100.400.091':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
		case '100.400.100':$erro=3;$msg='transaction declined - very bad rating';break;
		case '100.400.120':$erro=3;$msg='authorization failure';break;
		case '100.400.121':$erro=3;$msg='account blacklisted';break;
		case '100.400.122':$erro=3;$msg='transaction must be executed for valid German account';break;
		case '100.400.123':$erro=3;$msg='transaction declined because of missing obligatory parameter(s)';break;
		case '100.400.130':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
		case '100.400.139':$erro=3;$msg='system error (possible incorrect/missing input data)';break;
		case '100.400.140':$erro=3;$msg='transaction declined by GateKeeper';break;
		case '100.400.141':$erro=3;$msg='Challenge by ReD Shield';break;
		case '100.400.142':$erro=3;$msg='Deny by ReD Shield';break;
		case '100.400.143':$erro=3;$msg='Noscore by ReD Shield';break;
		case '100.400.144':$erro=3;$msg='ReD Shield data error';break;
		case '100.400.145':$erro=3;$msg='ReD Shield connection error';break;
		case '100.400.146':$erro=3;$msg='Line item error by ReD Shield';break;
		case '100.400.147':$erro=3;$msg='Payment void and transaction denied by ReD Shield';break;
		case '100.400.148':$erro=3;$msg='Payment void and transaction challenged by ReD Shield';break;
		case '100.400.149':$erro=3;$msg='Payment void and data error by ReD Shield';break;
		case '100.400.150':$erro=3;$msg='Payment void and connection error by ReD Shield';break;
		case '100.400.151':$erro=3;$msg='Payment void and line item error by ReD Shield';break;
		case '100.400.152':$erro=3;$msg='Payment void and error returned by ReD Shield';break;
		case '100.400.241':$erro=3;$msg='Challenged by Threat Metrix';break;
		case '100.400.242':$erro=3;$msg='Denied by Threat Metrix';break;
		case '100.400.243':$erro=3;$msg='Invalid sessionId';break;
		case '100.400.260':$erro=3;$msg='authorization failure';break;
		case '100.400.300':$erro=3;$msg='abort checkout process';break;
		case '100.400.301':$erro=3;$msg='reenter age/birthdate';break;
		case '100.400.302':$erro=3;$msg='reenter address (packstation not allowed)';break;
		case '100.400.303':$erro=3;$msg='reenter address';break;
		case '100.400.304':$erro=3;$msg='invalid input data';break;
		case '100.400.305':$erro=3;$msg='invalid foreign address';break;
		case '100.400.306':$erro=3;$msg='delivery address error';break;
		case '100.400.307':$erro=3;$msg='offer only secure methods of payment';break;
		case '100.400.308':$erro=3;$msg='offer only secure methods of payment; possibly abort checkout';break;
		case '100.400.309':$erro=3;$msg='confirm corrected address; if not confirmed, offer secure methods of payment only';break;
		case '100.400.310':$erro=3;$msg='confirm bank account data; if not confirmed, offer secure methods of payment only';break;
		case '100.400.311':$erro=3;$msg='transaction declined (format error)';break;
		case '100.400.312':$erro=3;$msg='transaction declined (invalid configuration data)';break;
		case '100.400.313':$erro=3;$msg='currency field is invalid or missing';break;
		case '100.400.314':$erro=3;$msg='amount invalid or empty';break;
		case '100.400.315':$erro=3;$msg='invalid or missing email address (probably invalid syntax)';break;
		case '100.400.316':$erro=3;$msg='transaction declined (card missing)';break;
		case '100.400.317':$erro=3;$msg='transaction declined (invalid card)';break;
		case '100.400.318':$erro=3;$msg='invalid IP number';break;
		case '100.400.319':$erro=3;$msg='transaction declined by risk system';break;
		case '100.400.320':$erro=3;$msg='shopping cart data invalid or missing';break;
		case '100.400.321':$erro=3;$msg='payment type invalid or missing';break;
		case '100.400.322':$erro=3;$msg='encryption method invalid or missing';break;
		case '100.400.323':$erro=3;$msg='certificate invalid or missing';break;
		case '100.400.324':$erro=3;$msg='Error on the external risk system';break;
		case '100.400.325':$erro=3;$msg='External risk system not available';break;
		case '100.400.326':$erro=3;$msg='Risk bank account check unsuccessful';break;
		case '100.400.327':$erro=3;$msg='Risk report unsuccessful';break;
		case '100.400.328':$erro=3;$msg='Risk report unsuccessful (invalid data)';break;

		case '800.400.100':$erro=3;$msg='AVS Check Failed';break;
		case '800.400.101':$erro=3;$msg='Mismatch of AVS street value';break;
		case '800.400.102':$erro=3;$msg='Mismatch of AVS street number';break;
		case '800.400.103':$erro=3;$msg='Mismatch of AVS PO box value fatal';break;
		case '800.400.104':$erro=3;$msg='Mismatch of AVS zip code value fatal';break;
		case '800.400.105':$erro=3;$msg='Mismatch of AVS settings (AVSkip, AVIgnore, AVSRejectPolicy) value';break;
		case '800.400.110':$erro=3;$msg='AVS Check Failed. Amount has still been reserved on the customer\'s card and will be released in a few business days. Please ensure the billing address is accurate before retrying the transaction.';break;
		case '800.400.150':$erro=3;$msg='Implausible address data';break;
		case '800.400.151':$erro=3;$msg='Implausible address state data';break;

		case '100.380.401':$erro=3;$msg='User Authentication Failed';break;
		case '100.390.101':$erro=3;$msg='purchase amount/currency mismatch';break;
		case '100.390.102':$erro=3;$msg='PARes Validation failed';break;
		case '100.390.103':$erro=3;$msg='PARes Validation failed - problem with signature';break;
		case '100.390.104':$erro=3;$msg='XID mismatch';break;
		case '100.390.105':$erro=3;$msg='Transaction rejected because of technical error in 3DSecure system';break;
		case '100.390.106':$erro=3;$msg='Transaction rejected because of error in 3DSecure configuration';break;
		case '100.390.107':$erro=3;$msg='Transaction rejected because cardholder authentication unavailable';break;
		case '100.390.108':$erro=3;$msg='Transaction rejected because merchant not participating in 3DSecure program';break;
		case '100.390.109':$erro=3;$msg='Transaction rejected because of VISA status \'U\' or AMEX status \'N\' or \'U\' in 3DSecure program';break;
		case '100.390.110':$erro=3;$msg='Cardholder Not Found - card number provided is not found in the ranges of the issuer';break;
		case '100.390.111':$erro=3;$msg='Communication Error to VISA/Mastercard Directory Server';break;
		case '100.390.112':$erro=3;$msg='Technical Error in 3D system';break;
		case '100.390.113':$erro=3;$msg='Unsupported User Device - Authentication not possible';break;
		case '100.390.115':$erro=3;$msg='Authentication failed due to invalid message format';break;
		case '100.390.116':$erro=3;$msg='Access denied to the authentication system';break;
		case '100.390.117':$erro=3;$msg='Authentication failed due to invalid data fields';break;
		case '100.390.118':$erro=3;$msg='Authentication failed due to suspected fraud';break;
		case '800.400.200':$erro=3;$msg='Invalid Payer Authentication in 3DSecure transaction';break;

		case '100.100.701':$erro=3;$msg='suspecting fraud, this card may not be processed';break;
		case '800.200.159':$erro=3;$msg='account or user is blacklisted (card stolen)';break;
		case '800.200.160':$erro=3;$msg='account or user is blacklisted (card blocked)';break;
		case '800.200.165':$erro=3;$msg='account or user is blacklisted (card lost)';break;
		case '800.200.202':$erro=3;$msg='account or user is blacklisted (account closed)';break;
		case '800.200.208':$erro=3;$msg='account or user is blacklisted (account blocked)';break;
		case '800.200.220':$erro=3;$msg='account or user is blacklisted (fraudulent transaction)';break;
		case '800.300.101':$erro=3;$msg='account or user is blacklisted';break;
		case '800.300.102':$erro=3;$msg='country blacklisted';break;
		case '800.300.200':$erro=3;$msg='email is blacklisted';break;
		case '800.300.301':$erro=3;$msg='ip blacklisted';break;
		case '800.300.302':$erro=3;$msg='ip is anonymous proxy';break;
		case '800.300.401':$erro=3;$msg='bin blacklisted';break;
		case '800.300.500':$erro=3;$msg='transaction temporary blacklisted (too many tries invalid CVV)';break;
		case '800.300.501':$erro=3;$msg='transaction temporary blacklisted (too many tries invalid expire date)';break;
		case '800.310.200':$erro=3;$msg='Account closed';break;
		case '800.310.210':$erro=3;$msg='Account not found';break;
		case '800.310.211':$erro=3;$msg='Account not found (BIN/issuer not participating)';break;

		case '800.110.100':$erro=3;$msg='duplicate transaction';break;
		case '800.120.100':$erro=3;$msg='Rejected by Throttling.';break;
		case '800.120.101':$erro=3;$msg='maximum number of transactions per account already exceeded';break;
		case '800.120.102':$erro=3;$msg='maximum number of transactions per ip already exceeded';break;
		case '800.120.103':$erro=3;$msg='maximum number of transactions per email already exceeded';break;
		case '800.120.200':$erro=3;$msg='maximum total volume of transactions already exceeded';break;
		case '800.120.201':$erro=3;$msg='maximum total volume of transactions per account already exceeded';break;
		case '800.120.202':$erro=3;$msg='maximum total volume of transactions per ip already exceeded';break;
		case '800.120.203':$erro=3;$msg='maximum total volume of transactions per email already exceeded';break;
		case '800.120.300':$erro=3;$msg='chargeback rate per bin exceeded';break;
		case '800.120.401':$erro=3;$msg='maximum number of transactions or total volume for configured MIDs or CIs exceeded';break;
		case '800.130.100':$erro=3;$msg='Transaction with same TransactionId already exists';break;
		case '800.140.100':$erro=3;$msg='maximum number of registrations per mobile number exceeded';break;
		case '800.140.101':$erro=3;$msg='maximum number of registrations per email address exceeded';break;
		case '800.140.110':$erro=3;$msg='maximum number of registrations of mobile per credit card number exceeded';break;
		case '800.140.111':$erro=3;$msg='maximum number of registrations of credit card number per mobile exceeded';break;
		case '800.140.112':$erro=3;$msg='maximum number of registrations of email per credit card number exceeded';break;
		case '800.140.113':$erro=3;$msg='maximum number of registrations of credit card number per email exceeded';break;
		case '800.150.100':$erro=3;$msg='Account Holder does not match Customer Name';break;
		case '800.160.100':$erro=3;$msg='Invalid payment data for configured Shopper Dispatching Type';break;
		case '800.160.110':$erro=3;$msg='Invalid payment data for configured Payment Dispatching Type';break;
		case '800.160.120':$erro=3;$msg='Invalid payment data for configured Recurring Transaction Dispatching Type';break;
		case '800.160.130':$erro=3;$msg='Invalid payment data for configured TicketSize Dispatching Type';break;

		case '500.100.201':$erro=3;$msg='Channel/Merchant is disabled (no processing possible)';break;
		case '500.100.202':$erro=3;$msg='Channel/Merchant is new (no processing possible yet)';break;
		case '500.100.203':$erro=3;$msg='Channel/Merchant is closed (no processing possible)';break;
		case '500.100.301':$erro=3;$msg='Merchant-Connector is disabled (no processing possible)';break;
		case '500.100.302':$erro=3;$msg='Merchant-Connector is new (no processing possible yet)';break;
		case '500.100.303':$erro=3;$msg='Merchant-Connector is closed (no processing possible)';break;
		case '500.100.304':$erro=3;$msg='Merchant-Connector is disabled at gateway (no processing possible)';break;
		case '500.100.401':$erro=3;$msg='Connector is unavailable (no processing possible)';break;
		case '500.100.402':$erro=3;$msg='Connector is new (no processing possible yet)';break;
		case '500.100.403':$erro=3;$msg='Connector is unavailable (no processing possible)';break;
		case '500.200.101':$erro=3;$msg='No target account configured for DD transaction';break;
		case '600.200.100':$erro=3;$msg='invalid Payment Method';break;
		case '600.200.200':$erro=3;$msg='Unsupported Payment Method';break;
		case '600.200.201':$erro=3;$msg='Channel/Merchant not configured for this payment method';break;
		case '600.200.202':$erro=3;$msg='Channel/Merchant not configured for this payment type';break;
		case '600.200.300':$erro=3;$msg='invalid Payment Type';break;
		case '600.200.310':$erro=3;$msg='invalid Payment Type for given Payment Method';break;
		case '600.200.400':$erro=3;$msg='Unsupported Payment Type';break;
		case '600.200.500':$erro=3;$msg='Invalid payment data. You are not configured for this currency or sub type (country or brand)';break;
		case '600.200.501':$erro=3;$msg='Invalid payment data for Recurring transaction. Merchant or transaction data has wrong recurring configuration.';break;
		case '600.200.600':$erro=3;$msg='invalid payment code (type or method)';break;
		case '600.200.700':$erro=3;$msg='invalid payment mode (you are not configured for the requested transaction mode)';break;
		case '600.200.800':$erro=3;$msg='invalid brand for given payment method and payment mode (you are not configured for the requested transaction mode)';break;
		case '600.200.810':$erro=3;$msg='invalid return code provided';break;
		case '600.300.101':$erro=3;$msg='Merchant key not found';break;
		case '600.300.200':$erro=3;$msg='merchant source IP address not whitelisted';break;
		case '600.300.210':$erro=3;$msg='merchant notificationUrl not whitelisted';break;
		case '600.300.211':$erro=3;$msg='shopperResultUrl not whitelisted';break;
		case '800.121.100':$erro=3;$msg='Channel not configured for given source type. Please contact your account manager.';break;
		case '800.121.200':$erro=3;$msg='Secure Query is not enabled for this entity. Please contact your account manager.';break;

		case '100.150.100':$erro=3;$msg='request contains no Account data and no registration id';break;
		case '100.150.101':$erro=3;$msg='invalid format for specified registration id (must be uuid format)';break;
		case '100.150.200':$erro=3;$msg='registration does not exist';break;
		case '100.150.201':$erro=3;$msg='registration is not confirmed yet';break;
		case '100.150.202':$erro=3;$msg='registration is already deregistered';break;
		case '100.150.203':$erro=3;$msg='registration is not valid, probably initially rejected';break;
		case '100.150.204':$erro=3;$msg='account registration reference pointed to no registration transaction';break;
		case '100.150.205':$erro=3;$msg='referenced registration does not contain an account';break;
		case '100.150.300':$erro=3;$msg='payment only allowed with valid initial registration';break;
		case '100.350.100':$erro=3;$msg='referenced session is REJECTED (no action possible).';break;
		case '100.350.101':$erro=3;$msg='referenced session is CLOSED (no action possible)';break;
		case '100.350.200':$erro=3;$msg='undefined session state';break;
		case '100.350.201':$erro=3;$msg='referencing a registration through reference id is not applicable for this payment type';break;
		case '100.350.301':$erro=3;$msg='confirmation (CF) must be registered (RG) first';break;
		case '100.350.302':$erro=3;$msg='session already confirmed (CF)';break;
		case '100.350.303':$erro=3;$msg='cannot deregister (DR) unregistered account and/or customer';break;
		case '100.350.310':$erro=3;$msg='cannot confirm (CF) session via XML';break;
		case '100.350.311':$erro=3;$msg='cannot confirm (CF) on a registration passthrough channel';break;
		case '100.350.312':$erro=3;$msg='cannot do passthrough on non-internal connector';break;
		case '100.350.313':$erro=3;$msg='registration of this type has to provide confirmation url';break;
		case '100.350.314':$erro=3;$msg='customer could not be notified of pin to confirm registration (channel)';break;
		case '100.350.315':$erro=3;$msg='customer could not be notified of pin to confirm registration (sending failed)';break;
		case '100.350.400':$erro=3;$msg='no or invalid PIN (email/SMS/MicroDeposit authentication) entered';break;
		case '100.350.500':$erro=3;$msg='unable to obtain personal (virtual) account - most likely no more accounts available';break;
		case '100.350.601':$erro=3;$msg='registration is not allowed to reference another transaction';break;
		case '100.350.602':$erro=3;$msg='Registration is not allowed for recurring payment migration';break;

		case '100.250.100':$erro=3;$msg='job contains no execution information';break;
		case '100.250.105':$erro=3;$msg='invalid or missing action type';break;
		case '100.250.106':$erro=3;$msg='invalid or missing duration unit';break;
		case '100.250.107':$erro=3;$msg='invalid or missing notice unit';break;
		case '100.250.110':$erro=3;$msg='missing job execution';break;
		case '100.250.111':$erro=3;$msg='missing job expression';break;
		case '100.250.120':$erro=3;$msg='invalid execution parameters, combination does not conform to standard';break;
		case '100.250.121':$erro=3;$msg='invalid execution parameters, hour must be between 0 and 23';break;
		case '100.250.122':$erro=3;$msg='invalid execution parameters, minute and seconds must be between 0 and 59';break;
		case '100.250.123':$erro=3;$msg='invalid execution parameters, Day of month must be between 1 and 31';break;
		case '100.250.124':$erro=3;$msg='invalid execution parameters, month must be between 1 and 12';break;
		case '100.250.125':$erro=3;$msg='invalid execution parameters, Day of week must be between 1 and 7';break;
		case '100.250.250':$erro=3;$msg='Job tag missing';break;
		case '100.360.201':$erro=3;$msg='unknown schedule type';break;
		case '100.360.300':$erro=3;$msg='cannot schedule(SD) unscheduled job';break;
		case '100.360.303':$erro=3;$msg='cannot deschedule(DS) unscheduled job';break;
		case '100.360.400':$erro=3;$msg='schedule module not configured for LIVE transaction mode';break;

		case '700.100.100':$erro=3;$msg='reference id not existing';break;
		case '700.100.200':$erro=3;$msg='non matching reference amount';break;
		case '700.100.300':$erro=3;$msg='invalid amount (probably too large)';break;
		case '700.100.400':$erro=3;$msg='referenced payment method does not match with requested payment method';break;
		case '700.100.500':$erro=3;$msg='referenced payment currency does not match with requested payment currency';break;
		case '700.100.600':$erro=3;$msg='referenced mode does not match with requested payment mode';break;
		case '700.100.700':$erro=3;$msg='referenced transaction is of inappropriate type';break;
		case '700.100.701':$erro=3;$msg='referenced a DB transaction without explicitly providing an account. Not allowed to used referenced account.';break;
		case '700.100.710':$erro=3;$msg='cross-linkage of two transaction-trees';break;
		case '700.300.100':$erro=3;$msg='referenced tx can not be refunded, captured or reversed (invalid type)';break;
		case '700.300.200':$erro=3;$msg='referenced tx was rejected';break;
		case '700.300.300':$erro=3;$msg='referenced tx can not be refunded, captured or reversed (already refunded, captured or reversed)';break;
		case '700.300.400':$erro=3;$msg='referenced tx can not be captured (cut off time reached)';break;
		case '700.300.500':$erro=3;$msg='chargeback error (multiple chargebacks)';break;
		case '700.300.600':$erro=3;$msg='referenced tx can not be refunded or reversed (was chargebacked)';break;
		case '700.300.700':$erro=3;$msg='referenced tx can not be reversed (reversal not possible anymore)';break;
		case '700.300.800':$erro=3;$msg='referenced tx can not be voided';break;
		case '700.400.000':$erro=3;$msg='serious workflow error (call support)';break;
		case '700.400.100':$erro=3;$msg='cannot capture (PA value exceeded, PA reverted or invalid workflow?)';break;
		case '700.400.101':$erro=3;$msg='cannot capture (Not supported by authorization system)';break;
		case '700.400.200':$erro=3;$msg='cannot refund (refund volume exceeded or tx reversed or invalid workflow?)';break;
		case '700.400.300':$erro=3;$msg='cannot reverse (already refunded|reversed, invalid workflow or amount exceeded)';break;
		case '700.400.400':$erro=3;$msg='cannot chargeback (already chargebacked or invalid workflow?)';break;
		case '700.400.402':$erro=3;$msg='chargeback can only be generated internally by the payment system';break;
		case '700.400.410':$erro=3;$msg='cannot reversal chargeback (chargeback is already reversaled or invalid workflow?)';break;
		case '700.400.411':$erro=3;$msg='cannot reverse chargeback or invalid workflow (second chargeback)';break;
		case '700.400.420':$erro=3;$msg='cannot reversal chargeback (no chargeback existing or invalid workflow?)';break;
		case '700.400.510':$erro=3;$msg='capture needs at least one successful transaction of type (PA)';break;
		case '700.400.520':$erro=3;$msg='refund needs at least one successful transaction of type (CP or DB or RB or RC)';break;
		case '700.400.530':$erro=3;$msg='reversal needs at least one successful transaction of type (CP or DB or RB or PA)';break;
		case '700.400.540':$erro=3;$msg='reconceile needs at least one successful transaction of type (CP or DB or RB)';break;
		case '700.400.550':$erro=3;$msg='chargeback needs at least one successful transaction of type (CP or DB or RB)';break;
		case '700.400.560':$erro=3;$msg='receipt needs at least one successful transaction of type (PA or CP or DB or RB)';break;
		case '700.400.561':$erro=3;$msg='receipt on a registration needs a successfull registration in state \'OPEN\'';break;
		case '700.400.562':$erro=3;$msg='receipts are configured to be generated only internally by the payment system';break;
		case '700.400.565':$erro=3;$msg='finalize needs at least one successful transaction of type (PA or DB)';break;
		case '700.400.570':$erro=3;$msg='cannot reference a waiting/pending transaction';break;
		case '700.400.580':$erro=3;$msg='cannot find transaction';break;
		case '700.400.590':$erro=3;$msg='installment needs at least one successful transaction of type (DB or PA)';break;
		case '700.400.600':$erro=3;$msg='finalize needs at least one successful transaction of type (IN, DB, PA or CD)';break;
		case '700.400.700':$erro=3;$msg='initial and referencing channel-ids do not match';break;
		case '700.450.001':$erro=3;$msg='cannot transfer money from one account to the same account';break;
		case '700.500.001':$erro=3;$msg='referenced session contains too many transactions';break;
		case '700.500.002':$erro=3;$msg='capture or preauthorization appears too late in referenced session';break;
		case '700.500.003':$erro=3;$msg='test accounts not allowed in production';break;
		case '700.500.004':$erro=3;$msg='cannot refer a transaction which contains deleted customer information';break;

		case '100.300.101':$erro=3;$msg='invalid test mode (please use LIVE or INTEGRATOR_TEST or CONNECTOR_TEST)';break;
		case '100.300.200':$erro=3;$msg='transaction id too long';break;
		case '100.300.300':$erro=3;$msg='invalid reference id';break;
		case '100.300.400':$erro=3;$msg='missing or invalid channel id';break;
		case '100.300.401':$erro=3;$msg='missing or invalid sender id';break;
		case '100.300.402':$erro=3;$msg='missing or invalid version';break;
		case '100.300.501':$erro=3;$msg='invalid response id';break;
		case '100.300.600':$erro=3;$msg='invalid or missing user login';break;
		case '100.300.601':$erro=3;$msg='invalid or missing user pwd';break;
		case '100.300.700':$erro=3;$msg='invalid relevance';break;
		case '100.300.701':$erro=3;$msg='invalid relevance for given payment type';break;
		case '100.370.100':$erro=3;$msg='transaction declined';break;
		case '100.370.101':$erro=3;$msg='responseUrl not set in Transaction/Frontend';break;
		case '100.370.102':$erro=3;$msg='malformed responseUrl in Transaction/Frontend';break;
		case '100.370.110':$erro=3;$msg='transaction must be executed for German address';break;
		case '100.370.111':$erro=3;$msg='system error( possible incorrect/missing input data)';break;
		case '100.370.121':$erro=3;$msg='no or unknown ECI Type defined in Authentication';break;
		case '100.370.122':$erro=3;$msg='parameter with null key provided in 3DSecure Authentication';break;
		case '100.370.123':$erro=3;$msg='no or unknown verification type defined in 3DSecure Authentication';break;
		case '100.370.124':$erro=3;$msg='unknown parameter key in 3DSecure Authentication';break;
		case '100.370.125':$erro=3;$msg='Invalid 3DSecure Verification_ID. Must have Base64 encoding a Length of 28 digits';break;
		case '100.370.131':$erro=3;$msg='no or unknown authentication type defined in Transaction/Authentication@type';break;
		case '100.370.132':$erro=3;$msg='no result indicator defined Transaction/Authentication/resultIndicator';break;
		case '100.500.101':$erro=3;$msg='payment method invalid';break;
		case '100.500.201':$erro=3;$msg='payment type invalid';break;
		case '100.500.301':$erro=3;$msg='invalid due date';break;
		case '100.500.302':$erro=3;$msg='invalid mandate date of signature';break;
		case '100.500.303':$erro=3;$msg='invalid mandate id';break;
		case '100.500.304':$erro=3;$msg='invalid mandate external id';break;
		case '100.600.500':$erro=3;$msg='usage field too long';break;
		case '100.900.500':$erro=3;$msg='invalid recurrence mode';break;
		case '200.100.101':$erro=3;$msg='invalid Request Message. No valid XML. XML must be url-encoded! maybe it contains a not encoded ampersand or something similar.';break;
		case '200.100.102':$erro=3;$msg='invalid Request. XML load missing (XML string must be sent within parameter \'load\')';break;
		case '200.100.103':$erro=3;$msg='invalid Request Message. The request contains structural errors';break;
		case '200.100.150':$erro=3;$msg='transaction of multirequest not processed because of subsequent problems';break;
		case '200.100.151':$erro=3;$msg='multi-request is allowed with a maximum of 10 transactions only';break;
		case '200.100.199':$erro=3;$msg='Wrong Web Interface / URL used. Please check out the Tech Quick Start Doc Chapter 3.';break;
		case '200.100.201':$erro=3;$msg='invalid Request/Transaction tag (not present or [partially] empty)';break;
		case '200.100.300':$erro=3;$msg='invalid Request/Transaction/Payment tag (no or invalid code specified)';break;
		case '200.100.301':$erro=3;$msg='invalid Request/Transaction/Payment tag (not present or [partially] empty)';break;
		case '200.100.302':$erro=3;$msg='invalid Request/Transaction/Payment/Presentation tag (not present or [partially] empty)';break;
		case '200.100.401':$erro=3;$msg='invalid Request/Transaction/Account tag (not present or [partially] empty)';break;
		case '200.100.402':$erro=3;$msg='invalid Request/Transaction/Account(Customer, Relevance) tag (one of Account/Customer/Relevance must be present)';break;
		case '200.100.403':$erro=3;$msg='invalid Request/Transaction/Analysis tag (Criterions must have a name and value)';break;
		case '200.100.404':$erro=3;$msg='invalid Request/Transaction/Account (must not be present)';break;
		case '200.100.501':$erro=3;$msg='invalid or missing customer';break;
		case '200.100.502':$erro=3;$msg='invalid Request/Transaction/Customer/Name tag (not present or [partially] empty)';break;
		case '200.100.503':$erro=3;$msg='invalid Request/Transaction/Customer/Contact tag (not present or [partially] empty)';break;
		case '200.100.504':$erro=3;$msg='invalid Request/Transaction/Customer/Address tag (not present or [partially] empty)';break;
		case '200.100.601':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay) tag (not present or [partially] empty)';break;
		case '200.100.602':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay)/PaymentToken tag (not present or [partially] empty)';break;
		case '200.100.603':$erro=3;$msg='invalid Request/Transaction/(ApplePay|GooglePay)/PaymentToken tag (decryption error)';break;
		case '200.200.106':$erro=3;$msg='duplicate transaction. Please verify that the UUID is unique';break;
		case '200.300.403':$erro=3;$msg='Invalid HTTP method';break;
		case '200.300.404':$erro=3;$msg='invalid or missing parameter';break;
		case '200.300.405':$erro=3;$msg='Duplicate entity';break;
		case '200.300.406':$erro=3;$msg='Entity not found';break;
		case '200.300.407':$erro=3;$msg='Entity not specific enough';break;
		case '800.900.100':$erro=3;$msg='sender authorization failed';break;
		case '800.900.101':$erro=3;$msg='invalid email address (probably invalid syntax)';break;
		case '800.900.200':$erro=3;$msg='invalid phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
		case '800.900.201':$erro=3;$msg='unknown channel';break;
		case '800.900.300':$erro=3;$msg='invalid authentication information';break;
		case '800.900.301':$erro=3;$msg='user authorization failed, user has no sufficient rights to process transaction';break;
		case '800.900.302':$erro=3;$msg='Authorization failed';break;
		case '800.900.303':$erro=3;$msg='No token created';break;
		case '800.900.399':$erro=3;$msg='Secure Registration Problem';break;
		case '800.900.401':$erro=3;$msg='Invalid IP number';break;
		case '800.900.450':$erro=3;$msg='Invalid birthdate';break;

		case '100.800.100':$erro=3;$msg='request contains no street';break;
		case '100.800.101':$erro=3;$msg='The combination of street1 and street2 must not exceed 201 characters.';break;
		case '100.800.102':$erro=3;$msg='The combination of street1 and street2 must not contain only numbers.';break;
		case '100.800.200':$erro=3;$msg='request contains no zip';break;
		case '100.800.201':$erro=3;$msg='zip too long';break;
		case '100.800.202':$erro=3;$msg='invalid zip';break;
		case '100.800.300':$erro=3;$msg='request contains no city';break;
		case '100.800.301':$erro=3;$msg='city too long';break;
		case '100.800.302':$erro=3;$msg='invalid city';break;
		case '100.800.400':$erro=3;$msg='invalid state/country combination';break;
		case '100.800.401':$erro=3;$msg='state too long';break;
		case '100.800.500':$erro=3;$msg='request contains no country';break;
		case '100.800.501':$erro=3;$msg='invalid country';break;

		case '100.700.100':$erro=3;$msg='customer.surname may not be null';break;
		case '100.700.101':$erro=3;$msg='customer.surname length must be between 0 and 50';break;
		case '100.700.200':$erro=3;$msg='customer.givenName may not be null';break;
		case '100.700.201':$erro=3;$msg='customer.givenName length must be between 0 and 50';break;
		case '100.700.300':$erro=3;$msg='invalid salutation';break;
		case '100.700.400':$erro=3;$msg='invalid title';break;
		case '100.700.500':$erro=3;$msg='company name too long';break;
		case '100.700.800':$erro=3;$msg='identity contains no or invalid \'paper\'';break;
		case '100.700.801':$erro=3;$msg='identity contains no or invalid identification value';break;
		case '100.700.802':$erro=3;$msg='identification value too long';break;
		case '100.700.810':$erro=3;$msg='specify at least one identity';break;
		case '100.900.100':$erro=3;$msg='request contains no email address';break;
		case '100.900.101':$erro=3;$msg='invalid email address (probably invalid syntax)';break;
		case '100.900.105':$erro=3;$msg='email address too long (max 50 chars)';break;
		case '100.900.200':$erro=3;$msg='invalid phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
		case '100.900.300':$erro=3;$msg='invalid mobile phone number (has to start with a digit or a '+', at least 7 and max 25 chars long)';break;
		case '100.900.301':$erro=3;$msg='mobile phone number mandatory';break;
		case '100.900.400':$erro=3;$msg='request contains no ip number';break;
		case '100.900.401':$erro=3;$msg='invalid ip number';break;
		case '100.900.450':$erro=3;$msg='invalid birthdate';break;
		case '100.900.500':$erro=3;$msg='invalid recurrence mode';break;


		case '100.100.100':$erro=3;$msg='request contains no creditcard, bank account number or bank name';break;
		case '100.100.101':$erro=3;$msg='invalid creditcard, bank account number or bank name';break;
		case '100.100.104':$erro=3;$msg='invalid unique id / root unique id';break;
		case '100.100.200':$erro=3;$msg='request contains no month';break;
		case '100.100.201':$erro=3;$msg='invalid month';break;
		case '100.100.300':$erro=3;$msg='request contains no year';break;
		case '100.100.301':$erro=3;$msg='invalid year';break;
		case '100.100.303':$erro=3;$msg='card expired';break;
		case '100.100.304':$erro=3;$msg='card not yet valid';break;
		case '100.100.305':$erro=3;$msg='invalid expiration date format';break;
		case '100.100.400':$erro=3;$msg='request contains no cc/bank account holder';break;
		case '100.100.401':$erro=3;$msg='cc/bank account holder too short or too long';break;
		case '100.100.402':$erro=3;$msg='cc/bank account holder not valid';break;
		case '100.100.500':$erro=3;$msg='request contains no credit card brand';break;
		case '100.100.501':$erro=3;$msg='invalid credit card brand';break;
		case '100.100.600':$erro=3;$msg='empty CVV for VISA,MASTER, AMEX not allowed';break;
		case '100.100.601':$erro=3;$msg='invalid CVV/brand combination';break;
		case '100.100.650':$erro=3;$msg='empty CreditCardIssueNumber for MAESTRO not allowed';break;
		case '100.100.651':$erro=3;$msg='invalid CreditCardIssueNumber';break;
		case '100.100.700':$erro=3;$msg='invalid cc number/brand combination';break;
		case '100.100.701':$erro=3;$msg='suspecting fraud, this card may not be processed';break;
		case '100.200.100':$erro=3;$msg='bank account contains no or invalid country';break;
		case '100.200.103':$erro=3;$msg='bank account has invalid bankcode/name account number combination';break;
		case '100.200.104':$erro=3;$msg='bank account has invalid acccount number format';break;
		case '100.200.200':$erro=3;$msg='bank account needs to be registered and confirmed first. Country is mandate based.';break;
		case '100.210.101':$erro=3;$msg='virtual account contains no or invalid Id';break;
		case '100.210.102':$erro=3;$msg='virtual account contains no or invalid brand';break;
		case '100.211.101':$erro=3;$msg='user account contains no or invalid Id';break;
		case '100.211.102':$erro=3;$msg='user account contains no or invalid brand';break;
		case '100.211.103':$erro=3;$msg='no password defined for user account';break;
		case '100.211.104':$erro=3;$msg='password does not meet safety requirements (needs 8 digits at least and must contain letters and numbers)';break;
		case '100.211.105':$erro=3;$msg='wallet id has to be a valid email address';break;
		case '100.211.106':$erro=3;$msg='voucher ids have 32 digits always';break;
		case '100.212.101':$erro=3;$msg='wallet account registration must not have an initial balance';break;
		case '100.212.102':$erro=3;$msg='wallet account contains no or invalid brand';break;
		case '100.212.103':$erro=3;$msg='wallet account payment transaction needs to reference a registration';break;


		case '100.550.300':$erro=3;$msg='request contains no amount or too low amount';break;
		case '100.550.301':$erro=3;$msg='amount too large';break;
		case '100.550.303':$erro=3;$msg='amount format invalid (only two decimals allowed).';break;
		case '100.550.310':$erro=3;$msg='amount exceeds limit for the registered account.';break;
		case '100.550.311':$erro=3;$msg='exceeding account balance';break;
		case '100.550.312':$erro=3;$msg='Amount is outside allowed ticket size boundaries';break;
		case '100.550.400':$erro=3;$msg='request contains no currency';break;
		case '100.550.401':$erro=3;$msg='invalid currency';break;
		case '100.550.601':$erro=3;$msg='risk amount too large';break;
		case '100.550.603':$erro=3;$msg='risk amount format invalid (only two decimals allowed)';break;
		case '100.550.605':$erro=3;$msg='risk amount is smaller than amount (it must be equal or bigger then amount)';break;
		case '100.550.701':$erro=3;$msg='amounts not matched';break;
		case '100.550.702':$erro=3;$msg='currencies not matched';break;

		case '100.380.101':$erro=3;$msg='transaction contains no risk management part';break;
		case '100.380.201':$erro=3;$msg='no risk management process type specified';break;
		case '100.380.305':$erro=3;$msg='no frontend information provided for asynchronous transaction';break;
		case '100.380.306':$erro=3;$msg='no authentication data provided in risk management transaction';break;

		case '000.100.200':$erro=3;$msg='Reason not Specified';break;
		case '000.100.201':$erro=3;$msg='Account or Bank Details Incorrect';break;
		case '000.100.202':$erro=3;$msg='Account Closed';break;
		case '000.100.203':$erro=3;$msg='Insufficient Funds';break;
		case '000.100.204':$erro=3;$msg='Mandate not Valid';break;
		case '000.100.205':$erro=3;$msg='Mandate Cancelled';break;
		case '000.100.206':$erro=3;$msg='Revocation or Dispute';break;
		case '000.100.207':$erro=3;$msg='Cancellation in Clearing Network';break;
		case '000.100.208':$erro=3;$msg='Account Blocked';break;
		case '000.100.209':$erro=3;$msg='Account does not exist';break;
		case '000.100.210':$erro=3;$msg='Invalid Amount';break;
		case '000.100.211':$erro=3;$msg='Transaction succeeded (amount of transaction is smaller then amount of pre-authorization)';break;
		case '000.100.212':$erro=3;$msg='Transaction succeeded (amount of transaction is greater then amount of pre-authorization)';break;
		case '000.100.220':$erro=3;$msg='Fraudulent Transaction';break;
		case '000.100.221':$erro=3;$msg='Merchandise Not Received';break;
		case '000.100.222':$erro=3;$msg='Transaction Not Recognized By Cardholder';break;
		case '000.100.223':$erro=3;$msg='Service Not Rendered';break;
		case '000.100.224':$erro=3;$msg='Duplicate Processing';break;
		case '000.100.225':$erro=3;$msg='Credit Not Processed';break;
		case '000.100.226':$erro=3;$msg='Cannot be settled';break;
		case '000.100.227':$erro=3;$msg='Configuration Issue';break;
		case '000.100.228':$erro=3;$msg='Temporary Communication Error - Retry';break;
		case '000.100.229':$erro=3;$msg='Incorrect Instructions';break;
		case '000.100.230':$erro=3;$msg='Unauthorised Charge';break;
		case '000.100.231':$erro=3;$msg='Late Representment';break;
		case '000.100.232':$erro=3;$msg='Liability Shift';break;
		case '000.100.233':$erro=3;$msg='Authorization-Related Chargeback';break;
		case '000.100.234':$erro=3;$msg='Non receipt of merchandise';break;
		case '000.100.299':$erro=3;$msg='Unspecified (Technical)';break;

	}
	return $msg;
}
function getNomeMetodoPagamentoByTipo_plataforma($tipo='')
{
	if(!$tipo)return;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;
	$metodo_pagamento = '';
	$sql="SELECT Abreviatura FROM metodos_pagamento mp WHERE (mp.ID=$tipo)";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodo_pagamento = $rs2->db_data['Abreviatura'];
	}
	$rs2->db_close();
	return $metodo_pagamento;
}
function getNomeMetodoExpedicao_plataforma($tipo='')
{
	if(!$tipo)return;

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;

	$metodos_expedicao = '';

	$sql="SELECT Abreviatura FROM metodos_expedicao mp WHERE mp.ID=$tipo";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodos_expedicao = $rs2->db_data['Abreviatura'];
	}
	$rs2->db_close();
	return $metodos_expedicao;
}
function menu_perfil($class='PC')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$return_resp='';
	$sql = 'SELECT
				m.ID,m.catalogo_id,m.tipo_catalogo,m.img_ext,m.TipoMenu,m.URL_Link,
				ml.URL_Nome2
			FROM
				menus_links m
				INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
			WHERE
				m.tipo_catalogo = "menu_reservado"
				AND m.Visivel=1
				AND ml.Lang="'.$GLOBALS['site_lang'].'"
				'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado = 2 OR Logado = 0)' : ' AND (Logado = 1 OR Logado = 0)').'
			ORDER BY
				m.Ordem ASC';

	$rs->db_open($db_link,$DB_name,$sql);

	$return = '';
	if(!$rs->EOF())
	{
		$return  = '<hr>';
		$return .= '<ul class="menu-areacliente '.$class.'">';
		while(!$rs->EOF())
		{
			$menu_id = $rs->db_data['ID'];
			$menu_ext = $rs->db_data['img_ext'];
			$catalogo_id = $rs->db_data['catalogo_id'];
			$catalogo_tipo = $rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
			$menu_tipo = $rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
			$menu_nome = $rs->db_data['URL_Nome2'];
			$menu_url = seo($rs->db_data['URL_Link']);

			$return .= '<li><a href="'.$menu_url.'" class="'.(isset($_GET['show_rmas']) ? ' active' : '').'">'.$menu_nome.'</a></li>';

			$rs->db_next();
		}

		$return .= '</ul>';
		$return .= '<hr>';
	}
	$rs->db_close(1);

	return $return;
}

function login_form($redir = 0)
{
    global $x_password,$x_email,$registo_page_id,$error_message;
    $num_tentativas = validarTentativaLogin();
    if ($num_tentativas < 5)
    {
        $registar_url = seo('pages.php?page_id='.$registo_page_id).'?registar=1';
        $recuperar_url = seo('pages.php?page_id='.$registo_page_id).'?recupera=1';
        $form_action_url = seo('pages.php?page_id='.$registo_page_id);
        $referer_dir = referer_dir();
        if ($error_message)
        {
            $error_message = show_alerta($error_message,'erro');
        }
        
		// echo '<div class="titulo_d">'. getLinguas('key_bem_vindo_auntentiquese',$GLOBALS['site_lang']) .'</div>';


        /*-------------- Begin Social Network Login -----------*/
	    global $DB_host,$DB_user,$DB_pass,$DB_name;
	    $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	    $rs = new DataObj;
	    $sql = 'SELECT facebook_id,facebook_secret,facebook_ativo,google_id,google_secret,google_ativo FROM modulos_config_site WHERE ID=1';
	    $rs->db_open($db_link,$DB_name,$sql);
	    if(!$rs->EOF())
	    {
	        $facebook_activo = $rs->db_data['facebook_ativo'];
	        $google_activo = $rs->db_data['google_ativo'];

	        if($facebook_activo==1 || $google_activo==1)
	        {
	        	/*-------------- Begin Social Network Login -----------*/?>
				<div class="zz-separator-text">
					<span style="position: relative;z-index: 1;padding: 0 10px;box-sizing: inherit;text-align: center;font-size: 18px;color:#000; background: #FFFFFF">Utilize as Redes Sociais</span>
				</div>

				<div class="container_social_login">
					<div class="row_social_login">
					  <div class="col_social_login" style="text-align:center;">
					  	<?php if($facebook_activo==1){ ?>
							<div class="facebook GTM_login_facebook" onclick="login_facebook()" style="display: inline-block; vertical-align: top; ">
								<a href="#" class="fb_social_login btn_social_login" style="width:100%;">
									<?php echo getLinguas('key_login_facebook',$GLOBALS['site_lang']) ?>
								</a>
							</div>
							<?} if($google_activo==1){ ?>
							<div class="google GTM_login_google" onclick="login_google()" style="display: inline-block; vertical-align: top;">
								<a href="#" class="google_social_login btn_social_login" style="width:100%;margin: 0 auto;">
								  <?php echo getLinguas('key_login_google',$GLOBALS['site_lang']) ?>
								</a>
							</div>
							<?php } ?>
					  </div>
					</div>
				</div>
				<?/*-------------- End Social Network Login -----------*/?>

				<div class="zz-separator-text">
					<span style="position: relative;z-index: 1;padding: 0 10px;box-sizing: inherit;text-align: center;font-size: 18px;color:#000; background: #FFFFFF">Ou</span>
				</div>
				<?php
			}
		}
		$rs->db_close();?>
		<br><br>
		<div class="row">
			<div class="PC col-md-6" style="padding: 0;">
				<img alt="" src="/files/girl_login.png" style="width: 100%;"/>
			</div>
			<div class="col-md-6" style="padding: 0;background: #f6f6f6;">
				<div class="login_box_right">			
					<div class="pag_pub_title" style="font-size: 23px;font-weight: normal;"><strong>BEM-VINDO</strong> À DOTBRIGHT</div>
					<br><br><br><br>
					<script type="text/javascript">
			        function validar_login() {
			            var campos_obrigatorios = ["m_email","m_password"];
			            var form_name = 'login_form1';
			            var campos_preenchidos;
			            var i = 0;
			            while (i < campos_obrigatorios.length) {
			                var $cmp = $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]');
			                $cmp.closest('.form-group').removeClass('has-success');
			                $cmp.closest('.form-group').removeClass('has-error');
			                i = i + 1;
			            }
			            i = 0;
			            while (i < campos_obrigatorios.length) {
			                if (document.forms[form_name].elements[campos_obrigatorios[i]].value == "") {
			                    campos_preenchidos = 1;
			                    $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]').closest('.form-group').addClass('has-error');
			                }
			                i = i + 1;
			            }
			            if (campos_preenchidos == 1) {
			                new Apprise("<?php echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
			                return false;
			            }
			            return true;
			        }
			    </script>
					<?php
						if($error_message){
							echo $error_message;
							echo '<br>';
						}
					?>
					<form action="<?=$form_action_url ?>" method="post" target="_top" name="login_form1" id="login_form1" onsubmit="return validar_login();">
						<input type="hidden" value="1" name="login"> <input type="hidden" name="submit1" value="1">
						<input type="hidden" name="redirect" value="<?=$redir ?>">
						<input type="hidden" size="90" name="referer" value="<?=$referer_dir ?>">

						<div class="form-group">
							<label for="m_email" style="color: #9da09c;font-size: 14px;font-weight: normal;"><?php echo getLinguas('key_email',$GLOBALS['site_lang']) ?>
								<span class="mandatory" style="color: #ec870e;font-size: 15px;">*</span></label>
							<input id="m_email" autocomplete="off" type="email" value="" name="m_email" class="form-control" style="border: none;border-bottom: 1px solid #D6D5D5;background: #f6f6f6;margin-bottom: 20px;<?=$x_email[3] ?>"/>
						</div>

						<div class="form-group">
							<label for="m_password" style="color: #9da09c;font-size: 14px;font-weight: normal;"><?php echo getLinguas('key_password',$GLOBALS['site_lang']) ?>
								<span class="mandatory" style="color: #ec870e;font-size: 15px;">*</span></label>
							<input id="m_password" autocomplete="off" type="password" value="" autocomplete="off" name="m_password" class="form-control" style="border: none;border-bottom: 1px solid #D6D5D5;background: #f6f6f6;margin-bottom: 20px;<?=$x_email[3] ?>"/>
						</div>

						<div class="row">
							<div class="col-md-6">
								<span class="mandatory" style="color: #ec870e;font-size: 15px;">*</span> <span style="color: #ec870e;"><?php echo getLinguas('key_cliente_camposobrigatorios',$GLOBALS['site_lang']); ?></span>
							</div>
							<div class="col-md-6" style="text-align: right;">
								<a class="link-recuperar" href="<?=$recuperar_url ?>"><?php echo getLinguas('key_cliente_recuperar_pass',$GLOBALS['site_lang']) ?></a>
							</div>
						</div>
						<br><br><br>
						<div class="form-group" style="text-align: center;">
							<input type="submit" value="ENTRAR" class="button3" style="font-size: 18px;padding: 15px 60px;border-radius: 25px;background: #292929;">
						</div>

					</form>
					<br><br><br>
					<div class="row">
						<span style="margin: 0 auto;color: #000;font-size: 16px;">Ainda não é nosso cliente? <a href="p5-registo-utilizador-pt?registar=1"><strong style="color: #ec870e;">Registe-se</strong></a></span>
					</div>
				</div>
			</div>
		</div>
		<br><br>
        <?php
    }
}

function showFormRecuperarPassword()
{
    $m_email = '';
    if (isset($_POST['m_email'])) $m_email = htmlentities($_POST['m_email']);
    ?>
    <script type="text/javascript">
        function validar_recupera() {
            var campos_obrigatorios = ["m_email"];
            var form_name = 'recuperar_form';
            var campos_preenchidos;
            var i = 0;
            while (i < campos_obrigatorios.length) {
                var $cmp = $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]');
                $cmp.closest('.form-group').removeClass('has-success');
                $cmp.closest('.form-group').removeClass('has-error');
                i = i + 1;
            }
            i = 0;
            while (i < campos_obrigatorios.length) {
                if (document.forms[form_name].elements[campos_obrigatorios[i]].value == "") {
                    campos_preenchidos = 1;
                    $('#' + form_name + ' input[name="' + campos_obrigatorios[i] + '"]').closest('.form-group').addClass('has-error');
                }
                i = i + 1;
            }
            if (campos_preenchidos == 1) {
                new Apprise("<?php echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
                return false;
            }
            return true;
        }
    </script><h1 class="text-center"><?php echo getLinguas('key_recuperar_pass',$GLOBALS['site_lang']) ?></h1>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <hr>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <form id="recuperar_form" name="recuperar_form" method="post" onsubmit="return validar_recupera();">
                        <input type="hidden" name="recupera" value="1">
                        <input type="hidden" name="submit1" value="1"><br>
                        <?php
                        if ($GLOBALS['error_message'] != '') {
                            echo show_alerta($GLOBALS['error_message'],'erro');
                        }
                        ?>
                        <br>
                        <div class="form-group <?php echo isset($GLOBALS['x_email'][3]) ? $GLOBALS['x_email'][3] : ''; ?>">
                            <input type="email" name="m_email" id="m_email" value="<?=$m_email; ?>" placeholder="<?php echo getLinguas('key_email',$GLOBALS['site_lang']) ?>" class="form-control">
                        </div>

                        <div class="form-group text-center">
                            <input type="button" value="<?php echo getLinguas('key_submeter_cancel',$GLOBALS['site_lang']) ?>" class="button3" onclick="window.open('<?php echo seo('pages.php?page_id='.$GLOBALS['registo_page_id']); ?>','_self')">
                            <input type="submit" value="<?php echo getLinguas('key_submeter_rec',$GLOBALS['site_lang']) ?>" class="button3">
                        </div>
                        <br><br>
                    </form>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
    <?php
}

function showFormRegisto()
{
    //caso tenha mais campos de registo
    if($GLOBALS['tem_cliente_ficha'] == 1)
	{
		showFormRegisto_proprio();
		return;
	}

    global $site_lang;
    global $x_email,$x_contribuinte,$x_morada,$x_codpos,$x_telefone,$x_cidade,$x_empresa,$x_website;
    global $x_password,$x_password_repete,$code;

    $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rs_paises = new Dataobj;

    ?>
	<script>
	function validar_registo()
	{
		var cor = "#ffffe5";
		var cor2 = "#ffffff";

		<?php

		if($GLOBALS['nif_mandatory']==1) echo 'campos_obrigatorios=new Array("m_password","m_password_repete","m_email","m_contribuinte","m_nome","m_apelido","m_morada","m_codpos","m_cidade","m_telefone");';
		else echo 'campos_obrigatorios=new Array("m_password","m_password_repete","m_email","m_nome","m_apelido","m_morada","m_codpos","m_cidade","m_telefone");';

		?>

		var campos_preenchidos;
		var i=0; while(i < campos_obrigatorios.length){ $('input[name="'+campos_obrigatorios[i]+'"]').css("background-color",cor2);i=i+1;}
		var i=0; while(i < campos_obrigatorios.length){ if(document.forms['registoform'].elements[campos_obrigatorios[i]].value==""){ campos_preenchidos=1; $('input[name="'+campos_obrigatorios[i]+'"]').css("background-color",cor);} i=i+1;	}


		// VALIDACAO CAPTCHA
		//var response = grecaptcha.getResponse();
		
		if(campos_preenchidos !=1)
		{
			if(document.getElementById('m_termos') && document.getElementById('m_termos').checked == false) {campos_preenchidos = 3;}
			if(document.getElementById('m_termos1') && document.getElementById('m_termos1').checked == false) {campos_preenchidos = 4;}
		}

		if(campos_preenchidos==1){Apprise("<?php echo getLinguas('key_camposobr'); ?>",'erro');return false;}
		else if(campos_preenchidos==2){Apprise("Selecione o campo de prevenção spam para sabermos que não é um robô",'erro');return false;}
		else if(campos_preenchidos==3){Apprise("Selecione que leu e aceita os Termos e Condições",'erro');return false;}
		else if(campos_preenchidos==4){Apprise("Selecione que leu e aceita os Termos de Confidencialidade",'erro');return false;}
		else if(document.forms['registoform'].elements[campos_obrigatorios[0]].value!=document.forms['registoform'].elements[campos_obrigatorios[1]].value)
		{
			document.forms['registoform'].elements[campos_obrigatorios[0]].background=cor;
			document.forms['registoform'].elements[campos_obrigatorios[1]].background=cor;
			Apprise("<?php echo getLinguas('key_password_repetida'); ?>",'erro'); return false;
		}
		return true;
	}
	</script>
    <div class="titulo_d"><?php echo getLinguas('key_registo_titulo',$GLOBALS['site_lang']); ?></div>
    <?php
    if ($GLOBALS['error_message']) {
        echo show_alerta($GLOBALS['error_message'],'erro');
    }
    ?>
    <form class="form-registo" method="post" name="registoform" onSubmit="return validar_registo()">
        <input type="hidden" name="registar" value="1"/> <input type="hidden" name="submit1" value="1"/>

        <div class="subtitulo_d margin-30"><?php echo getLinguas('key_registo_subtitulo',$GLOBALS['site_lang']); ?></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_email"><?php echo getLinguas('key_email',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_email" maxlength="255" autocomplete="off" type="email" value="<?php if (isset($_POST['m_email']))
                        echo htmlentities($_POST['m_email']) ?>" name="m_email" class="form-control <?=$x_email[3] ?>" />
                </div>
            </div>
            <?php
            if ($GLOBALS['subscribe_newsletter'] == 1) {
                ?>
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom:0">
                        <label><?php echo getLinguas('key_subscribe_newsletter2',$GLOBALS['site_lang']) ?></label>
                    </div>
                    <div class="checkbox" style="margin-top:5px;margin-bottom:25px;">
                        <label for="m_newsletter"><input type="checkbox" name="m_newsletter" id="m_newsletter" value="1"> <?php echo getLinguas('key_subscribe_newsletter3',$GLOBALS['site_lang']) ?></label>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="col-md-6"></div><?php
            }
            ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_password"><?php echo getLinguas('key_password',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_password" maxlength="255" autocomplete="off" type="password" value="" name="m_password" class="form-control <?=$x_password[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_password_repete"><?php echo getLinguas('key_repetepassword',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_password_repete" maxlength="255" autocomplete="off" type="password" value="" name="m_password_repete" class="form-control <?=$x_password_repete[3] ?>" style=""/>
                </div>
            </div>
        </div>
        <div class="subtitulo_d margin-30"><?php echo getLinguas('key_registo_subtitulo1',$GLOBALS['site_lang']); ?></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_nome"><?php echo getLinguas('key_nome',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_nome" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_nome'])) echo htmlentities($_POST['m_nome']) ?>" name="m_nome" class="form-control <?=$x_nome[3] ?>" style=""/>
                </div>
            </div>
			<div class="col-md-6">
                <div class="form-group">

                    <label for="m_apelido"><?php echo getLinguas('key_apelido',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_apelido" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_apelido'])) echo htmlentities($_POST['m_apelido']) ?>" name="m_apelido" class="form-control <?=$x_apelido[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_morada"><?php echo getLinguas('key_morada',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_morada" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_morada']))
                        echo htmlentities($_POST['m_morada']) ?>" name="m_morada" class="form-control <?=$x_morada[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_codpos"><?php echo getLinguas('key_codpos',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_codpos" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_codpos']))
                        echo htmlentities($_POST['m_codpos']) ?>" name="m_codpos" class="form-control <?=$x_codpos[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_cidade"><?php echo getLinguas('key_cidade',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_cidade" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_cidade']))
                        echo htmlentities($_POST['m_cidade']) ?>" name="m_cidade" class="form-control <?=$x_cidade[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_pais"><?php echo getLinguas('key_pais',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label> <select name="m_pais" id="m_pais" class="form-control">
                        <?php
                        $sql = "SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='$site_lang' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
                        $rs_paises->db_open($db_link,$GLOBALS['DB_name'],$sql);
                        while (!$rs_paises->EOF()) {
                            $zone_name = $rs_paises->db_data['zone_name'];
                            $zone_id = $rs_paises->db_data['zone_id'];
                            echo '<option value="'.$zone_id.'" ';
                            if (!isset($_POST['m_pais'])) {
                                $_POST['m_pais'] = 165;
                            }
                            if ($_POST['m_pais'] == $zone_id) {
                                echo 'selected="selected"';
                            }
                            echo '>'.$zone_name.'</option>';

                            $rs_paises->db_next();
                        }
                        $rs_paises->db_close();
                        ?>
                    </select>
                </div>
            </div>
			<div class="clear"></div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_contribuinte"><?php echo getLinguas('key_nif',$GLOBALS['site_lang']); ?>
                        <?php if($GLOBALS['nif_mandatory']==1) echo '<span class="mandatory">*</span>';?></label>
                    <input id="m_contribuinte" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_contribuinte']))
                        echo htmlentities($_POST['m_contribuinte']) ?>" name="m_contribuinte" class="form-control <?=$x_contribuinte[3] ?>" style=""/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="m_telefone"><?php echo getLinguas('key_tel',$GLOBALS['site_lang']) ?>
                        <span class="mandatory">*</span></label>
                    <input id="m_telefone" maxlength="255" autocomplete="off" type="text" value="<?php if (isset($_POST['m_telefone']))
                        echo htmlentities($_POST['m_telefone']) ?>" name="m_telefone" class="form-control <?=$x_telefone[3] ?>" style=""/>
                </div>
            </div>

        </div>


        <div class="checkbox">
            <label for="m_termos"> <input type="checkbox" id="m_termos" name="m_termos" value="1"> <strong><?php echo getLinguas('key_termos',$GLOBALS['site_lang']); ?></strong><span class="mandatory">*</span> </label> (<a href="<?php echo seo('pages.php?page_id='.$GLOBALS['termos_page_id']).'?popup=1'?>" class="fancy_content"><?php echo getLinguas('key_termos_registo',$GLOBALS['site_lang']); ?></a>)</strong>
            <br/><label for="m_termos1"> <input type="checkbox" id="m_termos1" name="m_termos1" value="1"> <strong><?php echo getLinguas('key_termos1',$GLOBALS['site_lang']); ?></strong><span class="mandatory">*</span> </label> (<a href="<?php echo seo('pages.php?page_id='.$GLOBALS['termosregisto_page_id']).'?popup=1'?>" class="fancy_content"><?php echo getLinguas('key_termos_registo2',$GLOBALS['site_lang']); ?></a>)</strong>
        </div>
        <br><?php /*
        <div class="row">
            <div class="col-xs-12 col-md-3 col-md-3">
                <div class="form-group">
                    <label for="captcha_code"><?php echo getLinguas('key_spam',$GLOBALS['site_lang']) ?><span class="mandatory">*</span></label>
                </div>
            </div>
            <div class="col-xs-12 col-md-5 col-md-5">
                <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_CAPTCHA_SITE_KEY; ?>"></div>
				<script src='https://www.google.com/recaptcha/api.js'></script>
            </div>

            <div class="col-md-4"></div>
        </div>*/?>
		<br>
        <div class="form-group text-center">
            <input type="submit" value="CRIAR CONTA" class="button3" />
        </div>

    </form>
    <script type="text/javascript">
        /* jQuery(document).ready(function ($) {
            $("#m_iniactividade").datepicker({format: 'yyyy-mm-dd',language: '<?=$GLOBALS["site_lang"];?>'});
            $("#m_iniactividade").siblings(".dtpicker").click(function () {
                $("#m_iniactividade").datepicker('show');
            });
        }); */
    </script>
    <?php
}

function show_registo_news()
{
    global $error_message;
    global $x_email,$code;

    $error_message = '';
    if (!isset($_POST['submit'])) {
        show_newsForm();
    } else {
        $error = 0;
        if (!empty($_POST['m_email'])) {
            $x_email[2] = clean_var($_POST['m_email']);
            if (!validEmail($x_email[2])) {
                $error = 1;
                $x_email[3] = ' has-error ';
            } else {
                $x_email[3] = ' has-success ';
            }
        } else {
            $error = 1;
            $x_email[3] = ' has-error ';
        }
        if (empty($_POST['captcha_code'])) {
            $error = 1;
            $code[3] = ' has-error ';
        } else {
            include_once $GLOBALS['site_shell_url'].'external_api/captcha/contact-securimage.php';
            $securimage = new Securimage();
            if ($securimage->check(strtolower($_POST['captcha_code'])) == false) {
                $error = 1;
                $code[3] = ' has-error ';
            } else {
                $code[3] = ' has-success ';
            }
        }
        if ($error == 1) {
            $error_message = getLinguas('key_captcha2',$GLOBALS['site_lang']);
            show_newsForm();
        } else {
            $email = clean_var($_POST['m_email']);
            $nome = clean_var($_POST['m_nome']);

            $db_link = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
            $rs = new DataObj;
            $rs2 = new DataObj;
            $rs3 = new DataObj;

            $sql = "SELECT ID,Email FROM nl_emails_registados WHERE Email='$email'";
            $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
            if ($rs->EOF()) {
                $sql = "SELECT ID,Email FROM nl_emails_removidos WHERE Email='$email'";
                $rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
                if ($rs2->EOF()) {
                    $sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
                    $rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
                    if (!$rs3->EOF()) $id_grupo = $rs3->db_data['ID'];
                    else $id_grupo = 0;
                    $rs3->db_close();

                    $sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."')";
                    $res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
                    if ($res) {
                        send_email($GLOBALS['Default_notifications_email_newsletter'],'Novo Registo na Newsletter','O email '.$email.' registou-se na newsletter. Para mais informações faça login no backoffice e aceda: '.$GLOBALS['site_http_url'].'admin/newsletter/newsletter_emails/list.php');
                        echo show_alerta2(getLinguas('key_newsletter_registook',$GLOBALS['site_lang']));
                    } else {
                        $error_message = getLinguas('key_newsletter_registoerro1',$GLOBALS['site_lang']);
                        show_newsForm();
                    }
                } else {
                    $sql = "DELETE FROM nl_emails_removidos WHERE ID='{$rs2->db_data['ID']}'";
                    Runquery($db_link,$GLOBALS['DB_name'],$sql);

                    $sql = "SELECT ID FROM nl_grupos WHERE Visivel=1";
                    $rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
                    if (!$rs3->EOF()) $id_grupo = $rs3->db_data['ID'];
                    else $id_grupo = 0;
                    $rs3->db_close();

                    $sql = "INSERT INTO nl_emails_registados (ID_ORG,Nome,Email,data_hora) values ('$id_grupo','$nome','$email','".date("Y-m-d H:i:s")."')";
                    $res = Runquery($db_link,$GLOBALS['DB_name'],$sql);
                    if ($res) {
                        send_email($GLOBALS['Default_notifications_email_newsletter'],'Novo Registo na Newsletter','O email '.$email.' registou-se na newsletter. Para mais informações faça login no backoffice e aceda: '.$GLOBALS['site_http_url'].'admin/newsletter/newsletter_emails/list.php');
                        echo show_alerta2(getLinguas('key_newsletter_registook',$GLOBALS['site_lang']));
                    } else {
                        $error_message = getLinguas('key_newsletter_registoerro1',$GLOBALS['site_lang']);
                        show_newsForm();
                    }
                }
                $rs2->db_close();
            } else {
                $error_message = getLinguas('key_newsletter_registoerro2',$GLOBALS['site_lang']);
                show_newsForm();
            }
            $rs->db_close();
        }
    }
}

function show_newsForm()
{
    $m_nome = '';
    if (isset($_POST['m_nome'])) $m_nome = htmlentities($_POST['m_nome']);
    $m_email = '';
    if (isset($_POST['m_email'])) $m_email = htmlentities($_POST['m_email']);
    elseif (isset($_POST['m_email_newsletter'])) $m_email = htmlentities($_POST['m_email_newsletter']);

	if(isset($GLOBALS['cor_border']) && $GLOBALS['cor_border'] != '' )$cor_border=$GLOBALS['cor_border'];
    else $cor_border='#5a0a00';
    if(isset($GLOBALS['cor_alerta_border']) && $GLOBALS['cor_alerta_border'] != '' )$cor_alerta_border=$GLOBALS['cor_alerta_border'];
	else $cor_alerta_border='#e83b44';

    ?>
    <script type="text/javascript">


		 function validar_newsletter_total() {
            var cor = "<?=$cor_border?>";
            var cor2 = "<?=$cor_alerta_border?>";
            var campos_obrigatorios = ["m_email","captcha_code"];
            var campos_preenchidos;
            var form_name = "registo_newsletter";
            var i = 0;
            while (i < campos_obrigatorios.length) {
                document.forms[form_name].elements[campos_obrigatorios[i]].style.background = cor2;
                i = i + 1;
            }
            i = 0;
            while (i < campos_obrigatorios.length) {
                if (document.forms[form_name].elements[campos_obrigatorios[i]].value == "") {
                    campos_preenchidos = 1;
                    document.forms[form_name].elements[campos_obrigatorios[i]].style.background = cor;
                }
                i = i + 1;
            }
            if (campos_preenchidos == 1) {
                new Apprise("<?php echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
                return false;
            }
            return true;
        }
    </script>
	<? //echo '<h1 class="text-center">'.getLinguas('key_newsletter_title',$GLOBALS['site_lang']).'</h1>'; ?>
    <hr>
    <div class="row">
        <? //echo '<div class="col-md-4"></div>'; ?>
        <div class="col-md-10">
            <form method="post" name="registo_newsletter" id="registo_newsletter" onsubmit="return validar_newsletter_total()">
                <input type="hidden" name="newsletter" value="1">

                <?php
                if ($GLOBALS['error_message'] != '') {
                    echo show_alerta($GLOBALS['error_message'],'erro');
                }
			?>
                <div class="form-group">
					<label for="m_nome" style="font-weight: bold;color: #000;">Nome</label>
                    <input type="text" class="form-control" placeholder="<?php //echo getLinguas('key_nome',$GLOBALS['site_lang']); ?>" name="m_nome" maxlength="254" value="<?=$m_nome; ?>">
                </div>
				<br>
                <div class="form-group <?php echo isset($GLOBALS['x_email'][3]) ? $GLOBALS['x_email'][3] : ''; ?>">
					<label for="m_email" style="font-weight: bold;color: #000;">Email</label>
                    <input type="email" class="form-control" placeholder="<?php //echo getLinguas('key_email',$GLOBALS['site_lang']); ?>" name="m_email" maxlength="254" value="<?=$m_email; ?>">
                </div>
				<br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="captcha" src="external_api/captcha/contact-securimage_show.php" alt="CAPTCHA Image"/>
                        </div>
                        <div class="col-md-8">
                            <a href="javascript:;" style="color: #6E6E6E;text-decoration: none;" onclick="document.getElementById('captcha').src='external_api/captcha/contact-securimage_show.php?' + Math.random(); return false">
                                <?php echo getLinguas('key_captcha1',$GLOBALS['site_lang']); ?>
                            </a>
                        </div>
                    </div>
                </div>
				<br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo getLinguas('key_spam2',$GLOBALS['site_lang']); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group <?php echo isset($GLOBALS['code'][3]) ? $GLOBALS['code'][3] : ''; ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <input autocomplete="off" placeholder="<?php echo getLinguas('key_spam',$GLOBALS['site_lang']); ?>" type="text" class="form-control" name="captcha_code" maxlength="255" value=""/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="<?php echo getLinguas('key_submeter_rec',$GLOBALS['site_lang']); ?>" id="submit_button" class="button3"/>
                </div>
            </form>
        </div>
        <? echo '<div class="col-xs-0 col-md-2"></div>'; ?>
    </div>
    <?php
}

function show_newsForm_small()
{
    $m_email = '';
    if (isset($_POST['m_email'])) $m_email = htmlentities($_POST['m_email']);
    if(isset($GLOBALS['cor_border']) && $GLOBALS['cor_border'] != '' )$cor_border=$GLOBALS['cor_border'];
    else $cor_border='#5a0a00';
    if(isset($GLOBALS['cor_alerta_border']) && $GLOBALS['cor_alerta_border'] != '' )$cor_alerta_border=$GLOBALS['cor_alerta_border'];
	else $cor_alerta_border='#e83b44';
    ?>
    <script type="text/javascript">
        function validar_newsletter() {
            var cor = "<?=$cor_border?>";
            var cor2 = "<?=$cor_alerta_border?>";
            var campos_obrigatorios = ["m_email"];
            var campos_preenchidos;
            var form_name = "newsletter_bottom";
            var i = 0;
            while (i < campos_obrigatorios.length) {
                document.forms[form_name].elements[campos_obrigatorios[i]].style.background = cor2;
                i = i + 1;
            }
            i = 0;
            while (i < campos_obrigatorios.length) {
                if (document.forms[form_name].elements[campos_obrigatorios[i]].value == "") {
                    campos_preenchidos = 1;
                    document.forms[form_name].elements[campos_obrigatorios[i]].style.background = cor;
                }
                i = i + 1;
            }
            if (campos_preenchidos == 1) {
                new Apprise("<?php echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
                return false;
            }
            return true;
        }
    </script><h3 class="text-center"><?php echo getLinguas('key_newsletter_title',$GLOBALS['site_lang']); ?></h3>
    <form method="post" name="newsletter_bottom" onsubmit="return validar_newsletter()" action="<?php echo seo('pages.php?page_id='.$GLOBALS['newsletter_page_id']); ?>">
        <input type="hidden" name="newsletter" value="1"/>
        <div class="pull-left">
            <input type="text" class="inputregiston" title="<?php echo getLinguas('key_seuemail',$GLOBALS['site_lang']); ?>" id="m_email" name="m_email" maxlength="254" value="<?=$m_email; ?>"/>
        </div>
        <div class="pull-left"><input type="image" src="layout/<?php echo PROJETO ?>/images/bt_news.png"/></div>
    </form>
    <?php
}

function newsletterRegisterSmallForm($class='',$nomargin='')
{
	global $site_lang,$site_http_url,$error_message;
	global $key_newsletter_title,$key_newsletter_subtitle,$key_newsletter_nome,$key_newsletter_email,$key_camposobr,$key_seuemail,$key_seunome;
	global $key_mensagem,$key_validacao,$key_obrigatorio,$key_friendnome,$key_friendmail,$key_recommend,$key_submeter_rec,$key_spam,$key_spam2,$key_spam3,$key_captcha1;
	global $x_nome,$x_email,$x_mensagem,$code;
	global $x_nome2,$x_email2;

	if(isset($GLOBALS['cor_border']) && $GLOBALS['cor_border'] != '' )$cor_border=$GLOBALS['cor_border'];
    else $cor_border='#5a0a00';
    if(isset($GLOBALS['cor_alerta_border']) && $GLOBALS['cor_alerta_border'] != '' )$cor_alerta_border=$GLOBALS['cor_alerta_border'];
	else $cor_alerta_border='#e83b44';
	?>

	<script>
	function checkEmail(field){email=field.value;AtPos=email.indexOf("@");StopPos=email.lastIndexOf(".");Message="";if(email==""||AtPos==-1||StopPos==-1||(StopPos<AtPos)||(StopPos-AtPos==1)){Apprise("Por favor insira um email correcto!");field.value="";}}
	function validar_newsletter()
	{
		var cor_border = "<?=$cor_border?>";
        var cor2 = "<?=$cor_alerta_border?>";
		var cor_background="#fff";
		campos_obrigatorios=new Array("m_email");
		var campos_preenchidos;
		var i=0; while(i < campos_obrigatorios.length){document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.background=cor2;i=i+1;}
		var i=0; while(i < campos_obrigatorios.length){if(document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].value==""){
			campos_preenchidos=1;
			document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.background=cor_background;
			document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.border='solid 1px';
			document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.borderColor=cor_border;
		}i=i+1;}
		if(campos_preenchidos==1){Apprise("<?=$key_camposobr[$site_lang]?>");return false;}
		return true;
	}
	</script>
	<div class="newsletter">
		<form method="post" name="newsletter_bottom" onSubmit="return validar_newsletter()" action="<?php echo seo('pages.php?page_id='.$GLOBALS['newsletter_page_id']);?>">
			<input type="hidden" name="newsletter" value="1" />
			<div class="floatleft2"><input type="text" title="<?php echo getLinguas('key_seuemail')?>" placeholder="<?php echo getLinguas('key_seuemail')?>" id="m_email" name="m_email" maxlength="254" value="<?php if(isset($_POST['m_email']))echo $_POST['m_email']?>" onChange="checkEmail(this)" /></div>
			<div class="floatleft2"><input type="image" title="Subscreva a newsletter" alt="Subscreva a newsletter" src="layout/<?php echo PROJETO?>/images/ns.png" /></div>
		</form>
	</div>

	<?php
}

function newsletterRegisterSmallForm_inmoveis($class='',$nomargin='')
{
	global $site_lang,$site_http_url,$error_message;
	global $key_newsletter_title,$key_newsletter_subtitle,$key_newsletter_nome,$key_newsletter_email,$key_camposobr,$key_seuemail,$key_seunome;
	global $key_mensagem,$key_validacao,$key_obrigatorio,$key_friendnome,$key_friendmail,$key_recommend,$key_submeter_rec,$key_spam,$key_spam2,$key_spam3,$key_captcha1;
	global $x_nome,$x_email,$x_mensagem,$code;
	global $x_nome2,$x_email2;
	?>

	<script>
	function checkEmail(field){email=field.value;AtPos=email.indexOf("@");StopPos=email.lastIndexOf(".");Message="";if(email==""||AtPos==-1||StopPos==-1||(StopPos<AtPos)||(StopPos-AtPos==1)){Apprise("Por favor insira um email correcto!");field.value="";}}
	function validar_newsletter()
	{
		var cor="#f7f7f7";
		var cor2="#e83b44";
		campos_obrigatorios=new Array("m_email");
		var campos_preenchidos;
		var i=0; while(i < campos_obrigatorios.length){document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.background=cor2;i=i+1;}
		var i=0; while(i < campos_obrigatorios.length){if(document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].value==""){campos_preenchidos=1;document.forms['newsletter_bottom'].elements[campos_obrigatorios[i]].style.background=cor;}i=i+1;}
		if(campos_preenchidos==1){Apprise("<?=$key_camposobr[$site_lang]?>");return false;}
		return true;
	}
	</script>
	<div class="newsletter">
		<form method="post" name="newsletter_bottom" onSubmit="return validar_newsletter()" action="<?php echo seo('pages.php?page_id='.$GLOBALS['newsletter_page_id']);?>">
			<input type="hidden" name="newsletter" value="1" />
			<div class="inputnlprop"><input type="text" title="<?php echo getLinguas('key_seuemail')?>" placeholder="<?php echo getLinguas('key_seuemail')?>" id="m_email" name="m_email" maxlength="254" value="<?php if(isset($_POST['m_email']))echo $_POST['m_email']?>" onChange="checkEmail(this)" /></div>
			<div class="inputnlprop">
				<input type="submit" class="inputnlprop-btn" title="Subscreva a newsletter" alt="Subscreva a newsletter" value="ENVIAR" />

		</div>
		</form>
	</div>

	<?php
}

function show_sugira()
{
    global $error_message,$site_shell_url;
    global $x_nome,$x_email,$x_mensagem,$code,$key_captcha2,$key_captcha3,$key_captcha4,$key_captcha5,$key_sugira_enviook,$key_sugira_envioerro,$key_emptyfield;
    global $x_nome2,$x_email2;
    $error_message = '';
    if (!isset($_POST['sugerir'])) {
        show_sugiraForm();
    } else {
        $error = 0;
        if (!empty($_POST['m_nome'])) {
            $x_nome[2] = clean_var($_POST['m_nome']);
        } else {
            $error = 1;
            $x_nome[3] = ';background-color:#ffffe5';
            $m_password[4] = '&nbsp;<strong><span>'.$key_emptyfield[$GLOBALS['site_lang']].'</span></strong>';
        }
        if (!empty($_POST['m_email'])) {
            $x_email[2] = clean_var($_POST['m_email']);
            if (!validEmail($x_email[2])) {
                $error = 1;
                $x_email[3] = ';background-color:#ffffe5';
                $x_email[4] = '&nbsp;<strong><span>'.$key_captcha3[$GLOBALS['site_lang']].'</span></strong>';
            }
        } else {
            $error = 1;
            $x_email[3] = ';background-color:#ffffe5';
            $x_email[4] = '&nbsp;<strong><span>'.$key_emptyfield[$GLOBALS['site_lang']].'</span></strong>';
        }
        if (!empty($_POST['m_nome2'])) {
            $x_nome2[2] = clean_var($_POST['m_nome2']);
        } else {
            $error = 1;
            $x_nome2[3] = ';background-color:#ffffe5';
        }
        if (!empty($_POST['m_email2'])) {
            $x_email2[2] = clean_var($_POST['m_email2']);
            if (!validEmail($x_email2[2])) {
                $error = 1;
                $x_email2[3] = ';background-color:#ffffe5';
                $x_email2[4] = '&nbsp;<strong><span>'.$key_captcha3[$GLOBALS['site_lang']].'</span></strong>';
            }
        } else {
            $error = 1;
            $x_email2[3] = ';background-color:#ffffe5';
        }
        if (!empty($_POST['mensagem'])) {
            $x_mensagem[2] = clean_var($_POST['mensagem']);
            if (function_exists('htmlspecialchars')) $x_mensagem[2] = htmlspecialchars($x_mensagem[2],ENT_QUOTES);
        }
        if (empty($_POST['captcha_code'])) {
            $error = 1;
            $code[3] = ';background-color:#ffffe5';
            $code[4] = '&nbsp;<strong><span>'.$key_captcha4[$GLOBALS['site_lang']].'</span></strong>';
        } else {
            include_once $site_shell_url.'external_api/captcha/contact-securimage.php';
            $securimage = new Securimage();
            if ($securimage->check(strtolower($_POST['captcha_code'])) == false) {
                $error = 1;
                $code[3] = ';background-color:#ffffe5';
                $code[4] = '&nbsp;<strong><span>'.$key_captcha5[$GLOBALS['site_lang']].'</span></strong>';
            }
        }
        if ($error == 1) {
            $error_message = '<span style="">'.$key_captcha2[$GLOBALS['site_lang']].'</span>';
            show_sugiraForm();
        } else {
            $body = array
            (
                '[nome]' => $x_nome[2],
                '[nome2]' => $x_nome2[2],
                '[mensagem]' => $x_mensagem[2],
                '[url]' => $GLOBALS['site_http_url']
            );
            $message = template_email($GLOBALS['template_sugestaosite'],$body);
            if (send_email($x_email2[2],$message['subject'],$message['body'])) {
                echo show_alerta2($key_sugira_enviook[$GLOBALS['site_lang']]);
            } else {
                echo show_alerta2($key_sugira_envioerro[$GLOBALS['site_lang']]);
            }

        }
    }
}

function show_sugiraForm()
{
    global $site_lang;
    global $key_sugira_subtitulo,$key_sugira_oseunome,$key_sugira_oseuemail,$key_sugira_mensagem,$key_sugira_nomeamigo,$key_sugira_emailamigo,$key_camposobr;
    global $x_nome,$x_email,$x_mensagem,$code;
    global $x_nome2,$x_email2;



    ?>
    <script>
        function checkEmail(field) {
            var email = field.value;
            var AtPos = email.indexOf("@");
            var StopPos = email.lastIndexOf(".");
            if (email == "" || AtPos == -1 || StopPos == -1 || (StopPos < AtPos) || (StopPos - AtPos == 1)) {
                new Apprise("Por favor insira um email correcto!");
                field.value = "";
            }
        }
        function validar_sugira() {
            var cor = "#ffffe5";
            var cor2 = "#FFFFFF";
            var campos_obrigatorios = ["m_nome","m_email","m_nome2","m_email2","captcha_code","mensagem"];
            var campos_preenchidos;
            var obj;
            for (obj in campos_obrigatorios) {
                document.forms['registo'].elements[campos_obrigatorios[obj]].style.background = cor2;
            }
            for (obj in campos_obrigatorios) {
                if (document.forms['registo'].elements[campos_obrigatorios[obj]].value == "") {
                    campos_preenchidos = 1;
                    document.forms['registo'].elements[campos_obrigatorios[obj]].style.background = cor;
                }
            }
            if (campos_preenchidos == 1) {
                new Apprise("<?=$key_camposobr[$GLOBALS['site_lang']]?>");
                return false;
            }
            return true;
        }
    </script>

<div class="row">
	<? //echo '<div class="col-md-4"></div>'; ?>
	<? //echo '<div class="col-md-4">'; ?>
		<br>
		<? //echo '<h1 class="text-center" style="width: 100%;">Sugerir o nosso Website</h1>';?>
		<div class="pag_pub_title">Sugerir o noso <strong>Website</strong></div>
		<br>
		<hr>
		<form id="form_sugerir" method="post" name="registo" onSubmit="return validar_sugira()">
			<input type="hidden" name="sugerir" value="1">
			<?php/* <div class="titulo_pagina"><?=$key_sugira_titulo[$site_lang]; ?></div><br> */?>
			<div class="col-xs-12 col-md-6"><div class="sugerir_text" style="font-size:16px;line-height: 24px;color:#6E6E6E;width: max-content;margin: auto;"><?=$key_sugira_subtitulo[$GLOBALS['site_lang']]; ?></div></div>
			<div class="col-xs-12 col-md-6">
				<div class="row">
					<?php if ($GLOBALS['error_message']) { ?>
						<div class="row" style="width: 100%;">
							<div class="form-group">
								<?php show_alerta($GLOBALS['error_message'],'erro'); ?>
							</div>
						</div>
					<?php } ?>
				
					<div class="row" style="width: 100%;">
						<div class="col-md-6">
							<div class="form-group">
								<?=$key_sugira_oseunome[$GLOBALS['site_lang']] ?>:*
								<br>
								<input type="text" autocomplete="off" title="<?=$key_sugira_oseunome[$GLOBALS['site_lang']] ?>" name="m_nome" maxlength="255" value="<?php echo htmlentities($x_nome[2]) ?>" class="inputregisto" style="width: 100%;"/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?=$key_sugira_oseuemail[$GLOBALS['site_lang']] ?>:*
								<br>
								<input type="text" autocomplete="off" title="<?=$key_sugira_oseuemail[$GLOBALS['site_lang']] ?>" name="m_email" maxlength="255" value="<?php echo htmlentities($x_email[2]) ?>" class="inputregisto" style="width: 100%;"/>
							</div>
						</div>
					</div>

					<div class="row" style="width: 100%;margin-left: 0;margin-right: 10px;">
						<div class="form-group" style="width: 100%;">
							<?=$key_sugira_mensagem[$GLOBALS['site_lang']] ?>:*
							<br>
							<textarea title="<?=$key_sugira_mensagem[$GLOBALS['site_lang']] ?>" class="inputregisto" name="mensagem" style="width:100%;resize:none; height:80px<?=$x_mensagem[3] ?>"><?=$x_mensagem[2] ?></textarea>
						</div>
					</div>

					<div class="row" style="width: 100%;">
						<div class="col-md-6">
							<div class="form-group">
								<?=$key_sugira_nomeamigo[$GLOBALS['site_lang']] ?>:*
								<br>
								<input type="text" autocomplete="off" title="<?=$key_sugira_nomeamigo[$GLOBALS['site_lang']] ?>" name="m_nome2" maxlength="255" value="<?php echo htmlentities($x_nome2[2]) ?>" class="inputregisto" style="width: 100%;"/>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?=$key_sugira_emailamigo[$GLOBALS['site_lang']] ?>:*
								<br>
								<input type="text" autocomplete="off" title="<?=$key_sugira_emailamigo[$GLOBALS['site_lang']] ?>" name="m_email2" maxlength="255" value="<?php echo htmlentities($x_email2[2]) ?>" class="inputregisto" style="width: 100%;"/>
							</div>
						</div>
					</div>

					<div class="row" style="width: 100%;">
						<div class="col-md-6">
							<div class="form-group">
								<?=$GLOBALS['key_spam'][$site_lang] ?>*
								<br>
								<img id="captcha" src="external_api/captcha/contact-securimage_show.php" alt="CAPTCHA Image"/><br/><br/><a href="javascript:;" class="HvRm" style="font-size:11px;color: #6E6E6E;text-decoration: none;" onClick="document.getElementById('captcha').src='external_api/captcha/contact-securimage_show.php?' + Math.random(); return false"><?=$GLOBALS['key_captcha1'][$site_lang] ?></a>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?=$GLOBALS['key_spam3'][$site_lang] ?>*
								<br>
								<input autocomplete="off" type="text" class="inputregisto" title="" name="captcha_code" maxlength="255" value="" style="width: 100%;"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<input type="submit" class="button3" value="Sugerir">
						</div>
					</div>
				</div>
				<br><br><br><br>
			</div>
			
		</form>
	</div>
	<? //echo '<div class="col-md-4"></div>'; ?>
</div>
   

    <?php
}

function drawRedirectPage($mensagem = '',$referer = '',$tempo = '20000',$estado = '')
{

    global $site_http_url,$site_https_url;

    // if ($referer == '') $referer = str_replace('https://','http://',$site_http_url);

    return '<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style>
				body{font-family:Arial,sans-serif;font-size:14px}
				.wrapper{max-width:800px;padding:20px 0;margin:50px auto;-webkit-box-shadow:4px 4px 10px rgba(50,50,50,0.75);-moz-box-shadow:4px 4px 10px rgba(50,50,50,0.75);box-shadow:4px 4px 10px rgba(50,50,50,0.75)}
				.logo,.content,.redirect{text-align:center;padding:20px 0}
				.redirect a{color:#000;}
			</style>
			<script>setTimeout(function(){window.location=\''.$referer.'\';},'.$tempo.');</script>
		</head>
		<body>
			<div class="wrapper">
				<div class="logo"><img style="max-width:300px" src="'.$site_http_url.'layout/'.PROJETO.'/logos/logo_top.png" alt="" /></div>
				<div class="content">'.$mensagem.'</div>
				<div class="redirect">'.str_replace('[site_path]',$site_http_url,getLinguas('key_website_redirect')). '</div>
			</div>
		</body>
	</html>';
}

function showLandingPage()
{
    global $site_title;

    return '<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>'.SITE_TITLE.'</title>
		<meta NAME="robots" CONTENT="all" />
		<meta name="revisit-after" content="1 day">
		<meta name="dcterms.audience" content="Global" />
		<meta name="googlebot" content="noodp">
		<meta name="rating" content="general">
		<meta name="viewport" content="width=1000,user-scalable=1" />
		<style>
		html{height:100%}
		body{background:'.(defined('MODO_OFFLINE_BG') && MODO_OFFLINE_BG != '' ? MODO_OFFLINE_BG : '#FFF').';margin:0;padding:0;min-height:100%;font-size:20px;font-family:Georgia;color:#222;}
		.main{position:absolute;display:table-cell;vertical-align:middle;width:1024px;text-align:center;height:200px;top:50%;margin-top:-100px;left:50%;margin-left:-512px;}
		</style>
	</head>
	<body>
		<div class="main">
			<img src="'.(defined('LOGOTIPO_PRINCIPAL') && LOGOTIPO_PRINCIPAL != '' ? LOGOTIPO_PRINCIPAL : 'layout/'.PROJETO.'/logos/logo_top.png').'" /><br/><br/><span style="color:'.color_inverse((defined('MODO_OFFLINE_BG') && MODO_OFFLINE_BG != '' ? MODO_OFFLINE_BG : '#FFF')).'">'.(defined('MODO_OFFLINE_BODY') && MODO_OFFLINE_BODY != '' ? MODO_OFFLINE_BODY : 'O site encontra-se em manutenção. Voltamos dentro de momentos.').'
		</div>
	</body>
	</html>';
}

function showLandingPage404()
{
    global $site_title;

    return '<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>'.SITE_TITLE.'</title>
		<meta NAME="robots" CONTENT="all" />
		<meta name="revisit-after" content="1 day">
		<meta name="dcterms.audience" content="Global" />
		<meta name="googlebot" content="noodp">
		<meta name="rating" content="general">
		<meta name="viewport" content="width=1000,user-scalable=1" />
		<style>
		html{height:100%}
		body{background:'.(defined('MODO_OFFLINE_BG') && MODO_OFFLINE_BG != '' ? MODO_OFFLINE_BG : '#FFF').';margin:0;padding:0;min-height:100%;font-size:20px;font-family:Georgia;color:#222;}
		.main{position:absolute;display:table-cell;vertical-align:middle;width:1024px;text-align:center;height:200px;top:50%;margin-top:-100px;left:50%;margin-left:-512px;}
		</style>
	</head>
	<body>
		<div class="main">
			<img src="'.(defined('LOGOTIPO_PRINCIPAL') && LOGOTIPO_PRINCIPAL != '' ? LOGOTIPO_PRINCIPAL : 'layout/'.PROJETO.'/logos/logo_top.png').'" /><br/><br/><span style="color:'.color_inverse((defined('MODO_OFFLINE_BG') && MODO_OFFLINE_BG != '' ? MODO_OFFLINE_BG : '#FFF')).'">404 Page Not Found
		</div>
	</body>
	</html>';
}

function templateimages($ID_PAI='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$mpath = $GLOBALS['site_http_url'].'files/templates/';

	$table = 'templates_imgs';
	$rs = new Dataobj;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

	$sql = "SELECT * FROM ".$table." WHERE ID_PAI=".$ID_PAI;
	$rs->db_open($db_link,$DB_name,$sql);
	$str = '';
	$str .= '<div class="template-images">';

	while(!$rs->EOF())
	{
		$rs->db_data['ID_PAI'];
		$str .= '<div class="template-images-inner">';
		$str .= '<div class="apagarimgt" id="apagarimgt'.$rs->db_data['ID'].'" ><span onclick="apagaimgtemp('.$rs->db_data['ID'].')">&#x2716;</span></div>';
		$str .= '<div class="img-wrap">';
		$str .= '<div class="img-content">';
		$str .= '<a href ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext'].'" class="preto fancybox_img">';
		$str .= '<img src ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'e.'.$rs->db_data['imge_ext'].'" style="width: 100%;">';
		$str .= '</a>';
		$str .= '</div>';
		$str .= '<div class="img-content" style="transform: translateY(-12px);">';
		$str .= '<input type="color" id="color'.$rs->db_data['ID'].'e" onchange="changedata(e,this)" value="'.$rs->db_data['bgcolore'].'">';
		$str .= '<input type="text" id="link'.$rs->db_data['ID'].'e" onchange="changedata(e,this)" value="'.$rs->db_data['link_imge_ext'].'">';
		$str .= '<a href="listcontent.php?field=link'.$rs->db_data['ID'].'e" class="preto fancybox2" style="font-size:16px;">&#x1F517;</a>';


		$str .= '</div>';
		$str .= '&nbsp;&nbsp;<span class="savelinkandcor"  onclick="savelinkandcor('.$rs->db_data['ID'].',\'e\')">&#x1F4BE;</span><span style="font-weight:bold;color:#ff4444;font-size:16px;" id="smsg'.$rs->db_data['ID'].'e"></span>';

		$str .= '</div>';
		$str .= '<div class="img-wrap">';
		$str .= '<div class="img-content" >';
		$str .= '<a href ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'d.'.$rs->db_data['imgd_ext'].'" class="preto fancybox_img">';
		$str .= '<img src ="'.$mpath.$ID_PAI.'_'.$rs->db_data['ID'].'d.'.$rs->db_data['imgd_ext'].'" style="width: 100%;">';
		$str .= '</a>';
			$str .= '</div>';
			$str .= '<div class="img-content" style="transform: translateY(-12px);">';
			$str .= '<input type="color" id="color'.$rs->db_data['ID'].'d" onchange="changedata(\'d\',this)" value="'.$rs->db_data['bgcolord'].'">';
		$str .= '<input type="text" id="link'.$rs->db_data['ID'].'d" onchange="changedata(\'d\',this)" value="'.$rs->db_data['link_imgd_ext'].'">';
		$str .= '<a href="listcontent.php?field=link'.$rs->db_data['ID'].'d" class="preto fancybox2" style="font-size:16px;">&#x1F517;</a>';

			$str .= '</div>';
			$str .= '&nbsp;&nbsp;<span class="savelinkandcor" onclick="savelinkandcor('.$rs->db_data['ID'].',\'d\')">&#x1F4BE;</span><span style="font-weight:bold;color:#ff4444;font-size:16px;" id="smsg'.$rs->db_data['ID'].'d"></span>';
			$str .= '</div>';
			$str .= '</div>';



		$rs->db_next();
	}
	$rs->db_close();

	$str .= '</div>';
	return $str;

}

function lateralbackground()
{
	if(isset($GLOBALS['lateralbackground']) && $GLOBALS['lateralbackground']==1)
	{
	?>
	<script>
		var id = '<?=$_GET['page_id']?>';
		var url = 'ajax/lateralbg.php?id='+id;
		$.getJSON(url,success=function(json)
		{
			$('body').append(json['dive']);
			$('body').append(json['divd']);
		});
	</script><?php
	}
}

function easypaydata($tipo='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$output = array();
	$data = array();

	$sql = 'SELECT * FROM metodos_pagamento WHERE Tipo='.$tipo;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$data['sandbox']['ep_account_id_sandbox'] = $rs->db_data['ep_account_id_sandbox'];
		$data['sandbox']['ep_api_key_sandbox'] = $rs->db_data['ep_api_key_sandbox'];
		$data['sandbox']['ep_user_sandbox'] = $rs->db_data['ep_user_sandbox'];
		$data['sandbox']['ep_cin_sandbox'] = $rs->db_data['ep_cin_sandbox'];
		$data['sandbox']['ep_entity_sandbox'] = $rs->db_data['ep_entity_sandbox'];
		$data['sandbox']['ep_url_sandbox'] = $rs->db_data['ep_url_sandbox'];

		$data['production']['ep_account_id_production'] = $rs->db_data['ep_account_id_production'];
		$data['production']['ep_api_key_production'] = $rs->db_data['ep_api_key_production'];
		$data['production']['ep_user_production'] = $rs->db_data['ep_user_production'];
		$data['production']['ep_cin_production'] = $rs->db_data['ep_cin_production'];
		$data['production']['ep_entity_production'] = $rs->db_data['ep_entity_production'];
		$data['production']['ep_url_production'] = $rs->db_data['ep_url_production'];

		$data['ep_sandbox_production'] = $rs->db_data['ep_sandbox_production'];
	}
	$rs->db_close(1);
	$output = $data[$data['ep_sandbox_production']];
	return $output;
}

function show_page_name()
{
	echo $GLOBALS['page_name'];
}
function show_page_name2()
{
	echo $GLOBALS['sub_page_name'];
}
function show_page_name_first($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$page_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	if(!$id)$id=$page_id;
	$sql = 'SELECT ID,ID_ORG FROM pages WHERE ID='.$id;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$ID_ORG=$rs->db_data['ID_ORG'];
		$rs->db_close();
		if($ID_ORG>0) show_page_name_first($ID_ORG);
		else
		{
			$sql = 'SELECT Cabecalho FROM pages_lang WHERE ID_PAI='.$id.' AND Lang="'.$site_lang.'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())$page_name=$rs->db_data['Cabecalho'];
			$rs->db_close();
			echo $page_name;
		}
	}
	$rs->db_close();
}
function show_page_name_second($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$page_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	if(!$id)$id=$page_id;
	$sql = 'SELECT ID,ID_ORG FROM pages WHERE ID='.$id;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$ID_ORG=$rs->db_data['ID_ORG'];
		$rs->db_close();
		if($ID_ORG>0) show_page_name_first($ID_ORG);
		else
		{
			$sql = 'SELECT Cabecalho2 FROM pages_lang WHERE ID_PAI='.$id.' AND Lang="'.$site_lang.'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())$page_name=$rs->db_data['Cabecalho2'];
			$rs->db_close();
			echo $page_name;
		}
	}
	$rs->db_close();
}


function faqs($IDb=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$page_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT ID,Template,Tipo,Data_modify,sim,img_ext,img_ext2,PageEnter,DefaultHome2,bootstrap,bootstrap_p FROM pages WHERE visible=1 and ID_ORG="'.$IDb.'" Order by Ordem');
	if(!$rs->EOF())
	{
		$total = $rs->db_rows;
		echo '<div class="mx-auto" style="width:100%">';
		echo '<div class="accordion" id="acoordeao'.$IDb.'">';

		$i=0;
		while(!$rs->EOF())
		{
		  $page_id=$rs->db_data['ID'];

		  $rs2->db_open($db_link,$GLOBALS['DB_name'],'SELECT * FROM pages_lang WHERE ID_PAI='.$page_id.' AND Lang="'.$GLOBALS['site_lang'].'"');
		  if(!$rs2->EOF())
		  {
		    $title=\strip_html_tagsc(stripslashes($rs2->db_data['Cabecalho']));
		    $title2=\strip_html_tagsc(stripslashes($rs2->db_data['Cabecalho2']));
		    $texto=\strip_html_tagsc(stripslashes($rs2->db_data['Texto']));
		    $texto2=\strip_html_tagsc(stripslashes($rs2->db_data['Texto2']));
		    $I1 = $rs2->db_data['img_ext'];
		    if($rs->db_data['img_ext']) $imagem='files/pages/'.$rs->db_data['ID'].'_1.'.$rs->db_data['img_ext'];

		    $i++;?>
		    <div class="card">
		        <div class="card-header" id="heading<?=$i?>">
		            <h5 class="mb-0">
		               <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?=$i?>" aria-expanded="true" aria-controls="collapse<?=$i?>"><?=$title?></button>
		            </h5>
		        </div>
		        <div id="collapse<?=$i?>" class="collapse" aria-labelledby="heading<?=$i?>" data-parent="#acoordeao<?=$IDb?>">
		            <div class="card-body"><? put_content($texto)?></div>
		        </div>
		    </div><?
		   }
		   $rs->db_next();
		}
		$rs->db_close();

		echo '</div>';
		echo '</div>';
	}
}