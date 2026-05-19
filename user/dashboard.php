<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_stmt = $conn->prepare("SELECT name FROM users WHERE id=?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Get reservations by status
$pending_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Pending' ORDER BY reservation_date ASC");
$pending_stmt->execute([$user_id]);
$pending = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

$approved_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Approved' ORDER BY reservation_date ASC");
$approved_stmt->execute([$user_id]);
$approved = $approved_stmt->fetchAll(PDO::FETCH_ASSOC);

$cancelled_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Cancelled' ORDER BY reservation_date ASC");
$cancelled_stmt->execute([$user_id]);
$cancelled = $cancelled_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../includes/header.php"); ?>

<div class="container">

<h2>Welcome, <?= $user['name'] ?>! 👋</h2>

<div style="text-align:center; margin-bottom:40px;">
    <a href="reserve.php" class="btn">📅 Book New Reservation</a>
</div>

<!-- Pending Reservations -->
<div class="status-section">
    <h3 class="section-title pending-title">⏳ Pending Reservations (<?= count($pending) ?>)</h3>
    
    <?php if(count($pending) > 0): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($pending as $row): ?>
        <tr>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="pending"><?= $row['status'] ?></span></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete Reservation?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p class="no-data">No pending reservations</p>
    <?php endif; ?>
</div>

<!-- Approved Reservations -->
<div class="status-section">
    <h3 class="section-title approved-title">✅ Approved Reservations (<?= count($approved) ?>)</h3>
    
    <?php if(count($approved) > 0): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($approved as $row): ?>
        <tr>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="approved"><?= $row['status'] ?></span></td>
            <td>
                <a href="receipt.php?id=<?= $row['id'] ?>" class="btn">Receipt</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Cancel this reservation?')">Cancel</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p class="no-data">No approved reservations yet</p>
    <?php endif; ?>
</div>

<!-- Cancelled Reservations -->
<div class="status-section">
    <h3 class="section-title cancelled-title">❌ Cancelled Reservations (<?= count($cancelled) ?>)</h3>
    
    <?php if(count($cancelled) > 0): ?>
    <table>
        <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($cancelled as $row): ?>
        <tr>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="cancelled"><?= $row['status'] ?></span></td>
            <td>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this record?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p class="no-data">No cancelled reservations</p>
    <?php endif; ?>
</div>

</div>

<?php include("../includes/footer.php"); ?>