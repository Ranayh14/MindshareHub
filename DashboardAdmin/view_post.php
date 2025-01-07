<?php
// Include the database connection
include("../conn.php");

// Ambil ID postingan dari URL
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
} else {
    header("Location: DashboardAdmin.php");
    exit();
}

// Ambil data postingan berdasarkan ID
$post_query = "SELECT p.content, p.created_at, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
$stmt = $conn->prepare($post_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    header("Location: DashboardAdmin.php");
    exit();
}

// Ambil komentar untuk postingan
$comments_query = "SELECT c.id, c.comment, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC";
$stmt = $conn->prepare($comments_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Tutup koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postingan tebaru - Forum Anonim</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2f3136;
            color: white;
        }
        .container {
            max-width: 800px;
        }
        .post-container {
            background-color: #40444b;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }
        .comment-box {
            background-color: #3a3d42;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .comment-author {
            font-weight: 600;
            color: #00bfff;
        }
        .comment-time {
            color: #b9bbbe;
            font-size: 0.85em;
        }
        .icon-comment {
            cursor: pointer;
            transition: transform 0.2s;
            margin-right: 10px;
        }
        .icon-comment:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold text-center mb-6">Postingan Terbaru</h1>
        
        <div class="post-container">
            <h2 class="text-2xl font-semibold mb-2">Dari: <?php echo htmlspecialchars($post['username']); ?></h2>
            <p class="mb-4 text-lg leading-relaxed"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <small class="comment-time">Dibuat pada: <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?></small>
        </div>

        <!-- Ikon Komentar -->
        <div class="flex items-center mb-4">
            <img src="https://img.icons8.com/material-outlined/24/ffffff/comments.png" alt="Comment Icon" class="icon-comment" onclick="toggleComments()">
            <span class="text-lg">Komentar</span>
        </div>

        <!-- Daftar Komentar -->
        <div id="comments-container" class="comment-container">
            <?php if ($comments): ?>
                <div class="space-y-4">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-box">
                            <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                            <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            <small class="comment-time"><?php echo date('d M Y, H:i', strtotime($comment['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-400">Belum ada komentar.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-6">
            <a href="DashboardAdmin.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-full transition duration-300">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        function toggleComments() {
            const commentsContainer = document.getElementById('comments-container');
            commentsContainer.style.display = commentsContainer.style.display === 'none' || commentsContainer.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>
</html>