<?php
session_start();
include('../conn.php');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'], $_GET['id'])) {
    die("Error: Akses ditolak.");
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['postContent'];
    if (empty($content)) {
        die("Error: Konten tidak boleh kosong.");
    }

    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $content, $post_id, $user_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT content FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        die("Error: Postingan tidak ditemukan atau bukan milik Anda.");
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>
<body>
    <form action="" method="POST">
        <textarea name="postContent" rows="4" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit">Update</button>
    </form>
</body>
</html>
