<?php
require __DIR__ . '/db.php';

// If already logged in, go to dashboard; otherwise show login.
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php?welcome=1');
    exit;
}

header('Location: login.php');
exit;
