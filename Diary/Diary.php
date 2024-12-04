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
                <input type="text" class="title-input w-full p-3 bg-[#2d2d3d] text-white text-lg rounded outline-none" placeholder="Judul" value="">
            </div>
            <div class="actions flex space-x-2">
                <button class="new-note-btn px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    <span class="mr-2">+</span> New notes
                </button>
                <button class="save-btn px-4 py-3 bg-green-500 text-white rounded hover:bg-green-600 transition">Save</button>
            </div>
        </div>

        <textarea class="note-input w-full h-48 p-5 bg-[#2d2d3d] text-white text-base rounded outline-none resize-none mb-5" placeholder="Tulis disini..."></textarea>
    </div>

    <div class="notes-sidebar w-72 bg-[#13141f] h-screen p-5 flex flex-col">
        <div class="notes-list flex-grow overflow-y-auto space-y-3">
            <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]">
                <h3 class="note-title text-base font-bold mb-1">Notes...</h3>
                <p class="note-snippet text-sm text-[#8e8ea0]">Hari ini benar-benar melelahkan. Sejak pagi, kuliah...</p>
            </div>
            <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]">
                <h3 class="note-title text-base font-bold mb-1">Kebahagiaan Sederhana</h3>
                <p class="note-snippet text-sm text-[#8e8ea0]">Hari ini aku menemukan kebahagiaan dalam hal kecil...</p>
            </div>
            <div class="note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]">
                <h3 class="note-title text-base font-bold mb-1">Rencana Masa Depan</h3>
                <p class="note-snippet text-sm text-[#8e8ea0]">Aku mulai memikirkan rencana untuk masa depanku...</p>
            </div>
        </div>
    </div>

    <script src="Diary.js"></script>
</body>
</html>
