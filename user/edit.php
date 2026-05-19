<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

if(isset($_POST['update'])){

    $event = $_POST['event'];
    $date = $_POST['date'];
    $guests = $_POST['guests'];

    // Verify ownership
    $stmt = $conn->prepare("UPDATE reservations SET event_type=?, reservation_date=?, guests=? WHERE id=? AND user_id=?");
    $stmt->execute([$event, $date, $guests, $id, $user_id]);

    header("Location: dashboard.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header("Location: dashboard.php");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">

<h2>Edit Reservation</h2>

<form method="POST">

    <input type="text"
    name="event"
    value="<?= $row['event_type'] ?>"
    required>

    <br>

    <input type="date"
    name="date"
    value="<?= $row['reservation_date'] ?>"
    min="<?= date('Y-m-d') ?>"
    required>

    <br>

    <input type="number"
    name="guests"
    value="<?= $row['guests'] ?>"
    min="1"
    max="100"
    required>

    <br><br>

    <button name="update" class="btn">
        Update Reservation
    </button>

</form>

</div>

<?php include("../includes/footer.php"); ?>