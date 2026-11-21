<?php
require_once __DIR__ . '/includes/functions.php';
$message = null;
$count = (int)db()->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if ($count > 0) {
        $message = 'Já existe uma equipe cadastrada. Faça login pela página inicial.';
    } else {
        $name = post('nome');
        $email = strtolower(post('email'));
        $password = post('senha');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) $message = 'Informe nome, e-mail válido e senha com pelo menos 8 caracteres.';
        else {
            db()->prepare("INSERT INTO usuario (nome,email,senha,tipo) VALUES (?,?,?,'admin')")->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Administrador criado. Entre com seu e-mail e senha.');
            go('/inkora-books/login.php');
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Configurar Inkora Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/inkora-books/assets/style.css">
</head>

<body>
    <main class="container">
        <section class="auth-card">
            <div class="eyebrow">CONFIGURAÇÃO INICIAL</div>
            <h1>Crie o administrador</h1>
            <p>Importe o schema.sql no phpMyAdmin antes de continuar. Esta tela só funciona enquanto ainda não houver usuários da equipe.</p><?php if ($message): ?><div class="notice error"><?= e($message) ?></div><?php endif; ?><?php if ($count === 0): ?><form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><label>Nome<input required name="nome"></label><label>E-mail<input required type="email" name="email"></label><label>Senha (mínimo 8 caracteres)<input required minlength="8" type="password" name="senha"></label><button class="button form-submit">Criar administrador <span>→</span></button></form><?php else: ?><a class="button" href="/inkora-books/login.php">Ir para o login</a><?php endif; ?>
        </section>
    </main>
</body>

</html>