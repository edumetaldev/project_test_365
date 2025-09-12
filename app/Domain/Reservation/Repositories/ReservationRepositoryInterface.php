<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Repositories;

use App\Domain\Reservation\Entities\Reservation;
use App\Domain\Reservation\ValueObjects\ReservationId;

interface ReservationRepositoryInterface
{
    public function findById(ReservationId $id): ?Reservation;

    public function save(Reservation $reservation): void;

    public function delete(ReservationId $id): void;

    public function exists(ReservationId $id): bool;
}
