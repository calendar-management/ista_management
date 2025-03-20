<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formateur Dashboard</title>
    <!-- Include Tailwind CSS via CDN -->
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

        .progress-bar {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background-color: #e5e7eb;
        }

        .progress-container {
            position: relative;
            margin-top: 20px;
            margin-bottom: 18px;
        }

        .progress-label {
            font-size: 0.75rem;
            font-weight: 500;
            position: absolute;
            top: -18px;
            left: 0;
            color: #6b7280;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 999px;
            transition: width 0.5s ease;
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
</head>

<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="bg-sidebar text-white w-64 px-4 py-6 flex flex-col hidden md:block">
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6">Formateur</h2>
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
                        <a href="#"
                            class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link active flex items-center px-4 py-2 text-white">
                            <i class="fas fa-users mr-3"></i>
                            <span>Groups</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                            <i class="fas fa-book mr-3"></i>
                            <span>Modules</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                            <i class="fas fa-calendar-alt mr-3"></i>
                            <span>Calendar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                            <i class="fas fa-file-alt mr-3"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto">
                <a href="#" class="sidebar-link flex items-center px-4 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top navbar -->
            <header class="navbar shadow-sm sticky top-0 z-10">
                <div class="flex justify-between items-center py-4 px-6">
                    <div class="flex items-center">
                        <button class="md:hidden mr-4 text-gray-600 focus:outline-none">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">Formateur Dashboard</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-600 hover:text-gray-800 relative">
                            <i class="fas fa-bell"></i>
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white w-4 h-4 text-xs flex items-center justify-center rounded-full">2</span>
                        </button>
                        <button class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="md:hidden">
                            <button class="avatar w-8 h-8">
                                <span
                                    class="text-white font-bold">{{ substr(auth()->user()->name ?? 'User', 0, 1) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard content -->
            <main class="py-6 px-6">
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Your Groups</h2>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input type="text" placeholder="Search modules..."
                                    class="search-input w-full border border-gray-300 focus:outline-none focus:ring-0">
                                <button class="absolute right-3 top-3 text-gray-500">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <button class="btn-primary">
                                <i class="fas fa-plus mr-2"></i> Add Module
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Group cards -->
                <div class="space-y-6">
                    @if ($groups->isEmpty())
                        <div class="bg-white rounded-lg shadow p-6 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-700">No groups assigned yet</h3>
                            <p class="text-gray-500 mt-2">You currently don't have any groups assigned to you.</p>
                            <button class="btn-primary mt-4">
                                Request Group Assignment
                            </button>
                        </div>
                    @else
                        @foreach ($groups as $group)
                            <div class="group-card bg-white shadow-md">
                                <div class="card-header px-6 py-4">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-800">{{ $group['name'] }}</h3>
                                            <p class="text-gray-600">{{ $group['fillier'] }} - {{ $group['niveau'] }}
                                            </p>
                                        </div>
                                        <div class="mt-3 md:mt-0 flex items-center space-x-2">
                                            <span
                                                class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                                {{ count($group['modules']) }} Modules
                                            </span>
                                            <button class="text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="data-grid">
                                        @foreach ($group['modules'] as $module)
                                            <div class="module-card bg-white p-5">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800">{{ $module['name'] }}
                                                        </h4>
                                                        <p class="text-sm text-gray-500">Code: {{ $module['code'] }}</p>
                                                    </div>
                                                    <span
                                                        class="status-badge 
                                                        {{ $module['completed_hours'] >= $module['total_hours']
                                                            ? 'status-completed'
                                                            : ($module['completed_hours'] > 0
                                                                ? 'status-in-progress'
                                                                : 'status-not-started') }}">
                                                        {{ $module['completed_hours'] >= $module['total_hours']
                                                            ? 'Completed'
                                                            : ($module['completed_hours'] > 0
                                                                ? 'In Progress'
                                                                : 'Not Started') }}
                                                    </span>
                                                </div>

                                                <div class="mt-4 space-y-3 flex flex-col gap-5">
                                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                                        <div>
                                                            <p class="stat-label">Total Hours</p>
                                                            <p class="stat-value">{{ $module['total_hours'] }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="stat-label">Completed</p>
                                                            <p class="stat-value">{{ $module['completed_hours'] }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="stat-label">Start Date</p>
                                                            <p class="stat-value">
                                                                {{ $module['start_date'] ?? 'Not set' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="stat-label">Exam Date</p>
                                                            <p class="stat-value">
                                                                {{ $module['exam_date'] ?? 'Not set' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="progress-container">
                                                        <span class="progress-label">Progress Totale</span>
                                                        <progress class="w-ful"
                                                            value="{{ $module['completed_hours'] }}"
                                                            max="{{ $module['total_hours'] }}"></progress>
                                                        <p class="text-center text-sm">
                                                            {{ $module['completed_hours'] }}h/{{ $module['total_hours'] }}h
                                                            {{-- {{$module['completed_hours']}} --}}
                                                        </p>
                                                    </div>

                                                    {{-- <div class="progress-container">
                                                        <span class="progress-label">Presentiel
                                                            ({{ $module['presentiel_hours'] }} hours)
                                                        </span>
                                                        <progress class="w-full"
                                                            value="{{ $module['presentiel_completed'] }}"
                                                            max="{{ $module['presentiel_hours'] }}"></progress>
                                                        <p class="text-center text-sm">
                                                            {{ $module['presentiel_completed'] }}h/{{ $module['presentiel_hours'] }}h
                                                        </p>
                                                    </div>

                                                    <div class="progress-container">
                                                        <span class="progress-label">Distanciel
                                                            ({{ $module['distanciel_hours'] }} hours)</span>
                                                        <progress class="w-full"
                                                            value="{{ $module['distanciel_completed'] }}"
                                                            max="{{ $module['distanciel_hours'] }}"></progress>
                                                        <p class="text-center text-sm">
                                                            {{ $module['distanciel_completed'] }}h/{{ $module['distanciel_hours'] }}h
                                                        </p>
                                                    </div> --}}
                                                </div>

                                                <div class="mt-4 flex justify-end">
                                                    <button
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                                                        View Details
                                                        <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </main>
        </div>
    </div>

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
                if (window.innerWidth < 768 && !sidebar.contains(event.target) && !sidebarToggle.contains(
                        event.target)) {
                    sidebar.classList.add('hidden');
                }
            });

            // Animation for progress bars
            const progressBars = document.querySelectorAll('.progress-bar-fill');
            setTimeout(function() {
                progressBars.forEach(bar => {
                    const targetWidth = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = targetWidth;
                    }, 100);
                });
            }, 300);
        });
    </script>
</body>

</html>
