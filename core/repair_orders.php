<?php
/** Repair requests only. No schema creation, mail transmission or output on include. */
require_once __DIR__.'/repair_request.php';
function dotbright_repair_orders_db()
{
    return new PDO('mysql:host='.$GLOBALS['DB_host'].';dbname='.$GLOBALS['DB_name'].';charset=utf8mb4', $GLOBALS['DB_user'], $GLOBALS['DB_pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES=>false));
}
function dotbright_repair_order_escape($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function dotbright_repair_order_statuses() { return array('received'=>'Recebido', 'review'=>'Em análise', 'collection_scheduled'=>'Recolha agendada', 'closed'=>'Concluído', 'cancelled'=>'Cancelado'); }
function dotbright_repair_order_can_manage($id, $permissions) { return (int)$id>0 && isset($permissions[8]) && (int)$permissions[8]===1; }
function dotbright_repair_order_can_reveal($id, $permissions) { return dotbright_repair_order_can_manage($id,$permissions) && isset($permissions[9]) && (int)$permissions[9]===1; }
function dotbright_repair_order_csrf()
{
    if (empty($_SESSION['repair_orders_csrf'])) $_SESSION['repair_orders_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['repair_orders_csrf'];
}
function dotbright_repair_order_verify_csrf($value) { return is_string($value) && isset($_SESSION['repair_orders_csrf']) && hash_equals($_SESSION['repair_orders_csrf'],$value); }
function dotbright_repair_order_key()
{
    $encoded=getenv('DOTBRIGHT_REPAIR_ACCESS_KEY');
    $key=is_string($encoded) ? base64_decode($encoded,true) : false;
    if (!is_string($key) || strlen($key)!==32) throw new RuntimeException('Configure a chave de proteção dos dados de acesso ao equipamento no servidor.');
    return $key;
}
function dotbright_repair_order_encrypt($values)
{
    if (!$values) return null;
    if (!function_exists('openssl_encrypt')) throw new RuntimeException('A proteção dos dados de acesso ao equipamento não está disponível.');
    $key=dotbright_repair_order_key(); $iv=random_bytes(12); $tag='';
    $cipher=openssl_encrypt(json_encode($values,JSON_UNESCAPED_UNICODE), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, 'dotbright-repair-access-v1');
    if ($cipher===false) throw new RuntimeException('Não foi possível proteger os dados de acesso ao equipamento.');
    return 'v1:'.base64_encode($iv.$tag.$cipher);
}
function dotbright_repair_order_decrypt($cipher)
{
    if (!$cipher) return array();
    if (!is_string($cipher) || substr($cipher,0,3)!=='v1:') throw new RuntimeException('Formato de acesso inválido.');
    $bytes=base64_decode(substr($cipher,3),true);
    if ($bytes===false || strlen($bytes)<29) throw new RuntimeException('Dados de acesso inválidos.');
    $plain=openssl_decrypt(substr($bytes,28), 'aes-256-gcm', dotbright_repair_order_key(), OPENSSL_RAW_DATA, substr($bytes,0,12), substr($bytes,12,16), 'dotbright-repair-access-v1');
    if ($plain===false) throw new RuntimeException('Não foi possível abrir os dados de acesso. Verifique a chave do servidor.');
    $data=json_decode($plain,true);
    if (!is_array($data)) throw new RuntimeException('Dados de acesso inválidos.');
    return $data;
}
function dotbright_repair_order_payload($product, $quote, $input)
{
    if (!dotbright_is_repair_family(isset($product['fam_id'])?$product['fam_id']:0)) throw new InvalidArgumentException('Este artigo não pertence à família de reparações.');
    $limits=array('r_avaria'=>2000,'r_nome'=>180,'r_tel'=>50,'r_email'=>254,'r_nif'=>30,'r_recolha'=>1000,'r_entrega'=>1000,'r_cp'=>30,'r_cidade'=>180,'r_nome2'=>180,'r_morada2'=>1000,'r_cp2'=>30,'r_nif2'=>30,'r_unlock'=>10,'m_padrao'=>50,'r_periodo'=>1);
    $clean=array();
    foreach ($limits as $key=>$limit) {
        $value=isset($input[$key])?$input[$key]:'';
        if (!is_string($value) || !preg_match('//u',$value) || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',$value) || preg_match_all('/./us',$value,$unused)>$limit) throw new InvalidArgumentException('Verifique os dados introduzidos no pedido.');
        $clean[$key]=trim($value);
    }
    foreach (array('r_avaria','r_nome','r_tel','r_email','r_recolha','r_cp','r_cidade') as $field) if ($clean[$field]==='') throw new InvalidArgumentException('Preencha os dados obrigatórios do pedido.');
    if (!filter_var($clean['r_email'],FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Indique um email válido.');
    if (!in_array($clean['r_periodo'],array('','1','2'),true)) throw new InvalidArgumentException('Período de recolha inválido.');
    foreach (array('m_termos','m_termos1') as $field) if (!isset($input[$field]) || !is_scalar($input[$field]) || (string)$input[$field]!=='1') throw new InvalidArgumentException('Aceite os Termos e Condições e os Termos de Confidencialidade.');
    $access=array();
    if ($clean['r_unlock']!=='') $access['code']=$clean['r_unlock'];
    if ($clean['m_padrao']!=='') $access['pattern']=$clean['m_padrao'];
    $data=dotbright_repair_request_data($clean);
    $data['r_periodo']=$clean['r_periodo'];
    $reference=explode('_',isset($product['prod_ref'])?$product['prod_ref']:'');
    $now=gmdate('Y-m-d H:i:s');
    return array('created_at'=>$now,'status'=>'received','customer_name'=>$data['r_nome'],'customer_email'=>$data['r_email'],'customer_phone'=>$data['r_tel'],
        'product_id'=>(int)$product['prod_id'],'product_name'=>rtrim($product['prod_nome'],'- '),'product_reference'=>$reference[0], 'total_cents'=>(int)round($quote['total']*100),
        'details_json'=>json_encode(array('customer'=>$data,'product'=>array('id'=>(int)$product['prod_id'],'name'=>rtrim($product['prod_nome'],'- '),'reference'=>$reference[0],'ean'=>isset($product['Ref_Ean'])?$product['Ref_Ean']:''),'quote'=>$quote,'consents'=>array('terms'=>true,'confidentiality'=>true,'accepted_at'=>$now)),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),
        'access_mode'=>!$access?'not_provided':(count($access)===2?'code_and_pattern':(isset($access['code'])?'code':'pattern')), 'access_cipher'=>dotbright_repair_order_encrypt($access), 'updated_at'=>$now);
}
function dotbright_repair_order_insert(PDO $db, $record)
{
    $columns=array_keys($record);
    $q=$db->prepare('INSERT INTO repair_orders ('.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')');
    $q->execute(array_values($record));
    return (int)$db->lastInsertId();
}
function dotbright_repair_order_create($product, $data, $quote, $input, PDO $db=null)
{
    $record=dotbright_repair_order_payload($product,$quote,$input);
    return dotbright_repair_order_insert($db ?: dotbright_repair_orders_db(),$record);
}
function dotbright_repair_order_mail_result($id,$recipient,$state,PDO $db=null)
{
    if (!in_array($recipient,array('staff','customer','copy'),true) || !in_array($state,array('accepted','failed','uncertain'),true)) return;
    try {
        $db=$db ?: dotbright_repair_orders_db();
        $q=$db->prepare('UPDATE repair_orders SET '.$recipient.'_mail=?,mail_updated_at=? WHERE id=?');
        $q->execute(array($state,gmdate('Y-m-d H:i:s'),(int)$id));
    } catch (Throwable $e) { error_log('Repair order mail state could not be recorded: '.(int)$id); }
}
function dotbright_repair_order_find(PDO $db,$id)
{
    $q=$db->prepare('SELECT * FROM repair_orders WHERE id=?'); $q->execute(array((int)$id));
    return $q->fetch(PDO::FETCH_ASSOC);
}
function dotbright_repair_orders_search(PDO $db,$term,$status,$page)
{
    $where=array(); $params=array(); $term=trim((string)$term);
    if ($term!=='') {
        $like='%'.strtr($term,array('!'=>'!!','%'=>'!%','_'=>'!_')).'%';
        $where[]="(id=? OR legacy_id=? OR customer_name LIKE ? ESCAPE '!' OR customer_email LIKE ? ESCAPE '!' OR customer_phone LIKE ? ESCAPE '!' OR product_reference LIKE ? ESCAPE '!')";
        $number=preg_match('/^(?:REP-)?([0-9]+)$/D',$term,$m)?(int)$m[1]:0;
        $params=array($number,$number,$like,$like,$like,$like);
    }
    if ($status!=='') { if (!isset(dotbright_repair_order_statuses()[$status])) throw new InvalidArgumentException('Estado inválido.'); $where[]='status=?';$params[]=$status; }
    $where=$where?' WHERE '.implode(' AND ',$where):'';
    $q=$db->prepare('SELECT COUNT(*) FROM repair_orders'.$where); $q->execute($params); $total=(int)$q->fetchColumn();
    $page=max(1,min((int)$page,max(1,(int)ceil($total/30))));
    $q=$db->prepare('SELECT id,legacy_id,created_at,status,customer_name,customer_email,customer_phone,product_id,product_name,product_reference,total_cents,staff_mail,customer_mail FROM repair_orders'.$where.' ORDER BY id DESC LIMIT 30 OFFSET '.(($page-1)*30));
    $q->execute($params);
    return array('rows'=>$q->fetchAll(PDO::FETCH_ASSOC),'total'=>$total,'page'=>$page);
}
function dotbright_repair_order_update(PDO $db,$id,$version,$status,$actor)
{
    if (!isset(dotbright_repair_order_statuses()[$status])) throw new InvalidArgumentException('Estado inválido.');
    $q=$db->prepare('UPDATE repair_orders SET status=?,version=version+1,updated_at=?,updated_by=? WHERE id=? AND version=?');
    $q->execute(array($status,gmdate('Y-m-d H:i:s'),(int)$actor,(int)$id,(int)$version));
    if ($q->rowCount()!==1) throw new RuntimeException('O pedido foi alterado por outro utilizador. Recarregue antes de guardar.');
}
function dotbright_repair_order_import_legacy(PDO $db,$after=0)
{
    // Limited batches; unique legacy_id also protects simultaneous/repeated imports.
    $q=$db->query('SELECT p.* FROM pedidos p LEFT JOIN repair_orders r ON r.legacy_id=p.ID WHERE r.id IS NULL AND p.prod_id>0 AND p.ID>'.max(0,(int)$after).' ORDER BY p.ID LIMIT 500');
    $rows=$q->fetchAll(PDO::FETCH_ASSOC); $imported=0; $skipped=0;
    foreach ($rows as $old) {
        $link=$db->prepare('SELECT ID_ORG FROM products_families_obj WHERE ID_PROD=?');$link->execute(array((int)$old['prod_id']));
        $repair=false; foreach ($link->fetchAll(PDO::FETCH_COLUMN) as $family) if (dotbright_is_repair_family($family)) {$repair=true;break;}
        if (!$repair) {$skipped++;continue;}
        $data=array();
        foreach (array('r_nome'=>'nome_completo','r_tel'=>'telefone','r_email'=>'email','r_nif'=>'NIF','r_recolha'=>'morada','r_cp'=>'cp','r_cidade'=>'localidade') as $key=>$column) $data[$key]=isset($old[$column])?(string)$old[$column]:'';
        $record=array('legacy_id'=>(int)$old['ID'],'status'=>'received','customer_name'=>$data['r_nome'],'customer_email'=>$data['r_email'],'customer_phone'=>$data['r_tel'],'product_id'=>(int)$old['prod_id'],
            'details_json'=>json_encode(array('legacy'=>true,'customer'=>$data,'missing'=>'Registo antigo incompleto. Data, valores, peça, garantia, faturação, entrega, consentimentos e acesso não foram guardados no pedido original.'),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR), 'updated_at'=>gmdate('Y-m-d H:i:s'));
        try { dotbright_repair_order_insert($db,$record);$imported++; }
        catch (PDOException $e) { if ((string)$e->getCode()!=='23000') throw $e; }
    }
    return array('imported'=>$imported,'skipped'=>$skipped,'scanned'=>count($rows),'after'=>$rows?(int)$rows[count($rows)-1]['ID']:0);
}

function dotbright_repair_order_send($id,$recipient,$to,$subject,$html)
{
    // If the process is interrupted during SMTP, do not report the attempt as unstarted.
    dotbright_repair_order_mail_result($id,$recipient,'uncertain');
    try { $ok=(bool)send_email($to,$subject,$html); $state=$ok?'accepted':'failed'; }
    catch (Throwable $e) { $ok=false; $state='uncertain'; }
    dotbright_repair_order_mail_result($id,$recipient,$state);
    return $ok;
}

