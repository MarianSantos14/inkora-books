<?php

$host = $_SERVER['HTTP_HOST'] ?? '';

if (in_array($host, ['localhost', '127.0.0.1'])) {
    define('BASE_URL', '/inkora-books');
} else {
    define('BASE_URL', '');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}