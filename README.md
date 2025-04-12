# bank-account-and-payment

Simple [bank account](src/Domain/BankAccount.php) and [payment](src/Domain/Payment.php) classes.

### Setup
Start Docker containers:
```console
docker compose up -d
```

Enter PHP container:
```console
docker compose exec -it php /bin/sh
```

Install dependencies:
```console
composer install
```

Run all checks:
```console
composer verify
```

### Simplifications
 - only 3 currencies allowed,
 - there is only single exception class `BankAccountAndPayment\Domain\DomainException`, instead of an exception for each domain case,
 - transaction cost and daily limit used as constant in `BankAccountAndPayment\Domain\BankAccount`, instead of provided as a config.
