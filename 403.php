<?php
require_once __DIR__ . '/includes/functions.php';
http_response_code(403);
$pageTitle = 'Acesso não autorizado';
require __DIR__ . '/includes/header.php';
?>
<section class="error-page">
    <div class="error-page-code">403</div>
    <div class="eyebrow">ACESSO RESTRITO</div>
    <h1>Você não tem permissão para entrar aqui.</h1>
    <p>Esta página está disponível apenas para contas autorizadas.</p>
    <a class="button" href="<?= url('') ?>">Voltar ao início <span>→</span></a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
