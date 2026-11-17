<?php
require_once __DIR__.'/privacy.php';

function store_privacy_public(PDO $db,$session,$input,$query,$method)
{
    $customer=store_privacy_customer($db,$session);
    $state=array('customer'=>$customer,'error'=>'','success'=>'','requests'=>array(),'csrf'=>store_privacy_csrf());
    if(!$customer)return $state;
    if($method==='POST') {
        if(!store_privacy_verify_csrf($input['_csrf'] ?? null))throw new InvalidArgumentException('Sessão expirada. Recarregue a página e tente novamente.');
        $action=$input['action'] ?? '';
        if($action==='request') {
            if(!empty($input['website']))throw new InvalidArgumentException('Pedido inválido.');
            $row=store_privacy_request($db,$session,$input);
            $state['success']='Pedido '.$row['reference'].' recebido. A conta mantém-se ativa até à análise da equipa.';
        } elseif($action==='cancel') {
            store_privacy_cancel($db,$session,is_scalar($input['id'] ?? null)?(int)$input['id']:0);$state['success']='Pedido cancelado.';
        } elseif($action==='newsletter') {
            store_privacy_newsletter($db,$session,$input['newsletter'] ?? '');$state['success']='Preferência de comunicações atualizada.';
        } else throw new InvalidArgumentException('Ação inválida.');
        $state['customer']=store_privacy_customer($db,$session);
    }
    if(isset($query['export']) && is_scalar($query['export'])) {
        $state['download']=store_privacy_export($db,(int)$query['export'],(int)$customer['ID']);
        return $state;
    }
    $q=$db->prepare('SELECT id,reference,kind,status,created_at,due_at,customer_response,export_ready_at FROM store_privacy_requests WHERE customer_id=? ORDER BY id DESC LIMIT 100');$q->execute(array($customer['ID']));$state['requests']=$q->fetchAll(PDO::FETCH_ASSOC);
    return $state;
}
