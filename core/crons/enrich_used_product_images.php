<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__FILE__) . '/../../core/';
require_once $root . 'core_files.php';
require_once $root . 'catalog_enrichment.php';
require_once $root . 'seminovo_reference_images.php';

$options = array('limit' => null, 'product-id' => null, 'apply' => false, 'allow-web' => false);
foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--apply') {
        $options['apply'] = true;
    } elseif ($argument === '--allow-web') {
        $options['allow-web'] = true;
    } elseif (str_starts_with($argument, '--') && str_contains($argument, '=')) {
        [$name, $value] = explode('=', substr($argument, 2), 2);
        if (array_key_exists($name, $options)) {
            $options[$name] = $value;
        }
    }
}

$config = dotbright_enrichment_load_config();
$limit = max(1, min(20, (int)($options['limit'] ?: 1)));
$productId = $options['product-id'] !== null ? (int)$options['product-id'] : null;
$privateDir = rtrim((string)$config['private_dir'], '/');
$lock = fopen($privateDir . '/seminovo-reference-images.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    fwrite(STDERR, "Another used-product reference-image run is active.\n");
    exit(4);
}

$db = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$summary = array('examined' => 0, 'applied' => 0, 'proposed' => 0, 'failed' => 0, 'results' => array());

foreach (dotbright_used_reference_candidates($db, $config, $limit, $productId) as $product) {
    $summary['examined']++;
    try {
        if ($options['apply']) {
            dotbright_used_reference_queue($db, $product);
        }
        $match = dotbright_used_reference_internal_match($db, $product);
        if ($match === null && $options['allow-web']) {
            $match = dotbright_used_reference_official_match($config, $product);
        }
        if ($match === null) {
            throw new RuntimeException($options['allow-web'] ? 'No safe internal or official reference image found.' : 'No safe internal reference image found.');
        }
        $item = array(
            'product_id' => (int)$product['ID'],
            'model_key' => (string)$product['model_key'],
            'source_type' => (string)$match['source_type'],
            'source_product_id' => $match['source_product_id'],
            'source_title' => (string)$match['source_title'],
            'confidence' => (int)$match['confidence'],
        );
        if ($options['apply']) {
            $item['result'] = dotbright_used_reference_apply($db, $config, $product, $match);
            $summary['applied']++;
        } else {
            $summary['proposed']++;
        }
        $summary['results'][] = $item;
    } catch (Throwable $e) {
        if ($options['apply']) {
            dotbright_used_reference_error($db, (int)$product['ID'], $e->getMessage());
        }
        $summary['failed']++;
        $summary['results'][] = array('product_id' => (int)$product['ID'], 'error' => $e->getMessage());
    }
}

$summary['at'] = gmdate('c');
file_put_contents($privateDir . '/seminovo-reference-images-' . gmdate('Ymd') . '.jsonl', json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
flock($lock, LOCK_UN);
fclose($lock);
exit($summary['failed'] > 0 ? 1 : 0);
