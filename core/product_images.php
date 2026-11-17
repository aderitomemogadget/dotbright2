<?php
require_once dirname(__DIR__).'/conf/product_images.php';

function dotbright_shared_images()
{
    return SHARED_PRODUCT_IMAGES === true;
}

function dotbright_image_root()
{
    return rtrim(str_replace('\\', '/', isset($GLOBALS['site_shell_url']) ? $GLOBALS['site_shell_url'] : dirname(__DIR__)), '/').'/';
}

// Recognise only product assets belonging to this application, never arbitrary URLs.
function dotbright_image_relative_input($input)
{
    $value = str_replace('\\', '/', (string)$input);
    foreach (array(dotbright_image_root(), isset($GLOBALS['site_http_url']) ? $GLOBALS['site_http_url'] : '') as $prefix) {
        if ($prefix !== '' && strpos($value, $prefix.'files/products/') === 0) return substr($value, strlen($prefix));
    }
    if (strpos($value, 'files/products/') === 0) return $value;
    $offset = strpos($value, '/files/products/');
    $root = realpath(dotbright_image_root());
    if ($root !== false && $offset !== false && realpath(substr($value, 0, $offset)) === $root) return substr($value, $offset + 1);
    return false;
}

function dotbright_image_name($input)
{
    $value = dotbright_image_relative_input($input);
    if ($value === false) return false;
    $value = rawurldecode(explode('?', substr($value, 15), 2)[0]);
    if ($value === '' || preg_match('~[\x00-\x1f\x7f%\\\\?#:]~', $value)) return false;
    foreach (explode('/', $value) as $part) if ($part === '' || $part === '.' || $part === '..') return false;
    if (!preg_match('~\.(?:jpe?g|png|webp|gif|avif|pdf)$~iD', $value)) return false;
    return $value;
}

function dotbright_image_reference($input)
{
    return dotbright_image_relative_input($input) !== false;
}

// realpath containment prevents symlinks from escaping either authorised tree.
function dotbright_image_file_in($root, $name)
{
    if ($root === '') return false;
    $base = realpath($root);
    if ($base === false || !is_dir($base) || is_link(rtrim($root, '/\\'))) return false;
    $file = realpath($base.DIRECTORY_SEPARATOR.$name);
    if ($file === false || strpos($file, $base.DIRECTORY_SEPARATOR) !== 0 || !is_file($file) || !is_readable($file)) return false;
    return $file;
}

function dotbright_image_source_url($name)
{
    $url = PRODUCT_IMAGES_SOURCE_URL;
    $parts = parse_url($url);
    if (!$parts || !isset($parts['scheme'], $parts['host']) || $parts['scheme'] !== 'https'
        || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) return '';
    return rtrim($url, '/').'/'.implode('/', array_map('rawurlencode', explode('/', $name)));
}

// HTTPS metadata only: no image downloads, DB access, or production writes.
// Disk and request caches avoid repeated HEAD requests from legacy existence/mtime checks.
function dotbright_image_remote_metadata($name)
{
    static $memo = array();
    $url = dotbright_image_source_url($name);
    if ($url === '') return false;
    if (array_key_exists($url, $memo)) return $memo[$url];
    $cacheDir = dotbright_image_cache_dir();
    $cacheFile = $cacheDir.hash('sha256', $url).'.json';
    $cached = $cacheDir !== false && !is_link($cacheFile) && is_file($cacheFile) ? json_decode((string)@file_get_contents($cacheFile), true) : null;
    if (is_array($cached) && isset($cached['expires']) && $cached['expires'] > time()) {
        return $memo[$url] = !empty($cached['exists']) ? $cached : false;
    }
    $status = 0; $mtime = 0; $type = '';
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, array(CURLOPT_NOBODY => true, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_FILETIME => true));
        curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $mtime = max(0, (int)curl_getinfo($curl, CURLINFO_FILETIME));
        $type = (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);
    } else {
        $context = stream_context_create(array('http' => array('method' => 'HEAD', 'timeout' => 4,
            'follow_location' => 0, 'ignore_errors' => true), 'ssl' => array('verify_peer' => true, 'verify_peer_name' => true)));
        $headers = @get_headers($url, true, $context);
        if (is_array($headers)) {
            preg_match('~\s([0-9]{3})\s~', $headers[0], $match);
            $status = isset($match[1]) ? (int)$match[1] : 0;
            foreach ($headers as $key => $value) {
                if (is_array($value)) $value = end($value);
                if (strtolower((string)$key) === 'last-modified') $mtime = max(0, (int)strtotime($value));
                if (strtolower((string)$key) === 'content-type') $type = $value;
            }
        }
    }
    $exists = $status === 200 && (strpos($type, 'image/') === 0 || strpos($type, 'application/pdf') === 0);
    // On transient errors use an earlier successful result, never poison it with a timeout.
    if (!$exists && $status !== 404 && $status !== 410 && is_array($cached) && !empty($cached['exists'])) {
        $cached['expires'] = time() + 60;
        $data = $cached;
    } else {
        $data = array('exists' => $exists, 'mtime' => $mtime, 'expires' => time() + ($exists ? 21600 : 60));
    }
    if ($cacheDir !== false && !is_link($cacheFile)) {
        @file_put_contents($cacheFile, json_encode($data), LOCK_EX);
    }
    return $memo[$url] = !empty($data['exists']) ? $data : false;
}

function dotbright_image_cache_dir()
{
    $dev = realpath(dotbright_image_root());
    if ($dev === false) return false;
    $dir = $dev.DIRECTORY_SEPARATOR.'files';
    foreach (array('', 'cache', 'product-images') as $part) {
        if ($part !== '') $dir .= DIRECTORY_SEPARATOR.$part;
        if (is_link($dir)) return false;
        if (!is_dir($dir) && !@mkdir($dir, 0755)) return false;
        $real = realpath($dir);
        if ($real === false || strpos($real, $dev.DIRECTORY_SEPARATOR) !== 0) return false;
    }
    return $dir.DIRECTORY_SEPARATOR;
}

function dotbright_image_asset($input)
{
    $name = dotbright_image_name($input);
    if ($name === false) return false;
    $local = dotbright_image_file_in(dotbright_image_root().'files/products', $name);
    if ($local !== false) return array('file' => $local, 'shared' => false, 'mtime' => filemtime($local));
    if (!dotbright_shared_images()) return false;
    if (PRODUCT_IMAGES_SOURCE_PATH !== '') {
        $file = dotbright_image_file_in(PRODUCT_IMAGES_SOURCE_PATH, $name);
        return $file === false ? false : array('file' => $file, 'shared' => true, 'mtime' => filemtime($file));
    }
    $metadata = dotbright_image_remote_metadata($name);
    return $metadata === false ? false : array('file' => false, 'shared' => true, 'mtime' => $metadata['mtime']);
}

// Compatibility wrappers for read operations only. All write paths stay local.
function dotbright_image_exists($input)
{
    if (!dotbright_shared_images() || !dotbright_image_reference($input)) return is_file($input);
    return dotbright_image_asset($input) !== false;
}
function dotbright_image_file_exists($input)
{
    if (!dotbright_shared_images() || !dotbright_image_reference($input)) return file_exists($input);
    return dotbright_image_asset($input) !== false;
}
function dotbright_image_resolve($input)
{
    if (!dotbright_shared_images() || !dotbright_image_reference($input)) return stream_resolve_include_path($input);
    return dotbright_image_exists($input);
}
function dotbright_image_mtime($input)
{
    if (!dotbright_shared_images() || !dotbright_image_reference($input)) return filemtime($input);
    $asset = dotbright_image_asset($input);
    return $asset === false ? 0 : $asset['mtime'];
}
function dotbright_image_url($input)
{
    if (!dotbright_shared_images()) return $input;
    $name = dotbright_image_name($input);
    if ($name === false) return dotbright_image_reference($input) ? '' : $input;
    $asset = dotbright_image_asset($input);
    if ($asset === false) return dotbright_image_root_url().'layout/'.(defined('PROJETO') ? PROJETO : 'dotbright').'/images/no_photo.png';
    $url = $asset['shared'] ? dotbright_image_source_url($name) : dotbright_image_root_url().'files/products/'.implode('/', array_map('rawurlencode', explode('/', $name)));
    $query = array();
    $originalQuery = parse_url((string)$input, PHP_URL_QUERY);
    if (is_string($originalQuery)) parse_str($originalQuery, $query);
    if ($asset['mtime']) $query['dp'] = $asset['mtime'];
    return $url.($query ? '?'.http_build_query($query) : '');
}
function dotbright_image_root_url()
{
    return isset($GLOBALS['site_http_url']) ? $GLOBALS['site_http_url'] : '/';
}
function dotbright_product_images_cache_key()
{
    return dotbright_shared_images() ? 'shared_'.substr(hash('sha256', PRODUCT_IMAGES_SOURCE_PATH.'|'.PRODUCT_IMAGES_SOURCE_URL), 0, 12).'_' : '';
}

// Map only the documented product image fields, including cached gallery arrays.
function dotbright_product_image_fields($product)
{
    if (!dotbright_shared_images() || !is_array($product)) return $product;
    foreach ($product as $key => $value) {
        if (strpos((string)$key, 'prd_img') !== 0 && !in_array($key, array('u_imagetiq', 'prod_ee', 'prod_ue', 'prod_dt', 'destaque_ext', 'destaque2_ext', 'destaque3_ext', 'destaque4_ext'), true)) continue;
        if (is_array($value)) {
            foreach ($value as $size => $images) {
                if (is_array($images)) $product[$key][$size] = array_map('dotbright_image_url', $images);
                elseif (is_string($images)) $product[$key][$size] = dotbright_image_url($images);
            }
        } elseif (is_string($value)) $product[$key] = dotbright_image_url($value);
    }
    return $product;
}
