<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>

            <div class="container">
                <div class="calendar-container my-4">
                    <div class="calendar-header d-flex justify-content-between align-items-center mb-4">
                        <h2 id="currentMonthYear">{{ Carbon\Carbon::now()->format('F Y') }}</h2>

                        <div class="btn-group mb-2">
                            <button type="button" class="btn btn-outline-primary" id="monthView">Month</button>
                            <button type="button" class="btn btn-outline-primary" id="weekView">Week</button>
                        </div>

                        <div>
                            <a href="#" class="btn btn-sm btn-primary" id="prevMonth">&lt; Previous</a>
                            <a href="#" class="btn btn-sm btn-primary" id="nextMonth">Next &gt;</a>
                        </div>
                    </div>
                    <table class="calendar-table table table-bordered">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $date = Carbon\Carbon::now()->startOfMonth();
                                $daysInMonth = Carbon\Carbon::now()->daysInMonth;
                                $firstDayOfWeek = $date->dayOfWeek;
                            @endphp

                            @for ($i = 0; $i < ceil(($daysInMonth + $firstDayOfWeek) / 7); $i++)
                                <tr>
                                    @for ($j = 0; $j < 7; $j++)
                                        @php
                                            $currentDay = $i * 7 + $j - $firstDayOfWeek + 1;
                                        @endphp
                                        <td class="calendar-cell {{ $currentDay == Carbon\Carbon::now()->day ? 'today' : '' }}"
                                            @if ($currentDay > 0 && $currentDay <= $daysInMonth) data-date="{{ Carbon\Carbon::now()->format('Y-m-') . $currentDay }}" @endif>
                                            @if ($currentDay > 0 && $currentDay <= $daysInMonth)
                                                <div class="date-number">{{ $currentDay }}</div>
                                                <div class="task-container"></div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add this after your calendar table -->
            <div class="task-details-section mt-4 d-none">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Task Details</h5>
                        <button type="button" class="btn-close" aria-label="Close"
                            onclick="hideTaskDetails()"></button>
                    </div>
                    <div class="card-body">
                        <div class="task-info">
                            <h6 class="task-title mb-3"></h6>
                            <p class="task-date mb-2"></p>
                            <p class="task-hours mb-3"></p>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-edit">Edit</button>
                            <button class="btn btn-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="taskForm" method="POST" action="{{ route('tasks.store') }}">
                                @csrf
                                <input type="hidden" id="taskDate" name="date">
                                <div class="mb-3">
                                    <label for="taskTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="taskTitle" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="taskHours" class="form-label">Nombre des heures</label>
                                    <input type="text" class="form-control" id="taskHours" name="hours" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Task</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editTaskForm" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="editTaskId" name="id">
                                <input type="hidden" id="editTaskDate" name="date">

                                <div class="mb-3">
                                    <label for="editTaskTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="editTaskTitle" name="title"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="editTaskHours" class="form-label">Nombre des heures</label>
                                    <input type="text" class="form-control" id="editTaskHours" name="hours"
                                        required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Task</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <style>
                .calendar-table {
                    table-layout: fixed;
                    width: 100%;
                    border-collapse: collapse;
                }

                .calendar-cell {
                    height: 120px;
                    vertical-align: top;
                    padding: 8px;
                    cursor: pointer;
                    border: 1px solid #dee2e6;
                    transition: background-color 0.2s ease;
                    overflow: visible;
                }

                .calendar-cell:hover:not(.past-date) {
                    background-color: #e9ecef;
                }

                .today {
                    background-color: #cfe2ff !important;
                    border: 2px solid #0d6efd !important;
                    position: relative;
                }

                .today::after {
                    content: 'Today';
                    position: absolute;
                    top: 2px;
                    right: 2px;
                    font-size: 0.7rem;
                    color: #0d6efd;
                    background: rgba(255, 255, 255, 0.8);
                    padding: 2px 4px;
                    border-radius: 3px;
                }

                .date-number {
                    font-weight: bold;
                    margin-bottom: 8px;
                    color: #495057;
                }

                .task-container {
                    min-height: 60px;
                    max-height: 100px;
                    overflow-y: auto;
                    padding-right: 4px;
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                }

                .task-container::-webkit-scrollbar {
                    width: 4px;
                }

                .task-container::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }

                .task-container::-webkit-scrollbar-thumb {
                    background: #888;
                    border-radius: 4px;
                }

                .task-item {
                    background-color: #e3f2fd;
                    border-left: 3px solid #0d6efd;
                    border-radius: 4px;
                    padding: 6px 8px;
                    font-size: 0.85rem;
                    color: #0d6efd;
                    transition: all 0.2s ease;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                    margin-bottom: 4px;
                    flex-shrink: 0;
                    cursor: pointer;
                    width: 100%;
                    box-sizing: border-box;
                }

                .past-date {
                    background-color: #f8f9fa;
                    color: #adb5bd;
                    cursor: not-allowed !important;
                    position: relative;
                }

                .past-date::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: repeating-linear-gradient(45deg,
                            rgba(0, 0, 0, 0.03),
                            rgba(0, 0, 0, 0.03) 10px,
                            rgba(0, 0, 0, 0.06) 10px,
                            rgba(0, 0, 0, 0.06) 20px);
                }

                .btn-group .btn.active {
                    background-color: #0d6efd;
                    color: white;
                }

                .task-item:hover {
                    background-color: #cfe2ff;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                }

                .calendar-cell {
                    height: 120px;
                    vertical-align: top;
                    padding: 10px;
                    cursor: pointer;
                    border: 1px solid #dee2e6;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                .calendar-cell:hover:not(.past-date) {
                    background-color: #f8f9fa;
                    box-shadow: inset 0 0 0 2px #0d6efd;
                }

                .date-number {
                    font-weight: 600;
                    margin-bottom: 10px;
                    color: #495057;
                    font-size: 1.1rem;
                }

                .calendar-header {
                    background-color: #f8f9fa;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                }

                #currentMonthYear {
                    font-size: 1.5rem;
                    font-weight: 600;
                    color: #212529;
                    margin: 0;
                }

                .btn-group .btn {
                    border-radius: 6px;
                    margin: 0 2px;
                }

                .calendar-table thead th {
                    background-color: #f1f3f5;
                    color: #495057;
                    font-weight: 600;
                    padding: 12px;
                    text-align: center;
                    border-bottom: 2px solid #dee2e6;
                }

                .task-item.dragging {
                    opacity: 0.5;
                    cursor: move;
                }

                .calendar-cell.drag-over {
                    background-color: #e3f2fd;
                    border: 2px dashed #0d6efd;
                }

                .task-details-section {
                    scroll-margin-top: 2rem;
                }

                .task-item:active {
                    transform: scale(0.98);
                }

                .card {
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .card-header {
                    background-color: #f8f9fa;
                    border-bottom: 1px solid #dee2e6;
                }

                .btn-group {
                    gap: 8px;
                }

                .btn-edit:hover {
                    background-color: #0b5ed7;
                }

                .btn-delete:hover {
                    background-color: #bb2d3b;
                }
            </style>





        </main>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const fetchedDates = JSON.parse(`{!! json_encode($data) !!}`);
            let currentDate = new Date();
            let currentView = 'month';

            // Initialize view toggle handlers
            document.getElementById('monthView').addEventListener('click', function() {
                currentView = 'month';
                this.classList.add('active');
                document.getElementById('weekView').classList.remove('active');
                updateCalendar(currentDate);
            });

            document.getElementById('weekView').addEventListener('click', function() {
                currentView = 'week';
                this.classList.add('active');
                document.getElementById('monthView').classList.remove('active');
                updateCalendar(currentDate);
            });

            // Navigation handlers
            document.getElementById('prevMonth').addEventListener('click', function(e) {
                e.preventDefault();
                if (currentView === 'month') {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                } else {
                    currentDate.setDate(currentDate.getDate() - 7);
                }
                updateCalendar(currentDate);
            });

            document.getElementById('nextMonth').addEventListener('click', function(e) {
                e.preventDefault();
                if (currentView === 'month') {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                } else {
                    currentDate.setDate(currentDate.getDate() + 7);
                }
                updateCalendar(currentDate);
            });

            function updateCalendar(date) {
                if (currentView === 'month') {
                    updateMonthView(date);
                } else {
                    updateWeekView(date);
                }
            }

            function updateMonthView(date) {
                // Update header
                const monthYearStr = date.toLocaleString('default', {
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('currentMonthYear').textContent = monthYearStr;

                const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
                const daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

                let calendarBody = document.querySelector('.calendar-table tbody');
                calendarBody.innerHTML = '';

                let day = 1;
                let currentRow = document.createElement('tr');

                // Add empty cells for days before the first of the month
                for (let i = 0; i < firstDay; i++) {
                    currentRow.appendChild(document.createElement('td'));
                }

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Fill in the days of the month
                while (day <= daysInMonth) {
                    if (currentRow.children.length === 7) {
                        calendarBody.appendChild(currentRow);
                        currentRow = document.createElement('tr');
                    }

                    let cell = document.createElement('td');
                    cell.className = 'calendar-cell';

                    const cellDate = new Date(date.getFullYear(), date.getMonth(), day);
                    const isPastDate = cellDate < today;

                    if (isPastDate) {
                        cell.classList.add('past-date');
                    }

                    if (day === today.getDate() &&
                        date.getMonth() === today.getMonth() &&
                        date.getFullYear() === today.getFullYear()) {
                        cell.classList.add('today');
                    }

                    const formattedDate =
                        `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    cell.dataset.date = formattedDate;


                    let myDate =
                        `${cellDate.getFullYear()}-${String(cellDate.getMonth() + 1).padStart(2, '0')}-${String(cellDate.getDate()).padStart(2, '0')}`;

                    function renderTasks(date, container) {
                        const tasksForDate = fetchedDates.filter(task => task.date === date);
                        container.innerHTML = '';

                        tasksForDate.forEach(task => {
                            const taskElement = document.createElement('div');
                            taskElement.className = 'task-item';
                            taskElement.draggable = true;
                            taskElement.setAttribute('data-task-id', task.id);
                            taskElement.setAttribute('data-date', date);
                            taskElement.innerHTML = `${task.title} - ${task.hours}h`;

                            // Add event listeners
                            taskElement.addEventListener('dragstart', handleDragStart);
                            taskElement.addEventListener('dragend', handleDragEnd);
                            taskElement.addEventListener('click', (e) => {
                                e.stopPropagation();
                                showTaskDetails(task);
                            });

                            container.appendChild(taskElement);
                        });
                    }
                    let taskContainer = document.createElement('div');
                    taskContainer.className = 'task-container';
                    cell.appendChild(taskContainer);
                    renderTasks(myDate, taskContainer);

                    function handleCellClick(cell, date) {
                        if (!cell.classList.contains('past-date')) {
                            document.getElementById('taskDate').value = date;
                            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                            modal.show();
                        }
                    }
                    if (!isPastDate) {
                        cell.addEventListener('click', function(e) {
                            // Only trigger if clicking the cell itself, not a task
                            if (e.target === cell || e.target.classList.contains('date-number')) {
                                handleCellClick(this, formattedDate);
                            }
                        });
                    }

                    currentRow.appendChild(cell);
                    day++;
                }

                // Fill remaining cells
                while (currentRow.children.length < 7) {
                    currentRow.appendChild(document.createElement('td'));
                }
                calendarBody.appendChild(currentRow);
                initializeDragAndDrop();

            }

            function updateWeekView(date) {
                const startOfWeek = new Date(date);
                startOfWeek.setDate(date.getDate() - date.getDay());

                // Update header for week view
                const endOfWeek = new Date(startOfWeek);
                endOfWeek.setDate(startOfWeek.getDate() + 6);
                const weekStr =
                    `${startOfWeek.toLocaleString('default', { month: 'long', day: 'numeric' })} - ${endOfWeek.toLocaleString('default', { month: 'long', day: 'numeric', year: 'numeric' })}`;
                document.getElementById('currentMonthYear').textContent = weekStr;

                let calendarBody = document.querySelector('.calendar-table tbody');
                calendarBody.innerHTML = '';
                let row = document.createElement('tr');

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Create week view
                for (let i = 0; i < 7; i++) {
                    const currentDate = new Date(startOfWeek);
                    currentDate.setDate(startOfWeek.getDate() + i);

                    const isPastDate = currentDate < today;

                    let cell = document.createElement('td');
                    cell.className = 'calendar-cell';

                    if (isPastDate) {
                        cell.classList.add('past-date');
                    }

                    if (currentDate.toDateString() === today.toDateString()) {
                        cell.classList.add('today');
                    }

                    const formattedDate =
                        `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
                    cell.dataset.date = formattedDate;

                    function renderTasks(date, container) {
                        const tasksForDate = fetchedDates.filter(task => task.date === date);
                        container.innerHTML = '';

                        tasksForDate.forEach(task => {
                            const taskElement = document.createElement('div');
                            taskElement.className = 'task-item';
                            taskElement.draggable = true;
                            taskElement.setAttribute('data-date', date);
                            taskElement.innerHTML = `${task.title} - ${task.hours}h`;

                            // Add click handler
                            taskElement.addEventListener('click', (e) => {
                                e.stopPropagation(); // Prevent cell click handler
                                showTaskDetails(task);
                            });

                            container.appendChild(taskElement);
                        });
                    }
                    let taskContainer = document.createElement('div');
                    taskContainer.className = 'task-container';
                    cell.appendChild(taskContainer);
                    renderTasks(formattedDate, taskContainer);

                    if (!isPastDate) {
                        cell.addEventListener('click', function() {
                            document.getElementById('taskDate').value = this.dataset.date;
                            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                            modal.show();
                        });
                    }

                    row.appendChild(cell);
                }

                calendarBody.appendChild(row);
                initializeDragAndDrop();
            }

            // Initialize calendar
            document.getElementById('monthView').classList.add('active');
            updateCalendar(currentDate);

            function initializeDragAndDrop() {
                // Add drag events to tasks
                document.querySelectorAll('.task-item').forEach(task => {
                    task.addEventListener('dragstart', handleDragStart);
                    task.addEventListener('dragend', handleDragEnd);
                });

                // Add drag events to calendar cells
                document.querySelectorAll('.calendar-cell').forEach(cell => {
                    if (!cell.classList.contains('past-date')) {
                        cell.addEventListener('dragover', handleDragOver);
                        cell.addEventListener('dragenter', handleDragEnter);
                        cell.addEventListener('dragleave', handleDragLeave);
                        cell.addEventListener('drop', handleDrop);
                    }
                });
            }

            function handleDragStart(e) {
                e.target.classList.add('dragging');
                const taskData = {
                    id: e.target.getAttribute('data-task-id'),
                    title: e.target.textContent.split(' - ')[0],
                    hours: e.target.textContent.split(' - ')[1].replace('h', ''),
                    date: e.target.getAttribute('data-date')
                };
                e.dataTransfer.setData('text/plain', JSON.stringify(taskData));
            }

            function handleDragEnd(e) {
                e.target.classList.remove('dragging');
                document.querySelectorAll('.calendar-cell').forEach(cell => {
                    cell.classList.remove('drag-over');
                });
            }

            function handleDragOver(e) {
                if (!e.currentTarget.classList.contains('past-date')) {
                    e.preventDefault();
                }
            }

            function handleDragEnter(e) {
                if (!e.currentTarget.classList.contains('past-date')) {
                    e.currentTarget.classList.add('drag-over');
                }
            }

            function handleDragLeave(e) {
                e.currentTarget.classList.remove('drag-over');
            }

            function handleDrop(e) {
                e.preventDefault();
                e.currentTarget.classList.remove('drag-over');

                try {
                    const taskData = JSON.parse(e.dataTransfer.getData('text/plain'));
                    const sourceTask = document.querySelector('.task-item.dragging');
                    const targetContainer = e.currentTarget.querySelector('.task-container');

                    if (sourceTask && targetContainer) {
                        // Move the task instead of creating a new one
                        sourceTask.setAttribute('data-date', e.currentTarget.dataset.date);
                        targetContainer.appendChild(sourceTask);
                        sourceTask.classList.remove('dragging');
                    }
                } catch (error) {
                    console.error('Error handling drop:', error);
                }
            }

            function showTaskDetails(task) {
                const detailsSection = document.querySelector('.task-details-section');
                const taskTitle = detailsSection.querySelector('.task-title');
                const taskDate = detailsSection.querySelector('.task-date');
                const taskHours = detailsSection.querySelector('.task-hours');

                taskTitle.textContent = task.title;
                taskDate.textContent = `Date: ${formatDate(task.date)}`;
                taskHours.textContent = `Hours: ${task.hours}`;

                detailsSection.classList.remove('d-none');
                detailsSection.scrollIntoView({
                    behavior: 'smooth'
                });

                // Add event listeners for edit and delete buttons
                const editBtn = detailsSection.querySelector('.btn-edit');
                const deleteBtn = detailsSection.querySelector('.btn-delete');

                editBtn.onclick = () => editTask(task);
                deleteBtn.onclick = () => deleteTask(task);
            }

            function hideTaskDetails() {
                const detailsSection = document.querySelector('.task-details-section');
                detailsSection.classList.add('d-none');
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            // Replace the existing editTask function
            function editTask(task) {
                // Populate edit form fields
                document.getElementById('editTaskId').value = task.id;
                document.getElementById('editTaskTitle').value = task.title;
                document.getElementById('editTaskHours').value = task.hours;
                document.getElementById('editTaskDate').value = task.date;

                // Set the form action
                const form = document.getElementById('editTaskForm');
                form.action = "{{ route('tasks.update', '') }}/" + task.id;

                // Show edit modal
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                modal.show();
            }
        </script>
</body>

</html>
