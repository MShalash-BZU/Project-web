<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit;
}
$stmt = $pdo->prepare("SELECT customerId FROM customers WHERE userId = :uid");
$stmt->execute([':uid' => $_SESSION['userId']]);
$customerId = $stmt->fetchColumn();
$sql = "SELECT f.flatRefNo, f.location, f.address, f.monthlyRent, r.rentStartDate, r.rentEndDate, f.flatId, r.rentalId
FROM rentals r 
JOIN flats f ON r.flatId = f.flatId
WHERE r.customerId = :custId AND r.paymentStatus = 'pending'
ORDER BY r.rentStartDate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':custId' => $customerId]);
$flats = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shalash Flat Rent</title>
  <link rel="stylesheet" href="css/style2.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #333;
      color: white;
    }
    tr:nth-child(even) { background-color: #f9f9f9; }
    tr:hover { background-color: #e0f0ff; }
    .ref-btn {
      background-color: #007bff;
      color: white;
      padding: 5px 10px;
      text-decoration: none;
      border-radius: 5px;
    }
    .ref-btn:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <?php $act='basket'; 
  include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

  <main class="main-content">
   <h2>Pending Flat Rentals (Basket)</h2>

    <?php if (count($flats) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Ref No</th>
            <th>Location</th>
            <th>Address</th>
            <th>Rent</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($flats as $flat): ?>
            <tr>
              <td><?= htmlspecialchars($flat['flatRefNo']) ?></td>
              <td><?= htmlspecialchars($flat['location']) ?></td>
              <td><?= htmlspecialchars($flat['address']) ?></td>
              <td><?= htmlspecialchars($flat['monthlyRent']) ?> JD</td>
              <td><?= htmlspecialchars($flat['rentStartDate']) ?></td>
              <td><?= htmlspecialchars($flat['rentEndDate']) ?></td>
              <td> <a class="ref-btn" href="confirmRent.php?rentalId=<?= $flat['rentalId'] ?>">Confirm</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No pending rentals in your basket.</p>
    <?php endif; ?>
  </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>