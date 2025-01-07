<?php
include("../conn.php");
session_start();

// Ambil data catatan diary dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT id, title, content, LEFT(title, 50) AS snippet FROM diarys WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menangani unggahan audio
    if (isset($_FILES['audio'])) {
        if (empty($_POST['title'])) {
            echo json_encode(["status" => "error", "message" => "Anda harus menyimpan catatan dengan judul sebelum merekam."]);
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
            echo json_encode(["status" => "success", "message" => "Audio berhasil diunggah."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal mengunggah audio."]);
        }
        exit;
    }

    // Menangani aksi untuk menyimpan, memperbarui, atau menghapus catatan
    $action = $_POST['action'] ?? null;
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $editId = $_POST['edit_id'] ?? null;

    if ($action === 'save') {
        if (empty($title)) {
            echo json_encode(["status" => "error", "message" => "Judul diperlukan untuk menyimpan catatan."]);
            exit;
        }
        $sql = "INSERT INTO diarys (title, content, user_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $title, $content, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "id" => $stmt->insert_id, "title" => $title, "content" => $content]);
    } elseif ($action === 'update' && !empty($editId)) {
        if (empty($title)) {
            echo json_encode(["status" => "error", "message" => "Judul diperlukan untuk memperbarui catatan."]);
            exit;
        }
        $sql = "UPDATE diarys SET title = ?, content = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $title, $content, $editId, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "id" => $editId, "title" => $title, "content" => $content]);
    } elseif ($action === 'delete' && !empty($editId)) {
        $sql = "DELETE FROM diarys WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $editId, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "success"]);
    }

    // Menangani penghapusan audio tertentu
    if ($action === 'delete_audio' && !empty($_POST['audio_id'])) {
        $audio_id = $_POST['audio_id'];
        $sql = "DELETE FROM audio_notes WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $audio_id, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "Rekaman audio berhasil dihapus."]);
        exit;
    }

    // Menangani penghapusan semua audio untuk catatan tertentu
    if ($action === 'delete_all_audio' && !empty($editId)) {
        $sql = "DELETE FROM audio_notes WHERE note_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $editId, $user_id);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "Semua rekaman audio berhasil dihapus."]);
        exit;
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindshareHub - Diary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/Dashboard/Dashboard.css">
</head>
<body class="bg-[#1a1b26] text-white flex">
    <!-- Sidebar -->
    <div>
        <?php include('../slicing/sidebar.php'); ?>
    </div>

    <div class="main-content flex-1 p-5">
        <div class="header flex items-center justify-between mb-5">
            <div class="title-container flex-grow mr-2">
                <input id="title" type="text" class="title-input w-full p-3 bg-[#2d2d3d] text-white text-lg rounded outline-none" placeholder="Judul" value="">
            </div>
            <div class="actions flex space-x-2">
                <button id="new-note-btn" class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <span class="mr-2">+</span> Catatan Baru
                </button>
                <button id="save-btn" class="save-btn px-4 py-3 bg-green-500 text-white rounded hover:bg-green-600 transition">Simpan</button>
                <button id="delete-btn" class="delete-btn px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition">Hapus</button>
            </div>
        </div>

        <textarea id="content" class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>

        <!-- Voice Note Section -->
        <div class="voice-note mt-5">
            <button id="record-btn" class="px-4 py-3 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                Mulai Merekam
            </button>
            <button id="stop-record-btn" class="px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition hidden">
                Hentikan Rekaman
            </button>
            <button id="delete-all-audio-btn" class="px-4 py-3 bg-red-500 text-white rounded hover:bg-red-600 transition hidden">
                Hapus Semua Rekaman
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
        const deleteAllAudioBtn = document.getElementById('delete-all-audio-btn');
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
                    recordingsContainer.innerHTML = ''; // Hapus rekaman sebelumnya
                    data.forEach(audio => {
                        const audioItem = document.createElement('div');
                        audioItem.classList.add('audio-item', 'flex', 'justify-between', 'items-center');
                        audioItem.innerHTML = ` 
                            <audio controls src="../uploads/${audio.file_name}"></audio>
                            <button class="delete-audio-btn text-red-500" data-id="${audio.id}">Hapus</button>
                            <p class="text-sm text-gray-400">Diunggah: ${audio.created_at}</p>
                        `;
                        recordingsContainer.appendChild(audioItem);
                    });
                    deleteAllAudioBtn.classList.remove('hidden'); // Tampilkan tombol hapus semua
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
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        alert('Catatan berhasil disimpan.');
                        if (!currentEditId) {
                            addNoteToSidebar(result);
                        } else {
                            updateNoteInSidebar(result);
                        }
                        clearInputs(); // Kosongkan input setelah menyimpan
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        newNoteBtn.addEventListener('click', function () {
            clearInputs(); // Kosongkan input untuk catatan baru
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
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        alert('Catatan berhasil dihapus.');
                        notesList.querySelector(`.note-item[data-id="${currentEditId}"]`).remove();
                        clearInputs(); // Kosongkan input setelah menghapus
                    }
                })
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
                alert(result.message);
                loadAudioForNote(currentEditId); // Muat ulang audio untuk catatan yang diperbarui
            } catch (error) {
                alert('Gagal mengunggah rekaman.');
            }
        }

        function addNoteToSidebar(note) {
            const noteItem = document.createElement('div');
            noteItem.classList.add('note-item', 'p-4', 'bg-[#1e1f2e]', 'rounded', 'cursor-pointer');
            noteItem.setAttribute('data-id', note.id);
            noteItem.setAttribute('data-title', note.title);
            noteItem.setAttribute('data-content', note.content);
            noteItem.innerHTML = `
                <h3 class="note-title text-base font-bold mb-1">${note.title}</h3>
                <p class="note-snippet text-sm text-[#8e8ea0]">${note.content}</p>
            `;
            notesList.appendChild(noteItem);
        }

        function updateNoteInSidebar(note) {
            const noteItem = notesList.querySelector(`.note-item[data-id="${note.id}"]`);
            noteItem.setAttribute('data-title', note.title);
            noteItem.setAttribute('data-content', note.content);
            noteItem.querySelector('.note-title').textContent = note.title;
            noteItem.querySelector('.note-snippet').textContent = note.content;
        }

        function clearInputs() {
            titleInput.value = '';
            contentInput.value = '';
            currentEditId = null;
            recordingsContainer.innerHTML = ''; // Kosongkan rekaman
        }

        // Menangani penghapusan audio tertentu
        recordingsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-audio-btn')) {
                const audioId = e.target.getAttribute('data-id');

                const formData = new FormData();
                formData.append('audio_id', audioId);
                formData.append('action', 'delete_audio'); // Tambahkan action untuk menghapus audio

                fetch('', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            alert('Rekaman audio berhasil dihapus.');
                            loadAudioForNote(currentEditId); // Muat ulang audio untuk catatan yang diperbarui
                        } else {
                            alert(result.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // Menangani penghapusan semua rekaman
        deleteAllAudioBtn.addEventListener('click', function () {
            if (!currentEditId) {
                alert('Silakan pilih catatan untuk menghapus semua rekaman.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_all_audio');
            formData.append('edit_id', currentEditId);

            fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        alert('Semua rekaman berhasil dihapus.');
                        recordingsContainer.innerHTML = ''; // Kosongkan tampilan rekaman
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>