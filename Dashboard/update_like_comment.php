<?php
session_start();
header('Content-Type: application/json');
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Periksa apakah comment_id dikirimkan
if (!isset($_POST['comment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Comment ID tidak ditemukan']);
    exit;
}

$comment_id = intval($_POST['comment_id']);

// Validasi apakah komentar ada
$sql_check = "SELECT id, likes FROM comments WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
if (!$stmt_check) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}
$stmt_check->bind_param("i", $comment_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak ditemukan']);
    exit;
}

$row = $result_check->fetch_assoc();
$current_likes = $row['likes'];

$stmt_check->close();

// Cek apakah user sudah like komentar ini
$sql_like_check = "SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?";
$stmt_like_check = $conn->prepare($sql_like_check);
if (!$stmt_like_check) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}
$stmt_like_check->bind_param("ii", $comment_id, $user_id);
$stmt_like_check->execute();
$result_like_check = $stmt_like_check->get_result();

if ($result_like_check->num_rows > 0) {
    // User sudah like, lakukan unlike
    $sql_unlike = "DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?";
    $stmt_unlike = $conn->prepare($sql_unlike);
    if (!$stmt_unlike) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement unlike: ' . $conn->error]);
        exit;
    }
    $stmt_unlike->bind_param("ii", $comment_id, $user_id);
    if ($stmt_unlike->execute()) {
        // Kurangi jumlah like
        $new_likes = $current_likes - 1;
        $sql_decrement = "UPDATE comments SET likes = ? WHERE id = ?";
        $stmt_decrement = $conn->prepare($sql_decrement);
        if (!$stmt_decrement) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement decrement: ' . $conn->error]);
            exit;
        }
        $stmt_decrement->bind_param("ii", $new_likes, $comment_id);
        $stmt_decrement->execute();
        $stmt_decrement->close();
        
        echo json_encode(['status' => 'unliked', 'likes' => $new_likes]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan unlike: ' . $stmt_unlike->error]);
    }
    $stmt_unlike->close();
} else {
    // User belum like, lakukan like
    $sql_like = "INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)";
    $stmt_like = $conn->prepare($sql_like);
    if (!$stmt_like) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement like: ' . $conn->error]);
        exit;
    }
    $stmt_like->bind_param("ii", $comment_id, $user_id);
    if ($stmt_like->execute()) {
        // Tambah jumlah like
        $new_likes = $current_likes + 1;
        $sql_increment = "UPDATE comments SET likes = ? WHERE id = ?";
        $stmt_increment = $conn->prepare($sql_increment);
        if (!$stmt_increment) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement increment: ' . $conn->error]);
            exit;
        }
        $stmt_increment->bind_param("ii", $new_likes, $comment_id);
        $stmt_increment->execute();
        $stmt_increment->close();
        
        echo json_encode(['status' => 'liked', 'likes' => $new_likes]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal melakukan like: ' . $stmt_like->error]);
    }
    $stmt_like->close();
}

$conn->close();
?>
