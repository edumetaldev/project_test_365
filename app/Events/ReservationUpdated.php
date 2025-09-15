<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ReservationUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation->load('passengers');
    }

    public function broadcastOn()
    {
        // Canal de Redis
        return new Channel('reservations');
    }

    public function broadcastAs()
    {
        return 'reservation.updated';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->reservation->id,
            'status' => $this->reservation->status,
            'passengers' => $this->reservation->passengers,
        ];
    }
}
