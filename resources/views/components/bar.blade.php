@props([
    "navlinks"
])

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

    <link href='assets/css/fullcalendar.css' rel='stylesheet' />
    <link href='assets/css/fullcalendar.print.css' rel='stylesheet' media='print' />
    <script src='assets/js/jquery-1.10.2.js' type="text/javascript"></script>
    <script src='assets/js/jquery-ui.custom.min.js' type="text/javascript"></script>
    <script src='assets/js/fullcalendar.js' type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    @vite('resources/js/vacances.js')
    <style>
        .logout{
            background-color: transparent;
            width: 100%;
            border: none;
            text-align: start;
        }

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
        
        #sidebar ul li a, .logout {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: white;
            text-decoration: none;
        }
        
        #sidebar ul li a:hover, .logout:hover {
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


<style>
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
        color: white;
    }

    .cal-scroll {
        width: 100%;
        overflow-x: auto;
        /* Enables horizontal scrolling */
        white-space: nowrap;
        display: flex;
        flex-direction: column
    }
</style>
</head>
<body>
    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>{{(auth()->user()->role)}}</h3>
            </div>
            
            <ul class="list-unstyled components">
                @foreach ($navlinks as $navlink)
                    <li class="{{$navlink['class']}}">
                        <a href="{{$navlink['route']}}"><i class="{{$navlink['icon']}} me-2"></i>{{$navlink['label']}}</a>
                    </li>  
                @endforeach  
                <li>
                    <a href="/profile"><i class="fas fa-user me-2"></i>Profil</a>
                </li>  
                <li>
                    <form action="{{route('logout')}}" method="post">
                        @csrf
                        <button class="logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</button>
                    </form>
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
                                <li><a class="dropdown-item" href="/profile">Profil</a></li>
                                <li>
                                    <form action="{{route('logout')}}" method="POST">
                                        @csrf
                                        <button class="dropdown-item" href="{{route('logout')}}">Déconnexion</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div id="container-fluid ">
                {{$slot}}
            </div>
        </div>

    </div>

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