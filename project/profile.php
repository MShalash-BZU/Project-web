<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE customers SET nationalId = :nid, name = :name, address = :addr, dateOfBirth = :dob, mobile = :mobile, telephone = :tel WHERE userId = :uid");
    $stmt->execute([
        ':nid' => $_POST['nationalId'],
        ':name' => $_POST['name'],
        ':addr' => $_POST['address'],
        ':dob' => $_POST['dateOfBirth'],
        ':mobile' => $_POST['mobile'],
        ':tel' => $_POST['telephone'],
        ':uid' => $userId
    ]);
    if (isset($_FILES['photo']) ) {
        $newName = 'images/' . $userId . '_' . time().'.jpg' ;
        move_uploaded_file($_FILES['photo']['tmp_name'], $newName);
        $stmt = $pdo->prepare("UPDATE customers SET photo = :photo WHERE userId = :uid");
        $stmt->execute([':photo' => $newName, ':uid' => $userId]);
    }
    $message = "Profile updated successfully.";
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE userId = :uid");
$stmt->execute([':uid' => $userId]);
$customer = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="css/style2.css">
</head>

<body>

    <?php
    $act = 'profile';
    include 'header.php'; ?>

    <section class="layout">
        <?php include 'nav.php'; ?>

        <main class="main-content">
            <h2 style="text-align:center;">Customer Profile</h2>
            
            <section class="profile-header">
                <figure>
                    <img  src="<?= htmlspecialchars($customer['photo']) ?>" alt="customer photo">
                    <figcaption><?= htmlspecialchars($customer['name']) ?></figcaption>
                </figure>
                
            </section>
            
            <?php if (isset($message)): ?>
                <p class="message"><?= $message ?></p>
            <?php endif; ?>

            <form method="POST" class="profile-form" enctype="multipart/form-data">
                <label for="custid">Customer ID:</label><br>
                <input type="text" id="cusid" value="<?= htmlspecialchars($customer['customerId']) ?>" readonly><br><br>
                <label for="natid" class="required">National ID:</label><br>
                <input type="text" id="natid" name="nationalId" value="<?= htmlspecialchars($customer['nationalId']) ?>" required><br><br>
                <label for="name" class="required">Name:</label><br>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required><br><br>
                <label for="addr">Address:</label><br>
                <input type="text" id="addr" name="address" value="<?= htmlspecialchars($customer['address']) ?>"><br><br>
                <label for="dob" class="required">Date of Birth:</label><br>
                <input type="date" id="dob" name="dateOfBirth" value="<?= htmlspecialchars($customer['dateOfBirth']) ?>" required><br><br>
                <label for="mo" class="required">Mobile:</label><br>
                <input type="text" id="mo" name="mobile" value="<?= htmlspecialchars($customer['mobile']) ?>" required><br><br>
                <label for="tel">Telephone:</label><br>
                <input type="text" id="tel" name="telephone" value="<?= htmlspecialchars($customer['telephone']) ?>"><br><br>
                <label for="photo">Profile Photo:</label><br>
                <input type="file" id="photo" name="photo" accept="image/*"><br>

                <button type="submit">Update</button>
            </form>
        </main>
    </section>

    <?php include 'footer.php'; ?>

</body>

</html>