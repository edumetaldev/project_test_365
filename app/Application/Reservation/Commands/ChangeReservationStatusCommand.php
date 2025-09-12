<?php

declare(strict_types=1);

namespace App\Application\Reservation\Commands;

use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;

final readonly class ChangeReservationStatusCommand
{
    public function __construct(
        public ReservationId $reservationId,
        public ReservationStatus $newStatus
    ) {}
}
