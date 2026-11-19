<?php
if (!isset($ro_row, $ro_csrf) || !$ro_row) return;
$ro_details=json_decode($ro_row['details_json'],true) ?: array();
$ro_customer=$ro_details['customer'] ?? array();
$ro_product=$ro_details['product'] ?? array();
$ro_quote=$ro_details['quote'] ?? array();
$ro_start_tab=$ro_access!==null || (($_POST['action'] ?? '')==='reveal')?'access':'customer';
?>
<div class="ro-detail" data-start-tab="<?=$re($ro_start_tab)?>">
<div class="ro-request-bar">
    <div class="ro-request-identity"><span class="ro-caption">Pedido de reparação</span><h2>REP-<?=(int)$ro_row['id']?></h2><p><?=$re($ro_row['created_at']?$ro_row['created_at'].' UTC':'Data original desconhecida')?><?php if ($ro_row['legacy_id']) { ?> · Antigo #<?=(int)$ro_row['legacy_id']?><?php } ?></p></div>
    <div class="ro-request-total"><span class="ro-caption">Total estimado</span><strong><?=$ro_row['total_cents']===null?'—':$re(number_format($ro_row['total_cents']/100,2,',','.').' €')?></strong></div>
    <form method="post" action="pagina.php" class="ro-status-form"><input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="version" value="<?=(int)$ro_row['version']?>"><label for="ro-status">Estado<select id="ro-status" name="status"><?php foreach($ro_labels as $value=>$label) { ?><option value="<?=$re($value)?>" <?=$ro_row['status']===$value?'selected':''?>><?=$re($label)?></option><?php } ?></select></label><button type="submit">Guardar estado</button></form>
</div>
<p class="ro-update-note">Última atualização: <?=$re($ro_row['updated_at'])?> UTC<?php if ($ro_row['updated_by']) { ?> · Operador <?=(int)$ro_row['updated_by']?><?php } ?></p>
<?php if (!empty($ro_details['legacy'])) { ?><p class="ro-notice"><?=$re($ro_details['missing'])?></p><?php } ?>
<div class="ro-detail-tabs" role="tablist" aria-label="Dados do pedido" hidden>
<?php foreach(array('customer'=>'Cliente e moradas','repair'=>'Reparação e valores','messages'=>'Emails e consentimentos','access'=>'Acesso ao equipamento') as $key=>$label) { ?>
<button type="button" role="tab" id="ro-tab-<?=$key?>" aria-controls="ro-panel-<?=$key?>" aria-selected="<?=$key==='customer'?'true':'false'?>" tabindex="<?=$key==='customer'?'0':'-1'?>" data-tab="<?=$key?>"><?=$re($label)?></button>
<?php } ?></div>
<section class="ro-detail-panel" id="ro-panel-customer" role="tabpanel" aria-labelledby="ro-tab-customer"><div class="ro-grid ro-customer-grid">
<?php foreach (array('Dados do cliente'=>array('r_nome'=>'Nome','r_tel'=>'Telefone','r_email'=>'Email','r_nif'=>'NIF'),'Recolha e entrega'=>array('r_recolha'=>'Morada de recolha','r_cp'=>'Código postal','r_cidade'=>'Cidade','r_entrega'=>'Morada de entrega','period'=>'Período de recolha'),'Faturação'=>array('r_nome2'=>'Nome / empresa','r_morada2'=>'Morada','r_cp2'=>'Código postal','r_nif2'=>'NIF')) as $title=>$fields) { ?>
<section class="ro-card"><h3><?=$re($title)?></h3><dl><?php foreach($fields as $field=>$label) $ro_show($label,$ro_customer[$field] ?? null); ?></dl></section><?php } ?>
</div></section>
<section class="ro-detail-panel" id="ro-panel-repair" role="tabpanel" aria-labelledby="ro-tab-repair"><div class="ro-grid ro-repair-grid">
<section class="ro-card"><h3>Reparação no momento do pedido</h3><dl><?php
$ro_show('Produto ID',$ro_row['product_id']);$ro_show('Nome',$ro_product['name'] ?? null);$ro_show('Referência',$ro_product['reference'] ?? null);$ro_show('EAN',$ro_product['ean'] ?? null);$ro_show('Prazo em loja',$ro_quote['service']['time'] ?? null);$ro_show('Peça',$ro_quote['service']['part'] ?? null);$ro_show('Descrição da peça',$ro_quote['service']['part_detail'] ?? null);$ro_show('Garantia',$ro_quote['warranty'] ?? null);
?></dl></section>
<section class="ro-card"><h3>Valores registados</h3><dl><?php foreach(array('repair_gross'=>'Reparação com IVA','repair_net'=>'Reparação sem IVA','shipping_gross'=>'Portes com IVA','shipping_net'=>'Portes sem IVA','vat'=>'IVA','total'=>'Total') as $field=>$label) $ro_show($label,isset($ro_quote[$field])?number_format($ro_quote[$field],2,',','.').' €':null); ?></dl></section>
</div></section>
<section class="ro-detail-panel" id="ro-panel-messages" role="tabpanel" aria-labelledby="ro-tab-messages"><div class="ro-grid">
<section class="ro-card"><h3>Emails</h3><p>Estes estados indicam a resposta do serviço de envio; não confirmam entrega na caixa de entrada.</p><dl><?php foreach(array('staff'=>'Assistência','copy'=>'Cópia interna','customer'=>'Cliente') as $field=>$label) $ro_show($label,$ro_mail_labels[$ro_row[$field.'_mail']] ?? $ro_row[$field.'_mail']);$ro_show('Última tentativa (UTC)',$ro_row['mail_updated_at']); ?></dl></section>
<section class="ro-card"><h3>Consentimentos</h3><dl><?php $ro_show('Termos e Condições',isset($ro_details['consents']['terms'])?'Aceites':null);$ro_show('Confidencialidade',isset($ro_details['consents']['confidentiality'])?'Aceite':null);$ro_show('Aceitação (UTC)',$ro_details['consents']['accepted_at'] ?? null); ?></dl></section>
</div></section>
<section class="ro-detail-panel ro-access-panel" id="ro-panel-access" role="tabpanel" aria-labelledby="ro-tab-access">
<section class="ro-card"><h3>Acesso ao equipamento</h3>
<?php if (!$ro_row['access_cipher']) { ?><p>Dados de acesso não fornecidos ou indisponíveis no registo antigo.</p>
<?php } elseif (!$ro_can_reveal) { ?><p>A consulta exige permissões de Produtos e Encomendas.</p>
<?php } elseif ($ro_access!==null) { ?><dl><?php foreach(array('code'=>'Código','pattern'=>'Padrão') as $field=>$label) if(isset($ro_access[$field])) $ro_show($label,$ro_access[$field]); ?></dl><p>Dados confidenciais, apenas para reparação e testes.</p>
<?php } else { ?><form method="post" action="pagina.php"><input type="hidden" name="_csrf" value="<?=$re($ro_csrf)?>"><input type="hidden" name="id" value="<?=(int)$ro_row['id']?>"><input type="hidden" name="action" value="reveal"><label><input type="checkbox" name="confirm" value="1" required> Preciso de consultar os dados para esta reparação.</label><button type="submit">Revelar dados de acesso</button></form><?php } ?>
</section></section>
</div>
<script defer src="reparacoes.js?v=<?=filemtime(__DIR__.'/reparacoes.js')?>"></script>
