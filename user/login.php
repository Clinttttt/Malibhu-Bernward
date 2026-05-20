<?php
session_start();
include("../config/db.php");

$error = '';
$notice = isset($_GET['registered']) ? "Registration complete. You can log in now." : '';

function user_password_matches($input_password, $stored_password){
    if(strpos($stored_password, '$2y$') === 0 || strpos($stored_password, '$argon') === 0){
        return password_verify($input_password, $stored_password);
    }

    return hash_equals($stored_password, $input_password);
}

function user_upgrade_password_if_needed($conn, $user_id, $input_password, $stored_password){
    if(strpos($stored_password, '$2y$') === 0 || strpos($stored_password, '$argon') === 0){
        return;
    }

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->execute([password_hash($input_password, PASSWORD_DEFAULT), $user_id]);
}

if(isset($_COOKIE['user_id'], $_COOKIE['user_token'])){
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_COOKIE['user_id']]);
    $cookie_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($cookie_user){
        $expected = hash('sha256', $cookie_user['id'] . ':' . $cookie_user['email'] . ':' . $cookie_user['password']);
        if(hash_equals($expected, $_COOKIE['user_token'])){
            $_SESSION['user_id'] = $cookie_user['id'];
            header("Location: dashboard.php");
            exit();
        }
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $remember = isset($_POST['remember']);

    if($email === '' || $password === ''){
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && user_password_matches($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            user_upgrade_password_if_needed($conn, $user['id'], $password, $user['password']);

            if($remember){
                $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
                $stmt->execute([$user['id']]);
                $fresh_user = $stmt->fetch(PDO::FETCH_ASSOC);
                $token = hash('sha256', $fresh_user['id'] . ':' . $fresh_user['email'] . ':' . $fresh_user['password']);
                setcookie('user_id', $fresh_user['id'], time() + (86400 * 30), '/', '', false, true);
                setcookie('user_token', $token, time() + (86400 * 30), '/', '', false, true);
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$page_title = 'User Login';
include("../includes/header.php");
?>

<main class="auth-section">
    <section class="auth-box">
        <h2>User Login</h2>
        <p style="text-align:center; margin-bottom:24px;">Access your reservation dashboard and booking receipts.</p>

        <?php if($notice): ?>
            <div class="alert alert-success"><?= htmlspecialchars($notice) ?></div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" placeholder="name@example.com" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Your password" required>

            <label style="display:flex; align-items:center; gap:10px; margin:8px 0 18px;">
                <input type="checkbox" name="remember" value="1" style="width:auto; min-height:auto;">
                <span>Keep me signed in for 30 days</span>
            </label>

            <button type="submit" class="btn-book">Login</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
            No account yet? <a href="register.php" style="color:var(--forest); font-weight:800; text-decoration:none;">Register here</a>
        </p>
    </section>
</main>

<?php include("../includes/footer.php"); ?>
