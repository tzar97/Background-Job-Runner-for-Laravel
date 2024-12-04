<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class ExampleJob
{
    public function handle($message)
    {
        Log::info("Executing example job Ejecutando ExampleJob con mensaje: {$message}");
    }
}
