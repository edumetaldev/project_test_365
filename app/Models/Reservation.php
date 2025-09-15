<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Reservation",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="flight_number", type="string"),
 *   @OA\Property(property="departure_time", type="string", format="date-time"),
 *   @OA\Property(property="status", type="string", enum={"PENDING","CONFIRMED","CANCELLED","CHECKED_IN"}),
 *   @OA\Property(property="passengers", type="array", @OA\Items(ref="#/components/schemas/Passenger"))
 * )
 */
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
