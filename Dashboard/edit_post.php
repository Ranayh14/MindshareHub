<?php
session_start();
include('../conn.php');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'], $_GET['id'])) {
    die("Error: Akses ditolak.");
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Handle form submission
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
    // Ambil postingan yang sedang diedit
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

    // Ambil postingan lain
    $stmt = $conn->prepare("SELECT id, content, created_at, (SELECT username FROM users WHERE users.id = posts.user_id) AS username FROM posts WHERE id != ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $other_posts = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="Dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen w-64 z-50">
        <?php include('../slicing/sidebar.html'); ?>
    </div>

    <!-- Main Content -->
    <div class="main-content ml-64 mr-4">
        <div id="default-carousel" class="relative w-full" data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative flex items-center justify-center h-40 overflow-hidden rounded-lg md:h-71">
                <!-- Item 1 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/Asset/8.png" alt="Banner MindshareHub1" class="w-full h-full object-cover">
                </div>
                <!-- Item 2 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/Asset/BannerAnjay.png" alt="Banner MindshareHub2" class="w-full h-full object-cover">
                </div>
                <!-- Item 3 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/Asset/BannerCurahkan.png" alt="Banner MindshareHub3" class="w-full h-full object-cover">
                </div>
            </div>
            <!-- Slider indicators -->
            <div class="absolute z-30 flex-translate-x-1/2 bottom-5 left-1/2 space-x-3">
                <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full" aria-label="Slide 3" data-carousel-slide-to="2"></button>
            </div>
            <!-- Slider controls -->
            <button type="button" class="carousel-button carousel-button-prev" data-carousel-prev>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                </svg>
            </button>
            <button type="button" class="carousel-button carousel-button-next" data-carousel-next>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                </svg>
            </button>
        </div>
        <!-- Form Edit Post -->
        <div class="content">
            <div class="edit-post-form border p-4 rounded-lg mb-6 mt-6 post">
                <h2 class="text-lg font-semibold mb-3">Edit Postingan</h2>
                <form action="" method="POST">
                    <textarea 
                        name="postContent" 
                        rows="4" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-black"
                        placeholder="Edit konten postingan..."
                        required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <button 
                        type="submit" 
                        class="mt-3 bg-white text-purple-600 px-4 py-2 rounded hover:bg-purple-600 hover:text-white">
                        Update
                    </button>
                </form>
            </div>
        </div>

        <!-- Other Posts -->
        <div class="other-posts">
            <?php
            if ($other_posts->num_rows > 0) {
                while ($row = $other_posts->fetch_assoc()) {
                    echo '<div class="post p-4 rounded-lg mb-4">';
                    echo '    <div class="post-header flex justify-between items-center">';
                    echo '        <div class="flex items-center">';
                    echo '            <div class="avatar"></div>';
                    echo '            <div class="post-author text-lg ml-2">' . htmlspecialchars($row['username']) . '</div>';
                    echo '            <div class="post-time pl-4 text-sm">' . time_ago($row['created_at']) . '</div>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '    <div class="post-content mt-2">' . htmlspecialchars($row['content']) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="post">Tidak ada postingan lain.</div>';
            }
            ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sticky top-0 right-0 h-screen w-64 bg-white z-50">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>
</body>
</html>

<?php
// Fungsi untuk menghitung waktu relatif
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $units = [
        'y' => 'tahun',
        'm' => 'bulan',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    ];
    $string = [];
    foreach ($units as $k => $v) {
        if ($diff->$k) {
            $string[] = $diff->$k . ' ' . $v;
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
}
?>
