@extends('layouts.admin')

@section('title', 'Academic Progress')
@section('page-title', 'Class Progress')

@section('content')
<body class="bg-slate-100 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">

        {{-- Filter Section --}}
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 mb-8  mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12M3.75 3h16.5M3.75 3a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25M3.75 14.25v2.25A2.25 2.25 0 006 18.75h12A2.25 2.25 0 0020.25 16.5v-2.25" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Class Progress Editor</h2>
                    <p class="text-sm text-slate-500">Select a class, subject, and date to add or update student progress.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Class Selector --}}
                <div>
                    <label for="classSelector" class="block text-sm font-medium text-slate-600 mb-2">Class</label>
                    <select id="classSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none">
                        <option value="" disabled selected>Select Class...</option>
                    </select>
                </div>

                {{-- Subject Selector --}}
                <div>
                    <label for="subjectSelector" class="block text-sm font-medium text-slate-600 mb-2">Subject</label>
                    <select id="subjectSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none">
                        <option value="" disabled selected>Select Subject...</option>
                    </select>
                </div>

                {{-- Date Picker --}}
                <div>
                    <label for="datePicker" class="block text-sm font-medium text-slate-600 mb-2">Date</label>
                    <input type="date" id="datePicker" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400">
                </div>

                {{-- Load Button --}}
                <div>
                    <button id="loadButton" class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white font-bold px-4 py-3 rounded-lg hover:bg-indigo-700 transition-transform hover:scale-105 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        View Progress
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Area for Class Progress --}}
        <section id="progressContent" class=" mx-auto"></section>

    </main>
</body>
@endsection

@push('scripts')
<style>
    /* Simple animation for the form appearing */
    .form-reveal {
        animation: fadeIn .3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
// ===================================================================================
// DATA STRUCTURES (Added student 'id' for easier targeting)
// ===================================================================================
const subjectsList = [ 'English', 'Math', 'Science', 'Art', 'Urdu', 'Social Studies', 'Computer', 'Islamiyat', 'History', 'Physics', 'Chemistry', 'Biology' ];

const studentsData = {
    'class-1': { name: 'Grade 1', students: {
        'std-101': { id: 'std-101', name: 'Ahmed Ali', avatarUrl: 'https://placehold.co/128x128/FFC107/white?text=AA', diary: { '2025-09-12': [{ subject: 'English', homework: 'Practice writing the alphabet.', status: 'Completed', teacherNotes: 'Very neat handwriting.'}, { subject: 'Math', homework: 'Count objects up to 20.', status: 'Pending', teacherNotes: ''}]}},
        'std-102': { id: 'std-102', name: 'Bisma Khan', avatarUrl: 'https://placehold.co/128x128/4CAF50/white?text=BK', diary: { '2025-09-12': [{ subject: 'English', homework: 'Practice writing the alphabet.', status: 'Pending', teacherNotes: 'Needs more practice.'}]}},
        'std-103': { id: 'std-103', name: 'Dania Noor', avatarUrl: 'https://placehold.co/128x128/9C27B0/white?text=DN', diary: { '2025-09-12': [{ subject: 'Art', homework: 'Draw your favorite animal.', status: 'Completed', teacherNotes: 'Creative use of colors!'}]}},
    }},
    'class-3': { name: 'Grade 3', students: {
        'std-301': { id: 'std-301', name: 'Haris Sohail', avatarUrl: 'https://placehold.co/128x128/3F51B5/white?text=HS', diary: {}},
        'std-302': { id: 'std-302', name: 'Hina Riaz', avatarUrl: 'https://placehold.co/128x128/00BCD4/white?text=HR', diary: { '2025-09-12': [{ subject: 'Science', homework: 'Label the parts of a plant.', status: 'Pending', teacherNotes: 'Please submit by tomorrow.'}, { subject: 'Social Studies', homework: 'Name the provinces of Pakistan.', status: 'Completed', teacherNotes: 'Well done.'}]}},
        'std-303': { id: 'std-303', name: 'Imran Malik', avatarUrl: 'https://placehold.co/128x128/8BC34A/white?text=IM', diary: { '2025-09-12': [{ subject: 'Science', homework: 'Label the parts of a plant.', status: 'Completed', teacherNotes: 'Good diagram.'}]}},
    }},
};

// ===================================================================================
// DOM ELEMENT REFERENCES
// ===================================================================================
const classSelector = document.getElementById('classSelector');
const subjectSelector = document.getElementById('subjectSelector');
const datePicker = document.getElementById('datePicker');
const loadButton = document.getElementById('loadButton');
const progressContent = document.getElementById('progressContent');

// ===================================================================================
// HELPER FUNCTIONS
// ===================================================================================
const getTodayDateString = () => new Date('2025-09-12').toISOString().split('T')[0];
const formatDisplayDate = (dateString) => {
    if (!dateString) return '';
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', options);
};

// ===================================================================================
// UI POPULATION & RENDERING FUNCTIONS
// ===================================================================================
function populateClassSelector() {
    Object.keys(studentsData).forEach(classId => {
        const option = document.createElement('option');
        option.value = classId;
        option.textContent = studentsData[classId].name;
        classSelector.appendChild(option);
    });
}

function populateSubjectSelector() {
    subjectsList.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject;
        option.textContent = subject;
        subjectSelector.appendChild(option);
    });
}

function renderInitialState() {
    progressContent.innerHTML = `<div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200">
        <svg class="mx-auto h-16 w-16 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12M3.75 3h16.5M3.75 3a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25M3.75 14.25v2.25A2.25 2.25 0 006 18.75h12A2.25 2.25 0 0020.25 16.5v-2.25" /></svg>
        <h3 class="mt-4 text-2xl font-bold text-slate-800">Welcome to the Progress Editor</h3>
        <p class="mt-2 text-sm text-slate-500">Please select a class, subject, and date to get started.</p>
    </div>`;
}

// THIS IS THE MAIN RENDER FUNCTION
function renderSubjectProgress() {
    const classId = classSelector.value;
    const selectedSubject = subjectSelector.value;
    const dateString = datePicker.value;

    if (!classId || !selectedSubject || !dateString) { return; } // Don't render if selections are incomplete

    const classInfo = studentsData[classId];
    const studentsInClass = classInfo ? Object.values(classInfo.students) : [];
    
    // Function to create the HTML for a single student card's content (either view or form)
    const createCardContentHTML = (student, subjectEntry) => {
        if (subjectEntry) {
            // VIEW MODE
            return `
                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-500">Homework:</p>
                    <p class="text-sm text-slate-700">${subjectEntry.homework || 'N/A'}</p>
                </div>
                ${subjectEntry.teacherNotes ? `
                <div class="mt-2 pt-2 border-t border-slate-200">
                    <p class="text-xs font-semibold text-slate-500">Teacher's Notes:</p>
                    <p class="text-sm italic text-slate-600">"${subjectEntry.teacherNotes}"</p>
                </div>` : ''}
            `;
        } else {
            // NO ENTRY VIEW
            return `<p class="mt-3 text-sm text-slate-500">No progress has been added for this subject yet.</p>`;
        }
    };

    // Function to create the full student card
    const createStudentProgressCard = (student) => {
        const diaryForDay = student.diary[dateString] || [];
        const subjectEntry = diaryForDay.find(entry => entry.subject === selectedSubject);
        const hasEntry = !!subjectEntry;

        const borderClass = hasEntry ? 'border-indigo-500' : 'border-slate-400';
        const buttonText = hasEntry ? 'Edit' : 'Add';
        const buttonClass = hasEntry 
            ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' 
            : 'bg-teal-100 text-teal-700 hover:bg-teal-200';

        return `
            <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 ${borderClass}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="${student.avatarUrl}" alt="${student.name}">
                        <p class="font-bold text-slate-800">${student.name}</p>
                    </div>
                    <button class="add-edit-btn text-xs font-bold px-3 py-1.5 rounded-md ${buttonClass} transition"
                            data-class-id="${classId}"
                            data-student-id="${student.id}"
                            data-subject="${selectedSubject}"
                            data-date="${dateString}">
                        ${buttonText} Progress
                    </button>
                </div>
                <div class="card-content-wrapper pl-13 pt-2">
                    ${createCardContentHTML(student, subjectEntry)}
                </div>
            </div>
        `;
    };
    
    const progressCardsHTML = studentsInClass.map(createStudentProgressCard).join('');

    progressContent.innerHTML = `
       
        <div class="space-y-3">${progressCardsHTML}</div>`;
}


// ===================================================================================
// DYNAMIC EVENT HANDLING
// ===================================================================================
function handleProgressAction(e) {
    const target = e.target;
    const isAddEditButton = target.classList.contains('add-edit-btn');
    const isUpdateButton = target.classList.contains('update-btn');

    if (!isAddEditButton && !isUpdateButton) return;

    e.preventDefault();
    const { classId, studentId, subject, date } = target.dataset;
    const student = studentsData[classId].students[studentId];
    const cardContentWrapper = target.closest('.bg-white').querySelector('.card-content-wrapper');

    if (isAddEditButton) {
        // --- RENDER THE EDIT FORM ---
        const diaryForDay = student.diary[date] || [];
        const subjectEntry = diaryForDay.find(entry => entry.subject === subject) || {};
        
        const formHTML = `
            <div class="form-reveal space-y-3 mt-3">
                <div>
                    <label class="text-xs font-semibold text-slate-600">Homework / Task</label>
                    <textarea class="homework-input w-full mt-1 bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" rows="3">${subjectEntry.homework || ''}</textarea>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Teacher's Notes</label>
                    <textarea class="notes-input w-full mt-1 bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" rows="2">${subjectEntry.teacherNotes || ''}</textarea>
                </div>
                <button class="update-btn text-xs font-bold px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition"
                        data-class-id="${classId}"
                        data-student-id="${studentId}"
                        data-subject="${subject}"
                        data-date="${date}">
                    Update Progress
                </button>
            </div>
        `;
        cardContentWrapper.innerHTML = formHTML;
    } 
    else if (isUpdateButton) {
        // --- SAVE THE DATA AND RE-RENDER ---
        const form = target.closest('.form-reveal');
        const homeworkText = form.querySelector('.homework-input').value;
        const notesText = form.querySelector('.notes-input').value;
        
        // Find or create the diary for the day
        if (!student.diary[date]) {
            student.diary[date] = [];
        }
        
        let subjectEntry = student.diary[date].find(entry => entry.subject === subject);
        
        if (subjectEntry) {
            // Update existing entry
            subjectEntry.homework = homeworkText;
            subjectEntry.teacherNotes = notesText;
            subjectEntry.status = 'Completed';
        } else {
            // Create new entry
            student.diary[date].push({
                subject: subject,
                homework: homeworkText,
                teacherNotes: notesText,
                status: 'Completed'
            });
        }
        
        // Refresh the entire list to show the updated view
        renderSubjectProgress();
    }
}


// ===================================================================================
// INITIALIZATION
// ===================================================================================
document.addEventListener('DOMContentLoaded', () => {
    datePicker.value = getTodayDateString();
    populateClassSelector();
    populateSubjectSelector();
    renderInitialState();
    
    loadButton.addEventListener('click', renderSubjectProgress);
    progressContent.addEventListener('click', handleProgressAction);
});
</script>
@endpush