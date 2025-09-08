<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 px-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-lg-none rounded-circle me-3">
         <i class="bi bi-list" style="font-size: 1.5rem;"></i>
    </button>
    
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 text-gray-800">@yield('page-title', 'Dashboard')</h1>
    </div>


    <!-- Topbar Navbar -->
    <ul class="navbar-nav ms-auto">

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bi bi-bell-fill"></i>
                <!-- Counter - Alerts -->
                <span class="badge bg-danger badge-counter rounded-pill" style="font-size: 0.6em; padding: 4px 6px; position: absolute; top: 10px; right: 0;">3+</span>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header bg-primary text-white">
                    Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-primary d-flex justify-content-center align-items-center" style="width:40px; height:40px; border-radius: 50%;">
                            <i class="bi bi-file-earmark-text text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">September 07, 2025</div>
                        <span class="fw-bold">A new monthly report is ready to download!</span>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block mx-2 border-end"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                <img class="img-profile rounded-circle"
                    src="{{ Auth::user()->user_pic ? asset('storage/' . Auth::user()->user_pic) : 'https://placehold.co/60x60/E8E8E8/424242?text=' . substr(Auth::user()->name, 0, 1) }}"
                    style="width: 40px; height: 40px; object-fit: cover;">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <div class="dropdown-header text-center">
                    <h6 class="text-primary fw-bold mb-0">{{ Auth::user()->name }}</h6>
                    <span class="small text-muted">
                        @if(Auth::user()->roles->isNotEmpty())
                        {{ Auth::user()->roles->first()->name }}
                        @endif
                    </span>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person-fill fa-sm fa-fw me-2 text-gray-400"></i>
                    Profile
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right fa-sm fa-fw me-2 text-gray-400"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>

