@extends('layouts.admin')

@section('title', 'Quiz Management')
@section('page-title', 'Quiz Management')

@push('styles')
    {{-- Add any necessary styles here --}}
@endpush

@section('content')
    {{-- Assuming a CSRF token meta tag is in layouts.admin.blade.php --}}

    <div class="p-6 md:p-8 bg-gray-50 min-h-screen">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Quiz Management</h2>
                <p class="text-sm text-gray-500 mt-1">Create, manage, and track student quizzes.</p>
            </div>
            <button id="create-quiz-btn"
                class="w-full sm:w-auto bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 flex items-center justify-center gap-2 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Create New Quiz
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <p class="text-sm font-medium text-gray-500">Total Quizzes</p>
                <p id="stat-total" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <p class="text-sm font-medium text-gray-500">Published</p>
                <p id="stat-published" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <p class="text-sm font-medium text-gray-500">Drafts</p>
                <p id="stat-drafts" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <p class="text-sm font-medium text-gray-500">Submissions</p>
                <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
        </div>

        {{-- NEW: Class Filter Dropdown --}}
        <div class="flex justify-end mb-6">
            <div class="w-full sm:w-64">
                <label for="class-filter" class="sr-only">Filter by Class</label>
                {{-- FIX: Removed the invalid inline handler onchange="fetchQuizzes()" --}}
                <select id="class-filter"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- Filter by Class (All) --</option>
                    @if (isset($classes))
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div id="quizzes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Quizzes will be rendered here by JavaScript --}}
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

                        {{-- NEW FIELD: Class Assignment --}}
                        <div class="md:col-span-2">
                            <label for="quiz-class" class="block text-sm font-medium text-gray-700 mb-1">Assign
                                Class</label>
                            <select id="quiz-class" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">-- Select Class --</option>
                                @if (isset($classes))
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="quiz-title" class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                            <input type="text" id="quiz-title" class="w-full rounded-md border-gray-300 shadow-sm" required
                                placeholder="e.g., Chapter 3: Cell Biology">
                        </div>
                        <div>
                            <label for="quiz-subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" id="quiz-subject" class="w-full rounded-md border-gray-300 shadow-sm"
                                required placeholder="e.g., Science">
                        </div>
                        <div>
                            <label for="quiz-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="quiz-status" class="w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="Published">Published</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div>
                            <label for="quiz-questions"
                                class="block text-sm font-medium text-gray-700 mb-1">Questions</label>
                            <input type="number" id="quiz-questions" class="w-full rounded-md border-gray-300 shadow-sm"
                                required placeholder="e.g., 20">
                        </div>
                        <div>
                            <label for="quiz-duration" class="block text-sm font-medium text-gray-700 mb-1">Duration
                                (Mins)</label>
                            <input type="number" id="quiz-duration" class="w-full rounded-md border-gray-300 shadow-sm"
                                required placeholder="e.g., 30">
                        </div>
                        <div class="md:col-span-2">
                            <label for="quiz-due-date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" id="quiz-due-date" class="w-full rounded-md border-gray-300 shadow-sm"
                                required>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-xl">
                    <button type="button"
                        class="close-modal-btn py-2 px-4 bg-white border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-100">Cancel</button>
                    <button type="submit"
                        class="py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Save
                        Quiz</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- 1. STATE & API ROUTES ---
            let quizzes = [];
            const API_URL = '{{ route('api.quizzes.all') }}';
            const STORE_URL = '{{ route('api.quizzes.store') }}';
            const DELETE_URL_TEMPLATE = '{{ route('api.quizzes.destroy', ['quiz' => '__ID__']) }}';
            const CLASSES = @json($classes ?? []); // Embed PHP classes data into JS

            // --- 2. DOM ELEMENT REFERENCES ---
            const quizzesGrid = document.getElementById('quizzes-grid');
            const createQuizBtn = document.getElementById('create-quiz-btn');
            const quizModal = document.getElementById('quiz-modal');
            const quizForm = document.getElementById('quiz-form');
            const modalTitle = document.getElementById('modal-title');
            const closeModalBtns = document.querySelectorAll('.close-modal-btn');
            const classFilter = document.getElementById('class-filter'); // Filter dropdown
            const quizClassInput = document.getElementById('quiz-class'); // Modal dropdown

            // Helper to get CSRF token
            const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

            // --- 3. RENDERING FUNCTIONS ---

            const renderQuizzes = () => {
                quizzesGrid.innerHTML = '';
                if (quizzes.length === 0) {
                    quizzesGrid.innerHTML = `<p class="text-gray-500 md:col-span-3 text-center">No quizzes found matching the criteria. Click 'Create New Quiz' to get started!</p>`;
                }

                quizzes.forEach(quiz => {
                    const statusClass = quiz.status === 'Published' ? 'text-green-800 bg-green-100' : 'text-gray-800 bg-gray-200';
                    const dueDateDisplay = quiz.due_date ? new Date(quiz.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';

                    // Find the Class Name for display
                    const className = CLASSES.find(c => c.id == quiz.class_id)?.name || 'N/A';

                    const quizCard = `
                    <div class="bg-white rounded-xl shadow-lg border border-transparent hover:border-blue-500 hover:shadow-xl transition-all duration-300 flex flex-col">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg font-bold text-gray-800">${quiz.title}</h3>
                                <span class="px-3 py-1 text-xs font-semibold ${statusClass} rounded-full">${quiz.status}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Class: <strong>${className}</strong> | Subject: ${quiz.subject}</p>
                            <div class="flex justify-between text-sm text-gray-500 mt-4 border-t pt-4">
                                <span><strong class="text-gray-700">${quiz.questions}</strong> Questions</span>
                                <span><strong class="text-gray-700">${quiz.duration}</strong> Mins</span>
                                <span>Due: <strong class="text-gray-700">${dueDateDisplay}</strong></span>
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
            };

            // Function to update the top statistics cards (no change)
            const updateStats = (stats) => {
                document.getElementById('stat-total').textContent = stats.total;
                document.getElementById('stat-published').textContent = stats.published;
                document.getElementById('stat-drafts').textContent = stats.drafts;
            };

            // --- 4. DATA FETCHING (Updated for filter) ---
            const fetchQuizzes = async () => {
                const selectedClassId = classFilter.value;
                let url = API_URL;

                if (selectedClassId) {
                    url += `?class_id=${encodeURIComponent(selectedClassId)}`;
                }

                // TEMPORARY DEBUG LINE
                console.log(`Fetching quizzes for URL: ${url}`);

                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    quizzes = data.quizzes;
                    updateStats(data.stats);
                    renderQuizzes();
                } catch (error) {
                    console.error('Error fetching quizzes:', error);
                    quizzesGrid.innerHTML = `<p class="text-red-500 md:col-span-3 text-center">Failed to load quizzes. Check console for details.</p>`;
                }
            };

            // --- 5. MODAL & FORM HANDLING ---

            // Open the modal (Updated to load class_id)
            const openModal = (mode = 'create', quizId = null) => {
                quizForm.reset();
                document.getElementById('quiz-id').value = '';

                if (mode === 'create') {
                    modalTitle.textContent = 'Create New Quiz';
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
                        document.getElementById('quiz-due-date').value = quiz.due_date ? quiz.due_date.substring(0, 10) : '';

                        // NEW: Load assigned class ID
                        quizClassInput.value = quiz.class_id || '';
                    }
                }
                quizModal.classList.remove('hidden');
            };

            // Close the modal (no change)
            const closeModal = () => {
                quizModal.classList.add('hidden');
            };

            // Handle form submission (Updated to include class_id)
            quizForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = {
                    quizId: document.getElementById('quiz-id').value || null,
                    class_id: quizClassInput.value, // NEW: Get class ID
                    title: document.getElementById('quiz-title').value,
                    subject: document.getElementById('quiz-subject').value,
                    status: document.getElementById('quiz-status').value,
                    questions: document.getElementById('quiz-questions').value,
                    duration: document.getElementById('quiz-duration').value,
                    dueDate: document.getElementById('quiz-due-date').value
                };

                try {
                    const response = await fetch(STORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        const error = await response.json();
                        console.error('Submission failed:', error);
                        alert('Save failed: ' + (error.message || 'Server error.'));
                        return;
                    }

                    closeModal();
                    fetchQuizzes(); // Reload data with current filter applied
                } catch (error) {
                    console.error('Network error during submission:', error);
                    alert('Network error. Check your connection.');
                }
            });

            // Handle Delete (no change)
            const deleteQuiz = async (quizId) => {
                if (!confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
                    return;
                }

                const url = DELETE_URL_TEMPLATE.replace('__ID__', quizId);

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                    });

                    if (response.status === 204) {
                        fetchQuizzes(); // Reload data
                    } else {
                        alert('Delete failed. Quiz not found or server error.');
                    }
                } catch (error) {
                    console.error('Network error during deletion:', error);
                    alert('Network error. Check your connection.');
                }
            };

            // --- 6. EVENT LISTENERS ---

            createQuizBtn.addEventListener('click', () => openModal('create'));
            closeModalBtns.forEach(btn => btn.addEventListener('click', closeModal));
            quizModal.addEventListener('click', (e) => {
                if (e.target === quizModal) closeModal();
            });

            quizzesGrid.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.edit-btn');
                const deleteBtn = e.target.closest('.delete-btn');

                if (editBtn) {
                    const quizId = parseInt(editBtn.dataset.id);
                    openModal('edit', quizId);
                }

                if (deleteBtn) {
                    const quizId = parseInt(deleteBtn.dataset.id);
                    deleteQuiz(quizId);
                }
            });

            // FIX: Bind the fetchQuizzes function to the change event of the filter dropdown
            classFilter.addEventListener('change', fetchQuizzes);

            // --- 7. INITIAL RENDER ---
            fetchQuizzes();
        });
    </script>
@endpush