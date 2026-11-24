<?php if (!empty($thumbnailBook['capa'])): ?>
    <img class="table-book-cover" src="<?= e(image_url($thumbnailBook['capa'], '')) ?>" alt="Capa de <?= e($thumbnailBook['titulo'] ?? 'livro') ?>" loading="lazy">
<?php else: ?>
    <span class="table-book-cover table-book-cover-empty" role="img" aria-label="Livro sem capa">
        <svg viewBox="0 0 64 64" aria-hidden="true">
            <path d="M14 7h37v50H14z"></path>
            <path d="M22 8v48M29 17h15M29 23h15M29 29h15" fill="none" stroke="#e3e7ec" stroke-linecap="round" stroke-width="2"></path>
        </svg>
    </span>
<?php endif; ?>