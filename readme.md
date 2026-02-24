# Ticket Calculator

A simple mathematical expression calculator built with Laravel.

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Start the development server with `php artisan serve`

## Running Tests

```bash
vendor/bin/pest
```

Run a specific test file:

```bash
vendor/bin/pest tests/Feature/CalculatorTest.php
```
