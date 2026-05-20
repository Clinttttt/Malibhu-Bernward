<?php
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

include("../config/db.php");

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_reservations = $conn->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pending_count = $conn->query("SELECT COUNT(*) FROM reservations WHERE status='Pending'")->fetchColumn();
$approved_count = $conn->query("SELECT COUNT(*) FROM reservations WHERE status='Approved'")->fetchColumn();
$cancelled_count = $conn->query("SELECT COUNT(*) FROM reservations WHERE status='Cancelled'")->fetchColumn();

// Get today's reservations
$today = date('Y-m-d');
$today_reservations = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_date=?");
$today_reservations->execute([$today]);
$today_count = $today_reservations->fetchColumn();

// Get reservations by status
$pending_stmt = $conn->query("SELECT reservations.*, users.name, users.email FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Pending' ORDER BY reservation_date ASC");
$pending = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

$approved_stmt = $conn->query("SELECT reservations.*, users.name, users.email FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Approved' ORDER BY reservation_date ASC");
$approved = $approved_stmt->fetchAll(PDO::FETCH_ASSOC);

$cancelled_stmt = $conn->query("SELECT reservations.*, users.name, users.email FROM reservations JOIN users ON reservations.user_id = users.id WHERE reservations.status='Cancelled' ORDER BY reservation_date ASC");
$cancelled = $cancelled_stmt->fetchAll(PDO::FETCH_ASSOC);

function reservation_schedule($row){
    $start = date('M d, Y', strtotime($row['reservation_date']));
    $end_date = !empty($row['checkout_date']) ? $row['checkout_date'] : $row['reservation_date'];
    $end = date('M d, Y', strtotime($end_date));
    return $start === $end ? $start : $start . ' - ' . $end;
}
?>

<?php
$page_title = 'Admin Dashboard';
include("../includes/header.php");
?>

<div class="container">

<h2>Admin Dashboard</h2>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<!-- Statistics Dashboard -->
<div class="stats-grid" style="margin-bottom:50px;">
    <div class="stat-card">
        <h3><?= $total_users ?></h3>
        <p>Total Users</p>
    </div>
    <div class="stat-card">
        <h3><?= $total_reservations ?></h3>
        <p>Total Bookings</p>
    </div>
    <div class="stat-card">
        <h3><?= $pending_count ?></h3>
        <p>Pending</p>
    </div>
    <div class="stat-card">
        <h3><?= $approved_count ?></h3>
        <p>Approved</p>
    </div>
    <div class="stat-card">
        <h3><?= $cancelled_count ?></h3>
        <p>Cancelled</p>
    </div>
    <div class="stat-card">
        <h3><?= $today_count ?></h3>
        <p>Today's Events</p>
    </div>
</div>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search by customer name, event type, or date..." onkeyup="searchTable()">
</div>

<div style="text-align:right; margin-bottom:30px;">
    <a href="logout.php" class="btn">Logout</a>
</div>

<!-- Pending Reservations -->
<div class="status-section">
    <h3 class="section-title pending-title">Pending Reservations (<?= count($pending) ?>)</h3>
    
    <?php if(count($pending) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Customer</th>
            <th>Email</th>
            <th>Event</th>
            <th>Schedule</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($pending as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="pending"><?= $row['status'] ?></span></td>
            <td>
                <a href="approve.php?id=<?= $row['id'] ?>" class="btn-edit">Approve</a>
                <a href="reject.php?id=<?= $row['id'] ?>" class="btn-delete">Reject</a>
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
    <h3 class="section-title approved-title">Approved Reservations (<?= count($approved) ?>)</h3>
    
    <?php if(count($approved) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Customer</th>
            <th>Email</th>
            <th>Event</th>
            <th>Schedule</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($approved as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="approved"><?= $row['status'] ?></span></td>
            <td>
                <span class="btn-disabled">Approved</span>
                <a href="reject.php?id=<?= $row['id'] ?>" class="btn-delete">Cancel</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <?php else: ?>
    <p class="no-data">No approved reservations</p>
    <?php endif; ?>
</div>

<!-- Cancelled Reservations -->
<div class="status-section">
    <h3 class="section-title cancelled-title">Cancelled Reservations (<?= count($cancelled) ?>)</h3>
    
    <?php if(count($cancelled) > 0): ?>
    <div class="table-wrap">
    <table class="data-table">
        <tr>
            <th>Customer</th>
            <th>Email</th>
            <th>Event</th>
            <th>Schedule</th>
            <th>Guests</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach($cancelled as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['event_type']) ?></td>
            <td><?= htmlspecialchars(reservation_schedule($row)) ?></td>
            <td><?= $row['guests'] ?> people</td>
            <td><span class="cancelled"><?= $row['status'] ?></span></td>
            <td>
                <a href="approve.php?id=<?= $row['id'] ?>" class="btn-edit">Re-approve</a>
                <span class="btn-disabled">Cancelled</span>
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
            const tdName = tr[i].getElementsByTagName('td')[0];
            const tdEmail = tr[i].getElementsByTagName('td')[1];
            const tdEvent = tr[i].getElementsByTagName('td')[2];
            const tdDate = tr[i].getElementsByTagName('td')[3];
            
            if(tdName || tdEmail || tdEvent || tdDate) {
                const nameText = tdName.textContent || tdName.innerText;
                const emailText = tdEmail.textContent || tdEmail.innerText;
                const eventText = tdEvent.textContent || tdEvent.innerText;
                const dateText = tdDate.textContent || tdDate.innerText;
                
                if(nameText.toUpperCase().indexOf(filter) > -1 || 
                   emailText.toUpperCase().indexOf(filter) > -1 ||
                   eventText.toUpperCase().indexOf(filter) > -1 || 
                   dateText.toUpperCase().indexOf(filter) > -1) {
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
