<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// simple auth check
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding:8px; border:1px solid #ccc; text-align:left; }
        a.button { display:inline-block; padding:6px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:3px; }
    </style>
</head>
<body>
    <div id="nav-placeholder"></div>
    <h1>Panel admina</h1>
    <p><a href="post_form.php" class="button">Dodaj nowy wpis</a> | <a href="logout.php">Wyloguj</a></p>

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
                        <a href="post_form.php?id=<?= $p['id'] ?>">Edytuj</a> |
                        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Usuń wpis?');">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <script src="../js/script.js"></script>

<?php
// handle deletion if requested
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    deletePost($id);
    header('Location: dashboard.php');
    exit;
}
?>
</body>
</html>