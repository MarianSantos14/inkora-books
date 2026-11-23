<?php require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/book_cards.php';
$search = trim((string)($_GET['q'] ?? ''));
$booksQuery = db()->prepare("SELECT * FROM livro WHERE titulo LIKE ? OR autor LIKE ? OR editora LIKE ? ORDER BY criado_em DESC");
$searchPattern = '%' . $search . '%';
$booksQuery->execute([$searchPattern, $searchPattern, $searchPattern]);
$books = $booksQuery->fetchAll();
$pageTitle = 'Catálogo de livros';
require __DIR__ . '/../includes/header.php'; ?>
<section class="page-heading">
    <div>
        <div class="eyebrow">BIBLIOTECA</div>
        <h1>Nosso catálogo</h1>
        <p>Histórias selecionadas para acompanhar você.</p>
    </div><?php if (staff()): ?><a class="button" href="<?= url('livros/form.php') ?>">Adicionar livro <span>+</span></a><?php endif; ?>
</section>
<form class="list-search" method="get" role="search">
    <label for="book-search">Pesquisar livros por título, autor ou editora</label>
    <div class="search-controls"><input id="book-search" type="search" name="q" value="<?= e($search) ?>" placeholder="Título, autor ou editora" autocomplete="off"><button class="button button-small search-button" type="submit"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg>Pesquisar</button><?php if ($search !== ''): ?><a class="search-clear" href="<?= url('livros/') ?>">Limpar</a><?php endif; ?></div>
</form>
<?php if ($search !== '' && !$books): ?><div class="empty-state"><h2>Nenhum livro encontrado.</h2><p>Tente outro título, autor ou editora.</p></div><?php else: render_books($books, true, false); endif;
require __DIR__ . '/../includes/footer.php'; ?>