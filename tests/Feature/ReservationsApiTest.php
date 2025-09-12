<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

final class ReservationsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_create_reservation_successfully(): void
    {
        $reservationData = [
            'flight_number' => 'AA123',
            'departure_time' => '2025-12-25 10:30:00',
            'passengers' => [
                [
                    'name' => 'Juan Pérez',
                    'document' => '12345678'
                ],
                [
                    'name' => 'María García',
                    'document' => '87654321'
                ]
            ]
        ];

        $response = $this->postJson('/api/reservations', $reservationData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Reservation created successfully',
                'data' => [
                    'flight_number' => 'AA123',
                    'status' => 'PENDING'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'flight_number',
                    'departure_time',
                    'passengers',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('reservations', [
            'flight_number' => 'AA123',
            'status' => 'PENDING'
        ]);

        $this->assertDatabaseHas('passengers', [
            'name' => 'María García',
            'document' => '87654321'
        ]);
    }

    public function test_can_create_reservation_with_default_status(): void
    {
        $reservationData = [
            'flight_number' => 'BB456',
            'departure_time' => '2025-12-26 14:15:00',
            'passengers' => [
                [
                    'name' => 'Carlos López',
                    'document' => '11223344'
                ]
            ]
        ];

        $response = $this->postJson('/api/reservations', $reservationData);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_create_reservation_fails_with_invalid_data(): void
    {
        $invalidData = [
            'flight_number' => '', // Required field empty
            'departure_time' => 'invalid-date',
            'passengers' => [] // Empty array not allowed
        ];

        $response = $this->postJson('/api/reservations', $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'Validation failed'
            ])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'flight_number',
                    'departure_time',
                    'passengers'
                ]
            ]);

        $this->assertDatabaseMissing('reservations', [
            'flight_number' => ''
        ]);
    }

    public function test_create_reservation_fails_with_missing_passenger_data(): void
    {
        $invalidData = [
            'flight_number' => 'CC789',
            'departure_time' => '2025-12-27 16:00:00',
            'passengers' => [
                [
                    'name' => 'Ana Rodríguez'
                    // Missing document field
                ]
            ]
        ];

        $response = $this->postJson('/api/reservations', $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            //  ->assertJsonHasErrors(['passengers.0.document'])
        ;
    }


    public function test_can_list_empty_reservations(): void
    {
        $response = $this->getJson('/api/reservations');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservations retrieved successfully',
                'data' => []
            ])
            ->assertJsonStructure([
                'data',
                'message'
            ]);
    }

    public function test_can_list_reservations_successfully(): void
    {
        // Crear algunas reservations de prueba
        $reservation1 = Reservation::create([
            'flight_number' => 'EE111',
            'departure_time' => '2025-12-29 08:00:00',
            'status' => 'PENDING',
            'passengers' => [
                [
                    'name' => 'Laura González',
                    'document' => '99887766'
                ]
            ]
        ]);

        $reservation2 = Reservation::create([
            'flight_number' => 'FF222',
            'departure_time' => '2025-12-30 12:45:00',
            'status' => 'CONFIRMED',
            'passengers' => [
                [
                    'name' => 'Roberto Silva',
                    'document' => '44556677'
                ],
                [
                    'name' => 'Carmen Ruiz',
                    'document' => '33445566'
                ]
            ]
        ]);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservations retrieved successfully',
                'data' => [
                    [
                        'id' => $reservation1->id,
                        'flight_number' => 'EE111',
                        'status' => 'PENDING'
                    ],
                    [
                        'id' => $reservation2->id,
                        'flight_number' => 'FF222',
                        'status' => 'CONFIRMED'
                    ]
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'flight_number',
                        'departure_time',
                        'status',
                        'passengers',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'message'
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_listed_reservations_contain_correct_passenger_data(): void
    {
        $passengersData = [
            [
                'name' => 'Diego Fernández',
                'document' => '12121212'
            ],
            [
                'name' => 'Sofía Morales',
                'document' => '34343434'
            ]
        ];

        Reservation::create([
            'flight_number' => 'GG333',
            'departure_time' => '2025-12-31 20:15:00',
            'status' => 'CONFIRMED',
            'passengers' => $passengersData
        ]);

        $response = $this->getJson('/api/reservations');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    [
                        'flight_number' => 'GG333',
                        'passengers' => $passengersData
                    ]
                ]
            ]);
    }
}
