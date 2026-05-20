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

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id=?");
$stmt->execute([$id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$reservation){
    header("Location: dashboard.php?error=Reservation not found");
    exit();
}

$conflict = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_date=? AND status='Approved' AND id<>?");
$conflict->execute([$reservation['reservation_date'], $id]);

if($conflict->fetchColumn() > 0){
    header("Location: dashboard.php?error=That date already has an approved reservation");
    exit();
}

$stmt = $conn->prepare("UPDATE reservations SET status='Approved' WHERE id=?");
$stmt->execute([$id]);

header("Location: dashboard.php?success=Reservation approved");
exit();
?>
