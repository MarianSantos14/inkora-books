<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = (int)($_GET['id'] ?? 0);
$query = db()->prepare('SELECT id,nome,email,tipo,foto,bio,criado_em FROM usuario WHERE id=?');
$query->execute([$userId]);
$user = $query->fetch();
if (!$user) {
    show_error_page(404);
}
$isAuthor = $user['tipo'] === 'autor';
$authorBio = trim((string)($user['bio'] ?? ''));
if (!$isAuthor && !admin()) {
    show_error_page(404);
}
$booksQuery = db()->prepare('SELECT id,titulo,autor,capa,criado_em,usuario_id FROM livro WHERE usuario_id=? ORDER BY criado_em DESC');
$booksQuery->execute([$userId]);
$books = $booksQuery->fetchAll();
$statsQuery = db()->prepare('SELECT COUNT(DISTINCT l.id) AS total, AVG(a.nota) AS media, COUNT(a.id) AS avaliacoes FROM livro l LEFT JOIN avaliacao_livro a ON a.livro_id=l.id WHERE l.usuario_id=?');
$statsQuery->execute([$userId]);
$authorStats = $statsQuery->fetch() ?: ['total'=>count($books),'media'=>null,'avaliacoes'=>0];
$followersQuery = db()->prepare('SELECT COUNT(*) FROM autor_seguidor WHERE autor_id=?');
$followersQuery->execute([$userId]);
$followerCount = (int)$followersQuery->fetchColumn();
$isFollowing = false;
if (($_SESSION['auth']['kind'] ?? '') === 'cliente') {
    $followingQuery = db()->prepare('SELECT 1 FROM autor_seguidor WHERE cliente_id=? AND autor_id=?');
    $followingQuery->execute([id(), $userId]);
    $isFollowing = (bool)$followingQuery->fetchColumn();
}
$ratingQuery = db()->prepare('SELECT livro_id,AVG(nota) AS media,COUNT(*) AS total FROM avaliacao_livro WHERE livro_id IN (SELECT id FROM livro WHERE usuario_id=?) GROUP BY livro_id');
$ratingQuery->execute([$userId]);
$bookRatings = [];
foreach ($ratingQuery->fetchAll() as $ratingRow) $bookRatings[(int)$ratingRow['livro_id']] = $ratingRow;
$canManageGallery = $isAuthor && can_manage_author_gallery($userId);
$canSeePrivateInfo = admin() || (logged() && id() === $userId);
$gallery = [];
if ($isAuthor) {
    $galleryQuery = db()->prepare('SELECT id,imagem,legenda,criado_em FROM galeria_autor WHERE usuario_id=? ORDER BY criado_em DESC');
    $galleryQuery->execute([$userId]);
    $gallery = $galleryQuery->fetchAll();
}
$useLightbox = $isAuthor;
$pageTitle = ($isAuthor ? 'Autora' : 'Usuário') . ' · ' . $user['nome'];
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div><div class="eyebrow"><?= $isAuthor ? 'PÁGINA DA AUTORA' : 'PERFIL DA EQUIPE' ?></div><h1><?=e($user['nome'])?></h1><p>Cadastro desde <?=date('d/m/Y', strtotime($user['criado_em']))?></p></div>
    <?php $backUrl = admin() ? url('usuarios/') : url('autores/'); ?><a class="text-link" href="<?=$backUrl?>"><?=admin() ? '← Voltar aos usuários' : '← Voltar aos autores'?></a>
</section>
<?php if ($isAuthor): ?>
<section class="author-profile-card">
    <div class="author-profile-portrait">
        <img src="<?=e(image_url($user['foto'] ?? null, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=700&q=85'))?>" alt="Foto de <?=e($user['nome'])?>">
        <span class="author-profile-role">Autora</span>
    </div>
    <div class="author-profile-main">
        <div class="eyebrow">BIOGRAFIA</div>
        <h2><?=e($user['nome'])?></h2>
        <p class="author-profile-bio"><?=e($authorBio ?: 'Biografia ainda não cadastrada.')?></p>
        <div class="author-profile-facts"><div><strong><?=number_format((int)$authorStats['total'], 0, ',', '.')?></strong><span>livros</span></div><div><strong><?=number_format($followerCount, 0, ',', '.')?></strong><span>seguidores</span></div><div><strong><?=number_format(count($gallery), 0, ',', '.')?></strong><span>fotos na galeria</span></div></div>
        <?php if ($authorStats['media'] !== null): ?><p class="author-community-rating"><span>★</span> <?=number_format((float)$authorStats['media'],1,',','.')?> <small>média dos livros · <?= (int)$authorStats['avaliacoes'] ?> avaliações</small></p><?php endif; ?>
        <?php if (($_SESSION['auth']['kind'] ?? '') === 'cliente'): ?><form class="author-follow-form" method="post" action="<?= url('actions.php') ?>"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="<?=$isFollowing ? 'unfollow_author' : 'follow_author'?>"><input type="hidden" name="autor_id" value="<?=$userId?>"><button class="button button-small <?=$isFollowing ? 'button-following' : ''?>"><?=$isFollowing ? 'Seguindo · deixar de seguir' : '＋ Seguir autora'?></button></form><?php elseif (!logged()): ?><a class="button button-small author-follow-guest" href="<?= url('login.php') ?>">Entre para seguir</a><?php endif; ?>
        <?php if (admin()): ?><div class="form-actions author-profile-actions"><a class="button button-small" href="<?= url('usuarios/') ?>?id=<?=$user['id']?>">Editar usuário</a><?php if ((int)$user['id'] !== id()): ?><form method="post" action="<?= url('actions.php') ?>" data-confirm="Excluir este usuário e suas imagens da galeria?"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?=$user['id']?>"><button class="danger-button">Excluir usuário</button></form><?php endif; ?></div><?php endif; ?>
    </div>
</section>
<?php else: ?>
<section class="editor admin-detail">
    <img class="admin-detail-avatar" src="<?=e(image_url($user['foto'] ?? null, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80'))?>" alt="Foto de <?=e($user['nome'])?>">
    <div class="admin-detail-info"><h2><?=e($user['nome'])?></h2><?php if ($canSeePrivateInfo): ?><p><?=e($user['email'])?></p><?php endif; ?><p><span class="status-pill"><?=e(ucfirst($user['tipo']))?></span></p><?php if (admin()): ?><div class="form-actions"><a class="button button-small" href="<?= url('usuarios/') ?>?id=<?=$user['id']?>">Editar usuário</a><?php if ((int)$user['id'] !== id()): ?><form method="post" action="<?= url('actions.php') ?>" data-confirm="Excluir este usuário e suas imagens da galeria?"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?=$user['id']?>"><button class="danger-button">Excluir usuário</button></form><?php endif; ?></div><?php endif; ?></div>
</section>
<?php endif; ?>
<section class="section-head author-feed-heading"><div><div class="eyebrow">ESTANTE DA AUTORA</div><h2>Livros de <?=e($user['nome'])?></h2><p>Histórias publicadas e lidas pela comunidade.</p></div></section>
<?php if (!$books): ?><div class="empty-state"><h2>Nenhum livro cadastrado por esta autora.</h2></div><?php else: ?>
<div class="author-book-feed">
<?php foreach ($books as $book): $bookRating=$bookRatings[(int)$book['id']]??null; ?><article class="author-book-post">
    <div class="author-book-post-head"><div class="author-post-avatar"><img src="<?=e(image_url($user['foto']??null,'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=100&q=80'))?>" alt=""></div><div><strong><?=e($user['nome'])?></strong><span>publicou um livro · <?=date('d/m/Y',strtotime($book['criado_em']))?></span></div><a class="author-post-link" href="<?= url('livros/ver.php') ?>?id=<?=$book['id']?>" aria-label="Ver <?=e($book['titulo'])?>">↗</a></div>
    <a class="author-book-cover-link" href="<?= url('livros/ver.php') ?>?id=<?=$book['id']?>"><?php if(!empty($book['capa'])): ?><img src="<?=e(image_url($book['capa'],''))?>" alt="Capa de <?=e($book['titulo'])?>" loading="lazy"><?php else: ?><div class="author-book-cover-placeholder">▤</div><?php endif; ?></a>
    <div class="author-book-post-content"><div class="author-book-post-rating"><?php if($bookRating): ?><span>★ <?=number_format((float)$bookRating['media'],1,',','.')?></span><small><?= (int)$bookRating['total'] ?> <?=((int)$bookRating['total']===1?'avaliação':'avaliações')?></small><?php else: ?><span class="rating-empty">☆ Ainda sem avaliações</span><?php endif; ?></div><h3><a href="<?= url('livros/ver.php') ?>?id=<?=$book['id']?>"><?=e($book['titulo'])?></a></h3><p>Por <?=e($book['autor'])?></p><?php if (can_manage_book(isset($book['usuario_id'])?(int)$book['usuario_id']:null)): ?><div class="manage-links"><a href="<?= url('livros/form.php') ?>?id=<?=$book['id']?>">Editar</a><form method="post" action="<?= url('actions.php') ?>" data-confirm="Excluir este livro e sua capa local?"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete_book"><input type="hidden" name="id" value="<?=$book['id']?>"><button>Excluir</button></form></div><?php endif; ?></div>
</article><?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($isAuthor): ?>
<section class="section-head author-gallery-heading" id="galeria"><div><div class="eyebrow">BASTIDORES E INSPIRAÇÕES</div><h2>Galeria de <?=e($user['nome'])?></h2><p>Fotos de eventos, leituras e momentos especiais.</p></div><div class="author-gallery-actions"><?php if ($canManageGallery): ?><button type="button" class="button" data-inline-form-toggle data-open-label="Adicionar à galeria" data-close-label="Fechar formulário" aria-expanded="false" aria-controls="gallery-upload-form">Adicionar à galeria</button><?php if ($gallery): ?><button type="button" class="gallery-edit-toggle" data-gallery-edit-toggle data-open-label="Editar fotos" data-close-label="Concluir edição" aria-pressed="false">Editar fotos</button><?php endif; ?><?php endif; ?></div></section>
<?php if ($canManageGallery): ?>

<form id="gallery-upload-form" class="editor gallery-upload" method="post" enctype="multipart/form-data" action="<?= url('actions.php') ?>" hidden>
    <input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="save_gallery_image"><input type="hidden" name="usuario_id" value="<?=$userId?>">
    <div class="gallery-picker" data-gallery-picker><div class="gallery-picker-preview" data-gallery-preview><span class="gallery-placeholder" aria-label="Prévia da foto"><svg viewBox="0 0 64 64" aria-hidden="true"><rect x="8" y="12" width="48" height="40" rx="4"></rect><circle cx="23" cy="26" r="5"></circle><path d="m12 47 14-13 9 8 7-6 10 11"></path></svg></span></div><button type="button" class="gallery-pencil" aria-label="Escolher foto"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 16.5-.8 4.3 4.3-.8L19 8.5 15.5 5z"></path><path d="m13.5 7 3.5 3.5"></path></svg></button><input type="file" name="imagem" data-gallery-file accept="image/jpeg,image/png,image/webp,image/gif" required></div>
    <label class="gallery-caption-field">Legenda (opcional)<input type="text" name="legenda" maxlength="180" placeholder="Um momento especial"></label>
    <div class="form-actions"><button class="button">Salvar foto</button></div>
</form>
<?php endif; ?>
<?php if (!$gallery): ?><div class="empty-state"><h2>A galeria ainda está vazia.</h2><?php if ($canManageGallery): ?><p>Adicione a primeira foto para compartilhar momentos com seus leitores.</p><?php endif; ?></div><?php else: ?>
<div class="author-gallery-grid" data-gallery-grid>
    <?php foreach ($gallery as $photo): $photoUrl=image_url($photo['imagem'],''); ?>
    <article class="author-gallery-card">
        <a href="<?=e($photoUrl)?>" data-lightbox="author-gallery-<?=$userId?>" data-title="<?=e($photo['legenda'] ?: $user['nome'])?>"><img src="<?=e($photoUrl)?>" alt="<?=e($photo['legenda'] ?: 'Foto da galeria de '.$user['nome'])?>" loading="lazy"></a>
        <p class="gallery-caption"><?=e($photo['legenda'] ?: 'Sem legenda')?></p><?php if ($canManageGallery): ?><form class="gallery-caption-form" method="post" action="<?= url('actions.php') ?>"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="update_gallery_caption"><input type="hidden" name="usuario_id" value="<?=$userId?>"><input type="hidden" name="id" value="<?=$photo['id']?>"><label>Legenda<input type="text" name="legenda" maxlength="180" value="<?=e($photo['legenda'] ?? '')?>" placeholder="Adicionar legenda"></label><button class="gallery-caption-save">Salvar legenda</button></form><?php endif; ?><?php if ($canManageGallery): ?><form method="post" action="<?= url('actions.php') ?>" data-confirm="Remover esta foto da galeria?"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete_gallery_image"><input type="hidden" name="usuario_id" value="<?=$userId?>"><input type="hidden" name="id" value="<?=$photo['id']?>"><button class="gallery-delete">Remover foto</button></form><?php endif; ?>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
