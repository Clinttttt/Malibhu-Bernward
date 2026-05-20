<?php
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

include("../config/db.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id){
    header("Location: dashboard.php?error=Invalid reservation selected");
    exit();
}

$stmt = $conn->prepare("UPDATE reservations SET status='Cancelled' WHERE id=?");
$stmt->execute([$id]);

header("Location: dashboard.php?success=Reservation updated");
exit();
?>
