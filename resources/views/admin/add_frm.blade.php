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
                    <label for="groupe" class="form-label">groupe</label>
                    <input type="text" class="form-control" id="groupe" name="groupe" required>
                </div>
                <div class="mb-3">
                    <label for="module" class="form-label">module</label>
                    <input type="text" class="form-control" id="module" name="module" required>
                </div>
                <div class="mb-3">
                    <label for="type de seances" >presentiel</label>
                    <input type="radio" class="form-control" id="type_seances" name="type_seances" value="presentiel" required>
                    <label for="type de seances">distanciel</label>
                    <input type="radio" class="form-control" id="type_seances" name="type_seances" value="distanciel" required>
                </div>




                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
@endsection
