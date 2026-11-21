<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$clientId = (int)($_GET['id'] ?? 0);
$query = db()->prepare('SELECT id,nome,email,telefone,foto,criado_em FROM cliente WHERE id=?');
$query->execute([$clientId]);
$client = $query->fetch();
if (!$client) {
    flash('Cliente não encontrado.', 'error');
    go(url('clientes/'));
}
$shelfQuery = db()->prepare("SELECT l.id,l.titulo,l.autor,l.capa,e.status,e.atualizado_em FROM estante e JOIN livro l ON l.id=e.livro_id WHERE e.cliente_id=? ORDER BY e.atualizado_em DESC");
$shelfQuery->execute([$clientId]);
$shelf = $shelfQuery->fetchAll();
$pageTitle = 'Cliente · ' . $client['nome'];
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow">PERFIL DE LEITOR</div><h1><?=e($client['nome'])?></h1><p>Cadastro desde <?=date('d/m/Y', strtotime($client['criado_em']))?></p></div><a class="text-link" href="<?= url('clientes/') ?>">← Voltar aos clientes</a></section>
<section class="editor admin-detail">
    <img class="admin-detail-avatar" src="<?=e(image_url($client['foto'] ?? null, 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80'))?>" alt="Foto de <?=e($client['nome'])?>">
    <div class="admin-detail-info"><h2><?=e($client['nome'])?></h2><p><?=e($client['email'])?></p><p><?=e($client['telefone'] ?: 'Telefone não informado')?></p><div class="form-actions"><a class="button button-small" href="<?= url('clientes/') ?>?id=<?=$client['id']?>">Editar cliente</a><form method="post" action="<?= url('pages/actions.php') ?>" data-confirm="Excluir este cliente e sua estante?"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="delete_client"><input type="hidden" name="id" value="<?=$client['id']?>"><button class="danger-button">Excluir cliente</button></form></div></div>
</section>
<section class="section-head"><div><div class="eyebrow">LEITURA</div><h2>Estante de <?=e($client['nome'])?></h2></div></section>
<?php if (!$shelf): ?><div class="empty-state"><h2>Esta estante ainda está vazia.</h2></div><?php else: ?><div class="table-wrap client-shelf-table-wrap"><table class="client-shelf-table"><thead><tr><th>Capa</th><th>Livro</th><th>Autor</th><th>Status</th><th class="client-shelf-updated">Atualizado</th></tr></thead><tbody><?php foreach ($shelf as $book): ?><tr><td class="book-cover-cell"><a href="<?= url('livros/ver.php') ?>?id=<?=$book['id']?>"><?php $thumbnailBook=$book;require __DIR__.'/../includes/book_thumbnail.php';?></a></td><td><a class="text-link" href="<?= url('livros/ver.php') ?>?id=<?=$book['id']?>"><?=e($book['titulo'])?></a></td><td><?=e($book['autor'])?></td><td><span class="status-pill"><?=e($book['status'])?></span></td><td class="client-shelf-updated"><?=date('d/m/Y', strtotime($book['atualizado_em']))?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
