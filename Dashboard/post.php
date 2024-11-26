<?php
session_start();
include('../conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['postContent'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO posts (content, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $content, $user_id);

    if ($stmt->execute()) {
        echo "Posting berhasil disimpan.";
        header("Location: dashboard.php");
    } else {
        echo "Gagal menyimpan postingan: " . $stmt->error;
    }

    $stmt->close();
}
?>
