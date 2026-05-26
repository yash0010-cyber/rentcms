<?php

class Property {
    private static function db(): PDO {
        return Database::getInstance()->getConnection();
    }

    public static function search(array $filters = []): array {
        try {
            $sql = "SELECT * FROM properties WHERE status != 'inactive'";
            $params = [];

            if (!empty($filters['city'])) {
                $sql .= " AND city = ?";
                $params[] = $filters['city'];
            }

            if (!empty($filters['category'])) {
                $sql .= " AND property_type = ?";
                $params[] = $filters['category'];
            }

            $sql .= " ORDER BY average_rating DESC, created_at DESC LIMIT 12";

            $stmt = self::db()->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            return array_map([self::class, 'formatListing'], $rows);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getPopular(int $limit = 6): array {
        try {
            $stmt = self::db()->prepare("SELECT * FROM properties WHERE status = 'available' ORDER BY average_rating DESC LIMIT ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            return array_map([self::class, 'formatListing'], $rows);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getById(int $id): ?array {
        try {
            $stmt = self::db()->prepare("SELECT p.*, u.full_name AS owner_name FROM properties p LEFT JOIN users u ON p.owner_id = u.id WHERE p.id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }
            $row['image'] = self::formatImage($row['featured_image'] ?? '');
            return $row;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getByOwner(int $ownerId): array {
        try {
            $stmt = self::db()->prepare("SELECT * FROM properties WHERE owner_id = ? ORDER BY created_at DESC");
            $stmt->execute([$ownerId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public static function create(array $data): int {
        $stmt = self::db()->prepare("INSERT INTO properties (owner_id, title, description, address, city, state, country, postal_code, price_per_month, bedrooms, bathrooms, square_feet, property_type, amenities, featured_image, status, verification_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', 'pending')");
        $stmt->execute([
            $data['owner_id'],
            $data['title'],
            $data['description'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['postal_code'],
            $data['price_per_month'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['square_feet'],
            $data['property_type'],
            $data['amenities'],
            $data['featured_image'],
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, int $ownerId, array $data): bool {
        $stmt = self::db()->prepare("UPDATE properties SET title = ?, description = ?, address = ?, city = ?, state = ?, country = ?, postal_code = ?, price_per_month = ?, bedrooms = ?, bathrooms = ?, square_feet = ?, property_type = ?, amenities = ?, featured_image = ? WHERE id = ? AND owner_id = ?");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['country'],
            $data['postal_code'],
            $data['price_per_month'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['square_feet'],
            $data['property_type'],
            $data['amenities'],
            $data['featured_image'],
            $id,
            $ownerId,
        ]);
    }

    public static function getDemoListings(): array {
        return [
            [
                'id' => 1,
                'title' => 'Beautiful villa for sale in Tampa',
                'address' => '4935 New Providence Ave, Tampa, FL',
                'price_label' => '$1600 / sq. foot',
                'deal_type' => 'Sale',
                'category' => 'Villa',
                'bedrooms' => 5,
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 2,
                'title' => 'Stylish two-level penthouse in Palm Beach',
                'address' => '101 Worth Ave, Palm Beach, FL 33480',
                'price_label' => '$2000 / mo',
                'deal_type' => 'Rent',
                'category' => 'Penthouse',
                'bedrooms' => 2,
                'image' => 'https://images.unsplash.com/photo-1502005097973-6a7082348e28?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 3,
                'title' => 'Bright and Cheerful alcove studio',
                'address' => '1451 Ocean Dr, Miami Beach, FL 33139',
                'price_label' => '$200 / day',
                'deal_type' => 'Rent',
                'category' => 'Apartment',
                'bedrooms' => 1,
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 4,
                'title' => 'Coastal family residence',
                'address' => '211 Shoreline Dr, Fort Myers, FL',
                'price_label' => '$1800 / mo',
                'deal_type' => 'Rent',
                'category' => 'House',
                'bedrooms' => 3,
                'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 5,
                'title' => 'Downtown loft with skyline view',
                'address' => '80 Brickell Ave, Miami, FL',
                'price_label' => '$2500 / mo',
                'deal_type' => 'Rent',
                'category' => 'Loft',
                'bedrooms' => 2,
                'image' => 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'id' => 6,
                'title' => 'Lakefront holiday retreat',
                'address' => '771 Lakeview Rd, Orlando, FL',
                'price_label' => '$310 / day',
                'deal_type' => 'Rent',
                'category' => 'Cabin',
                'bedrooms' => 4,
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }

    public static function getCityOptions(): array {
        return ['Miami', 'Tampa', 'Palm Beach', 'Orlando', 'Fort Myers'];
    }

    public static function getDealTypes(): array {
        return ['Rent', 'Sale'];
    }

    public static function getCategories(): array {
        return ['Villa', 'Apartment', 'Penthouse', 'House', 'Loft', 'Cabin'];
    }

    private static function formatListing(array $row): array {
        $priceLabel = '$' . number_format((float) $row['price_per_month'], 0) . ' / mo';
        $category = $row['property_type'] ?: 'Home';
        $dealType = 'Rent';
        $image = self::formatImage($row['featured_image'] ?? '');

        return [
            'id' => $row['id'],
            'title' => $row['title'],
            'address' => trim($row['address'] . ', ' . ($row['city'] ?? '')),
            'price_label' => $priceLabel,
            'deal_type' => $dealType,
            'category' => $category,
            'bedrooms' => $row['bedrooms'] ?? 1,
            'image' => $image,
        ];
    }

    private static function formatImage(string $path): string {
        if (empty($path)) {
            return 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80';
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }
}

?>
