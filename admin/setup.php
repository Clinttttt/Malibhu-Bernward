<?php
session_start();
include("../config/db.php");

// Check if admin already exists
$admin_file = '../config/admin.txt';

if(file_exists($admin_file)){
    header("Location: login.php");
    exit();
}

if(isset($_POST['setup'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);
    
    if(empty($username) || empty($password)){
        $error = "All fields are required";
    } elseif($password !== $confirm){
        $error = "Passwords do not match";
    } elseif(strlen($password) < 5){
        $error = "Password must be at least 5 characters";
    } else {
        // Save admin credentials
        $admin_data = $username . ':' . $password;
        file_put_contents($admin_file, $admin_data);
        
        echo "<script>
        alert('Admin account created successfully!');
        window.location='login.php';
        </script>";
        exit();
    }
}
?>

<?php include("../includes/header.php"); ?>

<div class="auth-section">
    <div class="auth-box">
        <h2>🔧 Admin Setup</h2>
        <p style="text-align:center; color:#666; margin-bottom:20px;">
            First time setup - Create your admin account
        </p>
        
        <?php if(isset($error)): ?>
            <p style="color:red; text-align:center; margin-bottom:20px;"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Admin Username" required minlength="3">
            <input type="password" name="password" placeholder="Admin Password" required minlength="5">
            <input type="password" name="confirm" placeholder="Confirm Password" required minlength="5">
            
            <button type="submit" name="setup" class="btn-gold">Create Admin Account</button>
        </form>
        
        <p style="text-align:center; margin-top:20px; color:#999; font-size:14px;">
            This page will only appear once
        </p>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
