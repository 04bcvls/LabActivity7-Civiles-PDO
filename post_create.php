<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!empty($title) && !empty($content)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)'
            );
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'title'   => $title,
                'content' => $content,
            ]);
            $postId = $pdo->lastInsertId();

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }
    }
}

header('Location: index.php');
exit();