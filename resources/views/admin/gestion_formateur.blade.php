
@extends("admin.bar")

@section("main")
    <div>
        <h1 class="text-success m-3">Gestion Des Formateurs:</h1>
        <table class="table table-bordered mt-5">
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Module</th>
                <th>Groupe</th>
                <th>Type(prt_syn)</th>
                <th>Actions</th>
            </tr>
            @foreach ($formateurs as $formateur)
            
            <tbody>
                <td>{{ $formateur->id }}</td>
                <td>{{ $formateur->name }}</td>
                <td>{{ $formateur->module }}</td>
                <td>{{ $formateur->groupe }}</td>
                <td>{{ $formateur->type_seances }}</td>

                <td><a href="/edit_formateur" class=" text-danger">Edit</a> <a href="/avancement_formateur" class=" text-primary ml-2">Suivie</a></td>
            </tbody>
            @endforeach
        </table>

    </div>
@endsection
