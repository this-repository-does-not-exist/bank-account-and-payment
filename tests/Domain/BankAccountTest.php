<?php declare(strict_types=1);

namespace Tests\Domain;

use BankAccountAndPayment\Domain\BankAccount;
use BankAccountAndPayment\Domain\BankAccountFactory;
use BankAccountAndPayment\Domain\Payment;
use BankAccountAndPayment\Domain\ValueObject\Date;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(BankAccount::class)]
#[CoversClass(BankAccountFactory::class)]
final class BankAccountTest extends TestCase
{
    private BankAccountFactory $bankAccountFactory;
    private LoggerInterface&MockObject $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->bankAccountFactory = new BankAccountFactory($this->logger);
    }

    public function testDepositingInTheDifferentCurrencies(): void
    {
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Cannot add 2 amounts in different currencies.');

        $bankAccount = $this->bankAccountFactory->create('JPY');

        self::assertFalse($bankAccount->credit(new Payment('10.00', 'USD')));
    }

    public function testDepositingMultipleTimes(): void
    {
        $this->logger->expects($this->never())->method('error');

        $bankAccount = $this->bankAccountFactory->create('EUR');

        for ($i = 0; $i < 111; $i++) {
            self::assertTrue($bankAccount->credit(new Payment('2.00', 'EUR')));
        }

        self::assertSame('222.00', $bankAccount->balance());
    }

    public function testWithdrawingInTheDifferentCurrencies(): void
    {
        $bankAccount = $this->bankAccountFactory->create('EUR');
        self::assertTrue($bankAccount->credit(new Payment('100.00', 'EUR')));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Cannot subtract 2 amounts in different currencies.');

        self::assertFalse($bankAccount->debit(new Payment('10.00', 'USD'), new Date('2025-04-12')));
    }

    public function testWithdrawingMoreThanOnTheAccount(): void
    {
        $bankAccount = $this->bankAccountFactory->create('EUR');
        self::assertTrue($bankAccount->credit(new Payment('100.00', 'EUR')));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Cannot subtract 2 amounts in different currencies.');

        self::assertFalse($bankAccount->debit(new Payment('10.00', 'USD'), new Date('2025-04-12')));
    }

    public function testWithdrawingFourTimesInSingleDay(): void
    {
        $date = new Date('2025-04-12');

        $bankAccount = $this->bankAccountFactory->create('EUR');
        self::assertTrue($bankAccount->credit(new Payment('1000.00', 'EUR')));

        self::assertTrue($bankAccount->debit(new Payment('10.00', 'EUR'), $date));
        self::assertTrue($bankAccount->debit(new Payment('10.00', 'EUR'), $date));
        self::assertTrue($bankAccount->debit(new Payment('10.00', 'EUR'), $date));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Debit operations limit for 2025-04-12 reached.');

        self::assertFalse($bankAccount->debit(new Payment('10.00', 'EUR'), $date));

        self::assertSame('969.85', $bankAccount->balance());
    }

    public function testWithdrawing(): void
    {
        $this->logger->expects($this->never())->method('error');

        $bankAccount = $this->bankAccountFactory->create('EUR');
        self::assertTrue($bankAccount->credit(new Payment('1000.00', 'EUR')));

        self::assertTrue($bankAccount->debit(new Payment('100.00', 'EUR'), new Date('2025-04-12')));
        self::assertSame('899.50', $bankAccount->balance());

        self::assertTrue($bankAccount->debit(new Payment('10.00', 'EUR'), new Date('2025-04-12')));
        self::assertSame('889.45', $bankAccount->balance());

        self::assertTrue($bankAccount->debit(new Payment('500.00', 'EUR'), new Date('2025-04-12')));
        self::assertSame('386.95', $bankAccount->balance());
    }

    public function testWithdrawingEverything(): void
    {
        $this->logger->expects($this->never())->method('error');

        $bankAccount = $this->bankAccountFactory->create('EUR');
        self::assertTrue($bankAccount->credit(new Payment('1005.00', 'EUR')));

        self::assertTrue($bankAccount->debit(new Payment('1000.00', 'EUR'), new Date('2025-04-12')));
        self::assertSame('0.00', $bankAccount->balance());
    }
}
