<?php
// Koneksi ke database
include("../conn.php");
session_start();

// Hapus semua sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Arahkan ke halaman login
header("Location: ../login/login.html");
exit();
?>