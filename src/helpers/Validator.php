<?php
/**
 * Input Validator Helper Class
 * Validates and sanitizes user input
 */

class Validator {
    
    /**
     * Validate email
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     */
    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate phone number
     */
    public static function phone($phone) {
        $pattern = '/^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/';
        return preg_match($pattern, $phone) === 1;
    }

    /**
     * Validate password strength
     */
    public static function password($password) {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['valid' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'message' => 'Password must contain uppercase letter'];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'message' => 'Password must contain lowercase letter'];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'message' => 'Password must contain number'];
        }

        return ['valid' => true, 'message' => 'Password is strong'];
    }

    /**
     * Validate username
     */
    public static function username($username) {
        $pattern = '/^[a-zA-Z0-9_-]{3,20}$/';
        return preg_match($pattern, $username) === 1;
    }

    /**
     * Validate date format
     */
    public static function date($date, $format = 'Y-m-d') {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate integer
     */
    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate float
     */
    public static function float($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Validate file upload
     */
    public static function file($file, $maxSize = MAX_FILE_SIZE, $allowedExtensions = null) {
        if ($allowedExtensions === null) {
            $allowedExtensions = ALLOWED_EXTENSIONS;
        }

        if (!isset($file['name']) || empty($file['name'])) {
            return ['valid' => false, 'message' => 'No file selected'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'message' => 'File upload error'];
        }

        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'message' => 'File size exceeds limit'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return ['valid' => false, 'message' => 'File type not allowed'];
        }

        return ['valid' => true, 'message' => 'File is valid'];
    }

    /**
     * Sanitize string
     */
    public static function sanitizeString($string) {
        $string = trim($string);
        $string = stripslashes($string);
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        return $string;
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize number
     */
    public static function sanitizeNumber($number) {
        return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validate required fields
     */
    public static function required($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }
        return $errors;
    }

    /**
     * Validate field length
     */
    public static function length($value, $min = null, $max = null) {
        $length = strlen($value);
        
        if ($min !== null && $length < $min) {
            return ['valid' => false, 'message' => 'Must be at least ' . $min . ' characters'];
        }
        
        if ($max !== null && $length > $max) {
            return ['valid' => false, 'message' => 'Cannot exceed ' . $max . ' characters'];
        }
        
        return ['valid' => true, 'message' => 'Length is valid'];
    }

    /**
     * Validate numeric range
     */
    public static function range($value, $min, $max) {
        return $value >= $min && $value <= $max;
    }

    /**
     * Validate value matches pattern
     */
    public static function pattern($value, $pattern) {
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Check if value exists in array
     */
    public static function inArray($value, $array) {
        return in_array($value, $array, true);
    }
}

?>
