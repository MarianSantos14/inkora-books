<?php
$pageTitle = $pageTitle ?? 'Inkora Books';
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($pageTitle) ?> · Inkora Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/style.css') ?>">
    <?php if (!empty($useLightbox)): ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.12.0/css/lightbox.min.css"><?php endif; ?>
    <link rel="icon" href="<?= url('assets/icons/favicon.ico') ?>" sizes="any">
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/icons/favicon.svg') ?>">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/inkora-books/" aria-label="Inkora Books, início"><span>Inkora <span class="brand-dot">Books</span></span></a>
            <button class="nav-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="site-menu" data-nav-toggle><span></span><span></span><span></span></button>
            <div class="topbar-menu" id="site-menu" data-site-menu>
                <nav aria-label="Navegação principal">
                    <a href="/inkora-books/">Início</a>
                    <a href="<?= url('livros/') ?>">Livros</a>
                    <a href="/inkora-books/autores/">Autores</a>
                    <a href="/inkora-books/clientes/">Comunidade</a>
                    <?php if (($_SESSION['auth']['kind'] ?? '') === 'cliente'): ?><a href="/inkora-books/estante/">Minha estante</a><?php endif; ?>
                    <?php if (admin()): ?><a href="/inkora-books/usuarios/">Usuários</a><a href="/inkora-books/dashboard.php">Painel</a><?php endif; ?>
                </nav>
                <div class="nav-right">
                    <button class="icon-button" data-theme-toggle aria-label="Alternar tema">◐</button>
                    <?php if (logged()): ?>
                        <a class="hello" href="/inkora-books/perfil.php"><?php if (!empty($_SESSION['auth']['foto'])): ?><img class="nav-avatar" src="<?= e(image_url($_SESSION['auth']['foto'], '')) ?>" alt=""><?php endif; ?><?= e($_SESSION['auth']['nome']) ?></a>
                        <form method="post" action="/inkora-books/actions.php"><input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="logout"><button class="nav-login">Sair</button></form>
                    <?php else: ?>
                        <a class="nav-login" href="/inkora-books/login.php">Entrar</a>
                        <a class="button button-small" href="/inkora-books/cadastro.php">Criar conta</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="container">
        <?php if ($flashMessage): ?><div class="notice <?= e($flashMessage[1]) ?>"><?= e($flashMessage[0]) ?></div><?php endif; ?>