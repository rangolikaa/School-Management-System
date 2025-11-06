# School-Management-System
# School Management System

A complete school management system web application built with Core PHP and MySQL, featuring multi-session logic for managing academic sessions, students, classes, sections, and teachers.

## Features

- **Multi-Session Management**: Switch between academic sessions and view/edit session-specific data
- **Student Management**: Complete CRUD operations for students with session-wise filtering
- **Class Management**: Create and manage classes linked to sessions
- **Section Management**: Create sections for classes
- **Teacher Management**: Manage teachers and assign them to classes
- **Authentication**: Admin and teacher role-based access control
- **Responsive Design**: Clean, modern, mobile-friendly UI built with custom CSS
- **No Dependencies**: Pure Core PHP, MySQL, HTML, CSS, and minimal JavaScript

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PHP extensions: mysqli, mbstring

## Installation

1. **Clone or download** this project to your web server directory (e.g., `htdocs`, `www`, or `public_html`)

2. **Create the database**:
   - Open phpMyAdmin or MySQL command line
   - Import `database/schema.sql` to create the database and tables

3. **Configure database connection**:
   - Open `database/connection.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'school_management');
     ```

4. **Import sample data** (optional):
   - Import `database/sample_data.sql` to add sample data for testing

5. **Set proper permissions**:
   - Ensure PHP has read/write permissions to the project directory

6. **Access the application**:
   - Open your browser and navigate to: `http://localhost/school-management/auth/login.php`
   - Default login credentials:
     - Username: `admin`
     - Password: `admin123`

## Project Structure

```
school-management/
├── auth/
│   ├── login.php          # Login page
│   └── logout.php         # Logout handler
├── classes/
│   ├── index.php          # List classes
│   ├── add.php            # Add class
│   ├── edit.php           # Edit class
│   └── delete.php         # Delete class
├── database/
│   ├── connection.php     # Database connection
│   ├── schema.sql         # Database schema
│   └── sample_data.sql    # Sample data
├── includes/
│   ├── auth.php           # Authentication functions
│   ├── session_handler.php # Session management
│   ├── switch_session.php # Session switcher handler
│   ├── get_sections.php   # AJAX endpoint for sections
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── sections/
│   ├── index.php          # List sections
│   ├── add.php            # Add section
│   ├── edit.php           # Edit section
│   └── delete.php         # Delete section
├── sessions/
│   ├── index.php          # List sessions (Admin only)
│   ├── add.php            # Add session
│   ├── edit.php           # Edit session
│   └── delete.php         # Delete session
├── students/
│   ├── index.php          # List students
│   ├── add.php            # Add student
│   ├── edit.php           # Edit student
│   └── delete.php         # Delete student
├── teachers/
│   ├── index.php          # List teachers
│   ├── add.php            # Add teacher
│   ├── edit.php           # Edit teacher
│   ├── assign.php         # Assign teacher to classes
│   └── delete.php         # Delete teacher
├── css/
│   └── style.css          # Custom CSS styles
├── js/
│   └── main.js            # JavaScript functions
├── index.php              # Dashboard
└── README.md              # This file
```

## Usage

### Admin Features

1. **Manage Sessions**:
   - Create new academic sessions
   - Set active session
   - Edit or delete sessions

2. **Manage Classes**:
   - Add classes for the current session
   - Edit class names
   - Delete classes (cascades to sections and students)

3. **Manage Sections**:
   - Add sections to classes
   - Edit section names
   - Delete sections (cascades to students)

4. **Manage Students**:
   - Add students with class, section, and parent information
   - Edit student details
   - Delete students
   - View all students filtered by current session

5. **Manage Teachers**:
   - Add teachers with contact information
   - Edit teacher details
   - Assign teachers to multiple classes
   - Delete teachers

### Teacher Features

- View students (filtered by current session)
- View classes and sections
- Switch between sessions to view historical data

### Session Switching

- Use the session switcher in the header to change the current session
- All views and data are automatically filtered by the selected session
- The active session is marked in the dropdown

## Database Schema

- **sessions**: Academic sessions (id, session_start, session_end, is_active)
- **teachers**: Teacher information (id, name, email, mobile)
- **classes**: Classes linked to sessions (id, session_id, class_name)
- **sections**: Sections linked to classes (id, class_id, section_name)
- **class_teachers**: Teacher-class assignments (id, class_id, teacher_id)
- **students**: Student information (id, name, class_id, section_id, session_id, mobile, father_name, mother_name)
- **users**: User authentication (id, username, password, role, teacher_id)

## Security Features

- Prepared statements to prevent SQL injection
- Password hashing using bcrypt
- Session-based authentication
- Role-based access control (Admin/Teacher)
- Input validation and sanitization
- XSS protection with htmlspecialchars()

## Customization

### Change Password

To change the admin password, you can create a simple PHP script:

```php
<?php
$password = password_hash('your_new_password', PASSWORD_DEFAULT);
echo $password;
?>
```

Then update the users table:

```sql
UPDATE users SET password = 'hashed_password_here' WHERE username = 'admin';
```

### Modify Database Connection

Edit `database/connection.php` to change database credentials.

### Customize Styling

Edit `css/style.css` to modify the appearance. The file uses CSS variables for easy theming.

## Troubleshooting

1. **Database connection error**: Check database credentials in `database/connection.php`
2. **Permission denied**: Ensure PHP has proper file permissions
3. **Login not working**: Verify users table has the admin user with correct password hash
4. **Session switcher not working**: Ensure sessions table has at least one session

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## License

This project is open-source and available for educational and commercial use.

## Support

For issues or questions, please check the code comments or refer to the database schema for structure details.

## Future Enhancements

- Export data to Excel/PDF
- Attendance management
- Grade/Report card system
- Fee management
- Parent portal
- SMS/Email notifications
- Advanced search and filtering
