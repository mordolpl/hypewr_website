<?php
require_once __DIR__ . '/includes/db.php';

try {
    $db = db();
    $stmt = $db->query('SELECT id, username, password_hash FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "USERS IN DB:\n";
    print_r($users);
    
    // Test PHP's logic on the first user just in case
    if (!empty($users)) {
        $u = $users[0];
        echo "\nTESTING PASSWORDS for {$u['username']}:\n";
        echo "password_verify('admin', hash): " . (password_verify('admin', $u['password_hash']) ? 'true' : 'false') . "\n";
        echo "plain text match: " . ('admin' === $u['password_hash'] ? 'true' : 'false') . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
