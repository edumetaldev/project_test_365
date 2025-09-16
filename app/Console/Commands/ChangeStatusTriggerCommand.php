<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Reservation\Commands\ChangeReservationStatusCommand;
use App\Application\Reservation\Handlers\ChangeReservationStatusHandler;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use App\Events\ReservationUpdated;
use App\Models\Reservation;
use Illuminate\Console\Command;

final class ChangeStatusTriggerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-status-trigger-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambia aleatoriamente el status de una reservación existente';

    public function __construct(
        private readonly ChangeReservationStatusHandler $handler
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

            while (true) {
                $reservation = Reservation::inRandomOrder()->first();
                if ($reservation) {
                    $newStatus = \Arr::random(['CONFIRMED','CANCELLED','CHECKED_IN']);
                    $reservation->status = $newStatus;
                    $reservation->save();

                   // event(new ReservationUpdated($reservation));
                }
                $this->info(sprintf(
                    'Status cambiado exitosamente para la reservación ID %d de "%s" a "%s"',
                    $reservation->id,
                    $newStatus,
                    $reservation->status,
                ));
                sleep(5);
            }
    }

    /**
     * Genera un status aleatorio válido
     */
    private function getRandomStatus(): ReservationStatus
    {
        $validStatuses = [
            ReservationStatus::PENDING,
            ReservationStatus::CONFIRMED,
            ReservationStatus::CANCELLED,
            ReservationStatus::CHECKED_IN,
        ];

        $randomStatusValue = $validStatuses[array_rand($validStatuses)];

        return ReservationStatus::fromString($randomStatusValue);
    }
}
