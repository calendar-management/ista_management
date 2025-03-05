<!DOCTYPE html>
<html>

<head>
    <title>Professor's Calendar</title>
    @vite('resources/js/app.jsx')
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

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
$(document).ready(function() {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    // Example module data (replace with dynamic data from your backend)
    var modules = [
        { id: 1, name: 'Module 1' }, // Module 1
        { id: 2, name: 'Module 2' }  // Module 2
    ];

    // Track total hours worked, first session date, and final exam dates for each module
    var moduleHours = {};
    var firstSessionDates = {}; // Store the first session date for each module
    var finalExamDates = {}; // Store final exam dates for each module
    var moduleEvents = {}; // Store events for each module
    var moduleDetails = {}; // Store total hours and weekly hours for each module

    // Initialize hours, first session dates, and exam dates for each module
    modules.forEach(function(module) {
        moduleHours[module.id] = 0; // Initialize total hours worked to 0
        firstSessionDates[module.id] = null; // Initialize first session date to null
        finalExamDates[module.id] = null; // Initialize final exam date to null
        moduleEvents[module.id] = []; // Initialize events array for the module
        moduleDetails[module.id] = { totalHours: null, weeklyHours: null }; // Initialize module details
    });

    // Function to adjust the date to Monday if it's Sunday
    function adjustToMonday(date) {
        if (date.getDay() === 0) { // Sunday
            date.setDate(date.getDate() + 1); // Move to Monday
        }
        return date;
    }

    // Function to calculate the final exam date
    function calculateFinalExamDate(moduleId, startDate) {
        var module = modules.find(function(m) {
            return m.id == moduleId;
        });
        if (module) {
            var totalHoursRequiredNum = moduleDetails[module.id].totalHours;
            var weeklyHoursNum = moduleDetails[module.id].weeklyHours;
            var sessions = Math.ceil(totalHoursRequiredNum / weeklyHoursNum); // Total sessions required

            // Calculate the final exam date
            var examDate = new Date(startDate); // Start from the first session date
            examDate.setDate(examDate.getDate() + (sessions * 7)); // Add weeks to the first session date

            // Adjust to Monday if the exam date is Sunday
            examDate = adjustToMonday(examDate);

            return examDate;
        }
        return null;
    }

    // Function to update the final exam date on the calendar
    function updateFinalExamDate(moduleId, examDate) {
        var module = modules.find(function(m) {
            return m.id == moduleId;
        });
        if (module) {
            // Remove the old final exam event
            var oldExamEvent = moduleEvents[module.id].find(function(event) {
                return event.title.includes('Final Exam');
            });
            if (oldExamEvent) {
                $('#calendar').fullCalendar('removeEvents', oldExamEvent._id);
            }

            // Add the new final exam event
            var newExamEvent = {
                title: module.name + ' - Final Exam',
                start: examDate,
                allDay: true,
                className: 'important' // Red color for final exam
            };
            $('#calendar').fullCalendar('renderEvent', newExamEvent, true); // Stick the event

            // Store the new final exam date
            finalExamDates[module.id] = examDate;

            // Update the module events
            moduleEvents[module.id] = moduleEvents[module.id].filter(function(event) {
                return !event.title.includes('Final Exam');
            });
            moduleEvents[module.id].push(newExamEvent);
        }
    }

    // Initialize the calendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek' // Only month and week views
        },
        defaultView: 'month', // Default view
        editable: true,
        selectable: true,
        selectHelper: true,
        select: function(start, end, allDay) {
            var moduleId = prompt('Enter Module ID (1 for Module 1, 2 for Module 2):');
            var totalHoursRequired = prompt('Enter Total Hours Required for This Module:');
            var weeklyHours = prompt('Enter Weekly Working Hours for This Module:');

            if (moduleId && totalHoursRequired && weeklyHours) {
                var module = modules.find(function(m) {
                    return m.id == moduleId;
                });

                if (module) {
                    var eventTitle = module.name + ' - ' + weeklyHours + ' hours this week';

                    // Add event to the calendar
                    var event = {
                        title: eventTitle,
                        start: start,
                        end: end,
                        allDay: allDay,
                        className: 'success'
                    };
                    $('#calendar').fullCalendar('renderEvent', event, true); // Stick the event

                    // Update total hours worked for the module
                    moduleHours[module.id] += parseInt(weeklyHours);

                    // Store the module details
                    moduleDetails[module.id] = {
                        totalHours: parseInt(totalHoursRequired),
                        weeklyHours: parseInt(weeklyHours)
                    };

                    // If this is the first session, store the start date
                    if (!firstSessionDates[module.id]) {
                        firstSessionDates[module.id] = start;
                    }

                    // Calculate final exam date
                    var examDate = calculateFinalExamDate(moduleId, firstSessionDates[module.id]);

                    // Update the final exam date on the calendar
                    updateFinalExamDate(moduleId, examDate);

                    // Store the event in the module events array
                    moduleEvents[module.id].push(event);

                    // Log the final exam date (you can send this to your database later)
                    console.log('Final Exam Date for ' + module.name + ': ' + examDate.toDateString());
                    alert('Final Exam Date for ' + module.name + ': ' + examDate.toDateString());
                } else {
                    alert('Invalid Module ID!');
                }
            }
            $('#calendar').fullCalendar('unselect');
        },
        eventDrop: function(event, delta, revertFunc) {
            if (event.title.includes('Final Exam')) {
                // If the final exam is dragged, ask for confirmation
                if (confirm('Are you sure you want to change the final exam date?')) {
                    var moduleId = modules.find(function(m) {
                        return event.title.includes(m.name);
                    }).id;
                    finalExamDates[moduleId] = event.start; // Update the final exam date
                } else {
                    revertFunc(); // Revert the event to its original date
                }
            } else {
                // If a working hours event is dragged, recalculate the final exam date
                var moduleId = modules.find(function(m) {
                    return event.title.includes(m.name);
                }).id;
                var examDate = calculateFinalExamDate(moduleId, event.start);
                updateFinalExamDate(moduleId, examDate);
            }
        },
        eventClick: function(event) {
            if (confirm('Do you want to delete this event?')) {
                var moduleId = modules.find(function(m) {
                    return event.title.includes(m.name);
                }).id;

                // Remove the event from the calendar
                $('#calendar').fullCalendar('removeEvents', event._id);

                // Remove the event from the module events array
                moduleEvents[moduleId] = moduleEvents[moduleId].filter(function(e) {
                    return e._id !== event._id;
                });

                // If the deleted event is the final exam, recalculate it
                if (event.title.includes('Final Exam')) {
                    var examDate = calculateFinalExamDate(moduleId, firstSessionDates[moduleId]);
                    // updateFinalExamDate(moduleId, examDate);
                }
            }
        },
        events: [
            // Example events (you can replace this with dynamic data)
            {
                title: 'Module 1 - 5 hours this week',
                start: new Date(y, m, d + 1, 10, 0),
                className: 'info'
            },
            {
                title: 'Module 2 - 6 hours this week',
                start: new Date(y, m, d + 2, 14, 0),
                className: 'important'
            }
        ]
    });

    // Function to get the final exam date for a module (for database storage)
    function getFinalExamDate(moduleId) {
        return finalExamDates[moduleId];
    }

    // Example: Get final exam date for Module 1
    var module1ExamDate = getFinalExamDate(1);
    console.log('Final Exam Date for Module 1:', module1ExamDate);
});
    </script>
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
    </style>
</head>

<body>

    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-danger sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Formateurs</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Calendar -->
            <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Calendar</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

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
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Calendar -->
                <div id='wrap'>
                    <div id='calendar'></div>
                    <div style='clear:both'></div>
                </div>
            </div>
        </div>
    </div>

    
</body>



</html>