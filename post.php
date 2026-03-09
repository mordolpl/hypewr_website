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

// Przygotowanie danych do meta tagów
$plain_text = strip_tags($post['content']);
// Usunięcie nadmiarowych białych znaków i przycięcie do ok. 150 znaków
$og_description = mb_strimwidth(trim(preg_replace('/\s+/', ' ', $plain_text)), 0, 150, '...');

// Domyślne zdjęcie (np. logo lub tło strony)
$og_image = 'https://images.unsplash.com/photo-1541185933-ef5d8ed016c2?q=80&w=2000'; 
// Wyszukanie pierwszego tagu <img> w treści posta
if (preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $post['content'], $match)) {
    $og_image = $match['src'];
}

// Pobranie aktualnego URL dla og:url
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Obliczenie czasu czytania
$word_count = str_word_count(strip_tags($post['content']));
$reading_time_minutes = ceil($word_count / 200); // Zakładamy średnie tempo 200 słów/minutę
if ($reading_time_minutes < 1) $reading_time_minutes = 1;

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - HYPEwR</title>
    
    <!-- Dynamiczne Meta Tagi SEO / Open Graph -->
    <meta name="description" content="<?= htmlspecialchars($og_description) ?>">
    
    <!-- Open Graph (Facebook, LinkedIn, Discord etc) -->
    <meta property="og:title" content="<?= htmlspecialchars($post['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($og_description) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($current_url) ?>">
    <meta property="og:type" content="article">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($post['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($og_description) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($og_image) ?>">
    
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
            <div style="display: flex; justify-content: center; align-items: center; gap: 15px; color: var(--accent-primary); font-family: var(--font-head); letter-spacing: 1px; font-size: 1rem; flex-wrap: wrap;">
                <span><svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg><?= htmlspecialchars($post['author'] ?? 'Administrator') ?></span>
                <span style="width: 5px; height: 5px; background: var(--accent-primary); border-radius: 50%;"></span>
                <time><?= date('d.m.Y H:i', strtotime($post['created_at'])) ?></time>
                <span style="width: 5px; height: 5px; background: var(--accent-primary); border-radius: 50%;"></span>
                <span>⏳ <?= $reading_time_minutes ?> min czytania</span>
            </div>
        </div>
    </header>

    <article class="section" style="padding-top: 60px; padding-bottom: 80px; max-width: 1800px; width: 95%; margin: 0 auto;">
        <div class="post-content" style="background: var(--card-bg); padding: 60px 80px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 50px rgba(0,0,0,0.6); font-size: 1.25rem; line-height: 1.9; color: #ddd; min-height: 50vh; overflow-wrap: anywhere; word-break: break-word;">
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
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            cursor: zoom-in;
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
    
    <!-- MediumZoom library for zooming images like on Medium.com -->
    <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.0.8/dist/medium-zoom.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mediumZoom('.post-content img', {
                margin: 24,
                background: 'rgba(5, 5, 8, 0.95)',
                scrollOffset: 0,
            });
        });
    </script>
</body>
</html>