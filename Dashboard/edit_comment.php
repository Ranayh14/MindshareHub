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

// Periksa apakah comment_id dan new_comment dikirimkan
if (!isset($_POST['comment_id']) || !isset($_POST['new_comment'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$comment_id = intval($_POST['comment_id']);
$new_comment = trim($_POST['new_comment']);

// Validasi komentar
if (empty($new_comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong']);
    exit;
}

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
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk mengedit komentar ini']);
    exit;
}

$stmt_check->close();

// Update komentar
$sql_update = "UPDATE comments SET comment = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
if (!$stmt_update) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement update: ' . $conn->error]);
    exit;
}
$stmt_update->bind_param("si", $new_comment, $comment_id);

if ($stmt_update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Komentar berhasil diubah']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah komentar: ' . $stmt_update->error]);
}

$stmt_update->close();
$conn->close();
?>
