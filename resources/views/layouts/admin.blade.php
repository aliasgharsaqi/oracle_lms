<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'School Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background-color: #f8f9fc;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.9s ease-in-out;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 270px;
            min-height: 100vh;
            background: linear-gradient(180deg, #212529 0%, #343a40 100%);
            color: #fff;
            transition: transform 0.9s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1045;
            transform: translateX(-100%);
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.9s ease-in-out, visibility 0.3s ease-in-out;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Cleaner Active Sidebar Link */
        .sidebar .nav-link.active {
            box-shadow: 0 2px 0px rgba(13, 110, 253, 0.4);
        }

        /* Header Styles */
        .topbar {
            height: 4.375rem;
            position: relative;
            z-index: 1000;
        }

        .topbar-divider {
            width: 0;
            border-right: 1px solid #e3e6f0;
            height: calc(4.375rem - 2rem);
            margin: auto 1rem;
        }

        .dropdown-list {
            width: 20rem !important;
        }

        .dropdown-list .dropdown-header {
            background-color: #4e73df;
            border: 1px solid #4e73df;
            padding: 0.75rem 1rem;
            color: #fff;
        }

        .dropdown-list .dropdown-item {
            white-space: normal;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            border-top: 1px solid #e3e6f0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1030;
            padding: 0.5rem 0;
        }

        .bottom-nav-inner {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #858796;
            font-size: 0.75rem;
            flex-grow: 1;
            transition: color 0.9s;
        }

        .bottom-nav-item i {
            font-size: 1.5rem;
            margin-bottom: 0.1rem;
        }

        .bottom-nav-item.active,
        .bottom-nav-item:hover {
            color: #4e73df;
        }

        /* Adjust main content padding for bottom nav on mobile */
        @media (max-width: 991.98px) {
            body {
                padding-bottom: 70px;
                /* Space for the bottom nav */
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        @include('layouts.partials.sidebar')
        <div class="main-content">
            @include('layouts.partials.header')
            <main class="container-fluid p-4 flex-grow-1">
                @yield('content')
            </main>
            @include('layouts.partials.footer')
        </div>
    </div>
    @include('layouts.partials.bottom-nav')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebarMenu');
            const sidebarToggle = document.getElementById('sidebarToggleTop');
            const closeSidebarBtn = document.getElementById('closeSidebarBtn');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', toggleSidebar);
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>

    @stack('scripts')


</body>

</html>