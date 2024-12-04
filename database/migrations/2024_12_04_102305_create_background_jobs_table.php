<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->string('method');
            $table->json('params')->nullable();
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(1);
            $table->integer('priority')->default(0);
            $table->timestamp('available_at')->nullable(); // for delay
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_jobs');
    }
};
