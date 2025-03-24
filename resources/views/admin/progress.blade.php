<x-bar :navlinks="[
    ['label'=>'Gestion Formateurs','route'=>'../gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'../gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'../add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
]">
            <div class="container-fluid py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-success">Suivi d'avancement du formateur: {{ $teacher->name }}</h1>
                    <a href="{{ route('export.weekly_progress', ['id' => $teacher->id]) }}" class="btn btn-primary">Export Weekly Progress</a>
                </div>
            
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            
                <!-- Progress Overview Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Modules</h5>
                                <h2>{{ count($modules) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Modules à jour</h5>
                                <h2>{{ count(array_filter($modules, function($m) { return $m['status'] === 'À jour'; })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Modules en retard</h5>
                                <h2>{{ count(array_filter($modules, function($m) { return $m['status'] === 'En retard'; })) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Progression moyenne</h5>
                                <h2>
                                    {{ count($modules) > 0 ? 
                                        round(array_sum(array_column($modules, 'completion_percentage')) / count($modules), 1) : 
                                        0 }}%
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Modules Progress Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="card-title">Détails d'avancement par module</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Module</th>
                                        <th>Groupe</th>
                                        <th>Filière</th>
                                        <th>Type</th>
                                        <th>Progression</th>
                                        <th>Heures (Faites/Total)</th>
                                        <th>Date début</th>
                                        <th>Date Examen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($modules as $module)
                                    <tr>
                                        <td>
                                            <strong>{{ $module['module_code'] }}</strong>
                                            <div class="small">{{ $module['module_name'] }}</div>
                                        </td>
                                        <td>{{ $module['group_name'] }}</td>
                                        <td>{{ $module['fillier_name'] }}</td>
                                        <td>{{ $module['type_seance']==='totale'?"distanciel/presentiel":$module['type_seance'] }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;background-color:rgb(160, 151, 151);">
                                                <div class="progress-bar 
                                                    @if($module['status'] == 'En retard') bg-danger 
                                                    @elseif($module['status'] == 'Terminé') bg-success 
                                                    @else bg-primary @endif" 
                                                    role="progressbar" 
                                                    style="width: {{ $module['completion_percentage'] }}%;"
                                                    aria-valuenow="{{ $module['completion_percentage'] }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <label for="" class="">
                                                {{ $module['completion_percentage'] }}%
                                            </label>
                                        </td>
                                        <td>{{ $module['completed_hours'] }} / {{ $module['total_hours'] }} h</td>
                                        <td>{{ $module['start_date'] }}</td>
                                        <td>{{ $module['exam_date'] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Aucun module assigné à ce formateur</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-bar>