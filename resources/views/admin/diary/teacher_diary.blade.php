@extends('layouts.admin')

@section('title', 'Teacher Management')
@section('page-title', 'Teacher Daily Schedule')

@section('content')
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 mb-8 max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-slate-700">Schedule Viewer</h2>
            <p class="text-sm text-slate-500 mt-1 mb-4">Select a teacher to view their daily subject schedule and overall progress.</p>
            <div class="relative">
                <select id="teacherSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none pr-10">
                    <option value="" disabled>Select a teacher...</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                    </svg>
                </div>
            </div>
        </div>

        <section id="teacherContent" class="max-w-7xl mx-auto"></section>

    </main>
</body>
@endsection

@push('scripts')
<style>
    details[open] summary ~ * { animation: sweep .5s ease-in-out; }
    @keyframes sweep {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    details > summary { list-style: none; }
    details > summary::-webkit-details-marker { display: none; }
</style>

<script>
// ===================================================================================
// MOCK DATA STRUCTURE: Now includes overall progress stats
// ===================================================================================
const teachersData = {
    'teacher-1': {
        name: 'Mr. Ahmed Raza',
        avatarUrl: 'https://placehold.co/128x128/6366f1/white?text=AR',
        progress: { total: 3, completed: 1, remaining: 2 },
        assignedSubjects: [
            { id: 'math-10', name: 'Mathematics', grade: 'Grade 10' },
            { id: 'physics-11', name: 'Physics', grade: 'Grade 11' },
            { id: 'cs-12', name: 'Computer Science', grade: 'Grade 12' },
        ],
        schedule: {
            '2025-09-10': [
                { subjectId: 'math-10', status: 'Taught', notes: 'Finished trigonometry chapter.' },
                { subjectId: 'physics-11', status: 'Taught', notes: 'Lab experiment on optics.' },
                { subjectId: 'cs-12', status: 'Not Taught', notes: 'Smartboard was not working.' },
            ],
            '2025-09-11': [
                { subjectId: 'math-10', status: 'Taught', notes: 'Started new chapter on calculus.' },
                { subjectId: 'physics-11', status: 'Not Taught', notes: 'Attended a staff meeting.' },
                { subjectId: 'cs-12', status: 'Taught', notes: '' },
            ],
            '2025-09-12': [
                { subjectId: 'math-10', status: 'Scheduled', notes: '' },
                { subjectId: 'physics-11', status: 'Scheduled', notes: '' },
                { subjectId: 'cs-12', status: 'Scheduled', notes: '' },
            ]
        }
    },
    'teacher-2': {
        name: 'Ms. Fatima Ali',
        avatarUrl: 'https://placehold.co/128x128/ec4899/white?text=FA',
        progress: { total: 2, completed: 2, remaining: 0 },
        assignedSubjects: [
            { id: 'eng-9', name: 'English', grade: 'Grade 9' },
            { id: 'hist-9', name: 'History', grade: 'Grade 9' },
        ],
        schedule: {
             '2025-09-11': [
                { subjectId: 'eng-9', status: 'Taught', notes: 'Poetry recitation session was successful.' },
                { subjectId: 'hist-9', status: 'Taught', notes: '' },
            ]
        }
    }
};

const teacherSelector = document.getElementById('teacherSelector');
const teacherContent = document.getElementById('teacherContent');

// ===================================================================================
// HELPER FUNCTIONS
// ===================================================================================
const getTodayDateString = () => new Date('2025-09-11').toISOString().split('T')[0]; // Hardcoded for consistency
const formatDisplayDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString + 'T00:00:00').toLocaleDateString('en-US', options);
};

// ===================================================================================
// CORE RENDERING FUNCTIONS
// ===================================================================================
function populateTeacherSelector() {
    Object.keys(teachersData).forEach(teacherId => {
        const option = document.createElement('option');
        option.value = teacherId;
        option.textContent = teachersData[teacherId].name;
        teacherSelector.appendChild(option);
    });
}

function renderTeacherSchedule(teacherId, dateString) {
    const teacher = teachersData[teacherId];
    if (!teacher) {
        teacherContent.innerHTML = '<p class="text-center text-red-500">Could not find teacher data.</p>';
        return;
    }

    const todayDateString = getTodayDateString();
    const isToday = (dateString === todayDateString);

    const scheduleForDay = teacher.schedule[dateString] || [];
    const subjectsForDay = teacher.assignedSubjects.map(assignedSub => {
        const scheduleEntry = scheduleForDay.find(s => s.subjectId === assignedSub.id);
        return { ...assignedSub, status: scheduleEntry?.status || 'Scheduled', notes: scheduleEntry?.notes || '' };
    });

    const createSubjectCard = (subject) => {
        let statusConfig, interactiveForm = '';
        switch(subject.status) {
            case 'Taught': statusConfig = { badge: 'bg-green-100 text-green-800', border: 'border-green-500' }; break;
            case 'Not Taught': statusConfig = { badge: 'bg-red-100 text-red-800', border: 'border-red-500' }; break;
            default: statusConfig = { badge: 'bg-indigo-100 text-indigo-800', border: 'border-indigo-500' };
        }
        
        if (isToday) {
            interactiveForm = `<div class="mt-4 pt-4 border-t border-slate-200">
                <label for="notes-${subject.id}" class="block text-xs font-semibold text-slate-600 mb-1">Add Update/Notes:</label>
                <textarea id="notes-${subject.id}" class="w-full bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" rows="3" placeholder="e.g., Completed Chapter 5...">${subject.notes}</textarea>
                <button class="save-update-btn mt-2 bg-indigo-600 text-white text-xs font-bold px-4 py-2 rounded-md hover:bg-indigo-700 transition" data-teacher-id="${teacherId}" data-subject-id="${subject.id}" data-date="${dateString}">Save Update</button>
            </div>`;
        } else if (subject.notes) {
            interactiveForm = `<div class="mt-4 pt-4 border-t border-slate-200">
                <p class="text-xs font-semibold text-slate-600">Notes:</p><p class="text-sm text-slate-700 italic">"${subject.notes}"</p>
            </div>`;
        }

        const cardContent = `<div class="flex items-center justify-between">
            <div><p class="font-bold text-slate-800">${subject.name}</p><p class="text-sm text-slate-500">${subject.grade}</p></div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusConfig.badge}">${subject.status}</span>
        </div>`;
        
        return isToday ?
            `<details class="bg-white rounded-lg shadow-sm border-l-4 ${statusConfig.border} overflow-hidden"><summary class="p-4 cursor-pointer list-none transition hover:bg-slate-50">${cardContent}</summary><div class="px-4 pb-4">${interactiveForm}</div></details>` :
            `<div class="bg-white p-4 rounded-lg shadow-sm border-l-4 ${statusConfig.border}">${cardContent}${interactiveForm}</div>`;
    };

    const createAssignedSubjectListItem = (subject) => `
        <div class="bg-white p-3 rounded-md shadow-sm border border-slate-200 flex items-center gap-3">
            <div class="bg-slate-100 p-2 rounded-md"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18" /></svg></div>
            <div>
                <p class="font-semibold text-slate-700">${subject.name}</p>
                <p class="text-xs text-slate-500">${subject.grade}</p>
            </div>
        </div>`;

    teacherContent.innerHTML = `
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-2xl shadow-xl shadow-indigo-300/40 mb-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-5"><img class="h-20 w-20 rounded-full object-cover border-4 border-white/50 shadow-md" src="${teacher.avatarUrl}" alt="${teacher.name}">
                    <div><h2 class="text-3xl font-bold">${teacher.name}</h2><p class="text-sm opacity-80">Daily Schedule & Progress</p></div>
                </div>
                <div class="bg-white/10 p-3 rounded-lg"><label for="datePicker" class="text-xs font-semibold opacity-80">SELECT DATE</label>
                    <input type="date" id="datePicker" value="${dateString}" class="bg-transparent text-white font-semibold border-none focus:ring-0 p-1">
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Overall Progress</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-indigo-600">${teacher.progress.total}</p><p class="text-sm font-semibold text-slate-500">Total Assigned</p></div>
                <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-green-600">${teacher.progress.completed}</p><p class="text-sm font-semibold text-slate-500">Completed</p></div>
                <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-amber-600">${teacher.progress.remaining}</p><p class="text-sm font-semibold text-slate-500">Remaining</p></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <div class="lg:col-span-3">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Schedule for <span class="text-indigo-600">${formatDisplayDate(dateString)}</span></h3>
                <div class="space-y-4">
                    ${subjectsForDay.map(createSubjectCard).join('') || '<p>No subjects scheduled.</p>'}
                </div>
            </div>
            <div class="lg:col-span-2">
                <h3 class="text-xl font-bold text-slate-800 mb-4">All Assigned Subjects</h3>
                <div class="space-y-3">
                    ${teacher.assignedSubjects.map(createAssignedSubjectListItem).join('')}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('datePicker').addEventListener('input', (e) => renderTeacherSchedule(teacherId, e.target.value));
}

// ===================================================================================
// EVENT HANDLERS & INITIALIZATION
// ===================================================================================
function handleUpdateClick(e) {
    if (!e.target.classList.contains('save-update-btn')) return;

    const button = e.target;
    const { teacherId, subjectId, date } = button.dataset;
    const notesTextarea = document.getElementById(`notes-${subjectId}`);
    
    const teacherSchedule = teachersData[teacherId]?.schedule[date];
    if (teacherSchedule) {
        const subjectEntry = teacherSchedule.find(s => s.subjectId === subjectId);
        if (subjectEntry) {
            subjectEntry.notes = notesTextarea.value;
            button.textContent = 'Saved!';
            button.classList.replace('bg-indigo-600', 'bg-green-600');
            setTimeout(() => {
                button.textContent = 'Save Update';
                button.classList.replace('bg-green-600', 'bg-indigo-600');
            }, 2000);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    populateTeacherSelector();

    // --- MODIFIED: Load first teacher by default ---
    const firstTeacherId = teacherSelector.options[1]?.value; // Get the first actual teacher
    if (firstTeacherId) {
        teacherSelector.value = firstTeacherId;
        renderTeacherSchedule(firstTeacherId, getTodayDateString());
    }
    
    teacherSelector.addEventListener('change', (e) => renderTeacherSchedule(e.target.value, getTodayDateString()));
    teacherContent.addEventListener('click', handleUpdateClick);
});
</script>
@endpush