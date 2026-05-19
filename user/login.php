<?php
session_start();
include("../config/db.php");

// Check if user has remember me cookie
if(!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['user_token'])){
    $user_id = $_COOKIE['user_id'];
    $token = $_COOKIE['user_token'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user && md5($user['email'].$user['password']) === $token){
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    }
}

if(isset($_POST['login'])){

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$remember = isset($_POST['remember']);

// Workaround for SQLite WHERE clause bug: fetch all and compare in PHP
$stmt = $conn->query("SELECT * FROM users");
$found = false;
$user = null;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if($row['email'] === $email && $row['password'] === $password){
        $found = true;
        $user = $row;
        break;
    }
}

if($found){
    $_SESSION['user_id'] = $user['id'];
    
    // Set remember me cookie for 30 days
    if($remember){
        $token = md5($user['email'].$user['password']);
        setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), '/');
        setcookie('user_token', $token, time() + (30 * 24 * 60 * 60), '/');
    }
    
    header("Location: dashboard.php");
    exit();
} else {
    echo "<script>alert('Invalid Login');</script>";
}

}
?>

<?php include("../includes/header.php"); ?>

<div class="auth-section">

<div class="auth-box">

<h2>User Login</h2>

<form method="POST">

<input type="email"
name="email"
placeholder="Email"
required>

<input type="password"
name="password"
placeholder="Password"
required>

<div style="text-align:left; margin:10px 0;">
<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
<input type="checkbox" name="remember" value="1" style="width:auto; margin:0;">
<span>Remember Me (30 days)</span>
</label>
</div>

<button
type="submit"
name="login"
class="btn-gold">

Login

</button>

</form>

</div>

</div>

<?php include("../includes/footer.php"); ?>