<?php
include("../conn.php"); // Hubungkan ke database

// Pastikan ID komentar dikirim melalui URL
if (!isset($_GET['id'])) {
    echo "ID komentar tidak ditemukan.";
    exit;
}

$commentId = $_GET['id'];

// Query untuk mendapatkan detail komentar
$sql = "
    SELECT c.comment AS comment_content, 
           c.created_at AS comment_date, 
           u.username AS commenter, 
           p.content AS post_content 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    WHERE c.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $commentId);
$stmt->execute();
$result = $stmt->get_result();

// Jika komentar tidak ditemukan
if ($result->num_rows == 0) {
    echo "Komentar tidak ditemukan.";
    exit;
}

// Ambil data komentar
$comment = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Komentar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#36393F] text-gray-100">
    <div class="container mx-auto p-8">
        <div class="bg-[#2F3136] p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-4 text-white">Detail Komentar</h1>

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white">Komentar:</h2>
                <p class="text-gray-300"><?php echo htmlspecialchars($comment['comment_content']); ?></p>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white">Tanggal Komentar:</h2>
                <p class="text-gray-300"><?php echo htmlspecialchars($comment['comment_date']); ?></p>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white">Pengguna:</h2>
                <p class="text-gray-300"><?php echo htmlspecialchars($comment['commenter']); ?></p>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold text-white">Konten Postingan:</h2>
                <p class="text-gray-300"><?php echo htmlspecialchars($comment['post_content']); ?></p>
            </div>

            <a href="LaporanMasuk.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Kembali ke Laporan
            </a>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>
