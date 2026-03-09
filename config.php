<?php
// config.php - Database configuration for PostgreSQL

function getDbConnection(): PDO {
    // Modify these with your actual PostgreSQL credentials
    $host = '127.0.0.1';
    $db   = 'hypewr';
    $user = 'postgres';
    $pass = 'postgres'; // or your password
    $port = '5432';

    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Initialize tables if they do not exist
        initDatabase($pdo);
        
        return $pdo;
    } catch (\PDOException $e) {
        // If the database 'hypewr' does not exist, it will throw an exception here.
        // We log the error. In a real app, do not echo the raw message.
        die("Connection failed: " . $e->getMessage() . " Please ensure PostgreSQL is running, the database '$db' exists, and credentials are correct.");
    }
}

function initDatabase(PDO $pdo) {
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Create posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL
        )
    ");

    // Insert default admin user if none exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        $defaultUser = 'admin';
        $defaultPass = password_hash('admin', PASSWORD_DEFAULT);
        
        $insertStmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password)");
        $insertStmt->execute([':username' => $defaultUser, ':password' => $defaultPass]);
    }
}
