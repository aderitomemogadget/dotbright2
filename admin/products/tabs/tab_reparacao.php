<?php
if (!isset($repair_settings_tab) || !$repair_settings_tab) return;
$repair_ready = dotbright_repair_settings_ready();
$repair_values = dotbright_repair_settings_load(array('prod_id' => (int)$ID, 'fam_id' => (int)$ID_Fam));
$repair_values = array_merge(array('time_code' => '', 'time_custom' => '', 'part_type' => '', 'part_label' => '', 'part_detail' => '', 'part_icon' => 'shield', 'warranty_months' => ''), $repair_values);
$repair_escape = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
?>
<div class="panel-body">
<h3>Configuração desta reparação</h3>
<p>Exclusivo da família REPARAÇÕES e respetivas subfamílias. O preço continua a ser definido no separador Geral.</p>
<?php if (!$repair_ready) { ?>
<p class="alert alert-warning">A tabela de configuração ainda não existe. Aplique primeiro a migração da fase 2 fornecida no chat. Os dados atuais da ficha mantêm-se.</p>
<?php } else { ?>
<input type="hidden" name="repair_settings_present" value="1">
<p>O tipo de peça e os textos configurados aqui são usados na ficha, no resumo, nos novos pedidos e nos emails. Prevalecem sobre a identificação pelo nome do artigo. Para uma peça OLED Premium não original, selecione «Compatível de qualidade premium». «Qualidade equivalente ao original» não significa «Peça original»; confirme também o nome e a descrição curta personalizados.</p>
<?php foreach (array('time_code' => 'Prazo estimado de reparação em loja', 'part_type' => 'Tipo de peça', 'part_icon' => 'Ícone da peça') as $key => $label) { ?>
<div class="form-group"><label for="repair_<?= $key ?>"><?= $label ?></label>
<select class="form-control" id="repair_<?= $key ?>" name="repair_settings[<?= $key ?>]">
<?php $repair_options = dotbright_repair_settings_options(); foreach ($repair_options[$key] as $value => $text) { ?>
<option value="<?= $repair_escape($value) ?>"<?= $repair_values[$key] === $value ? ' selected' : '' ?>><?= $repair_escape($text) ?></option>
<?php } ?></select></div>
<?php } ?>
<?php foreach (array('time_custom' => array('Prazo personalizado (preencher se selecionado acima)', 120), 'part_label' => array('Nome da peça apresentado (opcional, substitui o nome do tipo)', 120), 'part_detail' => array('Descrição curta da peça (opcional)', 500)) as $key => $field) { ?>
<div class="form-group"><label for="repair_<?= $key ?>"><?= $field[0] ?></label><input class="form-control" type="text" id="repair_<?= $key ?>" name="repair_settings[<?= $key ?>]" maxlength="<?= $field[1] ?>" value="<?= $repair_escape($repair_values[$key]) ?>"></div>
<?php } ?>
<div class="form-group"><label for="repair_warranty_months">Garantia em meses</label><input class="form-control" type="number" id="repair_warranty_months" name="repair_settings[warranty_months]" min="0" max="120" step="1" value="<?= $repair_escape($repair_values['warranty_months']) ?>"><p>Vazio: mantém a regra atual desta reparação. 0: apresenta 0 meses.</p></div>
<p>As definições são guardadas ao guardar a ficha. O prazo refere-se à reparação em loja, não ao transporte RRE.</p>
<?php } ?>
</div>
