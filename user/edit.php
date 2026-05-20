<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];
$error = '';

if(!$id){
    header("Location: dashboard.php?error=Invalid reservation selected");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id=? AND user_id=? AND status='Pending'");
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header("Location: dashboard.php?error=Only pending reservations can be edited");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $event = isset($_POST['event']) ? trim($_POST['event']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
    $guests = isset($_POST['guests']) ? (int) $_POST['guests'] : 0;
    $special = isset($_POST['special']) ? trim($_POST['special']) : '';

    if($event === '' || $date === '' || $checkout === '' || $guests < 1){
        $error = "Please complete all required fields.";
    } elseif($date < date('Y-m-d')){
        $error = "Reservation date cannot be in the past.";
    } elseif($checkout < $date){
        $error = "Checkout date must be the same day or later than check-in.";
    } elseif($guests > 200){
        $error = "Guest count cannot exceed 200.";
    } else {
        $stmt = $conn->prepare("
            UPDATE reservations
            SET event_type=?, reservation_date=?, checkout_date=?, guests=?, special_request=?
            WHERE id=? AND user_id=? AND status='Pending'
        ");
        $stmt->execute([$event, $date, $checkout, $guests, $special, $id, $user_id]);

        header("Location: dashboard.php?success=Reservation updated");
        exit();
    }
}

$page_title = 'Edit Reservation';
include("../includes/header.php");
?>

<main class="booking-section">
    <section class="booking-box">
        <h2>Edit Reservation</h2>
        <p style="text-align:center; margin-bottom:24px;">Update pending booking details before admin approval.</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div>
                    <label for="event">Event type</label>
                    <select id="event" name="event" required>
                        <?php
                        $events = ['Wedding', 'Birthday', 'Pool Party', 'Family Gathering', 'Debut', 'Corporate Event', 'Anniversary'];
                        foreach($events as $event_option):
                        ?>
                            <option value="<?= htmlspecialchars($event_option) ?>" <?= $row['event_type'] === $event_option ? 'selected' : '' ?>>
                                <?= htmlspecialchars($event_option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="guests">Number of guests</label>
                    <input id="guests" type="number" name="guests" value="<?= htmlspecialchars($row['guests']) ?>" min="1" max="200" required>
                </div>
                <div>
                    <label for="date">Check-in date</label>
                    <input id="date" type="date" name="date" value="<?= htmlspecialchars($row['reservation_date']) ?>" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label for="checkout">Checkout date</label>
                    <input id="checkout" type="date" name="checkout" value="<?= htmlspecialchars($row['checkout_date'] ?: $row['reservation_date']) ?>" min="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <label for="special">Special request</label>
            <textarea id="special" name="special" rows="4"><?= htmlspecialchars($row['special_request'] ?: '') ?></textarea>

            <div class="actions" style="margin-top:18px;">
                <button name="update" class="btn-book">Update Reservation</button>
                <a href="dashboard.php" class="btn-delete">Cancel</a>
            </div>
        </form>
    </section>
</main>

<script>
const dateInput = document.getElementById('date');
const checkoutInput = document.getElementById('checkout');
dateInput.addEventListener('input', function(){
    checkoutInput.min = dateInput.value;
    if(checkoutInput.value < dateInput.value){
        checkoutInput.value = dateInput.value;
    }
});
</script>

<?php include("../includes/footer.php"); ?>
