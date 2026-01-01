<?php

return [
    'database' => [
        'driver'    => 'pgsql',
        'host'      => '127.0.0.1',
        'port'      => '5432',
        'database'  => 'postgres',
        'username'  => 'postgres',
        'password'  => '2007',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
        'options'   => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ],
    'twig' => [
        'path'  => __DIR__ . '/../templates',
        'cache' => false,
    ],
];