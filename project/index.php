<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shalash Flat Rent</title>
  <link rel="stylesheet" href="css/style2.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="layout">
    <?php $active= 'home';
    include 'nav.php'; ?>
      <main class="main-content">
    <h1>Student Information</h1>
    <ul>
      <li><strong>Name:</strong> Mohamad Shalash</li>
      <li><strong>Student ID:</strong> 1220920</li>
    </ul>
    <hr>
    <h2>Main Page</h2>
    <a href="search.php" class="flat-link-button">Go to Main Page</a>
    <hr>
    <h2>Database Information</h2>
    <ul>
      <li><strong>Database Name:</strong> <code>web1220920_shalashFlatRental.sql</code></li>
      <li><strong>Database User:</strong> <code>web1220920_mohamadShalash</code></li>
      <li><strong>Database Password:</strong> <code>hamada132</code></li>
    </ul>
    <hr>
    <h2>Test Users</h2>
    <table >
      <thead>
        <tr>
          <th>Role</th>
          <th>Email (Username)</th>
          <th>Password</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Manager</td>
          <td>manager@shalash.com</td>
          <td>132hamada</td>
        </tr>
        <tr>
          <td>Customer</td>
          <td>customer@shalash.com</td>
          <td>132customer</td>
        </tr>
        <tr>
          <td>Owner</td>
          <td>owner@shalash.com</td>
          <td>132owner</td>
        </tr>
      </tbody>
    </table>
    <hr>
  </main>
  </section>

  <?php include 'footer.php'; ?>

</body>
</html>
