<?php

declare(strict_types=1);

namespace App\Application\Reservation\Handlers;

use App\Application\Reservation\Commands\ChangeReservationStatusCommand;
use App\Domain\Reservation\Services\ReservationStatusService;
use Illuminate\Events\Dispatcher;

final class ChangeReservationStatusHandler
{
    public function __construct(
        private readonly ReservationStatusService $reservationStatusService,
        private readonly Dispatcher $eventDispatcher
    ) {}

    public function handle(ChangeReservationStatusCommand $command): void
    {
        $reservation = $this->reservationStatusService->changeStatus(
            $command->reservationId,
            $command->newStatus
        );

        // Dispatch domain events
        foreach ($reservation->getDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        $reservation->clearDomainEvents();
    }
}
