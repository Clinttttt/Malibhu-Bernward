<?php
$is_user_area = strpos($_SERVER['PHP_SELF'], '/user/') !== false;
$is_admin_area = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$base_path = ($is_user_area || $is_admin_area) ? '../' : '';
$page_title = isset($page_title) ? $page_title . ' - Malibhu View Resort' : 'Malibhu View Resort';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="<?= $base_path ?>css/style.css">
    <script src="<?= $base_path ?>js/script.js" defer></script>
</head>
<body>

<header class="topbar">
    <a class="brand" href="<?= $base_path ?>index.php">
        <img src="<?= $base_path ?>images/logo.jpg" class="logo" alt="Malibhu View Resort logo">
        <span>
            <strong>Malibhu View Resort</strong>
            <small>Reservation Management System</small>
        </span>
    </a>

    <nav class="nav-links" aria-label="Primary navigation">
        <a href="<?= $base_path ?>index.php">Home</a>

        <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <a href="<?= $base_path ?>admin/dashboard.php">Admin Dashboard</a>
            <a href="<?= $base_path ?>admin/logout.php">Logout</a>
        <?php elseif(isset($_SESSION['user_id'])): ?>
            <a href="<?= $base_path ?>user/dashboard.php">Dashboard</a>
            <a href="<?= $base_path ?>user/reserve.php">Reserve</a>
            <a href="<?= $base_path ?>logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= $base_path ?>user/login.php">Login</a>
            <a href="<?= $base_path ?>user/register.php" class="nav-cta">Register</a>
            <a href="<?= $base_path ?>admin/login.php">Admin</a>
        <?php endif; ?>
    </nav>
</header>
