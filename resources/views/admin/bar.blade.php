<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Administrateur Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="../admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../admin/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Bootstrap CSS (Optional for grid responsiveness) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Include Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom styles for progress elements */
        progress {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
        }

        /* Customize the background (unfilled part) */
        progress::-webkit-progress-bar {
            background-color: #e5e7eb;
            border-radius: 999px;
        }

        /* Customize the progress bar (filled part) for each type */
        progress::-webkit-progress-value {
            border-radius: 999px;
            transition: width 0.5s ease;
        }

        /* Total progress */
        .progress-container:nth-child(1) progress::-webkit-progress-value {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        /* Presentiel progress */
        .progress-container:nth-child(2) progress::-webkit-progress-value {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        /* Distanciel progress */
        .progress-container:nth-child(3) progress::-webkit-progress-value {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        }

        /* Firefox styling */
        progress::-moz-progress-bar {
            border-radius: 999px;
        }

        .progress-container:nth-child(1) progress::-moz-progress-bar {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .progress-container:nth-child(2) progress::-moz-progress-bar {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        .progress-container:nth-child(3) progress::-moz-progress-bar {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .bg-sidebar {
            background-color: #0f172a;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        }

        .module-card {
            transition: all 0.25s ease;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .bg-gradient-blue {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        .bg-gradient-purple {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        }

        .bg-gradient-green {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
        }

        .card-header {
            border-bottom: 1px solid #f3f4f6;
        }

        .group-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .group-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .sidebar-link {
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 4px;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.9);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
        }

        .status-completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .status-in-progress {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-not-started {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .search-input {
            border-radius: 8px;
            padding: 10px 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
        }

        .btn-primary {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            color: white;
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }

        @media (max-width: 768px) {
            .data-grid {
                grid-template-columns: 1fr;
            }
        }

        .avatar {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
        }

        .stat-value {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
        }
    </style>


    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href='assets/css/fullcalendar.css' rel='stylesheet' />
    <link href='assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='assets/js/jquery-1.10.2.js' type="text/javascript"></script>
    <script src='assets/js/jquery-ui.custom.min.js' type="text/javascript"></script>
    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>


    @vite('resources/js/vacances.js')
    <style>
        body {
            text-align: center;
            display: flex;
            font-size: 14px;
            font-family: "Helvetica Nueue", Arial, Verdana, sans-serif;
            background-color: #DDDDDD;
        }

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
            color: white;
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
        @media (max-width: 333px) {
            .card-body .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-body .fs-4 {
                margin-bottom: 10px;
            }

            .card-body i {
                margin-bottom: 10px;
            }

            .card-body .text-gray-800 {
                margin-top: 5px;
            }
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">


        <x-navbar>
            <li>
                <a href="{{ route('adm_dashboard') }}"
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('gestion_calendrier') }}"
                    class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Calendrier</span>
                </a>
            </li>
        </x-navbar>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                                <img class="img-profile rounded-circle" src="../admin/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/profile">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button class="dropdown-item" href="#" data-toggle="modal"
                                        data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <main>
                        @yield('main')
                    </main>

                </div>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../admin/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../admin/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../admin/js/demo/chart-area-demo.js"></script>
    <script src="../admin/js/demo/chart-pie-demo.js"></script>
    <!-- jQuery (required for FullCalendar) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">


</body>

</html>
