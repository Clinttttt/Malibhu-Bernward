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

        <?php 
        // Check if we're on setup page - hide all navigation
        $on_setup_page = strpos($_SERVER['PHP_SELF'], 'setup.php') !== false;
        
        if(!$on_setup_page):
            // Check if admin is configured
            $admin_configured = file_exists(
                (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) 
                ? '../config/admin.txt' 
                : 'config/admin.txt'
            );
            
            if($admin_configured):
        ?>

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
        
        <?php 
            endif; // end admin_configured
        endif; // end !on_setup_page
        ?>

    </div>

</div>