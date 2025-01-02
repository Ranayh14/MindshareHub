<?php
session_start();
include('../conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <style>
    @tailwind base;
    @tailwind components;
    @tailwind utilities;

    @layer utilities {
      .bg-customMain {
        background-color: #1e1f2e;
      }
    }
  </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen w-64 z-50">
        <?php include('../slicing/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="bg-customBody flex-1 flex flex-col ml-64">
            <!-- Header -->
            <header class="bg-customMain p-4 flex items-center justify-between mr-8 ml-4 mt-4">
                <div class="flex items-center space-x-5">
                    <img alt="User profile picture" class="rounded-full mr-4" height="80" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="80">
                    <div>
                        <h1 class="text-2xl font-bold">User1234</h1>
                        <div class="flex items-center mt-1">
                            <div class="w-40 bg-gray-600 rounded-full h-2.5">
                                <div class="bg-red-500 h-2.5 rounded-full" style="width: 100%"></div>
                            </div>
                            <span class="ml-2 text-gray-300">4000/4000</span>
                        </div>
                    </div>
                    <i class="fas fa-heart text-red-500 text-5xl"></i>
                </div>
                <a data-modal-target="default-modal" data-modal-toggle="default-modal" class="mb-20 mt-4 mr-4"><i class="fi fi-ss-settings"></i></a>
            </header>

            <!-- Main modal -->
            <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Profile Settings
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5 space-y-4">
                            <p class="text-base leading-relaxed text-black dark:text-black">
                                Username&nbsp;&nbsp;: User1234 <br>
                                Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: N7eVr@example.com
                            </p>                            
                        </div>
                        <!-- Modal footer -->
                        <div class="space y-4 p-5">
                            <div>  
                                <button class="text-white inline-flex w-full justify-center bg-gray-700 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Next step
                                </button> 
                            </div>
                            <div>
                                <button class="text-white inline-flex w-full justify-center bg-gray-700 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Next step
                                </button>
                            </div>
                            <div>
                                <button class="text-white inline-flex w-full justify-center bg-gray-700 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Next step
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <nav class="bg-customMain text-gray-400 flex justify-center border-b border-gray-700 space-x-12 mr-8 ml-4">
                <a href="profile.php" class="px-4 py-2 text-white border-b-2 border-transparent border-white">Posts</a>
                <a href="profileComment.php" class="px-4 py-2 hover:text-white border-b-2 border-transparent hover:border-white">Replies</a>
                <a href="profileLike.php" class="px-4 py-2 hover:text-white border-b-2 border-transparent hover:border-white">Likes</a>
            </nav>

            <!-- Posts -->
            <main class="flex-1 p-4 space-y-4">
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
                <div class="post p-4 rounded-lg mr-4">
                    <div class="flex items-center">
                        <img alt="User profile picture" class="rounded-full mr-2" height="40" src="https://storage.googleapis.com/a1aa/image/vpFbuBQSu6LwNJADhiVP5b1OuoExmfWPdjHPBfqWjWA3zL6TA.jpg" width="40">
                        <div>
                            <div class="flex items-center">
                                <span class="font-bold">User1234</span>
                                <i class="fas fa-heart text-red-500 ml-2"></i>
                            </div>
                            <span class="text-gray-400 text-sm">2h yang lalu</span>
                        </div>
                    </div>
                    <p class="mb-2">
                        Aku sedang patah hati. Setiap memikirkan dia, aku sedih dan ingin menyendiri. Akibatnya, aku sering menolak ajakan teman-teman dan merasa kesepian. Sudah mencoba move on, tapi belum berhasil. Ada yang pernah mengalami hal ini? Bagaimana cara mengatasinya? Butuh saran dan dukungan. Terima kasih!
                    </p>
                    <div class="flex items-center text-gray-400">
                        <i class="fas fa-comment mr-2"></i> 13
                        <i class="fas fa-heart ml-4 mr-2"></i> 24
                    </div>
                </div>
            </main>
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

    <!-- Modal Konfirmasi -->
<div id="modalConfirmation" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
        <h2 id="modalTitle" class="text-xl font-semibold mb-4"></h2>
        <p id="modalBody" class="mb-6"></p>
        <div class="flex justify-end gap-3">
            <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Batal</button>
            <button id="confirmButton" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

 <!-- Modal Konfirmasi -->
 <div id="modalConfirmation" tabindex="-1" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4"></h2>
            <p id="modalBody" class="mb-6"></p>
            <div class="flex justify-end gap-3">
                <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Batal</button>
                <button id="confirmButton" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-800">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- Script JS -->
    <script>
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
