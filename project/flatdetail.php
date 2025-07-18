<?php
session_start();
require_once 'database.inc.php';


if (isset($_GET['id'])) {
  $flatId = $_GET['id'];
} else {
  $flatId = null;
}
if (!$flatId) {
  die("Invalid request");
}
$stmt = $pdo->prepare("SELECT f.*, o.name AS ownerName, o.mobile AS ownerMobile
  FROM flats f JOIN owners o ON f.ownerId = o.ownerId WHERE f.flatId = :id AND f.approved = 'approved'");
$stmt->execute([':id' => $flatId]);
$flat = $stmt->fetch();
if (!$flat) die("Flat not found or not approved.");

$photos = $pdo->prepare("SELECT photoUrl FROM flat_photos WHERE flatId = :id");
$photos->execute([':id' => $flatId]);
$images = $photos->fetchAll();



$marketing = $pdo->prepare("SELECT title, description, url FROM marketing_info WHERE flatId = :id");
$marketing->execute([':id' => $flatId]);
$marketingInfo = $marketing->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Flat Details</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php include 'nav.php'; ?>

    <main class="main-content">
      <section class="flatcard">

        <section class="photos">
          <?php foreach ($images as $img): ?>
            <figure>
              <img src="<?= $img['photoUrl'] ?>" alt="Flat Photo" width="300">
            </figure>
          <?php endforeach; ?>
        </section>

        <section class="description">
          <h2>Flat Ref #: <?= htmlspecialchars($flat['flatRefNo']) ?></h2>

          <table>
            <tr>
              <th>Address</th>
              <td><?= htmlspecialchars($flat['address']) ?></td>
            </tr>
            <tr>
              <th>Location</th>
              <td><?= htmlspecialchars($flat['location']) ?></td>
            </tr>
            <tr>
              <th>Price</th>
              <td><?= htmlspecialchars($flat['monthlyRent']) ?> JD/month</td>
            </tr>
            <tr>
              <th>Conditions</th>
              <td><?= htmlspecialchars($flat['rentConditions']) ?></td>
            </tr>
            <tr>
              <th>Bedrooms</th>
              <td><?= $flat['bedrooms'] ?></td>
            </tr>
            <tr>
              <th>Bathrooms</th>
              <td><?= $flat['bathrooms'] ?></td>
            </tr>
            <tr>
              <th>Size</th>
              <td><?= $flat['sizeSqm'] ?> m²</td>
            </tr>
            <tr>
              <th>Heating</th>
              <td><?= $flat['heating'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Air Conditioning</th>
              <td><?= $flat['airConditioning'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Access Control</th>
              <td><?= $flat['accessControl'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Parking</th>
              <td><?= $flat['parking'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Backyard</th>
              <td><?= $flat['backyard'] ?></td>
            </tr>
            <tr>
              <th>Playground</th>
              <td><?= $flat['playground'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Storage</th>
              <td><?= $flat['storage'] ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
              <th>Owner</th>
              <td><?= $flat['ownerName'] ?> | 📞 <?= $flat['ownerMobile'] ?></td>
            </tr>
          </table>
        </section>
      </section>
      <aside class="marketing">
        <h3>Nearby Places</h3>
        <?php if ($marketingInfo): ?>
          <ul>
            <?php foreach ($marketingInfo as $m): ?>
              <li>
                <strong><?= htmlspecialchars($m['title']) ?></strong><br>
                <em><?= htmlspecialchars($m['description']) ?></em><br>
                <?php if ($m['url']): ?>
                  <a href="<?= htmlspecialchars($m['url']) ?>" target="_blank">More Info</a>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No marketing info available.</p>
        <?php endif; ?>
      </aside>
      <section class="side-links">
        <a href="prevRequest.php?flatId=<?= $flatId ?>">Request Flat Viewing Appointment</a>
        <a href="rentFlat.php?id=<?= $flatId ?>">Rent the Flat</a>
      </section>
    </main>
  </section>

  <?php include 'footer.php'; ?>
</body>

</html>