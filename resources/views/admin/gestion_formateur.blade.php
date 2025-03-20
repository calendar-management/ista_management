@extends('admin.bar')

@section('main')
    @if (session('import_success'))
        <script>
            alert('{{ session('import_success') }}');
        </script>
    @endif
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="text-success m-3">Gestion Des Formateurs:</h1>
            <a href="{{ url('/download/AvancementProgramme_exemple.xlsx') }}" class="btn btn-primary">Télécharger un exemple
                de fichier</a>

        </div>
        <div class="border p-4 m-4 rounded shadow bg-light">
            <div class="row align-items-center">
                <div class="col-md-5 text-center text-md-start">
                    <a href="/add_formateur" class="btn btn-primary w-100">Ajouter Formateur</a>
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
                        <button type="submit" id="importButton" class="btn btn-primary m-3">Import</button>
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

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                            importButton.innerHTML = 'Importation en cours...';

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

                                    // Redirect after success (optional)
                                    setTimeout(function() {
                                        window.location.href = window.location.href; // Refresh the page
                                    }, 1500);
                                } else {
                                    progressBar.classList.remove('progress-bar-animated');
                                    progressBar.classList.add('bg-danger');
                                    statusMessage.textContent =
                                        'Erreur lors de l\'importation. Veuillez réessayer.';
                                    importButton.disabled = false;
                                    importButton.innerHTML = 'Import';
                                }
                            });

                            // Set up error listener
                            xhr.addEventListener('error', function() {
                                progressBar.classList.remove('progress-bar-animated');
                                progressBar.classList.add('bg-danger');
                                statusMessage.textContent = 'Erreur réseau. Veuillez réessayer.';
                                importButton.disabled = false;
                                importButton.innerHTML = 'Import';
                            });

                            // Open and send the request
                            xhr.open('POST', form.action, true);
                            xhr.send(formData);

                            // Start a fake progress indicator for backend processing
                            // This is just to give user feedback during the processing stage
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
            </div>
        </div>

        <div class="mb-3 search">
            <form action="{{ route('formateurs.search') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un formateur..."
                        value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table-bordered mt-5">
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>email</th>
                <th>Actions</th>
            </tr>
            @foreach ($formateurs as $formateur)
                <tbody>
                    <td>{{ $formateur->matricule }}</td>
                    <td>{{ $formateur->name }}</td>
                    <td>{{ $formateur->email }}</td>

                    <td>
                        <a href="/edit_formateur" class="text-danger">Edit</a>
                        <a href="" class="text-primary ml-2">Suivie</a>
                    </td>
                </tbody>
            @endforeach
        </table>
        <div>{{ $formateurs->links('pagination::bootstrap-4') }}</div>

    </div>
@endsection
