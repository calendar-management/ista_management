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
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Include Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
        }

        .cal-scroll {
            width: 100%;
            overflow-x: auto;
            /* Enables horizontal scrolling */
            white-space: nowrap;
            display: flex;
            flex-direction: column
        }
        #accordionSidebar {
            background: black;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            width: fit-content !important;
            padding: 10px
        }

        /* Adjust content wrapper for fixed sidebar */
        #content-wrapper {
            margin-left: 14rem;
            transition: margin 0.3s ease;
        }

        /* Adjust content wrapper when sidebar is toggled/collapsed */
        .sidebar.toggled+#content-wrapper {
            margin-left: 220px;
        }

        .sidebar-brand {
            height: 80px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .sidebar-brand-icon {
            background-color: rgba(255, 255, 255, 0.1);
            width: 20px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand-text {
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }

        .sidebar .nav-item {
            margin: 4px 10px;
        }

        .sidebar .nav-item .nav-link {
            border-radius: 8px;
            padding: 12px 16px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(3px);
        }

        .sidebar .nav-item.active .nav-link {
            background-color: #3b82f6;
            color: #fff;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .sidebar .nav-item .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            font-size: 1rem;
            opacity: 0.8;
        }

        .sidebar .nav-item.active .nav-link i {
            opacity: 1;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin: 15px 10px;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0 15px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        #sidebarToggle {
            background-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.7);
            height: 36px;
            width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        #sidebarToggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        #sidebarToggle i {
            transition: transform 0.3s ease;
        }

        .sidebar.toggled #sidebarToggle i {
            transform: rotate(180deg);
        }

        /* Collapsed sidebar styles */
        .sidebar.toggled {
            width: 6.5rem !important;
        }

        .sidebar.toggled .nav-item {
            margin: 4px;
        }

        .sidebar.toggled .nav-item .nav-link {
            /* padding: 12px 8px; */
            display: flex;
            align-items: center;
            width: 100%;
        }

        .sidebar.toggled .nav-item .nav-link span {
            font-size: 15px;
        }

        .sidebar.toggled .sidebar-brand-text {
            display: none;
        }

        .sidebar.toggled .sidebar-brand {
            justify-content: center;
        }

        /* Small screens adjustments */
        @media (max-width: 768px) {
            #accordionSidebar {
                position: fixed;
                transform: translateX(-100%);
            }

            #accordionSidebar.toggled {
                transform: translateX(0);
            }

            #content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <div id="wrapper">

        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
            <div class="">
                <div class="flex items-start pl-2">
                    <h2 class="text-2xl font-bold mt-2 mb-4 text-white">{{ ucfirst(auth()->user()->role) }}</h2>
                </div>
                <div class="flex items-center space-x-3 mb-6 pl-2">
                    <div class="avatar w-10 h-10 bg-blue-500 flex items-center justify-center rounded-full">
                        <span class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                    </div>
                    <div class="flex flex-col items-start gap-1">
                        <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'User Name' }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                    </div>
                </div>
            </div>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="/profile">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="formateur_dashboard">
                    <i class="fas fa-chart-bar"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="/formateur_calendar">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendrier</span>
                </a>
            </li>

            <li class="nav-item">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="nav-link" href="{{ route('calendar') }}"
                        class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white w-full">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>


        </ul>

        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow fixed w-screen" style="z-index: 500">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <button class="nav-link dropdown-toggle" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span
                                        class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                                    <img class="img-profile rounded-circle" src="admin/img/person.svg">
                            </button>
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

</body>



</html>
