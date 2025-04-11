$(document).ready(function () {
    // Calendar App - manages formateurs modules, progress tracking and scheduling
    class CalendarApp {
        //////////////////////////////////////hello world
        constructor() {
            this.date = new Date();
            this.hasUnsavedChanges = false;
            this.modules = this.fetchModules();
            this.holidays = this.fetchHolidays();

            // DOM element caching
            this.$calendar = $("#calendar");
            this.$moduleSelect = $("#moduleSelect");
            this.$weekSelect = $("#weekSelect");
            this.$hoursCompleted = $("#hoursCompleted");
            this.$saveBtn = $("#saveAllChangesBtn");
            this.$updateStatus = $("#updateStatus");

            this.init();
        }

        // Initialize the application
        init() {
            this.addCustomStyles();
            this.initCalendar();
            this.createAddEventDialog();
            this.createSaveButton();
            this.createWeeklyUpdateForm();
            this.updateAllModulesProgress();
            this.setupUnsavedChangesWarning();
            this.updateCalendar();
            this.setUnsavedChanges(false);
        }

        // Fetch module data from the database (mock)
        // Fetch module data from the database
        fetchModules() {
            // Check if data is defined
            if (typeof data !== "undefined") {
                return data;
            } else {
                console.warn("Module data not found. Using empty array.");
                return [];
            }
        }
        // Add this method after fetchModules()
        fetchHolidays() {
            return holidays;
        }

        // Format date based on specified format
        formatDate(date, format = "db") {
            const d = new Date(date);
            d.setHours(12, 0, 0, 0);

            switch (format) {
                case "db":
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, "0");
                    const day = String(d.getDate()).padStart(2, "0");
                    return `${year}-${month}-${day}`;

                case "display":
                    return d.toLocaleDateString(undefined, {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    });

                case "short":
                    const m = String(d.getMonth() + 1).padStart(2, "0");
                    const dy = String(d.getDate()).padStart(2, "0");
                    const weekday = [
                        "Sunday",
                        "Monday",
                        "Tuesday",
                        "Wednesday",
                        "Thursday",
                        "Friday",
                        "Saturday",
                    ][d.getDay()];
                    return `${weekday} ${dy}/${m}`;
            }
        }

        // Prepare module data for database
        prepareModulesForDatabase() {
            return this.modules.map((module) => {
                // Create a copy of the module data
                const moduleData = {
                    moduleId: module.id,
                    moduleName: module.name,
                    startDate: this.formatDate(module.startDate),
                    examDate: module.examDate
                        ? this.formatDate(new Date(module.examDate))
                        : null,
                    completedHours: module.completedHours,
                    weeklyProgress: module.weeklyProgress,
                    totalHours: module.totalHours,
                    weeklyHours: module.weeklyHours,
                    remainingHours: module.totalHours - module.completedHours,
                    customSessionDates: module.customSessionDates,
                };

                // Add the exam date as the last session if it exists
                if (module.examDate) {
                    const weeksNeeded = Math.ceil(
                        module.totalHours / module.weeklyHours
                    );
                    const lastSessionIndex = weeksNeeded;

                    // Ensure customSessionDates array exists
                    if (!moduleData.customSessionDates) {
                        moduleData.customSessionDates = [];
                    }

                    // Make sure the array is long enough
                    while (
                        moduleData.customSessionDates.length <= lastSessionIndex
                    ) {
                        moduleData.customSessionDates.push(null);
                    }

                    // Set the exam date as the last session
                    moduleData.customSessionDates[lastSessionIndex] =
                        module.examDate;
                    moduleData.finalExamDate = module.examDate;
                }

                return moduleData;
            });
        }

        // Update weekly progress for a module
        updateWeeklyProgress(moduleId, weekIndex, hoursCompleted) {
            const moduleIndex = this.modules.findIndex(
                (m) => m.id === moduleId
            );
            if (moduleIndex === -1) return false;

            const module = this.modules[moduleIndex];

            // Ensure the array has enough elements
            while (module.weeklyProgress.length <= weekIndex) {
                module.weeklyProgress.push(null);
            }

            // Update the specific week
            module.weeklyProgress[weekIndex] = hoursCompleted;

            // Update completed hours
            module.completedHours = module.weeklyProgress
                .filter((hours) => hours !== null)
                .reduce((sum, hours) => sum + hours, 0);

            this.refreshUI(moduleId);
            this.setUnsavedChanges(true);

            return { module };
        }

        // Refresh all UI elements
        refreshUI(moduleId = null) {
            this.updateCalendar();
            if (moduleId) this.updateProgressDisplay(moduleId);
            this.updateAllModulesProgress();
        }

        // Update progress display with progress bar
        updateProgressDisplay(moduleId) {
            const module = this.modules.find((m) => m.id === moduleId);
            if (!module) return;

            const progressPercentage = (
                (module.completedHours / module.totalHours) *
                100
            ).toFixed(1);

            $("#progressDisplayContainer").html(`
                <h4>${module.name} Progress</h4>
                <div class="progress mt-2" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: ${progressPercentage}%;"
                        aria-valuenow="${module.completedHours}"
                        aria-valuemin="0"
                        aria-valuemax="${module.totalHours}">
                        ${
                            module.completedHours
                        }/${module.totalHours} hours (${progressPercentage}%)
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Date de début :</strong> ${this.formatDate(
                        new Date(module.startDate),
                        "display"
                    )}<br>
                    ${
                        module.examDate
                            ? `<strong>Date d'examen :</strong> ${this.formatDate(
                                  new Date(module.examDate),
                                  "display"
                              )}`
                            : ""
                    }
                </div>
            `);
        }

        // Update progress for all modules
        updateAllModulesProgress() {
            let html = `
                    <div class="card mt-4 mb-4">
                        <div class="card-header">
                            <h4>Progression de tous les modules</h4>
                        </div>
                        <div class="card-body">
                `;

            this.modules.forEach((module) => {
                const cappedCompletedHours = Math.min(
                    module.completedHours,
                    module.totalHours
                );
                const remainingHours = Math.max(
                    module.totalHours - cappedCompletedHours,
                    0
                );
                const progressPercentage = Math.min(
                    ((cappedCompletedHours / module.totalHours) * 100).toFixed(
                        1
                    ),
                    100
                );

                html += `
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <h5>${module.name}</h5>
                                <span>${cappedCompletedHours}/${module.totalHours} heures (${remainingHours} restantes)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: ${progressPercentage}%;"
                                    aria-valuenow="${cappedCompletedHours}"
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
            if ($("#allModulesProgressContainer").length === 0) {
                $('<div id="allModulesProgressContainer"></div>').insertAfter(
                    "#weeklyUpdateContainer"
                );
            }

            $("#allModulesProgressContainer").html(html);
        }

        // Get week dates for a module, maintaining original weekday after holidays
        getWeekDates(moduleId, numberOfWeeks) {
            const module = this.modules.find((m) => m.id === moduleId);
            if (!module) return [];

            const startDate = new Date(module.startDate);
            startDate.setHours(12, 0, 0, 0);

            // Get the original weekday (0-6, where 0 is Sunday)
            const originalWeekday = startDate.getDay();

            const weekDates = [];
            let currentDate = new Date(startDate);

            for (let i = 0; i < numberOfWeeks; i++) {
                // Check if this week has a custom date
                if (module.customSessionDates && module.customSessionDates[i]) {
                    weekDates.push(new Date(module.customSessionDates[i]));
                } else {
                    // For the first week, use the start date
                    if (i === 0) {
                        weekDates.push(new Date(currentDate));
                        continue;
                    }

                    // For subsequent weeks, add 7 days to the previous date
                    currentDate = new Date(weekDates[i - 1]);
                    currentDate.setDate(currentDate.getDate() + 7);

                    // Check if this date falls on a holiday
                    if (this.isHolidayDate(currentDate)) {
                        // Find the next available date with the same weekday
                        while (this.isHolidayDate(currentDate)) {
                            // Move to the next week
                            currentDate.setDate(currentDate.getDate() + 7);
                        }
                    }

                    weekDates.push(new Date(currentDate));
                }
            }

            return weekDates;
        }

        // Create a form for updating weekly hours
        createWeeklyUpdateForm() {
            $("#weeklyUpdateContainer").html(`
                <div class="weekly-update-form p-3 border rounded">
                    <h4>Mettre à jour les heures hebdomadaires</h4>
                    <div class="form-group">
                        <label for="moduleSelect">Sélectionner un module :</label>
                        <select id="moduleSelect" class="form-control">
                            ${this.modules
                                .map(
                                    (m) =>
                                        `<option value="${m.id}">${m.name}</option>`
                                )
                                .join("")}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weekSelect">Numéro de semaine :</label>
                        <select id="weekSelect" class="form-control">
                            <!-- Will be dynamically populated -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hoursCompleted">Heures effectuées :</label>
                        <input type="number" id="hoursCompleted" class="form-control" min="0" max="40" step="0.5">
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <button id="updateProgressBtn" class="btn btn-primary btn-block">Enregistrer les heures</button>
                        </div>
                        <div class="col">
                            <button id="markAbsentBtn" class="btn btn-warning btn-block">Marquer comme 0 (Absent)</button>
                        </div>
                    </div>
                    <div id="updateStatus" class="mt-2"></div>
                </div>
            `);

            // Setup event handlers for form elements
            this.setupWeeklyFormEvents();
        }

        // Setup event handlers for weekly form
        setupWeeklyFormEvents() {
            const self = this;

            // Update week options when module changes
            $("#moduleSelect").on("change", function () {
                const moduleId = parseInt($(this).val());
                const module = self.modules.find((m) => m.id === moduleId);

                if (module) {
                    self.updateWeekSelectOptions(moduleId);
                    $("#hoursCompleted").val(module.weeklyHours);
                    self.updateProgressDisplay(moduleId);
                }
            });

            // Pre-fill with existing data when week changes
            $("#weekSelect").on("change", function () {
                const moduleId = parseInt($("#moduleSelect").val());
                const weekIndex = parseInt($(this).val());
                const module = self.modules.find((m) => m.id === moduleId);

                if (
                    module &&
                    weekIndex < module.weeklyProgress.length &&
                    module.weeklyProgress[weekIndex] !== null
                ) {
                    $("#hoursCompleted").val(module.weeklyProgress[weekIndex]);
                } else if (module) {
                    $("#hoursCompleted").val(module.weeklyHours);
                }
            });

            // Update progress button handler
            $("#updateProgressBtn").on("click", function () {
                const moduleId = parseInt($("#moduleSelect").val());
                const weekIndex = parseInt($("#weekSelect").val());
                const hoursCompleted = parseFloat($("#hoursCompleted").val());

                const result = self.updateWeeklyProgress(
                    moduleId,
                    weekIndex,
                    hoursCompleted
                );
                if (result) {
                    $("#updateStatus").html(`
                        <div class="alert alert-success">
                            <strong>Succès !</strong> La semaine ${
                                weekIndex + 1
                            } a été mise à jour avec ${hoursCompleted} heures.
                            <br><small>Souvenez-vous de cliquer sur "Enregistrer toutes les modifications" pour sauvegarder.</small>
                        </div>
                    `);
                } else {
                    $("#updateStatus").html(
                        '<div class="alert alert-danger">Échec de la mise à jour de la progression</div>'
                    );
                }
            });

            // Mark absent button handler
            $("#markAbsentBtn").on("click", function () {
                $("#hoursCompleted").val(0);
                $("#updateProgressBtn").click();
            });

            // Initialize form with data
            $("#moduleSelect").trigger("change");
        }

        // Update week select options
        updateWeekSelectOptions(moduleId) {
            const module = this.modules.find((m) => m.id === moduleId);
            if (!module) return;

            const weeksNeeded = Math.ceil(
                module.totalHours / module.weeklyHours
            );
            const weekDates = this.getWeekDates(moduleId, weeksNeeded);

            const options = weekDates
                .map(
                    (date, i) =>
                        `<option value="${i}">Week ${i + 1} - ${this.formatDate(
                            date,
                            "short"
                        )}</option>`
                )
                .join("");

            $("#weekSelect").html(options);
        }

        // Add custom styles for the calendar
        addCustomStyles() {
            if ($("#calendarCustomStyles").length === 0) {
                $('<style id="calendarCustomStyles">')
                    .text(
                        `
                        #saveChangesCard {
                            position: sticky;
                            bottom: 20px;
                            z-index: 100;
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                            display: flex;
                            flex-diraction: column;
                        }
                        .save-notification { margin-bottom: 10px; }
                        .fc-event.module-start {color: #000000; background-color: #ffff; border-color: #007bff; }
                        .fc-event.module-exam {color: #000000; background-color: #dc3545; border-color: #dc3545; }
                        .fc-event.progress {color: #000000; background-color: #28a745; border-color: #28a745; }
                        .fc-event.absence {color: #000000; background-color: #ffc107; border-color: #ffc107; }
                        .fc-event.planned-session {color: #000000; background-color: #FFFF; border-color: #6c757d; opacity: 0.7; }
                        .fc-event {cursor: pointer;}
                        .fc-event.holiday-event {opacity: 0.7; z-index: 1;  color:rgb(239, 222, 222); background-color:rgb(12, 3, 4);}
                        .holiday-label {font-weight: bold; font-style: italic; z-index: 2; color:rgb(239, 222, 222);}
                    `
                    )
                    .appendTo("head");
            }
        }
        // check if a date is within a holiday period
        isHolidayDate(date) {
            const formattedDate = this.formatDate(date);
            return this.holidays.some((holiday) => {
                const start = new Date(holiday.startDate);
                // If endDate is present, use it, otherwise use startDate for single-day holidays
                const end = holiday.endDate
                    ? new Date(holiday.endDate)
                    : new Date(holiday.startDate);
                const checkDate = new Date(formattedDate);

                // Set all to beginning of day for proper comparison
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999); // End of day
                checkDate.setHours(12, 0, 0, 0);

                return checkDate >= start && checkDate <= end;
                
            });
        }

        // Update the calendar with the latest data
        updateCalendar() {
            this.$calendar.fullCalendar("removeEvents");
            const events = this.generateEvents();
            const holidayEvents = this.generateHolidayEvents();
            this.$calendar.fullCalendar("addEventSource", events);
            this.$calendar.fullCalendar("addEventSource", holidayEvents);
        }

        // Generate calendar events from module data
        generateEvents() {
            const events = [];

            this.modules.forEach((module) => {
                const startDate = new Date(module.startDate);
                startDate.setHours(12, 0, 0, 0);

                // Module start event
                events.push({
                    id: "start_" + module.id,
                    title: `${module.name} - Starts`,
                    start: startDate,
                    allDay: true,
                    className: "module-start",
                    editable: true,
                    moduleId: module.id,
                    type: "module-start",
                });

                // Module exam date (if set)
                if (module.examDate) {
                    events.push({
                        id: "exam_" + module.id,
                        title: module.name + " - Exam",
                        start: new Date(module.examDate),
                        allDay: true,
                        className: "module-exam",
                        editable: true,
                        moduleId: module.id,
                        type: "module-exam",
                    });
                }

                // Generate weekly sessions
                const weeksNeeded = Math.ceil(
                    module.totalHours / module.weeklyHours
                );
                const weekDates = this.getWeekDates(module.id, weeksNeeded);

                for (let i = 0; i < weeksNeeded; i++) {
                    // Get hours for this week
                    let weekHours =
                        i < module.weeklyProgress.length &&
                        module.weeklyProgress[i] !== null
                            ? module.weeklyProgress[i]
                            : module.weeklyHours;

                    // Determine event class and color
                    let eventClass = "planned-session";
                    let eventColor = "#6c757d";

                    if (
                        i < module.weeklyProgress.length &&
                        module.weeklyProgress[i] !== null
                    ) {
                        if (module.weeklyProgress[i] > 0) {
                            eventClass = "progress";
                            eventColor = "#28a745";
                        } else if (module.weeklyProgress[i] === 0) {
                            eventClass = "absence";
                            eventColor = "#ffc107";
                        }
                    }

                    events.push({
                        id: "week_" + module.id + "_" + i,
                        title: `${module.name} - Week ${
                            i + 1
                        }: ${weekHours} hrs`,
                        start: weekDates[i],
                        allDay: true,
                        className: eventClass,
                        color: eventColor,
                        moduleId: module.id,
                        weekIndex: i,
                        type: "week",
                        editable: true,
                    });
                }
            });

            return events;
        }

        generateHolidayEvents() {
            return this.holidays.map(holiday => {
                // Set color and styling based on holiday type
                let backgroundColor, borderColor, textColor, title;

                switch(holiday.type) {
                    case 'vacance':
                        backgroundColor = 'rgba(255, 244, 245, 0.7)';
                        borderColor = '#ff8a93';
                        textColor = '#dd2c41';
                        title = `🏖️ ${holiday.name}`;
                        break;
                    case 'stage':
                        backgroundColor = 'rgba(232, 245, 233, 0.7)';
                        borderColor = '#81c784';
                        textColor = '#2e7d32';
                        title = `🏢 Stage: ${holiday.name}`;
                        if (holiday.affectedGroups && holiday.affectedGroups.length > 0) {
                            title += ` (${holiday.affectedGroups.join(', ')})`;
                        }
                        break;
                    case 'regional':
                        backgroundColor = 'rgba(227, 242, 253, 0.7)';
                        borderColor = '#64b5f6';
                        textColor = '#1565c0';
                        title = `📝 ${holiday.name}`;
                        if (holiday.additionalInfo) {
                            title += ` (${holiday.additionalInfo})`;
                        }
                        break;
                    default:
                        backgroundColor = 'rgba(255, 244, 245, 0.7)';
                        borderColor = '#ff8a93';
                        textColor = '#000';
                        title = holiday.name;
                }

                // For proper display in FullCalendar:
                // If the holiday is a single day event, we don't need to add one day to the end date
                // If it's a multi-day event, we use the actual end date as FullCalendar's end date is exclusive
                let endDateValue;

                if (holiday.endDate) {
                    // Use the holiday's end date directly without adding an extra day
                    endDateValue = holiday.endDate;
                    console.log(endDateValue);
                } else {
                    // For single-day holidays, use the start date as the end date as well
                    endDateValue = holiday.startDate;
                    console.log(endDateValue);

                }

                return {
                    id: 'holiday_' + holiday.id,
                    title: title,
                    start: holiday.startDate,
                    end: endDateValue,
                    allDay: true,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    textColor: textColor,
                    className: `holiday-event holiday-${holiday.type}`,
                    editable: false,
                    type: 'holiday',
                    extendedProps: {
                        holidayType: holiday.type,
                        additionalInfo: holiday.additionalInfo,
                        affectedGroups: holiday.affectedGroups
                    }
                };
            });
        }
        
        
        
        saveToDatabase() {
            const moduleData = this.prepareModulesForDatabase(); // Get modified data

            // Disable button and show loading spinner
            this.$saveBtn.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
            );
            this.$saveBtn.prop("disabled", true);

            // Convert moduleData to JSON and set it as the value of the hidden input
            const moduleDataJson = JSON.stringify(moduleData);
            $("#moduleDataInput").val(moduleDataJson);

            // Submit the form with AJAX
            $.ajax({
                url: "/save-calendar-data",
                type: "POST",
                data: {
                    moduleData: JSON.stringify(moduleData), // Send the module data
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: (response) => {
                    this.showSaveSuccess();
                    this.setUnsavedChanges(false);
                    this.$saveBtn.html(
                        '<i class="fas fa-save mr-1"></i> Save All Changes'
                    );
                    this.$saveBtn.prop("disabled", true);
                },
                error: (xhr, status, error) => {
                    console.error("Save error:", error);
                    console.error("Response:", xhr.responseText);
                    this.showSaveError(error);
                    this.$saveBtn.html(
                        '<i class="fas fa-save mr-1"></i> Save All Changes'
                    );
                    this.$saveBtn.prop("disabled", false);
                },
            });
        }

        // Show save success message
        showSaveSuccess() {
            const notification = $(`
                <div class="alert alert-success save-notification" role="alert">
                    <strong>Succès !</strong>Modifications enregistrées.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);

            $("#saveNotificationArea").html(notification);

            setTimeout(() => notification.alert("close"), 3000);
        }

        // Show save error message
        showSaveError(error) {
            const notification = $(`
                <div class="alert alert-danger save-notification" role="alert">
                    <strong>Erreur !</strong> Échec de l'enregistrement les modifications. Veuillez réessayer.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `);

            $("#saveNotificationArea").html(notification);
        }

        // Update unsaved changes status
        setUnsavedChanges(value) {
            this.hasUnsavedChanges = value;

            // Update save button state
            if (this.hasUnsavedChanges) {
                this.$saveBtn
                    .removeClass("btn-outline-primary")
                    .addClass("btn-primary")
                    .prop("disabled", false);

                if ($("#unsavedChangesAlert").length === 0) {
                    $("#saveNotificationArea").append(`
                        <div class="alert alert-warning" id="unsavedChangesAlert" role="alert">
                            <strong>Modifications non enregistrées !</strong> 
                        </div>
                    `);
                }
            } else {
                this.$saveBtn
                    .removeClass("btn-primary")
                    .addClass("btn-outline-primary")
                    .prop("disabled", true);
                $("#unsavedChangesAlert").remove();
            }
        }

        // Update module or exam date
        updateModuleDate(moduleId, dateType, newDate) {
            // First check if the new date falls on a holiday
            if (this.isHolidayDate(newDate)) {
                alert(
                    "Vous ne pouvez pas planifier d'événements pendant les périodes de congé."
                );
                return false;
            }

            const moduleIndex = this.modules.findIndex(
                (m) => m.id === moduleId
            );
            if (moduleIndex === -1) return false;

            if (dateType === "module-start") {
                // Keep existing weekly hours when updating via drag-drop
                const weeklyHours = this.modules[moduleIndex].weeklyHours;
                return this.updateModuleStartDate(
                    moduleId,
                    newDate,
                    weeklyHours
                );
            } else if (dateType === "module-exam") {
                // Add validation for exam date
                if (!this.validateExamDate(moduleId)) {
                    return false;
                }
                this.modules[moduleIndex].examDate = this.formatDate(newDate);
            }

            this.refreshUI(moduleId);

            // Update week options if this is the current module
            const currentModuleId = parseInt($("#moduleSelect").val());
            if (currentModuleId === moduleId) {
                $("#moduleSelect").trigger("change");
            }

            this.setUnsavedChanges(true);
            return true;
        }

        // Delete an exam date for a module
        deleteExamDate(moduleId) {
            const moduleIndex = this.modules.findIndex(
                (m) => m.id === moduleId
            );
            if (moduleIndex === -1) return false;

            // Set the exam date to null
            this.modules[moduleIndex].examDate = null;

            this.refreshUI(moduleId);

            // Update UI if this is the current module
            const currentModuleId = parseInt($("#moduleSelect").val());
            if (currentModuleId === moduleId) {
                $("#moduleSelect").trigger("change");
            }

            this.setUnsavedChanges(true);
            return true;
        }

        validateExamDate(moduleId) {
            const module = this.modules.find((m) => m.id === moduleId);
            if (!module) return false;

            // Calculate completion percentage
            const completionPercentage =
                (module.completedHours / module.totalHours) * 100;

            // Check if completion is at least 95%
            if (completionPercentage < 95) {
                alert(
                    `Vous ne pouvez pas programmer un examen avant d'avoir terminé au moins 95% du module. Progression actuelle: ${completionPercentage.toFixed(
                        1
                    )}%`
                );
                return false;
            }

            return true;
        }

        // Update progress session date
        updateProgressSessionDate(moduleId, weekIndex, newDate) {
            // First check if the new date falls on a holiday
            if (this.isHolidayDate(newDate)) {
                alert("You cannot schedule events during holiday periods.");
                return false;
            }

            const moduleIndex = this.modules.findIndex(
                (m) => m.id === moduleId
            );
            if (moduleIndex === -1) return false;

            // Validate that the date is not Sunday
            if (newDate.getDay() === 0) {
                alert(
                    "Progress sessions cannot be scheduled on Sundays. Please choose another day."
                );
                return false;
            }

            // Create the customSessionDates array if needed
            if (!this.modules[moduleIndex].customSessionDates) {
                this.modules[moduleIndex].customSessionDates = [];
            }

            // Ensure array has enough elements
            while (
                this.modules[moduleIndex].customSessionDates.length <= weekIndex
            ) {
                this.modules[moduleIndex].customSessionDates.push(null);
            }

            // Update only the specific week's custom date
            this.modules[moduleIndex].customSessionDates[weekIndex] =
                this.formatDate(newDate);

            // Only refresh calendar without re-calculating all sessions
            this.updateCalendar();

            // Update week selector if this is the current module
            if (parseInt($("#moduleSelect").val()) === moduleId) {
                this.updateWeekSelectOptions(moduleId);
            }

            this.setUnsavedChanges(true);
            return true;
        }

        // Create the dialog for adding events
        createAddEventDialog() {
            const self = this;

            if ($("#addEventModal").length === 0) {
                $("body").append(`
                    <div id="addEventModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ajouter un événement</h5>
                                    
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="eventTypeSelect">Type d'événement :</label>
                                        <select id="eventTypeSelect" class="form-control">
                                            <option value="module-start">Début du module</option>
                                            <option value="module-exam">Examen du module</option>
                                            <option value="session">Session</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="eventModuleSelect">Module:</label>
                                        <select id="eventModuleSelect" class="form-control">
                                            ${this.modules
                                                .map(
                                                    (m) =>
                                                        `<option value="${m.id}">${m.name}</option>`
                                                )
                                                .join("")}
                                        </select>
                                    </div>
                                    <div class="form-group" id="weeklyHoursGroup" style="display: none;">
                                        <label for="weeklyHoursInput">Heures hebdomadaires :</label>
                                        <input type="number" id="weeklyHoursInput" class="form-control" min="1" max="40" step="0.5">
                                    </div>
                                    <div class="form-group" id="weekNumberGroup" style="display: none;">
                                        <label for="eventWeekSelect">Numéro de semaine :</label>
                                        <select id="eventWeekSelect" class="form-control">
                                            <!-- Will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="eventDate">Date:</label>
                                        <input type="text" id="eventDate" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="saveEventBtn">Enregistrer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                // Add event listener for event type change
                $("#eventTypeSelect").on("change", function () {
                    const eventType = $(this).val();

                    // Show/hide weekly hours input for module-start
                    if (eventType === "module-start") {
                        $("#weeklyHoursGroup").show();
                        // Set default value from the selected module
                        const moduleId = parseInt(
                            $("#eventModuleSelect").val()
                        );
                        const module = self.modules.find(
                            (m) => m.id === moduleId
                        );
                        if (module) {
                            $("#weeklyHoursInput").val(module.weeklyHours);
                        }
                    } else {
                        $("#weeklyHoursGroup").hide();
                    }

                    // Show/hide week number for session
                    if (eventType === "session") {
                        $("#weekNumberGroup").show();
                        // Update week options based on selected module
                        self.updateEventWeekOptions();
                    } else {
                        $("#weekNumberGroup").hide();
                    }
                });

                // Add event listener for module change in event dialog
                $("#eventModuleSelect").on("change", function () {
                    const eventType = $("#eventTypeSelect").val();

                    // Update weekly hours input with the selected module's value
                    if (eventType === "module-start") {
                        const moduleId = parseInt($(this).val());
                        const module = self.modules.find(
                            (m) => m.id === moduleId
                        );
                        if (module) {
                            $("#weeklyHoursInput").val(module.weeklyHours);
                        }
                    }

                    if (eventType === "session") {
                        self.updateEventWeekOptions();
                    }
                });

                // Add event listener for save button
                $("#saveEventBtn").on("click", function () {
                    const moduleId = parseInt($("#eventModuleSelect").val());
                    const eventType = $("#eventTypeSelect").val();
                    const eventDate = new Date($("#eventDate").val());
                    let updated = false;

                    if (eventType === "module-start") {
                        // Get the weekly hours value
                        const weeklyHours = parseFloat(
                            $("#weeklyHoursInput").val()
                        );
                        updated = self.updateModuleStartDate(
                            moduleId,
                            eventDate,
                            weeklyHours
                        );
                    } else if (eventType === "session") {
                        const weekIndex = parseInt($("#eventWeekSelect").val());
                        updated = self.updateProgressSessionDate(
                            moduleId,
                            weekIndex,
                            eventDate
                        );
                    } else if (eventType === "module-exam") {
                        // Add validation for exam date
                        const module = self.modules.find(
                            (m) => m.id === moduleId
                        );
                        if (module) {
                            const completionPercentage =
                                (module.completedHours / module.totalHours) *
                                100;
                            if (completionPercentage < 95) {
                                alert(
                                    `Vous ne pouvez pas programmer un examen avant d'avoir terminé au moins 95% du module. Progression actuelle: ${completionPercentage.toFixed(
                                        1
                                    )}%`
                                );
                                return;
                            }
                        }
                        updated = self.updateModuleDate(
                            moduleId,
                            eventType,
                            eventDate
                        );
                    }

                    if (updated) {
                        $("#addEventModal").modal("hide");
                        self.setUnsavedChanges(true);
                    }
                });
            }
        }

        // Update module start date and weekly hours
        updateModuleStartDate(moduleId, newDate, weeklyHours) {
            // First check if the new date falls on a holiday
            if (this.isHolidayDate(newDate)) {
                alert("You cannot schedule events during holiday periods.");
                return false;
            }

            const moduleIndex = this.modules.findIndex(
                (m) => m.id === moduleId
            );
            if (moduleIndex === -1) return false;

            // Update both startDate and weeklyHours
            this.modules[moduleIndex].startDate = this.formatDate(newDate);

            // Only update weeklyHours if it's a valid number
            if (!isNaN(weeklyHours) && weeklyHours > 0) {
                this.modules[moduleIndex].weeklyHours = weeklyHours;
            }

            // Clear custom session dates so they'll be recalculated based on new start date
            this.modules[moduleIndex].customSessionDates = [];

            this.refreshUI(moduleId);

            // Update week options if this is the current module
            const currentModuleId = parseInt($("#moduleSelect").val());
            if (currentModuleId === moduleId) {
                $("#moduleSelect").trigger("change");
            }

            this.setUnsavedChanges(true);
            return true;
        }

        updateEventWeekOptions() {
            const moduleId = parseInt($("#eventModuleSelect").val());
            const module = this.modules.find((m) => m.id === moduleId);

            if (!module) return;

            const weeksNeeded = Math.ceil(
                module.totalHours / module.weeklyHours
            );
            const weekDates = this.getWeekDates(moduleId, weeksNeeded);

            const options = weekDates
                .map(
                    (date, i) =>
                        `<option value="${i}">Week ${i + 1} - ${this.formatDate(
                            date,
                            "short"
                        )}</option>`
                )
                .join("");

            $("#eventWeekSelect").html(options);
        }

        // Create "Save All Changes" button
        createSaveButton() {
            const self = this;

            $(`
                <div class="mt-4 card mb-4" id="saveChangesCard">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center">
                            <div id="saveNotificationArea"></div>
                            <button id="saveAllChangesBtn" class="btn btn-outline-primary" disabled>
                                <i class="fas fa-save mr-1"></i> Enregistrer toutes les modifications
                            </button>
                            
                        </div>
                    </div>
                </div>
            `).insertAfter("#calendar");

            // Update cached reference
            this.$saveBtn = $("#saveAllChangesBtn");

            // Add event listener
            this.$saveBtn.on("click", function () {
                if (self.hasUnsavedChanges) {
                    $(this).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                    );
                    $(this).prop("disabled", true);
                    self.saveToDatabase();
                }
            });
        }

        // Setup warning for unsaved changes
        setupUnsavedChangesWarning() {
            const self = this;

            $(window).on("beforeunload", function () {
                if (self.hasUnsavedChanges) {
                    return "Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter sans enregistrer ?";
                }
            });
        }

        // Initialize the calendar
        initCalendar() {
            const self = this;

            this.$calendar.fullCalendar({
                header: {
                    left: "prev,next today",
                    center: "title",
                    right: "month",
                },
                defaultView: "month",
                editable: true,
                selectable: true,
                selectHelper: true,
                firstDay: 1,
                hiddenDays: [0],

                // Add this to the select function in initCalendar()
                select: function (start, end) {
                    const isHoliday = self.isHolidayDate(start);

                    // Prevent adding events on holiday dates
                    if (isHoliday) {
                        alert(
                            "Vous ne pouvez pas planifier d'événements pendant les périodes de congé."
                        );
                        return;
                    } else {
                        $("#eventDate").val(self.formatDate(start));

                        // Reset the form
                        $("#eventTypeSelect")
                            .val("module-start")
                            .trigger("change");

                        // Show modal
                        $("#addEventModal").modal("show");
                    }
                },

                // Handle event drops (dragging)
                eventDrop: function (event, delta, revertFunc) {
                    // Don't allow moving holiday events
                    if (event.type === "holiday") {
                        revertFunc();
                        return;
                    }

                    // Check if new date is a holiday
                    const isHoliday = self.isHolidayDate(event.start);
                    if (isHoliday) {
                        // Show message and revert the action
                        alert(
                            "You cannot schedule events during holiday periods."
                        );
                        revertFunc();
                        return;
                    }

                    let updated = false;

                    if (event.type === "module-start") {
                        // Module start events affect all related sessions
                        updated = self.updateModuleStartDate(
                            event.moduleId,
                            event.start,
                            self.modules.find((m) => m.id === event.moduleId)
                                .weeklyHours
                        );
                    } else if (event.type === "module-exam") {
                        // Exam date events move independently
                        updated = self.updateModuleDate(
                            event.moduleId,
                            event.type,
                            event.start
                        );
                    } else if (event.type === "week") {
                        // Week sessions move independently
                        updated = self.updateProgressSessionDate(
                            event.moduleId,
                            event.weekIndex,
                            event.start
                        );
                    }

                    if (!updated) {
                        revertFunc();
                    }
                },

                // Handle clicking on an event
                eventClick: function (calEvent, jsEvent, view) {
                    if (calEvent.type === "week") {
                        $("#moduleSelect")
                            .val(calEvent.moduleId)
                            .trigger("change");
                        $("#weekSelect")
                            .val(calEvent.weekIndex)
                            .trigger("change");

                        $("html, body").animate(
                            {
                                scrollTop:
                                    $("#weeklyUpdateContainer").offset().top -
                                    50,
                            },
                            200
                        );

                        $("#hoursCompleted").focus().select();
                    } else if (calEvent.type === "module-exam") {
                        // Show confirmation dialog for exam deletion
                        const moduleName =
                            self.modules.find((m) => m.id === calEvent.moduleId)
                                ?.name || "this module";
                        if (
                            confirm(
                                "Êtes-vous sûr de vouloir supprimer la date d'examen pour ${moduleName} ?"
                            )
                        ) {
                            const deleted = self.deleteExamDate(
                                calEvent.moduleId
                            );
                            if (deleted) {
                                self.setUnsavedChanges(true);
                            }
                        }
                    }
                },

                events: [],
            });
        }
    }

    // Initialize the app
    const calendarApp = new CalendarApp();
});
