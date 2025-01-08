<!-- profile.php -->
<?php
session_start();
include('../conn.php');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$queryUser = "SELECT username, email, profile_picture FROM users WHERE id = ?";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows === 0) {
    die("Pengguna tidak ditemukan.");
}

$currentUser = $resultUser->fetch_assoc();
$stmtUser->close();

// Ambil semua postingan dari user yang sedang login
$queryPosts = "SELECT p.*, u.username,
                   (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS total_comments
               FROM posts p 
               JOIN users u ON p.user_id = u.id 
               WHERE p.user_id = ?
               ORDER BY p.created_at DESC";
$stmtPosts = $conn->prepare($queryPosts);
$stmtPosts->bind_param("i", $user_id);
$stmtPosts->execute();
$resultPosts = $stmtPosts->get_result();
$stmtPosts->close();

// Fungsi untuk menghitung waktu relatif
function timeAgo($datetime, $full = false) {
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
    <title>MindshareHub - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="/Dashboard/Dashboard.css">
</head>
<body class="bg-gray-900 text-white flex">

    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen w-[270px] bg-[#13141f] z-50">
        <?php include('../slicing/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-64 min-h-screen mr-4">
        <!-- Header -->
        <header class="bg-gray-800 p-4 flex items-center justify-between pt-10 pl-8">
            <div class="flex items-center space-x-4">
                <img src="<?php echo (!empty($currentUser['profile_picture']) ? '../Asset/' . htmlspecialchars($currentUser['profile_picture']) : '../Asset/pp1.png'); ?>" alt="Profile" class="rounded-full w-20 h-20 object-cover current-user-profile" />
                <div>
                    <h1 class="text-xl font-bold"><?php echo htmlspecialchars($currentUser['username']); ?></h1>
                    <div class="flex items-center mt-1">
                        <div class="w-40 bg-gray-400 rounded-full h-2.5">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <span class="ml-2 text-gray-300">0/100</span>
                    </div>
                </div>
                <i class="fas fa-heart text-gray-400 text-5xl"></i>
            </div>
            <!-- Tombol untuk membuka modal dengan ID -->
            <button id="openModalBtn" class="p-2 pr-5">
                <i class="fi fi-rr-settings text-3xl" style="color:rgb(173, 173, 196);"></i>
            </button>
        </header>

        <!-- Modal Settings -->
        <div id="default-modal" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-60 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
            <div class="relative p-4 w-full max-w-2xl h-full md:h-auto mx-auto">
                <div class="bg-white rounded-lg shadow dark:bg-gray-700 text-black">
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold">Profile Settings</h3>
                        <!-- Tombol untuk menutup modal dengan ID -->
                        <button id="closeModalBtn" type="button" class="absolute top-7.5 right-7 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-6">
                        <p>Username: <?php echo htmlspecialchars($currentUser['username']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($currentUser['email']); ?></p>
                    </div>
                    <div class="p-4 flex flex-col space-y-3">
                        <button id="openEditProfileBtn" class="bg-gray-700 text-white w-full py-2 rounded-full hover:bg-gray-800 text-center">
                            Edit Profile
                        </button>
                        <a href="/gantiPassword/gpTahap1.html" class="bg-gray-700 text-white w-full py-2 rounded-full hover:bg-gray-800 text-center">
                            Ganti Kata Sandi
                        </a>
                        <a href="logout.php" class="bg-gray-700 text-white w-full py-2 rounded-full hover:bg-gray-800 text-center">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Profile Picture -->
        <div id="edit-profile-modal" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-70 w-full md:inset-0 h-modal md:h-full flex items-center justify-center bg-black bg-opacity-70">
            <div class="relative p-4 w-full max-w-3xl h-full md:h-auto mx-auto">
                <div class="bg-white rounded-lg shadow dark:bg-gray-700 text-black">
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold">Ganti Profile Picture</h3>
                        <!-- Tombol untuk menutup modal edit profile -->
                        <button id="closeEditProfileBtn" type="button" class="absolute top-7.5 right-7 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Form untuk memilih profile picture -->
                        <form id="profilePictureForm" class="space-y-4">
                            <div class="grid grid-cols-4 gap-4">
                                <?php for($i =1; $i <=12; $i++): ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="profile_picture" value="pp<?php echo $i; ?>.png" <?php if($currentUser['profile_picture'] === "pp{$i}.png") echo 'checked'; ?> class="hidden" />
                                        <img src="../Asset/pp<?php echo $i; ?>.png" alt="pp<?php echo $i; ?>" class="w-20 h-20 object-cover rounded-full border-2 <?php if($currentUser['profile_picture'] === "pp{$i}.png") echo 'border-blue-500'; else echo 'border-transparent'; ?> hover:border-blue-500">
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button" id="cancelEditProfileBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    Simpan
                                </button>
                            </div>
                        </form>
                        <!-- Pesan Feedback -->
                        <div id="feedback" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <nav class="bg-gray-800 text-gray-400 flex justify-center border-b border-gray-700 space-x-12">
            <a href="profile.php" class="px-4 py-2 text-white border-b-2 border-white">Posts</a>
            <a href="profileComment.php" class="px-4 py-2 hover:text-white">Replies</a>
            <a href="profileLike.php" class="px-4 py-2 hover:text-white">Likes</a>
        </nav>

        <!-- Posts -->
        <main class="p-4 flex-1 space-y-4">
            <?php if($resultPosts && mysqli_num_rows($resultPosts) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($resultPosts)): ?>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <img src="<?php echo (!empty($currentUser['profile_picture']) ? '../Asset/' . htmlspecialchars($currentUser['profile_picture']) : '../Asset/pp1.png'); ?>" alt="Profile" class="rounded-full w-10 h-10 object-cover mr-3 current-user-profile" />
                            <div>
                                <span class="font-bold"><?php echo htmlspecialchars($row['username']); ?></span>
                                <span class="block text-gray-400 text-sm" data-time="<?php echo htmlspecialchars($row['created_at']); ?>"></span>
                            </div>
                        </div>
                        <p class="mb-2"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <?php if($row['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Post Image" class="mb-2 max-h-64 object-cover rounded">
                        <?php endif; ?>
                        <div class="flex items-center text-gray-400 text-sm">
                            <i class="fas fa-heart mr-2"></i>
                            <span><?php echo htmlspecialchars($row['likes']); ?> Likes</span> 
                            <i class="fas fa-comment ml-4 mr-2"></i>
                            <span><?php echo htmlspecialchars($row['total_comments']); ?> Komentar</span> 
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada postingan.</p>
            <?php endif; ?>
        </main>
    </div>

    <!-- Right Sidebar -->
    <div class="hidden lg:block sticky top-0 right-0 h-screen w-64 bg-gray-800 z-40">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>

    <!-- Script JS untuk Modal dan Time Ago -->
    <script>
        // Fungsi timeAgo
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

        // Dapatkan elemen-elemen waktu dan perbarui teksnya
        document.addEventListener('DOMContentLoaded', function() {
            const timeElements = document.querySelectorAll('[data-time]');
            timeElements.forEach(element => {
                const datetime = element.getAttribute('data-time');
                element.textContent = timeAgo(datetime);
            });
        });

        // Mendapatkan elemen-elemen yang diperlukan untuk modal
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('default-modal');

        // Mendapatkan elemen-elemen untuk edit profile modal
        const openEditProfileBtn = document.getElementById('openEditProfileBtn');
        const editProfileModal = document.getElementById('edit-profile-modal');
        const closeEditProfileBtn = document.getElementById('closeEditProfileBtn');
        const cancelEditProfileBtn = document.getElementById('cancelEditProfileBtn');
        const profilePictureForm = document.getElementById('profilePictureForm');
        const feedback = document.getElementById('feedback');

        // Fungsi untuk membuka modal settings
        function openModal() {
            modal.classList.remove('hidden');
        }

        // Fungsi untuk menutup modal settings
        function closeModal() {
            modal.classList.add('hidden');
        }

        // Event listener untuk membuka modal settings
        openModalBtn.addEventListener('click', openModal);

        // Event listener untuk menutup modal settings
        closeModalBtn.addEventListener('click', closeModal);

        // Menutup modal ketika klik di luar konten modal settings
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Menutup modal settings dengan menekan tombol Escape
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Fungsi untuk membuka modal edit profile
        function openEditProfileModal() {
            editProfileModal.classList.remove('hidden');
            modal.classList.add('hidden'); // Tutup modal settings
        }

        // Fungsi untuk menutup modal edit profile
        function closeEditProfileModal() {
            editProfileModal.classList.add('hidden');
        }

        // Event listener untuk membuka modal edit profile
        openEditProfileBtn.addEventListener('click', openEditProfileModal);

        // Event listener untuk menutup modal edit profile
        closeEditProfileBtn.addEventListener('click', closeEditProfileModal);

        // Event listener untuk tombol Cancel di modal edit profile
        cancelEditProfileBtn.addEventListener('click', closeEditProfileModal);

        // Menutup modal edit profile ketika klik di luar konten modal edit profile
        window.addEventListener('click', function(event) {
            if (event.target === editProfileModal) {
                closeEditProfileModal();
            }
        });

        // Menutup modal edit profile dengan menekan tombol Escape
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !editProfileModal.classList.contains('hidden')) {
                closeEditProfileModal();
            }
        });

        // Handle form submission untuk profile picture
        profilePictureForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(profilePictureForm);
            const selected_pp = formData.get('profile_picture');

            if (!selected_pp) {
                feedback.innerHTML = '<p class="text-red-500">Silakan pilih profile picture.</p>';
                return;
            }

            // Kirim data via Fetch API
            fetch('edit_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ profile_picture: selected_pp })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    feedback.innerHTML = '<p class="text-green-500">' + data.message + '</p>';
                    // Perbarui gambar profil di halaman yang memiliki kelas 'current-user-profile'
                    const profileImages = document.querySelectorAll('.current-user-profile');
                    profileImages.forEach(img => {
                        img.src = `../Asset/${selected_pp}`;
                    });
                    // Tutup modal edit profile setelah beberapa detik
                    setTimeout(() => {
                        closeEditProfileModal();
                        feedback.innerHTML = '';
                    }, 2000);
                } else {
                    feedback.innerHTML = '<p class="text-red-500">' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                feedback.innerHTML = '<p class="text-red-500">Terjadi kesalahan saat memperbarui profile picture.</p>';
            });
        });
    </script>

</body>
</html>
