<?php

class User {
    private static function db(): PDO {
        return Database::getInstance()->getConnection();
    }

    public static function all(): array {
        $stmt = self::db()->query("SELECT id, username, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $stmt = self::db()->prepare("SELECT id, username, full_name, email, role, status, phone, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function getMembers(): array {
        $stmt = self::db()->query("SELECT u.id, u.full_name, u.email, u.phone, m.occupation, m.verified, m.emergency_contact, m.emergency_phone FROM members m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
        return $stmt->fetchAll();
    }
}

?>
