<?php
include('../conn.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit();
}

$userId = $_SESSION['user_id'];
$query = "SELECT username, profile_picture, progress_percentage FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    $userName = $userData['username'];
    $userProfilePicture = $userData['profile_picture'] ?? 'default-avatar.png';
    $userProgressPercentage = $userData['progress_percentage'] ?? 0;
} else {
    $userName = "Guest";
    $userProfilePicture = "default-avatar.png";
    $userProgressPercentage = 0;
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="sidebar w-[270px] h-screen p-5 flex flex-col justify-between">
        <div>
            <div class="logo text-xl font-bold mb-8 text-white">MindshareHub</div>
            <nav class="menu space-y-2">
                <a href="/Dashboard/Dashboard.php" class="menu-item flex items-center p-3 text-[#8e8ea0] hover:bg-[#2d2d3d] hover:text-white rounded transition">
                    <span class="icon w-6 h-6 flex items-center justify-center mr-3">
                        <i class="fi fi-rr-home"></i>
                    </span>
                    Home
                </a>
                <a href="/notif/notif.php" class="menu-item flex items-center p-3 text-[#8e8ea0] hover:bg-[#2d2d3d] hover:text-white rounded transition">
                    <span class="icon w-6 h-6 flex items-center justify-center mr-3">
                        <i class="fi fi-rr-bell"></i>
                    </span>
                    Notifications
                </a>

                <a href="/Diary/Diary.php" class="menu-item flex items-center p-3 text-[#8e8ea0] hover:bg-[#2d2d3d] hover:text-white rounded transition">
                    <span class="icon w-6 h-6 flex items-center justify-center mr-3">
                        <i class="fi fi-rr-book"></i>
                    </span>
                    Diary
                </a>
                <a href="/Profile/profile.php" class="menu-item flex items-center p-3 text-[#8e8ea0] hover:bg-[#2d2d3d] hover:text-white rounded transition">
                    <span class="icon w-6 h-6 flex items-center justify-center mr-3">
                        <i class="fi fi-rr-user"></i>
                    </span>
                    Profile
                </a>
            </nav>
        </div>
        <div class="flex items-center p-4 bg-purple-700 rounded-full">
            <div class="bg-white rounded-full mr-2" style="width: 30px; height: 30px;"></div>
            <div>
                <div class="flex items-center">
                    <span class="text-white">
                        <?php echo htmlspecialchars($userName); ?>
                    </span>
                    <i class="fas fa-heart text-gray-400 ml-2"></i>
                </div>
                <div class="w-full bg-gray-400 rounded-full h-2.5 mt-1">
                    <div class="bg-red-500 h-2.5 rounded-full" style="width: <?php echo htmlspecialchars($userProgressPercentage); ?>%"></div>
                </div>
            </div>
            <i class="fas fa-ellipsis-h text-gray-400 ml-auto"></i>
        </div>


    </div>
    <script>
        // Highlight the current active menu item
        document.addEventListener("DOMContentLoaded", () => {
            const currentPath = window.location.pathname; // Get current page path
            const menuItems = document.querySelectorAll(".menu-item");
            
            menuItems.forEach(item => {
                if (item.getAttribute("href") === currentPath) {
                    item.classList.remove("text-black"); // Remove default text color
                    item.classList.add("bg-gray-800", "text-white"); // Add active styles
                }
            });
        });
    </script>
</body>
</html>
