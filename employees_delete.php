<?php
require __DIR__ . '/db.php';
require_login();

$errors = [];
$notice = '';
$employees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        $errors[] = 'A valid employee ID is required to delete.';
    } else {
        try {
            $stmt = db()->prepare('DELETE FROM employees WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $notice = 'Employee deleted (if existed).';
        } catch (Throwable $e) {
            $errors[] = 'Unable to delete employee right now.';
        }
    }
}

// Fetch employees to display in the list (always attempt to load latest)
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
    <title>Delete Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Employee Management</strong>
            <span class="muted">Delete employee</span>
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
            <h2>Delete Employee</h2>
            <p class="muted">Click the delete button on a row to remove that employee. Deletion is permanent.</p>

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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr><td colspan="8" class="muted center">No employees found.</td></tr>
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
                                    <td>
                                        <form method="post" onsubmit="return confirm('Delete employee <?php echo addslashes(htmlspecialchars(
                                            $emp['employee_name'])); ?> (ID <?php echo (int)$emp['id']; ?>)?');">
                                            <input type="hidden" name="id" value="<?php echo (int)$emp['id']; ?>">
                                            <button type="submit" class="danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>
            <h3>Delete by ID</h3>
            <form method="post" class="form">
                <label>
                    Employee ID *
                    <input type="number" name="id" min="1" required>
                </label>
                <button type="submit" class="danger">Delete</button>
            </form>
        </section>
    </main>
</body>
</html>
