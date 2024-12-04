<?php

use Illuminate\Support\Facades\Log;
use App\Models\BackgroundJob;

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Obtener el ID del trabajo
$options = getopt('', ['job_id:']);
$jobId = $options['job_id'] ?? null;

if (!$jobId) {
    Log::error("No se proporcionó el ID del trabajo.");
    exit(1);
}

$job = BackgroundJob::find($jobId);

if (!$job) {
    Log::error("Trabajo con ID {$jobId} no encontrado.");
    exit(1);
}

// Verificar si el trabajo está disponible para ejecutarse
if ($job->available_at && $job->available_at->isFuture()) {
    Log::info("El trabajo {$jobId} aún no está disponible para ejecutarse.");
    exit(0);
}

try {
    $job->status = 'running';
    $job->save();

    $class = $job->class;
    $method = $job->method;
    $params = $job->params ?? [];
    $maxAttempts = $job->max_attempts;

    // Validar y sanitizar la clase y el método
    if (!in_array($class, config('background_jobs.allowed_classes'))) {
        throw new Exception("Clase no permitida para ejecución en segundo plano.");
    }

    if (!method_exists($class, $method)) {
        throw new Exception("El método especificado no existe en la clase.");
    }

    $instance = app()->make($class);

    do {
        $job->attempts++;
        try {
            call_user_func_array([$instance, $method], $params);
            $job->status = 'completed';
            $job->save();
            Log::info("Tarea completada: {$class}@{$method}", ['params' => $params]);
            break;
        } catch (Exception $e) {
            Log::error("Error en intento {$job->attempts} de {$maxAttempts}: " . $e->getMessage());
            if ($job->attempts >= $maxAttempts) {
                $job->status = 'failed';
                $job->save();
                throw $e;
            }
            sleep(1); // Esperar antes de reintentar
        }
    } while ($job->attempts < $maxAttempts);

} catch (Exception $e) {
    Log::channel('background_jobs_errors')->error("Fallo en la tarea: " . $e->getMessage());
    $job->status = 'failed';
    $job->save();
    exit(1);
}

exit(0);
