<?php
include("../conn.php");

$userId = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $content = $_POST['content'];
    $editId = $_POST['edit_id']; 

    if ($action == 'save') {
        // Menyimpan catatan baru
        $sql = "INSERT INTO posts (content, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $content, $userId);
        $stmt->execute();
    } elseif ($action == 'update' && !empty($editId)) {
        // Memperbarui catatan yang ada
        $sql = "UPDATE posts SET content = ?, is_edited = TRUE WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sii', $content, $editId, $userId);
        $stmt->execute();
    } elseif ($action == 'delete' && !empty($editId)) {
        // Menghapus catatan
        $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $editId, $userId);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub - Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1a1b26] text-white flex">
    <!-- sidebar -->
    <div>
        <?php include('../slicing/sidebar.html'); ?>
    </div>

    <div class="main-content flex-1 p-5">
<<<<<<< Updated upstream
        <div class="header flex items-center justify-between mb-5">
            <div class="title-container flex-grow mr-2">
                <input id="title" type="text" class="title-input w-full p-3 bg-[#2d2d3d] text-white text-lg rounded outline-none" placeholder="Judul" value="">
            </div>
            <div class="actions flex space-x-2">
                <button class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <span class="mr-2">+</span> New notes
                </button>
                <button id="save-btn" class="save-btn px-4 py-3 bg-green-500 text-white rounded hover:bg-green-600 transition">Save</button>
            </div>
        </div>

        <textarea id="content" class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>
=======
        <form method="POST" action="">
            <div class="header flex items-center justify-between mb-5">
                <div class="title-container flex-grow mr-2">
                    <input type="text" name="title" class="title-input w-full p-3 bg-[#2d2d3d] text-white text-lg rounded outline-none" placeholder="Judul" value="">
                </div>
                <div class="actions flex space-x-2">
                    <button type="submit" name="action" value="save" class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                        <span class="mr-2">+</span> New notes
                    </button>
                    <button type="submit" name="action" value="update" class="save-btn px-4 py-3 bg-green-500 text-white rounded hover:bg-green-600 transition">Save</button>
                    <button type="submit" name="action" value="delete" class="delete-btn px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition">Delete</button>
                </div>
            </div>

            <textarea name="content" class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>
            <input type="hidden" name="edit_id" value="">
        </form>
>>>>>>> Stashed changes
    </div>

    <div class="notes-sidebar w-72 bg-[#13141f] h-screen p-5 flex flex-col">
        <div class="notes-list flex-grow overflow-y-auto space-y-3">
<<<<<<< Updated upstream
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]">
                    <h3 class="note-title text-base font-bold mb-1"><?php echo htmlspecialchars($row['snippet']); ?>...</h3>
                    <p class="note-snippet text-sm text-[#8e8ea0]"><?php echo htmlspecialchars($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
=======
            <?php
            // Menampilkan catatan
            $sql = "SELECT * FROM posts WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($note = $result->fetch_assoc()) {
                echo '<div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]" onclick="document.querySelector(\'[name=content]\').value=\'' . htmlspecialchars($note['content'], ENT_QUOTES) . '\'; document.querySelector(\'[name=edit_id]\').value=' . $note['id'] . ';">
                        <h3 class="note-title text-base font-bold mb-1">' . htmlspecialchars($note['content']) . '</h3>';
                
                if (isset($note['is_edited']) && $note['is_edited']) {
                    echo '<p class="text-sm text-red-500">Catatan sudah diedit.</p>';
                }
                
                echo '</div>';
            }
            ?>
>>>>>>> Stashed changes
        </div>
    </div>

    <script>
        document.getElementById('save-btn').addEventListener('click', function() {
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value.trim();

            if (title && content) {
                // Kirim data ke PHP menggunakan fetch
                fetch('saveDiary.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}`
                })
                .then(response => response.text())
                .then(data => {
                    alert('Catatan berhasil disimpan!');
                    location.reload(); // Refresh halaman untuk menampilkan catatan terbaru
                })
                .catch(error => {
                    alert('Terjadi kesalahan saat menyimpan catatan.');
                    console.error(error);
                });
            } else {
                alert('Judul dan konten tidak boleh kosong.');
            }
        });
    </script>
</body>
</html>