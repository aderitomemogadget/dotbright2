<?php
require_once dirname(__DIR__).'/conf/files_images.php';

function dotbright_files_images_frontend()
{
    $script = str_replace('\\', '/', isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '');
    return !preg_match('~/(?:admin|rmas|crons|tests)/~i', $script);
}

function dotbright_shared_files_images()
{
    // Backoffice uploads, importers and CRONs keep their original local checks.
    return SHARED_FILES_IMAGES === true && dotbright_files_images_frontend();
}

function dotbright_files_image_relative_input($input)
{
    if (!is_string($input)) return false;
    $value = str_replace('\\', '/', $input);
    $prefixes = array(dotbright_image_root(), dotbright_image_root_url(), FILES_IMAGES_SOURCE_URL);
    foreach ($prefixes as $prefix) {
        $prefix = rtrim($prefix, '/').'/';
        // Source URL already includes files/.
        if ($prefix === rtrim(FILES_IMAGES_SOURCE_URL, '/').'/') {
            if (strpos($value, $prefix) === 0) return 'files/'.substr($value, strlen($prefix));
        } elseif (strpos($value, $prefix.'files/') === 0) return substr($value, strlen($prefix));
    }
    $publicBase = parse_url(dotbright_image_root_url(), PHP_URL_PATH);
    if (is_string($publicBase) && strpos($value, rtrim($publicBase, '/').'/files/') === 0) {
        return substr($value, strlen(rtrim($publicBase, '/')) + 1);
    }
    if (preg_match('~^(?:\./|\.\./)*files/~', $value, $match)) return 'files/'.substr($value, strlen($match[0]));
    if (strpos($value, '/files/') === 0) return substr($value, 1);
    $offset = strpos($value, '/files/');
    $root = realpath(dotbright_image_root());
    if ($root !== false && $offset !== false && realpath(substr($value, 0, $offset)) === $root) return substr($value, $offset + 1);
    return false;
}

function dotbright_files_image_name($input)
{
    $relative = dotbright_files_image_relative_input($input);
    if ($relative === false) return false;
    $name = rawurldecode(preg_split('~[?#]~', substr($relative, 6), 2)[0]);
    if ($name === '' || preg_match('~[\x00-\x1f\x7f%\\\\:]~', $name)) return false;
    foreach (explode('/', $name) as $part) if ($part === '' || $part === '.' || $part === '..') return false;
    // No PDFs, documents, CSS, JavaScript, HTML or executable files.
    if (!preg_match('~\.(?:jpe?g|png|webp|gif|avif|svg|ico|bmp)$~iD', $name)) return false;
    return $name;
}

function dotbright_files_image_reference($input)
{
    $relative = dotbright_files_image_relative_input($input);
    return $relative !== false && preg_match('~\.(?:jpe?g|png|webp|gif|avif|svg|ico|bmp)(?:[?#]|$)~i', $relative);
}

function dotbright_files_image_missing_local($input)
{
    $name = dotbright_files_image_name($input);
    return dotbright_shared_files_images() && $name !== false
        && dotbright_image_file_in(dotbright_image_root().'files', $name) === false;
}

function dotbright_files_image_source_url($name)
{
    $parts = parse_url(FILES_IMAGES_SOURCE_URL);
    if (!$parts || !isset($parts['scheme'], $parts['host']) || $parts['scheme'] !== 'https'
        || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) return '';
    return rtrim(FILES_IMAGES_SOURCE_URL, '/').'/'.implode('/', array_map('rawurlencode', explode('/', $name)));
}

function dotbright_files_image_asset($input)
{
    $name = dotbright_files_image_name($input);
    if ($name === false) return false;
    // Preserve the existing product configuration and its separate source.
    if (strpos($name, 'products/') === 0 && dotbright_image_name('files/'.$name) !== false) return dotbright_image_asset('files/'.$name);
    $local = dotbright_image_file_in(dotbright_image_root().'files', $name);
    if ($local !== false) return array('file'=>$local, 'shared'=>false, 'mtime'=>filemtime($local));
    if (!dotbright_shared_files_images()) return false;
    if (FILES_IMAGES_SOURCE_PATH !== '') {
        $file = dotbright_image_file_in(FILES_IMAGES_SOURCE_PATH, $name);
        return $file === false ? false : array('file'=>$file, 'shared'=>true, 'mtime'=>filemtime($file));
    }
    $metadata = dotbright_image_remote_metadata_url(dotbright_files_image_source_url($name), true);
    return $metadata === false ? false : array('file'=>false, 'shared'=>true, 'mtime'=>$metadata['mtime']);
}

function dotbright_files_image_url($input)
{
    if (!dotbright_shared_files_images()) return $input;
    $name = dotbright_files_image_name($input);
    if ($name === false) return dotbright_files_image_reference($input) ? dotbright_files_no_photo_url() : $input;
    if (strpos($name, 'products/') === 0 && dotbright_image_name('files/'.$name) !== false) {
        $relative = dotbright_files_image_relative_input($input);
        $fragment = parse_url($input, PHP_URL_FRAGMENT);
        return dotbright_image_url(explode('#', $relative, 2)[0]).(is_string($fragment) ? '#'.rawurlencode($fragment) : '');
    }
    $asset = dotbright_files_image_asset($input);
    // Legacy favicon URLs contain an mtime alias handled by production rewrites.
    if ($asset === false && preg_match('~_v_[0-9]+(?=\.[^.]+$)~', $name)) {
        $canonical = preg_replace('~_v_[0-9]+(?=\.[^.]+$)~', '', $name);
        $asset = dotbright_files_image_asset('files/'.$canonical);
        if ($asset !== false) $name = $canonical;
    }
    if ($asset === false) return dotbright_files_no_photo_url();
    $url = $asset['shared'] ? dotbright_files_image_source_url($name)
        : dotbright_image_root_url().'files/'.implode('/', array_map('rawurlencode', explode('/', $name)));
    $query = array(); $parts = parse_url($input);
    if (isset($parts['query'])) parse_str($parts['query'], $query);
    if ($asset['mtime']) $query['dp'] = $asset['mtime'];
    return $url.($query ? '?'.http_build_query($query) : '').(isset($parts['fragment']) ? '#'.rawurlencode($parts['fragment']) : '');
}

function dotbright_files_no_photo_url()
{
    return dotbright_image_root_url().'layout/'.(defined('PROJETO') ? PROJETO : 'dotbright').'/images/no_photo.png';
}

function dotbright_files_image_size($input)
{
    $name = dotbright_files_image_name($input);
    if (!dotbright_shared_files_images() || $name === false) return @getimagesize($input);
    $asset = dotbright_files_image_asset($input);
    if ($asset === false) return false;
    if ($asset['file'] !== false) return @getimagesize($asset['file']);
    // getimagesize reads the image header; no source files are copied or written.
    static $memo = array();
    $url = strpos($name, 'products/') === 0 ? dotbright_image_source_url(substr($name, 9)) : dotbright_files_image_source_url($name);
    if (array_key_exists($url, $memo)) return $memo[$url];
    $dir = dotbright_image_cache_dir();
    $cache = $dir === false ? false : $dir.hash('sha256', $url.'|dimensions').'.json';
    $data = $cache && !is_link($cache) && is_file($cache) ? json_decode((string)@file_get_contents($cache), true) : null;
    if (is_array($data) && isset($data['expires'], $data['size']) && $data['expires'] > time()) return $memo[$url] = $data['size'];
    $context = stream_context_create(array('http'=>array('timeout'=>4, 'follow_location'=>0),
        'ssl'=>array('verify_peer'=>true, 'verify_peer_name'=>true)));
    $stream = @fopen($url, 'rb', false, $context);
    $bytes = $stream === false ? false : stream_get_contents($stream, 1048576);
    if (is_resource($stream)) fclose($stream);
    $size = $bytes === false ? false : @getimagesizefromstring($bytes);
    if ($cache && !is_link($cache)) @file_put_contents($cache, json_encode(array('expires'=>time()+($size ? 21600 : 60), 'size'=>$size)), LOCK_EX);
    return $memo[$url] = $size;
}

function dotbright_files_images_css($css)
{
    return preg_replace_callback('~url\(\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s)]*))\s*\)~i', function ($match) {
        $url = isset($match[3]) && $match[3] !== '' ? $match[3] : (isset($match[2]) && $match[2] !== '' ? $match[2] : $match[1]);
        if (dotbright_files_image_name($url) === false) return $match[0];
        return 'url("'.str_replace(array('"', '\\'), array('%22', '%5C'), dotbright_files_image_url($url)).'")';
    }, $css);
}

function dotbright_files_images_srcset($value)
{
    if (stripos($value, 'data:') !== false) return $value;
    $parts = explode(',', $value);
    foreach ($parts as &$part) {
        if (preg_match('~^(\s*)(\S+)(.*)$~s', $part, $match) && dotbright_files_image_name($match[2]) !== false) {
            $part = $match[1].dotbright_files_image_url($match[2]).$match[3];
        }
    }
    return implode(',', $parts);
}

// Target image attributes and CSS url() only; leave links to documents,
// scripts, JSON, executable code, forms and ordinary text unchanged.
function dotbright_files_images_html($html)
{
    if (!dotbright_shared_files_images() || stripos($html, 'files/') === false) return $html;
    return preg_replace_callback('~<!--.*?-->|<script\b[^>]*>.*?</script\s*>|<style\b[^>]*>.*?</style\s*>|<[a-z][a-z0-9:-]*\b(?:[^\x27"<>]|"[^"]*"|\x27[^\x27]*\x27)*>~is', function ($token) {
        $tag = $token[0];
        if (strpos($tag, '<!--') === 0 || preg_match('~^<script\b~i', $tag)) return $tag;
        if (preg_match('~^<style\b~i', $tag)) return dotbright_files_images_css($tag);
        preg_match('~^<([a-z0-9:-]+)~i', $tag, $type); $type = strtolower($type[1]);
        $imageTag = in_array($type, array('img','source','image','video','input'), true);
        return preg_replace_callback('~(\s)([a-z_:][a-z0-9_:.-]*)(\s*=\s*)(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+))~i', function ($attr) use ($type, $imageTag) {
            $name = strtolower($attr[2]);
            $value = isset($attr[6]) && $attr[6] !== '' ? $attr[6] : (isset($attr[5]) && $attr[5] !== '' ? $attr[5] : $attr[4]);
            $decoded = html_entity_decode($value, ENT_QUOTES, 'UTF-8'); $resolved = $decoded;
            if ($name === 'style') $resolved = dotbright_files_images_css($decoded);
            elseif ($imageTag && in_array($name, array('srcset','data-srcset'), true)) $resolved = dotbright_files_images_srcset($decoded);
            elseif (($imageTag && in_array($name, array('src','data-src','data-original','data-lazy-src','poster','href','xlink:href'), true))
                || (in_array($type, array('a','link'), true) && $name === 'href') || ($type === 'meta' && $name === 'content')) {
                if (dotbright_files_image_name($decoded) !== false) $resolved = dotbright_files_image_url($decoded);
            }
            if ($resolved === $decoded) return $attr[0];
            return $attr[1].$attr[2].$attr[3].'"'.htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8').'"';
        }, $tag);
    }, $html);
}

function dotbright_files_images_response($body)
{
    foreach (headers_list() as $header) {
        if (stripos($header, 'Content-Type:') === 0 && !preg_match('~^Content-Type:\s*(?:text/html|application/xhtml\+xml)\b~i', $header)) return $body;
    }
    // Endpoints without an explicit Content-Type may return JSON or downloads.
    $trimmed = ltrim($body);
    if ($trimmed === '' || $trimmed[0] !== '<') return $body;
    return dotbright_files_images_html($body);
}

function dotbright_files_images_start()
{
    static $started = false;
    if ($started || PHP_SAPI === 'cli' || !dotbright_shared_files_images()) return;
    $started = true;
    ob_start('dotbright_files_images_response');
}

function dotbright_files_images_browser_script()
{
    if (!dotbright_shared_files_images()) return '';
    $root = dotbright_image_root_url();
    $attrs = array('src'=>$root.'layout/dotbright/js/shared-files-images.js?v=1', 'data-files-root'=>$root.'files/',
        'data-files-source'=>FILES_IMAGES_SOURCE_URL, 'data-image-endpoint'=>$root.'shared-file-image.php',
        'data-image-fallback'=>dotbright_files_no_photo_url());
    $tag = '<script defer';
    foreach ($attrs as $key=>$value) $tag .= ' '.$key.'="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'"';
    return $tag.'></script>';
}
