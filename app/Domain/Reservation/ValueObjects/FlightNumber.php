<?php

declare(strict_types=1);

namespace App\Domain\Reservation\ValueObjects;

use InvalidArgumentException;

final readonly class FlightNumber
{
    public function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Flight number cannot be empty');
        }

        if (strlen($value) < 3 || strlen($value) > 10) {
            throw new InvalidArgumentException('Flight number must be between 3 and 10 characters');
        }

        if (!preg_match('/^[A-Z0-9]+$/', $value)) {
            throw new InvalidArgumentException('Flight number must contain only uppercase letters and numbers');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(FlightNumber $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
