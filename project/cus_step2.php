<?php
session_start();
require_once 'database.inc.php';
if (!isset($_SESSION['customer_reg_step1'])) {
  header("Location: cus_step1.php");
  exit;
}
$emailError = '';
$PASSError = '';
$CUS = ['username' => '', 'password' => '', 'confirm_password' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $CUS['username'] = trim($_POST['username']);
  $CUS['password'] = $_POST['password'];
  $CUS['confirm_password'] = $_POST['confirm_password'];

  
   $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
  $stmt->execute([':email' => $CUS['username']]);
  $emailExists = $stmt->fetchColumn();

  if ($emailExists) {
    $emailError = "This email is already registered. Please use another email.";
  } elseif ($CUS['password'] === $CUS['confirm_password']) {
    $_SESSION['customer_reg_step2'] = [
      'username' => $CUS['username'],
      'password' => $CUS['password']
    ];
    header("Location: cus_step3.php");
    exit;
  }else{
    $PASSError = "Passwords do not match. Please try again.";
  }
  
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>customer Register Step-2</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <h2>Customer Registration - Step 2 (E-Account)</h2>
      <form method="POST">
        <label for="username" class="required">Email (Username):</label><br>
        <input type="email" id="username" name="username" required /><br>
        <?php if ($emailError): ?>
           <section style="color:red"><?= $emailError ?></section>
        <?php endif; ?>
        <br>
        <label for="password" class="required">Password:</label><br>
        <input type="password" id="password" pattern="^\d.{4,13}[a-z]$" name="password" required />
        <br><br>
        <label for="confirm_password" class="required">Confirm Password:</label><br>
        <input type="password" id="confirm_password" pattern="^\d.{4,13}[a-z]$" name="confirm_password" required /><br>
        <?php if ($PASSError): ?>
           <section style="color:red"><?= $PASSError ?></section>
        <?php endif; ?>
        <br>
        <button type="submit">Next Step</button>
      </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>