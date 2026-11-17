<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
    exit();
}
function show_linguas($isMobile = 0)
{
	$response = '';
	$pageURL = get_uri();

	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new Dataobj;
	$sql = 'SELECT l.Sigla,l.Cabecalho FROM linguas l WHERE l.Visivel=1 ORDER BY l.Ordem ASC';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$t = 0;
	if (!$rs->EOF())
	{
		$linguas = '';
		while (!$rs->EOF())
		{
			$sigla = $rs->db_data['Sigla'];
			$cabecalho = $rs->db_data['Cabecalho'];
			$pageURL = str_replace('?site_lingua=' . $sigla, '', $pageURL);
			$pageURL = str_replace('&site_lingua=' . $sigla, '', $pageURL);

			if (substr_count($pageURL, '?') > 0)
			{
				$op = '&';
			}
			else
			{
				$op = '?';
			}

			$URL = $pageURL . $op . 'site_lingua=' . $sigla;

			$attrs = ' class="btn" ';
			if ($GLOBALS['site_lang'] == $sigla)
			{
				$attrs = ' class="btn active" ';
			}
			if ($isMobile == 1)
			{
				$linguas .= sprintf('<li><a href="%s">(%s) %s</a></li>', $URL, mb_strtoupper($sigla,'UTF-8'), mb_strtoupper($cabecalho,'UTF-8'));
			}
			else
			{
				$linguas .= sprintf('<li><a href="%s" %s><div>%s</div></a></li>', $URL, $attrs, mb_strtoupper($sigla,'UTF-8'));
			}

			$t++;
			$rs->db_next();
		}

		if ($t > 1)
		{
			//$label = $GLOBALS['txt_linguas_label'][$GLOBALS['site_lang']];

			if ($isMobile == 1)
			{
				$response .= '<ul id="lang-menu" class="collapse language-collapse slide custom">';
				$response .= sprintf('%s', $linguas);
				$response .= '</ul>';
			}
			else
			{
				$response .= sprintf('<ul class="menu-langs">%s</ul>', $linguas);
			}
		}
	}
	$rs->db_close();

	return $response;
}

function getLinguas($key='', $lingua='')
{
	//if(isset($_SESSION['ss_admin']) && isset($_GET['map_vars']) && $_GET['map_vars'] == 1 && $_SESSION['ss_admin'] == 1)
	if(isset($_SESSION['ss_admin']) && $GLOBALS['online_edit'] == 1 && $_SESSION['ss_admin'] == 1)
	{
		//if($key && isset($GLOBALS[$key][$GLOBALS['site_lang']])) return '@@@@@';


		if($key && isset($GLOBALS[$key][$GLOBALS['site_lang']])) return '<a class="edit" href="admin/sa_linguas/list.php?p=1&DESIGNACAO='.$key.'" target="_blank" title="'.$key.'">E2</a><font class="key">'.str_replace("\n", '<br>',stripslashes($GLOBALS[$key][(isset($lingua) && $lingua!='' ? $lingua : $GLOBALS['site_lang'])])).'</font>';
		else
		{
			if(stream_resolve_include_path(dirname( __FILE__ ).'/../files/tmp/linguas.php'))include dirname( __FILE__ ).'/../files/tmp/linguas.php';
			//Gravar em bd
			return '<a class="edit" href="admin/sa_linguas/list.php?p=1&DESIGNACAO='.$key.'" target="_blank" title="'.$key.'">E1</a><font class="key2">'.str_replace("\n", '<br>',stripslashes($GLOBALS[$key][(isset($lingua) && $lingua!='' ? $lingua : $GLOBALS['site_lang'])])).'</font>';

		}
	}
	else
	{
		if($key && isset($GLOBALS[$key][$GLOBALS['site_lang']]))
		{
			if(stream_resolve_include_path(dirname( __FILE__ ).'/../files/tmp/linguas.php'))include dirname( __FILE__ ).'/../files/tmp/linguas.php';
			return str_replace("\n", '<br>',stripslashes($GLOBALS[$key][(isset($lingua) && $lingua!='' ? $lingua : $GLOBALS['site_lang'])]));
		}
		else
		{
			if(stream_resolve_include_path(dirname( __FILE__ ).'/../files/tmp/linguas.php'))include dirname( __FILE__ ).'/../files/tmp/linguas.php';
			//Gravar em bd

			return str_replace("\n", '<br>',stripslashes($GLOBALS[$key][(isset($lingua) && $lingua!='' ? $lingua : $GLOBALS['site_lang'])]));

		}
	}
}
function getGlobals($key='')
{
	global $online_edit;
	$admin=$online_edit;

	if(isset($_GET['map_vars']) && $_GET['map_vars'] == 1)
	{
		return $key;
	}
	else
	{
		if($key && isset($GLOBALS[$key]))
		{
			if($admin)return '<a class="edit" href="admin/sa_linguas/list.php?p=1&DESIGNACAO='.$key.'" target="_blank">'.$GLOBALS[$key].'</a>';
			else return $GLOBALS[$key];
		}
	}
}


if (!isset($m_nome)) {
    $m_nome = '';
}
if (!isset($m_nome2)) {
    $m_nome2 = '';
}
if (!isset($Nome_Site)) {
    $Nome_Site = '';
}
if (!isset($m_nome3)) {
    $m_nome3 = '';
}
if (!isset($url)) {
    $url = '';
}
if (!isset($Nome_Siter)) {
    $Nome_Siter = '';
}
if (!isset($form_recipient)) {
    $form_recipient = '';
}

if(stream_resolve_include_path(dirname( __FILE__ ).'/../files/linguas/linguas.php')) require_once dirname( __FILE__ ).'/../files/linguas/linguas.php';
else
{
$key_btn_editar['en'] = 'Edit';
$key_btn_editar['fr'] = '';
$key_btn_editar['pt'] = 'Editar';
$key_btn_elim['en'] = 'Delete?';
$key_btn_elim['fr'] = '';
$key_btn_elim['pt'] = 'Eliminar?';
$key_btn_salvar_alteracoes['en'] = 'SAVE CHANGES';
$key_btn_salvar_alteracoes['fr'] = '';
$key_btn_salvar_alteracoes['pt'] = 'SALVAR ALTERAÇÕES';
$key_selecione['pt'] = 'Selecione';
$key_selecione['en'] = 'Select';
$key_selecione['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_selecione['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_ordernar['pt'] = 'Ordenar por';
$key_ordernar['en'] = 'Order by';
$key_ordernar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_ordernar['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_exibir['pt'] = 'exibir';
$key_exibir['en'] = 'show';
$key_exibir['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_exibir['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_resultados['pt'] = 'Resultados';
$key_resultados['en'] = 'Results';
$key_resultados['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_resultados['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_vermais['pt'] = 'ver mais';
$key_vermais['en'] = 'see more';
$key_vermais['fr'] = 'selecione'; //TODO: Alterar o texto para a lingua;
$key_vermais['es'] = 'selecione'; //TODO: Alterar o texto para a lingua;
$key_comprar['pt'] = 'Comprar';
$key_comprar['en'] = 'Buy';
$key_comprar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_comprar['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_adicionar['pt'] = 'Adicionar';
$key_adicionar['en'] = 'Add to cart';
$key_adicionar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_adicionar['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_inicio['pt'] = 'Homepage';
$key_inicio['en'] = 'Homepage';
$key_inicio['fr'] = 'Homepage'; //TODO: Alterar o texto para a lingua;
$key_inicio['es'] = 'Homepage'; //TODO: Alterar o texto para a lingua;
$key_novidades['pt'] = 'Novos Produtos';
$key_novidades['en'] = 'Recent Products';
$key_novidades['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_novidades['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_destaques['pt'] = 'Destaques';
$key_destaques['en'] = 'Highlights';
$key_destaques['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_destaques['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_modelo['pt'] = 'modelo';
$key_modelo['en'] = 'model';
$key_modelo['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_modelo['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_emstock['pt'] = 'em stock';
$key_emstock['en'] = 'in stock';
$key_emstock['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_emstock['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_opcoes['pt'] = 'Opções';
$key_opcoes['en'] = 'Options';
$key_opcoes['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_opcoes['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_bem_vindo_auntentiquese['pt'] = 'Bem vindo, autentique-se';
$key_bem_vindo_auntentiquese['en'] = 'Welcome, Log in';
$key_bem_vindo_auntentiquese['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_bem_vindo_auntentiquese['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_novo['pt'] = 'NOVOS CLIENTES';
$key_cliente_novo['en'] = 'New Client';
$key_cliente_novo['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_novo['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_registado['pt'] = 'CLIENTES REGISTADOS';
$key_cliente_registado['en'] = 'Registed Client';
$key_cliente_registado['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_registado['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_novo_texto['pt'] = 'Ao registar-se como cliente no nosso site poderá efetuar as suas compras de uma forma rápida e cómoda, para além de consultar o estado e histórico de todas as suas encomendas.';
$key_cliente_novo_texto['en'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_novo_texto['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_novo_texto['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_auten_dados['pt'] = 'Se já tem uma conta de utilizador, poderá iniciar a sessão';
$key_cliente_auten_dados['en'] = '<p>Login with your credencials.</p>';
$key_cliente_auten_dados['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_auten_dados['es'] = 'Selecione';
$key_cliente_camposobrigatorios['pt'] = 'Campos obrigatórios';
$key_cliente_camposobrigatorios['en'] = 'Mandatory fields';
$key_cliente_camposobrigatorios['fr'] = '';
$key_cliente_camposobrigatorios['es'] = '';
$key_cliente_campoobrigatorio['pt'] = 'Campo obrigatório';
$key_cliente_campoobrigatorio['en'] = 'Mandatory field';
$key_cliente_campoobrigatorio['fr'] = '';
$key_cliente_campoobrigatorio['es'] = '';

$key_cliente_perder_pass['pt'] = '<p>Se perdeu a sua Password, introduza o seu E-Mail e preencha o código de confirmação para que possamos enviar-lhe uma nova password.</p>';
$key_cliente_perder_pass['en'] = '<p>If you have lost your password, please enter your E-Mail and fill the confirmation code so we can send you a new passoword.</p>';
$key_cliente_perder_pass['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_perder_pass['es'] = 'Selecione';
$key_cliente_perder_pass_conf['pt'] = 'Código Confirmação';
$key_cliente_perder_pass_conf['en'] = 'Confirmation Code';
$key_cliente_perder_pass_conf['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_perder_pass_conf['es'] = 'Selecione';
$key_captcha1['pt'] = 'Altera';
$key_captcha1['en'] = 'Change';
$key_captcha1['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_captcha1['es'] = 'Selecione';
$key_continuar['pt'] = 'Continuar';
$key_continuar['en'] = 'Continue';
$key_continuar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_continuar['es'] = 'Selecione';
$key_voltar['pt'] = 'Voltar';
$key_voltar['en'] = 'Back';
$key_voltar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_voltar['es'] = 'Selecione';
$key_conta_pessoal['pt'] = 'Conta Pessoal';
$key_conta_pessoal['en'] = 'Personal Account';
$key_conta_pessoal['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_conta_pessoal['es'] = 'Selecione';
$key_nota_registo['pt'] = '<p><font color="#FF0000"><small><strong>NOTA:</strong></small></font> Se já possui uma Conta Pessoal, por favor introduza as suas credenciais <a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '"><u>aqui</u></a>.</p>';
$key_nota_registo['en'] = '<p><font color="#FF0000"><small><strong>NOTE:</strong></small></font> If you already have a Personal Account, please use your credencials <a href="' . seo('pages.php?page_id=' . $GLOBALS['registo_page_id']) . '"><u>here</u></a>.</p>';
$key_nota_registo['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_nota_registo['es'] = 'Selecione';
$key_registar['pt'] = 'Registar';
$key_registar['en'] = 'Register';
$key_registar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_registar['es'] = 'Selecione';
$key_carrinho1['pt'] = 'Carrinho';
$key_carrinho1['en'] = 'Cart';
$key_carrinho1['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_carrinho1['es'] = 'Selecione';
$key_encomendas['pt'] = 'Encomendas';
$key_encomendas['en'] = 'Orders';
$key_encomendas['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_encomendas['es'] = 'Selecione';
$key_rma['pt'] = 'Repair Tracking';
$key_rma['en'] = 'Repair Tracking';
$key_rma['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_rma['es'] = 'Selecione';
$key_imagem1['pt'] = 'Imagem';
$key_imagem1['en'] = 'Image';
$key_imagem1['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_imagem1['es'] = 'Selecione';
$key_nome['pt'] = 'Nome';
$key_nome['en'] = 'Name';
$key_nome['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_nome['es'] = 'Selecione';
$key_apelido['pt'] = 'Apelido';
$key_apelido['en'] = 'Surname';
$key_apelido['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_apelido['es'] = 'Selecione';
$key_quantidade['pt'] = 'Quantidade';
$key_quantidade['en'] = 'Quantity';
$key_quantidade['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_quantidade['es'] = 'Selecione';
$key_preco['pt'] = 'Preço';
$key_preco['en'] = 'Price';
$key_preco['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_preco['es'] = 'Selecione';
$key_atualizar['pt'] = 'Atualizar';
$key_atualizar['en'] = 'Refresh';
$key_atualizar['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_atualizar['es'] = 'Selecione';
$key_finalizar_encomenda['pt'] = 'FINALIZAR COMPRA';
$key_finalizar_encomenda['en'] = '';
$key_finalizar_encomenda['fr'] = ''; //TODO: Alterar o texto para a lingua;
$key_finalizar_encomenda['es'] = '';
$key_finalizar_encomenda2['pt'] = 'Finalizar Encomenda';
$key_finalizar_encomenda2['en'] = 'Checkout';
$key_finalizar_encomenda2['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_finalizar_encomenda2['es'] = 'Selecione';
$key_apelido['pt'] = "Apelido";
$key_apelido['en'] = "Surname";
$key_apelido['fr'] = "Apelido";
$key_apelido['es'] = "Apelido";
$key_empresa['pt'] = "Designação";
$key_empresa['en'] = "Name";
$key_empresa['fr'] = "";
$key_empresa['es'] = "";
$key_morada['pt'] = "Morada";
$key_morada['en'] = "Adress";
$key_morada['fr'] = "Adresse";
$key_morada['es'] = "Direcci&oacute;n";
$key_morada['de'] = "Adresse";
$key_codpos['pt'] = "C&oacute;digo Postal";
$key_codpos['en'] = "ZIP CODE";
$key_codpos['fr'] = "Code postal";
$key_codpos['es'] = "C&oacute;digo Postal";
$key_localidade['pt'] = "Localidade";
$key_localidade['en'] = "Locality";
$key_localidade['fr'] = "Localit&eacute;";
$key_localidade['es'] = "Sitio";
$key_tel['pt'] = "Telefone";
$key_tel['en'] = "Phone";
$key_tel['fr'] = "T&eacute;l&eacute;phone";
$key_tel['es'] = "Tel&eacute;fono";
$key_tel['de'] = "Telefon";
$key_fax['pt'] = "Fax";
$key_fax['en'] = "Fax";
$key_fax['fr'] = "Fax";
$key_fax['es'] = "Fax";
$key_fax['de'] = "Telefax";
$key_movel['pt'] = "Telem&oacute;vel";
$key_movel['en'] = "Mobile Phone";
$key_movel['fr'] = "T&eacute;l&eacute;phone portable";
$key_movel['es'] = "Tel&eacute;fono m&oacute;vil";
$key_nif['pt'] = "NIF";
$key_nif['en'] = "Identity Card n°";
$key_nif['fr'] = "";
$key_nif['es'] = "";
$key_website['pt'] = "Website";
$key_website['en'] = "Website";
$key_website['fr'] = "";
$key_website['es'] = "";
$key_doc_identidade['pt'] = "N&ordm; Contribuinte";
$key_doc_identidade['en'] = "Identity Card";
$key_doc_identidade['fr'] = "";
$key_doc_identidade['es'] = "Documento de Identidad";
$key_doc_identidade2['pt'] = "Passaporte";
$key_doc_identidade2['en'] = "Passport";
$key_doc_identidade2['fr'] = "";
$key_doc_identidade2['es'] = "Pasaporte";
$key_doc_identidade3['pt'] = "Carta de Condução";
$key_doc_identidade3['en'] = "Driving License";
$key_doc_identidade3['fr'] = "";
$key_doc_identidade3['es'] = "Licencia de Conducir";
$key_datanasc['pt'] = "Data de Nascimento";
$key_datanasc['en'] = "Date of Birth";
$key_datanasc['fr'] = "Date de naissance";
$key_datanasc['es'] = "Fecha de Nacimiento";
$key_validacao['pt'] = "C&oacute;digo Valida&ccedil;&atilde;o";
$key_validacao['en'] = "Validation Code";
$key_validacao['fr'] = "Code de Validation";
$key_validacao['es'] = "C&oacute;digo de Validaci&oacute;n";
$key_email['pt'] = "Email";
$key_email['en'] = "Email";
$key_email['fr'] = "Email";
$key_email['es'] = "Email";
$key_email["pe"] = "Email";
$key_password['pt'] = "Password";
$key_password['en'] = "Password";
$key_password['fr'] = "Password";
$key_password['es'] = "Password";
$key_password['de'] = "Passwort";
$key_repetepassword['pt'] = "Repetir Password";
$key_repetepassword['en'] = "Repeat Password";
$key_repetepassword['fr'] = "";
$key_repetepassword['es'] = "Repetir Contraseña";
$key_repetepassword['de'] = "";
$key_spam['pt'] = "Preven&ccedil;&atilde;o de spam";
$key_spam['en'] = "Spam prevention";
$key_spam['fr'] = "Protection contre spam";
$key_spam['es'] = "Prevención de spam";
$key_spam['de'] = "Spamschutz";
$key_spam2['pt'] = "Por favor insira o c&oacute;digo que est&aacute; a visualizar e clique em 'Confirmar'. Este passo adicional &eacute; necess&aacute;rio para nos ajudar a impedir o envio de mensagens de spam.";
$key_spam2['en'] = "Please enter the code shown above and click the 'Confirm' button. This additional step is required to help protect against message spam.";
$key_spam2['fr'] = "S'il vous plaît entrez le code montré ci-dessus et cliquez sur le \"Envoyer e-mail\". Cette étape supplémentaire est nécessaire pour aider à protéger contre le spam message.";
$key_spam2['es'] = "Por favor introduce el código que se muestra arriba y haga clic en el botón \"Confirmar\". Este paso adicional es necesario para ayudar a proteger contra el spam de mensajes.";
$key_spam2['de'] = "Bitte geben Sie den oben angezeigten Code ein und klicken Sie auf \"Email senden\" klicken. Dieser zusätzliche Schritt ist erforderlich, um Schutz vor Spam-Nachricht.";
$key_spam3['pt'] = "Insira o c&oacute;digo";
$key_spam3['en'] = "Enter code above";
$key_spam3['fr'] = "Entrez le code ci-dessus";
$key_spam3['es'] = "Introduzca el código de";
$key_spam3['de'] = "Geben sie oben";
$key_captcha1['pt'] = "Clique aqui se n&atilde;o consegue visualizar o c&oacute;digo";
$key_captcha1['en'] = "Click here if you cannot recognize the code.";
$key_captcha1['fr'] = "Cliquez ici si vous ne pouvez pas reconnaître le code.";
$key_captcha1['es'] = "Haga clic aquí si no puede reconocer el código.";
$key_captcha1['de'] = "Klicken sie hier, wenn sie nicht erkennen können, den Code.";
$key_captcha2['pt'] = "Por favor corrija os campos a cor.";
$key_captcha2['en'] = "Please correct/enter field(s) in color.";
$key_captcha2['fr'] = "S'il vous plaît corriger / entrer dans le champ (s) en couleur.";
$key_captcha2['es'] = "Por favor, corrija / entrar en el campo (s) en color.";
$key_captcha3['pt'] = "email inv&aacute;lido";
$key_captcha3['en'] = "invalid email";
$key_captcha3['fr'] = "mail invalide";
$key_captcha3['es'] = "email no es válida";
$key_captcha4['pt'] = "c&oacute;digo vazio";
$key_captcha4['en'] = "code empty";
$key_captcha4['fr'] = "code vide";
$key_captcha4['es'] = "código vacío";
$key_captcha5['pt'] = "c&oacute;digo inv&aacute;lido";
$key_captcha5['en'] = "incorrect code";
$key_captcha5['fr'] = "code incorrect";
$key_captcha5['es'] = "código incorrecto";
$key_rmas_estado0['pt'] = "Aguarda diagnóstico";
$key_rmas_estado0['en'] = "Awaiting diagnostic";
$key_rmas_estado0['fr'] = "Abonner à Newsletter";
$key_rmas_estado0['es'] = "Subscrever a Newsletter";
$key_rmas_estado1['pt'] = "Aguarda aceitação";
$key_rmas_estado1['en'] = "Awaiting acceptance";
$key_rmas_estado1['fr'] = "Abonner à Newsletter";
$key_rmas_estado1['es'] = "Subscrever a Newsletter";
$key_rmas_estado2['pt'] = "Aguarda reparação";
$key_rmas_estado2['en'] = "Awaiting repair";
$key_rmas_estado2['fr'] = "Abonner à Newsletter";
$key_rmas_estado2['es'] = "Subscrever a Newsletter";
$key_rmas_estado3['pt'] = "Aguarda peça";
$key_rmas_estado3['en'] = "Awaiting part";
$key_rmas_estado3['fr'] = "Abonner à Newsletter";
$key_rmas_estado3['es'] = "Subscrever a Newsletter";
$key_rmas_estado4['pt'] = "Pronto para entrega";
$key_rmas_estado4['en'] = "Ready to deliver";
$key_rmas_estado4['fr'] = "Abonner à Newsletter";
$key_rmas_estado4['es'] = "Subscrever a Newsletter";
$key_rmas_estado5['pt'] = "Concluído";
$key_rmas_estado5['en'] = "Completed";
$key_rmas_estado5['fr'] = "Abonner à Newsletter";
$key_rmas_estado5['es'] = "Subscrever a Newsletter";
$key_rmas_estado6['pt'] = "Recusado";
$key_rmas_estado6['en'] = "Refused";
$key_rmas_estado6['fr'] = "Abonner à Newsletter";
$key_rmas_estado6['es'] = "Subscrever a Newsletter";
$key_rmas_estado7['pt'] = "Aguarda peça para despiste";
$key_rmas_estado7['en'] = "Awaiting part for screening";
$key_rmas_estado7['fr'] = "Abonner à Newsletter";
$key_rmas_estado7['es'] = "Subscrever a Newsletter";

$key_subscribe_newsletter['pt'] = "Newsletter";
$key_subscribe_newsletter['en'] = "Subscribe to Newsletter";
$key_subscribe_newsletter['fr'] = "Abonner à Newsletter";
$key_subscribe_newsletter['es'] = "Subscrever a Newsletter";

$key_subscribe_newsletter2['pt'] = "Subscrever newslettter";
$key_subscribe_newsletter2['en'] = "Subscribe to Newsletter";
$key_subscribe_newsletter2['fr'] = "Abonner à Newsletter";
$key_subscribe_newsletter2['es'] = "Subscrever a Newsletter";

$key_subscribe_visitedproducts['pt'] = "Receber notificação de produtos visitados";
$key_subscribe_visitedproducts['en'] = "";
$key_subscribe_visitedproducts['fr'] = "";
$key_subscribe_visitedproducts['es'] = "";
$key_shop_msg1['pt'] = 'Agradecemos a sua preferência,<br>confirmamos o registo da sua encomenda.<br>Assim que a encomenda for processada,<br>será notificado via email.';
$key_shop_msg1['en'] = 'Thank you for your choice,<br>we confirm the registration of your order.<br> Once the order is processed<br>you will be notified by email.';
$key_shop_msg1['fr'] = 'Thank you for your choice,<br>we confirm the registration of your order.<br> Once the order is processed<br>you will be notified by email.';
$key_shop_msg1['es'] = 'Gracias por su preferencia, <br /> confirmamos el registro de su pedido. <br /> Una vez que la orden se procesa <br /> se le notificará por correo electrónico.';
$key_shop_msg1_c['pt'] = 'A sua encomenda foi cancelada.';
$key_shop_msg1_c['en'] = '';
$key_shop_msg1_c['fr'] = '';
$key_shop_msg1_c['es'] = '';
$key_shop_msg2['pt'] = 'Por favor, Escolha o método de pagamento pretendido';
$key_shop_msg2['en'] = 'Please Select your desired payment method';
$key_shop_msg2['fr'] = 'Please Select your desired payment method';
$key_shop_msg2['es'] = 'Por favor, elija la forma de pago deseada';
$key_shop_msg2_envio['pt'] = 'Por favor, Escolha o método de expedição pretendido';
$key_shop_msg2_envio['en'] = 'Please Select your desired delivery method';
$key_shop_msg2_envio['fr'] = 'Please Select your desired delivery method';
$key_shop_msg2_envio['es'] = 'Por favor, elija la forma de envio deseada';
$key_shop_msg3['pt'] = 'Confirme os dados para faturação';
$key_shop_msg3['en'] = 'Confirm the data for billing';
$key_shop_msg3['fr'] = 'Confirm the data for billing';
$key_shop_msg3['es'] = 'Confirme los datos para la facturación';
$key_shop_msg4['pt'] = 'Atualizar dados no meu perfil para posterior utilização';
$key_shop_msg4['en'] = 'Refresh data on my profile for later use';
$key_shop_msg4['fr'] = 'Refresh data on my profile for later use';
$key_shop_msg4['es'] = 'Refresh data on my profile for later use';
$key_shop_msg5['pt'] = 'Confirme os dados para entrega';
$key_shop_msg5['en'] = 'Confirm the data for delivery';
$key_shop_msg5['fr'] = 'Confirm the data for delivery';
$key_shop_msg5['es'] = 'Confirme los datos para la entrega';
// METODOS DE PAGAMENTO
$key_paypal['pt'] = "Paypal";
$key_paypal['en'] = "Paypal";
$key_paypal['fr'] = "Paypal";
$key_paypal['es'] = "Paypal";
$key_paypal_msg['pt'] = 'Pague as suas compras via Paypal';
$key_paypal_msg['en'] = 'Pay your purchases via paypal.';
$key_paypal_msg['fr'] = 'Pay your purchases via paypal.';
$key_paypal_msg['es'] = 'Pague sus compras a través de PayPal.';
$key_transferencia['pt'] = "Transferência Bancária";
$key_transferencia['en'] = "Bank Transfer";
$key_transferencia['fr'] = "";
$key_transferencia['es'] = "Transferencia bancaria";
$key_transferencia_msg['pt'] = 'Pague as suas compras via Transferência Bancária';
$key_transferencia_msg['en'] = 'Pay your purchases via Bank Transfer';
$key_transferencia_msg['fr'] = '';
$key_transferencia_msg['es'] = 'Pague sus compras a través de transferencia bancaria';
$key_dados_entr['pt'] = "Dados da Entrega";
$key_dados_entr['en'] = "Delivery Information";
$key_dados_entr['fr'] = "Renseignements sur la livraison";
$key_dados_entr['es'] = "Entrega de Informaci&oacute;n";
$key_expedicao['pt'] = "Por favor escolha o m&eacute;todo de expedi&ccedil;&atilde;o";
$key_expedicao['en'] = "Please choose the method of expedition";
$key_expedicao['fr'] = "S'il vous plaît choisir la m&eacute;thode d'exp&eacute;dition";
$key_expedicao['es'] = "Por favor, elija el m&eacute;todo de expedici&oacute;n";
$key_pagamento['pt'] = "Por favor escolha o m&eacute;todo de pagamento";
$key_pagamento['en'] = "Please choose your payment method";
$key_pagamento['fr'] = "S'il vous plaît choisissez votre m&eacute;thode de paiement";
$key_pagamento['es'] = "Por favor, elija la forma de pago";
$key_obs['pt'] = "Se desejar acrescentar algum coment&aacute;rio r nota de encomenda, pode fazê-lo aqui";
$key_obs['en'] = "If you want to add a comment to the purchase order, you can do it here";
$key_obs['fr'] = "Si vous souhaitez ajouter un commentaire r la commande, vous pouvez le faire ici";
$key_obs['es'] = "Si desea agregar un comentario a la orden de compra, puede hacerlo aqu&iacute;";
$key_send_encomenda['pt'] = "Submeter Encomenda";
$key_send_encomenda['en'] = "Submit Order";
$key_send_encomenda['fr'] = "Soumettre la commande";
$key_send_encomenda['es'] = "Enviar pedido";
$key_modify_cart['pt'] = 'Modificar Carrinho';
$key_modify_cart['en'] = 'Modify your cart';
$key_modify_cart['fr'] = '';
$key_modify_cart['es'] = 'Modificar su cesta';
$key_camposobr['pt'] = "Para prosseguir, preencha todos os campos assinalados.";
$key_camposobr['en'] = "To continue you need to fill in all marked fields";
$key_camposobr['fr'] = "Pour continuer, vous devez remplir tous les champs marqués";
$key_camposobr['es'] = "Todos los campos marcados son obligatorios!";
$key_registo3b['pt'] = "Os seus dados foram corretamente alterados. <br/> Obrigado.";
$key_registo3b['en'] = "Your data was correctly changed, thank you.";
$key_registo3b['fr'] = "Vos donn&eacute;es a &eacute;t&eacute; correctement chang&eacute;, je vous remercie.";
$key_registo3b['es'] = "Sus datos se ha cambiado correctamente, gracias.";
$key_pais['pt'] = "Pa&iacute;s";
$key_pais['en'] = "Country";
$key_pais['fr'] = "Pays";
$key_pais['es'] = "Pa&iacute;s";
$key_pais['de'] = "Land";
$key_obrigatorio['pt'] = "Campos de Preenchimento Obrigatórios";
$key_obrigatorio['en'] = "Required Fields";
$key_obrigatorio['fr'] = "Champs Obligatoires";
$key_obrigatorio['es'] = "obligatorio";
$key_obrigatorio['de'] = "";
$key_folha_reparacao['pt'] = "Folha de Reparação";
$key_folha_reparacao['en'] = "Repair Sheet";
$key_folha_reparacao['fr'] = "Champs Obligatoires";
$key_folha_reparacao['es'] = "obligatorio";
$key_folha_reparacao['de'] = "";
$key_folha_reparacao_naoencontrado['pt'] = "Não foi encontrado nenhuma reparação<br>com o Tracking:";
$key_folha_reparacao_naoencontrado['en'] = "It wasn't found any repair<br>with the Tracking:";
$key_folha_reparacao_naoencontrado['fr'] = "Champs Obligatoires";
$key_folha_reparacao_naoencontrado['es'] = "obligatorio";
$key_folha_reparacao_naoencontrado['de'] = "";
$key_folha_reparacao_referencia['pt'] = "Referência";
$key_folha_reparacao_referencia['en'] = "Reference";
$key_folha_reparacao_referencia['fr'] = "Referência";
$key_folha_reparacao_referencia['es'] = "Referência";
$key_folha_reparacao_estado['pt'] = "Estado";
$key_folha_reparacao_estado['en'] = "Status";
$key_folha_reparacao_estado['fr'] = "Referência";
$key_folha_reparacao_estado['es'] = "Referência";
$key_folha_reparacao_data['pt'] = "Data Act.";
$key_folha_reparacao_data['en'] = "Upd. Date";
$key_folha_reparacao_data['fr'] = "Referência";
$key_folha_reparacao_data['es'] = "Referência";
$key_folha_reparacao_accao['pt'] = "Acção";
$key_folha_reparacao_accao['en'] = "Action";
$key_folha_reparacao_accao['fr'] = "Referência";
$key_folha_reparacao_accao['es'] = "Referência";
$key_folha_reparacao_aceita['pt'] = "Aceita";
$key_folha_reparacao_aceita['en'] = "Accept";
$key_folha_reparacao_aceita['fr'] = "Referência";
$key_folha_reparacao_aceita['es'] = "Referência";
$key_folha_reparacao_recusa['pt'] = "Recusa";
$key_folha_reparacao_recusa['en'] = "Refuse";
$key_folha_reparacao_recusa['fr'] = "Referência";
$key_folha_reparacao_recusa['es'] = "Referência";
$key_tipo_cliente['pt'] = "Tipo de cliente";
$key_tipo_cliente['en'] = "Client type";
$key_tipo_cliente['fr'] = "TYPE DE CLIENT";
$key_tipo_cliente['es'] = "TIPO DE CLIENTE";
$key_tipo_cliente1['pt'] = "Cliente Final";
$key_tipo_cliente1['en'] = "End Client";
$key_tipo_cliente1['fr'] = "Client Final";
$key_tipo_cliente1['es'] = "Fin Cliente";
$key_tipo_cliente2['pt'] = "Revendedor";
$key_tipo_cliente2['en'] = "Retailer";
$key_tipo_cliente2['fr'] = "Concessionnaire";
$key_tipo_cliente2['es'] = "Comerciante";
$key_tipo_cliente6['pt'] = "Gabinete de Fiscalização";
$key_tipo_cliente6['en'] = "Office of Oversight";
$key_tipo_cliente6['fr'] = "Bureau de Contrôle";
$key_tipo_cliente6['es'] = "Oficina de Supervisión";
$key_sem_stock['pt'] = "De momento este produto não existe em stock.<br>A entrega poderá demorar até 15 dias, salvo rutura de stock.<br>Tem a certeza que pretende adicionar este produto ao carrinho?";
$key_sem_stock['en'] = "This product is currently out of stock.<br>Delivery can take up to 15 days, unless rupture of stock.<br>Are you sure you want to add this product to the cart?";
$key_sem_stock['fr'] = "Ce produit n'est pas en stock.<br>La livraison peut prendre jusqu'à 15 jours, sauf rupture de stock.<br>Vous êtes sûr que vous souhaitez ajouter ce produit au panier?";
$key_sem_stock['es'] = "Actualmente este producto no está disponible.<br>La entrega puede tardar hasta 15 días, salvo rotura de stock.<br>¿Seguro que quieres añadir este producto a la cesta?";
$key_adicionado_carrinho['pt'] = "Acabou de adicionar ao carrinho";
$key_adicionado_carrinho['en'] = "";
$key_adicionado_carrinho['fr'] = "";
$key_adicionado_carrinho['es'] = "";
$key_continuar_comprar['pt'] = "Continuar a comprar";
$key_continuar_comprar['en'] = "Continue to buying";
$key_continuar_comprar['fr'] = "Continuer à acheter";
$key_continuar_comprar['es'] = "Continuar para comprar";
$key_nao_adicionar['pt'] = "Não adicionar";
$key_nao_adicionar['en'] = "Don't add";
$key_nao_adicionar['fr'] = "Ne pas ajouter d'";
$key_nao_adicionar['es'] = "No agregue";
$key_adicionar_continuar_comprar['pt'] = "CONTINUAR A COMPRAR";
$key_adicionar_continuar_comprar['en'] = "";
$key_adicionar_continuar_comprar['fr'] = "";
$key_adicionar_continuar_comprar['es'] = "";
/* begin Sérgio */
$key_registo4b1['pt'] = "O seu registo foi aceite. Obrigado.";
$key_registo4b1['en'] = "Your registration has been accepted. Thank you.";
$key_registo4b1['fr'] = "Votre inscription a &eacute;t&eacute; accept&eacute;e. Je vous remercie.";
$key_registo4b1['es'] = "Su inscripci&oacute;n ha sido aceptada. Gracias.";
$key_registo4b2['pt'] = "O seu registo foi aceite, por favor aguarde a nossa confirmação por email.";
$key_registo4b2['en'] = "Your registration has been accepted, please wait for our confirmation email.";
$key_registo4b2['fr'] = "Votre inscription a &eacute;t&eacute; accept&eacute;e, s'il vous plaît attendre notre mail de confirmation.";
$key_registo4b2['es'] = "Su registro ha sido aceptado, por favor espere nuestra confirmaci&oacute;n por correo electr&oacute;nico.";
$key_erro11['pt'] = "<strong><font color='#cc0000'>Utilizador Errado ou inexistente!!!</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro11['en'] = "<strong><font color='#cc0000'>User Incorrect!!!</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' on, or got some wrong character.</font>";
$key_erro11['fr'] = "<strong><font color='#cc0000'>Utilisateur incorrect!!!</font></strong><br><font color='#000000'>S'il vous plaît v&eacute;rifiez si vous avez le 'verrouillage des majuscules', ou obtenu des mauvais caractcre.</font>";
$key_erro11['es'] = "<strong><font color='#cc0000'>Usuario incorrecto!!!</font></strong><br><font color='#000000'>Por favor, compruebe si tiene el 'bloqueo de may&uacute;sculas', o tiene alg&uacute;n personaje equivocado.</font>";
$key_erro12['pt'] = "<strong><font color='#cc0000'>Por favor insira a password!!!</font></strong><br><font color='#000000'>É necess&aacute;rio introduzir a password para que seja validado com sucesso.</font>";
$key_erro12['en'] = "<strong><font color='#cc0000'>Please insert the password!!!</font></strong><br><font color='#000000'>You must enter a password to be successfully validated.</font>";
$key_erro12['fr'] = "<strong><font color='#cc0000'>S'il vous plaît ins&eacute;rez le mot de passe!!!</font></strong><br><font color='#000000'>Vous devez entrer un mot de passe pour être valid&eacute; avec succcs.</font>";
$key_erro12['es'] = "<strong><font color='#cc0000'>Por favor, introduzca la contraseña!!!</font></strong><br><font color='#000000'>Debe introducir una contraseña para ser validado con &eacute;xito.</font>";
$key_erro13['pt'] = "<strong><font color='#cc0000'>Password Errada!!!</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro13['en'] = "<strong><font color='#cc0000'>Password Incorrect!!!</font></strong><br><font color='#000000'><br>Please check if you have the 'caps lock' on, or got some wrong character.</font>";
$key_erro13['fr'] = "<strong><font color='#cc0000'>Mot de passe incorrect!!!</font></strong><br><font color='#000000'>S'il vous plaît v&eacute;rifiez si vous avez le «verrouillage des majuscules sur, ou obtenu des mauvais caractcre.</font>";
$key_erro13['es'] = "<strong><font color='#cc0000'>Contraseña incorrecta!!!</font></strong><br><font color='#000000'>Por favor, compruebe si tiene el 'bloqueo de may&uacute;sculas' en, o tiene alg&uacute;n personaje equivocado.</font>";
$key_erro14['pt'] = "<strong><font color='#cc0000'>A Login n&atilde;o se encontra activa!!!</font></strong>";
$key_erro14['en'] = "<strong><font color='#cc0000'>The Registration is not active!!!</font></strong>";
$key_erro14['fr'] = "<strong><font color='#cc0000'>L'inscription n'est pas active!!!</font></strong>";
$key_erro14['es'] = "<strong><font color='#cc0000'>El registro no est&aacute; activo !!!</font></strong>";
$key_erro15['pt'] = "<strong><font color='#cc0000'>A Login n&atilde;o existe!!!</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro15['en'] = "<strong><font color='#cc0000'>The Login does not exist!!!</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' on, or got some wrong character.</font>";
$key_erro15['fr'] = "<strong><font color='#cc0000'>La connexion n'existe pas!!!</font></strong><br><font color='#000000'>S'il vous plaît v&eacute;rifiez si vous avez le «verrouillage des majuscules sur, ou obtenu des mauvais caractcre.</font>";
$key_erro15['es'] = "<strong><font color='#cc0000'>El inicio de sesi&oacute;n no existe!!!</font></strong><br><font color='#000000'>Por favor, compruebe si tiene el 'bloqueo de may&uacute;sculas' en, o tiene alg&uacute;n personaje equivocado.</font>";
$key_erro16['pt'] = "<strong><font color='#cc0000'>A Login est&aacute; ser usada por outro utilizador!</font></strong><br><font color='#000000'>Por favor, tente mais tarde. Obrigado.</font>";
$key_erro16['en'] = "<strong><font color='#cc0000'>The login is being used by another user!</font></strong><br><font color='#000000'>Please try again later. Thank you.</font>";
$key_erro16['fr'] = "<strong><font color='#cc0000'>De login wordt gebruikt door een andere gebruiker!!!</font></strong><br><font color='#000000'>Probeer het later opnieuw. Dank u.</font>";
$key_erro16['es'] = "<strong><font color='#cc0000'>Die Anmeldung wird von einem anderen Benutzer verwendet!</font></strong><br><font color='#000000'>Bitte versuchen Sie es später erneut. Danke.</font>";
$key_erro1['pt'] = "<strong><font color='#cc0000'>N&atilde;o foi inserida password!!!</font></strong><br><font color='#000000'>Por favor, insira a password.</font>";
$key_erro1['en'] = "<strong><font color='#cc0000'>No password was inserted!!!!</font></strong><br><font color='#000000'>Please enter the password.</font>";
$key_erro1['fr'] = "<font <strong> color='#cc0000'> Aucun mot de passe a &eacute;t&eacute; ins&eacute;r&eacute; !!!!</ font> </ color='#000000'> forte> <font <br> S'il vous plaît entrez le mot de passe. </ font>";
$key_erro1['es'] = "<font <strong> color='#cc0000'> No contraseña se insert&oacute; !!!!</ font> </ strong> <font color='#000000'> <br> Por favor, introduzca la contraseña. </ font>";
$key_erro2['pt'] = "A login que escolheu j&aacute; existe, por favor tente uma login diferente!!!";
$key_erro2['en'] = "The login you have chosen already exists, please try a different login!!!";
$key_erro2['fr'] = "La connexion que vous avez choisi existe d&eacute;jr, s'il vous plaît essayer de connexion diff&eacute;rentes";
$key_erro2['es'] = "La entrada que usted ha elegido ya existe, por favor, intente un inicio de sesi&oacute;n diferente!!!";
$key_erro3['pt'] = "O c&oacute;digo de valida&ccedil;&atilde;o n&atilde;o est&aacute; correcto!";
$key_erro3['en'] = "The validation code is incorrect!";
$key_erro3['fr'] = "Le code de validation est incorrect!";
$key_erro3['es'] = "El c&oacute;digo de validaci&oacute;n es incorrecto!";
$key_erro4['pt'] = "O n&uacute;mero de contribuinte indicado j&aacute; existe no nosso sistema!";
$key_erro4['en'] = "The NIF indicated already exists!";
$key_erro4['fr'] = "Le num&eacute;ro d'identification fiscale indiqu&eacute; d&eacute;jr existe!";
$key_erro4['es'] = "El n&uacute;mero de contribuyente ya existe!";
/* end Sérgio */
$key_msg_verify['pt'] = "Parabéns, o seu email foi confirmado!";
$key_msg_verify['en'] = "Congratulations, your email address has been confirmed!";
$key_msg_verify['fr'] = "Congratulations, your email address has been confirmed!";
$key_msg_verify['es'] = "Felicidades, tu dirección de correo electrónico ha sido confirmado!";
$key_msg_verify_erro['pt'] = "O seu email já se encontra verificado!";
$key_msg_verify_erro['en'] = "Your email is already verified!";
$key_msg_verify_erro['fr'] = "Congratulations, your email address has been confirmed!";
$key_msg_verify_erro['es'] = "Felicidades, tu dirección de correo electrónico ha sido confirmado!";
$key_msg_verify_erro2['pt'] = "O email não se encontra registado, por favor aceda zona de registo!";
$key_msg_verify_erro2['en'] = "The email is not registered, please go to the registration area!";
$key_msg_verify_erro2['fr'] = "";
$key_msg_verify_erro2['es'] = "El correo electrónico no está registrado, por favor vaya a la zona de registro!";
$key_fechar['pt'] = "Fechar";
$key_fechar['en'] = "Close";
$key_fechar['fr'] = "Proche";
$key_fechar['es'] = "Cerrar";
$key_seuemail['pt'] = "O seu email";
$key_seuemail['en'] = "Your email";
$key_seuemail['fr'] = "Votre email";
$key_seuemail['es'] = "Su email";
$key_seuemail["de"] = "Ihre e-mail";
$key_submeter_rec['pt'] = "Enviar Email";
$key_submeter_rec['en'] = "Send Email";
$key_submeter_rec['fr'] = "Envoyer e-mail";
$key_submeter_rec['es'] = "Enviar e-mail";
$key_submeter_cancel['pt'] = "Cancelar";
$key_submeter_cancel['en'] = "Cancel";
$key_submeter_cancel['fr'] = "Annuler";
$key_submeter_cancel['es'] = "Cancelar";
$key_dentmomentos['pt'] = "Dentro de momentos ir&aacute; receber a sua password na sua caixa de correio, obrigado!\n Por favor certifique-se que o nosso email n&atilde;o est&aacute; a ser encaminhado para SPAM. Caso contr&aacute;rio poder&aacute; n&atilde;o receber as nossas notifica&ccedil;&otilde;es.";
$key_dentmomentos['en'] = "Within moments you will receive your password in your mailbox, thank you!\n Please make sure that our email is not being sent to SPAM. Otherwise you may not receive our notifications.";
$key_dentmomentos['fr'] = "En quelques instants vous recevrez votre mot de passe dans votre boîte aux lettres, je vous remercie!";
$key_dentmomentos['es'] = "En momentos usted recibir&aacute; su contraseña en su buz&oacute;n de correo, gracias!\n S'il vous plaît assurez-vous que notre e-mail n'est pas envoy&eacute; au SPAM. Sinon, vous ne pouvez pas recevoir nos notifications.";
$key_dentmomentos2['pt'] = "Houve um problema ao enviar o email, por favor tente mais tarde.";
$key_dentmomentos2['en'] = "There was a problem sending the email, please try again later.";
$key_dentmomentos2['fr'] = "Il y avait un problčme d'envoyer le message, s'il vous plaît essayer ŕ nouveau plus tard.";
$key_dentmomentos2['es'] = "Hubo un problema al enviar el correo, int&eacute;ntalo de nuevo m&aacute;s tarde.";
$key_dentmomentos3['pt'] = "O endere&ccedil;o de e-mail introduzido n&atilde;o se encontra registado.";
$key_dentmomentos3['en'] = "The e-mail address you entered is not registered.";
$key_dentmomentos3['fr'] = "L'adresse e-mail que vous avez entr&eacute; n'est pas enregistr&eacute;.";
$key_dentmomentos3['es'] = "La direcci&oacute;n de correo electr&oacute;nico que ha introducido no est&aacute; registrado.";

$key_registo['pt'] = "Registar";
$key_registo['en'] = "Register";
$key_registo['fr'] = "Inscrivez";
$key_registo['es'] = "Registar";
$key_editarperfil['pt'] = "Editar perfil";
$key_editarperfil['en'] = "Edit Profile";
$key_editarperfil['fr'] = "Modifier le profil";
$key_editarperfil['es'] = "Editar perfil";
$key_noproductsfound['pt'] = "Não foram encontrados nenhuns resultados.";
$key_noproductsfound['en'] = "No results were found.";
$key_noproductsfound['fr'] = "Il n'y avait pas de resultat";
$key_noproductsfound['es'] = "No hay resultados encontrados";
$key_resultadospesquisa['pt'] = "Resultados da pesquisa";
$key_resultadospesquisa['en'] = "Search results";
$key_resultadospesquisa['fr'] = "Résultats de la recherche";
$key_resultadospesquisa['es'] = "Resultados de la búsqueda";
$key_emptyfield["pt"] = "Por favor preencha este campo";
$key_emptyfield["en"] = "Please complete this field";
$key_emptyfield["fr"] = "S'il vous plaît remplir ce champ";
$key_emptyfield["es"] = "Por favor, completa este campo";
$key_diferentpasswords["pt"] = "Passwords não correspondem";
$key_diferentpasswords["en"] = "Passwords não correspondem";
$key_diferentpasswords["fr"] = "Passwords não correspondem";
$key_diferentpasswords["es"] = "Passwords não correspondem";

//FILTROS
$txt_lptudo["pt"] = "Limpar Tudo";
$txt_lptudo["en"] = "Clean all";
$txt_lptudo["fr"] = "Limpar Tudo";
$txt_lptudo["es"] = "Limpar Tudo";

$txt_em_promocao["pt"] = "Promoções";
$txt_em_promocao["en"] = "Promoções";
$txt_em_promocao["fr"] = "Promoções";
$txt_em_promocao["es"] = "Promoções";

$txt_em_destaque["pt"] = "Destaques";
$txt_em_destaque["en"] = "Destaques";
$txt_em_destaque["fr"] = "Destaques";
$txt_em_destaque["es"] = "Destaques";

$txt_em_outlet["pt"] = "Outlet";
$txt_em_outlet["en"] = "Outlet";
$txt_em_outlet["fr"] = "Outlet";
$txt_em_outlet["es"] = "Outlet";

$txt_em_oportunidade["pt"] = "Em Oportunidades do Dia";
$txt_em_oportunidade["en"] = "In Oportunity of the Day";
$txt_em_oportunidade["fr"] = "Em Novidade";
$txt_em_oportunidade["es"] = "Em Novidade";

$txt_em_novidade["pt"] = "Novidades";
$txt_em_novidade["en"] = "New";
$txt_em_novidade["fr"] = "Novidades";
$txt_em_novidade["es"] = "Novidades";

$txt_prec_inter["pt"] = "Por Intervalo de Preço";
$txt_prec_inter["en"] = "By Price Range";
$txt_prec_inter["fr"] = "Por Intervalo de Preço";
$txt_prec_inter["es"] = "Por Intervalo de Preço";

$txt_caracteristicas["pt"] = "CARATERÍSTICAS";
$txt_caracteristicas["en"] = "CARATERÍSTICAS";
$txt_caracteristicas["fr"] = "CARATERÍSTICAS";
$txt_caracteristicas["es"] = "CARATERÍSTICAS";

$txt_f_filterby["pt"] = "FILTRAR POR";
$txt_f_filterby["en"] = "FILTER BY";
$txt_f_filterby["fr"] = "FILTRER PAR";
$txt_f_filterby["es"] = "FILTRAR POR";

$txt_rp_marcas["pt"] = "Marcas";
$txt_rp_marcas["en"] = "Brands";
$txt_rp_marcas["fr"] = "Marcas";
$txt_rp_marcas["es"] = "Marcas";

$txt_rp_familias["pt"] = "FAMÍLIAS";
$txt_rp_familias["en"] = "FAMILIES";
$txt_rp_familias["fr"] = "FAMILLES";
$txt_rp_familias["es"] = "FAMILIAS";

$txt_preco["pt"] = "Preço";
$txt_preco["en"] = "Price";
$txt_preco["fr"] = "Preço";
$txt_preco["es"] = "Preço";

$txt_produtos["pt"] = "Produtos";
$txt_produtos["en"] = "Products";
$txt_produtos["fr"] = "Produits";
$txt_produtos["es"] = "Productos";

$txt_produtosper["pt"] = "Número de produtos por página";
$txt_produtosper["en"] = "See by page";
$txt_produtosper["fr"] = "Produits";
$txt_produtosper["es"] = "Productos";

$txt_disponibilidade_total["pt"] = "Disponibilidade Total";
$txt_disponibilidade_total["en"] = "Disponibilidade Total";
$txt_disponibilidade_total["es"] = "Disponibilidade Total";
$txt_disponibilidade_total["fr"] = "Disponibilidade Total";

$txt_disponibilidade_reduzida["pt"] = "Disponibilidade Reduzida";
$txt_disponibilidade_reduzida["en"] = "Disponibilidade Reduzida";
$txt_disponibilidade_reduzida["es"] = "Disponibilidade Reduzida";
$txt_disponibilidade_reduzida["fr"] = "Disponibilidade Reduzida";

$txt_disponivel_brevemente["pt"] = "Disponível Brevemente";
$txt_disponivel_brevemente["en"] = "Disponível Brevemente";
$txt_disponivel_brevemente["es"] = "Disponível Brevemente";
$txt_disponivel_brevemente["fr"] = "Disponível Brevemente";

$txt_disponivel_encomenda["pt"] = "Disponível por Encomenda";
$txt_disponivel_encomenda["en"] = "Disponível por Encomenda";
$txt_disponivel_encomenda["es"] = "Disponível por Encomenda";
$txt_disponivel_encomenda["fr"] = "Disponível por Encomenda";

$txt_pagina2["pt"] = "Página";
$txt_pagina2["en"] = "Page";
$txt_pagina2["fr"] = "Página";
$txt_pagina2["es"] = "Página";

$txt_paginas["pt"] = "Páginas";
$txt_paginas["en"] = "Pages";
$txt_paginas["fr"] = "Páginas";
$txt_paginas["es"] = "Páginas";

$produtos_cab["pt"] = "Produtos";
$produtos_cab["en"] = "Products";
$produtos_cab["fr"] = "Produits";
$produtos_cab["es"] = "Productos";

$produtos_cab2["pt"] = "produtos";
$produtos_cab2["en"] = "products";
$produtos_cab2["fr"] = "produits";
$produtos_cab2["es"] = "productos";

$txt_maisbarato["pt"] = "Mais Barato";
$txt_maisbarato["en"] = "Less Expensive";
$txt_maisbarato["fr"] = "Preço com IVA:";
$txt_maisbarato["es"] = "Más barato";

$txt_maiscaro["pt"] = "Mais Caro";
$txt_maiscaro["en"] = "More Expensive";
$txt_maiscaro["fr"] = "Mais Caro";
$txt_maiscaro["es"] = "Más Caro";

$txt_ordby["pt"] = "Ordenar por";
$txt_ordby["en"] = "Order by";
$txt_ordby["fr"] = "Ordenar por";
$txt_ordby["es"] = "Ordenar por";

$key_retroceder["pt"] = "Retroceder";
$key_retroceder["en"] = "Back";
$key_retroceder["fr"] = "Reculer";
$key_retroceder["es"] = "Retroceder";

$key_finalizar["pt"] = "Finalizar Encomenda";
$key_finalizar["en"] = "Finalize Order";
$key_finalizar["fr"] = "Finaliser Commander";
$key_finalizar["es"] = "Finalizar Pedido";

$key_shop_msg6['pt'] = 'Os dados de entrega são diferentes?';
$key_shop_msg6['en'] = 'Os dados de entrega são diferentes?';//TODO: FALTA METER O TEXTO CORRECTO
$key_shop_msg6['fr'] = 'Os dados de entrega são diferentes?';//TODO: FALTA METER O TEXTO CORRECTO
$key_shop_msg6['es'] = 'Os dados de entrega são diferentes?';//TODO: FALTA METER O TEXTO CORRECTO


/*farmacia*/


$key_msg_welcome["pt"] = "Olá";
$key_msg_welcome["en"] = "Hello";
$key_msg_welcome["fr"] = ""; //TODO: Alterar o texto para a lingua;
$key_msg_welcome["es"] = ""; //TODO: Alterar o texto para a lingua; //TODO: Alterar o texto para a lingua;

$key_search_sem_sugestoes["pt"] = "Não foram encontradas sugestões";
$key_search_sem_sugestoes["en"] = "There were no suggestions found";
$key_search_sem_sugestoes["fr"] = "Il n'y avait pas de suggestions";
$key_search_sem_sugestoes["es"] = "No había sugerencias";


$key_cliente_recuperar_pass['pt'] = 'Esqueceu-se da palavra-passe?';
$key_cliente_recuperar_pass['en'] = 'Don\'t remember your password? ';
$key_cliente_recuperar_pass['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_recuperar_pass['es'] = 'Selecione'; //TODO: Alterar o texto para a lingua;

$key_cliente_auten_login['pt'] = 'Voltar ao Login';
$key_cliente_auten_login['en'] = 'Go back to Login';
$key_cliente_auten_login['fr'] = 'Selecione'; //TODO: Alterar o texto para a lingua;
$key_cliente_auten_login['es'] = 'Selecione';

$key_recuperar_pass['pt'] = "Recuperar palavra-passe";
$key_recuperar_pass['en'] = "Recover password";
$key_recuperar_pass['fr'] = "Récupérer mot de passe";
$key_recuperar_pass['es'] = "Recuperar contraseña";
$key_obrigatorio2['pt'] = "Campo de preenchimento obrigatório";
$key_obrigatorio2['en'] = "Required Fields"; //TODO: Alterar o texto para a lingua;
$key_obrigatorio2['fr'] = "Champs Obligatoires"; //TODO: Alterar o texto para a lingua;
$key_obrigatorio2['es'] = "obligatorio"; //TODO: Alterar o texto para a lingua;
$key_recuperar_pass_extramsg['pt'] = 'Por favor insira o seu endereço de e-mail abaixo. Receberá uma ligação para repor a sua palavra-passe.';
$key_recuperar_pass_extramsg['en'] = '';
$key_recuperar_pass_extramsg['fr'] = '';
$key_recuperar_pass_extramsg['es'] = '';
$key_recuperar_pass_msg['pt'] = "A sua password foi alterada com sucesso.";
$key_recuperar_pass_msg['en'] = "";
$key_recuperar_pass_msg['fr'] = "";
$key_recuperar_pass_msg['es'] = "";

$key_user_registadomsg['pt'] = 'Utilizador já registado';
$key_user_registadomsg['en'] = '';
$key_user_registadomsg['fr'] = '';
$key_user_registadomsg['es'] = '';
$key_user_notregistadomsg['pt'] = 'Se não é utilizador pode registar-se aqui';
$key_user_notregistadomsg['en'] = '';
$key_user_notregistadomsg['fr'] = '';
$key_user_notregistadomsg['es'] = '';
$key_iniciarsessao['pt'] = 'Iniciar sessão';
$key_iniciarsessao['en'] = '';
$key_iniciarsessao['fr'] = '';
$key_iniciarsessao['es'] = '';


$key_prdtag_new['pt'] = 'Novo';
$key_prdtag_new['en'] = 'New';
$key_prdtag_new['fr'] = '';
$key_prdtag_new['es'] = '';
$key_catalogo_ver_destaques['pt'] = 'Ver todos os destaques';
$key_catalogo_ver_destaques['en'] = '';
$key_catalogo_ver_destaques['fr'] = '';
$key_catalogo_ver_destaques['es'] = '';
$key_catalogo_sabermais['pt'] = 'SABER MAIS';
$key_catalogo_sabermais['en'] = 'READ MORE';
$key_catalogo_sabermais['fr'] = '';
$key_catalogo_sabermais['es'] = '';
$key_catalogo_comprar['pt'] = 'COMPRAR';
$key_catalogo_comprar['en'] = 'BUY';
$key_catalogo_comprar['fr'] = '';
$key_catalogo_comprar['es'] = '';
$key_promocoes['pt'] = 'Promoções';
$key_promocoes['en'] = '';
$key_promocoes['fr'] = '';
$key_promocoes['es'] = '';
$key_equivalentes_title['pt'] = 'Melhor resultado quando usado com...';
$key_equivalentes_title['en'] = '';
$key_equivalentes_title['fr'] = '';
$key_equivalentes_title['es'] = '';
$key_catalogo_ver_promocoes['pt'] = 'Ver todas as promoções';
$key_catalogo_ver_promocoes['en'] = '';
$key_catalogo_ver_promocoes['fr'] = '';
$key_catalogo_ver_promocoes['es'] = '';
$key_registo_titulo['pt'] = "Nova conta";
$key_registo_titulo['en'] = "";
$key_registo_titulo['fr'] = "";
$key_registo_titulo['es'] = "";
$key_registo_subtitulo['pt'] = "INFORMAÇÃO DE CONTA";
$key_registo_subtitulo['en'] = "";
$key_registo_subtitulo['fr'] = "";
$key_registo_subtitulo['es'] = "";
$key_registo_subtitulo1['pt'] = "DADOS PESSOAIS";
$key_registo_subtitulo1['en'] = "";
$key_registo_subtitulo1['fr'] = "";
$key_registo_subtitulo1['es'] = "";
$key_registo_subtitulo2['pt'] = "INFORMAÇÃO GERAL";
$key_registo_subtitulo2['en'] = "";
$key_registo_subtitulo2['fr'] = "";
$key_registo_subtitulo2['es'] = "";
$key_registo_subtitulo3['pt'] = "CONTACTOS";
$key_registo_subtitulo3['en'] = "";
$key_registo_subtitulo3['fr'] = "";
$key_registo_subtitulo3['es'] = "";
$key_registo_subtitulo4['pt'] = "INFORMAÇÃO BANCÁRIA";
$key_registo_subtitulo4['en'] = "";
$key_registo_subtitulo4['fr'] = "";
$key_registo_subtitulo4['es'] = "";

$key_registo_texto['pt'] = "<strong>Para finalizar o seu pedido deverá enviar-nos por FAX ou E-mail, uma cópia dos seguintes documentos:</strong><br><br><span class=\"mandatory\">*</span> Nº Contribuinte e B.I. ou C.C. dos sócios da empresa<br><span class=\"mandatory\">*</span> Comprovativo de Início de Actividade<br><span class=\"mandatory\">*</span> Certidão de teor do Registo Comercial (apenas para Sociedades Comerciais)<br><br>Nota: Todos os dados são confidenciais e destinam-se exclusivamente ao nosso Departamento Financeiro.";
$key_registo_texto['en'] = "";
$key_registo_texto['fr'] = "";
$key_registo_texto['es'] = "";
$key_cidade['pt'] = "Cidade";
$key_cidade['en'] = "";
$key_cidade['fr'] = "";
$key_cidade['es'] = "";

$key_password_repetida['pt'] = "As passwords não correspondem";
$key_password_repetida['en'] = "";
$key_password_repetida['fr'] = "";
$key_password_repetida['es'] = "";

$key_editarperfil_titulo['pt'] = "Informação de Conta";
$key_editarperfil_titulo['en'] = "Account Information";
$key_editarperfil_titulo['fr'] = "";
$key_editarperfil_titulo['es'] = "";
$key_editarperfil_subtitulo1['pt'] = "INFORMAÇÃO DA EMPRESA";
$key_editarperfil_subtitulo1['en'] = "";
$key_editarperfil_subtitulo1['fr'] = "";
$key_editarperfil_subtitulo1['es'] = "";
$key_editarperfil_subtitulo2['pt'] = "INFORMAÇÃO GERAL";
$key_editarperfil_subtitulo2['en'] = "";
$key_editarperfil_subtitulo2['fr'] = "";
$key_editarperfil_subtitulo2['es'] = "";
$key_editarperfil_subtitulo3['pt'] = "CONTACTOS";
$key_editarperfil_subtitulo3['en'] = "";
$key_editarperfil_subtitulo3['fr'] = "";
$key_editarperfil_subtitulo3['es'] = "";
$key_editarperfil_subtitulo4['pt'] = "INFORMAÇÃO BANCÁRIA";
$key_editarperfil_subtitulo4['en'] = "";
$key_editarperfil_subtitulo4['fr'] = "";
$key_editarperfil_subtitulo4['es'] = "";

$key_sumarioconta['pt'] = "Sumário da Conta";
$key_sumarioconta['en'] = "Account Summary";
$key_sumarioconta['fr'] = "";
$key_sumarioconta['es'] = "";
$key_sumarioconta_encomendasrecentes['pt'] = "ENCOMENDAS RECENTES";
$key_sumarioconta_encomendasrecentes['en'] = "RECENT ORDERS";
$key_sumarioconta_encomendasrecentes['fr'] = "";
$key_sumarioconta_encomendasrecentes['es'] = "";
$key_sumarioconta_vertudo['pt'] = "Ver tudo";
$key_sumarioconta_vertudo['en'] = "See all";
$key_sumarioconta_vertudo['fr'] = "";
$key_sumarioconta_vertudo['es'] = "";
$key_asminhasencomendas['pt'] = "As Minhas Encomendas";
$key_asminhasencomendas['en'] = "My Orders";
$key_asminhasencomendas['fr'] = "";
$key_asminhasencomendas['es'] = "";
$key_faturas['pt'] = "Facturas";
$key_faturas['en'] = "My Invoices";
$key_faturas['fr'] = "";
$key_faturas['es'] = "";

$key_noticias_titulo['pt'] = "Notícias";
$key_noticias_titulo['en'] = "News";
$key_noticias_titulo['fr'] = "";
$key_noticias_titulo['es'] = "";

$key_wishlist['pt'] = "Favoritos";
$key_wishlist['en'] = "Favoritos";
$key_wishlist['fr'] = "Favoritos";
$key_wishlist['es'] = "Favoritos";


$key_sugira_titulo['pt'] = "Sugira o nosso website";
$key_sugira_titulo['en'] = "";
$key_sugira_titulo['fr'] = "";
$key_sugira_titulo['es'] = "";
$key_sugira_subtitulo['pt'] = "O melhor elogio que podemos receber &eacute; a sua recomenda&ccedil;&atilde;o para a fam&iacute;lia e amigos.<br><br>Permita que outros que conhece tamb&eacute;m aproveitem o que temos para oferecer.<br><br>Vamos trat&aacute;-los como nossos amigos tamb&eacute;m.<br /><br /><b>Obrigado por confiar em n&oacute;s!<b><br />Preencha o formul&aacute;rio que se segue e clique em 'Enviar Email' para recomendar este site.";
$key_sugira_subtitulo['en'] = "";
$key_sugira_subtitulo['fr'] = "";
$key_sugira_subtitulo['es'] = "";

$key_sugira_oseunome['pt'] = "O seu nome";
$key_sugira_oseunome['en'] = "";
$key_sugira_oseunome['fr'] = "";
$key_sugira_oseunome['es'] = "";

$key_sugira_oseuemail['pt'] = "O seu e-mail";
$key_sugira_oseuemail['en'] = "";
$key_sugira_oseuemail['fr'] = "";
$key_sugira_oseuemail['es'] = "";

$key_sugira_mensagem['pt'] = "Mensagem";
$key_sugira_mensagem['en'] = "";
$key_sugira_mensagem['fr'] = "";
$key_sugira_mensagem['es'] = "";

$key_sugira_nomeamigo['pt'] = "O nome do seu amigo";
$key_sugira_nomeamigo['en'] = "";
$key_sugira_nomeamigo['fr'] = "";
$key_sugira_nomeamigo['es'] = "";

$key_sugira_emailamigo['pt'] = "O e-mail do seu amigo";
$key_sugira_emailamigo['en'] = "";
$key_sugira_emailamigo['fr'] = "";
$key_sugira_emailamigo['es'] = "";


$key_sugira_enviook["pt"] = "Obrigado pela sua recomenda&ccedil;&atilde;o.";
$key_sugira_enviook["en"] = "";
$key_sugira_enviook["fr"] = "";
$key_sugira_enviook["es"] = "";

$key_sugira_envioerro["pt"] = "Houve um problema no envio do email, por favor tente mais tarde.";
$key_sugira_envioerro["en"] = "";
$key_sugira_envioerro["fr"] = "";
$key_sugira_envioerro["es"] = "";


/* LIVE CHAT */

$key_chat_title["pt"] = "Chat!";
$key_chat_title["en"] = "";
$key_chat_title["fr"] = "";
$key_chat_title["es"] = "";
$key_chat_online["pt"] = "Estamos online!";
$key_chat_online["en"] = "";
$key_chat_online["fr"] = "";
$key_chat_online["es"] = "";
$key_chat_offline["pt"] = "Estamos offline. Deixe-nos a sua mensagem!";
$key_chat_offline["en"] = "";
$key_chat_offline["fr"] = "";
$key_chat_offline["es"] = "";
$key_chat_away["pt"] = "Estamos ausentes. Deixe-nos a sua mensagem!";
$key_chat_away["en"] = "";
$key_chat_away["fr"] = "";
$key_chat_away["es"] = "";
$key_chat_msgoffline["pt"] = "Estamos Offline";
$key_chat_msgoffline["en"] = "";
$key_chat_msgoffline["fr"] = "";
$key_chat_msgoffline["es"] = "";
$key_chat_msgonline["pt"] = "Estamos Online";
$key_chat_msgonline["en"] = "";
$key_chat_msgonline["fr"] = "";
$key_chat_msgonline["es"] = "";
$key_chat_msgoffline2["pt"] = "Por favor deixe-nos o seu nome e endereço de email, entraremos em contacto consigo brevemente. Obrigada.";
$key_chat_msgoffline2["en"] = "";
$key_chat_msgoffline2["fr"] = "";
$key_chat_msgoffline2["es"] = "";

$key_chat_offline_send["pt"] = "Enviar mensagem";
$key_chat_offline_send["en"] = "";
$key_chat_offline_send["fr"] = "";
$key_chat_offline_send["es"] = "";

$key_chat_offline_sucesso["pt"] = "Dados enviados com sucesso";
$key_chat_offline_sucesso["en"] = "";
$key_chat_offline_sucesso["fr"] = "";
$key_chat_offline_sucesso["es"] = "";


/* Favoritos */

$key_wishlist_title["pt"] = "Favoritos";
$key_wishlist_title["en"] = "Favoritos";
$key_wishlist_title["fr"] = "Favoritos";
$key_wishlist_title["es"] = "Favoritos";
$key_wishlist_subtitle["pt"] = "Os seus produtos";
$key_wishlist_subtitle["en"] = "";
$key_wishlist_subtitle["fr"] = "";
$key_wishlist_subtitle["es"] = "";
$key_wishlist_notloggedin["pt"] = "É necessário estar autenticado.";
$key_wishlist_notloggedin["en"] = "";
$key_wishlist_notloggedin["fr"] = "";
$key_wishlist_notloggedin["es"] = "";
$key_wishlist_semprodutos["pt"] = "Lista de produtos favoritos encontra-se vazia.";
$key_wishlist_semprodutos["en"] = "";
$key_wishlist_semprodutos["fr"] = "";
$key_wishlist_semprodutos["es"] = "";
$key_wishlist_addallproducts["pt"] = "Clique para adicionar todos os produtos ao Carrinho";
$key_wishlist_addallproducts["en"] = "";
$key_wishlist_addallproducts["fr"] = "";
$key_wishlist_addallproducts["es"] = "";


/* TESTEMUNHOS */
$key_testemunhos_title["pt"] = "Opiniões";
$key_testemunhos_title["en"] = "";
$key_testemunhos_title["fr"] = "";
$key_testemunhos_title["es"] = "";
$key_testemunhos_giveopinion["pt"] = "Dar a sua opinião";
$key_testemunhos_giveopinion["en"] = "Give your opinion";
$key_testemunhos_giveopinion["fr"] = "";
$key_testemunhos_giveopinion["es"] = "";
$key_testemunhos_nodata["pt"] = "Produto sem Opiniões";
$key_testemunhos_nodata["en"] = "";
$key_testemunhos_nodata["fr"] = "";
$key_testemunhos_nodata["es"] = "";
$key_testemunhos_nome["pt"] = "Nome";
$key_testemunhos_nome["en"] = "";
$key_testemunhos_nome["fr"] = "";
$key_testemunhos_nome["es"] = "";
$key_testemunhos_local["pt"] = "Local";
$key_testemunhos_local["en"] = "";
$key_testemunhos_local["fr"] = "";
$key_testemunhos_local["es"] = "";
$key_testemunhos_pais["pt"] = "Pais";
$key_testemunhos_pais["en"] = "";
$key_testemunhos_pais["fr"] = "";
$key_testemunhos_pais["es"] = "";
$key_testemunhos_mensagem["pt"] = "Mensagem";
$key_testemunhos_mensagem["en"] = "";
$key_testemunhos_mensagem["fr"] = "";
$key_testemunhos_mensagem["es"] = "";


/* SHOP */

$key_shop_noproducts["pt"] = "Carrinho vazio";
$key_shop_noproducts["en"] = "";
$key_shop_noproducts["fr"] = "";
$key_shop_noproducts["es"] = "";

$key_minishop_products["pt"] = "PRODUTOS";
$key_minishop_products["en"] = "PRODUCTS";
$key_minishop_products["fr"] = "";
$key_minishop_products["es"] = "";
$key_minishop_product["pt"] = "PRODUTO";
$key_minishop_product["en"] = "PRODUCT";
$key_minishop_product["fr"] = "";
$key_minishop_product["es"] = "";

$key_minishop_hover["pt"] = "Acessa ao seu carrinho de compras e finalize a sua encomenda";
$key_minishop_hover["en"] = "";
$key_minishop_hover["fr"] = "";
$key_minishop_hover["es"] = "";

$key_minishop_hover2["pt"] = "O seu carrinho não tem produtos";
$key_minishop_hover2["en"] = "";
$key_minishop_hover2["fr"] = "";
$key_minishop_hover2["es"] = "";

$key_wishlist_hover["pt"] = "Consulte os seus produtos favoritos";
$key_wishlist_hover["en"] = "";
$key_wishlist_hover["fr"] = "";
$key_wishlist_hover["es"] = "";


$key_shop_title["pt"] = "Finalizar Encomenda";
$key_shop_title["en"] = "";
$key_shop_title["fr"] = "";
$key_shop_title["es"] = "";

$key_shop_subtitle["pt"] = "Os seus dados pessoais";
$key_shop_subtitle["en"] = "";
$key_shop_subtitle["fr"] = "";
$key_shop_subtitle["es"] = "";

$key_shop_subtitle2["pt"] = "Dados utilizados apenas para envio de fatura e entrega da encomenda";
$key_shop_subtitle2["en"] = "";
$key_shop_subtitle2["fr"] = "";
$key_shop_subtitle2["es"] = "";

$key_shop_createaccount["pt"] = "Criar conta com estes dados";
$key_shop_createaccount["en"] = "";
$key_shop_createaccount["fr"] = "";
$key_shop_createaccount["es"] = "";

$key_shop_dadosdiferentes["pt"] = "Clique aqui se os dados de entrega foram diferentes de faturação";
$key_shop_dadosdiferentes["en"] = "";
$key_shop_dadosdiferentes["fr"] = "";
$key_shop_dadosdiferentes["es"] = "";

$key_shop_deliverytitle["pt"] = "Enviar para outra morada";
$key_shop_deliverytitle["en"] = "";
$key_shop_deliverytitle["fr"] = "";
$key_shop_deliverytitle["es"] = "";


/* NEWSLETTER */

$key_newsletter_title["pt"] = "Subscrever a newsletter";
$key_newsletter_title["en"] = "";
$key_newsletter_title["fr"] = "";
$key_newsletter_title["es"] = "";

$key_newsletter_subtitle["pt"] = "";
$key_newsletter_subtitle["en"] = "";
$key_newsletter_subtitle["fr"] = "";
$key_newsletter_subtitle["es"] = "";

$key_newsletter_nome["pt"] = "O seu nome:";
$key_newsletter_nome["en"] = "";
$key_newsletter_nome["fr"] = "";
$key_newsletter_nome["es"] = "";

$key_newsletter_email["pt"] = "O seu email:";
$key_newsletter_email["en"] = "";
$key_newsletter_email["fr"] = "";
$key_newsletter_email["es"] = "";

$key_newsletter_registook["pt"] = "O seu email foi registado com sucesso. <br /><b>Obrigado por se registar na nossa newsletter.</b>";
$key_newsletter_registook["en"] = "";
$key_newsletter_registook["fr"] = "";
$key_newsletter_registook["es"] = "";

$key_newsletter_registoerro1["pt"] = "Ocorreu um erro ao registar na nossa newsletter, volte a tentar mais tarde. Obrigado.";
$key_newsletter_registoerro1["en"] = "";
$key_newsletter_registoerro1["fr"] = "";
$key_newsletter_registoerro1["es"] = "";

$key_newsletter_registoerro2["pt"] = "O seu email já se encontra subscrito à nossa newsletter. Obrigado";
$key_newsletter_registoerro2["en"] = "";
$key_newsletter_registoerro2["fr"] = "";
$key_newsletter_registoerro2["es"] = "";


/* COMENTÁRIOS */

$key_comentarios_title["pt"] = "Comentários";
$key_comentarios_title["en"] = "Comments";
$key_comentarios_title["fr"] = "";
$key_comentarios_title["es"] = "";
$key_comentarios_giveopinion["pt"] = "Deixe o seu comentário";
$key_comentarios_giveopinion["en"] = "Leave your comment";
$key_comentarios_giveopinion["fr"] = "";
$key_comentarios_giveopinion["es"] = "";
$key_comentarios_nodata["pt"] = "Notícia sem comentários";
$key_comentarios_nodata["en"] = "";
$key_comentarios_nodata["fr"] = "";
$key_comentarios_nodata["es"] = "";
$key_comentarios_nome["pt"] = "Nome";
$key_comentarios_nome["en"] = "";
$key_comentarios_nome["fr"] = "";
$key_comentarios_nome["es"] = "";
$key_comentarios_local["pt"] = "Local";
$key_comentarios_local["en"] = "";
$key_comentarios_local["fr"] = "";
$key_comentarios_local["es"] = "";
$key_comentarios_pais["pt"] = "Pais";
$key_comentarios_pais["en"] = "";
$key_comentarios_pais["fr"] = "";
$key_comentarios_pais["es"] = "";
$key_comentarios_mensagem["pt"] = "Mensagem";
$key_comentarios_mensagem["en"] = "";
$key_comentarios_mensagem["fr"] = "";
$key_comentarios_mensagem["es"] = "";


$key_shop_embulho["pt"] = "Necessita preencher a mensagem de embrulho";
$key_shop_embulho["en"] = "";
$key_shop_embulho["fr"] = "";
$key_shop_embulho["es"] = "";

$key_shop_termos["pt"] = "Necessita de aceitar os termos de compra";
$key_shop_termos["en"] = "";
$key_shop_termos["fr"] = "";
$key_shop_termos["es"] = "";


$key_carrinho_voucher["pt"] = "O Código de Promoção já existe no carrinho";
$key_carrinho_voucher["en"] = "The Promotion Code already exists in cart";
$key_carrinho_voucher["fr"] = "";
$key_carrinho_voucher["es"] = "El código de promoción ya existe en el carrito";

$key_carrinho_voucher2["pt"] = "O Código de Promoção não existe ou já não se encontra ativo";
$key_carrinho_voucher2["en"] = "The Promotion Code does not exist or is no longer active";
$key_carrinho_voucher2["fr"] = "";
$key_carrinho_voucher2["es"] = "El código de promoción no existe o ya no está activa";


// Voucher stuff
$key_codigo_usado["pt"] = "O código que está a tentar usar já foi usado anteriormente!";
$key_codigo_usado["en"] = "The code you are trying to use has been used before!";
$key_codigo_usado["fr"] = "";
$key_codigo_usado["es"] = "El código que está intentando utilizar ha sido utilizado antes!";

$key_codigo_inexistente["pt"] = "O código inserido não é um código válido!";
$key_codigo_inexistente["en"] = "The code insert is not a valid code";
$key_codigo_inexistente["fr"] = "";
$key_codigo_inexistente["es"] = "El código introducido no es un código válido!";

$key_voucher_oferta["pt"] = "Voucher - Produto Oferta";
$key_voucher_oferta["en"] = "Voucher - Product Offer";
$key_voucher_oferta["fr"] = "";
$key_voucher_oferta["es"] = "Vale - Oferta de producto";

$key_voucher_oferta2["pt"] = "Produto Oferta";
$key_voucher_oferta2["en"] = "Product Offer";
$key_voucher_oferta2["fr"] = "";
$key_voucher_oferta2["es"] = "Oferta de producto";

$key_voucher_desconto["pt"] = "Voucher de desconto";
$key_voucher_desconto["en"] = "Discount Voucher";
$key_voucher_desconto["fr"] = "";
$key_voucher_desconto["es"] = "Vale de descuento";


$key_codigopromo["pt"] = "Código de Promoção";
$key_codigopromo["en"] = "Promotion Code";
$key_codigopromo["fr"] = "";
$key_codigopromo["es"] = "Código Promocional";

$key_shop_ofertas["pt"] = "Apenas pode escolher entre 1 a 3 marcas para receber 3 amostras gratuitas!";
$key_shop_ofertas["en"] = "";
$key_shop_ofertas["fr"] = "";
$key_shop_ofertas["es"] = "";

$key_codigo_invalido_cliente["pt"] = "Dado que não é o cliente associado a este código, não o pode usar!";
$key_codigo_invalido_cliente["en"] = "";
$key_codigo_invalido_cliente["fr"] = "";
$key_codigo_invalido_cliente["es"] = "Dado que não é o cliente associado a este código, não o pode usar!";


$key_home_description["pt"] = "Clique para voltar à homepage";
$key_home_description["en"] = "Click to return to the homepage";
$key_home_description["fr"] = "";
$key_home_description["es"] = "";

$key_unicre_part1["pt"] = "<br>Obrigado por ter efecuado a sua compra na nossa loja.<br><br>
            Os dados relativos à sua transacção são os seguintes:<br>
            Nome do cliente: ";
$key_unicre_part1["en"] = "<br>Thank you for purchasing in our store.<br><br>
            The information relating to your transaction are as follows:<br>
            Client name: ";
$key_unicre_part1["fr"] = "";
$key_unicre_part1["es"] = "";

$key_unicre_part2["pt"] = "<br>Valor da transacção: ";
$key_unicre_part2["en"] = "<br>Transaction value: ";
$key_unicre_part2["fr"] = "";
$key_unicre_part2["es"] = "";

$key_unicre_part3["pt"] = "<br>Data da transacção: ";
$key_unicre_part3["en"] = "<br>Transaction date: ";
$key_unicre_part3["fr"] = "";
$key_unicre_part3["es"] = "";

$key_unicre_part4["pt"] = "<br>Numero da encomenda: ";
$key_unicre_part4["en"] = "<br>Order number: ";
$key_unicre_part4["fr"] = "";
$key_unicre_part4["es"] = "";

$key_unicre_part5["pt"] = "<br><br>Poderá ver informação mais detalhada sobre esta transacção nas suas encomendas.<br>";
$key_unicre_part5["en"] = "<br><br>You can see more detailed information about this transaction in your orders.<br>";
$key_unicre_part5["fr"] = "";
$key_unicre_part5["es"] = "";


/*LOGIN*/
$key_login_loggedin["pt"] = 'Ol&aacute;';
$key_login_loggedin["en"] = 'Hello';
$key_login_loggedin["fr"] = 'Bonjour';
$key_login_loggedin["es"] = '&iexcl;hola';
$key_login_notloggedin["pt"] = 'Login / Registar';
$key_login_notloggedin["en"] = 'Login / Registar';
$key_login_notloggedin["fr"] = 'Login / Registar';
$key_login_notloggedin["es"] = 'Login / Registar';
$key_login_socialpremsg["pt"] = 'ou faça login usando';
$key_login_socialpremsg["en"] = 'or login with';
$key_login_socialpremsg["fr"] = 'ou connectez-vous &agrave; l\'aide';
$key_login_socialpremsg["es"] = 'o entre usando';


$key_website_locked["pt"] = 'Brevemente online';
$key_website_locked["en"] = '';
$key_website_locked["fr"] = '';
$key_website_locked["es"] = '';

$key_website_redirect["pt"] = 'Dentro de momentos ser&aacute; reencaminhado, se o redireccionamento n&atilde;o for autom&aacute;tico clique <a href="{$referer}">aqui</a>';
$key_website_redirect["en"] = '';
$key_website_redirect["fr"] = '';
$key_website_redirect["es"] = '';

$key_website_emailerrado["pt"] = 'Por favor insira um email correcto!';
$key_website_emailerrado["en"] = '';
$key_website_emailerrado["fr"] = '';
$key_website_emailerrado["es"] = '';

$key_afiliados["pt"] = "Afiliado";
$key_afiliados["en"] = "Afiliado";
$key_afiliados["fr"] = "";
$key_afiliados["es"] = "";


$key_seunome["pt"] = "Email";
$key_seunome["en"] = "Email";
$key_seunome["fr"] = "";
$key_seunome["es"] = "";


$key_ntrabalhadores["pt"] = "N.º de trabalhadores";
$key_ntrabalhadores["en"] = "";
$key_iniactividade["pt"] = "Início de actividade";
$key_iniactividade["en"] = "";
$key_faturacaoanual["pt"] = "Facturação anual";
$key_faturacaoanual["en"] = "";
$key_outrosfornecedores["pt"] = "Outros fornecedores";
$key_outrosfornecedores["en"] = "";
$key_principaismarcas["pt"] = "Principais marcas";
$key_principaismarcas["en"] = "";
$key_tiposprodutos["pt"] = "Tipos de produtos";
$key_tiposprodutos["en"] = "";
$key_gerente["pt"] = "Gerente";
$key_gerente["en"] = "";
$key_gerente_nome["pt"] = "Nome";
$key_gerente_nome["en"] = "";
$key_gerente_email["pt"] = "E-mail";
$key_gerente_email["en"] = "";
$key_gerente_telefone["pt"] = "Telefone";
$key_gerente_telefone["en"] = "";
$key_gerente_skype["pt"] = "Skype";
$key_gerente_skype["en"] = "";
$key_comercial["pt"] = "Comercial";
$key_comercial["en"] = "";
$key_comercial_nome["pt"] = "Nome";
$key_comercial_nome["en"] = "";
$key_comercial_email["pt"] = "E-mail";
$key_comercial_email["en"] = "";
$key_comercial_telefone["pt"] = "Telefone";
$key_comercial_telefone["en"] = "";
$key_comercial_skype["pt"] = "Skype";
$key_comercial_skype["en"] = "";
$key_financeiro["pt"] = "Financeiro";
$key_financeiro["en"] = "";
$key_financeiro_nome["pt"] = "Nome";
$key_financeiro_nome["en"] = "";
$key_financeiro_email["pt"] = "E-mail";
$key_financeiro_email["en"] = "";
$key_financeiro_telefone["pt"] = "Telefone";
$key_financeiro_telefone["en"] = "";
$key_financeiro_skype["pt"] = "Skype";
$key_financeiro_skype["en"] = "";
$key_banco["pt"] = "Banco";
$key_banco["en"] = "";
$key_agencia["pt"] = "Agência / Balcão";
$key_agencia["en"] = "";
$key_nconta["pt"] = "N.º de conta";
$key_nconta["en"] = "";
$key_termos["pt"] = "li e Aceito os Termos e Condições";
$key_termos["en"] = "";
















$key_manutencao['pt'] = 'O website  está em manutenção.';
$key_manutencao['ch'] = 'The website  is down for maintenance.';
$key_manutencao['en'] = 'The website  is down for maintenance.';
$key_manutencao['fr'] = 'Le site  est en maintenance.';
$key_manutencao['es'] = 'El sitio  está desactivado por mantenimiento.';
$key_manutencao['de'] = 'Die Website  ist im Wartungsmodus.';
$key_manutencao['it'] = 'Il sito web  fuori servizio per manutenzione.';

$key_selecione['pt'] = 'Selecione';
$key_selecione['ch'] = 'Select';
$key_selecione['en'] = 'Select';
$key_selecione['fr'] = 'Selecione';
$key_selecione['es'] = 'Selecione';
$key_selecione['de'] = 'Selektieren';
$key_selecione['it'] = 'Selezionare';

$key_ordernar['pt'] = 'Ordenar por';
$key_ordernar['ch'] = 'Order by';
$key_ordernar['en'] = 'Order by';
$key_ordernar['fr'] = 'Selecione';
$key_ordernar['es'] = 'Selecione';
$key_ordernar['de'] = 'Sortieren nach';
$key_ordernar['it'] = 'Ordina per';

$key_exibir['pt'] = 'exibir';
$key_exibir['ch'] = 'show';
$key_exibir['en'] = 'show';
$key_exibir['fr'] = 'afficher';
$key_exibir['es'] = 'visualización';
$key_exibir['de'] = 'anzeigen';
$key_exibir['it'] = 'display';

$key_resultados['pt'] = 'Resultados';
$key_resultados['ch'] = 'Results';
$key_resultados['en'] = 'Results';
$key_resultados['fr'] = 'Résultats';
$key_resultados['es'] = 'Resultados';
$key_resultados['de'] = 'Ergebnisse';
$key_resultados['it'] = 'Risultati';

$key_vermais['pt'] = 'Ver mais';
$key_vermais['ch'] = 'See more';
$key_vermais['en'] = 'See more';
$key_vermais['fr'] = 'Voir plus';
$key_vermais['es'] = 'Ver más';
$key_vermais['de'] = 'Mehr anzeigen';
$key_vermais['it'] = 'Leggi di piú';

$key_comprar['pt'] = 'Comprar';
$key_comprar['ch'] = 'Buy';
$key_comprar['en'] = 'Buy';
$key_comprar['fr'] = 'Acheter';
$key_comprar['es'] = 'Comprar';
$key_comprar['de'] = 'Kaufen';
$key_comprar['it'] = 'Acquista';

$key_adicionar['pt'] = 'ADICIONAR';
$key_adicionar['ch'] = 'Add to cart';
$key_adicionar['en'] = 'Add';
$key_adicionar['fr'] = 'Ajouter';
$key_adicionar['es'] = 'Añadir';
$key_adicionar['de'] = 'Hinzufügen';
$key_adicionar['it'] = 'Aggiungere';

$key_alertastock['pt'] = 'Alerta de stock';
$key_alertastock['ch'] = 'Stock Alert';
$key_alertastock['en'] = 'Stock Alert';
$key_alertastock['fr'] = 'Stock Alert';
$key_alertastock['es'] = 'Stock Alert';
$key_alertastock['de'] = 'Stock Alert';
$key_alertastock['it'] = 'Stock Alert';

$key_detalhes['pt'] = 'Detalhes';
$key_detalhes['ch'] = 'Details';
$key_detalhes['en'] = 'Details';
$key_detalhes['fr'] = 'Details';
$key_detalhes['es'] = 'Details';
$key_detalhes['de'] = 'Details';
$key_detalhes['it'] = 'Details';

$txt_populares['pt'] = 'Populares';
$txt_populares['ch'] = 'Popular';
$txt_populares['en'] = 'Popular';
$txt_populares['fr'] = 'Popular';
$txt_populares['es'] = 'Popular';
$txt_populares['de'] = 'Popular';
$txt_populares['it'] = 'Popular';

$txt_mais_vendidos['pt'] = 'Mais vendidos';
$txt_mais_vendidos['ch'] = 'Bestsellers';
$txt_mais_vendidos['en'] = 'Bestsellers';
$txt_mais_vendidos['fr'] = 'Bestsellers';
$txt_mais_vendidos['es'] = 'Bestsellers';
$txt_mais_vendidos['de'] = 'Bestsellers';
$txt_mais_vendidos['it'] = 'Bestsellers';


$key_inicio['pt'] = 'Home';
$key_inicio['ch'] = 'Home';
$key_inicio['en'] = 'Home';
$key_inicio['fr'] = 'Home';
$key_inicio['es'] = 'Home';
$key_inicio['de'] = 'Home';
$key_inicio['it'] = 'Home';

$key_novidades['pt'] = 'Novos Produtos';
$key_novidades['ch'] = 'New Products';
$key_novidades['en'] = 'New Products';
$key_novidades['fr'] = 'Nouveaux Produits';
$key_novidades['es'] = 'Nuevos Productos';
$key_novidades['de'] = 'Neue Produkte';
$key_novidades['it'] = 'Nuovi Prodotti';

$key_destaques['pt'] = 'Destaques';
$key_destaques['ch'] = 'Highlights';
$key_destaques['en'] = 'Highlights';
$key_destaques['fr'] = 'Faits saillants';
$key_destaques['es'] = 'Destacados';
$key_destaques['de'] = 'Höhepunkte';
$key_destaques['it'] = 'In evidenza';

$key_modelo['pt'] = 'MODELO';
$key_modelo['ch'] = 'MODEL';
$key_modelo['en'] = 'MODEL';
$key_modelo['fr'] = 'MODÈLE';
$key_modelo['es'] = 'MODELO';
$key_modelo['de'] = 'MODELL';
$key_modelo['it'] = 'MODELLO';

$key_modelo2['pt'] = 'Modelo';
$key_modelo2['ch'] = 'Model';
$key_modelo2['en'] = 'Model';
$key_modelo2['fr'] = 'Modèle';
$key_modelo2['es'] = 'Modelo';
$key_modelo2['de'] = 'Modell';
$key_modelo2['it'] = 'Modello';


$key_material['pt'] = 'Material';
$key_material['ch'] = 'Material';
$key_material['en'] = 'Material';
$key_material['fr'] = 'Matériel';
$key_material['es'] = 'Material';
$key_material['de'] = 'Material';
$key_material['it'] = 'Materiale';

$key_iva['pt'] = 'IVA incluído';
$key_iva['ch'] = 'VAT included';
$key_iva['en'] = 'VAT included';
$key_iva['fr'] = 'TVA incluse';
$key_iva['es'] = 'IVA incluido';
$key_iva['de'] = 'Inkl. MwSt.';
$key_iva['it'] = 'IVA inclusa';

$key_emstock['pt'] = 'em stock';
$key_emstock['ch'] = 'in stock';
$key_emstock['en'] = 'in stock';
$key_emstock['fr'] = 'en stock';
$key_emstock['es'] = 'en stock';
$key_emstock['de'] = 'auf Lager';
$key_emstock['it'] = 'in magazzino';

$key_opcoes['pt'] = 'Opções';
$key_opcoes['ch'] = 'Options';
$key_opcoes['en'] = 'Options';
$key_opcoes['fr'] = 'Options';
$key_opcoes['es'] = 'Opciones';
$key_opcoes['de'] = 'Optionen';
$key_opcoes['it'] = 'Opzioni';


$key_bem_vindo_auntentiquese['pt'] = 'Bem vindo, autentique-se';
$key_bem_vindo_auntentiquese['ch'] = 'Welcome, log in';
$key_bem_vindo_auntentiquese['en'] = 'Welcome, log in';
$key_bem_vindo_auntentiquese['fr'] = 'Bienvenue, connectez-vous';
$key_bem_vindo_auntentiquese['es'] = 'Bienvenido, inicia lá sesión';
$key_bem_vindo_auntentiquese['de'] = 'Willkommen, einloggen';
$key_bem_vindo_auntentiquese['it'] = 'Willkommen, einloggen';

$key_cliente_registado['pt'] = 'Cliente registado';
$key_cliente_registado['ch'] = 'Registered client';
$key_cliente_registado['en'] = 'Registered client';
$key_cliente_registado['fr'] = 'Client enregistré';
$key_cliente_registado['es'] = 'Cliente registrado';
$key_cliente_registado['de'] = 'Kunde bereits registriert';
$key_cliente_registado['it'] = 'Cliente registrato';

$key_cliente_novo['pt'] = 'Novo Cliente';
$key_cliente_novo['ch'] = 'New Client';
$key_cliente_novo['en'] = 'New Client';
$key_cliente_novo['fr'] = 'Nouveau Client';
$key_cliente_novo['es'] = 'Nuevo Cliente';
$key_cliente_novo['de'] = 'Neuer  Kunde';
$key_cliente_novo['it'] = 'Nuovo cliente';

$key_cliente_novo_texto['pt'] = 'Sou um novo cliente';
$key_cliente_novo_texto['ch'] = 'I am a new client';
$key_cliente_novo_texto['en'] = 'I am a new client';
$key_cliente_novo_texto['fr'] = 'Je suis un nouveau client';
$key_cliente_novo_texto['es'] = 'Soy un nuevo cliente';
$key_cliente_novo_texto['de'] = 'Ich bin ein neuer Kunde';
$key_cliente_novo_texto['it'] = 'Io sono un nuovo cliente';

$key_cliente_novo_texto2['pt'] = 'Ao registar-se como cliente no nosso site poderá efetuar as suas compras de uma forma rápida e cómoda, para além de consultar o estado e histórico das suas encomendas.';
$key_cliente_novo_texto2['ch'] = 'By registering as a client on our website you can make your purchases quickly and conveniently, in addition to querying the status and history of your orders.';
$key_cliente_novo_texto2['en'] = 'By registering as a client on our website you can make your purchases quickly and conveniently, in addition to querying the status and history of your orders.';
$key_cliente_novo_texto2['fr'] = 'En vous inscrivant comme un client  sur notre site Internet, vous pouvez faire vos achats vite et commodément. Par ailleurs, vous pouvez obtenir des informations sur l\'état et l\'enregistrement de vos commandes.';
$key_cliente_novo_texto2['es'] = 'Al registrarse como cliente en nuestro sitio web usted puede hacer sus compras rápida y cómodamente, además de consultar el estado y la historia de sus pedidos.';
$key_cliente_novo_texto2['de'] = 'Indem Sie sich als ein Kunde auf unserer Website einschreiben, können Sie Ihre Käufe schnell und günstig machen und  auch über des Status Ihrer Aufträge fragen.';
$key_cliente_novo_texto2['it'] = 'Registrandovi come cliente sul nostro sito puoi effettuare il tuo acquisti rapidamente e comodamente oltre l\'esecuzione di query lo stato e la cronologia dei tuoi ordini.';


$key_cliente_auten_dados['pt'] = '<p>Autentique-se com as suas credenciais.</p>';
$key_cliente_auten_dados['ch'] = '<p>Login</p>';
$key_cliente_auten_dados['en'] = '<p>Login</p>';
$key_cliente_auten_dados['fr'] = '<p>Identification</p>';
$key_cliente_auten_dados['es'] = '<p>Iniciar sesión</p>';
$key_cliente_auten_dados['de'] = '<p>Login</p>';
$key_cliente_auten_dados['it'] = '<p>Login</p>';


/* AFILIADOS */

$key_bem_vindo_auntentiquese_afl['pt'] = 'Afiliados';
$key_bem_vindo_auntentiquese_afl['ch'] = 'Affiliates';
$key_bem_vindo_auntentiquese_afl['en'] = 'Affiliates';
$key_bem_vindo_auntentiquese_afl['fr'] = 'Affiliés';
$key_bem_vindo_auntentiquese_afl['es'] = 'Afiliados';
$key_bem_vindo_auntentiquese_afl['de'] = 'Affiliates';
$key_bem_vindo_auntentiquese_afl['it'] = 'Affiliati';

$key_cliente_registado_afl['pt'] = 'LOGIN DE ASSOCIADO';
$key_cliente_registado_afl['ch'] = 'ASSOCIATE LOGIN';
$key_cliente_registado_afl['en'] = 'ASSOCIATE LOGIN';
$key_cliente_registado_afl['fr'] = 'LOGIN ASSOCIÉ';
$key_cliente_registado_afl['es'] = 'ACCESSO SOCIO';
$key_cliente_registado_afl['de'] = 'MITGLIED LOGIN';
$key_cliente_registado_afl['it'] = 'LOGIN ASSOCIATE';

$key_cliente_novo_afl['pt'] = 'NOVO ASSOCIADO';
$key_cliente_novo_afl['ch'] = 'NEW ASSOCIATE';
$key_cliente_novo_afl['en'] = 'NEW ASSOCIATE';
$key_cliente_novo_afl['fr'] = 'NOUVEL ASSOCIÉ';
$key_cliente_novo_afl['es'] = 'NUEVO ASOCIADO';
$key_cliente_novo_afl['de'] = 'NEUES MITGLIED';
$key_cliente_novo_afl['it'] = 'NUOVO SOCIO';

$key_cliente_novo_texto_afl['pt'] = 'EU NÃO SOU UM ASSOCIADO.';
$key_cliente_novo_texto_afl['ch'] = 'CURRENTLY I\'M NOT AN ASSOCIATE.';
$key_cliente_novo_texto_afl['en'] = 'CURRENTLY I\'M NOT AN ASSOCIATE.';
$key_cliente_novo_texto_afl['fr'] = 'JE NE SUIS PAS ASSOCIÉ.';
$key_cliente_novo_texto_afl['es'] = 'NO SOY UN ASOCIADO';
$key_cliente_novo_texto_afl['de'] = 'ICH BIN KEIN MITGLIED';
$key_cliente_novo_texto_afl['it'] = 'ATTUALMENTE IO NON SONO SOCIO.';

$key_cliente_novo_texto2_afl['pt'] = 'Clique abaixo em Continuar para criar uma nova conta de associado. Por favor, note que esta conta não tem vínculo com a sua conta de cliente.';
$key_cliente_novo_texto2_afl['ch'] = 'Click below in Continue to create a new affiliate account. Please note that this account is not related to your client account.';
$key_cliente_novo_texto2_afl['en'] = 'Click below in Continue to create a new affiliate account. Please note that this account is not related to your client account.';
$key_cliente_novo_texto2_afl['fr'] = 'Cliquez sur Continuer ci-dessous pour créer un nouveau compte de membre. Veuillez noter que ce compte est pas lié à votre compte client.';
$key_cliente_novo_texto2_afl['es'] = 'Haga clic abajo en Continuar, creando una nueva cuenta de afiliado. Por favor note que esta cuenta no se relaciona con su cuenta de cliente.';
$key_cliente_novo_texto2_afl['de'] = 'Klicken Sie auf weiter unten, um ein neues Mitglied Konto zu erstellen. Bitte beachten Sie, dass dieses Konto nicht mit Ihrem Kundenkonto verbunden ist.';
$key_cliente_novo_texto2_afl['it'] = 'Clicca qui sotto nel continuare per creare un nuovo account d’affiliati. Questo account non è legato al tuo account cliente.';

$key_cliente_auten_dados_afl['pt'] = 'JÁ SOU UM ASSOCIADO';
$key_cliente_auten_dados_afl['ch'] = 'I AM ALREADY A MEMBER';
$key_cliente_auten_dados_afl['en'] = 'I AM ALREADY A MEMBER';
$key_cliente_auten_dados_afl['fr'] = 'JE SUIS DÉJÀ UN ASSOCIÉ';
$key_cliente_auten_dados_afl['es'] = 'YA SOY USUARIO REGISTRADO';
$key_cliente_auten_dados_afl['de'] = 'ICH BIN BEREITS MITGLIED';
$key_cliente_auten_dados_afl['it'] = 'GIÀ SONO UN ASSOCIATO';
//-------------------

$key_cliente_perder_pass['pt'] = '<p>Se perdeu a sua Password, introduza o seu E-Mail e preencha o código de confirmação para que possamos enviar-lhe uma nova password.</p>';
$key_cliente_perder_pass['ch'] = '<p>If you have lost your password, please enter your E-Mail and fill the confirmation code so we can send you a new passoword.</p>';
$key_cliente_perder_pass['en'] = '<p>If you have lost your password, please enter your E-Mail and fill the confirmation code so we can send you a new passoword.</p>';
$key_cliente_perder_pass['fr'] = '<p>Si vous avez perdu votre mot de passe, entrez votre email et remplissez le code de confirmation afin que nous puissions vous envoyer un nouveau mot de passe.</p>';
$key_cliente_perder_pass['es'] = '<p>Si has perdido tu contraseña, introduce tu email y completar el código de confirmación para que podamos enviarle una nueva contraseña.</p>';
$key_cliente_perder_pass['de'] = '<p>Wenn Sie Ihr Passwort vergessen haben, geben Sie Ihre e-Mail und füllen Sie den Bestätigungscode, damit wir Ihnen ein neues Passwort zusenden können.</p>';
$key_cliente_perder_pass['it'] = '<p>Se hai perso la Password, Inserisci la tua email e completare il codice di conferma, in modo che possiamo inviare una nuova password.</p>';

$key_cliente_perder_pass_conf['pt'] = 'Código Confirmação';
$key_cliente_perder_pass_conf['ch'] = 'Confirmation Code';
$key_cliente_perder_pass_conf['en'] = 'Confirmation Code';
$key_cliente_perder_pass_conf['fr'] = 'Code de confirmation';
$key_cliente_perder_pass_conf['es'] = 'Código de confirmación';
$key_cliente_perder_pass_conf['de'] = 'Bestätigungs-Code';
$key_cliente_perder_pass_conf['it'] = 'Codice di conferma';

$key_captcha1['pt'] = 'Altera';
$key_captcha1['ch'] = 'Change';
$key_captcha1['en'] = 'Change';
$key_captcha1['fr'] = 'Changement';
$key_captcha1['es'] = 'Cambio';
$key_captcha1['de'] = 'Veränderung';
$key_captcha1['it'] = 'Modifiche';

$key_continuar['pt'] = 'Continuar';
$key_continuar['ch'] = 'Continue';
$key_continuar['en'] = 'Continue';
$key_continuar['fr'] = 'Continuer';
$key_continuar['es'] = 'Continuar';
$key_continuar['de'] = 'Weiter';
$key_continuar['it'] = 'Continuare';

$key_voltar['pt'] = 'Voltar';
$key_voltar['ch'] = 'Back';
$key_voltar['en'] = 'Back';
$key_voltar['fr'] = 'Retour';
$key_voltar['es'] = 'Volver';
$key_voltar['de'] = 'Zurück';
$key_voltar['it'] = 'Ritorno';

$key_conta_pessoal['pt'] = 'Conta Pessoal';
$key_conta_pessoal['ch'] = 'Personal Account';
$key_conta_pessoal['en'] = 'Personal Account';
$key_conta_pessoal['fr'] = 'Compte personnel';
$key_conta_pessoal['es'] = 'Cuenta Personal';
$key_conta_pessoal['de'] = 'Persönliche Daten';
$key_conta_pessoal['it'] = 'Account personale';

$key_nota_registo['pt'] = '<p><font color="#FF0000"><small><strong>NOTA:</strong></small></font> Se já possui uma Conta Pessoal, por favor introduza as suas credenciais <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>clica aqui</u></a>.</p>';
$key_nota_registo['ch'] = '<p><font color="#FF0000"><small><strong>NOTE:</strong></small></font> If you already have a personal account, please enter your credentials <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>click here</u></a>.</p>';
$key_nota_registo['en'] = '<p><font color="#FF0000"><small><strong>NOTE:</strong></small></font> If you already have a personal account, please enter your credentials <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>click here</u></a>.</p>';
$key_nota_registo['fr'] = '<p><font color="#FF0000"><small><strong>REMARQUE:</strong></small></font> Si vous avez déjà un compte personnel, veuillez saisir vos identifiants <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>cliquez ici</u></a>.</p>';
$key_nota_registo['es'] = '<p><font color="#FF0000"><small><strong>NOTA:</strong></small></font> Si ya tienes una cuenta personal, por favor ingrese sus credenciales, haga <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>clic en aquí</u></a>.</p>';
$key_nota_registo['de'] = '<p><font color="#FF0000"><small><strong>HINWEIS:</strong></small></font> Wenn Sie bereits ein persönliches Konto haben, bitte geben Sie Ihre Anmeldeinformationen <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>klicken Sie hier</u></a>.</p>';
$key_nota_registo['it'] = '<p><font color="#FF0000"><small><strong>NOTA:</strong></small></font> Se hai già un account personale, si prega di inserire le proprie credenziali <a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><u>Clicca qui</u></a>.</p>';

$key_registar['pt'] = 'Registar';
$key_registar['ch'] = 'Register';
$key_registar['en'] = 'Register';
$key_registar['fr'] = 'S\'inscrire';
$key_registar['es'] = 'Registro';
$key_registar['de'] = 'Registrieren';
$key_registar['it'] = 'Registro';

$key_carrinho1['pt'] = 'Carrinho';
$key_carrinho1['ch'] = 'Cart';
$key_carrinho1['en'] = 'Cart';
$key_carrinho1['fr'] = 'Panier';
$key_carrinho1['es'] = 'Cesto';
$key_carrinho1['de'] = 'Korb';
$key_carrinho1['it'] = 'Carrello';

$key_encomendas['pt'] = 'Encomendas';
$key_encomendas['ch'] = 'Orders';
$key_encomendas['en'] = 'Orders';
$key_encomendas['fr'] = 'Ordres';
$key_encomendas['es'] = 'Órdenes';
$key_encomendas['de'] = 'Bestellungen';
$key_encomendas['it'] = 'Ordini';

$key_imagem1['pt'] = 'Imagem';
$key_imagem1['ch'] = 'Image';
$key_imagem1['en'] = 'Image';
$key_imagem1['fr'] = 'Image';
$key_imagem1['es'] = 'Imagen';
$key_imagem1['de'] = 'Bild';
$key_imagem1['it'] = 'Immagine';

$key_nome['pt'] = 'Nome';
$key_nome['ch'] = 'Name';
$key_nome['en'] = 'Name';
$key_nome['fr'] = 'Nom';
$key_nome['es'] = 'Nombre';
$key_nome['de'] = 'Vorname';
$key_nome['it'] = 'Nome';

$key_quantidade['pt'] = 'Quantidade';
$key_quantidade['ch'] = 'Quantity';
$key_quantidade['en'] = 'Quantity';
$key_quantidade['fr'] = 'Quantité';
$key_quantidade['es'] = 'Cantidad';
$key_quantidade['de'] = 'Menge';
$key_quantidade['it'] = 'Quantità';

$key_entrega['pt'] = '3 a 4 dias úteis';
$key_entrega['ch'] = 'About a week for production and delivery';
$key_entrega['en'] = 'About a week for production and delivery';
$key_entrega['fr'] = 'Environ une semaine pour la production et la livraison';
$key_entrega['es'] = 'Cerca de una semana para producción y entrega';
$key_entrega['de'] = 'Etwa eine Woche für die Produktion und Lieferung';
$key_entrega['it'] = 'Circa una settimana per la produzione e consegna';

$key_sepretender['pt'] = 'Além destas cores, poderemos ainda fazer num tecido à sua escolha. <br/> Teremos todo o gosto em ir de encontro às suas preferências. A nossa equipa está ao seu dispor!';
$key_sepretender['ch'] = 'Besides these colours, we are able to produce the bows in a fabric of your choice. <br/> We will be pleased to meet your requirements. Our team is at your disposal!';
$key_sepretender['en'] = 'Besides these colours, we are able to produce the bows in a fabric of your choice. <br/> We will be pleased to meet your requirements. Our team is at your disposal!';
$key_sepretender['fr'] = 'En plus de ces couleurs, nous pouvons toujours faire un tissu de votre choix. <br/> Nous serions heureux de répondre à vos préférences. Notre équipe est à votre disposition!';
$key_sepretender['es'] = 'Además de estos colores, podemos todavía hacer una tela de su elección. <br/> estaremos encantados de satisfacer sus preferencias. Nuestro equipo está a su disposición.';
$key_sepretender['de'] = 'Neben diesen Farben sind wir in der Lage, um die Bögen in einem Stoff Ihrer Wahl zu erzeugen. <br/> Wir freuen uns auf Ihre Anforderungen zu erfüllen. Unser Team ist für Sie da!';
$key_sepretender['it'] = 'Oltre a questi colori, possiamo ancora fare un tessuto di vostra scelta. <br/> saremo lieti di soddisfare le vostre preferenze. Il nostro team è a vostra disposizione!';

$key_sepretender2['pt'] = 'Veja as cores disponíveis';
$key_sepretender2['ch'] = 'Take a look at the available colours';
$key_sepretender2['en'] = 'Take a look at the available colours';
$key_sepretender2['fr'] = 'Voir ici les couleurs disponibles';
$key_sepretender2['es'] = 'Vea aqui los colores disponibles';
$key_sepretender2['de'] = 'Sehen Sie hier die verfügbare Farben an';
$key_sepretender2['it'] = 'Vedere I colori disponibili';

$key_sepretender3['pt'] = 'No caso de pretender optar pela cor vermelha, existe sempre disponibilidade em stock. Se pretender outras cores, o prazo de entrega é mais alargado.';
$key_sepretender3['ch'] = 'If you opt for the red color, there is always availability in stock. If you want other colors, the delivery time is longer.';
$key_sepretender3['en'] = 'If you opt for the red color, there is always availability in stock. If you want other colors, the delivery time is longer.';
$key_sepretender3['fr'] = 'Si vous optez pour la couleur rouge, il y a toujours la disponibilité en stock. Si vous voulez d\'autres couleurs, le délai de livraison est plus long.';
$key_sepretender3['es'] = 'Si se opta por el color rojo, siempre hay disponibilidad en stock. Si desea otros colores, el tiempo de entrega es más largo.';
$key_sepretender3['de'] = 'Wenn Sie die rote Farbe wollen, gibt es immer auf Lager. Wenn Sie andere Farben zu wählen, ist die Lieferfrist länger.';
$key_sepretender3['it'] = 'Se si opta per il colore rosso, c\'è sempre disponibilità in magazzino. Se volete altri colori, il tempo di consegna è più lungo.';

$key_escreva['pt'] = 'Escreva uma mensagem no laço.';
$key_escreva['ch'] = 'Write a message on the Bow.';
$key_escreva['en'] = 'Write a message on the Bow.';
$key_escreva['fr'] = 'Ecrivez dans la Bow.';
$key_escreva['es'] = 'Escribir un mensaje en el Bow.';
$key_escreva['de'] = 'Schreibe eine Nachricht auf dem Bow.';
$key_escreva['it'] = 'Scrivi un messaggio sul Bow.'; //TODO: Alterar o texto para a lingua;

$key_escreva2['pt'] = '» veja aqui um exemplo';
$key_escreva2['ch'] = '» see here an example';
$key_escreva2['en'] = '» see here an example';
$key_escreva2['fr'] = '» voir ici un exemple';
$key_escreva2['es'] = '» vea aquí un ejemplo';
$key_escreva2['de'] = '» hier sehen Sie ein Beispiel';
$key_escreva2['it'] = '» vedi qui un esempio';

$key_formato['pt'] = 'Formato';
$key_formato['ch'] = 'Format';
$key_formato['en'] = 'Format';
$key_formato['fr'] = 'Formato';
$key_formato['es'] = 'Formato';
$key_formato['de'] = 'Format';
$key_formato['it'] = 'Formato';

$key_preco['pt'] = 'Preço';
$key_preco['ch'] = 'Price';
$key_preco['en'] = 'Price';
$key_preco['fr'] = 'Prix';
$key_preco['es'] = 'Precio';
$key_preco['de'] = 'Preis';
$key_preco['it'] = 'Prezzo';

$key_atualizar['pt'] = 'Atualizar';
$key_atualizar['ch'] = 'Refresh';
$key_atualizar['en'] = 'Refresh';
$key_atualizar['fr'] = 'Actualiser';
$key_atualizar['es'] = 'Refrescar';
$key_atualizar['de'] = 'Aktualisieren';
$key_atualizar['it'] = 'Rinfrescare';

$key_finalizar_encomenda['pt'] = 'FINALIZAR COMPRA';
$key_finalizar_encomenda['ch'] = 'CHECKOUT';
$key_finalizar_encomenda['en'] = 'CHECKOUT';
$key_finalizar_encomenda['fr'] = 'CHECK-OUT';
$key_finalizar_encomenda['es'] = 'PEDIDO COMPLETO';
$key_finalizar_encomenda['de'] = 'ZUR KASSE';
$key_finalizar_encomenda['it'] = 'CHECK-OUT';

$key_finalizar_encomenda2['pt'] = 'Finalizar Encomenda';
$key_finalizar_encomenda2['ch'] = 'Checkout';
$key_finalizar_encomenda2['en'] = 'Checkout';
$key_finalizar_encomenda2['fr'] = 'Check-out';
$key_finalizar_encomenda2['de'] = 'Auschecken';
$key_finalizar_encomenda2['it'] = 'Check-out';

$key_apelido['pt'] = "Apelido";
$key_apelido['ch'] = "Surname";
$key_apelido['en'] = "Surname";
$key_apelido['fr'] = "Prénom";
$key_apelido['es'] = "Apellido";
$key_apelido['de'] = "Nachname";
$key_apelido['it'] = "Cognome";

$key_empresa['pt'] = "Empresa";
$key_empresa['ch'] = "Company";
$key_empresa['en'] = "Company";
$key_empresa['fr'] = "Compagnie";
$key_empresa['es'] = "Empresa";
$key_empresa['de'] = "Unternehmen";
$key_empresa['it'] = "Affari";

$key_morada['pt'] = "Morada";
$key_morada['ch'] = "Address";
$key_morada['en'] = "Address";
$key_morada['fr'] = "Adresse";
$key_morada['es'] = "Dirección";
$key_morada['de'] = "Adresse";
$key_morada['it'] = "Indirizzo";

$key_codpos['pt'] = "Código Postal";
$key_codpos['ch'] = "ZIP CODE";
$key_codpos['en'] = "ZIP CODE";
$key_codpos['fr'] = "Code postal";
$key_codpos['es'] = "Código Postal";
$key_codpos['de'] = "Die Postleitzahl";
$key_codpos['it'] = "Codice postale";

$key_localidade['pt'] = "Localidade";
$key_localidade['ch'] = "City";
$key_localidade['en'] = "City";
$key_localidade['fr'] = "Ville";
$key_localidade['es'] = "Ciudad";
$key_localidade['de'] = "Ortschaft";
$key_localidade['it'] = "Città";

$key_tel['pt'] = "Telefone";
$key_tel['ch'] = "Phone";
$key_tel['en'] = "Phone";
$key_tel['fr'] = "Téléphone";
$key_tel['es'] = "Teléfono";
$key_tel['de'] = "Telefon";
$key_tel['it'] = "Telefono";

$key_fax['pt'] = "Fax";
$key_fax['ch'] = "Fax";
$key_fax['en'] = "Fax";
$key_fax['fr'] = "Télécopieur";
$key_fax['es'] = "Fax";
$key_fax['de'] = "Telefax";
$key_fax['it'] = "Fax";

$key_movel['pt'] = "Telem&oacute;vel";
$key_movel['ch'] = "Mobile Phone";
$key_movel['en'] = "Mobile Phone";
$key_movel['fr'] = "Téléphone";
$key_movel['es'] = "Teléfono";
$key_movel['de'] = "Handy";
$key_movel['it'] = "Telefono";

$key_nif['pt'] = "N&ordm; Contribuinte";
$key_nif['ch'] = "Identity Card n°";
$key_nif['en'] = "Identity Card n°";
$key_nif['fr'] = "Numéro de carte d'identité";
$key_nif['es'] = "Número de Tarjeta de Identidade";
$key_nif['de'] = "Personalausweisnummer";
$key_nif['it'] = "Numero di carta di identità";

$key_doc_identidade['pt'] = "N&ordm; Contribuinte";
$key_doc_identidade['ch'] = "Identity Card n°";
$key_doc_identidade['en'] = "Identity Card n°";
$key_doc_identidade['fr'] = "Numéro de carte d'identité";
$key_doc_identidade['es'] = "Número de Tarjeta de Identidade";
$key_doc_identidade['de'] = "Personalausweisnummer";
$key_doc_identidade['it'] = "Numero di carta di identità";

$key_doc_identidade2['pt'] = "Passaporte";
$key_doc_identidade2['ch'] = "Passport";
$key_doc_identidade2['en'] = "Passport";
$key_doc_identidade2['fr'] = "Passeport";
$key_doc_identidade2['es'] = "Pasaporte";
$key_doc_identidade2['de'] = "Pass";
$key_doc_identidade2['it'] = "Passaporto";

$key_doc_identidade3['pt'] = "Carta de Condução";
$key_doc_identidade3['ch'] = "Driver's license";
$key_doc_identidade3['en'] = "Driver's license";
$key_doc_identidade3['fr'] = "Conduire permis de";
$key_doc_identidade3['es'] = "Licencia de conducir";
$key_doc_identidade3['de'] = "Führerschein";
$key_doc_identidade3['it'] = "Patente di guida";

$key_datanasc['pt'] = "Data de nascimento";
$key_datanasc['ch'] = "Date of birth";
$key_datanasc['en'] = "Date of birth";
$key_datanasc['fr'] = "Date de naissance";
$key_datanasc['es'] = "Fecha de nacimiento";
$key_datanasc['de'] = "Geburtsdatum";
$key_datanasc['it'] = "Data di nascita";

$key_validacao['pt'] = "Código Validação";
$key_validacao['ch'] = "Validation Code";
$key_validacao['en'] = "Validation Code";
$key_validacao['fr'] = "Code de validation";
$key_validacao['es'] = "Código de validación";
$key_validacao['de'] = "Bestätigungs-Code";
$key_validacao['it'] = "Codice di convalida";

$key_email['pt'] = "Email";
$key_email['ch'] = "Email";
$key_email['en'] = "Email";
$key_email['fr'] = "Email";
$key_email['es'] = "Email";
$key_email["de"] = "Email";
$key_email["it"] = "Email";

$key_email_afl['pt'] = "Email de Associado";
$key_email_afl['ch'] = "Associate Email";
$key_email_afl['en'] = "Associate Email";
$key_email_afl['fr'] = "E-mail associé";
$key_email_afl['es'] = "Correo electrónico asociado";
$key_email_afl["de"] = "Mitglied e.mail";
$key_email_afl["it"] = "Posta elettronica associato";

$key_password['pt'] = "Password";
$key_password['ch'] = "Password";
$key_password['en'] = "Password";
$key_password['fr'] = "Mot de passe";
$key_password['es'] = "Contraseña";
$key_password['de'] = "Passwort";
$key_password['it'] = "Password";

$key_repetepassword['pt'] = "Repetir Password";
$key_repetepassword['ch'] = "Repeat Password";
$key_repetepassword['en'] = "Repeat Password";
$key_repetepassword['fr'] = "Répétez mot de passe";
$key_repetepassword['es'] = "Repetir contraseña";
$key_repetepassword['de'] = "Passwort wiederholen";
$key_repetepassword['it'] = "Confermare la password";

$key_spam['pt'] = "Prevenção de spam";
$key_spam['ch'] = "Spam prevention";
$key_spam['en'] = "Spam prevention";
$key_spam['fr'] = "Prévention du spam";
$key_spam['es'] = "Prevención del spam";
$key_spam['de'] = "Schutz vor Spam";
$key_spam['it'] = "Prevenzione spam";

$key_spam2['pt'] = "Por favor insira o código que está a visualizar e clique em 'Confirmar'. Este passo adicional é necessário para nos ajudar a impedir o envio de mensagens de spam.";
$key_spam2['ch'] = "Please enter the code that you are viewing and click 'Confirm'. This additional step is necessary to help us prevent spam messages.";
$key_spam2['en'] = "Please enter the code that you are viewing and click 'Confirm'. This additional step is necessary to help us prevent spam messages.";
$key_spam2['fr'] = "Veuillez entrer le code que vous voyez et cliquez sur 'Confirmer'. Cette étape supplémentaire est nécessaire pour nous aider à empêcher les messages de spam.";
$key_spam2['es'] = "Por favor ingrese el código que usted está viendo y haga clic en 'Confirmar'. Este paso adicional es necesario para prevenir mensajes de spam.";
$key_spam2['de'] = "Geben Sie den Code anzeigen und klicken Sie auf 'Bestätigen'. Dieser zusätzliche Schritt ist notwendig, uns die Spam-Nachrichten zu verhindern helfen.";
$key_spam2['it'] = "Inserisci il codice che si sta visualizzando e clicca su 'Conferma'. Questo ulteriore passaggio è necessario per impedire ai messaggi di spam.";

$key_spam3['pt'] = "Insira o código";
$key_spam3['ch'] = "Enter code";
$key_spam3['en'] = "Enter code";
$key_spam3['fr'] = "Entrez le code";
$key_spam3['es'] = "Introduzca el código";
$key_spam3['de'] = "Schreiben Sie den Code";
$key_spam3['it'] = "Inserire il codice";

$key_captcha1['pt'] = "Clique aqui se não consegue visualizar o código";
$key_captcha1['ch'] = "Click here if you can't view the code.";
$key_captcha1['en'] = "Click here if you can't view the code.";
$key_captcha1['fr'] = "Cliquez ici si vous ne pouvez pas afficher le code.";
$key_captcha1['es'] = "Haga clic aquí si no puede ver el código.";
$key_captcha1['de'] = "Klicken Sie hier, wenn Sie nicht den Code erkennen.";
$key_captcha1['it'] = "Clicca qui se non è possibile visualizzare il codice.";

$key_captcha2['pt'] = "Por favor corrija os campos a cor.";
$key_captcha2['ch'] = "Please correct the coloured fields.";
$key_captcha2['en'] = "Please correct the coloured fields.";
$key_captcha2['fr'] = "Veuillez corriger les champs en couleur.";
$key_captcha2['es'] = "Por favor, corrija los campos en color.";
$key_captcha2['de'] = "Bitte korrigieren Sie die Felder.";
$key_captcha1['it'] = "Clicca qui se non è possibile visualizzare il codice.";


$key_captcha3['pt'] = "email inválido";
$key_captcha3['ch'] = "invalid email";
$key_captcha3['en'] = "invalid email";
$key_captcha3['fr'] = "courriel non valide";
$key_captcha3['es'] = "correo electrónico no válida";
$key_captcha3['de'] = "ungültige E-Mail";
$key_captcha3['it'] = "posta elettronica non valido";

$key_captcha4['pt'] = "código vazio";
$key_captcha4['ch'] = "code empty";
$key_captcha4['en'] = "code empty";
$key_captcha4['fr'] = "code de vide";
$key_captcha4['es'] = "código vacío";
$key_captcha4['de'] = "leer-code";
$key_captcha4['it'] = "codice vuoto";

$key_captcha5['pt'] = "código inválido";
$key_captcha5['ch'] = "invalid code";
$key_captcha5['en'] = "invalid code";
$key_captcha5['fr'] = "code non valide";
$key_captcha5['es'] = "código no válido";
$key_captcha5['de'] = "Ungültiger code";
$key_captcha5['it'] = "codice non valido";

$key_subscribe_newsletter['pt']="DESEJO RECEBER NOVIDADES E OFERTAS DA MARCA LUÍSA ROSAS<br>(Esta informação não será partilhada)";
$key_subscribe_newsletter['ch'] = "Subscribe the Newsletter";
$key_subscribe_newsletter['en'] = "Subscribe the Newsletter";
$key_subscribe_newsletter['fr'] = "Inscrivez-vous à la newsletter";
$key_subscribe_newsletter['es'] = "Suscríbir a la Newsletter";
$key_subscribe_newsletter['de'] = "Newsletterbestellung";
$key_subscribe_newsletter['it'] = "Iscriviti alla nostra Newsletter";

$key_subscribe_visitedproducts['pt'] = "Receber notificação de produtos visitados";
$key_subscribe_visitedproducts['ch'] = "Receive notification of visited products";
$key_subscribe_visitedproducts['en'] = "Receive notification of visited products";
$key_subscribe_visitedproducts['fr'] = "Recibir notificaciones de los productos visitados";
$key_subscribe_visitedproducts['es'] = "Recibir notificaciones de los productos visitados";
$key_subscribe_visitedproducts['de'] = "Benachrichtigung, besuchten Produkte";
$key_subscribe_visitedproducts['it'] = "Ricevere la notifica di prodotti visitati";

$key_shop_msg1['pt'] = 'Agradecemos a sua preferência, confirmamos o registo da sua encomenda.<br>Assim que a encomenda for processada, será notificado via email.';
$key_shop_msg1['ch'] = 'Thank you for your choice, we confirm the registration of your order.<br> As soon as your order is processed, you will be notified by email.';
$key_shop_msg1['en'] = 'Thank you for your choice, we confirm the registration of your order.<br> As soon as your order is processed, you will be notified by email.';
$key_shop_msg1['fr'] = 'Merci d\'avoir choisi. Nous confirmons votre inscription.<br>u plus tôt que la commande soit processé, vous serez notifié par email.';
$key_shop_msg1['es'] = 'Gracias por elegir. Confirmamos el registro de su pedido.<br />Tan pronto que el pedido sea procesado, se le notificará através de email..';
$key_shop_msg1['de'] = 'Danke für Ihre Wahl. Wir bestätigen die Anmeldung Ihrem Auftragen. <br /> Sobald der Auftrag verarbeitet wird, erhalten Sie einen email.';
$key_shop_msg1['it'] = 'Grazie per la vostra scelta.<br />Confermiamo la registrazione del vostro ordine.<br>Non apena il vostro ordine è elaborato, riceverete una email.';

$key_shop_msg1_c['pt'] = 'A sua encomenda foi cancelada.';
$key_shop_msg1_c['ch'] = 'Your order has been canceled';
$key_shop_msg1_c['en'] = 'Your order has been canceled';
$key_shop_msg1_c['fr'] = 'Votre commande a été annulée.';
$key_shop_msg1_c['es'] = 'Su pedido ha sido cancelado.';
$key_shop_msg1_c['de'] = 'Ihre Bestellung wurde abgebrochen.';
$key_shop_msg1_c['it'] = 'Il tuo ordine è stato annullato.';

$key_shop_msg2['pt'] = 'Por favor, Escolha o método de pagamento pretendido';
$key_shop_msg2['ch'] = 'Please choose the desired method of payment';
$key_shop_msg2['en'] = 'Please choose the desired method of payment';
$key_shop_msg2['fr'] = 'Veuillez choisir le mode désiré de paiement';
$key_shop_msg2['es'] = 'Por favor, elija el deseado modo de pago';
$key_shop_msg2['de'] = 'Bitte wählen Sie die gewünschte Zahlungsweise';
$key_shop_msg2['it'] = 'Bitte wählen Sie die gewünschte Zahlungsweise';

$key_shop_msg2_envio['pt'] = 'Por favor, Escolha o método de expedição pretendido';
$key_shop_msg2_envio['ch'] = 'Please Select your desired delivery method';
$key_shop_msg2_envio['en'] = 'Please Select your desired delivery method';
$key_shop_msg2_envio['fr'] = 'Please Select your desired delivery method';
$key_shop_msg2_envio['es'] = 'Por favor, elija la forma de envio deseada';
$key_shop_msg2_envio['de'] = 'Bitte wählen Sie die gewünschte Art der Bezahlung';
$key_shop_msg2_envio['it'] = 'Si prega di scegliere il metodo di pagamento desiderato';

$key_shop_msg3['pt'] = 'Confirme os dados para faturação';
$key_shop_msg3['ch'] = 'Confirm the data for billing';
$key_shop_msg3['en'] = 'Confirm the data for billing';
$key_shop_msg3['fr'] = 'Confirmer les données de facturation';
$key_shop_msg3['es'] = 'Confirmar los datos para la facturación';
$key_shop_msg3['de'] = 'Bestätigen Sie die Daten für die Abrechnung';
$key_shop_msg3['it'] = 'Confermare i dati per la fatturazione';

$key_shop_msg4['pt'] = 'Atualizar dados no meu perfil para posterior utilização';
$key_shop_msg4['ch'] = 'Update data in my profile for later use';
$key_shop_msg4['en'] = 'Update data in my profile for later use';
$key_shop_msg4['fr'] = 'Mise à jour des données dans mon profil pour une utilisation ultérieure';
$key_shop_msg4['es'] = 'Actualizar datos en mi perfil para su uso posterior';
$key_shop_msg4['de'] = 'Aktualisieren von Daten in meinem Profil für die spätere Verwendung';
$key_shop_msg4['it'] = 'Aggiornare i dati nel mio profilo per un uso successivo';

$key_shop_msg5['pt'] = 'Confirme os dados para entrega';
$key_shop_msg5['ch'] = 'Confirm the data for delivery';
$key_shop_msg5['en'] = 'Confirm the data for delivery';
$key_shop_msg5['fr'] = 'Confirmer les données pour la livraison';
$key_shop_msg5['es'] = 'Confirmar los datos para la entrega';
$key_shop_msg5['de'] = 'Die Daten für die Zustellung bestätigen';
$key_shop_msg5['it'] = 'Confermare i dati per la consegna';

// METODOS DE PAGAMENTO
$key_paypal['pt'] = "Paypal";
$key_paypal['ch'] = "Paypal";
$key_paypal['en'] = "Paypal";
$key_paypal['fr'] = "Paypal";
$key_paypal['es'] = "Paypal";
$key_paypal['de'] = "Paypal";
$key_paypal['it'] = "Paypal";

$key_paypal_msg['pt'] = 'Pague as suas compras via Paypal';
$key_paypal_msg['ch'] = 'Pay your purchases via Paypal';
$key_paypal_msg['en'] = 'Pay your purchases via Paypal';
$key_paypal_msg['fr'] = 'Payez vos achats via Paypal';
$key_paypal_msg['es'] = 'Pagar tus compras mediante Paypal';
$key_paypal_msg['de'] = 'Bezahlen Sie Ihre Einkäufe per Paypal';
$key_paypal_msg['it'] = 'Pagare i tuoi acquisti tramite Paypal';

$key_transferencia['pt'] = "Transferência Bancária";
$key_transferencia['ch'] = "Wire Transfer";
$key_transferencia['en'] = "Wire Transfer";
$key_transferencia['fr'] = "Virement bancaire";
$key_transferencia['es'] = "Transferencia bancaria";
$key_transferencia['de'] = "Banküberweisung";
$key_transferencia['it'] = "Bonifico bancario";

$key_transferencia_msg['pt'] = 'Pague as suas compras via Transferência Bancária';
$key_transferencia_msg['ch'] = 'Pay your purchases via wire transfer';
$key_transferencia_msg['en'] = 'Pay your purchases via wire transfer';
$key_transferencia_msg['fr'] = 'Payez vos achats par virement bancaire';
$key_transferencia_msg['es'] = 'Pagar tus compras por transferencia bancariaa';
$key_transferencia_msg['de'] = 'Zahlen Sie Ihre Einkäufe per Banküberweisung';
$key_transferencia_msg['it'] = 'Pagare i tuoi acquisti tramite bonifico bancario';

$key_dados_entr['pt'] = "Dados da Entrega";
$key_dados_entr['ch'] = "Delivery information";
$key_dados_entr['en'] = "Delivery information";
$key_dados_entr['fr'] = "Données de livraison";
$key_dados_entr['es'] = "Datos de Entrega";
$key_dados_entr['de'] = "Lieferdaten";
$key_dados_entr['it'] = "Dati di consegna";

$key_expedicao['pt'] = "Por favor escolha o método de expedição";
$key_expedicao['ch'] = "Please choose the method of shipment";
$key_expedicao['en'] = "Please choose the method of shipment";
$key_expedicao['fr'] = "Veuillez choisir la méthode d'expédition";
$key_expedicao['es'] = "Por favor, elija el método de envío";
$key_expedicao['de'] = "Bitte wählen Sie die Methode des Versandes";
$key_expedicao['it'] = "Si prega di scegliere il metodo di spedizione";

$key_pagamento['pt'] = "Por favor escolha o método de pagamento";
$key_pagamento['ch'] = "Please choose your payment method";
$key_pagamento['en'] = "Please choose your payment method";
$key_pagamento['fr'] = "Veuillez choisir le mode de paiement";
$key_pagamento['es'] = "Por favor, elija el método de pago";
$key_pagamento['de'] = "Bitte wählen Sie die Zahlungsart";
$key_pagamento['it'] = "Si prega di scegliere il metodo di pagamento";

$key_obs['pt'] = "Se desejar acrescentar algum comentário à nota de encomenda, pode fazê-lo aqui";
$key_obs['ch'] = "If you want to add a comment to the purchase order, you can do so here";
$key_obs['en'] = "If you want to add a comment to the purchase order, you can do so here";
$key_obs['fr'] = "Si vous souhaitez ajouter un commentaire au bon de commande, vous pouvez le faire ici";
$key_obs['es'] = "Si desea agregar un comentario a la orden de compra, puede hacerlo aquí";
$key_obs['de'] = "Wenn Sie der Bestellung einen Kommentar hinzufügen möchten, können Sie dies hier tun";
$key_obs['it'] = "Se si desidera aggiungere un commento all'ordine di acquisto, è possibile farlo qui";

$key_send_encomenda['pt'] = "Submeter Encomenda";
$key_send_encomenda['ch'] = "Submit Order";
$key_send_encomenda['en'] = "Submit Order";
$key_send_encomenda['fr'] = "Soumettre la commande";
$key_send_encomenda['es'] = "Enviar pedido";
$key_send_encomenda['de'] = "Bestellung absenden";
$key_send_encomenda['it'] = "Inviare ordine";

$key_modify_cart['pt'] = 'Modificar Carrinho';
$key_modify_cart['ch'] = 'Modify your cart';
$key_modify_cart['en'] = 'Modify your cart';
$key_modify_cart['fr'] = 'Modifier votre panier';
$key_modify_cart['es'] = 'Modificar su cesta';
$key_modify_cart['de'] = 'Ihr Warenkorb ändern';
$key_modify_cart['it'] = 'Modificare il vostro carrello';

$key_camposobr['pt'] = "Para prosseguir, preencha todos os campos assinalados.";
$key_camposobr['ch'] = "Please correct the coloured fields.";
$key_camposobr['en'] = "Please correct the coloured fields.";
$key_camposobr['fr'] = "Pour continuer, veuillez remplir tous les champs marqués.";
$key_camposobr['es'] = "Para continuar, por favor, complete todos los campos marcados.";
$key_camposobr['de'] = "Um weiterzugehen, füllen Sie bitte alle markierten Felder.";
$key_camposobr['it'] = "Per procedere, si prega di compilare tutti i campi contrassegnati.";

$key_registo3b['pt'] = "Os seus dados foram corretamente alterados. <br/> Obrigado.";
$key_registo3b['ch'] = "Your data is correctly changed. <br/> Thanks.";
$key_registo3b['en'] = "Your data is correctly changed. <br/> Thanks.";
$key_registo3b['fr'] = "Vos données sont correctement modifiées. <br/> Merci.";
$key_registo3b['es'] = "Sus datos se modifican correctamente. <br/> Gracias.";
$key_registo3b['de'] = "Ihre Daten werden korrekt geändert. <br/> Vielen Dank.";
$key_registo3b['it'] = "I dati correttamente viene modificati. <br/> Grazie.";

$key_pais['pt'] = "País";
$key_pais['ch'] = "Country";
$key_pais['en'] = "Country";
$key_pais['fr'] = "Pays";
$key_pais['es'] = "País";
$key_pais['de'] = "Land";
$key_pais['it'] = "Paese";

$key_obrigatorio['pt'] = "Campos de preenchimento obrigatório";
$key_obrigatorio['ch'] = "Required Fields";
$key_obrigatorio['en'] = "Required Fields";
$key_obrigatorio['fr'] = "Champs Obligatoires";
$key_obrigatorio['es'] = "Campos requeridos";
$key_obrigatorio['de'] = "Erforderliche Felder";
$key_obrigatorio['it'] = "Campi obbligatori";

$key_tipo_cliente['pt'] = "Tipo de cliente";
$key_tipo_cliente['ch'] = "Client type";
$key_tipo_cliente['en'] = "Client type";
$key_tipo_cliente['fr'] = "Type de client";
$key_tipo_cliente['es'] = "Tipo de cliente";
$key_tipo_cliente['de'] = "Clienttyp";
$key_tipo_cliente['it'] = "Tipo di client";

$key_tipo_cliente1['pt'] = "Cliente Final";
$key_tipo_cliente1['ch'] = "End Client";
$key_tipo_cliente1['en'] = "End Client";
$key_tipo_cliente1['fr'] = "Client Final";
$key_tipo_cliente1['es'] = "Cliente final";
$key_tipo_cliente1['de'] = "Endkunde";
$key_tipo_cliente1['it'] = "Cliente finale";

$key_tipo_cliente2['pt'] = "Revendedor";
$key_tipo_cliente2['ch'] = "Retailer";
$key_tipo_cliente2['en'] = "Retailer";
$key_tipo_cliente2['fr'] = "Détaillant";
$key_tipo_cliente2['es'] = "Distribuidor";
$key_tipo_cliente2['de'] = "Comerciante";
$key_tipo_cliente2['it'] = "Rivenditore";

$key_tipo_cliente6['pt'] = "Gabinete de Fiscalização";
$key_tipo_cliente6['ch'] = "Office of Oversight";
$key_tipo_cliente6['en'] = "Office of Oversight";
$key_tipo_cliente6['fr'] = "Bureau de la Supervision";
$key_tipo_cliente6['es'] = "Oficina de Supervisión";
$key_tipo_cliente6['de'] = "Büro der Aufsicht";
$key_tipo_cliente6['it'] = "Ufficio di Supervisione";

$key_sem_stock['pt'] = "Currently this product does not exist in stock. <br> the delivery may take up to 15 days, unless breakage of stock. <br> are you sure you want to add this product to the cart?";
$key_sem_stock['ch'] = "Currently this product does not exist in stock. <br> the delivery may take up to 15 days, unless breakage of stock. <br> are you sure you want to add this product to the cart?";
$key_sem_stock['en'] = "Currently this product does not exist in stock. <br> the delivery may take up to 15 days, unless breakage of stock. <br> are you sure you want to add this product to the cart?";
$key_sem_stock['fr'] = "Actualmente este producto no existe en la acción. <br>la entrega puede tardar hasta 15 días, salvo rotura de stock. <br>¿está seguro que desea agregar este producto a la cesta?";
$key_sem_stock['es'] = "Actualmente este producto no está disponible.<br>La entrega puede tardar hasta 15 días, salvo rotura de stock.<br>¿Seguro que quieres añadir este producto a la cesta?";
$key_sem_stock['de'] = "Dieses Produkt ist derzeit nicht vorrätig vorhanden. <br>die Lieferung dauert bis zu 15 Tage, es sei denn, Bruch der Aktie. <br>sind Sie sicher, dass Sie dieses Produkt in den Warenkorb hinzufügen möchten?";
$key_sem_stock['it'] = "Questo prodotto non esiste attualmente in magazzino. <br>la consegna può richiedere fino a 15 giorni, a meno che rottura di stock. <br>sei sicuro che si desidera aggiungere questo prodotto al carrello?";

$key_adicionado_carrinho['pt'] = "Acabou de adicionar ao carrinho";
$key_adicionado_carrinho['ch'] = "";
$key_adicionado_carrinho['en'] = "You just added to cart";
$key_adicionado_carrinho['fr'] = "";
$key_adicionado_carrinho['es'] = "Acaba de añadir a la cesta";
$key_adicionado_carrinho['de'] = "";
$key_adicionado_carrinho['it'] = "";

$key_continuar_comprar['pt'] = "Continuar a comprar";
$key_continuar_comprar['ch'] = "Continue shopping";
$key_continuar_comprar['en'] = "Continue shopping";
$key_continuar_comprar['fr'] = "Continuer vos achats";
$key_continuar_comprar['es'] = "Continuar comprando";
$key_continuar_comprar['de'] = "Einkauf  fortsetzen";
$key_continuar_comprar['it'] = "Continua lo shopping";

$key_nao_adicionar['pt'] = "Não adicionar";
$key_nao_adicionar['ch'] = "Don't add";
$key_nao_adicionar['en'] = "Don't add";
$key_nao_adicionar['fr'] = "Ne pas ajouter";
$key_nao_adicionar['es'] = "No agregue";
$key_nao_adicionar['de'] = "Fügen Sie nicht";
$key_nao_adicionar['it'] = "Non aggiungere";

$key_adicionar_continuar_comprar['pt'] = "CONTINUAR A COMPRAR";
$key_adicionar_continuar_comprar['ch'] = "CONTINUE SHOPPING";
$key_adicionar_continuar_comprar['en'] = "CONTINUE SHOPPING";
$key_adicionar_continuar_comprar['fr'] = "CONTINUER VOS ACHATS";
$key_adicionar_continuar_comprar['es'] = "CONTINUAR COMPRANDO";
$key_adicionar_continuar_comprar['de'] = "EINKAUF  FORTSETZEN";
$key_adicionar_continuar_comprar['it'] = "CONTINUA LO SHOPPING";
/* begin Sérgio */
$key_registo4b1['pt'] = "O seu registo foi aceite. Obrigado.";
$key_registo4b1['ch'] = "Your registration has been accepted. Thank you.";
$key_registo4b1['en'] = "Your registration has been accepted. Thank you.";
$key_registo4b1['fr'] = "Votre inscription a été acceptée. Merci.";
$key_registo4b1['es'] = "Su inscripción ha sido aceptada. Gracias.";
$key_registo4b1['de'] = "Ihre Anmeldung angenommen wurde. Danke.";
$key_registo4b1['it'] = "La registrazione è stata accettata. Grazie.";

$key_registo4b2['pt'] = "O seu registo foi aceite, por favor aguarde a nossa confirmação por email.";
$key_registo4b2['ch'] = "Your registration has been accepted, please wait for our confirmation by e-mail.";
$key_registo4b2['en'] = "Your registration has been accepted, please wait for our confirmation by e-mail.";
$key_registo4b2['fr'] = "Votre inscription a été acceptée, veuillez attendre notre confirmation par courrier électronique.";
$key_registo4b2['es'] = "Su inscripción ha sido aceptada, por favor, espere nuestra confirmación por correo electrónico.";
$key_registo4b2['de'] = "Ihre Anmeldung angenommen wurde, bitte warten Sie unsere Bestätigung per E-mail.";
$key_registo4b2['it'] = "La registrazione è stata accettata, si prega di attendere per la nostra conferma via e-mail.";

$key_erro11['pt'] = "<strong><font color='#cc0000'>Utilizador Errado ou inexistente.</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro11['ch'] = "<strong><font color='#cc0000'>Wrong user or nonexistent.</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' turned on, or if you have entered a wrong character.</font>";
$key_erro11['en'] = "<strong><font color='#cc0000'>Wrong user or nonexistent.</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' turned on, or if you have entered a wrong character.</font>";
$key_erro11['fr'] = "<strong><font color='#cc0000'>Utilisateur incorrect ou inexistante.</font></strong><br><font color='#000000'>S'il vous plaît vérifiez si vous avez le 'caps lock' allumé, ou si vous avez entré un mauvais caractère.</font>";
$key_erro11['es'] = "<strong><font color='#cc0000'>Usuario incorrecto o inexistente.</font></strong><br><font color='#000000'>Por favor, compruebe si tiene el 'bloqueo de may&uacute;sculas', o tiene alg&uacute;n personaje equivocado.</font>";
$key_erro11['de'] = "<strong><font color='#cc0000'>Falsche Benutzer oder nicht vorhanden.</font></strong><br><font color='#000000'>Überprüfen Sie, wenn Sie die 'Caps Lock' eingeschaltet haben, oder wenn Sie ein falsches Zeichen eingegeben haben.</font>";
$key_erro11['it'] = "<strong><font color='#cc0000'>Utente errato o inesistente.</font></strong><br><font color='#000000'>Si prega di controllare se avete il 'Bloc' acceso, o se è stato immesso un carattere errato.</font>";

$key_erro12['pt'] = "<strong><font color='#cc0000'>Por favor insira a password.</font></strong><br><font color='#000000'>É necessário introduzir a password para que seja validado com sucesso.</font>";
$key_erro12['ch'] = "<strong><font color='#cc0000'>Please enter the password.</font></strong><br><font color='#000000'>You must enter the password to be validated successfully.</font>";
$key_erro12['en'] = "<strong><font color='#cc0000'>Please enter the password.</font></strong><br><font color='#000000'>You must enter the password to be validated successfully.</font>";
$key_erro12['fr'] = "<strong><font color='#cc0000'>Veuillez entrer le mot de passe.</font></strong><br><font color='#000000'>Vous devez entrer le mot de passe doit être validé avec succès.</font>";
$key_erro12['es'] = "<strong><font color='#cc0000'>Por favor ingrese la contraseña.</font></strong><br><font color='#000000'>Debe introducir la contraseña para ser validado con éxito.</font>";
$key_erro12['de'] = "<strong><font color='#cc0000'>Bitte geben Sie das Kennwort ein.</font></strong><br><font color='#000000'>Sie müssen das Passwort erfolgreich überprüft werden.</font>";
$key_erro12['it'] = "<strong><font color='#cc0000'>Si prega di inserire la password.</font></strong><br><font color='#000000'>È necessario immettere la password da convalidare correttamente.</font>";

$key_erro13['pt'] = "<strong><font color='#cc0000'>Password Errada.</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro13['ch'] = "<strong><font color='#cc0000'>Wrong Password.</font></strong><br><font color='#000000'><br>Please check if you have the 'caps lock' turned on, or if you have entered a wrong character.</font>";
$key_erro13['en'] = "<strong><font color='#cc0000'>Wrong Password.</font></strong><br><font color='#000000'><br>Please check if you have the 'caps lock' turned on, or if you have entered a wrong character.</font>";
$key_erro13['fr'] = "<strong><font color='#cc0000'>Mot de passe erroné.</font></strong><br><font color='#000000'>S'il vous plaît vérifiez si vous avez le 'caps lock' allumé, ou si vous avez entré un mauvais caractère.</font>";
$key_erro13['es'] = "<strong><font color='#cc0000'>Contraseña incorrecta.</font></strong><br><font color='#000000'>Por favor comprueba si tienes activado 'caps lock', o si ha entrado un mal carácter.</font>";
$key_erro13['de'] = "<strong><font color='#cc0000'>Falsches Passwort.</font></strong><br><font color='#000000'>Überprüfen Sie, wenn Sie die 'Caps Lock' eingeschaltet haben, oder wenn Sie ein falsches Zeichen eingegeben haben.</font>";
$key_erro13['it'] = "<strong><font color='#cc0000'>Password errata.</font></strong><br><font color='#000000'>Si prega di controllare se avete il 'Bloc' acceso, o se è stato immesso un carattere errato.</font>";

$key_erro14['pt'] = "<strong><font color='#cc0000'>A Login não se encontra activa.</font></strong>";
$key_erro14['ch'] = "<strong><font color='#cc0000'>The Login is not active.</font></strong>";
$key_erro14['en'] = "<strong><font color='#cc0000'>The Login is not active.</font></strong>";
$key_erro14['fr'] = "<strong><font color='#cc0000'>La connexion n'est pas active.</font></strong>";
$key_erro14['es'] = "<strong><font color='#cc0000'>El inicio de sesión no está activa.</font></strong>";
$key_erro14['de'] = "<strong><font color='#cc0000'>Die Anmeldung ist nicht aktiv.</font></strong>";
$key_erro14['it'] = "<strong><font color='#cc0000'>Il Login non è attivo.</font></strong>";

$key_erro15['pt'] = "<strong><font color='#cc0000'>A Login não existe.</font></strong><br><font color='#000000'>Por favor, verifique se têm o 'caps lock' ligado, ou se introduziu algum caracter errado.</font>";
$key_erro15['ch'] = "<strong><font color='#cc0000'>The Login does not exist.</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' on, or got some wrong character.</font>";
$key_erro15['en'] = "<strong><font color='#cc0000'>The Login does not exist.</font></strong><br><font color='#000000'>Please check if you have the 'caps lock' on, or got some wrong character.</font>";
$key_erro15['fr'] = "<strong><font color='#cc0000'>La connexion n'existe pas.</font></strong><br><font color='#000000'>S'il vous plaît vérifiez si vous avez le 'caps lock' allumé, ou si vous avez entré un mauvais caractère.</font>";
$key_erro15['es'] = "<strong><font color='#cc0000'>El inicio de sesión no existe.</font></strong><br><font color='#000000'>Por favor comprueba si tienes activado 'caps lock', o si ha entrado un mal carácter.</font>";
$key_erro15['de'] = "<strong><font color='#cc0000'>Die Anmeldung ist nicht vorhanden.</font></strong><br><font color='#000000'>Überprüfen Sie, wenn Sie die 'Caps Lock' eingeschaltet haben, oder wenn Sie ein falsches Zeichen eingegeben haben.</font>";
$key_erro15['it'] = "<strong><font color='#cc0000'>L'account di accesso non esiste.</font></strong><br><font color='#000000'>Si prega di controllare se avete il 'Bloc' acceso, o se è stato immesso un carattere errato.</font>";

$key_erro16['pt'] = "<strong><font color='#cc0000'>A Login está ser usada por outro utilizador.</font></strong><br><font color='#000000'>Por favor, tente outro. Obrigado.</font>";
$key_erro16['ch'] = "<strong><font color='#cc0000'>The login is being used by another user.</font></strong><br><font color='#000000'>Please try another. Thank you.</font>";
$key_erro16['en'] = "<strong><font color='#cc0000'>The login is being used by another user.</font></strong><br><font color='#000000'>Please try another. Thank you.</font>";
$key_erro16['fr'] = "<strong><font color='#cc0000'>Le login est utilisé par un autre utilisateur.</font></strong><br><font color='#000000'>Veuillez essayer un autre. Merci.</font>";
$key_erro16['es'] = "<strong><font color='#cc0000'>El inicio de sesión está siendo utilizado por otro usuario.</font></strong><br><font color='#000000'>Por favor, inténtelo con otro. Gracias.</font>";
$key_erro16['de'] = "<strong><font color='#cc0000'>Die Anmeldung wird von einem anderen Benutzer verwendet wird.</font></strong><br><font color='#000000'>Bitte versuchen Sie eine andere. Danke.</font>";
$key_erro16['it'] = "<strong><font color='#cc0000'>Il login è utilizzato da un altro utente.</font></strong><br><font color='#000000'>Provate con un altro. Grazie.</font>";

$key_erro1['pt'] = "<strong><font color='#cc0000'>Não foi inserida password.</font></strong><br><font color='#000000'>Por favor, insira a password.</font>";
$key_erro1['ch'] = "<strong><font color='#cc0000'>No password was inserted.</font></strong><br><font color='#000000'>Please enter a password.</font>";
$key_erro1['en'] = "<strong><font color='#cc0000'>No password was inserted.</font></strong><br><font color='#000000'>Please enter a password.</font>";
$key_erro1['fr'] = "<strong><font color='#cc0000'>Aucun mot de passe a été inséré.</font></strong><br><font color='#000000'>Veuillez entrer un mot de passe.</ font>";
$key_erro1['es'] = "<strong><font color='#cc0000'>No se insertó contraseña.</font></strong><br><font color='#000000'>Por favor, introduzca una contraseña.</ font>";
$key_erro1['de'] = "<strong><font color='#cc0000'>Kein Passwort wurde eingefügt.</font></strong><br><font color='#000000'>Bitte geben Sie ein Passwort.</ font>";
$key_erro1['it'] = "<strong><font color='#cc0000'>Nessuna password è stata inserita.</font></strong><br><font color='#000000'>Si prega di inserire una password.</ font>";

$key_erro2['pt'] = "A login que escolheu já existe, por favor tente uma login diferente.";
$key_erro2['ch'] = "The login you have chosen already exists, please try a different login.";
$key_erro2['en'] = "The login you have chosen already exists, please try a different login.";
$key_erro2['fr'] = "Le login que vous avez choisi déjà existe, s'il vous plaît essayer une connexion différente.";
$key_erro2['es'] = "El inicio de sesión que ha elegido ya existe, por favor, intente un inicio de sesión diferente.";
$key_erro2['de'] = "Die Anmeldung bereits von Ihnen gewählten vorhanden ist, versuchen Sie bitte einen anderen Benutzernamen.";
$key_erro2['it'] = "L'accesso che hai selezionato già esiste, si prega di provare un diverso account di accesso.";

$key_erro3['pt'] = "O código de validação está incorreto.";
$key_erro3['ch'] = "The validation code is incorrect.";
$key_erro3['en'] = "The validation code is incorrect.";
$key_erro3['fr'] = "Le code de validation est incorrect.";
$key_erro3['es'] = "El código de validación es incorrecto.";
$key_erro3['de'] = "Der Validierungscode ist falsch.";
$key_erro3['it'] = "Il codice di convalida non è corretto.";

$key_erro4['pt'] = "O número de contribuinte indicado já existe no nosso sistema.";
$key_erro4['ch'] = "The number indicated already exists in our system.";
$key_erro4['en'] = "The number indicated already exists in our system.";
$key_erro4['fr'] = "Le nombre indiqué déjà existe dans notre système.";
$key_erro4['es'] = "El número indicado ya existe en nuestro sistema.";
$key_erro4['de'] = "Die bereits genannte Rufnummer ist in unserem System vorhanden.";
$key_erro4['it'] = "Il numero indicato già esiste nel nostro sistema.";
/* end Sérgio */
$key_msg_verify['pt'] = "Parabéns, o seu email foi confirmado!";
$key_msg_verify['ch'] = "Congratulations, your email has been confirmed!";
$key_msg_verify['en'] = "Congratulations, your email has been confirmed!";
$key_msg_verify['fr'] = "Félicitations, votre email a été confirmé!";
$key_msg_verify['es'] = "Felicidades, su e-mail ha sido confirmado!";
$key_msg_verify['de'] = "Herzlichen Glückwunsch, ist Ihre e-Mail bestätigt worden!";
$key_msg_verify['it'] = "Congratulazioni, il tuo indirizzo email è stato confermato!";

$key_msg_verify_erro['pt'] = "O seu email já se encontra verificado!";
$key_msg_verify_erro['ch'] = "Your email is already verified!";
$key_msg_verify_erro['en'] = "Your email is already verified!";
$key_msg_verify_erro['fr'] = "Votre email est déjà vérifiée !";
$key_msg_verify_erro['es'] = "Ya se ha verificado tu correo electrónico!";
$key_msg_verify_erro['de'] = "Ihre R-Mail ist bereits bestätigt!";
$key_msg_verify_erro['it'] = "Il tuo indirizzo email è già verificato!";

$key_msg_verify_erro2['pt'] = "O email não se encontra registado, por favor aceda zona de registo!";
$key_msg_verify_erro2['ch'] = "The email is not registered, please go to the registration area!";
$key_msg_verify_erro2['en'] = "The email is not registered, please go to the registration area!";
$key_msg_verify_erro2['fr'] = "L'email n'est pas inscrit, veuillez vous rendre à l'aire d'inscription !";
$key_msg_verify_erro2['es'] = "El email no está registrado, por favor diríjase a la zona de inscripciones!";
$key_msg_verify_erro2['de'] = "Die e-Mail ist nicht registriert, gehen Sie bitte zum Registrierungsbereich!";
$key_msg_verify_erro2['it'] = "L'email non è registrato, si prega di andare alla zona di registrazione!";

$key_fechar['pt'] = "Fechar";
$key_fechar['ch'] = "Close";
$key_fechar['en'] = "Close";
$key_fechar['fr'] = "Fermer";
$key_fechar['es'] = "Cerrar";
$key_fechar['de'] = "Schließen";
$key_fechar['it'] = "Chiudere";

$key_seuemail['pt'] = "O seu email";
$key_seuemail['ch'] = "Your email";
$key_seuemail['en'] = "Your email";
$key_seuemail['fr'] = "Votre email";
$key_seuemail['es'] = "Su email";
$key_seuemail["de"] = "Ihre e-mail";
$key_seuemail["it"] = "Il tuo indirizzo email";

$key_submeter_rec['pt'] = "Enviar Email";
$key_submeter_rec['ch'] = "Send Email";
$key_submeter_rec['en'] = "Send Email";
$key_submeter_rec['fr'] = "Envoyer Email";
$key_submeter_rec['es'] = "Enviar Email";
$key_submeter_rec['de'] = "Senden Sie E-Mail";
$key_submeter_rec['it'] = "Inviare Email";

$key_submeter_cancel['pt'] = "Cancelar";
$key_submeter_cancel['ch'] = "Cancel";
$key_submeter_cancel['en'] = "Cancel";
$key_submeter_cancel['fr'] = "Annuler";
$key_submeter_cancel['es'] = "Cancelar";
$key_submeter_cancel['de'] = "Abbrechen";
$key_submeter_cancel['it'] = "Annulla";

$key_dentmomentos['pt'] = "Dentro de momentos ir&aacute; receber a sua password na sua caixa de correio, obrigado!<br>Por favor certifique-se que o nosso email n&atilde;o est&aacute; a ser encaminhado para SPAM. Caso contrário poderá não receber as nossas notificações.";
$key_dentmomentos['ch'] = "Within moments you will receive your password in your mailbox, thank you!<br>Please make sure that our email is not being sent to SPAM. Otherwise you may not receive our notifications.";
$key_dentmomentos['en'] = "Within moments you will receive your password in your mailbox, thank you!<br>Please make sure that our email is not being sent to SPAM. Otherwise you may not receive our notifications.";
$key_dentmomentos['fr'] = "En quelques instants, vous recevrez votre mot de passe dans votre boîte aux lettres, Merci!<br>s'il vous plaît assurez-vous que notre email n'est pas envoyé au SPAM. Dans le cas contraire, vous ne receviez pas nos notifications.";
$key_dentmomentos['es'] = "En instantes recibirá su contraseña en su buzón de correo, muchas gracias!<br> por favor asegúrese de que nuestro correo no se envía a SPAM. De lo contrario no recibirás nuestras notificaciones.";
$key_dentmomentos['de'] = "Innerhalb weniger Augenblicke erhalten Sie Ihr Passwort in Ihrem Postfach, vielen Dank!<br>Bitte stellen Sie sicher, dass unsere e-Mail nicht an SPAM gesendet wird. Ansonsten können Sie nicht unsere Benachrichtigungen empfangen.";
$key_dentmomentos['it'] = "In pochi istanti riceverai la password nella tua casella di posta, vi ringrazio!<br> si prega di assicurarsi che la nostra e-mail non viene inviato a SPAM. Altrimenti potresti non ricevere le nostre notifiche.";

$key_dentmomentos2['pt'] = "Houve um problema ao enviar o email, por favor tente mais tarde.";
$key_dentmomentos2['ch'] = "There was a problem sending the email, please try again later.";
$key_dentmomentos2['en'] = "There was a problem sending the email, please try again later.";
$key_dentmomentos2['fr'] = "Il y avait un problème d'envoi de l'e-mail, Veuillez réessayer ultérieurement.";
$key_dentmomentos2['es'] = "Hubo un problema enviando el correo electrónico, por favor Inténtalo más tarde.";
$key_dentmomentos2['de'] = "Beim Senden der e-Mail ein Problem aufgetreten, bitte versuchen Sie es später erneut.";
$key_dentmomentos2['it'] = "C'era un problema per l'invio di email, si prega di riprovare più tardi.";

$key_dentmomentos3['pt'] = "O endereço de e-mail introduzido não se encontra registado.";
$key_dentmomentos3['ch'] = "The e-mail address you entered is not registered.";
$key_dentmomentos3['en'] = "The e-mail address you entered is not registered.";
$key_dentmomentos3['fr'] = "L'adresse e-mail saisie n'est pas enregistré.";
$key_dentmomentos3['es'] = "El e-mail ingresado no está registrado.";
$key_dentmomentos3['de'] = "Eingegebene E-mail-Adresse ist nicht registriert.";
$key_dentmomentos3['it'] = "L'indirizzo di posta elettronica immesso non è registrato.";

$key_registo['pt'] = "Registar";
$key_registo['ch'] = "Register";
$key_registo['en'] = "Register";
$key_registo['fr'] = "S'inscrire";
$key_registo['es'] = "Registro";
$key_registo['de'] = "Registrieren";
$key_registo['it'] = "Registro";

$key_editarperfil['pt'] = "Editar perfil";
$key_editarperfil['ch'] = "Edit Profile";
$key_editarperfil['en'] = "Edit Profile";
$key_editarperfil['fr'] = "Modifier le profil";
$key_editarperfil['es'] = "Editar perfil";
$key_editarperfil['de'] = "Profil bearbeiten";
$key_editarperfil['it'] = "Modifica profilo";

$key_noproductsfound['pt']="Não foram encontrados nenhuns resultados.";
$key_noproductsfound['ch']="No results were found.";
$key_noproductsfound['en']="No results were found.";
$key_noproductsfound['fr']="Été trouvé aucun résultat.";
$key_noproductsfound['es']="No se han encontrado.";
$key_noproductsfound['de']="Es wurden keine Ergebnisse gefunden.";
$key_noproductsfound['it']="Nessun risultato trovato.";

$key_resultadospesquisa['pt']="Resultados da pesquisa";
$key_resultadospesquisa['ch']="Search results";
$key_resultadospesquisa['en']="Search results";
$key_resultadospesquisa['fr']="Résultats de la recherche";
$key_resultadospesquisa['es']="Resultados de la búsqueda";
$key_resultadospesquisa['de']="Suchergebnisse";
$key_resultadospesquisa['it']="Risultati della ricerca";

$key_emptyfield["pt"] = "Por favor preencha este campo";
$key_emptyfield["ch"] = "Please complete this field";
$key_emptyfield["en"] = "Please complete this field";
$key_emptyfield["fr"] = "Veuillez remplir ce champ";
$key_emptyfield["es"] = "Por favor complete este campo";
$key_emptyfield["de"] = "Bitte füllen Sie dieses Feld";
$key_emptyfield["it"] = "Si prega di compilare questo campo";

$key_diferentpasswords["pt"] = "Passwords não correspondem";
$key_diferentpasswords["ch"] = "Passwords don't match";
$key_diferentpasswords["en"] = "Passwords don't match";
$key_diferentpasswords["fr"] = "Mots de passe ne correspondent pas";
$key_diferentpasswords["es"] = "Las contraseñas no coinciden";
$key_diferentpasswords["de"] = "Kennwörter stimmen nicht überein";
$key_diferentpasswords["it"] = "Le password non corrispondono";

$key_pesquisar["pt"] = "Insira a palavra a pesquisar";
$key_pesquisar["ch"] = "search...";
$key_pesquisar["en"] = "search...";
$key_pesquisar["fr"] = "rechercher...";
$key_pesquisar["es"] = "búsqueda de...";
$key_pesquisar["de"] = "suche...";
$key_pesquisar["it"] = "ricerca...";

$key_pesquisar2["pt"] = "pesquisar";
$key_pesquisar2["ch"] = "search";
$key_pesquisar2["en"] = "search";
$key_pesquisar2["fr"] = "recherche";
$key_pesquisar2["es"] = "búsqueda";
$key_pesquisar2["de"] = "suche";
$key_pesquisar2["it"] = "ricerca";


//FILTROS
$txt_lptudo["pt"] = "Limpar Tudo";
$txt_lptudo["ch"] = "Clean all";
$txt_lptudo["en"] = "Clean all";
$txt_lptudo["fr"] = "Nettoyer toutes les";
$txt_lptudo["es"] = "Limpiar todos los";
$txt_lptudo["de"] = "Reinigen Sie alle";
$txt_lptudo["it"] = "Pulire tutte le";

$txt_em_promocao["pt"] = "Promoções";
$txt_em_promocao["ch"] = "Promotions";
$txt_em_promocao["en"] = "Promotions";
$txt_em_promocao["fr"] = "Promotions";
$txt_em_promocao["es"] = "Promociones";
$txt_em_promocao["de"] = "Angebote";
$txt_em_promocao["it"] = "Promozioni";

$txt_em_destaque["pt"] = "Destaques";
$txt_em_destaque["ch"] = "Highlights";
$txt_em_destaque["en"] = "Highlights";
$txt_em_destaque["fr"] = "Highlights";
$txt_em_destaque["es"] = "Pone de relieve";
$txt_em_destaque["de"] = "Höhepunkte";
$txt_em_destaque["it"] = "Gli highlights";

$txt_em_outlet["pt"] = "Outlet";
$txt_em_outlet["ch"] = "Outlet";
$txt_em_outlet["en"] = "Outlet";
$txt_em_outlet["fr"] = "Outlet";
$txt_em_outlet["es"] = "Outlet";
$txt_em_outlet["de"] = "Outlet";
$txt_em_outlet["it"] = "Outlet";

$txt_em_oportunidade["pt"] = "Em Oportunidades do Dia";
$txt_em_oportunidade["ch"] = "In Oportunity of the Day";
$txt_em_oportunidade["en"] = "In Oportunity of the Day";
$txt_em_oportunidade["fr"] = "À l'occasion de la journée";
$txt_em_oportunidade["es"] = "En oportunidad del día";
$txt_em_oportunidade["de"] = "In Oportunity des Tages";
$txt_em_oportunidade["it"] = "In occasione della giornata";

$txt_em_novidade["pt"] = "Novidades";
$txt_em_novidade["ch"] = "New";
$txt_em_novidade["en"] = "New";
$txt_em_novidade["fr"] = "Nouvelles";
$txt_em_novidade["es"] = "Noticias";
$txt_em_novidade["de"] = "Nachrichten";
$txt_em_novidade["it"] = "Notizie";

$txt_prec_inter["pt"] = "Por Intervalo de Preço";
$txt_prec_inter["ch"] = "By Price Range";
$txt_prec_inter["en"] = "By Price Range";
$txt_prec_inter["fr"] = "Par gamme de prix";
$txt_prec_inter["es"] = "Por rango de precio";
$txt_prec_inter["de"] = "Von Preisklasse";
$txt_prec_inter["it"] = "Fascia di prezzo";

$txt_caracteristicas["pt"] = "CARATERÍSTICAS";
$txt_caracteristicas["ch"] = "FEATURES";
$txt_caracteristicas["en"] = "FEATURES";
$txt_caracteristicas["fr"] = "CARACTÉRISTIQUES";
$txt_caracteristicas["es"] = "CARACTERÍSTICAS";
$txt_caracteristicas["de"] = "FUNKTIONEN";
$txt_caracteristicas["it"] = "CARATTERISTICHE";

$txt_descricao["pt"] = "DESCRIÇÃO";
$txt_descricao["ch"] = "DESCRIPTION";
$txt_descricao["en"] = "DESCRIPTION";
$txt_descricao["fr"] = "DESCRIPTION";
$txt_descricao["es"] = "DESCRIPCIÓN";
$txt_descricao["de"] = "BESCHREIBUNG";
$txt_descricao["it"] = "DESCRIZIONE";

$txt_f_filterby["pt"] = "FILTRAR POR";
$txt_f_filterby["ch"] = "FILTER BY";
$txt_f_filterby["en"] = "FILTER BY";
$txt_f_filterby["fr"] = "FILTRER PAR";
$txt_f_filterby["es"] = "FILTRAR POR";
$txt_f_filterby["de"] = "FILTERN NACH";
$txt_f_filterby["it"] = "FILTRA PER";

$txt_rp_marcas["pt"] = "MARCAS";
$txt_rp_marcas["ch"] = "BRANDS";
$txt_rp_marcas["en"] = "BRANDS";
$txt_rp_marcas["fr"] = "MARQUES";
$txt_rp_marcas["es"] = "MARCAS DE FÁBRICA";
$txt_rp_marcas["de"] = "MARKEN";
$txt_rp_marcas["it"] = "MARCHE";

$txt_rp_familias["pt"] = "FAMÍLIAS";
$txt_rp_familias["ch"] = "FAMILIES";
$txt_rp_familias["en"] = "FAMILIES";
$txt_rp_familias["fr"] = "FAMILLES";
$txt_rp_familias["es"] = "FAMILIAS";
$txt_rp_familias["de"] = "FAMILIEN";
$txt_rp_familias["it"] = "FAMIGLIE";

$txt_preco["pt"] = "PREÇO";
$txt_preco["ch"] = "PRICE";
$txt_preco["en"] = "PRICE";
$txt_preco["fr"] = "PRIX";
$txt_preco["es"] = "PRECIO";
$txt_preco["de"] = "PREIS";
$txt_preco["it"] = "PREZZO";

$txt_produtos["pt"] = "Produtos";
$txt_produtos["ch"] = "Products";
$txt_produtos["en"] = "Products";
$txt_produtos["fr"] = "Produits";
$txt_produtos["es"] = "Productos";
$txt_produtos["de"] = "Produkte";
$txt_produtos["it"] = "Prodotti";

$txt_produtosper["pt"] = "Ver por página";
$txt_produtosper["ch"] = "See by page";
$txt_produtosper["en"] = "See by page";
$txt_produtosper["fr"] = "Voir par page";
$txt_produtosper["es"] = "Ver por página";
$txt_produtosper["de"] = "Siehe Seite";
$txt_produtosper["it"] = "Vedere da pagina";

$txt_disponibilidade_total["pt"]="Disponibilidade Total";
$txt_disponibilidade_total["ch"]="Total Availability";
$txt_disponibilidade_total["en"]="Total Availability";
$txt_disponibilidade_total["es"]="Disponibilidad total";
$txt_disponibilidade_total["fr"]="Disponibilité totale";
$txt_disponibilidade_total["de"]="Gesamtverfügbarkeit";
$txt_disponibilidade_total["it"]="Disponibilità totale";

$txt_disponibilidade_reduzida["pt"]="Disponibilidade Reduzida";
$txt_disponibilidade_reduzida["ch"]="Reduced Availability";
$txt_disponibilidade_reduzida["en"]="Reduced Availability";
$txt_disponibilidade_reduzida["es"]="Menor disponibilidad";
$txt_disponibilidade_reduzida["fr"]="Disponibilité réduite";
$txt_disponibilidade_reduzida["de"]="Geringere Verfügbarkeit";
$txt_disponibilidade_reduzida["it"]="Ridotta disponibilità";

$txt_disponivel_brevemente["pt"]="Disponível Brevemente";
$txt_disponivel_brevemente["ch"]="Available Soon";
$txt_disponivel_brevemente["en"]="Available Soon";
$txt_disponivel_brevemente["es"]="Disponible pronto";
$txt_disponivel_brevemente["fr"]="Disponible bientôt";
$txt_disponivel_brevemente["de"]="Erhältlich bald";
$txt_disponivel_brevemente["it"]="Disponibile presto";

$txt_disponivel_encomenda["pt"]="Disponível por Encomenda";
$txt_disponivel_encomenda["ch"]="Available to order";
$txt_disponivel_encomenda["en"]="Available to order";
$txt_disponivel_encomenda["es"]="Disponible a pedido";
$txt_disponivel_encomenda["fr"]="Disponible sur commande";
$txt_disponivel_encomenda["de"]="Verfügbar auf Bestellung";
$txt_disponivel_encomenda["it"]="Disponibile su ordinazione";

$txt_pagina2["pt"] = "Página";
$txt_pagina2["ch"] = "Page";
$txt_pagina2["en"] = "Page";
$txt_pagina2["fr"] = "Page";
$txt_pagina2["es"] = "Página";
$txt_pagina2["de"] = "Seite";
$txt_pagina2["it"] = "Pagina";

$txt_paginas["pt"] = "Páginas";
$txt_paginas["ch"] = "Pages";
$txt_paginas["en"] = "Pages";
$txt_paginas["fr"] = "Pages";
$txt_paginas["es"] = "Páginas";
$txt_paginas["de"] = "Seiten";
$txt_paginas["it"] = "Pagine";

$produtos_cab["pt"] = "Produtos";
$produtos_cab["ch"] = "Products";
$produtos_cab["en"] = "Products";
$produtos_cab["fr"] = "Produits";
$produtos_cab["es"] = "Productos";
$produtos_cab["de"] = "Produkte";
$produtos_cab["it"] = "Prodotti";

$produtos_cab2["pt"] = "produtos";
$produtos_cab2["ch"] = "products";
$produtos_cab2["en"] = "products";
$produtos_cab2["fr"] = "produits";
$produtos_cab2["es"] = "productos";
$produtos_cab2["de"] = "Produkte";
$produtos_cab2["it"] = "prodotti";

$txt_maisbarato["pt"] = "Mais Barato";
$txt_maisbarato["ch"] = "Cheaper";
$txt_maisbarato["en"] = "Cheaper";
$txt_maisbarato["fr"] = "Moins cher";
$txt_maisbarato["es"] = "Más barato";
$txt_maisbarato["de"] = "Billiger";
$txt_maisbarato["it"] = "Più conveniente";

$txt_maiscaro["pt"] = "Mais Caro";
$txt_maiscaro["ch"] = "More Expensive";
$txt_maiscaro["en"] = "More Expensive";
$txt_maiscaro["fr"] = "Plus cher";
$txt_maiscaro["es"] = "Más Caro";
$txt_maiscaro["de"] = "Teurer";
$txt_maiscaro["it"] = "Più costoso";

$txt_ordby["pt"] = "Ordenar por";
$txt_ordby["ch"] = "Sort by";
$txt_ordby["en"] = "Sort by";
$txt_ordby["fr"] = "Trier par";
$txt_ordby["es"] = "Ordenar por";
$txt_ordby["de"] = "Sortieren nach";
$txt_ordby["it"] = "Ordina per";

$key_retroceder["pt"] = "Voltar";
$key_retroceder["ch"] = "Back";
$key_retroceder["en"] = "Back";
$key_retroceder["fr"] = "Précédent";
$key_retroceder["es"] = "Retroceder";
$key_retroceder["de"] = "Zurück";
$key_retroceder["it"] = "Indietro";

$key_finalizar["pt"] = "Finalizar Encomenda";
$key_finalizar["ch"] = "Finalize Order";
$key_finalizar["en"] = "Finalize Order";
$key_finalizar["fr"] = "Finaliser la commande";
$key_finalizar["es"] = "Finalizar pedido";
$key_finalizar["de"] = "Bestellung abschließen";
$key_finalizar["it"] = "Finalizzare l'ordine";

$key_shop_msg6['pt'] = 'Os dados de entrega são diferentes?';
$key_shop_msg6['ch'] = 'Delivery data are different?';
$key_shop_msg6['en'] = 'Delivery data are different?';
$key_shop_msg6['fr'] = 'Données de livraison sont différentes ?';
$key_shop_msg6['es'] = '¿Datos de entrega son diferentes?';
$key_shop_msg6['de'] = 'Sind Lieferdaten anders?';
$key_shop_msg6['it'] = 'Dati di consegna sono diversi?';



/*farmacia*/


$key_msg_welcome["pt"]="Olá";
$key_msg_welcome["ch"]="Hello";
$key_msg_welcome["en"]="Hello";
$key_msg_welcome["fr"]="Salut";
$key_msg_welcome["es"]="Hola";
$key_msg_welcome["de"]="Hallo";
$key_msg_welcome["it"]="Ciao";

$key_search_sem_sugestoes["pt"]="Não foram encontradas sugestões";
$key_search_sem_sugestoes["ch"]="There were no suggestions found";
$key_search_sem_sugestoes["en"]="There were no suggestions found";
$key_search_sem_sugestoes["fr"]="Il n'y a pas trouvées de suggestions";
$key_search_sem_sugestoes["es"]="No hubo ninguna sugerencias encontradas";
$key_search_sem_sugestoes["de"]="Es gab keine Vorschläge gefunden";
$key_search_sem_sugestoes["it"]="Non c'erano nessun suggerimento trovato";


$key_cliente_recuperar_pass['pt'] = 'Esqueceu-se da palavra-passe?';
$key_cliente_recuperar_pass['ch'] = 'Did you forget your password?';
$key_cliente_recuperar_pass['en'] = 'Did you forget your password?';
$key_cliente_recuperar_pass['fr'] = 'Est-ce que vous avez oublié votre mot de passe ?';
$key_cliente_recuperar_pass['es'] = '¿Olvidó su contraseña?';
$key_cliente_recuperar_pass['de'] = 'Passswort vergessen?';
$key_cliente_recuperar_pass['it'] = 'Hai dimenticato la password?';

$key_recuperar_pass['pt'] = "Recuperar palavra-passe";
$key_recuperar_pass['ch'] = "Recover password";
$key_recuperar_pass['en'] = "Recover password";
$key_recuperar_pass['fr'] = "Récupérer mot de passe";
$key_recuperar_pass['es'] = "Recuperar contraseña";
$key_recuperar_pass['de'] = "Passwort wiederherstellen";
$key_recuperar_pass["it"] = "Recuperare la password";

$key_obrigatorio2['pt'] = "Campos de preenchimento obrigatório";
$key_obrigatorio2['ch'] = "Required Fields";
$key_obrigatorio2['en'] = "Required Fields";
$key_obrigatorio2['fr'] = "Champs Obligatoires";
$key_obrigatorio2['es'] = "Campos requeridos";
$key_obrigatorio2['de'] = "Erforderliche Felder";
$key_obrigatorio2["it"] = "Campi obbligatori";

$key_recuperar_pass_extramsg['pt']='Se esqueceu a sua palavra-passe, indique-nos o seu email e enviar-lhe-emos uma mensagem com instruções para a recuperar.';
$key_recuperar_pass_extramsg['ch']='If you forgot your password, please inform us about your e.mail and we will send you instructions to recover it.';
$key_recuperar_pass_extramsg['en']='If you forgot your password, please inform us about your e.mail and we will send you instructions to recover it.';
$key_recuperar_pass_extramsg['fr']='Si vous avez oublié votre mot de passe, Merci de nous indiquer votre email et nous vous enverrons un message avec les instructions de récupération.';
$key_recuperar_pass_extramsg['es']='Si ha olvidado su contraseña, por favor informenos su correo eletrónico y le enviaremos un mensaje con instrucciones para recuperarla.';
$key_recuperar_pass_extramsg['de']='Wenn Sie Ihr Passwort vergessen haben, bitte teilen Sie uns Ihre Email und wir senden Ihnen eine Nachricht mit Anweisungen zur Erholung.';
$key_recuperar_pass_extramsg["it"]='Se hai dimenticato la password, si prega di dirci il tuo indirizzo email e ti invieremo un messaggio con le istruzioni per il recupero.';

$key_recuperar_pass_msg['pt']="A sua password foi alterada com sucesso.";
$key_recuperar_pass_msg['ch']="Your password was successfully changed.";
$key_recuperar_pass_msg['en']="Your password was successfully changed.";
$key_recuperar_pass_msg['fr']="Votre mot de passe a été changé avec succès.";
$key_recuperar_pass_msg['es']="Su contraseña se ha cambiado correctamente.";
$key_recuperar_pass_msg['de']="Ihr Passwort wurde erfolgreich geändert.";
$key_recuperar_pass_msg["it"]="La password è stata modificata.";

$key_user_registadomsg['pt']='Utilizador já registado';
$key_user_registadomsg['ch']='User already registered';
$key_user_registadomsg['en']='User already registered';
$key_user_registadomsg['fr']='Utilisateur déjà enregistré';
$key_user_registadomsg['es']='Usuario ya registrado';
$key_user_registadomsg['de']='Benutzer, die bereits registriert ist';
$key_user_registadomsg["it"]='Utente già registrato';

$key_user_notregistadomsg['pt']='Se não é utilizador pode registar-se aqui';
$key_user_notregistadomsg['ch']='If you are not a user, you may register here';
$key_user_notregistadomsg['en']='If you are not a user, you may register here';
$key_user_notregistadomsg['fr']='Si vous n\'êtes pas un utilisateur, Inscrivez-vous ici';
$key_user_notregistadomsg['es']='Si no eres usuario, puedes  regsitrarte aquí';
$key_user_notregistadomsg['de']='Wenn Sie kein Benutzer sind, können Sie hier anmelden';
$key_user_notregistadomsg["it"]='Se non sei un utente, si può  registrati qui';

$key_iniciarsessao['pt']='Iniciar sessão';
$key_iniciarsessao['ch']='Login';
$key_iniciarsessao['en']='Login';
$key_iniciarsessao['fr']='Ouverture de session';
$key_iniciarsessao['es']='Iniciar sesión';
$key_iniciarsessao['de']='Anmeldung';
$key_iniciarsessao["it"]='Account di accesso';

$key_prdtag_new['pt']='Novo';
$key_prdtag_new['ch']='New';
$key_prdtag_new['en']='New';
$key_prdtag_new['fr']='Nouveau';
$key_prdtag_new['es']='Nuevo';
$key_prdtag_new['de']='Neu';
$key_prdtag_new["it"]='Nuovo';

$key_catalogo_ver_destaques['pt']='Ver todos os destaques';
$key_catalogo_ver_destaques['ch']='See all highlights';
$key_catalogo_ver_destaques['en']='See all highlights';
$key_catalogo_ver_destaques['fr']='Voir tous les faits saillants';
$key_catalogo_ver_destaques['es']='Ver todos destacados';
$key_catalogo_ver_destaques['de']='Finden Sie alle highlights';
$key_catalogo_ver_destaques["it"]='Vedere tutti i video sintesi';

$key_catalogo_sabermais['pt']='SABER MAIS';
$key_catalogo_sabermais['ch']='READ MORE';
$key_catalogo_sabermais['en']='READ MORE';
$key_catalogo_sabermais['fr']='LIRE LA SUITE';
$key_catalogo_sabermais['es']='LEER MÁS';
$key_catalogo_sabermais['de']='LESEN SIE MEHR';
$key_catalogo_sabermais["it"]='LEGGI DI PIÙ';

$key_catalogo_comprar['pt']='COMPRAR';
$key_catalogo_comprar['ch']='BUY';
$key_catalogo_comprar['en']='BUY';
$key_catalogo_comprar['fr']='ACHETER';
$key_catalogo_comprar['es']='COMPRAR';
$key_catalogo_comprar['de']='KAUFEN';
$key_catalogo_comprar["it"]='ACQUISTA';

$key_promocoes['pt']='Promoções';
$key_promocoes['ch']='Promotions';
$key_promocoes['en']='Promotions';
$key_promocoes['fr']='Promotions';
$key_promocoes['es']='Promociones';
$key_promocoes['de']='Angebote';
$key_promocoes["it"]='Promozioni';

$key_equivalentes_title['pt']='Melhor resultado quando usado com...';
$key_equivalentes_title['ch']='Best results when used with ...';
$key_equivalentes_title['en']='Best results when used with ...';
$key_equivalentes_title['fr']='Obtenir les meilleurs résultats lorsqu\'il est utilisé avec...';
$key_equivalentes_title['es']='Mejores resultados cuando se utiliza con...';
$key_equivalentes_title['de']='Beste Ergebnisse bei Verwendung mit...';
$key_equivalentes_title["it"]='Migliori risultati quando utilizzato con...';

$key_catalogo_ver_promocoes['pt']='Ver todas as promoções';
$key_catalogo_ver_promocoes['ch']='View all promotions';
$key_catalogo_ver_promocoes['en']='View all promotions';
$key_catalogo_ver_promocoes['fr']='Découvre toutes les promotions';
$key_catalogo_ver_promocoes['es']='Ver todas las promociones';
$key_catalogo_ver_promocoes['de']='Alle Aktionen ansehen';
$key_catalogo_ver_promocoes["it"]='Visualizza tutte le promozioni';

$key_registo_titulo['pt'] = "REGISTO";
$key_registo_titulo['ch'] = "REGISTRACE";
$key_registo_titulo['en'] = "REGISTRATION";
$key_registo_titulo['fr'] = "ENREGISTREMENT";
$key_registo_titulo['es'] = "REGISTRO";
$key_registo_titulo['de'] = "ANMELDUNG";
$key_registo_titulo["it"] = "REGISTRAZIONE";

$key_registo_subtitulo['pt'] = "Os seus dados pessoais";
$key_registo_subtitulo['ch'] = "Your personal information";
$key_registo_subtitulo['en'] = "Your personal information";
$key_registo_subtitulo['fr'] = "Vos renseignements personnels";
$key_registo_subtitulo['es'] = "Tu información personal";
$key_registo_subtitulo['de'] = "Persönliche Daten";
$key_registo_subtitulo["it"] = "Le informazioni personali";

$key_registo_subtitulo2['pt'] = "Dados utilizados apenas para envio de fatura e entrega da encomenda";
$key_registo_subtitulo2['ch'] = "Data used only to send the invoice and order";
$key_registo_subtitulo2['en'] = "Data used only to send the invoice and order";
$key_registo_subtitulo2['fr'] = "Données utilisées uniquement pour envoyer la facture et l'ordre";
$key_registo_subtitulo2['es'] = "Datos que se utilizan sólo para enviar la factura y el pedido";
$key_registo_subtitulo2['de'] = "Daten verwendet nur um die Rechnung und die Bestellung abzuschicken";
$key_registo_subtitulo2["it"] = "Dati utilizzati solo per inviare la fattura e l'ordine";

$key_cidade['pt'] = "Cidade";
$key_cidade['ch'] = "City";
$key_cidade['en'] = "City";
$key_cidade['fr'] = "Ville";
$key_cidade['es'] = "Ciudad";
$key_cidade['de'] = "Stadt";
$key_cidade["it"] = "Città";

$key_password_repetida['pt'] = "As passwords não correspondem";
$key_password_repetida['ch'] = "Passwords do not match";
$key_password_repetida['en'] = "Passwords do not match";
$key_password_repetida['fr'] = "Mots de passe ne correspondent pas";
$key_password_repetida['es'] = "Las contraseñas no coinciden";
$key_password_repetida['de'] = "Kennwörter stimmen nicht überein";
$key_password_repetida["it"] = "Le password non corrispondono";

$key_editarperfil_titulo['pt'] = "Editar a sua conta";
$key_editarperfil_titulo['ch'] = "Edit your account";
$key_editarperfil_titulo['en'] = "Edit your account";
$key_editarperfil_titulo['fr'] = "Modifiez votre compte";
$key_editarperfil_titulo['es'] = "Editar tu cuenta";
$key_editarperfil_titulo['de'] = "Ihr Konto bearbeiten";
$key_editarperfil_titulo["it"] = "Modificare il tuo account";

$key_editarperfil_subtitulo['pt'] = "Os seus dados pessoais";
$key_editarperfil_subtitulo['ch'] = "Your personal information";
$key_editarperfil_subtitulo['en'] = "Your personal information";
$key_editarperfil_subtitulo['fr'] = "Vos renseignements personnels";
$key_editarperfil_subtitulo['es'] = "Tu información personal";
$key_editarperfil_subtitulo['de'] = "Persönliche Daten";
$key_editarperfil_subtitulo["it"] = "Le informazioni personali";

$key_editarperfil_subtitulo2['pt'] = "Dados utilizados apenas para envio de fatura e entrega da encomenda";
$key_editarperfil_subtitulo2['ch'] = "Data used only to send the invoice and order";
$key_editarperfil_subtitulo2['en'] = "Data used only to send the invoice and order";
$key_editarperfil_subtitulo2['fr'] = "Données utilisées uniquement pour envoyer la facture et l'ordre";
$key_editarperfil_subtitulo2['es'] = "Datos que se utilizan sólo para enviar la factura y orden";
$key_editarperfil_subtitulo2['de'] = "Daten nur verwendet, um die Rechnung und Bestellung abschicken";
$key_editarperfil_subtitulo2["it"] = "Dati utilizzati solo per inviare la fattura e l'ordine";


$key_noticias_titulo['pt'] = "Notícias";
$key_noticias_titulo['ch'] = "News";
$key_noticias_titulo['en'] = "News";
$key_noticias_titulo['fr'] = "Nouvelles";
$key_noticias_titulo['es'] = "Noticias";
$key_noticias_titulo['de'] = "Nachrichten";
$key_noticias_titulo["it"] = "Notizie";

$key_wishlist['pt'] = "Favoritos";
$key_wishlist['ch'] = "Favorites";
$key_wishlist['en'] = "Favorites";
$key_wishlist['fr'] = "Favoris";
$key_wishlist['es'] = "Le gusta";
$key_wishlist['de'] = "Favoriten";
$key_wishlist["it"] = "Favoriti";


$key_sugira_titulo['pt'] = "Sugira o nosso website";
$key_sugira_titulo['ch'] = "Suggest our website";
$key_sugira_titulo['en'] = "Suggest our website";
$key_sugira_titulo['fr'] = "Suggérer notre site Web";
$key_sugira_titulo['es'] = "Sugerir nuestra página web";
$key_sugira_titulo['de'] = "Unsere Website vorschlagen";
$key_sugira_titulo["it"] = "Consiglia il nostro sito";

$key_sugira_subtitulo['pt'] = "O melhor elogio que podemos receber é a sua recomendação para a família e amigos.<br>Permita que outros que conhece também aproveitem o que temos para oferecer.<br>Vamos tratá-los como nossos amigos também.<br /><br />Obrigado por confiar em nós!<br />Preencha o formulário que se segue e clique em 'Enviar Email' para recomendar este site.";
$key_sugira_subtitulo['ch'] = "The best compliment we can receive is your recommendation to family and friends.<br/>allows others who knows also enjoy what we have to offer. <br> we treat them as our friends too<br/><br/>thank you for trusting us!<br/>Please fill out the form below and click ' send Email ' to recommend this site.";
$key_sugira_subtitulo['en'] = "The best compliment we can receive is your recommendation to family and friends.<br/>allows others who knows also enjoy what we have to offer. <br> we treat them as our friends too<br/><br/>thank you for trusting us!<br/>Please fill out the form below and click ' send Email ' to recommend this site.";
$key_sugira_subtitulo['fr'] = "Le meilleur compliment que nous pouvons recevoir est votre recommandation pour famille et amis. <br>permet à d'autres qui sait aussi profiter de ce que nous avons à offrir. <br>nous les traitons comme nos amis trop<br/><br/>je vous remercie pour la confiance nous!<br/>s'il vous plaît remplir le formulaire ci-dessous et cliquez « envoyer Email » recommander ce site.";
$key_sugira_subtitulo['es'] = "El mejor cumplido que podemos recibir es su recomendación a familiares y amigos. <br>permite a otros que también sabe disfrutar de lo que tenemos que ofrecer. <br>les tratamos como nuestros amigos también<br/><br/>gracias por confiar en nosotros!<br/>por favor llene el siguiente formulario y haga clic en 'enviar Email' para recomendar este sitio.";
$key_sugira_subtitulo['de'] = "Das beste Kompliment erhalten wir ist Ihre Empfehlung an Freunde und Familie. <br>ermöglicht, dass andere, die auch weiß zu genießen, was wir zu bieten haben. <br>wir behandeln sie als unsere Freunde zu<br/><br/>danken Ihnen für Ihr Vertrauen!<br/>Bitte füllen Sie das untenstehende Formular aus und klicken Sie auf'senden', diese Seite zu empfehlen.";
$key_sugira_subtitulo["it"] = "Il miglior complimento che possiamo ricevere è la tua raccomandazione a parenti e amici. <br>permette altri chissà anche godere di ciò che abbiamo da offrire. <br>li trattiamo come i nostri amici troppo<br/><br/>grazie per la fiducia accordataci!<br/>si prega di compilare il modulo qui sotto e clicca 'Invia Email' di raccomandare questo sito.";

$key_sugira_oseunome['pt'] = "O seu nome";
$key_sugira_oseunome['ch'] = "Your name";
$key_sugira_oseunome['en'] = "Your name";
$key_sugira_oseunome['fr'] = "Votre nom";
$key_sugira_oseunome['es'] = "Su nombre";
$key_sugira_oseunome['de'] = "Ihr Name";
$key_sugira_oseunome["it"] = "Il tuo nome";

$key_sugira_oseuemail['pt'] = "O seu e-mail";
$key_sugira_oseuemail['ch'] = "Your e-mail";
$key_sugira_oseuemail['en'] = "Your e-mail";
$key_sugira_oseuemail['fr'] = "Votre adresse e-mail";
$key_sugira_oseuemail['es'] = "Su correo electrónico";
$key_sugira_oseuemail['de'] = "Ihre e-Mail-Adresse";
$key_sugira_oseuemail["it"] = "Il tuo indirizzo e-mail";

$key_sugira_mensagem['pt'] = "Mensagem";
$key_sugira_mensagem['ch'] = "Message";
$key_sugira_mensagem['en'] = "Message";
$key_sugira_mensagem['fr'] = "Message";
$key_sugira_mensagem['es'] = "Mensaje";
$key_sugira_mensagem['de'] = "Nachricht";
$key_sugira_mensagem["it"] = "Messaggio";

$key_limitado['pt'] = "Limitado a 33 caracteres";
$key_limitado['ch'] = "Limited to 33 characters";
$key_limitado['en'] = "Limited to 33 characters";
$key_limitado['fr'] = "Limité à 33 caractères";
$key_limitado['es'] = "Limitada a 33 caracteres";
$key_limitado['de'] = "Limitiert auf 33 Zeichen";
$key_limitado["it"] = "Limitata a 33 caratteri";

$key_sugira_nomeamigo['pt'] = "O nome do seu amigo";
$key_sugira_nomeamigo['ch'] = "Your friend's name";
$key_sugira_nomeamigo['en'] = "Your friend's name";
$key_sugira_nomeamigo['fr'] = "Nom de votre ami";
$key_sugira_nomeamigo['es'] = "El nombre de tu amigo";
$key_sugira_nomeamigo['de'] = "Name Ihres Freundes";
$key_sugira_nomeamigo["it"] = "Nome del tuo amico";

$key_sugira_emailamigo['pt'] = "O e-mail do seu amigo";
$key_sugira_emailamigo['ch'] = "Your friend's email";
$key_sugira_emailamigo['en'] = "Your friend's email";
$key_sugira_emailamigo['fr'] = "E-mail de votre ami";
$key_sugira_emailamigo['es'] = "Correo electrónico de tu amigo";
$key_sugira_emailamigo['de'] = "E-Mail des Empfängers";
$key_sugira_emailamigo["it"] = "Mail del tuo amico";


$key_sugira_enviook["pt"] = "Obrigado pela sua recomenda&ccedil;&atilde;o.";
$key_sugira_enviook["ch"] = "Thank you for the suggestion.";
$key_sugira_enviook["en"] = "Thank you for the suggestion.";
$key_sugira_enviook["fr"] = "Merci pour la suggestion.";
$key_sugira_enviook["es"] = "Gracias por la sugerencia.";
$key_sugira_enviook["de"] = "Vielen Dank für die Anregung.";
$key_sugira_enviook["it"] = "Grazie per il suggerimento.";

$key_sugira_envioerro["pt"] = "Houve um problema no envio do email, por favor tente mais tarde.";
$key_sugira_envioerro["ch"] = "There was a problem sending email, please try again later.";
$key_sugira_envioerro["en"] = "There was a problem sending email, please try again later.";
$key_sugira_envioerro["fr"] = "Il y avait un problème d'envoi d'e-mail, Veuillez réessayer ultérieurement.";
$key_sugira_envioerro["es"] = "Hubo un problema enviando el correo electrónico, por favor Inténtalo más tarde.";
$key_sugira_envioerro["de"] = "Es gab ein Problem das Senden von e-Mails, bitte versuchen Sie es später erneut.";
$key_sugira_envioerro["it"] = "C'era un problema per l'invio di e-mail, si prega di riprovare più tardi.";


/* CHAT LIVE \ */

$key_chat_title["pt"]="Chat!";
$key_chat_title["ch"]="Chat!";
$key_chat_title["en"]="Chat!";
$key_chat_title["fr"]="Chat!";
$key_chat_title["es"]="Chat!";
$key_chat_title["de"]="Chat!";
$key_chat_title["it"]="Chat!";

$key_chat_online["pt"]="Estamos online!";
$key_chat_online["ch"]="We are online!";
$key_chat_online["en"]="We are online!";
$key_chat_online["fr"]="Nous sommes en ligne";
$key_chat_online["es"]="Estamos en línea";
$key_chat_online["de"]="Wir sind online";
$key_chat_online["it"]="Siamo online";

$key_chat_offline["pt"]="Estamos offline. Deixe-nos a sua mensagem.";
$key_chat_offline["ch"]="We are offline. Leave us a message.";
$key_chat_offline["en"]="We are offline. Leave us a message.";
$key_chat_offline["fr"]="Nous sommes en mode hors connexion. Laissez-nous un message.";
$key_chat_offline["es"]="Estamos offline. Nos dejan un mensaje.";
$key_chat_offline["de"]="Wir sind offline. Hinterlassen Sie uns eine Nachricht.";
$key_chat_offline["it"]="Siamo fuori linea. Lasciare un messaggio.";

$key_chat_away["pt"]="Estamos ausentes. Deixe-nos a sua mensagem.";
$key_chat_away["ch"]="We are away. Leave us a message.";
$key_chat_away["en"]="We are away. Leave us a message.";
$key_chat_away["fr"]="Nous en sommes loin. Laissez-nous un message.";
$key_chat_away["es"]="Estamos lejos. Nos dejan un mensaje.";
$key_chat_away["de"]="Wir sind entfernt. Hinterlassen Sie uns eine Nachricht.";
$key_chat_away["it"]="Siamo lontani. Lasciare un messaggio.";

$key_chat_msgoffline["pt"]="Estamos Offline";
$key_chat_msgoffline["ch"]="We are Offline";
$key_chat_msgoffline["en"]="We are Offline";
$key_chat_msgoffline["fr"]="Nous sommes hors ligne";
$key_chat_msgoffline["es"]="Estamos fuera de línea";
$key_chat_msgoffline["de"]="Wir sind Offline";
$key_chat_msgoffline["it"]="Siamo Offline";

$key_chat_msgonline["pt"]="Estamos Online";
$key_chat_msgonline["ch"]="We are Online";
$key_chat_msgonline["en"]="We are Online";
$key_chat_msgonline["fr"]="Nous sommes en ligne";
$key_chat_msgonline["es"]="Estamos en línea";
$key_chat_msgonline["de"]="Wir sind Online";
$key_chat_msgonline["it"]="Siamo Online";

$key_chat_msgoffline2["pt"]="Por favor deixe-nos o seu nome e endereço de email, iremos contactá-lo brevemente. Obrigado";
$key_chat_msgoffline2["ch"]="Please leave us your name and email address, we will contact you shortly. Thank you.";
$key_chat_msgoffline2["en"]="Please leave us your name and email address, we will contact you shortly. Thank you.";
$key_chat_msgoffline2["fr"]="Veuillez nous laisser votre nom et votre adresse email, nous vous contacterons sous peu. Merci.";
$key_chat_msgoffline2["es"]="Por favor, déjenos su nombre y correo electrónico, nos pondremos en contacto pronto. Gracias.";
$key_chat_msgoffline2["de"]="Bitte hinterlassen Sie uns Ihren Namen und Ihre e-Mail-Adresse, wir werden Sie in Kürze kontaktieren. Danke.";
$key_chat_msgoffline2["it"]="Si prega di lasciare il vostro nome e indirizzo email, ti contatteremo al più presto. Grazie.";

$key_chat_offline_send["pt"]="Enviar mensagem";
$key_chat_offline_send["ch"]="Send message";
$key_chat_offline_send["en"]="Send message";
$key_chat_offline_send["fr"]="Envoyer message";
$key_chat_offline_send["es"]="Enviar mensaje";
$key_chat_offline_send["de"]="Nachricht senden";
$key_chat_offline_send["it"]="Invia messaggio";

$key_chat_offline_sucesso["pt"]="Dados enviados com sucesso";
$key_chat_offline_sucesso["ch"]="Information sent correctly";
$key_chat_offline_sucesso["en"]="Information sent correctly";
$key_chat_offline_sucesso["fr"]="Informations envoyées correctement";
$key_chat_offline_sucesso["es"]="Información enviada correctamente";
$key_chat_offline_sucesso["de"]="Informationen korrekt gesendet";
$key_chat_offline_sucesso["it"]="Informazioni inviate correttamente";

$key_chat_txt["pt"] = "Por favor insira o seu email";
$key_chat_txt["ch"] = "Please insert your email";
$key_chat_txt["en"] = "Please insert your email";
$key_chat_txt["fr"] = "Veuillez insérer votre email";
$key_chat_txt["es"] = "Por favor, introduzca su correo electrónico";
$key_chat_txt["de"] = "Setzen Sie bitte Ihr email";
$key_chat_txt["it"] = "Si prega di inserire il tuo indirizzo email";

$key_chat_ola["pt"] = "Olá, como pudemos ajudá-lo?";
$key_chat_ola["ch"] = "Hello! How can we help you?";
$key_chat_ola["en"] = "Hello! How can we help you?";
$key_chat_ola["fr"] = "Salut! Comment pouvons-nous vous aider ?";
$key_chat_ola["es"] = "¡Hola! ¿Cómo podemos ayudarte?";
$key_chat_ola["de"] = "Hallo! Wie können wir Ihnen helfen?";
$key_chat_ola["it"] = "Ciao! Come possiamo aiutarvi?";

$key_chat_btn["pt"] = "ENVIAR";
$key_chat_btn["ch"] = "SEND";
$key_chat_btn["en"] = "SEND";
$key_chat_btn["fr"] = "ENVOYER";
$key_chat_btn["es"] = "ENVIAR";
$key_chat_btn["de"] = "SENDEN";
$key_chat_btn["it"] = "INVIARE";

/* FIM CHAT */

/* Favoritos */

$key_wishlist_title["pt"]="Favoritos";
$key_wishlist_title["ch"]="Favourites";
$key_wishlist_title["en"]="Favourites";
$key_wishlist_title["fr"]="Favoritos";
$key_wishlist_title["es"]="Favoritos";
$key_wishlist_title["de"]="Favoriten";
$key_wishlist_title["it"]="Favoriti";

$key_wishlist_subtitle["pt"]="Os seus produtos";
$key_wishlist_subtitle["ch"]="Your products";
$key_wishlist_subtitle["en"]="Your products";
$key_wishlist_subtitle["fr"]="Vos produits";
$key_wishlist_subtitle["es"]="Sus productos";
$key_wishlist_subtitle["de"]="Ihre Produkte";
$key_wishlist_subtitle["it"]="Vostri prodotti";

$key_wishlist_notloggedin["pt"]="É necessário estar autenticado.";
$key_wishlist_notloggedin["ch"]="You need to be authenticated";
$key_wishlist_notloggedin["en"]="You need to be authenticated";
$key_wishlist_notloggedin["fr"]="Vous devez être authentifié";
$key_wishlist_notloggedin["es"]="Usted necesita estar autenticado";
$key_wishlist_notloggedin["de"]="Sie müssen in einer beglaubigten";
$key_wishlist_notloggedin["it"]="È necessario essere autenticati";

$key_wishlist_semprodutos["pt"]="Lista de produtos favoritos encontra-se vazia.";
$key_wishlist_semprodutos["ch"]="The favourit products list is empty.";
$key_wishlist_semprodutos["en"]="The favourit products list is empty.";
$key_wishlist_semprodutos["fr"]="La liste de produits de favoritisme est vide.";
$key_wishlist_semprodutos["es"]="La lista de productos favourit está vacía.";
$key_wishlist_semprodutos["de"]="Die Produktliste Favourit ist leer.";
$key_wishlist_semprodutos["it"]="L'elenco di prodotti del favourit è vuoto.";

$key_wishlist_addallproducts["pt"]="Clique para adicionar todos os produtos ao Carrinho";
$key_wishlist_addallproducts["ch"]="Click to add all products to the shopping cart";
$key_wishlist_addallproducts["en"]="Click to add all products to the shopping cart";
$key_wishlist_addallproducts["fr"]="Cliquez pour ajouter tous les produits à votre panier";
$key_wishlist_addallproducts["es"]="Haga clic en Agregar todos los productos a la cesta de compras";
$key_wishlist_addallproducts["de"]="Hier klicken, um alle Produkte in den Warenkorb hinzufügen";
$key_wishlist_addallproducts["it"]="Fare clic per aggiungere tutti i prodotti al carrello";



/* TESTEMUNHOS */
$key_testemunhos_title["pt"]="Opiniões";
$key_testemunhos_title["ch"]="Opinions";
$key_testemunhos_title["en"]="Opinions";
$key_testemunhos_title["fr"]="Avis";
$key_testemunhos_title["es"]="Opiniones";
$key_testemunhos_title["de"]="Meinungen";
$key_testemunhos_title["it"]="Opinioni";

$key_testemunhos_giveopinion["pt"]="Dar a sua opinião";
$key_testemunhos_giveopinion["ch"]="Give your opinion";
$key_testemunhos_giveopinion["en"]="Give your opinion";
$key_testemunhos_giveopinion["fr"]="Donnez votre avis";
$key_testemunhos_giveopinion["es"]="Dar tu opinión";
$key_testemunhos_giveopinion["de"]="Geben Sie Ihre Meinung";
$key_testemunhos_giveopinion["it"]="Dare la vostra opinione";

$key_testemunhos_nodata["pt"]="Produto sem Opiniões";
$key_testemunhos_nodata["ch"]="No Product Reviews";
$key_testemunhos_nodata["en"]="No Product Reviews";
$key_testemunhos_nodata["fr"]="Aucun avis sur le produit";
$key_testemunhos_nodata["es"]="No hay comentarios del producto";
$key_testemunhos_nodata["de"]="Keine Bewertungen";
$key_testemunhos_nodata["it"]="Nessun prodotto recensioni";

$key_testemunhos_nome["pt"]="Nome";
$key_testemunhos_nome["ch"]="Name";
$key_testemunhos_nome["en"]="Name";
$key_testemunhos_nome["fr"]="Nom";
$key_testemunhos_nome["es"]="Nombre";
$key_testemunhos_nome["de"]="Name";
$key_testemunhos_nome["it"]="Nome";

$key_testemunhos_local["pt"]="Cidade";
$key_testemunhos_local["ch"]="City";
$key_testemunhos_local["en"]="City";
$key_testemunhos_local["fr"]="Ville";
$key_testemunhos_local["es"]="Ciudad";
$key_testemunhos_local["de"]="Stadt";
$key_testemunhos_local["it"]="Città";

$key_testemunhos_pais["pt"]="País";
$key_testemunhos_pais["ch"]="Country";
$key_testemunhos_pais["en"]="Country";
$key_testemunhos_pais["fr"]="Pays";
$key_testemunhos_pais["es"]="País";
$key_testemunhos_pais["de"]="Land";
$key_testemunhos_pais["it"]="Paese";

$key_testemunhos_mensagem["pt"]="Mensagem";
$key_testemunhos_mensagem["ch"]="Message";
$key_testemunhos_mensagem["en"]="Message";
$key_testemunhos_mensagem["fr"]="Message";
$key_testemunhos_mensagem["es"]="Mensaje";
$key_testemunhos_mensagem["de"]="Nachricht";
$key_testemunhos_mensagem["it"]="Messaggio";



/* SHOP */

$key_adicionar_cart["pt"]="Adicionar";
$key_adicionar_cart["ch"]="";
$key_adicionar_cart["en"]="Add to Cart";
$key_adicionar_cart["fr"]="Ajouter au Panier";
$key_adicionar_cart["es"]="";
$key_adicionar_cart["de"]="";
$key_adicionar_cart["it"]="";

$key_shop_noproducts["pt"]="Carrinho vazio";
$key_shop_noproducts["ch"]="Empty Cart";
$key_shop_noproducts["en"]="Empty Cart";
$key_shop_noproducts["fr"]="Panier vide";
$key_shop_noproducts["es"]="Carrito vacío";
$key_shop_noproducts["de"]="Leeren Warenkorb";
$key_shop_noproducts["it"]="Carrello vuoto";

$key_minishop_products["pt"]="PRODUTOS";
$key_minishop_products["ch"]="PRODUCTS";
$key_minishop_products["en"]="PRODUCTS";
$key_minishop_products["fr"]="PRODUITS";
$key_minishop_products["es"]="PRODUCTOS";
$key_minishop_products["de"]="PRODUKTE";
$key_minishop_products["it"]="PRODOTTI";

$key_minishop_product["pt"]="PRODUTO";
$key_minishop_product["ch"]="PRODUCT";
$key_minishop_product["en"]="PRODUCT";
$key_minishop_product["fr"]="PRODUIT";
$key_minishop_product["es"]="PRODUCTO";
$key_minishop_product["de"]="PRODUKT";
$key_minishop_product["it"]="PRODOTTO";

$key_minishop_hover["pt"]="Acessa ao seu carrinho de compras e finalize a sua encomenda";
$key_minishop_hover["ch"]="Access your shopping cart and finish your order";
$key_minishop_hover["en"]="Access your shopping cart and finish your order";
$key_minishop_hover["fr"]="Accéder à votre panier et terminer votre commande";
$key_minishop_hover["es"]="Accede a tu carrito y finalize su pedido";
$key_minishop_hover["de"]="Zugriff auf den Warenkorb legen und beenden Sie Ihren Auftrag";
$key_minishop_hover["it"]="Accedere al tuo carrello e completare il tuo ordine";

$key_minishop_hover2["pt"]="O seu carrinho não tem produtos";
$key_minishop_hover2["ch"]="Your cart is empty";
$key_minishop_hover2["en"]="Your cart is empty";
$key_minishop_hover2["fr"]="Votre panier est vide";
$key_minishop_hover2["es"]="La cesta esta vacía";
$key_minishop_hover2["de"]="Dein Wagen ist leer";
$key_minishop_hover2["it"]="Il tuo carrello è vuoto";

$key_wishlist_hover["pt"]="Consulte os seus produtos favoritos";
$key_wishlist_hover["ch"]="See your favorite products";
$key_wishlist_hover["en"]="See your favorite products";
$key_wishlist_hover["fr"]="Voir vos produits favoris";
$key_wishlist_hover["es"]="Ver tus productos favoritos";
$key_wishlist_hover["de"]="Finden Sie Ihren Lieblings-Produkte";
$key_wishlist_hover["it"]="Vedere i tuoi prodotti preferiti";

$key_shop_title["pt"]="Finalize a sua encomenda";
$key_shop_title["ch"]="Finish your order";
$key_shop_title["en"]="Finish your order";
$key_shop_title["fr"]="Terminer votre commande";
$key_shop_title["es"]="Finalizar su pedido";
$key_shop_title["de"]="Zur kasse";
$key_shop_title["it"]="Completare il suo ordine";

$key_shop_subtitle["pt"]="Os seus dados pessoais";
$key_shop_subtitle["ch"]="Your personal data";
$key_shop_subtitle["en"]="Your personal data";
$key_shop_subtitle["fr"]="Vos données personnelles";
$key_shop_subtitle["es"]="Sus datos personales";
$key_shop_subtitle["de"]="Persönliche Daten";
$key_shop_subtitle["it"]="Dati personali";

$key_shop_subtitle2["pt"]="Dados utilizados apenas para envio de fatura e entrega da encomenda";
$key_shop_subtitle2["ch"]="Data used only to send the invoice and delivery of the order";
$key_shop_subtitle2["en"]="Data used only to send the invoice and delivery of the order";
$key_shop_subtitle2["fr"]="Données utilisées uniquement pour envoyer la facture et la livraison de la commande";
$key_shop_subtitle2["es"]="Datos que se utilizan sólo para enviar la factura y la entrega del pedido";
$key_shop_subtitle2["de"]="Daten verwendet nur um die Rechnung und die Bestellung abzuschicken";
$key_shop_subtitle2["it"]="Dati utilizzati solo per inviare la fattura e la consegna dell'ordine";

//$key_shop_createaccount["pt"]="Criar conta com estes dados";
//$key_shop_createaccount["ch"]="Create an account with this data";
//$key_shop_createaccount["en"]="Create an account with this data";
$key_shop_createaccount["pt"]="Criar conta";
$key_shop_createaccount["ch"]="Create an account";
$key_shop_createaccount["en"]="Create an account";
$key_shop_createaccount["fr"]="Créer un compte";
$key_shop_createaccount["es"]="Crear una cuenta";
$key_shop_createaccount["de"]="Konto erstellen";
$key_shop_createaccount["it"]="Creare un account";

$key_shop_dadosdiferentes["pt"]="Clique aqui se os dados de entrega forem diferentes dos de faturação";
$key_shop_dadosdiferentes["ch"]="Click here if the delivery address is different from the billing one";
$key_shop_dadosdiferentes["en"]="Click here if the delivery address is different from the billing one";
$key_shop_dadosdiferentes["fr"]="Cliquez ici si l'adresse de livraison est différent de celle de facturation";
$key_shop_dadosdiferentes["es"]="Haga clic aquí si la dirección de entrega es diferente de la facturación";
$key_shop_dadosdiferentes["de"]="Klicken Sie hier wenn die Lieferadresse von der Rechnungsadresse verschieden ist";
$key_shop_dadosdiferentes["it"]="Se l'indirizzo di consegna è diverso da quello di fatturazione Clicca qui";

$key_shop_deliverytitle["pt"]="Enviar para outra morada";
$key_shop_deliverytitle["ch"]="Send to another address";
$key_shop_deliverytitle["en"]="Send to another address";
$key_shop_deliverytitle["fr"]="Envoyer à une autre adresse";
$key_shop_deliverytitle["es"]="Enviar a otra dirección";
$key_shop_deliverytitle["de"]="An einer anderen adresse";
$key_shop_deliverytitle["it"]="Invia a un altro indirizzo";



/* NEWSLETTER */

$key_newsletter_title["pt"]="Subscrever a newsletter";
$key_newsletter_title["ch"]="Subscribe newsletter";
$key_newsletter_title["en"]="Subscribe newsletter";
$key_newsletter_title["fr"]="S'abonner newsletter";
$key_newsletter_title["es"]="Subscribirse al newsletter";
$key_newsletter_title["de"]="Newsletter abonnieren";
$key_newsletter_title["it"]="Iscriviti alla newsletter";

$key_newsletter_subtitle["pt"]="";
$key_newsletter_subtitle["ch"]="";
$key_newsletter_subtitle["en"]="";
$key_newsletter_subtitle["fr"]="";
$key_newsletter_subtitle["es"]="";
$key_newsletter_subtitle["de"]="";
$key_newsletter_subtitle["it"]="";

$key_newsletter_nome["pt"]="O seu nome:";
$key_newsletter_nome["ch"]="Your name:";
$key_newsletter_nome["en"]="Your name:";
$key_newsletter_nome["fr"]="Votre nom";
$key_newsletter_nome["es"]="Su nombre:";
$key_newsletter_nome["de"]="Ihr Name:";
$key_newsletter_nome["it"]="Il tuo nome:";

$key_newsletter_email["pt"]="O seu email:";
$key_newsletter_email["ch"]="Your email:";
$key_newsletter_email["en"]="Your email:";
$key_newsletter_email["fr"]="Votre adresse email:";
$key_newsletter_email["es"]="Su correo electronico:";
$key_newsletter_email["de"]="Ihre e-Mail:";
$key_newsletter_email["it"]="Il tuo indirizzo email:";

$key_newsletter_registook["pt"]="O seu email foi registado com sucesso. <br /><b>Obrigado por se registar na nossa newsletter.</b>";
$key_newsletter_registook["ch"]="Your email has been successfully registered. <br /><b>Thank you for subscribing to our newsletter.</b>";
$key_newsletter_registook["en"]="Your email has been successfully registered. <br /><b>Thank you for subscribing to our newsletter.</b>";
$key_newsletter_registook["fr"]="Votre email a été enregistrée avec succès. <br /><b>je vous remercie pour l'abonnement à notre infolettre.</b>";
$key_newsletter_registook["es"]="Su correo electrónico a sido registrado con sucesso. <br /><b>Gracias por subscribirse a nuestra newsletter.</b>";
$key_newsletter_registook["de"]="Ihre e-Mail wurde erfolgreich registriert. <br /><b>vielen Dank für das abonnieren unseres Newsletters.</b>";
$key_newsletter_registook["it"]="Il tuo indirizzo email è stato registrato correttamente. <br /><b>grazie per esserti iscritto alla nostra newsletter.</b>";

$key_newsletter_registoerro1["pt"]="Ocorreu um erro ao registar na nossa newsletter, volte a tentar mais tarde. Obrigado.";
$key_newsletter_registoerro1["ch"]="An error occurred while registering in our newsletter, try again later. Thank you.";
$key_newsletter_registoerro1["en"]="An error occurred while registering in our newsletter, try again later. Thank you.";
$key_newsletter_registoerro1["fr"]="Une erreur s'est produite lors de l'inscription à notre newsletter, essayez à nouveau plus tard. Merci.";
$key_newsletter_registoerro1["es"]="Un error ha accured en la inscrición a nuestro boletín, por favor intente de nuevo más tarde . Gracias";
$key_newsletter_registoerro1["de"]="Fehler bei der Registrierung in unserem Newsletter, versuchen Sie es später erneut. Danke.";
$key_newsletter_registoerro1["it"]="Si è verificato un errore durante la registrazione nella nostra newsletter, riprovare più tardi. Grazie.";

$key_newsletter_registoerro2["pt"]="O seu email já se encontra subscrito à nossa newsletter. Obrigado";
$key_newsletter_registoerro2["ch"]="Your email is allready subscribed to your newsletter. Thank you";
$key_newsletter_registoerro2["en"]="Your email is allready subscribed to your newsletter. Thank you";
$key_newsletter_registoerro2["fr"]="Votre email est deja inscrit à votre newsletter. Merci";
$key_newsletter_registoerro2["es"]="Su correo es ya suscrito a tu boletín de noticias. Gracias";
$key_newsletter_registoerro2["de"]="Ihre e-Mail ist bereits den Newsletter abonniert. Danke";
$key_newsletter_registoerro2["it"]="Il tuo indirizzo email è già iscritto alla newsletter. Grazie";


/* COMENTÁRIOS */

$key_comentarios_title["pt"]="Comentários";
$key_comentarios_title["ch"]="Comments";
$key_comentarios_title["en"]="Comments";
$key_comentarios_title["fr"]="Commentaires";
$key_comentarios_title["es"]="Comentarios";
$key_comentarios_title["de"]="Kommentare";
$key_comentarios_title["it"]="Commenti";

$key_comentarios_giveopinion["pt"]="Deixe o seu comentário";
$key_comentarios_giveopinion["ch"]="Leave your comment";
$key_comentarios_giveopinion["en"]="Leave your comment";
$key_comentarios_giveopinion["fr"]="Laissez votre commentaire";
$key_comentarios_giveopinion["es"]="Deja tu comentario";
$key_comentarios_giveopinion["de"]="Lassen Sie Ihren Kommentar";
$key_comentarios_giveopinion["it"]="Lascia il tuo commento";

$key_comentarios_nodata["pt"]="Notícia sem comentários";
$key_comentarios_nodata["ch"]="News no comments";
$key_comentarios_nodata["en"]="News no comments";
$key_comentarios_nodata["fr"]="News aucun commentaire";
$key_comentarios_nodata["es"]="Noticias no hay comentarios";
$key_comentarios_nodata["de"]="News keine Kommentare";
$key_comentarios_nodata["it"]="Notizie Nessun commento";

$key_comentarios_nome["pt"]="Nome";
$key_comentarios_nome["ch"]="Name";
$key_comentarios_nome["en"]="Name";
$key_comentarios_nome["fr"]="Nom";
$key_comentarios_nome["es"]="Nombre";
$key_comentarios_nome["de"]="Name";
$key_comentarios_nome["it"]="Nome";

$key_comentarios_local["pt"]="Cidade";
$key_comentarios_local["ch"]="City";
$key_comentarios_local["en"]="City";
$key_comentarios_local["fr"]="Ville";
$key_comentarios_local["es"]="Ciudad";
$key_comentarios_local["de"]="Stadt";
$key_comentarios_local["it"]="Città";

$key_comentarios_pais["pt"]="Pais";
$key_comentarios_pais["ch"]="Country";
$key_comentarios_pais["en"]="Country";
$key_comentarios_pais["fr"]="Pays";
$key_comentarios_pais["es"]="País";
$key_comentarios_pais["de"]="Land";
$key_comentarios_pais["it"]="Paese";

$key_comentarios_mensagem["pt"]="Mensagem";
$key_comentarios_mensagem["ch"]="Message";
$key_comentarios_mensagem["en"]="Message";
$key_comentarios_mensagem["fr"]="Message";
$key_comentarios_mensagem["es"]="Mensaje";
$key_comentarios_mensagem["de"]="Nachricht";
$key_comentarios_mensagem["it"]="Messaggio";

$key_shop_embulho["pt"]="Necessita preencher a mensagem de embrulho";
$key_shop_embulho["ch"]="Need to fill out the message wrapping paper";
$key_shop_embulho["en"]="Need to fill out the message wrapping paper";
$key_shop_embulho["fr"]="Devez remplir le message papier d'emballage";
$key_shop_embulho["es"]="Tiene que llenar el mensaje de papel de embalaje";
$key_shop_embulho["de"]="Die Nachricht Packpapier ausfüllen";
$key_shop_embulho["it"]="Necessario compilare il messaggio carta da imballaggio";

$key_shop_termos["pt"]="Necessita de aceitar os termos de compra";
$key_shop_termos["ch"]="You need to accept the terms of purchase";
$key_shop_termos["en"]="You need to accept the terms of purchase";
$key_shop_termos["fr"]="Vous devez accepter les conditions d'achat";
$key_shop_termos["es"]="Debes aceptar los términos de la compra";
$key_shop_termos["de"]="Sie müssen die Einkaufsbedingungen akzeptieren";
$key_shop_termos["it"]="È necessario accettare le condizioni di acquisto";

$key_carrinho_voucher["pt"] = "O Código de Promoção já existe no carrinho";
$key_carrinho_voucher["ch"] = "The Promotion Code already exists in cart";
$key_carrinho_voucher["en"] = "The Promotion Code already exists in cart";
$key_carrinho_voucher["fr"] = "Le Code de Promotion existe déjà dans le panier";
$key_carrinho_voucher["es"] = "El código de promoción ya existe en carro";
$key_carrinho_voucher["de"] = "Der Promotion-Code ist bereits im Warenkorb vorhanden";
$key_carrinho_voucher["it"] = "Il codice di promozione esiste già nel carrello";

$key_carrinho_voucher2["pt"] = "O Código de Promoção não existe ou já não se encontra ativo";
$key_carrinho_voucher2["ch"] = "The Promotion Code does not exist or is no longer active";
$key_carrinho_voucher2["en"] = "The Promotion Code does not exist or is no longer active";
$key_carrinho_voucher2["fr"] = "Le Code de Promotion n'existe pas ou n'est plus actif";
$key_carrinho_voucher2["es"] = "El código de promoción no existe o ya no está activo";
$key_carrinho_voucher2["de"] = "Der Promotion-Code existiert nicht oder ist nicht mehr aktiv";
$key_carrinho_voucher2["it"] = "Il codice di promozione non esiste o non è più attivo";

// Voucher stuff
$key_codigo_usado["pt"] = "O código que está a tentar usar já foi usado anteriormente!";
$key_codigo_usado["ch"] = "The code you are trying to use has been used before!";
$key_codigo_usado["en"] = "The code you are trying to use has been used before!";
$key_codigo_usado["fr"] = "Le code que vous essayez d'utiliser a été utilisé avant !";
$key_codigo_usado["es"] = "El código que está intentando utilizar ha sido utilizado antes!";
$key_codigo_usado["de"] = "Der Code, den Sie verwenden möchten ist vor der Benutzung!";
$key_codigo_usado["it"] = "Il codice che si sta tentando di utilizzare è stato utilizzato prima!";

$key_codigo_inexistente["pt"] = "O código inserido não é um código válido!";
$key_codigo_inexistente["ch"] = "The inserted code is not valid";
$key_codigo_inexistente["en"] = "The inserted code is not valid";
$key_codigo_inexistente["fr"] = "Le code inséré n'est pas valide";
$key_codigo_inexistente["es"] = "El código introducido no es válido";
$key_codigo_inexistente["de"] = "Der eingefügte Code ist nicht gültig";
$key_codigo_inexistente["it"] = "Il codice inserito non è valido";

$key_voucher_oferta["pt"] = "Voucher - Produto Oferta";
$key_voucher_oferta["ch"] = "Voucher - Product Offer";
$key_voucher_oferta["en"] = "Voucher - Product Offer";
$key_voucher_oferta["fr"] = "Bon - Offre Produit";
$key_voucher_oferta["es"] = "Vale - Oferta de productos";
$key_voucher_oferta["de"] = "Gutschein - Produktangebot";
$key_voucher_oferta["it"] = "Voucher - Offerta di Prodotti";

$key_voucher_oferta2["pt"] = "Produto Oferta";
$key_voucher_oferta2["ch"] = "Product Offer";
$key_voucher_oferta2["en"] = "Product Offer";
$key_voucher_oferta2["fr"] = "Offre de produits";
$key_voucher_oferta2["es"] = "Oferta de productos";
$key_voucher_oferta2["de"] = "Produkt-Angebot";
$key_voucher_oferta2["it"] = "Offerta di prodotto";

$key_voucher_desconto["pt"] = "Voucher de desconto";
$key_voucher_desconto["ch"] = "Discount Voucher";
$key_voucher_desconto["en"] = "Discount Voucher";
$key_voucher_desconto["fr"] = "Coupon de réduction";
$key_voucher_desconto["es"] = "Cupón de descuento";
$key_voucher_desconto["de"] = "Rabatt-Gutschein";
$key_voucher_desconto["it"] = "Buono sconton";


$key_codigopromo["pt"] = "Código de Promoção";
$key_codigopromo["ch"] = "Promotion Code";
$key_codigopromo["en"] = "Promotion Code";
$key_codigopromo["fr"] = "Code promotionnel";
$key_codigopromo["es"] = "Código de promoción";
$key_codigopromo["de"] = "Promotion-Code";
$key_codigopromo["it"] = "Codice promozione";

$key_shop_ofertas["pt"] = "Apenas pode escolher entre 1 a 3 marcas para receber 3 amostras gratuitas!";
$key_shop_ofertas["ch"] = "Only you can choose between 1 to 3 brands to receive 3 free samples!";
$key_shop_ofertas["en"] = "Only you can choose between 1 to 3 brands to receive 3 free samples!";
$key_shop_ofertas["fr"] = "Seulement, vous pouvez choisir entre 1 à 3 marques de recevoir 3 échantillons gratuits !";
$key_shop_ofertas["es"] = "Sólo se puede elegir entre 1 a 3 marcas para recibir 3 muestras gratis!";
$key_shop_ofertas["de"] = "Sie können nur zwischen 1 bis 3 Marken erhalten Sie 3 kostenlose Proben!";
$key_shop_ofertas["it"] = "Solo è possibile scegliere tra le marche di 1 a 3 per ricevere 3 campioni gratuiti!";

$key_codigo_invalido_cliente["pt"] = "Dado que não é o cliente associado a este código, não o pode usar!";
$key_codigo_invalido_cliente["ch"] = "Since you're not the client associated to this code, you can't use it!";
$key_codigo_invalido_cliente["en"] = "Since you're not the client associated to this code, you can't use it!";
$key_codigo_invalido_cliente["fr"] = "Puisque vous n'êtes pas le client associé à ce code, vous ne peut pas l'utiliser !";
$key_codigo_invalido_cliente["es"] = "Ya no eres el cliente asociado a este código, se puede utilizar!";
$key_codigo_invalido_cliente["de"] = "Da Sie nicht den Client, dieser Code zugeordnet sind, können Sie es nicht verwenden!";
$key_codigo_invalido_cliente["it"] = "Visto che non sei il client associato a questo codice, non è possibile utilizzare!";




$key_home_description["pt"] = "Clique para voltar à homepage";
$key_home_description["ch"] = "Click to return to the homepage";
$key_home_description["en"] = "Click to return to the homepage";
$key_home_description["fr"] = "Cliquer ici pour revenir à la homepage";
$key_home_description["es"] = "Haga clic en volver a la homepage";
$key_home_description["de"] = "Hier klicken, um zurück zu der homepage";
$key_home_description["it"] = "Fare clic per tornare alla homepage";

$key_unicre_part1["pt"] = "<br>Obrigado por ter efecuado a sua compra na nossa loja.<br><br>
            Os dados relativos à sua transacção são os seguintes:<br>
            Nome do cliente: ";
$key_unicre_part1["ch"] = "<br>Thank you for purchasing in our store. <br> <br> The information relating to your transaction is as follows: <br> Client name: ";
$key_unicre_part1["en"] = "<br>Thank you for purchasing in our store. <br> <br> The information relating to your transaction is as follows: <br> Client name: ";
$key_unicre_part1["fr"] = "<br>Nous vous remercions d'avoir acheté dans notre magasin. <br><br>Les informations concernant votre transaction sont comme suit : <br>nom du Client: ";
$key_unicre_part1["es"] = "<br>Gracias por comprar en nuestra tienda. <br><br>La información relativa a su transacción es el siguiente: nombre de <br>cliente: ";
$key_unicre_part1["de"] = "<br>Vielen Dank für den Einkauf in unserem Shop. <br><br>Die Informationen bezüglich Ihrer Transaktion lautet wie folgt: <br>Client-Namen: ";
$key_unicre_part1["it"] = "<br>Vi ringraziamo per l'acquisto nel nostro negozio. <br><br>Le informazioni relative alla transazione sono come segue: nome <br>Client: ";

$key_unicre_part2["pt"] = "<br>Valor da transacção: ";
$key_unicre_part2["ch"] = "<br>Transaction value: ";
$key_unicre_part2["en"] = "<br>Transaction value: ";
$key_unicre_part2["fr"] = "<br>Valeur transactionnelle";
$key_unicre_part2["es"] = "<br>Valor de transacción: ";
$key_unicre_part2["de"] = "<br>Transaktionswert: ";
$key_unicre_part2["it"] = "<br>Valore di transazione: ";

$key_unicre_part3["pt"] = "<br>Data da transacção: ";
$key_unicre_part3["ch"] = "<br>Transaction date: ";
$key_unicre_part3["en"] = "<br>Transaction date: ";
$key_unicre_part3["fr"] = "<br>Date de la transaction";
$key_unicre_part3["es"] = "<br>Data de la transacción: ";
$key_unicre_part3["de"] = "<br>Transaktionsdatum: ";
$key_unicre_part3["it"] = "<br>Data della transazione: ";

$key_unicre_part4["pt"] = "<br>Numero da encomenda: ";
$key_unicre_part4["ch"] = "<br>Order number: ";
$key_unicre_part4["en"] = "<br>Order number: ";
$key_unicre_part4["fr"] = "<br>Numéro de commande: ";
$key_unicre_part4["es"] = "<br>Número de la orden: ";
$key_unicre_part4["de"] = "<br>Nummer der Bestellung: ";
$key_unicre_part4["it"] = "<br>Numero dell'ordine: ";

$key_unicre_part5["pt"] = "<br><br>Poderá ver informação mais detalhada sobre esta transacção nas suas encomendas.<br>";
$key_unicre_part5["ch"] = "<br><br>You can see more detailed information about this transaction in your orders.<br>";
$key_unicre_part5["en"] = "<br><br>You can see more detailed information about this transaction in your orders.<br>";
$key_unicre_part5["fr"] = "<br><br>Vous pouvez voir des informations plus détaillées sur cette transaction dans vos commandes.<br>";
$key_unicre_part5["es"] = "<br><br>Puede ver información más detallada sobre esta transacción en sus pedidos.<br>";
$key_unicre_part5["de"] = "<br><br>Ausführlichere Informationen zu dieser Transaktion sehen Sie in Ihrer Aufträge.<br>";
$key_unicre_part5["it"] = "<br><br>Si possono vedere informazioni più dettagliate su questa transazione nei vostri ordini.<br>";


/*LOGIN*/
$key_login_loggedin["pt"] = 'Olá';
$key_login_loggedin["ch"] = 'Hello';
$key_login_loggedin["en"] = 'Hello';
$key_login_loggedin["fr"] = 'Bonjour';
$key_login_loggedin["es"] = 'Hola';
$key_login_loggedin["de"] = 'Hallo';
$key_login_loggedin["it"] = 'Ciao';

$key_login_notloggedin["pt"] = 'LOGIN OU REGISTE-SE';
$key_login_notloggedin["ch"] = 'LOGIN OR REGISTER';
$key_login_notloggedin["en"] = 'LOGIN OR REGISTER';
$key_login_notloggedin["fr"] = 'SE CONNECTER OU S\'INSCRIRE';
$key_login_notloggedin["es"] = 'INICIE SESIÓN O REGÍSTRESE';
$key_login_notloggedin["de"] = 'LOGIN ODER REGISTRIEREN';
$key_login_notloggedin["it"] = 'LOGIN / INSCRIVERSI';

$key_login_socialpremsg["pt"] = 'ou faça login usando';
$key_login_socialpremsg["ch"] = 'or login with';
$key_login_socialpremsg["en"] = 'or login with';
$key_login_socialpremsg["fr"] = 'ou connectez vous avec';
$key_login_socialpremsg["es"] = 'o inicio de sesión con';
$key_login_socialpremsg["de"] = 'oder loggen Sie sich mit';
$key_login_socialpremsg["it"] = 'o effettua il login con';

$key_login["pt"] = 'ENTRAR';
$key_login["ch"] = 'ENTER';
$key_login["en"] = 'ENTER';
$key_login["fr"] = 'ENTREZ';
$key_login["es"] = 'ENTRAR';
$key_login["de"] = 'EINGEBEN';
$key_login["it"] = 'EINGEBEN';


$key_website_locked["pt"] = 'Brevemente online';
$key_website_locked["ch"] = 'Online soon';
$key_website_locked["en"] = 'Online soon';
$key_website_locked["fr"] = 'En ligne bientôt';
$key_website_locked["es"] = 'Brevemente en línea';
$key_website_locked["de"] = 'Online bald';
$key_website_locked["it"] = 'Online presto';

$key_website_redirect["pt"] = 'Em instantes você será redirecionado. Se o redirecionamento automático não funcionar, clique <a href="{$referer}">aqui</a>';
$key_website_redirect["ch"] = 'In a few moments you will be redirected. If the automatic redirection doesnt work, click <a href="{$referer}">here</a>';
$key_website_redirect["en"] = 'In a few moments you will be redirected. If the automatic redirection doesnt work, click <a href="{$referer}">here</a>';
$key_website_redirect["fr"] = 'En unos instantes será redirigido. Si la redirección automática no funciona, haga click <a href="{$referer}">ici</a>';
$key_website_redirect["es"] = 'En unos momentos se le dirigirá, si la redirección no está automáticamente clic <a href="{$referer}">aquí</a>';
$key_website_redirect["de"] = 'In wenigen Augenblicken werden Sie weitergeleitet. Wenn die automatische Weiterleitung nicht funktioniert, klicken Sie <a href="{$referer}">hier</a>';
$key_website_redirect["it"] = 'In pochi istanti sarai reindirizzato. Se il reindirizzamento automatico non funziona, clicca <a href="{$referer}">hier</a>';

$key_website_emailerrado["pt"] = 'Por favor insira um email correcto';
$key_website_emailerrado["ch"] = 'Please enter a valid email';
$key_website_emailerrado["en"] = 'Please enter a valid email';
$key_website_emailerrado["fr"] = 'Veuillez entrer une adresse email valide';
$key_website_emailerrado["es"] = 'Por favor ingrese un email válido';
$key_website_emailerrado["de"] = 'Bitte geben Sie eine gültige e-Mail';
$key_website_emailerrado["it"] = 'Si prega di inserire un indirizzo email valido';

$key_afiliados["pt"] = "Afiliado";
$key_afiliados["ch"] = "Affiliated";
$key_afiliados["en"] = "Affiliated";
$key_afiliados["fr"] = "Affilié";
$key_afiliados["es"] = "Afiliados";
$key_afiliados["de"] = "Verbundenen";
$key_afiliados["it"] = "Affiliati";


$key_seunome["pt"] = "Email";
$key_seunome["ch"] = "Email";
$key_seunome["en"] = "Email";
$key_seunome["fr"] = "Email";
$key_seunome["es"] = "Email";
$key_seunome["de"] = "Email";
$key_seunome["it"] = "Email";


$key_nuvem["pt"] = "Nuvem";
$key_nuvem["ch"] = "Cloud";
$key_nuvem["en"] = "Cloud";
$key_nuvem["fr"] = "Nuage";
$key_nuvem["es"] = "Nube";
$key_nuvem["de"] = "Wolke";
$key_nuvem["it"] = "Nuvola";

$key_coracao["pt"] = "Coração";
$key_coracao["ch"] = "Heart";
$key_coracao["en"] = "Heart";
$key_coracao["fr"] = "Coeur";
$key_coracao["es"] = "Corazón";
$key_coracao["de"] = "Herz";
$key_coracao["it"] = "Cuore";


$key_prod_cart["pt"] = "PRODUTO";
$key_prod_cart["ch"] = "PRODUCT";
$key_prod_cart["en"] = "PRODUCT";
$key_prod_cart["fr"] = "PRODUIT";
$key_prod_cart["es"] = "PRODUCTO";
$key_prod_cart["de"] = "PRODUKT";
$key_prod_cart["it"] = "PRODOTTO";

$key_prod_desc["pt"] = "DESCRIÇÃO";
$key_prod_desc["ch"] = "DESCRIPTION";
$key_prod_desc["en"] = "DESCRIPTION";
$key_prod_desc["fr"] = "DESCRIPTION";
$key_prod_desc["es"] = "DESCRIPCIÓN";
$key_prod_desc["de"] = "BESCHREIBUNG";
$key_prod_desc["it"] = "DESCRIZIONE";


$key_prod_mont["pt"] = "UNITÁRIO";
$key_prod_mont["ch"] = "AMOUNT";
$key_prod_mont["en"] = "AMOUNT";
$key_prod_mont["fr"] = "MONTANT";
$key_prod_mont["es"] = "UNITARIO";
$key_prod_mont["de"] = "BETRAG";
$key_prod_mont["it"] = "IMPORTO";

$key_prod_confira["pt"] = "Confira a sua encomenda";
$key_prod_confira["ch"] = "Check your order";
$key_prod_confira["en"] = "Check your order";
$key_prod_confira["fr"] = "Vérifiez votre commande";
$key_prod_confira["es"] = "Compruebe su pedido";
$key_prod_confira["de"] = "Bestätigen";
$key_prod_confira["it"] = "Controlla il tuo ordine";

$key_prod_obs["pt"] = "Observações";
$key_prod_obs["ch"] = "Comments";
$key_prod_obs["en"] = "Comments";
$key_prod_obs["fr"] = "Commentaires";
$key_prod_obs["es"] = "Observaciones";
$key_prod_obs["de"] = "Bemerkungen";
$key_prod_obs["it"] = "Commenti";

$key_prod_condic["pt"] = "Eu aceito os termos de uso";
$key_prod_condic["ch"] = "I accept the terms of use";
$key_prod_condic["en"] = "I accept the terms of use";
$key_prod_condic["fr"] = "J'accepte les conditions d'utilisation";
$key_prod_condic["es"] = "Acepto los términos de uso";
$key_prod_condic["de"] = "Ich akzeptiere die Nutzungsbedingungen";
$key_prod_condic["it"] = "Accetto i termini d'uso";

$key_prod_termos["pt"] = "Veja os termos de uso";
$key_prod_termos["ch"] = "See Terms of use";
$key_prod_termos["en"] = "See Terms of use";
$key_prod_termos["fr"] = "Voir les conditions d'utilisation";
$key_prod_termos["es"] = "Ver términos de uso";
$key_prod_termos["de"] = "Siehe Nutzungsbedingungen";
$key_prod_termos["it"] = "Vedi condizioni d'uso";

$key_prod_finaliza["pt"] = "Finalizar Compra ";
$key_prod_finaliza["ch"] = "Checkout";
$key_prod_finaliza["en"] = "Checkout";
$key_prod_finaliza["fr"] = "Checkout";
$key_prod_finaliza["es"] = "Checkout";
$key_prod_finaliza["de"] = "Zur kasse";
$key_prod_finaliza["it"] = "Check-out";

$key_prod_tsiva["pt"] = "Total sem IVA";
$key_prod_tsiva["ch"] = "Total without VAT";
$key_prod_tsiva["en"] = "Total without VAT";
$key_prod_tsiva["fr"] = "Total sans TVA";
$key_prod_tsiva["es"] = "Total sin IVA";
$key_prod_tsiva["de"] = "Gesamt ohne MWST";
$key_prod_tsiva["it"] = "Total sin IVA";

$key_prod_gastos["pt"] = "Gastos de envio";
$key_prod_gastos["ch"] = "Delivery Charges";
$key_prod_gastos["en"] = "Delivery Charges";
$key_prod_gastos["fr"] = "Frais de livraison";
$key_prod_gastos["es"] = "Gastos de envío";
$key_prod_gastos["de"] = "Liefer-und Versandkosten";
$key_prod_gastos["it"] = "Spese di spedizione";

$key_prod_gratuito["pt"] = "Gratuíto";
$key_prod_gratuito["ch"] = "Free";
$key_prod_gratuito["en"] = "Free";
$key_prod_gratuito["fr"] = "Gratuit";
$key_prod_gratuito["es"] = "Gratis";
$key_prod_gratuito["de"] = "Kostenlose";
$key_prod_gratuito["it"] = "Gratis";

$key_prod_iva["pt"] = "IVA";
$key_prod_iva["ch"] = "VAT";
$key_prod_iva["en"] = "VAT";
$key_prod_iva["fr"] = "TVA";
$key_prod_iva["es"] = "IVA";
$key_prod_iva["de"] = "MWST";
$key_prod_iva["it"] = "IVA";

$key_prod_taxa["pt"] = "Taxas";
$key_prod_taxa["ch"] = "Tax";
$key_prod_taxa["en"] = "Tax";
$key_prod_taxa["fr"] = "Taxe";
$key_prod_taxa["es"] = "Impuesto";
$key_prod_taxa["de"] = "Steuer";
$key_prod_taxa["it"] = "Tassa di soggiorno";

$key_prod_metpag["pt"] = "Escolha a forma de pagamento que pretende";
$key_prod_metpag["ch"] = "Choose the payment method";
$key_prod_metpag["en"] = "Choose the payment method";
$key_prod_metpag["fr"] = "Choisissez le mode de paiement";
$key_prod_metpag["es"] = "Elegir el método de pago";
$key_prod_metpag["de"] = "Wählen Sie die Zahlungsmethode";
$key_prod_metpag["it"] = "Scegli il metodo di pagamento";


$key_prod_metpag2["pt"] = "Apenas usamos as formas mais seguras";
$key_prod_metpag2["ch"] = "We only use the safest forms";
$key_prod_metpag2["en"] = "We only use the safest forms";
$key_prod_metpag2["fr"] = "Nous n'utilisons que les formes plus sûres";
$key_prod_metpag2["es"] = "Utilizamos solamente las formas más seguras";
$key_prod_metpag2["de"] = "Wir benutzen nur die sichersten Formenn";
$key_prod_metpag2["it"] = "Usiamo solo le forme più sicure";

$key_total_receber["pt"] = "Total a Receber";
$key_total_receber["ch"] = "Amount to receive";
$key_total_receber["en"] = "Amount to receive";
$key_total_receber["fr"] = "Montant à recevoir";
$key_total_receber["es"] = "Cantidad a recibir";
$key_total_receber["de"] = "Menge zu empfangen";
$key_total_receber["it"] = "Importo da ricevere";

$key_banner1["pt"] = "Pode optar por usar um dos nossos Banners, Banner Horizontal 728 x 90";
$key_banner1["ch"] = "You can choose to use one of our banners, Banner Horizontal 728 x 90";
$key_banner1["en"] = "You can choose to use one of our banners, Banner Horizontal 728 x 90";
$key_banner1["fr"] = "Vous pouvez choisir d'utiliser une de nos bannières, Horizontal bannière 728 x 90";
$key_banner1["es"] = "Usted puede elegir utilizar uno de nuestros banners, Horizontal Banner 728 x 90";
$key_banner1["de"] = "Sie können wählen, verwenden Sie eines unserer Banner, horizontale Banner 728 x 90";
$key_banner1["it"] = "È possibile scegliere di utilizzare uno dei nostri banner, Banner orizzontale 728 x 90";

$key_banner2["pt"] = "Banner Vertical 120 x 600";
$key_banner2["ch"] = "Bannière verticale 120 x 600";
$key_banner2["en"] = "Bannière verticale 120 x 600";
$key_banner2["fr"] = "Bannière verticale 120 x 600";
$key_banner2["es"] = "Banner vertical 120 x 600";
$key_banner2["de"] = "Bannière verticale 120 x 600";
$key_banner2["it"] = "Banner verticale 120 x 600";

$key_banner3["pt"] = "Quadrado 300 x 250";
$key_banner3["ch"] = "Square 300 x 250";
$key_banner3["en"] = "Square 300 x 250";
$key_banner3["fr"] = "Place 300 x 250";
$key_banner3["es"] = "Cuadrado 300 x 250";
$key_banner3["de"] = "Platz 300 x 250";
$key_banner3["it"] = "Piazza 300 x 250";

$key_outrascores["pt"] = "OUTRAS CORES";
$key_outrascores["ch"] = "OTHER COLOURS";
$key_outrascores["en"] = "OTHER COLOURS";
$key_outrascores["fr"] = "AUTRES COULEURS";
$key_outrascores["es"] = "OTRAS COLORES";
$key_outrascores["de"] = "ANDERE FARBEN";
$key_outrascores["it"] = "ALTRI COLORI";

$key_paypal_a["pt"] = "Dentro de momento será reencaminhado para o Paypal para fazer o pagamento, Se não for reencaminhado";
$key_paypal_a["ch"] = "In a few momentts you will be directed to PayPal to make the payment, if you are not redirected";
$key_paypal_a["en"] = "In a few momentts you will be directed to PayPal to make the payment, if you are not redirected";
$key_paypal_a["fr"] = "dans quelques instants vous serez adressé vers PayPal pour faire le paiement. Si vous n’êtes pas réxpedié";
$key_paypal_a["es"] = "en algunos momentos se le redirigirá  a PayPal para hacer el pago. Si usted no está redireccionado";
$key_paypal_a["de"] = "in ein paar Momenten werden Sie zu PayPal weiterbgeleitet, um die Zahlung zu machen. Wenn Sie nicht adressiert werden,";
$key_paypal_a["it"] = "Tra qualche istanti Lei sarà indirizzato a PayPal per effettuare il pagamento. Se non sei reindirizzato,";

$key_paypal_b["pt"] = "clique aqui";
$key_paypal_b["ch"] = "click here";
$key_paypal_b["en"] = "clicking here";
$key_paypal_b["fr"] = "cliquer ici";
$key_paypal_b["es"] = "haga clic aquí";
$key_paypal_b["de"] = "klicken Sie hier";
$key_paypal_b["it"] = "fare click qui";

$key_voucher["pt"] = "Insira o código promocional/voucher de desconto";
$key_voucher["ch"] = "Insert the promotional code / discount voucher";
$key_voucher["en"] = "Insert the promotional code / discount voucher";
$key_voucher["fr"] = "Insérer le code de promotion / bon d’escompte";
$key_voucher["es"] = "Introducir el codigo de promoción / vale de descuento";
$key_voucher["de"] = "Fügen Sie das Sonderangebot Code / der Geschenkgutschein";
$key_voucher["it"] = "Inserirei il códice promozionale / buono sconto";

$key_voucherb["pt"] = "Oferta";
$key_voucherb["ch"] = "Offer";
$key_voucherb["en"] = "Offer";
$key_voucherb["fr"] = "Offre";
$key_voucherb["es"] = "Oferta";
$key_voucherb["de"] = "Angebot";
$key_voucherb["it"] = "Offerta";

// CARRINHO

$txt_header_a["pt"] = "PRODUTO";
$txt_header_a["ch"] = "PRODUCT";
$txt_header_a["en"] = "PRODUCT";
$txt_header_a["fr"] = "PRODUIT";
$txt_header_a["es"] = "PRODUCTO";
$txt_header_a["de"] = "PRODUKT";
$txt_header_a["it"] = "PRODOTTO";

$txt_header_b["pt"] = "DESCRIÇÃO";
$txt_header_b["ch"] = "DESCRIPTION";
$txt_header_b["en"] = "DESCRIPTION";
$txt_header_b["fr"] = "DESCRIPTION";
$txt_header_b["es"] = "DESCRIPCIÓN";
$txt_header_b["de"] = "BESCHREIBUNG";
$txt_header_b["it"] = "DESCRIZIONE";

$txt_header_c["pt"] = "QTD";
$txt_header_c["ch"] = "QT";
$txt_header_c["en"] = "QT";
$txt_header_c["fr"] = "QT";
$txt_header_c["es"] = "CTD";
$txt_header_c["de"] = "QT";
$txt_header_c["it"] = "QT";

$txt_header_d["pt"] = "UNITÁRIO";
$txt_header_d["ch"] = "AMOUNT";
$txt_header_d["en"] = "AMOUNT";
$txt_header_d["fr"] = "MONTANT";
$txt_header_d["es"] = "UNITARIO";
$txt_header_d["de"] = "BETRAG";
$txt_header_d["it"] = "IMPORTO";

$txt_envio_gratis["pt"] = "Envio grátis para compras";
$txt_envio_gratis["ch"] = "";
$txt_envio_gratis["en"] = "Free shipping for purchases";
$txt_envio_gratis["fr"] = "Livraison gratuite pour les achats";
$txt_envio_gratis["es"] = "";
$txt_envio_gratis["de"] = "";
$txt_envio_gratis["it"] = "";

$txt_aceito_cond["pt"] = "Aceito as condições de compra";
$txt_aceito_cond["ch"] = "";
$txt_aceito_cond["en"] = "I accept the conditions of purchase";
$txt_aceito_cond["fr"] = "Je accepte les conditions d´achat";
$txt_aceito_cond["es"] = "";
$txt_aceito_cond["de"] = "";
$txt_aceito_cond["it"] = "";

$txt_ver_cond["pt"] = "Ver Condições de Compra";
$txt_ver_cond["ch"] = "";
$txt_ver_cond["en"] = "See Terms of purchase";
$txt_ver_cond["fr"] = "Voir Conditions d´achat";
$txt_ver_cond["es"] = "";
$txt_ver_cond["de"] = "";
$txt_ver_cond["it"] = "";

/** AVISOS LOGIN **/
$key_aviso_email["pt"] = "Por favor insira o seu email";
$key_aviso_email["ch"] = "Please insert your email";
$key_aviso_email["en"] = "Please insert your email";
$key_aviso_email["fr"] = "Veuillez insérer votre email";
$key_aviso_email["es"] = "Por favor, introduzca su correo electrónico";
$key_aviso_email["de"] = "Setzen Sie bitte Ihr email";
$key_aviso_email["it"] = "Si prega di inserire il tuo indirizzo email";

$key_aviso_pw["pt"] = "Por favor insira a sua password";
$key_aviso_pw["ch"] = "Please insert your password";
$key_aviso_pw["en"] = "Please insert your password";
$key_aviso_pw["fr"] = "Veuillez saisir votre mot de passe";
$key_aviso_pw["es"] = "Por favor, introduzca su contraseña";
$key_aviso_pw["de"] = "Legen Sie bitte Ihr Passwort";
$key_aviso_pw["it"] = "Si prega di inserire la password";

$key_pgm_p["pt"] = "Métodos de Pagamento Dísponiveis";
$key_pgm_p["ch"] = "";
$key_pgm_p["en"] = "Available Payment Methods";
$key_pgm_p["fr"] = "Méthodes de paiement disponibles";
$key_pgm_p["es"] = "";
$key_pgm_p["de"] = "";
$key_pgm_p["it"] = "";

$key_pgm_p2["pt"] = "Método de Pagamento";
$key_pgm_p2["ch"] = "";
$key_pgm_p2["en"] = "Payment Method";
$key_pgm_p2["fr"] = "Méthode de paiement";
$key_pgm_p2["es"] = "";
$key_pgm_p2["de"] = "";
$key_pgm_p2["it"] = "";

$key_shop_msg2_envio2['pt'] = 'Método de expedição';
$key_shop_msg2_envio2['ch'] = 'Delivery method';
$key_shop_msg2_envio2['en'] = 'Delivery method';
$key_shop_msg2_envio2['fr'] = 'Delivery method';
$key_shop_msg2_envio2['es'] = 'Forma de envio';
$key_shop_msg2_envio2['de'] = '';
$key_shop_msg2_envio2['it'] = '';

$key_multibanco['pt'] = 'Pagamento por Multibanco ou Homebanking';
$key_multibanco['ch'] = '';
$key_multibanco['en'] = '';
$key_multibanco['fr'] = '';
$key_multibanco['es'] = '';
$key_multibanco['de'] = '';
$key_multibanco['it'] = '';


$key_morada_fac["pt"] = "MORADA DE FATURAÇÃO";
$key_morada_fac["ch"] = "";
$key_morada_fac["en"] = "ADDRESS BILLING";
$key_morada_fac["fr"] = "";
$key_morada_fac["es"] = "";
$key_morada_fac["de"] = "";
$key_morada_fac["it"] = "";

$key_morada_env["pt"] = "MORADA DE ENVIO";
$key_morada_env["ch"] = "";
$key_morada_env["en"] = "SHIPPING ADDRESS";
$key_morada_env["fr"] = "";
$key_morada_env["es"] = "";
$key_morada_env["de"] = "";
$key_morada_env["it"] = "";

$key_dear["pt"] = "Caro(a)";
$key_dear["ch"] = "";
$key_dear["en"] = "Dear";
$key_dear["fr"] = "";
$key_dear["es"] = "";
$key_dear["de"] = "";
$key_dear["it"] = "";


$key_dear2["pt"] = "Muito obrigado pela sua encomenda.<br>Caso não consiga pagar pelo método de pagamento que escolheu, pode sempre alterar a sua escolha bastando para isso [url]. De seguida são apresentadas todas as informações necessárias:";
$key_dear2["ch"] = "";
$key_dear2["en"] = "Thank you for your order.<br>If you can not pay by the chosen payment method, you can always go back and change it by [url].<br><br>Here are all the necessary informations:";
$key_dear2["fr"] = "";
$key_dear2["es"] = "";
$key_dear2["de"] = "";
$key_dear2["it"] = "";

$key_imp["pt"] = "Imprimir";
$key_imp["ch"] = "";
$key_imp["en"] = "Print";
$key_imp["fr"] = "";
$key_imp["es"] = "";
$key_imp["de"] = "";
$key_imp["it"] = "";

$key_autenticar['pt'] = 'Para finalizar a sua encomenda necessita de criar uma conta ou autenticar-se, para isso';
$key_autenticar["ch"] = "";
$key_autenticar["en"] = "";
$key_autenticar["fr"] = "";
$key_autenticar["es"] = "";
$key_autenticar["de"] = "";
$key_autenticar["it"] = "";


$key_clique_aqui['pt'] = 'clique aqui';
$key_clique_aqui["ch"] = "";
$key_clique_aqui["en"] = "";
$key_clique_aqui["fr"] = "";
$key_clique_aqui["es"] = "";
$key_clique_aqui["de"] = "";
$key_clique_aqui["it"] = "";

$key_produto_qualoseutamanho['pt'] = '(Qual o seu tamanho?)';
$key_produto_qualoseutamanho["ch"] = "";
$key_produto_qualoseutamanho["en"] = "";
$key_produto_qualoseutamanho["fr"] = "";
$key_produto_qualoseutamanho["es"] = "";
$key_produto_qualoseutamanho["de"] = "";
$key_produto_qualoseutamanho["it"] = "";


$key_comparador_add['pt'] = 'Comparador';
$key_comparador_add["ch"] = "";
$key_comparador_add["en"] = "";
$key_comparador_add["fr"] = "";
$key_comparador_add["es"] = "";
$key_comparador_add["de"] = "";
$key_comparador_add["it"] = "";
}