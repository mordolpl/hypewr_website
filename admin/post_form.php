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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-box { max-width:600px; margin:40px auto; }
        textarea { width:100%; height:200px; }
        input, textarea { margin-bottom:10px; padding:8px; }
    </style>
</head>
<body>
    <div id="nav-placeholder"></div>
    <div class="form-box">
        <h1><?= $edit ? 'Edytuj wpis' : 'Dodaj wpis' ?></h1>
        <?php if ($errors): ?>
            <div style="color:red;">
                <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label>Tytuł:<br><input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required></label><br>
            <label>Treść:<br><textarea name="content" required><?= htmlspecialchars($post['content']) ?></textarea></label><br>
            <button type="submit"><?= $edit ? 'Zapisz zmiany' : 'Dodaj' ?></button>
        </form>
        <p><a href="dashboard.php">Powrót</a></p>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>