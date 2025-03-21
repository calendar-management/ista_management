@extends('admin.bar')

@section('main')
<div class="container mt-4">
    <h2 class="mb-4 text-success">Suivi du formateur : {{ $teacher->name }}</h2>

    @forelse ($teacher->teaching as $teaching)
    @php
        // Check if the progress exists and get the completed hours
        $completed = $teaching->progress ? $teaching->progress->hours_completed : 0;
        $total = $teaching->module->hours ?? 0; // Make sure 'nbrHeure' exists in the module
        $remaining = max(0, $total - $completed);
        $percentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        // Progress bar color logic
        $progressClass = 'bg-success';
        if ($percentage < 30) {
            $progressClass = 'bg-danger';
        } elseif ($percentage < 70) {
            $progressClass = 'bg-warning';
        }
    @endphp

    <div class="mb-4">
        <strong>{{ $teaching->module->name ?? 'N/A' }} / {{ $teaching->group->name ?? 'N/A' }}</strong>

        <div class="progress mt-2" style="height: 25px;">
            <div 
                class="progress-bar {{ $progressClass }}" 
                role="progressbar" 
                style="width: {{ $percentage }}%;" 
                aria-valuenow="{{ $percentage }}" 
                aria-valuemin="0" 
                aria-valuemax="100">
                {{ $percentage }}%
            </div>
        </div>

        <small class="text-muted">
            {{ $completed }}/{{ $total }} heures ({{ $remaining }} restantes)
        </small>
    </div>
    @empty
        <p class="text-muted">Ce formateur n’enseigne aucun module pour le moment.</p>
    @endforelse
</div>
@endsection