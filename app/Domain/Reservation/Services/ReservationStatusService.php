<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Services;

use App\Domain\Reservation\Entities\Reservation;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use App\Domain\Reservation\Repositories\ReservationRepositoryInterface;
use DomainException;

final class ReservationStatusService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository
    ) {}

    public function changeStatus(ReservationId $reservationId, ReservationStatus $newStatus): Reservation
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if (!$reservation) {
            throw new DomainException('Reservation not found');
        }

        if (!$reservation->canBeModified()) {
            throw new DomainException('Reservation cannot be modified in its current status');
        }

        $reservation->changeStatus($newStatus);
        $this->reservationRepository->save($reservation);

        return $reservation;
    }

    public function getReservation(ReservationId $reservationId): Reservation
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if (!$reservation) {
            throw new DomainException('Reservation not found');
        }

        return $reservation;
    }
}
