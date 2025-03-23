<!DOCTYPE html>
<html>

<head>
    <title>Professor's Calendar</title>
    {{-- @vite('resources/js/app.jsx') --}}
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->

    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href='assets/css/fullcalendar.css' rel='stylesheet' />
    <link href='assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='assets/js/jquery-1.10.2.js' type="text/javascript"></script>
    <script src='assets/js/jquery-ui.custom.min.js' type="text/javascript"></script>
    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script>
        const data = JSON.parse('{!! json_encode($modules, JSON_HEX_APOS) !!}');
        const holidays = JSON.parse('{!! json_encode($holidays) !!}');
    </script>

    @vite('resources/js/calendar.js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    

   
    #wrapper {
        width: 100%;
        margin: 0 auto;
    }

    #calendar {
        margin: 0 auto;
        width: 900px;
        background-color: #FFFFFF;
        border-radius: 6px;
        box-shadow: 0 1px 2px #C3C3C3;
    }

    .fc-event {
        cursor: pointer;
    }

    .cal-scroll {
        width: 100%;
        overflow-x: auto;
        /* Enables horizontal scrolling */
        white-space: nowrap;
        display: flex;
        flex-direction: column
    }
    
    
</style>

<style>
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
        color: #333;
        line-height: 1.5;
        overflow-x: hidden;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    ul {
        list-style: none;
    }

    /* Layout */
    #wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        background-color: #0f172a;
        width: 260px;
        position: fixed;
        height: 100vh;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        padding: 1rem;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar-header {
        padding: 0.5rem 1rem;
        margin-bottom: 1.5rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 0 0.5rem;
    }

    .avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 12px;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        color: white;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .user-email {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .sidebar-divider {
        height: 1px;
        background-color: rgba(255, 255, 255, 0.1);
        margin: 1rem 0;
    }

    .nav-item {
        margin-bottom: 0.5rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: rgba(255, 255, 255, 0.7);
        border-radius: 8px;
        transition: all 0.2s ease;
        background: none;
        border: none;
        width: 100% !important;
        text-align: left;
        cursor: pointer;
        gap: 10px;
    }
    .nav-label{
        font-size: 16px !important;
        font-weight: 400 !important;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(3px);
    }

    .nav-link i {
        width: 24px;
        text-align: center;
    }

    .nav-item.active .nav-link {
        background-color: #3b82f6;
        color: white;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .sidebar.collapsed .nav-link span,
    .sidebar.collapsed .user-details {
        display: none;
    }

    .sidebar.collapsed .avatar {
        margin-right: 0;
    }

    .sidebar.collapsed .user-info {
        justify-content: center;
    }

    /* TopBar */
    .topbar {
        height: 70px;
        background-color: white;
        position: fixed;
        width: calc(100% - 260px);
        right: 0;
        top: 0;
        z-index: 900;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: width 0.3s ease;
    }

    .topbar.sidebar-collapsed {
        width: calc(100% - 80px);
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        color: #475569;
        font-size: 1.25rem;
        cursor: pointer;
    }

    

    .dropdown-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0.5rem;
        gap:15px;
    }
    

    /* Content */
    .content-wrapper {
        flex: 1;
        margin-left: 260px;
        padding-top: 70px;
        transition: margin-left 0.3s ease;
    }

    .content-wrapper.sidebar-collapsed {
        margin-left: 80px;
    }

    main {
        padding: 2rem;
    }

    /* Dashboard Components */
    .header {
        display: flex;
        flex-direction: column;
        margin-bottom: 2rem;
    }

    @media (min-width: 768px) {
        .header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .page-title {
            margin-bottom: 0;
        }
    }

    .actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .export-btn {
        background: linear-gradient(90deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        transition: all 0.3s ease;
    }

    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(16, 185, 129, 0.3);
    }

    /* Group Cards */
    .groups-container {
        margin-bottom: 2rem;
    }

    .group-card {
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .group-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .group-header {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    @media (min-width: 768px) {
        .group-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .group-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
    }

    .group-subtitle {
        color: #64748b;
        font-size: 0.875rem;
    }

    .group-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge {
        background-color: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .options-btn {
        color: #64748b;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }

    .options-btn:hover {
        background-color: #f1f5f9;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Module Grid */
    .modules-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .modules-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .modules-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1280px) {
        .modules-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .module-card {
        background-color: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.25rem;
        transition: all 0.25s ease;
    }

    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .module-title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .module-code {
        color: #64748b;
        font-size: 0.875rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-completed {
        background-color: rgba(16, 185, 129, 0.1);
        color: #047857;
    }

    .status-in-progress {
        background-color: rgba(245, 158, 11, 0.1);
        color: #b45309;
    }

    .status-not-started {
        background-color: rgba(239, 68, 68, 0.1);
        color: #b91c1c;
    }

    .module-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 1rem;
        margin-bottom: 1.25rem;
    }

    .stat-item {
        font-size: 0.875rem;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        color: #1e293b;
        font-weight: 500;
    }

    /* Progress Bars */
    .progress-container {
        position: relative;
        margin-top: 1.25rem;
    }

    .progress-label {
        font-size: 0.75rem;
        color: #64748b;
        position: absolute;
        top: -1.125rem;
        left: 0;
    }

    .progress-percentage {
        font-size: 0.75rem;
        color: #64748b;
        position: absolute;
        top: -1.125rem;
        right: 0;
        font-weight: 600;
    }

    progress {
        width: 100%;
        height: 10px;
        border-radius: 9999px;
        overflow: hidden;
        border: none;
        background-color: #e2e8f0;
    }

    progress::-webkit-progress-bar {
        background-color: #e2e8f0;
        border-radius: 9999px;
    }

    progress::-webkit-progress-value {
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 9999px;
        transition: width 0.5s ease;
    }

    progress::-moz-progress-bar {
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 9999px;
    }

    .progress-text {
        text-align: center;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.5rem;
    }

    /* Empty State */
    .empty-state {
        background-color: white;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .empty-icon {
        color: #cbd5e1;
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 500;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #64748b;
        margin-bottom: 1.5rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 1024px) {
        .modules-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 240px;
        }

        .sidebar.mobile-open {
            transform: translateX(0);
        }

        .topbar {
            width: 100%;
            padding: 0 1rem;
        }

        .content-wrapper {
            margin-left: 0;
        }

        .menu-toggle {
            display: block;
        }

        .actions {
            flex-wrap: wrap;
        }

        .modules-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        main {
            padding: 1.5rem 1rem;
        }
    }

    /* For keyboard users */
    :focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
</style>
</head>

<body>

    <div id="wrapper">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2 class="user-role" style="color: white; font-size: 1.5rem; font-weight: bold;">{{ ucfirst(auth()->user()->role) }}</h2>
            </div>
            
            <div class="user-info">
                <div class="avatar">
                    <span>{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <div class="user-details">
                    <span class="user-name">{{ auth()->user()->name ?? 'User Name' }}</span>
                    <span class="user-email">{{ auth()->user()->email ?? 'email@example.com' }}</span>
                </div>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/profile" class="nav-link">
                        <i class="fas fa-user-circle"></i>
                        <span class="nav-label">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/formateur_dashboard" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="/formateur_calendar" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-label">Calendrier</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="nav-label">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        <header class="topbar" id="topbar">
            <button id="menuToggle" class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div></div>
            <div class="user-dropdown">
                <div id="userDropdownToggle" class="dropdown-toggle">
                    <span class="user-name-display">{{ auth()->user()->name }}</span>
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </header>
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                

                <div class="container mt-12 pt-10">
                    <div class="call">
                        <div class="cal-scroll col-md-12">
                            <div id='calendar'></div>
                            <div style='clear:both'></div>

                        </div>
                        <div id="call"></div>
                        <hr>
                        <div class="col-md-12">
                            <div id="weeklyUpdateContainer"></div>
                        </div>
                        <br>
                        <form id="saveChangesForm" style="display: none;">
                            @csrf
                            <input type="hidden" name="moduleData" id="moduleDataInput">
                        </form>
                    </div>
                </div>
                <footer class="sticky-footer bg-white col-md-12">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Calendar 2025</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="admin/js/sb-admin-2.min.js"></script>
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const topbar = document.getElementById('topbar');
        const contentWrapper = document.getElementById('contentWrapper');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) &&
                sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // User dropdown toggle
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        userDropdownToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking elsewhere
        document.addEventListener('click', () => {
            if (userDropdownMenu.classList.contains('show')) {
                userDropdownMenu.classList.remove('show');
            }
        });
        
        // Prevent closing when clicking inside dropdown
        userDropdownMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // Export functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            // This would typically call a backend route to generate the Excel file
            alert('Exporting progress data as Excel file...');
            // Example: window.location.href = '/export-progress';
        });
    </script>
</body>



</html>
