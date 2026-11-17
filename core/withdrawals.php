<?php
// Store-specific service. No bootstrap, output or mutations on include.
require_once __DIR__.'/withdrawal_store.php';

function store_withdrawal_enabled()
{
    return realpath(dirname(__DIR__)) === STORE_WITHDRAWAL_ROOT;
}

function store_withdrawal_can_manage($adminId, $permissions)
{
    return (int)$adminId>0 && isset($permissions[9]) && (int)$permissions[9]===1;
}

function store_withdrawal_db()
{
    return new PDO('mysql:host='.$GLOBALS['DB_host'].';dbname='.$GLOBALS['DB_name'].';charset=utf8mb4', $GLOBALS['DB_user'], $GLOBALS['DB_pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES=>false));
}

function store_withdrawal_secret()
{
    if (empty($GLOBALS['DB_pass'])) throw new RuntimeException('Rate signing configuration missing');
    return hash_hmac('sha256','withdrawal-rate-v1|'.STORE_WITHDRAWAL_ROOT,$GLOBALS['DB_pass']);
}

function store_withdrawal_csrf_token()
{
    if (empty($_SESSION['store_withdrawal_csrf'])) $_SESSION['store_withdrawal_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['store_withdrawal_csrf'];
}

function store_withdrawal_verify_csrf($token)
{
    return is_string($token) && isset($_SESSION['store_withdrawal_csrf']) && hash_equals($_SESSION['store_withdrawal_csrf'],$token);
}

function store_withdrawal_escape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function store_withdrawal_language($language)
{
    return in_array($language, array('pt','en','fr'), true) ? $language : 'pt';
}

function store_withdrawal_text($key, $language='pt')
{
    static $texts=null;
    if ($texts===null) $texts=require __DIR__.'/withdrawals_text.php';
    $language=store_withdrawal_language($language);
    return $texts[$language][$key] ?? $texts['pt'][$key] ?? $key;
}

function store_withdrawal_validate($input, $language)
{
    $limits=array('name'=>180,'order_reference'=>100,'email'=>254,'scope'=>10,'items'=>2000,'received_on'=>10);
    $data=array('language'=>store_withdrawal_language($language));
    foreach ($limits as $key=>$max) {
        $value=$input[$key] ?? '';
        if (!is_string($value) || !mb_check_encoding($value,'UTF-8') || mb_strlen($value,'UTF-8')>$max
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',$value)) throw new InvalidArgumentException('invalid');
        $data[$key]=trim(str_replace(array("\r\n","\r"),"\n",$value));
    }
    foreach (array('name','order_reference','email') as $field) {
        if ($data[$field]==='' || strpos($data[$field],"\n")!==false) throw new InvalidArgumentException('invalid');
    }
    if (!filter_var($data['email'],FILTER_VALIDATE_EMAIL) || !in_array($data['scope'],array('whole','partial'),true)) throw new InvalidArgumentException('invalid');
    if ($data['scope']==='partial' && $data['items']==='') throw new InvalidArgumentException('partial_required');
    if ($data['scope']==='whole') $data['items']='';
    if ($data['received_on']!=='') {
        $date=DateTimeImmutable::createFromFormat('!Y-m-d',$data['received_on'],new DateTimeZone('UTC'));
        if (!$date || $date->format('Y-m-d')!==$data['received_on'] || $data['received_on']>gmdate('Y-m-d')) throw new InvalidArgumentException('invalid');
    }
    return $data;
}

function store_withdrawal_declaration($data)
{
    $lang=$data['language'];
    $text=store_withdrawal_text('declaration',$lang)."\n\n";
    foreach (array('name','order_reference','email') as $key) $text.=store_withdrawal_text($key,$lang).': '.$data[$key]."\n";
    $text.=store_withdrawal_text('scope',$lang).': '.store_withdrawal_text($data['scope'],$lang)."\n";
    if ($data['scope']==='partial') $text.=store_withdrawal_text('items',$lang).': '.$data['items']."\n";
    if ($data['received_on']!=='') $text.=store_withdrawal_text('received_on',$lang).': '.$data['received_on']."\n";
    return $text;
}

function store_withdrawal_customer(PDO $db, $session)
{
    if (empty($session['ss_cli_id']) || empty($session['ss_cli_token']) || !is_string($session['ss_cli_token'])) return null;
    $q=$db->prepare('SELECT ID FROM clientes WHERE ID=? AND Token=? LIMIT 1');
    $q->execute(array((int)$session['ss_cli_id'],$session['ss_cli_token']));
    return $q->fetchColumn() ?: null;
}

function store_withdrawal_associate(PDO $db, $data, $customerId)
{
    // Never expose matching results or stored customer details to a public caller.
    $params=array($data['order_reference']);
    $where='Referencia=?';
    if (ctype_digit($data['order_reference'])) { $where='('.$where.' OR ID=?)'; $params[]=(int)$data['order_reference']; }
    if ($customerId) { $where.=' AND ID_Cliente=?'; $params[]=(int)$customerId; }
    else { $where.=' AND LOWER(TRIM(enc_Email))=LOWER(?)'; $params[]=$data['email']; }
    $q=$db->prepare('SELECT ID FROM encomendas WHERE '.$where.' LIMIT 2'); $q->execute($params);
    $ids=$q->fetchAll(PDO::FETCH_COLUMN);
    return count($ids)===1 ? array((int)$ids[0],$customerId ? 'account' : 'reference_email') : array(null,'manual_review');
}

function store_withdrawal_rate(PDO $db, $bucket, $maximum)
{
    // Called inside the submission transaction: concurrent requests serialize here.
    $q=$db->prepare('INSERT INTO store_withdrawal_limits(bucket,attempts,expires_at) VALUES(?,1,DATE_ADD(UTC_TIMESTAMP(),INTERVAL 2 HOUR)) ON DUPLICATE KEY UPDATE attempts=attempts+1');
    $q->execute(array($bucket));
    $q=$db->prepare('SELECT attempts FROM store_withdrawal_limits WHERE bucket=?'); $q->execute(array($bucket));
    if ((int)$q->fetchColumn()>$maximum) throw new InvalidArgumentException('rate');
}

function store_withdrawal_record(PDO $db, $data, $submissionKey, $customerId, $buckets=array())
{
    if (!preg_match('/^[a-f0-9]{64}$/D',$submissionKey)) throw new InvalidArgumentException('invalid');
    $data=store_withdrawal_validate($data,$data['language']);
    $db->beginTransaction();
    try {
        $q=$db->prepare('SELECT * FROM store_withdrawals WHERE submission_key=?'); $q->execute(array($submissionKey));
        if ($existing=$q->fetch(PDO::FETCH_ASSOC)) { $db->commit(); return $existing; }
        foreach ($buckets as $bucket=>$maximum) store_withdrawal_rate($db,$bucket,$maximum);
        list($orderId,$association)=store_withdrawal_associate($db,$data,$customerId);
        $reference='LR-'.strtoupper(bin2hex(random_bytes(12)));
        $q=$db->prepare('INSERT INTO store_withdrawals(reference,submission_key,customer_id,order_id,association,order_reference,customer_name,receipt_email,language,scope,items,received_on,declaration,submitted_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,UTC_TIMESTAMP(6))');
        $q->execute(array($reference,$submissionKey,$customerId,$orderId,$association,$data['order_reference'],$data['name'],$data['email'],$data['language'],$data['scope'],$data['items'],$data['received_on'] ?: null,store_withdrawal_declaration($data)));
        $id=(int)$db->lastInsertId();
        store_withdrawal_event($db,$id,null,'received','');
        $db->commit();
        return store_withdrawal_find($db,$id);
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        // A simultaneous duplicate may have won the unique submission key.
        if ($e instanceof PDOException && $e->getCode()==='23000') {
            $q=$db->prepare('SELECT * FROM store_withdrawals WHERE submission_key=?'); $q->execute(array($submissionKey));
            if ($existing=$q->fetch(PDO::FETCH_ASSOC)) return $existing;
        }
        throw $e;
    }
}

function store_withdrawal_find(PDO $db, $id)
{
    $q=$db->prepare('SELECT * FROM store_withdrawals WHERE id=?'); $q->execute(array((int)$id));
    return $q->fetch(PDO::FETCH_ASSOC);
}

function store_withdrawal_event(PDO $db, $id, $actor, $type, $note)
{
    $q=$db->prepare('INSERT INTO store_withdrawal_events(withdrawal_id,actor_id,event_type,note,created_at) VALUES(?,?,?,?,UTC_TIMESTAMP(6))');
    $q->execute(array($id,$actor,$type,$note));
}

function store_withdrawal_receipt($row)
{
    $lang=$row['language'];
    return 'Dotbright — '.store_withdrawal_text('receipt',$lang)."\n".$row['reference']."\n".
        store_withdrawal_text('submitted_at',$lang).': '.$row['submitted_at']." UTC\n\n".$row['declaration']."\n".
        store_withdrawal_text('receipt_note',$lang)."\n\n".'lojaonline@dotbright.pt';
}

function store_withdrawal_mail(PDO $db, $id, callable $sender)
{
    $q=$db->prepare("UPDATE store_withdrawals SET mail_state='sending',mail_attempts=mail_attempts+1,mail_attempt_at=UTC_TIMESTAMP(),mail_error=NULL WHERE id=? AND mail_state IN ('pending','failed') AND mail_attempts<5 AND (mail_next_at IS NULL OR mail_next_at<=UTC_TIMESTAMP())");
    $q->execute(array((int)$id));
    if (!$q->rowCount()) return false;
    $row=store_withdrawal_find($db,$id);
    $text=store_withdrawal_receipt($row);
    $html='<html><body><h1>Dotbright</h1><pre style="font-family:Arial,sans-serif;white-space:pre-wrap;line-height:1.6">'.store_withdrawal_escape($text).'</pre></body></html>';
    try { $ok=(bool)$sender($row['receipt_email'],'Dotbright | '.store_withdrawal_text('receipt',$row['language']).' | '.$row['reference'],$html); }
    catch (Throwable $e) { $ok=false; }
    // Do not automatically retry an interrupted 'sending' delivery: SMTP may have accepted it.
    $q=$db->prepare("UPDATE store_withdrawals SET mail_state=?,mail_sent_at=".($ok ? 'UTC_TIMESTAMP()' : 'NULL').",mail_next_at=DATE_ADD(UTC_TIMESTAMP(),INTERVAL 15 MINUTE),mail_error=? WHERE id=? AND mail_state='sending'");
    $q->execute(array($ok ? 'sent' : 'failed',$ok ? null : 'transport_failed',(int)$id));
    store_withdrawal_event($db,$id,null,$ok ? 'receipt_sent' : 'receipt_failed','');
    return $ok;
}

function store_withdrawal_sender($email,$subject,$html)
{
    if (!store_withdrawal_enabled()) return false;
    require_once __DIR__.'/withdrawal_mailer.php';
    return store_withdrawal_deliver($email,$subject,$html);
}

function store_withdrawal_admin_update(PDO $db,$id,$version,$input,$actor)
{
    $allowed=array('status'=>array('received','review','accepted','not_applicable','closed'),
        'return_status'=>array('not_reviewed','awaiting_goods','received','not_required'),
        'refund_status'=>array('not_reviewed','pending','completed','not_applicable'));
    foreach ($allowed as $key=>$values) if (!isset($input[$key]) || !in_array($input[$key],$values,true)) throw new InvalidArgumentException('Estado inválido.');
    $note=$input['note'] ?? '';
    if (!is_string($note) || trim($note)==='' || mb_strlen($note)>2000) throw new InvalidArgumentException('Indique uma nota de análise (até 2000 caracteres).');
    $db->beginTransaction();
    try {
        $q=$db->prepare('UPDATE store_withdrawals SET status=?,return_status=?,refund_status=?,version=version+1 WHERE id=? AND version=?');
        $q->execute(array($input['status'],$input['return_status'],$input['refund_status'],$id,$version));
        if (!$q->rowCount()) throw new RuntimeException('O pedido foi alterado. Recarregue antes de guardar.');
        store_withdrawal_event($db,$id,$actor,'admin_update',json_encode(array('status'=>$input['status'],'return'=>$input['return_status'],'refund'=>$input['refund_status'],'note'=>trim($note)),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR));
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
}

function store_withdrawal_retry(PDO $db,$id,$actor,$acknowledged)
{
    if ($acknowledged!=='1') throw new InvalidArgumentException('Confirme a verificação do envio anterior.');
    $db->beginTransaction();
    try {
        $q=$db->prepare("UPDATE store_withdrawals SET mail_state='pending',mail_attempts=0,mail_next_at=NULL,version=version+1 WHERE id=? AND mail_state IN ('failed','uncertain')");
        $q->execute(array((int)$id));
        if (!$q->rowCount()) throw new InvalidArgumentException('O comprovativo não está disponível para reenvio manual.');
        store_withdrawal_event($db,$id,$actor,'receipt_retry_requested','Operador confirmou a verificação do envio anterior; poderá existir uma cópia já entregue.');
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
}

function store_withdrawal_process(PDO $db, $send, callable $sender)
{
    $counts=array('sent'=>0,'failed'=>0,'due'=>0,'uncertain'=>0);
    if ($send) {
        // An interrupted SMTP request has an unknown outcome. Require an operator to retry.
        $q=$db->query("SELECT id FROM store_withdrawals WHERE mail_state='sending' AND mail_attempt_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE) LIMIT 50");
        foreach ($q->fetchAll(PDO::FETCH_COLUMN) as $id) {
            $update=$db->prepare("UPDATE store_withdrawals SET mail_state='uncertain',mail_error='interrupted_delivery' WHERE id=? AND mail_state='sending' AND mail_attempt_at<DATE_SUB(UTC_TIMESTAMP(),INTERVAL 10 MINUTE)");
            $update->execute(array($id));
            if ($update->rowCount()) { store_withdrawal_event($db,$id,null,'receipt_uncertain',''); $counts['uncertain']++; }
        }
        $db->exec('DELETE FROM store_withdrawal_limits WHERE expires_at<UTC_TIMESTAMP() LIMIT 1000');
    }
    $q=$db->query("SELECT id FROM store_withdrawals WHERE mail_state IN ('pending','failed') AND mail_attempts<5 AND (mail_next_at IS NULL OR mail_next_at<=UTC_TIMESTAMP()) ORDER BY id LIMIT 20");
    $ids=$q->fetchAll(PDO::FETCH_COLUMN); $counts['due']=count($ids);
    if ($send) foreach ($ids as $id) { $counts[store_withdrawal_mail($db,$id,$sender) ? 'sent' : 'failed']++; }
    return $counts;
}
