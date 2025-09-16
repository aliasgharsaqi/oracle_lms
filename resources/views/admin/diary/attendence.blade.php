@extends('layouts.admin')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('content')
<body class="bg-slate-100 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">

        {{-- Filter Section for Attendance --}}
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 mb-8 `mx-auto">
            {{-- Updated Header with Import Button --}}
            <div class="flex items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-teal-100 p-2 rounded-lg">
                        <svg class="h-6 w-6 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Student Attendance</h2>
                        <p class="text-sm text-slate-500">Select a class and date to mark student attendance.</p>
                    </div>
                </div>
                {{-- New Import Button --}}
                <div>
                    <button id="importButton" class="flex items-center gap-2 bg-slate-100 text-slate-700 font-semibold text-sm px-4 py-2 rounded-lg hover:bg-slate-200 transition shadow-sm border border-slate-200">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        Import
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end pt-4 border-t border-slate-200">
                {{-- Class Selector --}}
                <div>
                    <label for="classSelector" class="block text-sm font-medium text-slate-600 mb-2">Class</label>
                    <select id="classSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none">
                        <option value="" disabled selected>Select Class...</option>
                    </select>
                </div>

                {{-- Subject Selector (Optional) --}}
                <div>
                    <label for="subjectSelector" class="block text-sm font-medium text-slate-600 mb-2">Subject <span class="text-xs text-slate-400">(Optional)</span></label>
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
                           <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        Load Students
                    </button>
                </div>
            </div>
        </div>

        {{-- Content Area for Attendance Sheet --}}
        <section id="attendanceSheet" class="mx-auto"></section>

    </main>
</body>
@endsection

@push('scripts')
<style>
    /* Custom radio button styles */
    .attendance-radio { display: none; }
    .attendance-label {
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        transition: all 0.2s ease-in-out;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px solid transparent;
    }
    .attendance-radio:checked + .attendance-label {
        transform: scale(1.05);
    }
    /* Present */
    .attendance-radio[value="Present"] + .attendance-label { background-color: #f0fdf4; color: #15803d; }
    .attendance-radio[value="Present"]:checked + .attendance-label { background-color: #22c55e; color: #ffffff; border-color: #16a34a; }
    /* Absent */
    .attendance-radio[value="Absent"] + .attendance-label { background-color: #fff1f2; color: #be123c; }
    .attendance-radio[value="Absent"]:checked + .attendance-label { background-color: #f43f5e; color: #ffffff; border-color: #e11d48; }
    /* Leave */
    .attendance-radio[value="Leave"] + .attendance-label { background-color: #fffbeb; color: #b45309; }
    .attendance-radio[value="Leave"]:checked + .attendance-label { background-color: #f59e0b; color: #ffffff; border-color: #d97706; }
</style>
<script>
// ===================================================================================
// DATA STRUCTURES (Now includes an 'attendance' object)
// ===================================================================================
const subjectsList = [ 'General', 'English', 'Math', 'Science', 'Art', 'Urdu', 'Social Studies', 'Computer', 'Islamiyat', 'History', 'Physics', 'Chemistry', 'Biology' ];

const studentsData = {
    'class-1': { name: 'Grade 1', students: {
        'std-101': { id: 'std-101', name: 'Ahmed Ali', avatarUrl: 'https://placehold.co/128x128/FFC107/white?text=AA', attendance: { '2025-09-12': { status: 'Present', subject: 'General', remarks: '' }}},
        'std-102': { id: 'std-102', name: 'Bisma Khan', avatarUrl: 'https://placehold.co/128x128/4CAF50/white?text=BK', attendance: { '2025-09-12': { status: 'Absent', subject: 'General', remarks: 'Not feeling well.' }}},
        'std-103': { id: 'std-103', name: 'Dania Noor', avatarUrl: 'https://placehold.co/128x128/9C27B0/white?text=DN', attendance: {}},
    }},
    'class-3': { name: 'Grade 3', students: {
        'std-301': { id: 'std-301', name: 'Haris Sohail', avatarUrl: 'https://placehold.co/128x128/3F51B5/white?text=HS', attendance: {}},
        'std-302': { id: 'std-302', name: 'Hina Riaz', avatarUrl: 'https://placehold.co/128x128/00BCD4/white?text=HR', attendance: { '2025-09-12': { status: 'Leave', subject: 'General', remarks: 'Family event.' }}},
        'std-303': { id: 'std-303', name: 'Imran Malik', avatarUrl: 'https://placehold.co/128x128/8BC34A/white?text=IM', attendance: {}},
    }},
};

// ===================================================================================
// DOM ELEMENT REFERENCES
// ===================================================================================
const classSelector = document.getElementById('classSelector');
const subjectSelector = document.getElementById('subjectSelector');
const datePicker = document.getElementById('datePicker');
const loadButton = document.getElementById('loadButton');
const importButton = document.getElementById('importButton'); // New button
const attendanceSheet = document.getElementById('attendanceSheet');

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
    attendanceSheet.innerHTML = `<div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200">
        <svg class="mx-auto h-16 w-16 text-teal-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <h3 class="mt-4 text-2xl font-bold text-slate-800">Ready to Mark Attendance</h3>
        <p class="mt-2 text-sm text-slate-500">Please select a class and date, then click "Load Students" to begin.</p>
    </div>`;
}

function renderAttendanceSheet() {
    const classId = classSelector.value;
    const selectedSubject = subjectSelector.value || 'General';
    const dateString = datePicker.value;

    if (!classId || !dateString) {
        attendanceSheet.innerHTML = `<div class="text-center p-6 bg-yellow-100 text-yellow-800 rounded-lg shadow"><p class="font-semibold">Please select a class and a date to load the attendance sheet.</p></div>`;
        return;
    }

    const classInfo = studentsData[classId];
    const studentsInClass = classInfo ? Object.values(classInfo.students) : [];
    
    // Function to create an attendance row for a single student
    const createStudentAttendanceRow = (student) => {
        const attendanceRecord = student.attendance[dateString];
        const currentStatus = attendanceRecord ? attendanceRecord.status : 'Present'; // Default to 'Present'

        return `
            <div class="student-row bg-white p-4 rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-3 items-center gap-4" data-student-id="${student.id}">
                <div class="flex items-center gap-3">
                    <img class="h-10 w-10 rounded-full object-cover" src="${student.avatarUrl}" alt="${student.name}">
                    <p class="font-bold text-slate-800">${student.name}</p>
                </div>
                
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <div>
                        <input type="radio" id="present-${student.id}" name="attendance-${student.id}" value="Present" class="attendance-radio" ${currentStatus === 'Present' ? 'checked' : ''}>
                        <label for="present-${student.id}" class="attendance-label">Present</label>
                    </div>
                    <div>
                        <input type="radio" id="absent-${student.id}" name="attendance-${student.id}" value="Absent" class="attendance-radio" ${currentStatus === 'Absent' ? 'checked' : ''}>
                        <label for="absent-${student.id}" class="attendance-label">Absent</label>
                    </div>
                    <div>
                        <input type="radio" id="leave-${student.id}" name="attendance-${student.id}" value="Leave" class="attendance-radio" ${currentStatus === 'Leave' ? 'checked' : ''}>
                        <label for="leave-${student.id}" class="attendance-label">Leave</label>
                    </div>
                </div>

                <div class="w-full">
                     <input type="text" class="remarks-input w-full bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" placeholder="Add remarks (optional)..." value="${attendanceRecord?.remarks || ''}">
                </div>
            </div>
        `;
    };
    
    const attendanceRowsHTML = studentsInClass.map(createStudentAttendanceRow).join('');

    attendanceSheet.innerHTML = `
        <div class="bg-white p-5 rounded-2xl shadow-lg border border-slate-200 mb-6">
            <h3 class="text-2xl font-bold text-slate-800">Attendance Sheet</h3>
            <p class="text-slate-500 mt-1">
                Marking for <strong class="text-indigo-600">${classInfo.name}</strong> on <strong class="text-indigo-600">${formatDisplayDate(dateString)}</strong>
            </p>
        </div>
        <div class="space-y-3">
            ${attendanceRowsHTML}
        </div>
        <div class="mt-6 flex justify-end">
            <button id="saveAttendanceBtn" class="bg-teal-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-teal-700 transition-transform hover:scale-105 shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                Save Attendance
            </button>
        </div>
    `;
}

// ===================================================================================
// DYNAMIC EVENT HANDLING & DATA SAVING
// ===================================================================================
function handleSaveAttendance(e) {
    if (e.target.id !== 'saveAttendanceBtn') return;
    
    const saveButton = e.target;
    const dateString = datePicker.value;
    const classId = classSelector.value;
    const selectedSubject = subjectSelector.value || 'General';

    const studentRows = document.querySelectorAll('.student-row');
    
    studentRows.forEach(row => {
        const studentId = row.dataset.studentId;
        const student = studentsData[classId].students[studentId];
        
        const selectedRadio = row.querySelector('input[type="radio"]:checked');
        const status = selectedRadio ? selectedRadio.value : 'Absent'; // Default to absent if nothing is selected
        const remarks = row.querySelector('.remarks-input').value;
        
        // Update the data object
        student.attendance[dateString] = {
            status,
            subject: selectedSubject,
            remarks
        };
    });

    // Provide user feedback
    saveButton.textContent = 'Saved!';
    saveButton.classList.replace('bg-teal-600', 'bg-green-600');
    saveButton.disabled = true;

    console.log('Updated Attendance Data:', studentsData); // For debugging

    setTimeout(() => {
        saveButton.textContent = 'Save Attendance';
        saveButton.classList.replace('bg-green-600', 'bg-teal-600');
        saveButton.disabled = false;
    }, 2500);
}

// ===================================================================================
// INITIALIZATION
// ===================================================================================
document.addEventListener('DOMContentLoaded', () => {
    datePicker.value = getTodayDateString();
    populateClassSelector();
    populateSubjectSelector();
    renderInitialState();
    
    loadButton.addEventListener('click', renderAttendanceSheet);
    attendanceSheet.addEventListener('click', handleSaveAttendance);
    
    // Event listener for the new import button
    importButton.addEventListener('click', () => {
        alert('Import functionality will be implemented here. You can open a file dialog or a modal.');
    });
});
</script>
@endpush