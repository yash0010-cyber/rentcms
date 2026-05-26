<?php
/**
 * Authentication Helper Class
 * Handles user authentication, registration, and session management
 */

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Register a new user
     */
    public function register($name, $email, $password, $phone = '', $role = 'tenant') {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Generate email verification token
        $token = bin2hex(random_bytes(32));

        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, phone, role, email_token, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([$name, $email, $hashedPassword, $phone, $role, $token]);

            // Send verification email
            if (REQUIRE_EMAIL_VERIFICATION) {
                Email::sendVerification($email, $name, $token);
            } else {
                // Mark email as verified if verification not required
                $updateStmt = $this->db->prepare("
                    UPDATE users SET email_verified = 1 WHERE email = ?
                ");
                $updateStmt->execute([$email]);
            }

            return ['success' => true, 'message' => 'Registration successful. Please check your email.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Login user
     */
    public function login($email, $password, $rememberMe = false) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, password, role, status, email_verified 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $user = $stmt->fetch();

            // Check if user is active
            if ($user['status'] == 0) {
                return ['success' => false, 'message' => 'Your account has been suspended'];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Check email verification
            if (REQUIRE_EMAIL_VERIFICATION && $user['email_verified'] == 0) {
                return ['success' => false, 'message' => 'Please verify your email first'];
            }

            // Update last login
            $updateStmt = $this->db->prepare("
                UPDATE users SET last_login = NOW() WHERE id = ?
            ");
            $updateStmt->execute([$user['id']]);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            // Handle remember me
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (REMEMBER_ME_DURATION * 24 * 60 * 60), '/');
                // Store token in database (optional)
            }

            // Log activity
            Activity::log($user['id'], 'login', 'user', $user['id']);

            return ['success' => true, 'message' => 'Login successful', 'user' => $user];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            Activity::log($userId, 'logout', 'user', $userId);
        }

        $_SESSION = [];
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');

        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, email FROM users WHERE email_token = ? AND email_verified = 0
            ");
            $stmt->execute([$token]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Invalid or expired token'];
            }

            $user = $stmt->fetch();

            $updateStmt = $this->db->prepare("
                UPDATE users SET email_verified = 1, email_token = NULL WHERE id = ?
            ");
            $updateStmt->execute([$user['id']]);

            return ['success' => true, 'message' => 'Email verified successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send password reset email
     */
    public function forgotPassword($email) {
        try {
            $stmt = $this->db->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() === 0) {
                // Don't reveal if email exists for security
                return ['success' => true, 'message' => 'If email exists, password reset link has been sent'];
            }

            $user = $stmt->fetch();
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + PASSWORD_RESET_TOKEN_EXPIRY);

            $updateStmt = $this->db->prepare("
                UPDATE users SET password_reset_token = ?, password_reset_token_expiry = ? WHERE id = ?
            ");
            $updateStmt->execute([$token, $expiry, $user['id']]);

            // Send email
            Email::sendPasswordReset($email, $user['name'], $token);

            return ['success' => true, 'message' => 'Password reset link has been sent to your email'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM users 
                WHERE password_reset_token = ? 
                AND password_reset_token_expiry > NOW()
            ");
            $stmt->execute([$token]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Invalid or expired token'];
            }

            $user = $stmt->fetch();
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updateStmt = $this->db->prepare("
                UPDATE users 
                SET password = ?, password_reset_token = NULL, password_reset_token_expiry = NULL 
                WHERE id = ?
            ");
            $updateStmt->execute([$hashedPassword, $user['id']]);

            return ['success' => true, 'message' => 'Password reset successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Change password for logged-in user
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        try {
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'User not found'];
            }

            $user = $stmt->fetch();

            // Verify old password
            if (!password_verify($oldPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updateStmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->execute([$hashedPassword, $userId]);

            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, email, phone, address, role, status, email_verified, 
                       profile_picture, bio, created_at, last_login
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);

            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Check if user is owner
     */
    public static function isOwner() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner';
    }

    /**
     * Check if user is tenant
     */
    public static function isTenant() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'tenant';
    }

    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     */
    public static function getCurrentUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /**
     * Require admin access
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole($role) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            header('HTTP/1.1 403 Forbidden');
            exit('Access Denied');
        }
    }
}

?>
