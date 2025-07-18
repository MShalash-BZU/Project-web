<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare("SELECT ownerId FROM owners WHERE userId = :uid");
  $stmt->execute([':uid' => $_SESSION['userId']]);
  $ownerId = $stmt->fetchColumn();
  if (count($_FILES['photos']['tmp_name']) < 3) {
    echo "Please upload at least 3 photos.";
    exit;
  }
  $stmt = $pdo->prepare("INSERT INTO flats (ownerId, location, address, monthlyRent, availableFrom, bedrooms, bathrooms, rentConditions,
   sizeSqm, heating, airConditioning, accessControl, parking, backyard, playground, storage, furnished, approved) 
    VALUES (:oid, :loc, :addr, :rent, :avail, :bed, :bath, :cond, :size, :heat, :ac, :access, :park, :yard, :play, :store, :fur, 'pending')");

  $stmt->execute([
    ':oid' => $ownerId,
    ':loc' => $_POST['location'],
    ':addr' => $_POST['address'],
    ':rent' => $_POST['rent'],
    ':avail' => $_POST['availableFrom'],
    ':bed' => $_POST['bedrooms'],
    ':bath' => $_POST['bathrooms'],
    ':cond' => $_POST['rentConditions'],
    ':size' => $_POST['size'],
    ':yard' => $_POST['backyard'],
    ':fur' => $_POST['furnished'],
    ':heat' => isset($_POST['heating']) ? 1 : 0,
    ':ac' => isset($_POST['airConditioning']) ? 1 : 0,
    ':access' => isset($_POST['accessControl']) ? 1 : 0,
    ':park' => isset($_POST['parking']) ? 1 : 0,
    ':play' => isset($_POST['playground']) ? 1 : 0,
    ':store' => isset($_POST['storage']) ? 1 : 0
  ]);
  $flatId = $pdo->lastInsertId();

  foreach ($_FILES['photos']['tmp_name'] as $i => $tmp_name) {
    $photo_name = basename($_FILES['photos']['name'][$i]);
    $target = "images/" . $photo_name;
    move_uploaded_file($tmp_name, $target);

    $pdo->prepare("INSERT INTO flat_photos (flatId, photoUrl) VALUES (:flatId, :url)")
      ->execute([':flatId' => $flatId, ':url' => $target]);
  }
  if (!empty($_POST['marketingTitle'])) {
    $pdo->prepare("INSERT INTO marketing_info (flatId, title, description, url)VALUES (:flatId, :title, :description, :url)")->execute([
      ':flatId' => $flatId,
      ':title' => $_POST['marketingTitle'],
      ':description' => $_POST['marketingDesc'],
      ':url' => $_POST['marketingUrl']
    ]);
  }

  if (!empty($_POST['appointmentDate'])) {
  foreach ($_POST['appointmentDate'] as $i => $date) {
    $time = trim($_POST['appointmentTime'][$i]);
    $phone = trim($_POST['appointmentPhone'][$i]);
    if ($date && $time && $phone) {
      $pdo->prepare("INSERT INTO appointments (flatId, ownerId, appointmentDate, status, phone) 
        VALUES (:flatId, :ownerId, :date, 'pending', :phone)")
        ->execute([
          ':flatId' => $flatId,
          ':ownerId' => $ownerId,
          ':date' => $date . ' ' . $time,
          ':phone' => $phone
--         ]);
    }
  }
}
  $ownerUserId = $_SESSION['userId'];
  $stmtMsg = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
  $stmtMsg->execute([
    $ownerUserId,
    1,
    "New Flat Pending Approval",
    "A new flat has been added and requires your approval."
  ]);

  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Offer Flat</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>
  <?php include 'header.php'; ?>

  <section class="layout">
    <?php $active = 'offer';
    include 'nav.php'; ?>


    <main class="main-content">
      <h2>Offer Flat for Rent</h2>
      <form method="post" enctype="multipart/form-data">
        <label for="loc" class="required">Location:</label><br>
        <input type="text" id="loc" name="location" required><br><br>
        <label for="addr" class="required">Address:</label><br>
        <input type="text" id="addr" name="address" required><br><br>
        <label for="mon" class="required">Monthly Rent:</label><br>
        <input type="number" id="mon" name="rent" required><br><br>
        <label for="av" class="required">Available From:</label><br>
        <input type="date" id="av" name="availableFrom" required><br><br>
        <label for="bed" class="required">Bedrooms:</label><br>
        <input type="number" id="bed" name="bedrooms" required><br><br>
        <label for="bath" class="required">Bathrooms:</label><br>
        <input type="number" id="bath" name="bathrooms" required><br><br>
        <label for="size" class="required">Size (sqm):</label><br>
        <input type="number" id="size" name="size" required><br><br>
        <label for="rent">Rent Conditions:</label><br>
        <input type="text" id="rent" name="rentConditions"><br><br>
        <input type="checkbox" name="heating"><strong>Heating</strong> <br>
        <input type="checkbox" name="airConditioning"><strong>Air Conditioning</strong> <br>
        <input type="checkbox" name="accessControl"><strong>Access Control</strong> <br>
        <input type="checkbox" name="parking"><strong>Parking</strong> <br>
        <input type="checkbox" name="playground"><strong>Playground</strong> <br>
        <input type="checkbox" name="storage"><strong>Storage</strong> <br><br>
        <label>Backyard:</label><br>
        <select name="backyard">
          <option value="none">None</option>
          <option value="individual">Individual</option>
          <option value="shared">Shared</option>
        </select><br><br>
        <label for="ph" class="required">Upload Photos (min 3):</label><br>
        <input type="file" id="ph" name="photos[]" accept="image/*" multiple required><br><br>
        <label class="required">Furnished:</label><br>
        <select name="furnished" required>
        <option value="1">Yes</option>
         <option value="0">No</option>
        </select><br><br>
        <h2>Marketing Info (optional)</h2>
        <label>Title:</label><br>
        <input type="text" name="marketingTitle"><br><br>
        <label>Description: </label><br>
        <textarea name="marketingDesc"></textarea><br><br>
        <label>URL:</label><br>
        <input type="url" name="marketingUrl"><br><br>
        <h2>Preview Appointment</h2>
        <?php for ($i = 0; $i < 3; $i++): ?>
          <fieldset style="margin-bottom:10px;"><br><br>
            <legend>Appointment <?= $i + 1 ?></legend>
            <label>Date:</label><br>
            <input type="date" name="appointmentDate[]"><br><br>
            <label>Time:</label><br>
            <input type="time" name="appointmentTime[]"><br><br>
            <label>Phone:</label><br>
            <input type="text" name="appointmentPhone[]"><br><br>
          </fieldset>
        <?php endfor; ?>
        <button type="submit">Submit Flat</button>
      </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>

</html>