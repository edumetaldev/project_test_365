<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Passenger;
use Illuminate\Database\Seeder;

final class PassengerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $passengers = [
            [
                'name' => 'Juan Pérez',
                'document' => '12345678',
            ],
            [
                'name' => 'María García',
                'document' => '87654321',
            ],
            [
                'name' => 'Carlos López',
                'document' => '11223344',
            ],
            [
                'name' => 'Ana Martínez',
                'document' => '44332211',
            ],
            [
                'name' => 'Luis Rodríguez',
                'document' => '55667788',
            ],
            [
                'name' => 'Sofia Hernández',
                'document' => '88776655',
            ],
            [
                'name' => 'Diego González',
                'document' => '99887766',
            ],
            [
                'name' => 'Valentina Morales',
                'document' => '66778899',
            ],
            [
                'name' => 'Roberto Silva',
                'document' => '33445566',
            ],
            [
                'name' => 'Camila Torres',
                'document' => '66554433',
            ],
            [
                'name' => 'Miguel Vargas',
                'document' => '77889900',
            ],
            [
                'name' => 'Isabella Ruiz',
                'document' => '00998877',
            ],
            [
                'name' => 'Fernando Castro',
                'document' => '44556677',
            ],
            [
                'name' => 'Natalia Jiménez',
                'document' => '77665544',
            ],
            [
                'name' => 'Andrés Mendoza',
                'document' => '88990011',
            ],
        ];

        foreach ($passengers as $passengerData) {
            Passenger::create($passengerData);
        }
    }
}
