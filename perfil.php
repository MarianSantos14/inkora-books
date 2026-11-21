<?php require_once __DIR__ . '/includes/functions.php';
require_login();
$table = $_SESSION['auth']['kind'] === 'cliente' ? 'cliente' : 'usuario';
$columns = $table === 'cliente' ? 'nome,email,foto,telefone' : 'nome,email,foto';
$q = db()->prepare("SELECT $columns FROM $table WHERE id=?");
$q->execute([id()]);
$profile = $q->fetch();
$pageTitle = 'Meu perfil';
require __DIR__ . '/includes/header.php'; ?><section class="auth-card">
    <div class="eyebrow">SUA CONTA</div>
    <h1>Meu perfil</h1>
    <p>Atualize seus dados, senha e foto.</p>
    <form method="post" enctype="multipart/form-data" class="form-grid" action="/inkora-books/actions.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="action" value="save_profile">
        <?php $photoValue = $profile['foto'] ?? null;
        require __DIR__ . '/includes/photo_picker.php'; ?>
        <label>Nome<input required name="nome" value="<?= e($profile['nome']) ?>"></label>
        <label>E-mail<input required type="email" name="email" value="<?= e($profile['email']) ?>"></label>
        <?php if ($table === 'cliente'): ?>
            <label>Telefone<input name="telefone" value="<?= e($profile['telefone'] ?? '') ?>"></label>
        <?php endif; ?>
        <label>Nova senha <small>(deixe em branco para manter)</small>
            <input type="password" minlength="8" name="senha"></label>
        <button class="button form-submit">Salvar alterações <span>→</span></button>
    </form>
    <?php if (($_SESSION['auth']['kind'] ?? '') === 'usuario' && ($_SESSION['auth']['role'] ?? '') === 'autor'): ?>
        <a class="button button-small author-own-page" href="/inkora-books/usuarios/ver.php?id=<?= id() ?>">Ver minha p&#225;gina de autora</a>
    <?php endif; ?>
</section><?php require __DIR__ . '/includes/footer.php'; ?>