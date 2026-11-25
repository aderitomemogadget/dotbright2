<?php
// php tests/files_images.php shared|local|remote
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$mode = isset($argv[1]) ? $argv[1] : 'shared';
if (!in_array($mode, array('shared','local','remote'), true)) exit(2);
$root = sys_get_temp_dir().'/dotbright-files-images-'.uniqid();
mkdir($root.'/dev/files', 0755, true);
mkdir($root.'/source/menus', 0755, true);
mkdir($root.'/source/documentos', 0755, true);
mkdir($root.'/source/products', 0755, true);
$GLOBALS['site_shell_url'] = $root.'/dev/';
$GLOBALS['site_http_url'] = 'https://dev.example/dotbright2/';
$_SERVER['SCRIPT_FILENAME'] = $root.'/dev/index.php';
define('SHARED_FILES_IMAGES', $mode !== 'local');
define('FILES_IMAGES_SOURCE_PATH', $mode === 'remote' ? '' : $root.'/source');
define('FILES_IMAGES_SOURCE_URL', 'https://www.dotbright.pt/files/');
define('SHARED_PRODUCT_IMAGES', $mode !== 'local');
define('PRODUCT_IMAGES_SOURCE_PATH', $root.'/source/products');
define('PRODUCT_IMAGES_SOURCE_URL', 'https://www.dotbright.pt/files/products/');
define('PROJETO', 'dotbright');
define('SITE_SHELL_URL', $root.'/dev/');
require dirname(__DIR__).'/core/product_images.php';
function check_files($condition, $label) {
    if (!$condition) throw new RuntimeException($label);
    echo 'PASS '.$label."\n";
}
function remove_files_fixture($dir) {
    foreach (scandir($dir) as $name) {
        if ($name === '.' || $name === '..') continue;
        $path = $dir.'/'.$name;
        if (is_link($path) || !is_dir($path)) unlink($path); else remove_files_fixture($path);
    }
    rmdir($dir);
}
function check_menu_image_caches($shared) {
    $id_pai = 1; $ID_ORG = 0; $id = 2; $responsive = 0;
    $GLOBALS['site_lang'] = 'pt'; $_GET['cat'] = 0; $count = 0;
    foreach (array('core/menus.php', 'layout/dotbright/code/menu.php') as $file) {
        $source = '';
        foreach (token_get_all(file_get_contents(dirname(__DIR__).'/'.$file)) as $token) {
            $source .= is_array($token) ? (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT), true) ? "\n" : $token[1]) : $token;
        }
        preg_match_all('~^\s*\$cache_filename\s*=\s*[^\r\n]+~m', $source, $matches);
        foreach ($matches[0] as $statement) {
            eval($statement);
            $hasSharedKey = strpos($cache_filename, 'shared_') !== false;
            check_files($hasSharedKey === $shared, $file.' cache '.(++$count).($shared
                ? ' cannot reuse HTML cached without shared images' : ' keeps original local cache name'));
            check_files(strpos($cache_filename, SITE_SHELL_URL.'files/tmp/') === 0, 'menu cache write path stays in DEV');
        }
    }
    check_files($count === 6, 'cover all six menu HTML caches');
}
try {
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a5l8AAAAASUVORK5CYII=');
    file_put_contents($root.'/source/menus/logo.png', $png);
    file_put_contents($root.'/source/menus/icon.svg', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10"><path id="symbol" d="M0 0h10v10z"/></svg>');
    file_put_contents($root.'/source/documentos/banner (1).png', $png);
    file_put_contents($root.'/source/documentos/private.pdf', 'fixture PDF');
    $hash = hash_file('sha256', $root.'/source/menus/logo.png');
    foreach (array('files/menus/../secret.png','files/%2e%2e/secret.png','files/%252e%252e/secret.png',
        'files/menus/bad%00.png','files/menus//logo.png','https://foreign.example/files/menus/logo.png',
        'files/documentos/private.pdf','files/classes.css','files/settings.php','files/script.js') as $bad) {
        check_files(dotbright_files_image_name($bad) === false, 'reject '.$bad);
    }
    foreach (array('files/menus/logo.png','/files/menus/logo.png','../../files/menus/logo.png',
        '/dotbright2/files/menus/logo.png','https://dev.example/dotbright2/files/menus/logo.png',
        $root.'/dev/../dev/files/menus/logo.png') as $path) {
        check_files(dotbright_files_image_name($path) === 'menus/logo.png', 'recognise '.$path);
    }
    check_files(dotbright_files_image_url('files/documentos/private.pdf') === 'files/documentos/private.pdf', 'leave PDF URL unchanged');
    check_files(dotbright_files_image_url('files/classes.css') === 'files/classes.css', 'leave CSS URL unchanged');
    if ($mode === 'shared') {
        check_menu_image_caches(true);
        check_files(dotbright_image_exists('files/menus/logo.png'), 'legacy image check reads shared source');
        check_files(dotbright_image_file_exists($root.'/dev/files/menus/logo.png'), 'absolute image check reads shared source');
        check_files(dotbright_image_resolve('files/menus/logo.png'), 'legacy include-path image check');
        check_files(dotbright_image_mtime('files/menus/logo.png') === filemtime($root.'/source/menus/logo.png'), 'shared cache timestamp');
        check_files(dotbright_image_exists('files/documentos/private.pdf') === false, 'PDF existence remains local');
        check_files(!dotbright_image_exists('files/menus/../secret.png'), 'invalid image reads fail closed');
        $url = dotbright_files_image_url('files/menus/logo.png?d=old&dp=1');
        check_files(strpos($url, FILES_IMAGES_SOURCE_URL.'menus/logo.png?d=old&dp=') === 0, 'public source URL and cache query');
        check_files(strpos(dotbright_files_image_url('files/documentos/banner%20%281%29.png'), '/banner%20%281%29.png?dp=') !== false, 'encoded names');
        check_files(strpos(dotbright_files_image_url('files/menus/icon.svg#symbol'), '#symbol') !== false, 'SVG fragments');
        check_files(dotbright_files_image_size('files/menus/logo.png')[0] === 1, 'shared physical image dimensions');
        $html = '<!DOCTYPE html><html><head><style>.banner{background:url(files/menus/logo.png)}@font-face{src:url(files/font.woff)}</style></head><body>'
            .'<img src="files/menus/logo.png" data-src="files/menus/icon.svg" srcset="files/menus/logo.png 1x, files/menus/logo.png 2x">'
            .'<picture><source srcset="files/menus/logo.png 300w"></picture><video poster="files/menus/logo.png" src="files/video.mp4"></video>'
            .'<div style="background-image:url(\'files/menus/logo.png\')"></div><a href="files/documentos/private.pdf">PDF</a>'
            .'<script>var untouched="<img src=\'files/menus/logo.png\'>";</script><!-- <img src="files/menus/logo.png"> -->'
            .'<span>files/menus/logo.png</span></body></html>';
        $resolved = dotbright_files_images_html($html);
        check_files(strpos($resolved, '<img src="'.FILES_IMAGES_SOURCE_URL.'menus/logo.png?dp=') !== false, 'HTML image src');
        check_files(strpos($resolved, 'data-src="'.FILES_IMAGES_SOURCE_URL.'menus/icon.svg?dp=') !== false, 'lazy image attributes');
        check_files(strpos($resolved, ' 1x, '.FILES_IMAGES_SOURCE_URL.'menus/logo.png?dp=') !== false, 'responsive srcset candidates');
        check_files(strpos($resolved, 'poster="'.FILES_IMAGES_SOURCE_URL.'menus/logo.png?dp=') !== false, 'video poster images');
        check_files(strpos($resolved, 'src="files/video.mp4"') !== false, 'leave video source unchanged');
        check_files(strpos($resolved, 'background:url("'.FILES_IMAGES_SOURCE_URL.'menus/logo.png?dp=') !== false, 'CSS image URLs');
        check_files(strpos($resolved, 'href="files/documentos/private.pdf"') !== false, 'leave document links unchanged');
        check_files(strpos($resolved, 'src:url(files/font.woff)') !== false, 'leave fonts unchanged');
        check_files(strpos($resolved, '<script>var untouched="<img src=\'files/menus/logo.png\'>";</script>') !== false, 'leave JavaScript strings unchanged');
        check_files(strpos($resolved, '<!-- <img src="files/menus/logo.png"> -->') !== false, 'leave comments unchanged');
        check_files(strpos($resolved, '<span>files/menus/logo.png</span>') !== false, 'leave ordinary text unchanged');
        $json = json_encode(array('image'=>'files/menus/logo.png', 'html'=>$html));
        check_files(dotbright_files_images_response($json) === $json, 'JSON responses unchanged');
        check_files(dotbright_files_image_url('files/menus/missing.png') === dotbright_files_no_photo_url(), 'missing image fallback');
        mkdir($root.'/dev/files/menus'); file_put_contents($root.'/dev/files/menus/logo.png', $png.'DEV');
        check_files(!dotbright_files_image_asset('files/menus/logo.png')['shared'], 'DEV uploads override source');
        unlink($root.'/dev/files/menus/logo.png'); clearstatcache();
        check_files(dotbright_files_image_asset('files/menus/logo.png')['shared'], 'DEV deletion leaves source intact');
        require dirname(__DIR__).'/core/gd.php';
        check_files(geraimagens('files/menus/logo.png','files/menus/logo_small.png',50,50) === false, 'do not generate source thumbnails');
        check_files(redimencionaImagem($root.'/dev/','files/menus/logo.png',50,50,'files/menus/logo_small.png') === false, 'legacy resizing stays local');
        check_files(!file_exists($root.'/source/menus/logo_small.png'), 'no derivatives in source');
        $beforeScript = $_SERVER['SCRIPT_FILENAME'];
        $_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__).'/admin/products/upload.php';
        check_files(!dotbright_shared_files_images(), 'backoffice keeps local behaviour');
        $_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__).'/core/crons/import.php';
        check_files(!dotbright_shared_files_images(), 'CRONs keep local behaviour');
        $_SERVER['SCRIPT_FILENAME'] = $beforeScript;
        check_files(strpos(dotbright_files_images_browser_script(), 'shared-files-images.js') !== false, 'browser support is registered');
    } elseif ($mode === 'local') {
        check_menu_image_caches(false);
        check_files(!dotbright_image_exists($root.'/dev/files/menus/logo.png'), 'disabled ignores shared source');
        check_files(dotbright_files_image_url('files/menus/logo.png?dp=2') === 'files/menus/logo.png?dp=2', 'disabled URL unchanged');
        check_files(dotbright_files_images_html('<img src="files/menus/logo.png">') === '<img src="files/menus/logo.png">', 'disabled HTML unchanged');
        check_files(dotbright_files_images_browser_script() === '', 'disabled browser support');
    } else {
        foreach (array('menus/222_1.svg','icons/favicon-32x32.png','documentos/2026030318594636396137326661326362313436.jpg') as $name) {
            $asset = dotbright_files_image_asset('files/'.$name);
            check_files($asset !== false && $asset['shared'] && $asset['file'] === false, 'real production image '.$name);
            check_files(strpos(dotbright_files_image_url('files/'.$name), FILES_IMAGES_SOURCE_URL.$name) === 0, 'real public URL '.$name);
        }
        check_files(!dotbright_files_image_asset('files/menus/dotbright-missing-999999999999.png'), 'real missing image');
    }
    check_files(hash_file('sha256', $root.'/source/menus/logo.png') === $hash, 'source bytes unchanged');
    check_files(count(glob($root.'/dev/files/menus/*') ?: array()) === 0, 'no physical image copies in DEV');
} finally {
    remove_files_fixture($root);
}
