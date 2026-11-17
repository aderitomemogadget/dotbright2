<?php if(!isset($privacy_state))exit; $ps=$privacy_state;$pe='store_privacy_escape';$pk=store_privacy_kinds();$pl=store_privacy_statuses(); ?>
<section class="store-privacy" aria-labelledby="privacy-title">
<h1 id="privacy-title">Privacidade e dados pessoais</h1>
<?php if($ps['error']): ?><p class="sp-error" role="alert"><?=$pe($ps['error'])?></p><?php endif; ?>
<?php if($ps['success']): ?><p class="sp-success" role="status"><?=$pe($ps['success'])?></p><?php endif; ?>
<?php if(empty($ps['customer'])): ?>
<p>Inicie sessão para gerir os seus dados, consultar pedidos e pedir a remoção da conta.</p>
<a class="sp-button" href="<?=$pe(store_privacy_account_url())?>">Iniciar sessão na minha conta</a>
<?php else: ?>
<p>Gira as suas preferências e exerça os seus direitos. Pode consultar aqui a resposta e o estado dos pedidos.</p>
<a href="<?=$pe(store_privacy_account_url())?>">← Voltar à minha conta</a>
<div class="sp-info"><strong>A remoção exige análise da equipa.</strong> O pedido não encerra a conta de imediato. Os dados com fundamento legal de conservação, incluindo documentos de encomendas e faturação, são analisados separadamente.</div>
<h2>Fazer um pedido</h2>
<form method="post" action="privacidade-conta.php">
<input type="hidden" name="_csrf" value="<?=$pe($ps['csrf'])?>"><input type="hidden" name="action" value="request">
<div class="sp-trap" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
<label for="sp-kind">O que pretende fazer?</label>
<select id="sp-kind" name="kind"><?php foreach($pk as $key=>$label): ?><option value="<?=$pe($key)?>" <?=($privacy_input['kind']??'erasure')===$key?'selected':''?>><?=$pe($label)?></option><?php endforeach; ?></select>
<label for="sp-message">Informação adicional (opcional)</label><p class="sp-small" id="sp-message-help">Identifique os dados a corrigir ou o âmbito pretendido. Não precisa de indicar um motivo para pedir a remoção da conta.</p>
<textarea id="sp-message" name="message" maxlength="2000" aria-describedby="sp-message-help"><?=$pe(is_string($privacy_input['message']??null)?$privacy_input['message']:'')?></textarea>
<label class="sp-check" id="sp-erasure-ack"><input type="checkbox" name="acknowledged" value="1"><span>Confirmo que pretendo pedir a remoção da conta. A equipa analisará o apagamento e os dados que devam ser conservados antes de encerrar o acesso.</span></label>
<button type="submit">Registar pedido</button>
</form>
<h2>Os meus pedidos</h2>
<?php if(!$ps['requests']): ?><p>Ainda não existem pedidos nesta área.</p><?php endif; ?>
<?php foreach($ps['requests'] as $r): ?>
<article class="sp-card"><h3><?=$pe($r['reference'])?> · <?=$pe($pk[$r['kind']]??$r['kind'])?></h3>
<p><strong><?=$pe($pl[$r['status']]??$r['status'])?></strong></p><p class="sp-meta">Recebido em <?=$pe($r['created_at'])?> UTC · Prazo inicial de resposta: <?=$pe($r['due_at'])?> UTC</p>
<?php if($r['customer_response']): ?><p><?=$pe($r['customer_response'])?></p><?php endif; ?>
<?php if($r['export_ready_at'] && in_array($r['status'],array('approved','completed'),true)): ?><a class="sp-button" href="privacidade-conta.php?export=<?=(int)$r['id']?>">Descarregar os meus dados (JSON)</a><?php endif; ?>
<?php if(in_array($r['status'],array('pending','review','awaiting_customer'),true)): ?><form method="post" action="privacidade-conta.php"><input type="hidden" name="_csrf" value="<?=$pe($ps['csrf'])?>"><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?=(int)$r['id']?>"><button class="sp-secondary" type="submit">Cancelar este pedido</button></form><?php endif; ?>
</article>
<?php endforeach; ?>
<h2>Preferências e consentimentos</h2>
<p>A preferência de marketing é independente da informação de privacidade e dos termos da compra.</p>
<form method="post" action="privacidade-conta.php"><input type="hidden" name="_csrf" value="<?=$pe($ps['csrf'])?>"><input type="hidden" name="action" value="newsletter">
<label for="sp-newsletter">Comunicações por email / newsletter</label><select id="sp-newsletter" name="newsletter"><option value="0" <?=empty($ps['customer']['Newsletter'])?'selected':''?>>Não pretendo receber</option><option value="1" <?=!empty($ps['customer']['Newsletter'])?'selected':''?>>Quero receber novidades e campanhas</option></select>
<button class="sp-secondary" type="submit">Guardar preferência</button></form>
<p class="sp-small">As preferências guardadas têm histórico. As definições de cookies dizem respeito a este dispositivo.</p><button type="button" class="sp-secondary" data-privacy-cookies hidden>Gerir cookies neste dispositivo</button>
<?php endif; ?>
<p><a href="<?=$pe(STORE_PRIVACY_SITE_URL)?>pages.php?page_id=43">Consultar a política de privacidade</a></p>
</section>
