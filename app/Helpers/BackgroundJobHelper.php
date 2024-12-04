<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackgroundJob;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessBackgroundJobs extends Command
{
    protected $signature = 'background-jobs:process';
    protected $description = 'Procesa trabajos en segundo plano pendientes'; 
    

    public function handle()
    {
        
        // Fetch pending jobs
        $jobs = BackgroundJob::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('available_at')
                      ->orWhere('available_at', '<=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($jobs as $job) {
            $this->processJob($job);
        }

        $this->info('Trabajos en segundo plano procesados.'); 
        // Background jobs processed.
    }

    protected function processJob($job)
    {
        try {
            // update to running
            $job->status = 'running';
            $job->save();

            
            // execute
            $class = $job->class;
            $method = $job->method;
            $params = $job->params ?? [];

            if (!in_array($class, config('background_jobs.allowed_classes'))) {
                throw new Exception("Clase no permitida para ejecución en segundo plano.(not allowed for background execution)."); 
                
            }

            if (!method_exists($class, $method)) {
                throw new Exception("El método especificado no existe en la clase.(method does not exist in the class)"); 
                
            }

            $instance = app()->make($class);
            call_user_func_array([$instance, $method], $params);

          
            // update to 'completed'
            $job->status = 'completed';
            $job->save();

            Log::info("Trabajo {$job->id} completed."); 
           
        } catch (Exception $e) {
           
            
            
            $job->attempts++;
            if ($job->attempts >= $job->max_attempts) {
                $job->status = 'failed';
            } else {
                $job->status = 'pending';
            }
            $job->save();

            Log::error("Error al procesar el trabajo {$job->id}: " . $e->getMessage()); 
            
        }
    }
}
