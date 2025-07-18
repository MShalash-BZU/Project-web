<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
} else {
  $id = 0;
}
$stmt = $pdo->prepare("SELECT f.*, o.name AS ownerName, o.nationalId AS ownerNationalId, o.address AS ownerAddress, o.mobile AS ownerMobile FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id");
$stmt->execute([':id' => $id]);
$flat = $stmt->fetch();
if (!$flat) {
    die("Flat not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['rent_flat'] = [
        'flatId' => $flat['flatId'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate']
    ];
    $stmt = $pdo->prepare("SELECT customerId FROM customers WHERE userId = :uid");
    $stmt->execute([':uid' => $_SESSION['userId']]);
    $customerId = $stmt->fetchColumn();
    $start = $_POST['startDate'];
    $end = $_POST['endDate'];
    $days = (strtotime($end) - strtotime($start)) / (60 * 60 * 24);
    $total = round($flat['monthlyRent'] * ($days / 30), 2);
    $stmt = $pdo->prepare("INSERT INTO rentals (flatId, customerId, rentStartDate, rentEndDate, totalAmount, paymentStatus) VALUES (:flat, :cust, :start, :end, :amount, 'pending')");
    $stmt->execute([
        ':flat' => $flat['flatId'],
        ':cust' => $customerId,
        ':start' => $start,
        ':end' => $end,
        ':amount' => $total
    ]);
    $_SESSION['rent_flat']['rentalId'] = $pdo->lastInsertId();

    header("Location: confirmRent.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rent Flat</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="layout">
  <?php include 'nav.php'; ?>
  <main class="main-content">
    <h2>Rent Flat</h2>
    <form method="post">
      <fieldset>
        <legend>Flat Details</legend>
        <table>
          <tr>
        <th>Flat Ref</th>
        <td><?= htmlspecialchars($flat['flatRefNo']) ?></td>
          </tr>
          <tr>
        <th>Flat Number</th>
        <td><?= htmlspecialchars($flat['flatId']) ?></td>
          </tr>
          <tr>
        <th>Location</th>
        <td><?= htmlspecialchars($flat['location']) ?></td>
          </tr>
          <tr>
        <th>Address</th>
        <td><?= htmlspecialchars($flat['address']) ?></td>
          </tr>
          <tr>
        <th>Details</th>
        <td><?= htmlspecialchars($flat['bedrooms']) ?> BR, <?= htmlspecialchars($flat['bathrooms']) ?> Bath, <?= $flat['furnished'] ? 'Furnished' : 'Unfurnished' ?></td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <legend>Owner Details</legend>
        <table>
          <tr>
        <th>Name</th>
        <td><?= htmlspecialchars($flat['ownerName']) ?></td>
          </tr>
          <tr>
        <th>National ID</th>
        <td><?= htmlspecialchars($flat['ownerNationalId']) ?></td>
          </tr>
          <tr>
        <th>Address</th>
        <td><?= htmlspecialchars($flat['ownerAddress']) ?></td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <legend>Rental Period</legend>
        <label for="st" class="required">Start Date:</label>
        <input type="date" id="st" name="startDate" required>
        <label for="end" class="required">End Date:</label>
        <input type="date" id="end" name="endDate" required>
      </fieldset>
      <button type="submit">Calculate Rent</button>
    </form>
  </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>