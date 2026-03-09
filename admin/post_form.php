<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$edit = false;
$post = ['title' => '', 'content' => '', 'author' => $_SESSION['username'] ?? 'Administrator', 'cover_image' => '', 'category' => 'Inne'];

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
    $category = trim($_POST['category'] ?? 'Inne');
    
    if ($title === '' || $content === '') {
        $errors[] = 'Tytuł i treść są wymagane.';
    }
    if (empty($errors)) {
        if ($edit) {
            updatePost($id, $title, $content, $author, $cover_image, $category);
        } else {
            createPost($title, $content, $author, $cover_image, $category);
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
    <link rel="stylesheet" href="admin-style.css?v=<?= time() ?>">
    
    <!-- Quill CSS & JS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: var(--body-bg);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .form-box {
            display: flex;
            flex-direction: column;
            width: 100vw;
            height: 100vh;
            max-width: none !important;
            margin: 0 !important;
            padding: 40px 60px;
            box-sizing: border-box;
            background: var(--body-bg);
            box-shadow: none;
            border: none;
        }
        form {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }
        .header-row h1 {
            margin: 0;
            color: var(--text-main);
            font-size: 1.8rem;
        }
        .header-row a, .header-row button {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            border: 1px solid var(--border-color);
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .header-row a {
            background: #fff;
            color: var(--text-main);
            margin-right: 12px;
        }
        .header-row a:hover {
            background: #fafafa;
        }
        .header-row button {
            background: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
        }
        .header-row button:hover {
            background: var(--primary-hover);
        }
        
        #editor-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            background: #fff;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 14px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            background: #fafafa;
            padding: 12px 15px !important;
        }
        .ql-container.ql-snow {
            border: none !important;
            background: #fff;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .ql-editor {
            flex-grow: 1;
            font-size: 1.1rem;
            color: var(--text-main);
            padding: 30px !important;
            overflow-y: auto;
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
        <input type="text" class="title-input" id="title" name="title" placeholder="Wpisz tytuł artykułu..." value="<?= htmlspecialchars($post['title']) ?>" required style="background: transparent;">
        
        <div class="flex-row" style="display: flex; gap: 20px;">
            <div style="flex: 1; display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
                <input type="text" class="title-input" id="author" name="author" placeholder="Autor publikacji..." value="<?= htmlspecialchars($post['author']) ?>" style="font-size: 1.1rem; padding: 10px 0; background: transparent;">
                
                <select name="category" id="category" style="font-size: 1.1rem; padding: 10px; background: #fff; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-main); font-family: var(--font-body); width: 100%;">
                    <?php 
                        $categories = ['Integrot', 'IT', 'Mechanika', 'Elektronika', 'Wydarzenia', 'Inne'];
                        foreach ($categories as $cat): 
                            $selected = ($post['category'] === $cat) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $selected ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="flex: 2; display: flex; flex-direction: column;">
                <input type="text" class="title-input" id="cover_image_url" placeholder="URL okładki (lub wgraj plik poniżej) -> domyślnie kosmos" value="<?= htmlspecialchars($post['cover_image']) ?>" style="font-size: 1.1rem; padding: 10px 0; margin-bottom: 5px; background: transparent;">
                <input type="file" id="cover_image_file" accept="image/*" style="font-size: 0.95rem; margin-bottom: 30px; color: var(--text-muted); font-family: var(--font-family);">
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