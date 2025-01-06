<?php
session_start();
include('../conn.php');

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil notifikasi untuk pengguna yang sedang login
$user_id = $_SESSION['user_id'];
$sql = "SELECT title, notes, created_at FROM notifications WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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

    <div class="fixed top-0 left-0 h-screen w-64 z-50">
        <?php include('../slicing/sidebar.php'); ?>
    </div>

    <div class="ml-64 flex-1 p-6">
        <h2 class="text-2xl font-semibold mb-6">Notification</h2>
        <div class="space-y-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-gray-800 p-4 rounded-lg flex items-start space-x-3">
                    <div class="w-10 h-10 bg-purple-500 flex items-center justify-center rounded-full">
                        <span class="text-white">
                            <i class="fi fi-rr-bell"></i>
                        </span>
                    </div>
                    <div class="mt-2">
                        <p class="text-white font-semibold"><?php echo $row['title']; ?></p>
                        <p class="text-gray-400"><?php echo $row['notes']; ?></p>
                        <p class="text-gray-500 text-sm"><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="sticky top-0 right-0 h-screen w-64 bg-white z-50">
        <?php include('../slicing/rightSidebar.html'); ?>
    </div>

    <script>
        // Skrip JavaScript jika diperlukan
    </script>
</body>
</html>