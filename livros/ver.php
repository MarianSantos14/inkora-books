<?php
require_once __DIR__ . '/../includes/functions.php';
$bookId = (int)($_GET['id'] ?? 0);
$query = db()->prepare('SELECT * FROM livro WHERE id=?');
$query->execute([$bookId]);
$book = $query->fetch();
if (!$book) {
    show_error_page(404);
}
$ownerId = isset($book['usuario_id']) ? (int)$book['usuario_id'] : null;
$readerStatus = null;
$myRating = null;
if (($_SESSION['auth']['kind'] ?? '') === 'cliente') {
    $readerQuery = db()->prepare('SELECT status FROM estante WHERE cliente_id=? AND livro_id=?');
    $readerQuery->execute([id(), $bookId]);
    $readerStatus = $readerQuery->fetchColumn() ?: null;
    $myRatingQuery = db()->prepare('SELECT nota,comentario FROM avaliacao_livro WHERE cliente_id=? AND livro_id=?');
    $myRatingQuery->execute([id(), $bookId]);
    $myRating = $myRatingQuery->fetch() ?: null;
}
$reviewsQuery = db()->prepare('SELECT a.nota,a.comentario,a.criado_em,c.nome,c.foto FROM avaliacao_livro a JOIN cliente c ON c.id=a.cliente_id WHERE a.livro_id=? ORDER BY a.atualizado_em DESC');
$reviewsQuery->execute([$bookId]);
$reviews = $reviewsQuery->fetchAll();
$reviewSummaryQuery = db()->prepare('SELECT AVG(nota) AS media,COUNT(*) AS total FROM avaliacao_livro WHERE livro_id=?');
$reviewSummaryQuery->execute([$bookId]);
$reviewSummary = $reviewSummaryQuery->fetch();
$synopsisText = trim((string)($book['descricao'] ?? ''));
$synopsisParagraphs = $synopsisText === '' ? ['Sinopse não informada.'] : (preg_split('/\R+/u', $synopsisText, -1, PREG_SPLIT_NO_EMPTY) ?: [$synopsisText]);
$authorAccount = null;
$authorNames = preg_split('/\s*(?:,|;|&|\be\b)\s*/iu', (string)$book['autor'], -1, PREG_SPLIT_NO_EMPTY) ?: [];
if ($authorNames) {
    $authors = db()->query("SELECT id,nome,foto FROM usuario WHERE tipo='autor' ORDER BY nome")->fetchAll();
    foreach ($authors as $candidate) {
        foreach ($authorNames as $authorName) {
            if (mb_strtolower(trim($candidate['nome']), 'UTF-8') === mb_strtolower(trim($authorName), 'UTF-8')) {
                $authorAccount = $candidate;
                break 2;
            }
        }
    }
}
$readingCounts = null;
if (can_view_reading_counts($ownerId)) {
    $countsQuery = db()->prepare("SELECT SUM(status='lido') AS lidos,SUM(status='lendo') AS lendo,SUM(status='quero ler') AS quero_ler FROM estante WHERE livro_id=?");
    $countsQuery->execute([$bookId]);
    $readingCounts = $countsQuery->fetch();
}
$pageTitle = $book['titulo'];
require __DIR__ . '/../includes/header.php';
?>

<section class="page-heading book-detail-heading">
    <div>
        <div class="eyebrow">DETALHES DO LIVRO</div>
        <h1><?= e($book['titulo']) ?></h1>
        <p><?= e($book['autor']) ?></p>
    </div>
    <a class="text-link" href="<?= url('livros/') ?>">← Voltar ao catálogo</a>
</section>
<article class="book-detail">
    <div class="book-detail-cover">
        <?php if (!empty($book['capa'])): ?><img src="<?= e(image_url($book['capa'], '')) ?>" alt="Capa de <?= e($book['titulo']) ?>">
        <?php else: ?>
            <div class="book-cover-placeholder" aria-label="Livro sem capa">
                <svg viewBox="0 0 64 64" aria-hidden="true">
                    <path d="M14 7h37v50H14z"></path>
                    <path d="M22 8v48M29 17h15M29 23h15M29 29h15" fill="none" stroke="#e3e7ec" stroke-linecap="round" stroke-width="2"></path>
                </svg>
            </div>
        <?php endif; ?>
    </div>
    <div class="book-detail-content">
        <div class="eyebrow">FICHA DO LIVRO</div>
        <h2><?= e($book['titulo']) ?></h2>
        <p class="book-detail-author">Por <?php if ($authorAccount): ?><a class="text-link" href="<?= url('usuarios/ver.php') ?>?id=<?= $authorAccount['id'] ?>"><?= e($authorAccount['nome']) ?></a><?php else: ?><?= e($book['autor']) ?><?php endif; ?></p>
        <div class="book-detail-metadata">
            <?php if (!empty($book['isbn'])): ?><div><span>ISBN</span><strong><?= e($book['isbn']) ?></strong></div><?php endif; ?>
            <?php if (!empty($book['editora'])): ?><div><span>Editora</span><strong><?= e($book['editora']) ?></strong></div><?php endif; ?>
            <?php if (!empty($book['ano_publicacao'])): ?><div><span>Ano de publicação</span><strong><?= e((string)$book['ano_publicacao']) ?></strong></div><?php endif; ?>
            <?php if (!empty($book['numero_paginas'])): ?><div><span>Páginas</span><strong><?= e((string)$book['numero_paginas']) ?></strong></div><?php endif; ?>
        </div>
        <?php if (can_view_reading_counts($ownerId) && $readingCounts && ((int)$readingCounts['lidos'] + (int)$readingCounts['lendo'] + (int)$readingCounts['quero_ler']) > 0): ?>
            <div class="reading-counts book-detail-counts">
                <?php if ((int)$readingCounts['lidos'] > 0): ?><span><strong><?= (int)$readingCounts['lidos'] ?></strong> pessoas leram</span><?php endif; ?>
                <?php if ((int)$readingCounts['lendo'] > 0): ?><span><strong><?= (int)$readingCounts['lendo'] ?></strong> pessoas lendo</span><?php endif; ?>
                <?php if ((int)$readingCounts['quero_ler'] > 0): ?><span><strong><?= (int)$readingCounts['quero_ler'] ?></strong> pessoas querem ler</span><?php endif; ?>
            </div>
        <?php endif; ?>
        <section class="book-detail-synopsis">
            <h3>Sinopse</h3><?php foreach ($synopsisParagraphs as $paragraph): ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endforeach; ?>
        </section>
        <div class="book-detail-actions">
            <?php if (($_SESSION['auth']['kind'] ?? '') === 'cliente'): ?>
                <form method="post" action="<?= url('pages/actions.php') ?>"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="shelf"><input type="hidden" name="livro_id" value="<?= $bookId ?>"><input type="hidden" name="status" value="quero ler"><button class="book-detail-secondary">Adicionar à estante</button></form>
            <?php elseif (!logged()): ?><a class="button" href="<?= url('pages/login.php') ?>">Entre para guardar na estante</a><?php endif; ?>
            <?php if (can_manage_book($ownerId)): ?><a class="book-detail-secondary" href="<?= url('livros/form.php') ?>?id=<?= $bookId ?>">Editar livro</a>
                <form method="post" action="<?= url('pages/actions.php') ?>" data-confirm="Excluir este livro e sua capa local?"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete_book"><input type="hidden" name="id" value="<?= $bookId ?>"><button class="book-detail-delete">Excluir livro</button></form><?php endif; ?>
        </div>
    </div>
</article>
<section class="book-reviews" id="avaliacoes">
    <div class="section-head">
        <div>
            <div class="eyebrow">LEITORES DA COMUNIDADE</div>
            <h2>Avaliações</h2>
            <p>
                <?php if ((int)$reviewSummary['total'] > 0): ?>★ <?= number_format((float)$reviewSummary['media'], 1, ',', '.') ?> de 5 · <?= (int)$reviewSummary['total'] ?> <?= ((int)$reviewSummary['total'] === 1 ? 'avaliação' : 'avaliações') ?><?php else: ?>Ainda não há avaliações para este livro.<?php endif; ?>
            </p>
        </div>
    </div>
    <?php if (($_SESSION['auth']['kind'] ?? '') === 'cliente' && $readerStatus === 'lido'): ?>
        <form class="editor book-review-form" method="post" action="<?= url('pages/actions.php') ?>"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="rate_book"><input type="hidden" name="livro_id" value="<?= $bookId ?>">
            <h3><?= $myRating ? 'Atualize sua avaliação' : 'Como foi a leitura?' ?></h3>
            <div class="review-rating-field"><label for="review-rating">Sua nota</label><select required id="review-rating" name="nota">
                    <option value="">Escolha de 1 a 5 estrelas</option><?php for ($star = 5; $star >= 1; $star--): ?><option value="<?= $star ?>" <?= ((int)($myRating['nota'] ?? 0) === $star ? 'selected' : '') ?>><?= $star ?> <?= ($star === 1 ? 'estrela' : 'estrelas') ?></option><?php endfor; ?>
                </select>
            </div>
            <label class="review-comment-field">Comentário (opcional)<textarea name="comentario" maxlength="1500" rows="3" placeholder="Conte o que achou da história"><?= e($myRating['comentario'] ?? '') ?></textarea></label><button class="button button-small">Salvar avaliação</button>
        </form>
    <?php elseif (($_SESSION['auth']['kind'] ?? '') === 'cliente'): ?>
        <div class="review-read-gate">
            <p>Marque este livro como lido para compartilhar sua avaliação.</p>
            <form method="post" action="<?= url('pages/actions.php') ?>"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="shelf"><input type="hidden" name="livro_id" value="<?= $bookId ?>"><input type="hidden" name="status" value="lido"><button class="button button-small">Marcar como lido</button></form>
        </div>
    <?php elseif (!logged()): ?><p class="review-read-gate"><a class="text-link" href="<?= url('pages/login.php') ?>">Entre como leitor</a> e marque este livro como lido para avaliá-lo.</p><?php endif; ?>
    <?php if ($reviews): ?>
        <div class="book-review-list"><?php foreach ($reviews as $review): ?>
                <article class="book-review-card">
                    <div class="review-reader-avatar"><?php if ($review['foto']): ?><img src="<?= e(image_url($review['foto'], '')) ?>" alt=""><?php else: ?><span><?= e(mb_substr($review['nome'], 0, 1)) ?></span><?php endif; ?></div>
                    <div class="book-review-body">
                        <div class="book-review-byline">
                            <strong><?= e($review['nome']) ?></strong><span><?= str_repeat('★', (int)$review['nota']) . str_repeat('☆', 5 - (int)$review['nota']) ?></span><small><?= date('d/m/Y', strtotime($review['criado_em'])) ?></small>
                        </div><?php if (trim((string)$review['comentario']) !== ''): ?><p><?= nl2br(e($review['comentario'])) ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div><?php endif; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>