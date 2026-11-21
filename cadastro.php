<?php require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Criar conta';
require __DIR__ . '/includes/header.php'; ?><section class="auth-card">
    <div class="eyebrow">SUA ESTANTE ESPERA POR VOCÊ</div>
    <h1>Crie sua conta</h1>
    <p>Comece a guardar histórias e acompanhar suas leituras.</p>
    <form method="post" enctype="multipart/form-data" class="form-grid" action="/inkora-books/actions.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="register">
        <?php $photoValue = null;
        require __DIR__ . '/includes/photo_picker.php'; ?>
        <label>Nome<input required name="nome" autocomplete="name"></label>
        <label>E-mail<input type="email" required name="email" autocomplete="email"></label>
        <label>Telefone <small>(opcional)</small>
            <input name="telefone"></label>
        <label>Senha<input type="password" required minlength="8" name="senha" autocomplete="new-password">
        </label>
        <button class="button form-submit">Criar minha conta <span>→</span></button>
    </form>
    <div class="auth-foot">Já tem conta? <a href="/inkora-books/login.php">Entre aqui</a></div>
</section><?php require __DIR__ . '/includes/footer.php'; ?>