!!Background Job Runner for Laravel!!

This project implements a system for executing background tasks in Laravel without relying on external services like Redis or RabbitMQ. Tasks are stored in the database and processed using scheduled Artisan commands. It includes a web panel to manage and monitor jobs and supports advanced features like execution delays and task prioritization.

git clone https://github.com/tzar97/Background-Job-Runner-for-Laravel.git
cd BackgroundJobRunnerforLaravel

-------------------------------------------
install dependencies
composer install
npm install
-------------------------------------------

env Configuration
Update your .env file with the following database configuration:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

-------------------------------------------
execute migrations

php artisan migrate

-------------------------------------------
Creating Background Jobs
You can create background jobs from routes or controllers. For example, in routes/web.php:

Route::get('/create-job', function () {
    runBackgroundJob(
        \App\Jobs\ExampleJob::class,
        'handle',
        ['message' => 'Hello World'],
        3,  // Maximum attempts
        5,  // Priority
        0   // Delay in seconds
    );

    return "Background job started.";
});

-------------------------------------------
Visit http://127.0.0.1:8000/create-job to create the job.
-------------------------------------------
You can call runBackgroundJob from anywhere in your application:

runBackgroundJob(
    \App\Jobs\ExampleJob::class,
    'handle',
    ['message' => 'Delayed message'],
    3,
    5,
    60  // 60-second delay
);

-------------------------------------------
manual execution for pending task proccess
php artisan background-jobs:process
-------------------------------------------

Schedule Automatic Processing
Set the Laravel scheduler to run the command every minute.

In app/Console/Kernel.php:

protected function schedule(Schedule $schedule)
{
    $schedule->command('background-jobs:process')->everyMinute();
}

-------------------------------------------
dashboard admin

http://127.0.0.1:8000/admin/background-jobs
-------------------------------------------

implementation details
BackgroundJob Model
Located at app/Models/BackgroundJob.php, this model represents the jobs in the database.

Table Structure:

Main Fields:

class: Job class.
method: Method to execute.
params: Parameters in JSON format.
status: Job status (pending, running, completed, failed, cancelled).
attempts: Number of attempts made.
max_attempts: Maximum allowed attempts.
priority: Job priority.
available_at: Timestamp for delays.

-------------------------------------------

runBackgroundJob Function
Defined in app/helpers.php (or wherever you prefer), this function registers a job in the database:

function runBackgroundJob($class, $method, $params = [], $max_attempts = 1, $priority = 0, $delay = 0)
{
    // Function implementation
}


-------------------------------------------

background-jobs:process Command
Located at app/Console/Commands/ProcessBackgroundJobs.php, this command processes pending jobs:

Handling Priority and Delays: Sorts jobs by priority and checks available_at.
Error Handling and Retries: Logs errors and controls retries up to max_attempts.

-------------------------------------------

Routes and Controllers
Routes Defined in routes/web.php:

/create-job: Creates an example job.
/execute-tasks: Creates multiple jobs in a controlled manner.
/admin/background-jobs: Administration panel.

-------------------------------------------

BackgroundJobController
Handles the panel views and actions like cancelling jobs.

-------------------------------------------


-------------------------------------------


-------------------------------------------
