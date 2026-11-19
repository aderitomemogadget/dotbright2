<?php
$repair_order_input = array_intersect_key($_POST, array_flip(array('r_form_version','r_recolha_obs','r_entrega_tipo','r_entrega_cp','r_entrega_cidade','r_entrega_obs','r_acesso','prod_id','r_avaria','r_nome','r_tel','r_email','r_nif','r_recolha','r_entrega','r_cp','r_cidade','r_nome2','r_morada2','r_cp2','r_nif2','r_unlock','m_padrao','r_periodo','m_termos','m_termos1')));
$_GET = $_POST = $_REQUEST = $_FILES = array();
$_COOKIE = array_intersect_key($_COOKIE, array_flip(array(session_name())));
$path = dirname(__FILE__).'/../core/';
include $path.'core_files.php';
require_once $path.'repair_request.php';
require_once $path.'repair_orders.php';
$_POST = $repair_order_input;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prod_id = isset($_POST['prod_id']) && is_scalar($_POST['prod_id']) ? (int)$_POST['prod_id'] : 0;
    $prods = $prod_id > 0 ? get_produtc_info($prod_id) : false;
    if (!is_array($prods) || empty($prods['prod_id'])) {
        echo 'Não foi possível identificar a reparação. Atualize a página e tente novamente.';
        return;
    }

    $data = dotbright_repair_request_data($_POST);
    $quote = dotbright_repair_quote($prods);
    $summary = dotbright_repair_email_summary($prods, $data, $quote);
    $subject = 'Pedido de recolha para reparação.';

    try {
        $request_id = dotbright_repair_order_create($prods, $data, $quote, $_POST);
    } catch (InvalidArgumentException $e) {
        echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        return;
    } catch (Throwable $e) {
        error_log('Repair request could not be saved. Check migration, database and access encryption configuration.');
        echo 'Não foi possível registar o pedido. Por favor, tente novamente mais tarde.';
        return;
    }
    $summary = '<p><strong>Pedido: REP-'.(int)$request_id.'</strong></p>'.$summary;

    if (!dotbright_repair_order_send($request_id, 'staff', 'assistencia@dotbright.pt', $subject, $summary)) {
        echo 'O pedido ficou registado, mas não foi possível enviar o aviso à assistência. Contacte a equipa antes de repetir o pedido.';
        return;
    }
    dotbright_repair_order_send($request_id, 'copy', 'aderitofernandes@gmail.com', $subject, $summary);

    $html = '<p>✅ Pedido de recolha solicitado com sucesso!</p><p>Somos rápidos a responder! Em breve receberá um e-mail com os próximos passos. Fique atento à sua caixa de entrada.</p><p>Obrigado pela preferência<br>DotBright - Serviço RRE</p>';
    $html .= '<p>Recebemos o seu pedido. A equipa irá confirmar consigo os detalhes e o valor estimado, agendar a recolha e combinar o pagamento. Este email confirma a receção do pedido; a recolha ainda está sujeita a agendamento.</p>';
    $html .= $summary;
    $html .= '<h3>Próximos passos</h3>';
    $html .= '<p><strong>1. Acondicionamento:</strong> acondicione devidamente o equipamento. Utilize a caixa original, se possível, dentro de uma caixa de envio, proteja o equipamento e preencha os espaços vazios.</p>';
    $html .= '<p><strong>2. Recolha:</strong> aguarde o contacto da equipa para confirmar a data e o período de recolha.</p>';
    $html .= '<p><strong>3. Reparação:</strong> após a receção, a equipa dará seguimento à intervenção e contactá-lo-á caso seja necessário confirmar informações adicionais.</p>';
    $html .= '<p>Para esclarecimentos, contacte-nos pelo <a href="https://wa.me/351914228888">WhatsApp 914 228 888</a>.</p><p>Cumprimentos,<br>Equipa DotBright</p>';

    if (dotbright_repair_order_send($request_id, 'customer', $data['r_email'], $subject, $html)) {
        echo 'Pedido enviado com sucesso!';
    } else {
        echo 'O pedido foi registado e enviado à assistência, mas não foi possível enviar o email de confirmação. Contacte a equipa antes de repetir o pedido.';
    }
}
