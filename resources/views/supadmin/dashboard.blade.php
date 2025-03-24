<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gestion Super Admin</title>

    <!-- Custom fonts for this template-->
    <link href="../admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../admin/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Bootstrap CSS (Optional for grid responsiveness) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href='assets/css/fullcalendar.css' rel='stylesheet' />
    <link href='assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='assets/js/jquery-1.10.2.js' type="text/javascript"></script>
    <script src='assets/js/jquery-ui.custom.min.js' type="text/javascript"></script>
    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link href='assets/css/fullcalendar.css' rel='stylesheet' />
    <link href='assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='assets/js/jquery-1.10.2.js' type="text/javascript"></script>
    <script src='assets/js/jquery-ui.custom.min.js' type="text/javascript"></script>
    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
        }
    body {
        text-align: center;
        display: flex;
        font-size: 14px;
        font-family: "Helvetica Nueue", Arial, Verdana, sans-serif;
        background-color: #DDDDDD;
    }

    html, body {
    height: 100vh; /* Full viewport height */
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden; /* Prevents scrolling outside of wrapper */
}



#wrapper {
    display: flex;
    flex-grow: 1; /* Takes full height */
    width: 100%;
    height: 100vh; /* Ensure it takes full viewport height */
}

#content-wrapper {
    flex-grow: 1; /* Takes up remaining space */
    display: flex;
    flex-direction: column;
    height: 100vh; /* Ensures full height */
    overflow: hidden; /* Prevents unnecessary scrolling */
}

#content {
    flex-grow: 1; /* Makes content area expand */
    overflow-y: auto; /* Allows scrolling if needed */
    padding: 20px; /* Adjust spacing */
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
    .cal-scroll{
        width: 100%;
    overflow-x: auto;  /* Enables horizontal scrolling */
    white-space: nowrap;
    display: flex;
    flex-direction: column
    }

    .bg-sidebar {
            background-color: #0f172a;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
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
        
        .bg-gradient-blue {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        .bg-gradient-purple {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        }

        .bg-gradient-green {
            background: linear-gradient(90deg, #10b981, #059669);
        }
    </style>


    <style>
        @media (max-width: 333px) {
            .card-body .d-flex {
                flex-direction: column; /* Stack elements vertically */
                align-items: flex-start; /* Align items to the left */
            }

            .card-body .fs-4 {
                margin-bottom: 10px; /* Add space between title and icon */
            }

            .card-body i {
                margin-bottom: 10px; /* Add space between icon and details */
            }

            .card-body .text-gray-800 {
                margin-top: 5px; /* Add space between "Details" and the icon */
            }
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <div class="bg-sidebar text-white w-64 px-4 py-6 flex flex-col hidden md:block">
            <div class="mb-8" href="/adm_dashboard">
                <h2 class="text-2xl font-bold mb-6">Super Administrateur</h2>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="avatar w-10 h-10">
                        <span class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ auth()->user()->name ?? 'User Name' }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li>
                        <a href="" 
                           class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/gestion_adm"
                           class="sidebar-link active flex items-center px-4 py-2 text-white" aria-expanded="true"
                           aria-controls="collapseTwo" >
                            <i class="fas fa-calendar-alt mr-3"></i>
                            <span>Gestion D'Administrateurs</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto">
                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button type="submit" 
                            class="sidebar-link w-full flex items-center px-4 py-2 text-gray-300 hover:text-white">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <header class="navbar shadow-sm sticky top-0 z-10">
                <div class="flex justify-between items-center py-4 px-6">
                    <div class="flex items-center">
                        <button class="md:hidden mr-4 text-gray-600 focus:outline-none">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Administrateur</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        
                        
                        <div class="md:hidden">
                            <button class="avatar w-8 h-8 ml-5 " >
                                <span class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                            </button>
                        </div>
                        <div class="hidden md:flex items-center space-x-3">
                            <div class="avatar w-8 h-8">
                                <span class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Administrateur Card -->
                        <div class="container">
                            <div class="row justify-content-center" style="margin-right: -1.75rem;margin-left: -1.75rem;">
                                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                                    <div class="card border-left-primary shadow h-100 py-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <div class="fs-4 fw-bold text-success text-uppercase mb-3">
                                                        Administrateurs
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fas fa-clipboard-list fa-3x text-gray-300"></i>
                                                    <div class="mt-2 text-gray-800">
                                                        <a href="/gestion_adm" class="text-primary fs-5">Details</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>

                    </div>

                </div>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="admin/vendor/jquery/jquery.min.js"></script>
    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="admin/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="admin/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="admin/js/demo/chart-area-demo.js"></script>
    <script src="admin/js/demo/chart-pie-demo.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile sidebar toggle
            const sidebarToggle = document.querySelector('button.md\\:hidden');
            const sidebar = document.querySelector('.bg-sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.add('hidden');
                }
            });
        });
    </script>

</body>

</html>
