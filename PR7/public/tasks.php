<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Acer\Pr7\service\TaskManager;
use Acer\Pr7\config\Database;
use Acer\Pr7\model\Task;

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$db = Database::getConnection();
$taskManager = new TaskManager($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $task = new Task($db, [
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? null,
        'creator_id' => (int)$_SESSION['user_id']
    ]);
    $taskManager->addTask($task);
}

$tasks = $taskManager->getTasksByUser((int)$_SESSION['user_id']);

require __DIR__ . '/../views/header.php';

echo "<h2>Your Tasks</h2><ul>";
foreach ($tasks as $task) {
    $desc = $task->description ? trim($task->description) : 'No description';
    echo "<li>
            <strong>{$task->title}</strong> ({$task->status->value})<br>
            <small>{$desc}</small> |
            <a href='/edit_task?id={$task->id}'>Edit</a> |
            <a href='/delete_task?id={$task->id}' onclick='return confirm(\"Delete this task?\")'>Delete</a>
          </li>";
}
echo "</ul>";

// Basic add form
echo "<h2>Add Task</h2>
<form method='post'>
    <input type='hidden' name='action' value='add'>
    Title: <input type='text' name='title' required><br>
    Description: <input type='text' name='description'><br>
    <button type='submit'>Add</button>
</form>";

require __DIR__ . '/../views/footer.php';