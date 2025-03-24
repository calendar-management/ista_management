<x-bar :navlinks="[
    ['label'=>'Gestion Formateurs','route'=>'gestion_formateur','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Gestion Calendrier','route'=>'gestion_calendrier','class'=>'','icon'=>'fas fa-calendar-alt'],
    ['label'=>'Dashboard','route'=>'dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Formateur','route'=>'add_formateur','class'=>'active','icon'=>'fas fa-user-plus'],
]">
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
                    <input type="text" class="form-control" value="{{old('name')}}" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="matricule" class="form-label">Matricule</label>
                    <input type="text" class="form-control" value="{{old('matricule')}}" id="matricule" name="matricule" required>
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Login</label>
                    <input type="text" class="form-control" value="{{old('email')}}" id="email" name="email" required>
                    @error('email')
                        <p class="text-danger ml-3">Ce login est déjà utilisé par un autre utilisateur</p>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</x-bar>