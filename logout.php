<?php
session_start();
session_destroy();

// Clear remember me cookies
setcookie('user_id', '', time() - 3600, '/');
setcookie('user_token', '', time() - 3600, '/');

header("Location: index.php");
?>