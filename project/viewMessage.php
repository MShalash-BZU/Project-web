<?php
session_start();
require_once('database.inc.php');

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];
$sort = 'messageDate';
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'messageDate' || $_GET['sort'] == 'messageTitle' || $_GET['sort'] == 'senderEmail') {
        $sort = $_GET['sort'];
    }
}
$dir = 'DESC';
if (isset($_GET['dir'])) {
    if ($_GET['dir'] == 'ASC') {
        $dir = 'ASC';
    }
}

$sql = "
  SELECT m.*, u.email AS senderEmail, 
         IFNULL(c.name, IFNULL(o.name, 'Manager')) AS senderName
  FROM messages m
  JOIN users u ON m.senderId = u.userId
  LEFT JOIN customers c ON u.userId = c.userId
  LEFT JOIN owners o ON u.userId = o.userId
  WHERE m.receiverId = :uid
  ORDER BY $sort $dir
";

$stmt = $pdo->prepare($sql);
$stmt->execute(array(':uid' => $userId));
$messages = $stmt->fetchAll();



if ($_SESSION['role'] === 'manager') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $flatId = $_POST['flatId'];
        if (isset($_POST['action']) && $_POST['action'] === 'reject') {
            $stmt = $pdo->prepare("UPDATE flats SET approved = 'rejected' WHERE flatId = :id");
            $stmt->execute([':id' => $flatId]);
            $stmtOwner = $pdo->prepare("SELECT o.userId, o.name FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id");
            $stmtOwner->execute([':id' => $flatId]);
            $owner = $stmtOwner->fetch();

            $stmtMsgOwner = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
            $stmtMsgOwner->execute([
                $_SESSION['userId'],
                $owner['userId'],
                "Flat Rejected",
                "Your flat submission has been rejected by the manager."
            ]);

            echo "<p style='color:red;'>Flat ID $flatId has been rejected.</p>";
        } else {
            $flatIdStr = (string)$flatId;
            $refNo = str_repeat('0', 6 - strlen($flatIdStr)) . $flatIdStr;

            $stmt = $pdo->prepare("UPDATE flats SET approved = 'approved', flatRefNo = :ref WHERE flatId = :id");
            $stmt->execute([':ref' => $refNo, ':id' => $flatId]);

            $stmtOwner = $pdo->prepare("SELECT o.userId, o.name FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id");
            $stmtOwner->execute([':id' => $flatId]);
            $owner = $stmtOwner->fetch();

            $stmtMsgOwner = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
            $stmtMsgOwner->execute([
                $_SESSION['userId'],
                $owner['userId'],
                "Flat Approved",
                "Your flat (Ref: $refNo) has been approved by the manager and is now listed."
            ]);

            $stmtMsgManager = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
            $stmtMsgManager->execute([
                $_SESSION['userId'],
                $_SESSION['userId'],
                "Flat Approval Confirmed",
                "You have approved flat (Ref: $refNo) for owner: {$owner['name']}."
            ]);

            echo "<p style='color:green;'>Flat ID $flatId approved with reference number: $refNo</p>";
        }
    }
    $stmt = $pdo->query("SELECT f.flatId, f.location, f.address, f.monthlyRent, o.name AS ownerName FROM flats f
     JOIN owners o ON f.ownerId = o.ownerId WHERE f.approved = 'pending'");
    $flats = $stmt->fetchAll();
}

if ($_SESSION['role'] === 'owner') {
    $stmtOwner = $pdo->prepare("SELECT ownerId FROM owners WHERE userId = :uid");
    $stmtOwner->execute([':uid' => $_SESSION['userId']]);
    $rowOwner = $stmtOwner->fetch();
    if ($rowOwner) {
        $ownerId = $rowOwner['ownerId'];
    } else {
        $ownerId = null;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointmentId = (int)$_POST['appointmentId'];
        $action = $_POST['actionapp'] === 'accept' ? 'accepted' : 'rejected';

        $stmt = $pdo->prepare("UPDATE appointments SET status = :status WHERE appointmentId = :aid AND ownerId = :oid");
        $stmt->execute([':status' => $action, ':aid' => $appointmentId, ':oid' => $ownerId]);

        $stmtApp = $pdo->prepare("SELECT a.*, c.userId AS customerUserId, f.flatRefNo, f.address, f.location, o.name AS ownerName, o.mobile AS ownerMobile
            FROM appointments a
            JOIN customers c ON a.customerId = c.customerId
            JOIN flats f ON a.flatId = f.flatId
            JOIN owners o ON f.ownerId = o.ownerId
            WHERE a.appointmentId = :aid");
        $stmtApp->execute([':aid' => $appointmentId]);
        $app = $stmtApp->fetch();

        if ($app) {
            if ($action === 'accepted') {
                $msgTitle = "Preview Appointment Accepted";
                $msgBody = "Your request to preview flat (Ref: {$app['flatRefNo']}) at {$app['appointmentDate']} has been accepted. "
                    . "Owner contact: {$app['ownerName']} ({$app['ownerMobile']}). Address: {$app['address']}, {$app['location']}.";
            } else {
                $msgTitle = "Preview Appointment Rejected";
                $msgBody = "Your request to preview flat (Ref: {$app['flatRefNo']}) at {$app['appointmentDate']} has been rejected.";
            }
            $stmtMsg = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
            $stmtMsg->execute([
                $_SESSION['userId'],
                $app['customerUserId'],
                $msgTitle,
                $msgBody
            ]);
            $stmtMsgManager = $pdo->prepare("INSERT INTO messages (senderId, receiverId, messageTitle, messageBody) VALUES (?, ?, ?, ?)");
            $stmtMsgManager->execute([
                $_SESSION['userId'],
                1,
                "Owner handled preview appointment",
                "Owner {$app['ownerName']} has {$action} a preview appointment for flat (Ref: {$app['flatRefNo']}) at {$app['appointmentDate']} for customer ID {$app['customerId']}."
            ]);
        }
    }

    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS customerName, c.mobile AS customerMobile, f.flatRefNo, f.address, f.location
        FROM appointments a
        JOIN flats f ON a.flatId = f.flatId
        LEFT JOIN customers c ON a.customerId = c.customerId
        WHERE a.ownerId = :oid AND a.customerId IS NOT NULL AND a.status = 'pending'
        ORDER BY a.appointmentDate ASC
    ");
    $stmt->execute([':oid' => $ownerId]);
    $appointments = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Message</title>
    <link rel="stylesheet" href="css/style2.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <section class="layout">
        <?php $active = 'message';
        include 'nav.php'; ?>

        <main class="main-content">
            <h2>Messages</h2>
            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="?sort=messageDate&dir=<?php echo ($sort == 'messageDate' && $dir == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                Date<?php if ($sort == 'messageDate') {
                                        if ($dir == 'ASC') {
                                            echo ' ▲';
                                        } else {
                                            echo ' ▼';
                                        }
                                    } ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=messageTitle&dir=<?php echo ($sort == 'messageTitle' && $dir == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                Title<?php if ($sort == 'messageTitle') {
                                            if ($dir == 'ASC') {
                                                echo ' ▲';
                                            } else {
                                                echo ' ▼';
                                            }
                                        } ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=senderEmail&dir=<?php echo ($sort == 'senderEmail' && $dir == 'ASC') ? 'DESC' : 'ASC'; ?>">
                                Sender<?php if ($sort == 'senderEmail') {
                                            if ($dir == 'ASC') {
                                                echo ' ▲';
                                            } else {
                                                echo ' ▼';
                                            }
                                        } ?>
                            </a>
                        </th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($messages && !empty($messages)) {
                        foreach ($messages as $msg) {
                            $row_class = '';
                            $title_class = '';
                            if (isset($msg['isRead']) && $msg['isRead'] == 0) {
                                $row_class = 'unread';
                                $title_class = 'icon-unread';
                            }
                            echo '<tr class="' . $row_class . '">';
                            echo '<td>' .  htmlspecialchars($msg['messageDate']) . '</td>';
                            echo '<td class="' . $title_class . '">' . htmlspecialchars($msg['messageTitle']) . '</td>';
                            echo '<td>';
                            if (!empty($msg['senderName'])) {
                                echo htmlspecialchars($msg['senderName']) . ' - ';
                            }
                            echo htmlspecialchars($msg['senderEmail']);
                            echo '</td>';
                            echo '<td>' . htmlspecialchars($msg['messageBody']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No messages found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php if ($_SESSION['role'] === 'manager'): ?>
                <h2>Flats Awaiting Approval</h2>

                <?php if (empty($flats)): ?>
                    <p>No flats awaiting approval.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Flat ID</th>
                                <th>Location</th>
                                <th>Address</th>
                                <th>Rent (JD)</th>
                                <th>Owner</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($flats as $flat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($flat['flatId']) ?></td>
                                    <td><?= htmlspecialchars($flat['location']) ?></td>
                                    <td><?= htmlspecialchars($flat['address']) ?></td>
                                    <td><?= htmlspecialchars($flat['monthlyRent']) ?></td>
                                    <td><?= htmlspecialchars($flat['ownerName']) ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="flatId" value="<?= $flat['flatId'] ?>">
                                            <button type="submit" name="action" value="approve">Approve</button>
                                            <button type="submit" name="action" value="reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($_SESSION['role'] === 'owner'): ?>
                <h2>Preview Appointment Requests</h2>
                <?php if ($appointments): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Flat Ref</th>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Customer Mobile</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $app): ?>
                                <tr>
                                    <td><?= htmlspecialchars($app['flatRefNo']) ?></td>
                                    <td><?= htmlspecialchars($app['appointmentDate']) ?></td>
                                    <td><?= htmlspecialchars($app['customerName']) ?></td>
                                    <td><?= htmlspecialchars($app['customerMobile']) ?></td>
                                    <td><?= htmlspecialchars($app['address']) ?>, <?= htmlspecialchars($app['location']) ?></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="appointmentId" value="<?= $app['appointmentId'] ?>">
                                            <button type="submit" name="actionapp" value="accept">Accept</button>
                                            <button type="submit" name="actionapp" value="reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No pending appointment requests.</p>
                <?php endif; ?>
            <?php endif; ?>

        </main>
    </section>

    <?php include 'footer.php'; ?>

</body>

</html>