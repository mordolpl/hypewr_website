<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// simple auth check and remember me fallback
if (empty($_SESSION['user_id'])) {
    if (!empty($_COOKIE['admin_remember_token'])) {
        $db = db();
        $stmt = $db->prepare('SELECT id FROM users WHERE md5(username || password_hash) = :token LIMIT 1');
        $stmt->execute([':token' => $_COOKIE['admin_remember_token']]);
        $user_id = $stmt->fetchColumn();
        if ($user_id) {
            $_SESSION['user_id'] = $user_id;
        } else {
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

// handle deletion if requested (must run before output)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    deletePost($id);
    header('Location: dashboard.php');
    exit;
}

$posts = getPosts();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
<div class="container">
    <div class="admin-header">
        <h1>Panel admina</h1>
        <a href="logout.php">Wyloguj</a>
    </div>

    <p><a href="post_form.php" class="button">Dodaj nowy wpis</a></p>

    <?php if (count($posts) === 0): ?>
        <p>Brak wpisów w bazie.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr><th>ID</th><th>Tytuł</th><th>Data</th><th>Akcje</th></tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                    <td>
                        <a href="post_form.php?id=<?= $p['id'] ?>">Edytuj</a>
                        <a href="?delete=<?= $p['id'] ?>" class="danger" onclick="return confirm('Usuń wpis?');">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>