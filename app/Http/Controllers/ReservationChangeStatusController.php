<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Reservation\Commands\ChangeReservationStatusCommand;
use App\Application\Reservation\Handlers\ChangeReservationStatusHandler;
use App\Application\Reservation\DTOs\ReservationDTO;
use App\Domain\Reservation\ValueObjects\ReservationId;
use App\Domain\Reservation\ValueObjects\ReservationStatus;
use App\Http\Requests\ChangeReservationStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use DomainException;

final class ReservationChangeStatusController extends Controller
{
    public function __construct(
        private readonly ChangeReservationStatusHandler $handler
    ) {}

    public function __invoke(int $reservationId, ChangeReservationStatusRequest $request): JsonResponse
    {
        try {
            $command = new ChangeReservationStatusCommand(
                ReservationId::fromInt($reservationId),
                ReservationStatus::fromString($request->getStatus())
            );

            $this->handler->handle($command);

            return response()->json([
                'message' => 'Reservation status changed successfully'
            ], Response::HTTP_OK);

        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
