<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Reservation\Commands\ChangeReservationStatusCommand;
use App\Application\Reservation\Handlers\ChangeReservationStatusHandler;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
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
        try {
            // Obtener una reservación aleatoria
            $randomReservation = Reservation::inRandomOrder()->first();

            if (!$randomReservation) {
                $this->error('No hay reservaciones disponibles en la base de datos.');
                return self::FAILURE;
            }

            // Generar un status aleatorio válido
            $randomStatus = $this->getRandomStatus();

            // Crear el comando con los parámetros aleatorios
            $command = new ChangeReservationStatusCommand(
                new ReservationId($randomReservation->id),
                $randomStatus
            );

            // Ejecutar el comando
            $this->handler->handle($command);

            $this->info(sprintf(
                'Status cambiado exitosamente para la reservación ID %d de "%s" a "%s"',
                $randomReservation->id,
                $randomReservation->status,
                $randomStatus->value()
            ));

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error al cambiar el status: ' . $e->getMessage());
            return self::FAILURE;
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
