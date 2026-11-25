<?php
/** Internal RRE workflow. No schema changes, notifications or side effects on include. */
function dotbright_repair_workflow_statuses()
{
    return array('not_recorded'=>'Não registado (anterior)','received'=>'Recebido','awaiting_quote'=>'Aguarda orçamento',
        'awaiting_approval'=>'Aguarda aprovação','awaiting_payment'=>'Aguarda pagamento','paid'=>'Pago',
        'sent_to_rma'=>'Enviado para RMA','received_from_rma'=>'Recebido da RMA','completed'=>'Terminado','cancelled'=>'Cancelado');
}
function dotbright_repair_product_statuses()
{
    return array('not_recorded'=>'Não registado (anterior)','awaiting_collection'=>'Aguarda recolha','in_transit'=>'Em transporte para assistência',
        'received'=>'Produto recebido','under_analysis'=>'Produto em análise','under_repair'=>'Em reparação',
        'ready_for_delivery'=>'Pronto para entrega','shipped'=>'Enviado ao cliente','delivered'=>'Entregue ao cliente');
}
function dotbright_repair_workflow_ready(PDO $db)
{
    try {
        $db->query('SELECT repair_status,product_status,rma_id,rma_sent_at,rma_sent_by FROM repair_orders WHERE 1=0');
        $db->query('SELECT id,repair_order_id,event_type,actor_id,before_json,after_json,created_at FROM repair_order_events WHERE 1=0');
        return true;
    } catch (PDOException $e) { return false; }
}
function dotbright_repair_rma_ready(PDO $db)
{
    if (!dotbright_repair_workflow_ready($db)) return false;
    try { $db->query('SELECT ID,repair_order_id FROM rmas WHERE 1=0'); return true; }
    catch (PDOException $e) { return false; }
}
function dotbright_repair_workflow_event(PDO $db,$id,$actor,$type,$before,$after)
{
    $q=$db->prepare('INSERT INTO repair_order_events (repair_order_id,event_type,actor_id,before_json,after_json,created_at) VALUES (?,?,?,?,?,?)');
    $q->execute(array($id,$type,$actor,json_encode($before,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),json_encode($after,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),gmdate('Y-m-d H:i:s')));
}
function dotbright_repair_workflow_update(PDO $db,$id,$version,$status,$repair,$product,$actor)
{
    if (!isset(dotbright_repair_order_statuses()[$status]) || !isset(dotbright_repair_workflow_statuses()[$repair]) || !isset(dotbright_repair_product_statuses()[$product])) throw new InvalidArgumentException('Estado inválido.');
    if ($actor<=0) throw new InvalidArgumentException('Operador inválido.');
    $db->beginTransaction();
    try {
        $before=dotbright_repair_order_find($db,$id);
        if (!$before || (int)$before['version']!==$version) throw new RuntimeException('O pedido foi alterado por outro utilizador. Recarregue antes de guardar.');
        if ($repair==='sent_to_rma' && empty($before['rma_id'])) throw new InvalidArgumentException('Utilize «Enviar para RMA» para criar e associar a ficha técnica.');
        $q=$db->prepare('UPDATE repair_orders SET status=?,repair_status=?,product_status=?,version=version+1,updated_at=?,updated_by=? WHERE id=? AND version=?');
        $q->execute(array($status,$repair,$product,gmdate('Y-m-d H:i:s'),$actor,$id,$version));
        if ($q->rowCount()!==1) throw new RuntimeException('O pedido foi alterado por outro utilizador. Recarregue antes de guardar.');
        dotbright_repair_workflow_event($db,$id,$actor,'status',array_intersect_key($before,array_flip(array('status','repair_status','product_status'))),array('status'=>$status,'repair_status'=>$repair,'product_status'=>$product));
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
}
function dotbright_repair_rma_options(PDO $db)
{
    $states=$db->query("SELECT e.ID,l.Cabecalho FROM rmas_estados e INNER JOIN rmas_estados_lang l ON l.ID_PAI=e.ID AND l.Lang='pt' WHERE e.Visivel=1 ORDER BY e.Ordem,e.ID")->fetchAll(PDO::FETCH_ASSOC);
    // m_origem uses conf.php's lojas_rmas IDs. rma_lojas stores companies,
    // whose IDs are unrelated (company 1 must not silently mean Online).
    $origins=array();
    foreach (($GLOBALS['lojas_rmas'] ?? array()) as $id=>$label) {
        if ((int)$id>0 && is_string($label) && trim($label)!=='') $origins[]=array('ID'=>(int)$id,'Cabecalho'=>$label,'Loja'=>'');
    }
    return array('states'=>$states,'origins'=>$origins);
}
function dotbright_repair_rma_summary(PDO $db,$row)
{
    if (empty($row['rma_id'])) return null;
    $q=$db->prepare("SELECT r.ID,r.repair_order_id,r.m_estado,r.m_origem,l.Cabecalho AS state_label FROM rmas r LEFT JOIN rmas_estados_lang l ON l.ID_PAI=r.m_estado AND l.Lang='pt' WHERE r.ID=? AND r.repair_order_id=?");
    $q->execute(array((int)$row['rma_id'],(int)$row['id']));
    $rma=$q->fetch(PDO::FETCH_ASSOC);
    if (!$rma) throw new RuntimeException('A ficha técnica associada não foi encontrada ou a ligação é inconsistente.');
    $rma['origin_label']=$GLOBALS['lojas_rmas'][(int)$rma['m_origem']] ?? 'Proveniência por confirmar';
    return $rma;
}
function dotbright_repair_rma_text($input,$key,$limit)
{
    $text=$input[$key] ?? '';
    if (!is_string($text) || !preg_match('//u',$text) || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/',$text) || preg_match_all('/./us',$text)>$limit) throw new InvalidArgumentException('Verifique o campo '.$key.'.');
    return trim($text);
}
function dotbright_repair_rma_payload($row,$input,$actor)
{
    $details=json_decode($row['details_json'],true,512,JSON_THROW_ON_ERROR);$c=$details['customer'] ?? array();$q=$details['quote'] ?? array();
    if (!empty($details['legacy'])) throw new InvalidArgumentException('O registo antigo está incompleto. Crie a RMA pelo processo habitual.');
    $notes=array('Origem: pedido de reparação REP-'.$row['id'].' (artigo '.$row['product_id'].').',
        'Reparação: '.$row['product_name'],'Referência: '.$row['product_reference'],
        'Faturação: '.($c['r_nome2'] ?? $row['customer_name']).' | NIF: '.($c['r_nif2'] ?? $c['r_nif'] ?? ''),
        'Recolha: '.($c['r_recolha'] ?? '').' | '.($c['r_cp'] ?? '').' | '.($c['r_cidade'] ?? ''),
        'Observações recolha: '.($c['r_recolha_obs'] ?? ''),
        'Entrega: '.($c['r_entrega'] ?? '').' | '.($c['r_entrega_cp'] ?? '').' | '.($c['r_entrega_cidade'] ?? ''),
        'Observações entrega: '.($c['r_entrega_obs'] ?? ''),'Período recolha: '.($c['period'] ?? ''),
        'Peça: '.($q['service']['part'] ?? '').' | '.($q['service']['part_detail'] ?? ''),
        'Prazo em loja: '.($q['service']['time'] ?? '').' | Garantia: '.($q['warranty'] ?? ''),
        'Estimativa com IVA: reparação '.number_format((float)($q['repair_gross'] ?? 0),2,',','.').' EUR; portes '.number_format((float)($q['shipping_gross'] ?? 0),2,',','.').' EUR; total '.number_format((float)($q['total'] ?? 0),2,',','.').' EUR.',
        'Estimativa sujeita a confirmação. A transferência não regista pagamento nem trabalho efetuado.',
        'Acesso: '.(dotbright_repair_access_labels()[$row['access_mode']] ?? 'Não indicado').' Códigos/padrões mantêm-se protegidos no pedido de origem.',
        'Transferido por operador BO #'.$actor.' em '.gmdate('Y-m-d H:i:s').' UTC.');
    $payload=array('repair_order_id'=>(int)$row['id'],'m_origem'=>(int)($input['rma_origin'] ?? 0),'m_estado'=>(int)($input['rma_state'] ?? 0),
        'ID_Cliente'=>0,'ID_Cliente2'=>0,'Grupo_ID'=>0,'fake'=>0,'m_tecnico'=>0,'m_tracking'=>'REP-'.$row['id'],
        'cliente_nome'=>$row['customer_name'],'cliente_apelido'=>'','cliente_sobrenome'=>'','cliente_empresa'=>$c['r_nome2'] ?? '',
        'cliente_telefone1'=>$row['customer_phone'],'cliente_telefone2'=>'','cliente_username'=>$row['customer_email'],
        'cliente_nif'=>$c['r_nif2'] ?? $c['r_nif'] ?? '', 'cliente_morada'=>$c['r_recolha'] ?? '', 'cliente_cp'=>$c['r_cp'] ?? '',
        'm_referencia'=>$row['product_reference'],'m_marca'=>dotbright_repair_rma_text($input,'rma_brand',255),'m_modelo'=>dotbright_repair_rma_text($input,'rma_model',255),
        'm_imei'=>dotbright_repair_rma_text($input,'rma_imei',255),'m_nserie'=>dotbright_repair_rma_text($input,'rma_serial',255),
        'm_avaria'=>$row['product_name']."\n".($c['r_avaria'] ?? ''),'m_observacoes'=>implode("\n",$notes),'notes'=>'',
        'codigo_desbloqueio'=>'','m_padrao'=>0,'sel_local'=>'','enc_id'=>0,'Loja'=>'','m_pago'=>0,'total'=>0,'m_valor'=>0,
        'preorcamento'=>(float)($q['repair_gross'] ?? 0),'DataInsert'=>date('Y-m-d'),'DataUpdate'=>date('Y-m-d'),'DataFinalizado'=>null,
        'rma1_ext'=>'','rma2_ext'=>'','rma3_ext'=>'','rma4_ext'=>'','rma5_ext'=>'');
    return $payload;
}
function dotbright_repair_send_to_rma(PDO $db,$id,$version,$input,$actor)
{
    if ($actor<=0) throw new InvalidArgumentException('Operador inválido.');
    if (!dotbright_repair_rma_ready($db)) throw new RuntimeException('Aplique o SQL de integração RRE/RMA fornecido no chat.');
    $db->beginTransaction();
    try {
        $lock=$db->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'?' FOR UPDATE':'';
        $q=$db->prepare('SELECT * FROM repair_orders WHERE id=?'.$lock);$q->execute(array($id));$row=$q->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new InvalidArgumentException('Pedido não encontrado.');
        $q=$db->prepare('SELECT ID,repair_order_id FROM rmas WHERE repair_order_id=?');$q->execute(array($id));$existing=$q->fetch(PDO::FETCH_ASSOC);
        if (!empty($row['rma_id'])) {
            if (!$existing || (int)$row['rma_id']!==(int)$existing['ID']) throw new RuntimeException('A ligação à RMA não é válida. Verifique o pedido antes de repetir.');
            $db->commit();return (int)$existing['ID'];
        }
        if ((int)$row['version']!==$version) throw new RuntimeException('O pedido foi alterado por outro utilizador. Recarregue antes de enviar.');
        if (in_array($row['status'],array('cancelled','closed'),true) || in_array($row['repair_status'],array('cancelled','completed'),true)) throw new InvalidArgumentException('Não é possível enviar um pedido terminado ou cancelado para RMA.');
        if ($existing) $rma_id=(int)$existing['ID'];
        else {
            $options=dotbright_repair_rma_options($db);
            foreach(array('rma_state'=>'states','rma_origin'=>'origins') as $field=>$list) {
                $value=$input[$field] ?? null;
                if (!is_scalar($value) || !preg_match('/^[1-9][0-9]*$/D',(string)$value) || !in_array((int)$value,array_map('intval',array_column($options[$list],'ID')),true)) throw new InvalidArgumentException('Selecione um estado inicial e uma proveniência válidos da RMA.');
            }
            $payload=dotbright_repair_rma_payload($row,$input,$actor);
            foreach ($options['origins'] as $origin) if ((int)$origin['ID']===$payload['m_origem']) $payload['Loja']=$origin['Loja'];
            $cols=array_keys($payload);$q=$db->prepare('INSERT INTO rmas ('.implode(',',$cols).') VALUES ('.implode(',',array_fill(0,count($cols),'?')).')');$q->execute(array_values($payload));
            $rma_id=(int)$db->lastInsertId();
        }
        // The legacy RMA table may be MyISAM: recovery uses its unique repair_order_id,
        // so a failed source update never creates another RMA on retry.
        $q=$db->prepare('UPDATE rmas SET m_tracking=? WHERE ID=? AND repair_order_id=?');$q->execute(array('DB'.str_pad((string)$rma_id,6,'0',STR_PAD_LEFT),$rma_id,$id));
        $now=gmdate('Y-m-d H:i:s');
        $q=$db->prepare('UPDATE repair_orders SET rma_id=?,rma_sent_at=?,rma_sent_by=?,repair_status=?,version=version+1,updated_at=?,updated_by=? WHERE id=? AND version=?');
        $q->execute(array($rma_id,$now,$actor,'sent_to_rma',$now,$actor,$id,$version));
        if ($q->rowCount()!==1) throw new RuntimeException('O pedido foi alterado por outro utilizador. Recarregue antes de enviar.');
        dotbright_repair_workflow_event($db,$id,$actor,'send_to_rma',array('repair_status'=>$row['repair_status']),array('rma_id'=>$rma_id,'repair_status'=>'sent_to_rma','recovered'=>(bool)$existing));
        $db->commit();return $rma_id;
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack(); throw $e; }
}
