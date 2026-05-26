# RentCMS - Open Source Rental Management System

A production-ready CMS for managing rental properties, built with PHP 8.x, MySQL, and Bootstrap.

## Overview

RentCMS is an open-source rental management system designed for small rent business owners. It provides a complete solution to manage rental properties, tenants, bookings, and ratings.

## Features

✅ **Multi-Role Access Control**
- Owner login and management
- Tenant login and profile management
- Admin login (Protected)

✅ **Property Management**
- Add and manage rental houses
- View property details
- Rate houses and view ratings
- Property availability tracking

✅ **Tenant & Member Management**
- View tenant and member details
- Manage tenant profiles
- Track member information
- Maintain contact details

✅ **Booking System**
- View all bookings
- Perform new bookings
- Booking history tracking
- Availability verification

✅ **Communication**
- Email verification for signup
- Password recovery via email
- Email notifications

## Technology Stack

| Component | Technology |
|-----------|-----------|
| **Language** | PHP 8.x (Stable) |
| **Frontend** | Bootstrap 5+ (CDN) |
| **Database** | MySQL (Latest Version) |
| **Email Service** | PHPMailer |
| **Server** | Apache/Nginx |

## Installation Guide

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/yash0010-cyber/rentcms.git
cd rentcms
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Database Setup

1. Create a new MySQL database:

```sql
CREATE DATABASE rentcms_db;
```

2. Import the database schema:

```bash
mysql -u root -p rentcms_db < database/schema.sql
```

### Step 4: Configure Environment

1. Copy the configuration template:

```bash
cp config/config.example.php config/config.php
```

2. Edit `config/config.php` with your database credentials:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'rentcms_db');

// Email Configuration (PHPMailer)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
define('MAIL_FROM', 'noreply@rentcms.local');
?>
```

### Step 5: Run Installation Script

Execute the installation script to set up the application:

```bash
php install.php
```

This script will:
- Create necessary database tables
- Set up default admin account
- Configure file permissions
- Verify all dependencies

### Step 6: Configure Web Server

**For Apache:**

Create `.htaccess` file in the root directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /rentcms/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [QSA,L]
</IfModule>
```

**For Nginx:**

Add to your server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Step 7: Start the Application

Point your browser to: `http://localhost/rentcms` (or your configured domain)

## Default Login Credentials

After installation, use these credentials to log in:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@rentcms.local | admin@123 |

**⚠️ Important:** Change these credentials immediately after first login.

## Project Structure

```
rentcms/
├── config/
│   ├── config.php              # Main configuration file
│   ├── config.example.php      # Configuration template
│   └── database.php            # Database connection
├── public/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   ├── images/                 # Image assets
│   └── uploads/                # User uploaded files
├── src/
│   ├── controllers/            # Application controllers
│   │   ├── AuthController.php
│   │   ├── PropertyController.php
│   │   ├── BookingController.php
│   │   ├── TenantController.php
│   │   └── AdminController.php
│   ├── models/                 # Data models
│   │   ├── User.php
│   │   ├── Property.php
│   │   ├── Booking.php
│   │   ├── Rating.php
│   │   └── Tenant.php
│   ├── views/                  # View templates
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── properties/
│   │   ├── bookings/
│   │   └── admin/
│   ├── helpers/                # Helper functions
│   │   ├── Auth.php
│   │   ├── Email.php
│   │   └── Validator.php
│   └── middleware/             # Middleware classes
│       ├── AuthMiddleware.php
│       └── AdminMiddleware.php
├── database/
│   ├── schema.sql              # Database schema
│   └── migrations/             # Database migrations
├── install.php                 # Installation script
├── .htaccess                   # Apache rewrite rules
├── composer.json               # PHP dependencies
├── index.php                   # Application entry point
└── README.md                   # This file
```

## Usage Guide

### For Owners

1. **Login** with owner credentials
2. **Add Properties** through the property management dashboard
3. **View Bookings** for your properties
4. **Manage Details** of your rental properties
5. **Check Ratings** provided by tenants

### For Tenants

1. **Sign Up** with email verification
2. **Login** to your account
3. **Browse Properties** available for rent
4. **Make Bookings** for desired properties
5. **Rate Properties** after renting
6. **View Booking History** and current bookings

### For Admins

1. **Login** with admin credentials (protected area)
2. **Manage Users** - View, edit, or suspend user accounts
3. **Manage Properties** - Approve or remove properties
4. **Manage Bookings** - View all system bookings
5. **View Reports** - Generate system reports
6. **Manage Settings** - Configure system settings

## Database Schema

### Tables

- **users** - User accounts (owners, tenants, admins)
- **properties** - Rental property listings
- **bookings** - Booking records
- **ratings** - Property ratings and reviews
- **members** - Member/tenant additional information
- **email_logs** - Email sending logs

## Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- CSRF token validation on forms
- XSS protection through output encoding
- Session-based authentication
- Role-based access control (RBAC)

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/forgot-password` - Password recovery

### Properties
- `GET /api/properties` - List all properties
- `POST /api/properties` - Create new property
- `GET /api/properties/:id` - Get property details
- `PUT /api/properties/:id` - Update property
- `DELETE /api/properties/:id` - Delete property

### Bookings
- `GET /api/bookings` - List bookings
- `POST /api/bookings` - Create new booking
- `GET /api/bookings/:id` - Get booking details
- `PUT /api/bookings/:id` - Update booking status

### Ratings
- `GET /api/ratings` - Get property ratings
- `POST /api/ratings` - Submit new rating
- `GET /api/ratings/:propertyId` - Get ratings for property

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/config.php`
- Ensure database exists

### Email Not Sending
- Check SMTP credentials
- Verify "Less secure app access" is enabled (for Gmail)
- Check email logs at `logs/email.log`

### Permission Denied Errors
- Ensure `uploads/` and `logs/` directories are writable
- Run: `chmod -R 755 uploads/ logs/`

### PHP Version Issues
- Verify PHP 8.0+ with: `php -v`
- Update PHP if necessary

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

For issues, questions, or feature requests, please open an issue on GitHub:
https://github.com/yash0010-cyber/rentcms/issues

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Author

**Yash Singh**
- GitHub: [@yash0010-cyber](https://github.com/yash0010-cyber)

## Changelog

### Version 1.0.0 (Initial Release)
- Core authentication system
- Property management
- Booking system
- Rating system
- Email verification
- Admin panel

## Roadmap

- [ ] Mobile application
- [ ] Payment gateway integration
- [ ] Advanced reporting
- [ ] Multi-language support
- [ ] API rate limiting
- [ ] SMS notifications
- [ ] Calendar view for bookings
- [ ] Property analytics

## Disclaimer

This software is provided as-is. Users are responsible for complying with local regulations regarding rental property management and data protection laws (GDPR, etc.).

---

**Last Updated:** May 26, 2026

For the latest updates, visit: https://github.com/yash0010-cyber/rentcms
