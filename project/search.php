<?php
session_start();
include_once 'database.inc.php';
$defaultSort = 'flatRefNo';
$defaultDir = 'ASC';

if (!isset($pdo) || !$pdo) {
    die('Database connection not established.');
}

if (isset($_GET['sort'])) {
  $sort = $_GET['sort'];
  setcookie('flat_sort', $sort, time() + 3600, '/');
} elseif (isset($_COOKIE['flat_sort'])) {
  $sort = $_COOKIE['flat_sort'];
} else {
  $sort = $defaultSort;
}

if (isset($_GET['dir'])) {
  if ($_GET['dir'] === 'DESC') {
    $dir = 'DESC';
  } else {
    $dir = 'ASC';
  }
  setcookie('flat_dir', $dir, time() + 3600, '/');
} elseif (isset($_COOKIE['flat_dir'])) {
  $dir = $_COOKIE['flat_dir'];
} else {
  $dir = $defaultDir;
}
$SER = [
  'location' => '',
  'minPrice' => '',
  'maxPrice' => '',
  'bedrooms' => '',
  'bathrooms' => '',
  'furnished' => ''
];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['location'])) {
    $SER['location'] = $_POST['location'];
  } else {
    $SER['location'] = '';
  }
  if (isset($_POST['minPrice'])) {
    $SER['minPrice'] = $_POST['minPrice'];
  } else {
    $SER['minPrice'] = '';
  }
  if (isset($_POST['maxPrice'])) {
    $SER['maxPrice'] = $_POST['maxPrice'];
  } else {
    $SER['maxPrice'] = '';
  }
  if (isset($_POST['bedrooms'])) {
    $SER['bedrooms'] = $_POST['bedrooms'];
  } else {
    $SER['bedrooms'] = '';
  }
  if (isset($_POST['bathrooms'])) {
    $SER['bathrooms'] = $_POST['bathrooms'];
  } else {
    $SER['bathrooms'] = '';
  }
  if (isset($_POST['furnished'])) {
    $SER['furnished'] = $_POST['furnished'];
  } else {
    $SER['furnished'] = '';
  }
}
$sql = "
SELECT f.*, MIN(p.photoUrl) AS photoUrl FROM flats f 
LEFT JOIN flat_photos p ON f.flatId = p.flatId 
WHERE f.approved = 'approved'
AND f.flatId NOT IN (SELECT flatId FROM rentals WHERE NOW() BETWEEN rentStartDate AND rentEndDate)";
$conditions = [];
$params = [];

if (!empty($SER['location'])) {
  $conditions[] = "f.location LIKE :location";
  $params[':location'] = '%' . $SER['location'] . '%';
}
if (!empty($SER['minPrice'])) {
  $conditions[] = "f.monthlyRent >= :minPrice";
  $params[':minPrice'] = $SER['minPrice'];
}
if (!empty($SER['maxPrice'])) {
  $conditions[] = "f.monthlyRent <= :maxPrice";
  $params[':maxPrice'] = $SER['maxPrice'];
}
if (!empty($SER['bedrooms'])) {
  $conditions[] = "f.bedrooms = :bedrooms";
  $params[':bedrooms'] = $SER['bedrooms'];
}
if (!empty($SER['bathrooms'])) {
  $conditions[] = "f.bathrooms = :bathrooms";
  $params[':bathrooms'] = $SER['bathrooms'];
}
if ($SER['furnished'] !== '') {
  $conditions[] = "f.furnished = :furnished";
  $params[':furnished'] = $SER['furnished'];
}

if ($conditions) {
  $sql .= " AND " . implode(' AND ', $conditions);
}

$allowedSort = array(
  'flatRefNo' => 'f.flatRefNo',
  'monthlyRent' => 'f.monthlyRent',
  'availableFrom' => 'f.availableFrom',
  'location' => 'f.location',
  'bedrooms' => 'f.bedrooms'
);
if (isset($allowedSort[$sort])) {
  $orderBy = $allowedSort[$sort];
} else {
  $orderBy = $allowedSort[$defaultSort];
}
$sql .= " GROUP BY f.flatId ORDER BY $orderBy $dir";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$RES = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Search Flats</title>
  <link rel="stylesheet" href="css/style2.css">
</head>

<body>
  <?php include 'header.php'; ?>
  <section class="layout">
    <?php $active = 'search';
    include 'nav.php'; ?>
    <main class="main-content">
      <section class="search-grid">
        <section class="search-form-section">
          <h2>Search for Available Flats</h2>
          <form method="POST" action="search.php">
            <label for="loc">Location: </label><br>
            <input type="text" id="loc" name="location" class="search_input"><br><br>
            <label for="min">Min Price: </label><br>
            <input type="number" id="min" name="minPrice" class="search_input"><br><br>
            <label for="max">Max Price: </label><br>
            <input type="number" id="max" name="maxPrice" class="search_input"><br><br>
            <label for="bed">Bedrooms: </label><br>
            <input type="number" id="bed" name="bedrooms" class="search_input"><br><br>
            <label for="bath">Bathrooms: </label><br>
            <input type="number" id="bath" name="bathrooms" class="search_input"><br><br>
            <label>Furnished:</label><br>
            <select name="furnished" class="search_input">
              <option value="">Any</option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select><br>
            <button type="submit">Search</button>
          </form>
        </section>

        <section class="search-results-section">
          <h2>Search Results</h2>
          <table>
            <thead>
              <tr>
                <?php
                $columns = [
                  'flatRefNo' => 'Flat Ref',
                  'monthlyRent' => 'Rent',
                  'availableFrom' => 'Available',
                  'location' => 'Location',
                  'bedrooms' => 'Bedrooms',
                  'photo' => 'Photo'
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
                  if ($col === 'photo') {
                    echo "<th>$label</th>";
                  } else {
                    $link = 'search.php?sort=' . $col;
                    if ($sort === $col && $dir === 'ASC') {
                      $link .= '&dir=DESC';
                    } else {
                      $link .= '&dir=ASC';
                    }
                    echo "<th><a href=\"$link\" ><span class='sort-icon'>$icon</span>$label</a></th>";
                  }
                }
                ?>
              </tr>
            </thead>
            <tbody>
              <?php if ($RES): ?>
                <?php foreach ($RES as $flat): ?>
                  <tr>
                    <td><?= htmlspecialchars($flat['flatRefNo']) ?></td>
                    <td><?= htmlspecialchars($flat['monthlyRent']) ?> JD</td>
                    <td><?= htmlspecialchars($flat['availableFrom']) ?></td>
                    <td><?= htmlspecialchars($flat['location']) ?></td>
                    <td><?= htmlspecialchars($flat['bedrooms']) ?></td>
                    <td>
                      <?php if ($flat['photoUrl']): ?>
                        <a href="flatdetail.php?id=<?= $flat['flatId'] ?>" target="_blank">
                          <img src="<?= htmlspecialchars($flat['photoUrl']) ?>" width="80" alt="Flat Photo">
                        </a>
                      <?php else: ?>
                        <em>No Photo</em>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No flats match your search.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </section>
    </main>
  </section>
  <?php include 'footer.php'; ?>
</body>

</html>