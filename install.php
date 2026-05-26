<?php
/**
 * RentCMS Installation Script
 * 
 * This script handles the initial setup and configuration of RentCMS
 * Run: php install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colors for CLI output
class Colors {
    const GREEN = "\033[32m";
    const RED = "\033[31m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const RESET = "\033[0m";
}

class Installer {
    private $errors = [];
    private $warnings = [];
    private $success = [];
    private $db = null;

    public function __construct() {
        echo Colors::BLUE . "
╔═══════════════════════════════════════════════════════════╗
║                   RentCMS Installation                    ║
║              Version 1.0.0 - Production Ready             ║
╚═══════════════════════════════════════════════════════════╝
" . Colors::RESET;
        echo "\n";
    }

    /**
     * Run installation
     */
    public function run() {
        echo Colors::BLUE . "Starting installation process...\n" . Colors::RESET;
        echo str_repeat("=", 60) . "\n\n";

        // Step 1: Check PHP Version
        $this->checkPhpVersion();

        // Step 2: Check required extensions
        $this->checkExtensions();

        // Step 3: Check directory permissions
        $this->checkDirectories();

        // Step 4: Create configuration file
        $this->createConfig();

        // Step 5: Connect to database
        $this->setupDatabase();

        // Step 6: Create database tables
        $this->createTables();

        // Step 7: Create default admin
        $this->createDefaultAdmin();

        // Step 8: Set file permissions
        $this->setPermissions();

        // Step 9: Summary
        $this->printSummary();
    }

    /**
     * Check PHP Version
     */
    private function checkPhpVersion() {
        echo "Step 1: Checking PHP version...\n";
        $phpVersion = phpversion();
        $requiredVersion = '8.0.0';

        if (version_compare($phpVersion, $requiredVersion, '>=')) {
            $this->addSuccess("PHP version: $phpVersion ✓");
        } else {
            $this->addError("PHP version $phpVersion is not supported. Required: $requiredVersion or higher");
        }
        echo "\n";
    }

    /**
     * Check required PHP extensions
     */
    private function checkExtensions() {
        echo "Step 2: Checking required PHP extensions...\n";
        $extensions = ['mysqli', 'openssl', 'json', 'spl'];

        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                $this->addSuccess("Extension '$ext' is installed ✓");
            } else {
                $this->addError("Required extension '$ext' is not installed");
            }
        }
        echo "\n";
    }

    /**
     * Check directory structure and permissions
     */
    private function checkDirectories() {
        echo "Step 3: Checking directory structure...\n";
        $directories = [
            'public/uploads',
            'logs',
            'temp',
            'database'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
                $this->addSuccess("Created directory: $dir ✓");
            } else {
                $this->addSuccess("Directory exists: $dir ✓");
            }
        }
        echo "\n";
    }

    /**
     * Create configuration file
     */
    private function createConfig() {
        echo "Step 4: Creating configuration file...\n";

        if (file_exists('config/config.php')) {
            $this->addWarning("Configuration file already exists, skipping...");
            echo "\n";
            return;
        }

        echo "Please provide your database configuration:\n\n";

        // Get database details from user input
        $dbHost = $this->prompt("Database Host [localhost]");
        $dbHost = !empty($dbHost) ? $dbHost : 'localhost';

        $dbPort = $this->prompt("Database Port [3306]");
        $dbPort = !empty($dbPort) ? $dbPort : '3306';

        $dbUser = $this->prompt("Database Username [root]");
        $dbUser = !empty($dbUser) ? $dbUser : 'root';

        $dbPass = $this->prompt("Database Password []");

        $dbName = $this->prompt("Database Name [rentcms_db]");
        $dbName = !empty($dbName) ? $dbName : 'rentcms_db';

        $mailHost = $this->prompt("Email SMTP Host [smtp.gmail.com]");
        $mailHost = !empty($mailHost) ? $mailHost : 'smtp.gmail.com';

        $mailPort = $this->prompt("Email SMTP Port [587]");
        $mailPort = !empty($mailPort) ? $mailPort : '587';

        $mailUser = $this->prompt("Email Username []");

        $mailPass = $this->prompt("Email Password []");

        $configContent = "<?php
/**
 * RentCMS Configuration File
 * 
 * Database and Application Settings
 */

// Application Settings
define('APP_NAME', 'RentCMS');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/rentcms');
define('APP_ENV', 'production');
define('APP_DEBUG', false);

// Database Configuration
define('DB_HOST', '$dbHost');
define('DB_PORT', '$dbPort');
define('DB_USER', '$dbUser');
define('DB_PASS', '$dbPass');
define('DB_NAME', '$dbName');
define('DB_CHARSET', 'utf8mb4');

// Security
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);

// Email Configuration
define('MAIL_HOST', '$mailHost');
define('MAIL_PORT', '$mailPort');
define('MAIL_USERNAME', '$mailUser');
define('MAIL_PASSWORD', '$mailPass');
define('MAIL_FROM', '$mailUser');
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
";

        if (!file_exists('config')) {
            mkdir('config', 0755, true);
        }

        file_put_contents('config/config.php', $configContent);
        $this->addSuccess("Configuration file created successfully ✓");
        echo "\n";
    }

    /**
     * Setup database connection
     */
    private function setupDatabase() {
        echo "Step 5: Setting up database connection...\n";

        require_once 'config/config.php';

        try {
            $this->db = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );

            if ($this->db->connect_error) {
                $this->addError("Database connection failed: " . $this->db->connect_error);
            } else {
                $this->db->set_charset("utf8mb4");
                $this->addSuccess("Database connection successful ✓");
            }
        } catch (Exception $e) {
            $this->addError("Database connection error: " . $e->getMessage());
        }
        echo "\n";
    }

    /**
     * Create database tables
     */
    private function createTables() {
        echo "Step 6: Creating database tables...\n";

        if (!$this->db) {
            $this->addError("Database not connected");
            echo "\n";
            return;
        }

        $tables = [
            // Users Table
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(100),
                phone VARCHAR(20),
                role ENUM('owner', 'tenant', 'admin') DEFAULT 'tenant',
                status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
                email_verified BOOLEAN DEFAULT FALSE,
                verification_token VARCHAR(255),
                reset_token VARCHAR(255),
                profile_picture VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX(email),
                INDEX(role),
                INDEX(status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Properties Table
            "CREATE TABLE IF NOT EXISTS properties (
                id INT AUTO_INCREMENT PRIMARY KEY,
                owner_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description LONGTEXT,
                address VARCHAR(255) NOT NULL,
                city VARCHAR(100),
                state VARCHAR(100),
                country VARCHAR(100),
                postal_code VARCHAR(20),
                price_per_month DECIMAL(10, 2) NOT NULL,
                bedrooms INT DEFAULT 1,
                bathrooms INT DEFAULT 1,
                square_feet INT,
                amenities TEXT,
                featured_image VARCHAR(255),
                status ENUM('available', 'occupied', 'maintenance', 'inactive') DEFAULT 'available',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX(owner_id),
                INDEX(status),
                INDEX(city)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Bookings Table
            "CREATE TABLE IF NOT EXISTS bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                property_id INT NOT NULL,
                tenant_id INT NOT NULL,
                owner_id INT NOT NULL,
                check_in_date DATE NOT NULL,
                check_out_date DATE NOT NULL,
                total_nights INT,
                total_price DECIMAL(10, 2) NOT NULL,
                status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(property_id) REFERENCES properties(id) ON DELETE CASCADE,
                FOREIGN KEY(tenant_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX(property_id),
                INDEX(tenant_id),
                INDEX(owner_id),
                INDEX(status),
                INDEX(check_in_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Ratings Table
            "CREATE TABLE IF NOT EXISTS ratings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                property_id INT NOT NULL,
                tenant_id INT NOT NULL,
                rating INT DEFAULT 5,
                review TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(property_id) REFERENCES properties(id) ON DELETE CASCADE,
                FOREIGN KEY(tenant_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY(property_id, tenant_id),
                INDEX(property_id),
                INDEX(tenant_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Members Table (Tenant Additional Info)
            "CREATE TABLE IF NOT EXISTS members (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNIQUE NOT NULL,
                date_of_birth DATE,
                gender ENUM('male', 'female', 'other'),
                address VARCHAR(255),
                occupation VARCHAR(100),
                company_name VARCHAR(100),
                id_type VARCHAR(50),
                id_number VARCHAR(50),
                emergency_contact VARCHAR(100),
                emergency_phone VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX(user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Email Logs Table
            "CREATE TABLE IF NOT EXISTS email_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                recipient_email VARCHAR(100) NOT NULL,
                subject VARCHAR(255),
                email_type VARCHAR(50),
                status ENUM('sent', 'failed', 'bounced') DEFAULT 'sent',
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX(recipient_email),
                INDEX(email_type),
                INDEX(status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            // Activity Logs Table
            "CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(100),
                description TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX(user_id),
                INDEX(created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        foreach ($tables as $query) {
            if (!$this->db->query($query)) {
                $this->addError("Error creating table: " . $this->db->error);
            }
        }

        $this->addSuccess("Database tables created successfully ✓");
        echo "\n";
    }

    /**
     * Create default admin user
     */
    private function createDefaultAdmin() {
        echo "Step 7: Creating default admin account...\n";

        if (!$this->db) {
            $this->addError("Database not connected");
            echo "\n";
            return;
        }

        $adminEmail = 'admin@rentcms.local';
        $adminPassword = 'admin@123';
        $adminUsername = 'admin';
        $fullName = 'System Administrator';

        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (username, email, password, full_name, role, status, email_verified) 
                  VALUES (?, ?, ?, ?, 'admin', 'active', 1)
                  ON DUPLICATE KEY UPDATE id=id";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            $this->addError("Error preparing statement: " . $this->db->error);
            echo "\n";
            return;
        }

        $stmt->bind_param("ssss", $adminUsername, $adminEmail, $hashedPassword, $fullName);

        if ($stmt->execute()) {
            $this->addSuccess("Default admin account created ✓");
            echo Colors::YELLOW . "  Email: $adminEmail\n";
            echo "  Password: $adminPassword\n";
            echo "  ⚠️  Change this password immediately after first login!\n" . Colors::RESET;
        } else {
            $this->addError("Error creating admin: " . $stmt->error);
        }

        $stmt->close();
        echo "\n";
    }

    /**
     * Set proper file permissions
     */
    private function setPermissions() {
        echo "Step 8: Setting file permissions...\n";

        $directories = [
            'public/uploads' => 0755,
            'logs' => 0755,
            'temp' => 0755
        ];

        foreach ($directories as $dir => $perms) {
            if (file_exists($dir)) {
                chmod($dir, $perms);
                $this->addSuccess("Permissions set for: $dir ✓");
            }
        }
        echo "\n";
    }

    /**
     * Print installation summary
     */
    private function printSummary() {
        echo str_repeat("=", 60) . "\n\n";
        echo Colors::BLUE . "Installation Summary\n" . Colors::RESET;
        echo str_repeat("-", 60) . "\n\n";

        if (!empty($this->success)) {
            echo Colors::GREEN . "✓ Successful Steps:\n" . Colors::RESET;
            foreach ($this->success as $msg) {
                echo "  • $msg\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo Colors::YELLOW . "⚠ Warnings:\n" . Colors::RESET;
            foreach ($this->warnings as $msg) {
                echo "  • $msg\n";
            }
            echo "\n";
        }

        if (!empty($this->errors)) {
            echo Colors::RED . "✗ Errors:\n" . Colors::RESET;
            foreach ($this->errors as $msg) {
                echo "  • $msg\n";
            }
            echo "\n";
            echo Colors::RED . "Installation completed with errors!\n" . Colors::RESET;
            echo "Please fix the errors above and run the installation script again.\n";
        } else {
            echo Colors::GREEN . "✓ Installation completed successfully!\n\n" . Colors::RESET;
            echo "Next steps:\n";
            echo "1. Update the admin password: http://localhost/rentcms/admin/profile\n";
            echo "2. Configure your rental properties\n";
            echo "3. Set up email notifications\n";
            echo "4. Customize the application settings\n\n";
            echo "Documentation: https://github.com/yash0010-cyber/rentcms\n";
        }

        echo str_repeat("=", 60) . "\n";
    }

    /**
     * Add success message
     */
    private function addSuccess($msg) {
        $this->success[] = $msg;
        echo Colors::GREEN . "✓ $msg\n" . Colors::RESET;
    }

    /**
     * Add error message
     */
    private function addError($msg) {
        $this->errors[] = $msg;
        echo Colors::RED . "✗ $msg\n" . Colors::RESET;
    }

    /**
     * Add warning message
     */
    private function addWarning($msg) {
        $this->warnings[] = $msg;
        echo Colors::YELLOW . "⚠ $msg\n" . Colors::RESET;
    }

    /**
     * Get user input from prompt
     */
    private function prompt($label) {
        echo Colors::BLUE . $label . ": " . Colors::RESET;
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        return $line;
    }
}

// Run installation
$installer = new Installer();
$installer->run();
?>
