<?php require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Entrar';
require __DIR__ . '/includes/header.php'; ?><section class="auth-card">
    <div class="eyebrow">SUA ESTANTE ESPERA POR VOCÊ</div>
    <h1>Boas-vindas de volta</h1>
    <p>Entre para acompanhar suas leituras e descobertas.</p>
    <form method="post" class="form-grid" action="/inkora-books/actions.php">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="login">
        <label>E-mail<input type="email" required name="email" autocomplete="email"></label>
        <label>Senha<input type="password" required name="senha" autocomplete="current-password"></label>
        <button class="button form-submit">Entrar na minha conta <span>→</span></button>
    </form>
    <div class="auth-foot">Ainda não tem conta?
        <a href="/inkora-books/cadastro.php">Cadastre-se</a>
    </div>
</section><?php require __DIR__ . '/includes/footer.php'; ?>