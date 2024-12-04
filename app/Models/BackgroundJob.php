<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundJob extends Model
{
    protected $fillable = [
        'class',
        'method',
        'params',
        'status',
        'attempts',
        'max_attempts',
        'priority',
        'available_at',
    ];

    protected $casts = [
        'params' => 'array',
        'available_at' => 'datetime',
    ];
}
