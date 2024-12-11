<?php
include("../conn.php");
session_start();

// Ambil data catatan dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT id, content, 
        LEFT(content, 50) AS snippet FROM posts WHERE user_id =
        $user_id ORDER BY created_at DESC";
        $result = $conn->query($query);
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
                <button class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
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
        <div class="notes-list flex-grow overflow-y-auto space-y-3">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]">
                    <h3 class="note-title text-base font-bold mb-1"><?php echo htmlspecialchars($row['snippet']); ?>...</h3>
                    <p class="note-snippet text-sm text-[#8e8ea0]"> <?php echo htmlspecialchars($row['content']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        const recordBtn = document.getElementById('record-btn');
        const recordStatus = document.getElementById('record-status');
        const recordingsContainer = document.getElementById('recordings-container');

        let mediaRecorder;
        let audioChunks = [];

        recordBtn.addEventListener('click', function () {
            if (!mediaRecorder || mediaRecorder.state === 'inactive') {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        mediaRecorder = new MediaRecorder(stream);

                        mediaRecorder.start();
                        recordStatus.classList.remove('hidden');
                        recordBtn.textContent = '⏹ Stop Recording';

                        mediaRecorder.ondataavailable = event => {
                            audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = () => {
                            recordStatus.classList.add('hidden');
                            recordBtn.textContent = '🎤 Start Recording';

                            const audioBlob = new Blob(audioChunks, { type: 'audio/mpeg' });
                            audioChunks = [];

                            const audioUrl = URL.createObjectURL(audioBlob);
                            const audioElement = document.createElement('audio');
                            audioElement.src = audioUrl;
                            audioElement.controls = true;

                            const deleteButton = document.createElement('button');
                            deleteButton.textContent = '🗑️ Delete';
                            deleteButton.className = 'ml-2 px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition';

                            const recordingItem = document.createElement('div');
                            recordingItem.className = 'recording-item flex items-center';
                            recordingItem.appendChild(audioElement);
                            recordingItem.appendChild(deleteButton);

                            recordingsContainer.appendChild(recordingItem);

                            deleteButton.addEventListener('click', () => {
                                recordingsContainer.removeChild(recordingItem);
                                alert('Recording deleted successfully.');
                                // TODO: Optionally, remove the recording from the server
                            });

                            // TODO: Upload audioBlob to the server if needed
                        };
                    })
                    .catch(error => {
                        alert('Microphone access denied.');
                        console.error(error);
                    });
            } else if (mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
        });
    </script>
</body>
</html>
