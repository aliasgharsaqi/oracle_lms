@extends('layouts.admin')

@section('title', 'Teacher Diary')
@section('page-title', 'Teacher Daily Diary')

@section('content')
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
        
        {{-- Teacher Selector and Action Buttons with Date Filter --}}
    <div class="bg-white p-6 rounded-2xl shadow-2xl border border-slate-200 mb-8 max-w-7xl mx-auto">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 items-start">
        
        {{-- Selector --}}
        {{-- WIDTH ADJUSTED: Mobile col-span-2 for prominence, Desktop col-span-1 for alignment --}}
        <div class="relative"> 
            <h2 class="text-base font-bold text-slate-800 mb-2 uppercase tracking-wider">Teacher Selection</h2>
            <div class="relative">
                 <select id="teacherSelector" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3.5 transition shadow-inner hover:border-indigo-400 appearance-none pr-10">
                    <option value="" disabled selected>Select a teacher...</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" 
                            data-pic="{{ asset('storage/' . $teacher->user->user_pic) }}"
                            data-name="{{ $teacher->user->name }}">
                            {{ $teacher->user->name }} ({{ $teacher->user->email }})
                        </option>
                    @endforeach
                </select>
                {{-- Custom Chevron Down Icon for visual appeal --}}
                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        {{-- Date Filter --}}
        <div class="relative">
            <h2 class="text-base font-bold text-slate-800 mb-2 uppercase tracking-wider">Filter Date</h2>
            <input type="date" id="dateFilter" class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3.5 transition shadow-inner hover:border-indigo-400" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>
        
        {{-- Action Buttons (Vertical Column Alignment) --}}
        {{-- This section is aligned vertically and takes up the remaining space --}}
        <div class="flex flex-row items-end md:items-center justify-center gap-3 md:gap-4 mt-4 md:mt-0 md:pt-3"> 
            
            {{-- Button 1: Assign Task --}}
            <button id="assignTaskBtn" class="w-full md:w-auto px-4 py-2.5 text-white font-semibold rounded-xl transition disabled:opacity-50 bg-green-600 hover:bg-green-700 shadow-md">
                <i class="fa-solid fa-plus-circle me-1"></i> Assign Task
            </button>
            
            {{-- Button 2: Monthly Report --}}
            <a id="monthlyReportLink" href="{{ route('teacher_diary.monthly_report') }}" 
               class="w-full md:w-auto px-4 py-2.5 font-semibold transition disabled:opacity-50 border border-indigo-500 text-indigo-600 rounded-xl hover:bg-indigo-50 shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-check me-1"></i> Monthly Report
            </a>
            
        </div>
    </div>
</div>

        {{-- Teacher Content Area --}}
        <section id="teacherContent" class="max-w-7xl mx-auto">
            <div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200">
                <svg class="mx-auto h-16 w-16 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75h.007v.008h-.007V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM5.75 6.75h.007v.008H5.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM12 3.75h.007v.008H12V3.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM5.75 14.25h.007v.008H5.75V14.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM15.75 14.25h.007v.008h-.007V14.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM12 11.25h.007v.008H12V11.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15M15 4.5v15" /></svg>
                <h3 class="mt-4 text-2xl font-bold text-slate-800">Select a Teacher</h3>
                <p class="mt-2 text-sm text-slate-500">Choose a teacher from the dropdown above to view their assignments and progress details.</p>
            </div>
        </section>

    </main>
    
    {{-- Assign Task Modal --}}
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
                                {{-- We added a change listener to this select via JavaScript --}}
                                <select multiple name="class_ids[]" id="class_ids" class="form-select shadow-sm" required>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted">Hold Ctrl/Cmd to select multiple.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Select Subject(s) <span class="text-danger">*</span></label>
                                {{-- Subjects will be loaded dynamically by JS based on classes --}}
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
    
    {{-- Progress Update Modal --}}
    <div id="progressUpdateModal" class="modal fade" tabindex="-1" aria-labelledby="progressUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-bold" id="progressUpdateModalLabel">Update Task Progress</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="progressUpdateForm">
                    @csrf
                    <input type="hidden" name="assignment_id" id="progressAssignmentId">
                    <div class="modal-body p-4">
                        <p class="mb-3">Task: <strong id="progressTaskTitle"></strong></p>
                        
                        <div class="mb-3">
                            <label for="progressStatus" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="progressStatus" class="form-select shadow-sm" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed (By Teacher)</option>
                                <option value="verified">Verified (By Admin)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="teacher_notes" class="form-label fw-semibold">Teacher/Admin Notes (on Completion)</label>
                            <textarea name="teacher_notes" id="progressTeacherNotes" rows="3" class="form-control shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient-success rounded-pill px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // NOTE: CSRF token retrieval is typically placed in the main layout file if using Laravel's default setup.
            // Assuming it's available, otherwise, it should be passed from the main layout.
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
            const teacherSelector = document.getElementById('teacherSelector');
            const dateFilter = document.getElementById('dateFilter'); // <-- NEW
            const teacherContent = document.getElementById('teacherContent');
            const assignTaskBtn = document.getElementById('assignTaskBtn');
            const monthlyReportLink = document.getElementById('monthlyReportLink');
            
            // Modal Elements
            const classIdsSelect = document.getElementById('class_ids');
            const subjectIdsSelect = document.getElementById('subject_ids');
            const dueDateInput = document.getElementById('due_date'); 
            
            // Modals and Forms
            const assignTaskModal = new bootstrap.Modal(document.getElementById('assignTaskModal'));
            const progressUpdateModal = new bootstrap.Modal(document.getElementById('progressUpdateModal'));
            const assignTaskForm = document.getElementById('assignTaskForm');
            const progressUpdateForm = document.getElementById('progressUpdateForm');

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

                // **FIXED LOGIC**: Use new Date(year, monthIndex, day) to force local time interpretation
                // This prevents the date shifting backwards due to timezone offsets.
                const localDate = new Date(parts[0], parts[1] - 1, parts[2]);

                if (isNaN(localDate.getTime())) {
                    return 'Today'; 
                }

                return localDate.toLocaleDateString('en-US', options);
            }

            function renderTeacherRecord(data) {
                const teacher = data.teacher;
                const stats = data.stats;
                const groupedAssignments = data.assignments;
                const filterDateDisplay = formatDisplayDate(dateFilter.value); // Get date from new filter

                const progressHtml = `
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 rounded-2xl shadow-xl shadow-indigo-300/40 mb-8">
                        <div class="flex items-center space-x-5">
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-white/50 shadow-md" 
                                src="${teacher.avatar_url || 'https://placehold.co/80x80/cccccc/333333?text=N/A'}" 
                                onerror="this.onerror=null;this.src='https://placehold.co/80x80/cccccc/333333?text=N/A';"
                                alt="${teacher.name}">
                            <div>
                                <h2 class="text-3xl font-bold">${teacher.name}</h2>
                                <p class="text-sm opacity-80">Assignments and Progress Overview for ${filterDateDisplay}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-slate-800 mb-4">Overall Progress</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-center">
                            <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-indigo-600">${stats.total}</p><p class="text-sm font-semibold text-slate-500">Total Assigned</p></div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-amber-600">${stats.pending}</p><p class="text-sm font-semibold text-slate-500">Pending</p></div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-green-600">${stats.completed}</p><p class="text-sm font-semibold text-slate-500">Completed</p></div>
                            <div class="bg-white p-4 rounded-lg shadow-sm border"><p class="text-2xl font-bold text-blue-600">${stats.verified}</p><p class="text-sm font-semibold text-slate-500">Verified (Admin)</p></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 mb-4">Assignments on ${filterDateDisplay}</h3>
                            <div class="space-y-4">
                                ${renderAssignments(groupedAssignments)}
                            </div>
                        </div>
                    </div>
                `;
                teacherContent.innerHTML = progressHtml;
            }

            function renderAssignments(groupedAssignments) {
                if (Object.keys(groupedAssignments).length === 0) {
                    return '<div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg">No active assignments found for this teacher on the selected date.</div>';
                }

                let html = '';
                for (const [classSubject, assignments] of Object.entries(groupedAssignments)) {
                    html += `<div class="bg-white p-4 rounded-xl shadow-md border-b-4 border-indigo-400">
                        <h4 class="font-bold text-lg text-indigo-700 mb-3">${classSubject}</h4>
                        <ul class="list-group list-group-flush">`;

                    assignments.forEach(assignment => {
                        let statusColor = assignment.status === 'verified' ? 'bg-blue-100 text-blue-800 border-blue-400' :
                                          assignment.status === 'completed' ? 'bg-green-100 text-green-800 border-green-400' :
                                          'bg-yellow-100 text-yellow-800 border-yellow-400';
                        
                        // **FIXED LOGIC**: Use the robust local time comparison for overdue status
                        let isOverdue = false;
                        if (assignment.due_date) {
                            const parts = assignment.due_date.split('-');
                            // Create date objects using local time construction
                            const assignmentDate = new Date(parts[0], parts[1] - 1, parts[2]);
                            const today = new Date();
                            const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate());

                            isOverdue = assignmentDate < todayMidnight && 
                                        assignment.status !== 'completed' && 
                                        assignment.status !== 'verified';
                        }
                        
                        let dateClasses = isOverdue ? 'text-danger fw-bold' : 'text-muted';
                        let overdueBadge = isOverdue ? '<span class="badge bg-danger ms-2">OVERDUE</span>' : '';

                        html += `<li class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="flex-grow-1 me-3">
                                <p class="mb-1 fw-semibold text-slate-800">${assignment.homework_assignment}</p>
                                <p class="text-xs ${dateClasses}">Due: ${formatDisplayDate(assignment.due_date)} ${overdueBadge}</p>
                                ${assignment.teacher_notes ? `<p class="text-xs text-info mt-1">Notes: ${assignment.teacher_notes}</p>` : ''}
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="badge rounded-pill ${statusColor} text-xs border border-1 px-3 py-1">${assignment.status.toUpperCase()}</span>
                                <button type="button" class="btn btn-sm btn-outline-success update-progress-btn" 
                                    data-id="${assignment.id}"
                                    data-status="${assignment.status}"
                                    data-notes="${assignment.teacher_notes || ''}"
                                    data-task="${assignment.homework_assignment}">
                                    Update
                                </button>
                            </div>
                        </li>`;
                    });

                    html += `</ul></div>`;
                }
                return html;
            }
            
            // --- 2. AJAX FETCH FUNCTIONS ---
            
            /**
             * Fetches subjects based on selected class IDs.
             * Also checks for existing assignments on the selected date to mark subjects as potentially pre-assigned.
             */
            async function fetchSubjectsAndCheckAssignments() {
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
                
                // Construct the URL with class_ids array
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
                            // Disable if already assigned
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

                const filterDate = dateFilter.value; // <-- Use the new filter date
                teacherContent.innerHTML = `<div class="text-center p-10"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading teacher data...</p></div>`;
                selectedTeacherId = teacherId;
                
                // --- Simple Fetch with Retry/Backoff Logic ---
                const maxRetries = 3;
                let retryCount = 0;

                async function attemptFetch() {
                    try {
                        // Pass the selected filter date to the controller
                        // NOTE: THIS URL IS CORRECTLY PASSING filter_date NOW
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
                            const delay = Math.pow(2, retryCount) * 1000; // 2s, 4s, 8s
                            setTimeout(attemptFetch, delay);
                        } else {
                            console.error('Error fetching teacher data after retries:', error);
                            teacherContent.innerHTML = `<div class="text-center p-10 bg-red-50 text-red-700 rounded-lg"><p class="fw-bold">Failed to load record after multiple attempts.</p><p class="text-sm mt-1">${error.message}</p></div>`;
                        }
                    }
                }
                
                attemptFetch();
            }

            // --- 3. EVENT LISTENERS & MODAL LOGIC ---
            
            // Teacher Selector Change
            teacherSelector.addEventListener('change', function() {
                const teacherId = this.value;
                if (teacherId) {
                    fetchTeacherRecord(teacherId);
                    assignTaskBtn.disabled = false;
                    monthlyReportLink.disabled = false;
                    
                    // Update Modal and Report Links
                    document.getElementById('modalTeacherId').value = teacherId;
                    document.getElementById('modalTeacherName').textContent = this.options[this.selectedIndex].dataset.name;
                    monthlyReportLink.href = `{{ route('teacher_diary.monthly_report') }}?teacher_id=${teacherId}&month=${new Date().toISOString().slice(0, 7)}`;
                    
                    // Reset assignment form fields on teacher change
                    assignTaskForm.reset();
                    classIdsSelect.selectedIndex = -1; // Deselect all classes
                    subjectIdsSelect.innerHTML = '';
                    subjectIdsSelect.disabled = true;
                } else {
                    assignTaskBtn.disabled = true;
                    monthlyReportLink.disabled = true;
                    teacherContent.innerHTML = `<div class="text-center p-10 bg-white rounded-2xl shadow-lg border border-slate-200"><h3 class="mt-4 text-2xl font-bold text-slate-800">Select a Teacher</h3><p class="mt-2 text-sm text-slate-500">Choose a teacher from the dropdown above to view their assignments and progress details.</p></div>`;
                }
            });

            // *** NEW: Date Filter Change ***
            dateFilter.addEventListener('change', function() {
                if (selectedTeacherId) {
                    fetchTeacherRecord(selectedTeacherId);
                }
            });
            
            // *** Handle dynamic subject loading on class change (for modal) ***
            classIdsSelect.addEventListener('change', fetchSubjectsAndCheckAssignments);
            // *** Handle re-checking subjects when due date changes (for modal) ***
            dueDateInput.addEventListener('change', fetchSubjectsAndCheckAssignments);
            
            // *** Setup when modal opens ***
            assignTaskModal._element.addEventListener('show.bs.modal', () => {
                // Set default due date to today
                if (!dueDateInput.value) {
                    dueDateInput.value = new Date().toISOString().slice(0, 10);
                }
                
                // Clear subjects and update instructional text
                subjectIdsSelect.innerHTML = '';
                subjectIdsSelect.disabled = true;
                subjectIdsSelect.parentElement.querySelector('.form-text').textContent = 'Select a class first.';

                // Check if classes were selected before modal opened (though generally, we assume they clear the form)
                if (classIdsSelect.selectedOptions.length > 0) {
                     fetchSubjectsAndCheckAssignments();
                }
            });


            // Assign Task Modal Open (Handler for button click)
            assignTaskBtn.addEventListener('click', () => {
                assignTaskForm.reset();
                assignTaskModal.show();
            });

            // Assign Task Form Submission (POST)
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
                        // Re-fetch record to show new assignment for the currently selected date
                        // Condition ensures refresh only happens if the assigned date matches the currently filtered view date
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
                    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Assign Task(s)';
                }
            });
            
            // Progress Update Modal Open/Submission
            teacherContent.addEventListener('click', function(e) {
                const button = e.target.closest('.update-progress-btn');
                if (button) {
                    const id = button.dataset.id;
                    const status = button.dataset.status;
                    const notes = button.dataset.notes;
                    const task = button.dataset.task;

                    document.getElementById('progressAssignmentId').value = id;
                    document.getElementById('progressTaskTitle').textContent = task;
                    document.getElementById('progressStatus').value = status;
                    document.getElementById('progressTeacherNotes').value = notes;
                    
                    progressUpdateModal.show();
                }
            });
            
            progressUpdateForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const assignmentId = document.getElementById('progressAssignmentId').value;
                // Note: The URL assumes the dynamic route structure from web.php
                const url = `/teacher-diary/progress/${assignmentId}`; 
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Updating...';

                // Convert FormData to JSON object manually for JSON payload
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
                            'Content-Type': 'application/json' // Crucial for passing JSON data
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await response.json();

                    if (response.ok) {
                        alert(data.message);
                        progressUpdateModal.hide();
                        // Refresh view for the currently selected date
                        if (selectedTeacherId) {
                            fetchTeacherRecord(selectedTeacherId); 
                        }
                    } else {
                        const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.error || 'Update failed.';
                        alert('Error: ' + errorMsg);
                    }
                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('Network error occurred.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Update';
                }
            });
            
            // --- INITIAL LOAD ---
            // Set date filter to today immediately
            dateFilter.value = new Date().toISOString().slice(0, 10);
            
            // Try to load the first teacher automatically if available
            const initialTeacherId = teacherSelector.options[1]?.value; 
            if (initialTeacherId) {
                teacherSelector.value = initialTeacherId;
                assignTaskBtn.disabled = false;
                monthlyReportLink.disabled = false;
                document.getElementById('modalTeacherId').value = initialTeacherId;
                document.getElementById('modalTeacherName').textContent = teacherSelector.options[1].dataset.name;
                // Ensure correct route generation for the initial load
                monthlyReportLink.href = `{{ route('teacher_diary.monthly_report') }}?teacher_id=${initialTeacherId}&month=${new Date().toISOString().slice(0, 7)}`;
                fetchTeacherRecord(initialTeacherId);
            }
        });
    </script>
    @endpush
</body>
@endsection