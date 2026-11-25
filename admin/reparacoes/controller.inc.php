<?php
if (!defined('REPAIR_ADMIN_SCREEN')) { http_response_code(403); exit; }
// Do not allow legacy request-global extraction to override authentication or controller state.
$ro_query=array_intersect_key($_GET,array_flip(array('id','q','status','p','repair_status','product_status','transfer')));
$ro_input=array_intersect_key($_POST,array_flip(array('_csrf','action','id','version','status','repair_status','product_status','confirm','after','rma_state','rma_origin','rma_brand','rma_model','rma_imei','rma_serial')));
$_GET=$_POST=$_REQUEST=$_FILES=array();
$_COOKIE=array_intersect_key($_COOKIE,array_flip(array(session_name())));
require __DIR__.'/settings.inc.php';
require_once __DIR__.'/../../core/repair_orders.php';
$_GET=$ro_query; $_POST=$ro_input;
if (!dotbright_repair_order_can_manage($admin_id ?? 0,$dados['Permissoes'] ?? array())) {http_response_code(403);exit('Sem permissões.');}
header('Cache-Control: no-store, private'); header('Referrer-Policy: no-referrer');
$ro_error='';$ro_row=null;$ro_result=array('rows'=>array(),'total'=>0,'page'=>1);$ro_access=null;
$ro_id=is_scalar($_GET['id'] ?? null)?max(0,(int)$_GET['id']):0;
$ro_term=is_string($_GET['q'] ?? null)?substr(trim($_GET['q']),0,150):'';
$ro_status=is_string($_GET['status'] ?? null)?$_GET['status']:'';
$ro_page=is_scalar($_GET['p'] ?? null)?max(1,min(100000,(int)$_GET['p'])):1;
$ro_can_reveal=dotbright_repair_order_can_reveal($admin_id,$dados['Permissoes']);
$ro_flash=$_SESSION['repair_orders_flash'] ?? '';unset($_SESSION['repair_orders_flash']);
$ro_workflow=false;$ro_rma_ready=false;$ro_rma_options=array('states'=>array(),'origins'=>array());$ro_events=array();
$ro_rma_summary=null;
$ro_repair_filter=is_string($_GET['repair_status'] ?? null)?$_GET['repair_status']:'';
$ro_product_filter=is_string($_GET['product_status'] ?? null)?$_GET['product_status']:'';
try {
    if (REPAIR_ADMIN_SCREEN==='detail' && $ro_id<=0 && $_SERVER['REQUEST_METHOD']!=='POST') throw new InvalidArgumentException('Indique o número do pedido.');
    if (REPAIR_ADMIN_SCREEN==='list' && $ro_id>0) {header('Location: pagina.php?id='.$ro_id,true,302);exit;}
    $ro_db=dotbright_repair_orders_db();
    $ro_workflow=dotbright_repair_workflow_ready($ro_db);$ro_rma_ready=dotbright_repair_rma_ready($ro_db);
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!dotbright_repair_order_verify_csrf($_POST['_csrf'] ?? null)) throw new InvalidArgumentException('Sessão expirada. Recarregue a página.');
        $ro_action=$_POST['action'] ?? '';
        $ro_id=is_scalar($_POST['id'] ?? null)?max(0,(int)$_POST['id']):0;
        if (($ro_action==='import' && REPAIR_ADMIN_SCREEN!=='list') || ($ro_action!=='import' && REPAIR_ADMIN_SCREEN!=='detail')) throw new InvalidArgumentException('Ação inválida nesta página.');
        if ($ro_action==='update') {
            if (!is_string($_POST['status'] ?? null)) throw new InvalidArgumentException('Estado inválido.');
            $ro_version=is_scalar($_POST['version'] ?? null)?(int)$_POST['version']:0;
            if ($ro_workflow) {
                if (!is_string($_POST['repair_status'] ?? null) || !is_string($_POST['product_status'] ?? null)) throw new InvalidArgumentException('Selecione os estados da reparação e do produto.');
                dotbright_repair_workflow_update($ro_db,$ro_id,$ro_version,$_POST['status'],$_POST['repair_status'],$_POST['product_status'],(int)$admin_id);
            } else dotbright_repair_order_update($ro_db,$ro_id,$ro_version,$_POST['status'],(int)$admin_id);
            $_SESSION['repair_orders_flash']='Estado atualizado.';
            header('Location: pagina.php?id='.$ro_id,true,303);exit;
        } elseif ($ro_action==='send_to_rma') {
            if (!dotbright_repair_order_can_reveal($admin_id,$dados['Permissoes'])) throw new InvalidArgumentException('A transferência exige permissões de Produtos e Encomendas.');
            $ro_rma_id=dotbright_repair_send_to_rma($ro_db,$ro_id,is_scalar($_POST['version'] ?? null)?(int)$_POST['version']:0,$_POST,(int)$admin_id);
            $_SESSION['repair_orders_flash']='Pedido associado à RMA #'.$ro_rma_id.'. Não foram enviados emails ou SMS.';
            header('Location: pagina.php?id='.$ro_id,true,303);exit;
        } elseif ($ro_action==='import') {
            $ro_import=dotbright_repair_order_import_legacy($ro_db,is_scalar($_POST['after'] ?? null)?(int)$_POST['after']:0);
            $_SESSION['repair_orders_import_after']=$ro_import['after'];
            $_SESSION['repair_orders_flash']='Importados: '.$ro_import['imported'].'. Ignorados por não pertencerem a reparações: '.$ro_import['skipped'].'. Lote analisado: '.$ro_import['scanned'].'.';
            header('Location: list.php',true,303);exit;
        } elseif ($ro_action==='reveal') {
            if (!$ro_can_reveal || ($_POST['confirm'] ?? '')!=='1') throw new InvalidArgumentException('Sem autorização ou confirmação para consultar os dados de acesso.');
            $ro_row=dotbright_repair_order_find($ro_db,$ro_id);
            if (!$ro_row) throw new InvalidArgumentException('Pedido não encontrado.');
            $ro_access=dotbright_repair_order_decrypt($ro_row['access_cipher']);
        } else throw new InvalidArgumentException('Ação inválida.');
    }
    if ($ro_id>0) {
        $ro_row=dotbright_repair_order_find($ro_db,$ro_id);
        if (!$ro_row) throw new InvalidArgumentException('Pedido não encontrado.');
        if ($ro_workflow) {$ro_event_query=$ro_db->prepare('SELECT event_type,actor_id,before_json,after_json,created_at FROM repair_order_events WHERE repair_order_id=? ORDER BY id DESC LIMIT 20');$ro_event_query->execute(array($ro_id));$ro_events=$ro_event_query->fetchAll(PDO::FETCH_ASSOC);}
        if ($ro_rma_ready && !empty($ro_row['rma_id'])) $ro_rma_summary=dotbright_repair_rma_summary($ro_db,$ro_row);
        if ($ro_rma_ready && empty($ro_row['rma_id'])) $ro_rma_options=dotbright_repair_rma_options($ro_db);
    } elseif (REPAIR_ADMIN_SCREEN==='list') $ro_result=dotbright_repair_orders_search($ro_db,$ro_term,$ro_status,$ro_page,$ro_repair_filter,$ro_product_filter);
} catch (Throwable $e) {
    $ro_error=$e instanceof PDOException?'Não foi possível aceder aos pedidos. Confirme que aplicou o SQL enviado no chat.':($e instanceof RuntimeException || $e instanceof InvalidArgumentException?$e->getMessage():'Não foi possível concluir a operação.');
}
$ro_csrf=dotbright_repair_order_csrf();
include __DIR__.'/../layout/header.inc.php';
include __DIR__.'/../layout/body.inc.php';
include __DIR__.'/view.inc.php';
include __DIR__.'/../layout/bottom.inc.php';
