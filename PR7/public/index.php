<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

match ($uri) {
    '', 'index'      => require __DIR__ . '/login.php',
    'login'          => require __DIR__ . '/login.php',
    'register'       => require __DIR__ . '/register.php',
    'tasks'          => require __DIR__ . '/tasks.php',
    'edit_task'      => require __DIR__ . '/edit_task.php',
    'delete_task'    => require __DIR__ . '/delete_task.php',
    'logout'         => \Acer\Pr7\model\User::logout() && header('Location: /') && exit,
    default          => http_response_code(404) && exit('404 Not Found')
};