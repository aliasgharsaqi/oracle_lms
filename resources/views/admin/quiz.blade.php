@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
@endpush

@section('content')
<div class="p-6 md:p-8 bg-gray-50 min-h-screen">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Quiz Management</h2>
            <p class="text-sm text-gray-500 mt-1">Create, manage, and track student quizzes.</p>
        </div>
        <button id="create-quiz-btn" class="w-full sm:w-auto bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 flex items-center justify-center gap-2 transition duration-300">
            <svg xmlns="http://www.w.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
            Create New Quiz
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg"><p class="text-sm font-medium text-gray-500">Total Quizzes</p><p id="stat-total" class="text-3xl font-bold text-gray-800">0</p></div>
        <div class="bg-white p-6 rounded-xl shadow-lg"><p class="text-sm font-medium text-gray-500">Published</p><p id="stat-published" class="text-3xl font-bold text-gray-800">0</p></div>
        <div class="bg-white p-6 rounded-xl shadow-lg"><p class="text-sm font-medium text-gray-500">Drafts</p><p id="stat-drafts" class="text-3xl font-bold text-gray-800">0</p></div>
        <div class="bg-white p-6 rounded-xl shadow-lg"><p class="text-sm font-medium text-gray-500">Submissions</p><p class="text-3xl font-bold text-gray-800">256</p></div>
    </div>

    <div id="quizzes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        </div>
</div>


<div id="quiz-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl" id="modal-panel">
        <form id="quiz-form">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-800">Create New Quiz</h3>
                <button type="button" class="close-modal-btn text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-6">
                <input type="hidden" id="quiz-id" name="quizId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="quiz-title" class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                        <input type="text" id="quiz-title" class="w-full rounded-md border-gray-300 shadow-sm" required placeholder="e.g., Chapter 3: Cell Biology">
                    </div>
                    <div>
                        <label for="quiz-subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" id="quiz-subject" class="w-full rounded-md border-gray-300 shadow-sm" required placeholder="e.g., Science">
                    </div>
                    <div>
                        <label for="quiz-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="quiz-status" class="w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div>
                        <label for="quiz-questions" class="block text-sm font-medium text-gray-700 mb-1">Questions</label>
                        <input type="number" id="quiz-questions" class="w-full rounded-md border-gray-300 shadow-sm" required placeholder="e.g., 20">
                    </div>
                    <div>
                        <label for="quiz-duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (Mins)</label>
                        <input type="number" id="quiz-duration" class="w-full rounded-md border-gray-300 shadow-sm" required placeholder="e.g., 30">
                    </div>
                     <div class="md:col-span-2">
                        <label for="quiz-due-date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" id="quiz-due-date" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-xl">
                <button type="button" class="close-modal-btn py-2 px-4 bg-white border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-100">Cancel</button>
                <button type="submit" class="py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Save Quiz</button>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- 1. MOCK DATA & STATE ---
    // In a real app, this data would come from your Laravel backend.
    let quizzes = [
        { id: 1, title: 'Chapter 3: Cell Biology', subject: 'Science', status: 'Published', questions: 20, duration: 30, dueDate: '2025-09-20' },
        { id: 2, title: 'Algebra Basics Quiz', subject: 'Mathematics', status: 'Draft', questions: 15, duration: 25, dueDate: '2025-09-25' }
    ];

    // --- 2. DOM ELEMENT REFERENCES ---
    const quizzesGrid = document.getElementById('quizzes-grid');
    const createQuizBtn = document.getElementById('create-quiz-btn');
    const quizModal = document.getElementById('quiz-modal');
    const quizForm = document.getElementById('quiz-form');
    const modalTitle = document.getElementById('modal-title');
    const closeModalBtns = document.querySelectorAll('.close-modal-btn');

    // --- 3. RENDERING FUNCTIONS ---

    // Function to render all quizzes to the grid
    const renderQuizzes = () => {
        quizzesGrid.innerHTML = ''; // Clear the grid first
        if (quizzes.length === 0) {
            quizzesGrid.innerHTML = `<p class="text-gray-500 md:col-span-3 text-center">No quizzes found. Click 'Create New Quiz' to get started!</p>`;
        }
        
        quizzes.forEach(quiz => {
            const statusClass = quiz.status === 'Published' ? 'text-green-800 bg-green-100' : 'text-gray-800 bg-gray-200';
            const quizCard = `
                <div class="bg-white rounded-xl shadow-lg border border-transparent hover:border-blue-500 hover:shadow-xl transition-all duration-300 flex flex-col">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-gray-800">${quiz.title}</h3>
                            <span class="px-3 py-1 text-xs font-semibold ${statusClass} rounded-full">${quiz.status}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${quiz.subject}</p>
                        <div class="flex justify-between text-sm text-gray-500 mt-4 border-t pt-4">
                            <span><strong class="text-gray-700">${quiz.questions}</strong> Questions</span>
                            <span><strong class="text-gray-700">${quiz.duration}</strong> Mins</span>
                            <span>Due: <strong class="text-gray-700">${new Date(quiz.dueDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</strong></span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 mt-auto flex justify-end gap-2 rounded-b-xl">
                        <button class="edit-btn text-sm font-medium text-gray-600 hover:text-gray-800 py-1 px-3 rounded hover:bg-gray-200" data-id="${quiz.id}">Edit</button>
                        <button class="delete-btn text-sm font-medium text-red-600 hover:text-red-800 py-1 px-3 rounded hover:bg-red-100" data-id="${quiz.id}">Delete</button>
                    </div>
                </div>
            `;
            quizzesGrid.insertAdjacentHTML('beforeend', quizCard);
        });
        updateStats();
    };
    
    // Function to update the top statistics cards
    const updateStats = () => {
        document.getElementById('stat-total').textContent = quizzes.length;
        document.getElementById('stat-published').textContent = quizzes.filter(q => q.status === 'Published').length;
        document.getElementById('stat-drafts').textContent = quizzes.filter(q => q.status === 'Draft').length;
    };

    // --- 4. MODAL & FORM HANDLING ---

    // Open the modal
    const openModal = (mode = 'create', quizId = null) => {
        quizForm.reset();
        if (mode === 'create') {
            modalTitle.textContent = 'Create New Quiz';
            document.getElementById('quiz-id').value = '';
        } else {
            modalTitle.textContent = 'Edit Quiz';
            const quiz = quizzes.find(q => q.id === quizId);
            if (quiz) {
                document.getElementById('quiz-id').value = quiz.id;
                document.getElementById('quiz-title').value = quiz.title;
                document.getElementById('quiz-subject').value = quiz.subject;
                document.getElementById('quiz-status').value = quiz.status;
                document.getElementById('quiz-questions').value = quiz.questions;
                document.getElementById('quiz-duration').value = quiz.duration;
                document.getElementById('quiz-due-date').value = quiz.dueDate;
            }
        }
        quizModal.classList.remove('hidden');
    };

    // Close the modal
    const closeModal = () => {
        quizModal.classList.add('hidden');
    };

    // Handle form submission
    quizForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const quizId = document.getElementById('quiz-id').value;
        const quizData = {
            title: document.getElementById('quiz-title').value,
            subject: document.getElementById('quiz-subject').value,
            status: document.getElementById('quiz-status').value,
            questions: parseInt(document.getElementById('quiz-questions').value),
            duration: parseInt(document.getElementById('quiz-duration').value),
            dueDate: document.getElementById('quiz-due-date').value
        };

        if (quizId) { // Editing an existing quiz
            const index = quizzes.findIndex(q => q.id == quizId);
            quizzes[index] = { ...quizzes[index], ...quizData };
        } else { // Creating a new quiz
            quizData.id = Date.now(); // Simple unique ID
            quizzes.push(quizData);
        }
        
        renderQuizzes();
        closeModal();
    });

    // --- 5. EVENT LISTENERS ---

    // Open modal to create a quiz
    createQuizBtn.addEventListener('click', () => openModal('create'));

    // Close modal with buttons or by clicking the background
    closeModalBtns.forEach(btn => btn.addEventListener('click', closeModal));
    quizModal.addEventListener('click', (e) => {
        if (e.target === quizModal) closeModal();
    });
    
    // Handle Edit and Delete clicks using Event Delegation
    quizzesGrid.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (editBtn) {
            const quizId = parseInt(editBtn.dataset.id);
            openModal('edit', quizId);
        }
        
        if (deleteBtn) {
            const quizId = parseInt(deleteBtn.dataset.id);
            if (confirm('Are you sure you want to delete this quiz?')) {
                quizzes = quizzes.filter(q => q.id !== quizId);
                renderQuizzes();
            }
        }
    });

    // --- 6. INITIAL RENDER ---
    // Render the initial list of quizzes when the page loads
    renderQuizzes();
});
</script>
@endpush