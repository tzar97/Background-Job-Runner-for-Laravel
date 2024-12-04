@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Trabajos en Segundo Plano</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Clase</th>
                <th>Método</th>
                <th>Estado</th>
                <th>Intentos</th>
                <th>Prioridad</th>
                <th>Creado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
            <tr>
                <td>{{ $job->id }}</td>
                <td>{{ $job->class }}</td>
                <td>{{ $job->method }}</td>
                <td>{{ $job->status }}</td>
                <td>{{ $job->attempts }} / {{ $job->max_attempts }}</td>
                <td>{{ $job->priority }}</td>
                <td>{{ $job->created_at }}</td>
                <td>
                    <a href="{{ route('background-jobs.show', $job->id) }}" class="btn btn-primary btn-sm">Ver</a>
                    @if(in_array($job->status, ['pending', 'running']))
                    <form action="{{ route('background-jobs.cancel', $job->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $jobs->links() }}
</div>
@endsection
