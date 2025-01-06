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

// Periksa apakah comment_id, reason, dan description dikirimkan
if (!isset($_POST['comment_id']) || !isset($_POST['reason']) || !isset($_POST['description'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$comment_id = intval($_POST['comment_id']);
$reason = trim($_POST['reason']);
$description = trim($_POST['description']);

// Validasi input
if (empty($reason) || empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'Alasan dan deskripsi tidak boleh kosong']);
    exit;
}

// Cek apakah komentar ada
$sql_check = "SELECT id FROM comments WHERE id = ?";
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

$stmt_check->close();

// Insert laporan ke database
$sql = "INSERT INTO comment_reports (comment_id, user_id, reason, description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iiss", $comment_id, $user_id, $reason, $description);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Laporan berhasil dikirim']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim laporan: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
