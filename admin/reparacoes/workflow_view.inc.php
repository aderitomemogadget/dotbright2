<?php
if (!isset($ro_row,$ro_csrf) || !$ro_row) return;
$ro_rma_ready=$ro_rma_ready ?? false;
$ro_rma_summary=$ro_rma_summary ?? null;
$ro_status_fields=array('status'=>array('Pedido RRE','Receção e gestão do pedido.',$ro_labels));
if ($ro_workflow) $ro_status_fields+=array(
    'repair_status'=>array('Reparação e pagamento','Encaminhamento e situação da reparação.',dotbright_repair_workflow_statuses()),
    'product_status'=>array('Situação do equipamento','Receção, transporte e entrega ao cliente.',dotbright_repair_product_statuses()));
?>
<div class="ro-operations">
<section class="ro-management" aria-label="Acompanhamento do pedido">
<div class="ro-section-title"><span class="ro-step-number">1</span><div><h3>Acompanhar o pedido</h3><p>Atualize a gestão do pedido e a situação do equipamento.</p></div></div>
<form method="post" action="pagina.php" class="ro-status-form">
<input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="version" value="<?=(int)$ro_row['version']?>">
<?php foreach($ro_status_fields as $ro_field=>$ro_config) { ?>
<label for="ro-<?=$re($ro_field)?>"><strong><?=$re($ro_config[0])?></strong><select id="ro-<?=$re($ro_field)?>" name="<?=$re($ro_field)?>" aria-describedby="ro-help-<?=$re($ro_field)?>">
<?php foreach($ro_config[2] as $ro_value=>$ro_label) { ?><option value="<?=$re($ro_value)?>" <?=($ro_row[$ro_field] ?? '')===$ro_value?'selected':''?>><?=$re($ro_value==='not_recorded'?'Por definir':$ro_label)?></option><?php } ?>
</select><small id="ro-help-<?=$re($ro_field)?>"><?=$re($ro_config[1])?></small></label>
<?php } ?>
<div class="ro-save-row"><span>Última atualização: <?=$re($ro_row['updated_at'])?> UTC<?php if ($ro_row['updated_by']) { ?> · Operador #<?=(int)$ro_row['updated_by']?><?php } ?></span><button type="submit">Guardar estados do pedido</button></div>
</form>
</section>
<section class="ro-technical" aria-label="Ficha técnica RMA">
<div class="ro-section-title"><span class="ro-step-number">2</span><div><h3>Tratar a assistência técnica</h3><p>A intervenção é acompanhada na ficha RMA.</p></div></div>
<?php if (!empty($ro_row['rma_id'])) { ?>
<div class="ro-rma-linked"><div><span class="ro-caption">Ficha associada</span><strong>RMA #<?=(int)$ro_row['rma_id']?></strong></div><a class="ro-button" href="../../rmas/rmas/pagina.php?ID=<?=(int)$ro_row['rma_id']?>">Abrir ficha técnica →</a></div>
<dl class="ro-technical-facts"><div><dt>Estado técnico atual</dt><dd><?=$re($ro_rma_summary['state_label'] ?? 'Consultar na RMA')?></dd></div><div><dt>Proveniência</dt><dd><?=$re($ro_rma_summary['origin_label'] ?? 'Consultar na RMA')?></dd></div></dl>
<?php if ($ro_rma_summary && empty($ro_rma_summary['origin_known'])) { ?>
<p class="ro-notice">Esta ficha recebeu anteriormente um código de empresa em vez da proveniência do atendimento. Escolha a origem correta para corrigir o registo.</p>
<?php if (!empty($ro_can_reveal)) { ?><form method="post" action="pagina.php">
<input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="action" value="fix_rma_origin"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="version" value="<?=(int)$ro_row['version']?>"><input type="hidden" name="rma_origin_before" value="<?=(int)$ro_rma_summary['m_origem']?>">
<div class="ro-transfer-fields"><label>Proveniência correta<select name="rma_origin" required><option value="">Selecionar</option><?php foreach(dotbright_rma_origins() as $ro_origin_id=>$ro_origin_label) { ?><option value="<?=(int)$ro_origin_id?>"><?=$re($ro_origin_label)?></option><?php } ?></select></label></div>
<button type="submit">Guardar proveniência da RMA</button></form><?php } ?>
<?php } ?>
<p class="ro-rma-caption">Associada em <?=$re($ro_row['rma_sent_at'])?> UTC.</p>
<p class="ro-guidance">Altere o estado técnico na RMA. No regresso do equipamento reparado, indique aqui «Recebido da RMA» e «Pronto para entrega».</p>
<p class="ro-rma-caption">Os estados da RMA não alteram automaticamente os estados do pedido.</p>
<?php } elseif ($ro_rma_ready && !empty($ro_can_reveal) && empty($ro_details['legacy']) && !in_array($ro_row['status'],array('closed','cancelled'),true) && !in_array($ro_row['repair_status'],array('completed','cancelled'),true)) { ?>
<p class="ro-guidance">Ainda não existe ficha técnica. Crie uma RMA para encaminhar a intervenção e manter a ligação a este pedido.</p>
<details class="ro-workflow ro-rma-transfer" id="ro-rma-transfer" <?=isset($_GET['transfer']) || (($_POST['action'] ?? '')==='send_to_rma')?'open':''?>>
<summary><span class="ro-rma-trigger">Criar ficha RMA <span aria-hidden="true">▾</span></span></summary>
<p class="ro-rma-intro">Escolha o estado inicial e a origem do atendimento. Criar a ficha coloca a reparação em «Enviado para RMA»; não regista transporte nem pagamento.</p>
<form method="post" action="pagina.php">
<input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="action" value="send_to_rma"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="version" value="<?=(int)$ro_row['version']?>">
<div class="ro-transfer-fields">
<?php foreach(array('rma_state'=>array('Estado inicial da RMA','states'),'rma_origin'=>array('Proveniência do atendimento','origins')) as $ro_field=>$ro_config) { ?>
<label><?=$re($ro_config[0])?><select name="<?=$re($ro_field)?>" required><option value="">Selecionar</option><?php foreach(($ro_rma_options[$ro_config[1]] ?? array()) as $ro_option) { ?><option value="<?=(int)$ro_option['ID']?>" <?=($_POST[$ro_field] ?? '')===(string)$ro_option['ID']?'selected':''?>><?=$re($ro_option['Cabecalho'])?></option><?php } ?></select></label>
<?php } ?>
<?php foreach(array('rma_brand'=>'Marca (opcional)','rma_model'=>'Modelo (opcional)','rma_imei'=>'IMEI (opcional)','rma_serial'=>'Número de série (opcional)') as $ro_field=>$ro_label) { ?>
<label><?=$re($ro_label)?><input name="<?=$re($ro_field)?>" maxlength="255" value="<?=$re(is_string($_POST[$ro_field] ?? null)?$_POST[$ro_field]:'')?>"></label>
<?php } ?></div>
<?php $ro_missing_options=empty($ro_rma_options['states']) || empty($ro_rma_options['origins']); ?>
<?php if ($ro_missing_options) { ?><p class="ro-notice">Configure um estado visível e as proveniências da RMA antes de transferir.</p><?php } ?>
<button type="submit" <?=$ro_missing_options?'disabled':''?>>Confirmar criação da RMA</button>
</form></details>
<?php } elseif (!$ro_rma_ready) { ?><p class="ro-notice">A ligação RMA aguarda a aplicação completa do SQL de integração.</p>
<?php } else { ?><p class="ro-rma-caption">Não existe ficha RMA associada. A criação não está disponível para este pedido ou para as suas permissões.</p><?php } ?>
</section></div>
<details class="ro-workflow ro-workflow-history">
<summary><strong>Histórico de alterações</strong><span><?=$ro_events?count($ro_events).' registo(s) recente(s)':'Sem alterações registadas'?> <span aria-hidden="true">▾</span></span></summary>
<?php if (!$ro_events) { ?><p class="ro-rma-caption">Ainda não existem alterações registadas. As próximas gravações e associações a RMA ficam guardadas com operador e data.</p>
<?php } else { ?><ol class="ro-history-list">
<?php foreach($ro_events as $ro_event) {
    $ro_after=json_decode($ro_event['after_json'],true) ?: array();
    $ro_before=json_decode($ro_event['before_json'] ?? '{}',true) ?: array();
    $ro_changes=array();
    foreach(array('status'=>array('Pedido RRE',dotbright_repair_order_statuses()),'repair_status'=>array('Reparação',dotbright_repair_workflow_statuses()),'product_status'=>array('Equipamento',dotbright_repair_product_statuses())) as $ro_key=>$ro_config) {
        if (!array_key_exists($ro_key,$ro_after) || (($ro_before[$ro_key] ?? null)===$ro_after[$ro_key])) continue;
        $ro_from=isset($ro_before[$ro_key])?($ro_config[1][$ro_before[$ro_key]] ?? $ro_before[$ro_key]):'Não registado';
        $ro_to=$ro_config[1][$ro_after[$ro_key]] ?? $ro_after[$ro_key];
        $ro_changes[]=$ro_config[0].': '.$ro_from.' → '.$ro_to;
    }
    if ($ro_event['event_type']==='rma_origin') {
        $ro_changes[]='Proveniência da RMA #'.(int)($ro_after['rma_id'] ?? 0).': código '.(int)($ro_before['rma_origin'] ?? 0).' → '.($ro_after['rma_origin_label'] ?? (string)($ro_after['rma_origin'] ?? ''));
    } elseif (isset($ro_after['rma_id'])) array_unshift($ro_changes,'Associação à RMA #'.(int)$ro_after['rma_id']);
?>
<li><div><strong><?=$ro_event['event_type']==='rma_origin'?'Proveniência da RMA corrigida':($ro_event['event_type']==='send_to_rma'?'Ficha RMA associada':'Estados do pedido guardados')?></strong><span><?=$re($ro_event['created_at'])?> UTC · Operador #<?=(int)$ro_event['actor_id']?></span></div><ul><?php foreach($ro_changes ?: array('Gravação sem alteração dos estados.') as $ro_change) { ?><li><?=$re($ro_change)?></li><?php } ?></ul></li>
<?php } ?></ol><p class="ro-rma-caption">Até 20 registos mais recentes. O histórico anterior mantém-se guardado.</p><?php } ?>
</details>
