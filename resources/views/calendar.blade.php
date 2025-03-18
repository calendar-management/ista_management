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


        <main>
            <div class="container py-4">
                <div class="row mb-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <button id="monthView" class="btn btn-outline-primary">Month</button>
                                    <button id="weekView" class="btn btn-outline-primary">Week</button>
                                </div>
                                <h3 id="currentMonthYear" class="mb-0"></h3>
                                <div class="btn-group" role="group">
                                    <button id="prevMonth" class="btn btn-outline-secondary">&lt;</button>
                                    <button id="nextMonth" class="btn btn-outline-secondary">&gt;</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="calendar-table table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sunday</th>
                                            <th>Monday</th>
                                            <th>Tuesday</th>
                                            <th>Wednesday</th>
                                            <th>Thursday</th>
                                            <th>Friday</th>
                                            <th>Saturday</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Calendar content will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Task Details Section -->
                <div class="task-details-section card d-none">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Task Details</h4>
                        <button type="button" class="btn-close" onclick="hideTaskDetails()"></button>
                    </div>
                    <div class="card-body">
                        <h5 class="task-title"></h5>
                        <p class="task-date"></p>
                        <p class="task-hours"></p>
                        <div class="btn-group">
                            <button class="btn btn-primary btn-edit">Edit</button>
                            <button class="btn btn-danger btn-delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Task Modal -->
            <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="taskModalLabel">Add Progress</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('calendar.store') }}" method="POST">
                                @csrf
                                <input type="hidden" id="taskDate" name="module_start_date">
                                
                                <div class="mb-3">
                                    <label for="teaching" class="form-label">Module & Group</label>
                                    <select class="form-control" id="teaching" name="id_teaching" required>
                                        @foreach($teachings as $teaching)
                                            <option value="{{ $teaching->id_teaching }}">
                                                {{ $teaching->module->name }} - {{ $teaching->group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div class="mb-3">
                                    <label for="hours_completed" class="form-label">Hours Completed</label>
                                    <input type="number" class="form-control" id="hours_completed" 
                                        name="hours_completed" required step="0.5" min="0">
                                </div>
        
                                <div class="mb-3">
                                    <label for="week" class="form-label">Week Number</label>
                                    <input type="number" class="form-control" id="week" 
                                        name="week" required min="1" max="52">
                                </div>
        
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="planned">Planned</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
        
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Progress</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Edit Task Modal -->
            <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Edit Progress</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editTaskForm" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="editTaskId" name="id">
                                
                                <!-- Add your edit form fields here similar to the create form -->
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Progress</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        
        <style>
            .calendar-cell {
                height: 120px;
                width: 14.28%;
                padding: 5px;
                vertical-align: top;
                position: relative;
            }
        
            .calendar-cell.today {
                background-color: #e8f4ff;
            }
        
            .calendar-cell.past-date {
                background-color: #f8f9fa;
            }
        
            .date-number {
                font-weight: bold;
                margin-bottom: 5px;
            }
        
            .task-container {
                min-height: 60px;
            }
        
            .task-item {
                background-color: #007bff;
                color: white;
                padding: 2px 5px;
                margin-bottom: 2px;
                border-radius: 3px;
                font-size: 0.8em;
                cursor: pointer;
            }
        
            .task-item.dragging {
                opacity: 0.5;
            }
        
            .calendar-cell.drag-over {
                background-color: #e9ecef;
            }
        
            .btn-group {
                gap: 5px;
            }
        </style>


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

                    let dateNumber = document.createElement('div');
                    dateNumber.className = 'date-number';
                    dateNumber.textContent = day;
                    cell.appendChild(dateNumber);

                    let taskContainer = document.createElement('div');
                    taskContainer.className = 'task-container';
                    cell.appendChild(taskContainer);

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

                    renderTasks(formattedDate, taskContainer);


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

                    function handleCellClick(cell, date) {
                        if (!cell.classList.contains('past-date')) {
                            document.getElementById('taskDate').value = date;
                            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                            modal.show();
                        }
                    }
                    if (!isPastDate) {
                        cell.addEventListener('click', function(e) {
                            // Check if the click was on the cell itself or the date number
                            if (e.target === cell || e.target === dateNumber || e.target === taskContainer) {
                                document.getElementById('taskDate').value = formattedDate;
                                const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                                modal.show();
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

                    let dateNumber = document.createElement('div');
                    dateNumber.className = 'date-number';
                    dateNumber.textContent = currentDate.getDate();
                    cell.appendChild(dateNumber);

                    if (isPastDate) {
                        cell.classList.add('past-date');
                    }

                    if (currentDate.toDateString() === today.toDateString()) {
                        cell.classList.add('today');
                    }

                    const formattedDate =
                        `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
                    cell.dataset.date = formattedDate;

                    let taskContainer = document.createElement('div');
                    taskContainer.className = 'task-container';
                    cell.appendChild(taskContainer);

                    renderTasks(formattedDate, taskContainer);

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
                    // let taskContainer = document.createElement('div');
                    // taskContainer.className = 'task-container';
                    // cell.appendChild(taskContainer);
                    // renderTasks(formattedDate, taskContainer);

                    if (!isPastDate) {
                        cell.addEventListener('click', function(e) {
                            // Check if the click was on the cell itself or the date number
                            if (e.target === cell || e.target === dateNumber || e.target === taskContainer) {
                                document.getElementById('taskDate').value = formattedDate;
                                const modal = new bootstrap.Modal(document.getElementById('taskModal'));
                                modal.show();
                            }
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
                form.action = "{{ route('calendar.update', '') }}/" + task.id;

                // Show edit modal
                const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                modal.show();
            }
        </script>
</body>

</html>
