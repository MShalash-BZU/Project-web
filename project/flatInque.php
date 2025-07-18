<?php
session_start();
require_once 'database.inc.php';

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit;
}
$filters = [];
if (isset($_GET['from_date'])) {
    $filters['from_date'] = $_GET['from_date'];
} else {
    $filters['from_date'] = '';
}
if (isset($_GET['to_date'])) {
    $filters['to_date'] = $_GET['to_date'];
} else {
    $filters['to_date'] = '';
}
if (isset($_GET['location'])) {
    $filters['location'] = $_GET['location'];
} else {
    $filters['location'] = '';
}
if (isset($_GET['available_on'])) {
    $filters['available_on'] = $_GET['available_on'];
} else {
    $filters['available_on'] = '';
}
if (isset($_GET['owner_id'])) {
    $filters['owner_id'] = $_GET['owner_id'];
} else {
    $filters['owner_id'] = '';
}
if (isset($_GET['customer_id'])) {
    $filters['customer_id'] = $_GET['customer_id'];
} else {
    $filters['customer_id'] = '';
}
$allowedSort = [
    'flatRefNo' => 'f.flatRefNo',
    'monthlyRent' => 'f.monthlyRent',
    'rentStartDate' => 'r.rentStartDate',
    'rentEndDate' => 'r.rentEndDate',
    'location' => 'f.location',
    'ownerName' => 'o.name',
    'customerName' => 'c.name'
];
$sort = isset($_GET['sort']) && isset($allowedSort[$_GET['sort']]) ? $_GET['sort'] : 'rentStartDate';
$dir = (isset($_GET['dir']) && strtoupper($_GET['dir']) === 'ASC') ? 'ASC' : 'DESC';

$sql = "
SELECT 
  r.rentStartDate,
  r.rentEndDate,
  f.flatRefNo, f.monthlyRent, f.flatId, f.location,
  o.ownerId, o.name AS ownerName, o.address AS ownerCity, o.telephone AS ownerPhone, uo.email AS ownerEmail,
  c.customerId, c.name AS customerName, c.address AS customerCity, c.telephone AS customerPhone, uc.email AS customerEmail
FROM rentals r
JOIN flats f ON r.flatId = f.flatId
JOIN owners o ON f.ownerId = o.ownerId
JOIN users uo ON o.userId = uo.userId
JOIN customers c ON r.customerId = c.customerId
JOIN users uc ON c.userId = uc.userId
WHERE 1=1
";
$params = [];
if ($filters['from_date']) {
    $sql .= " AND r.rentStartDate >= :from_date";
    $params[':from_date'] = $filters['from_date'];
}
if ($filters['to_date']) {
    $sql .= " AND r.rentEndDate <= :to_date";
    $params[':to_date'] = $filters['to_date'];
}
if ($filters['location']) {
    $sql .= " AND f.location LIKE :location";
    $params[':location'] = '%' . $filters['location'] . '%';
}
if ($filters['available_on']) {
    $sql .= " AND f.availableFrom = :available_on";
    $params[':available_on'] = $filters['available_on'];
}
if ($filters['owner_id']) {
    $sql .= " AND o.ownerId = :owner_id";
    $params[':owner_id'] = $filters['owner_id'];
}
if ($filters['customer_id']) {
    $sql .= " AND c.customerId = :customer_id";
    $params[':customer_id'] = $filters['customer_id'];
}
$sql .= " ORDER BY {$allowedSort[$sort]} $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$stmtOwners = $pdo->prepare("SELECT ownerId, name FROM owners ORDER BY name");
$stmtOwners->execute();
$owners = $stmtOwners->fetchAll();

$stmtCustomers = $pdo->prepare("SELECT customerId, name FROM customers ORDER BY name");
$stmtCustomers->execute();
$customers = $stmtCustomers->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flats Inquiry</title>
    <link rel="stylesheet" href="css/style2.css">

</head>

<body>
    <?php include 'header.php'; ?>
    <section class="layout">
        <?php $active = 'inquire';
        include 'nav.php'; ?>

        <main class="main-content">
            <h2>Flats Inquiry</h2>
            <form method="get" style="margin-bottom:20px;">
                <label for="fd">From Date: </label><br>
                <input type="date" id="fd" name="from_date" value="<?= htmlspecialchars($filters['from_date']) ?>"><br><br>
                <label for="td">To Date: </label><br>
                <input type="date" id="td" name="to_date" value="<?= htmlspecialchars($filters['to_date']) ?>"><br><br>
                <label for="avon">Available On: </label><br>
                <input type="date" id="avon" name="available_on" value="<?= htmlspecialchars($filters['available_on']) ?>"><br><br>
                <label for="loc">Location: </label><br>
                <input type="text" id="loc" name="location" value="<?= htmlspecialchars($filters['location']) ?>"><br><br>
                <label for="own">Owner:</label><br>
                <select id="own" name="owner_id">
                    <option value="">All</option>
                    <?php foreach ($owners as $o): ?>
                        <option value="<?= $o['ownerId'] ?>" <?= $filters['owner_id'] == $o['ownerId'] ? 'selected' : ''; ?>><?= htmlspecialchars($o['name']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <label for="cus">Customer:</label><br>
                <select id="cus" name="customer_id">
                    <option value="">All</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['customerId'] ?>" <?= $filters['customer_id'] == $c['customerId'] ? 'selected' : ''; ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select><br>
                <button type="submit">Search</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <?php
                        $columns = [
                            'flatRefNo' => 'Flat Ref',
                            'monthlyRent' => 'Rent',
                            'rentStartDate' => 'Start Date',
                            'rentEndDate' => 'End Date',
                            'location' => 'Location',
                            'ownerName' => 'Owner',
                            'customerName' => 'Customer'
                        ];
                        foreach ($columns as $col => $label) {
                            $icon = '';
                            if ($sort === $col) {
                                if ($dir === 'ASC') {
                                    $icon = '▲';
                                } else {
                                    $icon = '▼';
                                }
                            }
                            $link = 'flatInque.php?sort=' . $col;
                            if ($sort === $col && $dir === 'ASC') {
                                $link .= '&dir=DESC';
                            } else {
                                $link .= '&dir=ASC';
                            }
                            foreach ($filters as $key => $value) {
                                if ($value !== '') {
                                    $link .= '&' . $key . '=' . htmlspecialchars($value);
                                }
                            }
                            echo "<th><a href=\"$link\" style=\"color:#fff;text-decoration:none;\"><span class='sort-icon'>$icon</span>$label</a></th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results): ?>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td>
                                    <a href="flatdetail.php?id=<?= $r['flatId'] ?>" target="_blank" class="flat-link-button">
                                        <?= htmlspecialchars($r['flatRefNo']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($r['monthlyRent']) ?> JD</td>
                                <td><?= htmlspecialchars($r['rentStartDate']) ?></td>
                                <td><?= htmlspecialchars($r['rentEndDate']) ?></td>
                                <td><?= htmlspecialchars($r['location']) ?></td>
                                <td>
                                    <a href="ownerCard.php?id=<?= $r['ownerId'] ?>" target="_blank" class="user-link">
                                        <?= htmlspecialchars($r['ownerName']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="CustomerCard.php?id=<?= $r['customerId'] ?>" target="_blank" class="user-link">
                                        <?= htmlspecialchars($r['customerName']) ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No results found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </section>
    <?php include 'footer.php'; ?>
</body>

</html>