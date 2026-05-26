<?php
/**
 * Activity Logger Helper Class
 * Tracks user activity and system events
 */

class Activity {
    private static $db = null;

    /**
     * Initialize database connection
     */
    private static function init() {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
    }

    /**
     * Log user activity
     */
    public static function log($userId, $action, $resourceType, $resourceId, $description = '') {
        self::init();

        try {
            $ipAddress = self::getIpAddress();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $stmt = self::$db->prepare("
                INSERT INTO activity_logs (
                    user_id, action, description, resource_type, resource_id,
                    ip_address, user_agent, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'success')
            ");

            $stmt->execute([
                $userId,
                $action,
                $description,
                $resourceType,
                $resourceId,
                $ipAddress,
                $userAgent
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Activity logging error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log login activity
     */
    public static function logLogin($userId) {
        return self::log($userId, 'login', 'user', $userId, 'User login');
    }

    /**
     * Log logout activity
     */
    public static function logLogout($userId) {
        return self::log($userId, 'logout', 'user', $userId, 'User logout');
    }

    /**
     * Log property creation
     */
    public static function logPropertyCreated($userId, $propertyId, $propertyTitle) {
        return self::log($userId, 'create', 'property', $propertyId, "Created property: $propertyTitle");
    }

    /**
     * Log property update
     */
    public static function logPropertyUpdated($userId, $propertyId, $propertyTitle) {
        return self::log($userId, 'update', 'property', $propertyId, "Updated property: $propertyTitle");
    }

    /**
     * Log booking creation
     */
    public static function logBookingCreated($userId, $bookingId, $propertyId) {
        return self::log($userId, 'create', 'booking', $bookingId, "Created booking for property $propertyId");
    }

    /**
     * Log booking cancellation
     */
    public static function logBookingCancelled($userId, $bookingId) {
        return self::log($userId, 'cancel', 'booking', $bookingId, 'Cancelled booking');
    }

    /**
     * Log rating created
     */
    public static function logRatingCreated($userId, $ratingId, $propertyId) {
        return self::log($userId, 'create', 'rating', $ratingId, "Rated property $propertyId");
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin($email) {
        self::init();

        try {
            $ipAddress = self::getIpAddress();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $stmt = self::$db->prepare("
                INSERT INTO activity_logs (
                    user_id, action, description, resource_type,
                    ip_address, user_agent, status
                ) VALUES (NULL, 'login_failed', ?, 'user', ?, ?, 'failure')
            ");

            $stmt->execute(["Failed login attempt: $email", $ipAddress, $userAgent]);
            return true;
        } catch (Exception $e) {
            error_log("Activity logging error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user activity
     */
    public static function getActivity($userId, $limit = 50) {
        self::init();

        try {
            $stmt = self::$db->prepare("
                SELECT * FROM activity_logs WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get activity by resource
     */
    public static function getByResource($resourceType, $resourceId) {
        self::init();

        try {
            $stmt = self::$db->prepare("
                SELECT * FROM activity_logs WHERE resource_type = ? AND resource_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$resourceType, $resourceId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get recent activity (all users)
     */
    public static function getRecent($limit = 50) {
        self::init();

        try {
            $stmt = self::$db->prepare("
                SELECT a.*, u.full_name FROM activity_logs a
                LEFT JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Clean old activity logs
     */
    public static function cleanup($daysOld = 90) {
        self::init();

        try {
            $date = date('Y-m-d', strtotime("-$daysOld days"));
            $stmt = self::$db->prepare("
                DELETE FROM activity_logs WHERE created_at < ?
            ");
            $stmt->execute([$date]);
            return true;
        } catch (Exception $e) {
            error_log("Activity cleanup error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user IP address
     */
    private static function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
}

?>
