@extends('layouts.admin')

@section('title', 'Teacher Diary')
@section('page-title', 'Teacher Daily Diary')

@section('content')
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
        
        {{-- Teacher List and Action Buttons --}}
    <div class="bg-white p-6 rounded-2xl shadow-2xl border border-slate-200 mb-8 max-w-7xl mx-auto">
    
    <div class="grid grid-cols-1 gap-6 md:gap-8 items-start">
        
        {{-- Teacher List / Selector (Converted to a clickable list/card for all teachers) --}}
        <div class="relative"> 
            <h2 class="text-base font-bold text-slate-800 mb-4 uppercase tracking-wider">Select a Teacher for Daily Progress View</h2>
            <div id="teacherListContainer" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                {{-- Teachers will be rendered here via a small loop --}}
                @foreach($teachers as $teacher)
                    <button type="button" 
                        data-id="{{ $teacher->id }}" 
                        data-name="{{ $teacher->user->name }}"
                        class="teacher-card-btn p-3 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-semibold hover:bg-indigo-50 hover:border-indigo-400 transition shadow-sm text-center">
                        <img class="h-10 w-10 rounded-full object-cover mx-auto mb-2" 
                            src="{{ $teacher->user->user_pic ? asset('storage/' . $teacher->user->user_pic) : 'https://placehold.co/40x40/cccccc/333333?text=N/A' }}" 
                            onerror="this.onerror=null;this.src='https://placehold.co/40x40/cccccc/333333?text=N/A';"
                            alt="{{ $teacher->user->name }}">
                        {{ $teacher->user->name }}
                    </button>
                @endforeach
            </div>
            
            {{-- HIDDEN: Retaining the input to hold the filter date value but hiding it. --}}
            <input type="hidden" id="dateFilter" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-row items-center justify-start gap-4 mt-4 pt-3 border-t border-slate-200"> 
            
            {{-- Button 1: Assign Task (Kept for admin to assign tasks) --}}
            <button id="assignTaskBtn" class="px-4 py-2.5 text-white font-semibold rounded-xl transition disabled:opacity-50 bg-green-600 hover:bg-green-700 shadow-md" disabled>
                <i class="fa-solid fa-plus-circle me-1"></i> Assign Task
            </button>
            
            {{-- Button 2: Monthly Report --}}
            <a id="monthlyReportLink" href="{{ route('teacher_diary.monthly_report') }}" 
               class="px-4 py-2.5 font-semibold transition disabled:opacity-50 border border-indigo-500 text-indigo-600 rounded-xl hover:bg-indigo-50 shadow-md flex items-center justify-center gap-2" disabled>
                <i class="fa-solid fa-calendar-check me-1"></i> Monthly Report
            </a>
            
            {{-- Date Selector (Visible Filter) --}}
            <div class="relative ms-auto">
                <label for="displayDateFilter" class="text-xs font-bold text-slate-800 mb-1 uppercase tracking-wider block">View Date</label>
                <input type="date" id="displayDateFilter" class="w-auto bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition shadow-inner hover:border-indigo-400" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>
            
        </div>
    </div>
</div>

        {{-- Teacher Content Area --}}
        <section id="teacherContent" class="max-w-7xl mx-auto">
            <div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200">
                <svg class="mx-auto h-16 w-16 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75h.007v.008h-.007V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM5.75 6.75h.007v.008H5.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM12 3.75h.007v.008H12V3.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM5.75 14.25h.007v.008H5.75V14.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM15.75 14.25h.007v.008h-.007V14.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM12 11.25h.007v.008H12V11.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15M15 4.5v15" /></svg>
                <h3 class="mt-4 text-2xl font-bold text-slate-800">Select a Teacher</h3>
                <p class="mt-2 text-sm text-slate-500">Choose a teacher from the cards above to view their daily subject progress.</p>
            </div>
        </section>

    </main>
    
    {{-- Assign Task Modal (Retained for Admin assignment functionality) --}}
    <div id="assignTaskModal" class="modal fade" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold" id="assignTaskModalLabel">
                        <i class="bi bi-send-fill me-2"></i> Assign New Task to <span id="modalTeacherName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignTaskForm" action="{{ route('teacher_diary.store_task') }}" method="POST">
                    @csrf
                    <input type="hidden" name="teacher_id" id="modalTeacherId">
                    <div class="modal-body p-4">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Select Class(es) <span class="text-danger">*</span></label>
                                <select multiple name="class_ids[]" id="class_ids" class="form-select shadow-sm" required>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted">Hold Ctrl/Cmd to select multiple.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Select Subject(s) <span class="text-danger">*</span></label>
                                <select multiple name="subject_ids[]" id="subject_ids" class="form-select shadow-sm" required disabled>
                                    {{-- Subjects options go here --}}
                                </select>
                                <div class="form-text text-muted">Select a class first.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" id="due_date" class="form-control shadow-sm" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="homework_assignment" class="form-label fw-semibold">Assignment / Task <span class="text-danger">*</span></label>
                                <textarea name="homework_assignment" id="homework_assignment" rows="3" class="form-control shadow-sm" required></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="modalSubmitBtn" class="btn btn-gradient-primary rounded-pill px-4">
                            <i class="bi bi-save me-1"></i> Assign Task(s)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Progress Update Modal (REMOVED: Now handled inline in subject cards) --}}

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // NOTE: CSRF token retrieval is typically placed in the main layout file if using Laravel's default setup.
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
            // const teacherSelector = document.getElementById('teacherSelector'); // REMOVED
            const dateFilter = document.getElementById('dateFilter'); // Hidden filter date
            const displayDateFilter = document.getElementById('displayDateFilter'); // Visible filter date
            const teacherContent = document.getElementById('teacherContent');
            const assignTaskBtn = document.getElementById('assignTaskBtn');
            const monthlyReportLink = document.getElementById('monthlyReportLink');
            
            // Modal Elements (Kept for Assign Task functionality)
            const classIdsSelect = document.getElementById('class_ids');
            const subjectIdsSelect = document.getElementById('subject_ids');
            const dueDateInput = document.getElementById('due_date'); 
            
            // Modals and Forms
            const assignTaskModal = new bootstrap.Modal(document.getElementById('assignTaskModal'));
            const assignTaskForm = document.getElementById('assignTaskForm');

            let selectedTeacherId = null;

            // --- 1. RENDER FUNCTIONS ---
            function formatDisplayDate(dateString) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                if (!dateString) return 'N/A';
                
                const parts = dateString.split('-');
                
                // Check for valid YYYY-MM-DD structure
                if (parts.length !== 3 || isNaN(parseInt(parts[0])) || isNaN(parseInt(parts[1])) || isNaN(parseInt(parts[2]))) {
                    return 'Invalid Date Format'; 
                }

                const localDate = new Date(parts[0], parts[1] - 1, parts[2]);

                if (isNaN(localDate.getTime())) {
                    return 'Today'; 
                }

                return localDate.toLocaleDateString('en-US', options);
            }

            function renderTeacherRecord(data) {
                const teacher = data.teacher;
                const stats = data.stats;
                const groupedSubjects = data.subject_cards; // NEW structure
                const filterDateDisplay = formatDisplayDate(displayDateFilter.value); // Get date from visible filter

                let progressHtml = `
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-2xl shadow-xl shadow-indigo-300/40 mb-8">
                        <div class="flex items-center space-x-5">
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-white/50 shadow-md" 
                                src="${teacher.avatar_url || 'https://placehold.co/80x80/cccccc/333333?text=N/A'}" 
                                onerror="this.onerror=null;this.src='https://placehold.co/80x80/cccccc/333333?text=N/A';"
                                alt="${teacher.name}">
                            <div>
                                <h2 class="text-3xl font-bold">${teacher.name}</h2>
                                <p class="text-sm opacity-80">Daily Subjects and Progress for ${filterDateDisplay}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-white/30 flex justify-between text-xs font-medium">
                            <span>Total Daily Tasks: ${stats.total}</span>
                            <span>Completed: ${stats.completed}</span>
                            <span>Verified: ${stats.verified}</span>
                            <span>Pending: ${stats.pending}</span>
                        </div>
                    </div>
                `;
                
                // Add the new subject/progress cards
                progressHtml += renderSubjectProgressCards(groupedSubjects);
                
                teacherContent.innerHTML = progressHtml;
                // Re-attach the progress update listener to the newly rendered cards
                attachCardListeners();
            }

            function renderSubjectProgressCards(groupedSubjects) {
                if (Object.keys(groupedSubjects).length === 0) {
                    const currentDay = new Date(displayDateFilter.value).toLocaleDateString('en-US', { weekday: 'long' });
                    
                    return `
                        <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg">
                            No scheduled lectures or assignments found for this teacher on <strong>${currentDay}</strong>.
                        </div>
                    `;
                }

                let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
                
                // Get the date from the visible filter
                const currentDueDate = displayDateFilter.value; 
                const currentDueDateDisplay = formatDisplayDate(currentDueDate);
                const currentDayOfWeek = new Date(currentDueDate).toLocaleDateString('en-US', { weekday: 'long' });

                // Iterate through classes (outer group)
                for (const [className, subjects] of Object.entries(groupedSubjects)) {
                    // Iterate through subjects within that class
                    subjects.forEach(subject => {
                        const assignment = subject.assignment;
                        const schedule = subject.schedule; // NEW: Schedule details
                        
                        // Status determination
                        let status = assignment ? assignment.status : 'no_entry';
                        let statusText = status === 'verified' ? 'Verified' :
                                         status === 'completed' ? 'Completed' :
                                         status === 'pending' ? 'Pending' :
                                         'No Entry Yet';
                        
                        let statusBadgeClass = status === 'verified' ? 'bg-blue-600' :
                                               status === 'completed' ? 'bg-green-600' :
                                               status === 'pending' ? 'bg-yellow-600' :
                                               'bg-slate-500';

                        // Assignment / Task details
                        let currentAssignmentText = assignment ? (assignment.homework_assignment || '') : '';
                        let currentNotes = assignment ? (assignment.teacher_notes || '') : '';

                        // Determine the unique key for the card form submission
                        const cardKey = `${subject.class_id}-${subject.id}`;
                        
                        // Time Display (Show scheduled time if available)
                        const timeDisplay = schedule 
                            ? `${schedule.start_time} - ${schedule.end_time}` 
                            : 'Manual Entry';

                        html += `
                            <div class="bg-white p-5 rounded-2xl shadow-lg border border-slate-200">
                                
                                {{-- *** CARD HEADER STRUCTURE: Class | Day/Time | Subject *** --}}
                                <div class="bg-indigo-100 p-3 -mx-5 -mt-5 mb-4 rounded-t-2xl border-b border-indigo-200">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        {{-- LEFT: Class Name --}}
                                        <span class="text-xs font-semibold text-slate-600 uppercase">${subject.class_name}</span>
                                        {{-- RIGHT: Day and Time --}}
                                        <span class="text-xs font-semibold text-slate-600 text-nowrap">
                                            ${currentDayOfWeek} (${timeDisplay})
                                        </span>
                                    </div>
                                    {{-- CENTER: Subject Name --}}
                                    <h4 class="font-bold text-lg text-indigo-700 text-center">${subject.name}</h4>
                                </div>
                                {{-- *** END NEW CARD HEADER *** --}}
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <p class="text-sm text-slate-500 fw-semibold">Daily Status:</p>
                                    <span class="badge ${statusBadgeClass} text-white px-3 py-1 rounded-full text-xs">${statusText.toUpperCase()}</span>
                                </div>
                                
                                <form class="progress-card-form" data-assignment-id="${assignment ? assignment.id : ''}" data-subject-id="${subject.id}" data-class-id="${subject.class_id}">
                                    <input type="hidden" name="teacher_id" value="${selectedTeacherId}">
                                    <input type="hidden" name="due_date" value="${currentDueDate}">
                                    
                                    <div class="mb-3">
                                        <label for="assignment_text_${cardKey}" class="form-label fw-semibold text-sm">Assignment / Task</label>
                                        <textarea name="homework_assignment" id="assignment_text_${cardKey}" rows="2" class="form-control form-control-sm shadow-sm" required>${currentAssignmentText}</textarea>
                                        <div class="form-text text-muted">Enter the task/topic covered today.</div>
                                    </div>

                                    {{-- PROGRESS INPUT FIELD --}}
                                    <div class="mb-4">
                                        <label for="progress_notes_${cardKey}" class="form-label fw-semibold text-sm">Progress/Notes (Save your work here)</label>
                                        <textarea name="teacher_notes" id="progress_notes_${cardKey}" rows="2" class="form-control form-control-sm shadow-sm">${currentNotes}</textarea>
                                    </div>
                                    
                                    <input type="hidden" name="status" value="completed">
                                    
                                    <button type="submit" class="btn btn-primary w-full mt-2 rounded-xl py-2 progress-submit-btn">
                                        <i class="fa-solid fa-save me-1"></i> Save Progress
                                    </button>
                                </form>
                            </div>
                        `;
                    });
                }
                
                html += '</div>';
                return html;
            }
            
            // --- 2. AJAX FETCH FUNCTIONS ---
            // (Keeping fetchSubjectsAndCheckAssignments for the Admin Assign Task modal)
            async function fetchSubjectsAndCheckAssignments() {
                // ... (Original logic for Admin Modal)
                const selectedClassIds = Array.from(classIdsSelect.selectedOptions).map(option => option.value);
                
                // Reset and disable subjects field initially
                subjectIdsSelect.innerHTML = '';
                subjectIdsSelect.disabled = true;
                subjectIdsSelect.parentElement.querySelector('.form-text').textContent = 'Loading subjects...';
                
                if (selectedClassIds.length === 0) {
                    subjectIdsSelect.disabled = true;
                    subjectIdsSelect.parentElement.querySelector('.form-text').textContent = 'Select a class first.';
                    return;
                }
                
                const dueDate = dueDateInput.value;
                const teacherId = document.getElementById('modalTeacherId').value;
                
                const url = `{{ route('teacher_diary.get_subjects') }}?class_ids=${selectedClassIds.join(',')}&due_date=${dueDate}&teacher_id=${teacherId}`;

                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }
                    const data = await response.json();
                    
                    let subjectsHtml = '';
                    if (data.subjects.length === 0) {
                        subjectsHtml = '<option disabled>No subjects available for selected classes.</option>';
                    } else {
                        data.subjects.forEach(subject => {
                            const isAssigned = data.assigned_subjects.includes(subject.id);
                            const assignedLabel = isAssigned ? ' (Assigned)' : '';
                            const disabledAttr = isAssigned ? 'disabled' : ''; 
                            
                            subjectsHtml += `<option value="${subject.id}" ${disabledAttr}>
                                ${subject.name}${assignedLabel}
                            </option>`;
                        });
                    }

                    subjectIdsSelect.innerHTML = subjectsHtml;
                    subjectIdsSelect.disabled = false;
                    subjectIdsSelect.parentElement.querySelector('.form-text').textContent = data.subjects.length > 0 ? 'Hold Ctrl/Cmd to select multiple.' : 'No subjects available.';
                    
                } catch (error) {
                    console.error('Error fetching subjects:', error);
                    subjectIdsSelect.innerHTML = '<option disabled>Failed to load subjects.</option>';
                    subjectIdsSelect.disabled = true;
                    subjectIdsSelect.parentElement.querySelector('.form-text').textContent = 'Error loading subjects.';
                }
            }


            function fetchTeacherRecord(teacherId) {
                // Ensure a teacher is selected
                if (!teacherId) return;

                const filterDate = dateFilter.value; // <-- Use the hidden filter date
                teacherContent.innerHTML = `<div class="text-center p-10"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading teacher data...</p></div>`;
                selectedTeacherId = teacherId;
                
                const maxRetries = 3;
                let retryCount = 0;

                async function attemptFetch() {
                    try {
                        // Pass the selected filter date to the controller
                        const url = `{{ url('teacher-diary/record') }}/${teacherId}?filter_date=${filterDate}`;
                        const response = await fetch(url);
                        
                        if (response.status === 404) {
                            teacherContent.innerHTML = `<div class="text-center p-10 bg-red-50 text-red-700 rounded-lg"><p class="fw-bold">Teacher ID not found or unauthorized.</p></div>`;
                            return;
                        }

                        if (!response.ok) {
                            throw new Error(`HTTP Error: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        renderTeacherRecord(data);

                    } catch (error) {
                        if (retryCount < maxRetries) {
                            retryCount++;
                            const delay = Math.pow(2, retryCount) * 1000;
                            setTimeout(attemptFetch, delay);
                        } else {
                            console.error('Error fetching teacher data after retries:', error);
                            teacherContent.innerHTML = `<div class="text-center p-10 bg-red-50 text-red-700 rounded-lg"><p class="fw-bold">Failed to load record after multiple attempts.</p><p class="text-sm mt-1">${error.message}</p></div>`;
                        }
                    }
                }
                
                attemptFetch();
            }

            // --- 3. EVENT LISTENERS & FORM LOGIC ---
            
            // Teacher Card Click Listener (Replaces teacherSelector change)
            document.getElementById('teacherListContainer').addEventListener('click', function(e) {
                const button = e.target.closest('.teacher-card-btn');
                if (button) {
                    const teacherId = button.dataset.id;
                    // Visually mark selected teacher
                    document.querySelectorAll('.teacher-card-btn').forEach(btn => {
                        btn.classList.remove('border-indigo-600', 'bg-indigo-100');
                    });
                    button.classList.add('border-indigo-600', 'bg-indigo-100');

                    if (teacherId) {
                        fetchTeacherRecord(teacherId);
                        assignTaskBtn.disabled = false;
                        monthlyReportLink.disabled = false;
                        
                        // Update Modal and Report Links
                        document.getElementById('modalTeacherId').value = teacherId;
                        document.getElementById('modalTeacherName').textContent = button.dataset.name;
                        monthlyReportLink.href = `{{ route('teacher_diary.monthly_report') }}?teacher_id=${teacherId}&month=${new Date().toISOString().slice(0, 7)}`;
                        
                        // Reset assignment form fields on teacher change
                        assignTaskForm.reset();
                        classIdsSelect.selectedIndex = -1;
                        subjectIdsSelect.innerHTML = '';
                        subjectIdsSelect.disabled = true;
                    } 
                }
            });

            // *** Date Filter Change (The visible one) ***
            displayDateFilter.addEventListener('change', function() {
                // Update the hidden filter date used in the fetch request
                dateFilter.value = this.value; 
                if (selectedTeacherId) {
                    fetchTeacherRecord(selectedTeacherId);
                }
            });
            
            // Handle dynamic subject loading on class change (for Admin modal)
            classIdsSelect.addEventListener('change', fetchSubjectsAndCheckAssignments);
            // Handle re-checking subjects when due date changes (for Admin modal)
            dueDateInput.addEventListener('change', fetchSubjectsAndCheckAssignments);
            
            // Setup when Admin Assign Task modal opens
            assignTaskModal._element.addEventListener('show.bs.modal', () => {
                // Set default due date to today
                if (!dueDateInput.value) {
                    dueDateInput.value = new Date().toISOString().slice(0, 10);
                }
                
                // Clear subjects and update instructional text
                subjectIdsSelect.innerHTML = '';
                subjectIdsSelect.disabled = true;
                subjectIdsSelect.parentElement.querySelector('.form-text').textContent = 'Select a class first.';

                if (classIdsSelect.selectedOptions.length > 0) {
                     fetchSubjectsAndCheckAssignments();
                }
            });


            // Assign Task Modal Open (Handler for button click)
            assignTaskBtn.addEventListener('click', () => {
                assignTaskForm.reset();
                assignTaskModal.show();
            });

            // Assign Task Form Submission (POST - Admin Function)
            assignTaskForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = document.getElementById('modalSubmitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Assigning...';
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        alert(data.message);
                        assignTaskModal.hide();
                        // Re-fetch record if the assignment date matches the current view date
                        if (selectedTeacherId && dateFilter.value === dueDateInput.value) {
                            fetchTeacherRecord(selectedTeacherId);
                        }
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.error || 'Assignment failed.';
                        alert('Error: ' + errorMsg);
                    }
                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('Network error occurred.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Assign Task(s)';
                }
            });
            
            // *** NEW: Progress Card Form Submission Logic (Inline Update/Create) ***
            function attachCardListeners() {
                teacherContent.querySelectorAll('.progress-card-form').forEach(form => {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const assignmentId = this.dataset.assignmentId;
                        const subjectId = this.dataset.subjectId;
                        const classId = this.dataset.classId;
                        const teacherId = selectedTeacherId;
                        const dueDate = this.querySelector('input[name="due_date"]').value;
                        
                        const submitBtn = this.querySelector('.progress-submit-btn');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa-solid fa-arrow-rotate-right spin"></i> Saving...';

                        const formData = new FormData(this);
                        
                        let url = '';
                        let method = 'POST';
                        
                        if (assignmentId) {
                            // 1. UPDATE: If an assignment exists, update its progress
                            url = `/teacher-diary/progress/${assignmentId}`; 
                            
                            const payload = {};
                            for (let [key, value] of formData.entries()) {
                                payload[key] = value;
                            }
                            
                            try {
                                const response = await fetch(url, {
                                    method: 'POST', 
                                    headers: { 
                                        'X-CSRF-TOKEN': csrfToken, 
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json' 
                                    },
                                    body: JSON.stringify(payload)
                                });
                                
                                const data = await response.json();
                                
                                if (!response.ok) {
                                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.error || 'Update failed.';
                                    alert('Error: ' + errorMsg);
                                }
                            } catch (error) {
                                console.error('Fetch Error (Update):', error);
                                alert('Network error occurred during update.');
                            }
                            
                        } else {
                            // 2. CREATE: If no assignment exists for this date/subject, create one
                            const storePayload = new FormData();
                            storePayload.append('_token', csrfToken);
                            storePayload.append('teacher_id', teacherId);
                            storePayload.append('due_date', dueDate);
                            storePayload.append('homework_assignment', formData.get('homework_assignment'));
                            // storeTask expects arrays for class_ids and subject_ids
                            storePayload.append('class_ids[]', classId);
                            storePayload.append('subject_ids[]', subjectId);
                            
                            url = assignTaskForm.action; // Use the existing store_task route
                            
                            try {
                                const response = await fetch(url, {
                                    method: 'POST',
                                    headers: { 'Accept': 'application/json' },
                                    body: storePayload
                                });

                                const data = await response.json();

                                if (!response.ok) {
                                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.error || 'Creation failed.';
                                    alert('Error: ' + errorMsg);
                                }
                            } catch (error) {
                                console.error('Fetch Error (Create):', error);
                                alert('Network error occurred during creation.');
                            }
                        }
                        
                        // Re-fetch record to show updated status/notes and get new assignment ID
                        fetchTeacherRecord(selectedTeacherId);
                        
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Save Progress';
                    });
                });
            }
            
            // --- INITIAL LOAD ---
            displayDateFilter.value = new Date().toISOString().slice(0, 10);
            dateFilter.value = new Date().toISOString().slice(0, 10);
            
            // Try to load the first teacher automatically if available
            const initialTeacherCard = document.querySelector('.teacher-card-btn');
            if (initialTeacherCard) {
                const initialTeacherId = initialTeacherCard.dataset.id;
                
                initialTeacherCard.classList.add('border-indigo-600', 'bg-indigo-100'); // Select it visually
                
                assignTaskBtn.disabled = false;
                monthlyReportLink.disabled = false;
                
                document.getElementById('modalTeacherId').value = initialTeacherId;
                document.getElementById('modalTeacherName').textContent = initialTeacherCard.dataset.name;
                monthlyReportLink.href = `{{ route('teacher_diary.monthly_report') }}?teacher_id=${initialTeacherId}&month=${new Date().toISOString().slice(0, 7)}`;
                
                fetchTeacherRecord(initialTeacherId);
            }
        });
    </script>
    @endpush
</body>
@endsection