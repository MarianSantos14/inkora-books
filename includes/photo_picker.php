<?php
$photoValue = $photoValue ?? null;
$photoSrc = $photoValue ? image_url($photoValue, '') : '';
?>
<div class="photo-picker" data-photo-picker data-original="<?= e($photoSrc) ?>">
    <div class="photo-picker-avatar" data-photo-preview>
        <?php if ($photoSrc): ?><img src="<?= e($photoSrc) ?>" alt="Foto de perfil atual"><?php else: ?>
            <span class="photo-placeholder" aria-label="Sem foto"><svg viewBox="0 0 64 64" aria-hidden="true">
                    <circle cx="32" cy="22" r="12"></circle>
                    <path d="M9 58c1-13 10-21 23-21s22 8 23 21"></path>
                </svg>
            </span>
        <?php endif; ?>
    </div>
    <label class="photo-pencil" title="Escolher ou trocar foto" aria-label="Escolher ou trocar foto" tabindex="0">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M14 5l5 5M4 20l4.2-.8L19 8.4a2.1 2.1 0 0 0-3-3L5.2 16.2 4 20z"></path>
        </svg>
        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp,image/gif" data-photo-file>
    </label>
    <?php if ($photoValue): ?><label class="photo-remove"><input type="checkbox" name="remover_foto" value="1" data-photo-remove> Remover foto</label><?php endif; ?>
    <span class="photo-help">JPG, PNG, WEBP ou GIF · até 5 MB</span>
</div>