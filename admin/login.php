<?php
// simple login form for administrator
session_start();
require_once __DIR__ . '/../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = db();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // login success
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $errors[] = 'Nieprawidłowy login lub hasło';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="login-box">
        <h2>Login administratora</h2>
        <?php if ($errors): ?>
            <div class="error-box">
                <?php foreach ($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Użytkownik:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Zaloguj się</button>
        </form>
    </div>
</body>
</html>