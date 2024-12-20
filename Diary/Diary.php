<?php
include("../conn.php");
session_start();

// Ambil data catatan dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT id, title, content, LEFT(title, 50) AS snippet FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($query);

$userId = $user_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $editId = $_POST['edit_id']; 

    if ($action == 'save') {
        // Menyimpan catatan baru
        $sql = "INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $title, $content, $userId);
        $stmt->execute();
    } elseif ($action == 'update' && !empty($editId)) {
        // Memperbarui catatan yang ada
        $sql = "UPDATE posts SET title = ?, content = ?, is_edited = TRUE WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $title, $content, $editId, $userId);
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
        <div class="header flex items-center justify-between mb-5">
            <div class="title-container flex-grow mr-2">
                <input id="title" type="text" class="title-input w-full p-3 bg-[#2d2d3d] text-white text-lg rounded outline-none" placeholder="Judul" value="">
            </div>
            <div class="actions flex space-x-2">
                <button id="new-note-btn" class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <span class="mr-2">+</span> New notes
                </button>
                <button id="save-btn" class="save-btn px-4 py-3 bg-green-500 text-white rounded hover:bg-green-600 transition">Save</button>
            </div>
        </div>

        <textarea id="content" class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>

        <div class="voice-note mt-5">
            <button id="record-btn" class="px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition">
                <span id="record-btn-text"> Start Recording</span>
            </button>
            <p id="record-status" class="text-sm text-[#8e8ea0] mt-2 hidden">Recording...</p>
            <div id="recordings-container" class="space-y-4 mt-4"></div>
        </div>
    </div>

    <div class="notes-sidebar w-72 bg-[#13141f] h-screen p-5 flex flex-col">
        <div class="notes-list flex-grow overflow-y-auto space-y-3" id="notes-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]" data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-content="<?php echo htmlspecialchars($row['content']); ?>">
                    <h3 class="note-title text-base font-bold mb-1"> <?php echo htmlspecialchars($row['snippet']); ?>...</h3>
                    <p class="note-snippet text-sm text-[#8e8ea0]"> <?php echo htmlspecialchars($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        const notesList = document.getElementById('notes-list');
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const saveBtn = document.getElementById('save-btn');
        const newNoteBtn = document.getElementById('new-note-btn');

        let currentEditId = null;

        notesList.addEventListener('click', function (e) {
            const noteItem = e.target.closest('.note-item');
            if (!noteItem) return;

            currentEditId = noteItem.getAttribute('data-id');
            const title = noteItem.getAttribute('data-title');
            const content = noteItem.getAttribute('data-content');

            titleInput.value = title;
            contentInput.value = content;
        });

        saveBtn.addEventListener('click', function () {
            const title = titleInput.value;
            const content = contentInput.value;

            if (title.trim() === '' || content.trim() === '') {
                alert('Title and content cannot be empty.');
                return;
            }

            const formData = new FormData();
            formData.append('title', title);
            formData.append('content', content);
            formData.append('action', currentEditId ? 'update' : 'save');

            if (currentEditId) {
                formData.append('edit_id', currentEditId);
            }

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(() => {
                    alert('Note saved successfully!');
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        });

        newNoteBtn.addEventListener('click', function () {
            titleInput.value = '';
            contentInput.value = '';
            currentEditId = null;
        });
    </script>
</body>
</html>
