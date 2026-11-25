<?php
if (!isset($ro_row,$ro_csrf) || !$ro_row || empty($ro_workflow)) return;
$ro_rma_ready=$ro_rma_ready ?? false;
?>
<?php if (!empty($ro_row['rma_id'])) { ?>
<p class="ro-notice">Associado à <a href="../../rmas/rmas/pagina.php?ID=<?=(int)$ro_row['rma_id']?>">RMA #<?=(int)$ro_row['rma_id']?></a> em <?=$re($ro_row['rma_sent_at'])?> UTC. Os estados abaixo são do pedido RRE; a ficha técnica mantém os seus próprios estados.</p>
<?php } elseif ($ro_rma_ready && !empty($ro_can_reveal) && empty($ro_details['legacy']) && !in_array($ro_row['status'],array('closed','cancelled'),true) && !in_array($ro_row['repair_status'],array('completed','cancelled'),true)) { ?>
<details class="ro-card ro-workflow" id="ro-rma-transfer" <?=isset($_GET['transfer'])?'open':''?>>
<summary>Enviar para RMA</summary>
<p>Cria uma ficha técnica associada a este pedido, sem duplicar clientes, registar pagamentos ou enviar notificações. Os valores são estimados; os dados de acesso continuam protegidos no pedido de origem.</p>
<form method="post" action="pagina.php">
<input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="action" value="send_to_rma"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="version" value="<?=(int)$ro_row['version']?>">
<div class="ro-transfer-fields">
<?php foreach(array('rma_state'=>array('Estado inicial da RMA','states'),'rma_origin'=>array('Proveniência da RMA','origins')) as $ro_field=>$ro_config) { ?>
<label><?=$re($ro_config[0])?><select name="<?=$re($ro_field)?>" required><option value="">Selecionar</option><?php foreach(($ro_rma_options[$ro_config[1]] ?? array()) as $ro_option) { ?><option value="<?=(int)$ro_option['ID']?>" <?=($_POST[$ro_field] ?? '')===(string)$ro_option['ID']?'selected':''?>><?=$re($ro_option['Cabecalho'])?></option><?php } ?></select></label>
<?php } ?>
<?php foreach(array('rma_brand'=>'Marca (se conhecida)','rma_model'=>'Modelo (se conhecido)','rma_imei'=>'IMEI (se conhecido)','rma_serial'=>'Número de série (se conhecido)') as $ro_field=>$ro_label) { ?>
<label><?=$re($ro_label)?><input name="<?=$re($ro_field)?>" maxlength="255" value="<?=$re(is_string($_POST[$ro_field] ?? null)?$_POST[$ro_field]:'')?>"></label>
<?php } ?></div>
<?php $ro_missing_options=empty($ro_rma_options['states']) || empty($ro_rma_options['origins']); ?>
<?php if ($ro_missing_options) { ?><p class="ro-notice">Configure pelo menos um estado e uma proveniência visíveis na RMA antes de transferir.</p><?php } ?>
<button type="submit" <?=$ro_missing_options?'disabled':''?>>Criar e associar RMA</button>
</form></details>
<?php } elseif (!$ro_rma_ready) { ?><p class="ro-notice">A ligação RMA aguarda a aplicação completa do SQL de integração.</p><?php } ?>
<?php if (!empty($ro_events)) { ?>
<details class="ro-card ro-workflow"><summary>Histórico de estados e transferências</summary><div class="ro-table"><table><thead><tr><th>Data UTC</th><th>Operador</th><th>Operação</th><th>Dados registados</th></tr></thead><tbody>
<?php foreach($ro_events as $ro_event) { $ro_after=json_decode($ro_event['after_json'],true) ?: array();$ro_changes=array();
foreach($ro_after as $ro_key=>$ro_value) {
    if ($ro_key==='recovered') continue;
    $ro_label=array('status'=>'Estado pedido','repair_status'=>'Estado reparação','product_status'=>'Estado produto','rma_id'=>'RMA')[$ro_key] ?? $ro_key;
    $ro_map=$ro_key==='repair_status'?dotbright_repair_workflow_statuses():($ro_key==='product_status'?dotbright_repair_product_statuses():($ro_key==='status'?dotbright_repair_order_statuses():array()));
    $ro_changes[]=$ro_label.': '.($ro_map[$ro_value] ?? (string)$ro_value);
} ?>
<tr><td><?=$re($ro_event['created_at'])?></td><td>#<?=(int)$ro_event['actor_id']?></td><td><?=$ro_event['event_type']==='send_to_rma'?'Envio para RMA':'Alteração de estados'?></td><td><?=$re(implode(' · ',$ro_changes))?></td></tr>
<?php } ?></tbody></table></div></details>
<?php } ?>
