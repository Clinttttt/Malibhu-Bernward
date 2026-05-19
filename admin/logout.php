<?php
session_start();
unset($_SESSION['admin_logged_in']);
session_destroy();

// Clear remember me cookie
setcookie('admin_token', '', time() - 3600, '/');

header("Location: login.php");
exit();
?>
