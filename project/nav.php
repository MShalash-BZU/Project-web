<?php if (!isset($active)) {
    $active = ''; 
}?>
<nav>
      <ul id="listings">
        <li><a href="index.php" <?php if($active==='home'){echo 'class="active"';}?>>Home</a></li>
        <li><a href="search.php" <?php if($active==='search'){echo 'class="active"';}?>>Flat Search</a></li>
        <?php if (isset($_SESSION['role'])) : ?>
        <li><a href="viewMessage.php" <?php if($active==='message'){echo 'class="active"';}?>>View Messages</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'owner'): ?>
           <li><a href="offerFlat.php" <?php if($active==='offer'){echo 'class="active"';}?>>Offer a Flat</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
        <li><a href="viewRentalFlat.php" <?php if($active==='view'){echo 'class="active"';}?>>View Rental Flats</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
         <li><a href="flatInque.php" class="nav-item <?php if($active==='inque'){echo 'class="active"';}?>">Flat Inque</a></li>
       <?php endif; ?>
      
      </ul>
</nav>