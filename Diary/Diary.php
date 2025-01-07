<?php
include("../conn.php");
session_start();

// Ambil data catatan diary dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT id, title, content, LEFT(title, 50) AS snippet FROM diarys WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audio'])) {
        // Handle audio upload
        if (empty($_POST['title'])) {
            echo json_encode(["status" => "error", "message" => "You must save a note with a title before recording."]);
            exit;
        }

        $audioFile = $_FILES['audio'];
        $targetDir = "../uploads/";
        $fileName = uniqid() . "_" . basename($audioFile["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($audioFile["tmp_name"], $targetFilePath)) {
            // Menyimpan catatan audio terkait
            $note_id = $_POST['note_id'];
            $sql = "INSERT INTO audio_notes (user_id, file_name, created_at, note_id) VALUES (?, ?, NOW(), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('isi', $user_id, $fileName, $note_id);
            $stmt->execute();
            echo json_encode(["status" => "success", "message" => "Audio uploaded successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to upload audio."]);
        }
        exit;
    }

    // Handle actions for saving, updating, or deleting notes...
    $action = $_POST['action'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $editId = $_POST['edit_id'];

    if ($action == 'save') {
        // Menyimpan catatan baru
        if (empty($title)) {
            echo json_encode(["status" => "error", "message" => "Title is required to save a note."]);
            exit;
        }
        $sql = "INSERT INTO diarys (title, content, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $title, $content, $user_id);
        $stmt->execute();
    } elseif ($action == 'update' && !empty($editId)) {
        // Memperbarui catatan yang ada
        if (empty($title)) {
            echo json_encode(["status" => "error", "message" => "Title is required to update the note."]);
            exit;
        }
        $sql = "UPDATE diarys SET title = ?, content = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $title, $content, $editId, $user_id);
        $stmt->execute();
    } elseif ($action == 'delete' && !empty($editId)) {
        // Menghapus catatan
        $sql = "DELETE FROM diarys WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $editId, $user_id);
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
    <!-- Sidebar -->
    <div>
        <?php include('../slicing/sidbar.html'); ?>
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
                <button id="delete-btn" class="delete-btn px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition">Delete</button>
            </div>
        </div>

        <textarea id="content" class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>

        <!-- Voice Note Section -->
        <div class="voice-note mt-5">
            <button id="record-btn" class="px-4 py-3 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Start Recording
            </button>
            <button id="stop-record-btn" class="px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition hidden">
                Stop Recording
            </button>
            <div id="recordings-container" class="space-y-4 mt-4"></div>
        </div>
    </div>

    <div class="notes-sidebar w-72 bg-[#13141f] h-screen p-5 flex flex-col">
        <h3 class="text-lg font-semibold">Catatan Anda</h3>
        <div class="notes-list flex-grow overflow-y-auto space-y-3" id="notes-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]" data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-content="<?php echo htmlspecialchars($row['content']); ?>">
                        <h3 class="note-title text-base font-bold mb-1"><?php echo htmlspecialchars($row['snippet']); ?>...</h3>
                        <p class="note-snippet text-sm text-[#8e8ea0]"><?php echo htmlspecialchars($row['content']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-notes-message text-gray-500 text-center">
                    Belum ada catatan.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const notesList = document.getElementById('notes-list');
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const saveBtn = document.getElementById('save-btn');
        const newNoteBtn = document.getElementById('new-note-btn');
        const deleteBtn = document.getElementById('delete-btn');
        const recordBtn = document.getElementById('record-btn');
        const stopRecordBtn = document.getElementById('stop-record-btn');
        const recordingsContainer = document.getElementById('recordings-container');

        let currentEditId = null;
        let mediaRecorder;
        let audioChunks = [];

        notesList.addEventListener('click', function (e) {
            const noteItem = e.target.closest('.note-item');
            if (!noteItem) return;

            currentEditId = noteItem.getAttribute('data-id');
            const title = noteItem.getAttribute('data-title');
            const content = noteItem.getAttribute('data-content');

            titleInput.value = title;
            contentInput.value = content;

            // Hapus semua rekaman yang ada
            recordingsContainer.innerHTML = '';
            loadAudioForNote(currentEditId);
        });

        function loadAudioForNote(noteId) {
            fetch('get_audio.php?note_id=' + noteId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(audio => {
                        const audioItem = document.createElement('div');
                        audioItem.classList.add('audio-item', 'flex', 'justify-between', 'items-center');
                        audioItem.innerHTML = ` 
                            <audio controls src="../uploads/${audio.file_name}"></audio>
                            <button class="delete-audio-btn text-red-500" data-id="${audio.id}">Hapus</button>
                            <p class="text-sm text-gray-400">Uploaded: ${audio.created_at}</p>
                        `;
                        recordingsContainer.appendChild(audioItem);
                    });
                });
        }

        saveBtn.addEventListener('click', function () {
            if (titleInput.value.trim() === '') {
                alert('Judul tidak boleh kosong.');
                return;
            }

            const formData = new FormData();
            formData.append('title', titleInput.value);
            formData.append('content', contentInput.value);
            formData.append('action', currentEditId ? 'update' : 'save');
            if (currentEditId) formData.append('edit_id', currentEditId);

            fetch('', { method: 'POST', body: formData })
                .then(() => {
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        });

        newNoteBtn.addEventListener('click', function () {
            titleInput.value = '';
            contentInput.value = '';
            currentEditId = null;
            recordingsContainer.innerHTML = ''; // Hapus semua rekaman
        });

        deleteBtn.addEventListener('click', function () {
            if (!currentEditId) {
                alert('Silakan pilih catatan untuk dihapus.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('edit_id', currentEditId);

            fetch('', { method: 'POST', body: formData })
                .then(() => location.reload())
                .catch(error => console.error('Error:', error));
        });

        recordBtn.addEventListener('click', async () => {
            if (titleInput.value.trim() === '') {
                alert('Silakan isi judul sebelum merekam.');
                return;
            }

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    uploadAudio(audioBlob);
                    audioChunks = [];
                };

                mediaRecorder.start();
                recordBtn.classList.add('hidden');
                stopRecordBtn.classList.remove('hidden');
            } catch (error) {
                alert('Tidak bisa mengakses mikrofon: ' + error.message);
            }
        });

        stopRecordBtn.addEventListener('click', () => {
            mediaRecorder.stop();
            stopRecordBtn.classList.add('hidden');
            recordBtn.classList.remove('hidden');
        });

        async function uploadAudio(audioBlob) {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.webm');
            formData.append('title', titleInput.value);
            formData.append('note_id', currentEditId);  // Kirimkan note_id untuk catatan yang sedang diedit

            try {
                const response = await fetch('', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.status === 'success') {
                    alert(result.message);
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Gagal mengunggah rekaman.');
            }
        }
    </script>
</body>
</html>
