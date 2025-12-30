<?php
declare(strict_types=1);

namespace Acer\Pr7\model;

use Acer\Pr7\enum\TaskStatus;
use PDO;
use DateTime;

class Task
{
    public ?int $id;
    public string $title;
    public ?string $description;
    public TaskStatus $status;
    public int $creator_id;
    public ?int $assigned_to_id;
    public string $created_at;
    public ?string $updated_at;

    private PDO $db;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->status = isset($data['status']) ? TaskStatus::from($data['status']) : TaskStatus::NEW;
        $this->creator_id = $data['creator_id'] ?? 0;
        $this->assigned_to_id = $data['assigned_to_id'] ?? null;
        $this->created_at = $data['created_at'] ?? (new DateTime())->format('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function create(): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tasks (title, description, status, creator_id, assigned_to_id, created_at) 
            VALUES (:title, :description, :status, :creator_id, :assigned_to_id, :created_at)"
        );

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':status' => $this->status->value,
            ':creator_id' => $this->creator_id,
            ':assigned_to_id' => $this->assigned_to_id,
            ':created_at' => $this->created_at,
        ]);
    }

    public function update(array $data = []): bool
    {
        $this->title = $data['title'] ?? $this->title;
        $this->description = $data['description'] ?? $this->description;
        $this->assigned_to_id = $data['assigned_to_id'] ?? $this->assigned_to_id;
        $this->status = isset($data['status']) ? TaskStatus::from($data['status']) : $this->status;
        $this->updated_at = (new DateTime())->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            "UPDATE tasks 
            SET title = :title, description = :description, status = :status, assigned_to_id = :assigned_to_id, updated_at = :updated_at
            WHERE id = :id"
        );

        return $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':status' => $this->status->value,
            ':assigned_to_id' => $this->assigned_to_id,
            ':updated_at' => $this->updated_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(): bool
    {
        if ($this->id === null) return false;
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    public function assignTo(int $userId): bool
    {
        return $this->update(['assigned_to_id' => $userId]);
    }

    public function changeStatus(TaskStatus $newStatus = TaskStatus::IN_PROGRESS): bool
    {
        return $this->update(['status' => $newStatus->value]);
    }
}
