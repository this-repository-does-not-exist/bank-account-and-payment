<?php declare(strict_types=1);

namespace Tests\Domain\ValueObject;

use BankAccountAndPayment\Domain\DomainException;
use BankAccountAndPayment\Domain\ValueObject\Date;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Date::class)]
final class DateTest extends TestCase
{
    #[DataProvider('provideInvalidDateCases')]
    public function testInvalidDate(string $date): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid date.');

        new Date($date);
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideInvalidDateCases(): iterable
    {
        yield 'yesterday' => ['yesterday'];
        yield '1000-00-00' => ['1000-00-00'];
        yield '2024-13-01' => ['2024-13-01'];
        yield '2024-01-32' => ['2024-01-32'];
    }

    public function testValidDate(): void
    {
        $date = new Date('2025-04-12');

        self::assertSame('2025-04-12', $date->date);
    }
}
