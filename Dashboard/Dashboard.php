<?php
session_start();
include('../conn.php');

// Alihkan jika tidak login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit;
}

// Fungsi untuk mengonversi waktu ke format "waktu lalu"
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" />

    <!-- Flowbite JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>

    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen w-64 z-50">
        <?php include('../slicing/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="main-content ml-64 mr-4">
        <div id="default-carousel" class="relative w-full" data-carousel="slide">
            <!-- Pembungkus carousel -->
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
            <!-- Indikator slider -->
            <div class="absolute z-30 flex space-x-3 bottom-5 left-1/2 transform -translate-x-1/2">
                <button type="button" class="w-3 h-3 rounded-full bg-gray-700" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full bg-gray-500" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full bg-gray-500" aria-label="Slide 3" data-carousel-slide-to="2"></button>
            </div>
            <!-- Kontrol slider -->
            <button type="button" class="carousel-button carousel-button-prev" data-carousel-prev>
                <svg class="w-4 h-4 text-gray-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
                </svg>
                <span class="sr-only">Previous</span>
            </button>
            <button type="button" class="carousel-button carousel-button-next" data-carousel-next>
                <svg class="w-4 h-4 text-gray-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                </svg>
                <span class="sr-only">Next</span>
            </button>
        </div>

        <div class="content">
            <div class="new-post-form p-6 rounded-lg mb-6 mt-6 bg-customPurple text-white">
                <div class="flex items-center mb-4">
                    <div class="avatar w-12 h-12 bg-gray-300 rounded-full"></div> <!-- Placeholder avatar -->
                    <h2 class="text-lg font-semibold ml-4">Buat Postingan Baru</h2>
                </div>
                <form action="post.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <textarea 
                        name="postContent" 
                        rows="4" 
                        class="w-full p-3 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-black resize-none" 
                        placeholder="Tulis sesuatu..." 
                        required></textarea>
                    <input 
                        type="file" 
                        name="postImage" 
                        accept="image/*" 
                        class="block w-full text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-md cursor-pointer focus:outline-none"> 
                    <button 
                        type="submit" 
                        class="w-full bg-white text-purple-600 px-4 py-2 rounded-md hover:bg-purple-600 hover:text-white transition-colors duration-200">
                        Kirim
                    </button>
                </form>
            </div>

            <!-- Postingan -->
            <div>
                <?php
                    $user_id = $_SESSION['user_id'];
                    $username_session = htmlspecialchars($_SESSION['username']); // Menyimpan username session untuk digunakan di JavaScript
                    $sql = "SELECT posts.id, posts.content, posts.created_at, posts.likes, posts.user_id, users.username, posts.image_path,
                        (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id AND post_likes.user_id = ?) AS liked,
                        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS total_comments
                    FROM posts
                    JOIN users ON posts.user_id = users.id
                    ORDER BY posts.created_at DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result(); 

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $isOwner = $row['user_id'] === $user_id; // Cek apakah postingan milik pengguna
                            $isLiked = $row['liked'] > 0; // Jika pengguna telah menyukai postingan
                            echo '<div class="post relative p-6 rounded-lg mb-6 bg-customPurple text-white" id="post-' . htmlspecialchars($row['id']) . '">';
                            echo '    <div class="post-header flex justify-between items-start">'; // Mengubah 'items-center' menjadi 'items-start' untuk penyesuaian
                            echo '        <div class="flex items-center">';
                            echo '            <div class="avatar w-10 h-10 bg-gray-300 rounded-full"></div>'; // Placeholder avatar
                            
                            // Menambahkan "(You)" jika postingan milik pengguna
                            echo '            <div class="post-author text-lg font-semibold ml-3">' . htmlspecialchars($row['username']) . ($isOwner ? ' (You)' : '') . '</div>';
                            
                            echo '            <div class="post-time pl-4 text-sm text-gray-300">' . time_ago($row['created_at']) . '</div>'; 
                            echo '        </div>';
                            echo '    <div class="post-options absolute top-6 right-6">'; // Mengubah posisi menjadi absolut
                            echo '        <button id="options-button-' . htmlspecialchars($row['id']) . '" onclick="toggleOptions(' . htmlspecialchars($row['id']) . ')" class="options-button text-2xl font-bold focus:outline-none">⋮</button>';
                            echo '        <div id="options-' . htmlspecialchars($row['id']) . '" class="options-menu hidden bg-white border border-gray-300 rounded-md shadow-lg absolute right-0 mt-2 z-20">';
                            if ($isOwner) {
                                echo '                <a href="edit_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>';
                                echo '                <a href="delete_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Hapus</a>';
                                echo '                <button onclick="closeOptions(' . htmlspecialchars($row['id']) . ')" class="block w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">Batalkan</button>';
                            } else {
                                echo '                <button onclick="openReportModal(' . htmlspecialchars($row['id']) . ', \'' . addslashes(htmlspecialchars($row['content'])) . '\')" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">Laporkan</button>';
                                echo '                <button onclick="closeOptions(' . htmlspecialchars($row['id']) . ')" class="block w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">Batalkan</button>';
                            }
                            echo '        </div>';
                            echo '    </div>';
                            echo '    </div>';
                            echo '    <div class="post-content mt-4 text-base">' . nl2br(htmlspecialchars($row['content'])) . '</div>';

                            // Tampilkan gambar jika ada
                            if ($row['image_path']) {
                                echo '<div class="post-image mt-4">';
                                echo '    <img src="../uploads/' . htmlspecialchars($row['image_path']) . '" alt="Gambar Postingan" class="rounded-lg shadow-md w-full mt-2">';
                                echo '</div>';
                            }

                            // Bagian aksi untuk like dan komentar
                            echo '<div class="action flex items-center text-gray-300 mt-4">';
                            echo '<span class="like cursor-pointer flex items-center mr-4" data-liked="' . ($isLiked ? 'true' : 'false') . '" onclick="toggleLike(this, ' . $row['id'] . ')">';
                            echo '  <i class="fas fa-heart mr-2 ' . ($isLiked ? 'text-red-500' : '') . '"></i>';
                            echo '  <span>' . $row['likes'] . ' Likes</span>';
                            echo '</span>';
                            echo '<button onclick="toggleComments(' . $row['id'] . ')" class="ml-6 flex items-center text-gray-300 hover:text-gray-500 focus:outline-none">';
                            echo '    <i class="fas fa-comment mr-2"></i>';
                            if ($row['total_comments'] > 0) {
                                echo '    <span>Komentar (' . $row['total_comments'] . ')</span>';
                            } else {
                                echo '    <span>Komentar</span>';
                            }
                            echo '</button>';
                            echo '</div>';
                            echo '<div class="comments-section mt-4 hidden" id="comments-' . htmlspecialchars($row['id']) . '">';
                            echo '    <div class="existing-comments mb-4 space-y-4">';
                            echo '        <!-- Komentar akan dimuat di sini secara dinamis -->';
                            echo '    </div>';
                            echo '    <form class="add-comment-form" onsubmit="submitComment(event, ' . htmlspecialchars($row['id']) . ')">';
                            echo '        <div class="flex items-center space-x-3">';
                            echo '            <div class="avatar w-8 h-8 bg-gray-300 rounded-full"></div>'; // Placeholder avatar
                            echo '            <textarea name="comment" rows="1" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-white bg-gray-700 resize-none" placeholder="Tambahkan komentar..." required></textarea>';
                            echo '            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none">Kirim</button>';
                            echo '        </div>';
                            echo '    </form>';
                            echo '</div>';

                            echo '</div>'; // Akhir div postingan
                        }
                    } else {
                        echo '<div class="text-center text-gray-500">Belum ada postingan.</div>';
                    }
                ?>    
            </div>
        </div>
    </div>

    <!-- Sidebar Kanan -->
    <div class="hidden lg:block sticky top-0 right-0 h-screen w-64 bg-gray-800 z-50">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>

    <!-- Modal Report Post (Menggunakan Flowbite) -->
    <div id="reportModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Konten modal -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeReportModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="reportModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
                <div class="p-6 text-center">
                    <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400 mb-4">Report Post</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="reportContent">
                        "..."
                    </p>
                    <form id="reportForm" action="report_post.php" method="POST">
                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">ALASAN</label>
                            <select id="reason" name="reason" required class="w-full border border-gray-300 rounded-md shadow-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="" disabled selected>Pilih alasan</option>
                                <option value="Spam">Spam</option>
                                <option value="Konten Menyinggung">Konten Menyinggung</option>
                                <option value="Konten Tidak Sesuai">Konten Tidak Sesuai</option>
                                <option value="Pelecehan">Pelecehan</option>
                                <option value="Others">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">DESKRIPSI</label>
                            <textarea id="description" name="description" rows="4" placeholder="Silahkan beritahu kami tentang masalah anda"
                                required class="w-full border border-gray-300 rounded-md shadow-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <input type="hidden" id="postId" name="post_id" value="">
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeReportModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600">Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Report Comment (Baru) -->
    <div id="reportCommentModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Konten modal -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeReportCommentModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="reportCommentModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
                <div class="p-6 text-center">
                    <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400 mb-4">Report Comment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="reportCommentContent">
                        "..."
                    </p>
                    <form id="reportCommentForm" action="report_comment.php" method="POST">
                        <div class="mb-4">
                            <label for="commentReason" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">ALASAN</label>
                            <select id="commentReason" name="reason" required class="w-full border border-gray-300 rounded-md shadow-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="" disabled selected>Pilih alasan</option>
                                <option value="Spam">Spam</option>
                                <option value="Konten Menyinggung">Konten Menyinggung</option>
                                <option value="Konten Tidak Sesuai">Konten Tidak Sesuai</option>
                                <option value="Pelecehan">Pelecehan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="commentDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">DESKRIPSI</label>
                            <textarea id="commentDescription" name="description" rows="4" placeholder="Silahkan beritahu kami tentang masalah anda"
                                required class="w-full border border-gray-300 rounded-md shadow-sm p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>

                        <input type="hidden" id="commentId" name="comment_id" value="">
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeReportCommentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600">Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Komentar -->
    <div id="editCommentModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Konten modal -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeEditCommentModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="editCommentModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Komentar</h3>
                    <form id="editCommentForm" class="space-y-4">
                        <textarea 
                            id="editCommentTextarea" 
                            name="comment" 
                            rows="4" 
                            class="w-full p-3 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-black resize-none" 
                            placeholder="Tulis komentar Anda..." 
                            required></textarea>
                        <input type="hidden" id="editCommentId" name="comment_id" value="">
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="closeEditCommentModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md shadow-sm hover:bg-blue-600">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div id="notificationModal" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 left-0 w-full h-full flex items-start justify-center z-50">
        <!-- Latar belakang -->
        <div class="bg-black bg-opacity-50 w-full h-full absolute top-0 left-0"></div>
        <!-- Kotak Notifikasi -->
        <div class="relative mt-5 bg-white border rounded-md px-4 py-3 shadow-lg" role="alert">
            <span id="notificationMessage" class="block sm:inline"></span>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="confirmationModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Konten modal -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="confirmationTitle">Konfirmasi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="confirmationBody">Anda yakin ingin menghapus komentar ini?</p>
                    <input type="hidden" id="commentIdToDelete" value="">
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeConfirmationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300">Batal</button>
                        <button type="button" onclick="confirmDelete()" class="px-4 py-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip JS -->
    <script>
    // Variabel untuk menyimpan ID komentar yang akan dihapus
    let commentIdToDelete = null;

    // Mendapatkan username session dari PHP untuk digunakan di JavaScript
    const currentUsername = '<?php echo $username_session; ?>';

    // Fungsi untuk toggle menu opsi postingan
    function toggleOptions(postId) {
        var optionsMenu = document.getElementById('options-' + postId);
        if (optionsMenu.classList.contains('hidden')) {
            optionsMenu.classList.remove('hidden');
        } else {
            optionsMenu.classList.add('hidden');
        }
    }

    // Fungsi untuk menutup menu opsi postingan
    function closeOptions(postId) {
        var optionsMenu = document.getElementById('options-' + postId);
        var optionsButton = document.getElementById('options-button-' + postId);

        if (optionsMenu) {
            optionsMenu.classList.add('hidden');
        }

        if (optionsButton) {
            optionsButton.textContent = '⋮'; // Kembalikan ke ikon tiga titik
        }
    }

    // Fungsi untuk membuka modal laporan post
    function openReportModal(postId, content) {
        console.log('Post ID:', postId); // Debug postId
        console.log('Content:', content); // Debug content

        // Menggunakan Flowbite untuk membuka modal
        const modal = document.getElementById('reportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Gunakan flex untuk penengahan

        document.getElementById('postId').value = postId; // Tetapkan nilai post_id
        document.getElementById('reportContent').innerText = `"${content}"`;
    }

    // Fungsi untuk menutup modal laporan post
    function closeReportModal() {
        const modal = document.getElementById('reportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Menangani pengiriman form laporan post
    document.getElementById('reportForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const reason = document.getElementById('reason').value;
        const description = document.getElementById('description').value;
        const postId = document.getElementById('postId').value;

        // Kirim data ke server dalam format x-www-form-urlencoded
        fetch('report_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                reason: reason,
                description: description,
                post_id: postId
            })
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message || 'Laporan berhasil dikirim.', data.status === 'success' ? 'green' : 'red');
            closeReportModal();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat mengirim laporan.', 'red');
        });
    });

    // Fungsi untuk membuka modal laporan komentar
    function openReportCommentModal(commentId, commentContent) {
        console.log('Comment ID:', commentId); // Debug commentId
        console.log('Content:', commentContent); // Debug content

        // Menggunakan Flowbite untuk membuka modal
        const modal = document.getElementById('reportCommentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Gunakan flex untuk penengahan

        document.getElementById('commentId').value = commentId; // Tetapkan nilai comment_id
        document.getElementById('reportCommentContent').innerText = `"${commentContent}"`;
    }

    // Fungsi untuk menutup modal laporan komentar
    function closeReportCommentModal() {
        const modal = document.getElementById('reportCommentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Menangani pengiriman form laporan komentar
    document.getElementById('reportCommentForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const reason = document.getElementById('commentReason').value;
        const description = document.getElementById('commentDescription').value;
        const commentId = document.getElementById('commentId').value;

        // Kirim data ke server dalam format x-www-form-urlencoded
        fetch('report_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                reason: reason,
                description: description,
                comment_id: commentId
            })
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message || 'Laporan berhasil dikirim.', data.status === 'success' ? 'green' : 'red');
            closeReportCommentModal();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat mengirim laporan.', 'red');
        });
    });

    // Fungsi untuk toggle like pada postingan
    function toggleLike(element, postId) {
        const likeCountSpan = element.querySelector('span');
        let likeCount = parseInt(likeCountSpan.textContent);

        fetch('../Dashboard/update_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ post_id: postId }),
        })
            .then(response => response.json())
            .then(data => {
            if (data.status === 'liked') {
                likeCount++;
                element.setAttribute('data-liked', 'true');
                element.querySelector('i').classList.add('text-red-500');
            } else if (data.status === 'unliked') {
                likeCount--;
                element.setAttribute('data-liked', 'false');
                element.querySelector('i').classList.remove('text-red-500');
            }
            likeCountSpan.textContent = likeCount + ' Likes';
            })
            .catch(error => console.error('Error:', error));
    }

    // Fungsi untuk toggle bagian komentar
    function toggleComments(postId) {
        const commentsSection = document.getElementById('comments-' + postId);
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            loadComments(postId);
        } else {
            commentsSection.classList.add('hidden');
        }
    }

    // Fungsi untuk memuat komentar melalui AJAX
    function loadComments(postId) {
        const existingCommentsDiv = document.querySelector('#comments-' + postId + ' .existing-comments');
        existingCommentsDiv.innerHTML = '<div class="text-center text-gray-500">Memuat komentar...</div>';

        fetch('fetch_comments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.comments.length > 0) {
                    existingCommentsDiv.innerHTML = '';
                    data.comments.forEach(comment => {
                        const commentDiv = document.createElement('div');
                        commentDiv.classList.add('comment', 'p-3', 'bg-[#191a25]', 'rounded-lg', 'shadow-sm');
                        // Bagian yang memuat komentar dalam JavaScript
                        commentDiv.innerHTML = `
                            <div class="flex items-center mb-2">
                                <div class="avatar w-8 h-8 bg-gray-300 rounded-full"></div>
                                <div class="username font-semibold ml-2">${escapeHtml(comment.username)}${comment.username === currentUsername ? ' (You)' : ''}</div>
                                <div class="comment-time pl-4 text-sm text-gray-300">${timeAgo(comment.created_at)}</div>
                                <div class="relative ml-auto"> <!-- Mengubah ml-2 menjadi ml-auto untuk menempatkan opsi ke kanan -->
                                    <button onclick="toggleCommentOptions(${comment.id})" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="comment-options-${comment.id}" class="options-menu hidden absolute right-0 mt-2 bg-white border border-gray-300 rounded-md shadow-lg z-20">
                                        ${comment.username === currentUsername ? `
                                            <button onclick="openEditCommentModal(${comment.id}, \`${escapeHtml(comment.comment)}\`)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</button>
                                            <button onclick="initiateDeleteComment(${comment.id})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Hapus</button>
                                        ` : `
                                            <button onclick="openReportCommentModal(${comment.id}, \`${escapeHtml(comment.comment)}\`)" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">Laporkan</button>
                                        `}
                                        <button onclick="closeCommentOptions(${comment.id})" class="block w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">Batalkan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="comment-text text-white bg-[#191a25] p-2 rounded-md">${escapeHtml(comment.comment)}</div>
                            <div class="comment-actions flex items-center mt-2">
                                <span class="like-comment cursor-pointer flex items-center mr-4" data-liked="${comment.liked_by_user}" onclick="toggleCommentLike(this, ${comment.id})">
                                    <i class="fas fa-heart mr-1 ${comment.liked_by_user ? 'text-red-500' : 'text-gray-400'}"></i>
                                    <span>${comment.total_likes}</span>
                                </span>
                                <span class="reply-comment cursor-pointer flex items-center mr-4" onclick="replyToComment('${comment.username}')">
                                    <i class="fas fa-reply mr-1 text-gray-400"></i>
                                    <span>Reply</span>
                                </span>
                            </div>
                        `;

                        existingCommentsDiv.appendChild(commentDiv);
                    });
                } else {
                    existingCommentsDiv.innerHTML = '<div class="text-gray-500">Belum ada komentar.</div>';
                }
            } else {
                existingCommentsDiv.innerHTML = '<div class="text-red-500">Gagal memuat komentar.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            existingCommentsDiv.innerHTML = '<div class="text-red-500">Terjadi kesalahan saat memuat komentar.</div>';
        });
    }

    // Fungsi untuk mengirim komentar baru
    function submitComment(event, postId) {
        event.preventDefault();
        const form = event.target;
        const textarea = form.querySelector('textarea[name="comment"]');
        const comment = textarea.value.trim();
        const parentIdInput = form.querySelector('input[name="parent_id"]');
        const parentId = parentIdInput ? parentIdInput.value.trim() : null;

        if (comment === '') {
            showNotification('Komentar tidak boleh kosong.', 'red');
            return;
        }

        // Persiapkan data yang akan dikirim
        const params = new URLSearchParams();
        params.append('post_id', postId);
        params.append('comment', comment);
        if (parentId) {
            params.append('parent_id', parentId);
        }

        fetch('add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response Data:', data); // Debugging
            if (data.status === 'success') {
                // Tambahkan komentar baru ke bagian existing-comments
                const existingCommentsDiv = document.querySelector('#comments-' + postId + ' .existing-comments');
                if (existingCommentsDiv.innerHTML.includes('Belum ada komentar.') || existingCommentsDiv.innerHTML.includes('Gagal memuat komentar.')) {
                    existingCommentsDiv.innerHTML = '';
                }

                const commentData = data.comment;
                const commentDiv = document.createElement('div');
                commentDiv.classList.add('comment', 'p-3', 'bg-[#191a25]', 'rounded-lg', 'shadow-sm');

                commentDiv.innerHTML = `
                    <div class="flex items-center mb-2">
                        <div class="avatar w-8 h-8 bg-gray-300 rounded-full"></div>
                        <div class="username font-semibold ml-2">${escapeHtml(commentData.username)}${commentData.username === currentUsername ? ' (You)' : ''}</div>
                        <div class="comment-time pl-4 text-sm text-gray-300">${timeAgo(commentData.created_at)}</div>
                        <div class="relative ml-auto"> <!-- Mengubah ml-2 menjadi ml-auto untuk menempatkan opsi ke kanan -->
                            <button onclick="toggleCommentOptions(${commentData.id})" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="comment-options-${commentData.id}" class="options-menu hidden absolute right-0 mt-2 bg-white border border-gray-300 rounded-md shadow-lg z-20">
                                ${commentData.username === currentUsername ? `
                                    <button onclick="openEditCommentModal(${commentData.id}, \`${escapeHtml(commentData.comment)}\`)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</button>
                                    <button onclick="initiateDeleteComment(${commentData.id})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Hapus</button>
                                ` : `
                                    <button onclick="openReportCommentModal(${commentData.id}, \`${escapeHtml(commentData.comment)}\`)" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">Laporkan</button>
                                `}
                                <button onclick="closeCommentOptions(${commentData.id})" class="block w-full text-left px-4 py-2 text-sm text-gray-500 hover:bg-gray-100">Batalkan</button>
                            </div>
                        </div>
                    </div>
                    <div class="comment-text text-white bg-[#191a25] p-2 rounded-md">${escapeHtml(commentData.comment)}</div>
                    <div class="comment-actions flex items-center mt-2">
                        <span class="like-comment cursor-pointer flex items-center mr-4" data-liked="${commentData.liked_by_user}" onclick="toggleCommentLike(this, ${commentData.id})">
                            <i class="fas fa-heart mr-1 ${commentData.liked_by_user ? 'text-red-500' : 'text-gray-400'}"></i>
                            <span>${commentData.total_likes}</span>
                        </span>
                        <span class="reply-comment cursor-pointer flex items-center mr-4" onclick="replyToComment('${commentData.username}')">
                            <i class="fas fa-reply mr-1 text-gray-400"></i>
                            <span>Reply</span>
                        </span>
                    </div>
                `;

                existingCommentsDiv.appendChild(commentDiv);

                // Reset form
                form.reset();
                if (parentIdInput) {
                    parentIdInput.value = '';
                }

                showNotification('Komentar berhasil ditambahkan.', 'green');
            } else {
                showNotification(data.message || 'Gagal menambahkan komentar.', 'red');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menambahkan komentar.', 'red');
        });
    }

    // Fungsi untuk menghindari HTML agar mencegah XSS
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Fungsi untuk menangani like pada komentar
    function toggleCommentLike(element, commentId) {
        fetch('update_like_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ comment_id: commentId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'liked') {
                element.querySelector('i').classList.add('text-red-500');
                element.querySelector('i').classList.remove('text-gray-400');
            } else if (data.status === 'unliked') {
                element.querySelector('i').classList.remove('text-red-500');
                element.querySelector('i').classList.add('text-gray-400');
            }
            element.querySelector('span').textContent = data.total_likes;
        })
        .catch(error => console.error('Error:', error));
    }

    // Fungsi untuk membalas komentar
    function replyToComment(username) {
        const textarea = document.querySelector('.add-comment-form textarea');
        if (textarea) {
            textarea.value += `@${username} `;
            textarea.focus();
        }
    }

    // Fungsi untuk membuka modal edit komentar
    function openEditCommentModal(commentId, commentText) {
        const modal = document.getElementById('editCommentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Gunakan flex untuk penengahan

        document.getElementById('editCommentId').value = commentId;
        document.getElementById('editCommentTextarea').value = commentText;
    }

    // Fungsi untuk menutup modal edit komentar
    function closeEditCommentModal() {
        const modal = document.getElementById('editCommentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Menangani pengiriman form edit komentar
    document.getElementById('editCommentForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const commentId = document.getElementById('editCommentId').value;
        const newComment = document.getElementById('editCommentTextarea').value.trim();

        if (newComment === '') {
            showNotification('Komentar tidak boleh kosong.', 'red');
            return;
        }

        fetch('edit_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ comment_id: commentId, comment: newComment }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update teks komentar di DOM
                const commentTextDiv = document.querySelector(`#comments-` + getPostIdFromCommentId(commentId) + ` #comment-options-${commentId}`).closest('.comment').querySelector('.comment-text');
                commentTextDiv.textContent = newComment;

                showNotification('Komentar berhasil diupdate.', 'green');
                closeEditCommentModal();
            } else {
                showNotification(data.message || 'Gagal mengedit komentar.', 'red');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat mengedit komentar.', 'red');
        });
    });

    // Fungsi utilitas untuk mendapatkan ID postingan dari ID komentar
    function getPostIdFromCommentId(commentId) {
        // Fungsi ini mengasumsikan bahwa ID komentar unik dan dapat dipetakan ke postingan masing-masing.
        // Anda mungkin perlu menyesuaikan ini berdasarkan struktur data Anda yang sebenarnya.
        // Untuk kesederhanaan, mari kita menelusuri DOM ke atas untuk menemukan ID postingan.

        const commentElement = document.querySelector(`#comment-options-${commentId}`);
        if (commentElement) {
            const postElement = commentElement.closest('.post');
            if (postElement) {
                const postId = postElement.id.split('-')[1];
                return postId;
            }
        }
        return null;
    }

    // Fungsi untuk memulai penghapusan komentar (membuka modal konfirmasi)
    function initiateDeleteComment(commentId) {
        commentIdToDelete = commentId;
        const modal = document.getElementById('confirmationModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Gunakan flex untuk penengahan
    }

    // Fungsi untuk menutup modal konfirmasi
    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        commentIdToDelete = null;
    }

    // Fungsi untuk mengonfirmasi penghapusan
    function confirmDelete() {
        if (!commentIdToDelete) {
            showNotification('Komentar tidak ditemukan.', 'red');
            closeConfirmationModal();
            return;
        }

        fetch('delete_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ comment_id: commentIdToDelete }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Hapus elemen komentar dari DOM
                const commentDiv = document.querySelector(`#comment-options-${commentIdToDelete}`).closest('.comment');
                if (commentDiv) {
                    commentDiv.remove();
                }
                showNotification('Komentar berhasil dihapus.', 'green');
            } else {
                showNotification(data.message || 'Gagal menghapus komentar.', 'red');
            }
            closeConfirmationModal();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menghapus komentar.', 'red');
            closeConfirmationModal();
        });
    }

    // Fungsi untuk menangani pelaporan komentar
    // Tidak lagi menggunakan prompt, melainkan menggunakan modal
    // Fungsi openReportCommentModal ditambahkan di atas

    // Fungsi untuk toggle menu opsi komentar
    function toggleCommentOptions(commentId) {
        var optionsMenu = document.getElementById('comment-options-' + commentId);
        if (optionsMenu.classList.contains('hidden')) {
            optionsMenu.classList.remove('hidden');
        } else {
            optionsMenu.classList.add('hidden');
        }
    }

    // Fungsi untuk menutup menu opsi komentar
    function closeCommentOptions(commentId) {
        var optionsMenu = document.getElementById('comment-options-' + commentId);
        if (optionsMenu) {
            optionsMenu.classList.add('hidden');
        }
    }

    // Fungsi untuk menampilkan modal notifikasi
    function showNotification(message, type = 'green') {
        const notification = document.getElementById('notificationModal');
        const notificationMessage = document.getElementById('notificationMessage');

        notificationMessage.textContent = message;

        // Atur warna border dan teks berdasarkan tipe
        const notificationBox = notification.querySelector('.bg-white');
        if (type === 'green') {
            notificationBox.classList.remove('border-red-400', 'text-red-700');
            notificationBox.classList.add('border-green-400', 'text-green-700');
        } else if (type === 'red') {
            notificationBox.classList.remove('border-green-400', 'text-green-700');
            notificationBox.classList.add('border-red-400', 'text-red-700');
        }

        notification.classList.remove('hidden');
        notification.classList.add('flex');

        // Secara otomatis sembunyikan setelah 3 detik
        setTimeout(() => {
            notification.classList.remove('flex');
            notification.classList.add('hidden');
        }, 3000);
    }

    // Fungsi untuk menangani format "waktu lalu" dalam JavaScript (sama seperti fungsi PHP)
    function timeAgo(datetime) {
        const now = new Date();
        const ago = new Date(datetime);
        const diff = now - ago; // dalam milidetik

        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(diff / (1000 * 60));
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30));
        const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365));

        if (years > 0) {
            return years + ' tahun yang lalu';
        } else if (months > 0) {
            return months + ' bulan yang lalu';
        } else if (days > 0) {
            return days + ' hari yang lalu';
        } else if (hours > 0) {
            return hours + ' jam yang lalu';
        } else if (minutes > 0) {
            return minutes + ' menit yang lalu';
        } else {
            return 'baru saja';
        }
    }
    </script>

</body>
</html>
