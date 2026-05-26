<?php

class Rating {
    private static function db(): PDO {
        return Database::getInstance()->getConnection();
    }

    public static function create(int $tenantId, int $propertyId, int $rating, string $review): bool {
        $stmt = self::db()->prepare("INSERT INTO ratings (property_id, tenant_id, rating, review) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), review = VALUES(review)");
        $success = $stmt->execute([$propertyId, $tenantId, $rating, $review]);

        if ($success) {
            self::recalculatePropertyRating($propertyId);
        }

        return $success;
    }

    public static function getByProperty(int $propertyId): array {
        $stmt = self::db()->prepare("SELECT r.*, u.full_name FROM ratings r JOIN users u ON r.tenant_id = u.id WHERE r.property_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    private static function recalculatePropertyRating(int $propertyId): void {
        $stmt = self::db()->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM ratings WHERE property_id = ?");
        $stmt->execute([$propertyId]);
        $stats = $stmt->fetch();

        if ($stats) {
            $update = self::db()->prepare("UPDATE properties SET average_rating = ?, total_ratings = ? WHERE id = ?");
            $update->execute([
                round((float) $stats['avg_rating'], 2),
                (int) $stats['total'],
                $propertyId,
            ]);
        }
    }
}

?>
