$(document).ready(function () {
    var holidays = [];
    var groups = [];
    var filieres = [];

    function fetchHolidays() {
        $.ajax({
            url: '/fetch-vacations',
            method: 'GET',
            success: function (response) {
                // Store groups and filieres for later use
                groups = response.groupes.map(function(group) {
                    return {
                        name: group.name,
                        id: group.id_group,
                    };
                });

                filieres = response.filieres.map(function(filiere) {
                    return {
                        name: filiere.name,
                        id: filiere.id_fillier,
                    };
                });

                // Map holidays and include group/filiere names
                holidays = response.vacations.map(function (vacation) {
                    let extraInfo = '';

                    // If the vacation has a group ID, find the corresponding group name
                    if (vacation.id_group) {
                        let group = groups.find(g => g.id === vacation.id_group);
                        extraInfo = group ? group.name : '';
                    }
                    // If the vacation has a filiere ID, find the corresponding filiere name
                    else if (vacation.id_fillier) {
                        let filiere = filieres.find(f => f.id === vacation.id_fillier);
                        extraInfo = filiere ? filiere.name : '';
                    }

                    return {
                        id: vacation.id,
                        type: vacation.type,
                        startDate: vacation.date_debut,
                        endDate: vacation.date_fin || vacation.date_debut,
                        description: vacation.description_vacance,
                        extraInfo: extraInfo // This now contains the group/filiere name
                    };
                });

                // Update the calendar with the new data
                updateCalendar();
            },
            error: function (error) {
                console.error('Error fetching holidays:', error);
            }
        });
    }

    function updateDynamicField(eventType) {
        var fieldHTML = '';
        if (eventType === 'stage') {
            fieldHTML = `
                <label for="groupSelect">Select Group:</label>
                <select id="groupSelect" name="groupSelect" class="form-control">
                    ${groups.map(group => `<option value="${group.id}">${group.name}</option>`).join('')}
                </select>
            `;
        } else if (eventType === 'regional') {
            fieldHTML = `
                <label for="filiereSelect">Select Filiere:</label>
                <select id="filiereSelect" name="filiereSelect" class="form-control">
                    ${filieres.map(filiere => `<option value="${filiere.id}">${filiere.name}</option>`).join('')}
                </select>
            `;
        }
        $('#dynamicField').html(fieldHTML);
    }

    function updateCalendar() {
        $('#calendar').fullCalendar('removeEvents');
        var events = holidays.map((holiday) => {
            let endDate = new Date(holiday.endDate);
            endDate.setDate(endDate.getDate());

            return {
                id: holiday.id,
                title: `${holiday.type.toUpperCase()}: ${holiday.description} ${holiday.extraInfo ? '(' + holiday.extraInfo + ')' : ''}`,
                start: holiday.startDate,
                end: holiday.startDate === holiday.endDate ? null : endDate,
                allDay: true,
                color: holiday.type === 'vacance' ? '#ff0000' : holiday.type === 'stage' ? '#007bff' : '#28a745',
                editable: false
            };
        });
        $('#calendar').fullCalendar('addEventSource', events);
    }

    $('#eventType').on('change', function () {
        updateDynamicField($(this).val());
    });

    $('#holidayFormContainer').on('submit', function () {
        setTimeout(fetchHolidays, 1000);
    });

    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,listYear'
        },
        defaultView: 'month',
        editable: false,
        selectable: false,
        firstDay: 1,
        hiddenDays: [0],
        events: [],
        eventClick: function (event) {
            if (confirm(`Do you want to delete this event: ${event.title}?`)) {
                $.ajax({
                    url: '/delete-vacation/' + event.id,
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function () {
                        fetchHolidays();
                    },
                    error: function (error) {
                        console.error('Error deleting event:', error);
                    }
                });
            }
        }
    });

    fetchHolidays();
});