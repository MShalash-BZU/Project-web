<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['rentalId'])) {
    $rentalId = (int)$_GET['rentalId'];
} elseif (isset($_SESSION['rent_flat']['rentalId'])) {
    $rentalId = (int)$_SESSION['rent_flat']['rentalId'];
} else {
    die("No rental selected.");
}

$stmt = $pdo->prepare("SELECT r.*, f.*, o.name AS ownerName, o.mobile AS ownerMobile FROM rentals r
    JOIN flats f ON r.flatId = f.flatId
    JOIN owners o ON f.ownerId = o.ownerId
    WHERE r.rentalId = :rid");
$stmt->execute([':rid' => $rentalId]);
$rent = $stmt->fetch();
if (!$rent) {
    die("Rental not found.");
}

$start = $rent['rentStartDate'];
$end = $rent['rentEndDate'];
$days = (strtotime($end) - strtotime($start)) / (60 * 60 * 24);
$total = $rent['totalAmount'];

$stmt = $pdo->prepare("SELECT customerId FROM customers WHERE userId = :uid");
$stmt->execute([':uid' => $_SESSION['userId']]);
$customerId = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $pdo->prepare("UPDATE rentals SET paymentStatus = 'confirmed' WHERE rentalId = :rid");
    $stmt->execute([':rid' => $rentalId]);
    $stmtOwner = $pdo->prepare("SELECT o.userId, o.name, o.mobile FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id");
    $stmtOwner->execute([':id' => $rent['flatId']]);
    $owner = $stmtOwner->fetch();

    $stmtCustomer = $pdo->prepare("SELECT u.userId, c.name, c.mobile FROM customers c JOIN users u ON c.userId = u.userId WHERE c.customerId = :cid");
    $stmtCustomer->execute([':cid' => $customerId]);
    $customer = $stmtCustomer->fetch();

    $stmtMsg1 = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
    $stmtMsg1->execute([
        $customer['userId'],
        $owner['userId'],
        "Flat Rented",
        "Your flat (Ref: {$rent['flatRefNo']}) has been rented by {$customer['name']} (Mobile: {$customer['mobile']})."
    ]);
    $stmtMsg2 = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
    $stmtMsg2->execute([
        $owner['userId'],
        $customer['userId'],
        "Flat Rent Confirmed",
        "You have successfully rented flat (Ref: {$rent['flatRefNo']}). Please collect the key from the owner: {$owner['name']} (Mobile: {$owner['mobile']})."
    ]);
    $stmtMsg3 = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
    $stmtMsg3->execute([
        $customer['userId'],
        1,
        "Flat Rental Confirmed",
        "Customer {$customer['name']}has rented flat (Ref: {$rent['flatRefNo']})."
    ]);
    unset($_SESSION['rent_flat']);
    $confirmation = "Your flat has been successfully rented! Please collect the key from the owner: {$owner['name']} (Mobile: {$owner['mobile']}).";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Confirm Rent</title>
    <link rel="stylesheet" href="css/style2.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <section class="layout">
        <?php include 'nav.php'; ?>
        <main class="main-content">
            <h2>Confirm Rent</h2>
            <?php if (!empty($confirmation)): ?>
                <div class="confirmation"><?= $confirmation ?></div>
            <?php else: ?>
                <?php if (!empty($error)): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
                <form method="post">
                    <fieldset>
                        <legend>Summary</legend>
                        <p><strong>Flat Ref:</strong> <?= htmlspecialchars($rent['flatRefNo']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($rent['location']) ?></p>
                        <p><strong>Rental Period:</strong> <?= htmlspecialchars($start) ?> to <?= htmlspecialchars($end) ?> (<?= $days ?> days)</p>
                        <p><strong>Total Amount:</strong> <?= $total ?> JD</p>
                    </fieldset>
                    <fieldset>
                        <legend>Payment Details</legend>
                        <label class="required">Credit Card Number:</label>
                        <input type="text" name="cc_number" minlength="9" maxlength="9" required>
                        <label class="required">Expire Date:</label>
                        <input type="month" name="cc_exp" required>
                        <label class="required">Name on Card:</label>
                        <input type="text" name="cc_name" required>
                    </fieldset>
                    <button type="submit">Confirm Rent</button>
                </form>
            <?php endif; ?>
        </main>
    </section>
    <?php include 'footer.php'; ?>
</body>

</html>