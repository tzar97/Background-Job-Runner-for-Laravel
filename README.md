# **Background Job Runner for Laravel** (show as code)

This project implements a background job execution system in Laravel without relying on external services like Redis or RabbitMQ. Jobs are stored in the database and processed using scheduled Artisan commands. It includes a web dashboard to manage and monitor jobs, supports execution delays, and task prioritization.

---

## **Table of Contents**

- [Main Features](#main-features)
- [Prerequisites](#prerequisites)
- [Installation and Configuration](#installation-and-configuration)
- [System Usage](#system-usage)
  - [1. Creating Jobs](#1-creating-jobs)
  - [2. Processing Jobs](#2-processing-jobs)
  - [3. Admin Dashboard](#3-admin-dashboard)
- [Function runBackgroundJob](#function-runbackgroundjob)
  - [Usage Examples](#usage-examples)
  - [Parameters](#parameters)
- [Configuration of Retries, Delays, Priorities, and Security](#configuration-of-retries-delays-priorities-and-security)
  - [Retry Attempts](#retry-attempts)
  - [Execution Delays](#execution-delays)
  - [Job Priority](#job-priority)
  - [Security Settings](#security-settings)
- [Advanced Features](#advanced-features)
  - [Job Control Dashboard](#job-control-dashboard)
  - [Priority Handling](#priority-handling)
- [Testing and Logs](#testing-and-logs)
- [Assumptions, Limitations, and Areas for Improvement](#assumptions-limitations-and-areas-for-improvement)
- [Project Structure](#project-structure)
- [Conclusions](#conclusions)

---

## **Main Features**

- **Background Task Execution**: Execute tasks asynchronously without blocking the main application flow.
- **Execution Delays**: Schedule tasks to be executed after a specific delay.
- **Task Priority**: Set priorities so that the most important jobs run first.
- **Web Admin Dashboard**: Interface to visualize, monitor, and manage background tasks.
- **Error Handling and Retries**: Log errors and configure the maximum number of retries for each task.
- **Task Cancellation**: Cancel pending or running tasks directly from the dashboard.

---

## **Prerequisites**

- **PHP >= 8.0**
- **Composer**
- **Laravel Framework (version 8 or higher)**
- **Web Server (Apache, Nginx, etc.)**
- **Database (MySQL, PostgreSQL, etc.)**
- **Node.js and NPM (for asset compilation if using Laravel Mix)**

---

## **Installation and Configuration**

### **1. Clone the Repository**

```bash
git clone https://github.com/tzar97/Background-Job-Runner-for-Laravel.git
cd BackgroundJobRunnerforLaravel
```

### **2. Install Dependencies**

```bash
composer install
npm install
```

### **3. Configure the `.env` File**

Copy the example file and configure your environment variables:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Set up the database connection in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### **4. Run Migrations**

```bash
php artisan migrate
```

### **5. Compile Assets (Optional)**

If you are using Laravel Mix for the admin dashboard:

```bash
npm run dev
```

---

## **System Usage**

### **1. Creating Jobs**

You can create background jobs using the `runBackgroundJob` function.

#### **Basic Example**

```php
use App\Jobs\ExampleJob;

runBackgroundJob(
    ExampleJob::class,
    'handle',
    ['message' => 'Hello World'],
    3,  // Maximum retry attempts
    5,  // Priority
    0   // Delay in seconds
);
```

Visit `http://127.0.0.1:8000/create-job` to create a sample job.

### **2. Processing Jobs**

#### **Run the Command Manually**

Process pending jobs:

```bash
php artisan background-jobs:process
```

#### **Schedule Automatic Processing**

Set up Laravel's scheduler to run the command every minute.

- **In `app/Console/Kernel.php`:**

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('background-jobs:process')->everyMinute();
}
```

### **3. Admin Dashboard**

Access the web dashboard to manage jobs:

```
http://127.0.0.1:8000/admin/background-jobs
```

- **View Job List**: See all jobs with their status and details.
- **View Job Details**: Click on a job to view detailed information.
- **Cancel Jobs**: Cancel pending or running jobs.
- **Note**: Currently, the dashboard does not require authentication.

---

## **Function `runBackgroundJob`**

The `runBackgroundJob` function registers a job in the database to be processed in the background.

### **Usage Examples**

#### **1. Create an Immediate Job**

```php
runBackgroundJob(
    \App\Jobs\SendEmailJob::class,
    'handle',
    ['email' => 'user@example.com'],
    3,  // Maximum retry attempts
    5,  // Priority
    0   // No delay
);
```

#### **2. Create a Delayed Job**

```php
runBackgroundJob(
    \App\Jobs\GenerateReportJob::class,
    'handle',
    ['reportId' => 123],
    3,
    5,
    3600  // 1-hour delay
);
```

### **Parameters**

- **`$class`**: Fully qualified class name of the job.
- **`$method`**: Method to be executed in the class.
- **`$params`**: Array of parameters to pass to the method.
- **`$max_attempts`**: Maximum number of retry attempts in case of failure.
- **`$priority`**: Priority of the job (integer, higher number = higher priority).
- **`$delay`**: Delay in seconds before the job is available for processing.

---

## **Configuration of Retries, Delays, Priorities, and Security**

### **Retry Attempts**

**Definition**: Number of times a job will be retried if it fails.

**Configuration**: Set the `$max_attempts` parameter when creating the job.

**Example**:

```php
runBackgroundJob(
    ExampleJob::class,
    'handle',
    $params,
    5,  // Maximum retry attempts
    $priority,
    $delay
);
```

### **Execution Delays**

**Definition**: Time in seconds before the job is available for processing.

**Configuration**: Set the `$delay` parameter when creating the job.

**Example**:

```php
runBackgroundJob(
    ExampleJob::class,
    'handle',
    $params,
    $max_attempts,
    $priority,
    120  // 2-minute delay
);
```

### **Job Priority**

**Definition**: Higher priority jobs are processed first.

**Configuration**: Set the `$priority` parameter (integer, higher number = higher priority).

**Example**:

```php
runBackgroundJob(
    ExampleJob::class,
    'handle',
    $params,
    $max_attempts,
    10,  // High priority
    $delay
);
```

### **Security Settings**

**Allowed Classes**: Define which classes can be executed in the background to prevent unauthorized code execution.

**Configuration**: In `config/background_jobs.php`, add the allowed classes:

```php
return [
    'allowed_classes' => [
        \App\Jobs\ExampleJob::class,
        \App\Jobs\SendEmailJob::class,
        // Add more classes as needed
    ],
];

### **Advanced Features**

Job Control Panel 
Description: Web interface to view and manage background jobs.
Features:
    Job listing with details.
    View parameters and status.
    Cancel pending or running jobs.

Priority Management
    Implementation: Jobs are sorted by descending priority and then by creation date.
    Usage: When creating a job, set the $priority parameter.


### **Testing and logs**

Running Tests
    Create Test Jobs: Use the /create-job and /run-jobs paths to create test jobs.
    Process Jobs: Run php artisan background-jobs:process and verify that jobs are processed correctly.
Logs
    Laravel Logs: Located in storage/logs/laravel.log, these contain information about the execution of jobs.
    Job Error Logs: If an error occurs while processing a job, it is logged in storage/logs/background_jobs_errors.log.


    
-------------------------------------------


-------------------------------------------
