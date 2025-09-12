<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChangeReservationStatusRequest;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ReservationChangeStatusController extends Controller
{
    public function __invoke(Reservation $reservation, ChangeReservationStatusRequest $request): JsonResponse
    {
        $reservation->update(['status' => $request->getStatus()]);

        return response()->json([
            'data' => $reservation,
            'message' => 'Reservation status changed successfully'
        ], Response::HTTP_OK);
    }
}
