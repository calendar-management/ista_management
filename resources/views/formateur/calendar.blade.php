<x-bar :navlinks="[
    ['label' => 'Calendrier', 'route' => 'formateur_calendar', 'class' => 'active', 'icon' => 'fas fa-calendar-alt'],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => '', 'icon' => 'fas fa-chart-bar'],
]">
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
    @vite('resources/js/calendar.js')
    @vite('resources/js/app.js')

    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script>
        const data = JSON.parse('{!! json_encode($modules, JSON_HEX_APOS) !!}');
        const holidays = JSON.parse('{!! json_encode($holidays) !!}');
    </script>

    <div class="container mt-12 pt-10">
        <div class="call">
            <div class="cal-scroll col-md-12">
                <div id='calendar'></div>
                <div style='clear:both'></div>

            </div>
            <div id="call"></div>
            <hr>
            <div class="col-md-12">
                <div id="weeklyUpdateContainer"></div>
            </div>
            <br>
            <form id="saveChangesForm" style="display: none;">
                @csrf
                <input type="hidden" name="moduleData" id="moduleDataInput">
            </form>
        </div>
    </div>

    <!-- Add these modal structures at the end of your calendar.blade.php file -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Content will be dynamically inserted here -->
        </div>
    </div>
</div>

<div class="modal fade" id="addSessionModal" tabindex="-1" role="dialog" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Content will be dynamically inserted here -->
        </div>
    </div>
</div>
</x-bar>
