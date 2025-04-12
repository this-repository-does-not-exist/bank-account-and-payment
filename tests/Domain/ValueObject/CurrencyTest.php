<?php declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;
use BankAccountAndPayment\Domain\ValueObject\Currency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
final class CurrencyTest extends TestCase
{
    public function testCreatingInvalid(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Currency is not supported.');

        Currency::fromString('ABC');
    }

    public function testCreatingValid(): void
    {
        $currency = Currency::fromString('EUR');

        self::assertSame('EUR', $currency->value);
    }
}
