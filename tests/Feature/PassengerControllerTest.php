<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Passenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

final class PassengerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_show_passenger_returns_correct_resource_structure(): void
    {
        // Crear un passenger de prueba
        $passenger = Passenger::create([
            'name' => 'Juan Pérez',
            'document' => '12345678'
        ]);

        // Hacer la petición GET al endpoint show
        $response = $this->getJson("/api/passengers/{$passenger->id}");

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(Response::HTTP_OK);

        // Verificar la estructura de la respuesta JSON
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'document',
                'created_at',
                'updated_at'
            ]
        ]);

        // Verificar que los datos específicos sean correctos
        $response->assertJson([
            'data' => [
                'id' => $passenger->id,
                'name' => 'Juan Pérez',
                'document' => '12345678'
            ]
        ]);

        // Verificar que la respuesta contenga los timestamps
        $responseData = $response->json('data');
        $this->assertNotNull($responseData['created_at']);
        $this->assertNotNull($responseData['updated_at']);
    }

    public function test_show_passenger_with_different_data(): void
    {
        // Crear otro passenger con datos diferentes
        $passenger = Passenger::create([
            'name' => 'María García López',
            'document' => '87654321'
        ]);

        $response = $this->getJson("/api/passengers/{$passenger->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id' => $passenger->id,
                    'name' => 'María García López',
                    'document' => '87654321'
                ]
            ]);
    }

    public function test_show_nonexistent_passenger_returns_404(): void
    {
        // Intentar obtener un passenger que no existe
        $response = $this->getJson('/api/passengers/999999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_passenger_resource_uses_correct_format(): void
    {
        $passenger = Passenger::create([
            'name' => 'Test Passenger',
            'document' => '11111111'
        ]);

        $response = $this->getJson("/api/passengers/{$passenger->id}");

        // Verificar que la respuesta esté envuelta en un objeto 'data'
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'document',
                'created_at',
                'updated_at'
            ]
        ]);

        // Verificar que no haya campos adicionales inesperados
        $responseData = $response->json('data');
        $expectedKeys = ['id', 'name', 'document', 'created_at', 'updated_at'];
        $actualKeys = array_keys($responseData);

        $this->assertEquals($expectedKeys, $actualKeys);
    }
}
