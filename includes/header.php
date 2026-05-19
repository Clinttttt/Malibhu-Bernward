<!DOCTYPE html>
<html>

<head>

    <title>Malibhu View Resort</title>

    <link rel="stylesheet"
    href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''; ?>css/style.css">

</head>

<body>

<div class="topbar">

    <div class="logo-section">

        <img src="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''; ?>images/logo.jpg"
        class="logo">

        <div>
            <h1>Malibhu View Resort</h1>
            <p>Luxury Resort Reservation System</p>
        </div>

    </div>

    <div class="nav-links">

        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''; ?>index.php">
            Home
        </a>

        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '' : 'user/'; ?>dashboard.php">
            Dashboard
        </a>

        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '' : 'user/'; ?>reserve.php">
            Reservation
        </a>

        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : ''; ?>logout.php">
            Logout
        </a>
        <?php else: ?>
        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '' : 'user/'; ?>login.php">
            Login
        </a>

        <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '' : 'user/'; ?>register.php">
            Register
        </a>
        <?php endif; ?>

    </div>

</div>