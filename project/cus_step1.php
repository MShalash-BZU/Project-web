<?php
session_start();

$CUS = [
    'nationalId' => '',
    'name' => '',
    'addressFlat' => '',
    'addressStreet' => '',
    'addressCity' => '',
    'addressPostal' => '',
    'dob' => '',
    'email' => '',
    'mobile' => '',
    'telephone' => ''
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $CUS['nationalId'] = trim($_POST['nationalId']);
    $CUS['name'] = trim($_POST['name']);
    $CUS['addressFlat'] = trim($_POST['addressFlat']);
    $CUS['addressStreet'] = trim($_POST['addressStreet']);
    $CUS['addressCity'] = trim($_POST['addressCity']);
    $CUS['addressPostal'] = trim($_POST['addressPostal']);
    $CUS['dob'] = trim($_POST['dob']);
    $CUS['email'] = trim($_POST['email']);
    $CUS['mobile'] = trim($_POST['mobile']);
    $CUS['telephone'] = trim($_POST['telephone']);
    $_SESSION['customer_reg_step1'] = $CUS;
    header('Location: cus_step2.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>customer Register Step-1</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
    <h2>Customer Registration - Step 1</h2>
    <form method="POST" action="">
        <label for="nat" class="required">National ID Number:</label><br>
        <input type="number" id="nat" name="nationalId" required />
        <br><br>
        <label for="name" class="required">Full Name:</label><br>
        <input type="text" id="name" pattern="[a-zA-Z\s]+" name="name" required />
        <br><br>
        <fieldset><br><br>
            <legend><h2><strong>Address</strong></h2></legend>
            <label for="addr">Flat / House Number:</label><br>
            <input type="text" id="addr" name="addressFlat" />
            <br><br>
            <label for="addrS">Street Name:</label><br>
            <input type="text" id="addrS" name="addressStreet" />
            <br><br>
            <label for="addressCity">City:</label><br>
            <input type="text" id="addressCity" name="addressCity" />
            <br>
            <br>
            <label for="addressPostal">Postal Code:</label><br>
            <input type="text" id="addressPostal" name="addressPostal" /><br><br>
        </fieldset>
        <br>
        <label for="dAT" class="required">Date of Birth:</label><br>
        <input type="date" id="dAT" name="dob" required />
        <br><br>
        <label for="email" class="required">E-mail Address:</label><br>
        <input type="email" id="email" name="email" required />
        <br><br>
        <label for="mobile" class="required">Mobile Number:</label><br>
        <input type="text" id="mobile" name="mobile" required />
        <br><br>
        <label for="telephone">Telephone Number:</label><br>
        <input type="text" id="telephone" name="telephone" /><br>

        <button type="submit">Next Step</button>
    </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>
