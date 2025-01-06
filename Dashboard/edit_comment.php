<?php
session_start();
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Periksa apakah data dikirimkan
if (!isset($_POST['comment_id']) || !isset($_POST['comment'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$comment_id = intval($_POST['comment_id']);
$new_comment = trim($_POST['comment']);
$user_id = $_SESSION['user_id'];

// Validasi komentar
if (empty($new_comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong']);
    exit;
}

// Cek apakah komentar milik user
$sql_check = "SELECT user_id FROM comments WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $comment_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak ditemukan']);
    exit;
}

$stmt_check->bind_result($owner_id);
$stmt_check->fetch();

if ($owner_id !== $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk mengedit komentar ini']);
    exit;
}

// Update komentar
$sql_update = "UPDATE comments SET comment = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $new_comment, $comment_id);

if ($stmt_update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil diupdate']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate komentar: ' . $stmt_update->error]);
}

$stmt_update->close();
$conn->close();
?>
