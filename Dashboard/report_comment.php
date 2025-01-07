<?php
session_start();
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Periksa apakah data dikirimkan
if (!isset($_POST['comment_id']) || !isset($_POST['description'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$comment_id = intval($_POST['comment_id']);
$reason = trim($_POST['reason']);
$description = trim($_POST['description']);
$user_id = $_SESSION['user_id'];

// Validasi deskripsi
if (empty($description)) {
    echo json_encode(['status' => 'error', 'message' => 'Deskripsi laporan tidak boleh kosong']);
    exit;
}

// Insert laporan ke database (buat tabel reports jika belum ada)
$sql = "INSERT INTO comment_reports (comment_id, user_id, reason,description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iiss", $comment_id, $user_id, $reason, $description);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Komentar telah dilaporkan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal melaporkan komentar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
