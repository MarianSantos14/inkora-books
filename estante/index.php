<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$shelf = [];
$isReader = (($_SESSION['auth']['kind'] ?? '') === 'cliente');
if ($isReader) {
    $query = db()->prepare("SELECT l.*,e.status,e.atualizado_em,a.nota AS minha_nota,a.comentario AS minha_avaliacao FROM estante e JOIN livro l ON l.id=e.livro_id LEFT JOIN avaliacao_livro a ON a.livro_id=l.id AND a.cliente_id=e.cliente_id WHERE e.cliente_id=? ORDER BY e.atualizado_em DESC");
    $query->execute([id()]);
    $shelf = $query->fetchAll();
}
$pageTitle = 'Minha estante';
require __DIR__ . '/../includes/header.php';
?>
<?php if (!$isReader): ?>
    <div class="empty-state"><h2>Estante de leitoras e leitores</h2><p>Acesse com uma conta de cliente para acompanhar leituras.</p></div>
<?php else: ?>
    <section class="page-heading"><div><div class="eyebrow">SEU CANTINHO</div><h1>Minha estante</h1><p>Cada livro, uma nova parte da sua hist&oacute;ria.</p></div><?php if ($shelf): ?><button type="button" class="button button-small" id="shelfEditToggle" aria-controls="shelfBookGrid" aria-pressed="false">Editar estante</button><?php endif; ?></section>
    <?php if (!$shelf): ?>
        <div class="empty-state"><h2>Sua pr&oacute;xima hist&oacute;ria come&ccedil;a aqui.</h2><p>Explore o cat&aacute;logo e salve livros que quer ler.</p><a class="button" href="/inkora-books/livros/">Explorar livros</a></div>
    <?php else: ?>
        <div class="book-grid shelf-grid" id="shelfBookGrid">
        <?php foreach ($shelf as $book): ?>
            <article class="book-card shelf-book-card">
                <a class="book-card-cover-link" href="/inkora-books/livros/ver.php?id=<?=$book['id']?>">
                    <?php if (!empty($book['capa'])): ?><img class="book-cover" src="<?=e(image_url($book['capa'],''))?>" alt="Capa de <?=e($book['titulo'])?>" loading="lazy">
                    <?php else: ?><div class="book-cover-placeholder" aria-label="Livro sem capa"><svg viewBox="0 0 64 64" aria-hidden="true"><path d="M12 13h19c6 0 10 4 10 10v29c0-5-4-8-10-8H12z"></path><path d="M52 13H33c-6 0-10 4-10 10v29c0-5 4-8 10-8h19z"></path></svg></div><?php endif; ?>
                </a>
                <div class="book-info">
                    <div class="book-meta"><span><?=e($book['ano_publicacao'] ?: 'LEITURA')?></span><span class="status-pill shelf-status shelf-status-<?=e(str_replace(' ', '-', (string)$book['status']))?>"><?=e($book['status'])?></span></div>
                    <h3><a href="/inkora-books/livros/ver.php?id=<?=$book['id']?>"><?=e($book['titulo'])?></a></h3><p><?=e($book['autor'])?></p>
                    <?php if ($book['status'] === 'lido'): ?><div class="shelf-reader-rating"><?php if ($book['minha_nota']): ?><span aria-label="Nota pessoal: <?=$book['minha_nota']?> de 5"><?=str_repeat('★',(int)$book['minha_nota']) . str_repeat('☆',5-(int)$book['minha_nota'])?></span><strong><?=$book['minha_nota']?>/5</strong><?php else: ?><a class="text-link" href="/inkora-books/livros/ver.php?id=<?=$book['id']?>#avaliacoes">☆ Avaliar leitura</a><?php endif; ?></div><?php endif; ?>
                    <?php if (!empty($book['editora'])): ?><p class="book-publisher"><?=e($book['editora'])?></p><?php endif; ?>
                    <form method="post" action="/inkora-books/actions.php" class="shelf-control shelf-edit-control"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="shelf"><input type="hidden" name="livro_id" value="<?=$book['id']?>"><select name="status"><option <?=$book['status']==='quero ler'?'selected':''?>>quero ler</option><option <?=$book['status']==='lendo'?'selected':''?>>lendo</option><option <?=$book['status']==='lido'?'selected':''?>>lido</option></select><button class="text-link">Atualizar</button></form>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
<script>document.getElementById('shelfEditToggle')?.addEventListener('click', event => { const button = event.currentTarget; const grid = document.getElementById('shelfBookGrid'); const editing = grid.classList.toggle('is-editing'); button.setAttribute('aria-pressed', String(editing)); button.textContent = editing ? 'Concluir edição' : 'Editar estante'; });</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>