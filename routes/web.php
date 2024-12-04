<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackgroundJobController;
use App\Models\BackgroundJob;

// Rutas del panel de administración de background jobs
// Routes for the background jobs admin panel
Route::group([], function () {
    Route::get('/admin/background-jobs', [BackgroundJobController::class, 'index'])->name('background-jobs.index');
    Route::get('/admin/background-jobs/{job}', [BackgroundJobController::class, 'show'])->name('background-jobs.show');
    Route::post('/admin/background-jobs/{job}/cancel', [BackgroundJobController::class, 'cancel'])->name('background-jobs.cancel');
});

// Ruta para crear un solo trabajo
// Route to create a single job
Route::get('/crear-trabajo', function () {
    // Crear el trabajo pendiente
    // Create the pending job
    $job = BackgroundJob::create([
        'class' => \App\Jobs\ExampleJob::class,
        'method' => 'handle',
        'params' => ['message' => 'hello word'],
        'status' => 'pending',
        'max_attempts' => 3,
        'priority' => 5,
        'available_at' => now(),
    ]);

    return "Trabajo creado con ID: {$job->id}"; // Job created with ID: {$job->id}
});

// Nueva ruta para crear múltiples trabajos de manera controlada
// New route to create multiple jobs in a controlled way
Route::get('/ejecutar-tareas', function () {
    // Crear un trabajo con mensaje "Otro mensaje de prueba"
    // Create a job with message "Another test message"
    runBackgroundJob(
        \App\Jobs\ExampleJob::class,
        'handle',
        ['message' => 'Otro mensaje de prueba'], // Another test message
        3,  // Intentos máximos // Max attempts
        5,  // Prioridad // Priority
        0   // Retraso en segundos // Delay in seconds
    );

    // Crear un trabajo con mensaje "Mensaje con retraso" y retraso de 60 segundos
    // Create a job with message "Delayed message" and a delay of 60 seconds
    runBackgroundJob(
        \App\Jobs\ExampleJob::class,
        'handle',
        ['message' => 'Mensaje con retraso'], // Delayed message
        3,
        5,
        60  // Retraso en segundos // Delay in seconds
    ); 

    return "Trabajos en segundo plano iniciados."; // Background jobs started.
});
