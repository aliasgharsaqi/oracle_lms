<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<!-- Sidebar -->
<nav class="d-flex flex-column flex-shrink-0 p-3 sidebar" id="sidebarMenu">
    <!-- Logo and Close Button -->
    <div class="sidebar-brand-container d-flex justify-content-between align-items-center">
        <a href="/" class="d-flex align-items-center text-white text-decoration-none">
            <div class="sidebar-brand-icon bg-white rounded-circle d-flex align-items-center justify-content-center">
                @if (Auth::user()->school && Auth::user()->school->logo)
                <img src="{{ asset('storage/' . Auth::user()->school->logo) }}" alt="{{ Auth::user()->school->name }} Logo" style="max-width: 70px; max-height: 90px;">
                @else
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 70px; max-height: 90px;">
                @endif
            </div>
            @if (Auth::user()->school)
            <span class="fs-4 fw-bold ms-3 d-none d-lg-inline">{{ Auth::user()->school->name }}</span>
            @else
            <span class="fs-4 fw-bold ms-3 d-none d-lg-inline">Oracles Force</span>
            @endif
        </a>
        <button class="btn text-white d-lg-none" id="closeSidebarBtn">
            <i class="bi bi-x-lg fs-4"></i>
        </button>
    </div>
    <hr>

    <!-- Menu Items -->
    <ul class="nav nav-pills flex-column mb-auto mt-4">

        <li class="nav-item">
            <a href="{{  route('dashboard') }}"
                class="nav-link d-flex align-items-center {{ (request()->routeIs('dashboard') || request()->routeIs('user.dashboard')) ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @role('Super Admin')
        <li class="nav-item {{ request()->is('admin/schools*') ? 'active' : '' }}">
            <a href="{{ route('admin.schools.index') }}"
                class="nav-link d-flex align-items-center {{ request()->is('admin/schools*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-bank"></i>
                <span>Manage Schools</span>
            </a>
        </li>
        @endrole

        @can('Manage Admission')
        <li>
            <a href="{{ route('students.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('students.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-person-check-fill"></i>
                <span>Admissions</span>
            </a>
        </li>
        @endcan
        
        @can('Manage Fees')
        <li class="nav-item">
          <a href="javascript:void(0);" 
       class="nav-link text-white d-flex align-items-center justify-content-between toggle-dropdown"
       style="gap: 8px; padding: 1px 14px; border-radius: 8px; transition: all 0.3s;">
       <div class="d-flex align-items-center" style="gap: 4px;">
           <i class="bi bi-cash-coin fs-5"></i>
           <span class="fw-semibold">Fee Management</span>
       </div>
       <i class="bi bi-chevron-down dropdown-arrow fs-6"></i>
    </a>


            <ul class="list-unstyled ps-3 dropdown-submenu" style="display: {{ request()->routeIs(['fees.payments.*','fees.plans.*']) ? 'block' : 'none' }};">
                @can('Manage Student Fees Plan')
                <li>
                    <a href="{{ route('fees.plans.index') }}"
                        class="nav-link d-flex align-items-center text-white {{ request()->routeIs('fees.plans.*') ? 'active' : '' }}"
                        style="gap: 6px; padding: 2px 12px;">
                        Student Fee Plans
                    </a>
                </li>
                @endcan

                @can('Manage Collection of Fees')
                <li>
                    <a href="{{ route('fees.payments.index') }}"
                        class="nav-link d-flex align-items-center text-white {{ request()->routeIs('fees.payments.*') ? 'active' : '' }}"
                        style="gap: 6px; padding: 2px 12px;">
                        Collect Fees
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('Manage Reports')
        <li class="nav-item">
            <a href="javascript:void(0);" 
       class="nav-link text-white d-flex align-items-center justify-content-between toggle-dropdown"
       style="gap: 8px; padding: 1px 14px; border-radius: 8px; transition: all 0.3s;">
       <div class="d-flex align-items-center" style="gap: 4px;">
           <i class="bi bi-cash-coin fs-5"></i>
           <span class="fw-semibold">Reports</span>
       </div>
       <i class="bi bi-chevron-down dropdown-arrow fs-6"></i>
    </a>


            <ul class="list-unstyled ps-3 dropdown-submenu" style="display: {{ request()->routeIs(['reports.revenue_dashboard','reports.paid_fees','reports.pending_fees']) ? 'block' : 'none' }};">
                {{-- I've mapped the new dashboard to your existing permission for viewing monthly reports --}}
                @can('View Monthly Income Reports')
                <li>
                    <a href="{{ route('reports.revenue_dashboard') }}"
                        class="nav-link d-flex align-items-center text-white {{ request()->routeIs('reports.revenue_dashboard') ? 'active' : '' }}"
                        style="gap: 6px; padding: 2px 12px;">
                        Revenue Dashboard
                    </a>
                </li>
                @endcan

                @can('View Paid Student Reports')
                <li>
                    <a href="{{ route('reports.paid_fees') }}"
                        class="nav-link d-flex align-items-center text-white {{ request()->routeIs('reports.paid_fees') ? 'active' : '' }}"
                        style="gap: 6px; padding: 2px 12px;">
                        Paid Reports
                    </a>
                </li>
                @endcan

                @can('View Pending Student Reports')
                <li>
                    <a href="{{ route('reports.pending_fees') }}"
                        class="nav-link d-flex align-items-center text-white {{ request()->routeIs('reports.pending_fees') ? 'active' : '' }}"
                        style="gap: 6px; padding: 2px 12px;">
                        Pending Reports
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('Manage User')
        <li>
            <a href="{{ route('users.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('users.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-people-fill"></i>
                <span>Users</span>
            </a>
        </li>
        @endcan

        @can('Manage Roles')
        <li>
            <a href="{{ route('admin.roles.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('admin.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Roles & Permissions</span>
            </a>
        </li>
        @endcan

        @can('Manage Classes')
        <li>
            <a href="{{ route('classes.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('classes.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-person-workspace"></i>
                <span>Classes</span>
            </a>
        </li>
        @endcan

        @can('Manage Subject')
        <li>
            <a href="{{ route('subjects.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('subjects.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>Subjects</span>
            </a>
        </li>
        @endcan

        @can('Manage Teachers')
        <li>
            <a href="{{ route('teachers.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('teachers.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-person-video3"></i>
                <span>Teachers</span>
            </a>
        </li>
        @endcan

        @can('Manage Schedules')
        <li>
            <a href="{{ route('schedules.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('schedules.*') ? 'active' : 'text-white' }}"
                style="gap: 8px; padding: 2px 12px;">
                <i class="bi bi-calendar3"></i>
                <span>Schedules</span>
            </a>
        </li>
        @endcan

   @canany(['Manage Marks'])
<li class="nav-item">
      <a href="javascript:void(0);" 
       class="nav-link text-white d-flex align-items-center justify-content-between toggle-dropdown"
       style="gap: 8px; padding: 1px 14px; border-radius: 8px; transition: all 0.3s;">
       <div class="d-flex align-items-center" style="gap: 4px;">
           <i class="bi bi-cash-coin fs-5"></i>
           <span class="fw-semibold">Examination</span>
       </div>
       <i class="bi bi-chevron-down dropdown-arrow fs-6"></i>
    </a>
    <ul class="list-unstyled ps-3 dropdown-submenu"
        style="display: {{ request()->routeIs(['marks.*','results.*']) ? 'block' : 'none' }};">
        @can('Manage Marks')
        <li>
            <a href="{{ route('marks.index') }}"
                class="nav-link d-flex align-items-center text-white {{ request()->routeIs('marks.*') ? 'active' : '' }}"
                style="gap: 6px; padding: 2px 12px;">
                <i class="bi bi-card-checklist me-2"></i> Marks
            </a>
        </li>
        @endcan

        <li>
            <a href="{{ route('result-cards.index') }}"
                class="nav-link d-flex align-items-center text-white {{ request()->routeIs('results.*') ? 'active' : '' }}"
                style="gap: 6px; padding: 2px 12px;">
                <i class="bi bi-card-list me-2"></i> Result
            </a>
        </li>
    </ul>
</li>
@endcanany

        </li>

        <!-- static routes -->
        @role('Super Admin')
        <li>
            <a href="{{ route('teacher_diary') }}"
                class="nav-link d-flex align-items-center text-white {{ request()->routeIs('teacher_diary') ? 'active' : '' }}"
                style="gap: 6px; padding: 2px 12px;">
                <i class="bi bi-journal-check"></i> <!-- Teacher Diary Icon -->
                Teacher Diary
            </a>
        </li>
        <li>
            <a href="{{ route('student_diary') }}"
                class="nav-link d-flex align-items-center text-white {{ request()->routeIs('student_diary') ? 'active' : '' }}"
                style="gap: 6px; padding: 2px 12px;">
                <i class="bi bi-journal-text"></i> <!-- Student Diary Icon -->
                Student Diary
            </a>
        </li>
        <li>
            <a href="{{ route('attendence') }}"
                class="nav-link d-flex align-items-center text-white {{ request()->routeIs('attendence') ? 'active' : '' }}"
                style="gap: 6px; padding: 2px 12px;">
                <i class="bi bi-journal-text"></i> <!-- Student Diary Icon -->
                Attendence
            </a>
        </li>
        @endif
    </ul>


    <hr>

    <!-- User Dropdown -->
    <div class="dropdown user-info mt-auto">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
            data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://placehold.co/40x40/E8E8E8/424242?text={{ substr(Auth::user()->name, 0, 1) }}" alt=""
                width="40" height="40" class="rounded-circle me-2">
            <strong>{{ Auth::user()->name }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">Sign out</a>
                </form>
            </li>
        </ul>
    </div>
</nav>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-dropdown").forEach(function(toggle) {
            toggle.addEventListener("click", function() {
                let submenu = this.nextElementSibling;
                let arrow = this.querySelector("i.bi-chevron-down");

                if (submenu.style.display === "block") {
                    submenu.style.display = "none";
                    if (arrow) {
                        arrow.classList.remove("bi-chevron-up");
                        arrow.classList.add("bi-chevron-down");
                    }
                } else {
                    submenu.style.display = "block";
                    if (arrow) {
                        arrow.classList.remove("bi-chevron-down");
                        arrow.classList.add("bi-chevron-up");
                    }
                }
            });
        });
    });
</script>