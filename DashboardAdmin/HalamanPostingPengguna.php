<?php
// Menghubungkan ke database
include("../conn.php");

// Mendapatkan ID pengguna dari URL
if (!isset($_GET['user_id'])) {
    die("ID pengguna tidak ditemukan.");
}

$userId = intval($_GET['user_id']);

// Mengambil data pengguna berdasarkan user_id
$sqlUser = "SELECT id, username, email, roles, is_banned, ban_reason, ban_date, profile_picture, created_at FROM users WHERE id = $userId";
$resultUser = mysqli_query($conn, $sqlUser);

if ($resultUser && mysqli_num_rows($resultUser) > 0) {
    $user = mysqli_fetch_assoc($resultUser);
} else {
    die("Pengguna tidak ditemukan.");
}

// Menghapus postingan
if (isset($_POST['delete_post'])) {
    $postId = intval($_POST['post_id']);
    $sqlDelete = "DELETE FROM posts WHERE id = $postId AND user_id = $userId";
    if (mysqli_query($conn, $sqlDelete)) {
        header("Location: HalamanPostingPengguna.php?user_id=" . $userId);
        exit();
    } else {
        echo "Gagal menghapus postingan: " . mysqli_error($conn);
    }
}

// Mengambil semua postingan pengguna
$posts = [];
$sqlPosts = "SELECT id, content, created_at, image_path, likes FROM posts WHERE user_id = $userId ORDER BY created_at DESC";
$resultPosts = mysqli_query($conn, $sqlPosts);

if ($resultPosts && mysqli_num_rows($resultPosts) > 0) {
    while ($row = mysqli_fetch_assoc($resultPosts)) {
        $posts[] = $row;
    }
} else {
    $posts = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Forum Anonim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#2f3136] text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#202225] p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-white">Pusat Admin</h1>
            </div>
            <nav class="space-y-4">
                <a href="DashboardAdmin.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Beranda</a>
                <a href="KelolaPengguna.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Kelola Pengguna</a>
                <a href="KelolaKonten.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Kelola Konten</a>
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Kelola Komunitas</a>
                <a href="LaporanMasuk.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Laporan Masuk</a>
                <button id="logoutButton" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Keluar</button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Profil Pengguna: <?= htmlspecialchars($user['username']) ?></h3>
                
                <!-- Menampilkan informasi pengguna -->
                <div class="mt-4">
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Peran:</strong> <?= htmlspecialchars($user['roles']) ?></p>
                    <p><strong>Status Ban:</strong> <?= $user['is_banned'] ? 'Diblokir' : 'Aktif' ?></p>
                    <?php if ($user['is_banned']): ?>
                        <p><strong>Alasan Ban:</strong> <?= htmlspecialchars($user['ban_reason']) ?></p>
                        <p><strong>Tanggal Ban:</strong> <?= htmlspecialchars($user['ban_date']) ?></p>
                    <?php endif; ?>
                    <p><strong>Tanggal Bergabung:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                    <p><strong>Gambar Profil:</strong></p>
                    <?php if ($user['profile_picture']): ?>
                        <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" class="w-32 h-32 object-cover rounded-full">
                    <?php else: ?>
                        <span class="text-gray-400">Tidak ada gambar profil</span>
                    <?php endif; ?>
                </div>

                <h3 class="text-lg font-semibold mt-6">Postingan dari <?= htmlspecialchars($user['username']) ?></h3>

                <!-- Tabel Postingan Pengguna -->
                <table class="min-w-full table-auto text-left mt-4">
                    <thead class="bg-[#202225]">
                        <tr>
                            <th class="px-4 py-2 text-gray-400">No</th>
                            <th class="px-4 py-2 text-gray-400">Judul Posting</th>
                            <th class="px-4 py-2 text-gray-400">Tanggal</th>
                            <th class="px-4 py-2 text-gray-400">Likes</th>
                            <th class="px-4 py-2 text-gray-400">Gambar</th>
                            <th class="px-4 py-2 text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-gray-400 py-4">Tidak ada postingan ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $index => $post): ?>
                                <tr class="hover:bg-[#35393f]">
                                    <td class="px-4 py-2"><?= $index + 1 ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($post['content']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($post['created_at']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($post['likes']) ?></td>
                                    <td class="px-4 py-2">
                                        <?php if ($post['image_path']): ?>
                                            <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Image" class="w-16 h-16 object-cover">
                                        <?php else: ?>
                                            <span class="text-gray-400">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" name="delete_post" class="text-red-500 hover:text-red-300" title="Hapus Posting">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
