<?php
session_start();
require_once("../config/admin_auth.php");

if(!admin_credentials_exist()){
    header("Location: ../admin/setup.php");
    exit();
}

include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$error = '';
$booked_dates_stmt = $conn->query("SELECT reservation_date FROM reservations WHERE status='Approved'");
$booked_dates = $booked_dates_stmt->fetchAll(PDO::FETCH_COLUMN);
$booked_dates_json = json_encode($booked_dates);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_id = $_SESSION['user_id'];
    $event = isset($_POST['event']) ? trim($_POST['event']) : '';
    $checkin = isset($_POST['checkin']) ? trim($_POST['checkin']) : '';
    $checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
    $guests = isset($_POST['guests']) ? (int) $_POST['guests'] : 0;
    $special = isset($_POST['special']) ? trim($_POST['special']) : '';

    if($event === '' || $checkin === '' || $checkout === '' || $guests < 1){
        $error = "Please complete all required reservation fields.";
    } elseif($checkin < date('Y-m-d')){
        $error = "Reservation date cannot be in the past.";
    } elseif($checkout < $checkin){
        $error = "Checkout date must be the same day or later than check-in.";
    } elseif($guests > 200){
        $error = "Guest count cannot exceed 200.";
    } else {
        $check_stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM reservations
            WHERE status='Approved'
              AND reservation_date <= ?
              AND COALESCE(checkout_date, reservation_date) >= ?
        ");
        $check_stmt->execute([$checkout, $checkin]);

        if($check_stmt->fetchColumn() > 0){
            $error = "The selected date range overlaps an approved reservation. Please choose another date.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO reservations (user_id, event_type, reservation_date, checkout_date, guests, special_request, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')
            ");
            $stmt->execute([$user_id, $event, $checkin, $checkout, $guests, $special]);
            header("Location: dashboard.php?success=Reservation submitted for admin approval");
            exit();
        }
    }
}

$page_title = 'New Reservation';
include("../includes/header.php");
?>

<main class="booking-section">
    <section class="booking-box">
        <h2>New Reservation</h2>
        <p style="text-align:center; margin-bottom:24px;">Choose an available schedule and submit it for admin approval.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="calendar-section">
            <h3>Availability Guide</h3>
            <p style="text-align:center;">Dates with approved bookings are blocked by the system before submission.</p>
            <div class="calendar-legend">
                <div class="legend-item"><span class="legend-box available"></span><span>Available</span></div>
                <div class="legend-item"><span class="legend-box booked"></span><span>Booked</span></div>
                <div class="legend-item"><span class="legend-box selected"></span><span>Your selection</span></div>
            </div>
        </div>

        <form method="POST" id="reservationForm">
            <div class="form-grid">
                <div>
                    <label for="checkin">Check-in date</label>
                    <input type="date" name="checkin" id="checkin" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label for="checkout">Checkout date</label>
                    <input type="date" name="checkout" id="checkout" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label for="event">Event type</label>
                    <select name="event" id="event" required>
                        <option value="">Select event type</option>
                        <option>Wedding</option>
                        <option>Birthday</option>
                        <option>Pool Party</option>
                        <option>Family Gathering</option>
                        <option>Debut</option>
                        <option>Corporate Event</option>
                        <option>Anniversary</option>
                    </select>
                </div>
                <div>
                    <label for="guests">Number of guests</label>
                    <input type="number" name="guests" id="guests" min="1" max="200" placeholder="Example: 80" required>
                </div>
            </div>

            <label for="special">Special request</label>
            <textarea name="special" id="special" rows="4" placeholder="Food setup, decoration notes, accessibility needs, or other requests"></textarea>

            <button type="submit" name="reserve" class="btn-book">Submit Reservation</button>
        </form>
    </section>
</main>

<script>
const bookedDates = <?= $booked_dates_json ?: '[]' ?>;
const checkin = document.getElementById('checkin');
const checkout = document.getElementById('checkout');

checkin.addEventListener('input', function(e){
    const selectedDate = e.target.value;
    checkout.min = selectedDate || '<?= date('Y-m-d') ?>';

    if(checkout.value && checkout.value < selectedDate){
        checkout.value = selectedDate;
    }

    if(bookedDates.includes(selectedDate)){
        showToast('This date already has an approved reservation. Please choose another date.', 'error');
        e.target.value = '';
    }
});
</script>

<?php include("../includes/footer.php"); ?>
