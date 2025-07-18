<?php

$host = 'localhost';
$dbname = 'web1220920_shalashFlatRental';
$username = 'web1220920_mohamadShalash';
$password = 'hamada132';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
