<?php
// Menghubungkan ke database
include("../conn.php"); // Pastikan path ini sesuai dengan lokasi file koneksi Anda
session_start();

// Memastikan permintaan menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Memastikan user sudah login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
        exit;
    }

    // Mengambil data dari POST
    $audio_id = $_POST['audio_id'] ?? null;
    $file_name = $_POST['file_name'] ?? null;
    $user_id = $_SESSION['user_id'];

    if ($audio_id && $file_name) {
        // Menghapus dari database
        $sql = "DELETE FROM audio_notes WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        
        // Memastikan pernyataan berhasil disiapkan
        if ($stmt) {
            $stmt->bind_param('ii', $audio_id, $user_id);
            $stmt->execute();

            // Menghapus file dari server
            $file_path = "../uploads/" . $file_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            echo json_encode(["status" => "success", "message" => "Rekaman audio berhasil dihapus."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database query failed."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
