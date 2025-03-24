<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Des Formateurs</title>
    <!-- Bootstrap CSS -->
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
    <div class="wrapper">
        <!-- Sidebar -->
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
        
        <!-- Page Content -->
        <div id="content">
            <!-- Toggle button -->
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
            
            <!-- Main content -->
            <div class="container-fluid">
                @if (session('import_success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('import_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h1 class="text-success m-3">Gestion Des Formateurs:</h1>
                        <a href="{{ url('/download/AvancementProgramme_exemple.xlsx') }}" class="btn btn-primary m-3">
                            <i class="fas fa-download me-2"></i>Télécharger un exemple de fichier
                        </a>
                    </div>
                    
                    <div class="border p-4 m-2 m-md-4 rounded shadow bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-5 text-center text-md-start mb-3 mb-md-0">
                                <a href="/add_formateur" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Ajouter Formateur
                                </a>
                            </div>

                            <div class="col-12 d-md-none my-3 border-bottom"></div>

                            <div class="col-md-1 d-none d-md-block">
                                <div class="border-start h-100"></div>
                            </div>

                            <div class="col-md-6 text-center text-md-start">
                                <form id="importForm" action="{{ route('import_file') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label for="data" class="form-label fw-bold">Import Fichier Excel:</label>
                                    <input type="file" name="data" id="data" class="form-control btn btn-primary"
                                        style="padding-bottom: 2.25rem;">
                                    <button type="submit" id="importButton" class="btn btn-primary m-3">
                                        <i class="fas fa-file-import me-2"></i>Import
                                    </button>
                                </form>

                                <!-- Progress bar container (hidden by default) -->
                                <div id="progressContainer" class="mt-3" style="display: none;">
                                    <div class="progress" style="height: 25px;">
                                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                            style="width: 0%">0%</div>
                                    </div>
                                    <p id="statusMessage" class="mt-2">Préparation du fichier...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 search">
                        <form action="{{ route('formateurs.search') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher un formateur..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formateurs as $formateur)
                                    <tr>
                                        <td>{{ $formateur->matricule }}</td>
                                        <td>{{ $formateur->name }}</td>
                                        <td>{{ $formateur->email }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{route('edit_formateur',$formateur->id)}}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('teacher.progress', $formateur->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-line"></i> Suivre
                                                </a>
                                                <!-- Suppression -->
                                                <form action="{{ route('formateurs.destroy', $formateur->id) }}" method="POST" class="d-inline" 
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $formateurs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dark overlay when sidebar is active (mobile) -->
        <div class="overlay"></div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
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
</body>
</html>