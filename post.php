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
    
    <header class="sub-hero" style="height: 40vh; min-height: 300px; display: flex; justify-content: center; align-items: center; text-align: center; position: relative;">
        <!-- Background placeholder (can be dynamic if db had an image column) -->
        <img src="https://images.unsplash.com/photo-1541185933-ef5d8ed016c2?q=80&w=2000" class="hero-bg-img" alt="Post Background">
        <div class="video-overlay"></div>
        <div class="hero-content" style="z-index: 10; max-width: 1200px; padding: 0 40px; width: 100%;">
            <h1 style="font-size: 3.5rem; margin-bottom: 15px; text-shadow: 0 4px 20px rgba(0,195,255,0.4);"><?= htmlspecialchars($post['title']) ?></h1>
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px; color: var(--accent-primary); font-family: var(--font-head); letter-spacing: 2px; font-size: 1rem;">
                <span>PUBLIKACJA</span>
                <span style="width: 5px; height: 5px; background: var(--accent-primary); border-radius: 50%;"></span>
                <time><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></time>
            </div>
        </div>
    </header>

    <article class="section" style="padding-top: 60px; padding-bottom: 80px; max-width: 1400px; width: 90%; margin: 0 auto;">
        <div class="post-content" style="background: var(--card-bg); padding: 60px 80px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 50px rgba(0,0,0,0.6); font-size: 1.2rem; line-height: 1.8; color: #ddd; min-height: 50vh;">
            <?= $post['content'] ?>
        </div>
        
        <div style="margin-top: 60px; text-align: center;">
            <a href="pages/projekty.php" class="cta-btn outline" style="display: inline-flex; align-items: center; gap: 10px;">
                <span>←</span> Wróć do projektów
            </a>
        </div>
    </article>
    
    <style>
        /* Specific overrides for Quill content inside post-content to ensure images are responsive */
        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .post-content h1, .post-content h2, .post-content h3 {
            font-family: var(--font-head);
            color: #fff;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .post-content a {
            color: var(--accent-primary);
            text-decoration: underline;
        }
        .post-content ul, .post-content ol {
            padding-left: 20px;
            margin-bottom: 20px;
        }
        .post-content blockquote {
            border-left: 4px solid var(--accent-primary);
            padding-left: 15px;
            margin-left: 0;
            font-style: italic;
            color: #bbb;
        }
    </style>
    
    <div id="footer-placeholder"></div>
    <script src="js/script.js"></script>
</body>
</html>