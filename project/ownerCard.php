<?php
session_start();
require_once 'database.inc.php';

$ownerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$ownerId) {
    echo "Owner not found.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.name, o.address AS city, o.mobile, u.email
    FROM owners o
    JOIN users u ON o.userId = u.userId
    WHERE o.ownerId = :oid
");
$stmt->execute([':oid' => $ownerId]);
$owner = $stmt->fetch();

if (!$owner) {
    echo "Owner not found.";
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Owner Card</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php
  $act = 'login';
  include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <section class="own-card">
        <h2><?= htmlspecialchars($owner['name']) ?></h2>
        <section class="city"><?= htmlspecialchars($owner['city']) ?></section>
        <section class="contact">
            <span class="icon">&#128222;</span>
            <?= htmlspecialchars($owner['mobile']) ?>
        </section>
        <section class="contact">
            <span class="icon">&#9993;</span>
            <a href="mailto:<?= htmlspecialchars($owner['email']) ?>">
                <?= htmlspecialchars($owner['email']) ?>
            </a>
        </section>
    </section>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>