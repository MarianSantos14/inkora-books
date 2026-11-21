<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

function isbn_request(string $url): array
{
    $headers = "User-Agent: EstanteVirtual/1.0 (ISBN metadata lookup)\r\nAccept: application/json\r\n";
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'EstanteVirtual/1.0 (ISBN metadata lookup)',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http' => ['timeout' => 15, 'header' => $headers, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        $status = 0;
        $error = '';
        foreach (($http_response_header ?? []) as $header) {
            if (preg_match('~^HTTP/\S+\s+(\d+)~', $header, $match)) $status = (int)$match[1];
        }
        if (!is_string($body)) $error = error_get_last()['message'] ?? 'A chamada externa falhou.';
    }

    $raw = is_string($body) ? $body : '';
    $decoded = json_decode($raw, true);
    return ['status' => $status, 'json' => is_array($decoded) ? $decoded : null, 'raw' => $raw, 'error' => $error];
}

function isbn_equivalents(string $isbn): array
{
    $values = [$isbn];
    if (strlen($isbn) === 13 && str_starts_with($isbn, '978')) {
        $base = substr($isbn, 3, 9);
        $sum = 0;
        for ($i = 0; $i < 9; $i++) $sum += (int)$base[$i] * (10 - $i);
        $check = (11 - ($sum % 11)) % 11;
        $values[] = $base . ($check === 10 ? 'X' : (string)$check);
    } elseif (strlen($isbn) === 10) {
        $base = '978' . substr($isbn, 0, 9);
        $sum = 0;
        for ($i = 0; $i < 12; $i++) $sum += (int)$base[$i] * ($i % 2 === 0 ? 1 : 3);
        $values[] = $base . ((10 - ($sum % 10)) % 10);
    }
    return array_values(array_unique($values));
}

function normalize_synopsis(mixed $value): string
{
    if (is_array($value)) {
        $value = $value[0] ?? '';
        if (is_array($value)) $value = $value['value'] ?? $value['text'] ?? '';
    }
    $text = str_replace(["\r\n", "\r"], "\n", trim((string)$value));
    return preg_replace('/\n(?:[ \t]*\n){2,}/', "\n\n", $text) ?? $text;
}

function normalize_open_library(array $response, array $isbns): array
{
    $docs = $response['json']['docs'] ?? [];
    foreach ($docs as $doc) {
        $docIsbns = array_map(static fn($value) => strtoupper(preg_replace('/[^0-9X]/i', '', (string)$value)), $doc['isbn'] ?? []);
        if (!$docIsbns || !array_intersect($isbns, $docIsbns)) continue;

        $title = trim((string)($doc['title'] ?? ''));
        if ($title === '') continue;

        $synopsis = normalize_synopsis($doc['first_sentence'] ?? '');
        $coverId = $doc['cover_i'] ?? null;
        $year = $doc['first_publish_year'] ?? null;
        $pages = $doc['number_of_pages_median'] ?? null;
        return [
            'isbn' => $isbns[0],
            'title' => $title,
            'author' => (string)(is_array($doc['author_name'] ?? null) ? ($doc['author_name'][0] ?? '') : ($doc['author_name'] ?? '')),
            'publisher' => (string)(is_array($doc['publisher'] ?? null) ? ($doc['publisher'][0] ?? '') : ($doc['publisher'] ?? '')),
            'year' => is_numeric($year) ? (int)$year : null,
            'page_count' => is_numeric($pages) ? (int)$pages : null,
            'synopsis' => $synopsis,
            'cover_url' => $coverId ? 'https://covers.openlibrary.org/b/id/' . rawurlencode((string)$coverId) . '-L.jpg?default=false' : '',
        ];
    }
    return [];
}

try {
    $isbn = strtoupper(preg_replace('/[^0-9Xx]/', '', (string)($_GET['isbn'] ?? '')));
    $valid = false;
    if (preg_match('/^\d{9}[\dX]$/', $isbn)) {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) $sum += ($isbn[$i] === 'X' ? 10 : (int)$isbn[$i]) * (10 - $i);
        $valid = $sum % 11 === 0;
    } elseif (preg_match('/^\d{13}$/', $isbn)) {
        $sum = 0;
        for ($i = 0; $i < 13; $i++) $sum += (int)$isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        $valid = $sum % 10 === 0;
    }
    if (!$valid) {
        http_response_code(422);
        echo json_encode(['error' => 'Digite um ISBN-10 ou ISBN-13 válido, incluindo o dígito verificador.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $pdo = db();
    $debug = ($_GET['debug'] ?? '') === '1';
    if (!$debug) {
        $cached = $pdo->prepare('SELECT payload FROM isbn_cache WHERE isbn = ? AND cached_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) LIMIT 1');
        $cached->execute([$isbn]);
        $payload = $cached->fetchColumn();
        if (is_string($payload)) {
            $book = json_decode($payload, true);
            if (is_array($book)) {
                $book['_meta'] = ['source' => 'Open Library', 'cached' => true];
                echo json_encode($book, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
            }
        }
    }

    $equivalents = isbn_equivalents($isbn);
    $query = 'isbn:(' . implode(' OR ', $equivalents) . ')';
    $url = 'https://openlibrary.org/search.json?' . http_build_query([
        'q' => $query,
        'fields' => 'isbn,title,author_name,publisher,first_publish_year,number_of_pages_median,first_sentence,cover_i',
        'limit' => 20,
    ]);

    // Serializa os misses de cache e mantém pelo menos 1s entre chamadas da aplicação.
    $pdo->exec('INSERT IGNORE INTO isbn_api_state (id, last_request_at) VALUES (1, NULL)');
    $pdo->beginTransaction();
    $state = $pdo->query('SELECT last_request_at, GREATEST(0, 1.05 - TIMESTAMPDIFF(MICROSECOND, last_request_at, NOW(6)) / 1000000) AS wait_seconds FROM isbn_api_state WHERE id = 1 FOR UPDATE')->fetch();
    $waitSeconds = (float)($state['wait_seconds'] ?? 0);
    if ($waitSeconds > 0) usleep((int)ceil($waitSeconds * 1000000));
    $pdo->exec('UPDATE isbn_api_state SET last_request_at = NOW(6) WHERE id = 1');
    $pdo->commit();
    $apiResponse = isbn_request($url);

    if ($debug) {
        echo json_encode([
            'diagnostic' => true,
            'isbn' => $isbn,
            'sources' => [[
                'provider' => 'Open Library',
                'url' => $url,
                'http_status' => $apiResponse['status'],
                'transport_error' => $apiResponse['error'],
                'response' => $apiResponse['json'],
                'raw_body' => $apiResponse['raw'],
            ]],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($apiResponse['status'] === 429) {
        http_response_code(429);
        echo json_encode(['error' => 'A Open Library pediu para aguardar antes de uma nova consulta. Tente novamente em alguns segundos.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($apiResponse['status'] < 200 || $apiResponse['status'] >= 300 || !$apiResponse['json']) {
        http_response_code(502);
        echo json_encode(['error' => 'Não foi possível consultar a Open Library agora. Tente novamente mais tarde.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $book = normalize_open_library($apiResponse, $equivalents);
    if (!$book) {
        http_response_code(404);
        echo json_encode(['error' => 'Não encontramos esse ISBN na Open Library. Você pode preencher os dados manualmente.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $save = $pdo->prepare('INSERT INTO isbn_cache (isbn, payload, cached_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE payload = VALUES(payload), cached_at = NOW()');
    $save->execute([$isbn, json_encode($book, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    $book['_meta'] = [
        'source' => 'Open Library',
        'cached' => false,
        'diagnostic' => ['http_status' => $apiResponse['status'], 'response' => $apiResponse['json'], 'raw_body' => $apiResponse['raw']],
    ];
    echo json_encode($book, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha na consulta do catálogo. Importe a migration_v4.sql para criar o cache ISBN e tente novamente.'], JSON_UNESCAPED_UNICODE);
}
