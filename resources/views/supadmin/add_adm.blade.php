@extends("supadmin.bar")

@section("main")
    <div class="container">
        <h1 class="text-success my-3">Ajouter Nouveau Administrateur:</h1>

        <div class="card shadow p-4 mt-5">
            <form action="{{ route('add_admin') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="etablissement" class="form-label">Etablissement</label>
                    <input type="text" class="form-control" id="etablissement" name="etablissement" required>
                </div>




                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
@endsection
