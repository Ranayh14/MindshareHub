<?php
include("../conn.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string(trim($_POST['content']));

    $query = "INSERT INTO dairys (content, user_id, user_username) VALUES ('$content', $user_id, '{$_SESSION['username']}')";
    if ($conn->query($query)) {
        echo "Diary berhasil disimpan.";
    } else {
        echo "Terjadi kesalahan: " . $conn->error;
    }
}

$conn->close();
?>
