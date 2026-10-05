<?php
require_once 'db.php';
require_once 'auth.php';
require_guest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = 'Email is already registered.';
                $pdo->commit();
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
                );
                $stmt->execute([
                    'username' => $username,
                    'email'    => $email,
                    'password' => $hashedPassword,
                ]);
                $userId = $pdo->lastInsertId();

                $pdo->commit();

                header('Location: login.php?registered=1');
                exit();
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Registration failed. Please try again.';
        }
    }
}

require_once 'header.php';
?>

<h3>Register</h3>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="register.php">
    <div>
        <label>Username:</label>
        <input type="text" name="username" required maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div>
        <label>Email:</label>
        <input type="email" name="email" required maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div>
        <label>Password:</label>
        <input type="password" name="password" required minlength="6">
    </div>
    <button type="submit">Register</button>
</form>

</div>
</body>
</html>