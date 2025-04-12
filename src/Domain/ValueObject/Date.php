<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;

final readonly class Date
{
    public function __construct(
        public string $date,
    ) {
        $dateTime = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        if ($dateTime === false || $dateTime->format('Y-m-d') !== $date) {
            throw new DomainException('Invalid date.');
        }
    }
}
