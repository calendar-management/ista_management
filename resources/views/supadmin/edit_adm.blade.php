@extends("supadmin.bar")

@section("main")

<link href="../admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../admin/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        @media (max-width: 333px) {
            .card-body .d-flex {
                flex-direction: column; /* Stack elements vertically */
                align-items: flex-start; /* Align items to the left */
            }

            .card-body .fs-4 {
                margin-bottom: 10px; /* Add space between title and icon */
            }

            .card-body i {
                margin-bottom: 10px; /* Add space between icon and details */
            }

            .card-body .text-gray-800 {
                margin-top: 5px; /* Add space between "Details" and the icon */
            }
        }
    </style>

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
                    <label for="etablissement" class="form-label">Etablissement</label>
                    <input type="text" value="{{ $administrateur->etablissement }}" class="form-control" id="etablissement" name="etablissement" required>
                </div>


                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Mis a jour</button>
                </div>
            </form>
        </div>

    </div>
    <script src="../admin/vendor/jquery/jquery.min.js"></script>
    <script src="../admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../admin/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../admin/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../admin/js/demo/chart-area-demo.js"></script>
    <script src="../admin/js/demo/chart-pie-demo.js"></script>
@endsection
