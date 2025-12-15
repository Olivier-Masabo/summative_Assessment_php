<?php
require __DIR__ . '/db.php';
require_login();

$errors = [];
$employees = [];

try {
    $employees = db()->query('SELECT id, employee_name, email, phone, position, address, created_at FROM employees ORDER BY created_at DESC')->fetchAll();
} catch (Throwable $e) {
    $errors[] = 'Could not load employees.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Employees</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Employee Management</strong>
            <span class="muted">Viewing employees</span>
        </div>
        <nav class="nav-links">
            <a class="button ghost" href="dashboard.php">Dashboard</a>
            <a class="button ghost" href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="layout">
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $err): ?>
                    <div><?php echo htmlspecialchars($err); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <h2>Employee Records</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Address</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr><td colspan="7" class="muted center">No employees found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['id']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['employee_name']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['position']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['address']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
