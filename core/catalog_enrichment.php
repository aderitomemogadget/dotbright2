<?php

if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] === __FILE__) {
    exit(1);
}
/**
 * Safe catalogue enrichment helpers.
 *
 * Discovery is deliberately separated from publication. The discovery phase
 * only reads official manufacturer pages and stores a proposal. Images and
 * text are published only after an explicit approval/apply run.
 */

function dotbright_enrichment_site_root(): string
{
    $configured = (string)($GLOBALS['site_shell_url'] ?? '');
    $resolved = $configured !== '' ? realpath($configured) : false;
    return rtrim($resolved !== false ? $resolved : $configured, '/\\');
}

function dotbright_enrichment_private_root(): string
{
    $root = dotbright_enrichment_site_root();
    return $root !== '' ? dirname($root) . '/private' : '';
}

function dotbright_enrichment_default_config(): array
{
    return array(
        'enabled' => false,
        'target_family_roots' => array(3071, 3433),
        'language' => 'pt',
        'batch_size' => 10,
        'max_discovery_attempts' => 2,
        'max_images' => 7,
        'max_page_bytes' => 3000000,
        'max_image_bytes' => 10000000,
        'min_image_width' => 300,
        'min_image_height' => 300,
        'request_timeout' => 15,
        'search_provider' => 'google',
        'brave_api_key' => getenv('DOTBRIGHT_BRAVE_SEARCH_KEY') ?: '',
        'google_api_key' => getenv('DOTBRIGHT_GOOGLE_SEARCH_KEY') ?: '',
        'google_cx' => getenv('DOTBRIGHT_GOOGLE_SEARCH_CX') ?: '',
        'serpapi_api_key' => getenv('DOTBRIGHT_SERPAPI_KEY') ?: '',
        'auto_approve_exact_gtin' => false,
        'description_notice' => 'Nota: esta descrição pode conter erros. Por favor, confirme as características no site oficial do fabricante antes da compra.',
        'private_dir' => dotbright_enrichment_private_root() . '/catalog-enrichment',
        'official_domains' => array(
            'APPLE' => array('apple.com'),
            'SAMSUNG' => array('samsung.com'),
            'XIAOMI' => array('mi.com'),
            'MOTOROLA' => array('motorola.com'),
            'ZTE' => array('zte.com'),
            'OPPO' => array('oppo.com'),
            'ONEPLUS' => array('oneplus.com'),
            'GOOGLE' => array('store.google.com', 'support.google.com'),
            'AGM' => array('agmmobile.com'),
            'HONOR' => array('honor.com'),
            'HUAWEI' => array('huawei.com'),
            'NOKIA' => array('nokia.com', 'hmd.com'),
            'NOTHING' => array('nothing.tech'),
            'REALME' => array('realme.com'),
            'TCL' => array('tcl.com'),
            'ASUS' => array('asus.com'),
            'SONY' => array('sony.com'),
            'ULEFONE' => array('ulefone.com'),
            'BLACKVIEW' => array('blackview.hk', 'blackview.us'),
            'ENERGIZER' => array('energizeyourdevice.com'),
            'CROSSCALL' => array('crosscall.com'),
            'VODAFONE' => array('vodafone.pt'),
        ),
    );
}

function dotbright_enrichment_load_config(?string $path = null): array
{
    $config = dotbright_enrichment_default_config();
    if ($path === null || $path === '') {
        $path = dotbright_enrichment_private_root() . '/config/catalog_enrichment.php';
    }
    if (is_file($path)) {
        $custom = require $path;
        if (!is_array($custom)) {
            throw new RuntimeException('Catalog enrichment config must return an array.');
        }
        $config = array_replace_recursive($config, $custom);
    }
    $config['config_path'] = $path;
    return $config;
}

function dotbright_enrichment_normalize(string $value): string
{
    $value = trim($value);
    if (function_exists('iconv')) {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($ascii !== false) {
            $value = $ascii;
        }
    }
    $value = strtoupper($value);
    return trim((string)preg_replace('/[^A-Z0-9]+/', ' ', $value));
}

function dotbright_enrichment_gtin(string $value): ?string
{
    $digits = preg_replace('/\D+/', '', trim($value));
    if (!in_array(strlen($digits), array(8, 12, 13, 14), true)) {
        return null;
    }
    $sum = 0;
    $parity = 3;
    for ($i = strlen($digits) - 2; $i >= 0; $i--) {
        $sum += ((int)$digits[$i]) * $parity;
        $parity = $parity === 3 ? 1 : 3;
    }
    $check = (10 - ($sum % 10)) % 10;
    return $check === (int)$digits[strlen($digits) - 1] ? $digits : null;
}

function dotbright_enrichment_pick_gtin(array $product): ?string
{
    foreach (array('Ref_Ean', 'EAN', 'Ref') as $field) {
        $gtin = dotbright_enrichment_gtin((string)($product[$field] ?? ''));
        if ($gtin !== null) {
            return $gtin;
        }
    }
    return null;
}

function dotbright_enrichment_host_allowed(string $host, array $allowedDomains): bool
{
    $host = strtolower(rtrim($host, '.'));
    foreach ($allowedDomains as $domain) {
        $domain = strtolower(ltrim(rtrim((string)$domain, '.'), '.'));
        if ($domain !== '' && ($host === $domain || str_ends_with($host, '.' . $domain))) {
            return true;
        }
    }
    return false;
}

function dotbright_enrichment_public_host(string $host): bool
{
    $records = @dns_get_record($host, DNS_A | DNS_AAAA);
    if (!is_array($records) || count($records) === 0) {
        return false;
    }
    foreach ($records as $record) {
        $ip = $record['ip'] ?? ($record['ipv6'] ?? '');
        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
    }
    return true;
}

function dotbright_enrichment_safe_url(string $url, array $allowedDomains): bool
{
    $parts = parse_url($url);
    if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https') {
        return false;
    }
    $host = strtolower((string)($parts['host'] ?? ''));
    return $host !== ''
        && dotbright_enrichment_host_allowed($host, $allowedDomains)
        && dotbright_enrichment_public_host($host);
}

function dotbright_enrichment_http_get(string $url, array $allowedDomains, int $maxBytes, int $timeout, array $headers = array()): array
{
    $current = $url;
    for ($redirect = 0; $redirect <= 3; $redirect++) {
        if (!dotbright_enrichment_safe_url($current, $allowedDomains)) {
            throw new RuntimeException('Rejected non-official or unsafe URL: ' . $current);
        }
        $body = '';
        $responseHeaders = array();
        $ch = curl_init($current);
        $requestHeaders = array_merge(array('Accept: text/html,application/xhtml+xml,application/json,image/*;q=0.8'), $headers);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => min(8, $timeout),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'DotbrightCatalogEnrichment/1.0 (+https://www.dotbright.pt/)',
            CURLOPT_HTTPHEADER => $requestHeaders,
            CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$responseHeaders): int {
                $length = strlen($line);
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $length;
            },
            CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$body, $maxBytes): int {
                if (strlen($body) + strlen($chunk) > $maxBytes) {
                    return 0;
                }
                $body .= $chunk;
                return strlen($chunk);
            },
        ));
        $ok = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $contentType = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $errorCode = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($status >= 300 && $status < 400 && isset($responseHeaders['location'])) {
            $current = dotbright_enrichment_resolve_url($current, $responseHeaders['location']);
            continue;
        }
        $partialAtLimit = $ok === false
            && $errorCode === CURLE_WRITE_ERROR
            && $status >= 200
            && $status < 300
            && $body !== '';
        if (($ok === false && !$partialAtLimit) || $status < 200 || $status >= 300) {
            throw new RuntimeException('HTTP request failed (' . $status . '): ' . ($error ?: $current));
        }
        return array(
            'url' => $current,
            'body' => $body,
            'content_type' => $contentType,
            'status' => $status,
            'truncated' => $partialAtLimit,
        );
    }
    throw new RuntimeException('Too many redirects.');
}

function dotbright_enrichment_resolve_url(string $base, string $relative): string
{
    if (preg_match('~^https://~i', $relative)) {
        return $relative;
    }
    $parts = parse_url($base);
    if (!is_array($parts) || empty($parts['host'])) {
        return $relative;
    }
    $origin = 'https://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
    if (str_starts_with($relative, '//')) {
        return 'https:' . $relative;
    }
    if (str_starts_with($relative, '/')) {
        return $origin . $relative;
    }
    $path = (string)($parts['path'] ?? '/');
    return $origin . rtrim(str_replace('\\', '/', dirname($path)), '/') . '/' . $relative;
}

function dotbright_enrichment_product_image_url(string $url): bool
{
    $path = strtolower((string)(parse_url($url, PHP_URL_PATH) ?: ''));
    if ($path === '') {
        return false;
    }
    return !preg_match('~(?:^|[/_.-])(logo|favicon|icon|sprite|placeholder|no[-_]?image)(?:[/_.-]|$)~i', $path);
}

function dotbright_enrichment_search_brave(string $query, string $apiKey, int $timeout): array
{
    if ($apiKey === '') {
        throw new RuntimeException('Missing Brave Search API key.');
    }
    $url = 'https://api.search.brave.com/res/v1/web/search?' . http_build_query(array(
        'q' => $query,
        'count' => 10,
        'country' => 'PT',
        'search_lang' => 'pt',
        'safesearch' => 'strict',
    ));
    $result = dotbright_enrichment_http_get(
        $url,
        array('api.search.brave.com'),
        2000000,
        $timeout,
        array('Accept: application/json', 'X-Subscription-Token: ' . $apiKey)
    );
    $json = json_decode($result['body'], true);
    if (!is_array($json)) {
        throw new RuntimeException('Invalid search provider response.');
    }
    $urls = array();
    foreach (($json['web']['results'] ?? array()) as $item) {
        if (!empty($item['url'])) {
            $urls[] = (string)$item['url'];
        }
    }
    return array_values(array_unique($urls));
}

function dotbright_enrichment_search_google(string $query, string $apiKey, string $cx, int $timeout): array
{
    if ($apiKey === '' || $cx === '') {
        throw new RuntimeException('Missing Google Custom Search API key or search engine ID (cx).');
    }
    $url = 'https://customsearch.googleapis.com/customsearch/v1?' . http_build_query(array(
        'key' => $apiKey,
        'cx' => $cx,
        'q' => $query,
        'num' => 10,
        'safe' => 'active',
        'gl' => 'pt',
        'hl' => 'pt-PT',
    ));
    $result = dotbright_enrichment_http_get(
        $url,
        array('customsearch.googleapis.com'),
        2000000,
        $timeout,
        array('Accept: application/json')
    );
    $json = json_decode($result['body'], true);
    if (!is_array($json)) {
        throw new RuntimeException('Invalid Google Custom Search response.');
    }
    $urls = array();
    foreach (($json['items'] ?? array()) as $item) {
        if (!empty($item['link'])) {
            $urls[] = (string)$item['link'];
        }
    }
    return array_values(array_unique($urls));
}

function dotbright_enrichment_serpapi_urls(array $json): array
{
    $urls = array();
    foreach (($json['organic_results'] ?? array()) as $item) {
        if (is_array($item) && !empty($item['link'])) {
            $urls[] = (string)$item['link'];
        }
    }
    return array_values(array_unique($urls));
}

function dotbright_enrichment_serpapi_response(array $json): array
{
    if (!empty($json['error'])) {
        $error = (string)$json['error'];
        if (stripos($error, "hasn't returned any results") !== false
            || stripos($error, 'no results') !== false) {
            return array();
        }
        throw new RuntimeException('SerpApi error: ' . $error);
    }
    return dotbright_enrichment_serpapi_urls($json);
}

function dotbright_enrichment_search_serpapi(string $query, string $apiKey, int $timeout): array
{
    if ($apiKey === '') {
        throw new RuntimeException('Missing SerpApi API key.');
    }
    $url = 'https://serpapi.com/search.json?' . http_build_query(array(
        'engine' => 'google',
        'api_key' => $apiKey,
        'q' => $query,
        'num' => 10,
    ));
    $result = dotbright_enrichment_http_get(
        $url,
        array('serpapi.com'),
        2000000,
        $timeout,
        array('Accept: application/json')
    );
    $json = json_decode($result['body'], true);
    if (!is_array($json)) {
        throw new RuntimeException('Invalid SerpApi response.');
    }
    return dotbright_enrichment_serpapi_response($json);
}

function dotbright_enrichment_search(string $query, array $config): array
{
    if (($config['search_provider'] ?? '') === 'google') {
        return dotbright_enrichment_search_google(
            $query,
            (string)($config['google_api_key'] ?? ''),
            (string)($config['google_cx'] ?? ''),
            (int)$config['request_timeout']
        );
    }
    if (($config['search_provider'] ?? '') === 'brave') {
        return dotbright_enrichment_search_brave(
            $query,
            (string)($config['brave_api_key'] ?? ''),
            (int)$config['request_timeout']
        );
    }
    if (($config['search_provider'] ?? '') === 'serpapi') {
        return dotbright_enrichment_search_serpapi(
            $query,
            (string)($config['serpapi_api_key'] ?? ''),
            (int)$config['request_timeout']
        );
    }
    throw new RuntimeException('Unsupported search provider.');
}

function dotbright_enrichment_jsonld_nodes($value): array
{
    $nodes = array();
    if (!is_array($value)) {
        return $nodes;
    }
    $isProduct = false;
    $type = $value['@type'] ?? null;
    if (is_string($type)) {
        $isProduct = strcasecmp($type, 'Product') === 0;
    } elseif (is_array($type)) {
        foreach ($type as $candidate) {
            if (is_string($candidate) && strcasecmp($candidate, 'Product') === 0) {
                $isProduct = true;
                break;
            }
        }
    }
    if ($isProduct) {
        $nodes[] = $value;
    }
    foreach ($value as $child) {
        if (is_array($child)) {
            $nodes = array_merge($nodes, dotbright_enrichment_jsonld_nodes($child));
        }
    }
    return $nodes;
}

function dotbright_enrichment_meta(string $html, string $name): string
{
    $quoted = preg_quote($name, '~');
    $patterns = array(
        '~<meta[^>]+(?:property|name)=["\']' . $quoted . '["\'][^>]+content=["\']([^"\']+)["\'][^>]*>~i',
        '~<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']' . $quoted . '["\'][^>]*>~i',
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $match)) {
            return html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    return '';
}

function dotbright_enrichment_similarity(string $a, string $b): float
{
    $stop = array('GB', '5G', '4G', 'DUAL', 'SIM', 'SMARTPHONE', 'TELEMOVEL', 'TELEMOVEIS');
    $tokens = static function (string $value) use ($stop): array {
        $parts = array_values(array_filter(explode(' ', dotbright_enrichment_normalize($value)), static fn($v) => strlen($v) >= 2));
        return array_values(array_diff(array_unique($parts), $stop));
    };
    $left = $tokens($a);
    $right = $tokens($b);
    if (!$left || !$right) {
        return 0.0;
    }
    $intersection = count(array_intersect($left, $right));
    return (2.0 * $intersection) / (count($left) + count($right));
}

function dotbright_enrichment_extract_product(string $html, string $pageUrl, string $gtin, string $expectedTitle): array
{
    $nodes = array();
    if (preg_match_all('~<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is', $html, $matches)) {
        foreach ($matches[1] as $raw) {
            $json = json_decode(html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8'), true);
            if (is_array($json)) {
                $nodes = array_merge($nodes, dotbright_enrichment_jsonld_nodes($json));
            }
        }
    }
    $best = array();
    $bestScore = -1.0;
    foreach ($nodes as $node) {
        $nodeGtins = array();
        foreach (array('gtin', 'gtin8', 'gtin12', 'gtin13', 'gtin14') as $field) {
            if (isset($node[$field])) {
                foreach ((array)$node[$field] as $value) {
                    $candidate = dotbright_enrichment_gtin((string)$value);
                    if ($candidate !== null) {
                        $nodeGtins[] = $candidate;
                    }
                }
            }
        }
        $exact = in_array($gtin, $nodeGtins, true);
        $similarity = dotbright_enrichment_similarity($expectedTitle, (string)($node['name'] ?? ''));
        $score = ($exact ? 2.0 : 0.0) + $similarity;
        if ($score > $bestScore) {
            $bestScore = $score;
            $best = $node;
        }
    }

    $title = trim((string)($best['name'] ?? dotbright_enrichment_meta($html, 'og:title')));
    if ($title === '' && preg_match('~<title[^>]*>(.*?)</title>~is', $html, $titleMatch)) {
        $title = trim(html_entity_decode(strip_tags($titleMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
    $description = trim(strip_tags((string)($best['description'] ?? dotbright_enrichment_meta($html, 'description'))));
    if ($description === '') {
        $description = trim(strip_tags(dotbright_enrichment_meta($html, 'og:description')));
    }
    $images = array();
    foreach ((array)($best['image'] ?? array()) as $image) {
        if (is_array($image)) {
            $image = $image['url'] ?? ($image['contentUrl'] ?? '');
        }
        if (is_string($image) && $image !== '') {
            $resolvedImage = dotbright_enrichment_resolve_url($pageUrl, html_entity_decode($image, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (dotbright_enrichment_product_image_url($resolvedImage)) {
                $images[] = $resolvedImage;
            }
        }
    }
    $ogImage = dotbright_enrichment_meta($html, 'og:image');
    if ($ogImage !== '') {
        $resolvedImage = dotbright_enrichment_resolve_url($pageUrl, $ogImage);
        if (dotbright_enrichment_product_image_url($resolvedImage)) {
            $images[] = $resolvedImage;
        }
    }
    $specs = array();
    foreach ((array)($best['additionalProperty'] ?? array()) as $property) {
        if (is_array($property) && !empty($property['name']) && isset($property['value'])) {
            $specs[] = array('name' => trim(strip_tags((string)$property['name'])), 'value' => trim(strip_tags((string)$property['value'])));
        }
    }
    $exactGtin = false;
    foreach (array('gtin', 'gtin8', 'gtin12', 'gtin13', 'gtin14') as $field) {
        foreach ((array)($best[$field] ?? array()) as $value) {
            if (dotbright_enrichment_gtin((string)$value) === $gtin) {
                $exactGtin = true;
            }
        }
    }
    if (!$exactGtin) {
        $exactGtin = (bool)preg_match('/(?<!\d)' . preg_quote($gtin, '/') . '(?!\d)/', $html);
    }
    return array(
        'title' => $title,
        'description' => $description,
        'images' => array_values(array_unique($images)),
        'specs' => $specs,
        'exact_gtin' => $exactGtin,
        'title_similarity' => dotbright_enrichment_similarity($expectedTitle, $title),
    );
}

function dotbright_enrichment_descendants(PDO $db, array $roots): array
{
    $rows = $db->query('SELECT ID, ID_ORG, Download FROM products_families')->fetchAll(PDO::FETCH_ASSOC);
    $children = array();
    foreach ($rows as $row) {
        $children[(int)$row['ID_ORG']][] = (int)$row['ID'];
    }
    $result = array_values(array_unique(array_map('intval', $roots)));
    for ($i = 0; $i < count($result); $i++) {
        foreach (($children[$result[$i]] ?? array()) as $child) {
            if (!in_array($child, $result, true)) {
                $result[] = $child;
            }
        }
    }
    return $result;
}

function dotbright_enrichment_candidates(PDO $db, array $config, int $limit, ?int $productId = null, bool $onlyActionableQueue = false): array
{
    $families = dotbright_enrichment_descendants($db, (array)$config['target_family_roots']);
    if (!$families) {
        return array();
    }
    $placeholders = implode(',', array_fill(0, count($families), '?'));
    $sql = "SELECT p.ID, p.Ref, p.EAN, p.Ref_Ean, p.usado, p.Aproved,
                   pl.Cabecalho, pl.Texto1, pl.Texto3, bl.Nome AS Marca,
                   MAX(COALESCE(po.obj_stock, 0)) AS Stock,
                   GROUP_CONCAT(DISTINCT CONCAT(pi.Ordem, ':', pi.img_ext) ORDER BY pi.Ordem SEPARATOR ',') AS Images
            FROM products p
            INNER JOIN products_lang pl ON pl.ID_PAI=p.ID AND pl.Lang=?
            INNER JOIN brands_lang bl ON bl.ID_PAI=p.ID_Marca AND bl.Lang=?
            INNER JOIN products_families_obj pfo ON pfo.ID_PROD=p.ID
            INNER JOIN products_families pf ON pf.ID=pfo.ID_ORG
            LEFT JOIN products_objs po ON po.obj_type_id=p.ID AND po.obj_type='products' AND po.obj_ativo=1
            LEFT JOIN products_images pi ON pi.ID_ORG=p.ID
            LEFT JOIN product_enrichment_queue peq ON peq.product_id=p.ID
            WHERE p.Aproved=1 AND COALESCE(p.usado,0)=0 AND pf.Download=0
              AND p.Ref NOT LIKE 'REP%' AND pfo.ID_ORG IN ($placeholders)";
    $params = array((string)$config['language'], (string)$config['language']);
    $params = array_merge($params, $families);
    if ($productId !== null) {
        $sql .= ' AND p.ID=?';
        $params[] = $productId;
    } elseif ($onlyActionableQueue) {
        $sql .= " AND (peq.product_id IS NULL OR peq.status='pending'
                  OR (peq.status='error' AND peq.attempts < ?))";
        $params[] = max(1, (int)($config['max_discovery_attempts'] ?? 2));
    }
    $sql .= ' GROUP BY p.ID, p.Ref, p.EAN, p.Ref_Ean, p.usado, p.Aproved, pl.Cabecalho, pl.Texto1, pl.Texto3, bl.Nome, peq.product_id, peq.status
              ORDER BY (peq.product_id IS NULL) DESC, (MAX(COALESCE(po.obj_stock,0)) > 0) DESC, p.ID DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $root = dotbright_enrichment_site_root();
    $candidates = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hasPhysicalImage = false;
        foreach (array_filter(explode(',', (string)$row['Images'])) as $image) {
            [$order, $extension] = array_pad(explode(':', $image, 2), 2, '');
            if ($order !== '' && preg_match('/^[a-z0-9]{2,5}$/i', $extension)
                && is_file($root . '/files/products/' . (int)$row['ID'] . '_' . (int)$order . '.' . strtolower($extension))) {
                $hasPhysicalImage = true;
                break;
            }
        }
        $row['missing_image'] = !$hasPhysicalImage;
        $row['missing_description'] = trim(strip_tags((string)$row['Texto1'])) === '';
        $row['gtin'] = dotbright_enrichment_pick_gtin($row);
        if (($row['missing_image'] || $row['missing_description']) && $row['gtin'] !== null) {
            $candidates[] = $row;
            if (count($candidates) >= $limit) {
                break;
            }
        }
    }
    return $candidates;
}

function dotbright_enrichment_upsert_candidate(PDO $db, array $product): void
{
    $sql = "INSERT INTO product_enrichment_queue
                (product_id, gtin, brand_name, product_title, needs_image, needs_text, status, created_at, updated_at)
            VALUES (:product_id, :gtin, :brand, :title, :needs_image, :needs_text, 'pending', NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                gtin=VALUES(gtin), brand_name=VALUES(brand_name), product_title=VALUES(product_title),
                needs_image=VALUES(needs_image), needs_text=VALUES(needs_text),
                status=IF(status IN ('applied','approved','review'), status, 'pending'), updated_at=NOW()";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(
        ':product_id' => (int)$product['ID'],
        ':gtin' => $product['gtin'],
        ':brand' => (string)$product['Marca'],
        ':title' => (string)$product['Cabecalho'],
        ':needs_image' => $product['missing_image'] ? 1 : 0,
        ':needs_text' => $product['missing_description'] ? 1 : 0,
    ));
}

function dotbright_enrichment_discover(PDO $db, array $config, array $product): array
{
    $brandKey = dotbright_enrichment_normalize((string)$product['Marca']);
    $domains = $config['official_domains'][$brandKey] ?? array();
    if (!$domains) {
        throw new RuntimeException('No official-domain allowlist for brand ' . $product['Marca']);
    }
    $siteQuery = implode(' OR ', array_map(static fn($d) => 'site:' . $d, $domains));
    $query = $siteQuery . ' "' . $product['gtin'] . '"';
    $urls = dotbright_enrichment_search($query, $config);
    $urls = array_values(array_filter($urls, static function ($url) use ($domains): bool {
        $host = (string)(parse_url((string)$url, PHP_URL_HOST) ?: '');
        return dotbright_enrichment_host_allowed($host, $domains);
    }));
    if (!$urls) {
        $query = $siteQuery . ' ' . trim((string)$product['Cabecalho']);
        $urls = dotbright_enrichment_search($query, $config);
        $urls = array_values(array_filter($urls, static function ($url) use ($domains): bool {
            $host = (string)(parse_url((string)$url, PHP_URL_HOST) ?: '');
            return dotbright_enrichment_host_allowed($host, $domains);
        }));
    }
    $best = null;
    foreach ($urls as $url) {
        $host = (string)(parse_url($url, PHP_URL_HOST) ?: '');
        if (!dotbright_enrichment_host_allowed($host, $domains)) {
            continue;
        }
        try {
            $page = dotbright_enrichment_http_get($url, $domains, (int)$config['max_page_bytes'], (int)$config['request_timeout']);
            $extracted = dotbright_enrichment_extract_product($page['body'], $page['url'], $product['gtin'], (string)$product['Cabecalho']);
            $confidence = $extracted['exact_gtin'] ? 100 : (int)round($extracted['title_similarity'] * 85);
            if ($confidence < 55 || (!$extracted['images'] && $extracted['description'] === '')) {
                continue;
            }
            $proposal = array_merge($extracted, array(
                'source_url' => $page['url'],
                'source_host' => (string)parse_url($page['url'], PHP_URL_HOST),
                'confidence' => $confidence,
                'match_reason' => $extracted['exact_gtin'] ? 'exact_gtin_on_official_page' : 'official_domain_title_match',
            ));
            if ($best === null || $proposal['confidence'] > $best['confidence']) {
                $best = $proposal;
            }
            if ($confidence === 100) {
                break;
            }
        } catch (Throwable $e) {
            // Continue to another official result; the final queue row retains the outcome.
        }
    }
    if ($best === null) {
        throw new RuntimeException('No sufficiently reliable official product page found.');
    }
    $status = ($best['confidence'] === 100 && !empty($config['auto_approve_exact_gtin'])) ? 'approved' : 'review';
    $stmt = $db->prepare("UPDATE product_enrichment_queue SET
        source_url=:source_url, source_host=:source_host, proposed_title=:proposed_title,
        proposed_description=:proposed_description, proposed_specs=:proposed_specs,
        proposed_images=:proposed_images, confidence=:confidence, match_reason=:match_reason,
        status=:status, attempts=attempts+1, last_error=NULL, discovered_at=NOW(), updated_at=NOW()
        WHERE product_id=:product_id");
    $stmt->execute(array(
        ':source_url' => $best['source_url'],
        ':source_host' => $best['source_host'],
        ':proposed_title' => $best['title'],
        ':proposed_description' => $best['description'],
        ':proposed_specs' => json_encode($best['specs'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ':proposed_images' => json_encode(array_slice($best['images'], 0, (int)$config['max_images']), JSON_UNESCAPED_SLASHES),
        ':confidence' => $best['confidence'],
        ':match_reason' => $best['match_reason'],
        ':status' => $status,
        ':product_id' => (int)$product['ID'],
    ));
    return $best;
}

function dotbright_enrichment_download_image(string $url, array $domains, array $config): array
{
    $response = dotbright_enrichment_http_get($url, $domains, (int)$config['max_image_bytes'], (int)$config['request_timeout'], array('Accept: image/*'));
    $info = @getimagesizefromstring($response['body']);
    if (!is_array($info) || $info[0] < (int)$config['min_image_width'] || $info[1] < (int)$config['min_image_height']) {
        throw new RuntimeException('Image is invalid or below the minimum dimensions.');
    }
    if ((int)$info[0] * (int)$info[1] > 40000000) {
        throw new RuntimeException('Image pixel count is too large.');
    }
    $source = @imagecreatefromstring($response['body']);
    if (!$source) {
        throw new RuntimeException('GD could not decode the image.');
    }
    $canvas = imagecreatetruecolor(imagesx($source), imagesy($source));
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagecopy($canvas, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));
    ob_start();
    imagejpeg($canvas, null, 90);
    $jpeg = (string)ob_get_clean();
    imagedestroy($canvas);
    imagedestroy($source);
    return array('bytes' => $jpeg, 'extension' => 'jpg', 'sha256' => hash('sha256', $jpeg));
}

function dotbright_enrichment_apply(PDO $db, array $config, int $productId, bool $applyText, bool $appendImages): array
{
    $stmt = $db->prepare("SELECT * FROM product_enrichment_queue WHERE product_id=? AND status='approved'");
    $stmt->execute(array($productId));
    $queue = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$queue) {
        throw new RuntimeException('Product proposal is not approved.');
    }
    $brandKey = dotbright_enrichment_normalize((string)$queue['brand_name']);
    $domains = $config['official_domains'][$brandKey] ?? array();
    if (!$domains || !dotbright_enrichment_safe_url((string)$queue['source_url'], $domains)) {
        throw new RuntimeException('Proposal source is no longer allowed.');
    }
    $images = json_decode((string)$queue['proposed_images'], true);
    if (!is_array($images)) {
        $images = array();
    }
    $existing = $db->prepare('SELECT Ordem, img_ext FROM products_images WHERE ID_ORG=? ORDER BY Ordem');
    $existing->execute(array($productId));
    $existingRows = $existing->fetchAll(PDO::FETCH_ASSOC);
    if ($existingRows && !$appendImages) {
        $images = array();
    }
    $nextOrder = 1;
    foreach ($existingRows as $row) {
        $nextOrder = max($nextOrder, (int)$row['Ordem'] + 1);
    }
    $created = array();
    $root = dotbright_enrichment_site_root();
    $db->beginTransaction();
    try {
        foreach (array_slice($images, 0, max(0, (int)$config['max_images'] - count($existingRows))) as $url) {
            if (!is_string($url)
                || !dotbright_enrichment_product_image_url($url)
                || !dotbright_enrichment_safe_url($url, $domains)) {
                continue;
            }
            $download = dotbright_enrichment_download_image($url, $domains, $config);
            $file = $root . '/files/products/' . $productId . '_' . $nextOrder . '.jpg';
            $tmp = $file . '.tmp-' . bin2hex(random_bytes(4));
            if (file_put_contents($tmp, $download['bytes'], LOCK_EX) !== strlen($download['bytes']) || !rename($tmp, $file)) {
                @unlink($tmp);
                throw new RuntimeException('Unable to store product image.');
            }
            chmod($file, 0644);
            $insert = $db->prepare('INSERT INTO products_images (ID_ORG, img_ext, Ordem, URL_Source) VALUES (?, ?, ?, ?)');
            $insert->execute(array($productId, 'jpg', $nextOrder, $url));
            $created[] = $file;
            $nextOrder++;
        }
        $textChanged = false;
        if ($applyText) {
            $lang = (string)$config['language'];
            $current = $db->prepare('SELECT Texto1, Texto3 FROM products_lang WHERE ID_PAI=? AND Lang=? FOR UPDATE');
            $current->execute(array($productId, $lang));
            $row = $current->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $text1 = trim(strip_tags((string)$row['Texto1']));
                $text3 = trim(strip_tags((string)$row['Texto3']));
                $updates = array();
                $params = array();
                if ($text1 === '' && trim((string)$queue['proposed_description']) !== '') {
                    $updates[] = 'Texto1=?';
                    $params[] = nl2br(htmlspecialchars(trim((string)$queue['proposed_description']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'))
                        . '<br><strong>' . htmlspecialchars((string)$config['description_notice'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</strong>';
                }
                $specs = json_decode((string)$queue['proposed_specs'], true);
                if ($text3 === '' && $text1 === '' && is_array($specs) && $specs) {
                    $html = '<ul>';
                    foreach (array_slice($specs, 0, 60) as $spec) {
                        if (!empty($spec['name']) && isset($spec['value'])) {
                            $html .= '<li><strong>' . htmlspecialchars((string)$spec['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ':</strong> '
                                . htmlspecialchars((string)$spec['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
                        }
                    }
                    $updates[] = 'Texto3=?';
                    $params[] = $html . '</ul>';
                }
                if ($updates) {
                    $params[] = $productId;
                    $params[] = $lang;
                    $update = $db->prepare('UPDATE products_lang SET ' . implode(', ', $updates) . ' WHERE ID_PAI=? AND Lang=?');
                    $update->execute($params);
                    $textChanged = true;
                }
            }
        }
        $finish = $db->prepare("UPDATE product_enrichment_queue SET status='applied', applied_at=NOW(), updated_at=NOW(), last_error=NULL WHERE product_id=?");
        $finish->execute(array($productId));
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        foreach ($created as $file) {
            @unlink($file);
        }
        throw $e;
    }
    if (defined('IMG_SMALL') && defined('IMG_THUM')) {
        $small = @unserialize(IMG_SMALL);
        $thumb = @unserialize(IMG_THUM);
        foreach ($created as $file) {
            if (!preg_match('/_(\d+)\.jpg$/', $file, $match)) {
                continue;
            }
            $order = (int)$match[1];
            $original = 'files/products/' . $productId . '_' . $order . '.jpg';
            if (is_array($small) && !empty($small['ativo'])) {
                geraimagens($original, 'files/products/' . $productId . '_' . $order . '_small.png', (int)$small['w'], (int)$small['h'], (array)$small['crop'], (int)$small['m_trans'], (int)$small['c_white']);
            }
            if (is_array($thumb) && !empty($thumb['ativo'])) {
                geraimagens($original, 'files/products/' . $productId . '_' . $order . '_thum.png', (int)$thumb['w'], (int)$thumb['h'], (array)$thumb['crop'], (int)$thumb['m_trans'], (int)$thumb['c_white']);
            }
        }
    }
    $cachePatterns = array(
        $root . '/files/cache/p' . $productId . '_*.bin',
        $root . '/files/cache/products/p' . $productId . '_*.bin',
    );
    foreach ($cachePatterns as $pattern) {
        foreach (glob($pattern) ?: array() as $cache) {
            @unlink($cache);
        }
    }
    if (!empty($textChanged)) {
        $db->prepare('DELETE FROM acelerador_pesquisa WHERE ID_Prod=?')->execute(array($productId));
    }
    return array('images_added' => count($created), 'text_changed' => !empty($textChanged));
}

function dotbright_enrichment_log(array $config, array $event): void
{
    $directory = rtrim((string)$config['private_dir'], '/');
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create private enrichment directory.');
    }
    $event['at'] = gmdate('c');
    file_put_contents($directory . '/catalog-enrichment-' . gmdate('Ymd') . '.jsonl', json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
}
