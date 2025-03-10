@extends("supadmin.bar")

@section("main")
    <div class="container">
        @if (session('delete_success'))
            <script>
                alert("{{ session('delete_success') }}");
            </script>
        @endif
        <h1 class="text-success my-3">Gestion Des Administrateurs:</h1>
        
        <div class="d-flex justify-content-end m-4">
            <a href="/add_admin" class="btn btn-primary">Ajouter Administrateur</a>
        </div>

        
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Nom</th>
                        <th>Etablissement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($administrateurs as $administrateur)
                        <tr>
                            <td>{{ $administrateur->id }}</td>
                            <td>{{ $administrateur->name }}</td>
                            <td>{{ $administrateur->etablissement }}</td>
                            <td class="d-flex justify-content-center">
                                <a href="{{ route('edit_adm',$administrateur) }}" class="btn btn-success btn-md">Edit</a>
                                <form action="{{ route('delete_admin', $administrateur->id) }}" method="post" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-md" onclick="return confirm('vous voulez supprimer cet administrateur')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>{{ $administrateurs->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
@endsection



