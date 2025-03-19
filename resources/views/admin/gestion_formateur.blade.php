@extends("admin.bar")

@section("main")
    @if (session('import_success'))
        <script>
            alert('{{ session('import_success') }}');
        </script>
    @endif

    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

    <div>
        <h1 class="text-success m-3">Gestion Des Formateurs:</h1>
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
                    <form action="{{ route('import_file') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="data" class="form-label fw-bold">Import Fichier Excel:</label>
                        <input type="file" name="data" id="data" class="form-control btn btn-primary" style="padding-bottom: 2.25rem;">
                        <button type="submit" class="btn btn-primary m-3">Import</button>
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-bordered mt-5">
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            @foreach ($formateurs as $formateur)
                <tbody>
                    <td>{{ $formateur->id }}</td>
                    <td>{{ $formateur->name }}</td>
                    <td>{{ $formateur->email }}</td>

                    <td>
                        <a href="{{ route('formateurs.edit', $formateur->id) }}" class="text-danger">Edit</a>
                        <a href="{{ route('teacher.progress', $formateur->id) }}" class="text-primary ml-2">Suivre</a>
                       
                        <!-- Suppression -->
                        <form action="{{ route('formateurs.destroy', $formateur->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-dark text-secondary ml-2">Supprimer</button>
                        </form>
                    </td>
                </tbody>
            @endforeach
        </table>
        <div>{{ $formateurs->links('pagination::bootstrap-4') }}</div>

    </div>
@endsection
