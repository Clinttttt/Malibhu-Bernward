<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Reservation Receipt</title>

<link rel="stylesheet"
href="../css/style.css">

</head>

<body>

<div class="container">

<h2>Reservation Receipt</h2>

<p><b>Event:</b> <?= $row['event_type'] ?></p>

<p><b>Date:</b> <?= $row['reservation_date'] ?></p>

<p><b>Guests:</b> <?= $row['guests'] ?></p>

<p><b>Status:</b> <?= $row['status'] ?></p>

<button onclick="window.print()" class="btn">
Print Receipt
</button>

</div>

</body>
</html>