<?php

declare(strict_types=1);

namespace App\Domain\Reservation\ValueObjects;

use InvalidArgumentException;

final readonly class ReservationId
{
    public function __construct(
        private int $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Reservation ID must be a positive integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ReservationId $other): bool
    {
        return $this->value === $other->value;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
