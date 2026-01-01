<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Acer\Pr8\models\User;
use Acer\Pr8\models\Tag;
use Illuminate\Database\Capsule\Manager as Capsule;

$config = require __DIR__ . '/../src/config.php';
$capsule = new Capsule;
$capsule->addConnection($config['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

User::create([
    'username' => 'admin',
    'password' => password_hash('adminpass', PASSWORD_DEFAULT),
    'email' => 'admin@example.com',
    'role' => 'admin',
]);

User::create([
    'username' => 'user',
    'password' => password_hash('userpass', PASSWORD_DEFAULT),
    'email' => 'user@example.com',
    'role' => 'user',
]);

Tag::create(['name' => 'PHP']);
Tag::create(['name' => 'Slim']);
Tag::create(['name' => 'Blog']);

echo "Дані додано!\n";