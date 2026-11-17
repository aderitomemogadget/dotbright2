<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$path = dirname(__FILE__) . '/../../core/';
require_once $path . 'core_files.php';
require_once $path . 'catalog_enrichment.php';

function dotbright_enrichment_cli_options(array $argv): array
{
    $options = array(
        'mode' => 'scan',
        'limit' => null,
        'product-id' => null,
        'config' => null,
        'apply-text' => false,
        'append-images' => false,
        'auto-apply-approved' => false,
    );
    foreach (array_slice($argv, 1) as $argument) {
        if ($argument === '--apply-text') {
            $options['apply-text'] = true;
        } elseif ($argument === '--append-images') {
            $options['append-images'] = true;
        } elseif ($argument === '--auto-apply-approved') {
            $options['auto-apply-approved'] = true;
        } elseif (str_starts_with($argument, '--') && str_contains($argument, '=')) {
            [$name, $value] = explode('=', substr($argument, 2), 2);
            if (array_key_exists($name, $options)) {
                $options[$name] = $value;
            }
        }
    }
    return $options;
}

$options = dotbright_enrichment_cli_options($argv);
$config = dotbright_enrichment_load_config($options['config']);
$mode = strtolower((string)$options['mode']);
$limit = max(1, min(100, (int)($options['limit'] ?: $config['batch_size'])));
$productId = $options['product-id'] !== null ? (int)$options['product-id'] : null;

if (!in_array($mode, array('scan', 'discover', 'apply'), true)) {
    fwrite(STDERR, "Invalid mode. Use scan, discover or apply.\n");
    exit(2);
}
if (empty($config['enabled']) && $mode !== 'scan') {
    fwrite(STDERR, "Catalog enrichment is disabled in " . $config['config_path'] . ".\n");
    exit(3);
}

$lockDir = rtrim((string)$config['private_dir'], '/');
if (!is_dir($lockDir) && !mkdir($lockDir, 0750, true) && !is_dir($lockDir)) {
    throw new RuntimeException('Unable to create private enrichment directory.');
}
$lock = fopen($lockDir . '/catalog-enrichment.lock', 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    fwrite(STDERR, "Another catalogue enrichment run is active.\n");
    exit(4);
}

$db = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$summary = array('mode' => $mode, 'examined' => 0, 'success' => 0, 'failed' => 0, 'results' => array());

try {
    if ($mode === 'apply') {
        if (!$productId) {
            throw new RuntimeException('Apply mode requires --product-id=ID.');
        }
        $result = dotbright_enrichment_apply($db, $config, $productId, (bool)$options['apply-text'], (bool)$options['append-images']);
        $summary['examined'] = 1;
        $summary['success'] = 1;
        $summary['results'][] = array('product_id' => $productId, 'result' => $result);
    } else {
        $candidates = dotbright_enrichment_candidates($db, $config, $limit, $productId, $mode === 'discover');
        foreach ($candidates as $product) {
            $summary['examined']++;
            try {
                dotbright_enrichment_upsert_candidate($db, $product);
                $item = array(
                    'product_id' => (int)$product['ID'],
                    'gtin' => $product['gtin'],
                    'brand' => $product['Marca'],
                    'missing_image' => $product['missing_image'],
                    'missing_description' => $product['missing_description'],
                );
                if ($mode === 'discover') {
                    $proposal = dotbright_enrichment_discover($db, $config, $product);
                    $item['source_url'] = $proposal['source_url'];
                    $item['confidence'] = $proposal['confidence'];
                    if ($options['auto-apply-approved'] && $proposal['confidence'] === 100) {
                        $item['applied'] = dotbright_enrichment_apply($db, $config, (int)$product['ID'], true, false);
                    }
                }
                $summary['success']++;
                $summary['results'][] = $item;
            } catch (Throwable $e) {
                $summary['failed']++;
                $summary['results'][] = array('product_id' => (int)$product['ID'], 'error' => $e->getMessage());
                $error = $db->prepare("UPDATE product_enrichment_queue SET status='error', attempts=attempts+1, last_error=?, updated_at=NOW() WHERE product_id=?");
                $error->execute(array(substr($e->getMessage(), 0, 1000), (int)$product['ID']));
            }
        }
    }
} catch (Throwable $e) {
    $summary['failed']++;
    $summary['fatal'] = $e->getMessage();
}

dotbright_enrichment_log($config, $summary);
echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
flock($lock, LOCK_UN);
fclose($lock);
exit($summary['failed'] > 0 ? 1 : 0);
