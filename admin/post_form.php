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
    
    <!-- Quill CSS & JS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        .ql-editor {
            min-height: 400px;
            font-size: 16px;
            background: #fff;
            color: #000;
        }
        .ql-toolbar {
            background: #f4f4f4;
        }
    </style>
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
        <!-- Ukryte pole dla prawdziwych danych wędrujących przez POST -->
        <input type="hidden" name="content" id="hiddenContent">
        
        <!-- Widoczny edytor Quill -->
        <div id="editor-container"><?= $post['content'] ?></div>

        <button type="submit"><?= $edit ? 'Zapisz zmiany' : 'Dodaj' ?></button>
    </form>
    <p><a href="dashboard.php">Powrót do panelu</a></p>
</div>

<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });

    var form = document.querySelector('form');
    form.onsubmit = function() {
        // Pobieramy wygenerowany kod HTML i wrzucamy do inputa przed wysłaniem formularza
        var htmlContent = document.querySelector('.ql-editor').innerHTML;
        document.querySelector('#hiddenContent').value = htmlContent;
    };
</script>
</body>
</html>