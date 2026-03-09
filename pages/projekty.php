<?php
// dynamic projects page with news posts
require_once __DIR__ . '/../includes/functions.php';

// At the moment, the database connection is not working.
// The projects are temporarily hardcoded here.
// Once the database is fixed, this should be fetched from a 'projects' table.
$projects = [
    [
        'img' => 'https://images.unsplash.com/photo-1535378437323-95288ac57185?q=80&w=800',
        'title_i18n' => 'projekty_card1_title',
        'title' => 'Prototype Pod I',
        'desc_i18n' => 'projekty_card1_desc',
        'desc' => 'Pierwsza iteracja kapsuły testowej. Skupienie na aerodynamice i pasywnej lewitacji.',
        'cta_i18n' => 'projekty_card1_cta',
        'cta' => 'Szczegóły',
        'link' => 'projekt-szczegoly.html'
    ],
    [
        'img' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=800',
        'title_i18n' => 'projekty_card2_title',
        'title' => 'Napęd LIM',
        'desc_i18n' => 'projekty_card2_desc',
        'desc' => 'Autorski projekt silnika liniowego, zapewniający precyzyjne przyspieszenie.',
        'cta_i18n' => 'projekty_card2_cta',
        'cta' => 'Wkrótce',
        'link' => '#'
    ],
    [
        'img' => 'https://images.unsplash.com/photo-1581093450021-4a7360e9a6b5?q=80&w=800',
        'title_i18n' => 'projekty_card3_title',
        'title' => 'Tor Próżniowy',
        'desc_i18n' => 'projekty_card3_desc',
        'desc' => 'Projekt infrastruktury testowej w skali 1:10 do badań nad dekompresją.',
        'cta_i18n' => 'projekty_card3_cta',
        'cta' => 'Wkrótce',
        'link' => '#'
    ]
];

$posts = [];
// The getPosts() function will fail if the database is not configured.
// To prevent a fatal error, we wrap it in a try-catch block.
try {
    $posts = getPosts();
} catch (Exception $e) {
    // You could log the error here if a logging system was in place.
    // For now, we'll just ensure $posts remains an empty array.
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="projekty_title">HYPEwR | Projekty</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="scroll-track-container"><div class="scroll-pod-indicator" id="scrollPod"></div></div>
    <div id="nav-placeholder"></div>

    <header class="sub-hero">
        <img src="https://images.unsplash.com/photo-1517420879524-86d64ac2f339?q=80&w=2000" class="hero-bg-img" alt="Projects">
        <div class="video-overlay"></div>
        <div class="hero-content">
            <h1 data-i18n="projekty_h1">Nasze Projekty</h1>
        </div>
    </header>

    <section class="section">
        <div class="grid-3 reveal">
            <?php foreach ($projects as $project): ?>
                <div class="card">
                    <div class="image-placeholder" style="background-image: url('<?= htmlspecialchars($project['img']) ?>');"></div>
                    <h3 class="card-title" data-i18n="<?= $project['title_i18n'] ?>"><?= htmlspecialchars($project['title']) ?></h3>
                    <p class="card-description" data-i18n="<?= $project['desc_i18n'] ?>"><?= htmlspecialchars($project['desc']) ?></p>
                    <a href="<?= htmlspecialchars($project['link']) ?>" class="cta-btn outline card-cta" data-i18n="<?= $project['cta_i18n'] ?>"><?= htmlspecialchars($project['cta']) ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- news posts from database -->
    <section class="section" id="news-section">
        <h2 class="section-title">Aktualności</h2>
        <div class="title-bar"></div>
        <?php if (empty($posts)): ?>
            <p>Brak aktualności do wyświetlenia.</p>
        <?php else: ?>
            <div class="grid-3 reveal">
                <?php foreach ($posts as $post): ?>
                    <div class="card">
                        <?php $coverUrl = !empty($post['cover_image']) ? htmlspecialchars($post['cover_image']) : 'https://images.unsplash.com/photo-1495020689067-958852a7765e?q=80&w=800'; ?>
                        <div class="image-placeholder" style="background-image: url('<?= $coverUrl ?>');"></div>
                        <h3 class="card-title" style="font-family: var(--font-head); margin-bottom: 5px;"><?= htmlspecialchars($post['title']) ?></h3>
                        <small style="color: var(--accent-primary); margin-bottom: 15px; display:block; font-family: var(--font-head); font-size: 0.85rem; letter-spacing: 1px;"><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                        <p class="card-description" style="color: var(--text-muted); margin-bottom: 25px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            <?= htmlspecialchars(strip_tags($post['content'])) ?>
                        </p>
                        <a href="../post.php?id=<?= $post['id'] ?>" class="cta-btn outline card-cta" style="width: 100%; text-align: center; margin-top: auto;">Czytaj więcej</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <div id="footer-placeholder"></div>
    <script src="../js/script.js"></script>
</body>
</html>