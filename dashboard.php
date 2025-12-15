<?php
require __DIR__ . '/db.php';
require_login();

$welcome = isset($_GET['welcome']) ? 'Welcome, ' . htmlspecialchars($_SESSION['username'] ?? '') . '!' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Employee Management</strong>
            <?php if ($welcome): ?>
                <span class="muted"><?php echo $welcome; ?></span>
            <?php else: ?>
                <span class="muted">Signed in as <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <?php endif; ?>
        </div>
        <nav class="nav-links">
            <a class="button ghost" href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="layout">
        <div class="card">
            <h2>what would you like to do today🤗🤗 </h2>
            <div class="grid-buttons">
                <a class="button" href="employees_create.php">Add Employee</a>
                <a class="button" href="employees_update.php">Update Employee</a>
                <a class="button danger" href="employees_delete.php">Delete Employee</a>
                <a class="button ghost-blue" href="employees_list.php">View Employees</a>
            </div>
        </div>
    </main>
</body>
</html>
<?php
require __DIR__ . '/db.php';
require_login();

$welcome = isset($_GET['welcome']) ? 'Welcome, ' . htmlspecialchars($_SESSION['username'] ?? '') . '!' : '';
?>

