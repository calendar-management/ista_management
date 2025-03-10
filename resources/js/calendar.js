$(document).ready(function() {
    var date = new Date();
    var hasUnsavedChanges = false; // Track if there are unsaved changes

    // Fetch module data from the database (replace with your API endpoint)
    function fetchModules() {
        return [
            {
                id: 1,
                name: 'Module 1',
                totalHours: 30,
                weeklyHours: 5,
                startDate: '2025-03-01', // Example start date (YYYY-MM-DD)
                completedHours: 0, // Track actual completed hours
                weeklyProgress: Array(Math.ceil(30 / 5)).fill(null), // Initialize with null
                examDate: null, // No default exam date
                customSessionDates: [] // Track custom dates for sessions
            },
            {
                id: 2,
                name: 'Module 2',
                totalHours: 45,
                weeklyHours: 6,
                startDate: '2025-03-15', // Example start date (YYYY-MM-DD)
                completedHours: 0, // Track actual completed hours
                weeklyProgress: Array(Math.ceil(45 / 6)).fill(null), // Initialize with null
                examDate: null, // No default exam date
                customSessionDates: [] // Track custom dates for sessions
            },
            {
                id: 3,
                name: 'Module 3',
                totalHours: 45,
                weeklyHours: 6,
                startDate: '2025-03-05', // Example start date (YYYY-MM-DD)
                completedHours: 0, // Track actual completed hours
                weeklyProgress: Array(Math.ceil(45 / 6)).fill(null), // Initialize with null
                examDate: null, // No default exam date
                customSessionDates: [] // Track custom dates for sessions
            }
        ];
    }

    // Function to format date as YYYY-MM-DD
    function formatDateForDB(date) {
        var d = new Date(date);
        d.setHours(12, 0, 0, 0);

        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Function to format date in a human-readable format
    function formatDateForDisplay(date) {
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(date).toLocaleDateString(undefined, options);
    }

    // Function to format date as DD/MM
    function formatDateShort(date) {
        var d = new Date(date);
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        var weekday = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][d.getDay()];

        return `${weekday} ${day}/${month}`;
    }

    // Function to prepare module data for database
    function prepareModulesForDatabase(modules) {
        return modules.map(function(module) {
            return {
                moduleId: module.id,
                moduleName: module.name,
                startDate: formatDateForDB(new Date(module.startDate)),
                examDate: module.examDate ? formatDateForDB(new Date(module.examDate)) : null,
                completedHours: module.completedHours,
                weeklyProgress: module.weeklyProgress,
                totalHours: module.totalHours,
                weeklyHours: module.weeklyHours,
                remainingHours: module.totalHours - module.completedHours,
                customSessionDates: module.customSessionDates // Include custom session dates
            };
        });
    }

    // Function to update weekly hours for a module
    function updateWeeklyProgress(moduleId, weekIndex, hoursCompleted) {
        var moduleIndex = modules.findIndex(m => m.id === moduleId);
        if (moduleIndex === -1) return false;

        // Ensure the array has enough elements, initialized with null
        while (modules[moduleIndex].weeklyProgress.length <= weekIndex) {
            modules[moduleIndex].weeklyProgress.push(null);
        }

        // Update the specific week
        modules[moduleIndex].weeklyProgress[weekIndex] = hoursCompleted;

        // Update completed hours - recalculate from all weekly progress
        modules[moduleIndex].completedHours = modules[moduleIndex].weeklyProgress
            .filter(hours => hours !== null) // Ignore null values
            .reduce((sum, hours) => sum + hours, 0);

        // Update the calendar
        updateCalendar();

        // Update progress display
        updateProgressDisplay(moduleId);

        // Update all modules progress section
        updateAllModulesProgress();

        // Mark that there are unsaved changes
        setUnsavedChanges(true);

        // Return updated module data
        return {
            module: modules[moduleIndex]
        };
    }

    // Function to update progress display with progress bar
    function updateProgressDisplay(moduleId) {
        var module = modules.find(m => m.id === moduleId);
        if (!module) return;

        var progressPercentage = (module.completedHours / module.totalHours * 100).toFixed(1);

        var html = `
            <h4>${module.name} Progress</h4>
            <div class="progress mt-2" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar"
                    style="width: ${progressPercentage}%;"
                    aria-valuenow="${module.completedHours}"
                    aria-valuemin="0"
                    aria-valuemax="${module.totalHours}">
                    ${module.completedHours}/${module.totalHours} hours (${progressPercentage}%)
                </div>
            </div>
            <div class="mt-3">
                <strong>Start Date:</strong> ${formatDateForDisplay(new Date(module.startDate))}<br>
                ${module.examDate ? `<strong>Exam Date:</strong> ${formatDateForDisplay(new Date(module.examDate))}` : ''}
            </div>
        `;

        $('#progressDisplayContainer').html(html);
    }

    // Progress bar
    function updateAllModulesProgress() {
        var html = `
            <div class="card mt-4 mb-4">
                <div class="card-header">
                    <h4>All Modules Progress</h4>
                </div>
                <div class="card-body">
        `;

        modules.forEach(function(module) {
            var progressPercentage = (module.completedHours / module.totalHours * 100).toFixed(1);
            var remainingHours = module.totalHours - module.completedHours;

            html += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <h5>${module.name}</h5>
                        <span>${module.completedHours}/${module.totalHours} hours (${remainingHours} remaining)</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: ${progressPercentage}%;"
                            aria-valuenow="${module.completedHours}"
                            aria-valuemin="0"
                            aria-valuemax="${module.totalHours}">
                            ${progressPercentage}%
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        // Create the container if it doesn't exist
        if ($('#allModulesProgressContainer').length === 0) {
            $('<div id="allModulesProgressContainer"></div>').insertAfter('#weeklyUpdateContainer');
        }

        $('#allModulesProgressContainer').html(html);
    }

    // Function to get week dates for a module
    function getWeekDates(moduleId, numberOfWeeks) {
        var module = modules.find(m => m.id === moduleId);
        if (!module) return [];

        var startDate = new Date(module.startDate);
        startDate.setHours(12, 0, 0, 0);

        var weekDates = [];

        for (let i = 0; i < numberOfWeeks; i++) {
            // Check if this week has a custom date
            if (module.customSessionDates && module.customSessionDates[i]) {
                weekDates.push(new Date(module.customSessionDates[i]));
            } else {
                // Otherwise use calculated date
                let weekDate = new Date(startDate);
                weekDate.setDate(startDate.getDate() + (i * 7));
                weekDates.push(weekDate);
            }
        }

        return weekDates;
    }

    // Create a form for updating weekly hours
    function createWeeklyUpdateForm() {
        var formHTML = `
            <div class="weekly-update-form p-3 border rounded">
                <h4>Update Weekly Hours</h4>
                <div class="form-group">
                    <label for="moduleSelect">Select Module:</label>
                    <select id="moduleSelect" class="form-control">
                        ${modules.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                    </select>
                </div>
                <div class="form-group">
                    <label for="weekSelect">Week Number:</label>
                    <select id="weekSelect" class="form-control">
                        <!-- Will be dynamically populated based on selected module -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="hoursCompleted">Hours Completed:</label>
                    <input type="number" id="hoursCompleted" class="form-control" min="0" max="40" step="0.5">
                </div>
                <div class="form-row">
                    <div class="col">
                        <button id="updateProgressBtn" class="btn btn-primary btn-block">Save Hours</button>
                    </div>
                    <div class="col">
                        <button id="markAbsentBtn" class="btn btn-warning btn-block">Mark as 0 (Absent)</button>
                    </div>
                </div>
                <div id="updateStatus" class="mt-2"></div>
            </div>
        `;

        $('#weeklyUpdateContainer').html(formHTML);

        // Function to update the week select options based on the selected module
        function updateWeekSelectOptions(moduleId) {
            var module = modules.find(m => m.id === moduleId);
            if (!module) return;

            var weeksNeeded = Math.ceil(module.totalHours / module.weeklyHours);
            var weekDates = getWeekDates(moduleId, weeksNeeded);

            var options = weekDates.map((date, i) => {
                return `<option value="${i}">Week ${i+1} - ${formatDateShort(date)}</option>`;
            }).join('');

            $('#weekSelect').html(options);
        }

        // Pre-fill the hours input with the standard weekly hours of the selected module
        $('#moduleSelect').on('change', function() {
            var moduleId = parseInt($(this).val());
            var module = modules.find(m => m.id === moduleId);

            if (module) {
                updateWeekSelectOptions(moduleId);
                $('#hoursCompleted').val(module.weeklyHours);
                updateProgressDisplay(moduleId);
            }
        });

        // When week changes, pre-fill with existing data if available
        $('#weekSelect').on('change', function() {
            var moduleId = parseInt($('#moduleSelect').val());
            var weekIndex = parseInt($(this).val());
            var module = modules.find(m => m.id === moduleId);

            if (module && weekIndex < module.weeklyProgress.length && module.weeklyProgress[weekIndex] !== null) {
                $('#hoursCompleted').val(module.weeklyProgress[weekIndex]);
            } else if (module) {
                $('#hoursCompleted').val(module.weeklyHours); // Default to standard weekly hours
            }
        });

        // Add event listener for the update button
        $('#updateProgressBtn').on('click', function() {
            var moduleId = parseInt($('#moduleSelect').val());
            var weekIndex = parseInt($('#weekSelect').val());
            var hoursCompleted = parseFloat($('#hoursCompleted').val());

            var result = updateWeeklyProgress(moduleId, weekIndex, hoursCompleted);
            if (result) {
                $('#updateStatus').html(`
                    <div class="alert alert-success">
                        <strong>Success!</strong> Week ${weekIndex+1} updated with ${hoursCompleted} hours.
                        <br><small>Remember to click "Save All Changes" to save to database.</small>
                    </div>
                `);
            } else {
                $('#updateStatus').html('<div class="alert alert-danger">Failed to update progress</div>');
            }
        });

        // Add event listener for the mark absent button
        $('#markAbsentBtn').on('click', function() {
            $('#hoursCompleted').val(0);
            $('#updateProgressBtn').click();
        });

        // Trigger change event to initialize the form with data
        $('#moduleSelect').trigger('change');
    }
    // Add CSS for the unsaved changes button
    function addCustomStyles() {
        $('<style>')
            .text(`
                #saveChangesCard {
                    position: sticky;
                    bottom: 20px;
                    z-index: 100;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
                .save-notification {
                    margin-bottom: 0;
                }
                .fc-event.module-start {color: #000000; background-color: #ffff; border-color: #007bff; }
                .fc-event.module-exam {color: #000000; background-color: #dc3545; border-color: #dc3545; }
                .fc-event.progress {color: #000000; background-color: #28a745; border-color: #28a745; }
                .fc-event.absence {color: #000000; background-color: #ffc107; border-color: #ffc107; }
                .fc-event.planned-session {color: #000000; background-color: #FFFF; border-color: #6c757d; opacity: 0.7; }
                /* Add a visual cue for draggable events */
                .fc-event {cursor: pointer;}
            `)
            .appendTo('head');
    }
    // Function to update the calendar with the latest data
    function updateCalendar() {
        // Remove all existing events
        $('#calendar').fullCalendar('removeEvents');

        // Generate new events based on current module data
        var events = [];

        modules.forEach(function(module) {
            var startDate = new Date(module.startDate);
            startDate.setHours(12, 0, 0, 0);

            // Module start event - now on the exact same day as the first progress session
            events.push({
                id: 'start_' + module.id,
                title: module.name + ' - Starts',
                start: startDate,
                allDay: true,
                className: 'module-start', // Blue color for start date
                editable: true, // Make it draggable
                moduleId: module.id,
                type: 'module-start'
            });

            // Module exam date (draggable) - only if examDate is set
            if (module.examDate) {
                events.push({
                    id: 'exam_' + module.id,
                    title: module.name + ' - Exam',
                    start: new Date(module.examDate),
                    allDay: true,
                    className: 'module-exam', // Red color for exam date
                    editable: true, // Make it draggable
                    moduleId: module.id,
                    type: 'module-exam'
                });
            }

            // Generate weekly sessions based on the module's startDate
            var weeksNeeded = Math.ceil(module.totalHours / module.weeklyHours);
            var weekDates = getWeekDates(module.id, weeksNeeded);

            for (let i = 0; i < weeksNeeded; i++) {
                // Get the hours for this week (from weeklyProgress if available, otherwise planned hours)
                let weekHours = i < module.weeklyProgress.length && module.weeklyProgress[i] !== null ?
                    module.weeklyProgress[i] :
                    module.weeklyHours;

                // Determine the class and color based on this specific week's status only
                let eventClass = 'planned-session';
                let eventColor = '#6c757d';

                if (i < module.weeklyProgress.length && module.weeklyProgress[i] !== null) {
                    if (module.weeklyProgress[i] > 0) {
                        eventClass = 'progress';
                        eventColor = '#28a745';
                    } else if (module.weeklyProgress[i] === 0) {
                        eventClass = 'absence';
                        eventColor = '#ffc107';
                    }
                }

                events.push({
                    id: 'week_' + module.id + '_' + i,
                    title: `${module.name} - Week ${i+1}: ${weekHours} hrs`,
                    start: weekDates[i],
                    allDay: true,
                    className: eventClass,
                    color: eventColor,
                    moduleId: module.id,
                    weekIndex: i,
                    type: 'week',
                    editable: true // These are draggable
                });
            }
        });
        addCustomStyles()

        // Add the new events to the calendar
        $('#calendar').fullCalendar('addEventSource', events);
    }

    // New function to handle the save to database action
    function saveToDatabase() {
        var moduleData = prepareModulesForDatabase(modules);
        console.log("Saving to database:", moduleData);
        
        // Show loading state
        $('#saveAllChangesBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        $('#saveAllChangesBtn').prop('disabled', true);
        
        // Simulate an AJAX call (replace this with your actual AJAX call)
        setTimeout(function() {
            // Simulate successful save
            showSaveSuccess();
            setUnsavedChanges(false);
            
            // Reset the button state
            $('#saveAllChangesBtn').html('<i class="fas fa-save mr-1"></i> Save All Changes');
            $('#saveAllChangesBtn').prop('disabled', !hasUnsavedChanges);
        }, 800); // Simulate a 800ms delay for the save operation
        
        // Example AJAX call (uncomment and modify as needed):
        /*
        $.ajax({
            url: '/api/updateModules',
            type: 'POST',
            data: JSON.stringify(moduleData),
            contentType: 'application/json',
            success: function(response) {
                showSaveSuccess();
                setUnsavedChanges(false);
                
                // Reset the button state
                $('#saveAllChangesBtn').html('<i class="fas fa-save mr-1"></i> Save All Changes');
                $('#saveAllChangesBtn').prop('disabled', !hasUnsavedChanges);
            },
            error: function(error) {
                showSaveError(error);
                
                // Reset the button state even if there's an error
                $('#saveAllChangesBtn').html('<i class="fas fa-save mr-1"></i> Save All Changes');
                $('#saveAllChangesBtn').prop('disabled', !hasUnsavedChanges);
            }
        });
        */
    }

    // Function to show save success message
    function showSaveSuccess() {
        // Create a notification that auto-dismisses
        var notification = $(`
            <div class="alert alert-success save-notification" role="alert">
                <strong>Success!</strong> All changes saved to database.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('#saveNotificationArea').html(notification);
        
        // Auto-dismiss after 3 seconds
        setTimeout(function() {
            notification.alert('close');
        }, 3000);
    }

    // Function to show save error message
    function showSaveError(error) {
        var notification = $(`
            <div class="alert alert-danger save-notification" role="alert">
                <strong>Error!</strong> Failed to save changes to database. Please try again.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
        
        $('#saveNotificationArea').html(notification);
    }

    // Function to update the unsaved changes status
    function setUnsavedChanges(value) {
        hasUnsavedChanges = value;
        
        // Update the save button state
        if (hasUnsavedChanges) {
            $('#saveAllChangesBtn').removeClass('btn-outline-primary').addClass('btn-primary').prop('disabled', false);
        } else {
            $('#saveAllChangesBtn').removeClass('btn-primary').addClass('btn-outline-primary').prop('disabled', true);
        }
        
        // Update the notification area
        if (hasUnsavedChanges) {
            if ($('#unsavedChangesAlert').length === 0) {
                $('#saveNotificationArea').append(`
                    <div class="alert alert-warning" id="unsavedChangesAlert" role="alert">
                        <strong>Unsaved Changes!</strong> Click "Save All Changes" to save your changes to the database.
                    </div>
                `);
            }
        } else {
            $('#unsavedChangesAlert').remove();
        }
    }

    // Function to handle module/exam date changes
    function updateModuleDate(moduleId, dateType, newDate) {
        var moduleIndex = modules.findIndex(m => m.id === moduleId);
        if (moduleIndex === -1) return false;

        if (dateType === 'module-start') {
            modules[moduleIndex].startDate = formatDateForDB(newDate);
        } else if (dateType === 'module-exam') {
            modules[moduleIndex].examDate = formatDateForDB(newDate);
        }

        // Update the calendar to reflect the changes
        updateCalendar();

        // Update progress display if this is the currently selected module
        if (parseInt($('#moduleSelect').val()) === moduleId) {
            updateProgressDisplay(moduleId);
            // Update week options since start date might have changed
            var currentModuleId = parseInt($('#moduleSelect').val());
            if (currentModuleId === moduleId) {
                // Re-trigger the change to update week dates
                $('#moduleSelect').trigger('change');
            }
        }

        // Update all modules progress
        updateAllModulesProgress();

        // Mark that we have unsaved changes
        setUnsavedChanges(true);

        return true;
    }

    // Updated function to handle progress session date changes
    function updateProgressSessionDate(moduleId, weekIndex, newDate) {
        var moduleIndex = modules.findIndex(m => m.id === moduleId);
        if (moduleIndex === -1) return false;

        // Validate that the date is not Sunday
        var day = newDate.getDay();
        if (day === 0) { // 0 = Sunday
            alert("Progress sessions cannot be scheduled on Sundays. Please choose another day.");
            return false;
        }

        // Create the customSessionDates array if it doesn't exist
        if (!modules[moduleIndex].customSessionDates) {
            modules[moduleIndex].customSessionDates = [];
        }

        // Ensure the array has enough elements
        while (modules[moduleIndex].customSessionDates.length <= weekIndex) {
            modules[moduleIndex].customSessionDates.push(null);
        }

        // Update the specific week's custom date
        modules[moduleIndex].customSessionDates[weekIndex] = formatDateForDB(newDate);

        console.log(`Progress session for Module ${moduleId}, Week ${weekIndex} moved to ${formatDateForDB(newDate)}`);

        // Update the calendar
        updateCalendar();

        // Update the week selector if this is the currently selected module
        if (parseInt($('#moduleSelect').val()) === moduleId) {
            $('#moduleSelect').trigger('change');
        }

        // Mark that there are unsaved changes
        setUnsavedChanges(true);

        return true;
    }

    // Function to create the dialog for adding events to calendar
    function createAddEventDialog() {
        var dialogHTML = `
            <div id="addEventModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Event</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="eventTypeSelect">Event Type:</label>
                                <select id="eventTypeSelect" class="form-control">
                                    <option value="module-start">Module Start</option>
                                    <option value="module-exam">Module Exam</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="eventModuleSelect">Module:</label>
                                <select id="eventModuleSelect" class="form-control">
                                    ${modules.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="eventDate">Date:</label>
                                <input type="text" id="eventDate" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="saveEventBtn">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(dialogHTML);

        // Add event listener for save button
        $('#saveEventBtn').on('click', function() {
            var moduleId = parseInt($('#eventModuleSelect').val());
            var eventType = $('#eventTypeSelect').val();
            var eventDate = new Date($('#eventDate').val());

            var updated = updateModuleDate(moduleId, eventType, eventDate);

            if (updated) {
                $('#addEventModal').modal('hide');
                setUnsavedChanges(true);
            }
        });
    }

    // Function to create the "Save All Changes" button and notification area
    function createSaveAllChangesButton() {
        var buttonHTML = `
            <div class="mt-4 card mb-4" id="saveChangesCard">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div id="saveNotificationArea"></div>
                        <button id="saveAllChangesBtn" class="btn btn-outline-primary" disabled>
                            <i class="fas fa-save mr-1"></i> Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Insert the button after the calendar
        $(buttonHTML).insertAfter('#call');

        // Add event listener for save button
        $('#saveAllChangesBtn').on('click', function() {
            if (hasUnsavedChanges) {
                // Show loading state
                $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                $(this).prop('disabled', true);
                
                // Call save function
                saveToDatabase();
            }
        });
    }

    // Add window beforeunload event to warn about unsaved changes
    function setupUnsavedChangesWarning() {
        $(window).on('beforeunload', function() {
            if (hasUnsavedChanges) {
                return "You have unsaved changes. Are you sure you want to leave without saving?";
            }
        });
    }

    // Process modules to initialize
    var modules = fetchModules();

    // Create the add event dialog
    createAddEventDialog();

    // Create the save all changes button
    createSaveAllChangesButton();

    // Initialize the calendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month' // Only month view is enabled
        },
        defaultView: 'month', // Default view is month
        editable: true, // Enable editing
        selectable: true, // Allow adding new events
        selectHelper: true,
        firstDay: 1, // Start week on Monday (1 = Monday)
        hiddenDays: [0], // Hide Sundays (0 = Sunday)

        // Handle selecting a day on the calendar
        select: function(start, end) {
            // Set the selected date in the modal
            $('#eventDate').val(formatDateForDB(start));

            // Show the modal
            $('#addEventModal').modal('show');
        },

        // Handle event drops (dragging)
        eventDrop: function(event, delta, revertFunc) {
            if (event.type === 'module-start' || event.type === 'module-exam') {
                var updated = updateModuleDate(event.moduleId, event.type, event.start);
                if (!updated) {
                    revertFunc(); // Revert the drag if update failed
                }
            } else if (event.type === 'week') {
                // Handle progress session dragging
                var updated = updateProgressSessionDate(event.moduleId, event.weekIndex, event.start);
                if (!updated) {
                    revertFunc(); // Revert the drag if update failed
                }
            }
        },

        // Handle clicking on an event
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.type === 'week') {
                // Pre-select the module and week in the update form
                $('#moduleSelect').val(calEvent.moduleId).trigger('change');
                $('#weekSelect').val(calEvent.weekIndex).trigger('change');

                // Scroll to the form
                $('html, body').animate({
                    scrollTop: $("#weeklyUpdateContainer").offset().top - 50
                }, 200);

                // Focus on the hours input
                $('#hoursCompleted').focus().select();
            }
        },

        events: [] // Start with empty events, will be populated by updateCalendar()
    });

    // Create the weekly update form
    createWeeklyUpdateForm();

    // Initialize the all modules progress section
    updateAllModulesProgress();

    // Setup unsaved changes warning
    setupUnsavedChangesWarning();

    // Initial calendar update
    updateCalendar();

    // Set initial state for unsaved changes
    setUnsavedChanges(false);

    // Log the initial data
    console.log("Initial module data:", prepareModulesForDatabase(modules));
});