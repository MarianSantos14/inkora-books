<?php require_once __DIR__ . '/../includes/functions.php';
$clients = db()->query('SELECT id,nome,email,telefone,foto,criado_em FROM cliente ORDER BY nome')->fetchAll();
$edit = null;
if (staff() && isset($_GET['id'])) {
    $q = db()->prepare('SELECT id,nome,email,telefone,foto FROM cliente WHERE id=?');
    $q->execute([(int)$_GET['id']]);
    $edit = $q->fetch();
}
$pageTitle = 'Comunidade leitora';
require __DIR__ . '/../includes/header.php'; ?>
<section class="page-heading">
    <div>
        <div class="eyebrow">LEITORES</div>
        <h1>Comunidade leitora</h1>
        <p>Conheça as pessoas que fazem parte da nossa estante.</p>
    </div><?php if (staff() && !$edit): ?><button type="button" class="button inline-form-toggle" data-inline-form-toggle data-open-label="Cadastrar cliente" data-close-label="Fechar formulário" aria-expanded="false" aria-controls="client-form">Cadastrar cliente</button><?php endif; ?>
</section>
<?php if (staff()): ?><form id="client-form" class="editor" method="post" enctype="multipart/form-data" action="<?= url('pages/actions.php') ?>" <?= $edit ? '' : 'hidden' ?>>
        <h3><?= $edit ? 'Editar cliente' : 'Cadastrar cliente' ?></h3><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="save_client"><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <div class="form-grid two"><label>Nome<input required name="nome" value="<?= e($edit['nome'] ?? '') ?>"></label><label>E-mail<input type="email" required name="email" value="<?= e($edit['email'] ?? '') ?>"></label><label>Telefone<input name="telefone" value="<?= e($edit['telefone'] ?? '') ?>"></label><?php if (!$edit): ?><label>Senha inicial<input required minlength="8" type="password" name="senha"></label><?php endif; ?><?php $photoValue = $edit['foto'] ?? null;
                                                                                                                                                                                                                                                                                                                                                                                                                                    require __DIR__ . '/../includes/photo_picker.php'; ?></div>
        <div class="form-actions"><button class="button">Salvar cliente</button><?php if ($edit): ?><a class="text-link" href="<?= url('clientes/') ?>">Cancelar</a><?php endif; ?></div>
    </form><?php endif; ?>
<div class="people-grid"><?php foreach ($clients as $c): ?><article class="person-card"><img src="<?= e(image_url($c['foto'] ?? null, url('assets/icons/user-placeholder.svg'))) ?>" alt="Foto de <?= e($c['nome']) ?>">
            <div>
                <h3><?= e($c['nome']) ?></h3>
                <p><?= e($c['email']) ?></p><small>Leitor desde <?= date('m/Y', strtotime($c['criado_em'])) ?></small><?php if (staff()): ?><div class="manage-links"><?php if (admin()): ?><a href="ver.php?id=<?= $c['id'] ?>">Visualizar</a><?php endif; ?><a href="?id=<?= $c['id'] ?>">Editar</a>
                        <form method="post" action="<?= url('pages/actions.php') ?>" data-confirm="Excluir este cliente e sua foto local?"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete_client"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button>Excluir</button></form>
                    </div><?php endif; ?>
            </div>
        </article><?php endforeach; ?></div><?php require __DIR__ . '/../includes/footer.php'; ?>