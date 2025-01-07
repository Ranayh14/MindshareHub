<?php
include("../conn.php"); // Pastikan koneksi ke database sudah ada

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    
    // Ambil detail postingan berdasarkan ID
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        echo "Postingan tidak ditemukan.";
        exit;
    }

    // Ambil komentar-komentar untuk postingan ini
    $comment_sql = "SELECT c.id, c.comment, c.likes, c.created_at, u.username, c.parent_id
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.post_id = ?
                    ORDER BY c.created_at ASC";
    $comment_stmt = $conn->prepare($comment_sql);
    $comment_stmt->bind_param("i", $post_id);
    $comment_stmt->execute();
    $comments_result = $comment_stmt->get_result();

    $comments = [];
    while ($comment = $comments_result->fetch_assoc()) {
        $comments[] = $comment;
    }

} else {
    echo "ID tidak valid.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Postingan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2C2F33;
            color: #b9bbbe;
        }
        .bg-light {
            background-color: #23272A;
        }
        .highlight-text {
            color: #7289DA;
        }
        .button-custom {
            background-color: #3B82F6; /* Biru modern */
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .button-custom:hover {
            background-color: #2563EB; /* Biru sedikit lebih gelap */
        }
        .card-shadow {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .comment-card {
            background-color: #2C2F33;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .comment-author {
            font-weight: 600;
            color: #7289DA;
        }
        .comment-toggle {
            cursor: pointer;
            color: #7289DA;
            margin-top: 10px;
        }
        .comments-container {
            display: none; /* Menyembunyikan komentar awal */
        }
    </style>
</head>
<body>

    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold mb-4 text-white">Detail Postingan</h1>
        <div class="bg-light p-6 rounded-lg card-shadow">
            <p class="text-lg font-semibold text-white">Konten:</p>
            <p class="mb-4 text-light"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            
            <?php if ($post['image_path']): ?>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Gambar Postingan" class="mb-4 rounded-lg shadow-md">
            <?php endif; ?>

            <div class="flex justify-between items-center mt-4">
                <p class="text-light"><?php echo htmlspecialchars($post['created_at']); ?></p>
                <p class="text-light">Jumlah Likes: <?php echo htmlspecialchars($post['likes']); ?></p>
            </div>

            <!-- Ikon Komentar untuk Menampilkan Komentar -->
            <div class="flex justify-start mt-4">
                <span class="comment-toggle cursor-pointer text-white hover:text-gray-400" onclick="toggleComments()">
                    <i class="fas fa-comment-dots"></i> Lihat Komentar
                </span>
            </div>

            <!-- Menampilkan Komentar -->
            <div id="comments-container" class="comments-container mt-4">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-card">
                            <p class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></p>
                            
                            <!-- Menampilkan 'Membalas' jika ada parent_id -->
                            <?php if ($comment['parent_id'] != NULL): ?>
                                <?php
                                    // Ambil nama pengomentar yang dibalas (parent_id)
                                    $parent_sql = "SELECT u.username FROM comments c
                                                   JOIN users u ON c.user_id = u.id
                                                   WHERE c.id = ?";
                                    $parent_stmt = $conn->prepare($parent_sql);
                                    $parent_stmt->bind_param("i", $comment['parent_id']);
                                    $parent_stmt->execute();
                                    $parent_result = $parent_stmt->get_result();
                                    $parent_comment = $parent_result->fetch_assoc();
                                    $parent_username = $parent_comment['username'];
                                ?>
                                <p class="text-light text-sm">User <span class="highlight-text"><?php echo htmlspecialchars($comment['username']); ?></span> membalas komentar User <span class="highlight-text"><?php echo htmlspecialchars($parent_username); ?></span></p>
                            <?php endif; ?>

                            <p class="text-light"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            <p class="text-sm text-light mt-2"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                            <p class="text-sm text-light">Likes: <?php echo htmlspecialchars($comment['likes']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-light">Tidak ada komentar untuk postingan ini.</p>
                <?php endif; ?>
            </div>
        </div>

        <a href="LaporanMasuk.php" class="mt-4 inline-block button-custom">Kembali</a>
    </div>

    <script>
        // Fungsi untuk menampilkan atau menyembunyikan komentar
        function toggleComments() {
            var commentsContainer = document.getElementById('comments-container');
            if (commentsContainer.style.display === 'none' || commentsContainer.style.display === '') {
                commentsContainer.style.display = 'block';
            } else {
                commentsContainer.style.display = 'none';
            }
        }
    </script>

</body>
</html>