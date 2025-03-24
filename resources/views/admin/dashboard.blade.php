<x-bar :navlinks="[
    ['label' => 'Gestion Formateurs', 'route' => 'gestion_formateur', 'class' => '', 'icon' => 'fas fa-users'],
    ['label' => 'Gestion Calendrier', 'route' => 'gestion_calendrier', 'class' => '', 'icon' => 'fas fa-calendar-alt'],
    ['label' => 'Dashboard', 'route' => 'dashboard', 'class' => 'active', 'icon' => 'fas fa-chart-bar'],
    ['label' => 'Ajouter Formateur', 'route' => 'add_formateur', 'class' => '', 'icon' => 'fas fa-user-plus'],
]">
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </button>
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Formateur Card -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                        <div class="card border-left-primary shadow h-100 py-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fs-4 fw-bold text-success text-uppercase mb-3">
                                            Formateurs
                                        </div>
                                    </div>
                                    <div>
                                        <i class="fas fa-clipboard-list fa-3x text-gray-300"></i>
                                        <div class="mt-2 text-gray-800">
                                            <a href="/gestion_formateur" class="text-primary fs-5">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-bar>
