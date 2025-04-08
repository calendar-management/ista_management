<x-bar :navlinks="[
        ['label' => 'Gestion Admins', 'route' => 'gestion_formateur', 'class' => 'active', 'icon' => 'fas fa-users'],
        ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => '', 'icon' => 'fas fa-chart-bar'],
        ['label' => 'Ajouter Admin', 'route' => 'add_admin', 'class' => '', 'icon' => 'fas fa-user-plus'],
    ]">

    <div class="container-fluid px-4">
        @if (session('reset_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('reset_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        
        @endif
        @if (session('delete_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('delete_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm mt-5">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-users-cog me-3 fs-4"></i>
                <h1 class="card-title mb-0 fs-5">Gestion des Administrateurs</h1>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mt-4">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">
                                    <i class="fas fa-user me-2"></i>Name
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-envelope me-2"></i>Login
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-building me-2"></i>Institution
                                </th>
                                <th class="text-center">
                                    <i class="fas fa-cogs me-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($administrateurs as $administrateur)
                                <tr>
                                    <td class="text-center align-middle">{{ $administrateur->name }}</td>
                                    <td class="text-center align-middle">{{ $administrateur->email }}</td>
                                    <td class="text-center align-middle">{{ $administrateur->etablissement }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('edit_adm', $administrateur) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('delete_admin', $administrateur->id) }}" method="post"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this administrator?')">
                                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                                </button>
                                            </form>

                                            <form action="{{route('reset_db',$administrateur->id)}}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    onclick="return confirm('Are you sure you want to delete this administrator?')">
                                                    <i class="fas fa-rotate-right me-1"></i>Reinitialiser les donnees  
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $administrateurs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Optional: Add some interactivity
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.classList.add('table-active');
                });
                row.addEventListener('mouseleave', function () {
                    this.classList.remove('table-active');
                });
            });
        });
    </script>
</x-bar>