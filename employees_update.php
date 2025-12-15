<?php
require __DIR__ . '/db.php';
require_login();

$errors = [];
$notice = '';
$employees = [];
$selected = null;

// If an id is provided in the query string, load that employee for editing
$selectedId = (int)($_GET['id'] ?? 0);
if ($selectedId > 0) {
    try {
        $stmt = db()->prepare('SELECT id, employee_name, email, phone, position, address, created_at FROM employees WHERE id = :id');
        $stmt->execute([':id' => $selectedId]);
        $selected = $stmt->fetch();
    } catch (Throwable $e) {
        $errors[] = 'Could not load selected employee.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $employeeName = clean_string($_POST['employee_name'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $phone = clean_string($_POST['phone'] ?? '');
    $position = clean_string($_POST['position'] ?? '');
    $address = clean_string($_POST['address'] ?? '');

    if ($id <= 0) {
        $errors[] = 'A valid employee ID is required.';
    }

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
            $stmt = db()->prepare('UPDATE employees SET employee_name = :name, email = :email, phone = :phone, position = :position, address = :address WHERE id = :id');
            $stmt->execute([
                ':name' => $employeeName,
                ':email' => $email,
                ':phone' => $phone ?: null,
                ':position' => $position ?: null,
                ':address' => $address ?: null,
                ':id' => $id,
            ]);
            if ($stmt->rowCount() === 0) {
                $errors[] = 'No employee found with that ID.';
            } else {
                $notice = 'Employee updated successfully.';
            }
            // After update, reload the selected employee so the form shows current values
            try {
                $stmt2 = db()->prepare('SELECT id, employee_name, email, phone, position, address, created_at FROM employees WHERE id = :id');
                $stmt2->execute([':id' => $id]);
                $selected = $stmt2->fetch();
            } catch (Throwable $e) {
                // ignore; selected will remain null if fetch fails
            }
        } catch (Throwable $e) {
            $errors[] = 'Unable to update employee right now.';
        }
    }
}

// Fetch all employees for the list
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
    <title>Update Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <strong>Employee Management</strong>
            <span class="muted">Update employee</span>
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
            <h2>Update Employee</h2>

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
                                        <a class="button" href="employees_update.php?id=<?php echo (int)$emp['id']; ?>">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr>

            <h3><?php echo $selected ? 'Editing: ' . htmlspecialchars($selected['employee_name']) : 'Edit Employee'; ?></h3>
            <form method="post" class="form two-col">
                <label>
                    Employee ID 
                    <input type="number" name="id" min="1" required value="<?php echo $selected ? (int)$selected['id'] : ''; ?>">
                </label>
                <label>
                    Name 
                    <input type="text" name="employee_name" maxlength="60" required value="<?php echo $selected ? htmlspecialchars($selected['employee_name']) : ''; ?>">
                </label>
                <label>
                    Email 
                    <input type="email" name="email" maxlength="255" required value="<?php echo $selected ? htmlspecialchars($selected['email']) : ''; ?>">
                </label>
                <label>
                    Phone
                    <input type="text" name="phone" maxlength="15" value="<?php echo $selected ? htmlspecialchars($selected['phone']) : ''; ?>">
                </label>
                <label>
                    Position
                    <input type="text" name="position" maxlength="100" value="<?php echo $selected ? htmlspecialchars($selected['position']) : ''; ?>">
                </label>
                <label class="full">
                    Address
                    <textarea name="address" rows="2"><?php echo $selected ? htmlspecialchars($selected['address']) : ''; ?></textarea>
                </label>
                <button type="submit">Update Employee</button>
            </form>
        </section>
    </main>
</body>
</html>
