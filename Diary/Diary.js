// Previous JavaScript code remains, adding new functionality

// DOM Elements
const notesSidebar = document.querySelector('.notes-sidebar');
const notesList = document.querySelector('.notes-list');
let currentNoteId = null;

// Show/Hide Notes Sidebar
function toggleNotesSidebar() {
    notesSidebar.classList.toggle('active');
}

// Load Notes List
async function loadNotesList() {
    try {
        const response = await fetch('api/get_notes.php');
        const notes = await response.json();
        
        notesList.innerHTML = '';
        notes.forEach(note => {
            const noteCard = createNoteCard(note);
            notesList.appendChild(noteCard);
        });
    } catch (error) {
        console.error('Error loading notes:', error);
    }
}

// Create Note Card Element
function createNoteCard(note) {
    const card = document.createElement('div');
    card.className = 'note-card';
    card.innerHTML = `
        <h3>${note.title || 'Untitled'}</h3>
        <p>${note.content.substring(0, 50)}${note.content.length > 50 ? '...' : ''}</p>
        <div class="timestamp">${formatDate(note.created_at)}</div>
    `;
    
    card.addEventListener('click', () => loadNote(note.id));
    return card;
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

// Load Note Content
async function loadNote(noteId) {
    try {
        const response = await fetch(`api/get_note.php?id=${noteId}`);
        const note = await response.json();
        
        currentNoteId = note.id;
        titleInput.value = note.title;
        noteInput.value = note.content;
        
        if (note.audio_url) {
            audioUrl = note.audio_url;
            createAudioElement();
        }
    } catch (error) {
        console.error('Error loading note:', error);
    }
}

// Save Note Function (Updated)
async function saveNote() {
    const noteData = {
        id: currentNoteId,
        title: titleInput.value,
        content: noteInput.value,
        audio_url: audioUrl
    };
    
    try {
        const formData = new FormData();
        Object.keys(noteData).forEach(key => {
            if (noteData[key]) formData.append(key, noteData[key]);
        });
        
        if (audioBlob) {
            formData.append('audio_file', audioBlob, 'recording.wav');
        }
        
        const response = await fetch('api/save_note.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSaveSuccess();
            currentNoteId = result.note_id;
            loadNotesList();
            toggleNotesSidebar();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error saving note:', error);
        showSaveError();
    }
}

// New Note Function
function createNewNote() {
    currentNoteId = null;
    titleInput.value = '';
    noteInput.value = '';
    audioUrl = null;
    audioElement = null;
    audioBlob = null;
    audioChunks = [];
    timeDisplay.textContent = '0:00';
    progressBar.style.width = '0%';
}

// Save Feedback
function showSaveSuccess() {
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saved!';
    saveBtn.style.backgroundColor = '#4CAF50';
    
    setTimeout(() => {
        saveBtn.textContent = originalText;
        saveBtn.style.backgroundColor = '';
    }, 2000);
}

function showSaveError() {
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Error!';
    saveBtn.style.backgroundColor = '#f44336';
    
    setTimeout(() => {
        saveBtn.textContent = originalText;
        saveBtn.style.backgroundColor = '';
    }, 2000);
}

// Event Listeners
document.querySelector('.new-note-btn').addEventListener('click', createNewNote);
saveBtn.addEventListener('click', () => {
    saveNote();
    toggleNotesSidebar();
});

// Initialize
loadNotesList();