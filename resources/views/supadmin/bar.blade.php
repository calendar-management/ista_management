<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel App Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        #app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        #topbar {
            height: 60px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        #sidebar {
            width: 250px;
            position: fixed;
            top: 60px;
            left: 0;
            height: calc(100vh - 60px);
            z-index: 900;
            transition: all 0.3s;
            overflow-y: auto;
        }
        
        #content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            flex: 1;
            transition: all 0.3s;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .nav-link svg {
            margin-right: 10px;
        }
        
        #overlay {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 800;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            #sidebar {
                left: -250px;
            }
            
            #content {
                margin-left: 0;
            }
            
            #sidebar.active {
                left: 0;
            }
            
            #overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div id="app">
        <!-- Top Bar -->
        <nav id="topbar" class="bg-white shadow-md flex items-center justify-between px-4">
            <div class="flex items-center">
                <button id="sidebarToggle" class="block md:hidden text-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="#" class="text-xl font-bold text-gray-800 ml-4">Laravel App</a>
            </div>
            <div class="flex items-center">
                <div class="relative">
                    <button class="flex items-center text-gray-600 focus:outline-none">
                        <span class="mr-2">John Doe</span>
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="User">
                    </button>
                </div>
            </div>
        </nav>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-gray-800 text-white">
            <div class="p-4">
                <h5 class="text-xs uppercase font-semibold text-gray-400">Navigation</h5>
            </div>
            <ul class="mt-2">
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Users
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Projects
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Calendar
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        Notifications
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link hover:bg-gray-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Overlay for mobile sidebar -->
        <div id="overlay"></div>
        
        <!-- Main Content -->
        <main id="content" class="bg-gray-100">
            <div class="container mx-auto">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600 mt-2">Welcome to your Laravel application dashboard.</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-500 text-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold">Users</h3>
                            <p class="text-3xl font-bold">1,254</p>
                        </div>
                        <div class="bg-green-500 text-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold">Projects</h3>
                            <p class="text-3xl font-bold">42</p>
                        </div>
                        <div class="bg-yellow-500 text-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold">Tasks</h3>
                            <p class="text-3xl font-bold">128</p>
                        </div>
                        <div class="bg-purple-500 text-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold">Completed</h3>
                            <p class="text-3xl font-bold">78%</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
                        <div class="mt-4 bg-gray-50 rounded-lg p-4">
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" alt="User">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Jane Smith added a new task</p>
                                        <p class="text-sm text-gray-500">2 hours ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=Mike+Brown&background=random" alt="User">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Mike Brown completed Project X</p>
                                        <p class="text-sm text-gray-500">5 hours ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=random" alt="User">
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Sarah Johnson joined the team</p>
                                        <p class="text-sm text-gray-500">Yesterday</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        });
        
        // Close sidebar when clicking overlay
        document.getElementById('overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        });
    </script>
</body>
</html>