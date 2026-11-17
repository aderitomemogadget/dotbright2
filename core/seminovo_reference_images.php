<?php

/**
 * Reference images for used products.
 *
 * A reference image is never a real photograph of the unit in store. It is
 * copied from an existing new product first, or from an official brand page as
 * a fallback, and receives a permanent visible disclaimer.
 */

function dotbright_used_reference_clean_title(string $title): string
{
    $title = preg_replace('/\(SEMINOVO\)/iu', ' ', $title);
    $title = preg_replace('/\((?:NOVO|OFERTA|COM\s+CAPA)[^)]*\)/iu', ' ', (string)$title);
    return trim(preg_replace('/\s+/u', ' ', (string)$title));
}

function dotbright_used_reference_model_key(string $title): string
{
    $title = preg_replace('/([A-Z]\d+)\+/iu', '$1 PLUS', dotbright_used_reference_clean_title($title));
    $value = dotbright_enrichment_normalize((string)$title);
    $patterns = array(
        '/\bPIXEL\s+(\d+)(?:\s+(PRO\s+XL|PRO|XL|A))?\b/',
        '/\bMOTO\s+([A-Z]\d{1,3}[A-Z]?)\b/',
        '/\bGALAXY\s+WATCH\s+(ULTRA(?:\s+\d+)?|\d+)\b/',
        '/\bGALAXY\s+TAB\s+([A-Z]\d+(?:\s+(?:FE|ULTRA|PLUS))?)\b/',
        '/\bWATCH\s+(?:SERIE|SERIES)\s+(\d+)\b/',
        '/\bIPHONE\s+(\d+(?:\s+(?:PRO\s+MAX|PRO|PLUS|MINI|E))?)\b/',
        '/\bGALAXY\s+([ASZ]\d+(?:\s+(?:ULTRA|FE|PLUS))?)\b/',
    );
    foreach ($patterns as $index => $pattern) {
        if (!preg_match($pattern, $value, $match)) {
            continue;
        }
        $suffix = trim(implode(' ', array_slice($match, 1)));
        if ($index === 0) {
            return trim('PIXEL ' . $suffix);
        }
        if ($index === 1) {
            return 'MOTO ' . $suffix;
        }
        if ($index === 2) {
            return 'GALAXY WATCH ' . $suffix;
        }
        if ($index === 3) {
            return 'GALAXY TAB ' . $suffix;
        }
        if ($index === 4) {
            return 'APPLE WATCH SERIES ' . $suffix;
        }
        if ($index === 5) {
            return 'IPHONE ' . $suffix;
        }
        return 'GALAXY ' . $suffix;
    }
    return '';
}

function dotbright_used_reference_marker(string $sourceType, string $source): string
{
    if ($sourceType === 'internal') {
        return 'dotbright-reference:internal:' . (int)$source;
    }
    return 'dotbright-reference:official:' . substr(hash('sha256', $source), 0, 24);
}

function dotbright_used_reference_is_marker(?string $source): bool
{
    return str_starts_with((string)$source, 'dotbright-reference:');
}

function dotbright_used_reference_family_is_disallowed(PDO $db, int $familyId): bool
{
    static $cache = array();
    if (array_key_exists($familyId, $cache)) {
        return $cache[$familyId];
    }
    $visited = array();
    $current = $familyId;
    $lookup = $db->prepare('SELECT ID_ORG FROM products_families WHERE ID=?');
    for ($depth = 0; $depth < 20 && $current > 0; $depth++) {
        if (isset($visited[$current])) {
            return $cache[$familyId] = true;
        }
        $visited[$current] = true;
        // 7 is the seminovo tree and 85 is the repair-services tree.
        if (in_array($current, array(7, 85, 163), true)) {
            return $cache[$familyId] = true;
        }
        $lookup->execute(array($current));
        $parent = $lookup->fetchColumn();
        if ($parent === false) {
            return $cache[$familyId] = true;
        }
        $current = (int)$parent;
    }
    return $cache[$familyId] = false;
}

function dotbright_used_reference_physical_images(int $productId): array
{
    $root = dotbright_enrichment_site_root() . '/files/products';
    $result = array();
    foreach (glob($root . '/' . $productId . '_*.*') ?: array() as $file) {
        if (str_contains($file, '_small.') || str_contains($file, '_thum.') || str_contains($file, '_prod.')) {
            continue;
        }
        if (preg_match('/\/' . preg_quote((string)$productId, '/') . '_(\d+)\.(jpe?g|png|webp)$/i', str_replace('\\', '/', $file))) {
            $result[] = $file;
        }
    }
    return $result;
}

function dotbright_used_reference_candidates(PDO $db, array $config, int $limit, ?int $productId = null): array
{
    $sql = "SELECT p.ID, p.Ref, p.Ref_Ean, p.EAN, p.ID_Marca, pl.Cabecalho,
                   bl.Nome AS Marca, sri.status, sri.attempts
            FROM products p
            INNER JOIN products_lang pl ON pl.ID_PAI=p.ID AND pl.Lang=?
            INNER JOIN brands_lang bl ON bl.ID_PAI=p.ID_Marca AND bl.Lang=?
            INNER JOIN products_families_obj pfo ON pfo.ID_PROD=p.ID AND pfo.ID_ORG=163
            INNER JOIN products_objs po ON po.obj_type_id=p.ID AND po.obj_type='products' AND po.obj_ativo=1
            LEFT JOIN seminovo_reference_images sri ON sri.product_id=p.ID
            WHERE p.Aproved=1 AND po.obj_stock>0";
    $params = array((string)$config['language'], (string)$config['language']);
    if ($productId !== null) {
        $sql .= ' AND p.ID=?';
        $params[] = $productId;
    } else {
        $sql .= " AND (sri.product_id IS NULL OR sri.status='pending'
                  OR (sri.status='error' AND sri.attempts < ?))";
        $params[] = max(1, (int)($config['max_discovery_attempts'] ?? 2));
    }
    $sql .= ' GROUP BY p.ID,p.Ref,p.Ref_Ean,p.EAN,p.ID_Marca,pl.Cabecalho,bl.Nome,sri.status,sri.attempts
              ORDER BY (sri.product_id IS NULL) DESC,p.ID DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (dotbright_used_reference_physical_images((int)$row['ID'])) {
            continue;
        }
        $row['model_key'] = dotbright_used_reference_model_key((string)$row['Cabecalho']);
        if ($row['model_key'] === '') {
            continue;
        }
        $row['gtin'] = dotbright_enrichment_pick_gtin($row);
        $rows[] = $row;
        if (count($rows) >= $limit) {
            break;
        }
    }
    return $rows;
}

function dotbright_used_reference_queue(PDO $db, array $product): void
{
    $stmt = $db->prepare("INSERT INTO seminovo_reference_images
        (product_id, product_title, brand_name, model_key, status, attempts, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'pending', 0, NOW(), NOW())
        ON DUPLICATE KEY UPDATE product_title=VALUES(product_title), brand_name=VALUES(brand_name),
        model_key=VALUES(model_key), status=IF(status='applied',status,'pending'), updated_at=NOW()");
    $stmt->execute(array((int)$product['ID'], (string)$product['Cabecalho'], (string)$product['Marca'], (string)$product['model_key']));
}

function dotbright_used_reference_internal_match(PDO $db, array $product): ?array
{
    $stmt = $db->prepare("SELECT DISTINCT p.ID, p.Ref, p.Ref_Ean, p.EAN, pl.Cabecalho, pl.Texto1,
                                pfo.ID_ORG AS family_id, pi.Ordem, pi.img_ext, pi.URL_Source
        FROM products p
        INNER JOIN products_lang pl ON pl.ID_PAI=p.ID AND pl.Lang='pt'
        INNER JOIN products_families_obj pfo ON pfo.ID_PROD=p.ID
        INNER JOIN products_families pf ON pf.ID=pfo.ID_ORG AND pf.Download=0
        INNER JOIN products_images pi ON pi.ID_ORG=p.ID
        WHERE p.Aproved=1 AND COALESCE(p.usado,0)=0 AND p.ID_Marca=?
          AND pfo.ID_ORG<>163 AND (pi.URL_Source IS NULL OR pi.URL_Source NOT LIKE 'dotbright-reference:%')
        ORDER BY p.ID DESC, pi.Ordem ASC");
    $stmt->execute(array((int)$product['ID_Marca']));
    $root = dotbright_enrichment_site_root() . '/files/products/';
    $best = null;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (dotbright_used_reference_model_key((string)$row['Cabecalho']) !== $product['model_key']) {
            continue;
        }
        if (dotbright_used_reference_family_is_disallowed($db, (int)$row['family_id'])
            || preg_match('/\bREPARA(?:ÇÃO|CAO|ÇÕES|COES)\b/iu', (string)$row['Cabecalho'])) {
            continue;
        }
        $file = $root . (int)$row['ID'] . '_' . (int)$row['Ordem'] . '.' . strtolower((string)$row['img_ext']);
        if (!is_file($file)) {
            continue;
        }
        $score = dotbright_enrichment_similarity(dotbright_used_reference_clean_title((string)$product['Cabecalho']), (string)$row['Cabecalho']);
        if ($product['gtin'] !== null && in_array($product['gtin'], array(dotbright_enrichment_gtin((string)$row['Ref_Ean']), dotbright_enrichment_gtin((string)$row['EAN'])), true)) {
            $score += 1.0;
        }
        if ($best === null || $score > $best['score']) {
            $best = array(
                'source_type' => 'internal',
                'source_product_id' => (int)$row['ID'],
                'source_url' => '',
                'source_title' => (string)$row['Cabecalho'],
                'source_description' => (string)$row['Texto1'],
                'source_file' => $file,
                'score' => $score,
                'confidence' => min(100, 85 + (int)round(min(1.0, $score) * 15)),
            );
        }
    }
    return $best;
}

function dotbright_used_reference_official_match(array $config, array $product): ?array
{
    $brandKey = dotbright_enrichment_normalize((string)$product['Marca']);
    $domains = $config['official_domains'][$brandKey] ?? array();
    if (!$domains) {
        return null;
    }
    $siteQuery = implode(' OR ', array_map(static fn($domain) => 'site:' . $domain, $domains));
    $query = $siteQuery . ' "' . $product['model_key'] . '" ' . dotbright_used_reference_clean_title((string)$product['Cabecalho']);
    $urls = dotbright_enrichment_search($query, $config);
    foreach ($urls as $url) {
        $host = (string)(parse_url((string)$url, PHP_URL_HOST) ?: '');
        if (!dotbright_enrichment_host_allowed($host, $domains)) {
            continue;
        }
        try {
            $page = dotbright_enrichment_http_get((string)$url, $domains, (int)$config['max_page_bytes'], (int)$config['request_timeout']);
            $extracted = dotbright_enrichment_extract_product($page['body'], $page['url'], (string)($product['gtin'] ?? ''), (string)$product['Cabecalho']);
            if (dotbright_used_reference_model_key((string)$extracted['title']) !== $product['model_key'] || empty($extracted['images'])) {
                continue;
            }
            foreach ($extracted['images'] as $imageUrl) {
                if (dotbright_enrichment_product_image_url((string)$imageUrl)
                    && dotbright_enrichment_safe_url((string)$imageUrl, $domains)) {
                    return array(
                        'source_type' => 'official',
                        'source_product_id' => null,
                        'source_url' => (string)$imageUrl,
                        'page_url' => (string)$page['url'],
                        'source_title' => (string)$extracted['title'],
                        'source_description' => '',
                        'source_file' => '',
                        'score' => (float)$extracted['title_similarity'],
                        'confidence' => 90,
                    );
                }
            }
        } catch (Throwable $e) {
            continue;
        }
    }
    return null;
}

function dotbright_used_reference_prepare_image(string $bytes): string
{
    $source = @imagecreatefromstring($bytes);
    if (!$source || imagesx($source) < 250 || imagesy($source) < 250) {
        if ($source) {
            imagedestroy($source);
        }
        throw new RuntimeException('Reference image is invalid or too small.');
    }
    $width = imagesx($source);
    $height = imagesy($source);
    $canvas = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

    ob_start();
    imagejpeg($canvas, null, 90);
    $jpeg = (string)ob_get_clean();
    imagedestroy($canvas);
    imagedestroy($source);
    return $jpeg;
}

function dotbright_used_reference_apply(PDO $db, array $config, array $product, array $match): array
{
    if (dotbright_used_reference_physical_images((int)$product['ID'])) {
        return array('skipped' => 'image_already_exists');
    }
    if ($match['source_type'] === 'internal') {
        $bytes = (string)file_get_contents((string)$match['source_file']);
    } else {
        $brandKey = dotbright_enrichment_normalize((string)$product['Marca']);
        $domains = $config['official_domains'][$brandKey] ?? array();
        $download = dotbright_enrichment_download_image((string)$match['source_url'], $domains, $config);
        $bytes = $download['bytes'];
    }
    $jpeg = dotbright_used_reference_prepare_image($bytes);
    $root = dotbright_enrichment_site_root();
    $productId = (int)$product['ID'];
    $file = $root . '/files/products/' . $productId . '_1.jpg';
    $tmp = $file . '.tmp-' . bin2hex(random_bytes(4));
    if (file_put_contents($tmp, $jpeg, LOCK_EX) !== strlen($jpeg)) {
        @unlink($tmp);
        throw new RuntimeException('Unable to write the reference image.');
    }

    $db->beginTransaction();
    try {
        $stale = $db->prepare('SELECT ID, Ordem, img_ext, URL_Source FROM products_images WHERE ID_ORG=? FOR UPDATE');
        $stale->execute(array($productId));
        $staleRows = $stale->fetchAll(PDO::FETCH_ASSOC);
        $descriptionCopied = false;
        $descriptionBefore = null;
        $descriptionSourceHash = null;
        $currentText = $db->prepare('SELECT Texto1 FROM products_lang WHERE ID_PAI=? AND Lang=? FOR UPDATE');
        $currentText->execute(array($productId, (string)$config['language']));
        $currentTextRow = $currentText->fetch(PDO::FETCH_ASSOC);
        if ($currentTextRow) {
            $descriptionBefore = (string)$currentTextRow['Texto1'];
        }
        if (dotbright_used_reference_physical_images($productId)) {
            throw new RuntimeException('A real image appeared while the reference was being prepared.');
        }
        $db->prepare('DELETE FROM products_images WHERE ID_ORG=?')->execute(array($productId));
        if (!rename($tmp, $file)) {
            throw new RuntimeException('Unable to activate the reference image.');
        }
        chmod($file, 0644);
        $markerSource = $match['source_type'] === 'internal' ? (string)$match['source_product_id'] : (string)$match['source_url'];
        $marker = dotbright_used_reference_marker((string)$match['source_type'], $markerSource);
        $insert = $db->prepare('INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES (?, ?, 1, ?)');
        $insert->execute(array($productId, 'jpg', $marker));
        if ($currentTextRow
            && trim(strip_tags((string)$currentTextRow['Texto1'])) === ''
            && (string)$match['source_type'] === 'internal'
            && trim(strip_tags((string)($match['source_description'] ?? ''))) !== '') {
            $sourceDescription = (string)$match['source_description'];
            $copyText = $db->prepare('UPDATE products_lang SET Texto1=?, Data_modify=NOW() WHERE ID_PAI=? AND Lang=?');
            $copyText->execute(array($sourceDescription, $productId, (string)$config['language']));
            $descriptionCopied = true;
            $descriptionSourceHash = hash('sha256', $sourceDescription);
        }
        $auditSnapshot = array(
            'image_rows' => $staleRows,
            'description_before' => $descriptionBefore,
            'description_copied' => $descriptionCopied,
            'description_source_sha256' => $descriptionSourceHash,
        );
        $update = $db->prepare("UPDATE seminovo_reference_images SET source_type=?, source_product_id=?,
            source_url=?, source_page_url=?, source_title=?, confidence=?, image_sha256=?, stale_rows_json=?,
            status='applied', attempts=attempts+1, last_error=NULL, applied_at=NOW(), updated_at=NOW()
            WHERE product_id=?");
        $update->execute(array(
            (string)$match['source_type'],
            $match['source_product_id'],
            (string)($match['source_url'] ?? ''),
            (string)($match['page_url'] ?? ''),
            (string)$match['source_title'],
            (int)$match['confidence'],
            hash('sha256', $jpeg),
            json_encode($auditSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $productId,
        ));
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        @unlink($tmp);
        if (is_file($file)) {
            @unlink($file);
        }
        throw $e;
    }

    if (defined('IMG_SMALL') && defined('IMG_THUM')) {
        $small = @unserialize(IMG_SMALL);
        $thumb = @unserialize(IMG_THUM);
        $original = 'files/products/' . $productId . '_1.jpg';
        if (is_array($small) && !empty($small['ativo'])) {
            geraimagens($original, 'files/products/' . $productId . '_1_small.png', (int)$small['w'], (int)$small['h'], (array)$small['crop'], (int)$small['m_trans'], (int)$small['c_white']);
        }
        if (is_array($thumb) && !empty($thumb['ativo'])) {
            geraimagens($original, 'files/products/' . $productId . '_1_thum.png', (int)$thumb['w'], (int)$thumb['h'], (array)$thumb['crop'], (int)$thumb['m_trans'], (int)$thumb['c_white']);
        }
    }
    foreach (array(
        $root . '/files/cache/p' . $productId . '_*.bin',
        $root . '/files/cache/products/p' . $productId . '_*.bin',
    ) as $pattern) {
        foreach (glob($pattern) ?: array() as $cache) {
            @unlink($cache);
        }
    }
    return array(
        'image' => $file,
        'source_type' => $match['source_type'],
        'source_product_id' => $match['source_product_id'],
        'description_copied' => $descriptionCopied,
    );
}

function dotbright_used_reference_error(PDO $db, int $productId, string $message): void
{
    $stmt = $db->prepare("UPDATE seminovo_reference_images SET status='error', attempts=attempts+1,
        last_error=?, updated_at=NOW() WHERE product_id=?");
    $stmt->execute(array(substr($message, 0, 1000), $productId));
}
