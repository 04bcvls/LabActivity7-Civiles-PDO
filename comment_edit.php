<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$commentId = $_GET['id'] ?? null;
$error     = '';

try {
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = :id');
    $stmt->execute(['id' => $commentId]);
    $comment = $stmt->fetch();

    if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
        die('Unauthorized access.  Content not found.');
    }
} catch (Throwable $e) {
    die('Error loading comment.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    if (empty($content)) {
        $error = 'Comment cannot be empty.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'UPDATE comments 
                 SET content = :content, is_edited = 1 
                 WHERE id = :id AND user_id = :user_id'
            );
            $stmt->execute([
                'content' => $content,
                'id'      => $commentId,
                'user_id' => $_SESSION['user_id'],
            ]);

            $pdo->commit();

            header('Location: index.php');
            exit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Failed to update comment.';
        }
    }
}

require_once 'header.php';
?>

<h3>Edit Comment</h3>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="comment_edit.php?id=<?= $comment['id'] ?>">
    <div>
        <textarea name="content" rows="3" required><?= htmlspecialchars($comment['content']) ?></textarea>
    </div>
    <button type="submit">Update Comment</button>
    <a href="index.php" style="color: #94a3b8; margin-left: 10px;">Cancel</a>
</form>

</div>
</body>
</html>