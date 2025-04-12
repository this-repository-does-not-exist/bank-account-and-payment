<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;

final readonly class Money
{
    private function __construct(
        private int $amount,
        private Currency $currency,
    ) {}

    public static function create(string $amountString, string $currencyString): self
    {
        if (\preg_match('/^\\d+\\.\\d{2}$/', $amountString) !== 1) {
            throw new DomainException('Amount must be non-negative value with 2 decimal places.');
        }

        return new self((int) (100 * (float) $amountString), Currency::fromString($currencyString));
    }

    public function amount(): string
    {
        return \number_format($this->amount / 100, 2, '.', '');
    }

    public function currency(): string
    {
        return $this->currency->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->currency === $other->currency && $this->amount === $other->amount;
    }

    public function add(self $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new DomainException('Cannot add 2 amounts in different currencies.');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new DomainException('Cannot subtract 2 amounts in different currencies.');
        }

        if ($this->amount < $other->amount) {
            throw new DomainException('Negative amount is not allowed.');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        if ($multiplier <= 0) {
            throw new DomainException('Multiplier must be greater than 0.');
        }

        return new self((int) \round($multiplier * $this->amount), $this->currency);
    }
}
