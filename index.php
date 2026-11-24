<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/book_cards.php';

$pdo = db();
$books = $pdo->query("SELECT * FROM livro WHERE capa IS NOT NULL AND TRIM(capa)<>'' ORDER BY criado_em DESC LIMIT 8")->fetchAll();
$carouselBooks = $pdo->query("SELECT id,titulo,capa FROM livro WHERE capa IS NOT NULL AND TRIM(capa)<>'' ORDER BY criado_em DESC LIMIT 10")->fetchAll();
$community = $pdo->query("SELECT id,nome,foto FROM cliente WHERE foto IS NOT NULL AND TRIM(foto)<>'' ORDER BY id LIMIT 3")->fetchAll();
$pageTitle = 'Histórias que ficam com você';
require __DIR__ . '/includes/header.php';
?>
<section class="hero home-hero">
    <div class="hero-copy">
        <div class="eyebrow">SEU PRÓXIMO CAPÍTULO COMEÇA AQUI</div>
        <h1>Histórias que<br><em>ficam com você.</em></h1>
        <p>Uma estante feita para leitores. Descubra novos mundos, acompanhe sua leitura e guarde cada história especial.</p>
        <div class="hero-actions">
            <a class="button" href="<?= url('livros/') ?>">Explorar livros <span>↗</span></a>
            <a class="text-link" href="<?= logged() ? url('livros/') : url('pages/cadastro.php') ?>">Comece sua estante <span>→</span></a>
        </div>
        <div class="hero-note">
            <div class="avatars" aria-label="Leitores da comunidade">
                <?php foreach ($community as $reader): ?><span class="community-avatar"><img src="<?= e(image_url($reader['foto'], '')) ?>" alt="Foto de <?= e($reader['nome']) ?>" loading="lazy"></span><?php endforeach; ?>
            </div>
            <span>Um universo de histórias esperando por você</span>
        </div>
    </div>
</section>
<?php if ($carouselBooks): ?>
    <section class="home-book-carousel" aria-label="Capas de livros em destaque">
        <div class="home-book-carousel-viewport">
            <div class="home-book-carousel-track">
                <div class="home-book-carousel-group">
                    <?php foreach ($carouselBooks as $book): ?>
                        <a class="home-book-carousel-cover" href="<?= url('livros/ver.php') ?>?id=<?= (int)$book['id'] ?>" aria-label="Abrir <?= e($book['titulo']) ?>">
                            <?php if (!empty($book['capa'])): ?><img src="<?= e(image_url($book['capa'], '')) ?>" alt="Capa de <?= e($book['titulo']) ?>" loading="lazy"><?php else: ?><span class="home-carousel-no-cover" aria-label="Livro sem capa"><svg viewBox="0 0 64 64" width="28" height="28" aria-hidden="true"><path d="M14 7h37v50H14z" fill="#aab3bf"></path><path d="M22 8v48M29 17h15M29 23h15M29 29h15" fill="none" stroke="#e3e7ec" stroke-linecap="round" stroke-width="2"></path></svg></span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<section class="section-head home-books-heading">
    <div>
        <h2>Encontre sua próxima leitura</h2>
    </div><a class="text-link" href="<?= url('livros/') ?>">Ver catálogo <span>→</span></a>
</section>
<div class="home-reading-books"><?php render_books($books); ?></div>
<?php require __DIR__ . '/includes/footer.php'; ?>