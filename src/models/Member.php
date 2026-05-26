<?php

class Member {
    private static function db(): PDO {
        return Database::getInstance()->getConnection();
    }

    public static function all(): array {
        $stmt = self::db()->query("SELECT m.*, u.full_name, u.email, u.phone FROM members m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
        return $stmt->fetchAll();
    }
}

?>
