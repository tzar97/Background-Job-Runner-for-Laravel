@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Trabajo #{{ $job->id }}</h1>
    <ul>
        <li><strong>Clase:</strong> {{ $job->class }}</li>
        <li><strong>Método:</strong> {{ $job->method }}</li>
        <li><strong>Parámetros:</strong> {{ json_encode($job->params) }}</li>
        <li><strong>Estado:</strong> {{ $job->status }}</li>
        <li><strong>Intentos:</strong> {{ $job->attempts }} / {{ $job->max_attempts }}</li>
        <li><strong>Prioridad:</strong> {{ $job->priority }}</li>
        <li><strong>Creado:</strong> {{ $job->created_at }}</li>
        <li><strong>Actualizado:</strong> {{ $job->updated_at }}</li>
    </ul>
    <a href="{{ route('background-jobs.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
