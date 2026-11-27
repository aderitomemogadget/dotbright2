<?php
// Category catalogue headers are independent of category thumbnails and SEO images.
function dotbright_catalog_header_escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function dotbright_catalog_header_sql($db, $value) {
    if ($db instanceof PDO) return $db->quote((string)$value);
    if ($db instanceof mysqli) return "'".mysqli_real_escape_string($db, (string)$value)."'";
    return str2SQLstr((string)$value);
}
function dotbright_catalog_banner_valid_name($name) {
    return preg_match('/^\d+_catalogo_[a-zA-Z0-9_-]+_[a-f0-9]{16}\.(jpg|png|webp|gif)$/D', (string)$name) === 1;
}
function dotbright_catalog_banner_dir() {
    return dirname(__DIR__).'/files/products_families/';
}
function dotbright_catalog_banner_validate($file) {
    if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return '';
    if ($file['error'] !== UPLOAD_ERR_OK) throw new InvalidArgumentException('Não foi possível receber o banner. Tente novamente.');
    if (!is_file($file['tmp_name']) || filesize($file['tmp_name']) > 8 * 1024 * 1024) throw new InvalidArgumentException('O banner deve ter no máximo 8 MB.');
    $info = @getimagesize($file['tmp_name']);
    $types = array('image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp', 'image/gif'=>'gif');
    if (!$info || !isset($types[$info['mime']])) throw new InvalidArgumentException('Escolha uma imagem JPG, PNG, WebP ou GIF válida.');
    return $types[$info['mime']];
}
function dotbright_catalog_header_validate($post, $files) {
    foreach (array('catalogo_titulo'=>255, 'catalogo_texto'=>10000) as $field=>$limit) {
        if (isset($post[$field]) && (!is_string($post[$field]) || preg_match('/^.{0,'.$limit.'}$/us', $post[$field]) !== 1)) {
            throw new InvalidArgumentException($field === 'catalogo_titulo' ? 'O título deve ter no máximo 255 caracteres.' : 'O texto deve ter no máximo 10000 caracteres.');
        }
    }
    return dotbright_catalog_banner_validate(isset($files['catalogo_banner']) ? $files['catalogo_banner'] : null);
}
function dotbright_catalog_banner_url($name) {
    if (!dotbright_catalog_banner_valid_name($name)) return '';
    // The BO lives under /admin/products/: image helpers can return their input
    // unchanged for local non-product images, so start with a site-root URL.
    $absolute = rtrim($GLOBALS['site_http_url'], '/').'/files/products_families/'.$name;
    $url = function_exists('dotbright_image_url') ? dotbright_image_url($absolute) : $absolute;
    $path = dotbright_catalog_banner_dir().$name;
    if (is_file($path)) $url .= (strpos($url, '?') === false ? '?' : '&').'v='.filemtime($path);
    return $url;
}
function dotbright_save_catalog_header($id, $lang, $post, $files) {
    if (empty($post['catalogo_header_present'])) return true;
    $ext = dotbright_catalog_header_validate($post, $files);
    $id = (int)$id;
    $db = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $langSql = dotbright_catalog_header_sql($db, $lang);
    $rs = new DataObj;
    $rs->db_open($db, $GLOBALS['DB_name'], 'SELECT * FROM products_families_lang WHERE ID_PAI='.$id.' AND Lang='.$langSql);
    if ($rs->EOF()) { $rs->db_close(); return false; }
    $old = isset($rs->db_data['catalogo_banner']) ? $rs->db_data['catalogo_banner'] : '';
    $rs->db_close();
    $name = !empty($post['catalogo_banner_apagar']) ? '' : $old;
    $file = isset($files['catalogo_banner']) ? $files['catalogo_banner'] : null;
    $newPath = '';
    if ($ext !== '') {
        $safeLang = preg_replace('/[^a-zA-Z0-9_-]/', '_', $lang);
        $name = $id.'_catalogo_'.$safeLang.'_'.substr(hash('sha256', uniqid('', true).mt_rand()), 0, 16).'.'.$ext;
        $dir = dotbright_catalog_banner_dir();
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) return false;
        $newPath = $dir.$name;
        if (!move_uploaded_file($file['tmp_name'], $newPath)) return false;
    }
    $title = dotbright_catalog_header_sql($db, trim(isset($post['catalogo_titulo']) ? $post['catalogo_titulo'] : ''));
    $text = dotbright_catalog_header_sql($db, trim(isset($post['catalogo_texto']) ? $post['catalogo_texto'] : ''));
    $result = Runquery($db, $GLOBALS['DB_name'], 'UPDATE products_families_lang SET catalogo_titulo='.$title.', catalogo_texto='.$text.', catalogo_banner='.dotbright_catalog_header_sql($db, $name).' WHERE ID_PAI='.$id.' AND Lang='.$langSql);
    if (!$result) { if ($newPath !== '' && is_file($newPath)) unlink($newPath); return false; }
    // Remove only a superseded banner owned by this exact category and language.
    $prefix = $id.'_catalogo_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $lang).'_';
    if ($old !== $name && dotbright_catalog_banner_valid_name($old) && strpos($old, $prefix) === 0 && is_file(dotbright_catalog_banner_dir().$old)) unlink(dotbright_catalog_banner_dir().$old);
    return true;
}
function dotbright_catalog_header($id, $lang) {
    $id = (int)$id;
    if ($id <= 0) return '';
    $db = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $rs = new DataObj;
    $rs->db_open($db, $GLOBALS['DB_name'], 'SELECT PL.* FROM products_families P INNER JOIN products_families_lang PL ON PL.ID_PAI=P.ID WHERE P.ID='.$id.' AND P.visivel=1 AND PL.Lang='.dotbright_catalog_header_sql($db, $lang));
    $row = $rs->EOF() ? array() : $rs->db_data;
    $rs->db_close();
    return dotbright_render_catalog_header($row);
}
function dotbright_render_catalog_header($row) {
    $title = isset($row['catalogo_titulo']) ? trim($row['catalogo_titulo']) : '';
    $text = isset($row['catalogo_texto']) ? trim($row['catalogo_texto']) : '';
    $image = isset($row['catalogo_banner']) ? dotbright_catalog_banner_url($row['catalogo_banner']) : '';
    if ($title === '' && $text === '' && $image === '') return '';
    if ($title === '') $title = !empty($row['Cabecalho2']) ? $row['Cabecalho2'] : (isset($row['Cabecalho']) ? $row['Cabecalho'] : '');
    $html = '<section class="db-catalog-header">';
    if ($image !== '') $html .= '<img class="db-catalog-header-image" src="'.dotbright_catalog_header_escape($image).'" alt="">';
    if ($title !== '' || $text !== '') {
        $html .= '<div class="db-catalog-header-content">';
        if ($title !== '') $html .= '<h1>'.dotbright_catalog_header_escape($title).'</h1>';
        if ($text !== '') $html .= '<div class="db-catalog-header-text">'.nl2br(dotbright_catalog_header_escape($text)).'</div>';
        $html .= '</div>';
    }
    return $html.'</section>';
}
