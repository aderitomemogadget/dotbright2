<?php
// Run: php tests/product_images.php shared|local|remote
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$mode = isset($argv[1]) ? $argv[1] : 'shared';
if (!in_array($mode, array('shared', 'local', 'remote'), true)) exit(2);
$temp = sys_get_temp_dir().'/dotbright-images-'.uniqid();
mkdir($temp.'/dev/files/products', 0755, true);
mkdir($temp.'/source/EE', 0755, true);
$GLOBALS['site_shell_url'] = $temp.'/dev/';
$GLOBALS['site_http_url'] = 'https://dev.example/dotbright2/';
define('SHARED_PRODUCT_IMAGES', $mode !== 'local');
define('SHARED_FILES_IMAGES', false);
define('PRODUCT_IMAGES_SOURCE_PATH', $mode === 'remote' ? '' : $temp.'/source');
define('PRODUCT_IMAGES_SOURCE_URL', 'https://www.dotbright.pt/files/products/');
define('IMG_FOLDER', 'files');
define('PROJETO', 'dotbright');
require dirname(__DIR__).'/core/product_images.php';

function check($condition, $label) {
    if (!$condition) throw new RuntimeException($label);
    echo "PASS $label\n";
}
function cleanup_fixture($dir) {
    foreach (scandir($dir) as $name) {
        if ($name === '.' || $name === '..') continue;
        $path = $dir.'/'.$name;
        if (is_link($path) || !is_dir($path)) unlink($path); else cleanup_fixture($path);
    }
    rmdir($dir);
}
// Execute the actual legacy image-selection blocks, not a copy of their logic.
// These previously discarded shared images during the final local-only check.
function check_legacy_image_selection($missing = false) {
    $blocks = 0;
    foreach (array('core/produtos.php', 'layout/dotbright/code/produto.php', 'ajax/ajax_img.php') as $file) {
        $source = file_get_contents(dirname(__DIR__).'/'.$file);
        $offset = 0;
        while (($start = strpos($source, "\$img_file_small='files/products/'", $offset)) !== false) {
            $end = strpos($source, 'if($ext)', $start);
            if ($end === false) throw new RuntimeException('Image-selection block not found: '.$file);
            $block = substr($source, $start, $end - $start);
            $prod_id = 143580; $ordem = 1; $ext = 'png'; $webp = 0;
            $img_file = 'files/products/143580_1.png';
            $i_small = $i_thum = $i_prod = array('ativo'=>0, 'w'=>50, 'h'=>50, 'crop'=>array(), 'm_trans'=>0, 'c_white'=>0);
            eval($block);
            $url = dotbright_image_url($img_file_thum);
            check($missing ? strpos($url, '/images/no_photo.png') !== false
                : strpos($url, PRODUCT_IMAGES_SOURCE_URL.'143580_1_thum.png?dp=') === 0,
                $file.' block '.(++$blocks).($missing ? ' keeps missing-image fallback' : ' renders shared thumbnail without a local copy'));
            $offset = $end + strlen('if($ext)');
        }
    }
    check($blocks === 5, 'cover all five legacy image-selection blocks');
}
function check_search_image_selection($missing = false) {
    $source = file_get_contents(dirname(__DIR__).'/ajax/ajax_search_resultados.php');
    if (!function_exists('getProductThumb')) {
        $functionStart = strpos($source, 'function getProductThumb(');
        if ($functionStart === false) throw new RuntimeException('Search thumbnail resolver not found');
        eval(substr($source, $functionStart));
    }
    $start = strpos($source, '$img = getProductThumb($prodId);');
    $end = strpos($source, '$url = seo(', $start);
    if ($start === false || $end === false) throw new RuntimeException('Search image renderer not found');
    $prodId = $missing ? 999999999999 : 143580;
    $prodNome = 'Phone & "example"';
    eval(substr($source, $start, $end - $start));
    $expected = $missing ? $GLOBALS['site_http_url'].'layout/dotbright/images/no_photo.png'
        : (dotbright_shared_images() ? PRODUCT_IMAGES_SOURCE_URL : $GLOBALS['site_http_url'].'files/products/').'143580_1';
    check(strpos($imagem, '<img src="'.$expected) === 0,
        $missing ? 'search renders no-photo fallback' : 'search renders resolved image URL exactly once');
    check(strpos($imagem, 'alt="Phone &amp; &quot;example&quot;"') !== false, 'search escapes image attributes');
}
function db_opendb_conection($host, $user, $password) { return null; }
function Runquery($link, $database, $sql) { throw new RuntimeException('Unexpected database write: '.$sql); }
class DataObj {
    public $db_data;
    private $rows = array();
    private $position = 0;
    function db_open($link, $database, $sql) {
        $this->rows = $GLOBALS['fixtureRows']; $this->position = 0;
        $this->db_data = isset($this->rows[0]) ? $this->rows[0] : array();
    }
    function EOF() { return $this->position >= count($this->rows); }
    function db_next() { $this->position++; $this->db_data = isset($this->rows[$this->position]) ? $this->rows[$this->position] : array(); }
    function db_close() {}
}
try {
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a5l8AAAAASUVORK5CYII=');
    file_put_contents($temp.'/source/143580_1.png', $png);
    file_put_contents($temp.'/source/143580_1_small.png', $png);
    file_put_contents($temp.'/source/143580_1_thum.png', $png);
    file_put_contents($temp.'/source/EE/label (1).png', $png);
    $before = hash_file('sha256', $temp.'/source/143580_1.png');
    check(dotbright_image_name('files/products/../secret.png') === false, 'reject traversal');
    check(dotbright_image_name('files/products/%2e%2e/secret.png') === false, 'reject encoded traversal');
    check(dotbright_image_name('files/products/%252e%252e/secret.png') === false, 'reject double encoding');
    check(dotbright_image_name('files/products/x%00.png') === false, 'reject NUL');
    check(dotbright_image_name('https://elsewhere.example/files/products/a.png') === false, 'reject foreign URL');
    check(dotbright_image_url('layout/dotbright/images/no_photo.png') === 'layout/dotbright/images/no_photo.png', 'preserve existing fallback');
    check(dotbright_image_url('files/products_families/1.png') === 'files/products_families/1.png', 'leave non-product assets alone');
    if ($mode === 'remote') {
        $asset = dotbright_image_asset('files/products/143580_1_thum.png');
        check($asset !== false && $asset['shared'] && $asset['file'] === false, 'read real catalogue thumbnail over HTTPS');
        check($asset['mtime'] === 1794105012, 'preserve real Last-Modified cache version');
        check(dotbright_image_url('files/products/143580_1_thum.png?dp=1&refv=2') === PRODUCT_IMAGES_SOURCE_URL.'143580_1_thum.png?dp=1794105012&refv=2', 'public HTTPS URL and query parameters');
        check(!dotbright_image_exists('files/products/999999999999999_9999999.png'), 'real missing image');
        check(strpos(dotbright_image_url('files/products/999999999999999_9999999.png'), 'no_photo.png') !== false, 'missing image fallback');
        check(count(glob($temp.'/dev/files/products/*')) === 0, 'no image copies in DEV');
        $bytes = file_get_contents(PRODUCT_IMAGES_SOURCE_URL.'143580_1_thum.png?dp=1794105012');
        $size = getimagesizefromstring($bytes);
        check($size[0] === 200 && $size[1] === 200, 'real thumbnail dimensions preserved');
        check(hash('sha256', $bytes) === 'bf27f8f75d143209326db7b3726eb117dc4a36329c0c6031bb6828e40bc729ef', 'real production sample unchanged');
    } elseif ($mode === 'shared') {
        check(dotbright_image_exists($temp.'/dev/../dev/files/products/143580_1.png'), 'physical shared image through legacy path');
        check(strpos(dotbright_image_url('files/products/EE/label (1).png'), '/EE/label%20%281%29.png?dp=') !== false, 'nested paths and filename encoding');
        check(!dotbright_image_exists('files/products/../../source/143580_1.png'), 'invalid product paths fail closed');
        check(strpos(dotbright_product_images_cache_key(), 'shared_') === 0, 'separate shared product cache');
        $mapped = dotbright_product_image_fields(array('prd_imgm'=>'files/products/143580_1.png', 'prd_imgs'=>array('img_cart'=>array('files/products/143580_1_small.png')), 'text'=>'files/products/143580_1.png'));
        check(strpos($mapped['prd_imgm'], PRODUCT_IMAGES_SOURCE_URL) === 0 && strpos($mapped['prd_imgs']['img_cart'][0], PRODUCT_IMAGES_SOURCE_URL) === 0, 'main, gallery and cart image fields');
        check($mapped['text'] === 'files/products/143580_1.png', 'preserve other product data');
        $legacy = dotbright_product_image_fields(array('prd_imgm'=>array('files/products/143580_1.png')));
        check(strpos($legacy['prd_imgm'][0], PRODUCT_IMAGES_SOURCE_URL) === 0, 'legacy image lists');
        require dirname(__DIR__).'/core/gd.php';
        check(geraimagens('files/products/143580_1.png', 'files/products/143580_1_thum.png', 250, 200) === false, 'shared thumbnail generation cannot write to source');
        check(!file_exists($temp.'/dev/files/products/143580_1_thum.png'), 'no unnecessary derivative copies');
        file_put_contents($temp.'/dev/files/products/143580_1.png', $png.'DEV');
        check(dotbright_image_asset('files/products/143580_1.png')['shared'] === false, 'DEV upload overrides only DEV');
        unlink($temp.'/dev/files/products/143580_1.png');
        check(dotbright_image_asset('files/products/143580_1.png')['shared'] === true, 'DEV delete leaves shared source intact');
        foreach (array('DB_host', 'DB_user', 'DB_pass', 'DB_name') as $key) $GLOBALS[$key] = '';
        require dirname(__DIR__).'/core/produtos.php';
        $GLOBALS['fixtureRows'] = array(array('img_ext'=>'jpg', 'Ordem'=>1, 'URL_Source'=>''));
        check(get_product_images_first(143580)[0]['img_ext'] === 'png', 'extension correction without DB update');
        check(get_product_images(143580)[0]['img_ext'] === 'png', 'gallery extension correction without DB update');
        $GLOBALS['fixtureRows'] = array();
        check(get_product_images(143580)[0]['img_ext'] === 'png', 'filesystem discovery without DB insert');
        check_search_image_selection();
        check_search_image_selection(true);
        check_legacy_image_selection();
        unlink($temp.'/source/143580_1_thum.png');
        clearstatcache();
        check_legacy_image_selection(true);
    } else {
        check(!dotbright_image_exists($temp.'/dev/files/products/143580_1.png'), 'disabled configuration ignores shared source');
        file_put_contents($temp.'/dev/files/products/143580_1.png', $png);
        check(dotbright_image_exists($temp.'/dev/files/products/143580_1.png'), 'disabled local existence');
        check(dotbright_image_url('files/products/143580_1.png?dp=7') === 'files/products/143580_1.png?dp=7', 'disabled URL unchanged');
        check(dotbright_product_images_cache_key() === '', 'disabled legacy cache namespace');
        check_search_image_selection();
        check_search_image_selection(true);
    }
    check(hash_file('sha256', $temp.'/source/143580_1.png') === $before, 'source fixture byte-for-byte unchanged');
} finally {
    cleanup_fixture($temp);
}
