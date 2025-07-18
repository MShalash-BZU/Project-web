<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT customerId FROM customers WHERE userId = :uid");
$stmt->execute([':uid' => $_SESSION['userId']]);
$customerId = $stmt->fetchColumn();

$allowedSort = [
    'flatRefNo' => 'f.flatRefNo',
    'monthlyRent' => 'f.monthlyRent',
    'rentStartDate' => 'r.rentStartDate',
    'rentEndDate' => 'r.rentEndDate',
    'location' => 'f.location',
    'ownerName' => 'o.name'
];
$sort = isset($_GET['sort']) && isset($allowedSort[$_GET['sort']]) ? $_GET['sort'] : 'rentStartDate';
$dir = (isset($_GET['dir']) && strtoupper($_GET['dir']) === 'ASC') ? 'ASC' : 'DESC';

$sql = "
SELECT 
  r.rentStartDate,
  r.rentEndDate,
  f.flatRefNo, f.monthlyRent, f.flatId, f.location,
  o.ownerId, o.name AS ownerName, o.address AS ownerCity, o.telephone, u.email
FROM rentals r
JOIN flats f ON r.flatId = f.flatId
JOIN owners o ON f.ownerId = o.ownerId
JOIN users u ON o.userId = u.userId
WHERE r.customerId = :cid AND r.paymentStatus = 'confirmed'
ORDER BY {$allowedSort[$sort]} $dir
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cid' => $customerId]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Rented Flats</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="layout">
  <?php $active= 'view'; include 'nav.php'; ?>

  <main class="main-content">
    <h2>Rented Flats</h2>
    <table>
      <thead>
        <tr>
          <?php
            $columns = [
              'flatRefNo' => 'Flat Ref',
              'monthlyRent' => 'Rent',
              'rentStartDate' => 'Start Date',
              'rentEndDate' => 'End Date',
              'location' => 'Location',
              'ownerName' => 'Owner'
            ];
            foreach ($columns as $col => $label) {
              $icon = '';
              if ($sort === $col) $icon = $dir === 'ASC' ? '▲' : '▼';
              $query = $_GET;
              $query['sort'] = $col;
              $query['dir'] = ($sort === $col && $dir === 'ASC') ? 'DESC' : 'ASC';
              $link = 'viewRentalFlat.php?' . http_build_query($query);
              echo "<th><a href=\"$link\" style=\"color:#fff;text-decoration:none;\"><span class='sort-icon'>$icon</span>$label</a></th>";
            }
          ?>
        </tr>
      </thead>
      <tbody>
        <?php if ($results): ?>
          <?php foreach ($results as $r): ?>
            <?php
              $today = date('Y-m-d');
              if ($today >= $r['rentStartDate'] && $today <= $r['rentEndDate']) {
                $row_class = 'rental-current';
              } else {
                $row_class = 'rental-past';
              }
            ?>
            <tr class="<?= $row_class ?>">
              <td>
                <a href="flatdetail.php?id=<?= $r['flatId'] ?>" target="_blank" class="flat-link-button">
                  <?= htmlspecialchars($r['flatRefNo']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($r['monthlyRent']) ?> JD</td>
              <td><?= htmlspecialchars($r['rentStartDate']) ?></td>
              <td><?= htmlspecialchars($r['rentEndDate']) ?></td>
              <td><?= htmlspecialchars($r['location']) ?></td>
              <td>
                <a href="ownerCard.php?id=<?= $r['ownerId'] ?>" target="_blank" class="owner-link">
                  <?= htmlspecialchars($r['ownerName']) ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">You have no rentals.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>