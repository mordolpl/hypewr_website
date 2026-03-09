<?php
require_once __DIR__ . '/includes/functions.php';
if (!isset($_GET['id'])) {
    header('Location: pages/projekty.php');
    exit;
}
$id = (int)$_GET['id'];
$post = getPostById($id);
if (!$post) {
    http_response_code(404);
    echo 'Wpis nie znaleziony.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="nav-placeholder"></div>
    <article class="section" style="padding-top:120px; padding-bottom:40px; padding-left:40px; padding-right:40px; max-width:800px; margin:0 auto;">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <small><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
    </article>
    <div id="footer-placeholder"></div>
    <script src="js/script.js"></script>
</body>
</html>