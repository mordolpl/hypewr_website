<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$edit = false;
$post = ['title' => '', 'content' => ''];

if (isset($_GET['id'])) {
    $edit = true;
    $id = (int)$_GET['id'];
    $existing = getPostById($id);
    if ($existing) {
        $post = $existing;
    } else {
        die('Wpis nie istnieje.');
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        $errors[] = 'Tytuł i treść są wymagane.';
    }
    if (empty($errors)) {
        if ($edit) {
            updatePost($id, $title, $content);
        } else {
            createPost($title, $content);
        }
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit ? 'Edytuj wpis' : 'Dodaj wpis' ?></title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
<div class="form-box">
    <h1><?= $edit ? 'Edytuj wpis' : 'Dodaj wpis' ?></h1>
    <?php if ($errors): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <label for="title">Tytuł:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

        <label for="content">Treść:</label>
        <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>

        <button type="submit"><?= $edit ? 'Zapisz zmiany' : 'Dodaj' ?></button>
    </form>
    <p><a href="dashboard.php">Powrót do panelu</a></p>
</div>
</body>
</html>