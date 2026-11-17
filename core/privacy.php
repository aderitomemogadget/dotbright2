<?php
// Shared legacy-store adapter. Including this file performs no DB writes.
require_once __DIR__.'/privacy_store.php';

function store_privacy_enabled()
{
    return realpath(dirname(__DIR__)) === STORE_PRIVACY_ROOT;
}
function store_privacy_db()
{
    return new PDO('mysql:host='.$GLOBALS['DB_host'].';dbname='.$GLOBALS['DB_name'].';charset=utf8mb4', $GLOBALS['DB_user'], $GLOBALS['DB_pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES=>false));
}
function store_privacy_escape($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}
function store_privacy_csrf()
{
    if (empty($_SESSION['store_privacy_csrf'])) $_SESSION['store_privacy_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['store_privacy_csrf'];
}
function store_privacy_verify_csrf($value)
{
    return is_string($value) && isset($_SESSION['store_privacy_csrf']) && hash_equals($_SESSION['store_privacy_csrf'],$value);
}
function store_privacy_can_manage($id,$permissions)
{
    // Users who already administer user/role assignments are privileged owners.
    // Do not grant the shared legacy SMS bit merely to expose this new resource.
    return (int)$id>0 && ((isset($permissions[35]) && (int)$permissions[35]===1) || (isset($permissions[1]) && (int)$permissions[1]===1));
}
function store_privacy_customer(PDO $db,$session,$lock=false)
{
    if (empty($session['ss_cli_id']) || !is_scalar($session['ss_cli_id']) || !is_string($session['ss_cli_token'] ?? null) || $session['ss_cli_token']==='') return null;
    $hashed=defined('STORE_PRIVACY_SESSION_SHA256') && STORE_PRIVACY_SESSION_SHA256;
    if($hashed && !isset($session['ss_cli_session_version']))return null;
    $params=array((int)$session['ss_cli_id'],$hashed?'sha256:'.hash('sha256',$session['ss_cli_token']):$session['ss_cli_token']);
    if($hashed)$params[]=(int)$session['ss_cli_session_version'];
    $where=$hashed?' AND session_version=?':'';
    if(!empty($GLOBALS['registo_validar_email']))$where.=' AND Estado=1';
    $q=$db->prepare('SELECT * FROM clientes WHERE ID=? AND Token=? AND Ativo=1'.$where.' LIMIT 1'.($lock?' FOR UPDATE':''));
    $q->execute($params);
    return $q->fetch(PDO::FETCH_ASSOC) ?: null;
}
function store_privacy_text($value,$max=2000)
{
    if (!is_string($value) || !mb_check_encoding($value,'UTF-8') || mb_strlen($value)>$max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',$value)) throw new InvalidArgumentException('Texto inválido ou demasiado longo.');
    return trim(str_replace(array("\r\n","\r"),"\n",$value));
}
function store_privacy_kinds()
{
    return array('erasure'=>'Remover conta e pedir apagamento','access'=>'Acesso aos meus dados','export'=>'Exportação / portabilidade','rectification'=>'Retificação de dados','restriction'=>'Limitação ou oposição ao tratamento');
}
function store_privacy_statuses()
{
    return array('pending'=>'Recebido','review'=>'Em análise','awaiting_customer'=>'A aguardar informação','approved'=>'Aprovado / em execução','completed'=>'Concluído','rejected'=>'Não aplicável / recusado com fundamento','cancelled'=>'Cancelado pelo cliente');
}
function store_privacy_event(PDO $db,$id,$actor,$type,$note='')
{
    $q=$db->prepare('INSERT INTO store_privacy_events(request_id,actor_id,event_type,note,created_at) VALUES(?,?,?,?,UTC_TIMESTAMP(6))');
    $q->execute(array($id,$actor,$type,$note));
}
function store_privacy_require_transactions(PDO $db,$tables)
{
    foreach($tables as $table){
        if(!in_array($table,array('clientes','clientes_enderecos','nl_emails_registados','store_privacy_requests','store_privacy_events','store_privacy_consents'),true))throw new LogicException('Unknown transaction table');
        $create=$db->query('SHOW CREATE TABLE `'.$table.'`')->fetch(PDO::FETCH_NUM);
        if(!$create || !preg_match('/ENGINE=InnoDB\b/i',$create[1]))throw new RuntimeException('A operação exige tabelas transacionais. Nenhum apagamento foi iniciado; solicite a revisão técnica.');
    }
}
function store_privacy_request(PDO $db,$session,$input)
{
    $kind=$input['kind'] ?? '';
    if (!is_string($kind) || !isset(store_privacy_kinds()[$kind])) throw new InvalidArgumentException('Escolha um direito válido.');
    $message=store_privacy_text($input['message'] ?? '');
    if ($kind==='erasure' && ($input['acknowledged'] ?? '')!=='1') throw new InvalidArgumentException('Confirme que pretende pedir a remoção da conta.');
    $db->beginTransaction();
    try {
        $customer=store_privacy_customer($db,$session,true);
        if (!$customer) throw new InvalidArgumentException('Inicie sessão novamente.');
        $q=$db->prepare("SELECT * FROM store_privacy_requests WHERE customer_id=? AND kind=? AND status IN ('pending','review','awaiting_customer','approved') ORDER BY id DESC LIMIT 1");
        $q->execute(array($customer['ID'],$kind));
        if ($existing=$q->fetch(PDO::FETCH_ASSOC)) { $db->commit(); return $existing; }
        $q=$db->prepare('SELECT COUNT(*) FROM store_privacy_requests WHERE customer_id=? AND created_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 1 DAY)');$q->execute(array($customer['ID']));
        if ((int)$q->fetchColumn()>=10) throw new InvalidArgumentException('Já existem vários pedidos recentes. Consulte o estado ou contacte a loja.');
        $reference='RGPD-'.strtoupper(bin2hex(random_bytes(12)));
        $q=$db->prepare("INSERT INTO store_privacy_requests(reference,customer_id,kind,active_key,status,message,customer_response,created_at,due_at,updated_at) VALUES(?,?,?,?,'pending',?,'',UTC_TIMESTAMP(6),DATE_ADD(UTC_TIMESTAMP(),INTERVAL 1 MONTH),UTC_TIMESTAMP(6))");
        $q->execute(array($reference,$customer['ID'],$kind,$customer['ID'].':'.$kind,$message));
        $id=(int)$db->lastInsertId();store_privacy_event($db,$id,null,'received');
        $q=$db->prepare('SELECT * FROM store_privacy_requests WHERE id=?');$q->execute(array($id));$row=$q->fetch(PDO::FETCH_ASSOC);
        $db->commit();return $row;
    } catch (Throwable $e) {if($db->inTransaction())$db->rollBack();throw $e;}
}
function store_privacy_cancel(PDO $db,$session,$id)
{
    $db->beginTransaction();
    try {
        $customer=store_privacy_customer($db,$session,true);if(!$customer)throw new InvalidArgumentException('Inicie sessão novamente.');
        $q=$db->prepare("UPDATE store_privacy_requests SET status='cancelled',active_key=NULL,version=version+1,updated_at=UTC_TIMESTAMP(6) WHERE id=? AND customer_id=? AND status IN ('pending','review','awaiting_customer')");
        $q->execute(array($id,$customer['ID']));if(!$q->rowCount())throw new InvalidArgumentException('Este pedido já não pode ser cancelado automaticamente.');
        store_privacy_event($db,$id,null,'customer_cancelled');$db->commit();
    } catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function store_privacy_columns(PDO $db,$table)
{
    if(!in_array($table,array('clientes','clientes_enderecos','nl_emails_registados','encomendas'),true))throw new LogicException('Unknown schema');
    return $db->query('SHOW COLUMNS FROM `'.$table.'`')->fetchAll(PDO::FETCH_ASSOC);
}
function store_privacy_newsletter(PDO $db,$session,$enabled)
{
    if(!in_array($enabled,array('0','1'),true))throw new InvalidArgumentException('Preferência inválida.');
    store_privacy_require_transactions($db,array_merge(array('clientes','store_privacy_consents'),STORE_PRIVACY_NEWSLETTER_TABLE?array('nl_emails_registados'):array()));
    $db->beginTransaction();
    try {
        $customer=store_privacy_customer($db,$session,true);if(!$customer)throw new InvalidArgumentException('Inicie sessão novamente.');
        $previous=(int)!empty($customer['Newsletter']);
        if($previous!==(int)$enabled){
            $q=$db->prepare('UPDATE clientes SET Newsletter=? WHERE ID=?');$q->execute(array((int)$enabled,$customer['ID']));
            // Use the existing site's newsletter identity; never subscribe other addresses.
            if(STORE_PRIVACY_NEWSLETTER_TABLE){
                if($enabled==='0'){$q=$db->prepare('DELETE FROM nl_emails_registados WHERE LOWER(TRIM(Email))=LOWER(?)');$q->execute(array($customer['Email']));}
                $q=$db->prepare('SELECT COUNT(*) FROM nl_emails_registados WHERE LOWER(TRIM(Email))=LOWER(?)');$q->execute(array($customer['Email']));
                if($enabled==='1' && !(int)$q->fetchColumn()){
                    $q=$db->prepare('INSERT INTO nl_emails_registados(ID_ORG,Email,Nome,Apelido,data_hora) VALUES(?,?,?,?,UTC_TIMESTAMP())');
                    $q->execute(array((int)STORE_PRIVACY_NEWSLETTER_GROUP,$customer['Email'],$customer['Nome'],$customer['Apelido'] ?? ''));
                }
            }
            $q=$db->prepare("INSERT INTO store_privacy_consents(customer_id,preference,previous_value,current_value,source,created_at) VALUES(?,'newsletter',?,?,'customer',UTC_TIMESTAMP(6))");$q->execute(array($customer['ID'],$previous,(int)$enabled));
        }
        $db->commit();
    } catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function store_privacy_profile_fields()
{
    return array('ID','Username','Nome','Nome_ent','Apelido','Apelido_ent','Sobrenome','Empresa','Empresa_ent','Morada','Morada_ent','Cidade','Cidade_ent','Localidade','Localidade_ent','Codigo_Postal','Codigo_Postal_ent','Pais','Pais_ent','Telefone','Telefone_ent','Telefone2','Phone','Email','Email_ent','Newsletter','Notify_products','Notify_novidades','Nif','Nif_ent','Sexo','DataNascimento','Nickname','Language','Data','Data_modify');
}
function store_privacy_export(PDO $db,$requestId,$customerId,$actor=null)
{
    $q=$db->prepare("SELECT * FROM store_privacy_requests WHERE id=? AND customer_id=? AND kind IN ('access','export') AND export_ready_at IS NOT NULL AND erased_at IS NULL AND status IN ('approved','completed')");$q->execute(array($requestId,$customerId));
    if(!$q->fetch(PDO::FETCH_ASSOC))throw new InvalidArgumentException('Exportação não disponível para esta conta.');
    $q=$db->prepare('SELECT * FROM clientes WHERE ID=?');$q->execute(array($customerId));$row=$q->fetch(PDO::FETCH_ASSOC);if(!$row)throw new InvalidArgumentException('Conta não encontrada.');
    $profile=array_intersect_key($row,array_flip(store_privacy_profile_fields()));
    $q=$db->prepare('SELECT ID,Designacao,Nome,Apelido,Morada,Codigo_Postal,Cidade,Pais,Telefone,Nif FROM clientes_enderecos WHERE ID_Cliente=? ORDER BY ID LIMIT 1001');$q->execute(array($customerId));$addresses=$q->fetchAll(PDO::FETCH_ASSOC);
    $fields=array_intersect(array('ID','Referencia','Data','Estado','Total','Total_IVA','Valor_total'),array_column(store_privacy_columns($db,'encomendas'),'Field'));
    $q=$db->prepare('SELECT `'.implode('`,`',$fields).'` FROM encomendas WHERE ID_Cliente=? ORDER BY ID LIMIT 5001');$q->execute(array($customerId));$orders=$q->fetchAll(PDO::FETCH_ASSOC);
    $q=$db->prepare('SELECT preference,previous_value,current_value,source,created_at FROM store_privacy_consents WHERE customer_id=? ORDER BY id LIMIT 5001');$q->execute(array($customerId));$consents=$q->fetchAll(PDO::FETCH_ASSOC);
    if(count($addresses)>1000 || count($orders)>5000 || count($consents)>5000)throw new InvalidArgumentException('O volume de dados exige uma exportação preparada pela equipa. Contacte a loja indicando a referência deste pedido; nenhum ficheiro parcial foi disponibilizado.');
    $data=array('generated_at_utc'=>gmdate('c'),'profile'=>$profile,'addresses'=>$addresses,'orders'=>$orders,'consent_history'=>$consents,'scope'=>'Dados da conta nesta loja. Pedidos/documentos de outros sistemas e dados sujeitos a análise específica são tratados pela equipa no mesmo pedido.');
    store_privacy_event($db,$requestId,$actor,'export_downloaded');return $data;
}
function store_privacy_admin_update(PDO $db,$id,$version,$input,$actor)
{
    $status=$input['status'] ?? '';if(!is_string($status)||!isset(store_privacy_statuses()[$status])||$status==='cancelled')throw new InvalidArgumentException('Estado inválido.');
    $note=store_privacy_text($input['note'] ?? '');$response=store_privacy_text($input['customer_response'] ?? '');
    if($note==='')throw new InvalidArgumentException('Registe a análise e a justificação.');
    if($status==='rejected' && $response==='')throw new InvalidArgumentException('Indique ao cliente o fundamento da decisão.');
    $db->beginTransaction();
    try {
        $q=$db->prepare('SELECT * FROM store_privacy_requests WHERE id=? FOR UPDATE');$q->execute(array($id));$row=$q->fetch(PDO::FETCH_ASSOC);
        if(!$row || (int)$row['version']!==$version)throw new RuntimeException('O pedido foi alterado. Recarregue antes de guardar.');
        if(in_array($row['status'],array('completed','cancelled'),true))throw new InvalidArgumentException('Este pedido está encerrado.');
        if($row['kind']==='erasure' && $status==='completed' && !$row['erased_at'])throw new InvalidArgumentException('Use a ação de apagamento após análise. Alterar o estado não remove a conta.');
        $ready=($input['export_ready'] ?? '')==='1';
        if($ready && (!in_array($row['kind'],array('access','export'),true)||!in_array($status,array('approved','completed'),true)))throw new InvalidArgumentException('Reveja o pedido antes de disponibilizar a exportação.');
        $q=$db->prepare('UPDATE store_privacy_requests SET status=?,active_key=?,customer_response=?,export_ready_at='.($ready?'COALESCE(export_ready_at,UTC_TIMESTAMP())':'NULL').',version=version+1,updated_at=UTC_TIMESTAMP(6) WHERE id=? AND version=?');
        $q->execute(array($status,in_array($status,array('completed','rejected'),true)?null:$row['customer_id'].':'.$row['kind'],$response,$id,$version));
        store_privacy_event($db,$id,$actor,'admin_review',$note);$db->commit();
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function store_privacy_erase(PDO $db,$id,$version,$actor,$input)
{
    $note=store_privacy_text($input['note'] ?? '');
    if(($input['retention_reviewed'] ?? '')!=='1' || ($input['notified_customer'] ?? '')!=='1' || mb_strlen($note)<20)throw new InvalidArgumentException('Confirme a análise de conservação, a comunicação ao cliente e registe a fundamentação (mínimo 20 caracteres).');
    store_privacy_require_transactions($db,array_merge(array('clientes','clientes_enderecos','store_privacy_requests','store_privacy_events'),STORE_PRIVACY_NEWSLETTER_TABLE?array('nl_emails_registados'):array()));
    $db->beginTransaction();
    try {
        $q=$db->prepare('SELECT * FROM store_privacy_requests WHERE id=? FOR UPDATE');$q->execute(array($id));$r=$q->fetch(PDO::FETCH_ASSOC);
        if(!$r || (int)$r['version']!==$version || $r['kind']!=='erasure' || $r['status']!=='approved' || $r['erased_at'])throw new InvalidArgumentException('A remoção exige um pedido de apagamento aprovado e atualizado.');
        $q=$db->prepare('SELECT * FROM clientes WHERE ID=? FOR UPDATE');$q->execute(array($r['customer_id']));$customer=$q->fetch(PDO::FETCH_ASSOC);if(!$customer)throw new InvalidArgumentException('Conta não encontrada.');
        if(defined('STORE_PRIVACY_SESSION_SHA256') && STORE_PRIVACY_SESSION_SHA256){
            $q=$db->prepare('SELECT user_id FROM marketplace_customer_links WHERE legacy_customer_id=? LIMIT 1');$q->execute(array($customer['ID']));
            if($q->fetchColumn())throw new InvalidArgumentException('Existe uma identidade profissional ligada a esta conta. Conclua primeiro a análise no fluxo RGPD do marketplace, incluindo as ligações e obrigações de conservação. O perfil local ainda não foi apagado.');
        }
        if(!empty($customer['img_ext']))throw new InvalidArgumentException('Remova primeiro a fotografia/anexos do perfil através das ferramentas da loja e documente o tratamento. O apagamento local ainda não foi executado.');
        $columns=store_privacy_columns($db,'clientes');$set=array();$params=array();
        $personal=array_merge(store_privacy_profile_fields(),array('Password','Token','Token2','token_pass','fbid','googleid','twitterid','google_refresh_token','Cartao_Cidadao','Numero_Federado','Federacao','obs','img_ext','cod_instituicao','ID_instituicao','ID_departamento'));
        foreach($columns as $column){
            $field=$column['Field'];if(!in_array($field,$personal,true)||in_array($field,array('ID','Data','Data_modify'),true))continue;
            if($field==='Nome')$value='Conta removida';
            elseif(in_array($field,array('Email','Username'),true))$value='removed-'.$customer['ID'].'@example.invalid';
            elseif($field==='Password')$value=hash('md5',random_bytes(32)); // Random unguessable value fits older 32-character columns; account is disabled.
            elseif($column['Null']==='YES')$value=null;
            elseif(preg_match('/int|decimal|double|float|bit/i',$column['Type']))$value=0;
            elseif(preg_match('/date|time/i',$column['Type']))$value='1900-01-01';
            else $value='';
            $set[]='`'.$field.'`=?';$params[]=$value;
        }
        $set[]='Ativo=0';$set[]='Estado=0';$params[]=$customer['ID'];
        $q=$db->prepare('UPDATE clientes SET '.implode(',',$set).' WHERE ID=?');$q->execute($params);
        $q=$db->prepare('DELETE FROM clientes_enderecos WHERE ID_Cliente=?');$q->execute(array($customer['ID']));
        if(STORE_PRIVACY_NEWSLETTER_TABLE){$q=$db->prepare('DELETE FROM nl_emails_registados WHERE LOWER(TRIM(Email))=LOWER(?)');$q->execute(array($customer['Email']));}
        // Order/invoice/RMA/external records require the administrator's documented basis.
        // Leave those records intact; this action erases the local profile and address book.
        $q=$db->prepare("UPDATE store_privacy_requests SET status='completed',active_key=NULL,erased_at=UTC_TIMESTAMP(),customer_response='Conta encerrada e perfil local apagado após análise. Os registos com fundamento legal de conservação são mantidos com acesso limitado.',version=version+1,updated_at=UTC_TIMESTAMP(6) WHERE id=? AND version=?");$q->execute(array($id,$version));
        store_privacy_event($db,$id,$actor,'account_erased',$note);$db->commit();
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function store_privacy_account_link()
{
    if(!store_privacy_enabled() || empty($_SESSION['ss_cli_id']) || empty($_SESSION['ss_cli_token']))return;
    // Called only inside the existing authenticated profile renderer.
    echo '<p class="store-privacy-entry"><a href="privacidade-conta.php">Privacidade e dados pessoais · Pedir remoção da conta</a></p>';
}
