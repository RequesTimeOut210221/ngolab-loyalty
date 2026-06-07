<?php
$host = "lamp-db";
$user = "root";
$pass = "root";
$db = "ngolab_loyalty";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
