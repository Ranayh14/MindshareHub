<!-- profileComment.php -->
<?php
session_start();
include('../conn.php');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil semua komentar user beserta postingan yang dikomentari
$queryComments = "
  SELECT c.*, p.content AS postContent, p.image_path AS postImage, p.created_at AS postCreated, u.username AS postOwner
  FROM comments c
  JOIN posts p ON c.post_id = p.id
  JOIN users u ON p.user_id = u.id
  WHERE c.user_id = '$user_id'
  ORDER BY c.created_at DESC
";
$resultComments = mysqli_query($conn, $queryComments);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub - Replies</title>
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
                <img src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" alt="Profile"
                     class="rounded-full w-20 h-20 object-cover" />
                <div>
                    <h1 class="text-xl font-bold"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Tidak Ditemukan'); ?></h1>
                    <div class="flex items-center mt-1">
                        <div class="w-40 bg-gray-400 rounded-full h-2.5">
                            <i class="text-gray-400 ml-auto"></i>
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
                        <p>Username: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Tidak Ditemukan'); ?></p>
                        <p>Email: <?php echo htmlspecialchars($_SESSION['email'] ?? '-'); ?></p>
                    </div>
                    <div class="p-4 flex flex-col space-y-3">
                        <a href="editProfile.php" class="bg-gray-700 text-white w-full py-2 rounded-full hover:bg-gray-800 text-center">
                            Edit Profile
                        </a>
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

        <!-- Tabs -->
        <nav class="bg-gray-800 text-gray-400 flex justify-center border-b border-gray-700 space-x-12">
            <a href="profile.php" class="px-4 py-2 hover:text-white">Posts</a>
            <a href="profileComment.php" class="px-4 py-2 text-white border-b-2 border-white">Replies</a>
            <a href="profileLike.php" class="px-4 py-2 hover:text-white">Likes</a>
        </nav>

        <!-- Comments -->
        <main class="p-4 flex-1 space-y-4">
            <?php if($resultComments && mysqli_num_rows($resultComments) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($resultComments)): ?>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="mb-2">
                            <span class="text-sm text-gray-400">
                                Anda berkomentar pada postingan milik <strong><?php echo htmlspecialchars($row['postOwner']); ?></strong>
                            </span>
                            <p class="text-xs text-gray-500" data-time="<?php echo htmlspecialchars($row['created_at']); ?>">
                                <!-- Time will be populated by JavaScript -->
                            </p>
                        </div>
                        <div class="bg-gray-700 p-2 rounded mb-2">
                            <p class="text-gray-300">Post: <?php echo htmlspecialchars($row['postContent']); ?></p>
                            <?php if($row['postImage']): ?>
                                <img src="<?php echo htmlspecialchars($row['postImage']); ?>" class="mt-2 rounded max-h-64 object-cover" />
                            <?php endif; ?>
                        </div>
                        <div class="text-gray-200 border-l-4 border-blue-500 pl-3">
                            Komentar Anda: <?php echo htmlspecialchars($row['comment']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">Belum ada komentar.</p>
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

        // Dapatkan elemen-elemen yang diperlukan untuk modal
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modal = document.getElementById('default-modal');

        // Fungsi untuk membuka modal
        function openModal() {
            modal.classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            modal.classList.add('hidden');
        }

        // Event listener untuk membuka modal
        openModalBtn.addEventListener('click', openModal);

        // Event listener untuk menutup modal
        closeModalBtn.addEventListener('click', closeModal);

        // Menutup modal ketika klik di luar konten modal
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Menutup modal dengan menekan tombol Escape
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>

</body>
</html>
