<?php declare(strict_types=1);

namespace BankAccountAndPayment\Domain\Service;

use BankAccountAndPayment\Domain\ValueObject\Date;

final class DailyLimitChecker
{
    /** @var array<string, int> */
    private array $activitiesByDay = [];

    public function isLimitReached(Date $date, int $limit): bool
    {
        return isset($this->activitiesByDay[$date->date])
            && $this->activitiesByDay[$date->date] >= $limit;
    }

    public function increaseActivity(Date $date): void
    {
        if (!isset($this->activitiesByDay[$date->date])) {
            $this->activitiesByDay[$date->date] = 0;
        }

        $this->activitiesByDay[$date->date]++;
    }
}
