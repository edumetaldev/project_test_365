<?php

declare(strict_types=1);

namespace App\Domain\Reservation\ValueObjects;

use InvalidArgumentException;

final readonly class ReservationStatus
{
    public const PENDING = 'PENDING';
    public const CONFIRMED = 'CONFIRMED';
    public const CANCELLED = 'CANCELLED';
    public const CHECKED_IN = 'CHECKED_IN';

    private const VALID_STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::CANCELLED,
        self::CHECKED_IN,
    ];

    public function __construct(
        private string $value
    ) {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid reservation status: %s. Valid statuses are: %s',
                    $value,
                    implode(', ', self::VALID_STATUSES)
                )
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ReservationStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->value === self::CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }

    public function isCheckedIn(): bool
    {
        return $this->value === self::CHECKED_IN;
    }

    public function canTransitionTo(ReservationStatus $newStatus): bool
    {
        return match ($this->value) {
            self::PENDING => in_array($newStatus->value, [self::CONFIRMED, self::CANCELLED], true),
            self::CONFIRMED => in_array($newStatus->value, [self::CHECKED_IN, self::CANCELLED], true),
            self::CHECKED_IN => false, // No se puede cambiar desde CHECKED_IN
            self::CANCELLED => false, // No se puede cambiar desde CANCELLED
            default => false,
        };
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function confirmed(): self
    {
        return new self(self::CONFIRMED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public static function checkedIn(): self
    {
        return new self(self::CHECKED_IN);
    }

    public static function fromString(string $status): self
    {
        return new self($status);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
