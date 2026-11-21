<?php
function render_books(array $items, bool $manage = false, bool $showSynopsis = true): void
{
    $readingCounts = [];
    if ((admin() || (staff() && ($_SESSION['auth']['role'] ?? '') === 'autor')) && $items) {
        $countableBooks = array_filter($items, static fn($book) => can_view_reading_counts(isset($book['usuario_id']) ? (int)$book['usuario_id'] : null));
        $bookIds = array_values(array_unique(array_filter(array_map(static fn($book) => (int)($book['id'] ?? 0), $countableBooks))));
        if ($bookIds) {
            $marks = implode(',', array_fill(0, count($bookIds), '?'));
            $query = db()->prepare("SELECT livro_id, SUM(status='lido') AS lidos, SUM(status='lendo') AS lendo, SUM(status='quero ler') AS quero_ler FROM estante WHERE livro_id IN ($marks) GROUP BY livro_id");
            $query->execute($bookIds);
            foreach ($query->fetchAll() as $counts) $readingCounts[(int)$counts['livro_id']] = $counts;
        }
    }

    if (!$items) {
        echo '<div class="empty-state"><h2>O catálogo está começando.</h2><p>Em breve, novas histórias estarão por aqui.</p></div>';
        return;
    }
?>
    <div class="book-grid">
        <?php foreach ($items as $book):
            $bookUrl = url('livros/ver.php?id=') . (int)$book['id'];
            $ownerId = isset($book['usuario_id']) ? (int)$book['usuario_id'] : null;
            $counts = $readingCounts[(int)$book['id']] ?? [];
            $readCount = (int)($counts['lidos'] ?? 0);
            $readingCount = (int)($counts['lendo'] ?? 0);
            $wantCount = (int)($counts['quero_ler'] ?? 0);
        ?>
            <article class="book-card">
                <a class="book-card-main" href="<?= e($bookUrl) ?>" aria-label="Ver detalhes de <?= e($book['titulo']) ?>">
                    <?php if (!empty($book['capa'])): ?>
                        <img class="book-cover" src="<?= e(image_url($book['capa'], '')) ?>" alt="Capa de <?= e($book['titulo']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="book-cover-placeholder" aria-label="Livro sem capa"><svg viewBox="0 0 64 64" aria-hidden="true">
                                <path d="M12 13h19c6 0 10 4 10 10v29c0-5-4-8-10-8H12z"></path>
                                <path d="M52 13H33c-6 0-10 4-10 10v29c0-5 4-8 10-8h19z"></path>
                            </svg></div>
                    <?php endif; ?>
                    <div class="book-info">
                        <div class="book-meta"><span><?= e((string)($book['ano_publicacao'] ?: 'LIVRO')) ?></span></div>
                        <h3><?= e($book['titulo']) ?></h3>
                        <p><?= e($book['autor']) ?></p>
                        <?php if (!empty($book['editora'])): ?><p class="book-publisher"><?= e($book['editora']) ?></p><?php endif; ?>
                        <?php if (!empty($book['numero_paginas'])): ?><p class="book-pages"><?= e((string)$book['numero_paginas']) ?> páginas</p><?php endif; ?>
                        <?php if ($showSynopsis && !empty($book['descricao'])): ?><p class="synopsis"><?= e(mb_strimwidth((string)$book['descricao'], 0, 105, '…')) ?></p><?php endif; ?>
                        <?php if (can_view_reading_counts($ownerId) && ($readCount + $readingCount + $wantCount) > 0): ?>
                            <div class="reading-counts">
                                <?php if ($readCount > 0): ?><span><strong><?= $readCount ?></strong> pessoas leram</span><?php endif; ?>
                                <?php if ($readingCount > 0): ?><span><strong><?= $readingCount ?></strong> pessoas lendo</span><?php endif; ?>
                                <?php if ($wantCount > 0): ?><span><strong><?= $wantCount ?></strong> pessoas querem ler</span><?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="book-card-bottom">
                    <?php if (($_SESSION['auth']['kind'] ?? '') === 'cliente'): ?>
                        <div class="card-actions">
                            <form method="post" action="<?= url('actions.php') ?>"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="shelf"><input type="hidden" name="livro_id" value="<?= $book['id'] ?>"><input type="hidden" name="status" value="quero ler"><button class="save-link">＋ Estante</button></form>
                        </div>
                    <?php elseif (logged()): ?>
                        <div class="card-actions"><span class="staff-tag">Equipe</span></div>
                    <?php else: ?>
                        <div class="card-actions"><a class="save-link" href="<?= url('login.php') ?>">Entre para guardar ↗</a></div>
                    <?php endif; ?>
                    <?php if ($manage && can_manage_book($ownerId)): ?>
                        <div class="manage-links"><a href="<?= url('livros/form.php') ?>?id=<?= $book['id'] ?>">Editar</a>
                            <form method="post" action="<?= url('actions.php') ?>" data-confirm="Remover este livro e sua capa local?"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete_book"><input type="hidden" name="id" value="<?= $book['id'] ?>"><button>Excluir</button></form>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php
}
