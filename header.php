<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Site · Topic 7 PDO</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; }
        nav { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 20px; }
        nav a { margin-left: 10px; text-decoration: none; color: #38bdf8; }
        .error { color: #f87171; background: #450a0a; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #7f1d1d; }
        .post { border: 1px solid #334155; border-radius: 8px; padding: 16px; margin-bottom: 20px; background: #0f172a; }
        .comment { background: #1e293b; margin-left: 20px; border-left: 3px solid #38bdf8; padding: 10px; border-radius: 4px; margin-top: 10px; }
        .meta { font-size: 0.8em; color: #94a3b8; margin-top: 8px; }
        .edited-tag { color: #fbbf24; font-style: italic; font-size: 0.85em; margin-left: 6px; }
        form div { margin-bottom: 12px; }
        input[type="text"], input[type="email"], input[type="password"], textarea { width: 100%; padding: 10px; background: #0f172a; border: 1px solid #334155; color: #fff; border-radius: 6px; box-sizing: border-box; }
        button { background: #0284c7; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #0369a1; }
    </style>
</head>
<body>
<div class="container">
    <nav>
        <h2><a href="index.php" style="color: inherit; text-decoration: none;">Blog Site</a></h2>
        <div>
            <?php if (is_logged_in()): ?>
                <span>Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span> |
                <a href="index.php">Feed</a> |
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> |
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>