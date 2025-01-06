<?php
session_start();
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Periksa apakah comment_id dikirimkan
if (!isset($_POST['comment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Comment ID tidak ditemukan']);
    exit;
}

$comment_id = intval($_POST['comment_id']);
$user_id = $_SESSION['user_id'];

// Cek apakah user sudah menyukai komentar ini
$sql_check = "SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $comment_id, $user_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // User sudah menyukai komentar, lakukan unlike
    $sql_unlike = "DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?";
    $stmt_unlike = $conn->prepare($sql_unlike);
    $stmt_unlike->bind_param("ii", $comment_id, $user_id);
    if ($stmt_unlike->execute()) {
        // Hitung total like setelah unlike
        $sql_count = "SELECT COUNT(*) AS total_likes FROM comment_likes WHERE comment_id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $comment_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $count = $result_count->fetch_assoc()['total_likes'];

        echo json_encode(['status' => 'unliked', 'total_likes' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan unlike']);
    }
} else {
    // User belum menyukai komentar, lakukan like
    $sql_like = "INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)";
    $stmt_like = $conn->prepare($sql_like);
    $stmt_like->bind_param("ii", $comment_id, $user_id);
    if ($stmt_like->execute()) {
        // Hitung total like setelah like
        $sql_count = "SELECT COUNT(*) AS total_likes FROM comment_likes WHERE comment_id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $comment_id);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        $count = $result_count->fetch_assoc()['total_likes'];

        echo json_encode(['status' => 'liked', 'total_likes' => $count]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan like']);
    }
}

$stmt_check->close();
$conn->close();
?>
