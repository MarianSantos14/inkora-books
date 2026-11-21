<?php
declare(strict_types=1);

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new RuntimeException('.env não encontrado.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');

        $_ENV[trim($key)] = trim($value);
    }
}