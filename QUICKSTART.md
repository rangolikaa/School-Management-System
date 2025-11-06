# Quick Start Guide

Get your School Management System up and running in minutes!

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server

## Installation Methods

### Method 1: Installation Wizard (Recommended)

1. **Upload files** to your web server directory
2. **Open browser** and navigate to: `http://your-domain/install.php`
3. **Follow the wizard**:
   - Enter database credentials
   - Import database schema
   - Create admin user
4. **Done!** You can now login

### Method 2: Manual Installation

1. **Import database**:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

2. **Configure database**:
   - Edit `database/connection.php`
   - Update database credentials

3. **Import sample data** (optional):
   ```bash
   mysql -u root -p school_management < database/sample_data.sql
   ```

4. **Access the system**:
   - Navigate to `http://your-domain/auth/login.php`
   - Default credentials: `admin` / `admin123`

## First Steps After Installation

1. **Change Admin Password**:
   - Login with default credentials
   - Go to your profile settings (if available) or update directly in database

2. **Create Academic Session**:
   - Navigate to Sessions (Admin only)
   - Click "Add Session"
   - Enter start and end year
   - Set as active session

3. **Add Classes**:
   - Go to Classes
   - Click "Add Class"
   - Enter class name (e.g., "Class 1", "Grade 5")

4. **Add Sections**:
   - Go to Sections
   - Click "Add Section"
   - Select a class
   - Enter section name (e.g., "A", "B", "C")

5. **Add Teachers**:
   - Go to Teachers
   - Click "Add Teacher"
   - Fill in teacher details
   - Assign teachers to classes

6. **Add Students**:
   - Go to Students
   - Click "Add Student"
   - Fill in student information
   - Select class and section

## Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change this password immediately after first login!

## Troubleshooting

### Database Connection Error
- Check `database/connection.php` credentials
- Verify MySQL service is running
- Ensure database exists

### Session Switcher Not Showing
- Make sure at least one session exists in the database
- Verify you're logged in

### Permission Denied Errors
- Check file permissions (644 for files, 755 for directories)
- Ensure PHP has read/write access

### Page Not Found (404)
- Check if mod_rewrite is enabled (for Apache)
- Verify .htaccess file exists
- Check web server configuration

## Need Help?

- Check the main [README.md](README.md) for detailed documentation
- Review [SECURITY.md](SECURITY.md) for security best practices
- Check database schema in `database/schema.sql`

## Common Tasks

### Reset Admin Password
```sql
UPDATE users SET password = '$2y$10$YourHashedPasswordHere' WHERE username = 'admin';
```
Generate hash using:
```php
<?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>
```

### Clear All Data
```sql
DROP DATABASE school_management;
CREATE DATABASE school_management;
-- Then import schema.sql again
```

### Backup Database
```bash
mysqldump -u root -p school_management > backup.sql
```

## Next Steps

- Customize the application settings in `includes/config.php`
- Review security settings in `.htaccess`
- Set up regular database backups
- Configure email settings (for future features)

Happy managing! 🎓
