<x-bar :navlinks="[
    ['label'=>'Gestion Formateurs','route'=>'../gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'../gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'../add_formateur','class'=>'','icon'=>'fas fa-user-plus'],
]">
    <div class="container">
        <h2 class="text-success">Modifier Formateur</h2>

        <form action="{{ route('formateurs.update', $formateur->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nom:</label>
                <input type="text" name="name" class="form-control" value="{{ $formateur->name }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Login:</label>
                <input type="text" name="email" class="form-control" value="{{ $formateur->email }}" required>
                @error('email')
                        <p class="text-danger ml-3">Ce login est déjà utilisé par un autre utilisateur</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Mettre à jour</button>
        </form>
    </div>
</x-bar>
