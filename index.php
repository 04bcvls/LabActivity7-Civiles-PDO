<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

try {
    $stmt = $pdo->prepare('
        SELECT p.*, u.username 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC
    ');
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (Throwable $e) {
    $posts = [];
}

require_once 'header.php';
?>

<h3>News Feed</h3>

<div class="post">
    <h4>Create Post</h4>
    <form method="POST" action="post_create.php">
        <div>
            <input type="text" name="title" placeholder="Post Title" required maxlength="255">
        </div>
        <div>
            <textarea name="content" placeholder="Write your post content..." rows="3" required></textarea>
        </div>
        <button type="submit">Publish</button>
    </form>
</div>

<?php foreach ($posts as $post): ?>
    <div class="post">
        <h3 style="margin: 0 0 8px 0;">
            <?= htmlspecialchars($post['title']) ?>
            <?php if ($post['is_edited']): ?>
                <span class="edited-tag">(edited on <?= $post['updated_at'] ?>)</span>
            <?php endif; ?>
        </h3>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <div class="meta">
            Posted by <strong><?= htmlspecialchars($post['username']) ?></strong> on <?= $post['created_at'] ?>
            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                | <a href="post_edit.php?id=<?= $post['id'] ?>">Edit Post</a>
            <?php endif; ?>
        </div>

        <div style="margin-top: 16px;">
            <h5 style="margin-bottom: 8px;">Comments</h5>
            <?php
            try {
                $commentStmt = $pdo->prepare('
                    SELECT c.*, u.username 
                    FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.post_id = :post_id 
                    ORDER BY c.created_at ASC
                ');
                $commentStmt->execute(['post_id' => $post['id']]);
                $comments = $commentStmt->fetchAll();
            } catch (Throwable $e) {
                $comments = [];
            }
            ?>

            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p style="margin: 0;"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    <div class="meta">
                        By <strong><?= htmlspecialchars($comment['username']) ?></strong> on <?= $comment['created_at'] ?>
                        <?php if ($comment['is_edited']): ?>
                            <span class="edited-tag">(edited on <?= $comment['updated_at'] ?>)</span>
                        <?php endif; ?>
                        <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>
                            | <a href="comment_edit.php?id=<?= $comment['id'] ?>">Edit Comment</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="comment_create.php" style="margin-top: 12px;">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <div>
                    <textarea name="content" placeholder="Write a comment..." rows="2" required></textarea>
                </div>
                <button type="submit">Add Comment</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

</div>
</body>
</html>