<?php
session_start();
include('../conn.php');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['profile_picture'])) {
        $selected_pp = $_POST['profile_picture'];

        // Validasi pilihan gambar
        $allowed_pp = [];
        for ($i = 1; $i <= 12; $i++) {
            $allowed_pp[] = "pp{$i}.png";
        }

        if (!in_array($selected_pp, $allowed_pp)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilihan gambar tidak valid.']);
            exit();
        }

        // Update profile_picture dalam database
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $selected_pp, $user_id);

        if ($stmt->execute()) {
            // Jika menggunakan session variable for profile_picture, update it
            $_SESSION['profile_picture'] = $selected_pp;

            echo json_encode(['status' => 'success', 'message' => 'Profile picture berhasil diperbarui.']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui profile picture.']);
            exit();
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Profile picture tidak dipilih.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
    exit();
}
?>
