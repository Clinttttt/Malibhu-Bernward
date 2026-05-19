<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify ownership before deleting
$stmt = $conn->prepare("DELETE FROM reservations WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);

header("Location: dashboard.php");
exit();
?>