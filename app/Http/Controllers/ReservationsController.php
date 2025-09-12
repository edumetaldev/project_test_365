<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

final class ReservationsController extends Controller
{
    public function index(): JsonResponse
    {
        $reservations = Reservation::all();

        return response()->json([
            'data' => $reservations,
            'message' => 'Reservations retrieved successfully'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        //refactor RequestForm class
        //refactor Repository
        //refactor domain
        try {
            $validated = $request->validate([
                'flight_number' => 'required|string|max:255',
                'departure_time' => 'required|date',
                //'status' => 'sometimes|string|in:PENDING,CONFIRMED,CANCELLED',
                'passengers' => 'required|array|min:1',
                'passengers.*.name' => 'required|string|max:255',
                'passengers.*.document' => 'required|string|max:255',
            ]);
            $reservation = Reservation::create($validated);

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
