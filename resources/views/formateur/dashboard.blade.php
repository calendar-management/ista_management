<x-bar :navlinks="[
    ['label' => 'Calendrier', 'route' => 'formateur_calendar', 'class' => '', 'icon' => 'fas fa-calendar-alt'],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => 'active', 'icon' => 'fas fa-chart-bar'],
]">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(3px);
        }

        .nav-link i {
            width: 24px;
            margin-right: 12px;
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
            gap: 15px;
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

        /* .group-card {
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
        } */

        /* Enhanced Group Card Styles */
        .group-card {
            background-color: white;
            border-radius: 16px;
            /* Slightly larger border radius */
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            /* Softer, more pronounced shadow */
            margin-bottom: 2rem;
            /* More spacing between cards */
            border: 1px solid #f1f5f9;
            /* Subtle border for definition */
            transition: all 0.3s ease;
        }

        .group-card:hover {
            transform: translateY(-6px);
            /* Increased hover lift */
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
            /* More pronounced shadow on hover */
        }

        .card-header {
            background-color: #f8fafc;
            /* Subtle background for header */
            padding: 1.5rem;
            /* Increased padding */
            border-bottom: 1px solid #e2e8f0;
            /* Clearer separation */
        }

        .group-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            /* More space between elements */
        }

        @media (min-width: 768px) {
            .group-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .group-title {
            font-size: 1.35rem;
            /* Slightly larger title */
            font-weight: 700;
            /* Bolder font weight */
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .group-subtitle {
            color: #475569;
            /* Slightly darker subtitle color */
            font-size: 0.9rem;
            font-weight: 500;
        }

        .badge {
            background-color: #e0f2fe;
            /* Lighter blue background */
            color: #1d4ed8;
            /* Darker blue text */
            padding: 0.3rem 0.85rem;
            /* Slightly larger badge */
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .badge:hover {
            background-color: #dbeafe;
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
            /* Increased padding */
            background-color: #ffffff;
            /* Pure white background */
        }

        /* Module Card Enhancements */
        .module-card {
            background-color: #f9fafb;
            /* Very light gray background */
            border: 1px solid #e5e7eb;
            /* Subtle border */
            border-radius: 12px;
            /* Slightly rounded corners */
            padding: 1.5rem;
            /* More padding */
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .module-card:hover::before {
            opacity: 1;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .status-badge {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .modules-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .modules-grid {
                grid-template-columns: 1fr;
                /* Single column by default */
            }
        }

        @media (min-width: 1024px) {
            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
                /* Two columns on large screens */
                gap: 1.5rem;
                /* Increased gap between columns */
            }
        }

        .module-card {
            width: 100%;
            /* Ensure full width within grid cell */
            max-width: 100%;
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


    <main>
        <div class="header">
            <h2 class="page-title">Vos Groupes</h2>
        </div>

        <div class="groups-container">
            @if ($groups->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="empty-title">Aucun groupe assigné pour le moment</h3>
                    <p class="empty-text">Vous n'avez actuellement aucun groupe assigné.</p>
                </div>
            @else
                @foreach ($groups as $group)
                    <div class="group-card">
                        <div class="card-header">
                            <div class="group-header">
                                <div>
                                    <h3 class="group-title">{{ $group['name'] }}</h3>
                                    <p class="group-subtitle">{{ $group['fillier'] }} - {{ $group['niveau'] }}</p>
                                </div>
                                <div class="group-meta">
                                    <span class="badge">{{ count($group['modules']) }} Modules</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="modules-grid">
                                @foreach ($group['modules'] as $module)
                                    <div class="module-card">
                                        <div class="module-header">
                                            <div>
                                                <h4 class="module-title">{{ $module['name'] }}</h4>
                                                <p class="module-code">Code : {{ $module['code'] }}</p>
                                            </div>
                                            <span
                                                class="status-badge 
                                            {{ $module['completed_hours'] >= $module['total_hours']
                                                ? 'status-completed'
                                                : ($module['completed_hours'] > 0
                                                    ? 'status-in-progress'
                                                    : 'status-not-started') }}">
                                                {{ $module['completed_hours'] >= $module['total_hours']
                                                    ? 'Terminé'
                                                    : ($module['completed_hours'] > 0
                                                        ? 'En cours'
                                                        : 'Non commencé') }}
                                            </span>
                                        </div>

                                        <div class="module-stats">
                                            <div class="stat-item">
                                                <p class="stat-label">Heures totales</p>
                                                <p class="stat-value">{{ $module['total_hours'] }}</p>
                                            </div>
                                            <div class="stat-item">
                                                <p class="stat-label">Complété</p>
                                                <p class="stat-value">{{ $module['completed_hours'] }}</p>
                                            </div>
                                            <div class="stat-item">
                                                <p class="stat-label">Date d'examen</p>
                                                <p class="stat-value">{{ $module['exam_date'] ?? 'Non définie' }}</p>
                                            </div>
                                            <div class="stat-item">
                                                <p class="stat-label">Progression</p>
                                                <p class="stat-value">
                                                    {{ number_format(($module['completed_hours'] / $module['total_hours']) * 100, 1) }}%
                                                </p>
                                            </div>
                                        </div>

                                        <div class="progress-container">
                                            <span class="progress-label">Progression totale</span>
                                            <span
                                                class="progress-percentage">{{ number_format(($module['completed_hours'] / $module['total_hours']) * 100, 1) }}%</span>
                                            <progress value="{{ $module['completed_hours'] }}"
                                                max="{{ $module['total_hours'] }}"></progress>
                                            <p class="progress-text">
                                                {{ $module['completed_hours'] }}h/{{ $module['total_hours'] }}h</p>
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


</x-bar>
