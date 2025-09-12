<?php

declare(strict_types=1);

namespace App\Application\Reservation\DTOs;

use App\Domain\Reservation\ValueObjects\ReservationStatus;
use Carbon\Carbon;

final readonly class ReservationDTO
{
    public function __construct(
        public int $id,
        public string $flightNumber,
        public Carbon $departureTime,
        public string $status,
        public array $passengers
    ) {}

    public static function fromDomainEntity(\App\Domain\Reservation\Entities\Reservation $reservation): self
    {
        return new self(
            $reservation->id()->value(),
            $reservation->flightNumber()->value(),
            $reservation->departureTime(),
            $reservation->status()->value(),
            $reservation->passengers()
        );
    }
}
