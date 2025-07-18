<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['owner_reg_step1']) || !isset($_SESSION['owner_reg_step2'])) {
    header("Location: owner_step1.php");
    exit;
}

$step1 = $_SESSION['owner_reg_step1'];
$step2 = $_SESSION['owner_reg_step2'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $stmtUser = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, 'owner')");
        $stmtUser->execute([
            ':email' => $step2['username'],
            ':password' => $step2['password']
        ]);
        $userId = $pdo->lastInsertId();

        $address = "{$step1['addressFlat']}, {$step1['addressStreet']}, {$step1['addressCity']} {$step1['addressPostal']}";

        $stmtOwner = $pdo->prepare("INSERT INTO owners (userId, nationalId, name, address, dateOfBirth, mobile, telephone, bankName,
         bankBranch, bankAccountNumber)
        VALUES (:userId, :nid, :name, :addr, :dob, :mob, :tel, :bname, :bbranch, :bacc)");

        $stmtOwner->execute([
            ':userId' => $userId,
            ':nid' => $step1['nationalId'],
            ':name' => $step1['name'],
            ':addr' => $address,
            ':dob' => $step1['dob'],
            ':mob' => $step1['mobile'],
            ':tel' => $step1['telephone'],
            ':bname' => $step1['bankName'],
            ':bbranch' => $step1['bankBranch'],
            ':bacc' => $step1['accountNumber']
        ]);


        $ownerId = str_pad($userId, 9, '0', STR_PAD_LEFT);
        unset($_SESSION['owner_reg_step1'], $_SESSION['owner_reg_step2']);
        header("Location: login.php");
        exit;

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Register Step-3</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
       <h2>Owner Registration - Step 3 (Confirmation)</h2>
    <form method="POST">
        <table border="1" cellpadding="2" >
            <caption><strong>Personal Info</strong></caption>
            <tr>
          <th>National ID</th>
          <td><?= htmlspecialchars($step1['nationalId']) ?></td>
            </tr>
            <tr>
          <th>Name</th>
          <td><?= htmlspecialchars($step1['name']) ?></td>
            </tr>
            <tr>
          <th>Date of Birth</th>
          <td><?= htmlspecialchars($step1['dob']) ?></td>
            </tr>
        </table>

        <table border="1" cellpadding="2" >
            <caption><strong>Contact Info</strong></caption>
            <tr>
          <th>Email</th>
          <td><?= htmlspecialchars($step1['email']) ?></td>
            </tr>
            <tr>
          <th>Mobile</th>
          <td><?= htmlspecialchars($step1['mobile']) ?></td>
            </tr>
            <tr>
          <th>Telephone</th>
          <td><?= htmlspecialchars($step1['telephone']) ?></td>
            </tr>
            <tr>
          <th>Address</th>
          <td><?= htmlspecialchars($step1['addressFlat'] . ', ' . $step1['addressStreet'] . ', ' . $step1['addressCity'] . ' ' . $step1['addressPostal']) ?></td>
            </tr>
        </table>

        <table border="1" cellpadding="2" >
            <caption><strong>Bank Info</strong></caption>
            <tr>
          <th>Bank Name</th>
          <td><?= htmlspecialchars($step1['bankName']) ?></td>
            </tr>
            <tr>
          <th>Bank Branch</th>
          <td><?= htmlspecialchars($step1['bankBranch']) ?></td>
            </tr>
            <tr>
          <th>Account Number</th>
          <td><?= htmlspecialchars($step1['accountNumber']) ?></td>
            </tr>
        </table>

        <table border="1" cellpadding="2" >
            <caption><strong>E-Account Info</strong></caption>
            <tr>
          <th>Username</th>
          <td><?= htmlspecialchars($step2['username']) ?></td>
            </tr>
            <tr>
          <th>Password</th>
          <td>********</td>
            </tr>
        </table>
        <button type="submit">Confirm</button>
    </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>
