<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Acer\Pr7\service\TaskManager;
use Acer\Pr7\config\Database;

session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: /tasks');
    exit;
}

$db = Database::getConnection();
$taskManager = new TaskManager($db);
$task = $taskManager->getTaskById((int)$_GET['id']);

if (!$task || $task->creator_id !== (int)$_SESSION['user_id']) {
    header('Location: /tasks');
    exit;
}

$taskManager->deleteTask((int)$_GET['id']);
header('Location: /tasks');
exit;