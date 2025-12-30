<?php
declare(strict_types=1);

namespace Acer\Pr7\model;

use PDO;

class User
{
    public ?int $id;
    public string $username;
    public string $email;
    public string $password;
    private PDO $db;

    public function __construct(PDO $db, array $data = [])
    {
        $this->db = $db;
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
    }

    public function register(): bool
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return $stmt->execute([
            ':username' => $this->username,
            ':email' => $this->email,
            ':password' => $this->password
        ]);
    }

    public function login(): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $this->email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($this->password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        session_start();
        session_destroy();
    }

    public function changePassword(string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hashed, ':id' => $this->id]);
    }
}