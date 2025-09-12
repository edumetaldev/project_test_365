<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

final class ReservationsController extends Controller
{
    public function index(): JsonResponse
    {
        $reservations = Reservation::all();

        return response()->json([
            'message' => 'Reservations retrieved successfully',
            'data' => $reservations
        ], Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'flight_number' => ['required', 'string', 'max:10'],
            'departure_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'status' => ['sometimes', 'string', Rule::in(['PENDING', 'CONFIRMED', 'CANCELLED'])],
            'passengers' => ['required', 'array', 'min:1'],
            'passengers.*.name' => ['required', 'string', 'max:100'],
            'passengers.*.document' => ['required', 'string', 'max:20'],
        ], [
            'flight_number.required' => 'The flight number field is required.',
            'flight_number.max' => 'The flight number may not be greater than 10 characters.',
            'departure_time.required' => 'The departure time field is required.',
            'departure_time.date_format' => 'The departure time does not match the format Y-m-d H:i:s.',
            'passengers.required' => 'The passengers field is required.',
            'passengers.min' => 'There must be at least one passenger.',
            'passengers.*.name.required' => 'Each passenger must have a name.',
            'passengers.*.name.max' => 'Passenger name may not be greater than 100 characters.',
            'passengers.*.document.required' => 'Each passenger must have a document.',
            'passengers.*.document.max' => 'Passenger document may not be greater than 20 characters.',
        ]);

        try {
            $reservation = Reservation::create($validatedData);

            return response()->json([
                'message' => 'Reservation created successfully',
                'data' => $reservation
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating reservation',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}