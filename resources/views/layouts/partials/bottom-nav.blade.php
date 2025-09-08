<nav class="bottom-nav d-lg-none">
    <div class="bottom-nav-inner">
        <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('students.index') }}" class="bottom-nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Admissions</span>
        </a>

        <a href="{{ route('fees.payments.index') }}" class="bottom-nav-item {{ request()->routeIs('fees.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i>
            <span>Fees</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="bottom-nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>
    </div>
</nav>