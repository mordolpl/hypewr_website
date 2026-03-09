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

// --- FETCH DASHBOARD STATISTICS ---
$db = db();

// Total posts
$totalStmt = $db->query('SELECT COUNT(*) FROM posts');
$totalPosts = (int)$totalStmt->fetchColumn();

// Most popular post (by views)
$popStmt = $db->query('SELECT title, views FROM posts ORDER BY views DESC LIMIT 1');
$popularPost = $popStmt->fetch();
$popTitle = $popularPost ? $popularPost['title'] : 'Brak';
$popViews = $popularPost ? (int)$popularPost['views'] : 0;

// Last updated date
$updateStmt = $db->query('SELECT MAX(updated_at) FROM posts');
$lastUpdateRow = $updateStmt->fetchColumn();
$lastUpdate = $lastUpdateRow ? date('d.m.Y', strtotime($lastUpdateRow)) : 'Nigdy';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="admin-style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <div class="admin-header">
        <h1>Panel admina</h1>
        <a href="logout.php">Wyloguj</a>
    </div>

    <p><a href="post_form.php" class="button">Dodaj nowy wpis</a></p>

    <!-- DASHBOARD STATS ROW -->
    <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; margin-top: 30px;">
        <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: center;">
            <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Razem Wpisów</span>
            <span style="color: var(--text-main); font-size: 2.2rem; font-weight: 700;"><?= $totalPosts ?></span>
        </div>
        
        <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: center;">
            <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Najpopularniejszy Wpis</span>
            <span style="color: var(--text-main); font-size: 1.2rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($popTitle) ?>"><?= htmlspecialchars($popTitle) ?></span>
            <span style="color: var(--primary-color); font-size: 0.9rem; font-weight: 600; margin-top: 5px;">👁️ <?= $popViews ?> odsłon</span>
        </div>
        
        <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: center;">
            <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Ostatnia Aktualizacja</span>
            <span style="color: var(--text-main); font-size: 1.6rem; font-weight: 700;"><?= $lastUpdate ?></span>
        </div>
    </div>

    <?php if (count($posts) === 0): ?>
        <p>Brak wpisów w bazie.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr><th>ID</th><th>Tytuł</th><th>Autor</th><th>Data</th><th>Akcje</th></tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= htmlspecialchars($p['author'] ?? 'Administrator') ?></td>
                        <td><?= htmlspecialchars($p['created_at']) ?></td>
                        <td style="display: flex; gap: 10px; border-bottom: none;">
                            <a href="post_form.php?id=<?= $p['id'] ?>" class="edit-btn">Edytuj</a>
                            <a href="?delete=<?= $p['id'] ?>" class="danger" onclick="return confirm('Usuń wpis?');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>