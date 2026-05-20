<?php
session_start();
require_once("../config/admin_auth.php");

$error = '';
$credentials = admin_credentials_load();

if(!$credentials){
    header("Location: setup.php");
    exit();
}

if(isset($_COOKIE['admin_token'])){
    $expected = hash('sha256', $credentials['username'] . ':' . $credentials['password']);
    if(hash_equals($expected, $_COOKIE['admin_token'])){
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']);

    if($username === '' || $password === ''){
        $error = "Username and password are required.";
    } elseif(hash_equals($credentials['username'], $username) && admin_password_matches($password, $credentials['password'])){
        $_SESSION['admin_logged_in'] = true;
        admin_credentials_upgrade_if_needed($username, $password, $credentials['password']);
        $credentials = admin_credentials_load();

        if($remember && $credentials){
            $token = hash('sha256', $credentials['username'] . ':' . $credentials['password']);
            setcookie('admin_token', $token, time() + (86400 * 30), '/', '', false, true);
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials.";
    }
}

$page_title = 'Admin Login';
include("../includes/header.php");
?>

<main class="auth-section">
    <section class="auth-box">
        <h2>Admin Login</h2>
        <p style="text-align:center; margin-bottom:24px;">Manage reservation requests, approvals, and resort bookings.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Admin username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Admin password" required>

            <label style="display:flex; align-items:center; gap:10px; margin:8px 0 18px;">
                <input type="checkbox" name="remember" value="1" style="width:auto; min-height:auto;">
                <span>Keep me signed in for 30 days</span>
            </label>

            <button type="submit" class="btn-book">Login to Dashboard</button>
        </form>
    </section>
</main>

<?php include("../includes/footer.php"); ?>
