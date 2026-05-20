<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if(!$id){
    header("Location: dashboard.php?error=Invalid reservation selected");
    exit();
}

$stmt = $conn->prepare("SELECT status FROM reservations WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$reservation){
    header("Location: dashboard.php?error=Reservation not found");
    exit();
}

if($reservation['status'] === 'Approved'){
    $stmt = $conn->prepare("UPDATE reservations SET status='Cancelled' WHERE id=? AND user_id=?");
    $stmt->execute([$id, $user_id]);
    header("Location: dashboard.php?success=Reservation cancelled");
    exit();
}

$stmt = $conn->prepare("DELETE FROM reservations WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);

header("Location: dashboard.php?success=Reservation removed");
exit();
?>
