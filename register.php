<?php
require __DIR__ . '/db.php';

$errors = [];
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($username === '') {
        $errors[] = 'Username is required.';
    } elseif (mb_strlen($username) > 100) {
        $errors[] = 'Username must be 100 characters or less.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (mb_strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db()->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->execute([
                ':username' => $username,
                ':password' => $hash,
            ]);
            $notice = 'User registered successfully. You can now log in.';
        } catch (PDOException $e) {
            if ((int)$e->errorInfo[1] === 1062) {
                $errors[] = 'Username is already taken.';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered">
    <div class="card">
        <h1>Create Account</h1>
        <?php if ($notice): ?>
            <div class="alert success"><?php echo htmlspecialchars($notice); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $err): ?>
                    <div><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="form">
            <label>
                Username
                <input type="text" name="username" maxlength="100" required>
            </label>
            <label>
                Password
                <input type="password" name="password" minlength="6" required>
            </label>
            <label>
                Confirm password
                <input type="password" name="confirm" minlength="6" required>
            </label>
            <button type="submit">Register</button>
        </form>
        <p class="muted">Passwords are stored using PHP's password_hash().</p>
        <p><a href="login.php">Back to login</a></p>
    </div>
</body>
</html>
