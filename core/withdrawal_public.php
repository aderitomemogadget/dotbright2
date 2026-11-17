<?php
require_once __DIR__.'/withdrawals.php';


function store_withdrawal_public(PDO $db, $input, $query, $language, $secret, callable $sender)
{
    $language=store_withdrawal_language($language);
    $state=array('step'=>'form','language'=>$language,'error'=>'','data'=>array('name'=>'','order_reference'=>'','email'=>'','scope'=>'whole','items'=>'','received_on'=>''));
    if (empty($_SESSION['withdrawal_nonce'])) $_SESSION['withdrawal_nonce']=bin2hex(random_bytes(32));
    $token=store_withdrawal_csrf_token();
    $state['csrf']=$token;
    // Receipts are session-bound: knowing an ID or public reference is never sufficient.
    $receipt=is_string($query['receipt'] ?? null) ? $query['receipt'] : '';
    if ($receipt!=='' && isset($_SESSION['withdrawal_receipts'][$receipt])) {
        $row=store_withdrawal_find($db,$_SESSION['withdrawal_receipts'][$receipt]);
        if ($row) { $state['step']='receipt'; $state['row']=$row; $state['receipt_token']=$receipt; return $state; }
    }
    if ($_SERVER['REQUEST_METHOD']!=='POST') {
        if (is_string($query['order'] ?? null) && mb_strlen($query['order'])<=100) $state['data']['order_reference']=$query['order'];
        return $state;
    }
    try {
        if (!is_string($input['_csrf'] ?? null) || !store_withdrawal_verify_csrf($input['_csrf'])) throw new InvalidArgumentException('csrf');
        $action=$input['action'] ?? '';
        if ($action==='review') {
            if (!empty($input['website'])) throw new InvalidArgumentException('invalid');
            $state['data']=store_withdrawal_validate($input,$language);
            $draft=bin2hex(random_bytes(24));
            $_SESSION['withdrawal_drafts'][$draft]=$state['data'];
            $_SESSION['withdrawal_drafts']=array_slice($_SESSION['withdrawal_drafts'],-8,null,true);
            $state['draft']=$draft; $state['step']='review';
        } elseif ($action==='edit' || $action==='confirm') {
            $draft=$input['draft'] ?? '';
            if (!is_string($draft) || !isset($_SESSION['withdrawal_drafts'][$draft])) throw new InvalidArgumentException('csrf');
            $state['data']=$_SESSION['withdrawal_drafts'][$draft];
            $state['language']=$state['data']['language'];
            if ($action==='edit') return $state;
            if ($secret==='') throw new RuntimeException('Rate signing key unavailable');
            $key=hash('sha256',$_SESSION['withdrawal_nonce'].json_encode($state['data'],JSON_THROW_ON_ERROR));
            $hour=gmdate('Y-m-d-H');
            $buckets=array(
                hash_hmac('sha256','ip|'.$hour.'|'.($_SERVER['REMOTE_ADDR'] ?? 'unknown'),$secret)=>20,
                hash_hmac('sha256','session|'.$hour.'|'.$_SESSION['withdrawal_nonce'],$secret)=>10
            );
            $customer=store_withdrawal_customer($db,$_SESSION);
            $row=store_withdrawal_record($db,$state['data'],$key,$customer,$buckets);
            $receipt=bin2hex(random_bytes(24));
            $_SESSION['withdrawal_receipts'][$receipt]=(int)$row['id'];
            $_SESSION['withdrawal_receipts']=array_slice($_SESSION['withdrawal_receipts'],-12,null,true);
            // Once recorded, any transport failure must still lead to the receipt.
            try { store_withdrawal_mail($db,$row['id'],$sender); }
            catch (Throwable $e) { error_log('Dotbright withdrawal receipt pending: '.(int)$row['id']); }
            $state['redirect']='livre-resolucao.php?receipt='.$receipt.'&site_lingua='.$state['language'];
        } else throw new InvalidArgumentException('invalid');
    } catch (InvalidArgumentException $e) {
        $state['error']=in_array($e->getMessage(),array('invalid','partial_required','csrf','rate'),true) ? $e->getMessage() : 'invalid';
        // Preserve bounded user input so correcting a field does not erase the rest.
        foreach ($state['data'] as $field=>$value) if (isset($input[$field]) && is_string($input[$field])) $state['data'][$field]=mb_substr($input[$field],0,$field==='items'?2000:254);
    }
    return $state;
}
