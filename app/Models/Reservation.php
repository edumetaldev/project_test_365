<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'departure_time',
        'status',
        'passengers',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'passengers' => 'array',
    ];

    protected $attributes = [
        'status' => 'PENDING'
    ];
}
