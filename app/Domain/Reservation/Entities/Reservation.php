<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Entities;

use App\Domain\Reservation\ValueObjects\FlightNumber;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use App\Domain\Reservation\Events\ReservationStatusChanged;
use Carbon\Carbon;
use DomainException;

final class Reservation
{
    private array $domainEvents = [];

    public function __construct(
        private readonly ReservationId $id,
        private readonly FlightNumber $flightNumber,
        private readonly Carbon $departureTime,
        private ReservationStatus $status,
        private readonly array $passengers = []
    ) {}

    public function id(): ReservationId
    {
        return $this->id;
    }

    public function flightNumber(): FlightNumber
    {
        return $this->flightNumber;
    }

    public function departureTime(): Carbon
    {
        return $this->departureTime;
    }

    public function status(): ReservationStatus
    {
        return $this->status;
    }

    public function passengers(): array
    {
        return $this->passengers;
    }

    public function changeStatus(ReservationStatus $newStatus): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new DomainException(
                sprintf(
                    'Cannot change reservation status from %s to %s',
                    $this->status->value(),
                    $newStatus->value()
                )
            );
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;

        $this->addDomainEvent(new ReservationStatusChanged(
            $this->id,
            $oldStatus,
            $newStatus,
            Carbon::now()
        ));
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isConfirmed(): bool
    {
        return $this->status->isConfirmed();
    }

    public function isCancelled(): bool
    {
        return $this->status->isCancelled();
    }

    public function isCheckedIn(): bool
    {
        return $this->status->isCheckedIn();
    }

    public function canBeModified(): bool
    {
        return !$this->status->isCancelled() && !$this->status->isCheckedIn();
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    private function addDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public static function create(
        ReservationId $id,
        FlightNumber $flightNumber,
        Carbon $departureTime,
        array $passengers = []
    ): self {
        return new self(
            $id,
            $flightNumber,
            $departureTime,
            ReservationStatus::pending(),
            $passengers
        );
    }
}
