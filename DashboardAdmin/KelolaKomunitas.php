<?php
// Menghubungkan ke database
include("../conn.php");

// Proses Logout
if (isset($_POST['logout'])) {
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Inisialisasi variabel
$search = '';
$users = [];
$searchUserId = null;

// Ambil data pengguna dari database (akan digunakan untuk rekomendasi)
$sqlUsers = "SELECT id, username FROM users";
$resultUsers = mysqli_query($conn, $sqlUsers);

// Jika ada pencarian pengguna
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sqlUser = "SELECT id, username FROM users WHERE username LIKE '%$search%' LIMIT 5";
    $resultUser = mysqli_query($conn, $sqlUser);

    if ($resultUser && mysqli_num_rows($resultUser) > 0) {
        while ($user = mysqli_fetch_assoc($resultUser)) {
            $users[] = $user;
        }
        $searchUserId = $users[0]['id']; // Ambil ID pengguna pertama sebagai contoh
    }
}

// Menghapus postingan
if (isset($_POST['delete_post'])) {
    $postId = intval($_POST['post_id']);
    
    // Validasi agar hanya postingan milik user yang dipilih yang bisa dihapus
    if ($searchUserId) {
        $sqlDelete = "DELETE FROM posts WHERE id = $postId AND user_id = $searchUserId";
        mysqli_query($conn, $sqlDelete);
        header("Location: KelolaKomunitas.php?search=" . urlencode($search));
        exit();
    }
}

// Ambil semua postingan dari pengguna yang ditemukan
$posts = [];
if ($searchUserId) {
    $sqlPosts = "SELECT id, content, created_at FROM posts WHERE user_id = $searchUserId ORDER BY created_at ASC";
    $resultPosts = mysqli_query($conn, $sqlPosts);

    if ($resultPosts && mysqli_num_rows($resultPosts) > 0) {
        while ($row = mysqli_fetch_assoc($resultPosts)) {
            $posts[] = $row;
        }
    }
}

// Mulai output HTML
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komunitas - Forum Anonim</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function showSuggestions(value) {
            if (value.length === 0) {
                document.getElementById("suggestions").innerHTML = "";
                return;
            }
            // Kirimkan pencarian ke server untuk mendapatkan hasil pengguna yang sesuai
            document.getElementById("suggestions").innerHTML = "";
            fetch("<?php echo $_SERVER['PHP_SELF']; ?>?search=" + value)
                .then(response => response.text())
                .then(data => {
                    let suggestions = "";
                    let users = <?php echo json_encode($users); ?>;
                    users.forEach(user => {
                        suggestions += `<div class="cursor-pointer hover:bg-gray-200" onclick="selectUser(${user.id}, '${user.username}')">${user.username}</div>`;
                    });
                    document.getElementById("suggestions").innerHTML = suggestions;
                });
        }

        function selectUser(id, username) {
            document.getElementById("search").value = username;
            document.getElementById("searchUserId").value = id;
            document.getElementById("suggestions").innerHTML = "";
        }
    </script>
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
                <a href="KelolaKomunitas.php" class="flex items-center space-x-2 p-2 rounded-lg bg-[#5865F2] text-white">Kelola Komunitas</a>
                <a href="LaporanMasuk.php" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Laporan Masuk</a>
                <button id="logoutButton" class="flex items-center space-x-2 p-2 rounded-lg text-gray-300 hover:bg-[#5865F2] hover:text-white transition duration-200">Keluar</button>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="bg-[#2f3136] p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold">Kelola Posting</h3>
                <form method="GET" class="mt-2">
                    <input type="hidden" id="searchUserId" name="searchUserId" value="<?= $searchUserId ?>">
                    <input type="text" id="search" name="search" onkeyup="showSuggestions(this.value)" class="p-2 w-full bg-[#202225] text-gray-100 rounded" placeholder="Cari pengguna..." value="<?= htmlspecialchars($search) ?>">
                    <div id="suggestions" class="bg-white text-black mt-1 rounded shadow-md max-h-40 overflow-y-auto"></div>
                    <button type="submit" class="mt-2 p-2 bg-[#5865F2] text-white rounded">Kirim</button>
                </form>

                <!-- Menampilkan Pengguna yang Ditemukan -->
                <?php if ($searchUserId): ?>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-gray-200">
                            <strong>Pengguna Ditemukan:</strong> <?= htmlspecialchars($users[0]['username']) ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <table class="min-w-full table-auto text-left mt-4">
                    <thead class="bg-[#202225]">
                        <tr>
                            <th class="px-4 py-2 text-gray-400">No</th>
                            <th class="px-4 py-2 text-gray-400">Judul Posting</th>
                            <th class="px-4 py-2 text-gray-400">Tanggal</th>
                            <th class="px-4 py-2 text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-gray-400 py-4">Tidak ada postingan ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $index => $post): ?>
                                <tr class="hover:bg-[#35393f]">
                                    <td class="px-4 py-2"><?= $index + 1 ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($post['content']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($post['created_at']) ?></td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <a href="HalamanPostingPengguna.php?user_id=<?= $searchUserId ?>" title="Lihat Postingan">
                                            <i class="fas fa-eye text-blue-500 hover:text-blue-300"></i>
                                        </a>
                                        <form method="POST" action="" class="inline">
                                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" name="delete_post" class="text-red-500 hover:text-red-300">
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
