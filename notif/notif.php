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
    <link rel="stylesheet" href="/Dashboard/Dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</head>
<body>

    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen w-64 z-50">
        <?php include('../slicing/sidebar.html'); ?>
    </div>

    <!-- Main Content -->
    <div class="ml-64 flex-1 p-6">
        <h2 class="text-2xl font-semibold mb-6">Notification</h2>
        <div class="space-y-4">
            <!-- Notification Item -->
            <div class="bg-gray-800 p-4 rounded-lg flex items-start space-x-3">
                <div class="w-10 h-10 bg-purple-500 flex items-center justify-center rounded-full">
                    <span class="text-white">
                        <i class="fi fi-rr-lock"></i>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-white font-semibold">Terdapat login pada akun mu @User1234 di perangkat baru pada tanggal 1 Juni 2024</p>
                </div>
            </div>
            <div class="bg-gray-800 p-4 rounded-lg flex items-start space-x-3">
                <div class="w-10 h-10 bg-blue-500 flex items-center justify-center rounded-full">
                    <span class="text-white">
                        <i class="fi fi-rr-messages-question"></i>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-white font-semibold">Aku punya masalah dengan social anxiety...</p>
                </div>
            </div>
            <div class="bg-gray-800 p-4 rounded-lg flex items-start space-x-3">
                <div class="w-10 h-10 bg-red-500 flex items-center justify-center rounded-full">
                    <span class="text-white">
                        <i class="fi fi-rr-triangle-warning"></i>
                    </span>
                </div>
                <div class="mt-2">
                    <p class="text-white font-semibold">Terdeteksi kegiatan yang melanggar aturan komunitas...</p>
                </div>
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