let notes = [];
let editIndex = -1;

document.getElementById('new-note-btn').addEventListener('click', () => {
    document.getElementById('title').value = '';
    document.getElementById('note-input').value = '';
    editIndex = -1; // Reset edit index for new note
});

document.getElementById('save-btn').addEventListener('click', () => {
    const title = document.getElementById('title').value;
    const content = document.getElementById('note-input').value;

    if (editIndex === -1) {
        // Add new note
        notes.push({ title, content });
    } else {
        // Edit existing note (only once)
        notes[editIndex].title = title;
        notes[editIndex].content = content;
        editIndex = -1; // Reset after editing
    }

    renderNotes();
    clearInputs();
});

function clearInputs() {
    document.getElementById('title').value = '';
    document.getElementById('note-input').value = '';
}

function renderNotes() {
    const notesList = document.getElementById('notes-list');
    notesList.innerHTML = '';

    notes.forEach((note, index) => {
        const noteItem = document.createElement('div');
        noteItem.className = 'note-item p-4 bg-[#1e1f2e] rounded transition cursor-pointer hover:bg-[#2d2d3d]';
        noteItem.innerHTML = `
            <h3 class="note-title text-base font-bold mb-1">${note.title}</h3>
            <p class="note-snippet text-sm text-[#8e8ea0]">${note.content}</p>
            <button onclick="editNote(${index})" class="edit-btn text-blue-400">Edit</button>
            <button onclick="deleteNote(${index})" class="delete-btn text-red-400">Delete</button>
        `;
        notesList.appendChild(noteItem);
    });
}

function editNote(index) {
    const note = notes[index];
    document.getElementById('title').value = note.title;
    document.getElementById('note-input').value = note.content;
    editIndex = index; // Set index to edit
}

function deleteNote(index) {
    notes.splice(index, 1);
    renderNotes();
}