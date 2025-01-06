<?php
session_start();
include('../conn.php');

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.html");
    exit;
}

// Fungsi untuk mengubah waktu ke format "time ago"
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub</title>
    <link rel="stylesheet" href="Dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome untuk ikon -->
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
            <div class="absolute z-30 flex space-x-3 bottom-5 left-1/2 transform -translate-x-1/2">
                <button type="button" class="w-3 h-3 rounded-full bg-gray-700" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-full bg-gray-500" aria-label="Slide 2" data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-full bg-gray-500" aria-label="Slide 3" data-carousel-slide-to="2"></button>
            </div>
            <!-- Slider controls -->
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
                    <div class="avatar w-12 h-12 bg-gray-300 rounded-full"></div> <!-- Avatar placeholder -->
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

            <!-- Posts -->
            <div>
                <?php
                    $user_id = $_SESSION['user_id'];
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
                            $isOwner = $row['user_id'] === $user_id; // Cek apakah postingan milik user sendiri
                            $isLiked = $row['liked'] > 0; // Jika user telah menyukai postingan
                            echo '<div class="post relative p-6 rounded-lg mb-6 bg-customPurple text-white" id="post-' . htmlspecialchars($row['id']) . '">';
                            echo '    <div class="post-header flex justify-between items-center">';
                            echo '        <div class="flex items-center">';
                            echo '            <div class="avatar w-10 h-10 bg-gray-300 rounded-full"></div>'; // Avatar placeholder
                            echo '            <div class="post-author text-lg font-semibold ml-3">' . htmlspecialchars($row['username']) . '</div>';
                            echo '            <div class="post-time pl-4 text-sm text-gray-300">' . time_ago($row['created_at']) . '</div>'; 
                            echo '        </div>';
                            echo '    <div class="post-options relative">';
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

                            // Bagian aksi like dan komentar
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
                            echo '            <div class="avatar w-8 h-8 bg-gray-300 rounded-full"></div>'; // Avatar placeholder
                            echo '            <textarea name="comment" rows="1" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300 text-white bg-gray-700 resize-none" placeholder="Tambahkan komentar..." required></textarea>';
                            echo '            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none">Kirim</button>';
                            echo '        </div>';
                            echo '    </form>';
                            echo '</div>';

                            echo '</div>'; // Akhir div.post
                        }
                    } else {
                        echo '<div class="text-center text-gray-500">Belum ada postingan.</div>';
                    }
                ?>    
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sticky top-0 right-0 h-screen w-64 bg-white z-50">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>

    <!-- Modal Report (Menggunakan Flowbite) -->
    <div id="reportModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" onclick="closeModal()" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-hide="reportModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
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
                                <option value="Offensive Content">Konten Menyinggung</option>
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
                            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md shadow-sm hover:bg-red-600">Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi (Jika Diperlukan) -->
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
    function toggleOptions(postId) {
        var optionsMenu = document.getElementById('options-' + postId);
        if (optionsMenu.classList.contains('hidden')) {
            optionsMenu.classList.remove('hidden');
        } else {
            optionsMenu.classList.add('hidden');
        }
    }

    function closeOptions(postId) {
        var optionsMenu = document.getElementById('options-' + postId);
        var optionsButton = document.getElementById('options-button-' + postId);

        if (optionsMenu) {
            optionsMenu.classList.add('hidden');
        }

        if (optionsButton) {
            optionsButton.textContent = '⋮'; // Kembalikan ke ikon titik tiga
        }
    }

    function openReportModal(postId, content) {
        console.log('Post ID:', postId); // Debug postId
        console.log('Content:', content); // Debug content

        // Menggunakan Flowbite untuk membuka modal
        const modal = document.getElementById('reportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Menggunakan flex untuk centering

        document.getElementById('postId').value = postId; // Set nilai post_id
        document.getElementById('reportContent').innerText = `"${content}"`;
    }

    function closeModal() {
        const modal = document.getElementById('reportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

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
            alert(data.message);
            closeModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim laporan.');
        });
    });

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

    function toggleComments(postId) {
        const commentsSection = document.getElementById('comments-' + postId);
        if (commentsSection.classList.contains('hidden')) {
            commentsSection.classList.remove('hidden');
            loadComments(postId);
        } else {
            commentsSection.classList.add('hidden');
        }
    }

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
                        // Bagian yang memuat komentar di JavaScript
                        commentDiv.innerHTML = `
                            <div class="flex items-center mb-2 bg">
                                <div class="avatar w-8 h-8 bg-gray-300 rounded-full"></div>
                                <div class="username font-semibold ml-2">${comment.username}</div>
                                <div class="comment-time text-sm text-gray-500 ml-auto">${timeAgo(comment.created_at)}</div>
                            </div>
                            <div class="comment-text text-white bg-[#191a25] p-2 rounded-md">${comment.comment}</div>
                            <div class="comment-actions flex items-center mt-2">
                                <span class="like-comment cursor-pointer flex items-center mr-4" data-liked="${comment.liked_by_user}" onclick="toggleCommentLike(this, ${comment.id})">
                                    <i class="fas fa-heart mr-1 ${comment.liked_by_user ? 'text-red-500' : 'text-gray-400'}"></i>
                                    <span>${comment.total_likes}</span>
                                </span>
                                <!-- Fitur Reply akan ditambahkan di sini -->
                                <span class="reply-comment cursor-pointer flex items-center mr-4" onclick="replyToComment('${comment.username}')">
                                    <i class="fas fa-reply mr-1 text-gray-400"></i>
                                    <span>Reply</span>
                                </span>
                                <!-- Tombol Edit dan Delete jika komentar milik user sendiri -->
                                ${comment.username === '<?php echo htmlspecialchars($_SESSION['username']); ?>' ? `
                                    <span class="edit-comment cursor-pointer flex items-center mr-4" onclick="editComment(${comment.id}, this)">
                                        <i class="fas fa-edit mr-1 text-gray-400"></i>
                                        <span>Edit</span>
                                    </span>
                                    <span class="delete-comment cursor-pointer flex items-center mr-4" onclick="deleteComment(${comment.id}, this)">
                                        <i class="fas fa-trash mr-1 text-gray-400"></i>
                                        <span>Hapus</span>
                                    </span>
                                ` : `
                                    <span class="report-comment cursor-pointer flex items-center" onclick="reportComment(${comment.id})">
                                        <i class="fas fa-flag mr-1 text-gray-400"></i>
                                        <span>Laporkan</span>
                                    </span>
                                `}
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

    function submitComment(event, postId) {
        event.preventDefault();
        const form = event.target;
        const textarea = form.querySelector('textarea[name="comment"]');
        const comment = textarea.value.trim();
        const parentIdInput = form.querySelector('input[name="parent_id"]');
        const parentId = parentIdInput ? parentIdInput.value.trim() : null;

        if (comment === '') {
            alert('Komentar tidak boleh kosong.');
            return;
        }

        // Persiapkan data yang akan dikirimkan
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
                        <div class="username font-semibold ml-2">${commentData.username}</div>
                        <div class="comment-time text-sm text-gray-500 ml-auto">${timeAgo(commentData.created_at)}</div>
                    </div>
                    <div class="comment-text text-white bg-[#191a25] p-2 rounded-md">${commentData.comment}</div>
                    <div class="comment-actions flex items-center mt-2">
                        <span class="like-comment cursor-pointer flex items-center mr-4" data-liked="${commentData.liked_by_user}" onclick="toggleCommentLike(this, ${commentData.id})">
                            <i class="fas fa-heart mr-1 ${commentData.liked_by_user ? 'text-red-500' : 'text-gray-400'}"></i>
                            <span>${commentData.total_likes}</span>
                        </span>
                        <span class="reply-comment cursor-pointer flex items-center mr-4" onclick="replyToComment('${commentData.username}')">
                            <i class="fas fa-reply mr-1 text-gray-400"></i>
                            <span>Reply</span>
                        </span>
                        ${commentData.username === '<?php echo htmlspecialchars($_SESSION['username']); ?>' ? `
                            <span class="edit-comment cursor-pointer flex items-center mr-4" onclick="editComment(${commentData.id}, this)">
                                <i class="fas fa-edit mr-1 text-gray-400"></i>
                                <span>Edit</span>
                            </span>
                            <span class="delete-comment cursor-pointer flex items-center mr-4" onclick="deleteComment(${commentData.id}, this)">
                                <i class="fas fa-trash mr-1 text-gray-400"></i>
                                <span>Hapus</span>
                            </span>
                        ` : `
                            <span class="report-comment cursor-pointer flex items-center" onclick="reportComment(${commentData.id})">
                                <i class="fas fa-flag mr-1 text-gray-400"></i>
                                <span>Laporkan</span>
                            </span>
                        `}
                    </div>
                `;

                existingCommentsDiv.appendChild(commentDiv);

                // Reset form
                form.reset();
                if (parentIdInput) {
                    parentIdInput.value = '';
                }
            } else {
                alert(data.message || 'Gagal menambahkan komentar.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan komentar.');
        });
    }

    // Fungsi untuk menangani like/unlike pada komentar
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

    // Fungsi untuk mengedit komentar
    function editComment(commentId, element) {
        const commentDiv = element.parentElement.parentElement.querySelector('.comment-text');
        const originalText = commentDiv.textContent;
        const newText = prompt('Edit Komentar:', originalText);
        if (newText !== null) {
            // Kirim permintaan edit ke server
            fetch('edit_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ comment_id: commentId, comment: newText }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    commentDiv.textContent = newText;
                } else {
                    alert(data.message || 'Gagal mengedit komentar.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Fungsi untuk menghapus komentar
    function deleteComment(commentId, element) {
        if (confirm('Apakah Anda yakin ingin menghapus komentar ini?')) {
            fetch('delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ comment_id: commentId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Hapus elemen komentar dari DOM
                    const commentDiv = element.parentElement.parentElement.parentElement;
                    commentDiv.remove();
                } else {
                    alert(data.message || 'Gagal menghapus komentar.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Fungsi untuk melaporkan komentar
    function reportComment(commentId) {
        // Implementasikan modal report komentar atau langsung kirim laporan
        const description = prompt('Masukkan deskripsi laporan Anda:');
        if (description !== null && description.trim() !== '') {
            fetch('report_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ comment_id: commentId, description: description }),
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'Komentar telah dilaporkan.');
            })
            .catch(error => console.error('Error:', error));
        }
    }


    // Fungsi untuk mengubah waktu ke format "time ago" (sama seperti sebelumnya)
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
