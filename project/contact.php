<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $subject = trim($_POST['subject']);
  $message = trim($_POST['message']);
    $mail = new PHPMailer(true);
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'hmodshalasg33@gmail.com';
      $mail->Password = 'ozfg bnqx kjxp dqxp';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('hmodshalasg33@gmail.com', 'Shalash Contact Form');
      $mail->addReplyTo($email, $name);
      $mail->addAddress('shalashrentals@gmail.com', 'Shalash Flat Rent');

      $mail->Subject = $subject;
      $mail->Body    = "From: $name <$email>\n\n$message";

      $mail->send();
      $success = "Message sent successfully.";
      $_POST = [];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/style2.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <section class="layout">
        <?php include 'nav.php'; ?>
        <main class="main-content">
            <h2 style="text-align:center">Contact Us</h2>
            <?php if (!empty($success)): ?>
                <p class="message-success"><?= $success ?></p>
            <?php endif; ?>
            <form method="POST" class="contact-form">
                <label for="name" class="required">Name:</label><br>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($name ?? '') ?>" required><br><br>
                <label for="email" class="required">Email:</label><br>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>
                <label for="sub" class="required">Subject:</label><br>
                <input type="text" id="sub" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>" required><br><br>
                <label>Message:</label><br>
                <textarea name="message" rows="5"><?= htmlspecialchars($message ?? '') ?></textarea><br>
                <button type="submit">Send Message</button>
            </form>
        </main>
    </section>

    <?php include 'footer.php'; ?>

</body>

</html>