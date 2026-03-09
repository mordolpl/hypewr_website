<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$edit = false;
$post = ['title' => '', 'content' => '', 'author' => $_SESSION['username'] ?? 'Administrator', 'cover_image' => ''];

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
    $author = trim($_POST['author'] ?? 'Administrator');
    $cover_image = trim($_POST['cover_image'] ?? '');
    
    if ($title === '' || $content === '') {
        $errors[] = 'Tytuł i treść są wymagane.';
    }
    if (empty($errors)) {
        if ($edit) {
            updatePost($id, $title, $content, $author, $cover_image);
        } else {
            createPost($title, $content, $author, $cover_image);
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
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            overflow: hidden; /* Zapobiega przewijaniu całej strony, edytor będzie miał swój pasek */
        }
        .form-box {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            max-width: none !important; /* Nadpisuje limit z admin-style.css */
            margin: 0 !important;       /* Nadpisuje center margin z admin-style.css */
            padding: 20px;
            box-sizing: border-box;
            background: #fff;
        }
        form {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-row h1 {
            margin: 0;
            color: #333;
        }
        .header-row a, .header-row button {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .header-row a {
            background: #eee;
            color: #333;
            margin-right: 10px;
        }
        .header-row button {
            background: #007bff;
            color: #fff;
        }
        .title-input {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            background: #fff !important; /* nadpisanie ciemnego koloru */
            color: #000 !important;
            box-sizing: border-box;
        }
        /* Konfiguracja Quill żeby zajął resztę ekranu */
        #editor-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            /* Odejmujemy marginesy i tytuł żeby idealnie wpasować edytor */
        }
        .ql-toolbar.ql-snow {
            border-radius: 5px 5px 0 0;
            background: #f8f9fa;
        }
        .ql-container.ql-snow {
            border-radius: 0 0 5px 5px;
            background: #fff;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .ql-editor {
            flex-grow: 1;
            font-size: 16px;
            color: #000;
            overflow-y: auto; /* Fix for scrolling */
            max-height: calc(100vh - 200px); /* Limit height so toolbar stays visible */
        }
    </style>
</head>
<body>
<div class="form-box">
    <div class="header-row">
        <h1><?= $edit ? 'Edytuj wpis' : 'Dodaj wpis' ?></h1>
        <div>
            <a href="dashboard.php">Anuluj / Wróć</a>
            <!-- Zmiana przycisku form na zewnętrzny button zlecający submit form za pomocą JS (dla wygody układu na górze) -->
            <button type="button" onclick="document.getElementById('post-form').requestSubmit();"><?= $edit ? 'Zapisz zmiany' : 'Opublikuj' ?></button>
        </div>
    </div>
    
    <?php if ($errors): ?>
        <div class="error-box" style="color: red; margin-bottom: 15px;">
            <?php foreach ($errors as $e): ?><p><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form id="post-form" method="post" action="" style="height: 100%;">
        <input type="text" class="title-input" id="title" name="title" placeholder="Wpisz tytuł artykułu..." value="<?= htmlspecialchars($post['title']) ?>" required>
        
        <div style="display: flex; gap: 20px;">
            <input type="text" class="title-input" id="author" name="author" placeholder="Autor publikacji..." value="<?= htmlspecialchars($post['author']) ?>" style="font-size: 1rem; padding: 10px; margin-bottom: 20px; flex: 1;">
            
            <div style="flex: 2; display: flex; flex-direction: column;">
                <input type="text" class="title-input" id="cover_image_url" placeholder="URL okładki (lub wgraj plik poniżej) -> domyślnie kosmos" value="<?= htmlspecialchars($post['cover_image']) ?>" style="font-size: 1rem; padding: 10px; margin-bottom: 5px;">
                <input type="file" id="cover_image_file" accept="image/*" style="font-size: 0.9rem; margin-bottom: 20px;">
                <input type="hidden" name="cover_image" id="cover_image_hidden" value="<?= htmlspecialchars($post['cover_image']) ?>">
            </div>
        </div>
        
        <!-- Ukryte pole dla prawdziwych danych wędrujących przez POST -->
        <input type="hidden" name="content" id="hiddenContent">
        
        <!-- Widoczny edytor Quill zapakowany w wrapper dla Flexboxa -->
        <div id="editor-wrapper">
            <div id="editor-container"><?= $post['content'] ?></div>
        </div>
    </form>
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
    var fileInput = document.getElementById('cover_image_file');
    var urlInput = document.getElementById('cover_image_url');
    var hiddenCoverInput = document.getElementById('cover_image_hidden');

    form.onsubmit = function(e) {
        // Pobieramy wygenerowany kod HTML i wrzucamy do inputa przed wysłaniem formularza
        var htmlContent = document.querySelector('.ql-editor').innerHTML;
        document.querySelector('#hiddenContent').value = htmlContent;
        
        // Obsługa okładki
        if (fileInput.files.length > 0) {
            // Zatrzymaj domyślne wysyłanie na chwilę żeby przekonwertować zdjęcie na Base64
            e.preventDefault();
            var reader = new FileReader();
            reader.onload = function(event) {
                hiddenCoverInput.value = event.target.result;
                form.submit(); // Ręcznie wrzuć formularz po konwersji
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            // Jeśli nie wgrano pliku, użyj wpisanego URL'a
            hiddenCoverInput.value = urlInput.value;
            // Pozostawiamy domyślne zachowanie submita - pójdzie dalej
        }
    };
</script>
</body>
</html>