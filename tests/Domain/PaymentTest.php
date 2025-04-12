<?php declare(strict_types=1);

namespace Tests\Domain;

use BankAccountAndPayment\Domain\DomainException;
use BankAccountAndPayment\Domain\Payment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Payment::class)]
final class PaymentTest extends TestCase
{
    public function testCreatingWithNegativeAmount(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Amount must be non-negative value with 2 decimal places.');

        new Payment('-10.00', 'USD');
    }

    public function testCreatingWithNonSupportedCurrency(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Currency is not supported.');

        new Payment('1.00', 'ABC');
    }

    public function testCreatingCorrectly(): void
    {
        $payment = new Payment('2.50', 'EUR');

        self::assertSame('2.50', $payment->amount);
        self::assertSame('EUR', $payment->currency);
    }
}
