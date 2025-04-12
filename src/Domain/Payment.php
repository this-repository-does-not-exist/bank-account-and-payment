<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain;

use BankAccountAndPayment\Domain\ValueObject\Money;

final readonly class Payment
{
    public Money $money;

    public function __construct(
        public string $amount,
        public string $currency,
    ) {
        $this->money = Money::create($this->amount, $this->currency);
    }
}
