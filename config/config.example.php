<?php
/**
 * RentCMS Configuration File Template
 * 
 * Copy this file to config.php and update with your settings
 */

// Application Settings
define('APP_NAME', 'RentCMS');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/rentcms');
define('APP_ENV', 'production');
define('APP_DEBUG', false);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rentcms_db');
define('DB_CHARSET', 'utf8mb4');

// Security
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);

// Email Configuration (PHPMailer)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_FROM', 'noreply@rentcms.local');
define('MAIL_FROM_NAME', 'RentCMS');
define('MAIL_USE_AUTH', true);
define('MAIL_USE_TLS', true);

// File Upload Settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);
define('UPLOAD_PATH', 'public/uploads/');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Timezone
date_default_timezone_set('UTC');
?>
