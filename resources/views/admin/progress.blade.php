@extends('admin.bar')

@section('main')
<div class="container mt-4">
    <h2 class="mb-4 text-success">Suivi du formateur : {{ $teacher->name }}</h2>

    @forelse ($teacher->teaching as $teaching)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">
                    Module: {{ $teaching->module->name ?? 'N/A' }} /
                    Groupe: {{ $teaching->group->name ?? 'N/A' }}
                </h5>
                @if ($teaching->progress)
                    <ul class="list-group list-group-flush mt-3">
                        <li class="list-group-item">Heures complétées: {{ $teaching->progress->hours_completed }}</li>
                        <li class="list-group-item">Heures restantes: {{ $teaching->progress->remaining_hours }}</li>
                        <li class="list-group-item">Date examen final: {{ $teaching->progress->final_exam_date }}</li>
                    </ul>
                @else
                    <p class="text-muted mt-2">Aucun progrès enregistré pour ce module.</p>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Ce formateur n’enseigne aucun module pour le moment.</p>
    @endforelse
</div>
@endsection
