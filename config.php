<?php
// config.php - database configuration

function getDbConnection(): PDO {
    static $pdo;
    if ($pdo === null) {
        $host = 'db';
        $db = 'hyper_db';
        $user = 'hyper_admin';
        $pass = 'hmyUNq!7uf!am';
        $dsn = "pgsql:host=$host;dbname=$db";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // Add author column if it doesn't exist
        try {
            $pdo->exec("ALTER TABLE posts ADD COLUMN author VARCHAR(255) DEFAULT 'Administrator'");
        } catch (PDOException $e) {}
        
        // Add cover_image column if it doesn't exist
        try {
            $pdo->exec("ALTER TABLE posts ADD COLUMN cover_image TEXT DEFAULT ''");
        } catch (PDOException $e) {}
    }
    return $pdo;
}
?>