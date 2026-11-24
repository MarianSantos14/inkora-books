<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
function logged(): bool
{
    return !empty($_SESSION['auth']);
}
function admin(): bool
{
    return logged() && ($_SESSION['auth']['kind'] ?? '') === 'usuario' && ($_SESSION['auth']['role'] ?? '') === 'admin';
}
function staff(): bool
{
    return logged() && ($_SESSION['auth']['kind'] ?? '') === 'usuario';
}
function id(): ?int
{
    return logged() ? (int)$_SESSION['auth']['id'] : null;
}
function can_manage_author_gallery(int $authorId): bool
{
    return admin() || (logged() && ($_SESSION['auth']['kind'] ?? '') === 'usuario' && ($_SESSION['auth']['role'] ?? '') === 'autor' && id() === $authorId);
}
function can_manage_book(?int $ownerId): bool
{
    return admin() || (logged() && ($_SESSION['auth']['kind'] ?? '') === 'usuario' && ($_SESSION['auth']['role'] ?? '') === 'autor' && $ownerId !== null && id() === $ownerId);
}
function can_view_reading_counts(?int $ownerId): bool
{
    return admin() || (logged() && ($_SESSION['auth']['kind'] ?? '') === 'usuario' && ($_SESSION['auth']['role'] ?? '') === 'autor' && $ownerId !== null && id() === $ownerId);
}
function csrf(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function flash(string $message, string $kind = 'success'): void
{
    $_SESSION['flash'] = [$message, $kind];
}
function go(string $url = ''): never
{
    header('Location: ' . ($url === '' ? url() : $url));
    exit;
}
function post(string $key, string $default = ''): string
{
    return trim((string)($_POST[$key] ?? $default));
}
function require_login(): void
{
    if (!logged()) {
        flash('Entre na sua conta para continuar.', 'error');
        go(url('pages/login.php'));
    }
}
function show_error_page(int $status): never
{
    $status = in_array($status, [403, 404], true) ? $status : 404;
    http_response_code($status);
    require __DIR__ . '/' . $status . '.php';
    exit;
}
function require_admin(): void
{
    if (!admin()) show_error_page(403);
}
function csrf_check(): void
{
    if (!hash_equals(csrf(), (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        exit('Sessão expirada. Volte e tente novamente.');
    }
}
function cover(string $title, ?string $url): string
{
    return $url ?: url('assets/icons/book-placeholder.svg');
}
function image_upload(string $field, string $folder): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) throw new RuntimeException('A imagem deve ter até 5 MB e ser enviada corretamente.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
    if (!$ext || !getimagesize($file['tmp_name'])) throw new RuntimeException('Formato inválido. Use JPG, PNG, WEBP ou GIF.');
    $relative = 'assets/uploads/' . $folder . '/' . bin2hex(random_bytes(16)) . '.' . $ext;
    $target = dirname(__DIR__) . '/' . $relative;
    if (!is_dir(dirname($target))) mkdir(dirname($target), 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $target)) throw new RuntimeException('Não foi possível salvar a imagem enviada.');
    return $relative;
}
function delete_image(?string $path, string $folder): void
{
    $prefix = 'assets/uploads/' . $folder . '/';
    if (!$path || !str_starts_with($path, $prefix) || basename($path) !== substr($path, strlen($prefix))) return;
    $target = dirname(__DIR__) . '/' . $path;
    if (is_file($target) && !is_link($target)) @unlink($target);
}
function image_url(?string $path, string $fallback): string
{
    if (!$path) return $fallback;
    return preg_match('~^https?://~i', $path) ? $path : url(trim($path, '/'));
}
