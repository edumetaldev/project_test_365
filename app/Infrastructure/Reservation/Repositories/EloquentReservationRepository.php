<?php

declare(strict_types=1);

namespace App\Infrastructure\Reservation\Repositories;

use App\Domain\Reservation\Entities\Reservation;
use App\Domain\Reservation\Repositories\ReservationRepositoryInterface;
use App\Domain\Reservation\ValueObjects\FlightNumber;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use App\Models\Reservation as EloquentReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function findById(ReservationId $id): ?Reservation
    {
        $eloquentReservation = EloquentReservation::find($id->value());

        if (!$eloquentReservation) {
            return null;
        }

        return $this->toDomainEntity($eloquentReservation);
    }

    public function save(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $eloquentReservation = EloquentReservation::find($reservation->id()->value());

            if (!$eloquentReservation) {
                $eloquentReservation = new EloquentReservation();
                $eloquentReservation->id = $reservation->id()->value();
            }

            $eloquentReservation->flight_number = $reservation->flightNumber()->value();
            $eloquentReservation->departure_time = $reservation->departureTime();
            $eloquentReservation->status = $reservation->status()->value();
            $eloquentReservation->passengers = $reservation->passengers();

            $eloquentReservation->save();
        });
    }

    public function delete(ReservationId $id): void
    {
        EloquentReservation::destroy($id->value());
    }

    public function exists(ReservationId $id): bool
    {
        return EloquentReservation::where('id', $id->value())->exists();
    }

    private function toDomainEntity(EloquentReservation $eloquentReservation): Reservation
    {
        return new Reservation(
            ReservationId::fromInt($eloquentReservation->id),
            new FlightNumber($eloquentReservation->flight_number),
            Carbon::parse($eloquentReservation->departure_time),
            ReservationStatus::fromString($eloquentReservation->status),
            $eloquentReservation->passengers ?? []
        );
    }
}
