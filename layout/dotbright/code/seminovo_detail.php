<?php
// Presentation only: reuse the existing product data, markup and purchase actions.
function dotbright_seminovo_detail_data($product)
{
    if ((int)($product['fam_id'] ?? 0) !== 163) return null;
    static $benefits = null;
    if ($benefits === null) $benefits = require dirname(__DIR__, 3).'/conf/seminovo_presentation.php';
    $text = function ($value) { return trim(html_entity_decode(strip_tags((string)$value), ENT_QUOTES, 'UTF-8')); };
    $battery = $text($product['prod_bateria'] ?? '');
    $battery = preg_match('/^\s*(\d{1,3})(?:\s*%)?\s*$/', $battery, $match) && (int)$match[1] <= 100 ? (int)$match[1] : null;
    // Official definitions already used by ficha_p1_final; no inferred grading.
    $conditions = array(
        'A+'=>array('name'=>'Como novo', 'description'=>'O telefone encontra-se igual a novo, sem marcas ou riscos.'),
        'A'=>array('name'=>'Muito bom', 'description'=>'O telefone pode apresentar marcas ligeiras, pouco visíveis, tais como pequenos riscos, no entanto nunca marcas muito perceptíveis.'),
        'B'=>array('name'=>'Bom', 'description'=>'Apresenta marcas visíveis, tais como riscos, amolgadelas e outras marcas.')
    );
    return array('id'=>(int)($product['prod_id'] ?? 0), 'name'=>$text($product['prod_nome'] ?? ''),
        'grade'=>$text($product['prod_estado'] ?? ''), 'battery'=>$battery, 'conditions'=>$conditions,
        'warranty'=>$text($product['prod_garantia'] ?? ''), 'delivery'=>$text($product['prod_tempo_entrega'] ?? ''),
        // Texto4/prod_envio is used as the technical sheet by this storefront.
        'shipping'=>$text($product['prod_condicoes_entrega'] ?? ''), 'location'=>$text($product['prod_localizacao_fisica'] ?? ''),
        'stock'=>(float)($product['prod_precos']['STOCK'] ?? 0), 'referenceImage'=>!empty($product['seminovo_reference_image']), 'benefits'=>$benefits);
}

function dotbright_seminovo_detail($product, $html)
{
    $data = dotbright_seminovo_detail_data($product);
    if ($data === null) return $html;
    $base = isset($GLOBALS['site_http_url']) ? $GLOBALS['site_http_url'] : '';
    $asset = function ($relative) use ($base) {
        $file = dirname(__DIR__, 3).'/'.$relative;
        return htmlspecialchars($base.$relative.'?v='.(is_file($file) ? filemtime($file) : '1'), ENT_QUOTES, 'UTF-8');
    };
    return '<link rel="stylesheet" href="'.$asset('layout/dotbright/css/catalog-cards.css').'">'
        .'<link rel="stylesheet" href="'.$asset('layout/dotbright/css/seminovo-product.css').'">'
        .'<section class="db-seminovo-detail" data-product-id="'.$data['id'].'">'.$html
        .'<script type="application/json" class="db-semi-data">'.json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE).'</script></section>'
        .'<script defer src="'.$asset('layout/dotbright/js/bloco_produto.js').'"></script>'
        .'<script defer src="'.$asset('layout/dotbright/js/seminovo-product.js').'"></script>';
}

function dotbright_seminovo_related_data($card, $product)
{
    $data = dotbright_seminovo_detail_data($product);
    if ($data !== null) {
        if ($data['grade'] !== '') $card['title'] .= ' GRAU: '.$data['grade'];
        if ($data['battery'] !== null) $card['title'] .= ' BAT: '.$data['battery'].'%';
    }
    $card['fl'] = 0;
    return htmlspecialchars(json_encode($card, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}
