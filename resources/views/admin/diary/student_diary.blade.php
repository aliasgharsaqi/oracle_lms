@extends('layouts.admin')

@section('title', 'Academic Progress')
@section('page-title', 'Class Progress')

@section('content')
<body class="bg-slate-100 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">

        {{-- Filter Section --}}
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-200 mb-8  mx-auto">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12M3.75 3h16.5M3.75 3a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25M3.75 14.25v2.25A2.25 2.25 0 006 18.75h12A2.25 2.25 0 0020.25 16.5v-2.25" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Class Progress Editor</h2>
                    <p class="text-sm text-slate-500">Select a class, subject (optional), and date to add or update student progress.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Class Selector (Populated by PHP) --}}
                <div>
                    <label for="classSelector" class="block text-sm font-medium text-slate-600 mb-2">Class</label>
                    <select id="classSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none">
                        <option value="" disabled selected>Select Class...</option>
                        @if (isset($classes))
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Subject Selector (NOW OPTIONAL) --}}
                <div>
                    <label for="subjectSelector" class="block text-sm font-medium text-slate-600 mb-2">Subject (Optional)</label>
                    <select id="subjectSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition shadow-sm hover:border-indigo-400 appearance-none">
                        <option value="">All Subjects</option> 
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
    
    {{-- =================================================================================== --}}
    {{-- TEMPLATE STRUCTURES (HTML structures moved from JavaScript) --}}
    {{-- =================================================================================== --}}

    <template id="initialStateTemplate">
        {{-- Corrected background/padding classes --}}
        <div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200" data-role="initial-message-container">
            <svg class="mx-auto h-16 w-16 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12M3.75 3h16.5M3.75 3a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25M3.75 14.25v2.25A2.25 2.25 0 006 18.75h12A2.25 2.25 0 0020.25 16.5v-2.25" /></svg>
            <h3 class="mt-4 text-2xl font-bold text-slate-800" data-role="initial-message-title">Welcome to the Progress Editor</h3>
            <p class="mt-2 text-sm text-slate-500" data-role="initial-message-text">Please select a class, subject, and date to get started.</p>
        </div>
    </template>
    
    <template id="studentCardTemplate">
        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4" data-role="student-card-container">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img class="h-10 w-10 rounded-full object-cover" data-role="student-avatar" src="" alt="">
                    <p class="font-bold text-slate-800" data-role="student-name"></p>
                </div>
                {{-- This button is visible only in Single Subject View --}}
                <button class="add-edit-btn text-xs font-bold px-3 py-1.5 rounded-md transition"
                        data-role="add-edit-button">
                    Add Progress
                </button>
            </div>
            <div class="card-content-wrapper pl-13 pt-2" data-role="card-content-wrapper">
                {{-- Dynamic content (View or Form) is injected here --}}
            </div>
            {{-- Form is injected here, outside the wrapper in All Subjects View --}}
        </div>
    </template>
    
    <template id="viewContentTemplate">
        <div data-role="homework-view-section">
            <div class="mt-3">
                <p class="text-xs font-semibold text-slate-500">Homework:</p>
                <p class="text-sm text-slate-700" data-role="homework-text"></p>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-200" data-role="notes-section">
                <p class="text-xs font-semibold text-slate-500">Teacher's Notes:</p>
                <p class="text-sm italic text-slate-600" data-role="notes-text"></p>
            </div>
        </div>
    </template>

    <template id="editFormTemplate">
        <div class="form-reveal space-y-3 mt-3">
            <div>
                <label class="text-xs font-semibold text-slate-600">Homework / Task</label>
                <textarea class="homework-input w-full mt-1 bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" rows="3" data-role="form-homework-input"></textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600">Teacher's Notes</label>
                <textarea class="notes-input w-full mt-1 bg-slate-100 border border-slate-300 text-sm rounded-md p-2 focus:ring-2 focus:ring-indigo-500" rows="2" data-role="form-notes-input"></textarea>
            </div>
            <button class="update-btn text-xs font-bold px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition"
                    data-role="update-button">
                Save Progress
            </button>
        </div>
    </template>

    {{-- NEW TEMPLATE FOR ALL SUBJECTS VIEW (TABLE) --}}
    <template id="allSubjectsTemplate">
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <th class="py-2 px-3 text-left">Subject</th>
                        <th class="py-2 px-3 text-left">Homework</th>
                        <th class="py-2 px-3 text-left">Notes</th>
                        <th class="py-2 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" data-role="all-subjects-body">
                    {{-- Rows will be injected by JavaScript --}}
                </tbody>
            </table>
        </div>
    </template>

    <template id="allSubjectsRowTemplate">
        <tr class="text-sm text-slate-700 hover:bg-slate-50" data-role="subject-row">
            <td class="py-2 px-3 font-medium text-slate-800" data-role="subject-name-cell"></td>
            <td class="py-2 px-3" data-role="homework-cell"></td>
            <td class="py-2 px-3 italic text-slate-600" data-role="notes-cell"></td>
            <td class="py-2 px-3 text-center">
                 <button class="add-edit-btn-row text-xs font-bold px-2 py-1 rounded-md transition"
                         data-role="row-edit-button">
                     Edit
                 </button>
            </td>
        </tr>
    </template>

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
    .pl-13 { padding-left: 3.25rem; } /* Custom utility for alignment */
</style>
<script>
// ===================================================================================
// DYNAMIC DATA & ROUTES
// ===================================================================================
const routes = {
    getSubjects: '{{ route("progress.get_subjects", ["class_id" => "CLASS_ID_PLACEHOLDER"]) }}', 
    getProgressData: '{{ route("progress.get_data") }}',
    saveProgress: '{{ route("progress.save") }}', 
};

// ===================================================================================
// DOM ELEMENT REFERENCES
// ===================================================================================
const classSelector = document.getElementById('classSelector');
const subjectSelector = document.getElementById('subjectSelector');
const datePicker = document.getElementById('datePicker');
const loadButton = document.getElementById('loadButton');
const progressContent = document.getElementById('progressContent');

// References to HTML <template> elements
const initialStateTemplate = document.getElementById('initialStateTemplate');
const studentCardTemplate = document.getElementById('studentCardTemplate');
const viewContentTemplate = document.getElementById('viewContentTemplate');
const editFormTemplate = document.getElementById('editFormTemplate');
const allSubjectsTemplate = document.getElementById('allSubjectsTemplate');
const allSubjectsRowTemplate = document.getElementById('allSubjectsRowTemplate');

// ===================================================================================
// HELPER FUNCTIONS
// ===================================================================================

function getTodayDateString() {
    return new Date().toISOString().split('T')[0];
}

function renderInitialState(message, isError = false) {
    const templateContent = initialStateTemplate.content.cloneNode(true);
    const container = templateContent.querySelector('[data-role="initial-message-container"]');
    const title = templateContent.querySelector('[data-role="initial-message-title"]');
    const text = templateContent.querySelector('[data-role="initial-message-text"]');

    if (isError) {
        container.classList.remove('border-slate-200');
        container.classList.add('border-red-200', 'bg-red-50'); // Added bg-red-50 for error style
        title.classList.remove('text-slate-800');
        title.classList.add('text-red-800');
        title.textContent = "Data Fetch Error";
    } else {
        container.classList.remove('bg-red-50', 'border-red-200');
        container.classList.add('border-slate-200');
        title.classList.remove('text-red-800');
        title.classList.add('text-slate-800');
        title.textContent = "Welcome to the Progress Editor";
    }
    text.textContent = message;

    progressContent.innerHTML = '';
    progressContent.appendChild(templateContent);
}

function createViewContent(subjectEntry) {
    if (!subjectEntry) {
        const p = document.createElement('p');
        p.className = 'mt-3 text-sm text-slate-500';
        p.textContent = 'No progress has been added for this subject yet.';
        return p;
    }

    const templateContent = viewContentTemplate.content.cloneNode(true);
    const homeworkText = templateContent.querySelector('[data-role="homework-text"]');
    const notesSection = templateContent.querySelector('[data-role="notes-section"]');
    const notesText = templateContent.querySelector('[data-role="notes-text"]');

    homeworkText.textContent = subjectEntry.homework || 'N/A';

    if (subjectEntry.teacherNotes) {
        notesText.textContent = `"${subjectEntry.teacherNotes}"`;
    } else {
        notesSection.remove();
    }
    
    return templateContent;
}

function createEditForm(studentId, subject, date, homework, notes) {
    const templateContent = editFormTemplate.content.cloneNode(true);
    
    const homeworkInput = templateContent.querySelector('[data-role="form-homework-input"]');
    const notesInput = templateContent.querySelector('[data-role="form-notes-input"]');
    const updateButton = templateContent.querySelector('[data-role="update-button"]');

    homeworkInput.value = homework || '';
    notesInput.value = notes || '';

    updateButton.dataset.studentId = studentId;
    updateButton.dataset.subject = subject;
    updateButton.dataset.date = date;

    return templateContent;
}


// ===================================================================================
// AJAX FETCH FUNCTIONS
// ===================================================================================

async function fetchSubjects(classId) {
    // Keep 'All Subjects' option, then populate others
    subjectSelector.innerHTML = '<option value="">All Subjects</option>';
    if (!classId) return;

    subjectSelector.innerHTML = '<option value="">Loading Subjects...</option>';
    progressContent.innerHTML = '';

    try {
        const url = routes.getSubjects.replace('CLASS_ID_PLACEHOLDER', classId);
        const response = await fetch(url);
        
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const subjects = await response.json();
        
        subjectSelector.innerHTML = '<option value="">All Subjects</option>';
        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.name; 
            option.textContent = subject.name;
            subjectSelector.appendChild(option);
        });

    } catch (error) {
        console.error('Error fetching subjects:', error);
        subjectSelector.innerHTML = '<option value="">Error loading subjects</option>';
    }
}


async function renderSubjectProgress() {
    const classId = classSelector.value;
    const selectedSubject = subjectSelector.value;
    const dateString = datePicker.value;

    // VALIDATION CHANGE: Only Class and Date are mandatory
    if (!classId || !dateString) { 
        renderInitialState('Please select a Class and a Date to proceed.');
        return; 
    }

    progressContent.innerHTML = `<div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200">
        <p class="text-lg font-semibold text-indigo-600">Loading student progress...</p>
    </div>`;
    loadButton.disabled = true;

    try {
        const response = await fetch(routes.getProgressData, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({ 
                class_id: classId, 
                subject_name: selectedSubject, 
                date: dateString 
            })
        });

        // --- START DIAGNOSTICS FOR 500 ERROR ---
        if (!response.ok) {
            // Read the raw text response for debugging server errors (500)
            const rawText = await response.text();
            console.error('Server responded with an error:', response.status, rawText);
            
            // Attempt to parse JSON for validation errors (422)
            try {
                const errorData = JSON.parse(rawText);
                throw new Error(errorData.message || `Validation failed: ${JSON.stringify(errorData.errors)}`);
            } catch (e) {
                // If it wasn't JSON, it's likely an HTML 500 error page or route issue
                throw new Error(`Critical server error (${response.status}). Check console for HTML/text response.`);
            }
        }
        // --- END DIAGNOSTICS ---

        const data = await response.json();
        const studentsInClass = data.students_progress;
        
        // TEMPORARY LOGGING: Check the incoming data structure
        console.log("Received Student Data:", data); // Log the full data object

        const isSingleSubjectView = !!data.subject_name; 
        const allClassSubjects = data.all_class_subjects || []; // Get the full subject list

        if (studentsInClass.length === 0) {
              renderInitialState(`No students found registered in ${data.class_name}.`, false);
              return;
        }
        
        const listContainer = document.createElement('div');
        listContainer.className = 'space-y-3';
        
        studentsInClass.forEach(student => {
            const cardContent = studentCardTemplate.content.cloneNode(true);
            const card = cardContent.querySelector('[data-role="student-card-container"]');
            const button = cardContent.querySelector('[data-role="add-edit-button"]');
            const contentWrapper = cardContent.querySelector('[data-role="card-content-wrapper"]');
            
            // 1. Update Student Info
            cardContent.querySelector('[data-role="student-avatar"]').src = student.avatarUrl;
            cardContent.querySelector('[data-role="student-avatar"]').alt = student.name;
            cardContent.querySelector('[data-role="student-name"]').textContent = student.name;

            // 2. Set Card Styles
            card.classList.add('border-slate-400'); // Default border

            if (isSingleSubjectView) {
                // --- SINGLE SUBJECT VIEW ---
                const hasEntry = !!student.entry;
                const subjectEntry = student.entry;
                
                card.classList.add(hasEntry ? 'border-indigo-500' : 'border-slate-400');
                
                button.textContent = (hasEntry ? 'Edit' : 'Add') + ' Progress';
                
                const classString = hasEntry 
                    ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' 
                    : 'bg-teal-100 text-teal-700 hover:bg-teal-200';
                
                button.classList.add(...classString.split(' '));

                button.dataset.studentId = student.id;
                button.dataset.subject = data.subject_name;
                button.dataset.date = dateString;
                button.dataset.homework = subjectEntry?.homework || '';
                button.dataset.notes = subjectEntry?.teacherNotes || '';

                const viewContent = createViewContent(subjectEntry);
                contentWrapper.appendChild(viewContent);
            } else {
                // --- ALL SUBJECTS VIEW (The requested feature) ---
                button.style.display = 'none'; // Hide the single button
                
                const allSubjectsContent = allSubjectsTemplate.content.cloneNode(true);
                const tableBody = allSubjectsContent.querySelector('[data-role="all-subjects-body"]');
                
                const allExistingEntries = student.allEntries || {};
                
                // ITERATE OVER THE FULL LIST OF SUBJECTS, NOT JUST EXISTING ENTRIES
                allClassSubjects.forEach(subjectName => {
                    const entry = allExistingEntries[subjectName] || {};
                    
                    const rowContent = allSubjectsRowTemplate.content.cloneNode(true);
                    const row = rowContent.querySelector('[data-role="subject-row"]');
                    const editButton = rowContent.querySelector('[data-role="row-edit-button"]');

                    // Fill row cells
                    rowContent.querySelector('[data-role="subject-name-cell"]').textContent = subjectName;
                    rowContent.querySelector('[data-role="homework-cell"]').textContent = entry.homework || 'N/A';
                    rowContent.querySelector('[data-role="notes-cell"]').textContent = entry.teacherNotes ? `"${entry.teacherNotes}"` : 'N/A';
                    
                    // Set row colors/styles
                    const rowHasEntry = !!entry.homework || !!entry.teacherNotes;
                    if (rowHasEntry) {
                        row.classList.add('border-l-2', 'border-indigo-400');
                    }

                    // Configure the Edit button in the row
                    editButton.textContent = rowHasEntry ? 'Edit' : 'Add';
                    
                    const rowClassString = rowHasEntry 
                        ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' 
                        : 'bg-teal-100 text-teal-700 hover:bg-teal-200';
                        
                    editButton.classList.add(...rowClassString.split(' '));
                        
                    // Attach all data to the button for editing
                    editButton.dataset.studentId = student.id;
                    editButton.dataset.subject = subjectName; // Use the actual subject name
                    editButton.dataset.date = dateString;
                    editButton.dataset.homework = entry.homework || '';
                    editButton.dataset.notes = entry.teacherNotes || '';

                    tableBody.appendChild(rowContent);
                });

                // The allSubjectsContent contains the table structure
                // We inject it into the contentWrapper, replacing the initial simple view
                contentWrapper.innerHTML = ''; 
                contentWrapper.appendChild(allSubjectsContent);
            }

            listContainer.appendChild(cardContent);
        });
        
        const titleText = isSingleSubjectView 
            ? `${data.class_name} - ${data.subject_name} Progress on ${dateString}`
            : `${data.class_name} - All Subjects Progress on ${dateString}`;
            
        // 5. Final Render
        progressContent.innerHTML = `
            <h3 class="text-xl font-bold mb-4 text-slate-800">${titleText}</h3>`;
        progressContent.appendChild(listContainer);
        
    } catch (error) {
        console.error('Error fetching progress data:', error);
        renderInitialState(`Could not load progress. ${error.message}.`, true);
    } finally {
        loadButton.disabled = false;
    }
}


async function handleProgressAction(e) {
    const target = e.target;
    const isAddEditButton = target.classList.contains('add-edit-btn') || target.classList.contains('add-edit-btn-row');
    const isUpdateButton = target.classList.contains('update-btn');

    if (!isAddEditButton && !isUpdateButton) return;

    e.preventDefault();
    
    const classId = classSelector.value;
    const dataset = target.dataset; 
    
    const studentCardContainer = target.closest('[data-role="student-card-container"]');
    const contentWrapper = studentCardContainer.querySelector('[data-role="card-content-wrapper"]');
    

    if (isAddEditButton) {
        // --- RENDER THE EDIT FORM ---
        
        const formContent = createEditForm(dataset.studentId, dataset.subject, dataset.date, dataset.homework, dataset.notes);
        
        // Hide the original list content (either single-subject or all-subjects table)
        contentWrapper.style.display = 'none';

        // Find the element right after the main button/table wrapper to inject the form
        const formContainer = studentCardContainer.querySelector('.flex').nextElementSibling;
        
        // Remove old form if it exists
        const oldForm = studentCardContainer.querySelector('.form-reveal');
        if (oldForm) oldForm.remove();

        // Inject new form (append directly to the card container)
        studentCardContainer.appendChild(formContent);
        
        // Hide the specific button that was clicked
        target.style.display = 'none'; 
    } 
    else if (isUpdateButton) {
        // --- SAVE THE DATA ---
        target.disabled = true;
        target.textContent = 'Saving...';
        const form = target.closest('.form-reveal');
        const homeworkText = form.querySelector('[data-role="form-homework-input"]').value;
        const notesText = form.querySelector('[data-role="form-notes-input"]').value;

        try {
             const saveResponse = await fetch(routes.saveProgress, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ 
                    class_id: classId, 
                    student_id: dataset.studentId, 
                    subject: dataset.subject, 
                    date: dataset.date, 
                    homework: homeworkText, 
                    notes: notesText 
                })
            });
            
            if (!saveResponse.ok) {
                const errorData = await saveResponse.json();
                throw new Error(errorData.message || 'Server error during save.');
            }

            // Success: Remove the form and refresh the entire list to show saved data
            form.remove();
            contentWrapper.style.display = ''; // Show the main content area again
            await renderSubjectProgress();

        } catch (error) {
            alert(`Failed to save progress. ${error.message}`);
            console.error('Save error:', error);
            target.disabled = false;
            target.textContent = 'Try Again';
        }
    }
}

// ===================================================================================
// INITIALIZATION AND EVENT LISTENERS
// ===================================================================================
document.addEventListener('DOMContentLoaded', () => {
    datePicker.value = getTodayDateString();
    
    classSelector.addEventListener('change', (e) => {
        fetchSubjects(e.target.value);
        renderInitialState('Please click "View Progress" to load data.'); 
    });
    
    loadButton.addEventListener('click', renderSubjectProgress);
    
    // Delegated event listener for dynamically created buttons
    progressContent.addEventListener('click', handleProgressAction);
    
    renderInitialState('Please select a Class and Date, then click "View Progress."');
});
</script>
@endpush