<?php
$coverValue = $coverValue ?? null;
$coverSrc = $coverValue ? image_url($coverValue, '') : '';
?>
<div class="cover-picker" data-cover-picker data-original="<?= e($coverSrc) ?>">
    <div class="cover-picker-preview" data-cover-preview>
        <?php if ($coverSrc): ?><img src="<?= e($coverSrc) ?>" alt="Capa atual do livro"><?php else: ?>
            <span class="cover-placeholder" aria-label="Sem capa">
                <svg viewBox="0 0 64 64" aria-hidden="true">
                    <path d="M12 13h19c6 0 10 4 10 10v29c0-5-4-8-10-8H12z"></path>
                    <path d="M52 13H33c-6 0-10 4-10 10v29c0-5 4-8 10-8h19z"></path>
                </svg>
            </span>
        <?php endif; ?>
    </div>
    <label class="cover-pencil" title="Escolher ou trocar capa" aria-label="Escolher ou trocar capa" tabindex="0"><svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M14 5l5 5M4 20l4.2-.8L19 8.4a2.1 2.1 0 0 0-3-3L5.2 16.2 4 20z"></path>
        </svg><input type="file" name="capa_arquivo" accept="image/jpeg,image/png,image/webp,image/gif" data-cover-file></label>
    <?php if ($coverValue): ?><label class="cover-remove"><input type="checkbox" name="remover_capa" value="1" data-cover-remove> Remover capa</label><?php endif; ?>
    <span class="cover-help">Capa enviada ou carregada pelo ISBN · até 5 MB</span>
    <input type="hidden" name="capa_url" value="<?= e($coverSrc && preg_match('~^https?://~i', $coverSrc) ? $coverSrc : '') ?>" data-cover-url>
</div>