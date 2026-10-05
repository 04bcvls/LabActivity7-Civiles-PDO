<?php
require_once 'db.php';
require_once 'auth.php';
require_guest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];

                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Throwable $e) {
            $error = 'An error occurred during login.';
        }
    }
}

require_once 'header.php';
?>

<h3>Login</h3>
<?php if (isset($_GET['registered'])): ?>
    <div style="color: #4ade80; margin-bottom: 12px;">Account created successfully! Please log in.</div>
<?php endif; ?>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="login.php">
    <div>
        <label>Email:</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Log In</button>
</form>

</div>
</body>
</html>