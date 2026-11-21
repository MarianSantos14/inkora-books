<?php require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/book_cards.php';
$books = db()->query('SELECT * FROM livro ORDER BY criado_em DESC')->fetchAll();
$pageTitle = 'Catálogo de livros';
require __DIR__ . '/../includes/header.php'; ?>
<section class="page-heading">
    <div>
        <div class="eyebrow">BIBLIOTECA</div>
        <h1>Nosso catálogo</h1>
        <p>Histórias selecionadas para acompanhar você.</p>
    </div><?php if (staff()): ?><a class="button" href="/inkora-books/livros/form.php">Adicionar livro <span>+</span></a><?php endif; ?>
</section>
<?php render_books($books, true, false);
require __DIR__ . '/../includes/footer.php'; ?>