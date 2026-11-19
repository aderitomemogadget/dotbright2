<?php
$path = dirname(__FILE__).'/../core/';
include $path.'core_files.php';
require_once $path.'repair_request.php';

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

    // Preserve the existing table/columns; complete request storage is a later phase.
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $sql = "INSERT INTO pedidos(prod_id,nome_completo,morada,cp,telefone,NIF) VALUES ('".$prod_id."','".addslashes($data['r_nome'])."','".addslashes($data['r_recolha'])."','".addslashes($data['r_cp'])."','".addslashes($data['r_tel'])."','".addslashes($data['r_nif'])."')";
    $res = Runquery($db_link, $DB_name, $sql);
    if (!$res) {
        echo 'Não foi possível registar o pedido. Por favor, tente novamente mais tarde.';
        return;
    }

    if (!send_email('assistencia@dotbright.pt', $subject, $summary)) {
        echo 'O pedido ficou registado, mas não foi possível enviar o aviso à assistência. Contacte a equipa antes de repetir o pedido.';
        return;
    }
    send_email('aderitofernandes@gmail.com', $subject, $summary);

    $html = '<p>Muito obrigado pelo seu pedido de recolha para reparação.</p>';
    $html .= '<p>Recebemos o seu pedido. A equipa irá confirmar consigo os detalhes e o valor estimado, agendar a recolha e combinar o pagamento. Este email confirma a receção do pedido; a recolha ainda está sujeita a agendamento.</p>';
    $html .= $summary;
    $html .= '<h3>Próximos passos</h3>';
    $html .= '<p><strong>1. Acondicionamento:</strong> acondicione devidamente o equipamento. Utilize a caixa original, se possível, dentro de uma caixa de envio, proteja o equipamento e preencha os espaços vazios.</p>';
    $html .= '<p><strong>2. Recolha:</strong> aguarde o contacto da equipa para confirmar a data e o período de recolha.</p>';
    $html .= '<p><strong>3. Reparação:</strong> após a receção, a equipa dará seguimento à intervenção e contactá-lo-á caso seja necessário confirmar informações adicionais.</p>';
    $html .= '<p>Para esclarecimentos, contacte-nos pelo <a href="https://wa.me/351914228888">WhatsApp 914 228 888</a>.</p><p>Cumprimentos,<br>Equipa DotBright</p>';

    if (send_email($data['r_email'], $subject, $html)) {
        echo '✅ Pedido de recolha solicitado com sucesso!<br><br>Somos rápidos a responder! Em breve receberá um e-mail com os próximos passos. Fique atento à sua caixa de entrada.<br><br>Obrigado pela preferência<br>DotBright - Serviço RRE';
    } else {
        echo 'O pedido foi registado e enviado à assistência, mas não foi possível enviar o email de confirmação. Contacte a equipa antes de repetir o pedido.';
    }
}
