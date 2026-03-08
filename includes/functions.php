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

function createPost(string $title, string $content): int {
    $stmt = db()->prepare('INSERT INTO posts(title, content, created_at) VALUES(:title, :content, now()) RETURNING id');
    $stmt->execute([':title' => $title, ':content' => $content]);
    return (int)$stmt->fetchColumn();
}

function updatePost(int $id, string $title, string $content): bool {
    $stmt = db()->prepare('UPDATE posts SET title = :title, content = :content, updated_at = now() WHERE id = :id');
    return $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);
}

function deletePost(int $id): bool {
    $stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
