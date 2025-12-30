<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
\Acer\Pr7\model\User::logout();
header('Location: /');
exit;