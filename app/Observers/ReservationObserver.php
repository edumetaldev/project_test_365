<?php

namespace App\Observers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        Log::info('Nueva reservación creada', [
            'reservation_id' => $reservation->id,
            'reservation_number' => $reservation->reservation_number ?? 'N/A',
            'created_at' => $reservation->created_at
        ]);
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        Log::info('Reservación actualizada', [
            'reservation_id' => $reservation->id,
            'status' => $reservation->status,
            'updated_at' => $reservation->updated_at
        ]);
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "force deleted" event.
     */
    public function forceDeleted(Reservation $reservation): void
    {
        //
    }
}
