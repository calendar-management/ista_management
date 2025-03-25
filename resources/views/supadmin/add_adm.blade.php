<x-bar :navlinks="[
    ['label'=>'Gestion Admins','route'=>'gestion_adm','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'add_admin','class'=>'active','icon'=>'fas fa-user-plus'],
]">
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
                    <label for="email" class="form-label">Login</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                    @error('email')
                        <p class="text-danger m-2">Ce login est déjà utilisé. Essayez un autre</p>
                    @enderror
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
</x-bar>