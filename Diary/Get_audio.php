<?php
// Menyertakan file koneksi database
include("../conn.php");
session_start();

// Memeriksa apakah pengguna sudah login (terautentikasi)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User is not authenticated."]);
    exit;
}

// Mendapatkan user_id dari session
$user_id = $_SESSION['user_id'];

// Memeriksa apakah parameter `note_id` ada di URL
if (isset($_GET['note_id'])) {
    $note_id = $_GET['note_id'];  // Mendapatkan ID catatan dari URL

    // Menyiapkan query untuk mengambil audio yang terkait dengan note_id dan user_id
    $query = "SELECT id, file_name, created_at FROM audio_notes WHERE user_id = ? AND note_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $note_id);  // Mengikat parameter user_id dan note_id
    $stmt->execute();  // Menjalankan query
    $result = $stmt->get_result();  // Mendapatkan hasil query

    // Membuat array untuk menyimpan data audio
    $audioFiles = [];
    while ($row = $result->fetch_assoc()) {
        $audioFiles[] = [
            'id' => $row['id'],
            'file_name' => $row['file_name'],
            'created_at' => $row['created_at']
        ];
    }
   //Mengembalikan data audio dalam format JSON
    echo json_encode($audioFiles);
} else {
    // Jika parameter note_id tidak ada, mengirimkan error
    echo json_encode(["status" => "error", "message" => "Note ID is required."]);
}
?>
