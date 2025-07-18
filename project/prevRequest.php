<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit;
}

$flatId = 0;
if (isset($_GET['flatId'])) {
    $flatId = (int) $_GET['flatId'];
}
$stmtCust = $pdo->prepare("SELECT customerId FROM customers WHERE userId = :uid");
$stmtCust->execute([':uid' => $_SESSION['userId']]);
$customerRow = $stmtCust->fetch();
if (!$customerRow) {
    echo "<p>Customer profile not found.</p>";
    exit;
}
$customerId = $customerRow['customerId'];

if ($flatId === 0) {
  echo "<p>Invalid flat ID.</p>";
  exit;
}

$stmtFlat = $pdo->prepare("SELECT f.flatRefNo, f.location, f.address, o.name AS ownerName, o.mobile AS ownerMobile
  FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id");
$stmtFlat->execute([':id' => $flatId]);
$flat = $stmtFlat->fetch();

$ackMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointmentId'])) {
  $appointmentId = (int) $_POST['appointmentId'];

  $check = $pdo->prepare("SELECT * FROM appointments WHERE appointmentId = :id AND status = 'pending' AND appointmentDate > NOW() AND customerId IS NULL");
  $check->execute([':id' => $appointmentId]);
  $slot = $check->fetch();

  if ($slot) {
    $update = $pdo->prepare("UPDATE appointments SET customerId = :custId WHERE appointmentId = :id");
    $update->execute([':custId' => $customerId, ':id' => $appointmentId]);

    $ownerId = $slot['ownerId'];
    $stmtOwnerUser = $pdo->prepare("SELECT userId FROM owners WHERE ownerId = :oid");
    $stmtOwnerUser->execute([':oid' => $ownerId]);
    $ownerUserRow = $stmtOwnerUser->fetch();
    if ($ownerUserRow) {
        $ownerUserId = $ownerUserRow['userId'];
    } else {
        $ownerUserId = null;
    }

    $msg = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (:s, :r, :t, :b)");
    $msg->execute([
      ':s' => $_SESSION['userId'],
      ':r' => $ownerUserId,
      ':t' => 'New Preview Appointment Request',
      ':b' => 'A customer requested to preview flat ID ' . $flatId . ' on ' . $slot['appointmentDate']
    ]);

    $ackMsg = "<section class='ack-msg'>Request sent to owner. Waiting for confirmation.</section>";
} else {
    $ackMsg = "<section class='error-msg'>Sorry, this time slot is not available for booking.</section>";
  }
}

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE flatId = :flat AND appointmentDate > NOW() ORDER BY appointmentDate ASC");
$stmt->execute([':flat' => $flatId]);
$slots = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Preview Appointment</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="layout">
  <?php include 'nav.php'; ?>
  <main class="main-content">
    <h2>Available Time Slots to Preview Flat</h2>

    <?php if ($flat): ?>
      <section class="flat-info">
        <strong>Flat Ref:</strong> <?= htmlspecialchars($flat['flatRefNo']) ?> |
        <strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?> |
        <strong>Address:</strong> <?= htmlspecialchars($flat['address']) ?> <br>
        <strong>Owner:</strong> <?= htmlspecialchars($flat['ownerName']) ?> (<?= htmlspecialchars($flat['ownerMobile']) ?>)
      </section>
    <?php endif; ?>

    <?= $ackMsg ?>

    <table>
      <thead>
        <tr>
          <th>Date & Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($slots): ?>
          <?php foreach ($slots as $slot): ?>
            <tr class="<?= $slot['customerId'] ? 'booked' : 'available' ?>">
              <td><?= htmlspecialchars($slot['appointmentDate']) ?></td>
              <td>
                <?php if ($slot['customerId']): ?>
                  Booked
                <?php else: ?>
                  Available
                <?php endif; ?>
              </td>
              <td>
                <?php if (!$slot['customerId']): ?>
                  <form method="POST">
                    <input type="hidden" name="appointmentId" value="<?= $slot['appointmentId'] ?>">
                    <button type="submit">Book</button>
                  </form>
                <?php else: ?>
                  <em>Unavailable</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3">No available slots.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>