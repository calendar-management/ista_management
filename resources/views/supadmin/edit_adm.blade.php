<x-bar :navlinks="[
    ['label'=>'Gestion Admins','route'=>'../gestion_adm','class'=>'','icon'=>'fas fa-users'],
    ['label'=>'Dashboard','route'=>'../dashboard','class'=>'','icon'=>'fas fa-chart-bar'],
    ['label'=>'Ajouter Admin','route'=>'../add_admin','class'=>'','icon'=>'fas fa-user-plus'],
]">

    <div class="container">
        
        
        <h1 class="text-success my-3">Editer Administrateur:</h1>
        @if (session('update_success'))
            <p id="update_msg">{{ session('update_success')}}</p>
        @endif
        <div class="card shadow p-4 mt-5">
            <form action="{{ route('update_admin', $administrateur->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" value="{{ $administrateur->name }}" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" value="{{ $administrateur->email }}" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="etablissement" class="form-label">Etablissement</label>
                    <input type="text" value="{{ $administrateur->etablissement }}" class="form-control" id="etablissement" name="etablissement" required>
                </div>


                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Mis a jour</button>
                </div>
            </form>
        </div>

    </div>

</x-bar>