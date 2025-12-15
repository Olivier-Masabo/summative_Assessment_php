<?php
require __DIR__ . '/db.php';
require_login();

$errors = [];
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeName = clean_string($_POST['employee_name'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $phone = clean_string($_POST['phone'] ?? '');
    $position = clean_string($_POST['position'] ?? '');
    $address = clean_string($_POST['address'] ?? '');

    if ($employeeName === '') {
        $errors[] = 'Employee name is required.';
    } elseif (mb_strlen($employeeName) > 60) {
        $errors[] = 'Employee name must be 60 characters or less.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if ($phone !== '' && !preg_match('/^[0-9 +().-]{5,20}$/', $phone)) {
        $errors[] = 'Phone number format is invalid.';
    }

    if (mb_strlen($position) > 100) {
        $errors[] = 'Position must be 100 characters or less.';
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare('INSERT INTO employees (employee_name, email, phone, position, address) VALUES (:name, :email, :phone, :position, :address)');
            $stmt->execute([
                ':name' => $employeeName,
                ':email' => $email,
                ':phone' => $phone ?: null,
                ':position' => $position ?: null,
                ':address' => $address ?: null,
            ]);
            $notice = 'Employee created successfully.';
        } catch (Throwable $e) {
            $errors[] = 'Unable to create employee right now.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Employee Management</strong>
            <span class="muted">Add employee</span>
        </div>
        <nav class="nav-links">
            <a class="button ghost" href="dashboard.php">Dashboard</a>
            <a class="button ghost" href="employees_list.php">View</a>
            <a class="button ghost" href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="layout">
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

        <section class="card">
            <h2>Add Employee</h2>
            <form method="post" class="form two-col">
                <label>
                    Name *
                    <input type="text" name="employee_name" maxlength="60" required>
                </label>
                <label>
                    Email *
                    <input type="email" name="email" maxlength="255" required>
                </label>
                <label>
                    Phone
                    <input type="text" name="phone" maxlength="15" placeholder="+1 234 567 890">
                </label>
                <label>
                    Position
                    <input type="text" name="position" maxlength="100">
                </label>
                <label class="full">
                    Address
                    <textarea name="address" rows="2"></textarea>
                </label>
                <button type="submit">Create Employee</button>
            </form>
        </section>
    </main>
</body>
</html>
