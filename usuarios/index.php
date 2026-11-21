<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$pdo = db();
$users = $pdo->query('SELECT id,nome,email,tipo,foto,bio,criado_em FROM usuario ORDER BY tipo,nome')->fetchAll();
$roleCounts = $pdo->query("SELECT SUM(tipo='admin') admins, SUM(tipo='autor') autores FROM usuario")->fetch();
$edit = null;
if (isset($_GET['id'])) {
    $query = $pdo->prepare('SELECT id,nome,email,tipo,foto,bio FROM usuario WHERE id=?');
    $query->execute([(int)$_GET['id']]);
    $edit = $query->fetch();
}
$pageTitle = 'Usuários cadastrados';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading">
    <div>
        <div class="eyebrow">ACESSO ADMINISTRATIVO</div>
        <h1>Usuários cadastrados</h1>
        <p>Administre as contas de administradores e autoras/autores.</p>
    </div><?php if (!$edit): ?><button type="button" class="button" data-user-form-toggle data-inline-form-toggle data-open-label="Adicionar usuário" data-close-label="Fechar formulário" aria-expanded="false" aria-controls="user-form">Adicionar usuário</button><?php endif; ?>
</section>
<div class="stat-grid user-role-stats">
    <div class="stat-card"><span>Administradores</span><b><?= (int)($roleCounts['admins'] ?? 0) ?></b></div>
    <div class="stat-card"><span>Autoras e autores</span><b><?= (int)($roleCounts['autores'] ?? 0) ?></b></div>
</div>
<form id="user-form" class="editor" method="post" enctype="multipart/form-data" action="<?= url('pages/actions.php') ?>" <?= $edit ? '' : 'hidden' ?>>
    <h3><?= $edit ? 'Editar usuário' : 'Adicionar usuário' ?></h3><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="save_user"><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    <div class="form-grid two"><label>Nome<input required name="nome" value="<?= e($edit['nome'] ?? '') ?>"></label><label>E-mail<input type="email" required name="email" value="<?= e($edit['email'] ?? '') ?>"></label><label><?= $edit ? 'Nova senha (opcional)' : 'Senha inicial' ?><input type="password" name="senha" <?= $edit ? '' : 'required minlength="8"' ?> minlength="8"></label><label>Perfil<select name="tipo" id="authorRoleSelect">
                <option value="autor" <?= ($edit['tipo'] ?? '') === 'autor' ? 'selected' : '' ?>>Autor</option>
                <option value="admin" <?= ($edit['tipo'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select></label><label class="wide" id="authorBioField" <?= (($edit['tipo'] ?? 'autor') === 'autor') ? '' : 'hidden' ?>>Biografia<textarea name="bio" maxlength="3000" rows="5" placeholder="Conte um pouco sobre a autora, seus livros e seu estilo."><?= e($edit['bio'] ?? '') ?></textarea></label><?php $photoValue = $edit['foto'] ?? null;
                                                                                                                                                                                                                                                                                                                    require __DIR__ . '/../includes/photo_picker.php'; ?></div>
    <div class="form-actions"><button class="button">Salvar usuário</button><?php if ($edit): ?><a class="text-link" href="<?= url('usuarios/') ?>">Cancelar</a><?php endif; ?></div>
</form>
<div class="people-grid"><?php foreach ($users as $user): ?><article class="person-card"><img src="<?= e(image_url($user['foto'] ?? null, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80')) ?>" alt="Foto de <?= e($user['nome']) ?>">
            <div>
                <h3><?= e($user['nome']) ?></h3>
                <p><?= e($user['email']) ?></p><small><span class="status-pill"><?= e($user['tipo'] === 'admin' ? 'Administrador' : 'Autora/autor') ?></span></small>
                <div class="manage-links"><a href="ver.php?id=<?= $user['id'] ?>">Visualizar</a><a href="?id=<?= $user['id'] ?>">Editar</a><?php if ((int)$user['id'] !== id()): ?><form method="post" action="<?= url('pages/actions.php') ?>" data-confirm="Excluir este usuÃƒÂ¡rio e sua foto local?"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?= $user['id'] ?>"><button>Excluir</button></form><?php endif; ?></div>
            </div>
        </article><?php endforeach; ?></div>
<script>
    (() => {
        const role = document.getElementById("authorRoleSelect");
        const bio = document.getElementById("authorBioField");
        if (!role || !bio) return;
        const sync = () => bio.hidden = role.value !== "autor";
        role.addEventListener("change", sync);
        sync();
    })();
</script><?php require __DIR__ . '/../includes/footer.php'; ?>