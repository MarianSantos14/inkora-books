<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
if (!staff()) show_error_page(403);

function search_catalog(string $url): array
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_USERAGENT => 'EstanteVirtual/1.0 (book catalog search)',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);
    } else {
        $context = stream_context_create(['http' => [
            'timeout' => 15,
            'ignore_errors' => true,
            'header' => "User-Agent: EstanteVirtual/1.0 (book catalog search)\r\nAccept: application/json\r\n",
        ]]);
        $body = @file_get_contents($url, false, $context);
        $status = 0;
        $error = '';
        foreach (($http_response_header ?? []) as $header) {
            if (preg_match('~^HTTP/\S+\s+(\d+)~', $header, $match)) $status = (int)$match[1];
        }
        if (!is_string($body)) $error = error_get_last()['message'] ?? 'A consulta externa falhou.';
    }
    $raw = is_string($body) ? $body : '';
    $json = json_decode($raw, true);
    return ['status' => $status, 'error' => $error, 'json' => is_array($json) ? $json : null];
}

function equivalent_isbns(string $isbn): array
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

function search_rate_limit(PDO $pdo): void
{
    $pdo->exec('INSERT IGNORE INTO isbn_api_state (id, last_request_at) VALUES (1, NULL)');
    $pdo->beginTransaction();
    $state = $pdo->query('SELECT GREATEST(0, 1.05 - TIMESTAMPDIFF(MICROSECOND, last_request_at, NOW(6)) / 1000000) AS wait_seconds FROM isbn_api_state WHERE id = 1 FOR UPDATE')->fetch();
    $waitSeconds = (float)($state['wait_seconds'] ?? 0);
    if ($waitSeconds > 0) usleep((int)ceil($waitSeconds * 1000000));
    $pdo->exec('UPDATE isbn_api_state SET last_request_at = NOW(6) WHERE id = 1');
    $pdo->commit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $title = trim((string)($_POST['titulo'] ?? ''));
    if ($title === '') {
        flash('Esse resultado não tem título para cadastrar.', 'error');
        go('/inkora-books/livros/buscar.php');
    }
    $_SESSION['book_import_draft'] = [
        'isbn' => strtoupper(preg_replace('/[^0-9Xx]/', '', (string)($_POST['isbn'] ?? ''))),
        'titulo' => $title,
        'autor' => trim((string)($_POST['autor'] ?? '')),
        'editora' => trim((string)($_POST['editora'] ?? '')),
        'ano_publicacao' => trim((string)($_POST['ano_publicacao'] ?? '')),
        'numero_paginas' => trim((string)($_POST['numero_paginas'] ?? '')),
        'descricao' => trim((string)($_POST['descricao'] ?? '')),
        'capa' => filter_var((string)($_POST['capa'] ?? ''), FILTER_VALIDATE_URL) ? (string)$_POST['capa'] : '',
    ];
    go('/inkora-books/livros/form.php');
}

$type = (string)($_GET['tipo'] ?? 'titulo');
if (!in_array($type, ['titulo', 'autor', 'isbn'], true)) $type = 'titulo';
$term = trim((string)($_GET['q'] ?? ''));
$results = [];
$errorMessage = '';
$pdo = null;

if ($term !== '') {
    $query = '';
    $wantedIsbns = [];
    if ($type === 'isbn') {
        $isbn = strtoupper(preg_replace('/[^0-9Xx]/', '', $term));
        if (!preg_match('/^(?:\d{9}[\dX]|\d{13})$/', $isbn)) {
            $errorMessage = 'Digite um ISBN-10 ou ISBN-13.';
        } else {
            $wantedIsbns = equivalent_isbns($isbn);
            $query = 'isbn:(' . implode(' OR ', $wantedIsbns) . ')';
        }
    } else {
        $field = $type === 'autor' ? 'author' : 'title';
        $phrase = addcslashes($term, "\\\"");
        $query = $field . ':"' . $phrase . '"';
    }

    if ($query !== '') {
        try {
            $pdo = db();
            search_rate_limit($pdo);
            $url = 'https://openlibrary.org/search.json?' . http_build_query([
                'q' => $query,
                'fields' => 'isbn,title,author_name,publisher,first_publish_year,number_of_pages_median,first_sentence,cover_i',
                'limit' => 24,
            ]);
            $response = search_catalog($url);
            if ($response['status'] === 429) {
                $errorMessage = 'A Open Library pediu para aguardar. Tente novamente em alguns segundos.';
            } elseif ($response['status'] < 200 || $response['status'] >= 300 || !$response['json']) {
                $errorMessage = 'Não foi possível consultar a Open Library agora. Tente novamente mais tarde.';
            } else {
                foreach (($response['json']['docs'] ?? []) as $doc) {
                    $docIsbns = array_map(static fn($value) => strtoupper(preg_replace('/[^0-9X]/i', '', (string)$value)), $doc['isbn'] ?? []);
                    if ($wantedIsbns && !array_intersect($wantedIsbns, $docIsbns)) continue;
                    $title = trim((string)($doc['title'] ?? ''));
                    if ($title === '') continue;
                    $isbn = '';
                    if ($wantedIsbns) {
                        foreach ($wantedIsbns as $wanted) {
                            if (in_array($wanted, $docIsbns, true)) {
                                $isbn = $wanted;
                                break;
                            }
                        }
                    }
                    if ($isbn === '') {
                        foreach ($docIsbns as $candidate) {
                            if (preg_match('/^(?:\d{9}[\dX]|\d{13})$/', $candidate)) {
                                $isbn = $candidate;
                                break;
                            }
                        }
                    }
                    $coverId = $doc['cover_i'] ?? null;
                    $firstSentence = $doc['first_sentence'] ?? '';
                    if (is_array($firstSentence)) {
                        $firstSentence = array_is_list($firstSentence)
                            ? ($firstSentence[0] ?? '')
                            : ($firstSentence['value'] ?? $firstSentence['text'] ?? '');
                    }
                    if (is_array($firstSentence)) $firstSentence = $firstSentence['value'] ?? $firstSentence['text'] ?? '';
                    $results[] = [
                        'isbn' => $isbn,
                        'titulo' => $title,
                        'autor' => is_array($doc['author_name'] ?? null) ? (string)($doc['author_name'][0] ?? '') : (string)($doc['author_name'] ?? ''),
                        'editora' => is_array($doc['publisher'] ?? null) ? (string)($doc['publisher'][0] ?? '') : (string)($doc['publisher'] ?? ''),
                        'ano_publicacao' => is_numeric($doc['first_publish_year'] ?? null) ? (string)$doc['first_publish_year'] : '',
                        'numero_paginas' => is_numeric($doc['number_of_pages_median'] ?? null) ? (string)$doc['number_of_pages_median'] : '',
                        'descricao' => (string)$firstSentence,
                        'capa' => $coverId ? 'https://covers.openlibrary.org/b/id/' . rawurlencode((string)$coverId) . '-L.jpg?default=false' : '',
                    ];
                }
            }
        } catch (Throwable $exception) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
            $errorMessage = 'Não foi possível pesquisar agora. Verifique o banco e a conexão com a internet.';
        }
    }
}

$pageTitle = 'Procurar livros';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">NOVO CADASTRO</div>
        <h1>Encontre um livro</h1>
        <p>Pesquise pelo título, autor ou ISBN. Escolha uma edição para revisar e completar os dados.</p>
    </div>
    <a class="text-link" href="/inkora-books/livros/form.php">Preencher manualmente →</a>
</section>

<form class="editor catalog-search-form" method="get" action="/inkora-books/livros/buscar.php">
    <label for="catalog-query">Pesquisar na Open Library</label>
    <div class="catalog-search-controls">
        <select name="tipo" aria-label="Campo da pesquisa">
            <option value="titulo" <?= $type === 'titulo' ? 'selected' : '' ?>>Título</option>
            <option value="isbn" <?= $type === 'isbn' ? 'selected' : '' ?>>ISBN</option>
            <option value="autor" <?= $type === 'autor' ? 'selected' : '' ?>>Autor</option>
        </select>
        <input id="catalog-query" name="q" value="<?= e($term) ?>" placeholder="Digite o título, ISBN ou autor" required>
        <button class="button" type="submit">Pesquisar</button>
    </div>
    <?php if ($errorMessage): ?><p class="catalog-search-message error"><?= e($errorMessage) ?></p><?php endif; ?>
    <?php if ($term !== '' && !$errorMessage && !$results): ?><p class="catalog-search-message">Nenhum resultado encontrado. Tente outros termos ou preencha o livro manualmente.</p><?php endif; ?>
</form>

<?php if ($results): ?>
    <div class="catalog-search-summary"><?= count($results) ?> resultados para “<?= e($term) ?>”</div>
    <section class="catalog-search-results">
        <?php foreach ($results as $result): ?>
            <article class="catalog-result">
                <?php if ($result['capa']): ?>
                    <img class="catalog-result-cover" src="<?= e($result['capa']) ?>" alt="Capa de <?= e($result['titulo']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="catalog-result-cover catalog-result-no-cover" aria-label="Livro sem capa"><span>Sem capa</span></div>
                <?php endif; ?>
                <div class="catalog-result-info">
                    <h2><?= e($result['titulo']) ?></h2>
                    <p><?= e($result['autor'] ?: 'Autor não informado') ?></p>
                    <div class="catalog-result-meta">
                        <?php if ($result['editora']): ?><span><?= e($result['editora']) ?></span><?php endif; ?>
                        <?php if ($result['ano_publicacao']): ?><span><?= e($result['ano_publicacao']) ?></span><?php endif; ?>
                        <?php if ($result['numero_paginas']): ?><span><?= e($result['numero_paginas']) ?> páginas</span><?php endif; ?>
                        <?php if ($result['isbn']): ?><span>ISBN <?= e($result['isbn']) ?></span><?php endif; ?>
                    </div>
                    <?php if ($result['descricao']): ?><p class="catalog-result-description"><?= e($result['descricao']) ?></p><?php endif; ?>
                    <form method="post" action="/inkora-books/livros/buscar.php">
                        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                        <?php foreach ($result as $key => $value): ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>"><?php endforeach; ?>
                        <button class="button button-small" type="submit">Escolher e revisar dados <span>→</span></button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>