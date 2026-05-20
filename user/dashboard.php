<?php
session_start();

require_once("../config/admin_auth.php");

// Check if admin is configured (first run check)
if(!admin_credentials_exist()){
    header("Location: ../admin/setup.php");
    exit();
}

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

// Get statistics
$total_bookings = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id=?");
$total_bookings->execute([$user_id]);
$total_count = $total_bookings->fetchColumn();

$approved_count = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id=? AND status='Approved'");
$approved_count->execute([$user_id]);
$approved = $approved_count->fetchColumn();

$pending_count = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id=? AND status='Pending'");
$pending_count->execute([$user_id]);
$pending_num = $pending_count->fetchColumn();

// Get upcoming reservation
$upcoming_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Approved' AND reservation_date >= ? ORDER BY reservation_date ASC LIMIT 1");
$upcoming_stmt->execute([$user_id, date('Y-m-d')]);
$upcoming = $upcoming_stmt->fetch(PDO::FETCH_ASSOC);

// Get reservations by status
$pending_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Pending' ORDER BY reservation_date ASC");
$pending_stmt->execute([$user_id]);
$pending_list = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

$approved_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Approved' ORDER BY reservation_date ASC");
$approved_stmt->execute([$user_id]);
$approved_list = $approved_stmt->fetchAll(PDO::FETCH_ASSOC);

$cancelled_stmt = $conn->prepare("SELECT * FROM reservations WHERE user_id=? AND status='Cancelled' ORDER BY reservation_date ASC");
$cancelled_stmt->execute([$user_id]);
$cancelled = $cancelled_stmt->fetchAll(PDO::FETCH_ASSOC);

function reservation_schedule($row){
    $start = date('M d, Y', strtotime($row['reservation_date']));
    $end_date = !empty($row['checkout_date']) ? $row['checkout_date'] : $row['reservation_date'];
    $end = date('M d, Y', strtotime($end_date));
    return $start === $end ? $start : $start . ' - ' . $end;
}
?>

<?php
$page_title = 'User Dashboard';
include("../includes/header.php");
?>

<div class="container">

<h2>Welcome, <?= htmlspecialchars($user ? $user['name'] : 'Guest') ?></h2>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<!-- Statistics Dashboard -->
<div class="stats-grid" style="margin-bottom:50px;">
    <div class="stat-card">
        <h3><?= $total_count ?></h3>
        <p>Total Bookings</p>
    </div>
    <div class="stat-card">
        <h3><?= $approved ?></h3>
        <p>Approved</p>
    </div>
    <div class="stat-card">
        <h3><?= $pending_num ?></h3>
        <p>Pending</p>
    </div>
    <div class="stat-card">
        <h3><?= $upcoming ? date('M d', strtotime($upcoming['reservation_date'])) : 'None' ?></h3>
        <p>Next Event</p>
    </div>
</div>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search reservations by event type or date..." onkeyup="searchTable()">
</div>

<div style="text-align:center; margin-bottom:40px;">
    <a href="reserve.php" class="btn">Book New Reservation</a>
    <a href="../logout.php" class="btn" style="margin-left:10px;">Logout</a>
</div>

<!-- Pending Reservations -->
<div class="status-section">
    <h3 class="section-title pending-title">Pending Reservations (<?= count($pending_list) ?>)</h3>
    
    <?php if(count($pending_list) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Schedule</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($pending_list as $row): ?>
        <tr>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="pending"><?= $row['status'] ?></span></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete reservation?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <?php else: ?>
    <p class="no-data">No pending reservations</p>
    <?php endif; ?>
</div>

<!-- Approved Reservations -->
<div class="status-section">
    <h3 class="section-title approved-title">Approved Reservations (<?= count($approved_list) ?>)</h3>
    
    <?php if(count($approved_list) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Schedule</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($approved_list as $row): ?>
        <tr>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="approved"><?= $row['status'] ?></span></td>
            <td>
                <a href="receipt.php?id=<?= $row['id'] ?>" class="btn">Receipt</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Cancel this reservation?')">Cancel</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <?php else: ?>
    <p class="no-data">No approved reservations yet</p>
    <?php endif; ?>
</div>

<!-- Cancelled Reservations -->
<div class="status-section">
    <h3 class="section-title cancelled-title">Cancelled Reservations (<?= count($cancelled) ?>)</h3>
    
    <?php if(count($cancelled) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Schedule</th>
            <th>Event</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($cancelled as $row): ?>
        <tr>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="cancelled"><?= $row['status'] ?></span></td>
            <td>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this record?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <?php else: ?>
    <p class="no-data">No cancelled reservations</p>
    <?php endif; ?>
</div>

</div>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const tables = document.getElementsByClassName('data-table');
    
    for(let table of tables) {
        const tr = table.getElementsByTagName('tr');
        for(let i = 1; i < tr.length; i++) {
            const tdEvent = tr[i].getElementsByTagName('td')[1];
            const tdDate = tr[i].getElementsByTagName('td')[0];
            if(tdEvent || tdDate) {
                const eventText = tdEvent.textContent || tdEvent.innerText;
                const dateText = tdDate.textContent || tdDate.innerText;
                if(eventText.toUpperCase().indexOf(filter) > -1 || dateText.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
}
</script>

<?php include("../includes/footer.php"); ?>
