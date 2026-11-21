<?php
require_once __DIR__ . '/../includes/functions.php';
$authors = db()->query("SELECT id,nome,foto,bio FROM usuario WHERE tipo='autor' ORDER BY nome")->fetchAll();
$pageTitle = 'Autores';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow">VOZES DA ESTANTE</div><h1>Autores</h1><p>Conheça os autores, seus livros e os momentos que compartilham.</p></div></section>
<?php if (!$authors): ?><div class="empty-state"><h2>Nenhum autor cadastrado ainda.</h2></div><?php else: ?>
<div class="people-grid author-directory">
    <?php foreach ($authors as $author): ?>
    <a class="person-card author-directory-card" href="<?= url('usuarios/ver.php') ?>?id=<?=$author['id']?>">
        <img src="<?=e(image_url($author['foto'] ?? null, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80'))?>" alt="Foto de <?=e($author['nome'])?>" loading="lazy">
        <div><h3><?=e($author['nome'])?></h3><p class="author-directory-bio"><?=e(trim((string)($author['bio'] ?? '')) ?: 'Biografia ainda não cadastrada.')?></p><span class="text-link">Ver livros e galeria →</span></div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
