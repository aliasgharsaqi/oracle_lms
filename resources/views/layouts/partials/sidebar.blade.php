<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-buildings-fill me-2" style="font-size: 2rem;"></i>
        <span class="fs-4">School Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        @can('Manage User')
        <li>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-people-fill me-2"></i>
                Users
            </a>
        </li>
         @endcan
           <li>
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-people-fill me-2"></i>
                Role & Permission
            </a>
        </li>
           @can('Manage Classes')
        <li>
            <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-workspace me-2"></i>
                Classes
            </a>
        </li>
        @endcan
        @can('Manage Teachers')
        <li>
            <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-video3 me-2"></i>
                Teachers
            </a>
        </li>
            
        @endcan
        @can('Manage Subject')
            
       
        <li>
            <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-journal-bookmark-fill me-2"></i>
                Subjects
            </a>
        </li>
         @endcan
         @can('Manage Schedules')
             
         
        <li>
            <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-calendar3 me-2"></i>
                Schedules
            </a>
        </li>
        @endcan
        @can('Manage Admission')
            
      
        <li>
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-check-fill me-2"></i>
                Admissions
            </a>
        </li>
          @endcan
          @can('Manage Fees')
              
        
        <li>
            <a href="#feeSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="nav-link text-white dropdown-toggle">
                <i class="bi bi-cash-coin me-2"></i>
                Fee Management
            </a>
            <ul class="collapse list-unstyled {{ request()->routeIs('fees.*') ? 'show' : '' }}" id="feeSubmenu">
               @can('Manage Student Fees Plan')
                   
              
                <li>
                    {{-- This link is updated --}}
                    <a href="{{ route('fees.plans.index') }}" class="nav-link text-white ps-4">Student Fee Plans</a>
                </li>
                 @endcan
                 @can('Manage Collection of Fees')
                     
                 
                <li>
                    <a href="{{ route('fees.payments.index') }}" class="nav-link text-white ps-4">Collect Fees</a>
                </li>
                @endcan
            </ul>

        </li>
           @endcan

           @can('Manage Reports')
               
         
           <li>
            <a href="#feeSubmenua" data-bs-toggle="collapse" aria-expanded="false" class="nav-link text-white dropdown-toggle">
                <i class="bi bi-cash-coin me-2"></i>
                Reports
            </a>
            <ul class="collapse list-unstyled {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="feeSubmenua">
                @can('View Paid Student Reports')
                <li>
                    {{-- This link is updated --}}
                    <a href="{{ route('reports.paidFees') }}" class="nav-link text-white ps-4">Paid Student</a>
                </li>
                  @endcan
                   @can('View Pending Student Reports')
                <li>
                    <a href="{{ route('reports.pendingFees') }}" class="nav-link text-white ps-4">Pending Student</a>
                </li>
                  @endcan
                   @can('View Monthly Income Reports')
                 <li>
                    <a href="{{ route('reports.monthlyRevenue') }}" class="nav-link text-white ps-4">Monthly Revenue</a>
                </li>
                  @endcan
                   @can('View Total Income Reports')
                <li>
                    <a href="{{ route('reports.totalRevenue') }}" class="nav-link text-white ps-4">Total Income</a>
                </li>
                  @endcan
            </ul>

        </li>
         @endcan
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://placehold.co/32x32/E8E8E8/424242?text={{ substr(Auth::user()->name, 0, 1) }}" alt="" width="32" height="32" class="rounded-circle me-2">
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
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        Sign out
                    </a>
                </form>
            </li>
        </ul>
    </div>
</div>