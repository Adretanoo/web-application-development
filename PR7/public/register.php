<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use Acer\Pr7\model\User;
use Acer\Pr7\config\Database;

require __DIR__ . '/../views/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();
    $user = new User($db, [
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ]);
    if ($user->register()) {
        echo "Registration successful! <a href='/'>Login</a>";
    } else {
        echo "Error!";
    }
}

require __DIR__ . '/../views/register_form.php';
require __DIR__ . '/../views/footer.php';