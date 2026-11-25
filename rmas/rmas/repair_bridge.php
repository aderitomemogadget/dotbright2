<?php
/** Compatibility with RRE snapshots. Never creates accounts or sends notifications. */
function dotbright_rma_repair_context($rmaId)
{
    $rmaId=(int)$rmaId;
    if ($rmaId<=0) return array();
    $link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
    $rma=new DataObj;
    if (!$rma->db_open($link,$GLOBALS['DB_name'],'SELECT * FROM rmas WHERE ID='.$rmaId)) throw new RuntimeException('Não foi possível verificar a origem da RMA.');
    $source=$rma->EOF()?0:(int)($rma->db_data['repair_order_id'] ?? 0);$rma->db_close();
    if (!$source) return array();
    $order=new DataObj;
    if (!$order->db_open($link,$GLOBALS['DB_name'],'SELECT id,rma_id FROM repair_orders WHERE id='.$source.' AND rma_id='.$rmaId)) throw new RuntimeException('Não foi possível verificar o pedido de origem.');
    $found=!$order->EOF();$order->db_close();
    if (!$found) throw new RuntimeException('A ligação ao pedido RRE é inconsistente. Verifique a associação antes de gravar.');
    return array('id'=>$source,'rma_id'=>$rmaId);
}
function dotbright_rma_repair_token()
{
    if (empty($_SESSION['rma_repair_csrf'])) $_SESSION['rma_repair_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['rma_repair_csrf'];
}
function dotbright_rma_repair_validate($post)
{
    if (!is_string($post['_rre_csrf'] ?? null) || empty($_SESSION['rma_repair_csrf']) || !hash_equals($_SESSION['rma_repair_csrf'],$post['_rre_csrf'])) throw new InvalidArgumentException('Sessão expirada. Reabra a RMA antes de gravar.');
    foreach(array('cliente_nome'=>255,'cliente_username'=>254,'cliente_telefone1'=>50,'cliente_morada'=>1000,'cliente_cp'=>30,'cliente_sobrenome'=>255,'cliente_apelido'=>255,'m_imei'=>255) as $field=>$limit) {
        $value=$post[$field] ?? '';
        if (!is_string($value) || !preg_match('//u',$value) || preg_match_all('/./us',$value)>$limit) throw new InvalidArgumentException('Verifique os dados do cliente e do equipamento.');
        if (!in_array($field,array('cliente_sobrenome','cliente_apelido','m_imei'),true) && trim($value)==='') throw new InvalidArgumentException('Preencha nome, email, telefone e morada do cliente.');
    }
    if (!filter_var($post['cliente_username'],FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Indique um email válido.');
    if (!is_scalar($post['m_estado'] ?? null) || !preg_match('/^[1-9][0-9]*$/D',(string)$post['m_estado'])) throw new InvalidArgumentException('Selecione um estado técnico válido.');
    if (!is_scalar($post['m_origem'] ?? null) || !isset($GLOBALS['lojas_rmas'][(string)$post['m_origem']])) throw new InvalidArgumentException('Selecione uma proveniência válida.');
}
