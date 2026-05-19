<?php
session_start();
include("../config/db.php");

// Check if admin needs setup
$admin_file = '../config/admin.txt';
if(!file_exists($admin_file)){
    header("Location: setup.php");
    exit();
}

// Load admin credentials
$admin_data = file_get_contents($admin_file);
list($admin_username, $admin_password) = explode(':', $admin_data);

// Check if admin has remember me cookie
if(!isset($_SESSION['admin_logged_in']) && isset($_COOKIE['admin_token'])){
    if($_COOKIE['admin_token'] === md5($admin_username . ':' . $admin_password)){
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit();
    }
}

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    // Check credentials
    if($username === $admin_username && $password === $admin_password){
        $_SESSION['admin_logged_in'] = true;
        
        // Set remember me cookie for 30 days
        if($remember){
            $token = md5($admin_username . ':' . $admin_password);
            setcookie('admin_token', $token, time() + (30 * 24 * 60 * 60), '/');
        }
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<?php include("../includes/header.php"); ?>

<div class="auth-section">
    <div class="auth-box">
        <h2>Admin Login</h2>
        
        <?php if(isset($error)): ?>
            <p style="color:red; text-align:center; margin-bottom:20px;"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Admin Password" required>
            
            <div style="text-align:left; margin:10px 0;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
            <input type="checkbox" name="remember" value="1" style="width:auto; margin:0;">
            <span>Remember Me (30 days)</span>
            </label>
            </div>
            
            <button type="submit" name="login" class="btn-gold">Login as Admin</button>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
