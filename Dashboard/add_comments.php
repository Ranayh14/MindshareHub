<?php
session_start();
include('../conn.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Periksa apakah data dikirimkan
if (!isset($_POST['post_id']) || !isset($_POST['comment'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$post_id = intval($_POST['post_id']);
$comment = trim($_POST['comment']);
$user_id = $_SESSION['user_id'];

// Validasi komentar
if (empty($comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong']);
    exit;
}

// Insert komentar ke database
$sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $post_id, $user_id, $comment);

if ($stmt->execute()) {
    // Ambil informasi komentar yang baru ditambahkan
    $comment_id = $stmt->insert_id;
    $sql_fetch = "SELECT comments.id, comments.comment, comments.created_at, users.username, users.profile_picture
                  FROM comments
                  JOIN users ON comments.user_id = users.id
                  WHERE comments.id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $comment_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $new_comment = $result_fetch->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'comment' => [
            'id' => $new_comment['id'],
            'comment' => htmlspecialchars($new_comment['comment']),
            'created_at' => $new_comment['created_at'],
            'username' => htmlspecialchars($new_comment['username']),
            'profile_picture' => $new_comment['profile_picture']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan komentar']);
}

$conn->close();
?>
