<?php

class Booking {
    private static function db(): PDO {
        return Database::getInstance()->getConnection();
    }

    public static function create(array $data): int {
        $stmt = self::db()->prepare("INSERT INTO bookings (property_id, tenant_id, owner_id, check_in_date, check_out_date, total_nights, price_per_night, total_price, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')");
        $stmt->execute([
            $data['property_id'],
            $data['tenant_id'],
            $data['owner_id'],
            $data['check_in_date'],
            $data['check_out_date'],
            $data['total_nights'],
            $data['price_per_night'],
            $data['total_price'],
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function getForOwner(int $ownerId): array {
        $stmt = self::db()->prepare("SELECT b.*, p.title, u.full_name AS tenant_name FROM bookings b JOIN properties p ON b.property_id = p.id JOIN users u ON b.tenant_id = u.id WHERE b.owner_id = ? ORDER BY b.created_at DESC");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public static function getForTenant(int $tenantId): array {
        $stmt = self::db()->prepare("SELECT b.*, p.title, u.full_name AS owner_name FROM bookings b JOIN properties p ON b.property_id = p.id JOIN users u ON b.owner_id = u.id WHERE b.tenant_id = ? ORDER BY b.created_at DESC");
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public static function getAll(): array {
        $stmt = self::db()->query("SELECT b.*, p.title, u.full_name AS tenant_name FROM bookings b JOIN properties p ON b.property_id = p.id JOIN users u ON b.tenant_id = u.id ORDER BY b.created_at DESC");
        return $stmt->fetchAll();
    }
}

?>
