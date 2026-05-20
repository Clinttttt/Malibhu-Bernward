<?php
require_once("config/admin_auth.php");
if(!admin_credentials_exist()){
    header("Location: admin/setup.php");
    exit();
}

include("config/db.php");

$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_reservations = $conn->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$approved_reservations = $conn->query("SELECT COUNT(*) FROM reservations WHERE status='Approved'")->fetchColumn();
$pending_reservations = $conn->query("SELECT COUNT(*) FROM reservations WHERE status='Pending'")->fetchColumn();

$page_title = 'Online Resort Reservation';
include("includes/header.php");
?>

<section class="hero">
    <div class="hero-overlay">
        <h2>Malibhu View Resort</h2>
        <p>Reserve resort events, pool gatherings, family celebrations, and private functions through a fast online booking workflow.</p>
        <div class="hero-buttons">
            <a href="user/reserve.php" class="btn-gold">Start Reservation</a>
            <a href="user/login.php" class="btn">View My Bookings</a>
        </div>
    </div>
</section>

<section class="features">
    <h2>Live Booking Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= $total_users ?></h3>
            <p>Registered guests</p>
        </div>
        <div class="stat-card">
            <h3><?= $total_reservations ?></h3>
            <p>Total reservations</p>
        </div>
        <div class="stat-card">
            <h3><?= $approved_reservations ?></h3>
            <p>Approved events</p>
        </div>
        <div class="stat-card">
            <h3><?= $pending_reservations ?></h3>
            <p>Pending requests</p>
        </div>
    </div>
</section>

<section class="features">
    <h2>Resort Services</h2>
    <div class="feature-grid">
        <article class="feature-card">
            <img src="images/gallery/luxury_pool.jpg" alt="Malibhu View Resort pool area">
            <h3>Pool Reservations</h3>
            <p>Reserve a private swimming schedule with guest capacity tracking and admin approval.</p>
        </article>
        <article class="feature-card">
            <img src="images/gallery/event_venue.jpg" alt="Decorated event venue">
            <h3>Event Venue</h3>
            <p>Book weddings, birthdays, debuts, anniversaries, and corporate events in one organized form.</p>
        </article>
        <article class="feature-card">
            <img src="images/gallery/relaxing_resort.jpg" alt="Relaxing resort view">
            <h3>Guest Dashboard</h3>
            <p>Guests can edit pending reservations, review status updates, and print approved booking receipts.</p>
        </article>
    </div>
</section>

<section class="features">
    <h2>Built For Presentation</h2>
    <div class="feature-grid">
        <div class="stat-card">
            <h3>01</h3>
            <p>Admin dashboard with search, approval, cancellation, and live booking counts.</p>
        </div>
        <div class="stat-card">
            <h3>02</h3>
            <p>User reservation flow with booked date validation and a professional receipt page.</p>
        </div>
        <div class="stat-card">
            <h3>03</h3>
            <p>Responsive display for laptop demos and mobile views during the project defense.</p>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
