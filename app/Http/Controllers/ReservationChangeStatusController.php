<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationChangeStatusController extends Controller
{
    protected $posibleNextStatus = [
        'PENDING',
        'CONFIRMED',
        'CANCELLED',
        'CHECKED_IN'
    ];

    public function __invoke(Reservation $reservation, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->posibleNextStatus)
        ]);

        $reservation->update(['status' => $validated['status']]);

        return response()->json([
            'data' => $reservation,
            'message' => 'Reservation status change successfully'
        ], Response::HTTP_OK);
    }
}
