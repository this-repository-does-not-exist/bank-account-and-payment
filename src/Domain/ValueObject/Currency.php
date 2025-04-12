<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;

enum Currency: string
{
    case EUR = 'EUR';
    case JPY = 'JPY';
    case USD = 'USD';

    public static function fromString(string $currency): Currency
    {
        try {
            return Currency::from($currency);
        } catch (\ValueError) {
            throw new DomainException('Currency is not supported.');
        }
    }
}
