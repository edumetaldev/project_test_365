<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Events;

use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use Carbon\Carbon;

final readonly class ReservationStatusChanged
{
    public function __construct(
        public ReservationId $reservationId,
        public ReservationStatus $oldStatus,
        public ReservationStatus $newStatus,
        public Carbon $occurredAt
    ) {}

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function oldStatus(): ReservationStatus
    {
        return $this->oldStatus;
    }

    public function newStatus(): ReservationStatus
    {
        return $this->newStatus;
    }

    public function occurredAt(): Carbon
    {
        return $this->occurredAt;
    }
}
