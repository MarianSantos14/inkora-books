<?php
require_once __DIR__ . '/../includes/functions.php';
$search = trim((string)($_GET['q'] ?? ''));
$authorsQuery = db()->prepare("SELECT id,nome,foto,bio FROM usuario WHERE tipo='autor' AND (nome LIKE ? OR bio LIKE ?) ORDER BY nome");
$searchPattern = '%' . $search . '%';
$authorsQuery->execute([$searchPattern, $searchPattern]);
$authors = $authorsQuery->fetchAll();
$pageTitle = 'Autores';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow">VOZES DA ESTANTE</div><h1>Autores</h1><p>Conheça os autores, seus livros e os momentos que compartilham.</p></div></section>
<form class="list-search" method="get" role="search">
    <label for="author-search">Pesquisar autores por nome ou biografia</label>
    <div class="search-controls"><input id="author-search" type="search" name="q" value="<?= e($search) ?>" placeholder="Nome ou biografia" autocomplete="off"><button class="button button-small search-button" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg>Pesquisar</button><?php if ($search !== ''): ?><a class="search-clear" href="<?= url('autores/') ?>">Limpar</a><?php endif; ?></div>
</form>
<?php if (!$authors): ?><div class="empty-state"><h2><?= $search !== '' ? 'Nenhum autor encontrado.' : 'Nenhum autor cadastrado ainda.' ?></h2></div><?php else: ?>
<div class="people-grid author-directory">
    <?php foreach ($authors as $author): ?>
    <a class="person-card author-directory-card" href="<?= url('usuarios/ver.php') ?>?id=<?=$author['id']?>">
        <img src="<?=e(image_url($author['foto'] ?? null, url('assets/icons/user-placeholder.svg')))?>" alt="Foto de <?=e($author['nome'])?>" loading="lazy">
        <div><h3><?=e($author['nome'])?></h3><p class="author-directory-bio"><?=e(trim((string)($author['bio'] ?? '')) ?: 'Biografia ainda não cadastrada.')?></p><span class="text-link">Ver livros e galeria →</span></div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
