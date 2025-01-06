<?php
session_start();
header('Content-Type: application/json');
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

// Tambahkan pemeriksaan untuk parent_id
$parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

// Validasi komentar
if (empty($comment)) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak boleh kosong']);
    exit;
}

// Jika parent_id diberikan, pastikan valid
if ($parent_id !== null) {
    $sql_check_parent = "SELECT id FROM comments WHERE id = ? AND post_id = ?";
    $stmt_check_parent = $conn->prepare($sql_check_parent);
    if (!$stmt_check_parent) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        exit;
    }
    $stmt_check_parent->bind_param("ii", $parent_id, $post_id);
    $stmt_check_parent->execute();
    $result_check_parent = $stmt_check_parent->get_result();
    if ($result_check_parent->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Komentar induk tidak ditemukan']);
        exit;
    }
    $stmt_check_parent->close();
}

// Insert komentar ke database
if ($parent_id !== null) {
    // Komentar dengan parent_id
    $sql = "INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("iisi", $post_id, $user_id, $comment, $parent_id);
} else {
    // Komentar tanpa parent_id
    $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
}

if ($stmt->execute()) {
    // Ambil informasi komentar yang baru ditambahkan, termasuk jumlah like dan status like
    $comment_id = $stmt->insert_id;
    
    // Query untuk mengambil detail komentar termasuk likes dan status like
    $sql_fetch = "
        SELECT 
            comments.id, 
            comments.comment, 
            comments.created_at, 
            users.username, 
            users.profile_picture,
            COUNT(comment_likes.id) AS likes,
            IF(comment_likes.user_id IS NULL, 0, 1) AS liked,
            (comments.user_id = ?) AS is_owner
        FROM comments
        JOIN users ON comments.user_id = users.id
        LEFT JOIN comment_likes ON comments.id = comment_likes.comment_id AND comment_likes.user_id = ?
        WHERE comments.id = ?
        GROUP BY comments.id
    ";
    
    $stmt_fetch = $conn->prepare($sql_fetch);
    if (!$stmt_fetch) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyiapkan statement fetch: ' . $conn->error]);
        exit;
    }
    $stmt_fetch->bind_param("iii", $user_id, $user_id, $comment_id);
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
            'likes' => $new_comment['likes'],
            'liked' => $new_comment['liked'] == 1,
            'is_owner' => $new_comment['is_owner'] == 1,
            'parent_id' => $parent_id
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan komentar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
