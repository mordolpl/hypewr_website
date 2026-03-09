<?php
// includes/functions.php - data access functions for blog

require_once __DIR__ . '/db.php';

function getPosts(): array {
    $stmt = db()->query('SELECT * FROM posts ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function getPostById(int $id): ?array {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ? $row : null;
}

function incrementPostViews(int $id): bool {
    $stmt = db()->prepare('UPDATE posts SET views = COALESCE(views, 0) + 1 WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

function createPost(string $title, string $content, string $author = 'Administrator', string $coverImage = '', string $category = 'Inne'): int {
    $stmt = db()->prepare('INSERT INTO posts(title, content, author, cover_image, category, created_at) VALUES(:title, :content, :author, :cover_image, :category, now()) RETURNING id');
    $stmt->execute([':title' => $title, ':content' => $content, ':author' => $author, ':cover_image' => $coverImage, ':category' => $category]);
    return (int)$stmt->fetchColumn();
}

function updatePost(int $id, string $title, string $content, string $author, string $coverImage = '', string $category = 'Inne'): bool {
    $stmt = db()->prepare('UPDATE posts SET title = :title, content = :content, author = :author, cover_image = :cover_image, category = :category, updated_at = now() WHERE id = :id');
    return $stmt->execute([':title' => $title, ':content' => $content, ':author' => $author, ':cover_image' => $coverImage, ':category' => $category, ':id' => $id]);
}

function deletePost(int $id): bool {
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
