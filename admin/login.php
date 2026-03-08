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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* small overrides for login form */
        .login-box { max-width:400px; margin: 80px auto; padding: 20px; border:1px solid #ccc; background:#fff; }
        .login-box input { width: 100%; padding:8px; margin-bottom:10px; }
    </style>
</head>
<body>
    <div id="nav-placeholder"></div>
    <div class="login-box">
        <h2>Login administratora</h2>
        <?php if ($errors): ?>
            <div style="color:red;">
                <?php foreach ($errors as $e) echo '<p>'.htmlspecialchars($e).'</p>'; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label>Użytkownik:<br><input type="text" name="username" required></label><br>
            <label>Hasło:<br><input type="password" name="password" required></label><br>
            <button type="submit">Zaloguj się</button>
        </form>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>