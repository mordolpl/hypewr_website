<?php
// simple login form for administrator
session_start();
require_once __DIR__ . '/../includes/functions.php';

$errors = [];

// Jeśli użytkownik już ma ciastko, od razu go logujemy
if (empty($_SESSION['user_id']) && !empty($_COOKIE['admin_remember_token'])) {
    $db = db();
    $stmt = $db->prepare('SELECT id FROM users WHERE md5(username || password_hash) = :token LIMIT 1');
    $stmt->execute([':token' => $_COOKIE['admin_remember_token']]);
    $user_id = $stmt->fetchColumn();
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        header('Location: dashboard.php');
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = db();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['password_hash']) || $password === $user['password_hash'] || md5($password) === $user['password_hash'])) {
        // login success
        $_SESSION['user_id'] = $user['id'];
        
        if (isset($_POST['remember_me'])) {
            // Bezpieczny token dla bazy danych zapisany w ciastku na 30 dni
            $token = md5($user['username'] . $user['password_hash']);
            setcookie('admin_remember_token', $token, time() + (86400 * 30), "/"); // 86400 = 1 day
        }

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
    <link rel="stylesheet" href="admin-style.css?v=<?= time() ?>">
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
            
            <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="remember_me" name="remember_me" value="1" style="width: auto; margin: 0;">
                <label for="remember_me" style="margin: 0; font-weight: normal; cursor: pointer;">Zapamiętaj mnie (wylogowuje po 30 dniach)</label>
            </div>
            
            <button type="submit">Zaloguj się</button>
        </form>
    </div>
</body>
</html>