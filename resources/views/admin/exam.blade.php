@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
@endpush

@section('content')
<div class="p-6 md:p-8 bg-gray-50 min-h-screen">

    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Exam Management</h2>
            <p class="text-sm text-gray-500 mt-1">Organize, create, and monitor all school examinations.</p>
        </div>
        <div class="flex items-center gap-4 w-full sm:w-auto">
             <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" id="searchInput" placeholder="Search exams..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button id="createExamBtn" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 flex items-center gap-2 transition duration-300 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Create Exam
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
             <div>
                <p class="text-sm font-medium text-gray-500">Total Exams</p>
                <p id="total-exams-stat" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                 <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Active / Live</p>
                <p id="active-exams-stat" class="text-3xl font-bold text-gray-800">0</p>
            </div>
             <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M12 6v6l4 2" /></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Upcoming</p>
                <p id="upcoming-exams-stat" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
               <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p id="completed-exams-stat" class="text-3xl font-bold text-gray-800">0</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Exam Title</th>
                        <th scope="col" class="px-6 py-4">Subject</th>
                        <th scope="col" class="px-6 py-4">Class</th>
                        <th scope="col" class="px-6 py-4">Date</th>
                        <th scope="col" class="px-6 py-4">Duration</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="exam-table-body">
                    </tbody>
            </table>
        </div>
    </div>
</div>


<div id="examModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4" id="modal-panel">
        
        <form id="examForm">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Create New Exam</h3>
                <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="p-6">
                <input type="hidden" id="examId" name="examId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="exam_title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title</label>
                        <input type="text" name="exam_title" id="exam_title" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <select id="subject" name="subject" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Subject</option>
                            <option>Mathematics</option><option>Physics</option><option>Chemistry</option><option>English</option><option>Biology</option>
                        </select>
                    </div>
                    <div>
                        <label for="class_grade" class="block text-sm font-medium text-gray-700 mb-1">Class / Grade</label>
                        <select id="class_grade" name="class_grade" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                             <option value="">Select Class</option>
                            <option>Grade 9 - A</option><option>Grade 9 - B</option><option>Grade 10 - A</option><option>Grade 11 - A</option><option>Grade 12 - B</option>
                        </select>
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                         <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option>Upcoming</option><option>Active</option><option>Completed</option><option>Draft</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 rounded-b-xl">
                <button type="button" id="cancelBtn" class="py-2 px-4 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="py-2 px-4 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Save Exam</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- MOCK DATA (This would come from your Laravel backend) ---
    let exams = [
        { id: 1, title: 'Mid-Term Physics Exam', subject: 'Physics', class: 'Grade 10 - A', date: '2025-09-20', duration: 90, status: 'Active' },
        { id: 2, title: 'Final Chemistry Practical', subject: 'Chemistry', class: 'Grade 12 - B', date: '2025-10-05', duration: 120, status: 'Upcoming' },
        { id: 3, title: 'First Sessional English Test', subject: 'English', class: 'Grade 9 - C', date: '2025-08-15', duration: 45, status: 'Completed' },
        { id: 4, title: 'Mathematics Final Term (Draft)', subject: 'Mathematics', class: 'Grade 11 - A', date: '2025-10-10', duration: 180, status: 'Draft' }
    ];

    // --- DOM ELEMENT REFERENCES ---
    const createExamBtn = document.getElementById('createExamBtn');
    const examModal = document.getElementById('examModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const examForm = document.getElementById('examForm');
    const modalTitle = document.getElementById('modalTitle');
    const tableBody = document.getElementById('exam-table-body');
    const searchInput = document.getElementById('searchInput');

    // --- STATUS BADGE STYLES ---
    const statusStyles = {
        'Active': 'text-green-700 bg-green-100',
        'Upcoming': 'text-yellow-700 bg-yellow-100',
        'Completed': 'text-indigo-700 bg-indigo-100',
        'Draft': 'text-gray-600 bg-gray-200'
    };

    // --- CORE FUNCTIONS ---

    // Function to render the table with current exams data
    const renderTable = (filteredExams = exams) => {
        tableBody.innerHTML = ''; // Clear existing table rows
        if (filteredExams.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-10 text-gray-500">No exams found.</td></tr>`;
            return;
        }

        filteredExams.forEach(exam => {
            const row = `
                <tr class="bg-white border-b hover:bg-gray-50 transition">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${exam.title}</th>
                    <td class="px-6 py-4">${exam.subject}</td>
                    <td class="px-6 py-4">${exam.class}</td>
                    <td class="px-6 py-4">${new Date(exam.date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</td>
                    <td class="px-6 py-4">${exam.duration} mins</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full ${statusStyles[exam.status] || 'text-gray-700 bg-gray-100'}">
                            ${exam.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center flex items-center justify-center gap-3">
                        <button class="edit-btn font-medium text-yellow-500 hover:text-yellow-700" title="Edit" data-id="${exam.id}">✏️</button>
                        <button class="delete-btn font-medium text-red-600 hover:text-red-800" title="Delete" data-id="${exam.id}">🗑️</button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
        updateStats();
    };

    // Function to update the dashboard stats
    const updateStats = () => {
        document.getElementById('total-exams-stat').textContent = exams.length;
        document.getElementById('active-exams-stat').textContent = exams.filter(e => e.status === 'Active').length;
        document.getElementById('upcoming-exams-stat').textContent = exams.filter(e => e.status === 'Upcoming').length;
        document.getElementById('completed-exams-stat').textContent = exams.filter(e => e.status === 'Completed').length;
    };
    
    // --- MODAL HANDLING ---
    const openModal = (mode = 'create', examId = null) => {
        examForm.reset();
        if (mode === 'create') {
            modalTitle.textContent = 'Create New Exam';
            document.getElementById('examId').value = '';
        } else {
            modalTitle.textContent = 'Edit Exam';
            const exam = exams.find(e => e.id === examId);
            if (exam) {
                document.getElementById('examId').value = exam.id;
                document.getElementById('exam_title').value = exam.title;
                document.getElementById('subject').value = exam.subject;
                document.getElementById('class_grade').value = exam.class;
                document.getElementById('duration').value = exam.duration;
                document.getElementById('start_date').value = exam.date;
                document.getElementById('status').value = exam.status;
            }
        }
        examModal.classList.remove('hidden');
    };

    const closeModal = () => {
        examModal.classList.add('hidden');
    };
    
    // --- EVENT LISTENERS ---

    // Open modal for creating a new exam
    createExamBtn.addEventListener('click', () => openModal('create'));

    // Close modal buttons
    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    examModal.addEventListener('click', (e) => {
        if (e.target === examModal) closeModal();
    });

    // Handle form submission (for both create and edit)
    examForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(examForm);
        const examId = parseInt(formData.get('examId'));
        const examData = {
            title: formData.get('exam_title'),
            subject: formData.get('subject'),
            class: formData.get('class_grade'),
            date: formData.get('start_date'),
            duration: parseInt(formData.get('duration')),
            status: formData.get('status'),
        };

        if (examId) { // If ID exists, it's an update
            const index = exams.findIndex(e => e.id === examId);
            exams[index] = { ...exams[index], ...examData };
        } else { // Otherwise, it's a new exam
            examData.id = Date.now(); // Simple unique ID
            exams.push(examData);
        }

        renderTable();
        closeModal();
    });
    
    // Handle Edit and Delete clicks using event delegation
    tableBody.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (editBtn) {
            const examId = parseInt(editBtn.dataset.id);
            openModal('edit', examId);
        }

        if (deleteBtn) {
            const examId = parseInt(deleteBtn.dataset.id);
            // Confirmation dialog
            if (confirm('Are you sure you want to delete this exam?')) {
                exams = exams.filter(exam => exam.id !== examId);
                renderTable();
            }
        }
    });

    // Handle live search
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredExams = exams.filter(exam => 
            exam.title.toLowerCase().includes(searchTerm) ||
            exam.subject.toLowerCase().includes(searchTerm) ||
            exam.class.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredExams);
    });

    // --- INITIAL RENDER ---
    renderTable();
});
</script>
@endpush
