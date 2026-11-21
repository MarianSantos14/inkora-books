<?php if (!empty($thumbnailBook['capa'])): ?>
    <img class="table-book-cover" src="<?= e(image_url($thumbnailBook['capa'], '')) ?>" alt="Capa de <?= e($thumbnailBook['titulo'] ?? 'livro') ?>" loading="lazy">
<?php else: ?>
    <span class="table-book-cover table-book-cover-empty" role="img" aria-label="Livro sem capa">
        <svg viewBox="0 0 64 64" aria-hidden="true">
            <path d="M12 13h19c6 0 10 4 10 10v29c0-5-4-8-10-8H12z"></path>
            <path d="M52 13H33c-6 0-10 4-10 10v29c0-5 4-8 10-8h19z"></path>
        </svg>
    </span>
<?php endif; ?>