<?php declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;
use BankAccountAndPayment\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
final class MoneyTest extends TestCase
{
    #[DataProvider('provideInvalidAmountCases')]
    public function testInvalidAmount(string $message, string $amount): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage($message);

        Money::create($amount, 'EUR');
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideInvalidAmountCases(): iterable
    {
        yield 'negative value' => [
            'Amount must be non-negative value with 2 decimal places.',
            '-1.00',
        ];

        yield 'no decimal places' => [
            'Amount must be non-negative value with 2 decimal places.',
            '10',
        ];

        yield '1 decimal place' => [
            'Amount must be non-negative value with 2 decimal places.',
            '2.5',
        ];

        yield '3 decimal place' => [
            'Amount must be non-negative value with 2 decimal places.',
            '123.456',
        ];
    }

    public function testValidAmount(): void
    {
        $money = Money::create('12.34', 'USD');

        self::assertSame('12.34', $money->amount());
        self::assertSame('USD', $money->currency());
    }

    public function testComparingDifferentAmountsAndDifferentCurrencies(): void
    {
        self::assertFalse(Money::create('1.00', 'JPY')->isEqual(Money::create('2.00', 'USD')));
    }

    public function testComparingDifferentAmountsInTheSameCurrency(): void
    {
        self::assertFalse(Money::create('1000.00', 'JPY')->isEqual(Money::create('1000.01', 'JPY')));
    }

    public function testComparingTheSameAmountInDifferentCurrencies(): void
    {
        self::assertFalse(Money::create('1.00', 'USD')->isEqual(Money::create('1.00', 'EUR')));
    }

    public function testComparingTheSameAmountInTheSameCurrency(): void
    {
        self::assertTrue(Money::create('5.00', 'EUR')->isEqual(Money::create('5.00', 'EUR')));
    }

    public function testAddingMoneyInDifferentCurrencies(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot add 2 amounts in different currencies.');

        Money::create('2.00', 'EUR')
            ->add(Money::create('2.00', 'USD'));
    }

    public function testAddingMoneyInTheSameCurrency(): void
    {
        $money1 = Money::create('1.00', 'JPY');
        $money2 = Money::create('0.50', 'JPY');

        $sum = $money1->add($money2);

        self::assertSame('JPY', $sum->currency());
        self::assertSame('1.50', $sum->amount());
    }

    public function testSubtractingMoneyInDifferentCurrencies(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot subtract 2 amounts in different currencies.');

        Money::create('5.00', 'EUR')
            ->subtract(Money::create('1.00', 'JPY'));
    }

    public function testSubtractingMoreMoneyThanItIs(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Negative amount is not allowed.');

        Money::create('0.50', 'EUR')
            ->subtract(Money::create('0.51', 'EUR'));
    }

    public function testSubtractingMoneyInTheSameCurrency(): void
    {
        $money1 = Money::create('15.50', 'USD');
        $money2 = Money::create('6.30', 'USD');

        $difference = $money1->subtract($money2);

        self::assertSame('USD', $difference->currency());
        self::assertSame('9.20', $difference->amount());
    }

    public function testMultiplyingByNegativeMultiplier(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Multiplier must be greater than 0.');

        Money::create('100.00', 'EUR')
            ->multiply(-0.01);
    }

    public function testMultiplyingByZero(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Multiplier must be greater than 0.');

        Money::create('1000.00', 'EUR')
            ->multiply(0);
    }

    public function testMultiplyCorrectly(): void
    {
        $product = Money::create('101.00', 'JPY')->multiply(0.25);

        self::assertSame('JPY', $product->currency());
        self::assertSame('25.25', $product->amount());
    }
}
