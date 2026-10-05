<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$postId = $_GET['id'] ?? null;
$error  = '';

try {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute(['id' => $postId]);
    $post = $stmt->fetch();

    if (!$post || $post['user_id'] != $_SESSION['user_id']) {
        die('Unauthorized access. Content not found.');
    }
} catch (Throwable $e) {
    die('Error loading post.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'UPDATE posts 
                 SET title = :title, content = :content, is_edited = 1 
                 WHERE id = :id AND user_id = :user_id'
            );
            $stmt->execute([
                'title'   => $title,
                'content' => $content,
                'id'      => $postId,
                'user_id' => $_SESSION['user_id'],
            ]);

            $pdo->commit();

            header('Location: index.php');
            exit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Failed to update post.';
        }
    }
}

require_once 'header.php';
?>

<h3>Edit Blog Post</h3>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="post_edit.php?id=<?= $post['id'] ?>">
    <div>
        <label>Title:</label>
        <input type="text" name="title" required maxlength="255" value="<?= htmlspecialchars($post['title']) ?>">
    </div>
    <div>
        <label>Content:</label>
        <textarea name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
    </div>
    <button type="submit">Update Post</button>
    <a href="index.php" style="color: #94a3b8; margin-left: 10px;">Cancel</a>
</form>

</div>
</body>
</html>