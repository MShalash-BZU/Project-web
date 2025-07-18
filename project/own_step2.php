<?php
session_start();
require_once 'database.inc.php';
if (!isset($_SESSION['owner_reg_step1'])) {
  header("Location: owner_step1.php");
  exit;
}

$OWN = [
  'username' => '',
  'password' => '',
  'confirm_password' => ''
];
$emailErr = '';
$PASSErr= '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $OWN['username'] = trim($_POST['username']);
  $OWN['password'] = $_POST['password'];
  $OWN['confirm_password'] = $_POST['confirm_password'];
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute([':email' => $OWN['username']]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        $emailErr = "This email is already registered. Please use another email.";
    } elseif ($OWN['password'] === $OWN['confirm_password']) {
        $_SESSION['owner_reg_step2'] = [
            'username' => $OWN['username'],
            'password' => $OWN['password']
        ];
        header("Location: own_step3.php");
        exit;
    } else {
        $PASSErr= "Passwords do not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Owner Register Step-2</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <h2>Owner Registration - Step 2 (E-Account)</h2>
      <form method="POST">
        <label for="username" class="required">Email (Username):</label><br>
        <input type="email" id="username" name="username" required /><br>
        <?php if ($emailErr): ?>
          <section style="color:red"><?= $emailErr ?></section>
        <?php endif; ?>
        <br>
        <label for="password" class="required">Password:</label><br>
        <input type="password" id="password" pattern="^\d.{4,13}[a-z]$" name="password" required />
        <br><br>
        <label for="confirm_password" class="required">Confirm Password:</label><br>
        <input type="password" id="confirm_password" pattern="^\d.{4,13}[a-z]$" name="confirm_password" required />
        <?php if ($PASSErr): ?>
          <section style="color:red"><?= $PASSErr ?></section>
        <?php endif; ?>
        <br>
        <button type="submit">Next Step</button>
      </form>
    </main>
  </section>
  <?php include 'footer.php'; ?>
</body>

</html>