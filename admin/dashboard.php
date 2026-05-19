<?php
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

include("../config/db.php");

// Get reservations by status
$pending_stmt = $conn->query("SELECT reservations.*, users.name FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Pending' ORDER BY reservation_date ASC");
$pending = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

$approved_stmt = $conn->query("SELECT reservations.*, users.name FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Approved' ORDER BY reservation_date ASC");
$approved = $approved_stmt->fetchAll(PDO::FETCH_ASSOC);

$cancelled_stmt = $conn->query("SELECT reservations.*, users.name FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Cancelled' ORDER BY reservation_date ASC");
$cancelled = $cancelled_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../includes/header.php"); ?>

<div class="container">

<h2>Admin Reservation Management</h2>

<div style="text-align:right; margin-bottom:20px;">
    <a href="logout.php" class="btn-delete">Logout</a>
</div>

<!-- Pending Reservations -->
<div class="status-section">
    <h3 class="section-title pending-title">⏳ Pending Reservations (<?= count($pending) ?>)</h3>
    
    <?php if(count($pending) > 0): ?>
    <table>
        <tr>
            <th>User</th>
            <th>Event</th>
            <th>Date</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($pending as $row): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="pending"><?= $row['status'] ?></span></td>
            <td>
                <a href="approve.php?id=<?= $row['id'] ?>" class="btn-edit">Approve</a>
                <a href="reject.php?id=<?= $row['id'] ?>" class="btn-delete">Reject</a>
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
            <th>User</th>
            <th>Event</th>
            <th>Date</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($approved as $row): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="approved"><?= $row['status'] ?></span></td>
            <td>
                <span class="btn-disabled">Approved</span>
                <a href="reject.php?id=<?= $row['id'] ?>" class="btn-delete">Cancel</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p class="no-data">No approved reservations</p>
    <?php endif; ?>
</div>

<!-- Cancelled Reservations -->
<div class="status-section">
    <h3 class="section-title cancelled-title">❌ Cancelled Reservations (<?= count($cancelled) ?>)</h3>
    
    <?php if(count($cancelled) > 0): ?>
    <table>
        <tr>
            <th>User</th>
            <th>Event</th>
            <th>Date</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($cancelled as $row): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['event_type'] ?></td>
            <td><?= $row['reservation_date'] ?></td>
            <td><?= $row['guests'] ?></td>
            <td><span class="cancelled"><?= $row['status'] ?></span></td>
            <td>
                <a href="approve.php?id=<?= $row['id'] ?>" class="btn-edit">Re-approve</a>
                <span class="btn-disabled">Cancelled</span>
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