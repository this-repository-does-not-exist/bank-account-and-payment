<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain;

use BankAccountAndPayment\Domain\Service\DailyLimitChecker;
use BankAccountAndPayment\Domain\ValueObject\Date;
use BankAccountAndPayment\Domain\ValueObject\Money;
use Psr\Log\LoggerInterface;

final class BankAccount
{
    private const float DEBIT_COST_MULTIPLIER = 0.005;
    private const int DEBIT_OPERATION_DAILY_LIMIT = 3;

    private Money $balance;

    public function __construct(
        string $currency,
        private readonly DailyLimitChecker $debitDailyLimitChecker,
        private readonly LoggerInterface $logger,
    ) {
        $this->balance = Money::create('0.00', $currency);
    }

    public function balance(): string
    {
        return $this->balance->amount();
    }

    /**
     * @return bool was the transaction successful
     */
    public function credit(Payment $payment): bool
    {
        try {
            $this->balance = $this->balance->add($payment->money);
        } catch (DomainException $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @return bool was the transaction successful
     */
    public function debit(Payment $payment, Date $date): bool
    {
        if ($this->debitDailyLimitChecker->isLimitReached($date, self::DEBIT_OPERATION_DAILY_LIMIT)) {
            $this->logger->error(\sprintf('Debit operations limit for %s reached.', $date->date));

            return false;
        }
        $this->debitDailyLimitChecker->increaseActivity($date);

        $moneyToWithdraw = $payment->money->add($payment->money->multiply(self::DEBIT_COST_MULTIPLIER));

        try {
            $this->balance = $this->balance->subtract($moneyToWithdraw);
        } catch (DomainException $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }

        return true;
    }
}
