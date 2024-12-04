!! Background Job Runner for Laravel !!


Este proyecto es una implementación de un sistema de ejecución de tareas en segundo plano en Laravel sin depender de servicios externos como Redis o RabbitMQ. Las tareas se almacenan en la base de datos y se procesan mediante comandos Artisan programados, incluye un panel web para administrar y monitorear los trabajos. Además, soporta funcionalidades avanzadas como retrasos en la ejecución y priorización de tareas.

git clone https://github.com/tu-usuario/BackgroundJobRunnerforLaravel.git
cd BackgroundJobRunnerforLaravel


------------------------------------------
.env:
env
Copiar código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
-------------------------------------------

Puedes crear trabajos en segundo plano desde rutas o controladores. Por ejemplo, en routes/web.php:

Route::get('/crear-trabajo', function () {
    runBackgroundJob(
        \App\Jobs\ExampleJob::class,
        'handle',
        ['message' => 'Hola Mundo'],
        3,  // Intentos máximos
        5,  // Prioridad
        0   // Retraso en segundos
    );

    return "Trabajo en segundo plano iniciado.";
});

-------------------------------------------
Visita http://127.0.0.1:8000/crear-trabajo para crear el trabajo.
-------------------------------------------

Puedes llamar a runBackgroundJob desde cualquier parte de tu aplicación:

runBackgroundJob(
    \App\Jobs\ExampleJob::class,
    'handle',
    ['message' => 'Mensaje con retraso'],
    3,
    5,
    60  // Retraso de 60 segundos
);

-------------------------------------------
Modelo BackgroundJob
Ubicado en app/Models/BackgroundJob.php, representa los trabajos en la base de datos.

Estructura de la Tabla:

Campos Principales:
class: Clase del trabajo.
method: Método a ejecutar.
params: Parámetros en formato JSON.
status: Estado del trabajo (pending, running, completed, failed, cancelled).
attempts: Número de intentos realizados.
max_attempts: Máximo de intentos permitidos.
priority: Prioridad del trabajo.
available_at: Marca de tiempo para retrasos.

-------------------------------------------
Función runBackgroundJob
Definida en app/helpers.php (o donde prefieras), esta función registra un trabajo en la base de datos:

function runBackgroundJob($class, $method, $params = [], $max_attempts = 1, $priority = 0, $delay = 0)
{}

-------------------------------------------

Comando background-jobs:process
Ubicado en app/Console/Commands/ProcessBackgroundJobs.php, este comando procesa los trabajos pendientes:

Manejo de Prioridad y Retrasos: Ordena los trabajos por prioridad y verifica available_at.
Manejo de Errores y Reintentos: Registra errores y controla los reintentos hasta max_attempts.

-------------------------------------------

Rutas y Controladores
Rutas Definidas en routes/web.php:

/crear-trabajo: Crea un trabajo de ejemplo.
/ejecutar-tareas: Crea múltiples trabajos de manera controlada.
/admin/background-jobs: Panel de administración.

-------------------------------------------

Controlador BackgroundJobController:

Maneja las vistas del panel y acciones como cancelar trabajos.

-------------------------------------------




