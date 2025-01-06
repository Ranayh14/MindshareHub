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
$parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

// Validasi komentar
if (empty($comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong']);
    exit;
}

// Insert komentar ke database
$sql = "INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iisi", $post_id, $user_id, $comment, $parent_id);

if ($stmt->execute()) {
    // Ambil informasi komentar yang baru ditambahkan
    $comment_id = $stmt->insert_id;
    $sql_fetch = "SELECT comments.id, comments.comment, comments.created_at, users.username, users.profile_picture,
                     (SELECT COUNT(*) FROM comment_likes WHERE comment_likes.comment_id = comments.id) AS total_likes,
                     (SELECT COUNT(*) FROM comment_likes WHERE comment_likes.comment_id = comments.id AND comment_likes.user_id = ?) AS liked_by_user
                  FROM comments
                  JOIN users ON comments.user_id = users.id
                  WHERE comments.id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    if (!$stmt_fetch) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement fetch: ' . $conn->error]);
        exit;
    }
    $stmt_fetch->bind_param("ii", $user_id, $comment_id);
    if (!$stmt_fetch->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengeksekusi statement fetch: ' . $stmt_fetch->error]);
        exit;
    }
    $result_fetch = $stmt_fetch->get_result();
    $new_comment = $result_fetch->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'comment' => [
            'id' => $new_comment['id'],
            'comment' => htmlspecialchars($new_comment['comment']),
            'created_at' => $new_comment['created_at'],
            'username' => htmlspecialchars($new_comment['username']),
            'profile_picture' => $new_comment['profile_picture'],
            'total_likes' => $new_comment['total_likes'],
            'liked_by_user' => $new_comment['liked_by_user'] > 0 ? true : false
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan komentar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
