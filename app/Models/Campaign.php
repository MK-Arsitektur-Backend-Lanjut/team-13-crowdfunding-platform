<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];
}