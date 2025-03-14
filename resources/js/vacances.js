$(document).ready(function() {
    var holidays = []; // Array to store holidays
    var groupAvailability = []; // Array to store group availability
    var hasUnsavedChanges = false; // Track if there are unsaved changes

    // Function to format date as YYYY-MM-DD
    function formatDateForDB(date) {
        var d = new Date(date);
        d.setHours(12, 0, 0, 0);

        var year = d.getFullYear();
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Function to create the add holiday form
    function createAddHolidayForm() {
        var formHTML = `
            <div class="add-holiday-form p-3 border rounded">
                <h4>Add Holiday</h4>
                <div class="form-group">
                    <label for="holidayDate">Holiday Date:</label>
                    <input type="date" id="holidayDate" class="form-control">
                </div>
                <div class="form-group">
                    <label for="holidayDescription">Description:</label>
                    <input type="text" id="holidayDescription" class="form-control" placeholder="Enter holiday description">
                </div>
                <button id="addHolidayBtn" class="btn btn-primary">Add Holiday</button>
            </div>
        `;

        $('#holidayFormContainer').html(formHTML);

        // Add event listener for the add holiday button
        $('#addHolidayBtn').on('click', function() {
            var holidayDate = $('#holidayDate').val();
            var holidayDescription = $('#holidayDescription').val();

            if (holidayDate && holidayDescription) {
                holidays.push({
                    date: holidayDate,
                    description: holidayDescription
                });

                // Update the calendar to reflect the new holiday
                updateCalendar();

                // Clear the form
                $('#holidayDate').val('');
                $('#holidayDescription').val('');

                // Mark that there are unsaved changes
                setUnsavedChanges(true);
            }
        });
    }

    // Function to create the group availability form
    function createGroupAvailabilityForm() {
        var formHTML = `
            <div class="group-availability-form p-3 border rounded">
                <h4>Mark Group as Unavailable</h4>
                <div class="form-group">
                    <label for="groupSelect">Select Group:</label>
                    <select id="groupSelect" class="form-control">
                        <option value="Group 1">Group 1</option>
                        <option value="Group 2">Group 2</option>
                        <!-- Add more groups as needed -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="unavailableStartDate">Start Date:</label>
                    <input type="date" id="unavailableStartDate" class="form-control">
                </div>
                <div class="form-group">
                    <label for="unavailableEndDate">End Date:</label>
                    <input type="date" id="unavailableEndDate" class="form-control">
                </div>
                <button id="markUnavailableBtn" class="btn btn-primary">Mark as Unavailable</button>
            </div>
        `;

        $('#groupAvailabilityFormContainer').html(formHTML);

        // Add event listener for the mark unavailable button
        $('#markUnavailableBtn').on('click', function() {
            var group = $('#groupSelect').val();
            var startDate = $('#unavailableStartDate').val();
            var endDate = $('#unavailableEndDate').val();

            if (group && startDate && endDate) {
                groupAvailability.push({
                    group: group,
                    startDate: startDate,
                    endDate: endDate
                });

                // Update the calendar to reflect the new unavailability
                updateCalendar();

                // Clear the form
                $('#unavailableStartDate').val('');
                $('#unavailableEndDate').val('');

                // Mark that there are unsaved changes
                setUnsavedChanges(true);
            }
        });
    }

    // Function to update the calendar with holidays and group unavailability
    function updateCalendar() {
        // Remove all existing events
        $('#calendar').fullCalendar('removeEvents');

        // Generate new events based on holidays and group availability
        var events = [];

        // Add holidays to the calendar
        holidays.forEach(function(holiday) {
            events.push({
                id: 'holiday_' + holiday.date,
                title: 'Holiday: ' + holiday.description,
                start: holiday.date,
                allDay: true,
                className: 'holiday',
                color: '#ff0000', // Red color for holidays
                editable: false // Holidays are not editable
            });
        });

        // Add group unavailability to the calendar
        groupAvailability.forEach(function(availability) {
            events.push({
                id: 'unavailable_' + availability.group + '_' + availability.startDate,
                title: 'Unavailable: ' + availability.group,
                start: availability.startDate,
                end: availability.endDate,
                allDay: true,
                className: 'unavailable',
                color: '#808080', // Gray color for unavailability
                editable: false // Unavailability is not editable
            });
        });

        // Add the new events to the calendar
        $('#calendar').fullCalendar('addEventSource', events);
    }

    // Function to save holidays and group availability to the database
    function saveToDatabase() {
        var dataToSave = {
            holidays: holidays,
            groupAvailability: groupAvailability
        };

        console.log("Saving to database:", dataToSave);

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
    }

    // Function to show save success message
    function showSaveSuccess() {
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
        $(buttonHTML).insertAfter('#calendar');

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

    // Initialize the calendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month' // Only month view is enabled
        },
        defaultView: 'month', // Default view is month
        editable: false, // Disable editing for admin calendar
        selectable: false, // Disable adding new events for admin calendar
        firstDay: 1, // Start week on Monday (1 = Monday)
        hiddenDays: [0], // Hide Sundays (0 = Sunday)

        // Handle clicking on an event
        eventClick: function(calEvent, jsEvent, view) {
            // Display event details (optional)
            alert(`Event: ${calEvent.title}\nDate: ${calEvent.start.format('YYYY-MM-DD')}`);
        },

        events: [] // Start with empty events, will be populated by updateCalendar()
    });

    // Create the add holiday form
    createAddHolidayForm();

    // Create the group availability form
    createGroupAvailabilityForm();

    // Create the save all changes button
    createSaveAllChangesButton();

    // Setup unsaved changes warning
    setupUnsavedChangesWarning();

    // Initial calendar update
    updateCalendar();

    // Set initial state for unsaved changes
    setUnsavedChanges(false);

    // Log the initial data
    console.log("Initial holidays and group availability:", { holidays, groupAvailability });
});