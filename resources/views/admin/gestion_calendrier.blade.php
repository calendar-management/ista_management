<x-bar :navlinks="[
    ['label' => 'Gestion Formateurs', 'route' => 'gestion_formateur', 'class' => '', 'icon' => 'fas fa-users'],
    [
        'label' => 'Gestion Calendrier',
        'route' => 'gestion_calendrier',
        'class' => 'active',
        'icon' => 'fas fa-calendar-alt',
    ],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => '', 'icon' => 'fas fa-chart-bar'],
    ['label' => 'Ajouter Formateur', 'route' => 'add_formateur', 'class' => '', 'icon' => 'fas fa-user-plus'],
]">

    <style>
        .checkbox-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }

        .form-check {
            margin-bottom: 8px;
        }
    </style>

    @vite('resources/js/vacances.js')
    @if (session('ajouter_succ'))
        <script>
            alert("{{ session('ajouter_succ') }}")
        </script>
    @endif
    <div class="text-center">
        <h1 class="text-success m-3">Gestion Des Vacances:</h1>

        <div class="container">
            <div class="call">
                <div class="cal-scroll col-md-12">
                    <div id='calendar'></div>
                    <div style='clear:both'></div>

                </div>
                <form action="{{ route('add_vacances') }}" method="POST" id="holidayFormContainer">
                    @csrf
                    <div class="add-holiday-form p-3 border rounded">
                        <h4>Ajouter Un Nouvel Evénement</h4>
                        <div class="form-group">
                            <label for="eventType">Select Type:</label>
                            <select id="eventType" name="eventType" class="form-control">
                                <option value="vacance">Vacance</option>
                                <option value="stage">Stage</option>
                                <option value="regional">Regional</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="holidayStartDate">Date De Début:</label>
                            <input type="date" id="holidayStartDate" name="holidayStartDate" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="holidayEndDate">Date De Fin:</label>
                            <input type="date" id="holidayEndDate" name="holidayEndDate" class="form-control">
                        </div>
                        <div class="form-group" id="dynamicField"></div>
                        <div class="form-group">
                            <label for="holidayDescription">Description:</label>
                            <input type="text" id="holidayDescription" name="holidayDescription" class="form-control"
                                placeholder="Enter event description">
                        </div>
                        <button type="submit" id="addHolidayBtn" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>




            </div>
        </div>
    </div>
</x-bar>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM fully loaded");
        // Test if jQuery is working
        if (typeof $ === 'function') {
            console.log("jQuery is working");
        } else {
            console.log("jQuery is NOT working");
        }
    });
</script>
