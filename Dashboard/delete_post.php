<?php
session_start();
include('../conn.php');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'], $_GET['id'])) {
    die("Error: Akses ditolak.");
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Hapus postingan
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
