<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Passenger;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos passengers para usar en las reservas
        $passengers = Passenger::take(10)->get();

        if ($passengers->isEmpty()) {
            $this->command->warn('No hay passengers disponibles. Ejecuta primero PassengerSeeder.');
            return;
        }

        $reservations = [
            [
                'flight_number' => 'AA123',
                'departure_time' => Carbon::now()->addDays(5)->setTime(8, 30),
                'status' => 'CONFIRMED',
                'passengers' => [
                    [
                        'id' => $passengers[0]->id,
                        'name' => $passengers[0]->name,
                        'document' => $passengers[0]->document,
                    ],
                    [
                        'id' => $passengers[1]->id,
                        'name' => $passengers[1]->name,
                        'document' => $passengers[1]->document,
                    ],
                ],
            ],
            [
                'flight_number' => 'UA456',
                'departure_time' => Carbon::now()->addDays(7)->setTime(14, 15),
                'status' => 'PENDING',
                'passengers' => [
                    [
                        'id' => $passengers[2]->id,
                        'name' => $passengers[2]->name,
                        'document' => $passengers[2]->document,
                    ],
                ],
            ],
            [
                'flight_number' => 'DL789',
                'departure_time' => Carbon::now()->addDays(10)->setTime(20, 45),
                'status' => 'CONFIRMED',
                'passengers' => [
                    [
                        'id' => $passengers[3]->id,
                        'name' => $passengers[3]->name,
                        'document' => $passengers[3]->document,
                    ],
                    [
                        'id' => $passengers[4]->id,
                        'name' => $passengers[4]->name,
                        'document' => $passengers[4]->document,
                    ],
                    [
                        'id' => $passengers[5]->id,
                        'name' => $passengers[5]->name,
                        'document' => $passengers[5]->document,
                    ],
                ],
            ],
            [
                'flight_number' => 'BA321',
                'departure_time' => Carbon::now()->addDays(3)->setTime(6, 20),
                'status' => 'CANCELLED',
                'passengers' => [
                    [
                        'id' => $passengers[6]->id,
                        'name' => $passengers[6]->name,
                        'document' => $passengers[6]->document,
                    ],
                ],
            ],
            [
                'flight_number' => 'AF654',
                'departure_time' => Carbon::now()->addDays(12)->setTime(11, 30),
                'status' => 'CONFIRMED',
                'passengers' => [
                    [
                        'id' => $passengers[7]->id,
                        'name' => $passengers[7]->name,
                        'document' => $passengers[7]->document,
                    ],
                    [
                        'id' => $passengers[8]->id,
                        'name' => $passengers[8]->name,
                        'document' => $passengers[8]->document,
                    ],
                ],
            ],
            [
                'flight_number' => 'LH987',
                'departure_time' => Carbon::now()->addDays(15)->setTime(16, 45),
                'status' => 'PENDING',
                'passengers' => [
                    [
                        'id' => $passengers[9]->id,
                        'name' => $passengers[9]->name,
                        'document' => $passengers[9]->document,
                    ],
                ],
            ],
        ];

        foreach ($reservations as $reservationData) {
            Reservation::create($reservationData);
        }
    }
}
