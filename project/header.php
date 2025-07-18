<?php
if (!isset($act)) {
  $act = '';
}
$userPhoto = 'images/user.jpg';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
  require_once 'database.inc.php';
  $stmt = $pdo->prepare("SELECT photo FROM customers WHERE userId = :uid");
  $stmt->execute([':uid' => $_SESSION['userId']]);
  $row = $stmt->fetch();
  if ($row && !empty($row['photo']) && file_exists($row['photo'])) {
    $userPhoto = $row['photo'];
  } else {
    $userPhoto = 'images/user.jpg';
  }
}
?>
<header class="header">
  <figure><img src="images/shalash_logo.jpg" alt="Shalash Rent Logo" width="70"></figure>
  <h1 class="company-name">Shalash Flat Rentals</h1>
  <nav>
    <a href="about.php" <?php if ($act === 'about') {
                          echo 'class="active"';
                        } ?>>About Us</a>
    <?php if (isset($_SESSION['username'])): ?>
      <a href="profile.php" class="user-card <?php if ($_SESSION['role'] === 'customer') echo 'customer-card';
      elseif ($_SESSION['role'] === 'owner') echo 'owner-card';
      elseif ($_SESSION['role'] === 'manager') echo 'manager-card';
      if ($act === 'profile') echo ' active';?>">
        <img src="<?= htmlspecialchars($userPhoto) ?>" alt="User Photo" width="40">
        <span><?php echo $_SESSION['name']; ?></span>
      </a>
      <?php if ($_SESSION['role'] === 'customer'): ?>
        <a href="basket.php" class="shopping-basket <?php if ($act === 'basket') {  echo 'class=\"active\"';} ?>">🛒 Basket</a>
      <?php endif; ?>
      <a href="logout.php" class="nav-link">Logout</a>
    <?php else: ?>
      <a href="registerPage.php" <?php if ($act === 'register') {
                                    echo 'class="active"';
                                  } ?>>Register</a>
      <a href="login.php" <?php if ($act === 'login') {
                            echo 'class="active"';
                          } ?>>Login</a>
    <?php endif; ?>
  </nav>
</header>