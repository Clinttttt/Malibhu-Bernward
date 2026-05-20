<?php
session_start();
include("../config/db.php");

$error = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm = isset($_POST['confirm']) ? trim($_POST['confirm']) : '';

    if($name === '' || $email === '' || $password === '' || $confirm === ''){
        $error = "All fields are required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Enter a valid email address.";
    } elseif(strlen($password) < 5){
        $error = "Password must be at least 5 characters.";
    } elseif($password !== $confirm){
        $error = "Passwords do not match.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $success = true;
        } catch(PDOException $e){
            $error = $e->getCode() == 23000 ? "Email is already registered." : "Registration failed. Please try again.";
        }
    }
}

if($success){
    header("Location: login.php?registered=1");
    exit();
}

$page_title = 'User Registration';
include("../includes/header.php");
?>

<main class="auth-section">
    <section class="auth-box">
        <h2>Create Account</h2>
        <p style="text-align:center; margin-bottom:24px;">Register once, then manage your reservations from your dashboard.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" placeholder="Juan Dela Cruz" required>

            <label for="email">Email address</label>
            <input id="email" type="email" name="email" placeholder="name@example.com" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Create password" required>

            <label for="confirm">Confirm password</label>
            <input id="confirm" type="password" name="confirm" placeholder="Confirm password" required>

            <button type="submit" class="btn-book">Register</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            Already have an account? <a href="login.php" style="color:var(--forest); font-weight:800; text-decoration:none;">Login here</a>
        </p>
    </section>
</main>

<?php include("../includes/footer.php"); ?>
