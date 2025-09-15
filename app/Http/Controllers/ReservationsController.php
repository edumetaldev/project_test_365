<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class ReservationsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Listar reservas",
     *     tags={"Reservations"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado (PENDING, CONFIRMED, etc.)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de reservas",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Reservation"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $reservations = Reservation::all();

        return response()->json([
            'data' => $reservations,
            'message' => 'Reservations retrieved successfully'
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Crear una nueva reserva",
     *     tags={"Reservations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"flight_number","departure_time","passengers"},
     *             @OA\Property(property="flight_number", type="string", example="SKY123"),
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2025-09-12T18:00:00Z"),
     *             @OA\Property(
     *                 property="passengers",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"name","document"},
     *                     @OA\Property(property="name", type="string", example="Juan Perez"),
     *                     @OA\Property(property="document", type="string", example="12345678")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reserva creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Reservation")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request): JsonResponse
    {
        //refactor RequestForm class
        //refactor Repository
        //refactor domain
        try {
            $validated = $request->validate([
                'flight_number' => 'required|string|max:255',
                'departure_time' => 'required|date',
                'passengers' => 'required|array|min:1',
                'passengers.*.name' => 'required|string|max:255',
                'passengers.*.document' => 'required|string|max:255',
            ]);
            $reservation = Reservation::create($validated);

            foreach ($validated['passengers'] as $pas) {
                Passenger::create($pas);
            }

            return response()->json([
                'data' => $reservation,
                'message' => 'Reservation created successfully'
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
