<?php
session_start();
include('../conn.php');

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub</title>
    <link rel="stylesheet" href="Dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
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
                    <img src="/Asset/Web Banner 48.png" alt="Banner MindshareHub1" class="w-full h-full object-cover">
                </div>
                <!-- Item 2 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/Asset/Web Banner 48.png" alt="Banner MindshareHub2" class="w-full h-full object-cover">
                </div>
                <!-- Item 3 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/Asset/Web Banner 48.png" alt="Banner MindshareHub3" class="w-full h-full object-cover">
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

        <div class="content">
            <div class="new-post-form p-4 rounded-lg mb-6 mt-6 post">
                <div class="justify-center">
                    <div class="avatar"></div>
                    <h2 class="text-lg font-semibold mb-3 opacity-90">Buat Postingan Baru</h2>
                </div>
                <form action="post.php" method="POST">
                    <textarea 
                        name="postContent" 
                        rows="4" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 opacity-40 text-black"
                        placeholder="Tulis sesuatu..."
                        required></textarea>
                    <button 
                        type="submit" 
                        class="mt-3 bg-white text-purple-600 px-4 py-2 rounded hover:bg-purple-600 hover:text-white opacity-90">
                        Kirim
                    </button>
                </form>
            </div>

            <!-- Posts -->
            <div>
                <?php
                    $sql = "SELECT posts.id, posts.content, posts.created_at, users.username 
                            FROM posts 
                            JOIN users ON posts.user_id = users.id 
                            ORDER BY posts.created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="post relative">';
                            echo '    <div class="post-header flex justify-between items-center">';
                            echo '        <div class="flex items-center">';
                            echo '            <div class="avatar"></div>';
                            echo '            <div class="post-author text-lg ml-2">' . htmlspecialchars($row['username']) . '</div>';
                            echo '            <div class="post-time pl-4 text-sm">' . time_ago($row['created_at']) . '</div>'; 
                            echo '        </div>';
                            echo '        <div class="post-options absolute top-0 right-0 mt-2 mr-2">';
                            echo '            <div class="relative">';
                            echo '                <button onclick="toggleOptions(' . htmlspecialchars($row['id']) . ')" class="options-button">⋮</button>';
                            echo '                <div id="options-' . htmlspecialchars($row['id']) . '" class="options-menu hidden bg-white border border-gray-300 rounded-md shadow-lg absolute top-0 right-0">';
                            echo '                    <a href="edit_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>';
                            echo '                    <a href="delete_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Hapus</a>';
                            echo '                </div>';
                            echo '            </div>';
                            echo '        </div>';
                            echo '    </div>';
                            echo '    <div class="post-content mt-2">' . htmlspecialchars($row['content']) . '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="post">Belum ada postingan.</div>';
                    }
                ?>    
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sticky top-0 right-0 h-screen w-64 bg-white z-50">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>

    <script>
    function toggleOptions(postId) {
        var optionsMenu = document.getElementById('options-' + postId);
        if (optionsMenu.classList.contains('hidden')) {
            optionsMenu.classList.remove('hidden');
        } else {
            optionsMenu.classList.add('hidden');
        }
    }
    </script>
</body>
</html>

<?php
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
