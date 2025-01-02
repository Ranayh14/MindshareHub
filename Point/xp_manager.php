<?php
/**
 * Fungsi untuk memperbarui progres pengguna berdasarkan aktivitas.
 *
 * @param int $userId ID pengguna
 * @param int $xpChange Perubahan XP (bisa positif untuk penambahan atau negatif untuk pengurangan)
 * @param mysqli $conn Koneksi database
 * @return bool True jika pembaruan berhasil, False jika gagal
 */
function updateUserProgress($userId, $xpChange, $conn) {
    // Ambil nilai progress_percentage saat ini
    $query = "SELECT progress_percentage FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $currentProgress = $userData['progress_percentage'];

        // Hitung nilai progress baru
        $newProgress = max(0, $currentProgress + $xpChange); // Tidak boleh kurang dari 0

        // Update progress_percentage di database
        $updateQuery = "UPDATE users SET progress_percentage = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newProgress, $userId);

        if ($updateStmt->execute()) {
            return true;
        } else {
            error_log("Error updating progress: " . $updateStmt->error);
            return false;
        }
    } else {
        error_log("User ID not found: " . $userId);
        return false;
    }
}
