<?php
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$id = $_GET['id'];

$stmt = $conn->prepare("UPDATE reservations SET status='Approved' WHERE id=?");
$stmt->execute([$id]);

header("Location: dashboard.php");
exit();
?>