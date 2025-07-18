<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['customer_reg_step1']) || !isset($_SESSION['customer_reg_step2'])) {
    header("Location: cus_step1.php");
    exit;
}

$step1 = $_SESSION['customer_reg_step1'];
$step2 = $_SESSION['customer_reg_step2'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $stmtUser = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (:email, :pass, 'customer')");
        $stmtUser->execute([
            ':email' => $step2['username'],
            ':pass' => $step2['password']
        ]);
        $userId = $pdo->lastInsertId();
        $stmtCus = $pdo->prepare("INSERT INTO customers (userId, nationalId, name, address, dateOfBirth, mobile, telephone,photo) 
            VALUES (:userId, :nid, :name, :addr, :dob, :mob, :tel, 'images/user.jpg')");
        $fullAddress = $step1['addressFlat'] . ', ' . $step1['addressStreet'] . ', ' . $step1['addressCity'] . ' ' . $step1['addressPostal'];

        $stmtCus->execute([
            ':userId' => $userId,
            ':nid'     => $step1['nationalId'],
            ':name'    => $step1['name'],
            ':addr'    => $fullAddress,
            ':dob'     => $step1['dob'],
            ':mob'     => $step1['mobile'],
            ':tel'     => $step1['telephone']
        ]);

        $userIdStr = (string)$userId;
        $customerId = '';
        $len = strlen($userIdStr);
        for ($i = 0; $i < 9 - $len; $i++) {
            $customerId .= '0';
        }
        $customerId .= $userIdStr;

        unset($_SESSION['customer_reg_step1'], $_SESSION['customer_reg_step2']);

        header("Location: login.php");
        exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>customer Register Step-3</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
       <h2>Customer Registration - Step 3 (Confirmation)</h2>
    <form method="POST">
        <table cellpadding="2">
            <caption style="font-weight: bold; text-align: left;">Personal Info</caption>
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
        <table cellpadding="2">
            <caption style="font-weight: bold; text-align: left;">Contact Info</caption>
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
        <table cellpadding="2" >
            <caption style="font-weight: bold; text-align: left;">E-Account Info</caption>
            <tr>
          <th>Username</th>
          <td><?= htmlspecialchars($step2['username']) ?></td>
            </tr>
            <tr>
          <th>Password</th>
          <td>******** </td>
            </tr>
        </table>
        <button type="submit">Confirm and Submit</button>
    </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>

