<?php
require __DIR__ . '/db.php';

$error = '';
$remembered = $_COOKIE['remember_username'] ?? '';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        try {
            $stmt = db()->prepare('SELECT id, username, password FROM users WHERE username = :username');
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                if ($remember) {
                    setcookie('remember_username', $user['username'], time() + (7 * 24 * 60 * 60), '/', '', false, true);
                } else {
                    setcookie('remember_username', '', time() - 3600, '/');
                }

                header('Location: dashboard.php?welcome=1');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (Throwable $e) {
            $error = 'Login failed. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Employee Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="centered">
    <div class="card">
        <h1>NexTech Portal Login</h1>
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="login.php" class="form">
            <label>
                Username
                <input type="text" name="username" value="<?php echo htmlspecialchars($remembered); ?>" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label class="inline">
                <input type="checkbox" name="remember" <?php echo $remembered ? 'checked' : ''; ?>>
                Remember me (7 days)
            </label>
            <button type="submit">Login</button>
        </form>
        <p class="muted">Passwords are stored hashed in the database.</p>
        <p class="muted">Need an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
