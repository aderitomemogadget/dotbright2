<?php
if (!isset($withdrawal_state)) exit;
$wr=$withdrawal_state;
$wt=function($key) use ($wr) { return store_withdrawal_escape(store_withdrawal_text($key,$wr['language'])); };
$we='store_withdrawal_escape';
?>
<section class="store-withdrawal" id="withdrawal" aria-labelledby="withdrawal-title" lang="<?=$we($wr['language'])?>">
<h1 id="withdrawal-title"><?=$wt('title')?></h1>
<?php if ($wr['error']): ?><p class="wr-error" role="alert"><?=$wt($wr['error'])?></p><?php endif; ?>
<?php if ($wr['step']==='receipt'): ?>
    <p role="status"><strong><?=$wt('saved')?></strong></p>
    <p><?=$wt($wr['row']['mail_state']==='sent' ? 'sent' : 'pending')?></p>
    <pre><?=$we(store_withdrawal_receipt($wr['row']))?></pre>
    <a class="wr-button" href="?receipt=<?=$we($wr['receipt_token'])?>&amp;download=1&amp;site_lingua=<?=$we($wr['language'])?>"><?=$wt('download')?></a>
    <a href="livre-resolucao.php?site_lingua=<?=$we($wr['language'])?>"><?=$wt('another')?></a>
<?php elseif ($wr['step']==='review'): ?>
    <p><?=$wt('review_help')?></p>
    <pre><?=$we(store_withdrawal_declaration($wr['data']))?></pre>
    <form method="post" action="livre-resolucao.php?site_lingua=<?=$we($wr['language'])?>">
        <input type="hidden" name="_csrf" value="<?=$we($wr['csrf'])?>">
        <input type="hidden" name="draft" value="<?=$we($wr['draft'])?>">
        <button type="submit" name="action" value="confirm"><?=$wt('confirm')?></button>
        <button type="submit" name="action" value="edit" class="wr-secondary"><?=$wt('edit')?></button>
    </form>
<?php elseif ($wr['step']==='form'): ?>
    <p><?=$wt('intro')?></p>
    <form method="post" action="livre-resolucao.php?site_lingua=<?=$we($wr['language'])?>">
        <input type="hidden" name="_csrf" value="<?=$we($wr['csrf'])?>">
        <input type="hidden" name="action" value="review">
        <div class="wr-trap" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
        <?php foreach (array('name'=>180,'order_reference'=>100,'email'=>254) as $field=>$length): ?>
        <label for="wr-<?=$field?>"><?=$wt($field)?></label>
        <input id="wr-<?=$field?>" name="<?=$field?>" type="<?=$field==='email'?'email':'text'?>" autocomplete="<?=$field==='order_reference'?'off':$field?>" maxlength="<?=$length?>" value="<?=$we($wr['data'][$field])?>" required>
        <?php endforeach; ?>
        <label for="wr-scope"><?=$wt('scope')?></label>
        <select id="wr-scope" name="scope"><option value="whole" <?=$wr['data']['scope']==='whole'?'selected':''?>><?=$wt('whole')?></option><option value="partial" <?=$wr['data']['scope']==='partial'?'selected':''?>><?=$wt('partial')?></option></select>
        <label for="wr-items"><?=$wt('items')?></label><p id="wr-items-help"><?=$wt('items_help')?></p>
        <textarea id="wr-items" name="items" maxlength="2000" aria-describedby="wr-items-help"><?=$we($wr['data']['items'])?></textarea>
        <label for="wr-received"><?=$wt('received_on')?></label><input id="wr-received" name="received_on" type="date" max="<?=gmdate('Y-m-d')?>" value="<?=$we($wr['data']['received_on'])?>">
        <p><?=$wt('data_note')?></p>
        <button type="submit"><?=$wt('review')?></button>
    </form>
<?php endif; ?>
<aside class="wr-info"><p><?=$wt('guidance')?></p><p><?=$wt('alternatives')?></p><a href="mailto:lojaonline@dotbright.pt">lojaonline@dotbright.pt</a></aside>
<nav aria-label="<?=$wt('title')?>"><a href="<?=SITE_HTTP_URL?>pages.php?page_id=37"><?=$wt('terms')?></a><a href="<?=SITE_HTTP_URL?>pages.php?page_id=22"><?=$wt('returns')?></a><a href="<?=SITE_HTTP_URL?>pages.php?page_id=43"><?=$wt('privacy')?></a></nav>
</section>
