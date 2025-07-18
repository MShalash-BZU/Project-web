<?php
session_start();

$errors = [];
$OWN = [
    'nationalId' => '',
    'name' => '',
    'addressFlat' => '',
    'addressStreet' => '',
    'addressCity' => '',
    'addressPostal' => '',
    'dob' => '',
    'email' => '',
    'mobile' => '',
    'telephone' => '',
    'bankName' => '',
    'bankBranch' => '',
    'accountNumber' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $OWN['nationalId'] = trim($_POST['nationalId']);
    $OWN['name'] = trim($_POST['name']);
    $OWN['addressFlat'] = trim($_POST['addressFlat']);
    $OWN['addressStreet'] = trim($_POST['addressStreet']);
    $OWN['addressCity'] = trim($_POST['addressCity']);
    $OWN['addressPostal'] = trim($_POST['addressPostal']);
    $OWN['dob'] = trim($_POST['dob']);
    $OWN['email'] = trim($_POST['email']);
    $OWN['mobile'] = trim($_POST['mobile']);
    $OWN['telephone'] = trim($_POST['telephone']);
    $OWN['bankName'] = trim($_POST['bankName']);
    $OWN['bankBranch'] = trim($_POST['bankBranch']);
    $OWN['accountNumber'] = trim($_POST['accountNumber']);
    $_SESSION['owner_reg_step1'] = $OWN;
    header("Location: own_step2.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Owner Register Step-1</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
            <h2>Owner Registration - Step 1</h2>
    <form method="POST" >
        <label for="nat" class="required">National ID Number:</label><br>
        <input type="number" id="nat" name="nationalId" required /><br>
        <br>
        <label for="name" class="required">Full Name:</label><br>
        <input type="text" id="name" pattern="[a-zA-Z\s]+" name="name"  required /><br>
        <br>
        <fieldset><br><br>
            <legend>Address</legend>
            <label for="addr">Flat / House Number:</label><br>
            <input type="text" id="addr" name="addressFlat" />
            <br>
            <label for="addrS">Street Name:</label><br>
            <input type="text" id="addrS" name="addressStreet" />
            <br>
            <label for="addressCity">City:</label><br>
            <input type="text" id="addressCity" name="addressCity" />
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
        <input type="number" id="mobile" name="mobile" required />
        <br><br>
        <label for="telephone">Telephone Number:</label><br>
        <input type="number" id="telephone" name="telephone" />
        <br><br>
        <fieldset><br><br>
            <legend>Bank details</legend>
            <label for="bname">Bank name:</label><br>
            <input type="text" id="addr" name="bankName" />
            <br><br>
            <label for="bbranch"> Bank branch:</label><br>
            <input type="text" id="bbranch" name="bankBranch" />
            <br><br>
            <label for="accn">Account number:</label><br>
            <input type="number" id="accn" name="accountNumber" />
            <br><br>
        </fieldset><br>
        <br>
        <button type="submit">Next Step</button>
    </form>
    </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>