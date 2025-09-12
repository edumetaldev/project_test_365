<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Reservation\Handlers\ChangeReservationStatusHandler;
use App\Domain\Reservation\Repositories\ReservationRepositoryInterface;
use App\Domain\Reservation\Services\ReservationStatusService;
use App\Infrastructure\Reservation\Repositories\EloquentReservationRepository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            ReservationRepositoryInterface::class,
            EloquentReservationRepository::class
        );

        // Service bindings
        $this->app->bind(ReservationStatusService::class, function ($app) {
            return new ReservationStatusService(
                $app->make(ReservationRepositoryInterface::class)
            );
        });

        // Handler bindings
        $this->app->bind(ChangeReservationStatusHandler::class, function ($app) {
            return new ChangeReservationStatusHandler(
                $app->make(ReservationStatusService::class),
                $app->make('events')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
