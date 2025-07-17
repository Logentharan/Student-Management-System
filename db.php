<?php
session_start();
$host = 'localhost';
$dbname = 'students_db';
$username = 'root';
$password = '';

try {
    // First, try to connect to MySQL without specifying the database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists, create if not
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Now connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        student_id VARCHAR(20) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        major VARCHAR(50) DEFAULT 'Undeclared',
        year ENUM('Freshman', 'Sophomore', 'Junior', 'Senior', 'Graduate') DEFAULT 'Freshman',
        gpa DECIMAL(3,2) DEFAULT 0.00,
        credits INT DEFAULT 0,
        status VARCHAR(30) DEFAULT 'New Student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        code VARCHAR(20) NOT NULL,
        title VARCHAR(100) NOT NULL,
        credits INT NOT NULL,
        grade VARCHAR(2) NOT NULL,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");
    
} catch (PDOException $e) {
    // Handle connection error
    error_log("Database setup failed: " . $e->getMessage());
    die("Database setup failed. Please check your database configuration.");
}
?>