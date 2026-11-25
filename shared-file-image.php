<?php
// Public, read-only image routing. Never accepts arbitrary source URLs.
require_once __DIR__.'/core/product_images.php';
$site_http_url = rtrim(str_replace('\\', '/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/shared-file-image.php')), '/').'/';
$image = isset($_GET['image']) && is_string($_GET['image']) ? $_GET['image'] : '';
if (dotbright_files_image_name($image) === false) { http_response_code(404); exit; }
$name = dotbright_files_image_name($image);
if (!dotbright_shared_files_images()) {
    $file = dotbright_image_file_in(__DIR__.'/files', $name);
    $url = $file === false ? dotbright_files_no_photo_url()
        : $site_http_url.'files/'.implode('/', array_map('rawurlencode', explode('/', $name)));
} else $url = dotbright_files_image_url($image);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public,max-age='.(strpos($url, '/images/no_photo.png') !== false ? '60' : '300'));
header('Location: '.$url, true, 302);
