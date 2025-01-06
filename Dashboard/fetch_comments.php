<?php
session_start();
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Periksa apakah post_id dikirimkan
if (!isset($_POST['post_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Post ID tidak ditemukan']);
    exit;
}

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['user_id'];

// Ambil komentar dari database
$sql = "SELECT comments.id, comments.comment, comments.created_at, users.username, users.profile_picture
        FROM comments
        JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'id' => $row['id'],
            'comment' => htmlspecialchars($row['comment']),
            'created_at' => $row['created_at'],
            'username' => htmlspecialchars($row['username']),
            'profile_picture' => $row['profile_picture'] // Asumsikan ada kolom ini di tabel users
        ];
    }
}

echo json_encode(['status' => 'success', 'comments' => $comments]);
?>
