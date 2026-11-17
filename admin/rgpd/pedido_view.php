<?php if(!isset($pa_csrf))exit; $pe='store_privacy_escape'; ?>
<link rel="stylesheet" href="../../assets/privacy/privacy.css?v=20261103">
<section class="store-privacy store-privacy-admin" aria-labelledby="privacy-admin-title">
<h1 id="privacy-admin-title">RGPD · Pedidos e consentimentos</h1>
<p>Trate os direitos dos clientes e documente as decisões. A conta mantém-se ativa enquanto o pedido de remoção estiver a aguardar análise.</p>
<?php if($pa_error): ?><p class="sp-error" role="alert"><?=$pe($pa_error)?></p><a href="pedidos.php<?=$pa_id?'?id='.(int)$pa_id:''?>">Recarregar</a><?php endif; ?>
<?php if($pa_row): ?>
<a href="pedidos.php">← Voltar aos pedidos</a><h2><?=$pe($pa_row['reference'])?></h2>
<div class="sp-grid"><div><h3><?=$pe($pa_kinds[$pa_row['kind']]??$pa_row['kind'])?></h3><p><?=$pe($pa_customer['Nome']??'Conta removida')?> · <?=$pe($pa_customer['Email']??'')?></p><p>Conta #<?=(int)$pa_row['customer_id']?> · <?=!empty($pa_customer['Ativo'])?'Acesso ativo':'Acesso encerrado'?></p></div><div><p><strong><?=$pe($pa_statuses[$pa_row['status']]??$pa_row['status'])?></strong></p><p>Recebido: <?=$pe($pa_row['created_at'])?> UTC<br>Prazo inicial de resposta: <?=$pe($pa_row['due_at'])?> UTC</p></div></div>
<h3>Informação enviada pelo cliente</h3><pre><?=$pe($pa_row['message']?:'Sem informação adicional.')?></pre>
<?php if(!in_array($pa_row['status'],array('completed','cancelled'),true)): ?>
<h2>Análise e resposta</h2>
<form method="post" action="pedidos.php?id=<?=(int)$pa_id?>"><input type="hidden" name="_csrf" value="<?=$pe($pa_csrf)?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=(int)$pa_id?>"><input type="hidden" name="version" value="<?=(int)$pa_row['version']?>">
<label for="pa-status">Estado</label><select id="pa-status" name="status"><?php foreach($pa_statuses as $key=>$label):if($key==='cancelled')continue;?><option value="<?=$pe($key)?>" <?=$pa_row['status']===$key?'selected':''?>><?=$pe($label)?></option><?php endforeach; ?></select>
<label for="pa-note">Nota interna de análise / fundamento</label><textarea id="pa-note" name="note" maxlength="2000" required></textarea>
<label for="pa-response">Resposta visível ao cliente na área de privacidade</label><textarea id="pa-response" name="customer_response" maxlength="2000"><?=$pe($pa_row['customer_response'])?></textarea>
<?php if(in_array($pa_row['kind'],array('access','export'),true)): ?><label class="sp-check"><input type="checkbox" name="export_ready" value="1" <?=$pa_row['export_ready_at']?'checked':''?>><span>Após verificação de identidade e do âmbito, disponibilizar a exportação JSON na conta autenticada do cliente.</span></label><?php endif; ?>
<button type="submit">Guardar análise e resposta</button></form>
<?php endif; ?>
<?php if($pa_row['kind']==='erasure' && $pa_row['status']==='approved' && !$pa_row['erased_at']): ?>
<h2>Executar remoção após aprovação</h2>
<div class="sp-info">Esta ação encerra o acesso, remove os dados do perfil local e o livro de endereços e retira o email das listas locais de newsletter. Conserva as encomendas, faturação e os registos de análise. Verifique também processos RMA, ficheiros/anexos, sistemas externos, backups e eventuais deveres de comunicação antes de concluir a análise.</div>
<form method="post" action="pedidos.php?id=<?=(int)$pa_id?>"><input type="hidden" name="_csrf" value="<?=$pe($pa_csrf)?>"><input type="hidden" name="action" value="erase"><input type="hidden" name="id" value="<?=(int)$pa_id?>"><input type="hidden" name="version" value="<?=(int)$pa_row['version']?>">
<label for="pa-erasure-note">Dados conservados, fundamento e tratamento dos restantes sistemas</label><textarea id="pa-erasure-note" name="note" minlength="20" maxlength="2000" required></textarea>
<label class="sp-check"><input type="checkbox" name="retention_reviewed" value="1" required><span>Confirmei a identidade, a aprovação, os registos sujeitos a conservação e o tratamento de ficheiros e sistemas externos. Autorizo a remoção do perfil local.</span></label>
<label class="sp-check"><input type="checkbox" name="notified_customer" value="1" required><span>Comuniquei ao cliente a decisão e os dados que serão conservados, antes de encerrar o acesso à conta.</span></label>
<button class="sp-danger" type="submit">Encerrar acesso e apagar perfil local</button></form>
<?php endif; ?>
<h2>Histórico do pedido</h2><?php foreach($pa_events as $event): ?><article class="sp-card"><p class="sp-meta"><?=$pe($event['created_at'])?> UTC · <?=$pe($event['event_type'])?> · Operador <?=$pe($event['actor_id']??'cliente')?></p><?php if($event['note']): ?><p><?=$pe($event['note'])?></p><?php endif; ?></article><?php endforeach; ?>
<h2>Consentimentos / preferências de marketing</h2><p>Preferência atual: <?=!empty($pa_customer['Newsletter'])?'Receber newsletter':'Não receber newsletter'?>. Os termos e a informação de privacidade não são tratados como consentimento de marketing.</p>
<?php if(!$pa_consents): ?><p>Sem alterações registadas por este módulo.</p><?php endif; ?>
<?php foreach($pa_consents as $consent): ?><p class="sp-meta"><?=$pe($consent['created_at'])?> UTC · <?=$pe($consent['preference'])?>: <?=(int)$consent['previous_value']?> → <?=(int)$consent['current_value']?> · <?=$pe($consent['source'])?></p><?php endforeach; ?>
<?php elseif(!$pa_id): ?>
<form method="get" action="pedidos.php"><div class="sp-grid"><div><label for="pa-q">Referência, nome ou email</label><input id="pa-q" name="q" maxlength="180" value="<?=$pe($pa_filters['q'])?>"></div><div><label for="pa-kind">Direito</label><select id="pa-kind" name="kind"><option value="">Todos</option><?php foreach($pa_kinds as $key=>$label): ?><option value="<?=$pe($key)?>" <?=$pa_filters['kind']===$key?'selected':''?>><?=$pe($label)?></option><?php endforeach; ?></select></div></div>
<label for="pa-filter-status">Estado</label><select id="pa-filter-status" name="status"><option value="">Todos</option><?php foreach($pa_statuses as $key=>$label): ?><option value="<?=$pe($key)?>" <?=$pa_filters['status']===$key?'selected':''?>><?=$pe($label)?></option><?php endforeach; ?></select><button type="submit">Filtrar pedidos</button><a class="sp-button sp-secondary" href="pedidos.php">Limpar</a></form>
<p><?=$pa_total?> pedido(s)</p><div class="sp-table"><table><thead><tr><th>Referência</th><th>Cliente</th><th>Direito</th><th>Estado</th><th>Recebido (UTC)</th><th>Prazo inicial (UTC)</th></tr></thead><tbody><?php foreach($pa_rows as $r): ?><tr><td><a href="pedidos.php?id=<?=(int)$r['id']?>"><?=$pe($r['reference'])?></a></td><td><?=$pe($r['Nome']??'Conta removida')?><br><?=$pe($r['Email']??'')?></td><td><?=$pe($pa_kinds[$r['kind']]??$r['kind'])?></td><td><?=$pe($pa_statuses[$r['status']]??$r['status'])?></td><td><?=$pe($r['created_at'])?></td><td><?=$pe($r['due_at'])?></td></tr><?php endforeach; ?></tbody></table></div>
<nav aria-label="Paginação"><?php if($pa_page>1): ?><a class="sp-button sp-secondary" href="?<?=$pe(http_build_query(array_merge($pa_filters,array('p'=>$pa_page-1))))?>">Anterior</a><?php endif; ?><?php if($pa_total>$pa_page*30): ?><a class="sp-button sp-secondary" href="?<?=$pe(http_build_query(array_merge($pa_filters,array('p'=>$pa_page+1))))?>">Seguinte</a><?php endif; ?></nav>
<?php endif; ?>
<?php if(STORE_PRIVACY_LEGACY_RGPD && isset($dados['Permissoes'][35]) && (int)$dados['Permissoes'][35]===1): ?><p><a href="list.php">Consultar o módulo / registos RGPD anteriores</a></p><?php endif; ?>
<p class="sp-small">O prazo apresentado é o prazo inicial de um mês. Pedidos de informação, prorrogações justificadas e comunicações ao titular devem ser documentados na análise, sem encerrar automaticamente o pedido.</p>
</section>
