<?php
include("../conn.php"); // Koneksi ke database

session_start();
$user_id = $_SESSION['user_id']; // Ambil user_id dari sesi (pastikan pengguna login)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];

    // Cek apakah pengguna sudah menyukai postingan
    $checkQuery = "SELECT * FROM post_likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika sudah di-like, hapus dari `post_likes` dan kurangi jumlah like di `posts`
        $deleteQuery = "DELETE FROM post_likes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();

        $updatePostQuery = "UPDATE posts SET likes = likes - 1 WHERE id = ?";
        $stmt = $conn->prepare($updatePostQuery);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();

        echo json_encode(["status" => "unliked"]);
    } else {
        // Jika belum di-like, tambahkan ke `post_likes` dan tambahkan jumlah like di `posts`
        $insertQuery = "INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();

        $updatePostQuery = "UPDATE posts SET likes = likes + 1 WHERE id = ?";
        $stmt = $conn->prepare($updatePostQuery);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();

        echo json_encode(["status" => "liked"]);
    }
}
?>
