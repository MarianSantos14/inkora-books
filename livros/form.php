<?php require_once __DIR__ . '/../includes/functions.php';
require_login();
if (!staff()) show_error_page(403);
$pdo = db();
$idEdit = (int)($_GET['id'] ?? 0);
$book = null;
$hasImportDraft = false;
if ($idEdit) {
    $s = $pdo->prepare('SELECT * FROM livro WHERE id=?');
    $s->execute([$idEdit]);
    $book = $s->fetch();
    if (!$book) {
        show_error_page(404);
    }
    if (!can_manage_book($book['usuario_id'] === null ? null : (int)$book['usuario_id'])) {
        show_error_page(403);
    }
} elseif (isset($_SESSION['book_import_draft']) && is_array($_SESSION['book_import_draft'])) {
    $hasImportDraft = true;
    $draft = $_SESSION['book_import_draft'];
    unset($_SESSION['book_import_draft']);
    $book = [
        'isbn' => $draft['isbn'] ?? '',
        'titulo' => $draft['titulo'] ?? '',
        'autor' => $draft['autor'] ?? '',
        'editora' => $draft['editora'] ?? '',
        'ano_publicacao' => $draft['ano_publicacao'] ?? '',
        'numero_paginas' => $draft['numero_paginas'] ?? '',
        'descricao' => $draft['descricao'] ?? '',
        'capa' => $draft['capa'] ?? '',
    ];
}
$pageTitle = $idEdit ? 'Editar livro' : 'Adicionar livro';
require __DIR__ . '/../includes/header.php'; ?>
<section class="page-heading">
    <div>
        <div class="eyebrow">CATÁLOGO</div>
        <h1><?= $idEdit ? 'Editar livro' : ($hasImportDraft ? 'Revisar dados do livro' : 'Adicionar um livro') ?></h1>
        <p><?= $hasImportDraft ? 'Confira e ajuste as informações antes de adicionar o livro ao catálogo.' : 'Preencha os dados do livro para adicioná-lo ao catálogo.' ?></p>
    </div>
</section>
<form id="book-form" class="editor" method="post" enctype="multipart/form-data" action="/inkora-books/actions.php">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="save_book"><input type="hidden" name="id" value="<?= $book["id"] ?? "" ?>">
    <div class="form-grid two">
        <?php $coverValue = $book['capa'] ?? null;
        require __DIR__ . '/../includes/cover_picker.php'; ?>
        <label>Título<input required name="titulo" value="<?= e($book['titulo'] ?? '') ?>"></label>
        <label>Autor<input required name="autor" value="<?= e($book['autor'] ?? '') ?>"></label>
        <label>Editora<input name="editora" value="<?= e($book['editora'] ?? '') ?>"></label>
        <label>Ano de publicação<input type="number" min="1000" max="9999" name="ano_publicacao" value="<?= e((string)($book['ano_publicacao'] ?? '')) ?>"></label>
        <label>Número de páginas<input type="number" min="1" name="numero_paginas" value="<?= e((string)($book['numero_paginas'] ?? '')) ?>"></label>
        <label class="wide">Sinopse<textarea name="descricao" rows="5"><?= e($book['descricao'] ?? '') ?></textarea></label>
    </div>
    <details class="isbn-import" <?= $hasImportDraft ? 'open' : '' ?>>
        <summary>Preencher dados automaticamente pelo ISBN (opcional)</summary>
        <p>Preencha o formulário manualmente ou informe um ISBN para buscar dados e completar os campos.</p>
        <div class="isbn-lookup">
            <label>ISBN<input id="isbn" name="isbn" value="<?= e($book['isbn'] ?? '') ?>" placeholder="978..." inputmode="numeric"></label>
            <button type="button" class="button" id="lookup-isbn">Buscar dados do livro</button>
            <span id="isbn-result" aria-live="polite"></span>
        </div>
        <a class="text-link isbn-search-link" href="/inkora-books/livros/buscar.php">Ou pesquisar por título ou autor</a>
    </details>
    <div class="form-actions">
        <button class="button">Salvar livro <span>→</span></button>
        <a class="text-link" href="/inkora-books/livros/">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>