<?php
session_start();
require_once("../config/admin_auth.php");

$error = '';
$success = false;

if(admin_credentials_exist()){
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm = isset($_POST['confirm']) ? trim($_POST['confirm']) : '';

    if($username === '' || $password === '' || $confirm === ''){
        $error = "All fields are required.";
    } elseif(strlen($username) < 3){
        $error = "Username must be at least 3 characters.";
    } elseif(strlen($password) < 5){
        $error = "Password must be at least 5 characters.";
    } elseif($password !== $confirm){
        $error = "Passwords do not match.";
    } elseif(admin_credentials_save($username, $password)){
        $success = true;
    } else {
        $error = "Failed to create the admin account. Please check file permissions.";
    }
}

if($success){
    $_SESSION['admin_logged_in'] = true;
    header("Location: dashboard.php");
    exit();
}

$page_title = 'Admin Setup';
include("../includes/header.php");
?>

<main class="auth-section">
    <section class="auth-box">
        <h2>Create Admin</h2>
        <p style="text-align:center; margin-bottom:24px;">Set up the first administrator account for this reservation system.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="username">Admin username</label>
            <input id="username" type="text" name="username" placeholder="Enter username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter password" required>

            <label for="confirm">Confirm password</label>
            <input id="confirm" type="password" name="confirm" placeholder="Confirm password" required>

            <button type="submit" class="btn-book">Create Admin Account</button>
        </form>
    </section>
</main>

<?php include("../includes/footer.php"); ?>
