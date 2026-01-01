<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$dotenv->required([
    'DB_DRIVER',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
]);

return [
    'database' => [
        'driver'    => $_ENV['DB_DRIVER'] ?? 'pgsql',
        'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port'      => $_ENV['DB_PORT'] ?? '5432',
        'database'  => $_ENV['DB_DATABASE'],
        'username'  => $_ENV['DB_USERNAME'],
        'password'  => $_ENV['DB_PASSWORD'] ?? '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'options'   => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ],

    'twig' => [
        'path'  => __DIR__ . '/../templates',
        'cache' => ($_ENV['APP_ENV'] ?? 'development') === 'production'
            ? __DIR__ . '/../cache/twig'
            : false,
        'debug' => ($_ENV['APP_DEBUG'] ?? 'true') === 'true',
    ],
];