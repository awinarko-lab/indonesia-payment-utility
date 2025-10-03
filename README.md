# Indonesia Utilities

Indonesian utilities package for payment applications.

> **Requires [PHP 8.3+](https://php.net/releases/)**

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require awinarko/indonesia-utilities
```

## Features

- **Currency** - Format and parse Indonesian Rupiah (IDR)
- **Terbilang** - Convert numbers to Indonesian words
- **NIK** - Validate and parse Indonesian ID numbers (Nomor Induk Kependudukan)
- **Phone Numbers** - Validate and format Indonesian phone numbers
- **Bank Codes** - Indonesian bank code utilities and resolvers

## Usage

### Currency Formatting

```php
use Awinarko\IndonesiaUtilities\Currency\RupiahFormatter;

// Format numbers as Rupiah
```

### Terbilang (Number to Words)

```php
use Awinarko\IndonesiaUtilities\Terbilang\Terbilang;

// Convert numbers to Indonesian words
```

### NIK Validation

```php
use Awinarko\IndonesiaUtilities\Nik\NikValidator;

// Validate Indonesian ID numbers
```

### Phone Number Handling

```php
use Awinarko\IndonesiaUtilities\PhoneNumber\PhoneNumberValidator;

// Validate Indonesian phone numbers
```

### Bank Codes

```php
use Awinarko\IndonesiaUtilities\Bank\BankCode;

// Work with Indonesian bank codes
```

## Development

🧹 Keep a modern codebase with **Pint**:
```bash
composer lint
```

✅ Run refactors using **Rector**
```bash
composer refacto
```

⚗️ Run static analysis using **PHPStan**:
```bash
composer test:types
```

✅ Run unit tests using **PEST**
```bash
composer test:unit
```

🚀 Run the entire test suite:
```bash
composer test
```

## License

**Indonesia Utilities** is open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
