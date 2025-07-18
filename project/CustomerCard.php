<?php
session_start();
require_once 'database.inc.php';

$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$customerId) {
    echo "Customer not found.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.name, c.address AS city, c.mobile, u.email, u.userId
    FROM customers c
    JOIN users u ON c.userId = u.userId
    WHERE c.customerId = :cid
");
$stmt->execute([':cid' => $customerId]);
$customer = $stmt->fetch();

if (!$customer) {
    echo "Customer not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Customer Card</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php
  $act = 'login';
  include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <section class="cus-card">
        <h2>
            <?= htmlspecialchars($customer['name']) ?>
        </h2>
        <section class="city"><?= htmlspecialchars($customer['city']) ?></section>
        <section class="contact">
            <span class="icon">&#128222;</span>
            <?= htmlspecialchars($customer['mobile']) ?>
        </section>
        <section class="contact">
            <span class="icon">&#9993;</span>
            <a href="mailto:<?= htmlspecialchars($customer['email']) ?>">
                <?= htmlspecialchars($customer['email']) ?>
            </a>
        </section>
    </section>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>