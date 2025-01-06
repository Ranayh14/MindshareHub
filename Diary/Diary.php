<?php
include("../conn.php");
session_start();

// Ambil data catatan diary dari database
$user_id = $_SESSION['user_id'];  // Pastikan session user_id sudah diset sebelumnya
$query = "SELECT id, title, content, LEFT(title, 50) AS snippet FROM diarys WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($query);

$userId = $user_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $editId = $_POST['edit_id']; 

    if ($action == 'save') {
        // Menyimpan catatan baru
        $sql = "INSERT INTO diarys (title, content, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $title, $content, $userId);
        $stmt->execute();
    } elseif ($action == 'update' && !empty($editId)) {
        // Memperbarui catatan yang ada
        $sql = "UPDATE diarys SET title = ?, content = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $title, $content, $editId, $userId);
        $stmt->execute();
    } elseif ($action == 'delete' && !empty($editId)) {
        // Menghapus catatan
        $sql = "DELETE FROM diarys WHERE id = ? AND user_id = ?";
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
        <div class="notes-list flex-grow overflow-y-auto space-y-3" id="notes-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]" data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-content="<?php echo htmlspecialchars($row['content']); ?>">
                    <h3 class="note-title text-base font-bold mb-1"> <?php echo htmlspecialchars($row['snippet']); ?>...</h3>
                    <p class="note-snippet text-sm text-[#8e8ea0]"> <?php echo htmlspecialchars($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="save-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-[#2d2d3d] p-6 rounded shadow-md text-center">
            <p class="text-white mb-4">Are you sure you want to save this note?</p>
            <div class="flex justify-center space-x-4">
                <button id="cancel-save-btn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">No</button>
                <button id="confirm-save-btn" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Yes</button>
            </div>
        </div>
    </div>

    <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-[#2d2d3d] p-6 rounded shadow-md text-center">
            <p class="text-white mb-4">Are you sure you want to delete this note?</p>
            <div class="flex justify-center space-x-4">
                <button id="cancel-btn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">No</button>
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Yes</button>
            </div>
        </div>
    </div>

    <div id="delete-recording-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-[#2d2d3d] p-6 rounded shadow-md text-center">
            <p class="text-white mb-4">Are you sure you want to delete this recording?</p>
            <div class="flex justify-center space-x-4">
                <button id="cancel-delete-recording-btn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">No</button>
                <button id="confirm-delete-recording-btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Yes</button>
            </div>
        </div>
    </div>

    <script>
        const notesList = document.getElementById('notes-list');
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const saveBtn = document.getElementById('save-btn');
        const newNoteBtn = document.getElementById('new-note-btn');
        const deleteBtn = document.getElementById('delete-btn');
        const saveModal = document.getElementById('save-modal');
        const deleteModal = document.getElementById('delete-modal');
        const deleteRecordingModal = document.getElementById('delete-recording-modal');
        const cancelSaveBtn = document.getElementById('cancel-save-btn');
        const confirmSaveBtn = document.getElementById('confirm-save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const cancelDeleteRecordingBtn = document.getElementById('cancel-delete-recording-btn');
        const confirmDeleteRecordingBtn = document.getElementById('confirm-delete-recording-btn');

        let currentEditId = null;
        let currentRecording = null;

        // Handle note selection
        notesList.addEventListener('click', function (e) {
            const noteItem = e.target.closest('.note-item');
            if (!noteItem) return;

            currentEditId = noteItem.getAttribute('data-id');
            const title = noteItem.getAttribute('data-title');
            const content = noteItem.getAttribute('data-content');

            titleInput.value = title;
            contentInput.value = content;
        });

        // Handle save button
        saveBtn.addEventListener('click', function () {
            if (titleInput.value.trim() === '' || contentInput.value.trim() === '') {
                alert('Title and content cannot be empty.');
                return;
            }

            saveModal.classList.remove('hidden');
        });

        // Confirm save note
        confirmSaveBtn.addEventListener('click', function () {
            const title = titleInput.value;
            const content = contentInput.value;

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

            saveModal.classList.add('hidden');
        });

        cancelSaveBtn.addEventListener('click', function () {
            saveModal.classList.add('hidden');
        });

        // New note
        newNoteBtn.addEventListener('click', function () {
            titleInput.value = '';
            contentInput.value = '';
            currentEditId = null;
        });

        // Delete note
        deleteBtn.addEventListener('click', function () {
            if (!currentEditId) {
                alert('Please select a note to delete.');
                return;
            }

            deleteModal.classList.remove('hidden');
        });

        cancelBtn.addEventListener('click', function () {
            deleteModal.classList.add('hidden');
        });

        confirmDeleteBtn.addEventListener('click', function () {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('edit_id', currentEditId);

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(() => {
                    alert('Note deleted successfully!');
                    location.reload();
                })
                .catch(error => console.error('Error:', error));

            deleteModal.classList.add('hidden');
        });

        // Audio Recording Feature
        let mediaRecorder;
        let audioChunks = [];
        const recordBtn = document.getElementById('record-btn');
        const stopRecordBtn = document.getElementById('stop-record-btn');
        const recordingsContainer = document.getElementById('recordings-container');

        // Start recording
        recordBtn.addEventListener('click', function () {
            if (navigator.mediaDevices) {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        mediaRecorder = new MediaRecorder(stream);
                        mediaRecorder.ondataavailable = event => {
                            audioChunks.push(event.data);
                        };
                        mediaRecorder.onstop = () => {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                            const audioUrl = URL.createObjectURL(audioBlob);
                            const audioElement = document.createElement('audio');
                            audioElement.controls = true;
                            audioElement.src = audioUrl;

                            // Add delete button for each recording
                            const deleteBtn = document.createElement('button');
                            deleteBtn.textContent = 'Delete Recording';
                            deleteBtn.classList.add('px-4', 'py-2', 'bg-red-500', 'text-white', 'rounded', 'hover:bg-red-600', 'transition');
                            deleteBtn.onclick = () => {
                                currentRecording = audioElement;
                                deleteRecordingModal.classList.remove('hidden');
                                deleteBtn.remove();
                            };

                            const recordingContainer = document.createElement('div');
                            recordingContainer.classList.add('flex', 'items-center', 'space-x-4'); // Flex container for audio and delete button
                            recordingContainer.appendChild(audioElement);
                            recordingContainer.appendChild(deleteBtn);
                            recordingsContainer.appendChild(recordingContainer);

                            // Make delete button visible only after recording is available
                            deleteBtn.classList.remove('hidden');

                            audioChunks = []; // Reset after each recording
                        };

                        mediaRecorder.start();
                        recordBtn.classList.add('hidden');
                        stopRecordBtn.classList.remove('hidden');
                    })
                    .catch(err => console.error("Error accessing media devices.", err));
            }
        });

        // Stop recording
        stopRecordBtn.addEventListener('click', function () {
            mediaRecorder.stop();
            stopRecordBtn.classList.add('hidden');
            recordBtn.classList.remove('hidden');
        });

        // Handle delete recording
        confirmDeleteRecordingBtn.addEventListener('click', function () {
            if (currentRecording) {
                currentRecording.remove();
            }
            deleteRecordingModal.classList.add('hidden');
        });

        cancelDeleteRecordingBtn.addEventListener('click', function () {
            deleteRecordingModal.classList.add('hidden');
        });
    </script>
</body>
</html>
