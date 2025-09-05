<link rel="stylesheet" href="{{ asset('css/style.css') }}">


<!-- Sidebar -->
<nav class="d-flex flex-column flex-shrink-0 p-3 sidebar" id="sidebarMenu">
    <!-- Close Button (Mobile Only) -->
    <button class="btn text-white d-lg-none position-absolute top-0 end-0 my-3 mx-1"
        onclick="document.getElementById('sidebarMenu').classList.remove('show')">
        <i class="bi bi-x-lg fs-4"></i>
    </button>
    <!-- Logo -->
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-14 pr-2 h-auto">
        <span class="fs-4 fw-bold">Neshat us Sania</span>
    </a>
    <hr>

    <!-- Menu Items -->
    <ul class="nav nav-pills flex-column mb-auto mt-4">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        @can('Manage User')
        <li>
            <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-people-fill"></i> Users
            </a>
        </li>
        @endcan

        <li>
            <a href="{{ route('admin.roles.index') }}"
                class="nav-link {{ request()->routeIs('admin.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-shield-lock-fill"></i> Roles & Permissions
            </a>
        </li>

        @can('Manage Classes')
        <li>
            <a href="{{ route('classes.index') }}"
                class="nav-link {{ request()->routeIs('classes.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-workspace"></i> Classes
            </a>
        </li>
        @endcan

        @can('Manage Teachers')
        <li>
            <a href="{{ route('teachers.index') }}"
                class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-video3"></i> Teachers
            </a>
        </li>
        @endcan

        @can('Manage Subject')
        <li>
            <a href="{{ route('subjects.index') }}"
                class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-journal-bookmark-fill"></i> Subjects
            </a>
        </li>
        @endcan

        @can('Manage Schedules')
        <li>
            <a href="{{ route('schedules.index') }}"
                class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-calendar3"></i> Schedules
            </a>
        </li>
        @endcan

        @can('Manage Admission')
        <li>
            <a href="{{ route('students.index') }}"
                class="nav-link {{ request()->routeIs('students.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-check-fill"></i> Admissions
            </a>
        </li>
        @endcan

        @can('Manage Marks')
        <li>
            <a href="{{ route('marks.index') }}"
                class="nav-link {{ request()->routeIs('marks.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-check-fill"></i> Marks
            </a>
        </li>
        @endcan


        @can('Manage Fees')
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link text-white d-flex align-items-center toggle-dropdown">
                <i class="bi bi-cash-coin me-2"></i> Fee Management
            </a>

            <ul class="list-unstyled ps-3 dropdown-submenu" style="display: none;">
                @can('Manage Student Fees Plan')
                <li>
                    <a href="{{ route('fees.plans.index') }}"
                        class="nav-link text-white {{ request()->routeIs('fees.plans.*') ? 'active' : '' }}">
                        Student Fee Plans
                    </a>
                </li>
                @endcan

                @can('Manage Collection of Fees')
                <li>
                    <a href="{{ route('fees.payments.index') }}"
                        class="nav-link text-white {{ request()->routeIs('fees.payments.*') ? 'active' : '' }}">
                        Collect Fees
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        @can('Manage Reports')
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link text-white d-flex align-items-center toggle-dropdown">
                <i class="bi bi-bar-chart-line me-2"></i> Reports
            </a>

            <ul class="list-unstyled ps-3 dropdown-submenu" style="display: none;">
                @can('View Paid Student Reports')
                <li>
                    <a href="{{ route('reports.paidFees') }}"
                        class="nav-link text-white {{ request()->routeIs('reports.paidFees') ? 'active' : '' }}">
                        Paid Students
                    </a>
                </li>
                @endcan

                @can('View Pending Student Reports')
                <li>
                    <a href="{{ route('reports.pendingFees') }}"
                        class="nav-link text-white {{ request()->routeIs('reports.pendingFees') ? 'active' : '' }}">
                        Pending Students
                    </a>
                </li>
                @endcan

                @can('View Monthly Income Reports')
                <li>
                    <a href="{{ route('reports.monthlyRevenue') }}"
                        class="nav-link text-white {{ request()->routeIs('reports.monthlyRevenue') ? 'active' : '' }}">
                        Monthly Revenue
                    </a>
                </li>
                @endcan

                @can('View Total Income Reports')
                <li>
                    <a href="{{ route('reports.totalRevenue') }}"
                        class="nav-link text-white {{ request()->routeIs('reports.totalRevenue') ? 'active' : '' }}">
                        Total Income
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

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

<!-- Toggle Button (Mobile) -->
<button style="height: 50px; position: absolute;" class="btn btn-primary d-lg-none top-0 start-0 m-3" type="button"
    onclick="document.getElementById('sidebarMenu').classList.toggle('show')">
    <i class="bi bi-list fs-3"></i>
</button>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".toggle-dropdown").forEach(function(toggle) {
        toggle.addEventListener("click", function() {
            let submenu = this.nextElementSibling;
            let arrow = this.querySelector("i.bi-chevron-down");

            if (submenu.style.display === "block") {
                submenu.style.display = "none";
                arrow.classList.remove("bi-chevron-up");
                arrow.classList.add("bi-chevron-down");
            } else {
                submenu.style.display = "block";
                arrow.classList.remove("bi-chevron-down");
                arrow.classList.add("bi-chevron-up");
            }
        });
    });
});
</script>