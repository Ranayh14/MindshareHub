<?php
header('Content-Type: application/json');
session_start();
include('../conn.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Anda harus login terlebih dahulu.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari request
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $user_id = $_SESSION['user_id'];

    // Validasi input
    if (empty($post_id) || empty($reason) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi.']);
        exit;
    }

    // Validasi apakah postingan yang dilaporkan ada
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query.']);
        exit;
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Postingan tidak ditemukan.']);
        exit;
    }

    $post = $result->fetch_assoc();
    $post_owner_id = $post['user_id'];

    // Cegah user melaporkan postingannya sendiri
    if ($post_owner_id === $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak dapat melaporkan postingan Anda sendiri.']);
        exit;
    }

    // Simpan laporan ke database
    $stmt = $conn->prepare("INSERT INTO reports (post_id, reported_by, reason, description, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query.']);
        exit;
    }
    $stmt->bind_param("iiss", $post_id, $user_id, $reason, $description);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Postingan berhasil dilaporkan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan laporan.']);
    }

    $stmt->close();
    $conn->close();
} else {
    // Metode HTTP selain POST tidak diizinkan
    echo json_encode(['status' => 'error', 'message' => 'Metode HTTP tidak diizinkan.']);
}
?>
