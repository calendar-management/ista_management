<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Sidebar styles */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        #sidebar.active {
            margin-left: -250px;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: #212529;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: white;
            text-decoration: none;
        }
        
        #sidebar ul li a:hover {
            background: #495057;
        }
        
        #sidebar ul li.active > a {
            background: #007bff;
        }
        
        #content {
            width: calc(100% - 250px);
            margin-left: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        #content.active {
            width: 100%;
            margin-left: 0;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            
            #sidebar.active {
                margin-left: 0;
            }
            
            #content {
                width: 100%;
                margin-left: 0;
            }
            
            #content.active {
                width: calc(100% - 250px);
                margin-left: 250px;
            }
        }
        
        .overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 998;
            opacity: 0;
            transition: all 0.5s ease-in-out;
        }
        
        .overlay.active {
            display: block;
            opacity: 1;
        }
        
        .search {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Formation Panel</h3>
            </div>
            
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#"><i class="fas fa-users me-2"></i>Formateurs</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-graduation-cap me-2"></i>Étudiants</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-book me-2"></i>Cours</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-calendar-alt me-2"></i>Planning</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-chart-bar me-2"></i>Statistiques</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-cog me-2"></i>Paramètres</a>
                </li>
            </ul>
        </nav>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>{{auth()->user()->name}}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="#">Profil</a></li>
                                <li><a class="dropdown-item" href="#">Paramètres</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Déconnexion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
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
    </div>
</body>
</html>
@section('scripts')
<script>
    // JavaScript to handle conditionally showing error message for required fields
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');
        
        modals.forEach(modal => {
            const form = modal.querySelector('form');
            const hoursInput = modal.querySelector('input[name="hours_completed"]');
            
            form.addEventListener('submit', function(e) {
                if (hoursInput.value === '' || parseFloat(hoursInput.value) < 0) {
                    e.preventDefault();
                    alert('Veuillez entrer un nombre valide d\'heures complétées');
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const overlay = document.querySelector('.overlay');
        
        // Toggle sidebar
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        // Close sidebar when clicking on overlay (mobile)
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            content.classList.remove('active');
            overlay.classList.remove('active');
        });
        
        // Import form functionality
        const form = document.getElementById('importForm');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const statusMessage = document.getElementById('statusMessage');
        const importButton = document.getElementById('importButton');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show progress container
            progressContainer.style.display = 'block';

            // Disable submit button
            importButton.disabled = true;
            importButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importation en cours...';

            // Prepare form data
            const formData = new FormData(form);

            // Create AJAX request
            const xhr = new XMLHttpRequest();

            // Set up progress listener
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressBar.textContent = percentComplete + '%';
                    progressBar.setAttribute('aria-valuenow', percentComplete);

                    if (percentComplete === 100) {
                        statusMessage.textContent =
                            'Fichier téléchargé, traitement des données en cours...';
                    }
                }
            });

            // Set up completion listener
            xhr.addEventListener('load', function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    statusMessage.textContent = 'Importation réussie!';

                    // Redirect after success
                    setTimeout(function() {
                        window.location.href = window.location.href; // Refresh the page
                    }, 1500);
                } else {
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-danger');
                    statusMessage.textContent =
                        'Erreur lors de l\'importation. Veuillez réessayer.';
                    importButton.disabled = false;
                    importButton.innerHTML = '<i class="fas fa-file-import me-2"></i>Import';
                }
            });

            // Set up error listener
            xhr.addEventListener('error', function() {
                progressBar.classList.remove('progress-bar-animated');
                progressBar.classList.add('bg-danger');
                statusMessage.textContent = 'Erreur réseau. Veuillez réessayer.';
                importButton.disabled = false;
                importButton.innerHTML = '<i class="fas fa-file-import me-2"></i>Import';
            });

            // Open and send the request
            xhr.open('POST', form.action, true);
            xhr.send(formData);

            // Start a fake progress indicator for backend processing
            let fakeProgress = 0;
            const processingInterval = setInterval(function() {
                if (fakeProgress >= 95) {
                    clearInterval(processingInterval);
                } else if (fakeProgress >= 70) {
                    fakeProgress += 0.5;
                    updateFakeProgress();
                } else {
                    fakeProgress += 1;
                    updateFakeProgress();
                }
            }, 1000);

            function updateFakeProgress() {
                if (progressBar.getAttribute('aria-valuenow') === '100') {
                    progressBar.style.width = fakeProgress + '%';
                    progressBar.textContent = 'Traitement: ' + Math.round(fakeProgress) + '%';
                }
            }
        });
    });
</script>