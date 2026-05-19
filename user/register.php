<?php
// Check if admin is configured (first run check)
$admin_file = '../config/admin.txt';
if(!file_exists($admin_file)){
    header("Location: ../admin/setup.php");
    exit();
}

include("../config/db.php");

if(isset($_POST['register'])){

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

try {
    $stmt = $conn->prepare("INSERT INTO users(name,email,password) VALUES (?,?,?)");
    $stmt->execute([$name, $email, $password]);

    echo "
    <script>
    alert('Registration Successful');
    window.location='login.php';
    </script>
    ";
} catch(PDOException $e) {
    if($e->getCode() == 23000) {
        echo "
        <script>
        alert('Email already registered. Please use a different email or login.');
        </script>
        ";
    } else {
        echo "
        <script>
        alert('Registration failed. Please try again.');
        </script>
        ";
    }
}
}
?>

<?php include("../includes/header.php"); ?>

<div class="auth-section">

<div class="auth-box">

<h2>Create Account</h2>

<form method="POST">

<input type="text"
name="name"
placeholder="Full Name"
required>

<input type="email"
name="email"
placeholder="Email"
required>

<input type="password"
name="password"
placeholder="Password"
required>

<button
type="submit"
name="register"
class="btn-gold">

Register

</button>

</form>

</div>

</div>

<?php include("../includes/footer.php"); ?>