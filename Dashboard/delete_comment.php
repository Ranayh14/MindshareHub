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

// Cek apakah komentar milik user
$sql_check = "SELECT user_id FROM comments WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
if (!$stmt_check) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement check: ' . $conn->error]);
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
if ($row['user_id'] !== $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini']);
    exit;
}

$stmt_check->close();

// Hapus komentar
$sql_delete = "DELETE FROM comments WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
if (!$stmt_delete) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement delete: ' . $conn->error]);
    exit;
}
$stmt_delete->bind_param("i", $comment_id);

if ($stmt_delete->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus komentar: ' . $stmt_delete->error]);
}

$stmt_delete->close();
$conn->close();
?>
