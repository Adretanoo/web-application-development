<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
use Acer\Pr7\model\User;
use Acer\Pr7\config\Database;

session_start();
require __DIR__ . '/../views/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();
    $user = new User($db, [
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ]);
    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        header('Location: /tasks');
        exit;
    } else {
        echo "Invalid credentials!";
    }
}

require __DIR__ . '/../views/login_form.php';
require __DIR__ . '/../views/footer.php';