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
    
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table emoticons template help',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 500,
        // Umożliwiamy wrzucanie zdjęć bez zewnętrznych API - TinyMCE zamieni je na Base64 Data URI
        automatic_uploads: true,
        images_upload_handler: function (blobInfo, success, failure) {
            // Zwracamy zdjęcie na żywo jako Base64 zakodowane w znaczniku <img src="...">
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(blobInfo.blob());
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
      });
    </script>
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