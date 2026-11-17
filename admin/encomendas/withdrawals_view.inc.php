<?php if (!isset($wd_csrf)) exit; $we='store_withdrawal_escape'; ?>
<style>
.wd-admin{padding:24px;background:#fff;color:#243447;margin:20px;border-radius:8px}.wd-admin h1{font-size:24px}.wd-admin .wd-fields{display:flex;gap:16px;flex-wrap:wrap;align-items:end}.wd-admin label{display:block}.wd-admin input,.wd-admin select,.wd-admin textarea{display:block;max-width:100%;padding:8px;min-height:40px}.wd-admin textarea{width:100%}.wd-admin button,.wd-admin .wd-button{display:inline-block;padding:12px;margin:8px 4px 8px 0;background:#173f69;color:#fff;border:0;border-radius:4px}.wd-admin pre{white-space:pre-wrap;overflow-wrap:anywhere;padding:16px;border:1px solid #ddd}.wd-admin .wd-table{overflow-x:auto}.wd-admin table{width:100%;border-collapse:collapse}.wd-admin td,.wd-admin th{padding:10px;border-bottom:1px solid #ddd;text-align:left}.wd-admin :focus-visible{outline:3px solid #ed8b24;outline-offset:2px}.wd-admin .wd-error{padding:12px;background:#fff0ed;color:#8c2516}@media(max-width:600px){.wd-admin{margin:8px;padding:14px}.wd-admin .wd-fields>*{width:100%}}
</style>
<section class="wd-admin">
 <h1>Livre resolução</h1>
 <p>Registo da comunicação do cliente. A análise, a devolução e o reembolso são acompanhados aqui, sem executar operações financeiras ou alterar encomendas e stock.</p>
 <?php if ($wd_error) { ?><p role="alert" class="wd-error"><?=$we($wd_error)?></p><a href="withdrawals.php<?=$wd_id?'?id='.(int)$wd_id:''?>">Recarregar</a><?php } ?>
 <?php if ($wd_row) { ?>
  <a href="withdrawals.php">← Voltar à listagem</a>
  <h2><?=$we($wd_row['reference'])?></h2>
  <p>Comunicação: <?=$we($wd_row['submitted_at'])?> UTC · Associação: <?=$we($wd_row['association'])?></p>
  <?php if ($wd_row['order_id']) { ?><p><a href="pagina.php?ID=<?=(int)$wd_row['order_id']?>&amp;ID_ORG=0&amp;tipo=1">Consultar encomenda associada #<?=(int)$wd_row['order_id']?></a></p><?php } else { ?><p>Encomenda por identificar manualmente. A falta de associação não invalida o registo da declaração.</p><?php } ?>
  <h3>Declaração original</h3><pre><?=$we($wd_row['declaration'])?></pre>
  <p>Verifique o contrato, a receção dos bens (incluindo entregas separadas), o prazo aplicável, informação prestada e eventuais exceções por artigo. Não use a expedição/faturação como motivo automático de recusa.</p>
  <form method="post">
   <input type="hidden" name="_csrf" value="<?=$we($wd_csrf)?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=(int)$wd_row['id']?>"><input type="hidden" name="version" value="<?=(int)$wd_row['version']?>">
   <div class="wd-fields">
    <?php foreach (array('status'=>'Análise do pedido','return_status'=>'Devolução','refund_status'=>'Reembolso (acompanhamento)') as $field=>$label) { ?>
     <label><?=$we($label)?><select name="<?=$we($field)?>"><?php foreach ($wd_labels[$field] as $value=>$text) { ?><option value="<?=$we($value)?>" <?=$wd_row[$field]===$value?'selected':''?>><?=$we($text)?></option><?php } ?></select></label>
    <?php } ?>
   </div>
   <label for="wd-note">Fundamentação / nota de acompanhamento</label><textarea id="wd-note" name="note" required maxlength="2000" rows="3"></textarea>
   <button type="submit">Guardar acompanhamento</button>
  </form>
  <h3>Comprovativo por email</h3>
  <p><?=$we($wd_labels['mail'][$wd_row['mail_state']] ?? $wd_row['mail_state'])?> · Tentativas: <?=(int)$wd_row['mail_attempts']?> · Última tentativa: <?=$we($wd_row['mail_attempt_at'] ?? '—')?> UTC</p>
  <?php if (in_array($wd_row['mail_state'],array('failed','uncertain'),true)) { ?>
   <form method="post"><input type="hidden" name="_csrf" value="<?=$we($wd_csrf)?>"><input type="hidden" name="id" value="<?=(int)$wd_row['id']?>"><input type="hidden" name="action" value="retry">
    <label><input type="checkbox" name="acknowledged" value="1" required> Verifiquei o envio anterior. Compreendo que um envio incerto pode já ter sido entregue e que será reenviado o mesmo comprovativo.</label><button type="submit">Tentar reenviar comprovativo</button>
   </form>
  <?php } ?>
  <h3>Histórico administrativo (últimos 200 eventos)</h3>
  <?php foreach ($wd_events as $event) { ?><p><strong><?=$we($event['created_at'])?> UTC</strong> · <?=$we($event['event_type'])?> · Operador <?=$we($event['actor_id'] ?? 'sistema')?></p><pre><?=$we($event['note'])?></pre><?php } ?>
 <?php } elseif (!$wd_id) { ?>
  <form method="get" class="wd-fields">
   <label>Referência, cliente ou email<input name="q" maxlength="150" value="<?=$we($wd_filters['q'])?>"></label>
   <?php foreach (array('status'=>'Estado','mail'=>'Comprovativo') as $field=>$label) { ?><label><?=$we($label)?><select name="<?=$we($field)?>"><option value="">Todos</option><?php foreach ($wd_labels[$field] as $value=>$text) { ?><option value="<?=$we($value)?>" <?=$wd_filters[$field]===$value?'selected':''?>><?=$we($text)?></option><?php } ?></select></label><?php } ?>
   <label>Desde (UTC)<input type="date" name="from" value="<?=$we($wd_filters['from'])?>"></label><label>Até (UTC)<input type="date" name="to" value="<?=$we($wd_filters['to'])?>"></label><button type="submit">Filtrar</button><a href="withdrawals.php">Limpar</a>
  </form>
  <p><?=(int)$wd_total?> pedidos</p>
  <div class="wd-table"><table><thead><tr><th>Pedido</th><th>Encomenda indicada</th><th>Cliente</th><th>Comunicação UTC</th><th>Estado</th><th>Email</th></tr></thead><tbody>
  <?php foreach ($wd_rows as $row) { ?><tr><td><a href="withdrawals.php?id=<?=(int)$row['id']?>"><?=$we($row['reference'])?></a></td><td><?=$we($row['order_reference'])?></td><td><?=$we($row['customer_name'])?></td><td><?=$we($row['submitted_at'])?></td><td><?=$we($wd_labels['status'][$row['status']] ?? $row['status'])?></td><td><?=$we($wd_labels['mail'][$row['mail_state']] ?? $row['mail_state'])?></td></tr><?php } ?>
  </tbody></table></div>
  <nav aria-label="Paginação"><?php if ($wd_page>1) { ?><a class="wd-button" href="?<?=$we(http_build_query(array_merge($wd_filters,array('p'=>$wd_page-1))))?>">Anterior</a><?php } ?><?php if ($wd_page*30<$wd_total) { ?><a class="wd-button" href="?<?=$we(http_build_query(array_merge($wd_filters,array('p'=>$wd_page+1))))?>">Seguinte</a><?php } ?></nav>
 <?php } ?>
</section>
