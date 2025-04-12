<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain;

use BankAccountAndPayment\Domain\Service\DailyLimitChecker;
use Psr\Log\LoggerInterface;

final readonly class BankAccountFactory
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function create(string $currency): BankAccount
    {
        return new BankAccount(
            $currency,
            new DailyLimitChecker(),
            $this->logger,
        );
    }
}
