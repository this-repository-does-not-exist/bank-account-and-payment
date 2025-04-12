<?php declare(strict_types=1);

namespace Tests\Domain\Service;

use BankAccountAndPayment\Domain\Service\DailyLimitChecker;
use BankAccountAndPayment\Domain\ValueObject\Date;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DailyLimitChecker::class)]
final class DailyLimitCheckerTest extends TestCase
{
    public function testCanDoDebitOperation(): void
    {
        $dateJanuary = new Date('2025-01-01');
        $dateFebruary = new Date('2025-02-01');

        $limiter = new DailyLimitChecker();

        self::assertFalse($limiter->isLimitReached($dateJanuary, 1));
        self::assertFalse($limiter->isLimitReached($dateJanuary, 20));
        self::assertFalse($limiter->isLimitReached($dateFebruary, 5));

        $limiter->increaseActivity($dateJanuary);
        $limiter->increaseActivity($dateJanuary);

        $limiter->increaseActivity($dateFebruary);
        $limiter->increaseActivity($dateFebruary);
        $limiter->increaseActivity($dateFebruary);

        self::assertTrue($limiter->isLimitReached($dateJanuary, 1));
        self::assertTrue($limiter->isLimitReached($dateJanuary, 2));
        self::assertFalse($limiter->isLimitReached($dateJanuary, 3));
        self::assertFalse($limiter->isLimitReached($dateJanuary, 100));

        self::assertTrue($limiter->isLimitReached($dateFebruary, 3));
        self::assertFalse($limiter->isLimitReached($dateFebruary, 4));
    }
}
