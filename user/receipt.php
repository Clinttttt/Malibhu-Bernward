<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if(!$id){
    header("Location: dashboard.php?error=Invalid reservation selected");
    exit();
}

$stmt = $conn->prepare("
    SELECT reservations.*, users.name, users.email
    FROM reservations
    JOIN users ON reservations.user_id = users.id
    WHERE reservations.id=? AND reservations.user_id=? AND reservations.status='Approved'
");
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    header("Location: dashboard.php?error=Receipt is available only for approved reservations");
    exit();
}

$checkout_date = !empty($row['checkout_date']) ? $row['checkout_date'] : $row['reservation_date'];
$schedule = date('F d, Y', strtotime($row['reservation_date']));
if($checkout_date !== $row['reservation_date']){
    $schedule .= ' - ' . date('F d, Y', strtotime($checkout_date));
}

$qr_data = "Malibhu Resort Booking #" . $id . " | " . $row['name'] . " | " . $row['event_type'] . " | " . $schedule;
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservation Receipt #<?= $id ?></title>
<link rel="stylesheet" href="../css/style.css">
<style>
@media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .receipt-container { box-shadow: none !important; border: 1px solid #111 !important; margin: 0 auto; }
}

.receipt-shell {
    min-height: 100vh;
    padding: 36px 16px;
    background: #f5f7f2;
}

.receipt-container {
    width: min(860px, 100%);
    margin: 0 auto;
    padding: clamp(24px, 5vw, 46px);
}

.receipt-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    padding-bottom: 24px;
    margin-bottom: 28px;
    border-bottom: 2px solid var(--line);
}

.receipt-header h1 {
    color: var(--forest);
    font-size: clamp(30px, 5vw, 44px);
    line-height: 1;
}

.receipt-id {
    text-align: right;
    color: var(--muted);
    font-weight: 800;
}

.receipt-info {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
}

.info-section {
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 20px;
}

.info-section h3 {
    margin-bottom: 12px;
    color: var(--forest);
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 10px 0;
    border-bottom: 1px solid #edf1ee;
}

.info-row:last-child {
    border-bottom: 0;
}

.info-label {
    color: var(--muted);
    font-weight: 800;
}

.info-value {
    text-align: right;
    font-weight: 800;
}

.qr-section {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    margin: 28px 0;
    padding: 24px;
    border-radius: 8px;
    background: var(--mist);
}

.qr-section img {
    width: 160px;
    height: 160px;
    border: 8px solid #fff;
    border-radius: 8px;
}

.receipt-footer {
    padding-top: 24px;
    border-top: 2px solid var(--line);
    color: var(--muted);
    text-align: center;
}

.print-buttons {
    justify-content: center;
    margin-top: 24px;
}

@media (max-width: 720px) {
    .receipt-header,
    .qr-section {
        display: block;
        text-align: center;
    }

    .receipt-id {
        margin-top: 12px;
        text-align: center;
    }

    .receipt-info {
        grid-template-columns: 1fr;
    }

    .qr-section img {
        margin: 0 auto 16px;
    }
}
</style>
</head>

<body>
<main class="receipt-shell">
    <section class="receipt-container">
        <div class="receipt-header">
            <div>
                <h1>Malibhu View Resort</h1>
                <p>Official approved reservation receipt</p>
            </div>
            <div class="receipt-id">
                <p>Receipt #<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></p>
                <p><?= date('F d, Y h:i A') ?></p>
            </div>
        </div>

        <div class="receipt-info">
            <div class="info-section">
                <h3>Customer Information</h3>
                <div class="info-row"><span class="info-label">Name</span><span class="info-value"><?= htmlspecialchars($row['name']) ?></span></div>
                <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?= htmlspecialchars($row['email']) ?></span></div>
                <div class="info-row"><span class="info-label">Booking ID</span><span class="info-value">#<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></span></div>
            </div>

            <div class="info-section">
                <h3>Reservation Details</h3>
                <div class="info-row"><span class="info-label">Event</span><span class="info-value"><?= htmlspecialchars($row['event_type']) ?></span></div>
                <div class="info-row"><span class="info-label">Schedule</span><span class="info-value"><?= htmlspecialchars($schedule) ?></span></div>
                <div class="info-row"><span class="info-label">Guests</span><span class="info-value"><?= htmlspecialchars($row['guests']) ?> people</span></div>
                <div class="info-row"><span class="info-label">Status</span><span class="approved"><?= htmlspecialchars($row['status']) ?></span></div>
            </div>
        </div>

        <?php if(!empty($row['special_request'])): ?>
            <div class="info-section" style="margin-top:22px;">
                <h3>Special Request</h3>
                <p><?= nl2br(htmlspecialchars($row['special_request'])) ?></p>
            </div>
        <?php endif; ?>

        <div class="qr-section">
            <img src="<?= $qr_url ?>" alt="Reservation verification QR code">
            <div>
                <h3>Verification QR Code</h3>
                <p>Present this receipt or QR code at the resort entrance for booking verification.</p>
            </div>
        </div>

        <div class="receipt-footer">
            <h3>Thank you for choosing Malibhu View Resort.</h3>
            <p>For inquiries, contact info@malibhuresort.com or +63 123 456 7890.</p>
        </div>

        <div class="actions print-buttons no-print">
            <button onclick="window.print()" class="btn">Print Receipt</button>
            <a href="dashboard.php" class="btn-delete">Back to Dashboard</a>
        </div>
    </section>
</main>
</body>
</html>
