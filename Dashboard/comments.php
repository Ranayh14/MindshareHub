<?php
session_start();
include('../conn.php');

$postId = intval($_GET['post_id']);

// Fetch comments
$sql = "SELECT * FROM comments WHERE post_id = $postId";
$result = $conn->query($sql);

echo "<h1>Comments for Post #$postId</h1>";
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['comment']}</p>";
}
?>
