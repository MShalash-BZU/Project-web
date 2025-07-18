<?php
session_start();
require_once 'database.inc.php';

if (isset($_SESSION['userId'])) {
  header('Location: index.php');
  exit;
}
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();

  if ($user && $password === $user['password']) {
    $_SESSION['userId'] = $user['userId'];
    $_SESSION['username'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    if ($user['role'] === 'owner') {
      $stmt2 = $pdo->prepare("SELECT name FROM owners WHERE userId = :userId");
      $stmt2->execute([':userId' => $user['userId']]);
      $row = $stmt2->fetch();
      $_SESSION['name'] = $row['name'];
    } elseif ($user['role'] === 'customer') {
      $stmt2 = $pdo->prepare("SELECT name FROM customers WHERE userId = :userId");
      $stmt2->execute([':userId' => $user['userId']]);
      $row = $stmt2->fetch();
      $_SESSION['name'] = $row['name'];
    } elseif ($user['role'] === 'manager') {
      $_SESSION['name'] = 'Manager';
    }



    header('Location: index.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php
  $act = 'login';
  include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      
        
          <h1 style="text-align:center;"><strong>Login</strong></h1>
        
        <form method="post" class="login-form">
          <label for="email" class="required">Email:</label><br>
          <input type="email" id="email" name="email" class="login_text" required>
          <br>
          <br>
          <label for="password" class="required">Password:</label><br>
          <input type="password" id="password" name="password" class="login_text" required><br>
          <button type="submit">Login</button>
        </form>
      
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>