<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Acer\Pr7\service\TaskManager;
use Acer\Pr7\config\Database;
use Acer\Pr7\enum\TaskStatus;

session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: /tasks');
    exit;
}

$db = Database::getConnection();
$taskManager = new TaskManager($db);
$task = $taskManager->getTaskById((int)$_GET['id']);

if (!$task || $task->creator_id !== (int)$_SESSION['user_id']) {
    echo "Task not found or unauthorized!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'] ?? $task->title,
        'description' => $_POST['description'] !== '' ? $_POST['description'] : null,
        'status' => $_POST['status'] ?? $task->status->value,
    ];

    if (isset($_POST['assigned_to_id']) && $_POST['assigned_to_id'] !== '') {
        $data['assigned_to_id'] = (int)$_POST['assigned_to_id'];
    }

    $taskManager->updateTask($task->id, $data);
    header('Location: /tasks');
    exit;
}

require __DIR__ . '/../views/header.php';

echo "<h2>Edit Task</h2>
<form method='post'>
    Title: <input type='text' name='title' value='" . htmlspecialchars($task->title, ENT_QUOTES) . "' required><br><br>
    
    Description: <textarea name='description'>" . htmlspecialchars($task->description ?? '', ENT_QUOTES) . "</textarea><br><br>
    
    Status: <select name='status'>
        <option value='new'" . ($task->status === TaskStatus::NEW ? ' selected' : '') . ">New</option>
        <option value='in_progress'" . ($task->status === TaskStatus::IN_PROGRESS ? ' selected' : '') . ">In Progress</option>
        <option value='done'" . ($task->status === TaskStatus::DONE ? ' selected' : '') . ">Done</option>
    </select><br><br>
    
    Assigned To (User ID): <input type='number' name='assigned_to_id' value='" . ($task->assigned_to_id ?? '') . "'><br><br>
    
    <button type='submit'>Save Changes</button>
</form>
<a href='/tasks'>Back to tasks</a>";

require __DIR__ . '/../views/footer.php';