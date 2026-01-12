# Student Management System

A modern PHP-based student management system with CRUD operations, user authentication, and a beautiful dark theme UI.

## Features

- ✅ **User Authentication** - Secure login with session management
- ✅ **CRUD Operations** - Create, Read, Update, Delete student records
- ✅ **SQL Injection Protection** - All queries use prepared statements
- ✅ **Search & Sort** - Find students quickly and sort by any column
- ✅ **Modern UI** - Beautiful dark theme with responsive design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (or XAMPP/WAMP/MAMP)

## Installation

### 1. Set up the database

1. Open phpMyAdmin or MySQL command line
2. Run the SQL commands in `database.sql`:

```bash
mysql -u root -p < database.sql
```

Or copy and paste the contents of `database.sql` into phpMyAdmin's SQL tab.

### 2. Configure database connection

Edit `config.php` and update these values if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'student_db');
```

### 3. Start your web server

If using XAMPP:
1. Place the project folder in `htdocs`
2. Start Apache and MySQL from XAMPP Control Panel
3. Visit `http://localhost/sudent/`

## Default Login Credentials

- **Username:** `admin`
- **Password:** `admin123`

## Project Structure

```
sudent/
├── config.php          # Database connection settings
├── header.php          # Reusable header component with navbar
├── footer.php          # Reusable footer component
├── login.php           # User authentication page
├── logout.php          # Session destruction
├── index.php           # Main dashboard (Read)
├── add_student.php     # Add new student (Create)
├── edit_student.php    # Edit student (Update)
├── delete_student.php  # Delete student (Delete)
├── database.sql        # Database schema and sample data
└── README.md           # This file
```

## Security Features

1. **Prepared Statements** - All database queries use prepared statements to prevent SQL injection
2. **Password Hashing** - User passwords are hashed using `password_hash()` with bcrypt
3. **Session Management** - Secure session handling with proper destruction on logout
4. **Input Validation** - All user inputs are validated and sanitized
5. **Output Escaping** - All output is escaped using `htmlspecialchars()` to prevent XSS

## Usage

1. Login with admin credentials
2. View all students on the dashboard
3. Use the search box to find specific students
4. Click column headers to sort
5. Use "Add Student" to create new records
6. Use "Edit" button to modify student information
7. Use "Delete" button to remove students

## License

This project is open source and available for educational purposes.
