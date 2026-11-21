<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

loadEnv(dirname(__DIR__) . '/.env');

function db(): PDO
{
    static $pdo;

    if (!$pdo) {
        $pdo = new PDO(
            'mysql:host=' . $_ENV['DB_HOST'] .
            ';dbname=' . $_ENV['DB_NAME'] .
            ';charset=' . $_ENV['DB_CHARSET'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    return $pdo;
}
