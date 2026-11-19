<?php
/** Settings belong exclusively to REPARACOES (catalogue family 85) and descendants. */
function dotbright_repair_settings_row($sql)
{
    if (!class_exists('DataObj')) return false;
    $link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs = new DataObj;
    try {
        $ok = $rs->db_open($link, $GLOBALS['DB_name'], $sql);
        $row = $ok && is_array($rs->db_data) ? $rs->db_data : false;
        $rs->db_close();
        return $row;
    } catch (Exception $e) {
        return false;
    }
}
function dotbright_is_repair_family($family)
{
    static $cache = array();
    $family = (int)$family;
    if (isset($cache[$family])) return $cache[$family];
    $origin = $family;
    $seen = array();
    while ($family > 0 && !isset($seen[$family]) && count($seen) < 100) {
        $seen[$family] = true;
        $row = dotbright_repair_settings_row('SELECT ID, ID_ORG FROM products_families WHERE ID='.$family);
        if (!$row) break;
        if ($family === 85) return $cache[$origin] = true;
        $family = (int)$row['ID_ORG'];
    }
    return $cache[$origin] = false;
}
function dotbright_repair_settings_ready()
{
    static $ready = null;
    if ($ready === null) {
        $row = dotbright_repair_settings_row("SHOW TABLES LIKE 'repair_product_settings'");
        $ready = $row && in_array('repair_product_settings', $row, true);
    }
    return $ready;
}
function dotbright_repair_settings_options()
{
    return array(
        'time_code' => array('' => 'Manter atual (sob consulta)', '1h' => '1 hora', '3h' => '3 horas', '1d' => '1 dia', '3d' => '3 dias', '1-2w' => '1 a 2 semanas', 'custom' => 'Texto personalizado'),
        'part_type' => array('' => 'Manter identificação atual', 'apple' => 'Peça oficial Apple', 'original' => 'Peça original', 'take-out' => 'Peça original take-out', 'premium' => 'Compatível de qualidade premium', 'compatible' => 'Peça compatível', 'custom' => 'Texto personalizado'),
        'part_icon' => array('shield' => 'Escudo', 'tools' => 'Ferramentas', 'box' => 'Peça / caixa')
    );
}
function dotbright_repair_settings_validate($input)
{
    if (!is_array($input)) throw new InvalidArgumentException('Configuração de reparação inválida.');
    $out = array();
    foreach (dotbright_repair_settings_options() as $key => $options) {
        $default = $key === 'part_icon' ? 'shield' : '';
        $value = isset($input[$key]) ? $input[$key] : $default;
        if (!is_string($value) || !array_key_exists($value, $options)) throw new InvalidArgumentException('Opção de reparação inválida: '.$key);
        $out[$key] = $value;
    }
    foreach (array('time_custom' => 120, 'part_label' => 120, 'part_detail' => 500) as $key => $limit) {
        $value = isset($input[$key]) ? $input[$key] : '';
        if (!is_string($value) || preg_match('/[<>\x00-\x08\x0B\x0C\x0E-\x1F]/', $value)) throw new InvalidArgumentException('Texto de reparação inválido: '.$key);
        $value = trim($value);
        if (preg_match_all('/./us', $value, $unused) > $limit || !preg_match('//u', $value)) throw new InvalidArgumentException('Texto de reparação demasiado longo ou inválido: '.$key);
        $out[$key] = $value;
    }
    if ($out['time_code'] === 'custom' && $out['time_custom'] === '') throw new InvalidArgumentException('Preencha o prazo personalizado.');
    if ($out['part_type'] === 'custom' && $out['part_label'] === '') throw new InvalidArgumentException('Preencha o nome da peça personalizada.');
    $months = isset($input['warranty_months']) ? $input['warranty_months'] : '';
    if ($months !== '' && (!is_scalar($months) || !preg_match('/^[0-9]{1,3}$/D', (string)$months) || (int)$months > 120)) throw new InvalidArgumentException('A garantia deve ter entre 0 e 120 meses, ou ficar vazia para manter a regra atual.');
    $out['warranty_months'] = $months === '' ? null : (int)$months;
    return $out;
}
function dotbright_repair_settings_load($product)
{
    static $cache = array();
    // Never use reference/name heuristics or settings supplied by a request to decide eligibility.
    if (empty($product['fam_id']) || !dotbright_is_repair_family($product['fam_id']) || empty($product['prod_id'])) return array();
    $id = (int)$product['prod_id'];
    if (!isset($cache[$id])) {
        $row = dotbright_repair_settings_ready() ? dotbright_repair_settings_row('SELECT * FROM repair_product_settings WHERE product_id='.$id) : false;
        try { $cache[$id] = $row ? dotbright_repair_settings_validate($row) : array(); }
        catch (InvalidArgumentException $e) { $cache[$id] = array(); }
    }
    return $cache[$id];
}
function dotbright_repair_settings_save($id, $family, $settings)
{
    if ((int)$id <= 0 || !dotbright_is_repair_family($family)) return false;
    if (!dotbright_repair_settings_ready()) return false;
    $settings = dotbright_repair_settings_validate($settings);
    $columns = array('product_id');
    $values = array((int)$id);
    $updates = array();
    foreach ($settings as $key => $value) {
        $columns[] = $key;
        // Hex literals avoid dependence on the legacy engine's SQL escaping/magic quotes.
        $values[] = $value === null ? 'NULL' : ($value === '' ? "''" : "CONVERT(0x".bin2hex((string)$value)." USING utf8)");
        $updates[] = $key.'=VALUES('.$key.')';
    }
    $link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    try { return (bool)Runquery($link, $GLOBALS['DB_name'], 'INSERT INTO repair_product_settings ('.implode(',', $columns).') VALUES ('.implode(',', $values).') ON DUPLICATE KEY UPDATE '.implode(',', $updates)); }
    catch (Exception $e) { return false; }
}
function dotbright_repair_service_info($product)
{
    $name = isset($product['prod_nome']) ? $product['prod_nome'] : '';
    $info = array('time' => 'Sob consulta', 'part' => 'Tipo de peça sob consulta', 'part_detail' => 'Confirme com a equipa a peça indicada para esta intervenção.', 'part_icon' => 'shield', 'warranty_months' => null);
    if (preg_match('/premium/iu', $name)) {
        $info['part'] = 'Qualidade premium'; $info['part_detail'] = 'Peça compatível de qualidade premium.';
    } elseif (preg_match('/original|oficial/iu', $name)) {
        $takeout = preg_match('/take[ -]?out/iu', $name);
        $info['part'] = $takeout ? 'Peça original take-out' : 'Peça original';
        $info['part_detail'] = $takeout ? 'Peça original retirada de outro equipamento.' : 'Intervenção com a peça original indicada nesta ficha.';
    } elseif (preg_match('/compat.vel/iu', $name)) {
        $info['part'] = 'Peça compatível'; $info['part_detail'] = 'Intervenção com a peça compatível indicada nesta ficha.';
    }
    $settings = dotbright_repair_settings_load($product);
    if (!$settings) return $info;
    $options = dotbright_repair_settings_options();
    if ($settings['time_code'] !== '') $info['time'] = $settings['time_code'] === 'custom' ? $settings['time_custom'] : $options['time_code'][$settings['time_code']];
    if ($settings['part_type'] !== '') {
        $info['part'] = $options['part_type'][$settings['part_type']];
        $details = array('apple' => 'Peça oficial Apple indicada para esta reparação.', 'original' => 'Intervenção com uma peça original.', 'take-out' => 'Peça original retirada de outro equipamento.', 'premium' => 'Peça compatível de qualidade premium.', 'compatible' => 'Intervenção com uma peça compatível.', 'custom' => 'Confirme os detalhes desta peça com a equipa.');
        $info['part_detail'] = $details[$settings['part_type']];
    }
    if ($settings['part_label'] !== '') $info['part'] = $settings['part_label'];
    if ($settings['part_detail'] !== '') $info['part_detail'] = $settings['part_detail'];
    $info['part_icon'] = $settings['part_icon'];
    $info['warranty_months'] = $settings['warranty_months'];
    return $info;
}
