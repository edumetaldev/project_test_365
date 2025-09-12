<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

final class ReservationChangeStatusControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear una reserva base para usar en los tests
        $this->reservation = Reservation::create([
            'flight_number' => 'AA123',
            'departure_time' => '2025-12-25 10:30:00',
            'status' => 'PENDING',
            'passengers' => [
                [
                    'name' => 'Juan Pérez',
                    'document' => '12345678'
                ]
            ]
        ]);
    }

    public function test_can_change_reservation_status_to_confirmed(): void
    {
        $response = $this->postJson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'CONFIRMED'
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservation status changed successfully',
                'data' => [
                    'id' => $this->reservation->id,
                    'status' => 'CONFIRMED'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'flight_number',
                    'departure_time',
                    'status',
                    'passengers',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'CONFIRMED'
        ]);
    }

    public function test_can_change_reservation_status_to_cancelled(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'CANCELLED'
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservation status changed successfully',
                'data' => [
                    'id' => $this->reservation->id,
                    'status' => 'CANCELLED'
                ]
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'CANCELLED'
        ]);
    }

    public function test_can_change_reservation_status_to_checked_in(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'CHECKED_IN'
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservation status changed successfully',
                'data' => [
                    'id' => $this->reservation->id,
                    'status' => 'CHECKED_IN'
                ]
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'CHECKED_IN'
        ]);
    }

    public function test_can_change_reservation_status_back_to_pending(): void
    {
        // Primero cambiar a CONFIRMED
        $this->reservation->update(['status' => 'CONFIRMED']);

        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'PENDING'
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservation status changed successfully',
                'data' => [
                    'id' => $this->reservation->id,
                    'status' => 'PENDING'
                ]
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING'
        ]);
    }

    public function test_fails_with_invalid_status(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'INVALID_STATUS'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'status'
                ]
            ]);

        // Verificar que el estado no cambió
        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING' // Estado original
        ]);
    }

    public function test_fails_when_status_is_missing(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status'])
            ->assertJson([
                'message' => 'El estado es requerido.',
                'errors' => [
                    'status' => [
                        'El estado es requerido.'
                    ]
                ]
            ]);

        // Verificar que el estado no cambió
        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING' // Estado original
        ]);
    }

    public function test_fails_when_status_is_empty(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => ''
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status']);

        // Verificar que el estado no cambió
        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING' // Estado original
        ]);
    }

    public function test_fails_when_status_is_null(): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => null
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status']);

        // Verificar que el estado no cambió
        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING' // Estado original
        ]);
    }

    public function test_fails_when_reservation_does_not_exist(): void
    {
        $nonExistentId = 999999;

        $response = $this->postjson("/api/reservations/{$nonExistentId}/status", [
            'status' => 'CONFIRMED'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_status_change_preserves_other_reservation_data(): void
    {
        $originalData = [
            'flight_number' => $this->reservation->flight_number,
            'departure_time' => $this->reservation->departure_time->format('Y-m-d H:i:s'),
            'passengers' => $this->reservation->passengers,
        ];

        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => 'CONFIRMED'
        ]);

        $response->assertStatus(Response::HTTP_OK);

        // Recargar la reserva desde la base de datos
        $this->reservation->refresh();

        // Verificar que solo el estado cambió
        $this->assertEquals('CONFIRMED', $this->reservation->status);
        $this->assertEquals($originalData['flight_number'], $this->reservation->flight_number);
        $this->assertEquals($originalData['departure_time'], $this->reservation->departure_time->format('Y-m-d H:i:s'));
        $this->assertEquals($originalData['passengers'], $this->reservation->passengers);
    }

    /**
     * @dataProvider validStatusProvider
     */
    public function test_accepts_all_valid_statuses(string $status): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => $status
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Reservation status changed successfully',
                'data' => [
                    'status' => $status
                ]
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => $status
        ]);
    }

    /**
     * @dataProvider invalidStatusProvider
     */
    public function test_rejects_invalid_statuses(string $invalidStatus): void
    {
        $response = $this->postjson("/api/reservations/{$this->reservation->id}/status", [
            'status' => $invalidStatus
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['status']);

        // Verificar que el estado no cambió
        $this->assertDatabaseHas('reservations', [
            'id' => $this->reservation->id,
            'status' => 'PENDING' // Estado original
        ]);
    }

    public static function validStatusProvider(): array
    {
        return [
            'pending' => ['PENDING'],
            'confirmed' => ['CONFIRMED'],
            'cancelled' => ['CANCELLED'],
            'checked_in' => ['CHECKED_IN'],
        ];
    }

    public static function invalidStatusProvider(): array
    {
        return [
            'invalid_status' => ['INVALID'],
            'random_string' => ['RANDOM_STATUS'],
            'numeric_status' => ['123'],
            'special_characters' => ['STATUS@#$'],
            'empty_spaces' => [' '],
        ];
    }
}
