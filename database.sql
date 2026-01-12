-- Student Management System Database Setup
-- Run this SQL in phpMyAdmin or MySQL command line

-- Create database
CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    course VARCHAR(100) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
-- The password hash is generated using PHP's password_hash() function
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample students for testing
INSERT INTO students (name, email, phone, course, address) VALUES 
('John Smith', 'john.smith@email.com', '+1 555-0101', 'Computer Science', '123 Main Street, New York, NY 10001'),
('Emma Johnson', 'emma.johnson@email.com', '+1 555-0102', 'Business Administration', '456 Oak Avenue, Los Angeles, CA 90001'),
('Michael Brown', 'michael.brown@email.com', '+1 555-0103', 'Mechanical Engineering', '789 Pine Road, Chicago, IL 60601'),
('Sarah Davis', 'sarah.davis@email.com', '+1 555-0104', 'Psychology', '321 Elm Street, Houston, TX 77001'),
('James Wilson', 'james.wilson@email.com', '+1 555-0105', 'Data Science', '654 Maple Drive, Phoenix, AZ 85001');

-- Show created tables
SHOW TABLES;

-- Verify data
SELECT * FROM users;
SELECT * FROM students;
