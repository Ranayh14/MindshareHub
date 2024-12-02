<?php
$host = "localhost";  
$user = "root";       
$password = "";       
$database = "mindsharehub";  

$conn = mysqli_connect($host, $user, $password, $database);

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');
?>
