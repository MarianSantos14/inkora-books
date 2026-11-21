</main>
<footer class="footer">
    <a class="brand" href="/inkora-books/"><span>Inkora <span class="brand-dot">Books</span></span></a>
    <span class="footer-tagline">Feito para quem encontra um lar em cada história.</span>
    <section class="footer-credits" aria-label="Créditos">
        <span class="footer-credits-title">Criado por</span>
        <div class="footer-credit-person"><strong>Mariana</strong><a href="https://github.com/MarianSantos14" target="_blank" rel="noopener noreferrer" aria-label="GitHub de Mariana"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.55.1.76-.24.76-.54v-2.1c-3.1.67-3.75-1.32-3.75-1.32-.5-1.29-1.24-1.63-1.24-1.63-1.01-.69.08-.68.08-.68 1.12.08 1.71 1.15 1.71 1.15.99 1.7 2.59 1.2 3.22.91.1-.72.39-1.2.7-1.48-2.48-.28-5.09-1.24-5.09-5.51 0-1.22.44-2.22 1.15-3-.12-.28-.5-1.42.11-2.96 0 0 .94-.3 3.05 1.14a10.6 10.6 0 0 1 5.55 0c2.11-1.43 3.04-1.14 3.04-1.14.61 1.54.23 2.68.11 2.96.72.78 1.15 1.78 1.15 3 0 4.28-2.61 5.22-5.1 5.5.4.35.75 1.02.75 2.06v3.05c0 .3.2.65.77.54A11.1 11.1 0 0 0 12 .9Z" />
                </svg></a><a href="https://www.linkedin.com/in/marianacarvalho-tech" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de Mariana"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.45 2H3.55C2.69 2 2 2.68 2 3.52v16.96c0 .84.69 1.52 1.55 1.52h16.9c.86 0 1.55-.68 1.55-1.52V3.52c0-.84-.69-1.52-1.55-1.52ZM7.93 18.34H4.98V9.02h2.95v9.32ZM6.45 7.75a1.71 1.71 0 1 1 .02-3.42 1.71 1.71 0 0 1-.02 3.42Zm11.9 10.59H15.4v-4.54c0-1.08-.02-2.47-1.51-2.47-1.52 0-1.75 1.18-1.75 2.39v4.62H9.2V9.02h2.82v1.27h.04c.39-.73 1.35-1.5 2.78-1.5 2.98 0 3.53 1.96 3.53 4.51v5.04Z" />
                </svg></a></div> &
        <div class="footer-credit-person"><strong>Halexya</strong><a href="https://github.com/Halexya" target="_blank" rel="noopener noreferrer" aria-label="GitHub de Halexya"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 .9a11.1 11.1 0 0 0-3.51 21.63c.55.1.76-.24.76-.54v-2.1c-3.1.67-3.75-1.32-3.75-1.32-.5-1.29-1.24-1.63-1.24-1.63-1.01-.69.08-.68.08-.68 1.12.08 1.71 1.15 1.71 1.15.99 1.7 2.59 1.2 3.22.91.1-.72.39-1.2.7-1.48-2.48-.28-5.09-1.24-5.09-5.51 0-1.22.44-2.22 1.15-3-.12-.28-.5-1.42.11-2.96 0 0 .94-.3 3.05 1.14a10.6 10.6 0 0 1 5.55 0c2.11-1.43 3.04-1.14 3.04-1.14.61 1.54.23 2.68.11 2.96.72.78 1.15 1.78 1.15 3 0 4.28-2.61 5.22-5.1 5.5.4.35.75 1.02.75 2.06v3.05c0 .3.2.65.77.54A11.1 11.1 0 0 0 12 .9Z" />
                </svg></a></div>
    </section>
    <span class="footer-copyright">© <?= date('Y') ?> Inkora Books</span>
</footer>
<?php if (!empty($useLightbox)): ?><script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.12.0/js/lightbox-plus-jquery.min.js"></script>
    <script>
        lightbox.option({
            resizeDuration: 180,
            wrapAround: true,
            albumLabel: "Imagem %1 de %2"
        });
    </script><?php endif; ?>
<script src="/inkora-books/assets/app.js"></script>
</body>

</html>