<?php
// Limit request globals before the legacy bootstrap. Authentication remains session-based.
$_GET=array_intersect_key($_GET,array_flip(array('id','q','status','mail','from','to','p')));
$_POST=array_intersect_key($_POST,array_flip(array('_csrf','action','id','version','status','return_status','refund_status','note','acknowledged')));
$_COOKIE=array_intersect_key($_COOKIE,array_flip(array(session_name())));
$wd_input=$_POST; $wd_query=$_GET;
$_POST=array(); $_GET=array(); $_REQUEST=array(); $_FILES=array();
require __DIR__.'/settings.inc.php';
$_POST=$wd_input; $_GET=$wd_query;
require_once __DIR__.'/../../core/withdrawals.php';

if (!store_withdrawal_can_manage($admin_id ?? 0,$dados['Permissoes'] ?? array())) { http_response_code(403); exit('Sem permissões.'); }
header('Cache-Control: no-store, private');
header('Referrer-Policy: no-referrer');
$wd_error=''; $wd_row=null; $wd_rows=array(); $wd_events=array(); $wd_total=0;
$wd_id=isset($_GET['id']) && is_scalar($_GET['id']) ? (int)$_GET['id'] : 0;
$wd_page=max(1,min(100000,(int)(is_scalar($_GET['p'] ?? 1)?($_GET['p'] ?? 1):1)));
$wd_filters=array();
foreach (array('q','status','mail','from','to') as $field) $wd_filters[$field]=is_string($_GET[$field] ?? null)?mb_substr(trim($_GET[$field]),0,150):'';
$wd_labels=array(
 'status'=>array('received'=>'Recebido','review'=>'Em análise','accepted'=>'Direito reconhecido','not_applicable'=>'Não aplicável (fundamentar)','closed'=>'Concluído'),
 'return_status'=>array('not_reviewed'=>'Por analisar','awaiting_goods'=>'A aguardar bens','received'=>'Bens recebidos','not_required'=>'Não necessária'),
 'refund_status'=>array('not_reviewed'=>'Por analisar','pending'=>'Pendente','completed'=>'Efetuado externamente','not_applicable'=>'Não aplicável'),
 'mail'=>array('pending'=>'Pendente','sending'=>'A enviar','sent'=>'Aceite pelo SMTP','failed'=>'Falhou','uncertain'=>'Envio incerto')
);
try {
    if (!store_withdrawal_enabled() || realpath(dirname(__DIR__,2))!=='/home/www/dotbright.pt/public_html') throw new RuntimeException('Módulo ainda não ativado nesta instalação.');
    $wd_db=store_withdrawal_db();
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!is_string($_POST['_csrf'] ?? null) || !store_withdrawal_verify_csrf($_POST['_csrf'])) throw new InvalidArgumentException('Sessão expirada. Recarregue a página.');
        $wd_id=is_scalar($_POST['id'] ?? null)?(int)$_POST['id']:0;
        if (($_POST['action'] ?? '')==='update') {
            store_withdrawal_admin_update($wd_db,$wd_id,(int)($_POST['version'] ?? 0),$_POST,(int)$admin_id);
        } elseif (($_POST['action'] ?? '')==='retry') {
            store_withdrawal_retry($wd_db,$wd_id,(int)$admin_id,$_POST['acknowledged'] ?? '');
            try { store_withdrawal_mail($wd_db,$wd_id,'store_withdrawal_sender'); }
            catch (Throwable $e) { error_log('Dotbright withdrawal retry pending: '.$wd_id); }
        } else throw new InvalidArgumentException('Ação inválida.');
        header('Location: withdrawals.php?id='.$wd_id,true,303); exit;
    }
    if ($wd_id>0) {
        $wd_row=store_withdrawal_find($wd_db,$wd_id);
        if (!$wd_row) throw new InvalidArgumentException('Pedido não encontrado.');
        $q=$wd_db->prepare('SELECT * FROM store_withdrawal_events WHERE withdrawal_id=? ORDER BY id DESC LIMIT 200');
        $q->execute(array($wd_id)); $wd_events=$q->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $where=array('1=1'); $params=array();
        if ($wd_filters['q']!=='') {
            $where[]="(reference LIKE ? ESCAPE '!' OR order_reference LIKE ? ESCAPE '!' OR customer_name LIKE ? ESCAPE '!' OR receipt_email LIKE ? ESCAPE '!')";
            $term='%'.strtr($wd_filters['q'],array('!'=>'!!','%'=>'!%','_'=>'!_')).'%'; $params=array_fill(0,4,$term);
        }
        foreach (array('status'=>'status','mail'=>'mail_state') as $field=>$column) if ($wd_filters[$field]!=='') {
            if (!isset($wd_labels[$field][$wd_filters[$field]])) throw new InvalidArgumentException('Filtro inválido.');
            $where[]=$column.'=?'; $params[]=$wd_filters[$field];
        }
        foreach (array('from'=>'>=','to'=>'<') as $field=>$operator) if ($wd_filters[$field]!=='') {
            $date=DateTimeImmutable::createFromFormat('!Y-m-d',$wd_filters[$field],new DateTimeZone('UTC'));
            if (!$date || $date->format('Y-m-d')!==$wd_filters[$field]) throw new InvalidArgumentException('Data inválida.');
            $where[]='submitted_at'.$operator.'?'; $params[]=($field==='to'?$date->modify('+1 day'):$date)->format('Y-m-d');
        }
        if ($wd_filters['from']!=='' && $wd_filters['to']!=='' && $wd_filters['from']>$wd_filters['to']) throw new InvalidArgumentException('Intervalo de datas inválido.');
        $where=implode(' AND ',$where);
        $q=$wd_db->prepare('SELECT COUNT(*) FROM store_withdrawals WHERE '.$where); $q->execute($params); $wd_total=(int)$q->fetchColumn();
        $q=$wd_db->prepare('SELECT id,reference,order_reference,customer_name,submitted_at,status,mail_state FROM store_withdrawals WHERE '.$where.' ORDER BY id DESC LIMIT 30 OFFSET '.(($wd_page-1)*30));
        $q->execute($params); $wd_rows=$q->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $wd_error=$e instanceof PDOException ? 'Não foi possível aceder ao módulo. Verifique a migração e a configuração.' : ($e instanceof RuntimeException || $e instanceof InvalidArgumentException ? $e->getMessage() : 'Não foi possível concluir a operação.');
}
$wd_csrf=store_withdrawal_csrf_token();
include __DIR__.'/../layout/header.inc.php';
include __DIR__.'/../layout/body.inc.php';
include __DIR__.'/withdrawals_view.inc.php';
include __DIR__.'/../layout/bottom.inc.php';
