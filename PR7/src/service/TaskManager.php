<?php
declare(strict_types=1);

namespace Acer\Pr7\service;

use PDO;
use Acer\Pr7\model\Task;
use Acer\Pr7\model\User;

class TaskManager
{
    private PDO $db;
    private ?User $user = null;
    private ?Task $task = null;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function addTask(Task $task): bool
    {
        $this->task = $task;
        return $task->create();
    }

    public function updateTask(int $taskId, array $data): bool
    {
        $task = $this->getTaskById($taskId);
        if (!$task) return false;
        $this->task = $task;
        return $task->update($data);
    }

    public function deleteTask(int $taskId): bool
    {
        $task = $this->getTaskById($taskId);
        if (!$task) return false;
        $this->task = $task;
        return $task->delete();
    }

    public function getTasksByUser(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE creator_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $data) => new Task($this->db, $data), $rows);
    }

    public function getTaskById(int $taskId): ?Task
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $taskId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $this->task = new Task($this->db, $data);
            return $this->task;
        }

        $this->task = null;
        return null;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}

