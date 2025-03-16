@extends("admin.bar")

@section("main")
    <h2 class="text-success">Modifier Formateur</h2>

    <form action="{{ route('formateurs.update', $formateur->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nom:</label>
            <input type="text" name="name" class="form-control" value="{{ $formateur->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="text" name="email" class="form-control" value="{{ $formateur->email }}" required>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
    
@endsection
