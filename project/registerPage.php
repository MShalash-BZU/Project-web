<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php
  $act= 'register';
   include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <h2>Select User Type</h2>

    <section style="display: flex; gap: 2rem; margin-top: 2rem;">
      <form action="cus_step1.php" method="get">
        <button type="submit" class="role-button">Register as Customer</button>
      </form>

      <form action="own_step1.php" method="get">
        <button type="submit" class="role-button">Register as Owner</button>
      </form>
    </section>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>
