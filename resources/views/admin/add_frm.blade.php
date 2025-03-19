@extends("admin.bar")

@section("main")
@if (session("add_frm_success"))
    <script>
        alert("{{ session("add_frm_success") }}")
    </script>
@endif
    <div class="container">
        <h1 class="text-success my-3">Ajouter Nouveau Formateur:</h1>
        

        <div class="card shadow p-4 mt-5">
            <form action="{{ route('add_formateur') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="matricule" class="form-label">Matricule</label>
                    <input type="text" class="form-control" id="matricule" name="matricule" required>
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Login</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
@endsection