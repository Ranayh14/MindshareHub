<?php
session_start();
include('../conn.php');

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
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
        <?php include('../slicing/sidebar.php'); ?>
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

        <div class="content">
            <div class="new-post-form p-4 rounded-lg mb-6 mt-6 post">
                <div class="justify-center">
                    <div class="avatar"></div>
                    <h2 class="text-lg font-semibold mb-3 opacity-90">Buat Postingan Baru</h2>
                </div>
                <form action="post.php" method="POST" enctype="multipart/form-data">
                    <textarea 
                        name="postContent" 
                        rows="4" 
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-black"
                        placeholder="Tulis sesuatu..."
                        required></textarea>
                    <input 
                        type="file" 
                        name="postImage" 
                        accept="image/*" 
                        class="mt-3 block w-full text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-md cursor-pointer focus:outline-none"> 
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
                    $user_id = $_SESSION['user_id'];
                    $sql = "SELECT posts.id, posts.content, posts.created_at, posts.likes, users.username, posts.image_path
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
                            echo '        <div class="post-options absolute top-0 right-0 mt-2 mr-6">';
                            echo '            <div class="relative">';
                            echo '                <button onclick="toggleOptions(' . htmlspecialchars($row['id']) . ')" class="options-button text-xl text-bold">⋮</button>';
                            echo '                <div id="options-' . htmlspecialchars($row['id']) . '" class="options-menu hidden bg-white border border-gray-300 rounded-md shadow-lg absolute top-0 right-0">';
                            echo '                    <a href="edit_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>';
                            echo '                    <a href="delete_post.php?id=' . htmlspecialchars($row['id']) . '" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Hapus</a>';
                            echo '                </div>';
                            echo '            </div>';
                            echo '        </div>';
                            echo '    </div>';
                            echo '    <div class="post-content mt-2 text-white">' . htmlspecialchars($row['content']) . '</div>';

                            // Tampilkan gambar jika ada
                            echo '<div>';
                            echo '    <div class="post-image mt-2 flex justify-center ">';
                                if ($row['image_path']) {
                                    echo '<img src="../uploads/' . htmlspecialchars($row['image_path']) . '" alt="Gambar Postingan" class="mt-2">';
                                }                            
                            echo '    </div">';
                            echo '</div>';
                            echo '<div class="action flex items-center text-gray-400 mt-4">';
                            echo '<span class="like cursor-pointer" data-liked="false" onclick="toggleLike(this, ' . $row['id'] . ')">';
                            echo '  <i class="fas fa-heart ml-4 mr-2 transition-colors hover:text-red-500"></i>';
                            echo '</span>';
                            echo '<span class="like-count">' . $row['likes'] .'&nbsp;'. '</span> Likes';
                            echo '  <button onclick="navigateToComments(' . $row['id'] . ')" class="ml-4">';
                            echo '    <i class="fas fa-comment mr-2"></i>';
                            echo '  </button>';
                            echo '</div>';
                            echo '</div>';
                            echo '    </div>';
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

    <!-- Modal Report -->
    <div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-96 p-6">
            <h2 class="text-lg font-bold mb-2 text-gray-800 dark:text-white">Report @username</h2>
            <p class="text-sm mb-4 text-gray-600 dark:text-gray-300">
                "Aku punya masalah dengan social anxiety. Setiap kali harus berinteraksi dengan orang banyak, aku merasa cemas dan ingin menghindar..."
            </p>
            
            <form id="reportForm">
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">ALASAN</label>
                <select id="reason" name="reason" required class="w-full border border-gray-300 rounded-md shadow-sm p-2 mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="" disabled selected>Pilih alasan</option>
                    <option value="Spam">Spam</option>
                    <option value="Offensive Content">Konten Menyinggung</option>
                    <option value="Others">Lainnya</option>
                </select>

                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">DESKRIPSI</label>
                <textarea id="description" name="description" rows="4" placeholder="Silahkan beritahu kami tentang masalah anda"
                    required class="w-full border border-gray-300 rounded-md shadow-sm p-2 mb-4 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600">Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="modalConfirmation" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4 text-gray-800"></h2>
            <p id="modalBody" class="mb-6 text-gray-600"></p>
            <div class="flex justify-end gap-3">
                <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Batal</button>
                <button id="confirmButton" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- Script JS -->
    <script>
    function openModal() {
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }

    document.getElementById('reportForm').addEventListener('submit', function (event) {
        event.preventDefault();
        const reason = document.getElementById('reason').value;
        const description = document.getElementById('description').value;

        // Kirim data ke server
        fetch('report_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason, description })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            closeModal();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalConfirmation');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const confirmButton = document.getElementById('confirmButton');
        const cancelButton = document.getElementById('cancelButton');

        function showModal(action, callback) {
            modal.classList.remove('hidden');
            modalTitle.innerText = `Konfirmasi ${action}`;
            modalBody.innerText = `Apakah Anda yakin ingin ${action.toLowerCase()} konten ini?`;

            confirmButton.onclick = function () {
                callback();
                modal.classList.add('hidden');
            };

            cancelButton.onclick = function () {
                modal.classList.add('hidden');
            };
        }


        document.querySelectorAll('.post form button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                showModal('Posting', function () {
                    event.target.closest('form').submit();
                });
            });
        });

        document.querySelectorAll('.post-options a').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const actionType = link.innerText.trim().toLowerCase();
                showModal(actionType, function () {
                    window.location.href = link.href;
                });
            });
        });
    });

    function toggleLike(postId) {
        fetch('update_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ postId }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const post = document.querySelector(`.post[data-post-id="${postId}"]`);
                    const likeButton = post.querySelector('.like');
                    const likeCount = post.querySelector('.like-count');

                    if (data.liked) {
                        likeButton.textContent = '<i class="fas fa-heart ml-4 mr-2" style="color: red;"></i>';
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        likeButton.textContent = '<i class="fas fa-heart ml-4 mr-2"></i>';
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                }
            });
    }

    function navigateToComments(postId) {
        window.location.href = `comments.php?post_id=${postId}`;
    }

    function toggleLike(element, postId) {
        const likeCountSpan = element.nextElementSibling;
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
            likeCountSpan.textContent = likeCount;
            })
            .catch(error => console.error('Error:', error));
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

modifikasi kodingan Dashboard.php diatas menyesuaikan peraturan yang dijelaskan sebelumnya