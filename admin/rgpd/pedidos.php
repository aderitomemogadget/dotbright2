<?php
$_GET=array_intersect_key($_GET,array_flip(array('id','status','kind','q','p')));
$_POST=array_intersect_key($_POST,array_flip(array('_csrf','action','id','version','status','note','customer_response','export_ready','retention_reviewed','notified_customer')));
$_COOKIE=array_intersect_key($_COOKIE,array_flip(array(session_name())));
$privacy_admin_input=$_POST;$privacy_admin_query=$_GET;
$_POST=array();$_GET=array();$_REQUEST=array();$_FILES=array();
$path=__DIR__.'/../../core/';require $path.'core_files.php';require $path.'admin_globals.php';require_once $path.'privacy.php';
$dados=getDadosAdministrador($admin_id ?? 0);
if(!store_privacy_can_manage($admin_id ?? 0,$dados['Permissoes']??array())){http_response_code(403);exit('Sem permissão para gerir dados pessoais.');}
header('Cache-Control: no-store, private');header('Referrer-Policy: no-referrer');header('X-Frame-Options: SAMEORIGIN');
$pa_error='';$pa_row=null;$pa_rows=array();$pa_events=array();$pa_consents=array();$pa_customer=null;$pa_total=0;
$pa_id=is_scalar($privacy_admin_query['id']??null)?(int)$privacy_admin_query['id']:0;
$pa_page=max(1,min(100000,is_scalar($privacy_admin_query['p']??null)?(int)$privacy_admin_query['p']:1));
$pa_kinds=store_privacy_kinds();$pa_statuses=store_privacy_statuses();$pa_filters=array();
foreach(array('status','kind','q') as $key)$pa_filters[$key]=is_string($privacy_admin_query[$key]??null)?mb_substr(trim($privacy_admin_query[$key]),0,180):'';
try{
    if(!store_privacy_enabled())throw new RuntimeException('Módulo indisponível nesta instalação.');
    $pa_db=store_privacy_db();
    if($_SERVER['REQUEST_METHOD']==='POST'){
        if(!store_privacy_verify_csrf($privacy_admin_input['_csrf']??null))throw new InvalidArgumentException('Sessão expirada. Recarregue a página.');
        $pa_id=is_scalar($privacy_admin_input['id']??null)?(int)$privacy_admin_input['id']:0;
        $version=is_scalar($privacy_admin_input['version']??null)?(int)$privacy_admin_input['version']:0;
        if(($privacy_admin_input['action']??'')==='update')store_privacy_admin_update($pa_db,$pa_id,$version,$privacy_admin_input,(int)$admin_id);
        elseif(($privacy_admin_input['action']??'')==='erase')store_privacy_erase($pa_db,$pa_id,$version,(int)$admin_id,$privacy_admin_input);
        else throw new InvalidArgumentException('Ação inválida.');
        header('Location: pedidos.php?id='.$pa_id,true,303);exit;
    }
    if($pa_id>0){
        $q=$pa_db->prepare('SELECT * FROM store_privacy_requests WHERE id=?');$q->execute(array($pa_id));$pa_row=$q->fetch(PDO::FETCH_ASSOC);if(!$pa_row)throw new InvalidArgumentException('Pedido não encontrado.');
        $q=$pa_db->prepare('SELECT ID,Nome,Apelido,Email,Ativo,Newsletter FROM clientes WHERE ID=?');$q->execute(array($pa_row['customer_id']));$pa_customer=$q->fetch(PDO::FETCH_ASSOC);
        $q=$pa_db->prepare('SELECT * FROM store_privacy_events WHERE request_id=? ORDER BY id DESC LIMIT 200');$q->execute(array($pa_id));$pa_events=$q->fetchAll(PDO::FETCH_ASSOC);
        $q=$pa_db->prepare('SELECT * FROM store_privacy_consents WHERE customer_id=? ORDER BY id DESC LIMIT 200');$q->execute(array($pa_row['customer_id']));$pa_consents=$q->fetchAll(PDO::FETCH_ASSOC);
    }else{
        $where=array('1=1');$params=array();
        foreach(array('status'=>$pa_statuses,'kind'=>$pa_kinds) as $field=>$allowed)if($pa_filters[$field]!==''){
            if(!isset($allowed[$pa_filters[$field]]))throw new InvalidArgumentException('Filtro inválido.');
            $where[]='r.'.$field.'=?';$params[]=$pa_filters[$field];
        }
        if($pa_filters['q']!==''){$where[]="(r.reference LIKE ? ESCAPE '!' OR c.Nome LIKE ? ESCAPE '!' OR c.Email LIKE ? ESCAPE '!')";$term='%'.strtr($pa_filters['q'],array('!'=>'!!','%'=>'!%','_'=>'!_')).'%';foreach(range(1,3) as $_)$params[]=$term;}
        $from=' FROM store_privacy_requests r LEFT JOIN clientes c ON c.ID=r.customer_id WHERE '.implode(' AND ',$where);
        $q=$pa_db->prepare('SELECT COUNT(*)'.$from);$q->execute($params);$pa_total=(int)$q->fetchColumn();
        $q=$pa_db->prepare('SELECT r.*,c.Nome,c.Email'.$from.' ORDER BY r.id DESC LIMIT 30 OFFSET '.(($pa_page-1)*30));$q->execute($params);$pa_rows=$q->fetchAll(PDO::FETCH_ASSOC);
    }
}catch(Throwable $e){
    $pa_error=$e instanceof InvalidArgumentException||$e instanceof RuntimeException&&!($e instanceof PDOException)?$e->getMessage():'Não foi possível aceder ao módulo RGPD. Verifique a configuração e a migração.';
}
$pa_csrf=store_privacy_csrf();
include __DIR__.'/../layout/header.inc.php';include __DIR__.'/../layout/body.inc.php';include __DIR__.'/pedido_view.php';include __DIR__.'/../layout/bottom.inc.php';
