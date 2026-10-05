<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId  = $_POST['post_id'] ?? null;
    $content = trim($_POST['content'] ?? '');

    if (!empty($postId) && !empty($content)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)'
            );
            $stmt->execute([
                'post_id' => $postId,
                'user_id' => $_SESSION['user_id'],
                'content' => $content,
            ]);
            $commentId = $pdo->lastInsertId();

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