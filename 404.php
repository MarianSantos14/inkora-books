<?php
require_once __DIR__ . '/includes/functions.php';
http_response_code(404);
$pageTitle = 'Página não encontrada';
require __DIR__ . '/includes/header.php';
?>
<section class="error-page">
    <div class="error-page-code">404</div>
    <div class="eyebrow">PÁGINA NÃO ENCONTRADA</div>
    <h1>Esta história saiu da estante.</h1>
    <p>O endereço pode estar incorreto ou a página não existe mais.</p>
    <a class="button" href="<?= url('') ?>">Voltar ao início <span>→</span></a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
