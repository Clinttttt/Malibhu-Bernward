<?php
session_start();

// Check if admin is configured (first run check)
$admin_file = '../config/admin.txt';
if(!file_exists($admin_file)){
    header("Location: ../admin/setup.php");
    exit();
}

include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['reserve'])){

    $user_id = $_SESSION['user_id'];

    $event = $_POST['event'];

    $checkin = $_POST['checkin'];

    $checkout = $_POST['checkout'];

    $guests = $_POST['guests'];

    $special = $_POST['special'];

    $stmt = $conn->prepare("INSERT INTO reservations (user_id,event_type,reservation_date,guests,status) VALUES (?,?,?,?,'Pending')");
    $stmt->execute([$user_id, $event, $checkin, $guests]);

    echo "
    <script>
    alert('Reservation Submitted Successfully!');
    window.location='dashboard.php';
    </script>
    ";
}
?>

<?php include("../includes/header.php"); ?>

<div class="booking-section">

    <div class="booking-box">

        <h2>Online Reservation</h2>

        <form method="POST">

            <div class="form-grid">

                <div>
                    <label>Check-in Date</label>

                    <input type="date"
                    name="checkin"
                    min="<?= date('Y-m-d') ?>"
                    required>
                </div>

                <div>
                    <label>Check-out Date</label>

                    <input type="date"
                    name="checkout"
                    min="<?= date('Y-m-d') ?>"
                    required>
                </div>

                <div>
                    <label>Event Type</label>

                    <select name="event" required>

                        <option>Wedding</option>

                        <option>Birthday</option>

                        <option>Pool Party</option>

                        <option>Family Gathering</option>

                        <option>Debut</option>

                    </select>
                </div>

                <div>
                    <label>Guests</label>

                    <input type="number"
                    name="guests"
                    min="1"
                    max="100"
                    required>
                </div>

            </div>

            <label>Special Request</label>

            <input type="text"
            name="special"
            placeholder="Optional">

            <button
            type="submit"
            name="reserve"
            class="btn-book">

            Confirm Reservation

            </button>

        </form>

    </div>

</div>

<?php include("../includes/footer.php"); ?>