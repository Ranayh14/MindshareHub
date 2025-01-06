<?php
session_start();
header('Content-Type: application/json');
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

// Debugging: Log post_id dan user_id
error_log("fetch_comments.php - User ID: " . $user_id);
error_log("fetch_comments.php - Post ID: " . $post_id);

// Ambil komentar dari database, termasuk jumlah like dan status like pengguna
$sql = "
    SELECT 
        comments.id, 
        comments.comment, 
        comments.created_at, 
        comments.user_id, 
        comments.parent_id, 
        users.username, 
        users.profile_picture, 
        COUNT(comment_likes.id) AS likes,
        IF(comment_likes.user_id IS NULL, 0, 1) AS liked
    FROM comments
    JOIN users ON comments.user_id = users.id
    LEFT JOIN comment_likes ON comments.id = comment_likes.comment_id AND comment_likes.user_id = ?
    WHERE comments.post_id = ?
    GROUP BY comments.id
    ORDER BY comments.created_at ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("fetch_comments.php - Prepare failed: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $user_id, $post_id);

if (!$stmt->execute()) {
    error_log("fetch_comments.php - Execute failed: " . $stmt->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengeksekusi query: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = [
        'id' => $row['id'],
        'comment' => htmlspecialchars($row['comment']),
        'created_at' => $row['created_at'],
        'username' => htmlspecialchars($row['username']),
        'profile_picture' => $row['profile_picture'], 
        'likes' => $row['likes'],
        'liked' => $row['liked'] == 1, // Apakah user sudah like komentar ini
        'is_owner' => $row['user_id'] === $user_id, // Apakah komentar ini milik user
        'parent_id' => $row['parent_id']
    ];
}

echo json_encode(['status' => 'success', 'comments' => $comments]);

$stmt->close();
$conn->close();
?>
